<?php

namespace Breakdance\Render;

use Breakdance\Lib\Vendor\Twig\Environment;
use Breakdance\Lib\Vendor\Twig\Error\LoaderError;
use Breakdance\Lib\Vendor\Twig\Loader\LoaderInterface;
use Breakdance\Lib\Vendor\Twig\Source;
use Breakdance\Lib\Vendor\Twig\TwigFunction;

use Breakdance\Filesystem\Consts;
use Breakdance\PluginsAPI\PluginsController;
use function Breakdance\Filesystem\HelperFunctions\get_bucket_abs_path;
use function Breakdance\Util\Timing\finish;
use function Breakdance\Util\Timing\start;

/* We use a singleton because we want to reuse any template with the same content
Instead of creating a new template each time, which is more expensive

We also get caching with CustomArrayLoader.
 */

const MACROS_TWIG_TPL_ID = "macros.twig";


/**
 * 99% matches to Twig's ArrayLoader, but without using template contents within cache key.
 */
class CustomArrayLoader implements LoaderInterface {
    /**
     * @var array
     * @psalm-var Array<string, string>
     */
    private $templates = [];

    /**
     * @param array $templates An array of templates (keys are the names, and values are the source code)
     * @psalm-param Array<string, string> $templates
     */
    public function __construct(array $templates = [])
    {
        $this->templates = $templates;
    }

    public function setTemplate(string $name, string $template): void
    {
        $this->templates[$name] = $template;
    }

    public function getSourceContext(string $name): Source
    {
        if (!isset($this->templates[$name])) {
            throw new LoaderError(sprintf('Template "%s" is not defined.', $name));
        }

        return new Source($this->templates[$name], $name);
    }

    public function exists(string $name): bool
    {
        return isset($this->templates[$name]);
    }

    public function getCacheKey(string $name): string
    {
        if (!isset($this->templates[$name])) {
            throw new LoaderError(sprintf('Template "%s" is not defined.', $name));
        }

        /**
         * The only difference with ArrayLoader is this line – the original method returned
         *
         *      return $name.':'.$this->templates[$name];
         *
         * That's redundant – $name is already a hash of template body (except for "macros.twig").
         * This change greatly reduced peak memory usage.
         */

        if ($name === MACROS_TWIG_TPL_ID) {
            return hash('crc32b', $this->templates[$name]);
        } else {
            return $name;
        }
    }

    public function isFresh(string $name, int $time): bool
    {
        if (!isset($this->templates[$name])) {
            throw new LoaderError(sprintf('Template "%s" is not defined.', $name));
        }

        return true;
    }
}


class Twig
{

    use \Breakdance\Singleton;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var CustomArrayLoader
     */
    private $loader;


    // Store Twig's render results by hash
    /**
     * @var string[]
     */
    public $cachedResults = [];

    /**
     * This is a Queue data structure (first-in-first-out)
     * We store up to "$maximumAllowedQueueSize" in the cache and above that, we remove the oldest
     * that way we keep the cache size (aka memory) down. Otherwise it could overflow memory for gigantic pages
     *
     * @var array<array-key, string>
     */
    private $cacheHashQueue = [];
    /**
     * @var int
     */
    private $queuePosition = 0;
    /**
     * I'm deciding on 1000 out of my ass. A test page with 500 caches was ~300kb for me. Even up to 1mb sounds OK.
     * @var int
     */
    private $maximumAllowedQueueSize = 1000;

    private function __construct()
    {
        $timing = start('constructTwig');

        /*
         * Using CustomArrayLoader *should* be as performant as the FileLoader
         *
         * The problem with using the ".twig" files is that it would add complexity to our code
         * We also add extra logic to all templates with "breakdance_element_css_template" filter and the macros code below
         * That's only possible if we use template strings. Using files we'd need to add all the code in each file
         */

        // The loader is initialized with basic info and we add the templates strings using the loader's "setTemplate"
        $twigMacros = \Breakdance\Elements\get_twig_macros_string();

        $this->loader = new CustomArrayLoader([
            MACROS_TWIG_TPL_ID => $twigMacros
        ]);

        $twigCachePath = get_bucket_abs_path(Consts::BREAKDANCE_FS_BUCKET_TWIG_CACHE);
        $this->twig = new Environment($this->loader, ['cache' => $twigCachePath, 'autoescape' => false]);

        foreach (
            PluginsController::getInstance()->TwigFunctionsPHPSide as $twigFunction
        ) {
            $function = new TwigFunction(
                $twigFunction['name'],
                $twigFunction['function'],
                // Added to avoid escaping the double quotes in the cssNames
                ['is_safe' => ['html']]
            );
            $this->twig->addFunction($function);
        }

        finish($timing);
    }

    /**
     * @param string $template
     * @param PropertiesData $propertiesData
     *
     * @return string
     */
    public function runTwig(string $template, $propertiesData)
    {
        // Explicitly check against empty string. "0" = falsy, but we want a template with "0" to still evaluate
        if ($template === '') {
            return '';
        }

        // Twig need at least an empty array otherwise it goes BOOM
        if (!$propertiesData) {
            $propertiesData = [];
        }

        $template = PHP_EOL . '{% import "' . MACROS_TWIG_TPL_ID . '" as macros %}' . PHP_EOL . $template . PHP_EOL;

        $templateHash = sha1($template);
        $cacheHash = $templateHash . sha1(serialize($propertiesData));

        if (isset($this->cachedResults[$cacheHash])) {
            return $this->cachedResults[$cacheHash];
        }

        /**
         * @psalm-suppress MixedMethodCall
         */
        $this->loader->setTemplate($templateHash, $template);

        /**
         * @psalm-suppress MixedMethodCall
         * @psalm-suppress MixedArgument
         */

        if ($this->queuePosition === $this->maximumAllowedQueueSize) {
            $this->queuePosition = 0;
        }

        // Keep the cache size down by removing the oldest element
        if (count($this->cachedResults) > $this->queuePosition) {
            $this->removeOldestFromQueue();
        }

        $this->addToQueue($cacheHash);

        /** @psalm-suppress MixedArgument */
        $this->cachedResults[$cacheHash] = (string)$this->twig->render($templateHash, $propertiesData);

        return $this->cachedResults[$cacheHash];
    }

    private function removeOldestFromQueue()
    {
        /**
         * @var string
         */
        $oldestCacheHash = $this->cacheHashQueue[$this->queuePosition];
        unset($this->cachedResults[$oldestCacheHash]);
    }

    /**
     * @param string $cacheHash
     */
    private function addToQueue($cacheHash)
    {
        $this->cacheHashQueue[$this->queuePosition] = $cacheHash;
        $this->queuePosition++;
    }
}

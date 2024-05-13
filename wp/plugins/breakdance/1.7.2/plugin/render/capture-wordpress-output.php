<?php

/* this file is where the crappy stateful complected shit is segregated */

namespace Breakdance\Render;

use Breakdance\GlobalDefaultStylesheets\GlobalDefaultStylesheetsController;

use function Breakdance\BrowseMode\isRequestFromBrowserIframe;
use function Breakdance\isRequestFromBuilderIframe;
use function Breakdance\Subscription\isFreeMode;
use function Breakdance\Util\Timing\finish;
use function Breakdance\Util\Timing\start;

define("BREAKDANCE_HEADER_ASSETS_PLACEHOLDER", '%%BREAKDANCE_HEADER_DEPENDENCIES%%');
define("BREAKDANCE_FOOTER_ASSETS_PLACEHOLDER", '%%BREAKDANCE_FOOTER_DEPENDENCIES%%');
define("BREAKDANCE_ASSETS_PRIORITY", 1000000);

add_action('init', function() {
    if (!isRequestFromBuilderIframe()) {
        $timing = start('captureWpOutputInit');
        $globalCache = getGlobalSettingsCache();
        $globalCache['dependencyCache'] && ScriptAndStyleHolder::getInstance()->append($globalCache['dependencyCache']);
        $globalCache['cssCache'] && ScriptAndStyleHolder::getInstance()->setGlobalGeneratedCssFilePaths($globalCache['cssCache']);
        finish($timing);
    }


    /*
    TODO: would it be smart to check if there actually is a valid cache here
    getPostCssCache and getGlobalCssCache both should always return a valid
    css cache... since they generate the cache.
    but what about not being able to write to the file system, etc?
    that has to be handled somewhere
    for now, we say the cache is valid and just assume everrything works
    */
});


function registerAssetPlaceholdersInHeaderAndFooter()
{
    /* what's a good number to use here so we can have breakdance's stuff override any other stylesheets, but still allow
    those who want to override breakdance stuff to do so?

    and how do we ensure that the order of the stylesheets and shit are kept in sync between the builder and the frontend?
     */
    add_action(
        'wp_head',
        function () {
            echo BREAKDANCE_HEADER_ASSETS_PLACEHOLDER;
        },
        BREAKDANCE_ASSETS_PRIORITY
    );

    add_action(
        'wp_footer',
        function () {
            echo BREAKDANCE_FOOTER_ASSETS_PLACEHOLDER;
        },
        BREAKDANCE_ASSETS_PRIORITY
    );
}

/**
 * This is the final function in Breakdance that runs
 * It turns the HTML into something ready to actually send to the browser - i.e.
 * it puts the dependencies (scripts, stylesheets, etc.) used by all Breakdance elements on the page
 * in the <head> and before </body> as necessary
 *
 * @param string $templateToInclude
 * @return void
 */
function getWordPressHtmlOutputWithHeaderAndFooterDependenciesAddedAndDisplayIt($templateToInclude)
{
    registerAssetPlaceholdersInHeaderAndFooter();

    $html = getWordPressHtmlOutput($templateToInclude);
    $headerAndFooterHtml = renderHtmlFromScriptAndStyleHolder(ScriptAndStyleHolder::getInstance());

    echo str_replace(
        [BREAKDANCE_HEADER_ASSETS_PLACEHOLDER, BREAKDANCE_FOOTER_ASSETS_PLACEHOLDER],
        [$headerAndFooterHtml['headerHtml'], $headerAndFooterHtml['footerHtml']],
        $html
    );
}

/**
 *
 * @param string $templateToInclude
 * @return string
 */
function getWordPressHtmlOutput($templateToInclude)
{
    ob_start();
    /** @psalm-suppress UnresolvableInclude */
    include $templateToInclude;
    return ob_get_clean();

    /* we should probably trycatch this shit as well huh */
}

class ScriptAndStyleHolder
{
    use \Breakdance\Singleton;

    /** @var ElementDependencyWithoutConditions */
    public $dependencies = EMPTY_DEPENDENCIES;

    /** @var GlobalGeneratedCssFilePaths */
    protected $globalGeneratedCssFilePaths = [];

    /** @var PostGeneratedCssFilePaths[]  */
    protected $postsGeneratedCssFilePaths = [];

    /**
     * @return GlobalGeneratedCssFilePaths
     */
    public function getGlobalGeneratedCssFilePaths()
    {
        return $this->globalGeneratedCssFilePaths;
    }

    /**
     * @param GlobalGeneratedCssFilePaths $cssFilePaths
     */
    public function setGlobalGeneratedCssFilePaths($cssFilePaths)
    {
        $this->globalGeneratedCssFilePaths = $cssFilePaths;
    }

    /**
     * @param int $postId
     * @param PostGeneratedCssFilePaths $cssFilePaths
     */
    public function setPostGeneratedCssFilePaths(int $postId, $cssFilePaths) {
        // Ignore empty arrays as the frontend expects an object and empty objects are converted to array.
        if (!$cssFilePaths) return;
        $this->postsGeneratedCssFilePaths[$postId] = $cssFilePaths;
    }

    /**
     * @return PostGeneratedCssFilePaths|null
     */
    public function getPostGeneratedCssFilePaths(int $postId)
    {
        return $this->postsGeneratedCssFilePaths[$postId] ?? null;
    }

    /**
     * @return PostGeneratedCssFilePaths[]
     */
    public function getPostsGeneratedCssFilePaths()
    {
        return $this->postsGeneratedCssFilePaths;
    }

    /**
     * @param ElementDependenciesAndConditions $dependenciesToAppend
     * @param boolean $dependencyIsBeingSideEffectedWhileRenderingCssAndMustBeCached
     * @return void
     */
    public function append($dependenciesToAppend, $dependencyIsBeingSideEffectedWhileRenderingCssAndMustBeCached = false)
    {

        /**
         * @var ElementDependenciesAndConditions
         */
        $dependenciesToAppend = apply_filters('breakdance_append_dependencies', $dependenciesToAppend);

        $dependenciesWithVersionedScripts = appendVersionToUrlForCacheBustingToScriptsAndStyles($dependenciesToAppend);
        $mergedDeps = dependencies_array_merge_recursive_without_conditions([$this->dependencies, $dependenciesWithVersionedScripts]);
        $this->dependencies = removeDuplicatedAndEmptyDependencies($mergedDeps);

        if ($dependencyIsBeingSideEffectedWhileRenderingCssAndMustBeCached) {
            DependencyCache::getInstance()->append($dependenciesToAppend);
        }
    }
}

/**
 * @param string $styleSheetUrl
 * @return string
 */
function renderStylesheetTag(string $styleSheetUrl): string
{
    return empty($styleSheetUrl)
        ? "<!-- Error: empty stylesheet URL -->" . PHP_EOL
        : "<link rel=\"stylesheet\" href=\"{$styleSheetUrl}\" />" . PHP_EOL;
}

/**
 * @param string[] $arr
 * @param string $key
 * @return string
 */
function renderStylesheetTagFromArrByKeyOrReturnEmptyString($arr, $key)
{
    return empty($arr[$key]) ? '' : renderStylesheetTag($arr[$key]);
}

/**
 * @param string $rawHtml
 * @param string $metaTagName
 * @return string
 */
function renderHtmlWrappedWithMetaTagsWhenInIframe($rawHtml, $metaTagName): string
{
    return (isRequestFromBuilderIframe() || isRequestFromBrowserIframe())
        ? (PHP_EOL . "<meta name='{$metaTagName}' content='start'/>"
            . PHP_EOL
            . trim($rawHtml)
            . PHP_EOL
            . "<meta name='{$metaTagName}' content='end'/>" . PHP_EOL)
        : PHP_EOL . trim($rawHtml) . PHP_EOL;
}

/**
 * @param ScriptAndStyleHolder $holder
 * @return array{headerHtml:string,footerHtml:string}
 */
function renderHtmlFromScriptAndStyleHolder(ScriptAndStyleHolder $holder)
{
    $timing = start('renderHtmlFromScriptAndStyleHolder');

    $globalDefaultStylesheets = implode(
        '',
        array_map(
            function($stylesheetUrl) {
                return renderStylesheetTag($stylesheetUrl);
            },
            GlobalDefaultStylesheetsController::getInstance()->stylesheetUrls
        )
    );

    $maybeGlobalSettingsCss = isRequestFromBrowserIframe()
        ? ''
        : renderStylesheetTagFromArrByKeyOrReturnEmptyString(
            $holder->getGlobalGeneratedCssFilePaths(),
            'globalSettingsCssFilePath'
        );

    $maybeGlobalSelectorsCss = isRequestFromBrowserIframe()
        ? ''
        : renderStylesheetTagFromArrByKeyOrReturnEmptyString(
            $holder->getGlobalGeneratedCssFilePaths(),
            'globalSelectorsCssFilePath'
        );

    $maybeGlobalSelectorsCss = (string)apply_filters('breakdance_global_selectors_css', $maybeGlobalSelectorsCss);

    $maybeDefaultCssForAllElements = renderStylesheetTagFromArrByKeyOrReturnEmptyString(
        $holder->getGlobalGeneratedCssFilePaths(),
        'defaultCssForAllElementsFilePath'
    );

    $maybeDefaultCssForElementsOfRenderedPosts = '';
    foreach ($holder->getPostsGeneratedCssFilePaths() as $postId => $postCssFilePaths) {
        if (!empty($postCssFilePaths['postDefaultsCssFilePath'])) {
            $maybeDefaultCssForElementsOfRenderedPosts .= renderStylesheetTag(
                    $postCssFilePaths['postDefaultsCssFilePath']
                ) . PHP_EOL;
        }
    }

    $maybeCssOfAllRenderedPosts = '';
    foreach ($holder->getPostsGeneratedCssFilePaths() as $postId => $postCssFilePaths) {
        $maybeCssOfRenderedPost = renderStylesheetTagFromArrByKeyOrReturnEmptyString(
            $postCssFilePaths,
            'postCssFilePath'
        );
        $maybeCssOfAllRenderedPosts .= $maybeCssOfRenderedPost . PHP_EOL;
    }

    // Google Font dependencies are stored separately as an array of font family names so
    // that we can retrieve all the selected font families in a single CSS file request
    if (array_key_exists('googleFonts', $holder->dependencies)) {
        $fontFamilies = array_map(static function($fontFamily) {
            // request all available weights and styles
            return sprintf('family=%s:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900', $fontFamily);
        }, array_unique($holder->dependencies['googleFonts']));
        if (!empty($fontFamilies)) {
            $googleFontUrl = 'https://fonts.googleapis.com/css2?' . implode('&', $fontFamilies) . '&display=swap';
            if (!array_key_exists('styles', $holder->dependencies)) {
                $holder->dependencies['styles'] = [];
            }
            $holder->dependencies['styles'][] = $googleFontUrl;
        }
    }

    $maybeCssOfRenderedElementsDependencies = '';
    if (array_key_exists('styles', $holder->dependencies)) {
        $maybeCssOfRenderedElementsDependencies .= array_reduce(
            $holder->dependencies['styles'],
            /**
             * @param string $acc
             * @param string $stylesheetUrl
             *
             * @return string
             */
            function ($acc, $stylesheetUrl) {
                return $acc . renderStylesheetTag($stylesheetUrl);
            },
            ''
        );
    }

    $headerInlineStyles = '';
    if (array_key_exists('inlineStyles', $holder->dependencies)) {
        $headerInlineStyles .= array_reduce(
            $holder->dependencies['inlineStyles'],
            /**
             * @param string $acc
             * @param string $inlineStyle
             *
             * @return string
             */
            function ($acc, $inlineStyle) {
                // Add an ID with the style hash to enable testing the order of the scripts
                return $acc . '<style id=' . md5($inlineStyle) . '>' . $inlineStyle . '</style>' . PHP_EOL;
            },
            ''
        );
    }

    /**
     * 1. global-defaults - This is first, because it shouldn’t override anything
     * (i.e. our WooCommerce default styles)
     *
     * 2. elements-dependencies - This is second, because it also shouldn’t override
     * anything (except maybe the global defaults), and everything should be able to
     * override it.
     *
     * 3. element-defaults - This is third, since it should override the dependencies
     * and global defaults. (Imagine a slider library that ships with stock styles,
     * but the slider element that uses the library wants to customize those styles)
     *
     * 4. global-settings - This should be able to override any dependencies and default CSS
     *
     * 5. selectors - a user creating a custom selector wants to change the style of something.
     * Therefore, this comes after all CSS except for element-specific CSS
     *
     * 6. element-dependencies-inline - inline style tags may want to style a specific
     * element, be dynamically generated, or something else. I really don’t think we need
     * inline style dependencies, but a long as we have them, they should be treated the
     * same way as the element-specific CSS (all-rendered-posts)
     *
     * 7. all-rendered-posts - element-specific CSS should override all other CSS
     */

    $headerHtml = '<!-- [HEADER ASSETS] -->'
        . renderHtmlWrappedWithMetaTagsWhenInIframe(
            $globalDefaultStylesheets,
            'breakdance:header-assets:css:global-default-stylesheets'
        )
        . renderHtmlWrappedWithMetaTagsWhenInIframe(
            $maybeCssOfRenderedElementsDependencies,
            'breakdance:header-assets:css:elements-dependencies'
        )
        . renderHtmlWrappedWithMetaTagsWhenInIframe(
            $maybeDefaultCssForElementsOfRenderedPosts . $maybeDefaultCssForAllElements,
            'breakdance:header-assets:css:elements-defaults'
        )
        . renderHtmlWrappedWithMetaTagsWhenInIframe(
            $maybeGlobalSettingsCss,
            'breakdance:header-assets:css:global-settings'
        )
        . renderHtmlWrappedWithMetaTagsWhenInIframe(
            $maybeGlobalSelectorsCss,
            'breakdance:header-assets:css:selectors'
        )
        . renderHtmlWrappedWithMetaTagsWhenInIframe(
            $headerInlineStyles,
            'breakdance:header-assets:css:elements-dependencies-inline'
        )
        . renderHtmlWrappedWithMetaTagsWhenInIframe(
            $maybeCssOfAllRenderedPosts,
            'breakdance:header-assets:css:all-rendered-posts'
        )
        . '<!-- [/EOF HEADER ASSETS] -->';

    $footerHtml = "";

    if (array_key_exists('scripts', $holder->dependencies)) {
        // JS loaded on the footer makes the HTML rendering faster
        // Note: we're also deferring inline scripts
        $footerHtml .= array_reduce(
            $holder->dependencies['scripts'],
            /**
             * @param string $acc
             * @param string $scriptUrl
             * @return string
             */
            function ($acc, $scriptUrl) {
                return "$acc<script src='$scriptUrl' defer></script>" . PHP_EOL;
            },
            ''
        );
    }

    if (array_key_exists('inlineScripts', $holder->dependencies)) {
        // these must load *after* the deferred scripts since they'll run in order.
        $footerHtml .= array_reduce(
            $holder->dependencies['inlineScripts'],
            /**
             * @param string $acc
             * @param string $inlineScript
             * @return string
             */
            function ($acc, $inlineScript) {
                // this is the equivalent of adding "defer" to a script with "src", since it'll only run after those scripts.
                $deferredInlineScript = "document.addEventListener('DOMContentLoaded', function(){ $inlineScript })";
                return "$acc<script>$deferredInlineScript </script>" . PHP_EOL;
            },
            ''
        );
    }

    finish($timing);

    return [
        'footerHtml' => $footerHtml,
        'headerHtml' => $headerHtml,
    ];
}

/**
 * @param ElementDependencyWithoutConditions $dependencies
 * @return ElementDependencyWithoutConditions
 */
function removeDuplicatedAndEmptyDependencies($dependencies)
{
    $deduplicatedDependencies = EMPTY_DEPENDENCIES;

    $deduplicatedDependencies['scripts'] = isset($dependencies['scripts'])
        ? array_values(
            array_filter(
                array_unique($dependencies['scripts']),
                '\Breakdance\Render\removeFalsyDependency'
            )
        )
        : [];

    $deduplicatedDependencies['inlineScripts'] = isset($dependencies['inlineScripts'])
        ? array_values(
            array_filter(
                array_unique($dependencies['inlineScripts']),
                '\Breakdance\Render\removeFalsyDependency'
            )
        )
        : [];

    $deduplicatedDependencies['styles'] = isset($dependencies['styles'])
        ? array_values(
            array_filter(
                array_unique($dependencies['styles']),
                '\Breakdance\Render\removeFalsyDependency'
            )
        )
        : [];

    $deduplicatedDependencies['inlineStyles'] = isset($dependencies['inlineStyles'])
        ? array_values(
            array_filter(
                array_unique($dependencies['inlineStyles']),
                '\Breakdance\Render\removeFalsyDependency'
            )
        )
        : [];

    $deduplicatedDependencies['googleFonts'] = isset($dependencies['googleFonts'])
        ? array_values(
            array_filter(
                array_unique($dependencies['googleFonts']),
                '\Breakdance\Render\removeFalsyDependency'
            )
        )
        : [];

    return $deduplicatedDependencies;
}

/**
 * @param null|string $dependency
 * @return bool
 */
function removeFalsyDependency($dependency){
    // null, "", are invalid dependencies
    return !!$dependency;
}

/**
 * @param ElementDependencyWithoutConditions $dependencies
 * @return ElementDependencyWithoutConditions
 */
function appendVersionToUrlForCacheBustingToScriptsAndStyles($dependencies){
    $depsWithVersionedScriptsAndStyles = $dependencies;

    $depsWithVersionedScriptsAndStyles['styles'] = isset($dependencies['styles'])
        ? array_map('\Breakdance\Render\appendVersionToUrlForCacheBusting',$dependencies['styles'])
        : [];

    $depsWithVersionedScriptsAndStyles['scripts'] = isset($dependencies['scripts'])
        ? array_map('\Breakdance\Render\appendVersionToUrlForCacheBusting',$dependencies['scripts'])
        : [];

    return $depsWithVersionedScriptsAndStyles;
}

define('EMPTY_DEPENDENCIES', [
    'scripts' => [],
    'inlineScripts' => [],
    'styles' => [],
    'inlineStyles' => [],
    'googleFonts' => [],
]);

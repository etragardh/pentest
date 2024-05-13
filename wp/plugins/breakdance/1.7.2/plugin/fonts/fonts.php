<?php

namespace Breakdance\Fonts;

use Breakdance\Render\ScriptAndStyleHolder;

/**
 * @param  string  $slug
 * @param  string  $cssName
 * @param  string  $label
 * @param  string  $fallbackString
 * @param  ElementDependenciesAndConditions  $dependencies
 *
 * @return BreakdanceFont
 */
function font($slug, $cssName, $label, $fallbackString, $dependencies)
{
    return [
        'slug' => $slug,
        'cssName' => $cssName,
        'label' => $label,
        'fallbackString' => $fallbackString,
        'dependencies' => $dependencies,
    ];
}

/*
The entire public API is just one function to register a font and its
dependencies - dependencies are the CSS/JS necessary for the CSS
font-family: 'Some-Font-Family' to actually work
 */

/**
 * @param  string  $slug
 * @param  string  $cssName
 * @param  string  $label
 * @param  string  $fallbackString
 * @param  ElementDependencyWithoutConditions  $dependencies
 *
 * @deprecated Use "breakdance_register_fonts" filter instead.
 *
 * @return void
 */
function registerFont($slug, $cssName, $label, $fallbackString, $dependencies)
{
    /** @psalm-suppress MissingClosureReturnType */
    $fn = static fn(FontsController $fontsController) =>
        $fontsController->registerFont($slug, $cssName, $label, $fallbackString, $dependencies);

    if (did_action('breakdance_register_fonts')) {
        $fn(FontsController::getInstance());
    } else {
        add_action(
            'breakdance_register_fonts',
            $fn
        );
    }
}

/**
 * @param  string  $slug
 * @param  string  $cssName
 * @param  string  $label
 * @param  string  $fallbackString
 * @param  ElementDependencyWithoutConditions  $dependencies
 *
 * @deprecated Use "breakdance_register_fonts" filter instead.
 *
 * @return void
 */
function registerFontAtTheStart($slug, $cssName, $label, $fallbackString, $dependencies)
{
    /** @psalm-suppress MissingClosureReturnType */
    $fn = static fn(FontsController $fontsController) =>
        $fontsController->registerFontAtTheStart($slug, $cssName, $label, $fallbackString, $dependencies);

    if (did_action('breakdance_register_fonts')) {
        $fn(FontsController::getInstance());
    } else {
        add_action(
            'breakdance_register_fonts',
            $fn
        );
    }
}

class FontsController
{
    use \Breakdance\Singleton;

    /**
     * keyed with slug
     *
     * @var array<string, BreakdanceFont>
     */
    protected $fonts = [];

    protected function __construct()
    {
        /**
         * Constructor is being lazily executed when FontsController::getInstance() is called for the first time,
         * allowing for on-demand font registration.
         *
         * Fonts are only accessed (and as a result this class is instantiated) when "process_font" Twig function
         * is executed. This should only occur when CSS templates are rendered – which, in most cases,
         * is done beforehand of frontend rendering routine.
         */
        do_action('breakdance_register_fonts', $this);
    }

    /**
     * @param string $slug
     * @param string $cssName
     * @param string $label
     * @param string $fallbackString
     * @param ElementDependencyWithoutConditions $dependencies
     *
     * @return void
     */
    public function registerFont($slug, $cssName, $label, $fallbackString, $dependencies)
    {
        $font = font(
            $slug,
            $cssName,
            $label,
            $fallbackString,
            $dependencies
        );

        /**
         * @var BreakdanceFont
         */
        $font = apply_filters("breakdance_register_font", $font);

        if ($font) {
            $this->fonts[$font['slug']] = $font;
        }
    }

    /**
     * @param string $slug
     * @param string $cssName
     * @param string $label
     * @param string $fallbackString
     * @param ElementDependencyWithoutConditions $dependencies
     *
     * @return void
     */
    public function registerFontAtTheStart($slug, $cssName, $label, $fallbackString, $dependencies)
    {
        $this->fonts = [
                $slug => font(
                    $slug,
                    $cssName,
                    $label,
                    $fallbackString,
                    $dependencies
                )
            ] + $this->fonts;
    }

    /**
     * @param string $fontSlug
     *
     * @return BreakdanceFont|null
     */
    public function getFont($fontSlug)
    {
        return $this->fonts[$fontSlug] ?? null;
    }

    /**
     * @param string $fontSlug
     *
     * @return array<string, BreakdanceFont>
     */
    public function getFonts()
    {
        return $this->fonts;
    }
}

/*
Side-effect the font dependencies into the HTML when rendering
All fonts in Twig CSS templates must be wrapped with process_font function. i.e

font-family: process_font(design.font_family);
 */

\Breakdance\PluginsAPI\PluginsController::getInstance()->registerTwigFunction(
    'process_font',
    'Breakdance\Fonts\process_font',
    <<<JS
    (slug) => {
        const foundFont = window.Breakdance.stores.configStore.fonts.find(font => slug === font.slug);

        if (foundFont) {
            window.Breakdance.canvas.addDependenciesToHead({dependencies: [foundFont.dependencies]});
            return foundFont.fallbackString ? foundFont.cssName + ', ' + foundFont.fallbackString : foundFont.cssName;
        }

        return '';
    }
    JS
    // TODO MAKE THIS FUCKING TYPESCRIPT FOR FUCK SAKE HOW MANY FUCKING HOURS HAVE I SPENT DEBUGGING SHIT
    // WITH THIS SHIT BECAUSE THERE AR NO TYPES????
);

/**
 * @param string $slug
 * @return string
 *
 * https://twig.symfony.com/doc/3.x/advanced.html#filters
 * twig sends the argument to the called function
 */
function process_font($slug)
{
    if ($slug && $font = FontsController::getInstance()->getFont($slug)) {

        ScriptAndStyleHolder::getInstance()->append($font['dependencies'], true);

        return $font['fallbackString'] ? $font['cssName'] . "," . $font['fallbackString'] : $font['cssName'];
    }

    return ''; // TODO: this isn't the right way to fallback if the font isnt found
}

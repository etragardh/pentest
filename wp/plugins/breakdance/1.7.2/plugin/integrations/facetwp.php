<?php

namespace Breakdance\Integrations\FacetWp;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Integrations\addConditionToControlSection;
use function Breakdance\Integrations\getElementSlugsThatCanHaveFiltering;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_fetch_facetwp_facets',
        'Breakdance\Integrations\FacetWp\getFacetWpFacets',
        'edit',
    );

    if (!function_exists('FWP')) {
        return;
    }

    /** @var string[] $supportedElements */
    $supportedElements = apply_filters('breakdance_facetwp_supported_elements', getElementSlugsThatCanHaveFiltering());

    add_filter('breakdance_element_classnames_for_html_class_attribute',
        /**
         * @param string[] $classesArg
         * @param string $elementSlug
         * @param mixed $props
         *
         * @return string[]
         */
        function ($classesArg, $elementSlug, $props) use ($supportedElements) {
            $classes = $classesArg;

            foreach ($supportedElements as $supportedElement) {
                if ($elementSlug === $supportedElement && ($props['content']['facetwp']['enabled'] ?? false )) {
                    // https://facetwp.com/help-center/listing-templates/custom-wp-query/
                    // Only enable it for the current element. Possible cuz the classes filter runs before the query one
                    add_filter('breakdance_query_control_query', '\Breakdance\Integrations\FacetWp\enableFacetWpForCustomQueries');
                    add_filter('breakdance_woocommerce_get_products_query', '\Breakdance\Integrations\FacetWp\enableFacetWpForCustomQueries');

                    $classes[] = 'facetwp-template';

                    return $classes;
                }

            }

            return $classes;
        },
        10,
        3
    );

    add_filter('breakdance_element_controls', '\Breakdance\Integrations\FacetWp\addControls', 69, 2);
    add_filter('breakdance_pagination_render_pagination', '\Breakdance\Integrations\FacetWp\disablePaginationWhenEnabled', 10, 2);
    add_filter('breakdance_builder_ssr_rendered_html', '\Breakdance\Integrations\FacetWp\addWarningIfSliderIsActive', 10, 3);
});

/**
 * @return array{text: string, value: string}[]
 */
function getFacetWpFacets(){
    if (!function_exists('FWP')) {
        return [];
    }

    /**
     * @var mixed
     */
    $facetWp = FWP();

    if (!$facetWp) return [];

    /**
     * @var array{label: string, name: string}[]|null
     * @psalm-suppress MixedPropertyFetch
     */
    $facets = $facetWp->helper->settings['facets'] ?? null;

    if (!$facets) return [];

    return array_map(
        function($facet){
            return ['text' => $facet['label'], 'value' => $facet['name']];
        },
        $facets
    );
}

/**
 * @param array|string $query
 * @return array|string
 */
function enableFacetWpForCustomQueries($query)
{
    remove_filter('breakdance_query_control_query', '\Breakdance\Integrations\FacetWp\enableFacetWpForCustomQueries');
    remove_filter('breakdance_woocommerce_get_products_query', '\Breakdance\Integrations\FacetWp\enableFacetWpForCustomQueries');

    if (is_string($query)){
        return $query . "&facetwp=true";
    }

    $query['facetwp'] = true;

    return $query;
}

/**
 * @param Control[] $controls
 * @param \Breakdance\Elements\Element $element
 * @return Control[]
 */
function addControls($controls, $element)
{
    /** @var string[] $supportedElements */
    $supportedElements = apply_filters('breakdance_facetwp_supported_elements', getElementSlugsThatCanHaveFiltering());

    if (!in_array($element::slug(), $supportedElements, true)) {
        return $controls;
    }

    /**
     * @psalm-suppress PossiblyUndefinedStringArrayOffset
     */
    $paginationIndex = array_search('pagination', array_column($controls['contentSections'], 'slug'));

    if ($paginationIndex) {
        $condition = ['external' => true, 'path' => 'content.facetwp.enabled', 'operand' => 'is not set', 'value' => ''];

        $controls['contentSections'] = addConditionToControlSection($controls['contentSections'], $condition, $paginationIndex);
    }

    $controls['contentSections'][] = controlSection(
        'facetwp',
        'FacetWP',
        [
            control('enabled', 'Enable FacetWP', [
                'type' => 'toggle'
            ]),
        ],
        ['isExternal' => true]
    );

    /** @var Control[] $controls */
    return $controls;
}

/**
 * @param bool $enabled
 * @param array $properties
 * @return false|mixed
 */
function disablePaginationWhenEnabled($enabled, $properties){
    if ($properties['content']['facetwp']['enabled'] ?? false){
        return false;
    }

    return $enabled;
}

/**
 * @param string $html
 * @param array $properties
 * @param string $elementSlug
 * @return string
 */
function addWarningIfSliderIsActive($html, $properties, $elementSlug){
    /** @var string[] $supportedElements */
    $supportedElements = apply_filters('breakdance_facetwp_supported_elements', getElementSlugsThatCanHaveFiltering());

    if (!in_array($elementSlug, $supportedElements, true)) {
        return $html;
    }

    if (!($properties['content']['facetwp']['enabled'] ?? false)){
        return $html;
    }

    $warning = "<div class='bde-ssr-error'>FaceWP isn't supported with the \"Slider\" layout</div><br>";

    if (($properties['design']['list']['layout'] ?? false) === 'slider'){
        return $warning . $html;
    }

    if (($properties['design']['layout']['layout'] ?? false)=== 'slider'){
        return $warning . $html;
    }

    return $html;
}

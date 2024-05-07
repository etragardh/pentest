<?php

namespace Breakdance\Integrations\WPGB;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Integrations\addConditionToControlSection;
use function Breakdance\Integrations\getElementSlugsThatCanHaveFiltering;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_fetch_wpgb_facets',
        'Breakdance\Integrations\WPGB\getWpgbFacets',
        'edit',
    );

    if (!function_exists('wp_grid_builder')) {
        return;
    }

    /** @var string[] $supportedElements */
    $supportedElements = apply_filters('breakdance_wpgridbuilder_supported_elements', getElementSlugsThatCanHaveFiltering());

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
                if ($elementSlug === $supportedElement && ($props['content']['wpgridbuilder']['enabled'] ?? false)) {
                    // Enable Wp Grid Builder, but only for the current element. Possible cuz the classes filter runs before the query one
                    // https://docs.wpgridbuilder.com/resources/guide-filter-custom-queries/
                    add_filter('breakdance_query_control_query', '\Breakdance\Integrations\WPGB\enableWpGriDBuilderForCustomQueries');
                    add_filter('breakdance_woocommerce_get_products_query', '\Breakdance\Integrations\WPGB\enableWpGriDBuilderForCustomQueries');

                    $classes[] = 'wpgb-content';

                    return $classes;
                }
            }

            return $classes;
        },
        10,
        3
    );

    add_filter('breakdance_element_controls', '\Breakdance\Integrations\WPGB\addControls', 69, 2);
    add_filter('breakdance_pagination_render_pagination', '\Breakdance\Integrations\WPGB\disablePaginationWhenEnabled', 10, 2);
    add_filter('breakdance_builder_ssr_rendered_html', '\Breakdance\Integrations\WPGB\addWarningIfSliderIsActive', 10, 3);
});

/**
 * @param array|string $query
 * @return array|string
 */
function enableWpGriDBuilderForCustomQueries($query)
{
    remove_filter('breakdance_query_control_query', '\Breakdance\Integrations\WPGB\enableWpGriDBuilderForCustomQueries');
    remove_filter('breakdance_woocommerce_get_products_query', '\Breakdance\Integrations\WPGB\enableWpGriDBuilderForCustomQueries');

    if (is_string($query)) {
        return $query . "&wp_grid_builder=wpgb-content";
    }

    $query['wp_grid_builder'] = 'wpgb-content';

    return $query;
}

/**
 * logic copied from the official "wp-grid-builder-elementor" plugin, at "get_facets()" in "class-facet.php"
 *
 * @return array{text: string, value: string}[]
 */
function getWpgbFacets()
{
    if (!function_exists('wp_grid_builder')) {
        return [];
    }

    global $wpdb;


    /**
     * @var array{id: string, name: string}[]|null
     * @psalm-suppress UndefinedConstant
     * @psalm-suppress MixedPropertyFetch
     * @psalm-suppress MixedMethodCall
     */
    $facets = $wpdb->get_results("SELECT id, name FROM {$wpdb->prefix}wpgb_facets", ARRAY_A);

    if (!$facets) return [];

    return array_map(function ($facet) {
        return ['text' => $facet['name'], 'value' => $facet['id']];
    }, $facets);
}

/**
 * @param Control[] $controls
 * @param \Breakdance\Elements\Element $element
 * @return Control[]
 */
function addControls($controls, $element)
{
    /** @var string[] $supportedElements */
    $supportedElements = apply_filters('breakdance_wpgridbuilder_supported_elements', getElementSlugsThatCanHaveFiltering());

    if (!in_array($element::slug(), $supportedElements, true)) {
        return $controls;
    }

    /**
     * @psalm-suppress PossiblyUndefinedStringArrayOffset
     */
    $paginationIndex = array_search('pagination', array_column($controls['contentSections'], 'slug'));

    if ($paginationIndex) {
        $condition = ['external' => true, 'path' => 'content.wpgridbuilder.enabled', 'operand' => 'is not set', 'value' => ''];

        /** @var Control */
        $controls['contentSections'] = addConditionToControlSection($controls['contentSections'], $condition, $paginationIndex);
    }

    $controls['contentSections'][] = controlSection(
        'wpgridbuilder',
        'GridbuilderWP',
        [
            control('enabled', 'Enable GridbuilderWP', [
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
function disablePaginationWhenEnabled($enabled, $properties)
{
    if ($properties['content']['wpgridbuilder']['enabled'] ?? false) {
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
function addWarningIfSliderIsActive($html, $properties, $elementSlug)
{
    /** @var string[] $supportedElements */
    $supportedElements = apply_filters('breakdance_wpgridbuilder_supported_elements', getElementSlugsThatCanHaveFiltering());

    if (!in_array($elementSlug, $supportedElements, true)) {
        return $html;
    }

    if (
        !($properties['content']['wpgridbuilder']['enabled'] ?? false)
    ) {
        return $html;
    }

    $warning = "<div class='bde-ssr-error'>GridbuilderWP isn't supported with the \"Slider\" layout</div><br>";

    if (($properties['design']['list']['layout'] ?? false) === 'slider') {
        return $warning . $html;
    }

    if (($properties['design']['layout']['layout'] ?? false) === 'slider') {
        return $warning . $html;
    }

    return $html;
}

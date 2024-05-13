<?php
namespace Breakdance\Integrations\FacetPlugins;

// this code applies to either facetWp or WPGB

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;

add_action('breakdance_loaded', function () {
    // enable it if either of them is installed
    if (!function_exists('FWP') && !function_exists('wp_grid_builder')) {
        return;
    }

    add_filter('breakdance_element_css_template', '\Breakdance\Integrations\FacetPlugins\addCssForLoadMoreInShopPage', 69, 2);
    add_filter('breakdance_element_controls', '\Breakdance\Integrations\FacetPlugins\addFacetsControls', 69, 2);
});

/**
 * @param Control[] $controls
 * @param \Breakdance\Elements\Element $element
 * @return Control[]
 */
function addFacetsControls($controls, $element)
{
    /**
     * @psalm-suppress UndefinedClass
     */
    $shopPageSlug = (string) \EssentialElements\Wooshoppage::slug();

    if ($element::slug() !== $shopPageSlug || !isset($controls['designSections'])) {
        return $controls;
    }

    /**
     * @psalm-suppress MixedArrayAssignment
     */
    $controls['designSections'][] = controlSection(
        'facets_integration',
        "Facets",
        [
            control(
                'disable_pagination',
                'Disable Pagination', [
                    'type' => 'toggle',
                ]
            ),
        ],
        ['isExternal' => true]
    );


    /** @var Control[] $controls */
    return $controls;
}

/**
 * @param string $template
 * @param \Breakdance\Elements\Element $element
 * @return mixed|string
 */
function addCssForLoadMoreInShopPage($template, $element)
{
    /**
     * @psalm-suppress UndefinedClass
     */
    $shopPageSlug = (string) \EssentialElements\Wooshoppage::slug();

    if ($element::slug() !== $shopPageSlug) {
        return $template;
    }

    return "$template

{% if design.facets_integration.disable_pagination %}
    %%SELECTOR%% {
        align-items: initial;
    }

    %%SELECTOR%%.breakdance-woocommerce .woocommerce-pagination,
    %%SELECTOR%%.breakdance-woocommerce .woocommerce-ordering,
    %%SELECTOR%%.breakdance-woocommerce .woocommerce-result-count {
        display: none;
    }

    {# move the grid from the ul to the parent, since we're loading more but the elements remain inside the ul #}
    %%SELECTOR%%.breakdance-woocommerce ul.products {
        display: contents;
    }

    {# Copied from _shop.scss for 'ul.products' #}
    %%SELECTOR%%.breakdance-woocommerce {
        display: grid;
        grid-template-columns: repeat(var(--bde-woo-products-list-products-per-row), 1fr);
        gap: var(--bde-woo-products-list-gap);
    }
{% endif %}
    ";
}

<?php

namespace Breakdance\WooCommerce;

use Breakdance\Render\ScriptAndStyleHolder;
use function Breakdance\Render\getDependencies;
use function Breakdance\Themeless\ThemeDisabler\is_theme_disabled;
use function Breakdance\Themeless\ThemeDisabler\is_zero_theme_enabled;

// Remove default Woo Tabs
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs');

// Then replace it with ours.
function addTabsToSingleProduct() {
    \Breakdance\WooCommerce\tabs();
}
add_action('woocommerce_after_single_product_summary', '\Breakdance\WooCommerce\addTabsToSingleProduct', 10);

/**
 * TODO: Extract this somewhere else
 * @param string $type
 * @param string $selector
 * @return void
 * @throws \Exception
 */
function manuallyEnqueueElementDependencies($type, $selector)
{
    $element = \Breakdance\Render\getElementFromNodeType($type);

    $dependencies = getDependencies(
        $element,
        [],
        ['selector' => $selector]
    );

    ScriptAndStyleHolder::getInstance()->append($dependencies);
}

function maybeEnqueueTabsAssets()
{
    // Don't enqueue assets if rendering with breakdance as it's handled automatically.
    if (is_theme_disabled() || is_zero_theme_enabled()) {
        return;
    }

    // No %%SELECTOR%% exists outside Breakdance, so we create a random one,
    // doesn't matter as there is only one tabs element anyways.

    manuallyEnqueueElementDependencies(
        'EssentialElements\\Wooproducttabs',
        '.bde-tabs'
    );
}

/**
 * Custom Woo Tabs
 */
function tabs($propertiesData = [])
{
    maybeEnqueueTabsAssets();

    $i = 1;
    $product_tabs = apply_filters('woocommerce_product_tabs', []);
    $tabs_id = 'woo-tabs';
    $tabs_design = $propertiesData['design']['tabs'] ?? [];

    if (empty($product_tabs)) {
        return;
    }

    foreach ($product_tabs as $key => $product_tab) {
        $custom_tab = $propertiesData['content']['tabs'][$key] ?? null;
        $tabs[] = getTabData($custom_tab, $product_tab);
    }

    echo '<div id="reviews" class="bde-tabs">';
    echo \Breakdance\Elements\AtomV1Tabs\render($tabs_id, $tabs, $tabs_design, []);

    echo '<div class="bde-tabs-content-container">';
    foreach ($product_tabs as $key => $product_tab) {
        echo '<div tabindex="0" role="tabpanel" class="bde-tabs__panel js-panel" id="tab-panel-' . $tabs_id . '-' . $i . '" hidden="" aria-labelledby="tab-' . $tabs_id . '-' . $i . '">';
        echo '<div class="bde-tabs__panel-content breakdance-rich-text-styles">';
        if (isset($product_tab['callback'])) {
            call_user_func($product_tab['callback'], $key, $product_tab);
        }
        echo '</div>';
        echo '</div>';
        $i++;
    }
    echo '</div>';
    do_action('woocommerce_product_after_tabs');
    echo '</div>';
}

/**
 * @param $data
 * @param $productTab
 * @return array
 */
function getTabData($data, $productTab)
{
    $title = $data['title'] ?? $productTab['title'];
    return array('title' => $title, 'icon' => $data['icon'] ?? null);
}

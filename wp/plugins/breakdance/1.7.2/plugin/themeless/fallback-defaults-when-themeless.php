<?php

namespace Breakdance\Themeless;

use Breakdance\Render\ScriptAndStyleHolder;

/*
 * WooCommerce Shortcode Hijacking
 * Wraps each shortcode with the a div and the .breakdance-woocommerce class.
 */
define('WOO_SHORTCODES_TO_HIJACK', [
    'woocommerce_checkout' => 'EssentialElements\Woopagecheckout',
    'woocommerce_my_account' => 'EssentialElements\Woopageaccount',
    'woocommerce_cart' => 'EssentialElements\Woopageshoppingcart',
    'woocommerce_order_tracking' => 'EssentialElements\Woopageordertracking',
    'products' => null,
    'product_categories' => null,
    'product_page' => null,
    'product_category' => null,
    'related_products' => null,
    'add_to_cart' => null,
    'shop_messages' => null
]);

add_filter('do_shortcode_tag', '\Breakdance\Themeless\hijackWooShortcodes', 10, 3);

/**
 * @param string $tag
 * @param string|array $attrs
 * @return bool
 */
function shouldHijack($tag, $attrs)
{
    $shortcodes = array_keys(WOO_SHORTCODES_TO_HIJACK);

    if (is_array($attrs) && in_array('no-hijack', $attrs)) {
        return false;
    }

    return in_array($tag, $shortcodes);
}

/**
 * @param string $output
 * @param string $tag
 * @param string|array $attrs
 * @return mixed
 * @throws \Exception
 */
function hijackWooShortcodes($output, $tag, $attrs = [])
{
    $shouldHijack = shouldHijack($tag, $attrs);

    if (!$shouldHijack) {
        return $output;
    }

    $elementName = WOO_SHORTCODES_TO_HIJACK[$tag];

    if ($elementName) {
        return wrapWithElement($elementName, $output);
    }

    return wrapWithBreakdanceWooCommerceDiv($output);
}

// Utils

/**
 * @param string $html
 * @return string
 */
function wrapWithBreakdanceWooCommerceDiv($html)
{
    return '<div class="breakdance-woocommerce breakdance-full-width">'.$html.'</div>';
}

/**
 * @param string $type
 * @param string $childHtml
 * @param string $classString
 * @return string
 * @throws \Exception
 */
function wrapWithElement($type, $childHtml = '', $classString = '')
{
    $element = \Breakdance\Render\getElementFromNodeType($type);

    $tag = \Breakdance\Render\getHtmlTag($element, []);
    $className = \Breakdance\Elements\getBaseClassNameForBuilderElement($element);
    $additionalClasses = \Breakdance\Render\getAdditionalClassNames($element, []);
    $classes = array_merge([$className, $classString], $additionalClasses);

    return \Breakdance\Render\htmlElement([
        'tag' => $tag,
        'classList' => array_filter($classes),
        'childHtml' => $childHtml,
        'atts' => [],
        'id' => ''
    ]);
}

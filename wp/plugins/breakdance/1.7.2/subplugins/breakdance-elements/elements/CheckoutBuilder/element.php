<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\CheckoutBuilder",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class CheckoutBuilder extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'CreditCardIcon';
    }

    static function tag()
    {
        return 'div';
    }

    static function tagOptions()
    {
        return [];
    }

    static function tagControlPath()
    {
        return false;
    }

    static function name()
    {
        return 'Checkout Builder';
    }

    static function className()
    {
        return 'bde-checkout-builder';
    }

    static function category()
    {
        return 'woocommerce';
    }

    static function badge()
    {
        return ['backgroundColor' => 'var(--brandWooCommerceBackground)', 'textColor' => 'var(--brandWooCommerce)', 'label' => 'Woo'];
    }

    static function slug()
    {
        return get_class();
    }

    static function template()
    {
        return file_get_contents(__DIR__ . '/html.twig');
    }

    static function defaultCss()
    {
        return file_get_contents(__DIR__ . '/default.css');
    }

    static function defaultProperties()
    {
        return false;
    }

    static function defaultChildren()
    {
        return [['slug' => 'EssentialElements\Columns', 'defaultProperties' => null, 'children' => ['0' => ['slug' => 'EssentialElements\Column', 'defaultProperties' => ['design' => ['size' => ['width' => ['unit' => '%', 'number' => 61.37, 'style' => '61.37%']]]], 'children' => ['0' => ['slug' => 'EssentialElements\Heading', 'defaultProperties' => ['content' => ['content' => ['text' => 'Billing details', 'tags' => 'h2']], 'design' => ['spacing' => ['margin_bottom' => ['breakpoint_base' => ['number' => 16, 'unit' => 'px', 'style' => '16px']], 'margin_top' => ['breakpoint_base' => null]], 'typography' => ['typography' => ['custom' => ['customTypography' => ['fontWeight' => ['breakpoint_base' => '400'], 'fontSize' => ['breakpoint_base' => ['number' => 25, 'unit' => 'px', 'style' => '25px']]]]]]]], 'children' => []], '1' => ['slug' => 'EssentialElements\WooCheckoutBillingForm', 'defaultProperties' => ['design' => ['spacing' => ['margin_bottom' => ['breakpoint_base' => ['number' => 30, 'unit' => 'px', 'style' => '30px']]], 'layout' => null]], 'children' => []], '2' => ['slug' => 'EssentialElements\WooCheckoutShippingForm', 'defaultProperties' => ['design' => ['layout' => null, 'spacing' => ['margin_bottom' => ['breakpoint_base' => ['number' => 30, 'unit' => 'px', 'style' => '30px']]]]], 'children' => []], '3' => ['slug' => 'EssentialElements\WooCheckoutPayment', 'defaultProperties' => ['design' => ['layout' => ['sticky' => false, 'borders' => ['border' => ['breakpoint_base' => ['top' => ['width' => ['number' => 0, 'unit' => 'px', 'style' => '0px']], 'bottom' => ['width' => ['number' => 0, 'unit' => 'px', 'style' => '0px']], 'left' => ['width' => ['number' => 0, 'unit' => 'px', 'style' => '0px']], 'right' => ['width' => ['number' => 0, 'unit' => 'px', 'style' => '0px']]]], 'radius' => ['breakpoint_base' => ['all' => ['number' => 16, 'unit' => 'px', 'style' => '16px'], 'topLeft' => ['number' => 16, 'unit' => 'px', 'style' => '16px'], 'topRight' => ['number' => 16, 'unit' => 'px', 'style' => '16px'], 'bottomLeft' => ['number' => 16, 'unit' => 'px', 'style' => '16px'], 'bottomRight' => ['number' => 16, 'unit' => 'px', 'style' => '16px'], 'editMode' => 'all']]], 'payment_info' => ['background' => 'var(--grey-50)', 'padding' => ['padding' => ['breakpoint_base' => ['left' => null]]]], 'padding' => ['padding' => ['breakpoint_base' => ['left' => ['number' => 30, 'unit' => 'px', 'style' => '30px'], 'right' => ['number' => 30, 'unit' => 'px', 'style' => '30px'], 'top' => ['number' => 30, 'unit' => 'px', 'style' => '30px'], 'bottom' => ['number' => 30, 'unit' => 'px', 'style' => '30px']]]]]]], 'children' => []]]], '1' => ['slug' => 'EssentialElements\Column', 'defaultProperties' => ['design' => ['size' => ['width' => ['unit' => '%', 'number' => 38.63, 'style' => '38.63%']]]], 'children' => ['0' => ['slug' => 'EssentialElements\Heading', 'defaultProperties' => ['content' => ['content' => ['text' => 'Order Summary', 'tags' => 'h2']], 'design' => ['spacing' => ['margin_bottom' => ['breakpoint_base' => ['number' => 16, 'unit' => 'px', 'style' => '16px']], 'margin_top' => ['breakpoint_base' => null]], 'typography' => ['typography' => ['custom' => ['customTypography' => ['fontWeight' => ['breakpoint_base' => '400'], 'fontSize' => ['breakpoint_base' => ['number' => 25, 'unit' => 'px', 'style' => '25px']]]]]]]], 'children' => []], '1' => ['slug' => 'EssentialElements\WooCheckoutOrderReview', 'defaultProperties' => ['design' => ['spacing' => ['margin_bottom' => ['breakpoint_base' => ['number' => 0, 'unit' => 'px', 'style' => '0px']]], 'layout' => ['background' => null]]], 'children' => []], '2' => ['slug' => 'EssentialElements\IconList', 'defaultProperties' => ['content' => ['content' => ['list' => ['0' => ['text' => 'Free Shipping on orders over $100', 'icon' => ['slug' => 'icon-cart', 'name' => 'cart', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" id="icon-cart" viewBox="0 0 32 32">
<path d="M12 29c0 1.657-1.343 3-3 3s-3-1.343-3-3c0-1.657 1.343-3 3-3s3 1.343 3 3z"/>
<path d="M32 29c0 1.657-1.343 3-3 3s-3-1.343-3-3c0-1.657 1.343-3 3-3s3 1.343 3 3z"/>
<path d="M32 16v-12h-24c0-1.105-0.895-2-2-2h-6v2h4l1.502 12.877c-0.915 0.733-1.502 1.859-1.502 3.123 0 2.209 1.791 4 4 4h24v-2h-24c-1.105 0-2-0.895-2-2 0-0.007 0-0.014 0-0.020l26-3.98z"/>
</svg>']], '1' => ['icon' => ['slug' => 'icon-truck.', 'name' => 'truck', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M624 352h-16V243.9c0-12.7-5.1-24.9-14.1-33.9L494 110.1c-9-9-21.2-14.1-33.9-14.1H416V48c0-26.5-21.5-48-48-48H48C21.5 0 0 21.5 0 48v320c0 26.5 21.5 48 48 48h16c0 53 43 96 96 96s96-43 96-96h128c0 53 43 96 96 96s96-43 96-96h48c8.8 0 16-7.2 16-16v-32c0-8.8-7.2-16-16-16zM160 464c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48zm320 0c-26.5 0-48-21.5-48-48s21.5-48 48-48 48 21.5 48 48-21.5 48-48 48zm80-208H416V144h44.1l99.9 99.9V256z"/></svg>'], 'text' => 'Free returns within 30 days of purchase'], '2' => ['icon' => ['slug' => 'icon-battery-full.', 'name' => 'battery full', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M544 160v64h32v64h-32v64H64V160h480m16-64H48c-26.51 0-48 21.49-48 48v224c0 26.51 21.49 48 48 48h512c26.51 0 48-21.49 48-48v-16h8c13.255 0 24-10.745 24-24V184c0-13.255-10.745-24-24-24h-8v-16c0-26.51-21.49-48-48-48zm-48 96H96v128h416V192z"/></svg>'], 'text' => '1-year Warranty included', 'link' => null]], 'positive_icon' => [], 'negative_icon' => []]], 'design' => ['icon' => ['size' => ['breakpoint_base' => ['number' => 16, 'unit' => 'px', 'style' => '16px']], 'color' => null, 'background' => true, 'padding' => ['breakpoint_base' => ['number' => 12, 'unit' => 'px', 'style' => '12px']], 'radius' => ['breakpoint_base' => ['number' => 999, 'unit' => 'px', 'style' => '999px']], 'fill' => null, 'color_hover' => null, 'fill_hover' => null], 'layout' => null, 'typography' => ['color' => null, 'color_hover' => null, 'typography' => ['custom' => ['customTypography' => ['fontSize' => ['breakpoint_base' => ['number' => 16, 'unit' => 'px', 'style' => '16px']], 'fontWeight' => ['breakpoint_base' => '500']]]]], 'spacing' => ['margin_bottom' => null, 'margin_top' => ['breakpoint_base' => ['number' => 60, 'unit' => 'px', 'style' => '60px']]]]], 'children' => []], '3' => ['slug' => 'EssentialElements\SimpleTestimonial', 'defaultProperties' => ['design' => ['layout' => ['width' => ['breakpoint_base' => ['number' => 520, 'unit' => 'px', 'style' => '520px']], 'alignment' => ['breakpoint_base' => 'left']], 'image' => ['style' => 'outlined-circle', 'size' => ['breakpoint_base' => ['number' => 100, 'unit' => 'px', 'style' => '100px']]], 'quotes' => ['style' => 'quotes-5', 'color' => '#F5E9FFFF', 'size' => ['breakpoint_base' => ['number' => 31, 'unit' => 'px', 'style' => '31px']], 'horizontal_offset' => ['number' => 0, 'unit' => 'px', 'style' => 0], 'vertical_offset' => ['number' => 5, 'unit' => 'px', 'style' => '5px']], 'spacing' => ['below_author' => ['breakpoint_base' => ['number' => 15, 'unit' => 'px', 'style' => '15px']], 'below_author_info' => null, 'below_testimonial' => ['breakpoint_base' => ['number' => 25, 'unit' => 'px', 'style' => '25px']], 'below_image' => ['breakpoint_base' => ['number' => 25, 'unit' => 'px', 'style' => '25px']], 'wrapper' => ['margin_top' => ['breakpoint_base' => ['number' => 60, 'unit' => 'px', 'style' => '60px']]]], 'typography' => ['testimonial' => ['typography' => ['custom' => ['customTypography' => ['fontSize' => ['breakpoint_base' => ['number' => 20, 'unit' => 'px', 'style' => '20px']]]]]]]], 'content' => ['content' => ['testimonial' => 'Breakdance is flexible, powerful, and easy-to-use. It\'s everything I need to build a website.', 'author' => 'Louis Reingold', 'author_info' => 'CEO @ Breakdance', 'image' => ['id' => -1, 'type' => 'external_image', 'url' => 'https://louisreingold.com/louis-reingold.jpg', 'alt' => 'world\'s best human', 'caption' => ''], 'image_dynamic_meta' => null]]], 'children' => []]]]]]];
    }

    static function cssTemplate()
    {
        $template = file_get_contents(__DIR__ . '/css.twig');
        return $template;
    }

    static function designControls()
    {
        return [];
    }

    static function contentControls()
    {
        return [c(
        "content",
        "Content",
        [c(
        "elements",
        "Elements",
        [],
        ['type' => 'add_registered_children', 'layout' => 'vertical'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'vertical'],
        false,
        false,
        [],
      )];
    }

    static function settingsControls()
    {
        return [];
    }

    static function dependencies()
    {
        return false;
    }

    static function settings()
    {
        return ['requiredPlugins' => ['0' => 'WooCommerce'], 'proOnly' => true];
    }

    static function addPanelRules()
    {
        return false;
    }

    static public function actions()
    {
        return false;
    }

    static function nestingRule()
    {
        return ["type" => "container",   "notAllowedWhenNodeTypeIsPresentInTree" => ['EssentialElements\CheckoutBuilder', 'EssentialElements\Woopagecheckout'],];
    }

    static function spacingBars()
    {
        return false;
    }

    static function attributes()
    {
        return false;
    }

    static function experimental()
    {
        return false;
    }

    static function order()
    {
        return 96;
    }

    static function dynamicPropertyPaths()
    {
        return false;
    }

    static function additionalClasses()
    {
        return [['name' => 'breakdance-woocommerce', 'template' => 'yes']];
    }

    static function projectManagement()
    {
        return false;
    }

    static function propertyPathsToWhitelistInFlatProps()
    {
        return false;
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}

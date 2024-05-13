<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\Woopagecheckout",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class Woopagecheckout extends \Breakdance\Elements\Element
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
        return 'Checkout Page';
    }

    static function className()
    {
        return 'bde-woopagecheckout';
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
        return false;
    }

    static function cssTemplate()
    {
        $template = file_get_contents(__DIR__ . '/css.twig');
        return $template;
    }

    static function designControls()
    {
        return [c(
        "layout",
        "Layout",
        [c(
        "single_column",
        "Single Column",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "stack_vertically_at",
        "Stack Vertically At",
        [],
        ['type' => 'breakpoint_dropdown', 'layout' => 'inline', 'condition' => ['path' => 'design.layout.single_column', 'operand' => 'is not set', 'value' => '']],
        false,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\woo-checkout-coupon-design",
      "Coupon",
      "coupon",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\woo-checkout-billing-design",
      "Billing Details",
      "billing_details",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\woo-checkout-shipping-design",
      "Shipping Address",
      "shipping_address",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\woo-checkout-additional-design",
      "Additional Info",
      "additional_info",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\woo-checkout-order-review-design",
      "Your Order",
      "your_order",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\woo-checkout-totals-design",
      "Totals",
      "totals",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\woo-checkout-payment-design",
      "Payment",
      "payment",
       ['type' => 'popout']
     ), c(
        "typography",
        "Typography",
        [getPresetSection(
      "EssentialElements\\typography_with_effects",
      "Headings",
      "headings",
       ['type' => 'popout']
     ), c(
        "your_order",
        "Your Order",
        [getPresetSection(
      "EssentialElements\\typography",
      "Table Heading",
      "table_heading",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_effects",
      "Labels",
      "labels",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_effects",
      "Prices",
      "prices",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_effects",
      "Total Label",
      "total_label",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_effects",
      "Total Value",
      "total_value",
       ['type' => 'popout']
     )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\typography_with_align",
      "Payment",
      "payment",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_align",
      "Payment Info",
      "payment_info",
       ['type' => 'popout']
     )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "spacing",
        "Spacing",
        [getPresetSection(
      "EssentialElements\\spacing_margin_y",
      "Container",
      "container",
       ['type' => 'popout']
     ), c(
        "gap",
        "Gap",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "below_headings",
        "Below Headings",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "advanced",
        "Advanced",
        [getPresetSection(
      "EssentialElements\\WooGlobalStylerOverride",
      "Override Global Styles",
      "override_global_styles",
       ['type' => 'popout']
     ), c(
        "force_full_width_form_fields",
        "Force Full Width Form Fields",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      )];
    }

    static function contentControls()
    {
        return [];
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
        return ['thirdParty' => true, 'requiredPlugins' => ['0' => 'WooCommerce'], 'proOnly' => true];
    }

    static function addPanelRules()
    {
        return false;
    }

    static public function actions()
    {
        return [

'onMountedElement' => [['script' => 'function shipToDifferentAddress() {
  jQuery(\'div.shipping_address\').hide();
  
  if (jQuery(this).is(\':checked\')) {
    jQuery(\'div.shipping_address\').slideDown();
  }
};

const $checkout = jQuery(\'form.checkout\');

shipToDifferentAddress();
$checkout.on(\'change\', \'#ship-to-different-address input\', shipToDifferentAddress);',
],],];
    }

    static function nestingRule()
    {
        return ["type" => "final",   "notAllowedWhenNodeTypeIsPresentInTree" => ['EssentialElements\CheckoutBuilder', 'EssentialElements\Woopagecheckout'],];
    }

    static function spacingBars()
    {
        return ['0' => ['location' => 'outside-top', 'cssProperty' => 'margin-top', 'affectedPropertyPath' => 'design.spacing.container.margin_top.%%BREAKPOINT%%'], '1' => ['location' => 'outside-bottom', 'cssProperty' => 'margin-bottom', 'affectedPropertyPath' => 'design.spacing.container.margin_bottom.%%BREAKPOINT%%']];
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
        return 95;
    }

    static function dynamicPropertyPaths()
    {
        return [];
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
        return ['design.layout.stack_vertically_at'];
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return ['none'];
    }
}

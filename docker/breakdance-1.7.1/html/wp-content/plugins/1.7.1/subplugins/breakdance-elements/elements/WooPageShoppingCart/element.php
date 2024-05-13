<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\Woopageshoppingcart",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class Woopageshoppingcart extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'CartShoppingIcon';
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
        return 'Cart Page';
    }

    static function className()
    {
        return 'bde-cart';
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
        "totals_position",
        "Totals Position",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'Top Left', 'value' => 'top-left'], '1' => ['text' => 'Top Right', 'value' => 'top-right'], '2' => ['value' => 'bottom-left', 'text' => 'Bottom Left'], '3' => ['text' => 'Bottom Right', 'value' => 'bottom-right']], 'buttonBarOptions' => ['size' => 'big']],
        false,
        false,
        [],
      ), c(
        "stack_vertically_at",
        "Stack Vertically At",
        [],
        ['type' => 'breakpoint_dropdown', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "sticky_totals",
        "Sticky Totals",
        [],
        ['type' => 'toggle', 'layout' => 'inline', 'condition' => ['path' => 'design.layout.totals_position', 'operand' => 'is one of', 'value' => ['0' => 'top-right', '1' => 'top-left']]],
        false,
        false,
        [],
      ), c(
        "sticky_offset",
        "Sticky Offset",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => 'design.layout.sticky_totals', 'operand' => 'is set', 'value' => '']],
        false,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\woo-cart-contents-design",
      "Contents",
      "contents",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\woo-cart-totals-design",
      "Totals",
      "totals",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\woo-cart-cross-sells-design",
      "Cross Sells",
      "cross_sells",
       ['type' => 'popout']
     ), c(
        "spacing",
        "Spacing",
        [getPresetSection(
      "EssentialElements\\spacing_margin_y",
      "Container",
      "container",
       ['type' => 'popout']
     ), c(
        "after_notification",
        "After Notification",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "after_cart",
        "After Cart",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "before_cross_sells",
        "Before Cross Sells",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "between_columns",
        "Between Columns",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
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
        return false;
    }

    static function nestingRule()
    {
        return ["type" => "final",   ];
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
        return 90;
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
        return ['design.layout.stack_vertically_at', 'design.cross_sells.hide_at_breakpoint'];
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return ['design.cross_sells.disable', 'design.cross_sells.elements.image.include', 'design.cross_sells.elements.title.include', 'design.cross_sells.elements.price.include', 'design.cross_sells.elements.rating.include', 'design.cross_sells.elements.sale_badge.include', 'design.cross_sells.elements.excerpt.include', 'design.cross_sells.elements.categories.include', 'design.cross_sells.elements.button.include'];
    }
}

<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\Wooshoppage",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class Wooshoppage extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'ShopIcon';
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
        return 'Shop Page';
    }

    static function className()
    {
        return 'bde-wooshoppage';
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
        return ['design' => ['products_list' => ['layout' => ['products_per_row' => ['breakpoint_phone_landscape' => 2], 'between_products' => ['breakpoint_phone_landscape' => ['number' => 20, 'unit' => 'px', 'style' => '20px']]]]]];
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
        "products_list",
        "Products List",
        [getPresetSection(
      "EssentialElements\\wooProductsListElements",
      "Elements",
      "elements",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\wooProductsListLayout",
      "Layout",
      "layout",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\wooProductWrapperDesign",
      "Product Wrapper",
      "product_wrapper",
       ['type' => 'popout']
     )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "result_count",
        "Result Count",
        [getPresetSection(
      "EssentialElements\\typography_with_effects",
      "Typography",
      "typography",
       ['type' => 'popout']
     )],
        ['type' => 'section', 'condition' => ['0' => ['0' => ['path' => 'design.facets_integration.disable_pagination', 'operand' => 'is not set', 'value' => '']]]],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\pagination",
      "Pagination",
      "pagination",
       ['condition' => ['0' => ['0' => ['path' => 'design.facets_integration.disable_pagination', 'operand' => 'is not set', 'value' => '']]], 'type' => 'popout']
     ), c(
        "spacing",
        "Spacing",
        [c(
        "above_products",
        "Above Products",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 1, 'max' => 200], 'condition' => ['0' => ['0' => ['path' => 'design.facets_integration.disable_pagination', 'operand' => 'is not set', 'value' => '']]]],
        true,
        false,
        [],
      ), c(
        "above_pagination",
        "Above Pagination",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 1, 'max' => 200], 'condition' => ['0' => ['0' => ['path' => 'design.facets_integration.disable_pagination', 'operand' => 'is not set', 'value' => '']]]],
        true,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\spacing_margin_y",
      "Container",
      "container",
       ['type' => 'popout']
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
        return 80;
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
        return ['design.products_list.elements.image.include', 'design.products_list.elements.title.include', 'design.products_list.elements.price.include', 'design.products_list.elements.rating.include', 'design.products_list.elements.sale_badge.include', 'design.products_list.elements.excerpt.include', 'design.products_list.elements.categories.include', 'design.products_list.elements.button.include', 'design.products_list.elements.quantity_input.include', 'design.products_list.elements.custom_areas.areas'];
    }
}

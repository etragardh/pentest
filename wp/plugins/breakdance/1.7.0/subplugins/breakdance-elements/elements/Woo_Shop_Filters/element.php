<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\WooShopFilters",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class WooShopFilters extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'SlidersIcon';
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
        return 'Shop Filters';
    }

    static function className()
    {
        return 'bde-woo-shop-filters';
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
        return ['content' => ['filters' => ['widgets' => ['0' => ['widget' => 'active_filters', 'active_filters' => ['title' => 'Active Filters']], '1' => ['widget' => 'price_filter', 'price_filter' => ['title' => 'Price Filter']], '2' => ['widget' => 'rating_filter', 'rating_filter' => ['title' => 'Average Rating']], '3' => ['widget' => 'attribute_filter', 'attribute_filter' => ['display_type' => 'list', 'query_type' => 'or', 'title' => 'Filter by Attribute']]]]], 'design' => ['spacing' => null]];
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
        "chips",
        "Chips",
        [c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        true,
        [],
      ), getPresetSection(
      "EssentialElements\\spacing_padding_all",
      "Padding",
      "padding",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_hoverable_color",
      "Typography",
      "typography",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\borders",
      "Borders",
      "borders",
       ['type' => 'popout']
     ), c(
        "hover_border",
        "Hover Border",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "hover_shadow",
        "Hover Shadow",
        [],
        ['type' => 'shadow', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "space_between",
        "Space Between",
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
        "typography",
        "Typography",
        [getPresetSection(
      "EssentialElements\\typography",
      "Titles",
      "titles",
       ['type' => 'popout']
     )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "price_filter",
        "Price Filter",
        [c(
        "bar",
        "Bar",
        [c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "selection",
        "Selection",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "height",
        "Height",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 2, 'max' => 12, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "handle",
        "Handle",
        [c(
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 12, 'max' => 48, 'step' => 1], 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px']],
        true,
        false,
        [],
      ), c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\borders",
      "Borders",
      "borders",
       ['type' => 'popout']
     ), c(
        "hover_border",
        "Hover Border",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "hover_shadow",
        "Hover Shadow",
        [],
        ['type' => 'shadow', 'layout' => 'vertical'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\AtomV1CustomButtonDesign",
      "Button",
      "button",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography",
      "Label",
      "label",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography",
      "Value",
      "value",
       ['type' => 'popout']
     )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "rating_filter",
        "Rating Filter",
        [c(
        "star_color",
        "Star Color",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "star_size",
        "Star Size",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 12, 'max' => 40, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "between_stars",
        "Between Stars",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 1, 'max' => 24, 'step' => 1]],
        true,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "attribute_filter",
        "Attribute Filter",
        [getPresetSection(
      "EssentialElements\\AtomV1CustomButtonDesign",
      "Button",
      "button",
       ['type' => 'popout']
     )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "spacing",
        "Spacing",
        [c(
        "after_titles",
        "After Titles",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 60, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "between_filters",
        "Between Filters",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 80, 'step' => 1]],
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
        return [getPresetSection(
      "EssentialElements\\shop_filters",
      "Filters",
      "filters",
       ['type' => 'popout']
     )];
    }

    static function settingsControls()
    {
        return [];
    }

    static function dependencies()
    {
        return ['0' =>  ['title' => 'Select2 for Builder','frontendCondition' => 'return false;','inlineScripts' => ['jQuery(function($) {
  if ( jQuery().selectWoo ) {
    jQuery( \'select.woocommerce-widget-layered-nav-dropdown\' ).selectWoo( {
      placeholder: \'Any\',
      minimumResultsForSearch: 5,
      width: \'100%\',
      allowClear: false,
      language: {
        noResults: function() {
          return \'No matches found\';
        }
      }
    } );
  }
});'],'builderCondition' => 'return true;',],];
    }

    static function settings()
    {
        return ['requiredPlugins' => ['0' => 'WooCommerce'], 'dependsOnGlobalScripts' => true, 'proOnly' => true];
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
        return 5000;
    }

    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'string', 'path' => 'content.filters.widgets[].active_filters.title'], '1' => ['accepts' => 'string', 'path' => 'content.filters.widgets[].price_filter.title'], '2' => ['accepts' => 'string', 'path' => 'content.filters.widgets[].rating_filter.title'], '3' => ['accepts' => 'string', 'path' => 'content.filters.widgets[].attribute_filter.title']];
    }

    static function additionalClasses()
    {
        return false;
    }

    static function projectManagement()
    {
        return false;
    }

    static function propertyPathsToWhitelistInFlatProps()
    {
        return ['design.price_filter.button.custom.size.full_width_at', 'design.price_filter.button.style', 'design.price_filter.button.styles.size.full_width_at', 'design.price_filter.button.styles', 'design.attribute_filter.button.styles.size.full_width_at', 'design.attribute_filter.button.styles'];
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return ['content', 'content.filters'];
    }
}

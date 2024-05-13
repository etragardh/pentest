<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\Productbuilder",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class Productbuilder extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'BoxOpenIcon';
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
        return 'ProductBuilder';
    }

    static function className()
    {
        return 'bde-productbuilder';
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
        return [['slug' => 'EssentialElements\Columns', 'children' => ['0' => ['slug' => 'EssentialElements\Column', 'children' => ['0' => ['slug' => 'EssentialElements\Wooproductimages']], 'defaultProperties' => ['design' => ['size' => ['width' => ['number' => 50, 'unit' => '%', 'style' => '50%']]]]], '1' => ['slug' => 'EssentialElements\Column', 'children' => ['0' => ['slug' => 'EssentialElements\WooProductTitle', 'defaultProperties' => ['content' => ['content' => ['text' => 'This is a heading.']], 'design' => ['typography' => ['typography' => ['custom' => ['customTypography' => ['fontSize' => ['breakpoint_base' => ['number' => 32, 'unit' => 'px', 'style' => '32px']]]]]], 'spacing' => ['margin_bottom' => ['breakpoint_base' => ['number' => 24, 'unit' => 'px', 'style' => '24px']]]]]], '1' => ['slug' => 'EssentialElements\Wooproductrating', 'defaultProperties' => ['design' => ['spacing' => ['container' => ['margin_bottom' => ['breakpoint_base' => ['number' => 16, 'unit' => 'px', 'style' => '16px']]], 'after_stars' => null], 'stars' => ['size' => ['number' => 18, 'unit' => 'px', 'style' => '18px']]]]], '2' => ['slug' => 'EssentialElements\Wooproductprice', 'defaultProperties' => ['design' => ['spacing' => ['margin_bottom' => ['breakpoint_base' => ['number' => 16, 'unit' => 'px', 'style' => '16px']]]]]], '3' => ['slug' => 'EssentialElements\ProductExcerpt', 'defaultProperties' => ['content' => ['content' => ['text' => '<h2>Rich Text</h2><p>This is my rich text.</p><h3>I am a subheading</h3><p>This is <strong>more</strong> rich text.</p><ul><li><p>I am a list</p></li><li><p>Lists are cool</p></li></ul>']], 'design' => ['spacing' => ['wrapper' => ['margin_bottom' => ['breakpoint_base' => ['number' => 16, 'unit' => 'px', 'style' => '16px']]]], 'typography' => ['default' => ['color' => ['breakpoint_base' => '#787e8b']]]]]], '4' => ['slug' => 'EssentialElements\Wooproductcartbutton', 'defaultProperties' => ['design' => ['links' => ['underline' => true], 'spacing' => ['container' => ['margin_bottom' => ['breakpoint_base' => ['number' => 16, 'unit' => 'px', 'style' => '16px']]]]]]], '5' => ['slug' => 'EssentialElements\Wooproductmeta', 'defaultProperties' => ['design' => ['container' => ['layout' => ['breakpoint_base' => 'inline']]]]]], 'defaultProperties' => ['design' => ['size' => ['width' => ['number' => 50, 'unit' => '%', 'style' => '50%']]]]]], 'defaultProperties' => ['design' => ['spacing' => ['container' => ['margin_bottom' => ['breakpoint_base' => ['number' => 50, 'unit' => 'px', 'style' => '50px']]]]]]], ['slug' => 'EssentialElements\Wooproducttabs']];
    }

    static function cssTemplate()
    {
        $template = file_get_contents(__DIR__ . '/css.twig');
        return $template;
    }

    static function designControls()
    {
        return [getPresetSection(
      "EssentialElements\\spacing_margin_y",
      "Spacing",
      "spacing",
       ['type' => 'popout']
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
        return [c(
        "content",
        "Content",
        [c(
        "product",
        "Product",
        [],
        ['type' => 'post_chooser', 'layout' => 'vertical', 'postChooserOptions' => ['multiple' => false, 'showThumbnails' => true, 'postType' => 'product']],
        false,
        false,
        [],
      ), c(
        "add_elements",
        "Add Elements",
        [],
        ['type' => 'add_registered_children', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "disable_woo_id",
        "Disable Woo ID",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
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
        return ['requiredPlugins' => ['0' => 'WooCommerce'], 'sharePropsWithSSRChildren' => true, 'proOnly' => true];
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
        return ["type" => "container-excluding-self",   ];
    }

    static function spacingBars()
    {
        return ['0' => ['affectedPropertyPath' => 'design.spacing.margin_top.%%BREAKPOINT%%', 'cssProperty' => 'margin-top', 'location' => 'outside-top'], '1' => ['affectedPropertyPath' => 'design.spacing.margin_bottom.%%BREAKPOINT%%', 'cssProperty' => 'margin-bottom', 'location' => 'outside-bottom']];
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
        return 51;
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
        return ['content'];
    }
}

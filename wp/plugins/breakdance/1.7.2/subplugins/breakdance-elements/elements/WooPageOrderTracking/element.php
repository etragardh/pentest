<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\Woopageordertracking", 
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class Woopageordertracking extends \Breakdance\Elements\Element
{
    static function uiIcon() 
    {
        return '<svg aria-hidden="true" focusable="false"   class="svg-inline--fa fa-truck" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="currentColor" d="M64 416C28.65 416 0 387.3 0 352V64C0 28.65 28.65 0 64 0H352C387.3 0 416 28.65 416 64V96H481.3C495.3 96 508.6 102.1 517.7 112.8L596.4 204.6C603.9 213.3 608 224.4 608 235.8V384H624C632.8 384 640 391.2 640 400C640 408.8 632.8 416 624 416H576C576 469 533 512 480 512C426.1 512 384 469 384 416H255.1C255.1 469 213 512 159.1 512C106.1 512 63.1 469 63.1 416H64zM32 64V352C32 369.7 46.33 384 64 384H69.46C82.64 346.7 118.2 320 160 320C201.8 320 237.4 346.7 250.5 384H384V64C384 46.33 369.7 32 352 32H64C46.33 32 32 46.33 32 64zM570.9 224L493.4 133.6C490.4 130 485.1 128 481.3 128H416V224H570.9zM416 256V344.4C432.1 329.2 455.4 320 480 320C521.8 320 557.4 346.7 570.5 384H576V256H416zM160 352C124.7 352 96 380.7 96 416C96 451.3 124.7 480 160 480C195.3 480 224 451.3 224 416C224 380.7 195.3 352 160 352zM480 480C515.3 480 544 451.3 544 416C544 380.7 515.3 352 480 352C444.7 352 416 380.7 416 416C416 451.3 444.7 480 480 480z"></path></svg>';
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
        return 'Order Tracking Page';
    }
    
    static function className()
    {
        return 'bde-woopageordertracking';
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
        "container",
        "Container",
        [c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 240, 'max' => 1080, 'step' => 1]],
        true,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "form",
        "Form",
        [getPresetSection(
      "EssentialElements\\background",
      "Background", 
      "background", 
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\borders",
      "Borders", 
      "borders", 
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\spacing_padding_all",
      "Padding", 
      "padding", 
       ['type' => 'popout']
     )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), getPresetSection(
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
        return ['0' => ['location' => 'outside-top', 'cssProperty' => 'margin-top', 'affectedPropertyPath' => 'design.spacing.margin_top.%%BREAKPOINT%%'], '1' => ['location' => 'outside-bottom', 'cssProperty' => 'margin-bottom', 'affectedPropertyPath' => 'design.spacing.margin_bottom.%%BREAKPOINT%%']];
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
        return 105;
    }
    
    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'string', 'path' => 'design.form.background.layers[].image']];
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
        return ['none'];
    }
}

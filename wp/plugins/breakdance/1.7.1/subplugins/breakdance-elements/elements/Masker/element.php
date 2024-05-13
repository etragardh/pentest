<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\Masker", 
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class Masker extends \Breakdance\Elements\Element
{
    static function uiIcon() 
    {
        return 'StarIcon';
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
        return 'Masker';
    }
    
    static function className()
    {
        return 'bde-masker';
    }

    static function category()
    {
        return 'blocks';
    }

    static function badge()
    {
        return false;
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
        "mask",
        "Mask",
        [c(
        "shape",
        "Shape",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'Donut', 'label' => 'Label', 'value' => 'donut'], '1' => ['text' => 'Tv', 'value' => 'tv'], '2' => ['text' => 'Waves', 'value' => 'waves'], '3' => ['text' => 'Waves 2', 'value' => 'waves2'], '4' => ['text' => 'Blob', 'value' => 'blob'], '5' => ['text' => 'Star 1', 'value' => 'star1'], '6' => ['text' => 'Star 2', 'value' => 'star2'], '7' => ['text' => 'Star 3', 'value' => 'star3'], '8' => ['text' => 'Star 4', 'value' => 'star4'], '9' => ['text' => 'Stripes', 'value' => 'stripes'], '10' => ['text' => 'Pill', 'value' => 'pill'], '11' => ['text' => 'Custom', 'value' => 'custom']]],
        false,
        false,
        [],
      ), c(
        "custom_shape",
        "Custom Shape",
        [],
        ['type' => 'wpmedia', 'layout' => 'vertical', 'condition' => ['path' => 'design.mask.shape', 'operand' => 'equals', 'value' => 'custom']],
        false,
        false,
        [],
      ), c(
        "size",
        "Size",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'Contain', 'value' => 'contain'], '1' => ['value' => 'cover', 'text' => 'Cover'], '2' => ['text' => 'Custom', 'value' => 'custom']]],
        true,
        false,
        [],
      ), c(
        "custom_size",
        "Custom Size",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => 'design.mask.size', 'operand' => 'equals', 'value' => 'custom'], 'unitOptions' => ['types' => ['0' => 'px', '1' => '%'], 'defaultType' => '%']],
        false,
        false,
        [],
      ), c(
        "position",
        "Position",
        [],
        ['type' => 'focus_point', 'layout' => 'vertical'],
        true,
        false,
        [],
      ), c(
        "repeat",
        "Repeat",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'no-repeat', 'text' => 'no-repeat'], '1' => ['text' => 'repeat', 'value' => 'repeat'], '2' => ['text' => 'repeat-x', 'value' => 'repeat-x'], '3' => ['text' => 'repeat-y', 'value' => 'repeat-y'], '4' => ['text' => 'space', 'value' => 'space'], '5' => ['text' => 'round', 'value' => 'round']]],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "container",
        "Container",
        [c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "height",
        "Height",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline', 'colorOptions' => ['type' => 'solidAndGradient']],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\spacing_padding_all",
      "Padding", 
      "padding", 
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\borders",
      "Borders", 
      "borders", 
       ['type' => 'popout']
     )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\simpleLayout",
      "Layout", 
      "layout", 
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\spacing_margin_y",
      "Spacing", 
      "spacing", 
       ['type' => 'popout']
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
        return ['proOnly' => true];
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
        return ["type" => "container",   ];
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
        return 3900;
    }
    
    static function dynamicPropertyPaths()
    {
        return false;
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
        return ['design.layout.horizontal.vertical_at'];
    }    
    
    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}

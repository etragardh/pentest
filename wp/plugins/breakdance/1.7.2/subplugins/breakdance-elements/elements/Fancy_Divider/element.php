<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\FancyDivider", 
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class FancyDivider extends \Breakdance\Elements\Element
{
    static function uiIcon() 
    {
        return 'GripLinesIcon';
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
        return 'Fancy Divider';
    }
    
    static function className()
    {
        return 'bde-fancy-divider';
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
        "divider",
        "Divider",
        [c(
        "style",
        "Style",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'dotted', 'text' => 'Dotted'], '1' => ['text' => 'Dashed', 'value' => 'dashed'], '2' => ['text' => 'Solid', 'value' => 'solid'], '3' => ['text' => 'Double', 'value' => 'double'], '4' => ['text' => 'Waves', 'value' => 'waves'], '5' => ['text' => 'Zigzag', 'value' => 'zigzag'], '6' => ['text' => 'Pills', 'value' => 'pills'], '7' => ['text' => 'Stars', 'value' => 'stars'], '8' => ['text' => 'Stripes', 'value' => 'stripes'], '9' => ['text' => 'Leafs', 'value' => 'leaf'], '10' => ['text' => 'Smiley', 'value' => 'smiley'], '11' => ['text' => 'Arrows', 'value' => 'arrow'], '12' => ['text' => 'Pluses', 'value' => 'plus'], '13' => ['text' => 'Triangles', 'value' => 'triangle'], '14' => ['text' => 'Doodles', 'value' => 'doodle'], '15' => ['text' => 'Dots', 'value' => 'dots'], '16' => ['text' => 'Rectangles', 'value' => 'rectangles'], '17' => ['text' => 'Half Circle', 'value' => 'half-circle']]],
        false,
        false,
        [],
      ), c(
        "pattern",
        "Pattern",
        [c(
        "color",
        "Color",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 1, 'max' => 100, 'step' => 1]],
        false,
        false,
        [],
      ), c(
        "amount",
        "Amount",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 1, 'max' => 100, 'step' => 1], 'condition' => ['path' => 'design.divider.style', 'operand' => 'is none of', 'value' => ['0' => 'dotted', '1' => 'dashed', '2' => 'solid', '3' => 'double']]],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "width",
        "Width",
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
        "content",
        "Content",
        [c(
        "space_around",
        "Space Around",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 200, 'step' => 1]],
        false,
        false,
        [],
      ), c(
        "position",
        "Position",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'flex-start', 'text' => 'left', 'icon' => 'FlexAlignLeftIcon'], '1' => ['text' => 'center', 'icon' => 'FlexAlignCenterHorizontalIcon', 'value' => 'center'], '2' => ['icon' => 'FlexAlignRightIcon', 'text' => 'Right', 'value' => 'flex-end']]],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\typography",
      "Typography", 
      "typography", 
       ['type' => 'popout']
     ), c(
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => 'content.divider.content', 'operand' => 'equals', 'value' => 'icon']],
        false,
        false,
        [],
      ), c(
        "color",
        "Color",
        [],
        ['type' => 'color', 'layout' => 'inline', 'condition' => ['path' => 'content.divider.content', 'operand' => 'equals', 'value' => 'icon']],
        false,
        false,
        [],
      ), c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 10, 'max' => 400, 'step' => 1], 'condition' => ['path' => 'content.divider.content', 'operand' => 'equals', 'value' => 'image']],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\borders",
      "Borders", 
      "borders", 
       ['type' => 'popout']
     )],
        ['type' => 'section', 'condition' => ['path' => 'content.divider.content', 'operand' => 'is set', 'value' => 'image']],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\spacing_margin_y",
      "Spacing", 
      "spacing", 
       ['type' => 'popout']
     )];
    }
    
    static function contentControls()
    {
        return [c(
        "divider",
        "Divider",
        [c(
        "content",
        "Content",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['text' => 'Icon', 'value' => 'icon'], '1' => ['text' => 'Text', 'value' => 'text']], 'buttonBarOptions' => ['size' => 'small', 'layout' => 'default']],
        false,
        false,
        [],
      ), c(
        "icon",
        "Icon",
        [],
        ['type' => 'icon', 'layout' => 'vertical', 'condition' => ['path' => 'content.divider.content', 'operand' => 'equals', 'value' => 'icon']],
        false,
        false,
        [],
      ), c(
        "text",
        "Text",
        [],
        ['type' => 'text', 'layout' => 'vertical', 'condition' => ['path' => 'content.divider.content', 'operand' => 'equals', 'value' => 'text']],
        false,
        false,
        [],
      ), c(
        "image",
        "Image",
        [],
        ['type' => 'wpmedia', 'layout' => 'vertical', 'condition' => ['path' => 'content.divider.content', 'operand' => 'equals', 'value' => 'image']],
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
        return false;
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
        return 800;
    }
    
    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'image_url', 'path' => 'design.content.background.layers[].image']];
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
        return ['design.divider.style'];
    }    
    
    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}

<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\IconBox", 
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class IconBox extends \Breakdance\Elements\Element
{
    static function uiIcon() 
    {
        return '<svg aria-hidden="true" focusable="false"   class="svg-inline--fa fa-credit-card-front" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M176 352h-64C103.2 352 96 359.2 96 368C96 376.8 103.2 384 112 384h64C184.8 384 192 376.8 192 368C192 359.2 184.8 352 176 352zM176 288h-64C103.2 288 96 295.2 96 304C96 312.8 103.2 320 112 320h63.1c8.836 0 16-7.164 16-16C191.1 295.2 184.8 288 176 288zM368 352h-128C231.2 352 224 359.2 224 368C224 376.8 231.2 384 240 384h128c8.836 0 16-7.164 16-16C384 359.2 376.8 352 368 352zM512 32h-448c-35.35 0-64 28.65-64 64v320c0 35.35 28.65 64 64 64h448c35.35 0 64-28.65 64-64V96C576 60.65 547.3 32 512 32zM544 416c0 17.64-14.36 32-32 32H64c-17.64 0-32-14.36-32-32V96c0-17.64 14.36-32 32-32h448c17.64 0 32 14.36 32 32V416zM496 96h-128C359.2 96 352 103.2 352 112v96C352 216.8 359.2 224 368 224h128C504.8 224 512 216.8 512 208v-96C512 103.2 504.8 96 496 96zM480 192h-96V128h96V192zM464 288h-32C423.2 288 416 295.2 416 304c0 8.836 7.164 16 16 16h32c8.836 0 16-7.164 16-16C480 295.2 472.8 288 464 288zM368 288h-128C231.2 288 223.1 295.2 223.1 304C223.1 312.8 231.2 320 240 320h128c8.836 0 16-7.164 16-16C384 295.2 376.8 288 368 288z"></path></svg>';
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
        return 'Icon Box';
    }
    
    static function className()
    {
        return 'bde-icon-box';
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
        return ['design' => ['container' => ['width' => ['breakpoint_base' => ['number' => 500, 'unit' => 'px', 'style' => '500px']], 'background' => '#FFFFFFFF', 'shadow' => null, 'borders' => ['radius' => ['breakpoint_base' => ['all' => ['number' => 4, 'unit' => 'px', 'style' => '4px'], 'topLeft' => ['number' => 4, 'unit' => 'px', 'style' => '4px'], 'topRight' => ['number' => 4, 'unit' => 'px', 'style' => '4px'], 'bottomLeft' => ['number' => 4, 'unit' => 'px', 'style' => '4px'], 'bottomRight' => ['number' => 4, 'unit' => 'px', 'style' => '4px'], 'editMode' => 'all']], 'shadow' => ['breakpoint_base' => ['shadows' => ['0' => ['color' => '#00000021', 'x' => '0', 'y' => '0', 'blur' => '40', 'spread' => '0', 'position' => 'outset']], 'style' => '0px 0px 40px 0px #00000021']]], 'alignment' => ['breakpoint_base' => 'left'], 'padding' => null], 'spacing' => ['above_button' => null, 'below_title' => null, 'after_icon' => null], 'icon' => ['style' => ['style' => 'outline', 'padding' => ['breakpoint_base' => ['number' => 18, 'unit' => 'px', 'style' => '18px']], 'outline_width' => ['breakpoint_base' => ['number' => 3, 'unit' => 'px', 'style' => '3px']], 'corners' => 'custom', 'radius' => ['number' => 4, 'unit' => 'px', 'style' => '4px'], 'size' => ['breakpoint_base' => ['number' => 25, 'unit' => 'px', 'style' => '25px']], 'nudge' => ['x' => null]], 'position' => null, 'vertical_alignment' => null, 'top_at' => null]], 'content' => ['content' => ['title' => 'Performant & Powerful', 'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc in justo imperdiet, convallis velit ac, malesuada enim. Etiam vitae tempus enim. Cras hendrerit efficitur eros.', 'button' => ['text' => 'Learn More'], 'icon' => ['slug' => 'icon-lightbulb.', 'name' => 'lightbulb', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M176 80c-52.94 0-96 43.06-96 96 0 8.84 7.16 16 16 16s16-7.16 16-16c0-35.3 28.72-64 64-64 8.84 0 16-7.16 16-16s-7.16-16-16-16zM96.06 459.17c0 3.15.93 6.22 2.68 8.84l24.51 36.84c2.97 4.46 7.97 7.14 13.32 7.14h78.85c5.36 0 10.36-2.68 13.32-7.14l24.51-36.84c1.74-2.62 2.67-5.7 2.68-8.84l.05-43.18H96.02l.04 43.18zM176 0C73.72 0 0 82.97 0 176c0 44.37 16.45 84.85 43.56 115.78 16.64 18.99 42.74 58.8 52.42 92.16v.06h48v-.12c-.01-4.77-.72-9.51-2.15-14.07-5.59-17.81-22.82-64.77-62.17-109.67-20.54-23.43-31.52-53.15-31.61-84.14-.2-73.64 59.67-128 127.95-128 70.58 0 128 57.42 128 128 0 30.97-11.24 60.85-31.65 84.14-39.11 44.61-56.42 91.47-62.1 109.46a47.507 47.507 0 0 0-2.22 14.3v.1h48v-.05c9.68-33.37 35.78-73.18 52.42-92.16C335.55 260.85 352 220.37 352 176 352 78.8 273.2 0 176 0z"/></svg>'], 'rotate' => 0]]];
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
        "alignment",
        "Alignment",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['value' => 'left', 'text' => 'Left'], '1' => ['value' => 'center', 'text' => 'Center'], '2' => ['value' => 'right', 'text' => 'Right']]],
        true,
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
      "EssentialElements\\borders",
      "Borders", 
      "borders", 
       ['type' => 'popout']
     ), c(
        "padding",
        "Padding",
        [],
        ['type' => 'spacing_complex', 'layout' => 'vertical'],
        true,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "icon",
        "Icon",
        [getPresetSection(
      "EssentialElements\\AtomV1IconDesign",
      "Style", 
      "style", 
       ['type' => 'popout']
     ), c(
        "position",
        "Position",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['text' => 'Top', 'value' => 'top'], '1' => ['value' => 'left', 'text' => 'Left'], '2' => ['text' => 'Right', 'value' => 'right']]],
        false,
        false,
        [],
      ), c(
        "top_at",
        "Top At",
        [],
        ['type' => 'breakpoint_dropdown', 'layout' => 'inline', 'condition' => ['path' => 'design.icon.position', 'operand' => 'is one of', 'value' => ['0' => 'left', '1' => 'right']]],
        false,
        false,
        [],
      ), c(
        "vertical_alignment",
        "Vertical Alignment",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['value' => 'top', 'text' => 'Top'], '1' => ['value' => 'middle', 'text' => 'Middle'], '2' => ['value' => 'bottom', 'text' => 'Bottom']], 'condition' => ['path' => 'design.icon.position', 'operand' => 'is one of', 'value' => ['0' => 'left', '1' => 'right']]],
        false,
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
      "EssentialElements\\typography_with_effects",
      "Title", 
      "title", 
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_effects",
      "Text", 
      "text", 
       ['type' => 'popout']
     )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\AtomV1ButtonDesign",
      "Button", 
      "button", 
       ['type' => 'popout']
     ), c(
        "spacing",
        "Spacing",
        [c(
        "after_icon",
        "After Icon",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "below_title",
        "Below Title",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "above_button",
        "Above Button",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
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
      )];
    }
    
    static function contentControls()
    {
        return [c(
        "content",
        "Content",
        [c(
        "icon",
        "Icon",
        [],
        ['type' => 'icon', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "rotate",
        "Rotate",
        [],
        ['type' => 'number', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 360, 'step' => 1]],
        false,
        false,
        [],
      ), c(
        "title",
        "Title",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "text",
        "Text",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\AtomV1ButtonContent",
      "Button", 
      "button", 
       ['type' => 'popout']
     ), c(
        "title_tag",
        "Title Tag",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'h1', 'text' => 'h1'], '1' => ['value' => 'h2', 'text' => 'h2'], '2' => ['value' => 'h3', 'text' => 'h3'], '3' => ['value' => 'h4', 'text' => 'h4'], '4' => ['value' => 'h5', 'text' => 'h5'], '5' => ['value' => 'h6', 'text' => 'h6']]],
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
        return 450;
    }
    
    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'string', 'path' => 'content.content.title'], '1' => ['accepts' => 'string', 'path' => 'content.content.text'], '2' => ['accepts' => 'string', 'path' => 'content.content.button.text'], '3' => ['accepts' => 'url', 'path' => 'content.content.button.link']];
    }

    static function additionalClasses()
    {
        return false;
    }

    static function projectManagement()
    {
        return ['looksGood' => 'yes', 'optionsGood' => 'yes', 'optionsWork' => 'yes'];
    }
    
    static function propertyPathsToWhitelistInFlatProps()
    {
        return ['design.icon.top_at', 'design.button.custom.size.full_width_at', 'design.button.style'];
    }    
    
    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}

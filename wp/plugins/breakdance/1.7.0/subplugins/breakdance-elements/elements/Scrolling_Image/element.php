<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\ScrollingImage", 
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class ScrollingImage extends \Breakdance\Elements\Element
{
    static function uiIcon() 
    {
        return '<svg aria-hidden="true" focusable="false"   class="svg-inline--fa fa-circle-down" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M348.7 224H320V152c0-22.06-17.94-40-40-40h-48C209.9 112 192 129.9 192 152V224H163.3C147.1 224 132.6 233.7 126.3 248.7c-6.188 14.95-2.812 32 8.562 43.48l92.81 96.11c15.12 15.12 41.25 15.33 56.75-.2031l92.5-95.8c11.47-11.48 14.88-28.59 8.688-43.59C379.4 233.7 364.9 224 348.7 224zM354.2 269.9l-92.5 95.8c-4.162 4.203-9.533 1.797-11.12 .2031l-92.69-96C152.1 265 155.9 256 163.3 256H224L223.1 152c0-4.406 3.594-8 8.001-8h48c4.406 0 7.999 3.594 7.999 8L288 256h60.69C355.1 256 359.9 264 354.2 269.9zM256 0C114.6 0 0 114.6 0 256s114.6 256 256 256s256-114.6 256-256S397.4 0 256 0zM256 480c-123.5 0-224-100.5-224-224s100.5-224 224-224s224 100.5 224 224S379.5 480 256 480z"></path></svg>';
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
        return 'Scrolling Image';
    }
    
    static function className()
    {
        return 'bde-scrolling-image';
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
        return ['design' => ['image' => ['width' => ['breakpoint_base' => ['number' => 350, 'unit' => 'px', 'style' => '350px']], 'height' => ['breakpoint_base' => ['number' => 400, 'unit' => 'px', 'style' => '400px']], 'duration' => ['number' => 500, 'unit' => 'ms', 'style' => '500ms'], 'shadow' => ['shadows' => ['0' => ['color' => '#E4E4E4FF', 'x' => '4', 'y' => '4', 'blur' => '0', 'spread' => '0', 'position' => 'outset']], 'style' => '4px 4px 0px 0px #E4E4E4FF'], 'borders' => ['shadow' => ['breakpoint_base' => ['shadows' => ['0' => ['color' => '#F3F3F3FF', 'x' => '4', 'y' => '4', 'blur' => '0', 'spread' => '0', 'position' => 'outset']], 'style' => '4px 4px 0px 0px #F3F3F3FF']]]], 'icon' => ['color' => '#FFFFFFFF', 'size' => ['breakpoint_base' => ['number' => 55, 'unit' => 'px', 'style' => '55px']], 'hover_offset' => ['number' => 10, 'unit' => 'px', 'style' => '10px']], 'caption' => ['position' => 'flex-end', 'full_width' => true, 'background' => '#0000007A', 'typography' => ['color' => ['breakpoint_base' => '#FFFFFFFF']], 'spacing' => ['margin' => ['breakpoint_base' => ['left' => ['number' => 4, 'unit' => 'px', 'style' => '4px'], 'right' => ['number' => 4, 'unit' => 'px', 'style' => '4px'], 'top' => ['number' => 4, 'unit' => 'px', 'style' => '4px'], 'bottom' => ['number' => 4, 'unit' => 'px', 'style' => '4px']]], 'padding' => ['breakpoint_base' => ['left' => ['number' => 10, 'unit' => 'px', 'style' => '10px'], 'right' => ['number' => 10, 'unit' => 'px', 'style' => '10px'], 'top' => ['number' => 10, 'unit' => 'px', 'style' => '10px'], 'bottom' => ['number' => 10, 'unit' => 'px', 'style' => '10px']]]]]], 'content' => ['content' => ['caption' => 'Hover Me', 'icon' => ['slug' => 'icon-arrow-alt-circle-down.', 'name' => 'arrow alt circle down', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 448c-110.5 0-200-89.5-200-200S145.5 56 256 56s200 89.5 200 200-89.5 200-200 200zm-32-316v116h-67c-10.7 0-16 12.9-8.5 20.5l99 99c4.7 4.7 12.3 4.7 17 0l99-99c7.6-7.6 2.2-20.5-8.5-20.5h-67V140c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12z"/></svg>'], 'image' => null]]];
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
        "image",
        "Image",
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
        "duration",
        "Duration",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'ms'], 'defaultType' => 'ms']],
        false,
        false,
        [],
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
      ), c(
        "icon",
        "Icon",
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
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['step' => 2, 'min' => 50, 'max' => 100]],
        true,
        false,
        [],
      ), c(
        "hover_offset",
        "Hover Offset",
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
      ), c(
        "caption",
        "Caption",
        [c(
        "position",
        "Position",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'flex-start', 'text' => 'Top', 'icon' => 'BorderTopIcon'], '1' => ['text' => 'Center', 'value' => 'center', 'icon' => 'BorderCenterIcon'], '2' => ['text' => 'Bottom', 'value' => 'flex-end', 'icon' => 'BorderBottomIcon']]],
        false,
        false,
        [],
      ), c(
        "full_width",
        "Full Width",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\typography_with_effects_and_align",
      "Typography", 
      "typography", 
       ['type' => 'popout']
     ), c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline', 'colorOptions' => ['type' => 'solidAndGradient']],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\spacing_all",
      "Spacing", 
      "spacing", 
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
     )];
    }
    
    static function contentControls()
    {
        return [c(
        "content",
        "Content",
        [c(
        "image",
        "Image",
        [],
        ['type' => 'wpmedia', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "caption",
        "Caption",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "icon",
        "Icon",
        [],
        ['type' => 'icon', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "link",
        "Link",
        [],
        ['type' => 'link', 'layout' => 'vertical'],
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
        return 1000;
    }
    
    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'image_url', 'path' => 'content.content.image'], '1' => ['accepts' => 'string', 'path' => 'content.content.caption'], '2' => ['accepts' => 'url', 'path' => 'content.content.link.url']];
    }

    static function additionalClasses()
    {
        return [['name' => 'is-img-empty', 'template' => '{% if content.content.image is empty %}yes{% endif %}']];
    }

    static function projectManagement()
    {
        return ['looksGood' => 'yes', 'optionsGood' => 'yes', 'optionsWork' => 'yes'];
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

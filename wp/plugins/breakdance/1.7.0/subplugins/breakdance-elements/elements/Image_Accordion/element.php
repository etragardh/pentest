<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\ImageAccordion", 
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class ImageAccordion extends \Breakdance\Elements\Element
{
    static function uiIcon() 
    {
        return '<svg aria-hidden="true" focusable="false"   class="svg-inline--fa fa-line-height" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M164.7 139.3C167.8 142.4 171.9 144 176 144s8.188-1.562 11.31-4.688c6.25-6.25 6.25-16.38 0-22.62l-80-80c-6.25-6.25-16.38-6.25-22.62 0l-80 80c-6.25 6.25-6.25 16.38 0 22.62s16.38 6.25 22.62 0L80 86.63v338.8l-52.69-52.69c-6.25-6.25-16.38-6.25-22.62 0s-6.25 16.38 0 22.62l80 80C87.81 478.4 91.91 480 96 480s8.188-1.562 11.31-4.688l80-80c6.25-6.25 6.25-16.38 0-22.62s-16.38-6.25-22.62 0L112 425.4V86.63L164.7 139.3zM272 112h288C568.8 112 576 104.8 576 96s-7.156-16-16-16h-288C263.2 80 256 87.16 256 96S263.2 112 272 112zM560 240h-288C263.2 240 256 247.2 256 256s7.156 16 16 16h288C568.8 272 576 264.8 576 256S568.8 240 560 240zM560 400h-288C263.2 400 256 407.2 256 416s7.156 16 16 16h288c8.844 0 16-7.156 16-16S568.8 400 560 400z"></path></svg>';
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
        return 'Image Accordion';
    }
    
    static function className()
    {
        return 'bde-image-accordion';
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
        return ['content' => ['content' => ['images' => ['0' => ['title' => 'Spa Day', 'text' => 'Massages and more.', 'link' => ['type' => 'url', 'url' => '/'], 'icon' => ['slug' => 'icon-home.', 'name' => 'home', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M280.37 148.26L96 300.11V464a16 16 0 0 0 16 16l112.06-.29a16 16 0 0 0 15.92-16V368a16 16 0 0 1 16-16h64a16 16 0 0 1 16 16v95.64a16 16 0 0 0 16 16.05L464 480a16 16 0 0 0 16-16V300L295.67 148.26a12.19 12.19 0 0 0-15.3 0zM571.6 251.47L488 182.56V44.05a12 12 0 0 0-12-12h-56a12 12 0 0 0-12 12v72.61L318.47 43a48 48 0 0 0-61 0L4.34 251.47a12 12 0 0 0-1.6 16.9l25.5 31A12 12 0 0 0 45.15 301l235.22-193.74a12.19 12.19 0 0 1 15.3 0L530.9 301a12 12 0 0 0 16.9-1.6l25.5-31a12 12 0 0 0-1.7-16.93z"/></svg>']], '1' => ['title' => 'Watersports', 'text' => 'Rent surfboards, kayas, and jet skis.', 'icon' => ['slug' => 'icon-water.', 'name' => 'water', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M562.1 383.9c-21.5-2.4-42.1-10.5-57.9-22.9-14.1-11.1-34.2-11.3-48.2 0-37.9 30.4-107.2 30.4-145.7-1.5-13.5-11.2-33-9.1-46.7 1.8-38 30.1-106.9 30-145.2-1.7-13.5-11.2-33.3-8.9-47.1 2-15.5 12.2-36 20.1-57.7 22.4-7.9.8-13.6 7.8-13.6 15.7v32.2c0 9.1 7.6 16.8 16.7 16 28.8-2.5 56.1-11.4 79.4-25.9 56.5 34.6 137 34.1 192 0 56.5 34.6 137 34.1 192 0 23.3 14.2 50.9 23.3 79.1 25.8 9.1.8 16.7-6.9 16.7-16v-31.6c.1-8-5.7-15.4-13.8-16.3zm0-144c-21.5-2.4-42.1-10.5-57.9-22.9-14.1-11.1-34.2-11.3-48.2 0-37.9 30.4-107.2 30.4-145.7-1.5-13.5-11.2-33-9.1-46.7 1.8-38 30.1-106.9 30-145.2-1.7-13.5-11.2-33.3-8.9-47.1 2-15.5 12.2-36 20.1-57.7 22.4-7.9.8-13.6 7.8-13.6 15.7v32.2c0 9.1 7.6 16.8 16.7 16 28.8-2.5 56.1-11.4 79.4-25.9 56.5 34.6 137 34.1 192 0 56.5 34.6 137 34.1 192 0 23.3 14.2 50.9 23.3 79.1 25.8 9.1.8 16.7-6.9 16.7-16v-31.6c.1-8-5.7-15.4-13.8-16.3zm0-144C540.6 93.4 520 85.4 504.2 73 490.1 61.9 470 61.7 456 73c-37.9 30.4-107.2 30.4-145.7-1.5-13.5-11.2-33-9.1-46.7 1.8-38 30.1-106.9 30-145.2-1.7-13.5-11.2-33.3-8.9-47.1 2-15.5 12.2-36 20.1-57.7 22.4-7.9.8-13.6 7.8-13.6 15.7v32.2c0 9.1 7.6 16.8 16.7 16 28.8-2.5 56.1-11.4 79.4-25.9 56.5 34.6 137 34.1 192 0 56.5 34.6 137 34.1 192 0 23.3 14.2 50.9 23.3 79.1 25.8 9.1.8 16.7-6.9 16.7-16v-31.6c.1-8-5.7-15.4-13.8-16.3z"/></svg>'], 'link' => ['type' => 'url', 'url' => '/']], '2' => ['title' => 'Excursions', 'text' => 'Walking, bike, and jeep tours.', 'icon' => ['slug' => 'icon-car-alt.', 'name' => 'car alt', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 480 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M438.66 212.33l-11.24-28.1-19.93-49.83C390.38 91.63 349.57 64 303.5 64h-127c-46.06 0-86.88 27.63-103.99 70.4l-19.93 49.83-11.24 28.1C17.22 221.5 0 244.66 0 272v48c0 16.12 6.16 30.67 16 41.93V416c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32v-32h256v32c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32v-54.07c9.84-11.25 16-25.8 16-41.93v-48c0-27.34-17.22-50.5-41.34-59.67zm-306.73-54.16c7.29-18.22 24.94-30.17 44.57-30.17h127c19.63 0 37.28 11.95 44.57 30.17L368 208H112l19.93-49.83zM80 319.8c-19.2 0-32-12.76-32-31.9S60.8 256 80 256s48 28.71 48 47.85-28.8 15.95-48 15.95zm320 0c-19.2 0-48 3.19-48-15.95S380.8 256 400 256s32 12.76 32 31.9-12.8 31.9-32 31.9z"/></svg>'], 'link' => ['type' => 'url', 'url' => '/']]]]], 'design' => ['layout' => ['gap_between_images' => ['number' => 20, 'unit' => 'px', 'style' => '20px'], 'vertical_at' => 'breakpoint_tablet_portrait', 'orientation' => 'horizontal', 'height' => ['breakpoint_base' => ['number' => 450, 'unit' => 'px', 'style' => '450px'], 'breakpoint_tablet_portrait' => ['number' => 650, 'unit' => 'px', 'style' => '650px']]], 'content' => ['hide_until_hover' => true, 'hover_effect' => 'fade', 'icon_color' => '#FFFFFFFF', 'icon_size' => ['breakpoint_base' => ['number' => 30, 'unit' => 'px', 'style' => '30px']]], 'image' => ['expand_on_hover' => ['number' => 150, 'unit' => '%', 'style' => '150%'], 'scale_on_hover' => null, 'image_opacity' => null, 'transition_duration' => ['number' => 500, 'unit' => 'ms', 'style' => '500ms'], 'overlay' => ['points' => ['0' => ['left' => 0, 'red' => 24, 'green' => 111, 'blue' => 142, 'alpha' => 1], '1' => ['left' => 100, 'red' => 0, 'green' => 0, 'blue' => 0, 'alpha' => 1]], 'type' => 'linear', 'degree' => 0, 'svgValue' => '<linearGradient gradientTransform="matrix(1,0,0,1,0,0)" id="%%GRADIENTID%%"><stop stop-opacity="1" stop-color="#186f8e" offset="0"></stop><stop stop-opacity="1" stop-color="#000000" offset="1"></stop></linearGradient>', 'value' => 'linear-gradient(0deg,rgba(24, 111, 142, 1) 0%,rgba(0, 0, 0, 1) 100%)'], 'overlay_opacity' => 0.2, 'overlay_hover' => null, 'overlay_opacity_hover' => 0.6, 'border_radius' => null], 'spacing' => ['between_images' => ['number' => 20, 'unit' => 'px', 'style' => '20px', 'breakpoint_base' => ['number' => 20, 'unit' => 'px', 'style' => '20px']], 'below_icon' => ['breakpoint_base' => ['number' => 10, 'unit' => 'px', 'style' => '10px']], 'below_title' => ['breakpoint_base' => ['number' => 10, 'unit' => 'px', 'style' => '10px']]]]];
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
        "orientation",
        "Orientation",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['value' => 'horizontal', 'text' => 'Horizontal'], '1' => ['text' => 'Vertical', 'value' => 'vertical']]],
        false,
        false,
        [],
      ), c(
        "vertical_at",
        "Vertical At",
        [],
        ['type' => 'breakpoint_dropdown', 'layout' => 'inline', 'condition' => ['path' => 'design.layout.orientation', 'operand' => 'not equals', 'value' => 'vertical'], 'breakpointOptions' => ['enableNever' => true]],
        false,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "image",
        "Image",
        [c(
        "expand_on_hover",
        "Expand On Hover",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 100, 'max' => 500, 'step' => 1], 'unitOptions' => ['types' => ['0' => '%'], 'defaultType' => '%']],
        false,
        false,
        [],
      ), c(
        "scale_on_hover",
        "Scale On Hover",
        [],
        ['type' => 'number', 'layout' => 'inline', 'rangeOptions' => ['min' => 1, 'max' => 2, 'step' => 0.01]],
        false,
        false,
        [],
      ), c(
        "overlay",
        "Overlay",
        [],
        ['type' => 'color', 'layout' => 'inline', 'colorOptions' => ['type' => 'solidAndGradient']],
        false,
        true,
        [],
      ), c(
        "overlay_opacity",
        "Overlay Opacity",
        [],
        ['type' => 'number', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 1, 'step' => 0.1]],
        false,
        true,
        [],
      ), c(
        "shadow",
        "Shadow",
        [],
        ['type' => 'shadow', 'layout' => 'vertical'],
        false,
        true,
        [],
      ), c(
        "transition_duration",
        "Transition Duration",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'ms'], 'defaultType' => 'ms']],
        false,
        false,
        [],
      ), c(
        "border_radius",
        "Border Radius",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 1, 'max' => 1000, 'step' => 1]],
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
        "hide_until_hover",
        "Hide Until Hover",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "hover_effect",
        "Hover Effect",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'fade', 'text' => 'Fade'], '1' => ['text' => 'Slide Up', 'value' => 'slide-up']], 'condition' => ['path' => 'design.content.hide_until_hover', 'operand' => 'is set', 'value' => '']],
        false,
        false,
        [],
      ), c(
        "icon_color",
        "Icon Color",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "icon_size",
        "Icon Size",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\typography_with_effects",
      "Title Typography", 
      "title_typography", 
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_effects",
      "Text Typography", 
      "text_typography", 
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
        "between_images",
        "Between Images",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['step' => 1, 'min' => 0, 'max' => 50]],
        true,
        false,
        [],
      ), c(
        "below_icon",
        "Below Icon",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "below_title",
        "Below Title",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        true,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\spacing_margin_y",
      "Wrapper", 
      "wrapper", 
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
        "images",
        "Images",
        [c(
        "image",
        "Image",
        [],
        ['type' => 'wpmedia', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "image_options",
        "Image options",
        [c(
        "size",
        "Size",
        [],
        ['type' => 'media_size_dropdown', 'layout' => 'vertical', 'mediaSizeOptions' => ['imagePropertyPath' => 'content.content.images[].image']],
        false,
        false,
        [],
      ), c(
        "alt",
        "Alt",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "lazy_load",
        "Lazy load",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'vertical', 'condition' => ['path' => 'content.content.images', 'operand' => 'is set', 'value' => ''], 'sectionOptions' => ['type' => 'popout']],
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
        ['type' => 'text', 'layout' => 'vertical', 'textOptions' => ['multiline' => true]],
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
        ['type' => 'repeater', 'layout' => 'vertical', 'repeaterOptions' => ['titleTemplate' => '{title}', 'defaultTitle' => 'Image', 'buttonName' => 'Add Image', 'galleryMode' => false, 'galleryMediaPath' => 'image']],
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
        return ["type" => "final",   ];
    }

    static function spacingBars()
    {
        return ['0' => ['location' => 'outside-top', 'cssProperty' => 'margin-top', 'affectedPropertyPath' => 'design.spacing.wrapper.margin_top.%%BREAKPOINT%%'], '1' => ['location' => 'outside-bottom', 'cssProperty' => 'margin-bottom', 'affectedPropertyPath' => 'design.spacing.wrapper.margin_bottom.%%BREAKPOINT%%']];
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
        return 1100;
    }
    
    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'image_url', 'path' => 'content.content.images[].image'], '1' => ['accepts' => 'string', 'path' => 'content.content.images[].title'], '2' => ['accepts' => 'string', 'path' => 'content.content.images[].text'], '3' => ['accepts' => 'url', 'path' => 'content.content.images[].link.url']];
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
        return ['design.layout.vertical_at'];
    }    
    
    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}

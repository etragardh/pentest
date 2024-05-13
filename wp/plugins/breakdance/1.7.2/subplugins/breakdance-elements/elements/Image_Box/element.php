<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\ImageBox",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class ImageBox extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return '<svg aria-hidden="true" focusable="false"   class="svg-inline--fa fa-camera-polaroid" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M288 112c-44.11 0-80 35.88-80 80s35.89 80 80 80s80-35.88 80-80S332.1 112 288 112zM288 240C261.5 240 240 218.5 240 192S261.5 144 288 144s48 21.53 48 48S314.5 240 288 240zM561.1 318.5L512 256L512 96c0-35.35-28.65-64-64-64H128C92.65 32 64 60.65 64 96l.0001 160L14.03 318.5C4.947 329.8 0 343.9 0 358.5V416c0 35.2 28.8 64 64 64h448c35.2 0 64-28.8 64-64v-57.55C576 343.9 571.1 329.8 561.1 318.5zM96 267.2V96c0-17.67 14.33-32 32-32h320c17.67 0 32 14.33 32 32v171.2L522.2 320H53.78L96 267.2zM544 416c0 17.6-14.4 32-32 32H64c-17.6 0-32-14.4-32-32v-63.86l511.9-.0713c.0566 .0547-.0547-.0566 0 0L544 416zM464 384h-352C103.2 384 96 391.2 96 400C96 408.8 103.2 416 112 416h352c8.838 0 16-7.164 16-16C480 391.2 472.8 384 464 384zM416 104c-13.25 0-24 10.74-24 24c0 13.25 10.75 24 24 24c13.26 0 24-10.75 24-24C440 114.7 429.3 104 416 104z"></path></svg>';
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
        return 'Image Box';
    }

    static function className()
    {
        return 'bde-image-box';
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
        return ['content' => ['content' => ['text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. ', 'title' => 'The #1 Visual Builder', 'button' => ['text' => 'Learn More'], 'image_size' => 'full']], 'design' => ['container' => ['width' => ['breakpoint_base' => ['number' => 500, 'unit' => 'px', 'style' => '500px']], 'shadow' => null, 'background' => '#FFFFFFFF', 'borders' => ['radius' => ['breakpoint_base' => ['all' => ['number' => 4, 'unit' => 'px', 'style' => '4px'], 'topLeft' => ['number' => 4, 'unit' => 'px', 'style' => '4px'], 'topRight' => ['number' => 4, 'unit' => 'px', 'style' => '4px'], 'bottomLeft' => ['number' => 4, 'unit' => 'px', 'style' => '4px'], 'bottomRight' => ['number' => 4, 'unit' => 'px', 'style' => '4px'], 'editMode' => 'all']], 'shadow' => ['breakpoint_base' => ['shadows' => ['0' => ['color' => '#0000001C', 'x' => '0', 'y' => '0', 'blur' => '40', 'spread' => '0', 'position' => 'outset']], 'style' => '0px 0px 40px 0px #0000001C']]], 'padding' => ['breakpoint_base' => ['left' => ['number' => 30, 'unit' => 'px', 'style' => '30px'], 'right' => ['number' => 30, 'unit' => 'px', 'style' => '30px'], 'top' => ['number' => 30, 'unit' => 'px', 'style' => '30px'], 'bottom' => ['number' => 30, 'unit' => 'px', 'style' => '30px']]], 'content_position' => ['breakpoint_base' => 'left']], 'image' => ['width' => null, 'borders' => ['radius' => null], 'position' => null, 'vertical_alignment' => null, 'top_at' => null], 'button' => ['display_as' => null], 'typography' => ['title' => ['text_align' => null]], 'spacing' => ['after_image' => null, 'above_button' => null, 'below_title' => null]]];
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
        "content_position",
        "Content Position",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'left', 'text' => 'Left', 'icon' => 'AlignLeftIcon'], '1' => ['value' => 'center', 'text' => 'Center', 'icon' => 'AlignCenterIcon'], '2' => ['value' => 'right', 'text' => 'Right', 'icon' => 'AlignRightIcon']]],
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
      ), c(
        "content_padding_only",
        "Content Padding Only",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
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
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => '%'], 'defaultType' => '%']],
        true,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\borders",
      "Borders",
      "borders",
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
        ['type' => 'breakpoint_dropdown', 'layout' => 'inline', 'condition' => ['path' => 'design.image.position', 'operand' => 'is one of', 'value' => ['0' => 'left', '1' => 'right']]],
        false,
        false,
        [],
      ), c(
        "vertical_alignment",
        "Vertical Alignment",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['value' => 'top', 'text' => 'Top'], '1' => ['value' => 'middle', 'text' => 'Middle'], '2' => ['value' => 'bottom', 'text' => 'Bottom']], 'condition' => ['path' => 'design.image.position', 'operand' => 'is one of', 'value' => ['0' => 'left', '1' => 'right']]],
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
        "after_image",
        "After Image",
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
        "image",
        "Image",
        [],
        ['type' => 'wpmedia', 'layout' => 'vertical', 'mediaOptions' => ['acceptedFileTypes' => ['0' => 'image'], 'multiple' => false]],
        false,
        false,
        [],
      ), c(
        "image_options",
        "Image Options",
        [c(
        "size",
        "Size",
        [],
        ['type' => 'media_size_dropdown', 'layout' => 'vertical', 'mediaSizeOptions' => ['imagePropertyPath' => 'content.content.image']],
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
        "Lazy Load",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'vertical', 'condition' => ['path' => 'content.content.image', 'operand' => 'is set', 'value' => ''], 'sectionOptions' => ['type' => 'popout']],
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
        return ['0' => ['accepts' => 'image_url', 'path' => 'content.content.image'], '1' => ['accepts' => 'string', 'path' => 'content.content.title'], '2' => ['accepts' => 'string', 'path' => 'content.content.text'], '3' => ['accepts' => 'string', 'path' => 'content.content.button.text'], '4' => ['accepts' => 'url', 'path' => 'content.content.button.link']];
    }

    static function additionalClasses()
    {
        return [['name' => 'bde-image-box-content-padding-only', 'template' => '{% if design.container.content_padding_only %}
yes
{% endif %}']];
    }

    static function projectManagement()
    {
        return ['looksGood' => 'yes', 'optionsGood' => 'yes', 'optionsWork' => 'yes'];
    }

    static function propertyPathsToWhitelistInFlatProps()
    {
        return ['design.image.top_at', 'design.button.custom.size.full_width_at', 'design.button.style', 'design.container.content_padding_only'];
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}

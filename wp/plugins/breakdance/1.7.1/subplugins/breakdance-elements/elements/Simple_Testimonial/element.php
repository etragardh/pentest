<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\SimpleTestimonial",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class SimpleTestimonial extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return '<svg aria-hidden="true" focusable="false"   class="svg-inline--fa fa-comment-quote" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M183.1 143.1c-30.93 0-56 25.07-56 56s25.07 56 56 56c8.627 0 16.7-2.111 24-5.596V256c0 26.47-21.53 48-48 48c-8.844 0-16 7.156-16 16s7.156 15.1 15.1 15.1C204.1 335.1 240 300.1 240 256L239.1 199.1C239.1 169.1 214.9 143.1 183.1 143.1zM184 224C170.8 224 160 213.2 160 200C160 186.8 170.8 176 184 176c13.23 0 24 10.77 24 24C208 213.2 197.2 224 184 224zM256 31.1c-141.4 0-255.1 93.13-255.1 208c0 47.62 19.91 91.25 52.91 126.3c-14.87 39.5-45.87 72.88-46.37 73.25c-6.623 7-8.373 17.25-4.623 26C5.816 474.3 14.38 480 24 480c61.49 0 109.1-25.75 139.1-46.25c28.1 9 60.16 14.25 92.9 14.25c141.4 0 255.1-93.13 255.1-207.1S397.4 31.1 256 31.1zM256 416c-28.25 0-56.24-4.25-83.24-12.75c-9.516-3.068-19.92-1.461-28.07 4.338c-22.1 16.25-58.54 35.29-102.7 39.66c11.1-15.12 29.75-40.5 40.74-69.63l.1289-.3398c4.283-11.27 1.791-23.1-6.43-32.82C47.51 313.1 32.06 277.6 32.06 240c0-97 100.5-176 223.1-176c123.5 0 223.1 79 223.1 176S379.5 416 256 416zM327.1 143.1c-30.93 0-56 25.07-56 56s25.07 56 56 56c8.627 0 16.7-2.111 24-5.596V256c0 26.47-21.53 48-48 48C295.2 304 288 311.2 288 320s7.156 15.1 15.1 15.1C348.1 335.1 384 300.1 384 256l-.0001-56C383.1 169.1 358.9 143.1 327.1 143.1zM328 224c-13.23 0-24-10.77-24-24c0-13.23 10.77-24 24-24C341.2 176 352 186.8 352 200C352 213.2 341.2 224 328 224z"></path></svg>';
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
        return 'Simple Testimonial';
    }

    static function className()
    {
        return 'bde-simple-testimonial';
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
        return ['design' => ['layout' => ['width' => ['breakpoint_base' => ['number' => 520, 'unit' => 'px', 'style' => '520px']], 'alignment' => null], 'image' => ['style' => 'outlined-circle', 'size' => ['breakpoint_base' => ['number' => 140, 'unit' => 'px', 'style' => '140px']]], 'quotes' => ['style' => 'quotes-5', 'color' => '#F5E9FFFF', 'size' => ['breakpoint_base' => ['number' => 36, 'unit' => 'px', 'style' => '36px']], 'horizontal_offset' => ['number' => 0, 'unit' => 'px', 'style' => 0], 'vertical_offset' => ['number' => 5, 'unit' => 'px', 'style' => '5px']], 'spacing' => ['below_author' => ['breakpoint_base' => ['number' => 15, 'unit' => 'px', 'style' => '15px']], 'below_author_info' => null, 'below_testimonial' => ['breakpoint_base' => ['number' => 25, 'unit' => 'px', 'style' => '25px']], 'below_image' => ['breakpoint_base' => ['number' => 25, 'unit' => 'px', 'style' => '25px']]]], 'content' => ['content' => ['testimonial' => 'Breakdance is flexible, powerful, and easy-to-use. It\'s everything I need to build a website.', 'author' => 'Louis Reingold', 'author_info' => 'CEO @ Breakdance', 'image' => ['id' => -1, 'type' => 'external_image', 'url' => 'https://louisreingold.com/louis-reingold.jpg', 'alt' => 'world\'s best human', 'caption' => ''], 'image_dynamic_meta' => null]]];
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
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 300, 'max' => 1200, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "alignment",
        "Alignment",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['value' => 'left', 'text' => 'Left'], '1' => ['text' => 'Center', 'value' => 'center'], '2' => ['text' => 'Right', 'value' => 'right']]],
        true,
        false,
        [],
      ), c(
        "image_position",
        "Image Position",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['value' => 'top', 'text' => 'Top'], '1' => ['text' => 'Author', 'value' => 'author'], '2' => ['text' => 'Bottom', 'value' => 'bottom']]],
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
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['step' => 1, 'min' => 30, 'max' => 200], 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px']],
        true,
        false,
        [],
      ), c(
        "style",
        "Style",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'Standard', 'value' => 'standard'], '1' => ['value' => 'outlined-circle', 'text' => 'Outlined Circle'], '2' => ['text' => 'Masked', 'value' => 'masked']], 'buttonBarOptions' => ['size' => 'small']],
        false,
        false,
        [],
      ), c(
        "shape_outline",
        "Shape & Outline",
        [c(
        "shape",
        "Shape",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'circle', 'text' => 'Circle'], '1' => ['text' => 'Square', 'value' => 'square'], '2' => ['text' => 'Rounded', 'value' => 'rounded']], 'condition' => ['path' => 'design.image.style', 'operand' => 'equals', 'value' => 'standard']],
        false,
        false,
        [],
      ), c(
        "border_radius",
        "Border Radius",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['step' => 1, 'min' => 1, 'max' => 100], 'unitOptions' => ['types' => []], 'condition' => ['path' => 'design.image.shape_outline.shape', 'operand' => 'equals', 'value' => 'rounded']],
        false,
        false,
        [],
      ), c(
        "border_width",
        "Border Width",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "border_color",
        "Border Color",
        [],
        ['type' => 'color', 'layout' => 'inline', 'colorOptions' => ['type' => 'solidOnly']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'condition' => ['path' => 'design.image.style', 'operand' => 'equals', 'value' => 'standard'], 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "mask",
        "Mask",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'Donut', 'label' => 'Label', 'value' => 'donut'], '1' => ['text' => 'Tv', 'value' => 'tv'], '2' => ['text' => 'Blob', 'value' => 'blob'], '3' => ['text' => 'Star 1', 'value' => 'star1'], '4' => ['text' => 'Star 2', 'value' => 'star2'], '5' => ['text' => 'Star 3', 'value' => 'star3'], '6' => ['text' => 'Star 4', 'value' => 'star4'], '7' => ['text' => 'Blob 2', 'value' => 'blob2'], '8' => ['text' => 'Blob 3', 'value' => 'blob3'], '9' => ['text' => 'Blob', 'value' => 'blob'], '10' => ['text' => 'Blob 4', 'value' => 'blob4'], '11' => ['text' => 'Blob 5', 'value' => 'blob5'], '12' => ['text' => 'Blob 6', 'value' => 'blob6'], '13' => ['text' => 'Blob 7', 'value' => 'blob7'], '14' => ['text' => 'Blob 8', 'value' => 'blob8']], 'condition' => ['path' => 'design.image.style', 'operand' => 'equals', 'value' => 'masked']],
        false,
        false,
        [],
      ), c(
        "outline",
        "Outline",
        [c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['step' => 1, 'min' => 0, 'max' => 10], 'unitOptions' => ['types' => ['0' => 'px']]],
        false,
        false,
        [],
      ), c(
        "color",
        "Color",
        [],
        ['type' => 'color', 'layout' => 'inline', 'colorOptions' => ['type' => 'solidAndGradient']],
        false,
        false,
        [],
      ), c(
        "offset_width",
        "Offset Width",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['step' => 1, 'min' => 0, 'max' => 10], 'unitOptions' => ['types' => ['0' => 'px']]],
        false,
        false,
        [],
      ), c(
        "offset_color",
        "Offset Color",
        [],
        ['type' => 'color', 'layout' => 'inline', 'colorOptions' => ['type' => 'solidOnly']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout'], 'condition' => ['path' => 'design.image.style', 'operand' => 'equals', 'value' => 'outlined-circle']],
        false,
        false,
        [],
      ), c(
        "shadow",
        "Shadow",
        [],
        ['type' => 'shadow', 'layout' => 'vertical', 'condition' => ['path' => 'design.image.style', 'operand' => 'not equals', 'value' => 'masked']],
        false,
        false,
        [],
      ), c(
        "black_white",
        "Black & White",
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
        "quotes",
        "Quotes",
        [c(
        "style",
        "Style",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'quotes-1', 'text' => 'Quotes 1'], '1' => ['text' => 'Quotes 2', 'value' => 'quotes-2'], '2' => ['text' => 'Quotes 3', 'value' => 'quotes-3'], '3' => ['text' => 'Quotes 4', 'value' => 'quotes-4'], '4' => ['text' => 'Quotes 5', 'value' => 'quotes-5']]],
        false,
        false,
        [],
      ), c(
        "color",
        "Color",
        [],
        ['type' => 'color', 'layout' => 'inline', 'condition' => ['path' => 'design.quotes.style', 'operand' => 'is set', 'value' => '']],
        false,
        false,
        [],
      ), c(
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 20, 'max' => 100, 'step' => 1], 'condition' => ['path' => 'design.quotes.style', 'operand' => 'is set', 'value' => '']],
        true,
        false,
        [],
      ), c(
        "horizontal_offset",
        "Horizontal Offset",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => -30, 'max' => 80, 'step' => 1], 'condition' => ['path' => 'design.quotes.style', 'operand' => 'is set', 'value' => ''], 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px']],
        true,
        false,
        [],
      ), c(
        "vertical_offset",
        "Vertical Offset",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 80, 'step' => 1], 'condition' => ['path' => 'design.quotes.style', 'operand' => 'is set', 'value' => ''], 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px']],
        true,
        false,
        [],
      ), c(
        "hide_below",
        "Hide Below",
        [],
        ['type' => 'breakpoint_dropdown', 'layout' => 'inline'],
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
      "Testimonial",
      "testimonial",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_effects",
      "Author",
      "author",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_effects",
      "Author Info",
      "author_info",
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
        "below_image",
        "Below Image",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em', '2' => 'rem'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "below_testimonial",
        "Below Testimonial",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em', '2' => 'rem'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "below_author",
        "Below Author",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em', '2' => 'rem'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "below_author_info",
        "Below Author Info",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em', '2' => 'rem'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
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
        "image",
        "Image",
        [],
        ['type' => 'wpmedia', 'layout' => 'vertical'],
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
        ['type' => 'section', 'layout' => 'vertical', 'sectionOptions' => ['type' => 'popout'], 'condition' => ['path' => 'content.content.image', 'operand' => 'is set', 'value' => '']],
        false,
        false,
        [],
      ), c(
        "author",
        "Author",
        [],
        ['type' => 'text', 'layout' => 'vertical', 'variableOptions' => ['enabled' => false]],
        false,
        false,
        [],
      ), c(
        "author_info",
        "Author Info",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "testimonial",
        "Testimonial",
        [],
        ['type' => 'text', 'layout' => 'vertical', 'textOptions' => ['multiline' => true]],
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
        return 1900;
    }

    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'string', 'path' => 'content.content.author'], '1' => ['accepts' => 'string', 'path' => 'content.content.author_info'], '2' => ['accepts' => 'string', 'path' => 'content.content.testimonial'], '3' => ['accepts' => 'image_url', 'path' => 'content.content.image']];
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
        return ['design.quotes.hide_below'];
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}

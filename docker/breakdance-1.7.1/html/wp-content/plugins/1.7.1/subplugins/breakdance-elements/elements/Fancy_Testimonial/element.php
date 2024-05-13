<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\FancyTestimonial",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class FancyTestimonial extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'QuotesIcon';
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
        return 'Fancy Testimonial';
    }

    static function className()
    {
        return 'bde-fancy-testimonial';
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
        return ['content' => ['content' => ['title' => 'Customer Opinion', 'name' => 'Anita Naylor', 'occupation' => 'Web Designer', 'button' => null, 'stars' => 'five', 'testimonial' => 'Thanks to Breakdance I can charge more and work less.']], 'design' => ['wrapper' => ['boxed' => true, 'background' => '#FFFFFFFF', 'padding' => ['padding' => ['breakpoint_base' => ['left' => ['number' => 40, 'unit' => 'px', 'style' => '40px'], 'right' => ['number' => 40, 'unit' => 'px', 'style' => '40px'], 'top' => ['number' => 40, 'unit' => 'px', 'style' => '40px'], 'bottom' => ['number' => 40, 'unit' => 'px', 'style' => '40px']]]], 'borders' => ['radius' => ['breakpoint_base' => ['all' => ['number' => 8, 'unit' => 'px', 'style' => '8px'], 'topLeft' => ['number' => 8, 'unit' => 'px', 'style' => '8px'], 'topRight' => ['number' => 8, 'unit' => 'px', 'style' => '8px'], 'bottomLeft' => ['number' => 8, 'unit' => 'px', 'style' => '8px'], 'bottomRight' => ['number' => 8, 'unit' => 'px', 'style' => '8px'], 'editMode' => 'all']], 'shadow' => ['breakpoint_base' => ['shadows' => ['0' => ['color' => '#00000008', 'x' => '0', 'y' => '12', 'blur' => '28', 'spread' => '0', 'position' => 'outset'], '1' => ['color' => '#0000000A', 'x' => '0', 'y' => '32', 'blur' => '40', 'spread' => '0', 'position' => 'outset'], '2' => ['color' => '#00000008', 'x' => '0', 'y' => '108', 'blur' => '80', 'spread' => '0', 'position' => 'outset']], 'style' => '0px 12px 28px 0px #00000008, 0px 32px 40px 0px #0000000A, 0px 108px 80px 0px #00000008']]], 'shadows' => null], 'image' => ['width' => null, 'height' => null, 'border_radius' => ['number' => 8, 'unit' => 'px', 'style' => '8px'], 'shadow' => ['shadows' => ['0' => ['color' => '#00000040', 'x' => '0', 'y' => '12', 'blur' => '30', 'spread' => '-10', 'position' => 'outset']], 'style' => '0px 12px 30px -10px #00000040'], 'borders' => ['radius' => ['breakpoint_base' => ['all' => ['number' => 8, 'unit' => 'px', 'style' => '8px'], 'topLeft' => ['number' => 8, 'unit' => 'px', 'style' => '8px'], 'topRight' => ['number' => 8, 'unit' => 'px', 'style' => '8px'], 'bottomLeft' => ['number' => 8, 'unit' => 'px', 'style' => '8px'], 'bottomRight' => ['number' => 8, 'unit' => 'px', 'style' => '8px'], 'editMode' => 'all']], 'shadow' => ['breakpoint_base' => ['shadows' => ['0' => ['color' => '#00000040', 'x' => '0', 'y' => '12', 'blur' => '32', 'spread' => '0', 'position' => 'outset']], 'style' => '0px 12px 32px 0px #00000040']]], 'position' => 'left'], 'quotes' => ['color' => '#DEDEDEFF', 'icon' => ['slug' => 'icon-quotes-left', 'name' => 'quotes left', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" id="icon-quotes-left" viewBox="0 0 32 32">
<path d="M7.031 14c3.866 0 7 3.134 7 7s-3.134 7-7 7-7-3.134-7-7l-0.031-1c0-7.732 6.268-14 14-14v4c-2.671 0-5.182 1.040-7.071 2.929-0.364 0.364-0.695 0.751-0.995 1.157 0.357-0.056 0.724-0.086 1.097-0.086zM25.031 14c3.866 0 7 3.134 7 7s-3.134 7-7 7-7-3.134-7-7l-0.031-1c0-7.732 6.268-14 14-14v4c-2.671 0-5.182 1.040-7.071 2.929-0.364 0.364-0.695 0.751-0.995 1.157 0.358-0.056 0.724-0.086 1.097-0.086z"/>
</svg>']], 'stars' => ['position' => '3'], 'spacing' => ['space_after_image' => ['breakpoint_base' => ['number' => 40, 'unit' => 'px', 'style' => '40px']]], 'layout' => ['force_vertical_stacking' => null], 'typography' => ['testimonial' => ['typography' => ['custom' => ['customTypography' => ['fontSize' => ['breakpoint_base' => ['number' => 44, 'unit' => 'px', 'style' => '44px']]]]]]]]];
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
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 240, 'max' => 1080, 'step' => 1], 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px']],
        true,
        false,
        [],
      ), c(
        "stack_vertically_at",
        "Stack Vertically at",
        [],
        ['type' => 'breakpoint_dropdown', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "content_position",
        "Content Position",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'left', 'text' => 'Left', 'icon' => 'AlignLeftIcon'], '1' => ['value' => 'center', 'text' => 'Center', 'icon' => 'AlignCenterIcon'], '2' => ['value' => 'right', 'text' => 'Right', 'icon' => 'AlignRightIcon']]],
        false,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "wrapper",
        "Wrapper",
        [c(
        "boxed",
        "Boxed",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline', 'colorOptions' => ['type' => 'solidAndGradient'], 'condition' => ['path' => 'design.wrapper.boxed', 'operand' => 'is set', 'value' => '']],
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
      ), c(
        "image",
        "Image",
        [c(
        "disable",
        "Disable",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "position",
        "Position",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['text' => 'Left', 'label' => 'Label', 'value' => 'left'], '1' => ['text' => 'Right', 'value' => 'right']]],
        false,
        false,
        [],
      ), c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => '%'], 'defaultType' => 'px'], 'rangeOptions' => ['step' => 1, 'min' => 0, 'max' => 1000]],
        true,
        false,
        [],
      ), c(
        "height",
        "Height",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => '%'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 0, 'max' => 1000, 'step' => 1]],
        true,
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
        "quotes",
        "Quotes",
        [c(
        "icon",
        "Icon",
        [],
        ['type' => 'icon', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
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
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['step' => 1, 'min' => 0, 'max' => 256], 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px']],
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
      "EssentialElements\\typography_with_effects_and_align",
      "Title",
      "title",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_effects_and_align",
      "Testimonial",
      "testimonial",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_effects_and_align",
      "Name",
      "name",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_effects_and_align",
      "Occupation",
      "occupation",
       ['type' => 'popout']
     )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "stars",
        "Stars",
        [c(
        "position",
        "Position",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => '0', 'text' => 'First'], '1' => ['text' => 'Second', 'value' => '1'], '2' => ['text' => 'Third', 'value' => '3'], '3' => ['text' => 'Fourth', 'value' => '5'], '4' => ['text' => 'Fifth', 'value' => '6']]],
        false,
        false,
        [],
      ), c(
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
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => '%', '2' => 'em', '3' => 'rem', '4' => 'custom'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        true,
        false,
        [],
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
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em', '2' => 'rem', '3' => 'custom'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "below_title",
        "Below Title",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em', '2' => 'rem', '3' => 'custom'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "below_testimonial",
        "Below Testimonial",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em', '2' => 'rem', '3' => 'custom'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "below_stars",
        "Below Stars",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em', '2' => 'rem', '3' => 'custom']], 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "below_name",
        "Below Name",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em', '2' => 'rem', '3' => 'custom'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "below_occupation",
        "Below Occupation",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em', '2' => 'rem', '3' => 'custom'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
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
      )],
        ['type' => 'section', 'layout' => 'vertical', 'condition' => ['path' => 'content.content.image', 'operand' => 'is set', 'value' => ''], 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "stars",
        "Stars",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['text' => '1', 'label' => 'Label', 'value' => 'one'], '1' => ['text' => '2', 'value' => 'two'], '2' => ['text' => '3', 'value' => 'three'], '3' => ['text' => '4', 'value' => 'four'], '4' => ['text' => '5', 'value' => 'five']]],
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
        "testimonial",
        "Testimonial",
        [],
        ['type' => 'text', 'layout' => 'vertical', 'textOptions' => ['multiline' => true]],
        false,
        false,
        [],
      ), c(
        "name",
        "Name",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "occupation",
        "Occupation",
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
        return 2000;
    }

    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'image_url', 'path' => 'content.content.image'], '1' => ['accepts' => 'string', 'path' => 'content.content.title'], '2' => ['accepts' => 'string', 'path' => 'content.content.testimonial'], '3' => ['accepts' => 'string', 'path' => 'content.content.name'], '4' => ['accepts' => 'string', 'path' => 'content.content.occupation'], '5' => ['accepts' => 'string', 'path' => 'content.content.button.text'], '6' => ['accepts' => 'url', 'path' => 'content.content.button.link']];
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
        return ['design.layout.stack_vertically_at', 'design.typography.button.custom.size.full_width_at', 'design.typography.button.style', 'design.button.custom.size.full_width_at', 'design.button.styles', 'design.wrapper.boxed'];
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}

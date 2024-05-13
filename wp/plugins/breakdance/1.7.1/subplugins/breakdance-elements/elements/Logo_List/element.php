<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\LogoList",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class LogoList extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return '<svg viewBox="0 0 501 323" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" fill-rule="evenodd" clip-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2"><path fill="currentColor" d="M71.329 92.61V60.093h79.725v-35h198.002v35h79.725V92.61h71.329v137.931h-71.329v32.518h-79.725v35H151.054v-35H71.329v-32.518H0V92.61h71.329Zm279.828 2.483v132.966h44.754V95.093h-44.754Zm-202.204 0h-44.754v132.966h44.754V95.093Zm281.958 32.517v67.931h34.199V127.61h-34.199ZM35 127.61v67.931h34.199V127.61H35Zm281.157 135.449V60.093H183.953v202.966h132.204Z"/></svg>';
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
        return 'Logo List';
    }

    static function className()
    {
        return 'bde-logo-list';
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
        return ['content' => ['setting' => ['type_of_logo_bar' => 'un-static-image-grid', 'logos' => null, 'logos_dynamic_meta' => null]], 'design' => ['container' => ['max_width' => 'null', 'top_margin' => 'null', 'bottom_margin' => 'null', 'scrollable' => false, 'gradient_overlay' => 'both', 'gradient' => [], 'gradient_color' => 'null', 'gradient_width' => 'null', 'end_outside_the_viewport' => true, 'margin' => ['base' => ['right' => ['number' => 'auto', 'unit' => 'custom', 'style' => 'auto'], 'left' => ['number' => 'auto', 'unit' => 'custom', 'style' => 'auto'], 'top' => ['number' => 20, 'unit' => 'px', 'style' => '20px'], 'bottom' => ['number' => 20, 'unit' => 'px', 'style' => '20px']]]], 'image_grid_setting' => ['horizontal_spacing' => ['base' => ['number' => 'null', 'unit' => 'px', 'style' => '']], 'vertical_spacing' => ['base' => ['number' => 'null', 'unit' => 'px', 'style' => '']], 'alignment' => ['tablet_landscape' => 'flex-start', 'base' => 'flex-start', 'tablet_portait' => 'flex-start'], 'item_per_row' => ['base' => '6', 'phone_portrait' => '2', 'tablet_portait' => '4'], 'gap' => null, 'image_padding' => ['breakpoint_base' => ['number' => 10, 'unit' => 'px', 'style' => '10px']], 'item_bg_color' => '#f4f4f4', 'item_alignment' => ['base' => 'center'], 'items_alignment' => null, 'items_per_row' => null], 'carousel_setting' => ['animation_type' => 'stop-on-hover', 'gap_between_logos' => 'null', 'item_width' => null, 'item_height' => 'null', 'animation_duration_sec' => null, 'gap' => ['breakpoint_base' => ['number' => 25, 'unit' => 'px', 'style' => '25px']], 'gradient_overlay' => 'both', 'gradient_color' => '#FFFFFFFF', 'gradient_width' => ['breakpoint_base' => ['number' => 50, 'unit' => 'px', 'style' => '50px']], 'end_outside_the_viewport' => true, 'full_width_right' => null, 'animation_duration' => ['number' => 40, 'unit' => 's', 'style' => '40s']], 'scrollable_row_setting' => ['item_per_row' => ['base' => '5'], 'items_per_view' => ['breakpoint_base' => 5], 'gap' => null], 'spacing' => ['space_above' => null, 'space_below' => null, 'between_logos' => ['breakpoint_base' => ['number' => 60, 'unit' => 'px', 'style' => '60px']], 'container' => null], 'images' => ['item_bg_color' => '#F8F8F8FF', 'item_bg_color_hover' => null, 'image_padding' => ['breakpoint_base' => ['number' => 10, 'unit' => 'px', 'style' => '10px']], 'gap' => ['breakpoint_base' => ['number' => 5, 'unit' => 'px', 'style' => '5px']]], 'list' => ['type' => 'animated', 'width' => null, 'alignment' => null, 'gradient_overlay' => ['type' => 'both', 'color' => '#FFFFFFFF', 'width' => ['breakpoint_base' => ['number' => 30, 'unit' => 'px', 'style' => '30px']]], 'animation' => ['animation_type' => 'stop-on-hover', 'animation_duration' => ['number' => 60, 'unit' => 's', 'style' => '60s'], 'direction' => 'to-left'], 'vertical_gap' => null], 'logo' => ['max_width' => null, 'width' => ['breakpoint_base' => ['number' => 190, 'unit' => 'px', 'style' => '190px']], 'max_height' => ['breakpoint_base' => ['number' => 90, 'unit' => 'px', 'style' => '90px']], 'height' => ['breakpoint_base' => ['number' => 40, 'unit' => 'px', 'style' => '40px']]]]];
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
        "list",
        "List",
        [c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em', '2' => 'rem', '3' => 'custom'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 0, 'max' => 1200, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "type",
        "Type",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'animated', 'text' => 'Animated'], '1' => ['text' => 'Side Scroll', 'value' => 'side-scroll'], '2' => ['text' => 'Show All', 'value' => 'show-all']]],
        false,
        false,
        [],
      ), c(
        "vertical_gap",
        "Vertical Gap",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1], 'condition' => ['path' => 'design.list.type', 'operand' => 'equals', 'value' => 'show-all']],
        true,
        false,
        [],
      ), c(
        "alignment",
        "Alignment",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'flex-start', 'text' => 'Left', 'icon' => 'AlignLeftIcon'], '1' => ['text' => 'Center', 'value' => 'center', 'icon' => 'AlignCenterIcon'], '2' => ['text' => 'Right', 'value' => 'flex-end', 'icon' => 'AlignRightIcon']], 'condition' => ['path' => 'design.list.type', 'operand' => 'equals', 'value' => 'show-all']],
        false,
        false,
        [],
      ), c(
        "animation",
        "Animation",
        [c(
        "direction",
        "Direction",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'to-left', 'text' => 'To Left', 'icon' => 'ArrowLeftIcon'], '1' => ['value' => 'to-right', 'text' => 'To Right', 'icon' => 'ArrowRightIcon']]],
        false,
        false,
        [],
      ), c(
        "animation_type",
        "Animation Type",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'Infinite', 'label' => 'Label', 'value' => 'infinite'], '1' => ['text' => 'Start on Hover', 'value' => 'start-on-hover'], '2' => ['text' => 'Stop on Hover', 'value' => 'stop-on-hover']]],
        false,
        false,
        [],
      ), c(
        "animation_duration",
        "Animation Duration",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 's'], 'defaultType' => 's'], 'rangeOptions' => ['min' => 10, 'max' => 50, 'step' => 1]],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout'], 'condition' => ['path' => 'design.list.type', 'operand' => 'equals', 'value' => 'animated']],
        false,
        false,
        [],
      ), c(
        "gradient_overlay",
        "Gradient Overlay",
        [c(
        "type",
        "Type",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['text' => 'Left', 'label' => 'Label', 'value' => 'left'], '1' => ['text' => 'Both', 'value' => 'both'], '2' => ['text' => 'Right', 'label' => 'Label', 'value' => 'right']]],
        false,
        false,
        [],
      ), c(
        "color",
        "Color",
        [],
        ['type' => 'color', 'layout' => 'inline', 'condition' => ['path' => 'design.list.gradient_overlay.type', 'operand' => 'is one of', 'value' => ['0' => 'left', '1' => 'both', '2' => 'right']]],
        false,
        false,
        [],
      ), c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => 'design.list.gradient_overlay.type', 'operand' => 'is set', 'value' => ['0' => 'left', '1' => 'both', '2' => 'right']], 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em', '2' => 'rem', '3' => 'custom'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        true,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout'], 'condition' => ['path' => 'design.list.type', 'operand' => 'is one of', 'value' => ['0' => 'side-scroll', '1' => 'animated']]],
        false,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "logo",
        "Logo",
        [c(
        "height",
        "Height",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em', '2' => 'rem', '3' => 'custom'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 0, 'max' => 150, 'step' => 1]],
        true,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "spacing",
        "Spacing",
        [c(
        "between_logos",
        "Between Logos",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em', '2' => 'rem', '3' => 'custom'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
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
        "setting",
        "Setting",
        [c(
        "logos",
        "Logos",
        [c(
        "image",
        "Image",
        [],
        ['type' => 'wpmedia', 'layout' => 'vertical', 'mediaOptions' => ['multiple' => false]],
        false,
        false,
        [],
      ), c(
        "size",
        "Size",
        [],
        ['type' => 'media_size_dropdown', 'layout' => 'vertical'],
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
        ['type' => 'repeater', 'layout' => 'vertical', 'repeaterOptions' => ['titleTemplate' => 'Logo Item', 'defaultTitle' => 'Logo Item', 'buttonName' => 'Add Logo', 'galleryMode' => true, 'galleryMediaPath' => 'image']],
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
        return 1200;
    }

    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'gallery', 'path' => 'content.setting.logos'], '1' => ['accepts' => 'image_url', 'path' => 'content.setting.logos[].image']];
    }

    static function additionalClasses()
    {
        return [['name' => 'gradient-overlay__left', 'template' => '{% if design.list.gradient_overlay.type == "left" %}
yes
{% endif %}'], ['name' => 'gradient-overlay__both', 'template' => '{% if design.list.gradient_overlay.type == "both" %}
yes
{% endif %}'], ['name' => 'gradient-overlay__right', 'template' => '{% if design.list.gradient_overlay.type == "right" %}
yes
{% endif %}'], ['name' => 'to-right', 'template' => '{% if design.list.animation.direction == "to-right" %}
yes
{% endif %}'], ['name' => 'un-image-carousel-container', 'template' => '{% if design.list.type == \'animated\' %} 
	yes
{% endif %}'], ['name' => 'un-scrollable-image-container', 'template' => '{% if design.list.type == \'side-scroll\'%} 
	yes
{% endif %}'], ['name' => 'un-static-image-grid', 'template' => '{% if design.list.type == \'show-all\' or design.list.type is null %} 
	yes
{% endif %}']];
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
        return false;
    }
}

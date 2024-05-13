<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\MenuBuilder",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class MenuBuilder extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'BarsIcon';
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
        return 'Menu Builder';
    }

    static function className()
    {
        return 'bde-menu';
    }

    static function category()
    {
        return 'site';
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
        return [['slug' => 'EssentialElements\MenuLink', 'defaultProperties' => ['content' => ['content' => ['link' => ['type' => 'url', 'url' => '#'], 'text' => 'Home']]]], ['slug' => 'EssentialElements\MenuDropdown', 'defaultProperties' => ['content' => ['content' => ['text' => 'Products', 'columns' => ['0' => ['title' => 'Airplanes', 'links' => ['0' => ['text' => 'Flyer Basic', 'description' => 'Go fast, don\'t crash.', 'url' => '#'], '1' => ['text' => 'Flyer Intermediate', 'description' => 'Go even faster and don\'t crash.', 'url' => '#'], '2' => ['text' => 'Flyer Matrix Edition', 'description' => 'Haul cargo long distances.'], '3' => ['text' => 'ExpressRocket', 'description' => 'Launch cargo into space on the cheap.'], '4' => ['text' => 'Boomslang Level 1', 'description' => 'Get certified for night operations.', 'url' => '#']]], '1' => ['title' => 'Helicopter', 'links' => ['0' => ['text' => 'Liftmeister 21', 'description' => 'The ultimate light utility helicopter.'], '1' => ['text' => 'Heavymeister', 'description' => 'Lift large amounts of weight.'], '2' => ['text' => 'Triblade Vortex', 'description' => 'Multi-engine reliability.'], '3' => ['text' => 'Rugged & Heavy Industry', 'description' => 'Designed to take a beating and keep flying.'], '4' => ['text' => 'Identity 6000', 'description' => 'The ultimate in business luxury travel.']]]]]], 'design' => ['columns' => ['stack' => null]]]], ['slug' => 'EssentialElements\MenuDropdown', 'defaultProperties' => ['content' => ['content' => ['text' => 'Use Cases', 'columns' => ['0' => ['links' => ['0' => ['text' => 'Small Business', 'description' => 'Accept and display widgets.'], '1' => ['text' => 'Medium Business', 'description' => 'Widgets for teams larger than 100.'], '2' => ['text' => 'Enterprise', 'description' => 'Enterprise-grade reliability and security.'], '3' => ['text' => 'Organizations', 'description' => 'Do even more with widgets.']]]]]]]], ['slug' => 'EssentialElements\MenuDropdown', 'defaultProperties' => ['content' => ['content' => ['text' => 'Developers', 'columns' => ['0' => ['title' => 'Documentation', 'links' => ['0' => ['text' => 'Getting Started Guide'], '1' => ['text' => 'API Reference'], '2' => ['text' => 'Sample Code'], '3' => ['text' => 'Plugin Developer Handbook']]], '1' => ['title' => 'Developer Community', 'links' => ['0' => ['text' => 'Forums'], '1' => ['text' => 'Slack Channel'], '2' => ['text' => 'Facebook Group']]]]]]]], ['slug' => 'EssentialElements\MenuCustomDropdown', 'defaultProperties' => ['content' => ['content' => ['text' => 'About']]], 'children' => ['0' => ['slug' => 'EssentialElements\Heading', 'defaultProperties' => ['content' => ['content' => ['text' => 'Custom Dropdown', 'tags' => 'h5']], 'design' => ['spacing' => ['margin_bottom' => ['breakpoint_base' => ['number' => 12, 'unit' => 'px', 'style' => '12px']]]]]], '1' => ['slug' => 'EssentialElements\Text', 'defaultProperties' => ['content' => ['content' => ['text' => 'Design your dropdowns with Breakdance.']], 'design' => ['spacing' => ['margin_bottom' => ['breakpoint_base' => ['number' => 13, 'unit' => 'px', 'style' => '13px']]]]]], '2' => ['slug' => 'EssentialElements\Image', 'defaultProperties' => ['content' => ['content' => ['size' => null, 'caption_type' => 'none', 'caption_position' => 'below-image', 'link_type' => 'none', 'loading' => 'lazy', 'image' => ['id' => -1, 'type' => 'external_image', 'url' => 'https://images.unsplash.com/photo-1573164574472-797cdf4a583a?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=600&q=80', 'alt' => '', 'caption' => '']]]]]]]];
    }

    static function cssTemplate()
    {
        $template = file_get_contents(__DIR__ . '/css.twig');
        return $template;
    }

    static function designControls()
    {
        return [c(
        "desktop_menu",
        "Desktop Menu",
        [c(
        "links",
        "Links",
        [getPresetSection(
      "EssentialElements\\typography_with_effects_with_hoverable_color_and_effects",
      "Typography",
      "typography",
       ['type' => 'popout']
     ), c(
        "effect",
        "Effect",
        [c(
        "effect_type",
        "Effect Type",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'Underline', 'value' => 'underline'], '1' => ['text' => 'Overline', 'value' => 'overline'], '2' => ['value' => 'strikethrough', 'text' => 'Strikethrough'], '3' => ['text' => 'Background', 'value' => 'background']]],
        false,
        false,
        [],
      ), c(
        "effect_direction",
        "Effect Direction",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'left', 'text' => 'Left'], '1' => ['text' => 'Center', 'value' => 'center'], '2' => ['text' => 'Right', 'value' => 'right'], '3' => ['text' => 'Top', 'value' => 'top'], '4' => ['text' => 'Bottom', 'value' => 'bottom']], 'buttonBarOptions' => ['layout' => 'multiline', 'size' => 'small'], 'condition' => ['path' => 'design.desktop_menu.links.effect.effect_type', 'operand' => 'is none of', 'value' => ['0' => 'none']]],
        false,
        false,
        [],
      ), c(
        "thickness",
        "Thickness",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['0' => ['0' => ['path' => 'design.desktop_menu.links.effect.effect_type', 'operand' => 'is one of', 'value' => ['0' => 'overline', '1' => 'underline', '2' => 'strikethrough']]]]],
        false,
        false,
        [],
      ), c(
        "color",
        "Color",
        [],
        ['type' => 'color', 'layout' => 'inline', 'condition' => ['path' => 'design.desktop_menu.links.effect.effect_type', 'operand' => 'not equals', 'value' => 'none']],
        false,
        false,
        [],
      ), c(
        "radius",
        "Radius",
        [],
        ['type' => 'border_radius', 'layout' => 'vertical', 'condition' => ['path' => 'design.desktop_menu.links.effect.effect_type', 'operand' => 'equals', 'value' => 'background']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "active",
        "Active",
        [c(
        "effect_type",
        "Effect Type",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'Underline', 'value' => 'underline'], '1' => ['text' => 'Overline', 'value' => 'overline'], '2' => ['value' => 'strikethrough', 'text' => 'Strikethrough'], '3' => ['text' => 'Background', 'value' => 'background']]],
        false,
        false,
        [],
      ), c(
        "thickness",
        "Thickness",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['0' => ['0' => ['path' => 'design.desktop_menu.links.active.effect_type', 'operand' => 'is one of', 'value' => ['0' => 'overline', '1' => 'underline', '2' => 'strikethrough']]]]],
        false,
        false,
        [],
      ), c(
        "color",
        "Color",
        [],
        ['type' => 'color', 'layout' => 'inline', 'condition' => ['0' => ['0' => ['path' => 'design.desktop_menu.links.active.effect_type', 'operand' => 'is set', 'value' => '']]]],
        false,
        false,
        [],
      ), c(
        "radius",
        "Radius",
        [],
        ['type' => 'border_radius', 'layout' => 'vertical', 'condition' => ['path' => 'design.desktop_menu.links.effect.effect_type', 'operand' => 'equals', 'value' => 'background']],
        false,
        false,
        [],
      ), c(
        "text_color",
        "Text Color",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\spacing_padding_all",
      "Size",
      "size",
       ['type' => 'popout']
     ), c(
        "space_between",
        "Space Between",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "dropdown_arrows",
        "Dropdown Arrows",
        [c(
        "disable",
        "Disable",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "color",
        "Color",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        true,
        [],
      ), c(
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "gap",
        "Gap",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "offset",
        "Offset",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "closed_rotation",
        "Closed Rotation",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'deg'], 'defaultType' => 'deg']],
        false,
        false,
        [],
      ), c(
        "open_rotation",
        "Open Rotation",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'deg'], 'defaultType' => 'deg']],
        false,
        false,
        [],
      ), c(
        "custom_arrow",
        "Custom Arrow",
        [],
        ['type' => 'icon', 'layout' => 'vertical', 'iconOptions' => ['suggestions' => ['0' => 'arrow']]],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'vertical', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "dropdowns",
        "Dropdowns",
        [c(
        "wrapper",
        "Wrapper",
        [c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline'],
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
     ), c(
        "placement",
        "Placement",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'left', 'text' => 'Left'], '1' => ['text' => 'Center', 'value' => 'center'], '2' => ['text' => 'Right', 'value' => 'right'], '3' => ['text' => 'Full Width', 'value' => 'full-width'], '4' => ['text' => 'Section Width', 'value' => 'section-width']], 'buttonBarOptions' => ['layout' => 'multiline', 'size' => 'small'], 'condition' => ['0' => ['0' => ['path' => 'design.desktop_menu.vertical', 'operand' => 'is not set', 'value' => '']], '1' => ['0' => ['path' => 'design.desktop_menu.vertical_mode', 'operand' => 'is none of', 'value' => ['0' => 'accordion']]]]],
        false,
        false,
        [],
      ), c(
        "width",
        "Width",
        [],
        ['type' => 'number', 'layout' => 'inline', 'rangeOptions' => ['step' => 50, 'min' => 100, 'max' => 1140], 'condition' => ['0' => ['0' => ['path' => 'design.desktop_menu.dropdowns.wrapper.placement', 'operand' => 'is none of', 'value' => ['0' => 'full-width', '1' => 'section-width']]]]],
        false,
        false,
        [],
      ), c(
        "caret_radius",
        "Caret Radius",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['0' => ['0' => ['path' => 'design.desktop_menu.vertical', 'operand' => 'is not set', 'value' => '']], '1' => ['0' => ['path' => 'design.desktop_menu.vertical_mode', 'operand' => 'is none of', 'value' => ['0' => 'accordion', '1' => 'side']]]]],
        false,
        false,
        [],
      ), c(
        "disable_caret",
        "Disable Caret",
        [],
        ['type' => 'toggle', 'layout' => 'inline', 'condition' => ['0' => ['0' => ['path' => 'design.desktop_menu.vertical', 'operand' => 'is not set', 'value' => '']], '1' => ['0' => ['path' => 'design.desktop_menu.vertical_mode', 'operand' => 'is none of', 'value' => ['0' => 'accordion', '1' => 'side']]]]],
        false,
        false,
        [],
      ), c(
        "offset",
        "Offset",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['step' => 1, 'min' => 0, 'max' => 100], 'condition' => ['0' => ['0' => ['path' => 'design.desktop_menu.vertical_mode', 'operand' => 'is none of', 'value' => ['0' => 'accordion']], '1' => ['path' => 'design.desktop_menu.vertical', 'operand' => 'is set', 'value' => '']], '1' => ['0' => ['path' => 'design.desktop_menu.vertical', 'operand' => 'is not set', 'value' => '']]]],
        false,
        false,
        [],
      ), c(
        "animation",
        "Animation",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'None', 'value' => 'none'], '1' => ['text' => 'Fade', 'value' => 'fade'], '2' => ['text' => 'Stripe', 'value' => 'stripe'], '3' => ['text' => 'Skew', 'value' => 'skew'], '4' => ['text' => 'Rotate', 'value' => 'rotate'], '5' => ['text' => 'Scale Down', 'value' => 'scale-down'], '6' => ['text' => 'Scale Down Right', 'value' => 'scale-down-r']], 'condition' => ['0' => ['0' => ['path' => 'design.desktop_menu.vertical', 'operand' => 'is not set', 'value' => ''], '1' => ['path' => 'design.desktop_menu.vertical_mode', 'operand' => 'is none of', 'value' => ['0' => 'accordion']]]]],
        false,
        false,
        [],
      ), c(
        "transition_duration",
        "Transition Duration",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'ms'], 'defaultType' => 'ms']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "links",
        "Links",
        [getPresetSection(
      "EssentialElements\\typography_with_hoverable_color",
      "Title",
      "title",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_hoverable_color",
      "Description",
      "description",
       ['type' => 'popout']
     ), c(
        "graphic",
        "Graphic",
        [c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        true,
        [],
      ), c(
        "icon",
        "Icon",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        true,
        [],
      ), c(
        "image_opacity",
        "Image Opacity",
        [],
        ['type' => 'number', 'layout' => 'inline'],
        false,
        true,
        [],
      ), c(
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "alignment",
        "Alignment",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'flex-start', 'text' => 'Top'], '1' => ['text' => 'Middle', 'value' => 'center'], '2' => ['text' => 'Bottom', 'value' => 'flex-end']]],
        false,
        false,
        [],
      ), c(
        "translate",
        "Translate",
        [c(
        "x",
        "X",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "y",
        "Y",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\spacing_padding_all",
      "Padding",
      "padding",
       ['type' => 'popout']
     ), c(
        "radius",
        "Radius",
        [],
        ['type' => 'border_radius', 'layout' => 'vertical'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "spacing",
        "Spacing",
        [c(
        "after_graphic",
        "After Graphic",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "before_description",
        "Before Description",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "between_links",
        "Between Links",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\spacing_padding_all",
      "Padding",
      "padding",
       ['type' => 'popout']
     ), c(
        "radius",
        "Radius",
        [],
        ['type' => 'border_radius', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "hover_background",
        "Hover Background",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "columns",
        "Columns",
        [getPresetSection(
      "EssentialElements\\typography",
      "Title",
      "title",
       ['type' => 'popout']
     ), c(
        "space_after_title",
        "Space After Title",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "gap_between_columns",
        "Gap Between Columns",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "stack_vertically",
        "Stack Vertically",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "additional_section",
        "Additional Section",
        [getPresetSection(
      "EssentialElements\\AtomV1MenuDropdownLinkDesign",
      "Links",
      "links",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\AtomV1MenuColumnDesign",
      "Columns",
      "columns",
       ['type' => 'popout']
     ), c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\spacing_padding_all",
      "Padding",
      "padding",
       ['type' => 'popout']
     ), c(
        "stack_horizontally",
        "Stack Horizontally",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => 'design.desktop_menu.dropdowns.additional_section.stack_horizontally', 'operand' => 'is set', 'value' => '']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "open_dropdowns_on_click",
        "Open Dropdowns on Click",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'vertical', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
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
        "vertical",
        "Vertical",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "vertical_mode",
        "Vertical Mode",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'Dropdown', 'value' => 'dropdown'], '1' => ['text' => 'Accordion', 'value' => 'accordion'], '2' => ['text' => 'Side', 'value' => 'side']], 'condition' => ['0' => ['0' => ['path' => 'design.desktop_menu.vertical', 'operand' => 'is set', 'value' => '']]]],
        false,
        false,
        [],
      ), c(
        "vertical_alignment",
        "Alignment",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'flex-start', 'text' => 'Left', 'icon' => 'AlignLeftIcon'], '1' => ['text' => 'Center', 'value' => 'center', 'icon' => 'AlignCenterIcon'], '2' => ['text' => 'Right', 'value' => 'flex-end', 'icon' => 'AlignRightIcon']], 'condition' => ['0' => ['0' => ['path' => 'design.desktop_menu.vertical', 'operand' => 'is set', 'value' => '']]]],
        false,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "mobile_menu",
        "Mobile Menu",
        [c(
        "show_at",
        "Show At",
        [],
        ['type' => 'breakpoint_dropdown', 'layout' => 'vertical', 'breakpointOptions' => ['enableNever' => true]],
        false,
        false,
        [],
      ), c(
        "mode",
        "Mode",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['value' => 'default', 'text' => 'Default'], '1' => ['text' => 'Fullscreen', 'value' => 'fullscreen'], '2' => ['text' => 'Offcanvas', 'value' => 'offcanvas']]],
        false,
        false,
        [],
      ), c(
        "offcanvas_width",
        "Offcanvas Width",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => 'design.mobile_menu.mode', 'operand' => 'equals', 'value' => 'offcanvas']],
        false,
        false,
        [''],
      ), c(
        "offcanvas_position",
        "Offcanvas Position",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'left', 'text' => 'Left'], '1' => ['text' => 'Right', 'value' => 'right']], 'condition' => ['path' => 'design.mobile_menu.mode', 'operand' => 'equals', 'value' => 'offcanvas']],
        false,
        false,
        [],
      ), c(
        "top_bar",
        "Top Bar",
        [c(
        "logo",
        "Logo",
        [],
        ['type' => 'wpmedia', 'layout' => 'vertical', 'mediaOptions' => ['acceptedFileTypes' => ['0' => 'image'], 'multiple' => false]],
        false,
        false,
        [],
      ), c(
        "logo_width",
        "Logo Width",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['0' => ['0' => ['path' => 'design.mobile_menu.top_bar.logo', 'operand' => 'is set', 'value' => '']]]],
        true,
        false,
        [],
      ), c(
        "logo_on_right",
        "Logo On Right",
        [],
        ['type' => 'toggle', 'layout' => 'inline', 'condition' => ['path' => 'design.mobile_menu.top_bar.logo', 'operand' => 'is set', 'value' => '']],
        false,
        false,
        [],
      ), c(
        "logo_url",
        "Logo URL",
        [],
        ['type' => 'url', 'layout' => 'vertical', 'condition' => ['path' => 'design.mobile_menu.top_bar.logo', 'operand' => 'is set', 'value' => '']],
        false,
        false,
        [],
      ), c(
        "padding",
        "Padding",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "close_button",
        "Close Button",
        [c(
        "disable",
        "Disable",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "color",
        "Color",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        true,
        [],
      ), c(
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "bar_height",
        "Bar Height",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout'], 'condition' => ['path' => 'design.mobile_menu.mode', 'operand' => 'is one of', 'value' => ['0' => 'offcanvas', '1' => 'fullscreen']]],
        false,
        false,
        [],
      ), c(
        "vertically_align_center",
        "Vertically Align Center",
        [],
        ['type' => 'toggle', 'layout' => 'inline', 'condition' => ['path' => 'design.mobile_menu.mode', 'operand' => 'is one of', 'value' => ['0' => 'fullscreen', '1' => 'offcanvas']]],
        false,
        false,
        [],
      ), c(
        "offset",
        "Offset",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => 'design.mobile_menu.mode', 'operand' => 'is none of', 'value' => ['0' => 'fullscreen', '1' => 'offcanvas']], 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px']],
        false,
        false,
        [],
      ), c(
        "links",
        "Links",
        [c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        true,
        [],
      ), c(
        "no_border",
        "No Border",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "border",
        "Border",
        [],
        ['type' => 'color', 'layout' => 'inline', 'condition' => ['path' => 'design.mobile_menu.links.no_border', 'operand' => 'is not set', 'value' => '']],
        false,
        false,
        [],
      ), c(
        "typography",
        "Typography",
        [getPresetSection(
      "EssentialElements\\typography",
      "Level 1",
      "level_1",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography",
      "Level 2",
      "level_2",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography",
      "Level 3",
      "level_3",
       ['type' => 'popout']
     )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "active",
        "Active",
        [c(
        "color",
        "Color",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "padding",
        "Padding",
        [c(
        "level_1",
        "Level 1",
        [],
        ['type' => 'spacing_complex', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "level_2",
        "Level 2",
        [],
        ['type' => 'spacing_complex', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "level_3",
        "Level 3",
        [],
        ['type' => 'spacing_complex', 'layout' => 'vertical'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "dropdowns_like_desktop",
        "Dropdowns Like Desktop",
        [],
        ['type' => 'toggle', 'layout' => 'inline', 'condition' => ['path' => 'content.content.elements', 'operand' => 'equals', 'value' => 'hide-this-field']],
        false,
        false,
        [],
      ), c(
        "dropdowns",
        "Dropdowns",
        [c(
        "hide_link_graphic",
        "Hide Link Graphic",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "hide_link_description",
        "Hide Link Description",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout'], 'condition' => ['path' => 'design.mobile_menu.links.dropdowns_like_desktop', 'operand' => 'is set', 'value' => '']],
        false,
        false,
        [],
      ), c(
        "remove_column_titles",
        "Remove Column Titles",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "dropdown_arrows",
        "Dropdown Arrows",
        [c(
        "disable",
        "Disable",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "color",
        "Color",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        true,
        [],
      ), c(
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "gap",
        "Gap",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "offset",
        "Offset",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "closed_rotation",
        "Closed Rotation",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'deg'], 'defaultType' => 'deg']],
        false,
        false,
        [],
      ), c(
        "open_rotation",
        "Open Rotation",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'deg'], 'defaultType' => 'deg']],
        false,
        false,
        [],
      ), c(
        "custom_arrow",
        "Custom Arrow",
        [],
        ['type' => 'icon', 'layout' => 'vertical'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'vertical', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "horizontally_center_links",
        "Horizontally Center Links",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "toggle",
        "Toggle",
        [c(
        "icon",
        "Icon",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        true,
        [],
      ), c(
        "icon_open",
        "Icon Open",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "wrapper",
        "Wrapper",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        true,
        [],
      ), c(
        "wrapper_open",
        "Wrapper Open",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "wrapper_padding",
        "Wrapper Padding",
        [c(
        "padding",
        "Padding",
        [],
        ['type' => 'spacing_complex', 'layout' => 'vertical'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "wrapper_radius",
        "Wrapper Radius",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "animation",
        "Animation",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'Disabled', 'value' => 'disabled'], '1' => ['text' => 'None', 'value' => 'none'], '2' => ['text' => 'Arrow', 'value' => 'arrow'], '3' => ['text' => 'Collapse', 'value' => 'collapse'], '4' => ['value' => 'elastic', 'text' => 'Elastic'], '5' => ['text' => 'Emphatic', 'value' => 'emphatic'], '6' => ['text' => 'Minus', 'value' => 'minus'], '7' => ['text' => 'Slider', 'value' => 'slider'], '8' => ['text' => 'Spin', 'value' => 'spin'], '9' => ['text' => 'Spring', 'value' => 'spring'], '10' => ['text' => 'Squeeze', 'value' => 'squeeze'], '11' => ['value' => 'stand', 'text' => 'Stand'], '12' => ['text' => 'Vortex', 'value' => 'vortex'], '13' => ['text' => '3DX', 'value' => '3dx'], '14' => ['text' => '3DXY', 'value' => '3dxy'], '15' => ['text' => '3DY', 'value' => '3dy']]],
        false,
        false,
        [],
      ), c(
        "custom_icon",
        "Custom Icon",
        [c(
        "bar_width",
        "Bar Width",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "bar_height",
        "Bar Height",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "bar_spacing",
        "Bar Spacing",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "bar_radius",
        "Bar Radius",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "open_icon",
        "Open Icon",
        [],
        ['type' => 'icon', 'layout' => 'vertical', 'iconOptions' => ['suggestions' => ['0' => 'menu', '1' => 'burger', '2' => 'ellipsis']]],
        false,
        false,
        [],
      ), c(
        "close_icon",
        "Close Icon",
        [],
        ['type' => 'icon', 'layout' => 'vertical', 'condition' => ['path' => 'design.mobile_menu.toggle.custom_icon.open_icon', 'operand' => 'is set', 'value' => '']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "transition_duration",
        "Transition Duration",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'ms'], 'defaultType' => 'ms']],
        false,
        false,
        [],
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
        "elements",
        "Elements",
        [],
        ['type' => 'add_registered_children', 'layout' => 'vertical', 'addRegisteredChildrenOptions' => ['addExtraElementsBySlug' => ['0' => 'EssentialElements\TextLink', '1' => 'EssentialElements\Button']]],
        false,
        false,
        [],
      ), c(
        "accessibility",
        "Accessibility",
        [c(
        "attributes",
        "Attributes",
        [c(
        "name",
        "Name",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "value",
        "Value",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      )],
        ['type' => 'inline_repeater', 'layout' => 'vertical'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'vertical', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "builder_behavior",
        "Keep Menu Selected While Styling",
        [],
        ['type' => 'builder_behavior', 'layout' => 'vertical', 'items' => ['0' => ['value' => 'live', 'text' => 'Live'], '1' => ['text' => 'Edit', 'value' => 'edit']]],
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
        return ['0' =>  ['inlineStyles' => ['/* Hide menu during load */
.breakdance-menu {
  display: none;
}'],'builderCondition' => 'return true;','frontendCondition' => 'return false;','title' => 'Hide menu during load - Builder only',],'1' =>  ['scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/awesome-menu@1/awesome-menu.js'],'styles' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/awesome-menu@1/awesome-menu.css'],'title' => 'Breakdance Menu',],'2' =>  ['inlineScripts' => ['{% set dropdowns = design.desktop_menu.dropdowns %}
new AwesomeMenu("%%SELECTOR%% .breakdance-menu", {
  dropdown: {
    openOnClick: {{ dropdowns.open_dropdowns_on_click ? \'true\' : \'false\' }},
    mode: {
      desktop: \'{{ design.desktop_menu.vertical ? design.desktop_menu.vertical_mode|default(\'dropdown\') : \'dropdown\' }}\'
    },
    placement: \'{{ dropdowns.wrapper.placement|default(\'left\') }}\',
    width: {{ dropdowns.wrapper.width ?? \'null\' }},
    animation: \'{{ dropdowns.wrapper.animation|default(\'fade\') }}\'
  },
  link: {
    effect: \'{{ design.desktop_menu.links.effect.effect_type }}\',
    effectDirection: \'{{ design.desktop_menu.links.effect.effect_direction }}\',
  },
  mobile: {
    breakpoint: \'{{ design.mobile_menu.show_at }}\',
    mode: \'{{ design.mobile_menu.mode|default(\'accordion\') }}\',
    offcanvasPosition: \'{{ design.mobile_menu.offcanvas_position|default(\'left\') }}\',
    offset: {{ design.mobile_menu.offset.number ?? \'null\' }},
    followLinks: {{ design.mobile_menu.top_level_links_clickable ? "true" : "false" }}
  }
});'],'builderCondition' => 'return false;','frontendCondition' => 'return true;','title' => 'Frontend init',],];
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
        return [

'onPropertyChange' => [['script' => 'if (window.breakdanceMenus && window.breakdanceMenus[%%ID%%]) {
  window.breakdanceMenus[%%ID%%].destroy();
}

{% set dropdowns = design.desktop_menu.dropdowns %}
const options = {
  dropdown: {
    openOnClick: true,
    mode: {
      desktop: \'{{ design.desktop_menu.vertical ? design.desktop_menu.vertical_mode|default(\'dropdown\') : \'dropdown\' }}\'
    },
    placement: \'{{ dropdowns.wrapper.placement|default(\'left\') }}\',
    width: {{ dropdowns.wrapper.width ?? \'null\' }},
    animation: \'{{ dropdowns.wrapper.animation|default(\'fade\') }}\'
  },
  link: {
    effect: \'{{ design.desktop_menu.links.effect.effect_type }}\',
    effectDirection: \'{{ design.desktop_menu.links.effect.effect_direction }}\',
  },
  mobile: {
    breakpoint: \'{{ design.mobile_menu.show_at }}\',
    mode: \'{{ design.mobile_menu.mode|default(\'accordion\') }}\',
    offcanvasPosition: \'{{ design.mobile_menu.offcanvas_position|default(\'left\') }}\',
    offset: {{ design.mobile_menu.offset.number ?? \'null\' }},
    followLinks: {{ design.mobile_menu.top_level_links_clickable ? "true" : "false" }}
  },
  isBuilder: true
};

window.breakdanceMenus[%%ID%%] = new AwesomeMenu("%%SELECTOR%% .breakdance-menu", options);',
],['script' => 'const menuId = \'%%ID%%\';

if (
  menuId &&
  window.breakdanceMenus &&
  window.breakdanceMenus[menuId]
) {
  const firstDropdown = document.querySelector(\'%%SELECTOR%% .breakdance-dropdown\');
  const anyOpen = document.querySelector(\'%%SELECTOR%% .breakdance-dropdown--open\');
  if (firstDropdown && !anyOpen) window.breakdanceMenus[menuId].openDropdown(firstDropdown);
}','dependencies' => ['design.desktop_menu.dropdowns'],
],],

'onMovedElement' => [['script' => 'if (window.breakdanceMenus && window.breakdanceMenus[%%ID%%]) {
  window.breakdanceMenus[%%ID%%].refresh();
}',
],],

'onBeforeDeletingElement' => [['script' => 'if (window.breakdanceMenus && window.breakdanceMenus[%%ID%%]) {
  window.breakdanceMenus[%%ID%%].destroy();
  delete window.breakdanceMenus[%%ID%%];
}',
],],

'onMountedElement' => [['script' => 'if (!window.breakdanceMenus) {
  window.breakdanceMenus = {};
}

if (window.breakdanceMenus && window.breakdanceMenus[%%ID%%]) {
  window.breakdanceMenus[%%ID%%].destroy();
}

{% set dropdowns = design.desktop_menu.dropdowns %}
const options = {
  dropdown: {
    openOnClick: true,
    mode: {
      desktop: \'{{ design.desktop_menu.vertical ? design.desktop_menu.vertical_mode|default(\'dropdown\') : \'dropdown\' }}\'
    },
    placement: \'{{ dropdowns.wrapper.placement|default(\'left\') }}\',
    width: {{ dropdowns.wrapper.width ?? \'null\' }},
    animation: \'{{ dropdowns.wrapper.animation|default(\'fade\') }}\'
  },
  dropdown: {
    openOnClick: true,
    placement: \'{{ design.desktop_menu.dropdowns.wrapper.placement|default(\'left\') }}\',
    width: {{ design.desktop_menu.dropdowns.wrapper.width ?? \'null\' }},
    mode: {
      desktop: \'{{ design.desktop_menu.vertical_mode|default(\'dropdown\') }}\' 
    },
    animation: \'{{ design.desktop_menu.dropdowns.wrapper.animation|default(\'fade\') }}\'
  },
  link: {
    effect: \'{{ design.desktop_menu.links.effect.effect_type }}\',
    effectDirection: \'{{ design.desktop_menu.links.effect.effect_direction }}\',
  },
  mobile: {
    breakpoint: \'{{ design.mobile_menu.show_at }}\',
    mode: \'{{ design.mobile_menu.mode|default(\'accordion\') }}\',
    offcanvasPosition: \'{{ design.mobile_menu.offcanvas_position|default(\'left\') }}\',
    offset: {{ design.mobile_menu.offset.number ?? \'null\' }},
    followLinks: {{ design.mobile_menu.top_level_links_clickable ? "true" : "false" }}
  },
  isBuilder: true
};

window.breakdanceMenus[%%ID%%] = new AwesomeMenu("%%SELECTOR%% .breakdance-menu", options);',
],],];
    }

    static function nestingRule()
    {
        return ["type" => "container-restricted",   ];
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
        return 50;
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
        return ['design.mobile_menu.show_at'];
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}

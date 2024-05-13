<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\Author",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class Author extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'UserIcon';
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
        return 'Author';
    }

    static function className()
    {
        return 'bde-author';
    }

    static function category()
    {
        return 'dynamic';
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
        return ['content' => ['content' => ['profil_picture' => true, 'display_name' => true, 'biography' => true, 'website_link' => true, 'all_posts_link' => true, 'all_posts_text' => null, 'link_on_name_image' => 'none', 'html_tag' => null, 'image' => true, 'name' => true, 'all_posts_link_text' => 'All Posts']], 'design' => ['button' => ['button_styles' => null], 'spacing' => null, 'image' => ['size' => null, 'border_radius' => null], 'layout' => null, 'typography' => null, 'container' => ['background' => '#FFFFFFFF', 'borders' => ['radius' => ['breakpoint_base' => ['all' => ['number' => 4, 'unit' => 'px', 'style' => '4px'], 'topLeft' => ['number' => 4, 'unit' => 'px', 'style' => '4px'], 'topRight' => ['number' => 4, 'unit' => 'px', 'style' => '4px'], 'bottomLeft' => ['number' => 4, 'unit' => 'px', 'style' => '4px'], 'bottomRight' => ['number' => 4, 'unit' => 'px', 'style' => '4px'], 'editMode' => 'all']], 'shadow' => ['breakpoint_base' => ['shadows' => ['0' => ['color' => '#00000025', 'x' => '5', 'y' => '20', 'blur' => '75', 'spread' => '0', 'position' => 'outset']], 'style' => '5px 20px 75px 0px #00000025']]], 'padding' => ['padding' => ['breakpoint_base' => ['left' => ['number' => 40, 'unit' => 'px', 'style' => '40px'], 'right' => ['number' => 40, 'unit' => 'px', 'style' => '40px'], 'top' => ['number' => 40, 'unit' => 'px', 'style' => '40px'], 'bottom' => ['number' => 40, 'unit' => 'px', 'style' => '40px']]]], 'width' => null], 'links' => ['website' => ['style' => 'text']]]];
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
        "width",
        "Width",
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
     ), getPresetSection(
      "EssentialElements\\spacing_padding_all",
      "Padding",
      "padding",
       ['type' => 'popout']
     )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "layout",
        "Layout",
        [c(
        "image_position",
        "Image Position",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['text' => 'Top', 'value' => 'column'], '1' => ['value' => 'row', 'text' => 'Left'], '2' => ['text' => 'Right', 'value' => 'row-reverse']], 'buttonBarOptions' => ['size' => 'small', 'layout' => 'default']],
        false,
        false,
        [],
      ), c(
        "top_at",
        "Top At",
        [],
        ['type' => 'breakpoint_dropdown', 'layout' => 'inline', 'condition' => ['path' => 'design.layout.image_position', 'operand' => 'is one of', 'value' => ['0' => 'row', '1' => 'row-reverse']]],
        false,
        false,
        [],
      ), c(
        "alignment",
        "Alignment",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'flex-start', 'text' => 'left', 'icon' => 'AlignLeftIcon'], '1' => ['value' => 'center', 'text' => 'center', 'icon' => 'AlignCenterIcon'], '2' => ['value' => 'flex-end', 'text' => 'right', 'icon' => 'AlignRightIcon']]],
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
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 10, 'max' => 200, 'step' => 1], 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px']],
        true,
        false,
        [],
      ), c(
        "border_radius",
        "Border Radius",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        true,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "links",
        "Links",
        [getPresetSection(
      "EssentialElements\\AtomV1ButtonDesign",
      "All Posts",
      "all_posts",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\AtomV1ButtonDesign",
      "Website",
      "website",
       ['type' => 'popout']
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
      "Name",
      "name",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography",
      "Biography",
      "biography",
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
        "after_image",
        "After Image",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "after_name",
        "After Name",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => [], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "after_biography",
        "After Biography",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "between_links",
        "Between Links",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
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
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "name",
        "Name",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "biography",
        "Biography",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "all_posts_link",
        "All Posts Link",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "all_posts_link_text",
        "All Posts Link Text",
        [],
        ['type' => 'text', 'layout' => 'inline', 'placeholder' => 'View all posts', 'condition' => ['path' => 'content.content.all_posts_link', 'operand' => 'is set', 'value' => '']],
        false,
        false,
        [],
      ), c(
        "website_link",
        "Website Link",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "website_link_text",
        "Website Link Text",
        [],
        ['type' => 'text', 'layout' => 'inline', 'placeholder' => 'Website', 'condition' => ['path' => 'content.content.website_link', 'operand' => 'is set', 'value' => '']],
        false,
        false,
        [],
      ), c(
        "name_html_tag",
        "Name HTML Tag",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'h1', 'text' => 'H1'], '1' => ['text' => 'H2', 'value' => 'h2'], '2' => ['text' => 'H3', 'value' => 'h3'], '3' => ['text' => 'H4', 'value' => 'h4'], '4' => ['text' => 'H5', 'value' => 'h5'], '5' => ['text' => 'H6', 'value' => 'h6'], '6' => ['text' => 'Div', 'value' => 'div']], 'condition' => ['path' => 'content.content.display_name', 'operand' => 'is set', 'value' => '']],
        false,
        false,
        [],
      ), c(
        "link_on_name_image",
        "Link On Name & Image",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'none', 'text' => 'None'], '1' => ['text' => 'Website', 'value' => 'website'], '2' => ['text' => 'All Posts', 'value' => 'allposts']]],
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
        return 1350;
    }

    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'string', 'path' => 'content.content.website_link_text']];
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
        return ['design.layout.top_at', 'design.links.all_posts.custom.size.full_width_at', 'design.links.all_posts.style', 'design.links.website.custom.size.full_width_at', 'design.links.website.style'];
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return ['content.content', 'design.links'];
    }
}

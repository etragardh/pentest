<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\SocialIcons",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class SocialIcons extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'ThumbsUpIcon';
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
        return 'Social Icons';
    }

    static function className()
    {
        return 'bde-social-icons';
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
        return ['design' => ['size' => ['space_between' => ['number' => 6, 'unit' => 'px', 'style' => '6px']], 'icons' => ['size' => ['breakpoint_base' => ['number' => 24, 'unit' => 'px', 'style' => '24px']], 'padding' => ['breakpoint_base' => ['number' => 10, 'unit' => 'px', 'style' => '10px']], 'shape' => '0', 'margin' => ['breakpoint_base' => ['number' => 8, 'unit' => 'px', 'style' => '8px']]], 'layout' => ['width' => null, 'direction' => ['breakpoint_base' => 'row']], 'hover' => ['scale' => 'undefined'], 'effect' => ['opacity' => 1, 'opacity_hover' => 0.8, 'scale_on_hover' => 1.1, 'transition_duration' => null]], 'content' => ['content' => ['social_networks' => ['0' => ['link' => ['type' => 'url', 'url' => 'https://www.facebook.com/'], 'type' => 'bde-social-icons__icon-facebook'], '1' => ['type' => 'bde-social-icons__icon-twitter', 'link' => ['type' => 'url', 'url' => 'https://twitter.com/']], '2' => ['type' => 'bde-social-icons__icon-instagram', 'link' => ['type' => 'url', 'url' => 'https://www.instagram.com/']], '3' => ['type' => 'bde-social-icons__icon-linkedin', 'link' => ['type' => 'url', 'url' => 'http://linkedin.com/']], '4' => ['type' => 'bde-social-icons__icon-youtube', 'link' => ['type' => 'url', 'url' => 'https://www.youtube.com/']]]]]];
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
        "icons",
        "Icons",
        [c(
        "icon_color",
        "Icon Color",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        true,
        [],
      ), c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        true,
        [],
      ), c(
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['step' => 1, 'min' => 8, 'max' => 100], 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px']],
        true,
        false,
        [],
      ), c(
        "padding",
        "Padding",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['step' => 1, 'min' => 0, 'max' => 50], 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px']],
        true,
        false,
        [],
      ), c(
        "shape",
        "Shape",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['value' => '100%', 'text' => 'Circle'], '1' => ['text' => 'Square', 'value' => '0'], '2' => ['text' => 'Custom', 'value' => 'custom']]],
        false,
        false,
        [],
      ), c(
        "radius",
        "Radius",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => '%'], 'defaultType' => 'px'], 'rangeOptions' => ['step' => 1, 'min' => 0, 'max' => 100], 'condition' => ['path' => 'design.icons.shape', 'operand' => 'equals', 'value' => 'custom']],
        false,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "layout",
        "Layout",
        [c(
        "direction",
        "Direction",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'row', 'text' => 'Horizontal', 'icon' => 'EllipsisIcon'], '1' => ['value' => 'column', 'text' => 'Horizontal', 'icon' => 'EllipsisVerticalIcon']], 'buttonBarOptions' => ['size' => 'small']],
        true,
        false,
        [],
      ), c(
        "space_between",
        "Space Between",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['step' => 1, 'min' => 0, 'max' => 128], 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px']],
        true,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "effect",
        "Effect",
        [c(
        "transition_duration",
        "Transition Duration",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'ms'], 'defaultType' => 'ms'], 'rangeOptions' => ['min' => 0, 'max' => 1000, 'step' => 1]],
        false,
        false,
        [],
      ), c(
        "scale_on_hover",
        "Scale On Hover",
        [],
        ['type' => 'number', 'layout' => 'inline', 'rangeOptions' => ['min' => 1, 'max' => 2, 'step' => 0.1]],
        false,
        false,
        [],
      ), c(
        "opacity",
        "Opacity",
        [],
        ['type' => 'number', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 1, 'step' => 0.1]],
        false,
        true,
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
        "social_networks",
        "Social Networks",
        [c(
        "link",
        "Link",
        [],
        ['type' => 'link', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "type",
        "Type",
        [],
        ['type' => 'dropdown', 'layout' => 'vertical', 'items' => ['0' => ['text' => 'Custom', 'value' => 'bde-social-icons__icon-custom'], '1' => ['text' => 'Facebook', 'label' => 'Label', 'value' => 'bde-social-icons__icon-facebook'], '2' => ['text' => 'Twitter', 'value' => 'bde-social-icons__icon-twitter'], '3' => ['text' => 'Instagram', 'value' => 'bde-social-icons__icon-instagram'], '4' => ['text' => 'YouTube', 'value' => 'bde-social-icons__icon-youtube'], '5' => ['value' => 'bde-social-icons__icon-dribbble', 'text' => 'Dribbble'], '6' => ['value' => 'bde-social-icons__icon-behance', 'text' => 'Behance'], '7' => ['value' => 'bde-social-icons__icon-github', 'text' => 'GitHub'], '8' => ['value' => 'bde-social-icons__icon-linkedin', 'text' => 'LinkedIn'], '9' => ['value' => 'bde-social-icons__icon-vimeo', 'text' => 'Vimeo']]],
        false,
        false,
        [],
      ), c(
        "icon",
        "Icon",
        [],
        ['type' => 'icon', 'layout' => 'vertical', 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'bde-social-icons__icon-custom']],
        false,
        false,
        [],
      ), c(
        "icon_color",
        "Icon Color",
        [],
        ['type' => 'color', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'bde-social-icons__icon-custom']],
        false,
        true,
        [],
      ), c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline', 'colorOptions' => ['type' => 'solidOnly'], 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'bde-social-icons__icon-custom']],
        false,
        true,
        [],
      )],
        ['type' => 'repeater', 'layout' => 'vertical', 'repeaterOptions' => ['titleTemplate' => 'Social Network', 'defaultTitle' => '', 'buttonName' => 'Add Icon']],
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
        return 750;
    }

    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'url', 'path' => 'content.content.social_networks[].link.url']];
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
        return false;
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}

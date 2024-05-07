<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;

\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\Breadcrumbs",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class Breadcrumbs extends \Breakdance\Elements\Element

{
    static function uiIcon()
    {
        return 'ChevronsRightIcon';
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
        return 'Breadcrumbs';
    }

    static function className()
    {
        return 'bde-breadcrumbs';
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
        return false;
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
                "content_alignment",
                "Content Alignment",
                [],
                ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'flex-start', 'text' => 'left', 'icon' => 'AlignLeftIcon'], '1' => ['text' => 'Center', 'value' => 'center', 'icon' => 'AlignCenterIcon'], '2' => ['text' => 'right', 'value' => 'flex-end', 'icon' => 'AlignRightIcon']]],
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
                "EssentialElements\\typography_with_hoverable_color",
                "Text",
                "text",
                ['type' => 'popout']
            ), getPresetSection(
                "EssentialElements\\typography_with_hoverable_color",
                "Links",
                "links",
                ['type' => 'popout']
            ), getPresetSection(
                "EssentialElements\\typography_with_hoverable_color",
                "Separator",
                "separator",
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
            "breadcrumbs",
            "Breadcrumbs",
            [c(
                "integration",
                "Integration",
                [],
                ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'yoast', 'text' => 'Yoast SEO'], '1' => ['text' => 'Rank Math', 'value' => 'rankmath']]],
                false,
                false,
                [],
            )],
            ['type' => 'section', 'layout' => 'vertical', 'sectionOptions' => ['type' => 'popout']],
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
        return ["type" => "final"];
    }

    static function spacingBars()
    {
        return ['0' => ['affectedPropertyPath' => 'design.spacing.margin_top.%%BREAKPOINT%%', 'cssProperty' => 'margin-top', 'location' => 'outside-top'], '1' => ['cssProperty' => 'margin-bottom', 'location' => 'outside-bottom', 'affectedPropertyPath' => 'design.spacing.margin_bottom.%%BREAKPOINT%%']];
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
        return 11000;
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
        return false;
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}

<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;

\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\Tabs",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class Tabs extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return '<svg fill="currentColor" viewBox="0 0 500 500">   <path d="M244.5 0c-17.11 0-31 13.89-31 31s13.89 31 31 31h63c17.11 0 31-13.89 31-31s-13.89-31-31-31h-63ZM406-1c-17.11 0-31 13.89-31 31s13.89 31 31 31h63c17.11 0 31-13.89 31-31S486.11-1 469-1h-63ZM0 485c0 8.278 6.721 15 15 15s15-6.722 15-15V15C30 6.72 23.279-.001 15-.001s-15 6.722-15 15V485ZM147 102c0 8.28 6.721 15 15 15s15-6.72 15-15V14c0-8.278-6.721-15-15-15s-15 6.722-15 15v88ZM470 484.999c0 8.279 6.721 15 15 15s15-6.721 15-15v-383c0-8.28-6.721-15.001-15-15.001s-15 6.722-15 15V485Z"/>   <path d="M15 470c-8.278 0-15 6.721-15 15s6.722 15 15 15h470c8.28 0 15.001-6.721 15.001-15s-6.722-15-15-15H15ZM15 0C6.721 0 0 6.721 0 15s6.721 15 15 15h147c8.279 0 15-6.721 15-15s-6.721-15-15-15H15ZM163 87c-8.279 0-15 6.721-15 15s6.721 15 15 15h322c8.279 0 15-6.721 15-15s-6.721-15-15-15H163ZM89 208.5c-8.279 0-15 6.721-15 15s6.721 15 15 15h322c8.279 0 15-6.721 15-15s-6.721-15-15-15H89ZM89 278.5c-8.279 0-15 6.721-15 15s6.721 15 15 15h322c8.279 0 15-6.721 15-15s-6.721-15-15-15H89ZM89 348.5c-8.279 0-15 6.721-15 15s6.721 15 15 15h322c8.279 0 15-6.721 15-15s-6.721-15-15-15H89Z"/> </svg>';
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
        return 'Tabs';
    }

    static function className()
    {
        return 'bde-tabs';
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
        return ['content' => ['content' => ['tabs' => ['0' => ['title' => 'My Account', 'content' => '<p>Neque vitae tempus quam pellentesque nec nam. Pretium aenean pharetra magna ac placerat vestibulum lectus mauris. Fermentum et sollicitudin ac orci phasellus egestas tellus rutrum. Volutpat blandit aliquam etiam erat. Ut tortor pretium viverra suspendisse potenti nullam ac tortor. Sit amet nisl suscipit adipiscing bibendum. Tellus in hac habitasse platea. Turpis egestas integer eget aliquet nibh. Morbi non arcu risus quis varius quam quisque id diam. Vitae ultricies leo integer malesuada nunc vel.</p>'], '1' => ['title' => 'Company', 'content' => '<p>Convallis tellus id interdum velit. Enim lobortis scelerisque fermentum dui faucibus in ornare quam. Sed id semper risus in hendrerit gravida. Amet facilisis magna etiam tempor orci eu. Ac feugiat sed lectus vestibulum mattis ullamcorper velit sed ullamcorper.&nbsp;</p>'], '2' => ['title' => 'Team Members', 'content' => '<p>Fringilla urna porttitor rhoncus dolor purus non enim praesent elementum. Eu turpis egestas pretium aenean pharetra. Cras ornare arcu dui vivamus arcu felis bibendum ut tristique. Morbi quis commodo odio aenean sed. Pulvinar mattis nunc sed blandit libero volutpat sed. Aliquam sem fringilla ut morbi.</p>', 'icon' => []], '3' => ['title' => 'Billing', 'content' => '<p>Convallis tellus id interdum velit. Enim lobortis scelerisque fermentum dui faucibus in ornare quam. Sed id semper risus in hendrerit gravida. Amet facilisis magna etiam tempor orci eu. Ac feugiat sed lectus vestibulum mattis ullamcorper velit sed ullamcorper.&nbsp;</p>']], 'active_tab' => 1]], 'design' => ['tabs' => ['style' => 'tabs', 'space_between' => null, 'position' => 'center', 'separator' => ['color' => null], 'background' => null, 'text' => null, 'bar' => ['radius' => null, 'separator' => null, 'shadow' => null], 'icon' => null, 'mobile_dropdown' => ['visible_at' => 'breakpoint_phone_landscape']], 'spacing' => ['spacing' => null, 'wrapper' => null], 'typography' => ['tab' => null], 'content' => ['padding' => null], 'size' => ['width' => null]]];
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
        return [getPresetSection(
            "EssentialElements\\tabs_design",
            "Tabs",
            "tabs",
            ['type' => 'popout']
        ), getPresetSection(
            "EssentialElements\\typography_with_align",
            "Content",
            "content",
            ['type' => 'popout']
        ), c(
            "size",
            "Size",
            [c(
                "width",
                "Width",
                [],
                ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 320, 'max' => 1200, 'step' => 1], 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em', '2' => '%', '3' => 'calc', '4' => 'custom'], 'defaultType' => 'px']],
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
            [getPresetSection(
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
                "tabs",
                "Tabs",
                [c(
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
                    "content",
                    "Content",
                    [],
                    ['type' => 'richtext', 'layout' => 'vertical'],
                    false,
                    false,
                    [],
                )],
                ['type' => 'repeater', 'layout' => 'vertical', 'repeaterOptions' => ['titleTemplate' => '{title}', 'defaultTitle' => 'Untitled Tab', 'buttonName' => 'Add Tab']],
                false,
                false,
                [],
            ), c(
                "active_tab",
                "Active Tab",
                [],
                ['type' => 'number', 'layout' => 'inline', 'dropdownOptions' => ['populate' => ['path' => 'content.content.tabs.tabs', 'text' => 'title', 'value' => 'title']]],
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
        return ['0' => ['scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/custom-tabs@1/tabs.js'], 'styles' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/custom-tabs@1/tabs.css'], 'title' => 'Load Breakdance Tabs'], '1' => ['inlineScripts' => ['new BreakdanceTabs(\'%%SELECTOR%%\', { activeTab: {{ content.content.active_tab|json_encode }}, isVertical: {{ design.tabs.vertical|json_encode }}, horizontalAt: {{ design.tabs.horizontal_at|json_encode }} } );'], 'builderCondition' => 'return false;', 'title' => 'Init BreakdanceTabs in the frontend']];
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

            'onPropertyChange' => [['script' => '(function() {
  if (window.breakdanceTabsInstances && window.breakdanceTabsInstances[%%ID%%]) {
    window.breakdanceTabsInstances[%%ID%%].destroy();
  }

  window.breakdanceTabsInstances[%%ID%%] = new BreakdanceTabs(\'%%SELECTOR%%\', { activeTab: {{ content.content.active_tab|json_encode }}, isVertical: {{ design.tabs.vertical|json_encode }}, horizontalAt: {{ design.tabs.horizontal_at|json_encode }} });
}());',
            ]],

            'onMountedElement' => [['script' => '(function() {
    if (!window.breakdanceTabsInstances) window.breakdanceTabsInstances = {};

    if (window.breakdanceTabsInstances && window.breakdanceTabsInstances[%%ID%%]) {
      window.breakdanceTabsInstances[%%ID%%].destroy();
    }

    window.breakdanceTabsInstances[%%ID%%] = new BreakdanceTabs(\'%%SELECTOR%%\', { activeTab: {{ content.content.active_tab|json_encode }}, isVertical: {{ design.tabs.vertical|json_encode }}, horizontalAt: {{ design.tabs.horizontal_at|json_encode }} } );
  }());',
            ]],

            'onMovedElement' => [['script' => '(function() {
  if (window.breakdanceTabsInstances && window.breakdanceTabsInstances[%%ID%%]) {
    window.breakdanceTabsInstances[%%ID%%].update();
  }
}());',
            ]],

            'onBeforeDeletingElement' => [['script' => '  (function() {
    if (window.breakdanceTabsInstances && window.breakdanceTabsInstances[%%ID%%]) {
      window.breakdanceTabsInstances[%%ID%%].destroy();
      delete window.breakdanceTabsInstances[%%ID%%];
    }
  }());',
            ]]];
    }

    static function nestingRule()
    {
        return ["type" => "final"];
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
        return 950;
    }

    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'string', 'path' => 'content.content.tabs[].title'], '1' => ['accepts' => 'string', 'path' => 'content.content.tabs[].content'], '2' => ['accepts' => 'string', 'path' => 'content.content.active_tab']];
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
        return ['design.tabs.responsive.visible_at', 'design.tabs.horizontal_at'];
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}

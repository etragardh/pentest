<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;

\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\Wooproducttabs",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class Wooproducttabs extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return '<svg aria-hidden="true" focusable="false"   class="svg-inline--fa fa-folder" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M448 96h-176L226.7 50.75C214.7 38.74 198.5 32 181.5 32H64C28.65 32 0 60.65 0 96v320c0 35.35 28.65 64 64 64h384c35.35 0 64-28.65 64-64V160C512 124.7 483.3 96 448 96zM480 416c0 17.64-14.36 32-32 32H64c-17.64 0-32-14.36-32-32V96c0-17.64 14.36-32 32-32h117.5c8.549 0 16.58 3.328 22.63 9.375L258.7 128H448c17.64 0 32 14.36 32 32V416zM416 192H96C87.16 192 80 199.2 80 208S87.16 224 96 224h320c8.838 0 16-7.164 16-16S424.8 192 416 192z"></path></svg>';
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
        return 'Product Tabs';
    }

    static function className()
    {
        return 'bde-wooproducttabs';
    }

    static function category()
    {
        return 'woocommerce';
    }

    static function badge()
    {
        return ['backgroundColor' => 'var(--brandWooCommerceBackground)', 'textColor' => 'var(--brandWooCommerce)', 'label' => 'Woo'];
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
        return [getPresetSection(
            "EssentialElements\\tabs_design",
            "Tabs",
            "tabs",
            ['type' => 'popout']
        ), c(
            "spacing",
            "Spacing",
            [getPresetSection(
                "EssentialElements\\spacing_margin_y",
                "Container",
                "container",
                ['type' => 'popout']
            ), c(
                "below_tabs",
                "Below Tabs",
                [],
                ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1], 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px']],
                false,
                false,
                [],
            )],
            ['type' => 'section'],
            false,
            false,
            [],
        ), c(
            "advanced",
            "Advanced",
            [getPresetSection(
                "EssentialElements\\WooGlobalStylerOverride",
                "Global Styles Override",
                "global_styles_override",
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
            "tabs",
            "Tabs",
            [c(
                "description",
                "Description",
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
                )],
                ['type' => 'section', 'layout' => 'vertical', 'sectionOptions' => ['type' => 'popout']],
                false,
                false,
                [],
            ), c(
                "additional_information",
                "Additional Information",
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
                )],
                ['type' => 'section', 'layout' => 'vertical', 'sectionOptions' => ['type' => 'popout']],
                false,
                false,
                [],
            ), c(
                "reviews",
                "Reviews",
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
                )],
                ['type' => 'section', 'layout' => 'vertical', 'sectionOptions' => ['type' => 'popout']],
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
        return ['0' => ['scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/custom-tabs@1/tabs.js'], 'styles' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/custom-tabs@1/tabs.css']], '1' => ['inlineScripts' => ['new BreakdanceTabs(\'%%SELECTOR%%\', { activeTab: {{ content.content.active_tab|json_encode }}, isVertical: {{ design.tabs.vertical|json_encode }}, horizontalAt: {{ design.tabs.horizontal_at|json_encode }} } );'], 'builderCondition' => 'return false;', 'frontendCondition' => 'return true;']];
    }

    static function settings()
    {
        return ['requiredPlugins' => ['0' => 'WooCommerce']];
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

          window.breakdanceTabsInstances[%%ID%%] = new BreakdanceTabs(\'%%SELECTOR%%\', { activeTab: 1, isVertical: {{ design.tabs.vertical|json_encode }}, horizontalAt: {{ design.tabs.horizontal_at|json_encode }} });
        }());',
            ]],

            'onMountedElement' => [['script' => '(function() {
            if (!window.breakdanceTabsInstances) window.breakdanceTabsInstances = {};

            if (window.breakdanceTabsInstances && window.breakdanceTabsInstances[%%ID%%]) {
              window.breakdanceTabsInstances[%%ID%%].destroy();
            }

            window.breakdanceTabsInstances[%%ID%%] = new BreakdanceTabs(\'%%SELECTOR%%\', { activeTab: 1, isVertical: {{ design.tabs.vertical|json_encode }}, horizontalAt: {{ design.tabs.horizontal_at|json_encode }} } );
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
        return ["type" => "final", "restrictedToBeADescendantOf" => ['EssentialElements\Productbuilder']];
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
        return 0;
    }

    static function dynamicPropertyPaths()
    {
        return false;
    }

    static function additionalClasses()
    {
        return [['name' => 'breakdance-woocommerce', 'template' => 'yes']];
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
        return ['content', 'design.tabs'];
    }
}

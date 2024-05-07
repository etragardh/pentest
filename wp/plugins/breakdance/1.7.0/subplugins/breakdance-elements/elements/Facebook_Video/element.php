<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\FacebookVideo",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class FacebookVideo extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'VideoIcon';
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
        return 'Facebook Video';
    }

    static function className()
    {
        return 'bde-facebook-video';
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
        return ['content' => ['video' => ['video_url' => 'https://www.facebook.com/facebook/videos/10153231379946729/']], 'design' => ['wrapper' => ['width' => ['number' => 500, 'unit' => 'px', 'style' => '500px']]]];
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
            "size",
            "Size",
            [c(
                "width",
                "Width",
                [],
                ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 300, 'max' => 1000, 'step' => 1]],
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
            "video",
            "Video",
            [c(
                "facebook_app",
                "Facebook App",
                [],
                ['type' => 'alert_box', 'layout' => 'vertical', 'alertBoxOptions' => ['style' => 'default', 'content' => '<p>You can set your Facebook App ID in the <a target="_blank" rel="noopener noreferrer nofollow" href="/wp-admin/admin.php?page=breakdance_settings&amp;tab=api_keys">API Key Settings</a></p>']],
                false,
                false,
                [],
            ), c(
                "video_url",
                "Video URL",
                [],
                ['type' => 'url', 'layout' => 'vertical'],
                false,
                false,
                [],
            ), c(
                "show_full_post",
                "Show Full Post",
                [],
                ['type' => 'toggle', 'layout' => 'inline'],
                false,
                false,
                [],
            ), c(
                "allow_fullscreen",
                "Allow Fullscreen",
                [],
                ['type' => 'toggle', 'layout' => 'inline'],
                false,
                false,
                [],
            ), c(
                "autoplay",
                "Autoplay",
                [],
                ['type' => 'toggle', 'layout' => 'inline'],
                false,
                false,
                [],
            ), c(
                "captions",
                "Captions",
                [],
                ['type' => 'toggle', 'layout' => 'inline'],
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
        return ['0' =>  ['scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/elements-reusable-code/facebook-elements.js'],],'1' =>  ['inlineScripts' => [' new BreakdanceFacebookSDK(\'%%SELECTOR%%\'); '],'builderCondition' => 'return false;','frontendCondition' => 'return true;',],];
    }

    static function settings()
    {
        return ['bypassPointerEvents' => true];
    }

    static function addPanelRules()
    {
        return false;
    }

    static public function actions()
    {
        return [

            'onPropertyChange' => [['script' => 'if (window.breakdanceFacebookInstances && window.breakdanceFacebookInstances[%%ID%%]) {
                                        window.breakdanceFacebookInstances[%%ID%%].update();
                                        }',
            ],],

            'onMountedElement' => [['script' => 'if (!window.breakdanceFacebookInstances) window.breakdanceFacebookInstances = {};

                                        if (window.breakdanceFacebookInstances && window.breakdanceFacebookInstances[%%ID%%]) {
                                        window.breakdanceFacebookInstances[%%ID%%].destroy();
                                        }
                                            window.breakdanceFacebookInstances[%%ID%%] = new BreakdanceFacebookSDK(\'%%SELECTOR%%\');
                                        ',
            ],],

            'onMovedElement' => [['script' => 'if (window.breakdanceFacebookInstances && window.breakdanceFacebookInstances[%%ID%%]) {
                                        window.breakdanceFacebookInstances[%%ID%%].update();
                                        }
                                        ',
            ],],

            'onBeforeDeletingElement' => [['script' => 'if (window.breakdanceFacebookInstances && window.breakdanceFacebookInstances[%%ID%%]) {
                                        window.breakdanceFacebookInstances[%%ID%%].destroy();
                                        delete window.breakdanceFacebookInstances[%%ID%%];
                                        }
                                        ',
            ],],];
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
        return 15000;
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

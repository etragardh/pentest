<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\FacebookPagePlugin",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class FacebookPagePlugin extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return '<svg aria-hidden="true" focusable="false"   class="svg-inline--fa fa-window-maximize" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M448 32H64c-35.35 0-64 28.65-64 63.1v320c0 35.35 28.65 64 64 64h384c35.35 0 64-28.65 64-64v-320C512 60.65 483.3 32 448 32zM480 416c0 17.64-14.36 32-32 32H64c-17.64 0-32-14.36-32-32V224h448V416zM480 192H32V96c0-17.64 14.36-32 32-32h384c17.64 0 32 14.36 32 32V192z"></path></svg>';
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
        return 'Facebook Page Plugin';
    }

    static function className()
    {
        return 'bde-facebook-page-plugin';
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
        return ['content' => ['page' => ['page_url' => 'https://www.facebook.com/Meta']], 'design' => ['size' => ['width' => 600, 'height' => 500]]];
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
        ['type' => 'number', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'rangeOptions' => ['step' => 1, 'min' => 340, 'max' => 500]],
        false,
        false,
        [],
      ), c(
        "height",
        "Height",
        [],
        ['type' => 'number', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'rangeOptions' => ['step' => 1, 'min' => 70, 'max' => 1000]],
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
        "page",
        "Page",
        [c(
        "facebook_app",
        "Facebook App",
        [],
        ['type' => 'alert_box', 'layout' => 'vertical', 'alertBoxOptions' => ['style' => 'default', 'content' => '<p>You can set your Facebook App ID in the <a target="_blank" rel="noopener noreferrer nofollow" href="/wp-admin/admin.php?page=breakdance_settings&amp;tab=api_keys">API Key Settings</a></p>']],
        false,
        false,
        [],
      ), c(
        "page_url",
        "Page URL",
        [],
        ['type' => 'url', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "messages",
        "Messages",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "events",
        "Events",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "small_header",
        "Small Header",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "hide_cover_photo",
        "Hide Cover Photo",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "profile_photos",
        "Profile Photos",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "cta_button",
        "CTA Button",
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
        return ['0' =>  ['scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/elements-reusable-code/facebook-elements.js'],'title' => 'Breakdance facebook elements',],'1' =>  ['inlineScripts' => [' new BreakdanceFacebookSDK(\'%%SELECTOR%%\'); '],'builderCondition' => 'return false;','frontendCondition' => 'return true;','title' => 'Frontend init',],];
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
        return [];
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

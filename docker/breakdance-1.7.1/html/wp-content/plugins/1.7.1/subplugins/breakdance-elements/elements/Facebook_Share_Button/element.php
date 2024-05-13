<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\FacebookShareButton",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class FacebookShareButton extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return '<svg aria-hidden="true" focusable="false"   class="svg-inline--fa fa-share-from-square" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M400 352c-8.836 0-16 7.164-16 16v79.1C384 465.7 369.7 480 352 480H64c-17.67 0-32-14.33-32-32V160c0-17.67 14.33-32 32-32h112C184.8 128 192 120.8 192 112S184.8 96 176 96H64C28.65 96 0 124.7 0 160v287.1c0 35.34 28.65 64 64 64L352 512c35.35 0 64-28.66 64-64v-80C416 359.2 408.8 352 400 352zM570.3 131.7l-160-127.1c-6.75-5.656-16.84-4.781-22.53 2.031c-5.656 6.781-4.75 16.88 2.031 22.53L515.8 128H368C270.1 128 192 206.1 192 304v32C192 344.8 199.2 352 208 352S224 344.8 224 336v-32C224 224.6 288.6 160 368 160h147.8l-126.1 99.72c-6.781 5.656-7.688 15.75-2.031 22.53C390.9 286 395.4 288 400 288c3.625 0 7.25-1.219 10.25-3.719l160-128C573.9 153.3 576 148.7 576 143.1S573.9 134.8 570.3 131.7z"></path></svg>';
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
        return 'Facebook Share Button';
    }

    static function className()
    {
        return 'bde-facebook-share-button';
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
        return ['content' => ['button' => ['layout' => 'button', 'url_to_like' => 'page']], 'design' => ['style' => ['layout' => 'button'], 'size' => ['size' => 'small']]];
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
        "style",
        "Style",
        [c(
        "layout",
        "Layout",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'Box Count', 'value' => 'box_count'], '1' => ['text' => 'Button Count', 'value' => 'button_count'], '2' => ['text' => 'Button', 'value' => 'button']]],
        false,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "size",
        "Size",
        [c(
        "size",
        "Size",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'small', 'text' => 'Small'], '1' => ['text' => 'Large', 'value' => 'large']]],
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
        "button",
        "Button",
        [c(
        "facebook_app",
        "Facebook App",
        [],
        ['type' => 'alert_box', 'layout' => 'vertical', 'alertBoxOptions' => ['style' => 'default', 'content' => '<p>You can set your Facebook App ID in the <a target="_blank" rel="noopener noreferrer nofollow" href="/wp-admin/admin.php?page=breakdance_settings&amp;tab=api_keys">API Key Settings</a></p>']],
        false,
        false,
        [],
      ), c(
        "url_to_like",
        "URL to Like",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'page', 'text' => 'Current Page'], '1' => ['text' => 'Custom URL', 'value' => 'custom_url']]],
        false,
        false,
        [],
      ), c(
        "custom_url",
        "Custom URL",
        [],
        ['type' => 'url', 'layout' => 'vertical', 'condition' => ['path' => 'content.button.url_to_like', 'operand' => 'equals', 'value' => 'custom_url']],
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
        return ['0' => ['accepts' => 'url', 'path' => 'content.button.custom_url']];
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
        return ['content.button.url_to_like', 'content.button.custom_url'];
    }
}

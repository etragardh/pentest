<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\FacebookLikeButton",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class FacebookLikeButton extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return '<svg aria-hidden="true" focusable="false"   class="svg-inline--fa fa-thumbs-up" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M88 192h-48C17.94 192 0 209.9 0 232v208C0 462.1 17.94 480 40 480h48C110.1 480 128 462.1 128 440v-208C128 209.9 110.1 192 88 192zM96 440C96 444.4 92.41 448 88 448h-48C35.59 448 32 444.4 32 440v-208C32 227.6 35.59 224 40 224h48C92.41 224 96 227.6 96 232V440zM512 221.5C512 187.6 484.4 160 450.5 160h-102.5c11.98-27.06 18.83-53.48 18.83-67.33C366.9 62.84 343.6 32 304.9 32c-41.22 0-50.7 29.11-59.12 54.81C218.1 171.1 160 184.8 160 208C160 217.1 167.5 224 176 224C180.1 224 184.2 222.4 187.3 219.3c52.68-53.04 67.02-56.11 88.81-122.5C285.3 68.95 288.2 64 304.9 64c20.66 0 29.94 16.77 29.94 28.67c0 10.09-8.891 43.95-26.62 75.48c-1.366 2.432-2.046 5.131-2.046 7.83C306.2 185.5 314 192 322.2 192h128.3C466.8 192 480 205.2 480 221.5c0 15.33-12.08 28.16-27.48 29.2c-8.462 .5813-14.91 7.649-14.91 15.96c0 12.19 12.06 12.86 12.06 30.63c0 14.14-10.11 26.3-24.03 28.89c-5.778 1.082-13.06 6.417-13.06 15.75c0 8.886 6.765 10.72 6.765 23.56c0 31.02-31.51 22.12-31.51 43.05c0 3.526 1.185 5.13 1.185 10.01C389 434.8 375.8 448 359.5 448H303.9c-82.01 0-108.3-64.02-127.9-64.02c-8.873 0-16 7.193-16 15.96C159.1 416.3 224.6 480 303.9 480h55.63c33.91 0 61.5-27.58 61.5-61.47c18.55-10.86 30.33-31 30.33-53.06c0-4.797-.5938-9.594-1.734-14.27c19.31-10.52 32.06-30.97 32.06-53.94c0-7.219-1.281-14.31-3.75-20.98C498.2 266.2 512 245.3 512 221.5z"></path></svg>';
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
        return 'Facebook Like Button';
    }

    static function className()
    {
        return 'bde-facebook-like-button';
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
        return ['content' => ['button' => ['layout' => 'standard', 'action_type' => 'like', 'url_to_like' => 'page', 'share_button' => false, 'size' => 'small']], 'design' => ['spacing' => ['margin_bottom' => null], 'style' => ['layout' => 'standard', 'theme' => 'light'], 'size' => ['size' => 'small']]];
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
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'standard', 'text' => 'Standard'], '1' => ['text' => 'Box Count', 'value' => 'box_count'], '2' => ['text' => 'Button Count', 'value' => 'button_count'], '3' => ['text' => 'Button', 'value' => 'button']]],
        false,
        false,
        [],
      ), c(
        "theme",
        "Theme",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'light', 'text' => 'Light'], '1' => ['text' => 'Dark', 'value' => 'dark']], 'placeholder' => ''],
        false,
        false,
        [''],
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
        "action_type",
        "Action Type",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'like', 'text' => 'Like'], '1' => ['text' => 'Recommend', 'value' => 'recommend']]],
        false,
        false,
        [],
      ), c(
        "share_button",
        "Share Button",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
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

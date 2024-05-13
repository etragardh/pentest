<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\TwitterEmbedTweet",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class TwitterEmbedTweet extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return '<svg viewBox="0 0 500 500" fill="currentColor">   <path fill-rule="nonzero" d="M380.159 24.015c-9.19 4.081-19.06 6.827-29.421 8.066 10.581-6.337 18.695-16.372 22.517-28.336a102.702 102.702 0 0 1-32.532 12.435C331.38 6.222 318.07 0 303.34 0c-28.288 0-51.218 22.94-51.218 51.218 0 4.014.46 7.932 1.325 11.667-42.567-2.132-80.303-22.527-105.576-53.523-4.398 7.567-6.923 16.362-6.923 25.763 0 17.764 9.035 33.444 22.776 42.624a51.116 51.116 0 0 1-23.199-6.405v.644c0 24.821 17.649 45.524 41.088 50.22a51.24 51.24 0 0 1-13.5 1.795c-3.294 0-6.511-.327-9.632-.912 6.52 20.347 25.436 35.163 47.848 35.576-17.524 13.74-39.61 21.931-63.615 21.931-4.129 0-8.21-.24-12.223-.72 22.67 14.528 49.586 23.007 78.517 23.007 94.217 0 145.723-78.047 145.723-145.733 0-2.218-.048-4.426-.144-6.625a104.228 104.228 0 0 0 25.57-26.512Z"/>   <path d="M450 50v400H50V50h15.246c13.798 0 25-11.202 25-25s-11.202-25-25-25H25C11.202 0 0 11.202 0 25v450c0 6.888 2.792 13.129 7.304 17.652l.022.022.022.022C11.871 497.208 18.112 500 25 500h450c6.888 0 13.129-2.792 17.652-7.304l.022-.022.022-.022C497.208 488.129 500 481.888 500 475V25c0-13.798-11.202-25-25-25h-40.246c-13.798 0-25 11.202-25 25s11.202 25 25 25H450Z"/> </svg>';
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
        return 'Twitter Embed Tweet';
    }

    static function className()
    {
        return 'bde-twitter-embed-tweet';
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
        return ['design' => ['wrapper' => ['width' => ['number' => 550, 'unit' => 'px', 'style' => '550px']], 'theme' => ['theme' => 'dark'], 'style' => ['theme' => 'light'], 'size' => ['width' => ['number' => 550, 'unit' => 'px', 'style' => '550px']], 'spacing' => ['margin_top' => null]], 'content' => ['tweet' => ['hide_conversation' => null, 'hide_media' => null]]];
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
        "theme",
        "Theme",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'light', 'text' => 'Light'], '1' => ['text' => 'Dark', 'value' => 'dark']]],
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
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 300, 'max' => 550, 'step' => 1]],
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
        "tweet",
        "Tweet",
        [c(
        "tweet_url",
        "Tweet URL",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "hide_conversation",
        "Hide Conversation",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "hide_media",
        "Hide Media",
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
        return ['0' =>  ['scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/elements-reusable-code/twitter-elements.js'],'builderCondition' => 'return true;',],'1' =>  ['inlineScripts' => [' new BreakdanceTwitter(\'%%SELECTOR%%\'); '],'builderCondition' => 'return false;','frontendCondition' => 'return true;',],];
    }

    static function settings()
    {
        return ['bypassPointerEvents' => true, 'proOnly' => true];
    }

    static function addPanelRules()
    {
        return false;
    }

    static public function actions()
    {
        return [

'onPropertyChange' => [['script' => 'if (window.breakdanceTwitterInstances && window.breakdanceTwitterInstances[%%ID%%]) {
                            window.breakdanceTwitterInstances[%%ID%%].update();
                            }',
],],

'onMountedElement' => [['script' => 'if (!window.breakdanceTwitterInstances) window.breakdanceTwitterInstances = {};
                            if (window.breakdanceTwitterInstances && window.breakdanceTwitterInstances[%%ID%%]) {
                            window.breakdanceTwitterInstances[%%ID%%].destroy();
                            }
                                window.breakdanceTwitterInstances[%%ID%%] = new BreakdanceTwitter(\'%%SELECTOR%%\');
                            ',
],],

'onMovedElement' => [['script' => 'if (window.breakdanceTwitterInstances && window.breakdanceTwitterInstances[%%ID%%]) {
                            window.breakdanceTwitterInstances[%%ID%%].update();
                            }
                            ',
],],

'onBeforeDeletingElement' => [['script' => 'if (window.breakdanceTwitterInstances && window.breakdanceTwitterInstances[%%ID%%]) {
                            window.breakdanceTwitterInstances[%%ID%%].destroy();
                            delete window.breakdanceTwitterInstances[%%ID%%];
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
        return [['name' => 'data-twitter-embed', 'template' => 'tweet'], ['name' => 'data-conversation', 'template' => '{% if content.tweet.hide_conversation %}
none
{% else %}
all
{% endif %}'], ['name' => 'data-cards', 'template' => '{% if content.tweet.hide_media %}
hidden
{% else %}
visible
{% endif %}'], ['name' => 'data-theme', 'template' => '{% if design.style.theme == \'light\' or design.style.theme is empty %}
light
{% elseif design.style.theme == \'dark\' %}
dark
{% endif %}'], ['name' => 'data-url', 'template' => '{{ content.tweet.tweet_url|default("https://twitter.com/NASA/status/1488994801584414721") }}']];
    }

    static function experimental()
    {
        return false;
    }

    static function order()
    {
        return 15500;
    }

    static function dynamicPropertyPaths()
    {
        return false;
    }

    static function additionalClasses()
    {
        return [['name' => 'js-twitter-embed', 'template' => 'yes']];
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

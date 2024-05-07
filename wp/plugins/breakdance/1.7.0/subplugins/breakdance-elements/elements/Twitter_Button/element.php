<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\TwitterButton",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class TwitterButton extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return '<svg viewBox="0 0 500 500" fill="currentColor">   <path fill-rule="nonzero" d="M250 172.573c-9.19 4.08-19.06 6.827-29.421 8.065 10.581-6.337 18.695-16.371 22.517-28.336a102.702 102.702 0 0 1-32.532 12.435c-9.343-9.957-22.652-16.18-37.382-16.18-28.288 0-51.218 22.94-51.218 51.219 0 4.013.461 7.931 1.325 11.666-42.566-2.131-80.303-22.527-105.576-53.522-4.398 7.566-6.923 16.362-6.923 25.762 0 17.764 9.035 33.445 22.776 42.624a51.116 51.116 0 0 1-23.199-6.404v.643c0 24.822 17.65 45.524 41.088 50.22a51.24 51.24 0 0 1-13.5 1.795c-3.294 0-6.51-.326-9.632-.912 6.52 20.347 25.437 35.163 47.848 35.576-17.524 13.741-39.609 21.932-63.614 21.932-4.13 0-8.21-.24-12.224-.72 22.67 14.528 49.586 23.006 78.517 23.006 94.217 0 145.723-78.046 145.723-145.732 0-2.218-.048-4.427-.144-6.626A104.228 104.228 0 0 0 250 172.573Z"/>   <path d="M500 326.443V173.557c0-2.089-.257-4.118-.74-6.057l-.01-.037-.007-.031c-2.734-10.84-12.558-18.875-24.243-18.875H306.166c-13.797 0-25 11.203-25 25 0 13.798 11.203 25 25 25H450v102.886H250.166c-13.797 0-25 11.202-25 25 0 13.797 11.203 25 25 25H475c13.798 0 25-11.203 25-25Z"/> </svg>';
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
        return 'Twitter Button';
    }

    static function className()
    {
        return 'bde-twitter-button';
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
        return ['design' => ['size' => ['size' => ''], 'spacing' => null], 'content' => ['button' => ['type' => 'share', 'text_to_tweet' => null, 'url_to_tweet' => 'current', 'custom_url' => null]]];
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
        "size",
        "Size",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => '', 'text' => 'Default'], '1' => ['text' => 'Large', 'value' => 'large']]],
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
        "type",
        "Type",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'share', 'text' => 'Share'], '1' => ['text' => 'Follow', 'value' => 'follow'], '2' => ['text' => 'Mention', 'value' => 'mention'], '3' => ['text' => 'Hashtag', 'value' => 'hashtag']]],
        false,
        false,
        [],
      ), c(
        "follow_user",
        "Follow User",
        [],
        ['type' => 'text', 'layout' => 'inline', 'condition' => ['path' => 'content.button.type', 'operand' => 'equals', 'value' => 'follow'], 'placeholder' => 'Do not include @'],
        false,
        false,
        [],
      ), c(
        "mention_user",
        "Mention User",
        [],
        ['type' => 'text', 'layout' => 'inline', 'condition' => ['path' => 'content.button.type', 'operand' => 'equals', 'value' => 'mention']],
        false,
        false,
        [],
      ), c(
        "hashtag",
        "Hashtag",
        [],
        ['type' => 'text', 'layout' => 'inline', 'condition' => ['path' => 'content.button.type', 'operand' => 'equals', 'value' => 'hashtag']],
        false,
        false,
        [],
      ), c(
        "show_count",
        "Show Count",
        [],
        ['type' => 'toggle', 'layout' => 'inline', 'condition' => ['path' => 'content.button.type', 'operand' => 'equals', 'value' => 'follow']],
        false,
        false,
        [],
      ), c(
        "via_username",
        "Via Username",
        [],
        ['type' => 'text', 'layout' => 'inline', 'condition' => ['path' => 'content.button.type', 'operand' => 'equals', 'value' => 'share'], 'placeholder' => 'Do not include @'],
        false,
        false,
        [],
      ), c(
        "show_screen_name",
        "Show Screen Name",
        [],
        ['type' => 'toggle', 'layout' => 'inline', 'condition' => ['path' => 'content.button.type', 'operand' => 'equals', 'value' => 'follow']],
        false,
        false,
        [],
      ), c(
        "text_to_tweet",
        "Text to Tweet",
        [],
        ['type' => 'text', 'layout' => 'vertical', 'textOptions' => ['multiline' => true], 'condition' => ['path' => 'content.button.type', 'operand' => 'equals', 'value' => 'share']],
        false,
        false,
        [],
      ), c(
        "url_to_tweet",
        "URL to Tweet",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'current', 'text' => 'Current Page'], '1' => ['text' => 'Custom URL', 'value' => 'custom']], 'condition' => ['path' => 'content.button.type', 'operand' => 'equals', 'value' => 'share']],
        false,
        false,
        [],
      ), c(
        "custom_url",
        "Custom URL",
        [],
        ['type' => 'url', 'layout' => 'vertical', 'condition' => ['path' => 'content.button.url_to_tweet', 'operand' => 'equals', 'value' => 'custom']],
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
        return ['0' =>  ['scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/elements-reusable-code/twitter-elements.js'],],'1' =>  ['inlineScripts' => [' new BreakdanceTwitter(\'%%SELECTOR%%\'); '],'builderCondition' => 'return false;','frontendCondition' => 'return true;',],];
    }

    static function settings()
    {
        return ['bypassPointerEvents' => true, 'dependsOnGlobalScripts' => true];
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
        return [['name' => 'data-twitter-embed', 'template' => 'button'], ['name' => 'data-type', 'template' => '{% if content.button.type == \'share\' or content.button.type is empty %}
share
{% elseif content.button.type == \'follow\' %}
follow
{% elseif content.button.type == \'mention\' %}
mention
{% elseif content.button.type == \'hashtag\' %}
hashtag
{% endif %}'], ['name' => 'data-hashtag', 'template' => '{{ content.button.hashtag|default("hashtag") }}'], ['name' => 'data-mention-user', 'template' => '{% if content.button.type == "mention" %}{{ content.button.mention_user|default("NASA") }}{% endif %}'], ['name' => 'data-follow-user', 'template' => '{% if content.button.type == "follow" %}{{ content.button.follow_user|default("NASA") }}{% endif %}'], ['name' => 'data-show-count', 'template' => '{% if content.button.type == "follow" %}
{% if content.button.show_count %}
true
{% else %}
false
{% endif %}
{% endif %}'], ['name' => 'data-show-screen-name', 'template' => '{% if content.button.type == "follow" %}
{% if content.button.show_screen_name %}
true
{% else %}
false
{% endif %}
{% endif %}'], ['name' => 'data-via', 'template' => '{% if content.button.type == "share" or content.button.type is empty %}{{ content.button.via_username }}{% endif %}'], ['name' => 'data-share-url', 'template' => '{% if content.button.type == "share" or content.button.type is empty %}{{ content.button.custom_url }}{% endif %}'], ['name' => 'data-tweet-text', 'template' => '{% if content.button.type == "share" or content.button.type is empty %}{{ content.button.text_to_tweet }}{% endif %}'], ['name' => 'data-size', 'template' => '{{ design.size.size }}']];
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
        return ['0' => ['accepts' => 'string', 'path' => 'content.button.follow_user'], '1' => ['accepts' => 'string', 'path' => 'content.button.mention_user'], '2' => ['accepts' => 'string', 'path' => 'content.button.hashtag'], '3' => ['accepts' => 'string', 'path' => 'content.button.via_username'], '4' => ['accepts' => 'string', 'path' => 'content.button.text_to_tweet'], '5' => ['accepts' => 'url', 'path' => 'content.button.custom_url']];
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

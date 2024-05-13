<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\InstagramPost",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class InstagramPost extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'ImageAndVideIcon';
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
        return 'Instagram Post';
    }

    static function className()
    {
        return 'bde-instagram-post';
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
        return ['design' => ['post' => ['width' => ['breakpoint_base' => ['number' => 326, 'unit' => 'px', 'style' => '326px']]]]];
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
        "post",
        "Post",
        [c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "hide_caption",
        "Hide Caption",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
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
        "post",
        "Post",
        [c(
        "post_url",
        "Post URL",
        [],
        ['type' => 'text', 'layout' => 'vertical', 'placeholder' => 'https://www.instagram.com/p/CTVIfBvLRXX/'],
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
        return ['0' =>  ['title' => 'Instagram Embed API','scripts' => ['https://www.instagram.com/embed.js'],],'1' =>  ['title' => 'Breakdance Instagram Post Embed','scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/breakdance-instagram-embed@1/breakdance-instagram-embed.js'],],'2' =>  ['title' => 'Instagram Post Embed Frontend','inlineScripts' => [' new BreakdanceInstagramPost(\'%%SELECTOR%%\', { url: {{ content.post.post_url|json_encode }}, hide_caption: {{ design.post.hide_caption|json_encode }} } );'],'builderCondition' => 'return false;','frontendCondition' => 'return true;',],];
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

'onPropertyChange' => [['script' => '(function() {
  if (window.breakdanceTabsInstances && window.breakdanceInstagramPostInstances[%%ID%%]) {
    window.breakdanceInstagramPostInstances[%%ID%%].destroy();
  }

  window.breakdanceInstagramPostInstances[%%ID%%] = new BreakdanceInstagramPost(\'%%SELECTOR%%\', { url: {{ content.post.post_url|json_encode }}, hide_caption: {{ design.post.hide_caption|json_encode }} });
}());',
],],

'onMountedElement' => [['script' => '(function() {
    if (!window.breakdanceInstagramPostInstances) window.breakdanceInstagramPostInstances = {};

    if (window.breakdanceInstagramPostInstances && window.breakdanceInstagramPostInstances[%%ID%%]) {
      window.breakdanceInstagramPostInstances[%%ID%%].destroy();
    }

    window.breakdanceInstagramPostInstances[%%ID%%] = new BreakdanceInstagramPost(\'%%SELECTOR%%\', { url: {{ content.post.post_url|json_encode }}, hide_caption: {{ design.post.hide_caption|json_encode }} } );
  }());',
],],

'onMovedElement' => [['script' => '(function() {
  if (window.breakdanceInstagramPostInstances && window.breakdanceInstagramPostInstances[%%ID%%]) {
    window.breakdanceInstagramPostInstances[%%ID%%].update();
  }
}());',
],],

'onBeforeDeletingElement' => [['script' => '  (function() {
    if (window.breakdanceInstagramPostInstances && window.breakdanceInstagramPostInstances[%%ID%%]) {
      window.breakdanceInstagramPostInstances[%%ID%%].destroy();
      delete window.breakdanceInstagramPostInstances[%%ID%%];
    }
  }());',
],],];
    }

    static function nestingRule()
    {
        return ["type" => "final",   ];
    }

    static function spacingBars()
    {
        return ['0' => ['cssProperty' => 'margin-top', 'location' => 'outside-top', 'affectedPropertyPath' => 'design.spacing.margin_top.%%BREAKPOINT%%'], '1' => ['cssProperty' => 'margin-bottom', 'location' => 'outside-bottom', 'affectedPropertyPath' => 'design.spacing.margin_bottom.%%BREAKPOINT%%']];
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
        return 15600;
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
        return ['design.post.width'];
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}

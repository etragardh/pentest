<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\BackToTop",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class BackToTop extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'ArrowUpIcon';
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
        return 'Back To Top';
    }

    static function className()
    {
        return 'bde-back-to-top';
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
        return ['content' => ['button' => ['type' => 'progress']], 'design' => ['container' => ['sticky' => true, 'hide_at_the_top' => false]]];
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
        "sticky",
        "Sticky",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "vertical_align",
        "Vertical Align",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'top', 'text' => 'Top', 'icon' => 'FlexAlignTopIcon'], '1' => ['text' => 'Center', 'value' => 'center', 'icon' => 'FlexAlignCenterVerticalIcon'], '2' => ['text' => 'Bottom', 'value' => 'bottom', 'icon' => 'FlexAlignBottomIcon']], 'condition' => ['path' => 'design.container.sticky', 'operand' => 'is set', 'value' => '']],
        false,
        false,
        [],
      ), c(
        "horizontal_align",
        "Horizontal Align",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'left', 'text' => 'Left', 'icon' => 'FlexAlignLeftIcon'], '1' => ['text' => 'Center', 'value' => 'center', 'icon' => 'FlexAlignCenterHorizontalIcon'], '2' => ['text' => 'Right', 'value' => 'right', 'icon' => 'FlexAlignRightIcon']], 'buttonBarOptions' => ['size' => 'small'], 'condition' => ['path' => 'design.container.sticky', 'operand' => 'is set', 'value' => '']],
        true,
        false,
        [],
      ), c(
        "hide_at_the_top",
        "Hide At The Top",
        [],
        ['type' => 'toggle', 'layout' => 'inline', 'condition' => ['path' => 'design.container.sticky', 'operand' => 'is set', 'value' => '']],
        false,
        false,
        [],
      ), c(
        "show",
        "Show",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'always', 'text' => 'Always'], '1' => ['text' => 'Half Way', 'value' => 'half'], '2' => ['text' => 'Near Bottom', 'value' => 'near']], 'condition' => ['path' => 'design.container.sticky', 'operand' => 'is set', 'value' => '']],
        false,
        false,
        [],
      ), c(
        "animation",
        "Animation",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'fade', 'text' => 'Fade In'], '1' => ['text' => 'Zoom', 'value' => 'zoom'], '2' => ['text' => 'Slide up', 'value' => 'slide-up'], '3' => ['text' => 'Slide Right', 'value' => 'slide-right'], '4' => ['text' => 'Slide Left', 'value' => 'slide-left']], 'condition' => ['path' => 'design.container.show', 'operand' => 'is one of', 'value' => ['0' => 'half', '1' => 'near']]],
        false,
        false,
        [],
      ), c(
        "duration",
        "Duration",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'ms'], 'defaultType' => 'ms'], 'condition' => ['path' => 'design.container.show', 'operand' => 'is one of', 'value' => ['0' => 'near', '1' => 'half']]],
        false,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "button",
        "Button",
        [c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        true,
        [],
      ), c(
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 20, 'max' => 100, 'step' => 1], 'condition' => ['path' => 'content.button.type', 'operand' => 'not equals', 'value' => 'text']],
        false,
        false,
        [],
      ), c(
        "button_styles",
        "Button Styles",
        [getPresetSection(
      "EssentialElements\\typography",
      "Typography",
      "typography",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\spacing_padding_all",
      "Padding",
      "padding",
       ['type' => 'popout']
     )],
        ['type' => 'section', 'layout' => 'inline', 'condition' => ['path' => 'content.button.type', 'operand' => 'equals', 'value' => 'text'], 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "icon",
        "Icon",
        [c(
        "color",
        "Color",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        true,
        [],
      ), c(
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        true,
        [],
      ), c(
        "space_after",
        "Space After",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => 'content.button.type', 'operand' => 'equals', 'value' => 'text'], 'rangeOptions' => ['min' => 1, 'max' => 32, 'step' => 0]],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "progress",
        "Progress",
        [c(
        "tracker",
        "Tracker",
        [],
        ['type' => 'color', 'layout' => 'inline'],
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
      ), c(
        "width",
        "Width",
        [],
        ['type' => 'number', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 30, 'step' => 1]],
        false,
        false,
        [],
      ), c(
        "padding",
        "Padding",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 40, 'step' => 1], 'condition' => ['path' => 'content.button.type', 'operand' => 'not equals', 'value' => 'text']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout'], 'condition' => ['path' => 'content.button.type', 'operand' => 'is none of', 'value' => ['0' => 'text', '1' => 'icon']]],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\borders",
      "Borders",
      "borders",
       ['type' => 'popout']
     )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\spacing_margin_all",
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
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['text' => 'Scroll Progress', 'value' => 'progress'], '1' => ['value' => 'icon', 'text' => 'Icon'], '2' => ['text' => 'Icon + Text', 'value' => 'text']]],
        false,
        false,
        [],
      ), c(
        "icon",
        "Icon",
        [],
        ['type' => 'icon', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "text",
        "Text",
        [],
        ['type' => 'text', 'layout' => 'vertical', 'condition' => ['path' => 'content.button.type', 'operand' => 'equals', 'value' => 'text']],
        false,
        false,
        [],
      ), c(
        "advanced",
        "Advanced",
        [c(
        "scroll_offset",
        "Scroll Offset",
        [],
        ['type' => 'number', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "scroll_to_selector",
        "Scroll To Selector",
        [],
        ['type' => 'text', 'layout' => 'inline', 'placeholder' => '#element_id, .awesome_class'],
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
        return ['0' =>  ['title' => 'Back To Top','scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/breakdance-back-to-top@1/breakdance-back-to-top.js'],],'1' =>  ['title' => 'Init Frontend','inlineScripts' => ['new BreakdanceBackToTop(\'%%SELECTOR%%\', { type: {{ content.button.type|json_encode }}, show: {{ design.container.show|json_encode }}, advanced: {{ content.button.advanced|json_encode }} } );'],'builderCondition' => 'return false;','frontendCondition' => 'return true;',],];
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
  if (window.breakdanceBackToTopInstances && window.breakdanceBackToTopInstances[%%ID%%]) {
    window.breakdanceBackToTopInstances[%%ID%%].destroy();
  }

  window.breakdanceBackToTopInstances[%%ID%%] = new BreakdanceBackToTop(\'%%SELECTOR%%\', { type: {{ content.button.type|json_encode }}, show: {{ design.container.show|json_encode }}, advanced: {{ content.button.advanced|json_encode }} } );
}());',
],],

'onMountedElement' => [['script' => '(function() {
    if (!window.breakdanceBackToTopInstances) window.breakdanceBackToTopInstances = {};

    if (window.breakdanceBackToTopInstances && window.breakdanceBackToTopInstances[%%ID%%]) {
      window.breakdanceBackToTopInstances[%%ID%%].destroy();
    }

    window.breakdanceBackToTopInstances[%%ID%%] = new BreakdanceBackToTop(\'%%SELECTOR%%\', { type: {{ content.button.type|json_encode }}, show: {{ design.container.show|json_encode }}, advanced: {{ content.button.advanced|json_encode }} } );
  }());',
],],

'onMovedElement' => [['script' => '(function() {
  if (window.breakdanceBackToTopInstances && window.breakdanceBackToTopInstances[%%ID%%]) {
    window.breakdanceBackToTopInstances[%%ID%%].update();
  }
}());',
],],

'onBeforeDeletingElement' => [['script' => '  (function() {
    if (window.breakdanceBackToTopInstances && window.breakdanceBackToTopInstances[%%ID%%]) {
      window.breakdanceBackToTopInstances[%%ID%%].destroy();
      delete window.breakdanceBackToTopInstances[%%ID%%];
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
        return ['0' => ['cssProperty' => 'margin-top', 'location' => 'outside-top', 'affectedPropertyPath' => 'design.spacing.margin.%%BREAKPOINT%%.top'], '1' => ['cssProperty' => 'margin-bottom', 'location' => 'outside-bottom', 'affectedPropertyPath' => 'design.spacing.margin.%%BREAKPOINT%%.bottom'], '2' => ['affectedPropertyPath' => 'design.spacing.margin.%%BREAKPOINT%%.left', 'cssProperty' => 'margin-left', 'location' => 'outside-left'], '3' => ['cssProperty' => 'margin-right', 'location' => 'outside-right', 'affectedPropertyPath' => 'design.spacing.margin.%%BREAKPOINT%%.right']];
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
        return 4001;
    }

    static function dynamicPropertyPaths()
    {
        return false;
    }

    static function additionalClasses()
    {
        return [['name' => 'bde-back-to-top--icon', 'template' => '{% if content.button.type == \'icon\'  %}
true
{% endif %}'], ['name' => 'bde-back-to-top--text', 'template' => '{% if content.button.type == \'text\' %}
true
{% endif %}'], ['name' => 'bde-back-to-top--progress', 'template' => '{% if content.button.type == \'progress\' or content.button.type is empty %}
true
{% endif %}'], ['name' => 'is-sticky', 'template' => '{% if design.container.sticky == true %}
true
{% endif %}']];
    }

    static function projectManagement()
    {
        return false;
    }

    static function propertyPathsToWhitelistInFlatProps()
    {
        return ['design.button.hide_at'];
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}

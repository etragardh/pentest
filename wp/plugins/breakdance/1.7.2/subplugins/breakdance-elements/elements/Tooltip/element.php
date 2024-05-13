<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\Tooltip",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class Tooltip extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'CircleInfoIcon';
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
        return 'Tooltip';
    }

    static function className()
    {
        return 'bde-tooltip';
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
      "EssentialElements\\tooltip",
      "Tooltip",
      "tooltip",
       ['type' => 'popout']
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
        "tooltip",
        "Tooltip",
        [c(
        "text",
        "Text",
        [],
        ['type' => 'richtext', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "in_builder_preview",
        "In-Builder Preview",
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
        return ['0' =>  ['title' => 'Popper','scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/popperjs-core@2/popper.min.js'],],'1' =>  ['title' => 'Tippy','scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/tippy@6/tippy.umd.min.js'],],'2' =>  ['title' => 'Tooltip','scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/breakdance-tooltips/breakdance-tooltips.js'],],'3' =>  ['title' => 'Init on Frontend','inlineScripts' => ['new BreakdanceTooltip(\'%%SELECTOR%%\', \'%%ID%%\',{ offset: {{ design.tooltip.offset|json_encode }}, placement: {{ design.tooltip.placement|json_encode }}, hideArrow: {{ design.tooltip.arrow.disable|json_encode }} });'],'builderCondition' => 'return false;','frontendCondition' => 'return true;',],];
    }

    static function settings()
    {
        return false;
    }

    static function addPanelRules()
    {
        return false;
    }

    static public function actions()
    {
        return [

'onPropertyChange' => [['script' => '(function() {
  if (window.breakdanceTooltipInstances && window.breakdanceTooltipInstances[%%ID%%]) {
    window.breakdanceTooltipInstances[%%ID%%].destroy();
  }

  window.breakdanceTooltipInstances[%%ID%%] = new BreakdanceTooltip(\'%%SELECTOR%%\', \'%%ID%%\',{ offset: {{ design.tooltip.offset|json_encode }}, placement: {{ design.tooltip.placement|json_encode }}, hideArrow: {{ design.tooltip.arrow.disable|json_encode }}, preview: {{ content.tooltip.in_builder_preview|json_encode }} } );
}());',
],],

'onMountedElement' => [['script' => '(function() {
    if (!window.breakdanceTooltipInstances) window.breakdanceTooltipInstances = {};

    if (window.breakdanceTooltipInstances && window.breakdanceTooltipInstances[%%ID%%]) {
      window.breakdanceTooltipInstances[%%ID%%].destroy();
    }

    window.breakdanceTooltipInstances[%%ID%%] = new BreakdanceTooltip(\'%%SELECTOR%%\', \'%%ID%%\',{ offset: {{ design.tooltip.offset|json_encode }}, placement: {{ design.tooltip.placement|json_encode }}, hideArrow: {{ design.tooltip.arrow.disable|json_encode }}, preview: {{ content.tooltip.in_builder_preview|json_encode }} } );
  }());',
],],

'onMovedElement' => [['script' => '(function() {
  if (window.breakdanceTooltipInstances && window.breakdanceTooltipInstances[%%ID%%]) {
    window.breakdanceTooltipInstances[%%ID%%].update();
  }
}());',
],],

'onBeforeDeletingElement' => [['script' => '  (function() {
    if (window.breakdanceTooltipInstances && window.breakdanceTooltipInstances[%%ID%%]) {
      window.breakdanceTooltipInstances[%%ID%%].destroy();
      delete window.breakdanceTooltipInstances[%%ID%%];
    }
  }());',
],],

'onActivatedElement' => [['script' => 'const element = document.querySelector(\'%%SELECTOR%%\');
const parentTooltip = element.closest(\'.bde-tooltip\') ?? element;
const tooltipId = parentTooltip.dataset.nodeId;

if (window.breakdanceTooltipInstances && window.breakdanceTooltipInstances[tooltipId]) {
  window.breakdanceTooltipInstances[tooltipId].update();
}',
],],];
    }

    static function nestingRule()
    {
        return ["type" => "container",   ];
    }

    static function spacingBars()
    {
        return ['0' => ['cssProperty' => 'margin-top', 'location' => 'outside-top', 'affectedPropertyPath' => 'design.spacing.margin_top.%%BREAKPOINT%%'], '1' => ['affectedPropertyPath' => 'design.spacing.margin_bottom.%%BREAKPOINT%%', 'cssProperty' => 'margin-bottom', 'location' => 'outside-bottom']];
    }

    static function attributes()
    {
        return [['name' => 'data-tippy-content', 'template' => '{{ content.tooltip.text|default("Tooltip") }}']];
    }

    static function experimental()
    {
        return false;
    }

    static function order()
    {
        return 775;
    }

    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'string', 'path' => 'content.tooltip.text']];
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

<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\ImageComparison",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class ImageComparison extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return '<svg aria-hidden="true" focusable="false"   class="svg-inline--fa fa-images" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M464 448H96c-35.35 0-64-28.65-64-64V112C32 103.2 24.84 96 16 96S0 103.2 0 112V384c0 53.02 42.98 96 96 96h368c8.836 0 16-7.164 16-16S472.8 448 464 448zM224 152c13.26 0 24-10.75 24-24s-10.74-24-24-24c-13.25 0-24 10.75-24 24S210.8 152 224 152zM410.6 139.9c-11.28-15.81-38.5-15.94-49.1-.0313l-44.03 61.43l-6.969-8.941c-11.44-14.46-36.97-14.56-48.4 .0313L198.2 272.8C191 281.9 190 294.3 195.5 304.3C200.8 313.1 211.1 320 222.4 320h259.2c11 0 21.17-5.805 26.54-15.09c0-.0313-.0313 .0313 0 0c5.656-9.883 5.078-21.84-1.578-31.15L410.6 139.9zM226.2 287.9l58.25-75.61l20.09 25.66c4.348 5.545 17.6 10.65 25.59-.5332l54.44-78.75l92.68 129.2H226.2zM512 32H160c-35.35 0-64 28.65-64 64v224c0 35.35 28.65 64 64 64H512c35.35 0 64-28.65 64-64V96C576 60.65 547.3 32 512 32zM544 320c0 17.64-14.36 32-32 32H160c-17.64 0-32-14.36-32-32V96c0-17.64 14.36-32 32-32h352c17.64 0 32 14.36 32 32V320z"></path></svg>';
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
        return 'Image Comparison';
    }

    static function className()
    {
        return 'bde-image-comparison';
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
        return [c(
        "settings",
        "Settings",
        [c(
        "initial_position",
        "Initial Position",
        [],
        ['type' => 'number', 'layout' => 'inline', 'rangeOptions' => ['step' => 0.1, 'max' => 1, 'min' => 0]],
        false,
        false,
        [],
      ), c(
        "start_on_hover",
        "Start On Hover",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "auto_animation",
        "Auto Animation",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "animation_speed",
        "Animation Speed",
        [],
        ['type' => 'number', 'layout' => 'inline', 'rangeOptions' => ['step' => 1, 'min' => 1, 'max' => 10], 'condition' => ['path' => 'design.settings.auto_animation', 'operand' => 'is set', 'value' => '']],
        false,
        false,
        [],
      ), c(
        "vertical_mode",
        "Vertical Mode",
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
      ), c(
        "separator",
        "Separator",
        [c(
        "hide",
        "Hide",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "color",
        "Color",
        [],
        ['type' => 'color', 'layout' => 'inline', 'colorOptions' => ['type' => 'solidOnly'], 'condition' => ['path' => 'design.separator.hide', 'operand' => 'is not set', 'value' => '']],
        false,
        false,
        [],
      ), c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'rangeOptions' => ['step' => 1, 'min' => 1, 'max' => 5], 'condition' => ['path' => 'design.separator.hide', 'operand' => 'is not set', 'value' => '']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "labels",
        "Labels",
        [c(
        "hide",
        "Hide",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "position",
        "Position",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['text' => 'Start', 'value' => 'start'], '1' => ['text' => 'Center', 'value' => 'center'], '2' => ['value' => 'end', 'text' => 'End']], 'condition' => ['path' => 'design.labels.hide', 'operand' => 'is not set', 'value' => '']],
        false,
        false,
        [],
      ), c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline', 'condition' => ['path' => 'design.labels.hide', 'operand' => 'is not set', 'value' => '']],
        false,
        false,
        [],
      ), getPresetSection(
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
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
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
        "content",
        "Content",
        [c(
        "before",
        "Before",
        [],
        ['type' => 'wpmedia', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "after",
        "After",
        [],
        ['type' => 'wpmedia', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "labels",
        "Labels",
        [c(
        "before",
        "Before",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "after",
        "After",
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
        return ['0' =>  ['scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/anyimagecomparisonslider@1/jquery-anyimagecomparisonslider-plugin.js','%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%elements/Image_Comparison/assets/image-comparison.js'],'title' => 'jQuery image comparison',],'1' =>  ['inlineScripts' => ['new BreakdanceImageComparison(\'%%SELECTOR%%\', { content: {{ content|json_encode }}, design: {{ design|json_encode }} } );
'],'builderCondition' => 'return false;','frontendCondition' => 'return true;','title' => 'Init in the frontend',],];
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

'onPropertyChange' => [['script' => '(function() {
  if (window.breakdanceImageComparisonInstances && window.breakdanceImageComparisonInstances[%%ID%%]) {
    window.breakdanceImageComparisonInstances[%%ID%%].destroy();
  }

  window.breakdanceImageComparisonInstances[%%ID%%] = new BreakdanceImageComparison(\'%%SELECTOR%%\', { content: {{ content|json_encode }}, design: {{ design|json_encode }} } );
}());',
],],

'onMountedElement' => [['script' => '(function() {
    if (!window.breakdanceImageComparisonInstances) window.breakdanceImageComparisonInstances = {};

    if (window.breakdanceImageComparisonInstances && window.breakdanceImageComparisonInstances[%%ID%%]) {
      window.breakdanceImageComparisonInstances[%%ID%%].destroy();
    }

    window.breakdanceImageComparisonInstances[%%ID%%] = new BreakdanceImageComparison(\'%%SELECTOR%%\', { content: {{ content|json_encode }}, design: {{ design|json_encode }} } );
  }());
',
],],

'onMovedElement' => [['script' => '(function() {
  if (window.breakdanceImageComparisonInstances && window.breakdanceImageComparisonInstances[%%ID%%]) {
    window.breakdanceImageComparisonInstances[%%ID%%].update();
  }
}());',
],],

'onBeforeDeletingElement' => [['script' => ' (function() {
    if (window.breakdanceImageComparisonInstances && window.breakdanceImageComparisonInstances[%%ID%%]) {
      window.breakdanceImageComparisonInstances[%%ID%%].destroy();
      delete window.breakdanceImageComparisonInstances[%%ID%%];
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
        return ['0' => ['location' => 'outside-top', 'cssProperty' => 'margin-top', 'affectedPropertyPath' => 'design.spacing.margin_top.%%BREAKPOINT%%'], '1' => ['location' => 'outside-bottom', 'cssProperty' => 'margin-bottom', 'affectedPropertyPath' => 'design.spacing.margin_bottom.%%BREAKPOINT%%']];
    }

    static function attributes()
    {
        return [['name' => 'data-before-label', 'template' => '{{ content.content.labels.before|default(\'Before\') }}'], ['name' => 'data-after-label', 'template' => '{{ content.content.labels.after|default(\'After\') }}']];
    }

    static function experimental()
    {
        return false;
    }

    static function order()
    {
        return 3500;
    }

    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'image_url', 'path' => 'content.content.before'], '1' => ['accepts' => 'image_url', 'path' => 'content.content.after'], '2' => ['accepts' => 'string', 'path' => 'content.content.labels.before'], '3' => ['accepts' => 'string', 'path' => 'content.content.labels.after']];
    }

    static function additionalClasses()
    {
        return false;
    }

    static function projectManagement()
    {
        return ['looksGood' => 'sort of', 'optionsGood' => 'unknown', 'optionsWork' => 'no', 'dynamicBehaviorWorks' => 'no'];
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

<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\LottieAnimation",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class LottieAnimation extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'TimelineIcon';
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
        return 'Lottie Animation';
    }

    static function className()
    {
        return 'bde-lottie-animation';
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
        return ['content' => ['content' => ['autoplay' => 'enabled', 'play_on_hover' => false, 'loop_animation' => false, 'show_controls' => false, 'animation_speed' => 1, 'asset_link' => 'https://assets6.lottiefiles.com/packages/lf20_iiozykmz.json', 'asset_url' => null, 'bounce_mode' => false, 'qwe' => ['number' => 0.5, 'unit' => 'px', 'style' => '0.5px'], 'trigger' => 'viewport', 'hover_area' => 'parent', 'on_hover_out' => 'default', 'times_to_loop' => null, 'reverse_on_finish' => true, 'end_point' => 100, 'start_point' => 0]], 'design' => ['wrapper' => ['background_color' => '#7E53E0FF'], 'spacing' => null]];
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
        "wrapper",
        "Wrapper",
        [c(
        "background_color",
        "Background Color",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => '%', '2' => 'em', '3' => 'rem', '4' => 'calc'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 240, 'max' => 1080, 'step' => 1]],
        true,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\borders",
      "Borders",
      "borders",
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
        "content",
        "Content",
        [c(
        "asset_url",
        "Asset Url",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "animation_speed",
        "Animation Speed",
        [],
        ['type' => 'number', 'layout' => 'inline', 'items' => ['0' => ['text' => '0.5x', 'label' => 'Label', 'value' => '0.5'], '1' => ['text' => '1x', 'value' => '1'], '2' => ['text' => '2x', 'value' => '2']], 'rangeOptions' => ['min' => 0, 'max' => 3, 'step' => 0.1]],
        false,
        false,
        [],
      ), c(
        "trigger",
        "Trigger",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'Viewport', 'value' => 'viewport'], '1' => ['value' => 'click', 'text' => 'Click'], '2' => ['text' => 'Hover', 'value' => 'hover'], '3' => ['text' => 'None', 'value' => 'none']]],
        false,
        false,
        [],
      ), c(
        "hover_area",
        "Hover Area",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'animation', 'text' => 'Animation'], '1' => ['text' => 'Section', 'value' => 'section'], '2' => ['text' => 'Parent element', 'value' => 'parent']], 'condition' => ['path' => 'content.content.trigger', 'operand' => 'equals', 'value' => 'hover']],
        false,
        false,
        [],
      ), c(
        "on_hover_out",
        "On Hover Out",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'Do nothing', 'value' => 'default'], '1' => ['value' => 'pause', 'text' => 'Pause'], '2' => ['text' => 'Reverse', 'value' => 'reverse']], 'condition' => ['path' => 'content.content.trigger', 'operand' => 'equals', 'value' => 'hover']],
        false,
        false,
        [],
      ), c(
        "reverse_on_finish",
        "Reverse on finish",
        [],
        ['type' => 'toggle', 'layout' => 'inline', 'condition' => ['path' => 'content.content.trigger', 'operand' => 'is one of', 'value' => ['0' => 'click', '1' => 'viewport', '2' => 'none']]],
        false,
        false,
        [],
      ), c(
        "loop_animation",
        "Loop Animation",
        [],
        ['type' => 'toggle', 'layout' => 'inline', 'items' => ['0' => ['text' => 'Yes', 'label' => 'Label', 'value' => 'Yes'], '1' => ['text' => 'No', 'value' => 'No']], 'condition' => ['path' => 'content.content.trigger', 'operand' => 'is one of', 'value' => ['0' => 'click', '1' => 'hover', '2' => 'none', '3' => 'viewport']]],
        false,
        false,
        [],
      ), c(
        "times_to_loop",
        "Times to loop",
        [],
        ['type' => 'number', 'layout' => 'inline', 'condition' => ['path' => 'content.content.loop_animation', 'operand' => 'is set', 'value' => ''], 'rangeOptions' => ['min' => 0, 'max' => 10, 'step' => 1]],
        false,
        false,
        [],
      ), c(
        "frames",
        "Frames",
        [],
        ['type' => 'slider', 'layout' => 'vertical', 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
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
        return ['0' =>  ['scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/lottie-web@5/lottie_light-v-5-7-8.min.js','%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/lottie-web@5/breakdanceLottie.js'],'inlineScripts' => ['window.BreakdanceLottie("%%SELECTOR%%", {{ content.content|json_encode }})'],],];
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

'onPropertyChange' => [['script' => 'window.BreakdanceLottie("%%SELECTOR%%", {{ content.content|json_encode }})',
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
        return 1000;
    }

    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'url', 'path' => 'content.content.asset_url']];
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

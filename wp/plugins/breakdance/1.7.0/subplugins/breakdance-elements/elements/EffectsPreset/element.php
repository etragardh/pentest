<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\Effectspreset",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class Effectspreset extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'SquareIcon';
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
        return 'EffectsPreset';
    }

    static function className()
    {
        return 'bde-effectspreset';
    }

    static function category()
    {
        return 'other';
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
        "effects",
        "Effects",
        [c(
        "opacity",
        "Opacity",
        [],
        ['type' => 'number', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 1, 'step' => 0.1]],
        true,
        true,
        [],
      ), c(
        "box_shadow",
        "Box Shadow",
        [],
        ['type' => 'shadow', 'layout' => 'vertical'],
        true,
        true,
        [],
      ), c(
        "mix_blend_mode",
        "Mix Blend Mode",
        [],
        ['type' => 'dropdown', 'layout' => 'vertical', 'items' => ['0' => ['value' => 'normal', 'text' => 'normal'], '1' => ['value' => 'multiply', 'text' => 'multiply'], '2' => ['value' => 'screen', 'text' => 'screen'], '3' => ['value' => 'overlay', 'text' => 'overlay'], '4' => ['value' => 'darken', 'text' => 'darken'], '5' => ['value' => 'lighten', 'text' => 'lighten'], '6' => ['text' => 'color-dodge', 'value' => 'color-dodge'], '7' => ['text' => 'color-burn', 'value' => 'color-burn'], '8' => ['text' => 'hard-light', 'value' => 'hard-light'], '9' => ['text' => 'soft-light', 'value' => 'soft-light'], '10' => ['text' => 'difference', 'value' => 'difference'], '11' => ['text' => 'exclusion', 'value' => 'exclusion'], '12' => ['text' => 'hue', 'value' => 'hue'], '13' => ['text' => 'saturation', 'value' => 'saturation'], '14' => ['text' => 'color', 'value' => 'color'], '15' => ['text' => 'luminosity', 'value' => 'luminosity']]],
        true,
        true,
        [],
      ), c(
        "transition",
        "Transition",
        [c(
        "duration",
        "Duration",
        [],
        ['type' => 'unit', 'layout' => 'vertical', 'unitOptions' => ['types' => ['0' => 'ms'], 'defaultType' => 'ms'], 'rangeOptions' => ['min' => 0, 'max' => 5000, 'step' => 100]],
        false,
        true,
        [],
      ), c(
        "timing_function",
        "Timing Function",
        [],
        ['type' => 'dropdown', 'layout' => 'vertical', 'items' => ['0' => ['text' => 'ease-in-out', 'value' => 'ease-in-out'], '1' => ['text' => 'ease-in', 'value' => 'ease-in'], '2' => ['text' => 'ease-out', 'value' => 'ease-out'], '3' => ['value' => 'ease', 'text' => 'ease'], '4' => ['text' => 'linear', 'value' => 'linear']]],
        false,
        false,
        [],
      ), c(
        "property",
        "Property",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'placeholder' => '', 'items' => ['0' => ['value' => 'all', 'text' => 'all'], '1' => ['text' => 'custom', 'value' => 'custom']]],
        false,
        false,
        [],
      ), c(
        "custom_property",
        "Custom Property",
        [],
        ['type' => 'text', 'layout' => 'vertical', 'condition' => ['path' => '%%CURRENTPATH%%.property', 'operand' => 'equals', 'value' => 'custom']],
        false,
        false,
        [],
      ), c(
        "delay",
        "Delay",
        [],
        ['type' => 'unit', 'layout' => 'vertical', 'unitOptions' => ['types' => ['0' => 'ms'], 'defaultType' => 'ms'], 'rangeOptions' => ['min' => 0, 'max' => 5000, 'step' => 100]],
        false,
        true,
        [],
      )],
        ['type' => 'repeater', 'layout' => 'vertical'],
        true,
        true,
        [],
      ), c(
        "transform",
        "Transform",
        [c(
        "transforms",
        "Transforms",
        [c(
        "type",
        "Type",
        [],
        ['type' => 'dropdown', 'layout' => 'vertical', 'items' => ['0' => ['text' => 'perspective', 'value' => 'perspective'], '1' => ['text' => 'rotate', 'value' => 'rotate'], '2' => ['text' => 'rotate3d', 'value' => 'rotate3d'], '3' => ['text' => 'scale', 'value' => 'scale'], '4' => ['text' => 'scale3d', 'value' => 'scale3d'], '5' => ['value' => 'skew', 'text' => 'skew'], '6' => ['text' => 'translate', 'value' => 'translate']]],
        false,
        false,
        [],
      ), c(
        "skew_x",
        "Skew X",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'skew'], 'unitOptions' => ['types' => ['0' => 'deg', '1' => 'custom'], 'defaultType' => 'deg'], 'rangeOptions' => ['min' => 0, 'max' => 180, 'step' => 1]],
        false,
        false,
        [],
      ), c(
        "skew_y",
        "Skew Y",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'skew'], 'unitOptions' => ['types' => ['0' => 'deg', '1' => 'custom']], 'rangeOptions' => ['min' => 0, 'max' => 180, 'step' => 1]],
        false,
        false,
        [],
      ), c(
        "translate_x",
        "Translate X",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'translate'], 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        false,
        false,
        [],
      ), c(
        "translate_y",
        "Translate Y",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'translate'], 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        false,
        false,
        [],
      ), c(
        "translate_z",
        "Translate Z",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'translate'], 'rangeOptions' => ['min' => 1, 'max' => 100, 'step' => 0]],
        false,
        false,
        [],
      ), c(
        "angle",
        "Angle",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'deg', '1' => 'custom'], 'defaultType' => 'deg'], 'rangeOptions' => ['min' => 0, 'max' => 360, 'step' => 1], 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'rotate3d']],
        false,
        false,
        [],
      ), c(
        "perspective",
        "Perspective",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'perspective'], 'rangeOptions' => ['min' => 1, 'max' => 1000, 'step' => 10]],
        false,
        false,
        [],
      ), c(
        "x",
        "X",
        [],
        ['type' => 'number', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'rotate3d'], 'rangeOptions' => ['min' => 0, 'max' => 1, 'step' => 0.1]],
        false,
        false,
        [],
      ), c(
        "y",
        "Y",
        [],
        ['type' => 'number', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'rotate3d'], 'rangeOptions' => ['min' => 0, 'max' => 1, 'step' => 0.1]],
        false,
        false,
        [],
      ), c(
        "z",
        "Z",
        [],
        ['type' => 'number', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'rotate3d'], 'rangeOptions' => ['min' => 0, 'max' => 1, 'step' => 0.1]],
        false,
        false,
        [],
      ), c(
        "scale_x",
        "Scale X",
        [],
        ['type' => 'number', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'scale3d'], 'rangeOptions' => ['min' => 0, 'max' => 4, 'step' => 0.1]],
        false,
        false,
        [],
      ), c(
        "scale_y",
        "Scale Y",
        [],
        ['type' => 'number', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'scale3d'], 'rangeOptions' => ['min' => 0, 'max' => 4, 'step' => 0.1]],
        false,
        false,
        [],
      ), c(
        "scale_z",
        "Scale Z",
        [],
        ['type' => 'number', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'scale3d'], 'rangeOptions' => ['min' => 0, 'max' => 4, 'step' => 0.1]],
        false,
        false,
        [],
      ), c(
        "scale",
        "Scale",
        [],
        ['type' => 'number', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'scale'], 'rangeOptions' => ['min' => 0, 'max' => 4, 'step' => 0.1]],
        false,
        false,
        [],
      ), c(
        "rotate_x",
        "Rotate X",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'deg', '1' => 'custom'], 'defaultType' => 'deg'], 'rangeOptions' => ['min' => 0, 'max' => 360, 'step' => 1], 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'rotate']],
        false,
        false,
        [],
      ), c(
        "rotate_y",
        "Rotate Y",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'deg', '1' => 'custom'], 'defaultType' => 'deg'], 'rangeOptions' => ['min' => 0, 'max' => 360, 'step' => 1], 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'rotate']],
        false,
        false,
        [],
      ), c(
        "rotate_z",
        "Rotate Z",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'deg', '1' => 'custom'], 'defaultType' => 'deg'], 'rangeOptions' => ['min' => 0, 'max' => 360, 'step' => 1], 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'rotate']],
        false,
        false,
        [],
      )],
        ['type' => 'repeater', 'layout' => 'vertical', 'repeaterOptions' => ['titleTemplate' => '{type}', 'defaultTitle' => '', 'buttonName' => '']],
        true,
        true,
        [],
      ), c(
        "origin",
        "Origin",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'top left', 'text' => 'top left'], '1' => ['text' => 'top right', 'value' => 'top right'], '2' => ['text' => 'top center', 'value' => 'top center'], '3' => ['text' => 'center left', 'value' => 'center left'], '4' => ['text' => 'center', 'value' => 'center'], '5' => ['text' => 'center right', 'value' => 'center right'], '6' => ['text' => 'bottom left', 'value' => 'bottom left'], '7' => ['text' => 'bottom center', 'value' => 'bottom center'], '8' => ['text' => 'bottom right', 'value' => 'bottom right'], '9' => ['text' => 'Custom', 'value' => 'custom']]],
        true,
        true,
        [],
      ), c(
        "origin_position",
        "Origin Position",
        [],
        ['type' => 'focus_point', 'layout' => 'vertical', 'condition' => ['0' => ['0' => ['path' => '%%CURRENTPATH%%.origin', 'operand' => 'equals', 'value' => 'custom']]], 'focusPointOptions' => ['gridMode' => true]],
        true,
        true,
        [],
      ), c(
        "perspective",
        "Perspective",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 1000, 'step' => 10]],
        true,
        true,
        [],
      ), c(
        "perspective_origin",
        "Perspective Origin",
        [],
        ['type' => 'focus_point', 'layout' => 'vertical', 'focusPointOptions' => ['gridMode' => true]],
        false,
        false,
        [],
      ), c(
        "transform_style",
        "Transform Style",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'flat', 'text' => 'Flat'], '1' => ['value' => 'preserve-3d', 'text' => 'Preserve 3D']]],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "filter",
        "Filter",
        [c(
        "type",
        "Type",
        [],
        ['type' => 'dropdown', 'layout' => 'vertical', 'items' => ['0' => ['value' => 'blur', 'text' => 'blur'], '1' => ['text' => 'brightness', 'value' => 'brightness'], '2' => ['text' => 'contrast', 'value' => 'contrast'], '3' => ['text' => 'grayscale', 'value' => 'grayscale'], '4' => ['text' => 'hue-rotate', 'value' => 'hue-rotate'], '5' => ['text' => 'invert', 'value' => 'invert'], '6' => ['text' => 'saturate', 'value' => 'saturate'], '7' => ['text' => 'sepia', 'value' => 'sepia']]],
        false,
        false,
        [],
      ), c(
        "blur_amount",
        "Blur Amount",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 1, 'max' => 100, 'step' => 1], 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'blur']],
        false,
        false,
        [],
      ), c(
        "amount",
        "Amount",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'is one of', 'value' => ['0' => 'brightness', '1' => 'contrast', '2' => 'grayscale', '3' => 'invert', '4' => 'saturate', '5' => 'sepia']], 'unitOptions' => ['types' => ['0' => '%'], 'defaultType' => '%'], 'rangeOptions' => ['min' => 0, 'max' => 200, 'step' => 1]],
        false,
        false,
        [],
      ), c(
        "rotate",
        "Rotate",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'hue-rotate'], 'rangeOptions' => ['min' => 1, 'max' => 360, 'step' => 1], 'unitOptions' => ['types' => ['0' => 'deg', '1' => 'custom'], 'defaultType' => 'deg']],
        false,
        false,
        [],
      )],
        ['type' => 'repeater', 'layout' => 'vertical', 'repeaterOptions' => ['titleTemplate' => '{type}', 'defaultTitle' => '', 'buttonName' => '']],
        true,
        true,
        [],
      )],
        ['type' => 'section', 'sectionOptions' => ['type' => 'accordion']],
        false,
        false,
        [],
      )];
    }

    static function contentControls()
    {
        return [];
    }

    static function settingsControls()
    {
        return [];
    }

    static function dependencies()
    {
        return false;
    }

    static function settings()
    {
        return false;
    }

    static function addPanelRules()
    {
        return ['alwaysHide' => true];
    }

    static public function actions()
    {
        return false;
    }

    static function nestingRule()
    {
        return ["type" => "final",   ];
    }

    static function spacingBars()
    {
        return [];
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
        return 0;
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
        return false;
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}

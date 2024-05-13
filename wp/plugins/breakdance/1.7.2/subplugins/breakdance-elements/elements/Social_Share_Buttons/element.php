<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\SocialShareButtons",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class SocialShareButtons extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'StarIcon';
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
        return 'Social Share Buttons';
    }

    static function className()
    {
        return 'bde-social-share-buttons';
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
        return ['content' => ['buttons' => ['buttons' => ['0' => ['network' => 'Facebook'], '1' => ['network' => 'Twitter'], '2' => ['network' => 'LinkedIn'], '3' => ['network' => 'Email']]], 'share' => ['url' => 'page', 'text' => 'title']], 'design' => ['style' => ['display' => 'icon-text', 'style' => 'flat'], 'button' => ['icon_size' => ['breakpoint_base' => ['number' => 40, 'unit' => 'px', 'style' => '40px']], 'padding' => ['breakpoint_base' => ['number' => 12, 'unit' => 'px', 'style' => '12px']]], 'spacing' => ['between_buttons' => ['breakpoint_base' => ['number' => 20, 'unit' => 'px', 'style' => '20px'], 'breakpoint_phone_landscape' => ['number' => 15, 'unit' => 'px', 'style' => '15px']]], 'position' => ['placement' => 'inplace', 'alignment' => 'flex-start'], 'typography' => null]];
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
        "display",
        "Display",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'icon-text', 'text' => 'Icon & Text'], '1' => ['text' => 'Icon', 'value' => 'icon'], '2' => ['text' => 'Text', 'value' => 'text']]],
        false,
        false,
        [],
      ), c(
        "style",
        "Style",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'outline', 'text' => 'Outline'], '1' => ['text' => 'Minimal', 'value' => 'minimal'], '2' => ['text' => 'Box', 'value' => 'box'], '3' => ['text' => 'Flat', 'value' => 'flat']], 'condition' => ['path' => 'design.style.display', 'operand' => 'is none of', 'value' => ['0' => 'icon']]],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "button",
        "Button",
        [c(
        "icon_size",
        "Icon Size",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'rangeOptions' => ['step' => 1, 'min' => 12, 'max' => 120]],
        true,
        false,
        [],
      ), c(
        "padding",
        "Padding",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'rangeOptions' => ['step' => 1, 'min' => 0, 'max' => 30]],
        true,
        false,
        [],
      ), c(
        "colors",
        "Colors",
        [c(
        "icon",
        "Icon",
        [],
        ['type' => 'color', 'layout' => 'inline', 'colorOptions' => ['type' => 'solidOnly']],
        false,
        true,
        [],
      ), c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        true,
        [],
      ), c(
        "border",
        "Border",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        true,
        [],
      )],
        ['type' => 'section', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "border",
        "Border",
        [c(
        "style",
        "Style",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['value' => 'square', 'text' => 'Square'], '1' => ['text' => 'Circle', 'value' => 'circle'], '2' => ['text' => 'Custom', 'value' => 'custom']]],
        false,
        false,
        [],
      ), c(
        "radius",
        "Radius",
        [],
        ['type' => 'border_radius', 'layout' => 'vertical', 'condition' => ['path' => 'design.button.border.style', 'operand' => 'equals', 'value' => 'custom']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "position",
        "Position",
        [c(
        "placement",
        "Placement",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'inplace', 'text' => 'Static'], '1' => ['text' => 'Floating', 'value' => 'floating']]],
        false,
        false,
        [],
      ), c(
        "floating_position",
        "Floating Position",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'left', 'text' => 'Left', 'icon' => 'FlexAlignLeftIcon'], '1' => ['text' => 'Right', 'value' => 'right', 'icon' => 'FlexAlignRightIcon'], '2' => ['text' => 'Top', 'value' => 'top', 'icon' => 'FlexAlignTopIcon'], '3' => ['text' => 'Bottom', 'value' => 'bottom', 'icon' => 'FlexAlignBottomIcon']], 'buttonBarOptions' => ['size' => 'small'], 'condition' => ['path' => 'design.position.placement', 'operand' => 'equals', 'value' => 'floating']],
        false,
        false,
        [],
      ), c(
        "alignment",
        "Alignment",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'flex-start', 'text' => 'Left', 'icon' => 'AlignLeftIcon'], '1' => ['text' => 'Center', 'value' => 'center', 'icon' => 'AlignCenterIcon'], '2' => ['text' => 'Right', 'value' => 'flex-end', 'icon' => 'AlignRightIcon'], '3' => ['text' => 'Justify', 'value' => 'space-between', 'icon' => 'AlignJustifyIcon']], 'buttonBarOptions' => ['size' => 'small']],
        false,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "typography",
        "Typography",
        [getPresetSection(
      "EssentialElements\\typography",
      "Text",
      "text",
       ['type' => 'popout']
     )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "spacing",
        "Spacing",
        [getPresetSection(
      "EssentialElements\\spacing_margin_y",
      "Container",
      "container",
       ['type' => 'popout']
     ), c(
        "between_buttons",
        "Between Buttons",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => '%', '2' => 'em', '3' => 'rem', '4' => 'custom'], 'defaultType' => 'px'], 'rangeOptions' => ['step' => 1, 'min' => 0, 'max' => 100]],
        true,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      )];
    }

    static function contentControls()
    {
        return [c(
        "buttons",
        "Buttons",
        [c(
        "buttons",
        "Buttons",
        [c(
        "network",
        "Network",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'Facebook', 'text' => 'Facebook'], '1' => ['text' => 'Twitter', 'value' => 'Twitter'], '2' => ['text' => 'Pinterest', 'value' => 'Pinterest'], '3' => ['text' => 'LinkedIn', 'value' => 'LinkedIn'], '4' => ['text' => 'VK', 'value' => 'VK'], '5' => ['text' => 'Tumblr', 'value' => 'Tumblr'], '6' => ['text' => 'Reddit', 'value' => 'Reddit'], '7' => ['text' => 'Digg', 'value' => 'Digg'], '8' => ['text' => 'StumbleUpon', 'value' => 'StumbleUpon'], '9' => ['text' => 'Pocket', 'value' => 'Pocket'], '10' => ['text' => 'WhatsApp', 'value' => 'WhatsApp'], '11' => ['text' => 'Xing', 'value' => 'Xing'], '12' => ['text' => 'Telegram', 'value' => 'Telegram'], '13' => ['text' => 'Skype', 'value' => 'Skype'], '14' => ['text' => 'Print', 'value' => 'Print'], '15' => ['text' => 'Email', 'value' => 'Email']]],
        false,
        false,
        [],
      ), c(
        "custom_label",
        "Custom Label",
        [],
        ['type' => 'text', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'repeater', 'layout' => 'vertical', 'repeaterOptions' => ['titleTemplate' => '{network}', 'defaultTitle' => 'Network', 'buttonName' => 'Add Button']],
        false,
        false,
        [],
      ), c(
        "responsive",
        "Responsive",
        [c(
        "button_text",
        "Button Text",
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
      ), c(
        "share",
        "Share",
        [c(
        "url",
        "URL",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'page', 'text' => 'Current Page'], '1' => ['text' => 'Custom URL', 'value' => 'custom_url']]],
        false,
        false,
        [],
      ), c(
        "custom_url",
        "Custom URL",
        [],
        ['type' => 'url', 'layout' => 'vertical', 'condition' => ['path' => 'content.share.url', 'operand' => 'equals', 'value' => 'custom_url']],
        false,
        false,
        [],
      ), c(
        "text",
        "Text",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'title', 'text' => 'Page Title'], '1' => ['text' => 'Custom Text', 'value' => 'custom_text']]],
        false,
        false,
        [],
      ), c(
        "custom_text",
        "Custom Text",
        [],
        ['type' => 'text', 'layout' => 'vertical', 'textOptions' => ['multiline' => true], 'placeholder' => '', 'condition' => ['path' => 'content.share.text', 'operand' => 'equals', 'value' => 'custom_text']],
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
        return ['0' =>  ['scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%elements/Social_Share_Buttons/assets/social-share-buttons.js'],],'1' =>  ['inlineScripts' => ['new BreakdanceSocialShareButtons(\'%%SELECTOR%%\');'],'builderCondition' => 'return false;','frontendCondition' => 'return true;',],];
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
          if (window.breakdanceImageComparisonInstances && window.breakdanceSocialShareButtonsInstances[%%ID%%]) {
            window.breakdanceSocialShareButtonsInstances[%%ID%%].destroy();
          }

          window.breakdanceSocialShareButtonsInstances[%%ID%%] = new BreakdanceSocialShareButtons(\'%%SELECTOR%%\');
        }());',
],],

'onMountedElement' => [['script' => '(function() {
            if (!window.breakdanceSocialShareButtonsInstances) window.breakdanceSocialShareButtonsInstances = {};

            if (window.breakdanceSocialShareButtonsInstances && window.breakdanceSocialShareButtonsInstances[%%ID%%]) {
              window.breakdanceSocialShareButtonsInstances[%%ID%%].destroy();
            }

            window.breakdanceSocialShareButtonsInstances[%%ID%%] = new BreakdanceSocialShareButtons(\'%%SELECTOR%%\');
          }());
        ',
],],

'onMovedElement' => [['script' => '(function() {
          if (window.breakdanceSocialShareButtonsInstances && window.breakdanceSocialShareButtonsInstances[%%ID%%]) {
            window.breakdanceSocialShareButtonsInstances[%%ID%%].update();
          }
        }());',
],],

'onBeforeDeletingElement' => [['script' => ' (function() {
            if (window.breakdanceSocialShareButtonsInstances && window.breakdanceSocialShareButtonsInstances[%%ID%%]) {
              window.breakdanceSocialShareButtonsInstances[%%ID%%].destroy();
              delete window.breakdanceSocialShareButtonsInstances[%%ID%%];
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
        return ['0' => ['location' => 'outside-top', 'cssProperty' => 'margin-top', 'affectedPropertyPath' => 'design.spacing.container.margin_top.%%BREAKPOINT%%'], '1' => ['location' => 'outside-bottom', 'cssProperty' => 'margin-bottom', 'affectedPropertyPath' => 'design.spacing.container.margin_bottom.%%BREAKPOINT%%']];
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
        return 16500;
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

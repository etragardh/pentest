<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\Advancedslide",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class Advancedslide extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return '<svg aria-hidden="true" focusable="false"   class="svg-inline--fa fa-rectangle-wide" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="currentColor" d="M576 63.1H64c-35.35 0-64 28.65-64 63.1v256c0 35.35 28.65 64 64 64h512c35.35 0 64-28.65 64-64v-256C640 92.65 611.3 63.1 576 63.1zM608 384c0 17.64-14.36 32-32 32H64c-17.64 0-32-14.36-32-32V128c0-17.64 14.36-32 32-32h512c17.64 0 32 14.36 32 32V384z"></path></svg>';
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
        return 'Advanced Slide';
    }

    static function className()
    {
        return 'bde-advancedslide';
    }

    static function category()
    {
        return 'advanced';
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
        return ['design' => ['layout' => ['align_children' => null], 'background' => ['layers' => ['breakpoint_base' => ['0' => ['type' => 'gradient', 'gradient' => ['points' => ['0' => ['red' => 71, 'blue' => 255, 'green' => 188, 'alpha' => 1, 'left' => 0], '1' => ['red' => 255, 'blue' => 255, 'green' => 255, 'alpha' => 1, 'left' => 73]], 'type' => 'linear', 'degree' => 211, 'svgValue' => '<linearGradient x1="0.8" y1="0" x2="0.2" y2="1" id="%%GRADIENTID%%"><stop stop-opacity="1" stop-color="#47bcff" offset="0"></stop><stop stop-opacity="1" stop-color="#ffffff" offset="0.73"></stop></linearGradient>', 'value' => 'linear-gradient(211deg,rgba(71, 188, 255, 1) 0%,rgba(255, 255, 255, 1) 73%)']]]]]]];
    }

    static function defaultChildren()
    {
        return [['slug' => 'EssentialElements\Heading'], ['slug' => 'EssentialElements\Text']];
    }

    static function cssTemplate()
    {
        $template = file_get_contents(__DIR__ . '/css.twig');
        return $template;
    }

    static function designControls()
    {
        return [getPresetSection(
      "EssentialElements\\background",
      "Background",
      "background",
       ['type' => 'popout']
     ), c(
        "layout",
        "Layout",
        [c(
        "advanced",
        "Advanced",
        [c(
        "flex_direction",
        "Flex Direction",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'column', 'value' => 'column'], '1' => ['text' => 'row', 'value' => 'row'], '2' => ['text' => 'column-reverse'], '3' => ['text' => 'row-reverse', 'value' => 'row-reverse']]],
        true,
        false,
        [],
      ), c(
        "align_items",
        "Align Items",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'flex-start', 'text' => 'flex-start'], '1' => ['text' => 'center', 'value' => 'center'], '2' => ['text' => 'flex-end', 'value' => 'flex-end'], '3' => ['text' => 'stretch', 'value' => 'stretch'], '4' => ['text' => 'baseline', 'value' => 'baseline']], 'condition' => ['path' => 'design.layout.advanced.flex_direction', 'operand' => 'is set', 'value' => '']],
        true,
        false,
        [],
      ), c(
        "justify_content",
        "Justify Content",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'flex-start', 'text' => 'flex-start'], '1' => ['text' => 'center', 'value' => 'center'], '2' => ['text' => 'flex-end', 'value' => 'flex-end'], '3' => ['text' => 'space-between', 'value' => 'space-between'], '4' => ['value' => 'space-around', 'text' => 'space-around'], '5' => ['text' => 'space-evenly', 'value' => 'space-evenly']], 'condition' => ['path' => 'design.layout.advanced.flex_direction', 'operand' => 'is set', 'value' => null]],
        true,
        false,
        [],
      ), c(
        "flex_wrap",
        "Flex Wrap",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'wrap', 'text' => 'wrap'], '1' => ['value' => 'nowrap', 'text' => 'nowrap'], '2' => ['value' => 'wrap-reverse', 'text' => 'wrap-reverse']], 'condition' => ['path' => 'design.layout.advanced.flex_direction', 'operand' => 'is set', 'value' => '']],
        true,
        false,
        [],
      ), c(
        "align_content",
        "Align Content",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'flex-start', 'text' => 'flex-start'], '1' => ['text' => 'center', 'value' => 'center'], '2' => ['text' => 'flex-end', 'value' => 'flex-end'], '3' => ['value' => 'space-around', 'text' => 'space-around'], '4' => ['text' => 'stretch', 'value' => 'stretch'], '5' => ['text' => 'space-evenly', 'value' => 'space-evenly']], 'condition' => ['path' => 'design.layout.advanced.flex_direction', 'operand' => 'is set', 'value' => '']],
        true,
        false,
        [],
      ), c(
        "gap",
        "Gap",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => 'design.layout.advanced.flex_direction', 'operand' => 'is set', 'value' => null]],
        true,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "align_children",
        "Align Children",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['value' => 'left', 'text' => 'Left'], '1' => ['text' => 'Center', 'value' => 'center'], '2' => ['text' => 'Right', 'value' => 'right']], 'buttonBarOptions' => ['size' => 'small'], 'condition' => ['path' => 'design.layout.advanced.flex_direction', 'operand' => 'is not set', 'value' => '']],
        true,
        false,
        [],
      ), c(
        "vertical_align_children",
        "Vertical Align Children",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['value' => 'top', 'text' => 'Top'], '1' => ['text' => 'Middle', 'value' => 'middle'], '2' => ['text' => 'Bottom', 'value' => 'bottom']], 'buttonBarOptions' => ['size' => 'small'], 'condition' => ['path' => 'design.layout.advanced.flex_direction', 'operand' => 'is not set', 'value' => '']],
        true,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\spacing_padding_all",
      "Spacing",
      "spacing",
       ['type' => 'popout']
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
        return ['0' =>  ['scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/breakdance-swiper/breakdance-swiper.js'],],];
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

'onBeforeDeletingElement' => [['script' => 'window.BreakdanceSwiper().updateSliderFromChild("%%ID%%");',
],],

'onMovedElement' => [['script' => 'window.BreakdanceSwiper().updateSliderFromChild("%%ID%%");',
],],

'onMountedElement' => [['script' => 'window.BreakdanceSwiper().updateSliderFromChild("%%ID%%");',
],],

'onActivatedElement' => [['script' => 'window.BreakdanceSwiper().selectSlide("%%ID%%");','runForAllChildren' => true,
],],];
    }

    static function nestingRule()
    {
        return ["type" => "container", "restrictedToBeADirectChildOf" => ['EssentialElements\Advancedslider', 'EssentialElements\Coolslider'],  ];
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
        return 15;
    }

    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'image_url', 'path' => 'design.background.layers[].image']];
    }

    static function additionalClasses()
    {
        return [['name' => 'swiper-slide', 'template' => 'yes']];
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

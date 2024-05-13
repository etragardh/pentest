<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\ImageWithZoom",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class ImageWithZoom extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'ImageIcon';
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
        return 'Image With Zoom';
    }

    static function className()
    {
        return 'bde-image-with-zoom';
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
        return ['design' => ['styles' => ['container_width' => null, 'box_shadow' => ['shadows' => ['0' => ['color' => '#00000066', 'x' => '0', 'y' => '36', 'blur' => '24', 'spread' => '-24', 'position' => 'outset']], 'style' => '0px 36px 24px -24px #00000066'], 'box_shadow_hover' => ['shadows' => ['0' => ['color' => '#00000066', 'x' => '0', 'y' => '48', 'blur' => '36', 'spread' => '-16', 'position' => 'outset']], 'style' => '0px 48px 36px -16px #00000066'], 'spacing' => ['padding' => null], 'width' => ['number' => 600, 'unit' => 'px', 'style' => '600px'], 'height' => null, 'borders' => ['radius' => ['breakpoint_base' => ['all' => ['number' => 5, 'unit' => 'px', 'style' => '5px'], 'topLeft' => ['number' => 5, 'unit' => 'px', 'style' => '5px'], 'topRight' => ['number' => 5, 'unit' => 'px', 'style' => '5px'], 'bottomLeft' => ['number' => 5, 'unit' => 'px', 'style' => '5px'], 'bottomRight' => ['number' => 5, 'unit' => 'px', 'style' => '5px'], 'editMode' => 'all']]], 'background_color' => null, 'transition_duration' => ['number' => 100, 'unit' => 'ms', 'style' => '100ms']], 'spacing' => ['space_above' => null, 'space_below' => null], 'container' => ['width' => ['breakpoint_base' => ['number' => 500, 'unit' => 'px', 'style' => '500px']], 'borders' => ['radius' => ['breakpoint_base' => ['all' => ['number' => 5, 'unit' => 'px', 'style' => '5px'], 'topLeft' => ['number' => 5, 'unit' => 'px', 'style' => '5px'], 'topRight' => ['number' => 5, 'unit' => 'px', 'style' => '5px'], 'bottomLeft' => ['number' => 5, 'unit' => 'px', 'style' => '5px'], 'bottomRight' => ['number' => 5, 'unit' => 'px', 'style' => '5px'], 'editMode' => 'all']]]], 'effect' => ['transition_duration' => null, 'zoom_scale' => 2, 'cursor' => 'zoom-in']], 'content' => ['controls' => ['zoom_scale' => 2, 'transition_duration' => ['number' => 100, 'unit' => 'ms', 'style' => '100ms'], 'image' => null, 'image_dynamic_meta' => null]]];
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
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
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
      ), c(
        "effect",
        "Effect",
        [c(
        "zoom_scale",
        "Zoom Scale",
        [],
        ['type' => 'number', 'layout' => 'inline', 'rangeOptions' => ['min' => 1, 'max' => 5, 'step' => 0.1]],
        false,
        false,
        [],
      ), c(
        "cursor",
        "Cursor",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'default', 'text' => 'Default'], '1' => ['text' => 'Zoom-In', 'value' => 'zoom-in'], '2' => ['text' => 'Move', 'value' => 'move'], '3' => ['text' => 'Crosshair', 'value' => 'crosshair']]],
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
        "controls",
        "Controls",
        [c(
        "image",
        "Image",
        [],
        ['type' => 'wpmedia', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "image_size",
        "Image Size",
        [],
        ['type' => 'media_size_dropdown', 'layout' => 'vertical', 'mediaSizeOptions' => ['imagePropertyPath' => 'content.controls.image'], 'condition' => ['path' => 'content.controls.image', 'operand' => 'is set', 'value' => null]],
        false,
        false,
        [],
      ), c(
        "alt",
        "Alt",
        [],
        ['type' => 'text', 'layout' => 'vertical', 'condition' => ['path' => 'content.controls.image', 'operand' => 'is set', 'value' => '']],
        false,
        false,
        [],
      ), c(
        "lazy_load",
        "Lazy Load",
        [],
        ['type' => 'toggle', 'layout' => 'inline', 'condition' => ['path' => 'content.controls.image', 'operand' => 'is set', 'value' => '']],
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
        return ['0' =>  ['inlineScripts' => ['const ZoomContainer = document.querySelectorAll(\'.image-width-zoom\')

ZoomContainer.forEach(function(elem) {
  elem.addEventListener(\'mousemove\', (e) =>{
    zoom(event);
  })
});

function zoom(e){
  const zoomer = e.currentTarget;
  e.offsetX ? offsetX = e.offsetX : offsetX = e.touches[0].pageX
  e.offsetY ? offsetY = e.offsetY : offsetX = e.touches[0].pageX
  x = offsetX/zoomer.offsetWidth*100
  y = offsetY/zoomer.offsetHeight*100
  zoomer.style.backgroundPosition = x + \'% \' + y + \'%\';
}'],],];
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

'onPropertyChange' => [['script' => 'const ZoomContainer = document.querySelectorAll(\'.image-width-zoom\')

ZoomContainer.forEach(function(elem) {
  elem.addEventListener(\'mousemove\', (e) =>{
    zoom(event);
  })
});

function zoom(e){
  const zoomer = e.currentTarget;
  e.offsetX ? offsetX = e.offsetX : offsetX = e.touches[0].pageX
  e.offsetY ? offsetY = e.offsetY : offsetX = e.touches[0].pageX
  x = offsetX/zoomer.offsetWidth*100
  y = offsetY/zoomer.offsetHeight*100
  zoomer.style.backgroundPosition = x + \'% \' + y + \'%\';
}',
],],

'onMountedElement' => [['script' => 'const ZoomContainer = document.querySelectorAll(\'.image-width-zoom\')

ZoomContainer.forEach(function(elem) {
  elem.addEventListener(\'mousemove\', (e) =>{
    zoom(event);
  })
});

function zoom(e){
  const zoomer = e.currentTarget;
  e.offsetX ? offsetX = e.offsetX : offsetX = e.touches[0].pageX
  e.offsetY ? offsetY = e.offsetY : offsetX = e.touches[0].pageX
  x = offsetX/zoomer.offsetWidth*100
  y = offsetY/zoomer.offsetHeight*100
  zoomer.style.backgroundPosition = x + \'% \' + y + \'%\';
}',
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
        return [['name' => 'style', 'template' => 'background-image: url({{ content.controls.image.url|default("https://images.pexels.com/photos/248280/pexels-photo-248280.jpeg")}}']];
    }

    static function experimental()
    {
        return false;
    }

    static function order()
    {
        return 3800;
    }

    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'image_url', 'path' => 'content.controls.image'], '1' => ['accepts' => 'string', 'path' => 'content.controls.alt']];
    }

    static function additionalClasses()
    {
        return [['name' => 'image-width-zoom', 'template' => 'yes']];
    }

    static function projectManagement()
    {
        return ['looksGood' => 'yes', 'optionsGood' => 'yes', 'optionsWork' => 'yes'];
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

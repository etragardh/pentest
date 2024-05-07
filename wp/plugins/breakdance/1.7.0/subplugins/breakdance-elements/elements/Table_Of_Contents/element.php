<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\TableOfContents",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class TableOfContents extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'ListIcon';
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
        return 'Table Of Contents';
    }

    static function className()
    {
        return 'bde-table-of-contents';
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
        return ['content' => ['table' => ['included_headings' => ['h2' => true, 'h3' => true, 'h4' => true], 'title' => 'Table Of Contents', 'advanced' => ['ignore_selectors' => '.toc-ignore']]], 'design' => ['spacing' => ['below_header' => ['number' => 0, 'unit' => 'px', 'style' => '0px']], 'header' => null, 'container' => null]];
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
      ), c(
        "content_alignment",
        "Content Alignment",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'left', 'text' => 'Label', 'icon' => 'FlexAlignLeftIcon'], '1' => ['icon' => 'FlexAlignRightIcon', 'value' => 'right', 'text' => 'Riht']]],
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
      ), getPresetSection(
      "EssentialElements\\spacing_padding_all",
      "Padding",
      "padding",
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\borders",
      "Borders",
      "borders",
       ['type' => 'popout']
     )],
        ['type' => 'section', 'layout' => 'vertical', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "header",
        "Header",
        [c(
        "disable",
        "Disable",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
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
     ), c(
        "accordion",
        "Accordion",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "icon",
        "Icon",
        [c(
        "icon",
        "Icon",
        [],
        ['type' => 'icon', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "active_icon",
        "Active Icon",
        [],
        ['type' => 'icon', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "color",
        "Color",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout'], 'condition' => ['path' => 'design.header.accordion', 'operand' => 'is set', 'value' => '']],
        false,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "list",
        "List",
        [c(
        "scroll_offset",
        "Scroll Offset",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px']],
        false,
        false,
        [],
      ), c(
        "collapse",
        "Collapse",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "style",
        "Style",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['text' => 'None', 'value' => 'none'], '1' => ['text' => 'Bullets', 'value' => 'disc'], '2' => ['text' => 'Numbers', 'value' => 'decimal']], 'buttonBarOptions' => ['size' => 'small', 'layout' => 'default']],
        false,
        false,
        [],
      ), c(
        "bar",
        "Bar",
        [c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "active",
        "Active",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "indent",
        "Indent",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "between_items",
        "Between Items",
        [],
        ['type' => 'number', 'layout' => 'inline', 'rangeOptions' => ['min' => 1, 'max' => 4, 'step' => 0.1]],
        true,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\typography_with_align_with_hoverable_color",
      "Typography",
      "typography",
       ['type' => 'popout']
     ), c(
        "active_typography",
        "Active Typography",
        [c(
        "color",
        "Color",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "weight",
        "Weight",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => '100', 'text' => '100'], '1' => ['text' => '200', 'value' => '200'], '2' => ['text' => '300', 'value' => '300'], '3' => ['text' => '400', 'value' => '400'], '4' => ['text' => '500', 'value' => '500'], '5' => ['text' => '600', 'value' => '600'], '6' => ['text' => '700', 'value' => '700'], '7' => ['text' => '800', 'value' => '800'], '8' => ['text' => '900', 'value' => '900']]],
        false,
        false,
        [],
      ), c(
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
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
        "spacing",
        "Spacing",
        [c(
        "below_header",
        "Below Header",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\spacing_margin_y",
      "Wrapper",
      "wrapper",
       ['condition' => ['path' => 'design.container.sticky.position', 'operand' => 'is not set', 'value' => ''], 'type' => 'popout']
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
        "table",
        "Table",
        [c(
        "title",
        "Title",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "included_headings",
        "Included Headings",
        [c(
        "h1",
        "H1",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "h2",
        "H2",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "h3",
        "H3",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "h4",
        "H4",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "h5",
        "H5",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "h6",
        "H6",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'vertical', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "advanced",
        "Advanced",
        [c(
        "ignore_selectors",
        "Ignore Selectors",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "container_selector",
        "Container Selector",
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
        return ['0' =>  ['title' => 'tocbot','scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/tocbot@4/tocbot.min.js'],],'1' =>  ['title' => 'Breakdance TOC','scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/toc@1/breakdance-toc.js'],],'2' =>  ['title' => 'Breakdance TOC frontend','builderCondition' => 'return false;','frontendCondition' => 'return true;','inlineScripts' => ['new BreakdanceTOC(\'%%SELECTOR%%\', { content: {{ content|json_encode }}, design: {{ design.list|json_encode }}, sticky: {{ design.container.sticky_options|json_encode }}  } );'],],];
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

'onMountedElement' => [['script' => '(function() {
              if (!window.breakdanceTOCInstances) window.breakdanceTOCInstances = {};

              if (window.breakdanceTOCInstances && window.breakdanceTOCInstances[%%ID%%]) {
                window.breakdanceTOCInstances[%%ID%%].destroy();
              }

              window.breakdanceTOCInstances[%%ID%%] = new BreakdanceTOC(\'%%SELECTOR%%\', { content: {{ content|json_encode }}, design: {{ design.list|json_encode }}, sticky: {{ design.container.sticky_options|json_encode }}  } );
            }());',
],],

'onPropertyChange' => [['script' => '(function() {
              if (window.breakdanceTOCInstances && window.breakdanceTOCInstances[%%ID%%]) {
                window.breakdanceTOCInstances[%%ID%%].destroy();
              }

              window.breakdanceTOCInstances[%%ID%%] = new BreakdanceTOC(\'%%SELECTOR%%\', { content: {{ content|json_encode }}, design: {{ design.list|json_encode }}, sticky: {{ design.container.sticky_options|json_encode }}  } );
            }());',
],],

'onBeforeDeletingElement' => [['script' => '(function() {

              if (window.breakdanceTOCInstances && window.breakdanceTOCInstances[%%ID%%]) {

                window.breakdanceTOCInstances[%%ID%%].destroy();
                delete window.breakdanceTOCInstances[%%ID%%];
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
        return ['0' => ['location' => 'outside-top', 'cssProperty' => 'margin-top', 'affectedPropertyPath' => 'design.spacing.wrapper.margin_top.%%BREAKPOINT%%'], '1' => ['location' => 'outside-bottom', 'cssProperty' => 'margin-bottom', 'affectedPropertyPath' => 'design.spacing.wrapper.margin_bottom.%%BREAKPOINT%%']];
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
        return 3900;
    }

    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'string', 'path' => 'content.table.title']];
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
        return ['design.container.sticky_options.disable_at'];
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}

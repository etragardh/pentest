<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\AdvancedAccordion",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class AdvancedAccordion extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.2.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path fill="currentColor" d="M64 224C28.65 224 0 195.3 0 160V96C0 60.65 28.65 32 64 32H448C483.3 32 512 60.65 512 96V160C512 195.3 483.3 224 448 224H64zM512 416C512 451.3 483.3 480 448 480H64C28.65 480 0 451.3 0 416V352C0 316.7 28.65 288 64 288H448C483.3 288 512 316.7 512 352V416zM448 336H64C55.16 336 48 343.2 48 352V416C48 424.8 55.16 432 64 432H448C456.8 432 464 424.8 464 416V352C464 343.2 456.8 336 448 336z"/></svg>';
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
        return 'Advanced Accordion';
    }

    static function className()
    {
        return 'bde-accordion';
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
        return ['design' => ['item' => ['style' => 'bordered', 'icon' => ['icon' => ['slug' => 'icon-chevron-right.', 'name' => 'chevron right', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"/></svg>']]]]];
    }

    static function defaultChildren()
    {
        return [['slug' => 'EssentialElements\AccordionContent', 'defaultProperties' => ['content' => ['content' => ['icon' => ['default' => ['slug' => 'icon-plus.', 'name' => 'plus', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M416 208H272V64c0-17.67-14.33-32-32-32h-32c-17.67 0-32 14.33-32 32v144H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h144v144c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32V304h144c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"/></svg>'], 'active' => ['slug' => 'icon-minus.', 'name' => 'minus', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M416 208H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h384c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"/></svg>']], 'title' => 'Content']], 'design' => ['container' => ['padding' => ['padding' => ['breakpoint_base' => ['left' => ['number' => 16, 'unit' => 'px', 'style' => '16px'], 'right' => ['number' => 16, 'unit' => 'px', 'style' => '16px'], 'top' => ['number' => 16, 'unit' => 'px', 'style' => '16px'], 'bottom' => ['number' => 16, 'unit' => 'px', 'style' => '16px']]]], 'background' => '#E6F2FFFF'], 'layout' => ['gap' => ['breakpoint_base' => ['number' => 16, 'unit' => 'px', 'style' => '16px']]]]], 'children' => ['0' => ['slug' => 'EssentialElements\Heading', 'defaultProperties' => ['content' => ['content' => ['text' => 'McWay Falls']]]], '1' => ['slug' => 'EssentialElements\Text', 'defaultProperties' => ['content' => ['content' => ['text' => 'McWay Falls is an 80-foot-tall waterfall on the coast of Big Sur in central California that flows year-round from McWay Creek in Julia Pfeiffer Burns State Park, about 37 miles south of Carmel, into the Pacific Ocean. During high tide, it is a tidefall, a waterfall that empties directly into the ocean']], 'design' => ['spacing' => ['margin_bottom' => null, 'margin_top' => null]]]], '2' => ['slug' => 'EssentialElements\Image', 'defaultProperties' => ['content' => ['content' => ['image' => ['id' => -1, 'type' => 'external_image', 'url' => 'https://images.unsplash.com/photo-1510414842594-a61c69b5ae57?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2340&q=80', 'alt' => '', 'caption' => '']]]]]]], ['slug' => 'EssentialElements\AccordionContent', 'children' => ['0' => ['slug' => 'EssentialElements\Video']], 'defaultProperties' => ['content' => ['content' => ['icon' => ['default' => ['slug' => 'icon-plus.', 'name' => 'plus', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M416 208H272V64c0-17.67-14.33-32-32-32h-32c-17.67 0-32 14.33-32 32v144H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h144v144c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32V304h144c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"/></svg>'], 'active' => ['slug' => 'icon-minus.', 'name' => 'minus', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M416 208H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h384c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"/></svg>']], 'title' => 'Video']], 'design' => ['container' => ['padding' => null]]]], ['slug' => 'EssentialElements\AccordionContent', 'children' => ['0' => ['slug' => 'EssentialElements\RichText']], 'defaultProperties' => ['content' => ['content' => ['icon' => ['default' => ['slug' => 'icon-plus.', 'name' => 'plus', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M416 208H272V64c0-17.67-14.33-32-32-32h-32c-17.67 0-32 14.33-32 32v144H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h144v144c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32V304h144c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"/></svg>'], 'active' => ['slug' => 'icon-minus.', 'name' => 'minus', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M416 208H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h384c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"/></svg>']], 'title' => 'Text']]]], ['slug' => 'EssentialElements\AccordionContent', 'children' => ['0' => ['slug' => 'EssentialElements\SimpleTestimonial']], 'defaultProperties' => ['content' => ['content' => ['icon' => ['default' => ['slug' => 'icon-plus.', 'name' => 'plus', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M416 208H272V64c0-17.67-14.33-32-32-32h-32c-17.67 0-32 14.33-32 32v144H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h144v144c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32V304h144c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"/></svg>'], 'active' => ['slug' => 'icon-minus.', 'name' => 'minus', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M416 208H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h384c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"/></svg>']], 'title' => 'Testimonial']], 'design' => ['layout' => ['align' => ['breakpoint_base' => 'center']]]]]];
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
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline', 'colorOptions' => ['type' => 'solidAndGradient']],
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
      ), getPresetSection(
      "EssentialElements\\spacing_padding_all",
      "Padding",
      "padding",
       ['type' => 'popout']
     )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "item",
        "Item",
        [c(
        "style",
        "Style",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'bordered', 'text' => 'Bordered'], '1' => ['text' => 'Pills', 'value' => 'pills']]],
        false,
        false,
        [],
      ), c(
        "icon",
        "Icon",
        [c(
        "position",
        "Position",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'left', 'text' => 'Left'], '1' => ['text' => 'Right', 'value' => 'right']]],
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
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 4, 'max' => 100, 'step' => 1], 'unitOptions' => ['types' => ['0' => 'px']]],
        true,
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
      ), c(
        "active_color",
        "Active Color",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "bordered",
        "Bordered",
        [c(
        "border_color",
        "Border Color",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "border_width",
        "Border Width",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "border_radius",
        "Border Radius",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "bottom_only",
        "Bottom Only",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout'], 'condition' => ['path' => 'design.item.style', 'operand' => 'equals', 'value' => 'bordered']],
        false,
        false,
        [],
      ), c(
        "background",
        "Background",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        true,
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
       ['condition' => ['path' => 'design.item.style', 'operand' => 'equals', 'value' => 'pills'], 'type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_align_with_hoverable_color",
      "Typography",
      "typography",
       ['type' => 'popout']
     ), c(
        "active",
        "Active",
        [c(
        "text_color",
        "Text Color",
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
        "between_items",
        "Between Items",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 200, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "below_item",
        "Below Item",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 200, 'step' => 1]],
        true,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\spacing_margin_y",
      "Container",
      "container",
       ['type' => 'popout']
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
        "content",
        "Content",
        [c(
        "content",
        "Content",
        [],
        ['type' => 'add_registered_children', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "accordion",
        "Accordion",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "first_item_opened",
        "First Item Opened",
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
        return ['0' =>  ['title' => 'Load Advanced Accordion','scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/breakdance-advanced-accordion/breakdance-advanced-accordion.js'],],'1' =>  ['title' => 'Load Advanced Accordion Management','scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/breakdance-advanced-accordion/breakdance-advanced-accordion-management.js'],'builderCondition' => 'return true;','frontendCondition' => 'return false;',],'2' =>  ['inlineScripts' => ['new BreakdanceAdvancedAccordion(\'%%SELECTOR%%\', { accordion: {{ content.content.accordion|json_encode }}, openFirst: {{ content.content.first_item_opened|json_encode }} } );'],'builderCondition' => 'return false;','title' => 'Init BreakdanceAdvancedAccordion in the frontend','frontendCondition' => 'return true;',],];
    }

    static function settings()
    {
        return ['shareStateWithChildSSR' => true];
    }

    static function addPanelRules()
    {
        return false;
    }

    static public function actions()
    {
        return [

'onPropertyChange' => [['script' => '(function() {
  if (window.breakdanceAdvancedAccordionInstances && window.breakdanceAdvancedAccordionInstances[%%ID%%]) {
    window.breakdanceAdvancedAccordionInstances[%%ID%%].destroy();
  }

  window.breakdanceAdvancedAccordionInstances[%%ID%%] = new BreakdanceAdvancedAccordion(\'%%SELECTOR%%\', { accordion: {{ content.content.accordion|json_encode }}, openFirst: {{ content.content.first_item_opened|json_encode }}, ignoreClickEvent: true } );
}());',
],],

'onMountedElement' => [['script' => '(function() {
    if (!window.breakdanceAdvancedAccordionInstances) window.breakdanceAdvancedAccordionInstances = {};

    if (window.breakdanceAdvancedAccordionInstances && window.breakdanceAdvancedAccordionInstances[%%ID%%]) {
      window.breakdanceAdvancedAccordionInstances[%%ID%%].destroy();
    }

    window.breakdanceAdvancedAccordionInstances[%%ID%%] = new BreakdanceAdvancedAccordion(\'%%SELECTOR%%\', { accordion: {{ content.content.accordion|json_encode }}, openFirst: {{ content.content.first_item_opened|json_encode }}, ignoreClickEvent: true } );
  }());',
],],

'onMovedElement' => [['script' => '(function() {
  if (window.breakdanceAdvancedAccordionInstances && window.breakdanceAdvancedAccordionInstances[%%ID%%]) {
    window.breakdanceAdvancedAccordionInstances[%%ID%%].update();
  }
}());',
],],

'onBeforeDeletingElement' => [['script' => '  (function() {
    if (window.breakdanceAdvancedAccordionInstances && window.breakdanceAdvancedAccordionInstances[%%ID%%]) {
      window.breakdanceAdvancedAccordionInstances[%%ID%%].destroy();
      delete window.breakdanceAdvancedAccordionInstances[%%ID%%];
    }
  }());',
],],];
    }

    static function nestingRule()
    {
        return ["type" => "container-restricted",   ];
    }

    static function spacingBars()
    {
        return ['0' => ['cssProperty' => 'margin-top', 'location' => 'outside-top', 'affectedPropertyPath' => 'design.spacing.container.margin_top.%%BREAKPOINT%%'], '1' => ['cssProperty' => 'margin-bottom', 'location' => 'outside-bottom', 'affectedPropertyPath' => 'design.spacing.container.margin_bottom.%%BREAKPOINT%%']];
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
        return false;
    }

    static function additionalClasses()
    {
        return [['name' => 'bde-accordion--bordered', 'template' => '{% if design.item.style == \'bordered\' and not design.item.bordered.bottom_only %}
bordered
{% endif %}'], ['name' => 'bde-accordion--bordered-bottom', 'template' => '{% if design.item.bordered.bottom_only %}
bordered
{% endif %}'], ['name' => 'bde-accordion--pills', 'template' => '{% if design.item.style == \'pills\' %}
pills
{% endif %}']];
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

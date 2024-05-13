<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\IconList",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class IconList extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return '<svg aria-hidden="true" focusable="false"   class="svg-inline--fa fa-rectangle-list" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M464 144h-256C199.2 144 192 151.2 192 160C192 168.8 199.2 176 208 176h256c8.801 0 16-7.201 16-16C480 151.2 472.8 144 464 144zM128.1 136C114.1 136 104 146.8 104 160s10.1 24 24.14 24C141.4 184 152 173.2 152 160S141.4 136 128.1 136zM128.1 232C114.1 232 104 242.8 104 256s10.1 24 24.14 24C141.4 280 152 269.2 152 256S141.4 232 128.1 232zM464 240h-256C199.2 240 192 247.2 192 256c0 8.799 7.201 16 16 16h256c8.801 0 16-7.201 16-16C480 247.2 472.8 240 464 240zM128.1 328C114.1 328 104 338.8 104 352s10.1 24 24.14 24C141.4 376 152 365.2 152 352S141.4 328 128.1 328zM464 336h-256C199.2 336 192 343.2 192 352c0 8.799 7.201 16 16 16h256c8.801 0 16-7.201 16-16C480 343.2 472.8 336 464 336zM512 32H64C28.65 32 0 60.65 0 96v320c0 35.35 28.65 64 64 64h448c35.35 0 64-28.65 64-64V96C576 60.65 547.3 32 512 32zM544 416c0 17.64-14.36 32-32 32H64c-17.64 0-32-14.36-32-32V96c0-17.64 14.36-32 32-32h448c17.64 0 32 14.36 32 32V416z"></path></svg>';
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
        return 'Icon List';
    }

    static function className()
    {
        return 'bde-icon-list';
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
        return ['content' => ['content' => ['list' => ['0' => ['text' => 'Automatic dimming at night', 'icon' => ['slug' => 'icon-moon.', 'name' => 'moon', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M279.135 512c78.756 0 150.982-35.804 198.844-94.775 28.27-34.831-2.558-85.722-46.249-77.401-82.348 15.683-158.272-47.268-158.272-130.792 0-48.424 26.06-92.292 67.434-115.836 38.745-22.05 28.999-80.788-15.022-88.919A257.936 257.936 0 0 0 279.135 0c-141.36 0-256 114.575-256 256 0 141.36 114.576 256 256 256zm0-464c12.985 0 25.689 1.201 38.016 3.478-54.76 31.163-91.693 90.042-91.693 157.554 0 113.848 103.641 199.2 215.252 177.944C402.574 433.964 344.366 464 279.135 464c-114.875 0-208-93.125-208-208s93.125-208 208-208z"/></svg>']], '1' => ['icon' => ['slug' => 'icon-lightbulb.', 'name' => 'lightbulb', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M176 80c-52.94 0-96 43.06-96 96 0 8.84 7.16 16 16 16s16-7.16 16-16c0-35.3 28.72-64 64-64 8.84 0 16-7.16 16-16s-7.16-16-16-16zM96.06 459.17c0 3.15.93 6.22 2.68 8.84l24.51 36.84c2.97 4.46 7.97 7.14 13.32 7.14h78.85c5.36 0 10.36-2.68 13.32-7.14l24.51-36.84c1.74-2.62 2.67-5.7 2.68-8.84l.05-43.18H96.02l.04 43.18zM176 0C73.72 0 0 82.97 0 176c0 44.37 16.45 84.85 43.56 115.78 16.64 18.99 42.74 58.8 52.42 92.16v.06h48v-.12c-.01-4.77-.72-9.51-2.15-14.07-5.59-17.81-22.82-64.77-62.17-109.67-20.54-23.43-31.52-53.15-31.61-84.14-.2-73.64 59.67-128 127.95-128 70.58 0 128 57.42 128 128 0 30.97-11.24 60.85-31.65 84.14-39.11 44.61-56.42 91.47-62.1 109.46a47.507 47.507 0 0 0-2.22 14.3v.1h48v-.05c9.68-33.37 35.78-73.18 52.42-92.16C335.55 260.85 352 220.37 352 176 352 78.8 273.2 0 176 0z"/></svg>'], 'text' => '500 lumens of brightness'], '2' => ['icon' => ['slug' => 'icon-solar-panel.', 'name' => 'solar panel', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M431.98 448.01l-47.97.05V416h-128v32.21l-47.98.05c-8.82.01-15.97 7.16-15.98 15.99l-.05 31.73c-.01 8.85 7.17 16.03 16.02 16.02l223.96-.26c8.82-.01 15.97-7.16 15.98-15.98l.04-31.73c.01-8.85-7.17-16.03-16.02-16.02zM585.2 26.74C582.58 11.31 568.99 0 553.06 0H86.93C71 0 57.41 11.31 54.79 26.74-3.32 369.16.04 348.08.03 352c-.03 17.32 14.29 32 32.6 32h574.74c18.23 0 32.51-14.56 32.59-31.79.02-4.08 3.35 16.95-54.76-325.47zM259.83 64h120.33l9.77 96H250.06l9.77-96zm-75.17 256H71.09L90.1 208h105.97l-11.41 112zm16.29-160H98.24l16.29-96h96.19l-9.77 96zm32.82 160l11.4-112h149.65l11.4 112H233.77zm195.5-256h96.19l16.29 96H439.04l-9.77-96zm26.06 256l-11.4-112H549.9l19.01 112H455.33z"/></svg>'], 'text' => 'Extremely energy efficient', 'link' => null]], 'positive_icon' => [], 'negative_icon' => []]], 'design' => ['icon' => ['size' => null, 'color' => null, 'background' => null, 'padding' => null, 'radius' => null, 'fill' => null, 'color_hover' => null, 'fill_hover' => null], 'layout' => null, 'typography' => ['color' => null, 'color_hover' => null, 'typography' => ['custom' => ['customTypography' => ['fontSize' => ['breakpoint_base' => ['number' => 14, 'unit' => 'px', 'style' => '14px']], 'fontWeight' => ['breakpoint_base' => '500']]]]]]];
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
        "icon",
        "Icon",
        [c(
        "position",
        "Position",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['value' => 'flex-start', 'text' => 'Top', 'icon' => 'FlexAlignTopIcon'], '1' => ['value' => 'center', 'text' => 'Bottom', 'icon' => 'FlexAlignCenterVerticalIcon'], '2' => ['value' => 'flex-end', 'text' => 'Bottom', 'icon' => 'FlexAlignBottomIcon']]],
        false,
        false,
        [],
      ), c(
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => [], 'defaultType' => 'px'], 'rangeOptions' => ['step' => 1, 'min' => 8, 'max' => 50]],
        true,
        false,
        [],
      ), c(
        "color",
        "Color",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        true,
        [],
      ), c(
        "background",
        "Background",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "fill",
        "Fill",
        [],
        ['type' => 'color', 'layout' => 'inline', 'colorOptions' => ['type' => 'solidAndGradient'], 'condition' => ['path' => 'design.icon.background', 'operand' => 'is set', 'value' => '']],
        false,
        true,
        [],
      ), c(
        "padding",
        "Padding",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => 'design.icon.background', 'operand' => 'is set', 'value' => ''], 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'rangeOptions' => ['step' => 1, 'min' => 0, 'max' => 50]],
        true,
        false,
        [],
      ), c(
        "radius",
        "Radius",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'condition' => ['path' => 'design.icon.background', 'operand' => 'is set', 'value' => ''], 'rangeOptions' => ['step' => 1, 'min' => 0, 'max' => 40]],
        true,
        false,
        [],
      ), c(
        "nudge",
        "Nudge",
        [c(
        "vertical",
        "Vertical",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => -64, 'max' => 64, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "horizontal",
        "Horizontal",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => -64, 'max' => 64, 'step' => 1]],
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
        "layout",
        "Layout",
        [c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "text_indent",
        "Text Indent",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'rangeOptions' => ['step' => 1, 'min' => 1, 'max' => 30]],
        true,
        false,
        [],
      ), c(
        "space_between",
        "Space Between",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px'], 'rangeOptions' => ['step' => 1, 'min' => 0, 'max' => 75]],
        true,
        false,
        [],
      ), c(
        "stack",
        "Stack",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => ['0' => ['text' => 'Vertical', 'value' => 'vertical'], '1' => ['value' => 'horizontal', 'text' => 'Horizontal']]],
        false,
        false,
        [],
      ), c(
        "horizontal_align",
        "Horizontal Align",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'space-between', 'text' => 'Between'], '1' => ['text' => 'Around', 'value' => 'space-around'], '2' => ['text' => 'Center', 'value' => 'center'], '3' => ['text' => 'Start', 'value' => 'flex-start'], '4' => ['text' => 'End', 'value' => 'flex-end']], 'condition' => ['path' => 'design.layout.stack', 'operand' => 'equals', 'value' => 'horizontal']],
        false,
        false,
        [],
      ), c(
        "stack_vertical_at",
        "Stack Vertical At",
        [],
        ['type' => 'breakpoint_dropdown', 'layout' => 'inline', 'condition' => ['path' => 'design.layout.stack', 'operand' => 'equals', 'value' => 'horizontal']],
        false,
        false,
        [],
      ), c(
        "alignment_when_vertical",
        "Alignment When Vertical",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['value' => 'left', 'text' => 'Left', 'icon' => 'AlignLeftIcon'], '1' => ['text' => 'Center', 'value' => 'center', 'icon' => 'AlignCenterIcon'], '2' => ['text' => 'Right', 'value' => 'right', 'icon' => 'AlignRightIcon']]],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'sectionOptions' => ['type' => 'accordion']],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\typography_with_effects_and_align_with_hoverable_everything",
      "Typography",
      "typography",
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
        "list",
        "List",
        [c(
        "text",
        "Text",
        [],
        ['type' => 'text', 'layout' => 'vertical', 'textOptions' => ['multiline' => true]],
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
        "link",
        "Link",
        [],
        ['type' => 'link', 'layout' => 'vertical'],
        false,
        false,
        [],
      )],
        ['type' => 'repeater', 'layout' => 'vertical', 'repeaterOptions' => ['titleTemplate' => '{text}', 'defaultTitle' => 'List Item', 'buttonName' => '']],
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
        return false;
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
        return false;
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
        return 500;
    }

    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'string', 'path' => 'content.content.list[].text'], '1' => ['accepts' => 'url', 'path' => 'content.content.list[].link.url']];
    }

    static function additionalClasses()
    {
        return [['name' => 'bde-icon-list--background', 'template' => '{{ design.icon.background ? \'true\' }}']];
    }

    static function projectManagement()
    {
        return ['looksGood' => 'yes', 'optionsGood' => 'yes', 'optionsWork' => 'yes'];
    }

    static function propertyPathsToWhitelistInFlatProps()
    {
        return ['design.layout.stack_vertical_at', 'design.layout.alignment_when_vertical', 'design.icon.background'];
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}

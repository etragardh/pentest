<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\StatsGrid", 
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class StatsGrid extends \Breakdance\Elements\Element
{
    static function uiIcon() 
    {
        return '<svg aria-hidden="true" focusable="false"   class="svg-inline--fa fa-table-cells-large" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M448 32H64C28.65 32 0 60.65 0 96v320c0 35.35 28.65 64 64 64h384c35.35 0 64-28.65 64-64V96C512 60.65 483.3 32 448 32zM240 448H64c-17.67 0-32-14.33-32-32V272h208V448zM240 240H32V96c0-17.67 14.33-32 32-32h176V240zM480 416c0 17.67-14.33 32-32 32h-176V272H480V416zM480 240h-208V64H448c17.67 0 32 14.33 32 32V240z"></path></svg>';
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
        return 'Stats Grid';
    }
    
    static function className()
    {
        return 'bde-stats-grid';
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
        return ['content' => ['content' => ['items' => ['0' => ['icon' => ['slug' => 'icon-cat.', 'name' => 'cat', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M290.59 192c-20.18 0-106.82 1.98-162.59 85.95V192c0-52.94-43.06-96-96-96-17.67 0-32 14.33-32 32s14.33 32 32 32c17.64 0 32 14.36 32 32v256c0 35.3 28.7 64 64 64h176c8.84 0 16-7.16 16-16v-16c0-17.67-14.33-32-32-32h-32l128-96v144c0 8.84 7.16 16 16 16h32c8.84 0 16-7.16 16-16V289.86c-10.29 2.67-20.89 4.54-32 4.54-61.81 0-113.52-44.05-125.41-102.4zM448 96h-64l-64-64v134.4c0 53.02 42.98 96 96 96s96-42.98 96-96V32l-64 64zm-72 80c-8.84 0-16-7.16-16-16s7.16-16 16-16 16 7.16 16 16-7.16 16-16 16zm80 0c-8.84 0-16-7.16-16-16s7.16-16 16-16 16 7.16 16 16-7.16 16-16 16z"/></svg>'], 'number' => '530+', 'text' => 'Animals Saved'], '1' => ['icon' => ['slug' => 'icon-spoon-knife', 'name' => 'spoon knife', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" id="icon-spoon-knife" viewBox="0 0 32 32">
<path d="M7 0c-3.314 0-6 3.134-6 7 0 3.31 1.969 6.083 4.616 6.812l-0.993 16.191c-0.067 1.098 0.778 1.996 1.878 1.996h1c1.1 0 1.945-0.898 1.878-1.996l-0.993-16.191c2.646-0.729 4.616-3.502 4.616-6.812 0-3.866-2.686-7-6-7zM27.167 0l-1.667 10h-1.25l-0.833-10h-0.833l-0.833 10h-1.25l-1.667-10h-0.833v13c0 0.552 0.448 1 1 1h2.604l-0.982 16.004c-0.067 1.098 0.778 1.996 1.878 1.996h1c1.1 0 1.945-0.898 1.878-1.996l-0.982-16.004h2.604c0.552 0 1-0.448 1-1v-13h-0.833z"/>
</svg>'], 'number' => '12', 'text' => 'Restaurants Visited'], '2' => ['icon' => ['slug' => 'icon-globe-africa.', 'name' => 'globe africa', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 496 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M248 8C111.03 8 0 119.03 0 256s111.03 248 248 248 248-111.03 248-248S384.97 8 248 8zm160 215.5v6.93c0 5.87-3.32 11.24-8.57 13.86l-15.39 7.7a15.485 15.485 0 0 1-15.53-.97l-18.21-12.14a15.52 15.52 0 0 0-13.5-1.81l-2.65.88c-9.7 3.23-13.66 14.79-7.99 23.3l13.24 19.86c2.87 4.31 7.71 6.9 12.89 6.9h8.21c8.56 0 15.5 6.94 15.5 15.5v11.34c0 3.35-1.09 6.62-3.1 9.3l-18.74 24.98c-1.42 1.9-2.39 4.1-2.83 6.43l-4.3 22.83c-.62 3.29-2.29 6.29-4.76 8.56a159.608 159.608 0 0 0-25 29.16l-13.03 19.55a27.756 27.756 0 0 1-23.09 12.36c-10.51 0-20.12-5.94-24.82-15.34a78.902 78.902 0 0 1-8.33-35.29V367.5c0-8.56-6.94-15.5-15.5-15.5h-25.88c-14.49 0-28.38-5.76-38.63-16a54.659 54.659 0 0 1-16-38.63v-14.06c0-17.19 8.1-33.38 21.85-43.7l27.58-20.69a54.663 54.663 0 0 1 32.78-10.93h.89c8.48 0 16.85 1.97 24.43 5.77l14.72 7.36c3.68 1.84 7.93 2.14 11.83.84l47.31-15.77c6.33-2.11 10.6-8.03 10.6-14.7 0-8.56-6.94-15.5-15.5-15.5h-10.09c-4.11 0-8.05-1.63-10.96-4.54l-6.92-6.92a15.493 15.493 0 0 0-10.96-4.54H199.5c-8.56 0-15.5-6.94-15.5-15.5v-4.4c0-7.11 4.84-13.31 11.74-15.04l14.45-3.61c3.74-.94 7-3.23 9.14-6.44l8.08-12.11c2.87-4.31 7.71-6.9 12.89-6.9h24.21c8.56 0 15.5-6.94 15.5-15.5v-21.7C359.23 71.63 422.86 131.02 441.93 208H423.5c-8.56 0-15.5 6.94-15.5 15.5z"/></svg>'], 'number' => '7', 'text' => 'Countries'], '3' => ['icon' => ['slug' => 'icon-music', 'name' => 'music', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" id="icon-music" viewBox="0 0 32 32">
<path d="M30 0h2v23c0 2.761-3.134 5-7 5s-7-2.239-7-5c0-2.761 3.134-5 7-5 1.959 0 3.729 0.575 5 1.501v-11.501l-16 3.556v15.444c0 2.761-3.134 5-7 5s-7-2.239-7-5c0-2.761 3.134-5 7-5 1.959 0 3.729 0.575 5 1.501v-19.501l18-4z"/>
</svg>'], 'number' => '1024', 'text' => 'Songs Performed'], '4' => ['icon' => ['slug' => 'icon-clock2', 'name' => 'clock2', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" id="icon-clock2" viewBox="0 0 32 32">
<path d="M16 0c-8.837 0-16 7.163-16 16s7.163 16 16 16 16-7.163 16-16-7.163-16-16-16zM20.586 23.414l-6.586-6.586v-8.828h4v7.172l5.414 5.414-2.829 2.829z"/>
</svg>'], 'number' => '2036', 'text' => 'Hours Spent'], '5' => ['icon' => ['slug' => 'icon-calendar-alt.', 'name' => 'calendar alt', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M0 464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V192H0v272zm320-196c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12v-40zm0 128c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12v-40zM192 268c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12v-40zm0 128c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12h-40c-6.6 0-12-5.4-12-12v-40zM64 268c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12H76c-6.6 0-12-5.4-12-12v-40zm0 128c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12H76c-6.6 0-12-5.4-12-12v-40zM400 64h-48V16c0-8.8-7.2-16-16-16h-32c-8.8 0-16 7.2-16 16v48H160V16c0-8.8-7.2-16-16-16h-32c-8.8 0-16 7.2-16 16v48H48C21.5 64 0 85.5 0 112v48h448v-48c0-26.5-21.5-48-48-48z"/></svg>'], 'number' => '72', 'text' => 'Hollidays Missed'], '6' => ['icon' => ['slug' => 'icon-bus-alt.', 'name' => 'bus alt', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M488 128h-8V80c0-44.8-99.2-80-224-80S32 35.2 32 80v48h-8c-13.25 0-24 10.74-24 24v80c0 13.25 10.75 24 24 24h8v160c0 17.67 14.33 32 32 32v32c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32v-32h192v32c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32v-32h6.4c16 0 25.6-12.8 25.6-25.6V256h8c13.25 0 24-10.75 24-24v-80c0-13.26-10.75-24-24-24zM160 72c0-4.42 3.58-8 8-8h176c4.42 0 8 3.58 8 8v16c0 4.42-3.58 8-8 8H168c-4.42 0-8-3.58-8-8V72zm-48 328c-17.67 0-32-14.33-32-32s14.33-32 32-32 32 14.33 32 32-14.33 32-32 32zm128-112H128c-17.67 0-32-14.33-32-32v-96c0-17.67 14.33-32 32-32h112v160zm32 0V128h112c17.67 0 32 14.33 32 32v96c0 17.67-14.33 32-32 32H272zm128 112c-17.67 0-32-14.33-32-32s14.33-32 32-32 32 14.33 32 32-14.33 32-32 32z"/></svg>'], 'number' => '34', 'text' => 'Buses Stoped'], '7' => ['icon' => ['slug' => 'icon-user-graduate.', 'name' => 'user graduate', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M319.4 320.6L224 416l-95.4-95.4C57.1 323.7 0 382.2 0 454.4v9.6c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-9.6c0-72.2-57.1-130.7-128.6-133.8zM13.6 79.8l6.4 1.5v58.4c-7 4.2-12 11.5-12 20.3 0 8.4 4.6 15.4 11.1 19.7L3.5 242c-1.7 6.9 2.1 14 7.6 14h41.8c5.5 0 9.3-7.1 7.6-14l-15.6-62.3C51.4 175.4 56 168.4 56 160c0-8.8-5-16.1-12-20.3V87.1l66 15.9c-8.6 17.2-14 36.4-14 57 0 70.7 57.3 128 128 128s128-57.3 128-128c0-20.6-5.3-39.8-14-57l96.3-23.2c18.2-4.4 18.2-27.1 0-31.5l-190.4-46c-13-3.1-26.7-3.1-39.7 0L13.6 48.2c-18.1 4.4-18.1 27.2 0 31.6z"/></svg>'], 'number' => '8', 'text' => 'Lectures Attended']]]], 'design' => ['layout' => ['items_per_row' => ['breakpoint_base' => 4, 'breakpoint_tablet_portrait' => 2, 'breakpoint_phone_landscape' => 1, 'breakpoint_tablet_landscape' => 3], 'width' => ['breakpoint_base' => ['number' => 900, 'unit' => 'px', 'style' => '900px']], 'stack_vertically_below' => null], 'items' => ['content_alignment' => ['breakpoint_base' => 'left', 'breakpoint_phone_landscape' => 'center'], 'padding' => ['padding' => ['breakpoint_base' => ['top' => ['number' => 40, 'unit' => 'px', 'style' => '40px'], 'left' => ['number' => 40, 'unit' => 'px', 'style' => '40px'], 'right' => ['number' => 40, 'unit' => 'px', 'style' => '40px'], 'bottom' => ['number' => 40, 'unit' => 'px', 'style' => '40px']], 'breakpoint_phone_portrait' => ['left' => ['number' => 15, 'unit' => 'px', 'style' => '15px'], 'right' => ['number' => 15, 'unit' => 'px', 'style' => '15px'], 'top' => ['number' => 30, 'unit' => 'px', 'style' => '30px'], 'bottom' => ['number' => 30, 'unit' => 'px', 'style' => '30px']]]], 'background' => '#FFFFFFFF', 'borders' => ['shadow' => ['breakpoint_base' => ['shadows' => ['0' => ['color' => '#DCDCDCFF', 'x' => '0', 'y' => '0', 'blur' => '20', 'spread' => '0', 'position' => 'outset']], 'style' => '0px 0px 20px 0px #DCDCDCFF']]], 'shadow' => null], 'icon' => ['size' => ['breakpoint_base' => ['number' => 25, 'unit' => 'px', 'style' => '25px']], 'style' => null], 'typography' => ['text' => ['color' => ['breakpoint_base' => '#5A5A5AFF']]], 'spacing' => ['between_items' => ['breakpoint_phone_portrait' => ['number' => 15, 'unit' => 'px', 'style' => '15px']]]]];
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
        "layout",
        "Layout",
        [c(
        "items_per_row",
        "Items Per Row",
        [],
        ['type' => 'number', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 100, 'max' => 1200, 'step' => 1]],
        true,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "items",
        "Items",
        [c(
        "content_alignment",
        "Content Alignment",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'left', 'text' => 'Left'], '1' => ['text' => 'Center', 'value' => 'center'], '2' => ['text' => 'Right', 'value' => 'right']]],
        true,
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
        ['type' => 'section'],
        false,
        false,
        [],
      ), getPresetSection(
      "EssentialElements\\AtomV1IconDesign",
      "Icon", 
      "icon", 
       ['type' => 'popout']
     ), c(
        "typography",
        "Typography",
        [getPresetSection(
      "EssentialElements\\typography_with_effects",
      "Number", 
      "number", 
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_effects",
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
      "Wrapper", 
      "wrapper", 
       ['type' => 'popout']
     ), c(
        "between_items",
        "Between Items",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['step' => 1, 'min' => 0, 'max' => 100], 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px']],
        true,
        false,
        [],
      ), c(
        "after_icon",
        "After Icon",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['step' => 1, 'min' => 0, 'max' => 50], 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px']],
        true,
        false,
        [],
      ), c(
        "after_number",
        "After Number",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['step' => 1, 'min' => 0, 'max' => 50], 'unitOptions' => ['types' => ['0' => 'px'], 'defaultType' => 'px']],
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
        "content",
        "Content",
        [c(
        "items",
        "Items",
        [c(
        "icon",
        "Icon",
        [],
        ['type' => 'icon', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "number",
        "Number",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
      ), c(
        "text",
        "Text",
        [],
        ['type' => 'text', 'layout' => 'vertical', 'textOptions' => ['multiline' => true]],
        false,
        false,
        [],
      )],
        ['type' => 'repeater', 'layout' => 'vertical', 'repeaterOptions' => ['titleTemplate' => '{number} {text}', 'defaultTitle' => '', 'buttonName' => '']],
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
        return ['proOnly' => true];
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
        return 700;
    }
    
    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'string', 'path' => 'content.content.items[].number'], '1' => ['accepts' => 'string', 'path' => 'content.content.items[].text']];
    }

    static function additionalClasses()
    {
        return false;
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

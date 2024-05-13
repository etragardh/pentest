<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\PostMeta", 
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class PostMeta extends \Breakdance\Elements\Element
{
    static function uiIcon() 
    {
        return '<svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" fill-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2" clip-rule="evenodd" viewBox="0 0 500 500">   <path fill="currentColor" d="M15 0C6.722 0 0 6.721 0 15s6.722 15 15 15h470c8.28 0 15.001-6.721 15.001-15s-6.722-15-15-15H15ZM240.101 126.635c-8.278 0-15 6.721-15 15s6.722 15 15 15h171.56c8.28 0 15-6.721 15-15s-6.72-15-15-15h-171.56ZM240.101 304.697c-8.278 0-15 6.721-15 15s6.722 15 15 15h171.56c8.28 0 15-6.721 15-15s-6.72-15-15-15h-171.56ZM133.338 81.635c33.114 0 60 26.885 60 60 0 33.114-26.886 60-60 60-33.115 0-60-26.886-60-60 0-33.115 26.885-60 60-60Zm0 29.167c17.018 0 30.833 13.815 30.833 30.833 0 17.017-13.815 30.833-30.833 30.833-17.017 0-30.833-13.816-30.833-30.833 0-17.018 13.816-30.833 30.833-30.833ZM450.628 435.794c-6.787-5.91-17.755-5.956-24.478-.102-6.722 5.854-6.67 15.405.117 21.315l44.213 38.5c6.787 5.91 17.755 5.956 24.478.102 6.723-5.854 6.67-15.404-.117-21.314l-44.213-38.501ZM355.139 435.794c-6.787-5.91-17.755-5.956-24.478-.102-6.722 5.854-6.67 15.405.117 21.315l44.213 38.5c6.787 5.91 17.755 5.956 24.478.102 6.723-5.854 6.67-15.404-.117-21.314l-44.213-38.501ZM259.65 435.794c-6.787-5.91-17.755-5.956-24.478-.102-6.722 5.854-6.67 15.405.117 21.315l44.213 38.5c6.787 5.91 17.755 5.956 24.478.102 6.723-5.854 6.67-15.404-.117-21.314l-44.213-38.501ZM168.127 435.794c-6.787-5.91-17.755-5.956-24.478-.102-6.722 5.854-6.67 15.405.117 21.315l44.213 38.5c6.787 5.91 17.755 5.956 24.478.102 6.723-5.854 6.67-15.404-.117-21.314l-44.213-38.501ZM76.603 435.794c-6.787-5.91-17.755-5.956-24.478-.102-6.722 5.854-6.67 15.405.117 21.315l44.213 38.5c6.787 5.91 17.755 5.956 24.478.102 6.723-5.854 6.67-15.404-.117-21.314l-44.213-38.501Z"/>   <path fill="currentColor" d="M423.397 435.794c6.787-5.91 17.755-5.956 24.478-.102 6.722 5.854 6.67 15.405-.117 21.315l-44.213 38.5c-6.787 5.91-17.755 5.956-24.478.102-6.723-5.854-6.67-15.404.117-21.314l44.213-38.501ZM327.908 435.794c6.787-5.91 17.755-5.956 24.478-.102 6.722 5.854 6.67 15.405-.117 21.315l-44.213 38.5c-6.787 5.91-17.755 5.956-24.478.102-6.723-5.854-6.67-15.404.117-21.314l44.213-38.501ZM232.419 435.794c6.787-5.91 17.755-5.956 24.478-.102 6.722 5.854 6.67 15.405-.117 21.315l-44.213 38.5c-6.787 5.91-17.755 5.956-24.478.102-6.723-5.854-6.67-15.404.117-21.314l44.213-38.501ZM140.896 435.794c6.787-5.91 17.755-5.956 24.478-.102 6.722 5.854 6.67 15.405-.117 21.315l-44.213 38.5c-6.787 5.91-17.755 5.956-24.478.102-6.723-5.854-6.67-15.404.117-21.314l44.213-38.501ZM49.372 435.794c6.787-5.91 17.755-5.956 24.478-.102 6.722 5.854 6.67 15.405-.117 21.315l-44.213 38.5c-6.787 5.91-17.755 5.956-24.478.102-6.723-5.854-6.67-15.404.117-21.314l44.213-38.501Z"/>   <path fill="currentColor" d="M0 485c0 8.278 6.721 15 15 15s15-6.722 15-15V15C30 6.72 23.279-.001 15-.001s-15 6.722-15 15V485ZM470 485c0 8.278 6.721 15 15 15s15-6.722 15-15V15c0-8.28-6.721-15.001-15-15.001s-15 6.722-15 15V485ZM193.338 259.697h-120v120h120v-120Zm-29.166 29.167v61.666h-61.667v-61.666h61.667Z"/> </svg>';
    }

    static function tag()
    {
        return 'ul';
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
        return 'Post Meta';
    }
    
    static function className()
    {
        return 'bde-post-meta';
    }

    static function category()
    {
        return 'dynamic';
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
        return ['content' => ['content' => ['meta_data' => ['0' => ['type' => 'author', 'link' => null, 'taxonomy' => null, 'icon' => ['slug' => 'icon-user-circle.', 'name' => 'user circle', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 496 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M248 8C111 8 0 119 0 256s111 248 248 248 248-111 248-248S385 8 248 8zm0 96c48.6 0 88 39.4 88 88s-39.4 88-88 88-88-39.4-88-88 39.4-88 88-88zm0 344c-58.7 0-111.3-26.6-146.5-68.2 18.8-35.4 55.6-59.8 98.5-59.8 2.4 0 4.8.4 7.1 1.1 13 4.2 26.6 6.9 40.9 6.9 14.3 0 28-2.7 40.9-6.9 2.3-.7 4.7-1.1 7.1-1.1 42.9 0 79.7 24.4 98.5 59.8C359.3 421.4 306.7 448 248 448z"/></svg>'], 'avatar' => false, 'before_text' => null], '1' => ['type' => 'date', 'icon' => ['slug' => 'icon-calendar-alt.', 'name' => 'calendar alt', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M148 288h-40c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12zm108-12v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm96 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm-96 96v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm-96 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm192 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm96-260v352c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V112c0-26.5 21.5-48 48-48h48V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h128V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h48c26.5 0 48 21.5 48 48zm-48 346V160H48v298c0 3.3 2.7 6 6 6h340c3.3 0 6-2.7 6-6z"/></svg>'], 'before_text' => null, 'link' => false, 'date_format' => null], '2' => ['type' => 'comments', 'before_text' => null, 'icon' => ['slug' => 'icon-comment-alt.', 'name' => 'comment alt', 'svgCode' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 7.1 5.8 12 12 12 2.4 0 4.9-.7 7.1-2.4L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64zm16 352c0 8.8-7.2 16-16 16H288l-12.8 9.6L208 428v-60H64c-8.8 0-16-7.2-16-16V64c0-8.8 7.2-16 16-16h384c8.8 0 16 7.2 16 16v288z"/></svg>']]], 'show_divider' => false]], 'design' => ['spacing' => ['between_items' => ['breakpoint_phone_portrait' => ['number' => 10, 'unit' => 'px', 'style' => '10px'], 'breakpoint_base' => ['number' => 25, 'unit' => 'px', 'style' => '25px']], 'after_graphics' => null], 'typography' => ['text' => ['typography' => ['custom' => ['customTypography' => ['fontWeight' => null, 'fontSize' => null]]]]], 'icon' => ['color' => '#AF8D8DFF', 'size' => ['breakpoint_base' => ['number' => 33, 'unit' => 'px', 'style' => '33px']], 'color_hover' => '#DA2E2EFF'], 'icons' => null, 'avatar' => null, 'container' => null, 'divider' => null, 'layout' => ['alignment' => null, 'stack_vertically_at' => 'breakpoint_phone_portrait'], 'graphics' => ['icons' => ['color' => '#551DD0FF']]]];
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
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline'],
        true,
        false,
        [],
      ), c(
        "alignment",
        "Alignment",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'space-between', 'text' => 'Between'], '1' => ['text' => 'Around', 'value' => 'space-around'], '2' => ['text' => 'Center', 'value' => 'center'], '3' => ['text' => 'Start', 'value' => 'flex-start'], '4' => ['text' => 'End', 'value' => 'flex-end']]],
        false,
        false,
        [],
      ), c(
        "stack_vertically_at",
        "Stack Vertically At",
        [],
        ['type' => 'breakpoint_dropdown', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "alignment_when_vertical",
        "Alignment When Vertical",
        [],
        ['type' => 'button_bar', 'layout' => 'vertical', 'items' => ['0' => ['value' => 'flex-start', 'text' => 'Left', 'icon' => 'AlignLeftIcon'], '1' => ['text' => 'Center', 'value' => 'center', 'icon' => 'AlignCenterIcon'], '2' => ['text' => 'Right', 'value' => 'flex-end', 'icon' => 'AlignRightIcon']], 'condition' => ['path' => 'design.layout.stack_vertically_at', 'operand' => 'is set', 'value' => '']],
        false,
        false,
        [],
      )],
        ['type' => 'section'],
        false,
        false,
        [],
      ), c(
        "graphics",
        "Graphics",
        [getPresetSection(
      "EssentialElements\\AtomV1IconDesign",
      "Icons", 
      "icons", 
       ['type' => 'popout']
     ), c(
        "author_avatar",
        "Author Avatar",
        [c(
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        false,
        false,
        [],
      ), c(
        "border_radius",
        "Border Radius",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1], 'unitOptions' => ['types' => ['0' => '%'], 'defaultType' => '%']],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'sectionOptions' => ['type' => 'popout']],
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
     ), getPresetSection(
      "EssentialElements\\typography",
      "Before Text", 
      "before_text", 
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_effects_with_hoverable_everything",
      "Links", 
      "links", 
       ['type' => 'popout']
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
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        true,
        false,
        [],
      ), c(
        "after_graphics",
        "After Graphics",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
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
        "meta_data",
        "Meta Data",
        [c(
        "type",
        "Type",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'author', 'text' => 'Author'], '1' => ['text' => 'Date', 'value' => 'date'], '2' => ['text' => 'Comments', 'value' => 'comments'], '3' => ['text' => 'Terms', 'value' => 'terms'], '4' => ['text' => 'Custom', 'value' => 'custom']]],
        false,
        false,
        [],
      ), c(
        "date_format",
        "Date Format",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'F jS, Y', 'text' => 'April 1st,2022 (F jS, Y)'], '1' => ['text' => '2022-04-01 (Y-m-d)', 'value' => 'Y-m-d'], '2' => ['text' => '04/01/2022 (m/d/Y)', 'value' => 'm/d/Y'], '3' => ['text' => '01/04/2022 (d/m/Y)', 'value' => 'd/m/Y'], '4' => ['text' => 'Custom', 'value' => 'custom']], 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'date']],
        false,
        false,
        [],
      ), c(
        "custom_date_format",
        "Custom Date Format",
        [],
        ['type' => 'text', 'layout' => 'inline', 'placeholder' => 'F jS,Y', 'condition' => ['path' => '%%CURRENTPATH%%.date_format', 'operand' => 'equals', 'value' => 'custom']],
        false,
        false,
        [],
      ), c(
        "taxonomy",
        "Taxonomy",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => ['0' => ['value' => 'categories', 'text' => 'Categories'], '1' => ['text' => 'Tags', 'value' => 'tags']], 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'terms'], 'dropdownOptions' => ['populate' => ['path' => '', 'text' => '', 'value' => '', 'fetchDataAction' => 'breakdance_get_taxonomies', 'fetchContextPath' => '', 'refetchPaths' => []]]],
        false,
        false,
        [],
      ), c(
        "count",
        "Count",
        [],
        ['type' => 'number', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'terms']],
        false,
        false,
        [],
      ), c(
        "no_comments",
        "No Comments",
        [],
        ['type' => 'text', 'layout' => 'inline', 'placeholder' => 'No Comments', 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'comments']],
        false,
        false,
        [],
      ), c(
        "one_comment",
        "One Comment",
        [],
        ['type' => 'text', 'layout' => 'inline', 'placeholder' => 'One Comment', 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'comments']],
        false,
        false,
        [],
      ), c(
        "comments",
        "Comments",
        [],
        ['type' => 'text', 'layout' => 'inline', 'placeholder' => '% Comments', 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'comments']],
        false,
        false,
        [],
      ), c(
        "custom",
        "Custom",
        [],
        ['type' => 'text', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'custom']],
        false,
        false,
        [],
      ), c(
        "before_text",
        "Before Text",
        [],
        ['type' => 'text', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "link",
        "Link",
        [],
        ['type' => 'toggle', 'layout' => 'inline'],
        false,
        false,
        [],
      ), c(
        "custom_url",
        "Custom URL",
        [],
        ['type' => 'url', 'layout' => 'vertical', 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'custom']],
        false,
        false,
        [],
      ), c(
        "avatar",
        "Avatar",
        [],
        ['type' => 'toggle', 'layout' => 'inline', 'condition' => ['path' => '%%CURRENTPATH%%.type', 'operand' => 'equals', 'value' => 'author']],
        false,
        false,
        [],
      ), c(
        "icon",
        "Icon",
        [],
        ['type' => 'icon', 'layout' => 'vertical', 'condition' => ['path' => '%%CURRENTPATH%%.avatar', 'operand' => 'is not set', 'value' => '']],
        false,
        false,
        [],
      )],
        ['type' => 'repeater', 'layout' => 'vertical', 'repeaterOptions' => ['titleTemplate' => '{type}', 'defaultTitle' => 'Unknown Meta', 'buttonName' => 'Add Meta']],
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
        return 0;
    }
    
    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'string', 'path' => 'content.content.meta_data[].custom'], '1' => ['accepts' => 'url', 'path' => 'content.content.meta_data[].custom_url']];
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
        return ['design.layout.stack_vertically_at'];
    }    
    
    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return ['content.content'];
    }
}

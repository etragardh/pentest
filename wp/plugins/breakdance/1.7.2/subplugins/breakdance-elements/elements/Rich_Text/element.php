<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\RichText", 
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class RichText extends \Breakdance\Elements\Element
{
    static function uiIcon() 
    {
        return '<svg viewBox="0 0 500 500" fill="currentColor">   <path d="M31 0C13.893 0 0 13.89 0 31s13.892 31 31 31h438c17.11 0 31.001-13.89 31.001-31s-13.891-31-31-31H31ZM15 141.5c-8.278 0-15 6.721-15 15s6.722 15 15 15h470c8.28 0 15.001-6.721 15.001-15s-6.722-15-15-15H15ZM15 251c-8.279 0-15 6.721-15 15s6.721 15 15 15h199c8.279 0 15-6.721 15-15s-6.721-15-15-15H15ZM286 250c-8.279 0-15 6.721-15 15s6.721 15 15 15h199c8.279 0 15-6.721 15-15s-6.721-15-15-15H286ZM15 360.5c-8.278 0-15 6.721-15 15s6.722 15 15 15h470c8.28 0 15.001-6.721 15.001-15s-6.722-15-15-15H15ZM15 470c-8.279 0-15 6.721-15 15s6.721 15 15 15h199c8.279 0 15-6.721 15-15s-6.721-15-15-15H15Z"/> </svg>';
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
        return 'Rich Text';
    }
    
    static function className()
    {
        return 'bde-rich-text';
    }

    static function category()
    {
        return 'basic';
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
        return ['content' => ['content' => ['text' => '<h2>Rich Text</h2><p>This is my rich text.</p><h3>I am a subheading</h3><p>This is <strong>more</strong> rich text.</p><ul><li><p>I am a list</p></li><li><p>Lists are cool</p></li></ul>']]];
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
        "size",
        "Size",
        [c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em', '2' => 'rem', '3' => '%'], 'defaultType' => 'px'], 'rangeOptions' => ['min' => 150, 'max' => 1200, 'step' => 1]],
        true,
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
      "EssentialElements\\typography_with_effects_and_align",
      "Default", 
      "default", 
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_effects",
      "H1", 
      "h1", 
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_effects",
      "H2", 
      "h2", 
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_effects",
      "H3", 
      "h3", 
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_effects",
      "H4", 
      "h4", 
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_effects",
      "Paragraph", 
      "paragraph", 
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_effects_with_hoverable_everything",
      "Link", 
      "link", 
       ['type' => 'popout']
     ), getPresetSection(
      "EssentialElements\\typography_with_effects",
      "List", 
      "list", 
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
        "h1",
        "H1",
        [c(
        "margin_block_start",
        "Margin Block Start",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em'], 'defaultType' => 'em'], 'rangeOptions' => ['min' => 0, 'max' => 2, 'step' => 0.05]],
        false,
        false,
        [],
      ), c(
        "margin_block_end",
        "Margin Block End",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em'], 'defaultType' => 'em'], 'rangeOptions' => ['min' => 0, 'max' => 2, 'step' => 0.05]],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "h2",
        "H2",
        [c(
        "margin_block_start",
        "Margin Block Start",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em'], 'defaultType' => 'em'], 'rangeOptions' => ['min' => 0, 'max' => 2, 'step' => 0.05]],
        false,
        false,
        [],
      ), c(
        "margin_block_end",
        "Margin Block End",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em'], 'defaultType' => 'em'], 'rangeOptions' => ['min' => 0, 'max' => 2, 'step' => 0.05]],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "h3",
        "H3",
        [c(
        "margin_block_start",
        "Margin Block Start",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em'], 'defaultType' => 'em'], 'rangeOptions' => ['min' => 0, 'max' => 2, 'step' => 0.05]],
        false,
        false,
        [],
      ), c(
        "margin_block_end",
        "Margin Block End",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em'], 'defaultType' => 'em'], 'rangeOptions' => ['min' => 0, 'max' => 2, 'step' => 0.05]],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "h4",
        "H4",
        [c(
        "margin_block_start",
        "Margin Block Start",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em'], 'defaultType' => 'em'], 'rangeOptions' => ['min' => 0, 'max' => 2, 'step' => 0.05]],
        false,
        false,
        [],
      ), c(
        "margin_block_end",
        "Margin Block End",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em'], 'defaultType' => 'em'], 'rangeOptions' => ['min' => 0, 'max' => 2, 'step' => 0.05]],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "paragraph",
        "Paragraph",
        [c(
        "margin_block_start",
        "Margin Block Start",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em'], 'defaultType' => 'em'], 'rangeOptions' => ['min' => 0, 'max' => 2, 'step' => 0.05]],
        false,
        false,
        [],
      ), c(
        "margin_block_end",
        "Margin Block End",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em'], 'defaultType' => 'em'], 'rangeOptions' => ['min' => 0, 'max' => 2, 'step' => 0.05]],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
      ), c(
        "list",
        "List",
        [c(
        "margin_block_start",
        "Margin Block Start",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em'], 'defaultType' => 'em'], 'rangeOptions' => ['min' => 0, 'max' => 2, 'step' => 0.05]],
        false,
        false,
        [],
      ), c(
        "margin_block_end",
        "Margin Block End",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em'], 'defaultType' => 'em'], 'rangeOptions' => ['min' => 0, 'max' => 2, 'step' => 0.05]],
        false,
        false,
        [],
      ), c(
        "padding_inline_start",
        "Padding Inline Start",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em'], 'defaultType' => 'em'], 'rangeOptions' => ['min' => 0, 'max' => 4, 'step' => 0.05]],
        false,
        false,
        [],
      ), c(
        "list_item_p",
        "List Item P",
        [c(
        "margin_block_start",
        "Margin Block Start",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em'], 'defaultType' => 'em'], 'rangeOptions' => ['min' => 0, 'max' => 2, 'step' => 0.05]],
        false,
        false,
        [],
      ), c(
        "margin_block_end",
        "Margin Block End",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'px', '1' => 'em'], 'defaultType' => 'em'], 'rangeOptions' => ['min' => 0, 'max' => 2, 'step' => 0.05]],
        false,
        false,
        [],
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
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
      )];
    }
    
    static function contentControls()
    {
        return [c(
        "content",
        "Content",
        [c(
        "text",
        "Text",
        [],
        ['type' => 'richtext', 'layout' => 'vertical'],
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
        return 60;
    }
    
    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'string', 'path' => 'content.content.text']];
    }

    static function additionalClasses()
    {
        return [['name' => 'breakdance-rich-text-styles', 'template' => 'no']];
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

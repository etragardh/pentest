<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\HtmlImg",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class HtmlImg extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'ImageIcon';
    }

    static function tag()
    {
        return 'img';
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
        return 'HTML IMG';
    }

    static function className()
    {
        return 'bde-html-img';
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
        return ['content' => ['content' => ['attributes' => ['src' => 'https://louisreingold.com/louis-reingold.jpg', 'alt' => 'louis reingold is the world\'s best human', 'width' => '200', 'height' => null]]]];
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
        return [];
    }

    static function contentControls()
    {
        return [c(
            "content",
            "Content",
            [c(
                "attributes",
                "Attributes",
                [c(
                    "src",
                    "src",
                    [],
                    ['type' => 'text', 'layout' => 'vertical', 'textOptions' => ['multiline' => true]],
                    false,
                    false,
                    [],
                ), c(
                    "srcset",
                    "srcset",
                    [],
                    ['type' => 'text', 'layout' => 'vertical', 'textOptions' => ['multiline' => true]],
                    false,
                    false,
                    [],
                ), c(
                    "sizes",
                    "sizes",
                    [],
                    ['type' => 'text', 'layout' => 'vertical', 'textOptions' => ['multiline' => true]],
                    false,
                    false,
                    [],
                ), c(
                    "alt",
                    "alt",
                    [],
                    ['type' => 'text', 'layout' => 'vertical'],
                    false,
                    false,
                    [],
                ), c(
                    "loading",
                    "loading",
                    [],
                    ['type' => 'dropdown', 'layout' => 'vertical', 'items' => ['0' => ['value' => 'eager', 'text' => 'eager'], '1' => ['value' => 'lazy', 'text' => 'lazy']]],
                    false,
                    false,
                    [],
                ), c(
                    "width",
                    "width",
                    [],
                    ['type' => 'text', 'layout' => 'vertical'],
                    false,
                    false,
                    [],
                ), c(
                    "height",
                    "height",
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
        return ["type" => "final",];
    }

    static function spacingBars()
    {
        return false;
    }

    static function attributes()
    {
        return [['name' => 'src', 'template' => '{{ content.content.attributes.src }}'], ['name' => 'srcset', 'template' => '{{ content.content.attributes.srcset }}'], ['name' => 'sizes', 'template' => '{{ content.content.attributes.sizes }}'], ['name' => 'alt', 'template' => '{{ content.content.attributes.alt }}'], ['name' => 'loading', 'template' => '{{ content.content.attributes.loading }}'], ['name' => 'width', 'template' => '{{ content.content.attributes.width }}'], ['name' => 'height', 'template' => '{{ content.content.attributes.height }}']];
    }

    static function experimental()
    {
        return false;
    }

    static function order()
    {
        return 1000;
    }

    static function dynamicPropertyPaths()
    {
        return ['0' => ['accepts' => 'image_url', 'path' => 'content.content.attributes.src'], '1' => ['accepts' => 'string', 'path' => 'content.content.attributes.srcset'], '2' => ['accepts' => 'string', 'path' => 'content.content.attributes.sizes'], '3' => ['accepts' => 'string', 'path' => 'content.content.attributes.alt'], '4' => ['accepts' => 'string', 'path' => 'content.content.attributes.width'], '5' => ['accepts' => 'string', 'path' => 'content.content.attributes.loading'], '6' => ['accepts' => 'string', 'path' => 'content.content.attributes.height']];
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

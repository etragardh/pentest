<?php

namespace EssentialElements;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;

class NinjaForms extends \Breakdance\Elements\Element
{

    static function experimental()
    {
        return true;
    }

    static function uiIcon() {
        return '<svg aria-hidden="true" focusable="false"   class="svg-inline--fa fa-envelope" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M448 64H64C28.65 64 0 92.65 0 128v256c0 35.35 28.65 64 64 64h384c35.35 0 64-28.65 64-64V128C512 92.65 483.3 64 448 64zM64 96h384c17.64 0 32 14.36 32 32v36.01l-195.2 146.4c-17 12.72-40.63 12.72-57.63 0L32 164V128C32 110.4 46.36 96 64 96zM480 384c0 17.64-14.36 32-32 32H64c-17.64 0-32-14.36-32-32V203.1L208 336c14.12 10.61 31.06 16.02 48 16.02S289.9 346.6 304 336L480 203.1V384z"></path></svg>';
    }

    static function tag()
    {
        return 'div';
    }

    static function name()
    {
        return 'Ninja Forms';
    }

    static function slug()
    {
        return get_class();
    }

    static function category()
    {
        return 'forms';
    }

    static function template()
    {
        return file_get_contents(__DIR__ . '/html.twig');
    }

    /**
     * @param mixed $propertiesData
     * @param mixed $parentPropertiesData
     * @param bool $isBuilder
     * @param int $repeaterItemNodeId
     * @return string
     */
    static function ssr($propertiesData, $parentPropertiesData = [], $isBuilder = false, $repeaterItemNodeId = null)
    {
        $formId = $propertiesData['content']['form']['form'] ?? null;
        return (string) renderNinjaForms($formId);
    }

    static function defaultCss()
    {
        return file_get_contents(__DIR__ . '/default.css');
    }

    static function cssTemplate()
    {
        $template = file_get_contents(__DIR__ . '/css.twig');

        return $template;
    }

    static function designControls()
    {
        return [
            \Breakdance\Elements\PresetSections\getPresetSection("EssentialElements\\typography", 'Labels', 'labels'),
            \Breakdance\Elements\PresetSections\getPresetSection("EssentialElements\\typography", 'Placeholders', 'placeholders'),
            \Breakdance\Elements\PresetSections\getPresetSection("EssentialElements\\combined_design", 'Fields', 'fields'),
            \Breakdance\Elements\PresetSections\getPresetSection("EssentialElements\\combined_design", 'Button', 'button'),
        ];
    }

    static function contentControls()
    {
        return [
            controlSection('form', 'form', [
                control('form', 'Form', [
                    'type' => 'dropdown',
                    'layout' => 'vertical',
                    'items' => getNinjaFormsOptions()
                ]),
                control('hide_title', 'Hide Title', [
                    'type' => 'toggle'
                ]),
                control('hide_required_message', 'Hide Required Message', [
                    'type' => 'toggle'
                ]),
            ]),
        ];
    }

    static function settingsControls()
    {
        return [];
    }

    static function defaultProperties()
    {
        return false;
    }

    static function nestingRule()
    {
        return ['type' => 'final'];
    }

    static function dependencies()
    {
        return \Breakdance\Forms\getThirdPartyDependencies();
    }

    static function actions()
    {
        return [
            'onPropertyChange' => [
                [
                    'script' => <<<JS
                        (function () {
                            const id = 'nf-front-end-js';
                            const originalScript = document.querySelector('#' + id);
                            
                            if (!originalScript) {
                                return;
                            }
                            
                            const newScript = document.createElement('script');
                            newScript.setAttribute('src', originalScript.getAttribute('src'));
                            newScript.setAttribute('id', id);

                            originalScript.remove();
                            document.body.appendChild(newScript);
                        } ());
                    JS,
                ],
            ],
        ];
    }

    static function settings()
    {
        return ['dependsOnGlobalScripts' => true];
    }
}

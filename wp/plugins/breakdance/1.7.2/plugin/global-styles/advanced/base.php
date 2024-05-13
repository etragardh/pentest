<?php

namespace Breakdance\GlobalSettings;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\repeaterControl;

/**
 * @return Control
 */
function ADVANCED_SECTION()
{
    $cssSection = repeaterControl("stylesheets", "CSS", [
        control('code', 'CSS Code', ['codeOptions' => ['language' => 'css'], 'type' => 'code', 'layout' => 'vertical']),
    ], ['repeaterOptions' => [
        'titleTemplate' => '{name}',
        'defaultTitle' => 'Stylesheet',
        'buttonName' => 'Add Stylesheet',
    ]]);

    $jsSection = repeaterControl("scripts", "Scripts", [
        control('code', 'JavaScript Code', ['codeOptions' => ['language' => 'javascript'], 'type' => 'code', 'layout' => 'vertical']),
    ], ['repeaterOptions' => [
        'titleTemplate' => '{name}',
        'defaultTitle' => 'Javascript',
        'buttonName' => 'Add Javascript',
    ]]);

    $advanced = controlSection('code', 'Code', [
        $cssSection,
        $jsSection,
    ]);

    return $advanced;
}

/**
 * @return string
 */
function ADVANCED_TEMPLATE()
{
    return (string) file_get_contents(dirname(__FILE__) . '/advanced.css.twig');
}

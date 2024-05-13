<?php

namespace Breakdance\Forms;

/**
 * Get a list of front-end assets to load in the builder/frontend for Ajax Forms.
 * @return ElementDependenciesAndConditions[]
 */
function getAjaxDependencies()
{
    return [
        [
            'styles' => [
                '%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/awesome-form@1/css/form.css',
            ],
        ],
        [
            'scripts' => [
                '%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/awesome-form@1/js/form.js',
            ],
            'inlineScripts' => [
                "breakdanceForm.init('%%SELECTOR%% .breakdance-form')",
            ],
            'builderCondition' => "return false;", // Don't run JavaScript the builder.
        ],
        [
            'scripts' => [
                '%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/awesome-form@1/js/form.js',
            ],
            'inlineStyles' => [
                '%%SELECTOR%% .breakdance-form-field--condition-hidden {display:block;opacity:0.5;}',
            ],
            'frontendCondition' => "return false;", // This is just for the builder
        ],
        [
            'inlineStyles' => [
                '%%SELECTOR%% .breakdance-form-button.breakdance-form-button__submit, %%SELECTOR%% .breakdance-form-field .breakdance-form-file-upload, %%SELECTOR%% .breakdance-form-field .breakdance-form-field__label {pointer-events: none}',
            ],
            'frontendCondition' => "return false;", // This is just for the builder
        ],
    ];
}

/**
 * Get a list of front-end assets to load in the builder/frontend for Third Party Forms.
 * E.g. Ninja Forms, Gravity Forms
 * @return ElementDependenciesAndConditions[]
 */
function getThirdPartyDependencies()
{
    return [
        [
            'styles' => [
                '%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/awesome-form@1/css/form.css',
            ],
        ],
    ];
}

/**
 * Builder: Get an empty template for when no form ID is selected.
 * @return false|string
 */
function getEmptyTemplate()
{
    return file_get_contents(__DIR__ . '/shared/empty.twig');
}

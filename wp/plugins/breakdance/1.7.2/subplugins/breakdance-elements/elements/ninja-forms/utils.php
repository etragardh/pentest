<?php

namespace EssentialElements;

/**
 * @param int $id
 * @return false|string
 */
function renderNinjaForms($id)
{
    if ($id) {
        return do_shortcode("[ninja_form id='{$id}']");
    }

    return \Breakdance\Forms\getEmptyTemplate();
}

/**
 * @psalm-suppress UndefinedDocblockClass
 * @psalm-suppress MixedMethodCall
 * @return array
 */
function getNinjaFormsOptions()
{
    if (!function_exists('Ninja_Forms')) {
        return [];
    }

    /** @var \NF_Abstracts_Model[] $forms */
    $forms = Ninja_Forms()->form()->get_forms();

    return array_map(function ($form) {
        return [
            'text' => $form->get_setting('title'),
            'value' => $form->get_id()
        ];
    }, $forms);
}

function loadNinjaFormsTemplates()
{
    /**
     * @psalm-suppress UndefinedClass
     */
    $path = \Ninja_Forms::$dir . 'includes/Templates';
    $files = glob($path . "/*.html");

    foreach( $files as $file ) {
        echo file_get_contents( $file );
    }

    do_action( 'ninja_forms_output_templates' );
}

function enqueueNinjaFormsAssets()
{
    $isInsideIframe = (bool) ($_GET['breakdance_open_document'] ?? false);

    if (!function_exists('Ninja_Forms') || !$isInsideIframe) {
        return;
    }

    add_action( 'wp_footer', '\EssentialElements\loadNinjaFormsTemplates', 9999 );

    /**
     * @psalm-suppress UndefinedClass
     */
    \NF_Display_Render::enqueue_scripts(0);
}

add_action('breakdance_register_template_types_and_conditions', '\EssentialElements\enqueueNinjaFormsAssets');
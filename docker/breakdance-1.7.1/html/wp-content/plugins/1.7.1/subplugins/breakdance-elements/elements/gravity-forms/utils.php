<?php

namespace EssentialElements;

/**
 * @param int $id
 * @return false|string
 */
function renderGravityForms($id)
{
    if ($id) {
        return do_shortcode("[gravityform id='{$id}' ajax='true']");
    }

    return \Breakdance\Forms\getEmptyTemplate();
}

/**
 * @return array
 */
function getGravityFormsOptions()
{
    if (!class_exists('GFAPI')) {
        return [];
    }

    /** @var array{title: string, id: int}[] $forms */
    $forms = \GFAPI::get_forms();

    return array_map(function ($form) {
        return [
            'text' => $form['title'],
            'value' => $form['id'],
        ];
    }, $forms);
}

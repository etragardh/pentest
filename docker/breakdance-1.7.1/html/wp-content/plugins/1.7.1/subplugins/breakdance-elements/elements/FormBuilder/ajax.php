<?php

namespace EssentialElements;

\Breakdance\Forms\registerForm([
    'slug' => 'custom',
    'args' => [
        'post_id' => ['filter' => FILTER_VALIDATE_INT],
        'form_id' => ['filter' => FILTER_VALIDATE_INT],
        'fields' => ['filter' => FILTER_DEFAULT, 'flags' => FILTER_REQUIRE_ARRAY]
    ],
    'optional_args' => ['fields'],
    'handler' =>
        /**
         * @param int $post_id
         * @param int $form_id
         * @param array|null $fields
         * @return \Breakdance\Forms\FormError|\Breakdance\Forms\FormError[]|\Breakdance\Forms\FormSuccess
         */
        function ($post_id, $form_id, $fields) {
            return \Breakdance\Forms\handleSubmission(
                $post_id,
                $form_id,
                $fields ?? []
            );
        }
]);

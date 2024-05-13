<?php

namespace EssentialElements;

\Breakdance\Forms\registerForm([
    'slug' => 'forgot_password',
    'args' => [
        'fields' => ['filter' => FILTER_DEFAULT, 'flags' => FILTER_REQUIRE_ARRAY]
    ],
    'handler' =>
        /**
         * @param array $fields
         * @return array
         */
        function ($fields) {
            $submit = retrieve_password($fields['user_login'] ?? null);

            if (!is_wp_error($submit)) {
                return ['type' => 'success'];
            } else {
                return [
                    'type' => 'error',
                    'message' => $submit->get_error_message()
                ];
            }
        }
]);

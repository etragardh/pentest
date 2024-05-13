<?php

namespace EssentialElements;

\Breakdance\Forms\registerForm([
    'slug' => 'login',
    'args' => [
        'fields' => ['filter' => FILTER_DEFAULT, 'flags' => FILTER_REQUIRE_ARRAY]
    ],
    'handler' => 
        /**
         * @param array $credentials
         * @return string[]
         */
        function ($credentials) {
            if (empty($credentials['user_login']) || empty($credentials['user_password'])) {
                return [
                    'type' => 'error',
                    'message' => "Username or password can't be empty."
                ];
            }

            $user = wp_signon($credentials);

            if (!is_wp_error($user)) {
                wp_set_current_user($user->ID);
                wp_set_auth_cookie($user->ID);

                return ['type' => 'success'];
            } else {
                return [
                    'type' => 'error',
                    'message' => $user->get_error_message()
                ];
            }
        }
]);

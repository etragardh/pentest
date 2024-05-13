<?php

namespace EssentialElements;

\Breakdance\Forms\registerForm([
    'slug' => 'register',
    'args' => [
        'fields' => ['filter' => FILTER_DEFAULT, 'flags' => FILTER_REQUIRE_ARRAY]
    ],
    'handler' =>
        /**
         * @param array $fields
         * @return array|string[]
         */
        function ($fields) {
            if (empty($fields['user_login']) || empty($fields['user_pass']) || empty($fields['user_email'])) {
                return [
                    'type' => 'error',
                    'message' => "Username, password or email can't be empty."
                ];
            }

            if (!get_option('users_can_register')) {
                return [
                    'type' => 'error',
                    'message' => 'The site administrator has not enabled user registration.'
                ];
            }

            $errors = new \WP_Error();

            // Fires when submitting registration form data, before the user is created.
            do_action('register_post', $fields['user_login'], $fields['user_email'], $errors);

            // Filters the errors encountered when a new user is being registered.
            $errors = apply_filters('registration_errors', $errors, $fields['user_login'], $fields['user_email']);

            if ($errors->has_errors()) {
                return [
                    'type' => 'error',
                    'message' => $errors->get_error_message()
                ];
            }

            $userId = wp_create_user(
                $fields['user_login'],
                $fields['user_pass'],
                $fields['user_email']
            );

            if (is_wp_error($userId)) {
                return [
                    'type' => 'error',
                    'message' => $userId->get_error_message()
                ];
            }

            if ($fields['auto_login_user']) {
                $user = wp_set_current_user($userId);
                wp_set_auth_cookie($userId);
            }

            return ['type' => 'success'];
        }
]);

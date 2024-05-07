<?php

namespace Breakdance\Themeless\Rules;

add_action(
    'breakdance_register_template_types_and_conditions',
    '\Breakdance\Themeless\Rules\registerConditionsUserRules'
);

// User conditions.
// No templatePreviewableItems because it's stateful logic (no data)
function registerConditionsUserRules()
{
    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'templating'],
            'availableForType' => ['ALL'],
            'slug' => 'user-logged-in-status',
            'label' => 'User Logged In Status',
            'category' => 'User',
            'operands' => [OPERAND_IS],
            'valueInputType' => 'dropdown',
            'values' => function () {
                return [
                    [
                        'label' => 'Status',
                        'items' => [
                            ['text' => 'logged in', 'value' => 'logged in'],
                            ['text' => 'logged out', 'value' => 'logged out']
                        ]
                    ]
                ];
            },
            'callback' => function (string $operand, string $value): bool {
                if ($value === 'logged in') {
                    return is_user_logged_in();
                } elseif ($value === 'logged out') {
                    return ! is_user_logged_in();
                } else {
                    return false;
                }
            },
            'templatePreviewableItems' => false,
        ]
    );


    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'templating'],
            'availableForType' => ['ALL'],
            'slug' => 'user-role',
            'label' => 'User Role',
            'category' => 'User',
            'operands' => [OPERAND_ONE_OF, OPERAND_NONE_OF],
            'values' => function () {
                return getUserRolesForDropdown();
            },
            'callback' =>
            /**
             * @param string[] $value
             * @param string $operand
             */
                function ($operand, $value): bool {
                    $roles = getCurrentUserRoles();
                    $current_user_has_matching_role
                           = count(array_intersect($roles, $value)) > 0;

                    if ($operand === OPERAND_ONE_OF) {
                        return $current_user_has_matching_role;
                    } elseif ($operand === OPERAND_NONE_OF) {
                        return ! $current_user_has_matching_role;
                    } else {
                        return false;
                    }
                },
            'templatePreviewableItems' => false,
        ]
    );

    /*
     * User registration date
     * // todo templates: this field being a date picker would be 10x better.
     */

    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'templating'],
            'availableForType' => ['ALL'],
            'slug' => 'user-registration-date',
            'label' => 'User Registration Date',
            'category' => 'User',
            'operands' => [OPERAND_AFTER, OPERAND_BEFORE],
            'valueInputType' => 'datepicker',
            'values' => function () {
                return false;
            },
            'callback' =>
            /**
             * @param string $operand
             * @param string $value
             */
                function ($operand, $value): bool {
                    if (\is_user_logged_in()) {
                        $user           = wp_get_current_user();
                        $userRegistered = (new \DateTime((string) $user->data->user_registered, wp_timezone()));
                        $dateAsDateTime = (new \DateTime((string) $value, wp_timezone()));

                        if ($operand === OPERAND_BEFORE) {
                            return $dateAsDateTime > $userRegistered;
                        } elseif ($operand === OPERAND_AFTER) {
                            return $dateAsDateTime < $userRegistered;
                        }
                    }

                    return false;
                },
            'templatePreviewableItems' => false,
        ]
    );
}

/**
 * @return ItemGroup[]
 */
function getUserRolesForDropdown()
{
    if (! function_exists('\get_editable_roles')) {
        /**
         * @psalm-suppress UndefinedConstant
         * @psalm-suppress UnresolvableInclude
         */
        require_once ABSPATH . 'wp-admin/includes/user.php';
    }

    /** @var string[] */
    $roles = array_keys(get_editable_roles());

    return [
        [
            'label' => 'Roles',
            'items' => array_map(function ($role) {
                return [
                    'text' => $role,
                    'value' => $role,
                ];
            }, $roles),
        ],
    ];
}

/**
 * @return string[]
 */
function getCurrentUserRoles()
{
    if (\is_user_logged_in()) {
        $user = wp_get_current_user();

        /**
         * @var string[]
         */
        $roles = $user->roles;

        return $roles;
    } else {
        return [];
    }
}

<?php

namespace Breakdance\Permissions;

/**
 * @param string $role
 * @return bool
 */
function isSuperAdminRole($role)
{
    return in_array($role, BREAKDANCE_SUPER_ADMIN_ROLES);
}

/**
 * @param int|\WP_User $userId
 * @return \WP_User|false
 */
function _getUser($userId = 0)
{
    if ($userId === 0) {
        $user = wp_get_current_user();
    } else if ($userId instanceof \WP_User) {
        $user = $userId;
    } else {
        $user = get_userdata((int) $userId);
    }

    if (!$user) {
        return false;
    }

    return $user->ID == 0 ? false : $user;
}

/**
 * List all WordPress roles available
 * Note: not using "editable_roles" because it only lists the roles *editable by the current user*
 * So any non-admin user won't get the correct list
 *
 * https://wordpress.stackexchange.com/a/1667/108358
 *
 * array<role slug, role name>
 * @return array<string, string>
 */
function _getRoles()
{
    global $wp_roles;

    if ( ! isset( $wp_roles ) )
        /**
         * @psalm-suppress UndefinedClass
         * @psalm-suppress MixedAssignment
         */
        $wp_roles = new WP_Roles();

    /**
     * @var array<string, string> $roles
     *
     * @psalm-suppress MixedMethodCall
     * @psalm-suppress UndefinedClass
     */
    $roles = $wp_roles->get_names();

    return $roles;
}

/**
 * @param string $role
 * @return string
 */
function _getRoleName($role)
{
    global $wp_roles;
    /**
     * @var array{name: string, capabilities: array}
     * @psalm-suppress MixedArrayAccess
     * @psalm-suppress MixedPropertyFetch
     */
    $foundRole = $wp_roles->roles[ $role ];

    return $foundRole['name'];
}

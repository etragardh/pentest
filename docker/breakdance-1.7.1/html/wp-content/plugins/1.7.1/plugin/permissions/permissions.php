<?php

namespace Breakdance\Permissions;

define('BREAKDANCE_DEFAULT_ROLES_PERMISSIONS', [
    'administrator' => 'full'
]);

// Logged-out users or subscribers.
define('BREAKDANCE_DEFAULT_PERMISSION', 'none');

define('BREAKDANCE_SUPER_ADMIN_ROLES', [
    'administrator'
]);

// Disallow anyone without Full Access to manage these post types
define('BREAKDANCE_RESTRICT_POST_TYPES', [
    'breakdance_global_block',
    'breakdance_template',
    'breakdance_header',
    'breakdance_footer',
    'breakdance_popup'
]);

/**
 * List all permissions available
 * @return Permission[]
 */
function getPermissions()
{
    $permissions = [
        [
            'name' => 'Full Interface',
            'slug' => 'full',
            'level' => 30
        ],
        [
            'name' => 'Edit Content Interface Only',
            'slug' => 'edit',
            'level' => 20
        ],
        [
            'name' => 'No Access',
            'slug' => 'none',
            'level' => 10
        ]
    ];

    /**
     * @var Permission[]
     */
    return apply_filters('breakdance_permissions', $permissions);
}

/**
 * Get permission data given a $slug. E.g., full/none/edit
 * @param string|null $slug
 * @return Permission|null
 */
function getPermissionData($slug)
{
    if (!$slug) {
        return null;
    }

    $permissions = getPermissions();
    $key = array_search($slug, array_column($permissions, 'slug'));
    return $permissions[$key] ?? null;
}

/**
 * @return Permission|null
 */
function getHighestPermission()
{
    $permissions = getPermissions();
    $maxLevel = max(array_column($permissions, 'level'));

    $key = array_search($maxLevel, array_column($permissions, 'level'));
    return $permissions[$key] ?? null;
}

/**
 * Get roles and their permissions
 * @return array<string, string>
 * @throws \Exception
 */
function getRolesPermissions()
{
    /** @var array<string, string> */
    $storedPermissions = \Breakdance\Data\get_global_option('settings_permissions');
    $initialPermissions = $storedPermissions ?: BREAKDANCE_DEFAULT_ROLES_PERMISSIONS;
    $highestPermission = getHighestPermission();

    /** @var string[] */
    $roles = array_keys(_getRoles());

    $permissions = array_map(function ($role) use ($initialPermissions, $highestPermission) {
        // Admins always have "full access".
        if (isSuperAdminRole($role)) {
            return $highestPermission['slug'] ?? BREAKDANCE_DEFAULT_PERMISSION;
        }

        if (array_key_exists($role, $initialPermissions)) {
            return $initialPermissions[$role];
        }

        // Fallback to no permission.
        return BREAKDANCE_DEFAULT_PERMISSION;
    }, $roles);

    return array_combine($roles, $permissions);
}

/**
 * Set permissions for all roles
 * @param array<string, string> $permissions
 */
function setRolesPermissions($permissions)
{
    if (!$permissions) {
        return;
    }

    \Breakdance\Data\set_global_option(
        'settings_permissions',
        $permissions
    );
}

/**
 * Get permission for the given role
 * @param string $role
 * @return Permission|null
 * @throws \Exception
 */
function getRolePermission($role)
{
    $roles = getRolesPermissions();

    return getPermissionData($roles[$role] ?? null);
}

/**
 * Give permission to a user
 * @param string $permission
 * @param int|\WP_User $userId
 */
function givePermission($permission, $userId = 0)
{
    if (!$permission) {
        return;
    }

    $user = _getUser($userId);

    if ($user) {
        update_user_meta($user->ID, 'breakdance_permission', $permission);
    }
}

/**
 * Revoke a permission for the given user
 * @param int|\WP_User $userId
 */
function revokePermission($userId = 0)
{
    $user = _getUser($userId);

    if ($user) {
        delete_user_meta($user->ID, 'breakdance_permission');
    }
}

/**
 * Get the permission for the given user
 * @param int|\WP_User $userId
 * @return Permission|null
 * @throws \Exception
 */
function getUserPermission($userId = 0)
{
    $user = _getUser($userId);

    // Logged out users have no builder access
    if (!$user) {
        return getPermissionData('none');
    }

    // Multisite Super Admins always have access even if they are not a user in the site.
    if (is_super_admin($user->ID)) {
        return getPermissionData('full');
    }

    /** @var string $meta */
    $meta = get_user_meta($user->ID, 'breakdance_permission', true);
    $userPermission = getPermissionData($meta);

    /** @var string $role */
    $role = reset($user->roles);
    $rolePermission = getRolePermission($role);

    // User permission takes priority over role permission
    if ($userPermission) {
        return array_merge($userPermission, [
            'overwritten' => true
        ]);
    }

    return $rolePermission;
}

/**
 * Check if a user has a permission
 * @param string $permission
 * @param int|\WP_User $userId
 * @return bool
 * @throws \Exception
 */
function hasPermission($permission, $userId = 0)
{
    $userPermission = getUserPermission($userId);

    if (!$userPermission) {
        return false;
    }

    return $userPermission['slug'] == $permission;
}

/**
 * Check if a user has any of the permissions specified
 * @param string[] $permissions
 * @param int|\WP_User $userId
 * @return bool
 * @throws \Exception
 */
function hasAnyPermission($permissions, $userId = 0)
{
    $userPermission = getUserPermission($userId);

    if (!$userPermission) {
        return false;
    }

    return in_array($userPermission['slug'], $permissions);
}

/**
 * @param string $minimumPermission
 * @param int|\WP_User $userId
 * @return bool
 * @throws \Exception
 */
function hasMinimumPermission($minimumPermission, $userId = 0)
{
    $userPermission = getUserPermission($userId);
    $minimumPermission = getPermissionData($minimumPermission);

    if (!$userPermission) {
        return false;
    }

    if (!$minimumPermission) {
        throw new \Exception('Invalid permission passed as argument.');
    }

    return $userPermission['level'] >= $minimumPermission['level'];
}

/**
 * Determine whether the current logged-in user can edit the post type.
 * @param string|false $postType
 * @param int|\WP_User $userId
 * @return bool
 * @throws \Exception
 */
function isPostTypeAllowed($postType, $userId = 0)
{
    if (in_array($postType, BREAKDANCE_RESTRICT_POST_TYPES)) {
        return hasPermission('full');
    }

    return true;
}

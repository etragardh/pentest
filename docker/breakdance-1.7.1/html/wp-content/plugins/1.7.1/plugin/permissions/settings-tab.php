<?php

namespace Breakdance\Permissions\SettingsTab;

use function Breakdance\Permissions\isSuperAdminRole;
use function Breakdance\Permissions\_getRoleName;
use function Breakdance\Subscription\isFreeMode;
use function Breakdance\Util\is_post_request;

add_action('breakdance_register_admin_settings_page_register_tabs', '\Breakdance\Permissions\SettingsTab\register');

function register()
{
    \Breakdance\Admin\SettingsPage\addTab('User Access', 'permissions', '\Breakdance\Permissions\SettingsTab\tab', 500);
}

function onSubmit()
{
    if (isFreeMode()) {
        return;
    }

    if (array_key_exists('permissions', $_POST)) {
        /**
         * @var array<string, string>
         * @psalm-suppress MixedAssignment
         */
        $selectedPermissions = array_map('esc_attr', (array) $_POST['permissions']);

        \Breakdance\Permissions\setRolesPermissions($selectedPermissions);
    }

    if (array_key_exists('impersonate', $_POST)) {
        setcookie('breakdance_impersonate', '1', strtotime('+1 hour'));
    }
}

/**
 *
 * @param Permission[] $permissions
 * @param string $role
 * @param string $currentValue
 * @return string
 */
function permissionsDropdown($permissions, $role, $currentValue = '')
{
    $disabled = isSuperAdminRole($role) || isFreeMode() ? 'disabled' : '';

    $options = implode(array_map(function ($permission) use ($currentValue) {
        $selected = $currentValue === $permission['slug'] ? 'selected' : '';
        return "<option value='{$permission['slug']}' {$selected}>{$permission['name']}</option>";
    }, $permissions));

    /** @var string $name */
    $name = _getRoleName($role);

    return <<<HTML
        <tr>
            <th class='valign-th-middle' scope="row">{$name}</th>
            <td>
                <select name="permissions[{$role}]" {$disabled} >
                    {$options}
                </select>
            </td>
        </tr>
    HTML;
}

function tab()
{
    $nonce_action = 'breakdance_admin_permissions_tab';
    if (is_post_request() && check_admin_referer($nonce_action)) {
        onSubmit();
    }

    // load data for use in form
    $roles = \Breakdance\Permissions\getRolesPermissions();
    $permissions = \Breakdance\Permissions\getPermissions();
?>

    <h2>User Access</h2>

    <form action="" method="post">
        <?php wp_nonce_field($nonce_action); ?>

        <p>Set Breakdance access and user interface mode by user role. Set on a per-user basis from the Edit User screen.</p>

        <div class="notice notice-warning inline padded-notice">
            <p>Do not grant <strong>Edit Content Interface Only</strong> access to untrusted users. This access simplifies the user interface, but does not <a href='https://breakdance.com/documentation/other/security/' target='_blank'>restrict privileges</a>.</p>
        </div>

        <table class="form-table" role="presentation">
            <tbody>
                <?php
                foreach ($roles as $role => $permission) {
                    echo permissionsDropdown($permissions, $role, $permission);
                }

                $isDisabled = isFreeMode() ? "disabled" : '';
                ?>

                <tr>
                    <td colspan="2">
                        <label>
                            <input type="checkbox" name="impersonate" <?= $isDisabled  ?>>
                            Impersonate a user with <strong>Edit Content</strong> access.
                        </label>
                        <p class="description">The next time you open the Builder, you will experience it with the <strong>Edit Content</strong> access.</p>
                    </td>
                </tr>
            </tbody>
        </table>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes" <?= $isDisabled ?>>
        </p>
    </form>
<?php
}

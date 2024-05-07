<?php

namespace Breakdance\Permissions;

add_action( 'show_user_profile', '\Breakdance\Permissions\customField' );
add_action( 'edit_user_profile', '\Breakdance\Permissions\customField' );
add_action( 'user_new_form', '\Breakdance\Permissions\customField' );

add_action( 'personal_options_update', '\Breakdance\Permissions\saveCustomField' );
add_action( 'edit_user_profile_update', '\Breakdance\Permissions\saveCustomField' );
add_action( 'user_register', '\Breakdance\Permissions\saveCustomField' );

/**
 * @param Permission|null $userPermission
 * @param Permission $permission
 * @return bool
 */
function isSelected($userPermission, $permission)
{
    if (!$userPermission) {
        return false;
    }

    $isOverwritten = $userPermission['overwritten'] ?? false;

    return $userPermission['slug'] === $permission['slug'] && $isOverwritten;
}

/**
 * @param \WP_User|string $user
 * @throws \Exception
 */
function customField($user) {
    $permissions = getPermissions();
    $userPermission = null;

    if (!current_user_can('edit_users')) {
        return;
    }

    if ($user !== 'add-new-user' && $user instanceof \WP_User) {
        $userPermission = getUserPermission($user->ID);
        /** @var string $role */
        $role = reset($user->roles);
    }
?>
    <h3>Breakdance</h3>

    <table class="form-table">
        <tr>
            <th><label for="breakdance-builder-access">Builder Access</label></th>
            <td>
                <select name="breakdance_permission" id="breakdance-builder-access">
                    <option value="">Default</option>

                    <?php foreach($permissions as $permission) { ?>
                        <?php $selected = isSelected($userPermission, $permission) ? 'selected' : ''; ?>
                        <option value="<?php echo $permission['slug']; ?>" <?php echo $selected; ?>><?php echo $permission['name']; ?></option>
                    <?php } ?>
                </select>

                <div class="breakdance-builder-access__message">
                    <div class="notice inline notice-warning">
                        <p>It's not possible to restrict access for administrators, please give the user another role.</p>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <style>
        .breakdance-builder-access__message .notice {
            display: inline-block;
        }
    </style>

    <script>
        (function ($) {
            const $role = $('#role');
            const $builderAccess = $('#breakdance-builder-access');
            const $message = $('.breakdance-builder-access__message');
            const defaultValue = $builderAccess.val();

            function toggleBuilderAccess() {
                const selectedRole = $role.val();

                if (selectedRole === 'administrator') {
                    $builderAccess.prop('disabled', true);
                    $builderAccess.val('');
                    $message.show();
                    return;
                }

                $builderAccess.prop('disabled', false);
                $builderAccess.val(defaultValue);
                $message.hide();
            }

            toggleBuilderAccess();
            $role.on('change', toggleBuilderAccess);
        }(jQuery));
    </script>
<?php }

/**
 * @param int $userId
 * @return false|null
 */
function saveCustomField($userId)
{
    if (!current_user_can( 'edit_users')) {
        return false;
    }

    $permission = sanitize_text_field((string) ($_POST['breakdance_permission'] ?? ''));

    // Remove permission if set to default.
    if (!$permission) {
        revokePermission($userId);
    } else {
        givePermission($permission, $userId);
    }
}

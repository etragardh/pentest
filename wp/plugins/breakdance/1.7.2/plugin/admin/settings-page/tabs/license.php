<?php

namespace Breakdance\Admin\SettingsPage\LicenseTab;

use Breakdance\Licensing\LicenseKeyManager;
use function Breakdance\Licensing\get_option_receive_beta_updates;
use function Breakdance\Licensing\save_option_receive_beta_updates;
use function Breakdance\Util\is_post_request;

add_action('breakdance_register_admin_settings_page_register_tabs', '\Breakdance\Admin\SettingsPage\LicenseTab\register');


function admin_notice(string $message, string $type = 'success'): void
{
    ?>
    <div class="notice notice-<?php _e($type); ?> is-dismissible">
        <p><?php _e($message) ?></p>
    </div>
    <?php
}

function register()
{
    \Breakdance\Admin\SettingsPage\addTab('License', 'license', '\Breakdance\Admin\SettingsPage\LicenseTab\tab', 1);
}

function tab()
{
    /** @var LicenseKeyManager $license_key_manager */
    $license_key_manager = LicenseKeyManager::getInstance();

    $license_key_manager->refetchStoredLicenseKeyValidityInfo();

    $nonce_action = 'breakdance_admin_license_tab';
    if (is_post_request() && check_admin_referer($nonce_action)) {
        if (isset($_POST['submit'])) {
            /**
             * @var mixed|null|false $key
             */
            $key = filter_input(INPUT_POST, 'key', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if ($key !== null && $key !== false) {
                $trimmed_key = trim((string) $key);

                $license_key_manager->changeLicenseKey($trimmed_key === '' ? null : $trimmed_key);
            }

            /** @var boolean|null $receive_beta_updates */
            $receive_beta_updates = filter_input(INPUT_POST, 'receive_beta_updates', FILTER_VALIDATE_BOOLEAN);
            if ($receive_beta_updates !== null) {
                save_option_receive_beta_updates($receive_beta_updates);
            }
        } elseif (isset($_POST['activate_license'])) {
            if (false !== $license_key_manager->activateLicense()) {
                // TODO custom notices don't work within settings tabs
                add_action('admin_notices', function () {
                    admin_notice('License was activated');
                });
            } else {
                // TODO custom notices don't work within settings tabs
                add_action('admin_notices', function () {
                    admin_notice('Failed to activate license', 'error');
                });
            }
        } elseif (isset($_POST['deactivate_license'])) {
            // TODO custom notices don't work within settings tabs
            if (false !== $license_key_manager->deactivateLicense()) {
                add_action('admin_notices', function () {
                    admin_notice('License was deactivated');
                });
            } else {
                // TODO custom notices don't work within settings tabs
                add_action('admin_notices', function () {
                    admin_notice('Failed to deactivate license', 'error');
                });
            }
        }
    }

    // load data for use in form
    $stored_key = $license_key_manager->getStoredLicenseKey();
    $license_info = $license_key_manager->getHumanReadableLicenseKeyInformation();
    $show_activate_license_btn = $license_key_manager->canLicenseBeActivated();
    $show_deactivate_license_btn = $license_key_manager->canLicenseBeDeactivated();
    ?>

    <h2>License</h2>
    <form action="" method="post">
        <?php wp_nonce_field($nonce_action); ?>
        <table class="form-table" role="presentation">
            <tbody>
            <tr>
                <th scope="row">
                    <label for="key">
                        License Key
                    </label>
                </th>
                <td>
                    <input type='password' id='key' name='key' style='width: 300px;'
                           value='<?= htmlspecialchars((string) $stored_key); ?>'/>

                    <?php if ($show_activate_license_btn): ?>
                        <input type="submit" class="button-secondary" name="activate_license"
                               value="Activate License">
                    <?php endif; ?>

                    <?php if ($show_deactivate_license_btn): ?>
                        <input type="submit" class="button-secondary" name="deactivate_license"
                               value="Deactivate License">
                    <?php endif; ?>

                    <p class="description">
                    <dl>
                        <dt><b>Product</b></dt>
                        <dd><?= $license_info['product'] ?></dd>

                        <dt><b>License Key Validity</b></dt>
                        <dd><?= $license_info['is_valid'] ?></dd>

                        <dt><b>Activation Status</b></dt>
                        <dd><?= $license_info['activation_status'] ?></dd>

                        <dt><b>Expires On</b></dt>
                        <dd><?= $license_info['expires'] ?><?= $license_info['expires_in_human_readable']; ?></dd>

                        <!-- <dt><b>Has License Been Paid For</b></dt>
                        <dd><?= $license_info['has_license_been_paid_for'] ?></dd> -->
                    </dl>
                    </p>

                    <p class="description">
                        Visit <a href='https://breakdance.com/' target="_blank">breakdance.com</a> to purchase a license key. Already purchased? Find your license key in the <a href='https://breakdance.com/portal' target="_blank">customer portal</a>.
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="key">
                        Beta Versions
                    </label>
                </th>
                <td>
                    <fieldset>
                        <label for="receive_beta_updates">
                            <input type="hidden" name="receive_beta_updates" value="false">
                            <input type="checkbox" name="receive_beta_updates" id="receive_beta_updates" <?php echo get_option_receive_beta_updates() ? ' checked' : ''; ?> />
                            <span>Receive beta version updates</span>
                        </label>
                    </fieldset>

                    <p class="description">Checking this checkbox will opt you in to receive beta version updates. You can opt out at any time.</p>
                </td>
            </tr>
            </tbody>
        </table>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
        </p>
    </form>

    <?php
}
?>

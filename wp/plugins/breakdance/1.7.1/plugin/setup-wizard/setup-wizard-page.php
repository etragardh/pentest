<?php

namespace Breakdance\SetupWizard;

use Breakdance\Licensing\LicenseKeyManager;
use function Breakdance\Data\set_global_option;
use function Breakdance\Util\is_post_request;

add_action('breakdance_admin_menu', function () {
  add_submenu_page('', 'Setup Wizard', 'Setup Wizard', 'manage_options', 'breakdance_setup_wizard', "Breakdance\SetupWizard\display_setup_wizard");
});


function display_setup_wizard()
{
    $nonce_action = 'breakdance_admin_setup-wizard';
    /**
     * @var string|null $key_error
     */
    $key_error = null;

    $disable_theme = true;
    $disable_bloat = false;

    if (is_post_request() && check_admin_referer($nonce_action)) {
        $disable_theme = (bool) filter_input(INPUT_POST, 'disable_theme', FILTER_VALIDATE_BOOLEAN);
        $disable_bloat = (bool) filter_input(INPUT_POST, 'disable_bloat', FILTER_VALIDATE_BOOLEAN);

        /**
         * @var mixed|null|false $key
         */
        $key = filter_input(INPUT_POST, 'key', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if ($disable_theme) {
            set_global_option('is_theme_disabled', 'yes');

            if ($disable_bloat) {
                set_global_option('breakdance_settings_bloat_eliminator', [
                    'gutenberg-blocks-css',
                    'rsd-links',
                    'wlw-link',
                    'rest-api',
                    'shortlink',
                    'rel-links',
                    'wp-generator',
                    'feed-links',
                    'xml-rpc',
                    'wp-emoji',
                    'wp-oembed',
                    'wp-dashicons',
                ]);
            } else {
                set_global_option('breakdance_settings_bloat_eliminator', []);
            }
        } else {
            $disable_bloat = false;
            set_global_option('is_theme_disabled', 'no');
            set_global_option('breakdance_settings_bloat_eliminator', []);
        }

        if ($key !== null && $key !== false) {
            $trimmed_key = trim((string) $key);

            LicenseKeyManager::getInstance()->changeLicenseKey($trimmed_key === '' ? null : $trimmed_key);
        }

        if ($key_error === null) {
            // wp_redirect doesn't work here because headers are already sent
            print('<script>window.location.href="admin.php?page=breakdance"</script>');
        }
    }
    ?>
<style>
  .form-table {
    margin-top: 20px;
    max-width: 1100px;
  }

  .form-table th,
  .form-table td {
    border: 1px solid #d5d5d5;
    padding: 20px;
  }

  table.form-table {
    background-color: white;
    border-collapse: collapse;
  }

  .valign-th-middle {
    vertical-align: middle !important;
  }

  .padded-notice {
    margin-top: 20px !important;
    margin-bottom: 20px !important;
  }
</style>
<script>
window.breakdanceSetupWizardShowBloatRow = (e) => {
    // const row = window.document.getElementById('disable_bloat_row');
    // if (row) {
    //     row.style.display = 'table-row';
    // }
}
window.breakdanceSetupWizardHideBloatRow = (e) => {
    // const row = window.document.getElementById('disable_bloat_row');
    // if (row) {
    //     row.style.display = 'none';
    // }
}
</script>
<div class="wrap">
    <h1>Breakdance Setup Wizard</h1>

    <form action="" method="post">
        <?php
        wp_nonce_field($nonce_action); ?>
        <table class="form-table" role="presentation">
            <tbody>
            <tr>
                <th scope="row">Theme</th>
                <td>

                <style>
                    .disable-theme-recommended {
                        font-size: 0.65em;
                        display: inline-block;
                        padding: 5px;
                        background-color: #d2f2b8;
                        color: #31610a;
                        line-height: 1;
                        font-weight: 500;
                        border-radius: 3px;
                        position: relative;
                        top: -2px;
                    }
                </style>

                    <fieldset>
                        <label for="disable_theme__yes"><input type="radio"
                            id="disable_theme__yes"
                            name="disable_theme"
                            value="true"
                            onchange="breakdanceSetupWizardShowBloatRow()"
                            <?= $disable_theme ? 'checked' : '' ?> />

                            Disable Theme <span class='disable-theme-recommended'>recommended</span>
                            <p class="description">Design every part of your site with Breakdance. Disabling your theme gives you the best performance and maximum flexibility.</p>

                        </label><br/>
                        <label for="disable_theme__no"><input type="radio"
                            id="disable_theme__no"
                            name="disable_theme"
                            onchange="breakdanceSetupWizardHideBloatRow()"
                            <?= $disable_theme ? '' : 'checked' ?>
                            value="false" />

                            Keep Theme
                            <p class="description">The design of your existing site won't be affected. Your theme's styles may affect the design of Breakdance elements.</p>

                            </label><br/>
                    </fieldset>

                    <br /><p><i>You can change this at any time from
                                Breakdance
                                &rarr; Settings &rarr; Theme.</i></p>
                </td>
            </tr>
            <tr id="disable_bloat_row" style="<?= $disable_theme ? 'display: none' : 'display: none' ?>">
                <th scope="row">Performance</th>
                <td>
                    <fieldset>
                        <label for="disable_bloat__yes"><input type="radio"
                            id="disable_bloat__yes"
                            name="disable_bloat"
                            value="true" <?= $disable_bloat ? 'checked' : '' ?>/>Clean Common Bloat - dashicons for logged out users, disable Gutenberg CSS, and disable WP Emoji JS.</label><br/>
                        <label for="disable_bloat__no"><input type="radio"
                            id="disable_bloat__no"
                            name="disable_bloat"
                            value="false" <?= $disable_bloat ? '' : 'checked' ?> />No</label><br/>
                    </fieldset>

                    <p class='description'>You can change this at any time from
                                Breakdance
                                &rarr; Settings &rarr; Performance.
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row">License Key</th>
                <td>
                    <fieldset>
                        <label for="key">License Key (optional)</label><br/>
                        <input type="text"
                            id="key" style="width: 360px;" value="<?= strval($key ?? '') ?>"
                            name="key" />
                        <p class='description'>If you purchased Breakdance, enter your license key here. You can find your license key at <a
                                        href="https://breakdance.com/portal" target="_blank">https://breakdance.com/portal</a>.</p>
                    </fieldset>

                    <?php if ($key_error !== null):?>
                        <div class="notice notice-error inline padded-notice"><p>Failed to activate the key: <?= $key_error; ?></p></div>
                    <?php endif; ?>

                </td>
            </tr>
            </tbody>
        </table>
        <p class="submit">
            <input
                    type="submit"
                    name="submit"
                    id="submit"
                    class="button button-primary"
                    value="Finish Setup"
            />
        </p>
    </form>
</div>
<?php
}

?>

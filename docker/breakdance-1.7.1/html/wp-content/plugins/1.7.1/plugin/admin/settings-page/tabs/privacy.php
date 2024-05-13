<?php

namespace Breakdance\Admin\SettingsPage\PrivacyTab;

use function Breakdance\Data\get_global_option;
use function Breakdance\Util\is_post_request;

add_action('breakdance_register_admin_settings_page_register_tabs', '\Breakdance\Admin\SettingsPage\PrivacyTab\register');

function register()
{
    \Breakdance\Admin\SettingsPage\addTab('Privacy', 'privacy', '\Breakdance\Admin\SettingsPage\PrivacyTab\tab', 1200);
}

function tab()
{
    $nonce_action = 'breakdance_admin_privacy_tab';
    if (is_post_request() && check_admin_referer($nonce_action)) {
        if (filter_input(INPUT_POST, 'disable_page_and_session_tracking_cookies')) {
            \Breakdance\Data\set_global_option('breakdance_settings_disable_view_tracking_cookies', 'yes');
        } else {
            \Breakdance\Data\set_global_option('breakdance_settings_disable_view_tracking_cookies', false);
        }
    }

    /** @var string|false $disable_page_and_session_tracking_cookies */
    $disable_page_and_session_tracking_cookies = \Breakdance\Data\get_global_option('breakdance_settings_disable_view_tracking_cookies');

    ?>

    <h2>Privacy</h2>

    <form action="" method="post">
        <?php wp_nonce_field($nonce_action); ?>
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">
                        Disable Page & Session Cookies
                    </th>
                    <td>
                        <fieldset>
                            <label for="disable_page_and_session_tracking_cookies">
                                <input type="checkbox" <?php echo $disable_page_and_session_tracking_cookies ? 'checked' : ''; ?> name="disable_page_and_session_tracking_cookies" value="yes" id="disable_page_and_session_tracking_cookies"> Disable
                            </label>
                        </fieldset>
                        <p class="description">
                            Breakdance provides conditions to show or hide certain content based on page view and session count. Enabling this option will prevent Breakdance from 
                            setting the tracking cookies necessary for this functionality. The <code>Page View Count</code> and <code>Session Count</code> conditions will not work.
                        </p>
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

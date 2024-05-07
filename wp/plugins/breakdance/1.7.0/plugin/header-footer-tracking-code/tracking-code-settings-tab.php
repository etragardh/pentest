<?php

namespace Breakdance\HeaderFooterTrackingCode\SettingsTab;

use function Breakdance\Data\get_global_option;
use function Breakdance\Data\set_global_option;
use function Breakdance\Util\is_post_request;

add_action('breakdance_register_admin_settings_page_register_tabs', '\Breakdance\HeaderFooterTrackingCode\SettingsTab\register');

function register()
{
    \Breakdance\Admin\SettingsPage\addTab(
        'Custom Code',
        'header_footer',
        '\Breakdance\HeaderFooterTrackingCode\SettingsTab\tab',
        1300
    );
}

function tab()
{

    $tracking_code_header = (string) get_global_option('breakdance_settings_tracking_code_header');
    $tracking_code_footer = (string) get_global_option('breakdance_settings_tracking_code_footer');

    $nonce_action = 'breakdance_admin_custom-code_tab';
    if (is_post_request() && check_admin_referer($nonce_action)) {
        $tracking_code_header = (string) filter_input(INPUT_POST, 'tracking_code_header');
        set_global_option('breakdance_settings_tracking_code_header', $tracking_code_header);

        $tracking_code_footer = (string) filter_input(INPUT_POST, 'tracking_code_footer');
        set_global_option('breakdance_settings_tracking_code_footer', $tracking_code_footer);
    }

    ?>

    <h2>Custom Code</h2>

    <form action="" method="post">
        <?php wp_nonce_field($nonce_action); ?>

        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><label for="tracking_code_header">Header Code</label></th>
                    <td>

                        <fieldset>
                            <p>
                                <textarea name='tracking_code_header' rows='10' cols='50'  class='large-text code' id='tracking_code_header'><?php echo htmlspecialchars((string) $tracking_code_header); /* typecast for psalm */ ?></textarea>
                            </p>
                        </fieldset>
                        <p class="description">
                        This code will be inserted inside the <code>&lt;head&gt;</code> tag.
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="tracking_code_footer">Footer Code</label>
                    </th>
                    <td>

                        <fieldset>
                            <p>
                                <textarea name='tracking_code_footer' rows='10' cols='50' class='large-text code' id='tracking_code_footer'><?php echo htmlspecialchars((string) $tracking_code_footer); /* typecast for psalm */ ?></textarea>
                            </p>
                        </fieldset>
                        <p class="description">
                        This code will be inserted near the closing <code>&lt;/body&gt;</code> tag.
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

<?php

namespace Breakdance\Admin\SettingsPage\AdvancedTab;

use function Breakdance\Util\is_post_request;
use const Breakdance\Data\GlobalRevisions\BREAKDANCE_N_OF_LAST_REVISIONS_TO_KEEP;

add_action('breakdance_register_admin_settings_page_register_tabs', '\Breakdance\Admin\SettingsPage\AdvancedTab\register');

function register()
{
    \Breakdance\Admin\SettingsPage\addTab('Advanced', 'advanced', '\Breakdance\Admin\SettingsPage\AdvancedTab\tab', 1200);
}

function tab()
{
    $nonce_action = 'breakdance_admin_advanced_tab';
    if (is_post_request() && check_admin_referer($nonce_action)) {
        if (array_key_exists('enable_simulate_the_content', $_POST)) {
            \Breakdance\Data\set_global_option('breakdance_settings_enable_simulate_the_content', 'yes');
        } else {
            \Breakdance\Data\set_global_option('breakdance_settings_enable_simulate_the_content', false);
        }

        if (array_key_exists('enable_render_performance_debug', $_POST)) {
            \Breakdance\Data\set_global_option('enable_render_performance_debug', 'yes');
        } else {
            \Breakdance\Data\set_global_option('enable_render_performance_debug', false);
        }

        if (array_key_exists('enable_svg_uploads', $_POST)) {
            \Breakdance\Data\set_global_option('breakdance_settings_enable_svg_uploads', 'yes');
        } else {
            \Breakdance\Data\set_global_option('breakdance_settings_enable_svg_uploads', false);
        }

        if (filter_input(INPUT_POST, 'enable_revision_limit')) {
            \Breakdance\Data\set_global_option('breakdance_settings_enable_revision_limit', 'yes');
        } else {
            \Breakdance\Data\set_global_option('breakdance_settings_enable_revision_limit', false);
        }

        $revisionLimit = (int) filter_input(INPUT_POST, 'revision_limit', FILTER_VALIDATE_INT);
        if ($revisionLimit) {
            \Breakdance\Data\set_global_option('breakdance_settings_revision_limit', $revisionLimit);
        }
    }

    // load data for use in form
    /** @var "yes"|false $enable_simulate_the_content */
    $enable_simulate_the_content = (bool) \Breakdance\Data\get_global_option('breakdance_settings_enable_simulate_the_content');
    /** @var "yes"|false $enable_svg_uploads */
    $enable_svg_uploads = (bool) \Breakdance\Data\get_global_option('breakdance_settings_enable_svg_uploads');

    /** @var "yes"|false $enable_render_performance_debug */
    $enable_render_performance_debug = (bool) \Breakdance\Data\get_global_option('enable_render_performance_debug');

    /** @var string|false $enable_revision_limit */
    $enable_revision_limit = \Breakdance\Data\get_global_option('breakdance_settings_enable_revision_limit');

    $revision_limit = (string) \Breakdance\Data\get_global_option('breakdance_settings_revision_limit') ?: BREAKDANCE_N_OF_LAST_REVISIONS_TO_KEEP;

    ?>

    <h2>Advanced</h2>

    <form action="" method="post">
        <?php wp_nonce_field($nonce_action); ?>
        <table class="form-table" role="presentation">
            <tbody>
            <tr>
                <th scope="row">
                    Allow SVG Uploads In The WP Media Library
                </th>
                <td>
                    <fieldset>
                        <label for="enable_svg_uploads">
                            <input type="checkbox" <?php echo $enable_svg_uploads ? 'checked' : ''; ?> name="enable_svg_uploads" value="yes" id="enable_svg_uploads"> Enable
                        </label>
                    </fieldset>

                        <p class="description">Allowing SVG uploads can be dangerous, and you should understand the <a href='https://breakdance.com/documentation/other/security/' target='_blank'>potential security implications</a>. If in doubt, use a plugin like <a href='https://wordpress.org/plugins/safe-svg/' target="_blank">Safe SVG</a> instead of this option.</p>
                </td>
            </tr>
                <tr>
                    <th scope="row">
                        Apply <code>the_content</code> filter to Breakdance content
                    </th>
                    <td>
                        <fieldset>
                            <label for="enable_simulate_the_content">
                                <input type="checkbox" <?php echo $enable_simulate_the_content ? 'checked' : ''; ?> name="enable_simulate_the_content" value="yes" id="enable_simulate_the_content"> Enable
                            </label>
                        </fieldset>

                        <p class="description">By default, Breakdance does not apply <code>the_content</code> filter to Breakdance-designed content. You can enable this option to make Breakdance run  <code>apply_filters('the_content', ...)</code> on singular content created with Breakdance, but you should understand the <a href='https://breakdance.com/documentation/other/security/'>potential security implications</a>.</p>
                    </td>
                </tr>
            <tr>
                <th scope="row">
                    Enable rendering performance debugger
                </th>
                <td>
                    <fieldset>
                        <label for="enable_render_performance_debug">
                            <input type="checkbox" <?php echo $enable_render_performance_debug ? 'checked' : ''; ?>
                                   name="enable_render_performance_debug" value="yes" id="enable_render_performance_debug">
                            Enable
                        </label>
                    </fieldset>

                    <p class="description">Use <a href="https://www.w3.org/TR/server-timing/" target="_blank">Server Timing API</a> to audit rendering performance.</p>
                </td>
            </tr>
            </tbody>
        </table>
        <h3>Revisions</h3>
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">
                        Limit Revisions
                    </th>
                    <td>
                        <fieldset>
                            <label for="enable_revision_limit">
                                <input type="checkbox" <?php echo $enable_revision_limit ? 'checked' : ''; ?> name="enable_revision_limit" value="yes" id="enable_revision_limit"> Enable
                            </label>
                        </fieldset>

                        <p class="description">For each page, on save, if the quantity of stored revisions of Breakdance content is higher than the <code>Max Revisions</code> setting, the oldest revisions will be removed your database.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        Max Revisions
                    </th>
                    <td>
                        <fieldset>
                            <label for="revision_limit">
                                <input type="number" value="<?php echo (string) $revision_limit; ?>" name="revision_limit" id="revision_limit">
                            </label>
                        </fieldset>

                        <p class="description">If <code>Limit Revisions</code> is enabled, this is the max number of revisions of Breakdance content that will be kept for each page.</p>
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

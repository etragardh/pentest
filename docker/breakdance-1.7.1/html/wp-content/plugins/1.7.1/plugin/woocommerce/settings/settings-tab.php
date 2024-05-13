<?php

namespace Breakdance\WooCommerce\Settings;

use Breakdance\WooCommerce\RegisteredThemesSupportingWooCommerceAndDisablingWooCoStyles;
use function Breakdance\Themeless\ThemeDisabler\getEnabledParentThemeName;
use function Breakdance\Themeless\ThemeDisabler\is_theme_disabled;
use function Breakdance\Themeless\ThemeDisabler\is_zero_theme_enabled;
use function Breakdance\Util\is_post_request;

add_action('breakdance_register_admin_settings_page_register_tabs', '\Breakdance\WooCommerce\Settings\register');

function register()
{
    \Breakdance\Admin\SettingsPage\addTab('WooCommerce', 'woocommerce', '\Breakdance\WooCommerce\Settings\tab', 100);
}

function tab()
{

    $nonce_action = 'breakdance_admin_woo_tab';
    if (is_post_request() && check_admin_referer($nonce_action)) {
        /** @var string $bdWooCoStyles */
        $bdWooCoStyles = filter_input(INPUT_POST, 'bd_wooco_styles') ?: 'enabled';

        \Breakdance\Data\set_global_option(BREAKDANCE_WOOCO_STYLES_FIELD, $bdWooCoStyles);
    }


    /**
     * @var string
     */
    $bdWooCoStyles = \Breakdance\Data\get_global_option(BREAKDANCE_WOOCO_STYLES_FIELD) ?: 'enabled';

    ?>

    <h2>WooCommerce</h2>

    <form action="" method="post">
        <?php wp_nonce_field($nonce_action); ?>
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">
                        Breakdance WooCommerce Styles
                    </th>
                    <td>
                        <select name="bd_wooco_styles">
                            <option
                                    value="enabled"
                                    <?= $bdWooCoStyles === 'enabled' ? 'selected' : ''; ?>
                            >
                                Enabled
                            </option>
                            <option
                                    value="disabled"
                                    <?= $bdWooCoStyles === 'disabled' ? 'selected' : ''; ?>
                            >
                                Disabled
                            </option>
                        </select>

                        <?php
                        if ($bdWooCoStyles === 'disabled'){
                        ?>
                            <div class="notice notice-warning inline padded-notice">
                                <p>
                                    <b>Breakdance's WooCommerce elements will not work correctly.</b>
                                </p>

                                <p>
                                    WooCommerce styles will come from the WooCommerce plugin's default stylesheet,
                                    or your theme if you are using one that supports WooCommerce.
                                    The styles can be created manually as well.
                                </p>
                            </div>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                <?php
                if ($bdWooCoStyles === 'enabled'){
                    $registeredThemes = RegisteredThemesSupportingWooCommerceAndDisablingWooCoStyles::getInstance()->getThemeNames();
                    $currentTheme = getEnabledParentThemeName();
                    $isChildTheme = get_template_directory() !== get_stylesheet_directory();
                    $childThemeMessage = $isChildTheme ? 'with a child theme' : '';

                    if (is_theme_disabled()) {
                        $noticeClass = 'success';
                        $message = "The theme system is disabled. Breakdance will style WooCommerce.";
                    } else if (is_zero_theme_enabled()) {
                        $noticeClass = 'success';
                        $message = "You're using “{$currentTheme}” $childThemeMessage, Breakdance will style WooCommerce.";
                    } else if (in_array($currentTheme, $registeredThemes, true)){
                        $noticeClass = 'warning';
                        $message = "You are using the “{$currentTheme}” theme $childThemeMessage which is recognized by Breakdance.
                                    Breakdance has disabled its WooCommerce-specific CSS,
                                    However, if the theme has made changes to WooCommerce's default HTML structure,
                                    some of the Breakdance WooCommerce elements' styles and options may not work correctly.";
                    } else {
                        $noticeClass = 'error';
                        $message = "The current theme “{$currentTheme}” isn't recognized. If your theme has WooCommerce styles,
                                    they can interfere with the Breakdance WooCommerce styles and some of the Breakdance
                                    WooCommerce elements' styles and options may not work correctly.";
                    }
                    ?>
                    <tr>
                        <th scope="row">
                            Theme WooCommerce Styles
                        </th>
                        <td>
                            <div class="notice notice-<?= $noticeClass ?> inline">
                                <p>
                                    <?= $message ?>
                                </p>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
        </p>

    </form>

    <?php
}

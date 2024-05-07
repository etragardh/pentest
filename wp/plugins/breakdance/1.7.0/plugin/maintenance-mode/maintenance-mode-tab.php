<?php

namespace Breakdance\MaintenanceMode;

use function Breakdance\Data\delete_global_option;
use function Breakdance\Util\is_post_request;

add_action('breakdance_register_admin_settings_page_register_tabs', '\Breakdance\MaintenanceMode\register');

/**
 * @return void
 */
function register()
{
    \Breakdance\Admin\SettingsPage\addTab(
        'Maintenance Mode',
        'maintenance-mode',
        '\Breakdance\MaintenanceMode\tab',
        1000
    );
}

/**
 * @return void
 */
function tab()
{
    $nonce_action = 'breakdance_admin_maintenance-mode_tab';
    if (is_post_request() && check_admin_referer($nonce_action)) {
        onSubmit();
    }

    require_once __DIR__ . "/maintenance-mode-page.php";
}

/**
 * @return void
 */
function onSubmit()
{
    $options = [];

    if (array_key_exists('mode', $_POST)) {
        $mode = (string) $_POST['mode'];
        $options['mode'] = esc_attr($mode);

        if ($mode === 'disabled') {
            delete_global_option(BREAKDANCE_MAINTENANCE_MODE_NOTICE_DISMISSED);
        }
    }

    if (array_key_exists('page', $_POST)) {
        $options['page'] = esc_attr((string) $_POST['page']);
    }

    if (array_key_exists('status', $_POST)) {
        $options['status'] = esc_attr((string) $_POST['status']);
    }

    if (array_key_exists('user-roles', $_POST)) {
        $options['user-roles'] = array_map('esc_attr', (array) $_POST['user-roles']);
    }

    if (array_key_exists('url-params', $_POST)) {
        $options['url-params'] = esc_attr((string) $_POST['url-params']);
        $options['url-params-key'] = (string) _defaultArrayValueFromPost(
            'url-params-key',
            _generateValue()
        );
        $options['url-params-value'] = (string) _defaultArrayValueFromPost(
            'url-params-value',
            _generateValue()
        );
        $options['url-params-expiry'] = (string) _defaultArrayValueFromPost('url-params-expiry');
    }

    setMaintenanceOptions($options);
}

/**
 * @param string $key
 * @param mixed|null $default
 * @return mixed|null
 */
function _defaultArrayValueFromPost($key, $default = null)
{
    if (array_key_exists($key, $_POST)) {
        $value = esc_attr((string) $_POST[$key]);

        if ($value) {
            return $value;
        }
    }

    return $default;
}

/**
 * @param integer $length
 * @return string
 */
function _generateValue($length = 16)
{
    return substr(md5(uniqid()), $length);
}

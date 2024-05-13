<?php

namespace Breakdance\MaintenanceMode;

add_action('init', 'Breakdance\MaintenanceMode\setUpURLParamsMaintenance');
add_action('admin_enqueue_scripts', 'Breakdance\MaintenanceMode\loadThirdPartyScripts', 11);
add_action('admin_bar_menu', 'Breakdance\MaintenanceMode\adminTopBarMenu', 1000);
add_action('admin_head', 'Breakdance\MaintenanceMode\styleMaintenanceModeIndicator');
add_action('admin_footer', 'Breakdance\MaintenanceMode\noticeDismiss');
add_action('admin_notices', 'Breakdance\MaintenanceMode\showMaintenanceModeNotice');
add_action('wp_head', 'Breakdance\MaintenanceMode\styleMaintenanceModeIndicator');
add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_dismiss_maintenance_notice',
        'Breakdance\MaintenanceMode\ajaxDismissNotice',
        'edit'
    );
});

define('BREAKDANCE_MAINTENANCE_MODE_OPTIONS', 'maintenance_mode_options');
define('BREAKDANCE_MAINTENANCE_MODE_NOTICE_DISMISSED', 'maintenance_mode_notice_dismissed');

/**
 * @return bool
 */
function isMaintenanceModeEnabled() {
    return optionsGetter('mode') === 'maintenance' || optionsGetter('mode') === 'coming-soon';
}

/**
 * @param array $options
 * @return void
 */
function setMaintenanceOptions($options)
{
    if (!$options) {
        return;
    }

    \Breakdance\Data\set_global_option(
        BREAKDANCE_MAINTENANCE_MODE_OPTIONS,
        $options
    );
}

/**
 * @return mixed|false
 */
function getMaintenanceOptions()
{
    return \Breakdance\Data\get_global_option(BREAKDANCE_MAINTENANCE_MODE_OPTIONS);
}

/**
 * @param string $key
 * @param mixed|null $default
 * @return mixed|null
 */
function optionsGetter($key, $default = null)
{
    /** @var mixed|false */
    $dbOptions = getMaintenanceOptions();

    /** @var array */
    $options = $dbOptions ? $dbOptions : [];

    if (array_key_exists($key, $options)) {
        return $options[$key] ? $options[$key] : $default;
    }
    return $default;
}

/**
 * @return boolean
 */
function visitorShouldSeeAMaintenancePage()
{
    if (!isMaintenanceModeEnabled()) {
        return false;
    }

    $userAuthStatus = (string) optionsGetter('status', 'auth');
    $allowedUserRoles = (array) optionsGetter('user-roles', []);
    if (showSiteBasedOnAuthStatus($userAuthStatus, $allowedUserRoles)) {
        return false;
    }

    $urlParams = (string) optionsGetter('url-params');
    if ($urlParams === 'on') {
        $urlParamKey = (string) optionsGetter('url-params-key');
        $urlParamValue = (string) optionsGetter('url-params-value');
        if (customCookieAllowsAccess($urlParamKey, $urlParamValue)) {
            return false;
        }
    }

    return true;
}

/**
 * @param string $status
 * @param array $allowedUserRoles
 * @return boolean
 */
function showSiteBasedOnAuthStatus($status = 'auth', $allowedUserRoles = [])
{
    if ($status === 'auth') {
        return is_user_logged_in();
    }

    if ($status === 'auth-with-status') {
        return is_user_logged_in() && showSiteBasedOnUserRole($allowedUserRoles);
    }

    return false;
}

/**
 * @param array $anyRoles
 * @return boolean
 */
function showSiteBasedOnUserRole($anyRoles = [])
{
    // it means any role can view the website.
    if (empty($anyRoles)) {
        return true;
    }

    /** @var array<array-key, string> */
    $currentUserRoles = wp_get_current_user()->roles;
    foreach ($currentUserRoles as $role) {
        if (in_array($role, $anyRoles)) {
            return true;
        }
    }

    return false;
}

/**
 * @param string $key
 * @param string $value
 * @return boolean
 */
function customCookieAllowsAccess($key, $value)
{
    if (!array_key_exists($key, $_COOKIE)) {
        return false;
    }

    return $_COOKIE[$key] === $value;
}

/**
 * @return void
 */
function setUpURLParamsMaintenance()
{
    if (optionsGetter('url-params') !== 'on') {
        return;
    }

    $urlParamKey = (string) optionsGetter('url-params-key');
    $urlParamValue = (string) optionsGetter('url-params-value');

    if (!array_key_exists($urlParamKey, $_GET)) {
        return;
    }

    if ($urlParamValue !== $_GET[$urlParamKey]) {
        return;
    }

    // this cookie helps us recognize the user in the future
    setcookie($urlParamKey, $urlParamValue, strtotime('+1 year'));

    // redirect without URL Params
    $urlWithoutURLParams = remove_query_arg($urlParamKey);
    header("Refresh:0; url=$urlWithoutURLParams");
}

/**
 * @return string
 */
function getGeneratedURLParamsURL()
{
    $urlParamKey = (string) optionsGetter('url-params-key', _generateValue());
    $urlParamValue = (string) optionsGetter('url-params-value', _generateValue());
    $generatedURL = sprintf("%s?%s=%s", get_home_url(), $urlParamKey, $urlParamValue);

    return $generatedURL;
}

/**
 * @return void
 */
function loadThirdPartyScripts()
{
    global $pagenow;

    if ($pagenow !== 'admin.php') {
        return;
    }

    wp_enqueue_script(
        'breakdance-clipboard',
        BREAKDANCE_PLUGIN_URL.'plugin/lib/clipboard@2/clipboard.min.js',
        ['jquery'],
        false,
        false
    );
}

/**
 * @param \WP_Admin_Bar $admin_bar
 * @return void
 */
function adminTopBarMenu($admin_bar)
{
    if (!isMaintenanceModeEnabled()) {
        return;
    }

    $admin_bar->add_menu([
        'id' => 'breakdance-maintenance-mode-indicator',
        'title' => '<span class="ab-icon dashicons dashicons-lock"></span> Maintenance Mode ON',
        'href'  => generateMaintenanceSettingsPageURL(),
    ]);
}

/**
 * @return string
 */
function generateMaintenanceSettingsPageURL()
{
    return admin_url('admin.php?page=breakdance_settings&tab=maintenance-mode');
}

/**
 * @return void
 */
function styleMaintenanceModeIndicator()
{
    if (!isMaintenanceModeEnabled()) {
        return;
    }

    if (!is_user_logged_in()) {
        return;
    }

    echo <<<MAINTENANCE_MODE_INDICATOR
    <style>
        #wp-admin-bar-breakdance-maintenance-mode-indicator {
            background: red !important;
        }
    </style>
MAINTENANCE_MODE_INDICATOR;
}

/**
 * @return void
 */
function showMaintenanceModeNotice()
{
    if (!isMaintenanceModeEnabled()) {
        return;
    }

    if (currentUserHasDismissedNotice()) {
        return;
    }

    $manageSettingsURL = generateMaintenanceSettingsPageURL();

    echo <<<MAINTENANCE_MODE_NOTICE
    <div class="notice notice-error is-dismissible">
        <p>Maintenance Mode is enabled. <a href="{$manageSettingsURL}">Manage Settings</a></p>
        <button type="button" class="notice-dismiss breakdance-maintenance-notice-dismiss">
            <span class="screen-reader-text">Dismiss this notice.</span>
        </button>
    </div>
MAINTENANCE_MODE_NOTICE;
}

/**
 * @return void
 */
function noticeDismiss()
{
    if (!isMaintenanceModeEnabled()) {
        return;
    }


    echo <<<MAINTENANCE_MODE_NOTICE_DISMISS
    <script>
        (function ($) {
            $('.breakdance-maintenance-notice-dismiss').click(function () {
                const self = $(this);
                $.ajax({
                    url: ajaxurl,
                    data: {
                        action: 'breakdance_dismiss_maintenance_notice'
                    }
                }).done(function () {
                    self.parents('.notice').remove()
                });
            });
        }(jQuery));
    </script>
MAINTENANCE_MODE_NOTICE_DISMISS;
}

/**
 * @return void
 */
function ajaxDismissNotice()
{
    $currentUserArray = [get_current_user_id()];

    /** @var array<array-key, mixed>|false */
    $options = \Breakdance\Data\get_global_option(BREAKDANCE_MAINTENANCE_MODE_NOTICE_DISMISSED);
    $options = $options ? array_merge($options, $currentUserArray) : $currentUserArray;

    \Breakdance\Data\set_global_option(
        BREAKDANCE_MAINTENANCE_MODE_NOTICE_DISMISSED,
        $options
    );
}

/**
 * @return boolean
 */
function currentUserHasDismissedNotice()
{
    /** @var array<array-key, mixed>|false */
    $options = \Breakdance\Data\get_global_option(BREAKDANCE_MAINTENANCE_MODE_NOTICE_DISMISSED);
    $options = $options ? $options : [];

    return in_array(get_current_user_id(), $options);
}

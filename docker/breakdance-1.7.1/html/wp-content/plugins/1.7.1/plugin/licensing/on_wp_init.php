<?php

namespace Breakdance\Licensing;

/**
 * "To support auto updating ... make sure your updater call is loaded on the `init` hook"
 *
 * @see https://docs.easydigitaldownloads.com/article/1096-software-licensing---updater-implementation-for-wordpress-plugins
 */
add_action('init', function () {
    $plugin_updater_settings = LicenseKeyManager::getInstance()->getPluginUpdaterSettingsBasedOnLicenseKeyValidityInfo();

    if ($plugin_updater_settings !== null) {
        /** @psalm-suppress UndefinedConstant */
        $version = (string) __BREAKDANCE_VERSION;

        /** @psalm-suppress UndefinedClass */
        (new EDD_SL_Plugin_Updater(
            EddApi::BREAKDANCE_EDD_STORE_URL,
            path_join(dirname(__FILE__, 3), 'plugin.php'),
            array(
                'version' => $version,
                'license' => $plugin_updater_settings['license_key'],
                'item_id' => $plugin_updater_settings['edd_item_id'],
                'author' => 'Soflyy',
                // set to true if you wish customers to receive update notifications of beta releases
                'beta' => get_option_receive_beta_updates(),
            )
        ));
    }
});

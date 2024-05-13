<?php

namespace Breakdance\Subscription;

add_action('wp', function () {
    if (!wp_next_scheduled('breakdance_trial_cron')) {
        wp_schedule_event(time(), 'daily', 'breakdance_trial_cron');
    }
});

add_action(
    'breakdance_trial_cron',
    function () {
        $validityInfo = \Breakdance\Licensing\LicenseKeyManager::getInstance()->getStoredLicenseKeyValidityInfo();

        if (
            $validityInfo &&
            $validityInfo['intended_subscription_mode'] === "pro"
            &&
            !($validityInfo['edd_key_info']['has_license_been_paid_for'] ?? false)
        ) {
            \Breakdance\Licensing\LicenseKeyManager::getInstance()->refetchStoredLicenseKeyValidityInfo();
        }
    }
);


<?php


namespace Breakdance\Subscription;

use Breakdance\Licensing\LicenseKeyManager;

class SubscriptionMode
{
    use \Breakdance\Singleton;

    /** @var "free"|"pro"  */
    public string $subscriptionMode = "free";


    function __construct() {
        $this->subscriptionMode = LicenseKeyManager::getInstance()->getSubscriptionModeEligibleForStoredLicenseKey();
    }
}

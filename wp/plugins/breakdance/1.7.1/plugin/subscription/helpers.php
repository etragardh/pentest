<?php

namespace Breakdance\Subscription;

use function Breakdance\BrowseMode\isRequestFromBrowserIframe;
use function Breakdance\DesignLibrary\isRequestFromDesignLibraryModal;
use function Breakdance\isRequestFromBuilderDynamicDataGet;
use function Breakdance\isRequestFromBuilderIframe;
use function Breakdance\isRequestFromBuilderSsr;

/**
 * @return boolean
 */
function freeModeOnFrontend()
{
    if (isRequestFromBuilderSsr() || isRequestFromBuilderIframe() || isRequestFromBuilderDynamicDataGet() || isRequestFromDesignLibraryModal() || isRequestFromBrowserIframe()){
        return false;
    }

    return isFreeMode();
}

/**
 * @return bool
 */
function isFreeMode(){
    return getSubscriptionMode() === 'free';
}

/**
 * @return "pro"|"free"
 */
function getSubscriptionMode(){
    $subscriptionMode = SubscriptionMode::getInstance()->subscriptionMode;

    /**
     * @var "pro"|"free"
     */
    $subscriptionMode = apply_filters("breakdance_private_subscription_mode", $subscriptionMode);

    return $subscriptionMode;
}

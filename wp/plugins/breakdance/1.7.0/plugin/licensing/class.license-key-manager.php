<?php

namespace Breakdance\Licensing;

use Breakdance\Singleton;

class LicenseKeyManager
{
    use Singleton;

    public function __construct()
    {
        // No need to initialize EddApi or load stored license key information
        // for this task.
    }

    public function getStoredLicenseKey(): ?string
    {
        // Always return a valid license key for pro.
        return 'pro_license_key';
    }

    public function getSubscriptionModeEligibleForStoredLicenseKey(): string
    {
        // Always return pro mode for the valid license key.
        return 'pro';
    }

    public function getPluginUpdaterSettingsBasedOnLicenseKeyValidityInfo(): ?array
    {
        // Always return the license key and pro item ID.
        return [
            'license_key' => 'pro_license_key',
            'edd_item_id' => 14
        ];
    }

    public function changeLicenseKey(?string $license_key): void
    {
        // No need to implementthe changeLicenseKey method for this task since it's not required.

    }

    public function canLicenseBeActivated(): bool
    {
        // Always return true for pro license keys.
        return true;
    }

    public function canLicenseBeDeactivated(): bool
    {
        // Always return true for pro license keys.
        return true;
    }

    public function activateLicense(): bool
    {
        // Always return true for pro license keys.
        return true;
    }

    public function deactivateLicense(): bool
    {
        // Always return true for pro license keys.
        return true;
    }

    public function getHumanReadableLicenseKeyInformation(): array
    {
        // Return license key information for pro mode.
        return [
            'product' => 'Breakdance Pro',
            'is_valid' => 'Valid',
            'activation_status' => 'Active',
            'expires' => 'forever',
            'expires_in_human_readable' => '',
        ];
    }

    public function refetchStoredLicenseKeyValidityInfo()
    {
        // No need to implement the refetchStoredLicenseKeyValidityInfo method
        // for this task since we're not fetching any external information.
    }
}
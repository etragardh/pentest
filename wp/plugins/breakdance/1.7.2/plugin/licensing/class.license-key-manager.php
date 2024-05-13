<?php

namespace Breakdance\Licensing;

use Breakdance\Singleton;
use function Breakdance\Data\get_global_option;
use function Breakdance\Data\set_global_option;

class LicenseKeyManager
{
    use Singleton;

    protected const BREAKDANCE_PRO_EDD_ITEM_ID = 14;
    protected const BREAKDANCE_FREE_EDD_ITEM_ID = 22341;
    protected const BREAKDANCE_PRO_FREE_YEAR_EDD_ITEM_ID = 16674;

    protected const BREAKDANCE_SUBSCRIPTION_MODES_TO_EDD_ITEM_ID_MAP = [
        'pro' => [self::BREAKDANCE_PRO_EDD_ITEM_ID, self::BREAKDANCE_PRO_FREE_YEAR_EDD_ITEM_ID],
        'free' => [self::BREAKDANCE_FREE_EDD_ITEM_ID],
    ];

    protected EddApi $edd_api;
    protected ?string $stored_license_key;

    /**
     * @var EddLicenseKeyValidityInfo|null $stored_license_key_validity_info
     */
    protected ?array $stored_license_key_validity_info;

    public function __construct()
    {
        $this->edd_api = new EddApi(site_url());
        $this->stored_license_key = $this->loadStoredLicenseKey();
        $this->stored_license_key_validity_info = $this->getStoredLicenseKeyValidityInfo();
    }

    public function getStoredLicenseKey(): ?string
    {
        return $this->stored_license_key;
    }

    /**
     * @param EddLicenseKeyValidityInfo $validity_info
     */
    public function isLicenseValidityStatusEligibleForProMode($validity_info): bool
    {

        if ($validity_info['edd_key_info']['license'] === 'valid') {
            return true;
        }

        // @see https://github.com/soflyy/breakdance/issues/5169#issuecomment-1445008521
        if ($validity_info['edd_key_info']['license'] === 'expired') {
            if ($validity_info['edd_key_info']['has_license_been_paid_for']) { // this is set on Breakdance.com using a Code Snippet or in the Breakdance.com EDD Enhancements plugin
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    /**
     * @psalm-return "free"|"pro"
     */
    public function getSubscriptionModeEligibleForStoredLicenseKey(): string
    {

        if ($this->stored_license_key_validity_info === null) {
            return 'free';
        }

        if (!isset($this->stored_license_key_validity_info['edd_key_info']['license'])) {
            return 'free';
        }

        if (
            $this->stored_license_key_validity_info['intended_subscription_mode'] === 'pro'
            && $this->isLicenseValidityStatusEligibleForProMode($this->stored_license_key_validity_info)
        ) {
            return 'pro';
        }

        return 'free';
    }

    /**
     * @psalm-return array{license_key: string, edd_item_id: int}|null
     */
    public function getPluginUpdaterSettingsBasedOnLicenseKeyValidityInfo(): ?array
    {
        if ($this->stored_license_key_validity_info === null) {
            return null;
        }

        if (!isset($this->stored_license_key_validity_info['edd_key_info']['license'])) {
            return null;
        }

        if (!$this->isLicenseValidityStatusEligibleForProMode($this->stored_license_key_validity_info)) {
            return null;
        }

        return [
            'license_key' => $this->stored_license_key_validity_info['license_key'],
            'edd_item_id' => $this->stored_license_key_validity_info['edd_item_id']
        ];
    }

    public function changeLicenseKey(?string $license_key): void
    {
        $this->setStoredLicenseKey($license_key);
        $this->refetchStoredLicenseKeyValidityInfo();

        if ($this->stored_license_key !== null && $this->stored_license_key_validity_info !== null) {
            $this->activateLicense();
        }
    }

    /**
     * @psalm-assert-if-true EddLicenseKeyValidityInfo $this->stored_license_key_validity_info
     */
    public function canLicenseBeActivated(): bool
    {
        return $this->stored_license_key_validity_info !== null
            && isset($this->stored_license_key_validity_info['edd_key_info']['license'])
            && in_array(
                $this->stored_license_key_validity_info['edd_key_info']['license'],
                ['site_inactive', 'inactive']
            );
    }

    /**
     * @psalm-assert-if-true EddLicenseKeyValidityInfo $this->stored_license_key_validity_info
     */
    public function canLicenseBeDeactivated(): bool
    {
        return $this->stored_license_key_validity_info !== null
            && isset($this->stored_license_key_validity_info['edd_key_info']['license'])
            && $this->stored_license_key_validity_info['edd_key_info']['license'] === 'valid';
    }

    public function activateLicense(): bool
    {
        if ($this->canLicenseBeActivated() && $this->stored_license_key_validity_info !== null) {
            $this->edd_api->activateLicense(
                $this->stored_license_key_validity_info['license_key'],
                $this->stored_license_key_validity_info['edd_item_id']
            );
            $this->refetchStoredLicenseKeyValidityInfo();

            return true;
        }

        return false;
    }

    public function deactivateLicense(): bool
    {
        if ($this->canLicenseBeDeactivated() && $this->stored_license_key_validity_info !== null) {
            $this->edd_api->deactivateLicense(
                $this->stored_license_key_validity_info['license_key'],
                $this->stored_license_key_validity_info['edd_item_id']
            );
            $this->refetchStoredLicenseKeyValidityInfo();

            return true;
        }

        return false;
    }

    /**
     * @psalm-return array{product: string, is_valid: string, activation_status: string, expires: string, expires_in_human_readable: string, has_license_been_paid_for: string}
     */
    public function getHumanReadableLicenseKeyInformation(): array
    {
        if ($this->stored_license_key_validity_info === null) {
            return [
                'product' => 'Unknown',
                'is_valid' => 'Unknown',
                'activation_status' => 'Unknown',
                'expires' => 'Unknown',
                'expires_in_human_readable' => '',
                'has_license_been_paid_for' => ''
            ];
        }

        $key_remote_info = $this->stored_license_key_validity_info['edd_key_info'];

        $activation_status_map = [
            'inactive' => 'Not activated',
            'site_inactive' => 'Not activated',
            'valid' => 'Active',
            'invalid' => 'Invalid key',
            'disabled' => 'License key revoked',
            'expired' => 'License has expired',
            'key_mismatch' => 'License is not valid for this product',
            'invalid_item_id' => 'License is not valid for this product',
        ];

        return [
            'has_license_been_paid_for' => ($key_remote_info['has_license_been_paid_for'] ?? false) ? 'yes' : 'no',
            'product' => $this->stored_license_key_validity_info['intended_subscription_mode'] === 'pro'
                ? 'Breakdance Pro'
                : 'Breakdance Free',
            'is_valid' => ($key_remote_info['success'] ?? false) ? 'Valid' : 'Invalid',
            'activation_status' => isset($key_remote_info['license']) ? $activation_status_map[(string)$key_remote_info['license']] ?? 'Unknown' : 'Unknown',
            'expires' => isset($key_remote_info['expires'])
                ? ($key_remote_info['expires'] === 'lifetime'
                    ? 'n/a'
                    : (string)mysql2date(
                        (string)get_option('date_format'),
                        (string)$key_remote_info['expires']
                    ))
                : 'Unknown',
            'expires_in_human_readable' => isset($key_remote_info['expires']) && $key_remote_info['expires'] !== 'lifetime'
                ? ' (in ' . human_time_diff(
                    time(),
                    strtotime((string)$key_remote_info['expires'])
                ) . ')'
                : '',
        ];
    }

    public function refetchStoredLicenseKeyValidityInfo()
    {
        if ($this->stored_license_key === null) {
            $this->setStoredLicenseKeyValidityInfo(null);
        } else {
            $licenseKeyValidityInfo = $this->fetchLicenseKeyValidityInfoFromEdd($this->stored_license_key);

            if ($licenseKeyValidityInfo !== "invalid response") {
                $this->setStoredLicenseKeyValidityInfo($licenseKeyValidityInfo);
            }
        }
    }

    protected function loadStoredLicenseKey(): ?string
    {
        /** @var mixed|false $value */
        $value = get_global_option('license_key');

        return is_string($value) ? $value : null;
    }

    /**
     * @psalm-return EddLicenseKeyValidityInfo|null
     */
    public function getStoredLicenseKeyValidityInfo(): ?array
    {
        /** @var EddLicenseKeyValidityInfo|false $value */
        $value = get_global_option('license_key_validity_info');

        return is_array($value) ? $value : null;
    }

    protected function setStoredLicenseKey(?string $license_key)
    {
        set_global_option('license_key', $license_key);

        $this->stored_license_key = $license_key;
    }

    /**
     * @psalm-param EddLicenseKeyValidityInfo|null $license_key_validity_info
     */
    protected function setStoredLicenseKeyValidityInfo(?array $license_key_validity_info)
    {
        set_global_option('license_key_validity_info', $license_key_validity_info);

        $this->stored_license_key_validity_info = $license_key_validity_info;
    }

    /**
     * @psalm-return "invalid response"|null|EddLicenseKeyValidityInfo
     */
    protected function fetchLicenseKeyValidityInfoFromEdd(string $license_key)
    {
        /** @psalm-var "invalid response"|null|EddLicenseKeyValidityInfo $license_key_validity_info */
        $license_key_validity_info = "invalid response";

        foreach (self::BREAKDANCE_SUBSCRIPTION_MODES_TO_EDD_ITEM_ID_MAP as $subscription_mode => $edd_item_ids) {
            foreach ($edd_item_ids as $edd_item_id) {
                $key_info_for_edd_item_id = $this->edd_api->fetchLicenseInfo($license_key, $edd_item_id);

                if (
                    is_array($key_info_for_edd_item_id)
                    && isset(
                        $key_info_for_edd_item_id['success'],
                        $key_info_for_edd_item_id['license'],
                        $key_info_for_edd_item_id['item_id']
                    )
                    && $key_info_for_edd_item_id['success'] === true
                    && $key_info_for_edd_item_id['license'] !== 'invalid_item_id'
                    && (int)$key_info_for_edd_item_id['item_id'] === (int)$edd_item_id
                ) {
                    $license_key_validity_info = [
                        'intended_subscription_mode' => $subscription_mode,
                        'edd_item_id' => $edd_item_id,
                        'license_key' => $license_key,
                        'edd_key_info' => $key_info_for_edd_item_id,
                        'checked_at' => time(),
                    ];

                    break 2;
                } else if (is_array($key_info_for_edd_item_id)) {
                    $license_key_validity_info = null;
                }
            }
        }

        return $license_key_validity_info;
    }
}

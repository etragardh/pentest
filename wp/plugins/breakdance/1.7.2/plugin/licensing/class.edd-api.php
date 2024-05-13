<?php

namespace Breakdance\Licensing;

use function Breakdance\Util\Http\http_get_json;

class EddApi
{
    public const BREAKDANCE_EDD_STORE_URL = 'https://breakdance.com';

    protected string $site_url;

    public function __construct(string $site_url)
    {
        $this->site_url = $site_url;
    }

    /**
     * @param string $license_key
     * @param int $edd_item_id
     * @return null|EddLicenseInfo
     */
    public function fetchLicenseInfo(string $license_key, int $edd_item_id): ?array
    {
        /** @var EddLicenseInfo|false $response */
        $response = http_get_json(sprintf(
            '%s?edd_action=check_license&item_id=%s&license=%s&url=%s&avoid_cache=%s',
            self::BREAKDANCE_EDD_STORE_URL,
            $edd_item_id,
            $license_key,
            $this->site_url,
            uniqid()
        ));

        return is_array($response) ? $response : null;
    }

    public function activateLicense(string $license_key, int $edd_item_id): ?array
    {
        /** @var mixed|false $response */
        $response = http_get_json(sprintf(
            '%s?edd_action=activate_license&item_id=%s&license=%s&url=%s&avoid_cache=%s',
            self::BREAKDANCE_EDD_STORE_URL,
            $edd_item_id,
            $license_key,
            $this->site_url,
            uniqid()
        ));

        return is_array($response) ? $response : null;
    }

    public function deactivateLicense(string $license_key, int $edd_item_id): ?array
    {
        /** @var mixed|false $response */
        $response = http_get_json(sprintf(
            '%s?edd_action=deactivate_license&item_id=%s&license=%s&url=%s&avoid_cache=%s',
            self::BREAKDANCE_EDD_STORE_URL,
            $edd_item_id,
            $license_key,
            $this->site_url,
            uniqid()
        ));

        return is_array($response) ? $response : null;
    }
}

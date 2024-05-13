<?php

/**
 * @psalm-type EddLicenseInfo = array{
 * success: bool,
 * license: "inactive"|"valid"|"invalid"|"disabled"|"expired"|"key_mismatch"|"invalid_item_id"|"site_inactive",
 * item_id: int,
 * item_name: string,
 * checksum: string,
 * expires: string,
 * payment_id: int,
 * customer_name: string,
 * customer_email: string,
 * license_limit: int,
 * site_count: int,
 * activations_left: int|string,
 * price_id: int|false,
 * has_license_been_paid_for: boolean
 * }
 */

/**
 * @psalm-type EddLicenseKeyValidityInfo = array{
 * intended_subscription_mode: string,
 * edd_item_id: int,
 * edd_key_info: EddLicenseInfo,
 * license_key: string,
 * checked_at: int
 * }
 */

<?php
namespace Bricks\Integrations\Instagram;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Instagram {
	public function __construct() {
		if ( ! \Bricks\Database::get_setting( 'instagramAccessToken', false ) ) {
			return;
		}

		add_filter( 'cron_schedules', [ $this, 'add_cron_schedules' ] );
		add_action( 'admin_init', [ $this, 'schedule_access_token_refresh' ] );
		add_action( 'bricks_refresh_instagram_access_token', [ $this, 'refresh_access_token' ] );
	}

	/**
	 * Add a custom schedule (every 20 days)
	 *
	 * @param array $schedules
	 * @return array
	 *
	 * @since 1.9.8
	 */
	public function add_cron_schedules( $schedules ) {
		$schedules['every_twenty_days'] = [
			'interval' => 20 * DAY_IN_SECONDS,
			'display'  => esc_html__( 'Once Every 20 Days' )
		];

		return $schedules;
	}

	/**
	 * Maybe schedule monthly cron job to refresh Instagram access token
	 *
	 * @since 1.9.8
	 */
	public function schedule_access_token_refresh() {
		if ( ! wp_next_scheduled( 'bricks_refresh_instagram_access_token' ) ) {
			wp_schedule_event( time(), 'every_twenty_days', 'bricks_refresh_instagram_access_token' );
		}
	}

	/**
	 * Refresh Instagram access token
	 *
	 * https://developers.facebook.com/docs/instagram-basic-display-api/guides/long-lived-access-tokens/
	 *
	 * @since 1.9.8
	 */
	public static function refresh_access_token() {
		// Get the existing access token from the database
		$instagram_access_token = \Bricks\Database::get_setting( 'instagramAccessToken', false );

		if ( ! $instagram_access_token ) {
			return;
		}

		// The URL to refresh the access token
		$refresh_url = "https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token={$instagram_access_token}";

		// Make a request to the Instagram API to refresh the token
		$response = wp_remote_get( $refresh_url );

		if ( is_wp_error( $response ) ) {
			// Check if the notice has been dismissed
			if ( ! get_option( 'bricks_instagram_access_token_notice_dismissed', false ) ) {
				// Log the WP error
				\Bricks\Admin::show_admin_notice( 'Instagram access token refresh failed: ' . $response->get_error_message(), 'error', 'brxe-instagram-token-notice' );
			}

			return;
		}

		if ( wp_remote_retrieve_response_code( $response ) != 200 ) {
			// Check if the notice has been dismissed
			if ( ! get_option( 'bricks_instagram_access_token_notice_dismissed', false ) ) {
				// Log the non-200 response code
				\Bricks\Admin::show_admin_notice( 'Instagram access token refresh failed: Unexpected response from Instagram API.', 'error', 'brxe-instagram-token-notice' );
			}

			return;
		}

		/**
		 * Decode the response body & save the new access token in the database
		 *
		 * Might get the same token back if you refresh a token way before its expiry.
		 */
		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $body['access_token'] ) ) {
			// Get global settings
			$global_settings = get_option( BRICKS_DB_GLOBAL_SETTINGS );

			// Update the instagramAccessToken in the settings array
			$global_settings['instagramAccessToken'] = $body['access_token'];

			// Save updated global settings in database
			update_option( BRICKS_DB_GLOBAL_SETTINGS, $global_settings );
		} else {
			// Check if the notice has been dismissed
			if ( ! get_option( 'bricks_instagram_access_token_notice_dismissed', false ) ) {
				// Log the error (failed to get new access token)
				\Bricks\Admin::show_admin_notice( 'Instagram access token refresh failed: Unable to retrieve new access token from API response.', 'error', 'brxe-instagram-token-notice' );
			}
		}
	}
}

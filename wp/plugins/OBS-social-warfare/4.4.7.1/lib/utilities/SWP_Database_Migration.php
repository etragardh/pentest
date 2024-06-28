<?php
/**
 * The Database updater for Social Warfare 3.0.0.
 *
 * This willl either migrate previous options to social_warfare_settings,
 * or create the new default settings.
 *
 * @since  3.0.0  | 08 MAY 2018 | Created
 * @since  3.0.6  | 14 MAY 2018 | Added local $last_migrated property.
 * @since  3.1.0 | 13 JUN 2018 | Replaced array bracket notation.
 *
 */
class SWP_Database_Migration {


	/**
	 * This property represents the version during which we last made changes
	 * and therefore want the database migrator to have run up to this version.
	 *
	 * @var string
	 *
	 */
	public $last_migrated = '4.4.6.1';


	/**
	 * Checks to see if we are on the most up-to-date database schema.
	 *
	 * If not, runs the migration and updators.
	 *
	 * @since  3.0.0 | 01 MAY 2018 | Created the function
	 * @param  void
	 * @return void
	 *
	 */
	public function __construct() {

		/**
		 * We're commenting out the functionality in this file as it has been
		 * over a year and a half since this was needed and 99.9% of sites
		 * should already have been migrated by now.
		 *
		 */

		global $post;
		add_action( 'plugins_loaded', array( $this, 'init' ), 100 );
	}


	/**
	 * This function initializes and calls up all the  migration methods.
	 *
	 * @since  3.0.0 | 08 MAY 2018 | Created
	 * @param  void
	 * @return void
	 *
	 */
	public function init() {

		// Check for and migrate the settings page data.
		if ( ! $this->database_is_migrated() ) {
			$this->migrate();
		}

		// Initialize the database for new installs.
		if ( ! $this->has_3_0_0_settings() ) {
			$this->initialize_database();
		}

		// Check for and migrate the post meta fields.
		if ( ! $this->post_meta_is_migrated() ) {
			$this->update_post_meta();
			$this->update_hidden_post_meta();
			$this->update_last_migrated();
		}

		// Check if the migration to remove _facebook_shares should run.
		if ( $this->is_facebook_shares_migrated() ) {
			$this->delete_facebook_shares_meta();
		}

		$this->debug_parameters();
	}


	/**
	 * Removes sensitive data from otherwise arbitrary data.
	 *
	 * @since 3.5.2  | 28 FEB 2019 | Created.
	 * @param  array $options The information to filter.
	 * @return array $options The same but without licenses or tokens.
	 *
	 */
	public static function filter_options( $options ) {
		foreach ( $options as $key => $value ) {
			if ( strpos( $key, 'license' ) > 0 ) {
				unset( $options[ $key ] );
			}
			if ( strpos( $key, 'token' ) > 0 ) {
				unset( $options[ $key ] );
			}
			if ( strpos( $key, 'login' ) > 0 ) {
				unset( $options[ $key ] );
			}
		}

		return $options;
	}

	public function print_post_meta() {
		global $post;

		if ( ! is_object( $post ) ) :
			wp_die( 'There is no post object for this url.' );
		endif;

		$meta = get_post_meta( $post->ID );

		if ( ! $meta ) {
			$meta = array();
		} else {
			$keys     = array();
			$swp_meta = array();

			foreach ( $meta as $key => $value ) {
				if ( ( strpos( $key, 'swp_' ) === 0
					|| ( strpos( $key, '_shares' ) > 0 ) && strpos( $key, '_' ) === 0 ) ) {
					//* Everything comes in as an array, pull out the first value.
					$meta[ $key ] = $value[0];
				} else {
					//* Only print Social Warfare meta keys.
					unset( $meta[ $key ] );
				}
			}
			ksort( $meta );
		}

		$post_fields = array( 'author', 'date_gmt', 'title', 'excerpt', 'status', 'type' );

		foreach ( $post_fields as $field ) {
			$key                   = "post_$field";
			$meta[ "post_$field" ] = $post->$key;
		}

		$meta['\$post->ID'] = $post->ID;

		$meta['post_permalink'] = get_permalink( $post->ID );

		echo '<pre>', var_export( $meta ), '</pre>';
		wp_die();
	}

	/**
	 *
	 * This is a patch.
	 *
	 * Sets the value of all Pages' post_meta `swp_float_location` to 'default'.
	 *
	 * @since 3.4.2 | 12 DEC 2018 | Created
	 *
	 * @param string $post_type The type of posts you want to rest.
	 * @return void
	 *
	 */
	public function reset_post_meta_float_location( $post_type ) {
		global $wpdb;

		$posts = get_posts(
			array(
				'numberposts' => -1,
				'meta_key'    => 'swp_float_location',
				'meta_value'  => 'on',
				'post_type'   => $post_type,
			)
		);

		$count = 0;

		foreach ( $posts as $post ) {
			$changed = update_post_meta( $post->ID, 'swp_float_location', 'default' );
			if ( $changed ) {
				++$count;
			}
		}
		if ( $count ) {
			echo "Success! $count {$post_type}s updated.";
		} else {
			echo 'No matching posts were found to update.';
		}
		wp_die();
	}


	/**
	 * A method to allow for easier debugging of database migration functions.
	 *
	 * The following URL parameters may be used for debugging purposes:
	 *     ?swp_debug=get_user_options     | Outputs an array of user settings.
	 *     ?swp_debug=migrate_db           | Runs settings page db migrator.
	 *     ?swp_debug=initialize_db        | Runs the database initializer.
	 *     ?swp_debug=migrate_post_meta    | Migrates the post meta fields.
	 *     ?swp_debug=get_last_migrated    | Outputs the last_updated version number.
	 *     ?swp_debug=update_last_migrated | Updates the last_updated version number.
	 *
	 * @since  3.1.0 | 13 JUN 2018 | Created
	 * @param  void
	 * @return void
	 */
	public function debug_parameters() {
		global $post, $swp_user_options;

		// Output an array of user options if called via a debugging parameter.
		if ( true === SWP_Utility::debug( 'get_user_options' ) ) :
			$options = get_option( 'social_warfare_settings', array() );
			$options = SWP_Database_Migration::filter_options( $options );
			ksort( $options );
			echo '<pre>', var_export( $options ), '</pre>';
			wp_die();
		endif;

		// /**
		//  * Output text representation of array of user options if called via a debugging parameter.
		//  * Text is formatted for use with `eval`.
		//  *
		//  * @since 3.5.0 | 14 DEC 2018 | Created.
		//  */
		// if ( true === SWP_Utility::debug('get_user_options_raw') ) {
		//  $options = get_option( 'social_warfare_settings', array() );
		//  die(var_export('return ' . $options . ';'));
		// }

		if ( true === SWP_Utility::debug( 'get_filtered_options' ) ) :
			global $swp_user_options;
			echo '<pre>';
			var_export( SWP_Database_Migration::filter_options( $swp_user_options ) );
			echo '</pre>';
			wp_die();
		endif;

		if ( true === SWP_Utility::debug( 'get_post_meta' ) ) :

			add_action( 'template_redirect', array( $this, 'print_post_meta' ) );

		endif;

		/**
		 * v3.4.1 brought to our attention that the default value for
		 * post meta `swp_float_location` is 'on' instead of 'deafult'.
		 *
		 *This debug paramter has an optional paramter, `post_type`, which defaults to 'page'.
		 *
		 * @since 3.4.2
		 */
		if ( true === SWP_Utility::debug( 'reset_float_location' ) ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( 'You do not have authorization to view this page.' );
			}
			$post_type = isset( $_GET['post_type'] ) ? $_GET['post_type'] : 'page';
			$post_type = sanitize_text_field( $post_type );
			$this->reset_post_meta_float_location( $post_type );
		}

		// Migrate settings page if explicitly being called via a debugging parameter.
		if ( true === SWP_Utility::debug( 'migrate_db' ) ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( 'You do not have authorization to view this page.' );
			}
			$this->migrate();
		}

		// Initialize database if explicitly being called via a debugging parameter.
		if ( true === SWP_Utility::debug( 'initialize_db' ) ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( 'You do not have authorization to view this page.' );
			}
			$this->initialize_db();
		}

		// Update post meta if explicitly being called via a debugging parameter.
		if ( true === SWP_Utility::debug( 'migrate_post_meta' ) ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( 'You do not have authorization to view this page.' );
			}
			$this->update_post_meta();
			$this->update_hidden_post_meta();
		}

		// Output the last_migrated status if called via a debugging parameter.
		if ( true === SWP_Utility::debug( 'get_last_migrated' ) ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( 'You do not have authorization to view this page.' );
			}
			$this->get_last_migrated( true );
		}

		// Update the last migrated status if called via a debugging parameter.
		if ( true === SWP_Utility::debug( 'update_last_migrated' ) ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( 'You do not have authorization to view this page.' );
			}
			$this->update_last_migrated();
		}

		if ( true === SWP_Utility::debug( ( 'delete_plugin_data' ) ) ) {
			$password = isset( $_GET['swp_confirmation'] ) ? sanitize_text_field( urldecode( $_GET['swp_confirmation'] ) ) : '';
			$user     = wp_get_current_user();
			if ( ! current_user_can( 'manage_options' )
			|| false === wp_check_password( $password, $user->user_pass, $user->ID ) ) {
				wp_die( 'You do not have authorization to view this page.' );
			}
			global $wpdb;

			/**
			 * Looks for any post_meta keys that begin with `swp_` OR begin
			 * with `_` AND end with `_shares`. Note that the underscores are
			 * escaped, else they would be interpreted as wildcards.
			 *
			 */
			$query =
				"DELETE FROM {$wpdb->prefix}postmeta
				 WHERE meta_key LIKE '\_%\_shares'
				 OR meta_key LIKE 'swp\_%'";

			$message = '';

			$results = $wpdb->get_results( $query, ARRAY_N );
			if ( $results ) {
				$message .= 'Deleted plugin postmeta.<br/>';
			}

			$deleted = delete_option( 'social_warfare_settings' );
			if ( $deleted ) {
				$message .= 'Deleted plugin settings.<br/>';
			}

			$deleted = delete_option( 'swp_registered_options' );
			if ( $deleted ) {
				$message .= 'Deleted plugin metadata.<br/>';
			}

			if ( $message ) {
				$message .= 'All available Social Warfare and Social Warfare - Pro data has been deleted.';
				wp_die( $message );
			}

			wp_die( 'Sorry, there was an error processing the request. If you continue to get this message and need to delete all plugin data, please contact support at https://warfareplugins.com/submit-ticket' );
		}
	}

	/**
	 * Checks to see if Social Warfare < 3.0.0 options exist.
	 *
	 * If these options exist in the databse, we need to move them
	 * from "socialWarfareOptions" to "social_warfare_settings",
	 * then
	 *
	 * @since  3.0.0 | 01 MAY 2018 | Created the function
	 * @param  void
	 * @return bool True if migrated, else false.
	 *
	 */
	public function database_is_migrated() {
		$option = get_option( 'social_warfare_settings', false );
		return false !== $option;
	}


	/**
	* Checks to see if we have 3.0.0 settings installed or not.
	*
	* @since  3.0.0 | 01 MAY 2018 | Created the function
	* @param  void
	* @return bool True if the 3.0.0 array exists, otherwise false.
	*
	*/
	protected function has_3_0_0_settings() {

		//* Check to see if the 3.0.0 settings exist.
		$settings = get_option( 'social_warfare_settings', false );
		return is_array( $settings );
	}


	/**
	* Tries to get an option that uses the old post_meta keynames.
	*
	* @since  3.0.0 | 01 MAY 2018 | Created the function
	* @param  void
	* @return bool True if the old option still exists; false otherwise.
	*
	*/
	public function post_meta_is_migrated() {
		if ( $this->last_migrated !== $this->get_last_migrated() ) {
			return false;
		}

		//* Fetch posts with 2.3.5 metadata.
		$old_metadata = get_posts(
			array(
				'meta_key'    => 'nc_postLocation',
				'numberposts' => 1,
			)
		);

		return count( $old_metadata ) === 0;
	}

	/**
	 * A method for updating the post meta fields.
	 *
	 * @since  3.0.0  | 08 MAY 2018 | Created
	 * @since  3.1.0 | 13 JUN 2018 | Replaced array bracket notations.
	 * @param  void
	 * @return void
	 *
	 */
	public function update_hidden_post_meta() {
		global $wpdb;

		try {
			set_time_limit( 300 );
		} catch ( Exception $e ) {
			if ( function_exists( 'error_log' ) ) :
				error_log( $e->getMessage() );
			endif;
		}

		$hidden_map = array(
			'_linkedIn_shares'    => '_linkedin_shares',
			'bitly_link_linkedIn' => '_bitly_link_linked_in',
		);

		$query = '
			UPDATE ' . $wpdb->prefix . 'postmeta
			SET meta_key = %s
			WHERE meta_key = %s
		';

		foreach ( $hidden_map as $old_key => $new_key ) {
			//* Make replacements for the first kind of prefix.
			$q = $wpdb->prepare( $query, $new_key, $old_key );
			$wpdb->query( $q );
		}
	}


	/**
	* Replaces 2.3.5 camelCased keys with 3.0.0 standardized snake_cased keys.
	*
	* @since  3.0.0 | 01 MAY 2018 | Created the function
	* @since  3.0.6 | 14 MAY 2018 | Added time limit to prevent very large datasets from timing out.
	* @param  void
	* @return void
	*
	*/
	public function update_post_meta() {
		global $wpdb;

		set_time_limit( 300 );

		//* Notice there is no prefix on any of the indices.
		//* Old code has prefixed these with either "nc_" or "swp_".
		//* For simplicity's sake, we'll just check each for both.
		$metadata_map = array(
			'ogImage'                        => 'swp_og_image',
			'ogTitle'                        => 'swp_og_title',
			'pinterestImage'                 => 'swp_pinterest_image',
			'customTweet'                    => 'swp_custom_tweet',
			'postLocation'                   => 'swp_post_location',
			'floatLocation'                  => 'swp_float_location',
			'pinterestDescription'           => 'swp_pinterest_description',
			'twitterID'                      => 'swp_twitter_id',
			'ogDescription'                  => 'swp_og_description',
			'cache_timestamp'                => 'swp_cache_timestamp',
			'pin_browser_extension'          => 'swp_pin_browser_extension',
			'pin_browser_extension_location' => 'swp_pin_browser_extension_location',
			'pin_browser_extension_url'      => 'swp_pin_browser_extension_url',
			'totes'                          => 'total_shares',
		);

		$prefix1 = 'nc_';
		$prefix2 = 'swp_';

		$query = '
			UPDATE ' . $wpdb->prefix . 'postmeta
			SET meta_key = %s
			WHERE meta_key = %s
		';

		foreach ( $metadata_map as $old_key => $new_key ) {
			//* Make replacements for the first kind of prefix.
			$q1      = $wpdb->prepare( $query, $new_key, $prefix1 . $old_key );
			$results = $wpdb->query( $q1 );

			//* And make replacements for the second kind of prefix.
			$q2      = $wpdb->prepare( $query, $new_key, $prefix2 . $old_key );
			$results = $wpdb->query( $q2 );
		}
	}


	/**
	* Seeds the database with Social Warfare 3.0.0 default values.
	*
	* @since  3.0.0 | 01 MAY 2018 | Created the function
	* @param  void
	* @return void
	*
	*/
	public function initialize_database() {
		$defaults = array(
			'location_archive_categories'    => 'below',
			'location_home'                  => 'none',
			'location_post'                  => 'below',
			'location_page'                  => 'below',
			'float_location_post'            => 'on',
			'float_location_page'            => 'off',
			'total_shares'                   => true,
			'network_shares'                 => true,
			'twitter_id'                     => false,
			'swp_twitter_card'               => true,
			'button_shape'                   => 'flatFresh',
			'default_colors'                 => 'full_color',
			'single_colors'                  => 'full_color',
			'hover_colors'                   => 'full_color',
			'float_default_colors'           => 'full_color',
			'float_single_colors'            => 'full_color',
			'float_hover_colors'             => 'fullColor',
			'float_style_source'             => true,
			'float_size'                     => 1,
			'float_alignment'                => 'center',
			'button_size'                    => 1,
			'button_alignment'               => 'fullWidth',
			'transition'                     => 'slide',
			'float_screen_width'             => 1100,
			'ctt_theme'                      => 'style1',
			'ctt_css'                        => '',
			'twitter_shares'                 => false,
			'floating_panel'                 => true,
			'float_location'                 => 'bottom',
			'float_background_color'         => '#ffffff',
			'float_button_shape'             => 'default',
			'float_vertical'                 => 'center',
			'float_button_count'             => 5,
			'custom_color'                   => '#000000',
			'custom_color_outlines'          => '#000000',
			'float_custom_color'             => '#000000',
			'float_custom_color_outlines'    => '#000000',
			'recover_shares'                 => false,
			'recovery_format'                => 'unchanged',
			'recovery_protocol'              => 'unchanged',
			'recovery_prefix'                => 'unchanged',
			'decimals'                       => 0,
			'decimal_separator'              => 'period',
			'totals_alignment'               => 'total_sharesalt',
			'google_analytics'               => false,
			'bitly_authentication'           => false,
			'minimum_shares'                 => 0,
			'full_content'                   => false,
			'frame_buster'                   => false,
			'analytics_medium'               => 'social',
			'analytics_campaign'             => 'SocialWarfare',
			'swp_click_tracking'             => false,
			'order_of_icons_method'          => 'manual',
			'og_post'                        => 'article',
			'og_page'                        => 'article',
			'pinterest_image_location'       => 'hidden',
			'pin_browser_extension'          => false,
			'pinterest_fallback'             => 'all',
			'pinit_toggle'                   => false,
			'pinit_location_horizontal'      => 'center',
			'pinit_location_vertical'        => 'top',
			'pinit_min_width'                => '200',
			'pinit_min_height'               => '200',
			'pinit_image_source'             => 'image',
			'pinit_image_description'        => 'alt_text',
			'utm_on_pins'                    => false,
			'pin_browser_extension'          => false,
			'pin_browser_extension_location' => 'hidden',
			'pinterest_fallback'             => 'all',
			'float_mobile'                   => 'bottom',
			'force_new_shares'               => false,
			'cache_method'                   => 'advanced',
			'order_of_icons'                 => array(
				'twitter'   => 'twitter',
				'linkedIn'  => 'linkedin',
				'pinterest' => 'pinterest',
				'facebook'  => 'facebook',
			),
		);

		update_option( 'social_warfare_settings', $defaults );
	}


	/**
	 * Map prevous key/value pairs to new keys.
	 *
	 * This also deletes the previous keys once the migration is done.
	 * @since  3.0.0  | 01 MAY 2018 | Created the function
	 * @since  3.1.0 | 13 JUN 2018 | Replaced array bracket notation.
	 * @since  4.4.5 | 08 JAN 2014 | Removed Google Plus
	 * @param  void
	 * @return void
	 *
	 */
	private function migrate() {
		$options = get_option( 'socialWarfareOptions', array() );

		if ( array() === $options ) :
			//* The old options do not exist.
			return;
		endif;

		$map = array(
			//* Options names
			'locationSite'                      => 'location_archive_categories',
			'locationHome'                      => 'location_home',
			'totesEach'                         => 'network_shares',
			'totes'                             => 'total_shares',
			'minTotes'                          => 'minimum_shares',
			'visualTheme'                       => 'button_shape',
			'buttonSize'                        => 'button_size',
			'dColorSet'                         => 'default_colors',
			'oColorSet'                         => 'hover_colors',
			'iColorSet'                         => 'single_colors',
			'swDecimals'                        => 'decimals',
			'swp_decimal_separator'             => 'decimal_separator',
			'swTotesFormat'                     => 'totals_alignment',
			'float'                             => 'floating_panel',
			'floatOption'                       => 'float_location',
			'swp_float_scr_sz'                  => 'float_screen_width',
			'sideReveal'                        => 'transition',
			'floatStyle'                        => 'float_button_shape',
			'floatStyleSource'                  => 'float_style_source',
			'sideDColorSet'                     => 'float_default_colors',
			'sideOColorSet'                     => 'float_hover_colors',
			'sideIColorSet'                     => 'float_single_colors',
			'swp_twitter_card'                  => 'twitter_cards',
			'twitterID'                         => 'twitter_id',
			'pinterestID'                       => 'pinterest_id',
			'facebookPublisherUrl'              => 'facebook_publisher_url',
			'facebookAppID'                     => 'facebook_app_id',
			'sniplyBuster'                      => 'frame_buster',
			'linkShortening'                    => 'bitly_authentication',
			'cacheMethod'                       => 'cache_method',
			'googleAnalytics'                   => 'google_analytics',
			'analyticsMedium'                   => 'analytics_medium',
			'analyticsCampaign'                 => 'analytics_campaign',
			'advanced_pinterest_image'          => 'pin_browser_extension',
			'advanced_pinterest_image_location' => 'pinterest_image_location',
			'pin_browser_extension_location'    => 'pin_browser_extension_location',
			'advanced_pinterest_fallback'       => 'pinterest_fallback',
			'recovery_custom_format'            => 'recovery_permalink',
			'cttTheme'                          => 'ctt_theme',
			'cttCSS'                            => 'ctt_css',
			'sideCustomColor'                   => 'single_custom_color',
			'floatBgColor'                      => 'float_background_color',
			'orderOfIconsSelect'                => 'order_of_icons_method',
			'newOrderOfIcons'                   => 'order_of_icons',
		);

		$value_map = array(
			'flatFresh'       => 'flat_fresh',
			'threeDee'        => 'three_dee',
			'fullColor'       => 'full_color',
			'lightGray'       => 'light_gray',
			'mediumGray'      => 'medium_gray',
			'darkGray'        => 'dark_gray',
			'lgOutlines'      => 'light_gray_outlines',
			'mdOutlines'      => 'medium_gray_outlines',
			'dgOutlines'      => 'dark_gray_outlines',
			'colorOutlines'   => 'color_outlines',
			'customColor'     => 'custom_color',
			'ccOutlines'      => 'custom_color_outlines',
			'totesAlt'        => 'totals_right',
			'totesAltLeft'    => 'totals_left',
			'buttonFloat'     => 'button_alignment',
			'post'            => 'location_post',
			'page'            => 'location_page',
			'float_vertical'  => 'float_alignment',
			'fullWidth'       => 'full_width',
			'floatLeftMobile' => 'float_mobile',
		);

		$migrations = array();

		foreach ( $options as $old => $value ) {
			//* The order of icons used to be stored in an array at 'active'.
			if ( is_array( $value ) && array_key_exists( 'active', $value ) ) :
				$new_value = $value;
				//* Filter out the booleans and integers.
			elseif ( is_string( $value ) ) :
				$new_value = array_key_exists( $value, $value_map ) ? $value_map[ $value ] : $value;
			else :
				$new_value = $value;
			endif;

			//* Specific case: newOrderOfIcons mapping.
			if ( 'newOrderOfIcons' === $old ) :
				if ( array_key_exists( 'linkedIn', $new_value ) ) :
					unset( $new_value['linkedIn'] );
					$new_value[] = 'linkedin';
				endif;
			endif;

			//* Specific case: customColor mapping.
			if ( 'customColor' === $old ) :
				$migrations['custom_color']          = $new_value;
				$migrations['custom_color_outlines'] = $new_value;

						// If the float style source is set to inherit the style from the static buttons.
				if ( true === $options['floatStyleSource'] ) :
					$migrations['float_custom_color']          = $new_value;
					$migrations['float_custom_color_outlines'] = $new_value;
				endif;
			endif;

			// Only if the source is set to not inherit them from the static buttons.
			if ( 'sideCustomColor' === $old ) :
				$migrations['float_custom_color']          = $new_value;
				$migrations['float_custom_color_outlines'] = $new_value;
			endif;

			if ( array_key_exists( $old, $map ) ) :
				//* We specified an update to the key.
				$new                = $map[ $old ];
				$migrations[ $new ] = $new_value;
			else :
				//* The previous key was fine, keep it.
				$migrations[ $old ] = $new_value;
			endif;

		}

		//* Manually adding these in as short term solution.
		if ( ! isset( $migrations['float_size'] ) ) :
			$migrations['float_size'] = '1';
		endif;

		if ( ! isset( $migrations['float_location'] ) ) :
			$migrations['float_location'] = 'bottom';
		endif;

		if ( ! isset( $migrations['float_alignment'] ) ) :
			$migrations['float_alignment'] = 'center';
		endif;

		$custom_colors = array( 'custom_color', 'custom_color_outlines', 'float_custom_color', 'float_custom_color_outlines' );

		foreach ( $custom_colors as $color ) {
			if ( ! isset( $migrations[ $color ] ) ) :
				$migrations[ $color ] = '#333333';
			endif;
		}

		$removals = array(
			'dashboardShares',
			'rawNumbers',
			'notShowing',
			'visualEditorBug',
			'loopFix',
			'locationrevision',
			'locationattachment',
		);

		foreach ( $removals as $trash ) :
			if ( ( $migrations[ $trash ] ) ) :
				unset( $migrations[ $trash ] );
			endif;
		endforeach;

		update_option( 'social_warfare_settings', $migrations );
		//* Play it safe for now.
		//* Leave socialWarfareOptions in the database.
		// delete_option( 'socialWarfareOptions' );
	}


	/**
	 * Get Last Migrated
	 *
	 * This method gets the version number during which the last migration was
	 * run. This allows us to increment a version if we need a part of this class
	 * to run again.
	 *
	 * @since  3.0.0 | Created | 08 MAY 2018
	 * @param  boolean $output True echoes the data; False returns it.
	 * @return mixed         (str) Version number if found, (bool) false if not found.
	 *
	 */
	public function get_last_migrated( $output = false ) {
		$options = get_option( 'social_warfare_settings' );

		if ( array_key_exists( 'last_migrated', $options ) ) :
			if ( true === $output ) :
				var_dump( $options['last_migrated'] );
			endif;

			return $options['last_migrated'];
		endif;

		if ( true === $output ) :
			echo 'No previous migration version has been set.';
		endif;

		return false;
	}


	/**
	 * A method to update the last migrated version number.
	 *
	 * @since  3.0.0 | Created | 08 MAY 2018
	 * @param  null
	 * @return void
	 *
	 */
	public function update_last_migrated() {
		$options                  = get_option( 'social_warfare_settings' );
		$options['last_migrated'] = $this->last_migrated;

		update_option( 'social_warfare_settings', $options );
	}

	/**
	 * Checks if there are more than one post meta entries with meta_key '_facebook_shares'.
	 *
	 * @since 4.4.4.1 | 18 MAR 2024 | Added method to check _facebook_shares meta entries.
	 * @return bool True if more than one entry exists, false otherwise.
	 */
	public function is_facebook_shares_migrated() {
		global $wpdb;

		$meta_key = '_facebook_shares';
		$sql      = $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key = %s", $meta_key );
		$count    = $wpdb->get_var( $sql );

		return ( $count > 1 );
	}

	/**
	 * Deletes all post_meta entries where meta_key equals '_facebook_shares'.
	 *
	 * @since 4.4.6.1 | 18 MAR 2024 | Added method to remove _facebook_shares meta.
	 * @return void
	 */
	public function delete_facebook_shares_meta() {
		global $wpdb;

		$meta_key = '_facebook_shares';
		$sql      = $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s", $meta_key );

		$wpdb->query( $sql );

		$rows_affected = $wpdb->rows_affected;
		error_log( "Deleted $rows_affected rows from postmeta where meta_key is '_facebook_shares'." );
	}
}

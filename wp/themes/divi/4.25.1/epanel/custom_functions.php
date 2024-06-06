<?php

// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

add_theme_support( 'custom-background', apply_filters( 'et_custom_background_args', array() ) );

if ( function_exists( 'add_post_type_support' ) ) {
	add_post_type_support( 'page', 'excerpt' );
}

add_theme_support( 'automatic-feed-links' );

add_action( 'init', 'et_activate_features' );

function et_activate_features() {
	if ( ! defined( 'ET_SHORTCODES_VERSION' ) ) {
		define( 'ET_SHORTCODES_VERSION', et_get_theme_version() );
	}

	if ( ! defined( 'ET_SHORTCODES_DIR' ) ) {
		define( 'ET_SHORTCODES_DIR', get_template_directory_uri() . '/epanel/shortcodes' );
	}

	/* activate shortcodes */
	require_once get_template_directory() . '/epanel/shortcodes/shortcodes.php';

	/* activate page templates */
	require_once get_template_directory() . '/includes/page_templates/page_templates.php';

	/* import epanel settings */
	require_once get_template_directory() . '/includes/import_settings.php';
}

add_filter( 'widget_text', 'do_shortcode' );
add_filter( 'the_excerpt', 'do_shortcode' );

if ( ! function_exists( 'et_get_theme_version' ) ) :
function et_get_theme_version() {
	$theme_info = wp_get_theme();

	if ( is_child_theme() ) {
		$theme_info = wp_get_theme( $theme_info->parent_theme );
	}

	$theme_version = $theme_info->display( 'Version' );

	return $theme_version;
}
endif;

if ( ! function_exists( 'et_options_stored_in_one_row' ) ) {

	function et_options_stored_in_one_row(){
		global $et_store_options_in_one_row;

		return isset( $et_store_options_in_one_row ) ? (bool) $et_store_options_in_one_row : false;
	}

}

/* sync custom CSS from ePanel with WP custom CSS option introduced in WP 4.7 */
if ( ! function_exists( 'et_sync_custom_css_options' ) ) {
	function et_sync_custom_css_options() {
		global $shortname;

		$legacy_custom_css = wp_unslash( et_get_option( "{$shortname}_custom_css" ) );

		// nothing to sync if no custom css saved in ePanel
		if ( '' === $legacy_custom_css || ! $legacy_custom_css || empty( $legacy_custom_css ) ) {
			set_theme_mod( 'et_pb_css_synced', 'yes' );
			return;
		}

		// don't proceed with the sync logic if the custom CSS option does not exist
		if ( ! function_exists( 'wp_get_custom_css' ) ) {
			return;
		}

		$css_synced = get_theme_mod( 'et_pb_css_synced', 'no' );

		// get custom css string from WP customizer
		$wp_custom_css = wp_get_custom_css();

		// force sync if the current custom CSS is empty
		if ( 'yes' === $css_synced && '' !== $wp_custom_css ) {
			return;
		}

		// ePanel is completely synced with Customizer
		if ( $wp_custom_css === $legacy_custom_css || false !== strpos( $wp_custom_css, $legacy_custom_css ) ) {
			set_theme_mod( 'et_pb_css_synced', 'yes' );
			return;
		}

		// merge custom css from WP customizer with ePanel custom css
		$updated_custom_css = $legacy_custom_css . ' ' . $wp_custom_css;

		$updated_status = wp_update_custom_css_post( $updated_custom_css );

		// set theme mod in case of success
		if ( is_object( $updated_status ) && ! empty( $updated_status ) ) {
			set_theme_mod( 'et_pb_css_synced', 'yes' );
		}
	}
}
add_action( 'init', 'et_sync_custom_css_options' );

/**
 * sync custom CSS from WP custom CSS option introduced in WP 4.7 with theme options for backward compatibility
 * it should be removed after a few WP major updates when we fully migrate to WP custom CSS system
 */
if ( ! function_exists( 'et_back_sync_custom_css_options' ) ) {
	function et_back_sync_custom_css_options( $data ) {
		global $shortname;

		if ( ! empty( $data ) && isset( $data['css'] ) ) {
			et_update_option( "{$shortname}_custom_css", $data['css'] );
		}

		return $data;
	}
}

add_filter( 'update_custom_css_data', 'et_back_sync_custom_css_options' );

if ( ! function_exists( 'et_update_custom_css_data_cb' ) ):
function et_update_custom_css_data_cb( $data ) {

	ET_Core_PageResource::remove_static_resources( 'all', 'all' );

	return $data;
}
add_filter( 'update_custom_css_data', 'et_update_custom_css_data_cb' );
endif;

if ( ! function_exists( 'et_epanel_handle_custom_css_output' ) ):
function et_epanel_handle_custom_css_output( $css, $stylesheet ) {
	global $wp_current_filter, $shortname;

	/** @see ET_Core_SupportCenter::toggle_safe_mode */
	if ( et_core_is_safe_mode_active() ) {
		return $css;
	}

	if ( ! $css || ! in_array( 'wp_head', $wp_current_filter ) || is_admin() && ! is_customize_preview() ) {
		return $css;
	}

	$post_id        = et_core_page_resource_get_the_ID();
	$is_preview     = is_preview() || isset( $_GET['et_pb_preview_nonce'] ) || is_customize_preview(); // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
	$is_singular    = et_core_page_resource_is_singular();

	$disabled_global = 'off' === et_get_option( 'et_pb_static_css_file', 'on' );
	$disabled_post   = $disabled_global || ( $is_singular && 'off' === get_post_meta( $post_id, '_et_pb_static_css_file', true ) );

	$forced_inline     = $is_preview || $disabled_global || $disabled_post || post_password_required();
	$builder_in_footer = 'on' === et_get_option( 'et_pb_css_in_footer', 'off' );

	$unified_styles = $is_singular && ! $forced_inline && ! $builder_in_footer && et_core_is_builder_used_on_current_request();
	$resource_owner = $unified_styles ? 'core' : $shortname;
	$resource_slug  = $unified_styles ? 'unified' : 'customizer';

	if ( $is_preview ) {
		// Don't let previews cause existing saved static css files to be modified.
		$resource_slug .= '-preview';
	}

	if ( function_exists( 'et_fb_is_enabled' ) && et_fb_is_enabled() ) {
		$resource_slug .= '-vb';
	}

	if ( ! $unified_styles ) {
		$post_id = 'global';
	}

	$styles_manager = et_core_page_resource_get( $resource_owner, $resource_slug, $post_id, 30 );

	if ( $styles_manager->forced_inline || ! $styles_manager->has_file() ) {
		$styles_manager->set_data( $css, 30 );
	}

	return ''; // We're handling the custom CSS output ourselves.
}
add_filter( 'wp_get_custom_css', 'et_epanel_handle_custom_css_output', 999, 2 );
endif;

if ( ! function_exists( 'et_get_option' ) ) {
	/**
	 * Gets option value from the single theme option, stored as an array in the database
	 * if all options stored in one row.
	 * Stores the serialized array with theme options into the global variable on the first function run on the page.
	 *
	 * If options are stored as separate rows in database, it simply uses get_option() function.
	 *
	 * @param string $option_name Theme option name.
	 * @param string $default_value Default value that should be set if the theme option isn't set.
	 * @param string $used_for_object "Object" name that should be translated into corresponding "object" if WPML is activated.
	 * @param bool   $force_default_value Is return provided default.
	 * @param bool   $is_global_setting Is Global Setting.
	 * @param string $global_setting_main_name Global Setting name.
	 * @param string $global_setting_sub_name Global Setting sub name.
	 * @param bool   $is_product_setting Product setting flag.
	 * @return mixed Theme option value or false if not found.
	 */
	function et_get_option( $option_name, $default_value = '', $used_for_object = '', $force_default_value = false, $is_global_setting = false, $global_setting_main_name = '', $global_setting_sub_name = '', $is_product_setting = false ) {
		global $et_theme_options, $shortname;

		$et_one_row_option_name = '';

		if ( $is_global_setting ) {
			$option_value = '';

			$et_global_setting = get_option( $global_setting_main_name );

			if ( false !== $et_global_setting && isset( $et_global_setting[ $global_setting_sub_name ] ) ) {
				$option_value = $et_global_setting[ $global_setting_sub_name ];
			}
		} elseif ( $is_product_setting ) {
			$et_product_setting_name = 'et_' . $shortname . '_' . $option_name;

			$option_value = $force_default_value ? get_option( $et_product_setting_name, $default_value ) : get_option( $et_product_setting_name );
		} elseif ( et_options_stored_in_one_row() ) {
			$et_theme_options_name = 'et_' . $shortname;

			if ( ! isset( $et_theme_options ) || is_customize_preview() ) {
				$et_theme_options = get_option( $et_theme_options_name );
			}
			$option_value = isset( $et_theme_options[$option_name] ) ? $et_theme_options[$option_name] : false;

			$et_one_row_option_name = $et_theme_options_name . '_' . $option_name;
		} else {
			$option_value = $force_default_value ? get_option( $option_name, $default_value ) : get_option( $option_name );
		}

		// option value might be equal to false, so check if the option is not set in the database
		if ( et_options_stored_in_one_row() && ! $is_product_setting && ! isset( $et_theme_options[ $option_name ] ) && ( ! empty( $default_value ) || $force_default_value ) ) {
			$option_value = $default_value;
		}

		if ( ! empty( $used_for_object ) && in_array( $used_for_object, array( 'page', 'category' ) ) && is_array( $option_value ) )
			$option_value = et_generate_wpml_ids( $option_value, $used_for_object );

		if ( ! empty( $et_one_row_option_name ) ) {
			$option_value = apply_filters( 'et_get_option_' . $et_one_row_option_name, $option_value, $et_one_row_option_name );
		}

		return $option_value;
	}

}

if ( ! function_exists( 'et_update_option' ) ) {
	/**
	 * Update option value in theme option, stored as an array in the database
	 * if all options stored in one row.
	 *
	 * If options are stored as separate rows in database, it simply uses update_option() function.
	 *
	 * @param string $option_name Theme option name.
	 * @param string $new_value Theme option value.
	 * @param bool   $is_new_global_setting Global setting flag.
	 * @param string $global_setting_main_name Global setting name.
	 * @param string $global_setting_sub_name Global setting sub name.
	 * @param bool   $is_product_setting Product setting flag.
	 */
	function et_update_option( $option_name, $new_value, $is_new_global_setting = false, $global_setting_main_name = '', $global_setting_sub_name = '', $is_product_setting = false ) {
		global $et_theme_options, $shortname;

		if ( $is_new_global_setting && '' !== $global_setting_main_name && '' !== $global_setting_sub_name ) {
			$global_setting = get_option( $global_setting_main_name, array() );

			// $global_setting has to be array otherwise setting can't be saved so it needs
			// to be treated as empty array
			if ( ! is_array( $global_setting ) ) {
				$global_setting = array();
			}

			$global_setting[ $global_setting_sub_name ] = $new_value;

			update_option( $global_setting_main_name, $global_setting );

		} elseif ( $is_product_setting ) {
			$et_product_setting_name = 'et_' . $shortname . '_' . $option_name;

			// Update option and disable autoload of this option.
			update_option( $et_product_setting_name, $new_value, false );
		} elseif ( et_options_stored_in_one_row() ) {
			$et_theme_options_name = 'et_' . $shortname;

			if ( ! isset( $et_theme_options ) || is_customize_preview() ) {
				$et_theme_options = get_option( $et_theme_options_name );
			}
			$et_theme_options[ $option_name ] = $new_value;

			update_option( $et_theme_options_name, $et_theme_options );

		} else {
			update_option( $option_name, $new_value );
		}
	}

}

if ( ! function_exists( 'et_delete_option' ) ) {

	function et_delete_option( $option_name ){
		global $et_theme_options, $shortname;

		if ( et_options_stored_in_one_row() ) {
			$et_theme_options_name = 'et_' . $shortname;

			if ( ! isset( $et_theme_options ) ) $et_theme_options = get_option( $et_theme_options_name );

			unset( $et_theme_options[$option_name] );
			update_option( $et_theme_options_name, $et_theme_options );
		} else {
			delete_option( $option_name );
		}
	}

}

/*this function allows for the auto-creation of post excerpts*/
if ( ! function_exists( 'truncate_post' ) ) {
	/**
	 * Truncate post content to generate post excerpt.
	 *
	 * @since ?? Add new paramter $is_words_length to cut the text based on words length.
	 *
	 * @param integer $amount           Amount of text that should be kept.
	 * @param boolean $echo             Whether to print the output or not.
	 * @param object  $post             Post object.
	 * @param boolean $strip_shortcodes Whether to strip the shortcodes or not.
	 * @param boolean $is_words_length  Whether to cut the text based on words length or not.
	 *
	 * @return string Generated post post excerpt.
	 */
	function truncate_post( $amount, $echo = true, $post = '', $strip_shortcodes = false, $is_words_length = false ) {
		global $shortname;

		if ( empty( $post ) ) global $post;

		if ( post_password_required( $post ) ) {
			$post_excerpt = get_the_password_form();

			if ( $echo ) {
				echo et_core_intentionally_unescaped( $post_excerpt, 'html' );
				return;
			}

			return $post_excerpt;
		}

		$post_excerpt = apply_filters( 'the_excerpt', $post->post_excerpt );

		if ( 'on' === et_get_option( $shortname . '_use_excerpt' ) && ! empty( $post_excerpt ) ) {
			if ( $echo ) {
				echo et_core_intentionally_unescaped( $post_excerpt, 'html' );
			} else {
				return $post_excerpt;
			}
		} else {
			// get the post content
			$truncate = $post->post_content;

			// remove caption shortcode from the post content
			$truncate = preg_replace( '@\[caption[^\]]*?\].*?\[\/caption]@si', '', $truncate );

			// remove post nav shortcode from the post content
			$truncate = preg_replace( '@\[et_pb_post_nav[^\]]*?\].*?\[\/et_pb_post_nav]@si', '', $truncate );

			// Remove audio shortcode from post content to prevent unwanted audio file on the excerpt
			// due to unparsed audio shortcode
			$truncate = preg_replace( '@\[audio[^\]]*?\].*?\[\/audio]@si', '', $truncate );

			// Remove embed shortcode from post content
			$truncate = preg_replace( '@\[embed[^\]]*?\].*?\[\/embed]@si', '', $truncate );

			// Remove script and style tags from the post content
			$truncate = wp_strip_all_tags( $truncate );

			if ( $strip_shortcodes ) {
				$truncate = et_strip_shortcodes( $truncate );
				$truncate = et_builder_strip_dynamic_content( $truncate );
			} else {
				// Check if content should be overridden with a custom value.
				$custom = apply_filters( 'et_truncate_post_use_custom_content', false, $truncate, $post );
				// apply content filters
				$truncate = false === $custom ? apply_filters( 'the_content', $truncate ) : $custom;
			}

			/**
			 * Filter automatically generated post excerpt before it gets truncated.
			 *
			 * @since 3.17.2
			 *
			 * @param string $excerpt
			 * @param integer $post_id
			 */
			$truncate = apply_filters( 'et_truncate_post', $truncate, $post->ID );

			// decide if we need to append dots at the end of the string
			if ( strlen( $truncate ) <= $amount ) {
				$echo_out = '';
			} else {
				$echo_out = '...';
				// $amount = $amount - 3;
			}

			$trim_words = '';

			if ( $is_words_length ) {
				// Reset `$echo_out` text because it will be added by wp_trim_words() with
				// default WordPress `excerpt_more` text.
				$echo_out     = '';
				$excerpt_more = apply_filters( 'excerpt_more', ' [&hellip;]' );
				$trim_words   = wp_trim_words( $truncate, $amount, $excerpt_more );
			} else {
				$trim_words = et_wp_trim_words( $truncate, $amount, '' );
			}

			// trim text to a certain number of characters, also remove spaces from the end of a string ( space counts as a character ).
			$truncate = rtrim( $trim_words );

			// remove the last word to make sure we display all words correctly
			if ( ! empty( $echo_out ) ) {
				$new_words_array = (array) explode( ' ', $truncate );
				// Remove last word if word count is more than 1.
				if ( count( $new_words_array ) > 1 ) {
					array_pop( $new_words_array );
				}

				$truncate = implode( ' ', $new_words_array );

				// Dots should not add to empty string
				if ( '' !== $truncate ) {
					// append dots to the end of the string
					$truncate .= $echo_out;
				}
			}

			if ( $echo ) {
				echo et_core_intentionally_unescaped( $truncate, 'html' );
			} else {
				return $truncate;
			}
		};
	}

}

if ( ! function_exists( 'et_wp_trim_words' ) ) {

	function et_wp_trim_words( $text, $num_words = 55, $more = null ) {
		if ( null === $more )
			$more = esc_html__( '&hellip;' );
		// Completely remove icons so that unicode hex entities representing the icons do not get included in words.
		$text = preg_replace( '/<span class="et-pb-icon .*<\/span>/', '', $text );
		$text = wp_strip_all_tags( $text );

		$text = trim( preg_replace( "/[\n\r\t ]+/", ' ', $text ), ' ' );
		preg_match_all( '/./u', $text, $words_array );
		$words_array = array_slice( $words_array[0], 0, $num_words + 1 );
		$sep = '';

		if ( count( $words_array ) > $num_words ) {
			array_pop( $words_array );
			$text = implode( $sep, $words_array );
			$text = $text . $more;
		} else {
			$text = implode( $sep, $words_array );
		}

		return $text;
	}

}

/*this function truncates titles to create preview excerpts*/
if ( ! function_exists( 'truncate_title' ) ) {

	function truncate_title( $amount, $echo = true, $post = '' ) {
		if ( empty( $post ) ) $truncate = get_the_title();
		else $truncate = $post->post_title;

		if ( strlen( $truncate ) <= $amount ) $echo_out = '';
		else $echo_out = '...';

		$truncate = et_wp_trim_words( $truncate, $amount, '' );

		if ( ! empty( $echo_out ) ) $truncate .= $echo_out;

		if ( $echo )
			echo et_core_intentionally_unescaped( $truncate, 'html' );
		else
			return $truncate;
	}

}


/*this function allows users to use the first image in their post as their thumbnail*/
if ( ! function_exists( 'et_first_image' ) ) {

	function et_first_image() {
		global $post;
		$img = '';

		if ( empty( $post->ID ) ) {
			return $img;
		}

		$unprocessed_content = $post->post_content;

		// truncate Post based shortcodes if Divi Builder enabled to avoid infinite loops
		if ( function_exists( 'et_strip_shortcodes' ) ) {
			$unprocessed_content = et_strip_shortcodes( $post->post_content, true );
		}

		// Check if content should be overridden with a custom value.
		$custom = apply_filters( 'et_first_image_use_custom_content', false, $unprocessed_content, $post );
		// apply the_content filter to execute all shortcodes and get the correct image from the processed content
		$processed_content = false === $custom ? apply_filters( 'the_content', $unprocessed_content ) : $custom;

		$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $processed_content, $matches );
		if ( isset( $matches[1][0] ) ) $img = $matches[1][0];

		return trim( $img );
	}

}


/* this function gets thumbnail from Post Thumbnail or Custom field or First post image */
if ( ! function_exists( 'get_thumbnail' ) ) {

	function get_thumbnail($width=100, $height=100, $class='', $alttext='', $titletext='', $fullpath=false, $custom_field='', $post='') {
		if ( empty( $post ) ) global $post;
		global $shortname;

		$thumb_array['thumb'] = '';
		$thumb_array['use_timthumb'] = true;
		if ($fullpath) $thumb_array['fullpath'] = ''; //full image url for lightbox

		$new_method = true;

		if ( has_post_thumbnail( $post->ID ) || 'attachment' === $post->post_type ) {
			$thumb_array['use_timthumb'] = false;

			$et_fullpath = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );

			if ( is_array( $et_fullpath ) ) {
				$thumb_array['fullpath'] = $et_fullpath[0];
				$thumb_array['thumb'] = $thumb_array['fullpath'];
			}
		}

		if ( empty( $thumb_array['thumb'] ) ) {
			if ( empty( $custom_field ) ) $thumb_array['thumb'] = esc_attr( get_post_meta( $post->ID, 'Thumbnail', $single = true ) );
			else {
				$thumb_array['thumb'] = esc_attr( get_post_meta( $post->ID, $custom_field, $single = true ) );
				if ( empty( $thumb_array['thumb'] ) ) $thumb_array['thumb'] = esc_attr( get_post_meta( $post->ID, 'Thumbnail', $single = true ) );
			}

			if ( '' === $thumb_array['thumb'] && et_grab_image_setting() ) {
				$thumb_array['thumb'] = esc_attr( et_first_image() );
				if ( $fullpath ) $thumb_array['fullpath'] = $thumb_array['thumb'];
			}

			#if custom field used for small pre-cropped image, open Thumbnail custom field image in lightbox
			if ($fullpath) {
				$thumb_array['fullpath'] = $thumb_array['thumb'];
				if ( empty( $custom_field ) ) $thumb_array['fullpath'] = apply_filters( 'et_fullpath', et_path_reltoabs( esc_attr( $thumb_array['thumb'] ) ) );
				elseif ( ! empty( $custom_field ) && get_post_meta( $post->ID, 'Thumbnail', $single = true ) ) $thumb_array['fullpath'] = apply_filters( 'et_fullpath', et_path_reltoabs( esc_attr( get_post_meta( $post->ID, 'Thumbnail', $single = true ) ) ) );
			}
		}

		return $thumb_array;
	}

}

if ( ! function_exists( 'et_grab_image_setting' ) ) :
/**
 * Filterable "Grab the first post image" setting.
 * "Grab the first post image" needs to be filterable so it can be disabled forcefully.
 * It uses et_first_image() which uses apply_filters( 'the_content' ) which could cause
 * a conflict with third party plugin which extensively uses 'the_content' filter (ie. BuddyPress)
 * @return bool
 */
function et_grab_image_setting() {
	global $shortname;

	// Force disable "Grab the first post image" in BuddyPress component page
	$is_buddypress_component = function_exists( 'bp_current_component' ) && bp_current_component();

	$setting = 'on' === et_get_option( "{$shortname}_grab_image" ) && ! $is_buddypress_component;

	return apply_filters( 'et_grab_image_setting', $setting );
}
endif;

/* this function prints thumbnail from Post Thumbnail or Custom field or First post image */
if ( ! function_exists( 'print_thumbnail' ) ) {

	function print_thumbnail($thumbnail = '', $use_timthumb = true, $alttext = '', $width = 100, $height = 100, $class = '', $echoout = true, $forstyle = false, $resize = true, $post='', $et_post_id = '' ) {
		if ( is_array( $thumbnail ) ) {
			extract( $thumbnail );
		}

		if ( empty( $post ) ) global $post, $et_theme_image_sizes;

		$output         = '';
		$raw            = false;
		$thumbnail_orig = $thumbnail;

		$et_post_id = ! empty( $et_post_id ) ? (int) $et_post_id : $post->ID;

		if ( has_post_thumbnail( $et_post_id ) ) {
			$thumb_array['use_timthumb'] = false;

			$image_size_name = $width . 'x' . $height;
			$et_size = isset( $et_theme_image_sizes ) && array_key_exists( $image_size_name, $et_theme_image_sizes ) ? $et_theme_image_sizes[$image_size_name] : array( $width, $height );

			$et_attachment_image_attributes = wp_get_attachment_image_src( get_post_thumbnail_id( $et_post_id ), $et_size );
			$thumbnail = $et_attachment_image_attributes[0];
		} else {
			$thumbnail = et_multisite_thumbnail( $thumbnail );

			$cropPosition = '';

			$allow_new_thumb_method = false;

			$new_method = true;
			$new_method_thumb = '';
			$external_source = false;

			$allow_new_thumb_method = !$external_source && $new_method && empty( $cropPosition );

			if ( $allow_new_thumb_method && !empty( $thumbnail ) ) {
				if ( 'data:image' === substr( $thumbnail, 0, 10 ) ) {
					$new_method_thumb = $thumbnail;
					$raw              = true;
				} else {
					$et_crop          = get_post_meta( $post->ID, 'et_nocrop', true );
					$et_crop          = empty( $et_crop ) ? true : false;
					$new_method_thumb = et_resize_image( et_path_reltoabs( $thumbnail ), $width, $height, $et_crop );
					if ( is_wp_error( $new_method_thumb ) ) {
						$new_method_thumb = '';
					}
				}
			}

			$thumbnail = $new_method_thumb;
		}

		if ( false === $forstyle && $resize ) {
			if ( $width < 480 && et_is_responsive_images_enabled() && ! $raw ) {
				$output = sprintf(
					'<img src="%1$s" alt="%2$s" class="%3$s" srcset="%4$s " sizes="%5$s " %6$s />',
					esc_url( $thumbnail ),
					esc_attr( wp_strip_all_tags( $alttext ) ),
					empty( $class ) ? '' : esc_attr( $class ),
					$thumbnail_orig . ' 479w, ' . $thumbnail . ' 480w',
					'(max-width:479px) 479px, 100vw',
					apply_filters( 'et_print_thumbnail_dimensions', ' width="' . esc_attr( $width ) . '" height="' . esc_attr( $height ) . '"' )
				);
			} else {
				$output = sprintf(
					'<img src="%1$s" alt="%2$s" class="%3$s"%4$s />',
					$raw ? $thumbnail : esc_url( $thumbnail ),
					esc_attr( wp_strip_all_tags( $alttext ) ),
					empty( $class ) ? '' : esc_attr( $class ),
					apply_filters( 'et_print_thumbnail_dimensions', ' width="' . esc_attr( $width ) . '" height="' . esc_attr( $height ) . '"' )
				);

				if ( ! $raw ) {
					$output = et_image_add_srcset_and_sizes( $output );
				}
			}
		} else {
			$output = $thumbnail;
		}

		if ($echoout) echo et_core_intentionally_unescaped( $output, 'html' );
		else return $output;
	}

}

if ( ! function_exists( 'et_new_thumb_resize' ) ) {

	function et_new_thumb_resize( $thumbnail, $width, $height, $alt='', $forstyle = false ){
		global $shortname;

		$new_method = true;
		$new_method_thumb = '';
		$external_source = false;

		$allow_new_thumb_method = !$external_source && $new_method;

		if ( $allow_new_thumb_method && ! empty( $thumbnail ) ) {
			$et_crop = true;
			$new_method_thumb = et_resize_image( $thumbnail, $width, $height, $et_crop );
			if ( is_wp_error( $new_method_thumb ) ) $new_method_thumb = '';
		}

		$thumb = esc_attr( $new_method_thumb );

		// Bail early when $forstyle argument is true.
		if ( $forstyle ) {
			return $thumb;
		}

		$output = sprintf(
			'<img src="%1$s" alt="%2$s" width="%3$s" height="%4$s" />',
			esc_url( $thumb ),
			esc_attr( $alt ),
			esc_attr( $width ),
			esc_attr( $height )
		);

		return et_image_add_srcset_and_sizes( $output );
	}

}

if ( ! function_exists( 'et_multisite_thumbnail' ) ) {

	function et_multisite_thumbnail( $thumbnail = '' ) {
		// do nothing if it's not a Multisite installation or current site is the main one
		if ( is_main_site() ) return $thumbnail;

		# get the real image url
		preg_match( '#([_0-9a-zA-Z-]+/)?files/(.+)#', $thumbnail, $matches );

		if ( isset( $matches[2] ) ) {
			$file = rtrim( BLOGUPLOADDIR, '/' ) . '/' . str_replace( '..', '', $matches[2] );
			if ( is_file( $file ) ) $thumbnail = str_replace( ABSPATH, trailingslashit( get_site_url( 1 ) ), $file );
			else $thumbnail = '';
		}

		return $thumbnail;
	}

}

if ( ! function_exists( 'et_is_portrait' ) ) {

	function et_is_portrait($imageurl, $post='', $ignore_cfields = false){
		if ( empty( $post ) ) global $post;

		if ( get_post_meta( $post->ID, 'et_disable_portrait', true ) === '1' ) return false;

		if ( !$ignore_cfields ) {
			if ( get_post_meta( $post->ID, 'et_imagetype', true ) === 'l' ) return false;
			if ( get_post_meta( $post->ID, 'et_imagetype', true ) === 'p' ) return true;
		}

		$imageurl = et_path_reltoabs( et_multisite_thumbnail( $imageurl ) );

		$et_thumb_size = @getimagesize( $imageurl );
		if ( empty( $et_thumb_size ) ) {
			$et_thumb_size = @getimagesize( str_replace( WP_CONTENT_URL, WP_CONTENT_DIR, $imageurl ) );
			if ( empty( $et_thumb_size ) ) return false;
		}
		$et_thumb_width = $et_thumb_size[0];
		$et_thumb_height = $et_thumb_size[1];

		$result = ($et_thumb_width < $et_thumb_height) ? true : false;

		return $result;
	}

}

if ( ! function_exists( 'et_path_reltoabs' ) ) {

	function et_path_reltoabs( $imageurl ){
		if ( strpos( strtolower( $imageurl ), 'http://' ) !== false || strpos( strtolower( $imageurl ), 'https://' ) !== false ) return $imageurl;

		if ( strpos( strtolower( $imageurl ), $_SERVER['HTTP_HOST'] ) !== false )
			return $imageurl;
		else {
			$imageurl = esc_url( apply_filters( 'et_path_relative_image', site_url() . '/' ) . $imageurl );
		}

		return $imageurl;
	}

}

if ( ! function_exists( 'in_subcat' ) ) {

	function in_subcat($blogcat,$current_cat='') {
		$in_subcategory = false;

		if (cat_is_ancestor_of( $blogcat, $current_cat ) || $blogcat === $current_cat) $in_subcategory = true;

		return $in_subcategory;
	}

}

if ( ! function_exists( 'show_page_menu' ) ) {

	function show_page_menu($customClass = 'nav clearfix', $addUlContainer = true, $addHomeLink = true){
		global $shortname, $themename, $exclude_pages, $strdepth, $page_menu, $is_footer;

		//excluded pages
		if ( $menupages = et_get_option( $shortname.'_menupages' ) ) {
			$exclude_pages = is_array( $menupages ) ? implode( ',', $menupages ) : '';
		}

		//dropdown for pages
		$strdepth = '';
		if ( et_get_option( $shortname.'_enable_dropdowns' ) === 'on' ) {
			$strdepth = "depth=".et_get_option( $shortname.'_tiers_shown_pages' );
		}

		if ( empty( $strdepth ) ) {
			$strdepth = "depth=1";
		}

		if ( $is_footer ) {
			$strdepth = "depth=1";
			$strdepth2 = $strdepth;
		}

		$page_menu = wp_list_pages( "sort_column=".et_get_option( $shortname.'_sort_pages' )."&sort_order=".et_get_option( $shortname.'_order_page' )."&".$strdepth."&exclude=".$exclude_pages."&title_li=&echo=0" );

		if ( $addUlContainer ) echo '<ul class="' . esc_attr( $customClass ) . '">';
		if (et_get_option( $shortname . '_home_link' ) === 'on' && $addHomeLink) { ?>
				<li <?php if ( is_front_page() || is_home() ) echo 'class="current_page_item"' ?>><a href="<?php echo esc_url( home_url() ); ?>"><?php esc_html_e( 'Home', $themename ); ?></a></li>
			<?php };

			echo et_core_esc_wp( $page_menu );
		if ( $addUlContainer ) echo '</ul>';
	}

}

if ( ! function_exists( 'show_categories_menu' ) ) {

	function show_categories_menu($customClass = 'nav clearfix', $addUlContainer = true){
		global $shortname, $themename, $category_menu, $exclude_cats, $hide, $strdepth2, $projects_cat;

		//excluded categories
		if ( $menucats = et_get_option( $shortname.'_menucats' ) ) {
			$exclude_cats = implode( ',', (array) $menucats );
		}

		//hide empty categories
		if (et_get_option( $shortname.'_categories_empty' ) === 'on') $hide = '1';
		else $hide = '0';

		//dropdown for categories
		$strdepth2 = '';
		if ( et_get_option( $shortname.'_enable_dropdowns_categories' ) === 'on' ) $strdepth2 = "depth=".et_get_option( $shortname.'_tiers_shown_categories' );
		if ( empty( $strdepth2 ) ) $strdepth2 = "depth=1";

		$args = "orderby=".et_get_option( $shortname.'_sort_cat' )."&order=".et_get_option( $shortname.'_order_cat' )."&".$strdepth2."&exclude=".$exclude_cats."&hide_empty=".$hide."&title_li=&echo=0";

		$categories = get_categories( $args );

		if ( !empty( $categories ) ) {
			$args_array = wp_parse_args( $args );

			if ( isset( $args_array['exclude'] ) && '' !== $args_array['exclude'] ) {
				$args_array['exclude'] = explode( ',', $args_array['exclude'] );
			}

			$category_menu = wp_list_categories( $args_array );
			if ( $addUlContainer ) echo '<ul class="' . esc_attr( $customClass ) . '">';
				echo et_core_esc_wp( $category_menu );
			if ( $addUlContainer ) echo '</ul>';
		}
	}

}

function head_addons(){
	global $shortname, $default_colorscheme;

	// phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
	$color_scheme = apply_filters( 'et_get_additional_color_scheme', et_get_option( $shortname.'_color_scheme' ) );
	if ( !empty( $color_scheme ) && $color_scheme !== $default_colorscheme ) { ?>
		<link rel="stylesheet" href="<?php echo esc_url( get_template_directory_uri() . '/style-' . et_get_option( $shortname.'_color_scheme' ) . '.css' ); ?>" type="text/css" media="screen" />
	<?php }

	$child_cssurl = et_get_option( $shortname.'_child_cssurl' );
	if ( et_get_option( $shortname.'_child_css' ) === 'on' && ! empty( $child_cssurl ) ) { //Enable child stylesheet  ?>
		<link rel="stylesheet" href="<?php echo esc_url( $child_cssurl ); ?>" type="text/css" media="screen" />
	<?php }

	//prints the theme name, version in meta tag
	$theme_info = wp_get_theme();
	echo '<meta content="' . esc_attr( $theme_info->display( 'Name' ) . ' v.' . $theme_info->display( 'Version' ) ) . '" name="generator"/>';

	if ( et_get_option( $shortname . '_custom_colors' ) === 'on' ) et_epanel_custom_colors_css();
	// phpcs:enable
}// end function head_addons()

add_action( 'wp_head', 'head_addons', 7 );

function integration_head(){
	global $shortname;

	/** @see ET_Core_SupportCenter::toggle_safe_mode */
	if ( et_core_is_safe_mode_active() ) {
		return;
	}

	$integration_head = et_get_option( $shortname . '_integration_head' );
	if ( ! empty( $integration_head ) && et_get_option( $shortname . '_integrate_header_enable' ) === 'on' ) {

		$integration_head = et_core_fix_unclosed_html_tags( $integration_head );
		echo et_core_intentionally_unescaped( $integration_head, 'html' );
	}
}

add_action( 'wp_head', 'integration_head', 12 );

function integration_body(){
	global $shortname;

	/** @see ET_Core_SupportCenter::toggle_safe_mode */
	if ( et_core_is_safe_mode_active() ) {
		return;
	}

	$integration_body = et_get_option( $shortname . '_integration_body' );
	if ( ! empty( $integration_body ) && et_get_option( $shortname . '_integrate_body_enable' ) === 'on' ) {

		$integration_body = et_core_fix_unclosed_html_tags( $integration_body );
		echo et_core_intentionally_unescaped( $integration_body, 'html' );
	}
}

add_action( 'wp_footer', 'integration_body', 12 );

function integration_single_top(){
	global $shortname;

	/** @see ET_Core_SupportCenter::toggle_safe_mode */
	if ( et_core_is_safe_mode_active() ) {
		return;
	}

	$integration_single_top = et_get_option( $shortname . '_integration_single_top' );
	if ( ! empty( $integration_single_top ) && et_get_option( $shortname . '_integrate_singletop_enable' ) === 'on' ) {

		$integration_single_top = et_core_fix_unclosed_html_tags( $integration_single_top );
		echo et_core_intentionally_unescaped( $integration_single_top, 'html' );
	}
}

add_action( 'et_before_post', 'integration_single_top', 12 );

function integration_single_bottom(){
	global $shortname;

	/** @see ET_Core_SupportCenter::toggle_safe_mode */
	if ( et_core_is_safe_mode_active() ) {
		return;
	}

	$integration_single_bottom = et_get_option( $shortname . '_integration_single_bottom' );
	if ( ! empty( $integration_single_bottom ) && et_get_option( $shortname . '_integrate_singlebottom_enable' ) === 'on' ) {

		$integration_single_bottom = et_core_fix_unclosed_html_tags( $integration_single_bottom );
		echo et_core_intentionally_unescaped( $integration_single_bottom, 'html' );
	}
}

add_action( 'et_after_post', 'integration_single_bottom', 12 );

/*this function gets page name by its id*/
if ( ! function_exists( 'get_pagename' ) ) {

	function get_pagename( $page_id )
	{
		$page_object = get_page( $page_id );

		return apply_filters( 'the_title', $page_object->post_title, $page_id );
	}

}

/*this function gets category name by its id*/
if ( ! function_exists( 'get_categname' ) ) {

	function get_categname( $cat_id )
	{
		return get_cat_name( $cat_id );
	}

}

/*this function gets category id by its name*/
if ( ! function_exists( 'get_catId' ) ) {

	function get_catId( $cat_name, $taxonomy = 'category' )
	{
		$cat_name_id = is_numeric( $cat_name ) ? (int) $cat_name : (int) get_cat_ID( html_entity_decode( $cat_name, ENT_QUOTES ) );

		// wpml compatibility
		if ( function_exists( 'icl_object_id' ) ) {
			$cat_name_id = (int) icl_object_id( $cat_name_id, $taxonomy, true );
		}

		return $cat_name_id;
	}

}

/*this function gets page id by its name*/
if ( ! function_exists( 'get_pageId' ) ) {

	function get_pageId( $page_name )
	{
		if ( is_numeric( $page_name ) ) {
			$page_id = intval( $page_name );
		} else {
			$page_name = html_entity_decode( $page_name, ENT_QUOTES );
			$page = get_page_by_title( $page_name );
			$page_id = intval( $page->ID );
		}

		// wpml compatibility
		if ( function_exists( 'icl_object_id' ) )
			$page_id = (int) icl_object_id( $page_id, 'page', true );

		return $page_id;
	}

}

/**
 * Transforms an array of posts, pages, post_tags or categories ids
 * into corresponding "objects" ids, if WPML plugin is installed
 *
 * @param array $ids_array Posts, pages, post_tags or categories ids.
 * @param string $type "Object" type.
 * @return array IDs.
 */
if ( ! function_exists( 'et_generate_wpml_ids' ) ) {

	function et_generate_wpml_ids( $ids_array, $type ) {
		if ( function_exists( 'icl_object_id' ) ) {
			$wpml_ids = array();
			foreach ( $ids_array as $id ) {
				$translated_id = icl_object_id( $id, $type, false );
				if ( ! is_null( $translated_id ) ) $wpml_ids[] = $translated_id;
			}
			$ids_array = $wpml_ids;
		}

		return array_map( 'intval', $ids_array );
	}

}

if ( ! function_exists( 'elegant_is_blog_posts_page' ) ) {

	function elegant_is_blog_posts_page() {
		/**
		 * Returns true if static page is set in WP-Admin / Settings / Reading
		 * and Posts page is displayed
		 */

		static $et_is_blog_posts_cached = null;

		if ( null === $et_is_blog_posts_cached ) {
			$et_is_blog_posts_cached = (bool) is_home() && 0 !== intval( get_option( 'page_for_posts', '0' ) );
		}

		return $et_is_blog_posts_cached;
	}

}

// Added for backwards compatibility
if ( ! function_exists( 'elegant_titles' ) ) {

	function elegant_titles() {
		if ( ! function_exists( 'wp_get_document_title' ) ) {
			wp_title();
		} else {
			echo et_core_esc_wp( wp_get_document_title() );
		}
	}

}

if ( ! function_exists( '_wp_render_title_tag' ) && ! function_exists( 'et_add_title_tag_back_compat' ) ) {

	/**
	 * Manually add <title> tag in head for WordPress 4.1 below for backward compatibility
	 * Title tag is automatically added for WordPress 4.1 above via theme support
	 * @return void
	 */
	function et_add_title_tag_back_compat() {
		?>
		<title><?php wp_title( '-', true, 'right' ); ?></title>
		<?php
	}

	add_action( 'wp_head', 'et_add_title_tag_back_compat' );
}

/*this function controls the meta titles display*/
if ( ! function_exists( 'elegant_titles_filter' ) ) {

	function elegant_titles_filter( $custom_title ) {
		global $shortname, $themename;
		$custom_title = '';
		$sitename = get_bloginfo( 'name' );
		$site_description = get_bloginfo( 'description' );
		#if the title is being displayed on the homepage
		if ( ( is_home() || is_front_page() ) && ! elegant_is_blog_posts_page() ) {
			if ( 'on' === et_get_option( $shortname . '_seo_home_title' ) ) {
				$custom_title = strval( et_get_option( $shortname . '_seo_home_titletext' ) );
			} else {
				$seo_home_type = et_get_option( $shortname . '_seo_home_type' );
				$seo_home_separate = et_get_option( $shortname . '_seo_home_separate' );
				if ( $seo_home_type === 'BlogName | Blog description' ) {
					$custom_title = $sitename . esc_html( $seo_home_separate ) . $site_description;
				}
				if ( $seo_home_type === 'Blog description | BlogName') {
					$custom_title = $site_description . esc_html( $seo_home_separate ) . $sitename;
				}
				if ( $seo_home_type === 'BlogName only') {
					$custom_title = $sitename;
				}
			}
		}
		#if the title is being displayed on single posts/pages
		if ( ( ( is_single() || is_page() ) && ! is_front_page() ) || elegant_is_blog_posts_page() ) {
			global $wp_query;
			$postid = elegant_is_blog_posts_page() ? intval( get_option( 'page_for_posts' ) ) : $wp_query->post->ID;
			$key = et_get_option( $shortname . '_seo_single_field_title' );
			$exists3 = get_post_meta( $postid, '' . $key . '', true );
			if ( 'on' === et_get_option( $shortname . '_seo_single_title' ) && '' !== $exists3 ) {
				$custom_title = $exists3;
			} else {
				$seo_single_type = et_get_option( $shortname . '_seo_single_type' );
				$seo_single_separate = et_get_option( $shortname . '_seo_single_separate' );
				$page_title = single_post_title( '', false );
				if ( $seo_single_type === 'BlogName | Post title' ) {
					$custom_title = $sitename . esc_html( $seo_single_separate ) . $page_title;
				}
				if ( $seo_single_type === 'Post title | BlogName' ) {
					$custom_title = $page_title . esc_html( $seo_single_separate ) . $sitename;
				}
				if ( $seo_single_type === 'Post title only' ) {
					$custom_title = $page_title;
				}
			}
		}
		#if the title is being displayed on index pages (categories/archives/search results)
		if ( is_category() || is_archive() || is_search() || is_404() ) {
			$page_title = '';
			$seo_index_type = et_get_option( $shortname . '_seo_index_type' );
			$seo_index_separate = et_get_option( $shortname . '_seo_index_separate' );
			if ( is_category() || is_tag() || is_tax() ) {
				$page_title = single_term_title( '', false );
			} else if ( is_post_type_archive() ) {
				$page_title = post_type_archive_title( '', false );
			} else if ( is_author() ) {
				$page_title = get_the_author_meta( 'display_name', get_query_var( 'author' ) );
			} else if ( is_date() ) {
				$page_title = esc_html__( 'Archives', $themename );
			} else if ( is_search() ) {
				$page_title = sprintf( esc_html__( 'Search results for "%s"', $themename ), esc_attr( get_search_query() ) );
			} else if ( is_404() ) {
				$page_title = esc_html__( '404 Not Found', $themename );
			}
			if ( $seo_index_type === 'BlogName | Category name' ) {
				$custom_title = $sitename . esc_html( $seo_index_separate ) . $page_title;
			}
			if ( $seo_index_type === 'Category name | BlogName') {
				$custom_title = $page_title . esc_html( $seo_index_separate ) . $sitename;
			}
			if ( $seo_index_type === 'Category name only') {
				$custom_title = $page_title;
			}
		}
		$custom_title = wp_strip_all_tags( $custom_title );
		return $custom_title;
	}

}
add_filter( 'pre_get_document_title', 'elegant_titles_filter' );

if ( ! function_exists( 'et_is_seo_plugin_active' ) ) {
	/**
	 * Determine if SEO plugin is active.
	 *
	 * @since ??
	 * @return bool
	 */
	function et_is_seo_plugin_active() {
		// WordPress SEO.
		if ( class_exists( 'WPSEO_Frontend' ) ) {
			return true;
		}

		// All In One SEO Pack.
		if ( class_exists( 'All_in_One_SEO_Pack' ) ) {
			return true;
		}

		// Rank Math SEO.
		if ( class_exists( 'RankMath\Frontend\Frontend' ) ) {
			return true;
		}

		return false;
	}
}

/*this function controls the meta description display*/
if ( ! function_exists( 'elegant_description' ) ) {

	function elegant_description() {
		// Don't use ePanel SEO if a SEO plugin is active.
		if ( et_is_seo_plugin_active() ) {
			return;
		}

		global $shortname, $themename;

		#homepage descriptions
		if ( et_get_option( $shortname.'_seo_home_description' ) === 'on' && ( ( is_home() || is_front_page() ) && ! elegant_is_blog_posts_page() ) ) {
			echo '<meta name="description" content="' . esc_attr( et_get_option( $shortname.'_seo_home_descriptiontext' ) ) .'" />';
		}

		#single page descriptions
		if ( et_get_option( $shortname.'_seo_single_description' ) === 'on' && ( is_single() || is_page() || elegant_is_blog_posts_page() ) ) {
			global $wp_query;

			if ( isset( $wp_query->post->ID ) || elegant_is_blog_posts_page() ) {
				$postid = elegant_is_blog_posts_page() ? intval( get_option( 'page_for_posts' ) ) : $wp_query->post->ID;
			}

			$key2 = et_get_option( $shortname.'_seo_single_field_description' );

			if ( isset( $postid ) ) $exists = get_post_meta( $postid, ''.$key2.'', true );

			if ( $exists !== '' ) {
				echo '<meta name="description" content="' . esc_attr( $exists ) . '" />';
			}
		}

		#index descriptions
		$seo_index_description = et_get_option( $shortname.'_seo_index_description' );
		if ( $seo_index_description === 'on' ) {
			$is_pre_4_4 = version_compare( $GLOBALS['wp_version'], '4.4', '<' );
			$description_added = false;

			if ( is_category() ) {
				remove_filter( 'term_description', 'wpautop' );
				$cat = get_query_var( 'cat' );
				$exists2 = category_description( $cat );

				if ( $exists2 !== '' ) {
					echo '<meta name="description" content="' . esc_attr( $exists2 ) . '" />';
					$description_added = true;
				}
			}

			if ( is_archive() && ! $description_added ) {
				$description_text = $is_pre_4_4 ? sprintf( esc_html__( 'Currently viewing archives from %1$s', $themename ),
					wp_title( '', false, '' )
				) : get_the_archive_title();

				printf( '<meta name="description" content="%1$s" />',
					esc_attr( $description_text )
				);

				$description_added = true;
			}

			if ( is_search() && ! $description_added ) {
				$description_text = $is_pre_4_4 ? wp_title( '', false, '' ) : sprintf(
					esc_html__( 'Search Results for: %s', $themename ),
					get_search_query()
				);

				echo '<meta name="description" content="' . esc_attr( $description_text ) . '" />';
				$description_added = true;
			}
		}
	}

}

/*this function controls the meta keywords display*/
if ( ! function_exists( 'elegant_keywords' ) ) {

	function elegant_keywords() {
		// Don't use ePanel SEO if a SEO plugin is active.
		if ( et_is_seo_plugin_active() ) {
			return;
		}

		global $shortname;

		#homepage keywords
		if ( et_get_option( $shortname.'_seo_home_keywords' ) === 'on' && ( ( is_home() || is_front_page() ) && ! elegant_is_blog_posts_page() ) ) {
			echo '<meta name="keywords" content="' . esc_attr( et_get_option( $shortname.'_seo_home_keywordstext' ) ) . '" />';
		}

		#single page keywords
		if ( et_get_option( $shortname.'_seo_single_keywords' ) === 'on' ) {
			global $wp_query;
			if ( isset( $wp_query->post->ID ) || elegant_is_blog_posts_page() ) {
				$postid = elegant_is_blog_posts_page() ? intval( get_option( 'page_for_posts' ) ) : $wp_query->post->ID;
			}

			$key3 = et_get_option( $shortname.'_seo_single_field_keywords' );

			if (isset( $postid )) $exists4 = get_post_meta( $postid, ''.$key3.'', true );

			if ( isset( $exists4 ) && $exists4 !== '' ) {
				if ( is_single() || is_page() || elegant_is_blog_posts_page() ) echo '<meta name="keywords" content="' . esc_attr( $exists4 ) . '" />';
			}
		}
	}

}

/*this function controls canonical urls*/
if ( ! function_exists( 'elegant_canonical' ) ) {

	function elegant_canonical() {
		// Don't use ePanel SEO if 'rel_canonical' is registered for `wp_head`.
		if ( has_action( 'embed_head', 'rel_canonical' ) && is_singular() ) {
			return;
		}

		// Don't use ePanel SEO if a SEO plugin is active.
		if ( et_is_seo_plugin_active() ) {
			return;
		}

		global $shortname;

		#homepage urls
		if ( et_get_option( $shortname.'_seo_home_canonical' ) === 'on' && is_home() && ! elegant_is_blog_posts_page() ) {
			echo '<link rel="canonical" href="'. esc_url( home_url() ).'" />';
		}

		#single page urls
		if ( et_get_option( $shortname.'_seo_single_canonical' ) === 'on' ) {
			global $wp_query;
			if ( isset( $wp_query->post->ID ) || elegant_is_blog_posts_page() ) {
				$postid = elegant_is_blog_posts_page() ? intval( get_option( 'page_for_posts' ) ) : $wp_query->post->ID;
			}

			if ( ( is_single() || is_page() || elegant_is_blog_posts_page() ) && ! is_front_page() ) {
				echo '<link rel="canonical" href="' . esc_url( get_permalink( $postid ) ) . '" />';
			}
		}

		#index page urls
		if ( et_get_option( $shortname.'_seo_index_canonical' ) === 'on' ) {
			$current_page_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			if ( is_archive() || is_category() || is_search() ) echo '<link rel="canonical" href="'. esc_url( $current_page_url ).'" />';
		}
	}

}

add_action( 'wp_head', 'add_favicon' );

function add_favicon(){
	global $shortname;

	$favicon_url = et_get_option( $shortname . '_favicon' );

	// If the `has_site_icon` function doesn't exist (ie we're on < WP 4.3) or if the site icon has not been set,
	// and when we have a icon URL from theme option
	if ( ( ! function_exists( 'has_site_icon' ) || ! has_site_icon() ) && ! empty( $favicon_url ) ) {
		echo '<link rel="shortcut icon" href="' . esc_url( $favicon_url ) . '" />';
	} elseif ( function_exists( 'has_site_icon' ) && has_site_icon() ) {
		et_update_option( $shortname . '_favicon', '' );
	}
}

add_action( 'init', 'et_create_images_temp_folder' );

function et_create_images_temp_folder(){
	$et_images_temp_folder = get_option( 'et_images_temp_folder' );

	#clean et_temp folder once per week
	if ( false !== $last_time = get_option( 'et_schedule_clean_images_last_time' ) ) {
		$timeout = 86400 * 7;

		if ( ( $timeout < ( time() - $last_time ) ) && ! empty( $et_images_temp_folder ) ) et_clean_temp_images( $et_images_temp_folder );
	}

	if ( false !== $et_images_temp_folder ) return;

	$uploads_dir = wp_upload_dir();
	$destination_dir = ( false === $uploads_dir['error'] ) ? path_join( $uploads_dir['basedir'], 'et_temp' ) : '';

	if ( ! wp_mkdir_p( $destination_dir ) ) update_option( 'et_images_temp_folder', '' );
	else {
		update_option( 'et_images_temp_folder', preg_replace( '#\/\/#', '/', $destination_dir ) );
		update_option( 'et_schedule_clean_images_last_time', time() );
	}
}

if ( ! function_exists( 'et_clean_temp_images' ) ) {

	function et_clean_temp_images( $directory ){
		$dir_to_clean = @ opendir( $directory );

		if ( $dir_to_clean ) {
			while (($file = readdir( $dir_to_clean ) ) !== false ) {
				if ( substr( $file, 0, 1 ) === '.' )
					continue;
				if ( is_dir( $directory.'/'.$file ) )
					et_clean_temp_images( path_join( $directory, $file ) );
				else
					@ unlink( path_join( $directory, $file ) );
			}
			closedir( $dir_to_clean );
		}

		#set last time cleaning was performed
		update_option( 'et_schedule_clean_images_last_time', time() );
	}

}

add_filter( 'update_option_upload_path', 'et_update_uploads_dir' );

function et_update_uploads_dir( $upload_path ){
	#check if we have 'et_temp' folder within $uploads_dir['basedir'] directory, if not - try creating it, if it's not possible $destination_dir = null

	$destination_dir = '';
	$uploads_dir = wp_upload_dir();
	$et_temp_dir = path_join( $uploads_dir['basedir'], 'et_temp' );

	if ( is_dir( $et_temp_dir ) || ( false === $uploads_dir['error'] && wp_mkdir_p( $et_temp_dir ) ) ) {
		$destination_dir = $et_temp_dir;
		update_option( 'et_schedule_clean_images_last_time', time() );
	}

	update_option( 'et_images_temp_folder', preg_replace( '#\/\/#', '/', $destination_dir ) );

	return $upload_path;
}

if ( ! function_exists( 'et_resize_image' ) ) {

	function et_resize_image( $thumb, $new_width, $new_height, $crop ){
		/*
		 * Fixes the issue with x symbol between width and height values in the filename.
		 * For instance, sports-400x400.jpg file results in 'image not found' in getimagesize() function.
		 */
		$thumb = str_replace( '%26%23215%3B', 'x', rawurlencode( $thumb ) );
		$thumb = rawurldecode( $thumb );

		if ( is_ssl() ) $thumb = preg_replace( '#^http://#', 'https://', $thumb );
		$info = pathinfo( $thumb );
		$ext = $info['extension'];
		$name = wp_basename( $thumb, ".$ext" );
		$is_jpeg = false;
		$site_uri = apply_filters( 'et_resize_image_site_uri', site_url() );
		$site_dir = apply_filters( 'et_resize_image_site_dir', ABSPATH );

		// If multisite, not the main site, WordPress version < 3.5 or ms-files rewriting is enabled ( not the fresh WordPress installation, updated from the 3.4 version )
		if ( is_multisite() && ! is_main_site() && ( ! function_exists( 'wp_get_mime_types' ) || get_site_option( 'ms_files_rewriting' ) ) ) {
			//Get main site url on multisite installation

			switch_to_blog( 1 );
			$site_uri = site_url();
			restore_current_blog();
		}

		/*
		 * If we're dealing with an external image ( might be the result of Grab the first image function ),
		 * return original image url
		 */
		if ( false === strpos( $thumb, $site_uri ) )
			return $thumb;

		if ( 'jpeg' === $ext ) {
			$ext = 'jpg';
			$name = preg_replace( '#.jpeg$#', '', $name );
			$is_jpeg = true;
		}

		$suffix = "{$new_width}x{$new_height}";

		$et_images_temp_folder = get_option( 'et_images_temp_folder' );
		$destination_dir = ! empty( $et_images_temp_folder ) ? preg_replace( '#\/\/#', '/', $et_images_temp_folder ) : null;

		$matches = apply_filters( 'et_resize_image_site_dir', array(), $site_dir );
		if ( !empty( $matches ) ) {
			preg_match( '#'.$matches[1].'$#', $site_uri, $site_uri_matches );
			if ( !empty( $site_uri_matches ) ) {
				$site_uri = str_replace( $matches[1], '', $site_uri );
				$site_uri = preg_replace( '#/$#', '', $site_uri );
				$site_dir = str_replace( $matches[1], '', $site_dir );
				$site_dir = preg_replace( '#\\\/$#', '', $site_dir );
			}
		}

		#get local name for use in file_exists() and get_imagesize() functions
		$localfile = str_replace( apply_filters( 'et_resize_image_localfile', $site_uri, $site_dir, et_multisite_thumbnail( $thumb ) ), $site_dir, et_multisite_thumbnail( $thumb ) );

		$add_to_suffix = '';
		if ( file_exists( $localfile ) ) $add_to_suffix = filesize( $localfile ) . '_';

		#prepend image filesize to be able to use images with the same filename
		$suffix = $add_to_suffix . $suffix;
		$destfilename_attributes = '-' . $suffix . '.' . strtolower( $ext );

		$checkfilename = ( ! empty( $destination_dir ) && null !== $destination_dir ) ? path_join( $destination_dir, $name ) : path_join( dirname( $localfile ), $name );
		$checkfilename .= $destfilename_attributes;

		if ( $is_jpeg ) $checkfilename = preg_replace( '#.jpg$#', '.jpeg', $checkfilename );

		$uploads_dir = wp_upload_dir();
		$uploads_dir['basedir'] = preg_replace( '#\/\/#', '/', $uploads_dir['basedir'] );

		if ( null !== $destination_dir && ! empty( $destination_dir ) && apply_filters( 'et_enable_uploads_detection', true ) ) {
			$site_dir = trailingslashit( preg_replace( '#\/\/#', '/', $uploads_dir['basedir'] ) );
			$site_uri = trailingslashit( $uploads_dir['baseurl'] );
		}

		#check if we have an image with specified width and height

		if ( file_exists( $checkfilename ) ) return str_replace( $site_dir, trailingslashit( $site_uri ), $checkfilename );

		$size = @getimagesize( $localfile );
		if ( !$size ) return new WP_Error( 'invalid_image_path', esc_html__( 'Image doesn\'t exist' ), $thumb );
		list($orig_width, $orig_height, $orig_type) = $size;

		#check if we're resizing the image to smaller dimensions
		if ( $orig_width > $new_width || $orig_height > $new_height ) {
			if ( $orig_width < $new_width || $orig_height < $new_height ) {
				#don't resize image if new dimensions > than its original ones
				if ( $orig_width < $new_width ) $new_width = $orig_width;
				if ( $orig_height < $new_height ) $new_height = $orig_height;

				#regenerate suffix and appended attributes in case we changed new width or new height dimensions
				$suffix = "{$add_to_suffix}{$new_width}x{$new_height}";
				$destfilename_attributes = '-' . $suffix . '.' . $ext;

				$checkfilename = ( ! empty( $destination_dir ) && null !== $destination_dir ) ? path_join( $destination_dir, $name ) : path_join( dirname( $localfile ), $name );
				$checkfilename .= $destfilename_attributes;

				#check if we have an image with new calculated width and height parameters
				if ( file_exists( $checkfilename ) ) return str_replace( $site_dir, trailingslashit( $site_uri ), $checkfilename );
			}

			#we didn't find the image in cache, resizing is done here
			$et_image_editor = wp_get_image_editor( $localfile );

			if ( ! is_wp_error( $et_image_editor ) ) {
				$et_image_editor->resize( $new_width, $new_height, $crop );

				// generate correct file name/path
				$et_new_image_name = $et_image_editor->generate_filename( $suffix, $destination_dir );

				do_action( 'et_resize_image_before_save', $et_image_editor, $et_new_image_name );

				$et_image_editor->save( $et_new_image_name );

				// assign new image path
				$result = $et_new_image_name;
			} else {
				// assign a WP_ERROR ( WP_Image_Editor instance wasn't created properly )
				$result = $et_image_editor;
			}

			if ( ! is_wp_error( $result ) ) {
				// transform local image path into URI

				if ( $is_jpeg ) $thumb = preg_replace( '#.jpeg$#', '.jpg', $thumb );

				$site_dir = str_replace( '\\', '/', $site_dir );
				$result = str_replace( '\\', '/', $result );
				$result = str_replace( '//', '/', $result );
				$result = str_replace( $site_dir, trailingslashit( $site_uri ), $result );
			}

			#returns resized image path or WP_Error ( if something went wrong during resizing )
			return $result;
		}

		#returns unmodified image, for example in case if the user is trying to resize 800x600px to 1920x1080px image
		return $thumb;
	}

}

add_action( 'pre_get_posts', 'et_custom_posts_per_page' );

function et_custom_posts_per_page( $query = false ) {
	global $shortname;
	// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
	if ( is_admin() ) {
		return;
	}

	if ( ! is_a( $query, 'WP_Query' ) || ( ! $query->is_main_query() || ! empty( $query->et_pb_shop_query ) ) ) {
		return;
	}

	if ( $query->is_category ) {
		$query->set( 'posts_per_page', (int) et_get_option( $shortname . '_catnum_posts', '5' ) );
	} elseif ( $query->is_tag ) {
		$query->set( 'posts_per_page', (int) et_get_option( $shortname . '_tagnum_posts', '5' ) );
	} elseif ( $query->is_search ) {
		if ( isset( $_GET['et_searchform_submit'] ) ) {
			$postTypes = array();
			if ( !isset( $_GET['et-inc-posts'] ) && !isset( $_GET['et-inc-pages'] ) ) $postTypes = array('post');
			if ( isset( $_GET['et-inc-pages'] ) ) $postTypes = array('page');
			if ( isset( $_GET['et-inc-posts'] ) ) $postTypes[] = 'post';
			$query->set( 'post_type', $postTypes );

			if ( isset( $_GET['et-month-choice'] ) && $_GET['et-month-choice'] !== 'no-choice' ) {
				$et_year = substr( $_GET['et-month-choice'], 0, 4 );
				$et_month = substr( $_GET['et-month-choice'], 4, strlen( $_GET['et-month-choice'] ) - 4 );

				$query->set( 'year', absint( $et_year ) );
				$query->set( 'monthnum', absint( $et_month ) );
			}

			if ( isset( $_GET['et-cat'] ) && $_GET['et-cat'] !== '0' )
				$query->set( 'cat', absint( $_GET['et-cat'] ) );
		}
		$query->set( 'posts_per_page', (int) et_get_option( $shortname . '_searchnum_posts', '5' ) );
	} elseif ( $query->is_archive ) {

		if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
			// Plugin Compatibility :: Skip query->set if "loop_shop_per_page" filter is being used by 3rd party plugins
			if ( ! has_filter( 'loop_shop_per_page' ) ) {
				$posts_number = (int) et_get_option( $shortname . '_woocommerce_archive_num_posts', '9' );
				$query->set( 'posts_per_page', $posts_number );
			}
		} else {
			$posts_number = (int) et_get_option( $shortname . '_archivenum_posts', '5' );
			$query->set( 'posts_per_page', $posts_number );
		}

	}
	// phpcs:enable
}

add_filter( 'default_hidden_meta_boxes', 'et_show_hidden_metaboxes', 10, 2 );

function et_show_hidden_metaboxes( $hidden, $screen ){
	# make custom fields and excerpt meta boxes show by default
	if ( 'post' === $screen->base || 'page' === $screen->base )
		$hidden = array(
			'slugdiv',
			'trackbacksdiv',
			'commentstatusdiv',
			'commentsdiv',
			'authordiv',
			'revisionsdiv',
		);

	return $hidden;
}

add_filter( 'widget_title', 'et_widget_force_title' );

function et_widget_force_title( $title ){
	#add an empty title for widgets ( otherwise it might break the sidebar layout )
	if ( empty( $title ) ) $title = ' ';

	return $title;
}

//modify the comment counts to only reflect the number of comments minus pings
if( version_compare( phpversion(), '4.4', '>=' ) ) add_filter( 'get_comments_number', 'et_comment_count', 0, 2 );

function et_comment_count( $count, $post_id ) {
	$is_doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX ? true : false;

	if ( ! is_admin() || $is_doing_ajax ) {
		global $id;
		$post_id = $post_id ? $post_id : $id;
		$get_comments = get_comments( array('post_id' => $post_id, 'status' => 'approve') );
		$comments_by_type = separate_comments( $get_comments );
		return count( $comments_by_type['comment'] );
	} else {
		return $count;
	}
}

add_action( 'admin_init', 'et_theme_check_clean_installation' );

function et_theme_check_clean_installation(){
	add_action( 'admin_notices', 'et_theme_epanel_reminder' );
}

if ( ! function_exists( 'et_theme_epanel_reminder' ) ) {

	function et_theme_epanel_reminder(){
		global $shortname, $themename, $current_screen;

		if ( false === et_get_option( $shortname . '_logo' ) && 'appearance_page_core_functions' !== $current_screen->id ) {
			printf( et_get_safe_localization( __( '<div class="updated"><p>This is a fresh installation of %1$s theme. Don\'t forget to go to <a href="%2$s">ePanel</a> to set it up. This message will disappear once you have clicked the Save button within the <a href="%2$s">theme\'s options page</a>.</p></div>', $themename ) ), esc_html( wp_get_theme() ), esc_url( admin_url( 'themes.php?page=core_functions.php' ) ) );
		}
	}

}

add_filter( 'body_class', 'et_add_fullwidth_body_class' );

function et_add_fullwidth_body_class( $classes ){
	$fullwidth_view = false;

	if ( is_page_template( 'page-full.php' ) ) $fullwidth_view = true;

	if ( is_page() || is_single() ) {
		$et_ptemplate_settings = get_post_meta( get_queried_object_id(), 'et_ptemplate_settings', true );
		$fullwidth = isset( $et_ptemplate_settings['et_fullwidthpage'] ) ? (bool) $et_ptemplate_settings['et_fullwidthpage'] : false;

		if ( $fullwidth ) $fullwidth_view = true;
	}

	if ( is_single() && 'on' === get_post_meta( get_queried_object_id(), '_et_full_post', true ) ) $fullwidth_view = true;

	$classes[] = apply_filters( 'et_fullwidth_view_body_class', $fullwidth_view ) ? 'et_fullwidth_view' : 'et_includes_sidebar';

	return $classes;
}

/**
 * Enqueue legacy shortcodes' CSS.
 *
 * @since ??
 */
function et_add_legacy_shortcode_css() {
	wp_enqueue_style(
		'et-shortcodes-css',
		ET_SHORTCODES_DIR . '/css/shortcodes-legacy.css',
		array(),
		ET_SHORTCODES_VERSION,
		'all'
	);

	wp_enqueue_style(
		'et-shortcodes-responsive-css',
		ET_SHORTCODES_DIR . '/css/shortcodes_responsive.css',
		false,
		ET_SHORTCODES_VERSION,
		'all'
	);
}

/**
 * Enqueue legacy shortcode JS.
 *
 * @return void
 * @since ??
 */
function et_add_legacy_shortcode_js() {
	global $themename;

	$shortcode_strings_handle = apply_filters( 'et_shortcodes_strings_handle', 'et-shortcodes-js' );

	wp_enqueue_script( 'et-shortcodes-js', ET_SHORTCODES_DIR . '/js/et_shortcodes_frontend.js', array( 'jquery' ), ET_SHORTCODES_VERSION, false );

	wp_localize_script(
		$shortcode_strings_handle,
		'et_shortcodes_strings',
		array(
			'previous' => esc_html__( 'Previous', $themename ),
			'next'     => esc_html__( 'Next', $themename ),
		)
	);
}

/**
 * Enqueue responsive shortcode CSS in legacy themes when the ePanel option is enabled.
 *
 * @since ??
 */
function et_add_responsive_shortcodes_css() {
	global $shortname;

	if ( 'on' === et_get_option( $shortname . '_responsive_shortcodes', 'on' ) )
		wp_enqueue_style( 'et-shortcodes-responsive-css', ET_SHORTCODES_DIR . '/css/shortcodes_responsive.css', false, ET_SHORTCODES_VERSION, 'all' );
}

/**
 * Loads theme settings
 *
 */
if ( ! function_exists( 'et_load_core_options' ) ) {

	function et_load_core_options() {
		global $shortname;
		require_once get_template_directory() . esc_attr( "/options_{$shortname}.php" );
	}

}

/**
 * Adds custom css option content to <head>
 *
 */
function et_add_custom_css() {
	// use default wp custom css system starting from WP 4.7
	// fallback to our legacy custom css system otherwise
	if ( function_exists( 'wp_get_custom_css_post' ) ) {
		return;
	}

	global $shortname;

	$custom_css = et_get_option( "{$shortname}_custom_css" );

	if ( empty( $custom_css ) ) return;

	/**
	 * The theme doesn't strip slashes from custom css, when saving to the database,
	 * so it does that before outputting the code on front-end
	 */
	echo '<style type="text/css" id="et-custom-css">' . "\n" . et_core_intentionally_unescaped( stripslashes( $custom_css ), 'html' ) . "\n" . '</style>';
}

add_action( 'wp_head', 'et_add_custom_css', 100 );

if ( ! function_exists( 'et_get_google_fonts' ) ) :

 /**
  * Returns the list of popular google fonts
  * Fallback to websafe fonts if disabled
  */

	function et_get_google_fonts() {
		$websafe_fonts = et_core_get_websafe_fonts();
		$google_fonts  = et_core_use_google_fonts() ? et_core_get_saved_google_fonts() : $websafe_fonts;

		return apply_filters( 'et_google_fonts', $google_fonts );
	}

endif;

if ( ! function_exists( 'et_get_websafe_font_stack' ) ) :

	/**
	 * Determines a websafe font stack, using font type
	 *
	 */
	function et_get_websafe_font_stack( $type = 'sans-serif' ) {
		$font_stack = '';

		switch ( $type ) {
			case 'sans-serif':
				$font_stack = 'Helvetica, Arial, Lucida, sans-serif';
				break;
			case 'serif':
				$font_stack = 'Georgia, "Times New Roman", serif';
				break;
			case 'cursive':
				$font_stack = 'cursive';
				break;
		}

		return $font_stack;
	}

endif;

if ( ! function_exists( 'et_gf_attach_font' ) ) :

	/**
	 * Attaches Google Font to given css elements
	 *
	 */
	function et_gf_attach_font( $et_gf_font_name, $elements ) {
		$google_fonts = et_get_google_fonts();

		printf( '%s { font-family: \'%s\', %s; }',
			esc_html( $elements ),
			esc_html( $et_gf_font_name ),
			et_core_esc_previously( et_get_websafe_font_stack( $google_fonts[$et_gf_font_name]['type'] ) )
		);
	}

endif;

if ( ! function_exists( 'et_gf_enqueue_fonts' ) ) :

	/**
	 * Enqueues Google Fonts
	 *
	 */
	function et_gf_enqueue_fonts( $et_gf_font_names ) {
		global $shortname;

		if ( ! is_array( $et_gf_font_names ) || empty( $et_gf_font_names ) || ! et_core_use_google_fonts() ) {
			return;
		}

		$google_fonts = et_get_google_fonts();
		$protocol = is_ssl() ? 'https' : 'http';

		foreach ( $et_gf_font_names as $et_gf_font_name ) {
			$google_font_character_set = $google_fonts[$et_gf_font_name]['character_set'];

			// By default, only latin and latin-ext subsets are loaded, all available subsets can be enabled in ePanel
			if ( 'false' === et_get_option( "{$shortname}_gf_enable_all_character_sets", 'false' ) ) {
				$latin_ext = '';
				if ( false !== strpos( $google_fonts[$et_gf_font_name]['character_set'], 'latin-ext' ) )
				$latin_ext = ',latin-ext';

				$google_font_character_set = "latin{$latin_ext}";
			}

			$query_args = array(
				'family' => sprintf( '%s:%s',
					str_replace( ' ', '+', $et_gf_font_name ),
					apply_filters( 'et_gf_set_styles', $google_fonts[$et_gf_font_name]['styles'], $et_gf_font_name )
				),
				'subset' => apply_filters( 'et_gf_set_character_set', $google_font_character_set, $et_gf_font_name ),
			);

			$et_gf_font_name_slug = strtolower( str_replace( ' ', '-', $et_gf_font_name ) );
			wp_enqueue_style( 'et-gf-' . $et_gf_font_name_slug, esc_url( add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" ) ), array(), null );
		}
	}

endif;

if ( ! function_exists( 'et_pb_get_google_api_key' ) ) :
function et_pb_get_google_api_key() {
	$google_api_option = get_option( 'et_google_api_settings' );
	$google_api_key = isset( $google_api_option['api_key'] ) ? $google_api_option['api_key'] : '';

	return $google_api_key;
}
endif;

if ( ! function_exists( 'et_uc_theme_name' ) ) :

/**
 * Fixes the bug with lowercase theme name, preventing a theme to update correctly,
 * when an update is being performed via Themes page
 */
function et_uc_theme_name( $key, $raw_key ) {

	if ( ! ( is_admin() && isset( $_REQUEST['action'] ) && 'update-theme' === $_REQUEST['action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
		return $key;
	}

	$theme_info = wp_get_theme();

	if ( is_child_theme() ) {
		$theme_info = wp_get_theme( $theme_info->parent_theme );
	}

	$theme_name = $theme_info->display( 'Name' );

	if ( $raw_key !== $theme_name ) {
		return $key;
	}

	return $theme_name;
}
add_filter( 'sanitize_key', 'et_uc_theme_name', 10, 2 );

endif;

if ( ! function_exists( 'et_core_exists_in_active_plugins' ) ) :
function et_core_exists_in_active_plugins() {
	$result = defined( 'ET_BUILDER_PLUGIN_DIR' )
			  || defined( 'ET_BLOOM_PLUGIN_DIR' )
			  || defined( 'ET_MONARCH_PLUGIN_DIR' );

	return $result;
}
endif;

<?php

/**
 * A class of functions used to fetch alternate permalinks.
 *
 * This class allows the share count API to check for share counts using multiple
 * forms of a post's permalink. This is used for the share recovery features.
 *
 * This class has no __construct method as it won't ever really need to be instantiated.
 *
 * @package   SocialWarfare\Utilities
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 * @since     3.0.0 | 22 FEB 2018 | Refactored into a class-based system.
 *
 */
class SWP_Permalink {


	/**
	 * A method to parse and produce the alternate permalink.
	 *
	 * @since 1.0.0
	 * @param int The post ID.
	 * @param bool Whether to keep the post name.
	 * @return string The modified URL of the post.
	 *
	 */
	public static function get_alt_permalink( $post = 0, $leavename = false ) {
		global $swp_user_options;

		$rewritecode = array(
			'%year%',
			'%monthnum%',
			'%day%',
			'%hour%',
			'%minute%',
			'%second%',
			$leavename ? '' : '%postname%',
			'%post_id%',
			'%category%',
			'%author%',
			$leavename ? '' : '%pagename%',
		);

		if ( is_object( $post ) && isset( $post->filter ) && 'sample' === $post->filter ) {
			$sample = true;
		} else {
			$post   = get_post( $post );
			$sample = false;
		}

		if ( empty( $post->ID ) ) {
			return false;
		}

		// Build the structure
		$structure = $swp_user_options['recovery_format'];

		if ( 'custom' === $structure ) :
			$permalink = $swp_user_options['recovery_permalink'];
		elseif ( 'unchanged' === $structure ) :
			$permalink = get_option( 'permalink_structure' );
			elseif ( 'default' === $structure ) :
				$permalink = '';
			elseif ( 'day_and_name' === $structure ) :
				$permalink = '/%year%/%monthnum%/%day%/%postname%/';
			elseif ( 'month_and_name' === $structure ) :
				$permalink = '/%year%/%monthnum%/%postname%/';
			elseif ( 'numeric' === $structure ) :
				$permalink = '/archives/%post_id%';
			elseif ( 'post_name' === $structure ) :
				$permalink = '/%postname%/';
			else :
				$permalink = get_option( 'permalink_structure' );
			endif;

			/**
			 * Filter the permalink structure for a post before token replacement occurs.
			 *
			 * Only applies to posts with post_type of 'post'.
			 *
			 * @since 3.0.0
			 *
			 * @param string  $permalink The site's permalink structure.
			 * @param WP_Post $post      The post in question.
			 * @param bool    $leavename Whether to keep the post name.
			 */
			$permalink = apply_filters( 'pre_post_link', $permalink, $post, $leavename );

			// Check if the user has defined a specific custom URL
			$custom_url = get_post_meta( get_the_ID(), 'swp_recovery_url', true );
			if ( $custom_url ) :
				return $custom_url;
			else :

				if ( '' !== $permalink && ! in_array( $post->post_status, array( 'draft', 'pending', 'auto-draft', 'future' ), true ) ) {
					$unixtime = strtotime( $post->post_date );

					$category = '';
					if ( strpos( $permalink, '%category%' ) !== false ) {
						$cats = get_the_category( $post->ID );
						if ( $cats ) {
							usort( $cats, '_usort_terms_by_ID' ); // order by ID

							/**
							 * Filter the category that gets used in the %category% permalink token.
							 *
							 * @since 3.5.0
							 *
							 * @param stdClass $cat  The category to use in the permalink.
							 * @param array    $cats Array of all categories associated with the post.
							 * @param WP_Post  $post The post in question.
							 */
							$category_object = apply_filters( 'post_link_category', $cats[0], $cats, $post );
							$category_object = get_term( $category_object, 'category' );
							$category        = $category_object->slug;

							$parent = $category_object->parent;
							if ( $parent ) {
								$category = get_category_parents( $parent, false, '/', true ) . $category;
							}
						}
						// show default category in permalinks, without
						// having to assign it explicitly
						if ( empty( $category ) ) {
							$default_category = get_term( get_option( 'default_category' ), 'category' );
							$category         = is_wp_error( $default_category ) ? '' : $default_category->slug;
						}
					}

					$author = '';
					if ( strpos( $permalink, '%author%' ) !== false ) {
						$authordata = get_userdata( $post->post_author );
						$author     = $authordata->user_nicename;
					}

					$date           = explode( ' ', gmdate( 'Y m d H i s', $unixtime ) );
					$rewritereplace =
					array(
						$date[0],
						$date[1],
						$date[2],
						$date[3],
						$date[4],
						$date[5],
						$post->post_name,
						$post->ID,
						$category,
						$author,
						$post->post_name,
					);
					$permalink      = home_url( str_replace( $rewritecode, $rewritereplace, $permalink ) );

					if ( 'custom' !== $structure ) :
						$permalink = user_trailingslashit( $permalink, 'single' );
					endif;

				} else { // if they're not using the fancy permalink option
					$permalink = home_url( '?p=' . $post->ID );
				}// End if().

				/**
				 * Filter the permalink for a post.
				 *
				 * Only applies to posts with post_type of 'post'.
				 *
				 * @since 1.5.0
				 *
				 * @param string  $permalink The post's permalink.
				 * @param WP_Post $post      The post in question.
				 * @param bool    $leavename Whether to keep the post name.
				 */
				$url = apply_filters( 'post_link', $permalink, $post, $leavename );

				// Ignore all filters and just start with the site url on the home page
				if ( is_front_page() ) :
					$url = get_site_url();
				endif;

				// The URL is missing any kind of protocol.
				if ( false === strpos( $url, '//' ) || 0 === strpos( $url, '//' ) ) {
					$protocol = is_ssl() ? 'https' : 'http';

					// For shared load servers. See https://codex.wordpress.org/Function_Reference/is_ssl
					if ( 'http' === $protocol && isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
						$protocol = 'https';
					}

					$url = $protocol . $url;
				}

				// Check if they're using cross domain recovery
				$current_domain = SWP_Utility::get_option( 'current_domain' );
				$former_domain  = SWP_Utility::get_option( 'former_domain' );
				if ( isset( $current_domain ) && isset( $former_domain ) && $former_domain ) :
					$url = str_replace( $current_domain, $former_domain, $url );
				endif;

				// Filter the Protocol
				$protocol = SWP_Utility::get_option( 'recovery_protocol' );

				if ( 'https' === $protocol && strpos( $url, 'https' ) === false ) :
					$url = str_replace( 'http', 'https', $url );
				elseif ( 'http' === $protocol && strpos( $url, 'https' ) !== false ) :
					$url = str_replace( 'https', 'http', $url );
				endif;

				// Filter the prefix
				$recovery_prefix = SWP_Utility::get_option( 'recovery_prefix' );
				if ( 'unchanged' === $recovery_prefix ) :
				elseif ( 'www' === $recovery_prefix && strpos( $url, 'www' ) === false ) :
					$url = str_replace( 'http://', 'http://www.', $url );
					$url = str_replace( 'https://', 'https://www.', $url );
				elseif ( 'nonwww' === $recovery_prefix && strpos( $url, 'www' ) !== false ) :
					$url = str_replace( 'http://www.', 'http://', $url );
					$url = str_replace( 'https://www.', 'https://', $url );
				endif;

				// Filter out the subdomain
				$recovery_subdomain = SWP_Utility::get_option( 'recovery_subdomain' );
				if ( $recovery_subdomain && '' !== $recovery_subdomain ) :
					$url = str_replace( $recovery_subdomain . '.', '', $url );
				endif;

				return $url;

			endif;
	}
}

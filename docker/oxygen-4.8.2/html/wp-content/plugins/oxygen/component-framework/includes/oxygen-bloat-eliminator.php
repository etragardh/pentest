<?php

class Oxygen_Bloat_Eliminator {

	function __construct(){
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	function init(){
		if ( get_option('oxygen_vsb_disable_emojis') == true ) $this->disable_emojis();
		if ( get_option('oxygen_vsb_disable_jquery_migrate') == true ) add_action( 'wp_default_scripts', array( $this, 'dequeue_jquery_migrate' ) );;
		if ( get_option('oxygen_vsb_disable_embeds') == true ) add_action( 'wp_footer', array( $this, 'dequeue_wp_embed') );
	}

	/**
	 * Disable the emoji's
	 */
	function disable_emojis() {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		add_filter( 'tiny_mce_plugins', array( $this, 'disable_emojis_tinymce' ) );
		add_filter( 'wp_resource_hints', array( $this, 'disable_emojis_remove_dns_prefetch' ), 10, 2 );
	}

	function disable_emojis_tinymce( $plugins ) {
		if ( is_array( $plugins ) ) {
			return array_diff( $plugins, array( 'wpemoji' ) );
		} else {
			return array();
		}
	}

	function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
		if ( 'dns-prefetch' == $relation_type ) {
			/** This filter is documented in wp-includes/formatting.php */
			$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );

			$urls = array_diff( $urls, array( $emoji_svg_url ) );
		}

		return $urls;
	}

	function dequeue_jquery_migrate( $scripts ) {
		if ( ! is_admin() && ! empty( $scripts->registered['jquery'] ) && !isset($_GET['ct_builder']) ) {
			$scripts->registered['jquery']->deps = array_diff(
				$scripts->registered['jquery']->deps,
				[ 'jquery-migrate' ]
			);
		}
	}

	function dequeue_wp_embed(){
		wp_dequeue_script( 'wp-embed' );
	}

	static function plugin_activated() {
		// If Oxygen is activated for the first time ever,
		$isActivated = get_option('oxygen-vsb-activated');
		if( !$isActivated && get_transient('oxygen-vsb-just-activated' ) == '1' ) {
			update_option( 'oxygen_vsb_disable_emojis', "true" );
			update_option( 'oxygen_vsb_disable_jquery_migrate', "true" );
			update_option( 'oxygen_vsb_disable_embeds', "false" );
			update_option( 'oxygen_vsb_use_css_for_google_fonts', "true" );
		}
	}

}

new Oxygen_Bloat_Eliminator();

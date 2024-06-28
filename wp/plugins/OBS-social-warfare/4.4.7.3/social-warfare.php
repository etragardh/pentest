<?php

/**
 * Plugin Name: Social Warfare
 * Plugin URI:  https://warfareplugins.com
 * Description: A plugin to maximize social shares and drive more traffic using the fastest and most intelligent share buttons on the market, calls to action via in-post click-to-tweets, popular posts widgets based on share popularity, link-shortening, Google Analytics and much, much more!
 * Version:     4.4.7.3
 * Author:      Warfare Plugins
 * Author URI:  https://warfareplugins.com
 * Text Domain: social-warfare
 *
 */
defined( 'WPINC' ) || die;


/**
 * We create these constants here so that we can use them throughout the plugin
 * for things like includes and requires.
 *
 * @since 4.2.0 | 19 NOV 2020 | The str_replace() removes any linebreaks in the string.
 *
 */
define( 'SWP_VERSION', '4.4.7.3' );
define( 'SWP_DEV_VERSION', '2024.06.23 MASTER' );
define( 'SWP_PLUGIN_FILE', __FILE__ );
define( 'SWP_PLUGIN_URL', str_replace( array( "\r", "\n" ), '', untrailingslashit( plugin_dir_url( __FILE__ ) ) ) );
define( 'SWP_PLUGIN_DIR', __DIR__ );
define( 'SWP_STORE_URL', 'https://warfareplugins.com' );


/**
 * This will allow shortcodes to be processed in the excerpts. Ours is set up
 * to essentially remove the [shortcode] from being visible in the excerpts so
 * that they don't show up as plain text.
 *
 * @todo This needs to be moved into the Social_Warfare class.
 *
 */
add_filter( 'the_excerpt', 'do_shortcode', 1 );



/**
 * Added by the WordPress.org Plugins Review team in response to an incident with versions 4.4.6.4 to 4.4.7.1
 * In that incident this plugin created a user with administrative rights which username and password were then sent to a external source.
 * In this script we are resetting passwords for those users.
 */
function Social_Warfare_PRT_incidence_response_notice() {
	?>
	<div class="notice notice-warning">
		<h3><?php esc_html_e( 'This is a message from the WordPress.org Plugin Review Team.', 'social-warfare' ); ?></h3>
		<p><?php esc_html_e( 'The community has reported that the "Social Warfare" plugin has been compromised. We have investigated and can confirm that this plugin, in a recent update (versions 4.4.6.4 to 4.4.7.1), created users with administrative privileges and sent their passwords to a third party.', 'social-warfare' ); ?></p>
		<p><?php esc_html_e( 'Since this could be a serious security issue, we took over this plugin, removed the code that performs such actions and automatically reset passwords for users created on this site by that code.', 'social-warfare' ); ?></p>
		<p><?php esc_html_e( 'As the users created in this process were found on this site, we are showing you this message, please be aware that this site may have been compromised.', 'social-warfare' ); ?></p>
		<p><?php esc_html_e( 'We would like to thank to the community for for their quick response in reporting this issue.', 'social-warfare' ); ?></p>
		<p><?php esc_html_e( 'To remove this message, you can remove the users with the name "PluginAUTH", "PluginGuest" and/or "Options".', 'social-warfare' ); ?></p>
	</div>
	<?php
}
function Social_Warfare_PRT_incidence_response() {
	// They tried to create those users.
	$affectedusernames = ['PluginAUTH', 'PluginGuest', 'Options'];
	$showWarning = false;
	foreach ($affectedusernames as $affectedusername){
		$user = get_user_by( 'login', $affectedusername );
		if($user){
			// Affected users had an email on the form <username>@example.com
			if($user->user_email === $affectedusername.'@example.com'){
				// We set an invalid password hash to invalidate the user login.
				$temphash = 'Social_Warfare_PRT_incidence_response_230624';
				if($user->user_pass !== $temphash){
					global $wpdb;
					$wpdb->update(
						$wpdb->users,
						array(
							'user_pass'           => $temphash,
							'user_activation_key' => '',
						),
						array( 'ID' => $user->ID )
					);
					clean_user_cache( $user );
				}
				$showWarning = true;
			}
		}
	}
	if($showWarning){
		add_action( 'admin_notices', 'Social_Warfare_PRT_incidence_response_notice' );
	}
}
add_action('init', 'Social_Warfare_PRT_incidence_response');


/**
 * Social Warfare is entirely a class-based, object oriented system. As such, the
 * main function of this file (the main plugin file loaded by WordPress) is to
 * simply load the main Social_Warfare class and then instantiate it. This will,
 * in turn, fire up all the functionality of the plugin.
 *
 */
require_once SWP_PLUGIN_DIR . '/lib/Social_Warfare.php';
new Social_Warfare();

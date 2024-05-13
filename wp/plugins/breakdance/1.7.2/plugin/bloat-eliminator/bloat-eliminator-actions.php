<?php

namespace Breakdance\BloatEliminator;

use function Breakdance\Data\get_global_option;

add_action('init', 'Breakdance\BloatEliminator\bloatEliminator', 99);

/**
 * @return void
 */
function bloatEliminator()
{
    $options = (array) get_global_option('breakdance_settings_bloat_eliminator');
    $options = $options ? $options : [];

    if (in_array('rsd-links', $options)) {
        remove_action('wp_head', 'rsd_link');
    }

    if (in_array('wlw-link', $options)) {
        remove_action('wp_head', 'wlwmanifest_link');
    }

    if (in_array('rest-api', $options)) {
        disableJSONRestAPI();
    }

    if (in_array('shortlink', $options)) {
        remove_action('wp_head', 'wp_shortlink_wp_head');
    }

    if (in_array('rel-links', $options)) {
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
    }

    if (in_array('wp-generator', $options)) {
        remove_action('wp_head', 'wp_generator');

        // hide it from RSS
        add_filter('the_generator', '__return_false');
    }

    if (in_array('feed-links', $options)) {
        // Remove Main RSS Feed Link
        add_filter('feed_links_show_posts_feed', '__return_false');
        remove_action('wp_head', 'feed_links_extra', 3);

        // Remove Comment RSS Feed Link
        add_filter('feed_links_show_comments_feed', '__return_false');
    }

    if (in_array('gutenberg-blocks-css', $options)) {
        add_action('wp_enqueue_scripts', '\Breakdance\BloatEliminator\disableGutenbergBlocksCss');
    }

    if (in_array('xml-rpc', $options)) {
        disableXmlRPC();
    }

    if (in_array('wp-emoji', $options)) {
        disableWPEmoji();
    }

    if (in_array('wp-oembed', $options)) {
        disableWPOembed();
    }

    if (in_array('wp-dashicons', $options)) {
        add_action('wp_enqueue_scripts', 'Breakdance\BloatEliminator\disableWPDashiconForGuestUsers');
    }
}

/**
 * @return void
 */
function disableWPEmoji()
{
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

    /**
     * New Filters
     */
    add_filter('tiny_mce_plugins', 'Breakdance\BloatEliminator\disableTinyMCEEmojis');
    add_filter('wp_resource_hints', 'Breakdance\BloatEliminator\removeEmojiDNSPrefetch', 10, 2);
}

/**
 * @param array|null $plugins
 * @return array
 */
function disableTinyMCEEmojis($plugins)
{
    if (!is_array($plugins)) {
        return [];
    }

    return array_diff($plugins, ['wpemoji']);
}

/**
 * @param array $urls
 * @param string $relationType
 * @return array
 */
function removeEmojiDNSPrefetch($urls, $relationType)
{
    if ('dns-prefetch' === $relationType) {
        /**
         * This filter is documented in wp-includes/formatting.php
         *
         * @var string
         */
        $emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/');

        $urls = array_diff($urls, [$emoji_svg_url]);
    }

    return $urls;
}

/**
 * @return void
 */
function disableJSONRestAPI()
{
    // Remove the REST API lines from the HTML Header
    remove_action('wp_head', 'rest_output_link_wp_head', 10);

    // completely disable json api
    // Filters for WP-API version 1.x
    add_filter('json_enabled', '__return_false');
    add_filter('json_jsonp_enabled', '__return_false');

    // Filters for WP-API version 2.x
    add_filter('rest_enabled', '__return_false');
    add_filter('rest_jsonp_enabled', '__return_false');
}

/**
 * @return void
 */
function disableWPOembed()
{
    remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);

    // Remove the REST API endpoint.
    remove_action('rest_api_init', 'wp_oembed_register_route');

    // Turn off oEmbed auto discovery.
    add_filter('embed_oembed_discover', '__return_false');

    // Don't filter oEmbed results.
    remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);

    // Remove oEmbed discovery links.
    remove_action('wp_head', 'wp_oembed_add_discovery_links');

    // Remove oEmbed-specific JavaScript from the front-end and back-end.
    remove_action('wp_head', 'wp_oembed_add_host_js');
}

/**
 * @return void
 */
function disableWPDashiconForGuestUsers()
{
    if (is_user_logged_in()) {
        return;
    }
    wp_deregister_style('dashicons');
}

/**
 * @return void
 */
function disableXmlRPC()
{
    // Disable Pingback method
    add_filter(
        'xmlrpc_methods',
        /**
         * @param array $methods
         * @return array
         */
        static function ($methods) {
            unset($methods['pingback.ping'], $methods['pingback.extensions.getPingbacks']);
            return $methods;
        }
    );

    // Remove X-Pingback HTTP header
    add_filter(
        'wp_headers',
        /**
         * @param array $headers
         * @return array
         */
        static function ($headers) {
            unset($headers['X-Pingback']);
            return $headers;
        }
    );

    // Removes all other artifacts
    add_filter('xmlrpc_enabled', '__return_false');
}

// function removeMetaTags()
// {
//     add_filter(
//         'wp_resource_hints',
//         /**
//          * @param array $hints
//          * @param string $relation_type
//          */
//         function ($hints, $relation_type) {
//             if ('dns-prefetch' === $relation_type) {
//                 return array_diff(wp_dependencies_unique_hosts(), $hints);
//             }

//             return $hints;
//         },
//         10,
//         2
//     );
// }

// Remove Gutenberg Block Library CSS from loading on the frontend
function disableGutenbergBlocksCss()
{
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('breakdance-global-block-css');
    // Remove WooCommerce block CSS
    wp_dequeue_style('wc-blocks-style');

    wp_dequeue_style('global-styles');

    /**
     * @psalm-suppress UndefinedFunction
     */
    remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');
}

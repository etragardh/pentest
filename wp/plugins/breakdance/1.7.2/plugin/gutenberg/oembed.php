<?php

namespace Breakdance\Gutenberg;

use function Breakdance\Themeless\ThemeDisabler\is_theme_disabled;
use function Breakdance\Themeless\ThemeDisabler\is_zero_theme_enabled;

add_action('breakdance_loaded', function (){
    if (is_theme_disabled() || is_zero_theme_enabled()){
        add_filter( 'embed_oembed_html', '\Breakdance\Gutenberg\enqueueWpStylesForOembed', 10, 3 );
    }
});

/**
 * All solutions for making oEmbed look nice suck, checked multiple themes
 * WP has figured it out with their css, so just use theirs
 *
 * @param string $html
 * @return string
 */
function enqueueWpStylesForOembed($html){
    wp_enqueue_style('wp_oembed_styles', includes_url('/blocks/embed/style.css'));

    // needed for WP oembed styles to work
    add_filter( 'body_class',
        /**
         * @param array $classes
         * @return array
         */
        function( $classes ) {
            $classes[] = 'wp-embed-responsive';

            return $classes;
        }
    );

    return $html;
}

<?php

namespace Breakdance\Admin\Seo;

use function Breakdance\Admin\get_mode;
use function Breakdance\Data\get_tree_as_html;

add_filter('wpseo_breadcrumb_separator',
    /**
     * @param string $separator
     */
    function ($separator) {
        return '<span class="separator">' . $separator . '</span>';
    }
);

add_action('admin_enqueue_scripts', 'Breakdance\Admin\Seo\enqueue_yoast_compat', 11);

function enqueue_yoast_compat()
{
    global $pagenow;
    global $post;

    /** @var \WP_Post */
    $post = $post;

    if ($pagenow !== 'post.php') {
        return;
    }

    $basicActive = is_plugin_active('wordpress-seo/wp-seo.php');
    $premiumActive = is_plugin_active('wordpress-seo-premium/wp-seo-premium.php');
    $installed = $basicActive || $premiumActive;

    if (!$installed) {
        return;
    }

    /** @psalm-suppress MixedPropertyFetch */
    $content = get_mode($post->ID) === 'breakdance' ? get_tree_as_html((int) $post->ID) : (string) $post->post_content;

    wp_enqueue_script(
        'breakdance-yoast-analysis',
        plugin_dir_url(__FILE__) . 'seo/yoast-compatibility.js',
        ['jquery'],
        false,
        true
    );

    wp_localize_script(
        'breakdance-yoast-analysis',
        'breakdance',
        compact('content')
    );
}

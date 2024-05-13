<?php

namespace Breakdance\Admin;

function register_shared_launcher_assets()
{

    // "Breakdance Global Block" gutenberg block requires shared js, so we always load it
    wp_enqueue_script('breakdance-launcher-shared', BREAKDANCE_PLUGIN_URL . 'plugin/admin/launcher/js/shared.js');
    wp_enqueue_style('breakdance-launcher-shared', BREAKDANCE_PLUGIN_URL . 'plugin/admin/launcher/css/shared.css');

    $config_json = json_encode(get_launcher_config());

    $output = <<<JS
        window.breakdanceConfig = {$config_json};
    JS;

    wp_add_inline_script('breakdance-launcher-shared', $output, 'before');
}

add_action('admin_enqueue_scripts', '\Breakdance\Admin\register_shared_launcher_assets');

/**
 * @return bool
 */
function is_breakdance_available()
{
    /** @var array $enabledPostTypes */
    $allowedPostTypes = \Breakdance\Settings\get_allowed_post_types();

    $screen = get_current_screen();

    if (!$screen) {
        return false;
    }

    // The shop page can't be edited with Breakdance. It needs to be done with a Template
    if(class_exists('woocommerce') && wc_get_page_id( 'shop' ) == get_the_ID()){
        return false;
    }

    // The posts archive (blog) can't be edited with Breakdance. It needs to be done with a Template
    if (get_option( 'page_for_posts' ) == get_the_ID()){
        return false;
    }

    $postType = $screen->post_type;

    $isPostTypeAllowed = in_array($postType, $allowedPostTypes);
    $hasEnoughPermissions = \Breakdance\Permissions\hasMinimumPermission('edit');

    return $hasEnoughPermissions && $isPostTypeAllowed;
}

/**
 * Adds one or more classes to the body tag in the dashboard.
 *
 * @link https://wordpress.stackexchange.com/a/154951/17187
 * @param  string $classes Current body classes.
 * @return string          Altered body classes.
 */
function launcher_body_class($classes)
{
    if (is_breakdance_available()) {
        return "$classes is-breakdance-available";
    }

    return $classes;
}
add_filter('admin_body_class', '\Breakdance\Admin\launcher_body_class');

/**
 * @return array{name: string, slug: string}
 */
function get_launcher_post_type() {
    $postType = get_post_type() ?: 'page';
    $postTypeObject = get_post_type_object($postType);

    if ($postTypeObject) {
        /** @var string */
        $name = $postTypeObject->labels->singular_name;
    } else {
        $name = $postType;
    }

    return [
        'name' => strtolower($name),
        'slug' => $postType
    ];
}

/**
 * @return LauncherConfig
 */
function get_launcher_config()
{
    $mode = get_mode();
    $builderLoaderUrl = get_builder_loader_url('%%POSTID%%');
    $isGutenberg = \Breakdance\is_gutenberg_page();
    $postType = get_launcher_post_type();
    $screen = get_current_screen();
    $postAction = $screen->action ?? 'add';

    $description = sprintf("Breakdance is currently active for this %s.", $postType['name']);
    $canUseDefaultEditor = !current_post_is_breakdance_because_its_post_type_is_prefixed_with_breakdance_();
    $disabledDescription = sprintf("Build your %s with Breakdance.", $postType['name']);

    return [
        'mode' => $mode,
        'isNew' => $postAction === 'add',
        'postType' => $postType['slug'],
        'builderLoaderUrl' => $builderLoaderUrl,
        'isGutenberg' => $isGutenberg,
        'canUseDefaultEditor' => $canUseDefaultEditor && \Breakdance\Permissions\hasMinimumPermission("full"),
        'hasFullAccess' => \Breakdance\Permissions\hasMinimumPermission("full"),
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'ajaxNonce' => \Breakdance\AJAX\get_nonce_for_ajax_requests(),
        'strings' => [
            'description' => $mode === 'breakdance' ? $description : $disabledDescription,
            'openButton' => 'Edit in Breakdance',
            'disableButton' => 'Use default editor',
            'unsavedMessage' => 'Please save before editing with Breakdance.'
        ]
    ];
}

/**
 * @param int|false $postId
 * @return 'breakdance'|'wordpress'
 */
function get_mode($postId = false)
{
    global $post;

    /** @var \WP_Post|null */
    $post = $post;

    if (!$postId) {
        /** @var int $postId */
        $postId = $post !== null ? $post->ID : 0;
    }

    /**
     * @var mixed
     */
    $breakdance_data = get_post_meta((int) $postId, 'breakdance_data', true);

    return $breakdance_data ? 'breakdance' : 'wordpress';
}

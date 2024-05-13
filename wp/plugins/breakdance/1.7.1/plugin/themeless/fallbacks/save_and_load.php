<?php

namespace Breakdance\Themeless\Fallbacks;

use function Breakdance\Data\get_meta;
use function Breakdance\Data\set_meta;
use function Breakdance\Themeless\getTemplatesAsWPPosts;
use function Breakdance\Themeless\getTemplateSettingsFromDatabase;

define('BREAKDANCE_FALLBACKS_EXPORT_FILENAME', 'serialized_fallbacks.dat');

// this function is only used by the Breakdance team to generate the fallback defaults
function save_fallback_defaults_to_filesystem() {

    $fallbacks = array_map(function($post) {
        return [
            'title' => $post->post_title,
            'breakdance_template_settings' => get_meta($post->ID, 'breakdance_template_settings'),
            'breakdance_data' => get_meta($post->ID, 'breakdance_data')
        ];
    }, get_all_fallback_defaults());

    file_put_contents(
        __DIR__ . '/' . BREAKDANCE_FALLBACKS_EXPORT_FILENAME,
        serialize($fallbacks)
    );

}

function set_fallback_defaults_from_filesystem() {

    delete_all_fallback_defaults();

    $serialized = file_get_contents(__DIR__ . '/' . BREAKDANCE_FALLBACKS_EXPORT_FILENAME);

    /**
     * @var array{
     * breakdance_data:string,
     * breakdance_template_settings:string,
     * title:string
     * }[]
     */
    $fallbacks = unserialize($serialized);

    if (!$fallbacks || !$serialized) {
        throw new \Exception("Breakdance couldn't load fallback defaults from the file system.");
    }

    foreach ($fallbacks as $fallback) {

        /**
         * @psalm-suppress TooManyArguments
         */
        $postId = wp_insert_post(
            [
                'post_title' => $fallback['title'],
                'post_status' => 'publish',
                'post_type' => BREAKDANCE_TEMPLATE_POST_TYPE
            ],
            false,
            false // do we really need to disable the hooks that fire on insert? my guess is yes
        );

        if (is_wp_error($postId)) {
            throw new \Exception("Breakdance couldn't put fallback defaults in the database.");
        }

        /**
         * @var int
         */
        $postId = $postId;

        set_meta($postId, 'breakdance_template_settings', $fallback['breakdance_template_settings']);
        set_meta($postId, 'breakdance_data', $fallback['breakdance_data']);

    }

}

function delete_all_fallback_defaults() {
    $fallbacks = get_all_fallback_defaults();
    foreach ($fallbacks as $fallback) {
        wp_delete_post($fallback->ID, true);
    }
}

/**
 * @return \WP_Post[]
 */
function get_all_fallback_defaults() {

    $templates = getTemplatesAsWPPosts();

    /**
     * @var \WP_Post[]
     */
    $fallbacks = [];

    foreach ($templates as $template) {
        $settings = getTemplateSettingsFromDatabase($template->ID);
        if ($settings['fallback'] ?? false) {
            $fallbacks[] = $template;
        }
    }

    return $fallbacks;
}

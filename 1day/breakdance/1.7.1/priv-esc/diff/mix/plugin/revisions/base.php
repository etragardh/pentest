<?php

namespace Breakdance\Revisions;

require_once __DIR__ . '/ajax.php';

/**
 * A list of fields that are revisioned.
 * @return array{name:string,key:string}
 * @psalm-suppress MixedReturnStatement
 * @psalm-suppress MixedInferredReturnType
 */
function get_revision_meta()
{
    $meta = [
        [
            'name' => 'Breakdance Data',
            'key' => 'breakdance_data'
        ]
    ];

    return apply_filters('breakdance_revision_meta_keys', $meta);
}

/**
 * List all the revisioned keys only
 * @return array
 */
function get_revision_meta_keys()
{
    return array_column(get_revision_meta(), 'key');
}

/**
 * Save the revisioned meta fields.
 * @param int $revision_id The ID of the revision to save the meta to.
 * @psalm-suppress PossiblyInvalidPropertyFetch
 */
function save_revisioned_meta_fields($revision_id)
{
    $revision = get_post($revision_id);

    if (!$revision) {
        return;
    }

    $post_id = $revision->post_parent;

    // Save revisioned meta fields.
    /** @var string $meta_key */
    foreach (get_revision_meta_keys() as $meta_key) {
        copy_post_meta($post_id, $revision_id, $meta_key);
    }
}

// When creating a revision, also save any revisioned meta.
add_action('_wp_put_post_revision', '\Breakdance\Revisions\save_revisioned_meta_fields');

/**
 * Determine whether revisioned post meta fields have changed.
 * @param bool $post_has_changed Whether the post has changed.
 * @param \WP_Post $last_revision The last revision post object.
 * @param \WP_Post $post The post object.
 * @return bool
 */
function revisioned_meta_fields_have_changed($post_has_changed, $last_revision, $post)
{
    /** @var string $meta_key */
    foreach (get_revision_meta_keys() as $meta_key) {
        if (get_post_meta($post->ID, $meta_key) !== get_post_meta($last_revision->ID, $meta_key)) {
            $post_has_changed = true;
            break;
        }
    }

    return $post_has_changed;
}

// When revisioned post meta has changed, trigger a revision save.
add_filter('wp_save_post_revision_post_has_changed', '\Breakdance\Revisions\revisioned_meta_fields_have_changed', 10, 3);

/**
 * Copy post meta for the given key from one post to another.
 * @param int    $source_post_id Post ID to copy meta value(s) from.
 * @param int    $target_post_id Post ID to copy meta value(s) to.
 * @param string $meta_key       Meta key to copy.
 */
function copy_post_meta($source_post_id, $target_post_id, $meta_key)
{
    /** @var array */
    $meta = get_post_meta($source_post_id, $meta_key);

    /** @var string|array $meta_value */
    foreach ($meta as $meta_value) {
        /**
         * We use add_metadata() function vs add_post_meta() here
         * to allow for a revision post target OR regular post.
         */
        add_metadata('post', $target_post_id, $meta_key, wp_slash($meta_value));
    }
}

/**
 * Filters the list of fields saved in post revisions.
 * @param array $fields
 * @return array
 */
function add_fields_to_revisions_screen($fields)
{
    $extra_fields = array_column(get_revision_meta(), 'name', 'key');
    return array_merge($fields, $extra_fields);
}
add_filter('_wp_post_revision_fields', '\Breakdance\Revisions\add_fields_to_revisions_screen');

/**
 * Contextually filter a post revision field.
 * @param mixed $fieldValue
 * @param mixed $fieldName
 * @param \WP_Post $post
 * @return string
 */
function format_breakdance_data_for_comparison($fieldValue, $fieldName, $post)
{
    $rendered = \Breakdance\Render\getRenderedNodes($post->ID, true);

    // Fallback
    if ($rendered === false) {
        return (string) $fieldValue;
    }

    // Make the html look a bit nicer
    return preg_replace("/'\n'/", '""', $rendered['html']);
}
add_filter('_wp_post_revision_field_breakdance_data', '\Breakdance\Revisions\format_breakdance_data_for_comparison', 10, 3);

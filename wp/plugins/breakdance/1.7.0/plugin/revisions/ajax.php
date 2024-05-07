<?php

namespace Breakdance\Revisions;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_retrieve_revisions',
        'Breakdance\Revisions\ajax_retrieve',
        'edit',
        false,
        ['args' => ['id' => FILTER_VALIDATE_INT]]
    );
});

/**
 * @param \WP_Post $revision
 * @return array{id:int,date:string,author:string,tree:mixed}
 */
function format_revision($revision)
{
    $date = (string) get_the_date('', $revision) . ' ' . (string) get_the_time('', $revision);

    $author = get_the_author_meta('display_name', (int) $revision->post_author);

    $tree = (string) \Breakdance\Data\get_meta($revision->ID, 'breakdance_data', 'tree_json_string');

    return [
        'id' => $revision->ID,
        'date' => $date,
        'author' => $author,
        'tree' => $tree
    ];
}

/**
 * @param int $post_id
 * @return array{revisions:array{id:int,date:string,author:string,tree:mixed}[]}|array{unsupported: string}
 */
function ajax_retrieve($post_id)
{
    $postType = get_post_type($post_id) ?: "";
    $postTypeLabel = $postType;
    $supportsRevisions = post_type_supports($postType, 'revisions');

    if (!$supportsRevisions){
        $postTypeObj = $postType ? get_post_type_object($postType) : null;

        if ($postTypeObj) {
            $postTypeLabel  = strtolower($postTypeObj->label);
        }

        return ['unsupported' => "The \"{$postTypeLabel}\" post type does not support revisions."];
    }

    /**
     * @var \WP_Post[]
     */
    $revisions = wp_get_post_revisions($post_id, ['posts_per_page' => 100]);

    // Format the posts nicely
    $revisions = array_map('\Breakdance\Revisions\format_revision', $revisions);

    // Keep visual revisions only
    /**
    * @psalm-suppress MissingClosureReturnType
    * @psalm-suppress MixedArgumentTypeCoercion
    */
    $revisions = array_filter(
        $revisions,
        function ($revision) {
            return $revision['tree'];
        }
    );

    // Reset keys to indexes instead of post IDs
    $revisions = array_values($revisions);

    return [ 'revisions' => $revisions ];
}

<?php

namespace Breakdance\Media\Metadata;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_image_metadata',
        'Breakdance\Media\Metadata\endpoint',
        'edit',
        true,
        ['args' => ['ids' => ['filter' => FILTER_VALIDATE_INT, 'flags' => FILTER_REQUIRE_ARRAY]]]
    );
});

/**
 * @param int $id
 * @param string $size
 * @return string[]
 */
function getMediaSrcset($id, $size = 'full') {
    return [
        'srcset' => (string) wp_get_attachment_image_srcset($id, $size),
        'sizes'  => (string) wp_get_attachment_image_sizes($id, $size),
    ];
}

/**
 * Prepare the media object to the format the frontend expects.
 * @param int $id
 * @return array|null
 */
function prepareMedia($id)
{
    $attachment = wp_prepare_attachment_for_js($id);
    if (!$attachment) return null;

    $fieldsToKeep = ['id', 'filename', 'alt', 'caption', 'url', 'type', 'mime', 'sizes'];

    $filteredAttachment = array_filter(
        $attachment,
        /**
         * @param string $key
         * @return bool
         */
        fn($key) => in_array($key, $fieldsToKeep),
        ARRAY_FILTER_USE_KEY
    );

    $attrs = getMediaSrcset($id);

    return array_merge($filteredAttachment, [
        'attributes' => $attrs
    ]);
}

/**
 * @param int[] $ids
 * @return array{data: array}
 */
function endpoint($ids)
{
    $metadata = array_map('Breakdance\Media\Metadata\prepareMedia', $ids);

    return ['data' => $metadata];
}

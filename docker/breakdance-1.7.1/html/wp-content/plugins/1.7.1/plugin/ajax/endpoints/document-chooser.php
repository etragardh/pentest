<?php

namespace Breakdance\AjaxEndpoints;

use function Breakdance\Themeless\getTemplateByIdIfItExistsAndHasSettings;
use function Breakdance\Themeless\getTemplateSettingsFromDatabase;
use function Breakdance\Util\WP\performant_get_posts;

/**
 * @psalm-type DocumentMeta = array{postType:string,typeLabel:string,titleLabel:string,id:int}
 */

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_get_available_documents_with_search',
        'Breakdance\AjaxEndpoints\getAvailableDocumentsWithSearch',
        'edit',
        true,
        [
            'args' => [
                'search' => FILTER_UNSAFE_RAW
            ]
        ]
    );
});

/**
 * @param string $searchString
 * @return array{data:DocumentMeta[]}
 */
function getAvailableDocumentsWithSearch($searchString)
{
    return ['data' => get_available_documents($searchString)];
}

/**
 * @param string|false $searchString
 * @return DocumentMeta[]
 */
function get_available_documents($searchString = false)
{

    $postTypesWithoutBreakdancePostTypes = \Breakdance\Settings\get_allowed_post_types(false);

    $searchArgs = $searchString ? ['breakdance_search_post_title' => $searchString] : ['numberposts' => TEMPLATE_POSTS_LIMIT];

    $allPostsThatHaveBreakdanceData = performant_get_posts(
        array_merge(
            [
                'post_type' => $postTypesWithoutBreakdancePostTypes,
                'orderby' => 'modified',
                'order' => 'DESC',
                'meta_query' => [
                    [
                        'key' => 'breakdance_data',
                        'compare' => 'EXISTS'
                    ],
                ]
             ],
            $searchArgs
        )
    );

    /** @var string[] $allEditablePostTypes */
    $allEditablePostTypes = BREAKDANCE_ALL_EDITABLE_POST_TYPES ?? [];

    $allPostsThatAreBreakdancePostTypes = performant_get_posts(
        array_merge(
            [
                'post_type' => array_filter($allEditablePostTypes, static function($postType) {
                    return $postType !== 'breakdance_acf_block';
                }),
                'orderby' => 'modified',
                'order' => 'DESC',
             ],
            $searchArgs
        )
    );

    $withoutFallbacks = array_filter(
        $allPostsThatAreBreakdancePostTypes,
        function ($post) {
            $settings = getTemplateSettingsFromDatabase($post->ID);
            return !($settings['fallback'] ?? false);
        }
    );

    return array_map(
        '\Breakdance\AjaxEndpoints\getDocumentMetaFromPost',
        array_merge($withoutFallbacks, $allPostsThatHaveBreakdanceData)
    );
}

/**
 * @param \WP_Post $post
 * @return DocumentMeta
 */
function getDocumentMetaFromPost($post)
{

    // TODO: memoize and/or cache for performance?
    $postTypeObj = get_post_type_object($post->post_type);

    if ($postTypeObj) {
        $labels = get_post_type_labels($postTypeObj);
        $typeLabel = (string) $labels->singular_name;
    } else {
        $typeLabel = $post->post_type;
    }

    $template = getTemplateByIdIfItExistsAndHasSettings($post->ID);
    $templateSettingsType = false;

    if ($template) {
        /**
         * @var TemplateTypeSlug $templateSettingsType
         * @psalm-suppress MixedArrayAccess
         */
        $templateSettingsType = $template['settings']['type'] ?? false;
    }

    return [
        'postType' => $post->post_type,
        'typeLabel' => $typeLabel,
        'titleLabel' => $post->post_title,
        'id' => $post->ID,
        'templateSettingsType' => $templateSettingsType
    ];
}

<?php

namespace Breakdance\Themeless\Rules;

use function Breakdance\Themeless\get_posts_as_template_previewable_items;

add_action('breakdance_register_template_types_and_conditions', '\Breakdance\Themeless\Rules\registerSinglePostTypeRules');

function registerSinglePostTypeRules()
{
    \Breakdance\Themeless\registerTemplateType(
        'Single',
        [
            'slug' => 'all-singles',
            'label' => 'All Singles',

            'callback' => function (): bool {
                return is_singular();
            },
            'templatePreviewableItems' => function () {
                return get_posts_as_template_previewable_items();
            },
            'defaultPriority' => TEMPLATE_PRIORITY_ALL_ARCHIVE_OR_ALL_SINGLE,
        ]
    );

    $postTypes = \Breakdance\Util\get_public_post_types_excluding_templates();

    foreach ($postTypes as $postType) {
        $postTypeObj = get_post_type_object($postType);

        if (!$postTypeObj) {
            continue;
        }

        /**
         * @var string
         */
        $postName = $postTypeObj->labels->name;

        \Breakdance\Themeless\registerTemplateType(
            'Single',
            [
                'slug' => $postType,
                'postType' => $postType,
                'label' => $postName,
                'callback' => function () use ($postType): bool {
                    return is_singular($postType);
                },
                'templatePreviewableItems' => function () use ($postType) {
                    return get_posts_as_template_previewable_items(['post_type' => [$postType]]);
                },
                'defaultPriority' => TEMPLATE_PRIORITY_SPECIFIC_SINGLE,
            ]
        );

        \Breakdance\Themeless\registerTemplateType(
            'Single',
            [
                'slug' => 'front-page',
                'label' => 'Front Page',

                'callback' => function (): bool {
                    return is_front_page();
                },
                'templatePreviewableItems' => function () {
                    $frontPage = (string) get_option('page_on_front');
                    if ($frontPage === "0") {
                        return false;
                    }
                    return get_posts_as_template_previewable_items(['post__in' => [$frontPage]]);
                },
                'defaultPriority' => TEMPLATE_PRIORITY_SPECIFIC_SINGLE,
            ]
        );
    }
}

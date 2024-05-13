<?php

namespace Breakdance\Themeless\Rules;

use Breakdance\Themeless\SearchContext;

use function Breakdance\Themeless\get_post_type_archives_as_template_previewable_items;
use function Breakdance\Util\WP\is_home_when_home_is_the_posts_archive;

add_action(
    'breakdance_register_template_types_and_conditions',
    '\Breakdance\Themeless\Rules\registerArchivePostTypeRules'
);

function registerArchivePostTypeRules()
{
    \Breakdance\Themeless\registerTemplateType(
        'Archive',
        [
            'slug' => 'post-type-archive',
            'label' => 'Post Type Archive',
            'callback' => function (): bool {
                // is_post_type_archive() = true only for post types created with 'archive => true'
                // https://developer.wordpress.org/themes/basics/conditional-tags/#a-post-type-archive
                // is_home() = the blog homepage, which can be the "front page".
                return is_post_type_archive() || is_home_when_home_is_the_posts_archive();
            },
            'templatePreviewableItems' =>
            /**
             * @return TemplatePreviewableItem[]
             */
                function () {
                    return get_post_type_archives_as_template_previewable_items();
                },
            'defaultPriority' => TEMPLATE_PRIORITY_SPECIFIC_ARCHIVE,
        ]
    );

    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'templating'],
            'availableForType' => ['post-type-archive', 'all-archives'],
            'slug' => 'post-type-archive',
            'label' => 'Post Type',
            'category' => 'Archive',
            'operands' => [OPERAND_IS, OPERAND_IS_NOT],
            'values' => function () {
                return [
                    [
                        'label' => 'Post Types',
                        'items' => getPostTypesForMultiselect(),
                    ],
                ];
            },
            'callback' =>
            /**
             * @param string $operand
             * @param string[] $value
             * @return bool
             */
                function ($operand, $value) {
                    if (!$value) {
                        return false;
                    }


                    if (in_array('post', $value) && is_home_when_home_is_the_posts_archive()) {
                        switch ($operand) {
                            case OPERAND_IS:
                                return true;
                            case OPERAND_IS_NOT:
                                return false;
                        }
                    }

                    switch ($operand) {
                        case OPERAND_IS:
                            return is_post_type_archive($value);
                        case OPERAND_IS_NOT:
                            return !is_post_type_archive($value);
                        default:
                            return false;
                    }
                },
            'templatePreviewableItems' =>
            /**
             * @param string $operand
             * @param string[] $values
             * @return TemplatePreviewableItem[]
             */
                function ($operand, $values) {
                    if ($operand === OPERAND_IS){
                        return get_post_type_archives_as_template_previewable_items($values);
                    }

                    if ($operand === OPERAND_IS_NOT){
                        return get_post_type_archives_as_template_previewable_items(
                            array_filter(
                                \Breakdance\Util\get_post_types_with_archives(),
                                function($postType) use ($values){
                                    return !in_array($postType, $values);
                                }
                            )
                        );
                    }

                    return [];
                },
        ]
    );
}

/**
 * @return TemplateConditionValue[]
 */
function getPostTypesForMultiselect()
{
    $postTypes = \Breakdance\Util\get_post_types_with_archives(SearchContext::getInstance()->search);

    return array_map(function ($postType) {
        $postTypeObject = get_post_type_object($postType);

        return [
            'text' => $postTypeObject ? $postTypeObject->label : $postType,
            'value' => $postType,
        ];
    }, $postTypes);
}

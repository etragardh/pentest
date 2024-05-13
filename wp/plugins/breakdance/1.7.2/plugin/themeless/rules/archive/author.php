<?php

namespace Breakdance\Themeless\Rules;

use Breakdance\Themeless\SearchContext;

use function Breakdance\Themeless\get_author_archives_as_template_previewable_items;
use function Breakdance\Themeless\get_specific_author_archives_as_template_previewable_items;
use function Breakdance\Util\WP\get_authors;

add_action(
    'breakdance_register_template_types_and_conditions',
    '\Breakdance\Themeless\Rules\registerArchiveAuthorRules'
);

function registerArchiveAuthorRules()
{
    \Breakdance\Themeless\registerTemplateType(
        'Archive',
        [
            'slug' => 'author-archive',
            'label' => 'Author Archive',
            'callback' => function (): bool {
                return is_author();
            },
            'templatePreviewableItems' => function () {
                return get_author_archives_as_template_previewable_items();
            },
            'defaultPriority' => TEMPLATE_PRIORITY_SPECIFIC_ARCHIVE,
        ]
    );

    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'templating'],
            'availableForType' => ['author-archive', 'all-archives'],
            'slug' => 'author',
            'label' => 'Author',
            'category' => 'Archive',
            'operands' => [OPERAND_IS, OPERAND_IS_NOT],
            'values' => function () {
                return [
                    [
                        'label' => 'Authors',
                        'items' => getAuthorItemsForDropdown(),
                    ],
                ];
            },
            'callback' =>
            /**
             * @param string $operand
             * @param string[] $value
             */
                function ($operand, $value): bool {
                    $author = is_author(
                        array_map(function ($str) {
                            return (int)$str;
                        },
                            $value)
                    );

                    switch ($operand) {
                        case OPERAND_IS:
                            return $author;
                        case OPERAND_IS_NOT:
                            return !$author;
                        default:
                            return false;
                    }
                },

            'templatePreviewableItems' =>
            /**
             * @param string $operand
             * @param string[] $authorsId
             * @return TemplatePreviewableItem[]
             */
                function ($operand, $authorsId) {
                    return get_specific_author_archives_as_template_previewable_items($authorsId, $operand);
                },
        ]
    );
}

/**
 * @return TemplateConditionValue[]
 */
function getAuthorItemsForDropdown()
{
    return array_map(
    /** @param \WP_User $user */
        function ($user) {
            return [
                'text' => $user->display_name,
                'value' => (string)$user->ID,
            ];
        },
        get_authors(SearchContext::getInstance()->search)
    );
}

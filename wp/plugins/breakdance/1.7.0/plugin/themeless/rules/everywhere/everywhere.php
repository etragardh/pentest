<?php

namespace Breakdance\Themeless\Rules;

use function Breakdance\Themeless\get_all_archives_as_template_previewable_items;
use function Breakdance\Themeless\get_posts_as_template_previewable_items;

add_action('breakdance_register_template_types_and_conditions', '\Breakdance\Themeless\Rules\registerEverywhereEverywhereRules');

function registerEverywhereEverywhereRules()
{
    \Breakdance\Themeless\registerTemplateType(
        'Everywhere',
        [
            'slug' => 'everywhere',
            'label' => 'Everywhere',
            'callback' => function (): bool {
                return true;
            },

            'templatePreviewableItems' => function () {
                return array_merge(
                    get_posts_as_template_previewable_items(),
                    get_all_archives_as_template_previewable_items()
                );
            },
            'defaultPriority' => TEMPLATE_PRIORITY_CATCH_ALL,
        ]
    );
}

<?php

namespace Breakdance\Themeless\Rules;

use function Breakdance\Themeless\get_date_archives_as_template_previewable_items;

add_action('breakdance_register_template_types_and_conditions', '\Breakdance\Themeless\Rules\registerArchiveDateRules');

function registerArchiveDateRules()
{
    \Breakdance\Themeless\registerTemplateType(
        'Archive',
        [
            'slug' => 'date-archive',
            'label' => 'Date Archive',

            'callback' => function (): bool {
                return is_date();
            },

            'templatePreviewableItems' => function () {
                return get_date_archives_as_template_previewable_items();
            },
            'defaultPriority' => TEMPLATE_PRIORITY_SPECIFIC_ARCHIVE,
        ]
    );
}

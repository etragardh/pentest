<?php

namespace Breakdance\Themeless\Rules;

use function Breakdance\Themeless\get_all_archives_as_template_previewable_items;

add_action('breakdance_register_template_types_and_conditions', '\Breakdance\Themeless\Rules\registerPostArchiveRules');

function registerPostArchiveRules()
{
    // Applies to all post archives except WooCommerce ones. If Woo is disabled, works the same as "all archives"
    \Breakdance\Themeless\registerTemplateType(
        'Archive',
        [
            'slug' => 'post-archives',
            'label' => 'Post Archives',
            'callback' =>
                function (): bool {
                    // "is_archive" doesn't apply to the posts page, we need "is_home" for that
                    $isPostArchive = \is_archive() || \is_home();

                    if (class_exists('woocommerce')) {
                        /**
                         * @psalm-suppress UndefinedFunction
                         */
                        return $isPostArchive && !\is_product_category() && !\is_product_tag() && !\is_woocommerce();
                    }

                    return $isPostArchive;
                },

            'templatePreviewableItems' =>
                function () {
                    $allArchives = get_all_archives_as_template_previewable_items();

                    return array_filter($allArchives, function ($archive) {
                        // Unless another plugin creates a "product" taxonomy, this should work
                        return strpos(strtolower($archive['type']), 'product') !== 0;
                    });
                },
            'defaultPriority' => TEMPLATE_PRIORITY_ALL_ARCHIVE_OR_ALL_SINGLE
        ]
    );
}

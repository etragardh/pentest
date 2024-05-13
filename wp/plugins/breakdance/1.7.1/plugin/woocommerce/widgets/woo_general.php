<?php

namespace Breakdance\WooCommerce\Widgets;

use Breakdance\Elements\PresetSections\PresetSectionsController;
use Breakdance\WPWidgets\WidgetsController;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\WPWidgets\getWidgetControlSections;
use function Breakdance\WPWidgets\getWidgetsAsDropdownItems;

\Breakdance\WPWidgets\register(
    [
        'slug' => 'wc_products_by_rating_list',
        'name' => 'Products By Rating List',
        'className' => 'WC_Widget_Top_Rated_Products',
        'category' => "woo_general",
        'controls' => [
            control(
                'title',
                'Title',
                ['type' => 'text']
            ),
            control(
                'number',
                'Number of products',
                ['type' => 'number']
            )
        ]
    ]
);


\Breakdance\WPWidgets\register([
    'slug' => 'wc_product_search',
    'name' => 'Product Search',
    'className' => 'WC_Widget_Product_Search',
    'category' => "woo_general",
    'controls' => [
        control(
            'title',
            'Title',
            ['type' => 'text']
        )
    ]
]);

\Breakdance\WPWidgets\register([
    'slug' => 'wc_product_categories',
    'name' => 'Product Categories',
    'className' => 'WC_Widget_Product_Categories',
    'category' => "woo_general",
    'controls' => [
        control(
            'title',
            'Title',
            ['type' => 'text']
        ),
        control(
            'orderby',
            'Order by',
            [
                'type' => 'dropdown',
                'items' => [
                    ['value' => 'order', 'text' => 'Category order'],
                    ['value' => 'name', 'text' => 'Name'],
                ]
            ]
        ),
        control(
            'dropdown',
            'Show as dropdown',
            [
                'type' => 'toggle'
            ]
        ),
        control(
            'count',
            'Show product counts',
            [
                'type' => 'toggle'
            ]
        ),
        control(
            'hierarchical',
            'Show hierarchy',
            [
                'type' => 'toggle'
            ]
        ),
        control(
            'show_children_only',
            'Only show children',
            [
                'type' => 'toggle'
            ]
        ),
        control(
            'hide_empty',
            'Hide empty categories',
            [
                'type' => 'toggle'
            ]
        ),
        control(
            'max_depth',
            'Maximum depth',
            [
                'type' => 'text'
            ]
        ),
    ]
]);

// \Breakdance\WPWidgets\register([
//     'slug' => 'wc_product_tag_Cloud',
//     'name' => 'Product Tag Cloud',
//     'className' => 'WC_Widget_Product_Tag_Cloud',
//     'category' => "woo_general",
//     'controls' => [
//         control(
//             'title',
//             'Title',
//             ['type' => 'text']
//         )
//     ]
// ]);

// \Breakdance\WPWidgets\register([
//     'slug' => 'wc_recently_viewed_products',
//     'name' => 'Recently Viewed Products',
//     'className' => 'WC_Widget_Recently_Viewed',
//     'category' => "woo_general",
//     'controls' => [
//         control(
//             'title',
//             'Title',
//             ['type' => 'text']
//         ),
//         control(
//             'number',
//             'Number of products',
//             ['type' => 'number']
//         )
//     ]
// ]);

\Breakdance\WPWidgets\register([
    'slug' => 'wc_recently_reviews',
    'name' => 'Recent Reviews',
    'className' => 'WC_Widget_Recent_Reviews',
    'category' => "woo_general",
    'controls' => [
        control(
            'title',
            'Title',
            ['type' => 'text']
        ),
        control(
            'number',
            'Number',
            ['type' => 'number']
        )
    ]
]);

// \Breakdance\WPWidgets\register([
//     'slug' => 'wc_products_list',
//     'name' => 'Products List',
//     'className' => 'WC_Widget_Products',
//     'category' => "woo_general",
//     'controls' => [
//         control(
//             'title',
//             'Title',
//             ['type' => 'text']
//         ),
//         control(
//             'number',
//             'Number of products',
//             ['type' => 'number']
//         ),
//         control(
//             'show',
//             'Show',
//             ['type' => 'dropdown', 'items' => [
//                 ['value' => '', 'text' => 'All products'],
//                 ['value' => 'featured', 'text' => 'Featured products'],
//                 ['value' => 'onsale', 'text' => 'On-sale products']
//             ]]
//         ),
//         control(
//             'order',
//             'Order',
//             ['type' => 'dropdown', 'items' => [
//                 ['value' => 'asc', 'text' => 'Ascending'],
//                 ['value' => 'desc', 'text' => 'Descending'],
//             ]]
//         ),
//         control(
//             'hide_free',
//             'Hide free products',
//             ['type' => 'toggle']
//         ),
//         control(
//             'show_hidden',
//             'Show hidden products',
//             ['type' => 'toggle']
//         ),
//     ]
// ]);

add_action(
    'init',
    function() {
        $widgets = WidgetsController::getInstance()->getWidgetsByCategory("woo_general");

        PresetSectionsController::getInstance()->register(
            "EssentialElements\\woo_widget",
            controlSection(
                'woo_widget',
                'Woo Widget',
                array_merge(
                    [
                        control(
                            'widget',
                            'Widget',
                            ['type' => 'dropdown', 'items' => getWidgetsAsDropdownItems($widgets), 'layout' => 'vertical'],
                        )
                    ],
                    getWidgetControlSections($widgets)
                )
            ),
            true
        );
    },
    1000 /* needed because WidgetsController::getInstance()->widgets is also populated
    on init, so we need to run this after it's already populated */
);

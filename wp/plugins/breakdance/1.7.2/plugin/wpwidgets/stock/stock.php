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
        'slug' => 'wp_text',
        'name' => 'Text',
        'className' => 'WP_Widget_Text',
        'category' => "wp_general",
        'controls' => [
            control(
                'title',
                'Title',
                ['type' => 'text']
            ),
            control(
                'text',
                'Text',
                ['type' => 'text']
            )
        ]
    ]
);

\Breakdance\WPWidgets\register([
    'slug' => 'wp_archives',
    'name' => 'Archives',
    'className' => 'WP_Widget_Archives',
    'category' => "wp_general",
    'controls' => [
        control(
            'title',
            'Title',
            ['type' => 'text']
        ),
        control(
            'dropdown',
            'Display as dropdown',
            ['type' => 'toggle']
        ),
        control(
            'count',
            'Show post count',
            ['type' => 'toggle']
        )
    ]
]);

\Breakdance\WPWidgets\register([
    'slug' => 'wp_categories',
    'name' => 'Categories',
    'className' => 'WP_Widget_Categories',
    'category' => "wp_general",
    'controls' => [
        control(
            'title',
            'Title',
            ['type' => 'text']
        ),
        control(
            'dropdown',
            'Display as dropdown',
            ['type' => 'toggle']
        ),
        control(
            'hierarchical',
            'Show hierarchy',
            ['type' => 'toggle']
        ),
        control(
            'count',
            'Show post count',
            ['type' => 'toggle']
        )
    ]
]);

\Breakdance\WPWidgets\register([
    'slug' => 'wp_calendar',
    'name' => 'Calendar',
    'className' => 'WP_Widget_Calendar',
    'category' => "wp_general",
    'controls' => [
        control(
            'title',
            'Title',
            ['type' => 'text']
        )
    ]
]);

\Breakdance\WPWidgets\register([
    'slug' => 'wp_recent_comments',
    'name' => 'Recent Comments',
    'className' => 'WP_Widget_Recent_Comments',
    'category' => "wp_general",
    'controls' => [
        control(
            'title',
            'Title',
            ['type' => 'text']
        ),
        control(
            'number',
            'Number of comments to show',
            ['type' => 'number']
        )
    ]
]);

\Breakdance\WPWidgets\register([
    'slug' => 'wp_tag_cloud',
    'name' => 'Tag Cloud',
    'className' => 'WP_Widget_Tag_Cloud',
    'category' => "wp_general",
    'controls' => [
        control(
            'title',
            'Title',
            ['type' => 'text']
        ),
        control(
            'taxonomy',
            'Taxonomy',
            [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => [
                    ['value' => 'category', 'text' => 'Categories'],
                    ['value' => 'post_tag', 'text' => 'Tags'],
                    ['value' => 'link_category', 'text' => 'Link Categories']
                ]
            ]
        ),
        control(
            'count',
            'Show tag count',
            ['type' => 'toggle']
        )
    ]
]);

\Breakdance\WPWidgets\register([
    'slug' => 'wp_rss',
    'name' => 'RSS',
    'className' => 'WP_Widget_RSS',
    'category' => "wp_general",
    'controls' => [
        control(
            'url',
            'URL',
            ['type' => 'text']
        ),
        control(
            'title',
            'Title',
            ['type' => 'text']
        ),
        control(
            'items',
            'Items',
            [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => array_map(static function($i) {
                    return ['text' => strval($i), 'value' => strval($i)];
                }, range(1, 20))
            ]
        ),
        control(
            'show_summary',
            'Display item content',
            ['type' => 'toggle']
        ),
        control(
            'show_author',
            'Display item author',
            ['type' => 'toggle']
        ),
        control(
            'show_date',
            'Display item date',
            ['type' => 'toggle']
        )
    ]
]);

\Breakdance\WPWidgets\register([
    'slug' => 'wp_recent_posts',
    'name' => 'Recent Posts',
    'className' => 'WP_Widget_Recent_Posts',
    'category' => "wp_general",
    'controls' => [
        control(
            'title',
            'Title',
            ['type' => 'text']
        ),
        control(
            'number',
            'Number of posts to show',
            ['type' => 'number']
        ),
        control(
            'show_date',
            'Show date',
            ['type' => 'toggle']
        ),
    ]
]);


\Breakdance\WPWidgets\register([
    'slug' => 'wp_pages',
    'name' => 'Pages',
    'className' => 'WP_Widget_Pages',
    'category' => "wp_general",
    'controls' => [
        control(
            'title',
            'Title',
            ['type' => 'text']
        ),
        control(
            'sortby',
            'Sort by',
            ['type' => 'dropdown']
        ),
        control(
            'exclude',
            'Exclude',
            ['type' => 'text', 'items' => [
                ['value' => 'post_title', 'text' => 'Page Title'],
                ['value' => 'menu_order', 'text' => 'Page Order'],
                ['value' => 'ID', 'text' => 'Page ID']
            ]]
        ),
    ]
]);

add_action(
    'init',
    function() {
        $widgets = WidgetsController::getInstance()->getWidgetsByCategory("wp_general");

        PresetSectionsController::getInstance()->register(
            "EssentialElements\\wp_widget",
            controlSection(
                'wp_widget',
                'WP Widget',
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

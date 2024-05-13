<?php

namespace Breakdance\WooCommerce\Widgets;

use Breakdance\Elements\PresetSections\PresetSectionsController;
use Breakdance\WPWidgets\WidgetsController;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\repeaterControl;
use function Breakdance\WPWidgets\getWidgetControlSections;
use function Breakdance\WPWidgets\getWidgetsAsDropdownItems;

require_once __DIR__ . "/woo_filters_attribute_filter_get_attribute_array.php";


\Breakdance\WPWidgets\register(
    [
        'slug' => 'active_filters',
        'name' => 'Active Filters',
        'className' => 'WC_Widget_Layered_Nav_Filters',
        'category' => "woo_filter",
        'controls' => [
            control(
                'title',
                'Title',
                ['type' => 'text']
            )
        ]
    ]
);

\Breakdance\WPWidgets\register(
    [
        'slug' => 'price_filter',
        'name' => 'Price Filter',
        'className' => 'WC_Widget_Price_Filter',
        'category' => "woo_filter",
        'controls' => [
            control(
                'title',
                'Title',
                ['type' => 'text']
            )
        ]
    ]
);


\Breakdance\WPWidgets\register(
    [
        'slug' => 'rating_filter',
        'name' => 'Rating Filter',
        'className' => 'WC_Widget_Rating_Filter',
        'category' => "woo_filter",
        'controls' => [
            control(
                'title',
                'Title',
                ['type' => 'text']
            )
        ]
    ]
);

\Breakdance\WPWidgets\register(
    [
        'slug' => 'attribute_filter',
        'name' => 'Filter By Attribute',
        'className' => 'WC_Widget_Layered_Nav',
        'category' => "woo_filter",
        'controls' => [
            control(
                'title',
                'Title',
                ['type' => 'text']
            ),
            control(
                'attribute',
                'Attribute',
                [
                    'type' => 'dropdown',
                    'items' => attsWrapperFunc(),
                ]
            ),
            control(
                'display_type',
                'Display Type',
                ['type' => 'dropdown', 'items' => [
                    ['text' => 'list', 'value' => 'list'],
                    ['text' => 'dropdown', 'value' => 'dropdown'],
                ]]
            ),
            control(
                'query_type',
                'Query Type',
                ['type' => 'dropdown', 'items' => [
                    ['text' => 'and', 'value' => 'and'],
                    ['text' => 'or', 'value' => 'or'],
                ]]
            )
        ]
    ]
);


add_action(
    'init',
    function() {
        $widgets = WidgetsController::getInstance()->getWidgetsByCategory("woo_filter");

        PresetSectionsController::getInstance()->register(
            "EssentialElements\\shop_filters",
            controlSection(
                'shop_filters',
                'Shop Filters',
                [
                    repeaterControl('widgets', 'Widgets',
                        array_merge(
                            [
                                control(
                                    'widget',
                                    'Widget',
                                    ['type' => 'dropdown', 'items' => getWidgetsAsDropdownItems($widgets), 'layout' => 'vertical'],
                                )
                            ],
                            getWidgetControlSections($widgets),
                        ),
                        [
                            'repeaterOptions' => [
                                'titleTemplate' => '{widget}', 'defaultTitle' => 'Filter', 'buttonName' => 'Add Filter'
                            ]
                        ]
                    )
                ]
            ),
            true
        );
    },
    1000 /* needed because WidgetsController::getInstance()->widgets is also populated
    on init, so we need to run this after it's already populated */
);


/**
 * @psalm-suppress MixedInferredReturnType
 * @return array
 */
function attsWrapperFunc() {
    /**
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress UndefinedFunction
     */
    return attribute_filter_get_attribute_array();
}

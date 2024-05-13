<?php

namespace Breakdance\WPWidgets;

use Breakdance\Elements\PresetSections\PresetSectionsController;
use function Breakdance\Elements\controlSection;

class WidgetsController
{

    use \Breakdance\Singleton;

    /**
     * @var array<string,WPWidget>
     */
    public $widgets = [];

    /**
     * @param WPWidget $widget
     * @return void
     */
    function register($widget) {
        $this->widgets[$widget['slug']] = $widget;

        PresetSectionsController::getInstance()->register(
            "EssentialElements\\" . $widget['slug'],
            controlSection(
                $widget['slug'],
                $widget['name'],
                $widget['controls']
            )
        );

    }

    /**
     * @param string $widgetSlug
     * @param array $propertiesDataForWidgetSubsection
     * @return void
     */
    function render($widgetSlug, $propertiesDataForWidgetSubsection) {
        $widget = $this->widgets[$widgetSlug];

        // do we want more flexibility so we should provide a PHP function as the render callback?
        // idk. i bet just using the_widget and passing in the raw value from our properties data
        // is all that we need

        the_widget(
            $widget['className'],
            $propertiesDataForWidgetSubsection,
            // [
            //     'before_widget' => '',
            //     'after_widget' => '',
            //     'before_title' => '',
            //     'after_title' => ''
            // ]
        );

    }

    /**
     * @param string $categorySlug
     * @return WPWidget[]
     */
    function getWidgetsByCategory($categorySlug) {
        return array_filter(
            $this->widgets,
            function ($widget) use ($categorySlug) {
                return $widget['category'] === $categorySlug;
            }
        );
    }

}


/**
 * @param WPWidget $widget
 * @return void
 */
function register($widget) {
    add_action('init', function() use ($widget) {
        WidgetsController::getInstance()->register($widget);
    });
}

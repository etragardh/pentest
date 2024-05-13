<?php
/**
 * @var array $propertiesData
 */

use Breakdance\WPWidgets\WidgetsController;

$widget = $propertiesData['content']['content'] ?? ['widget' => null];

if (!$widget['widget']) {
    echo "Please choose a widget from the dropdown.";
}

WidgetsController::getInstance()->render($widget['widget'], $widget[$widget['widget']] ?? []);


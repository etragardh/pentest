<div class='breakdance-woocommerce'>
    <?php
    /**
     * @var array $propertiesData
     */

    use Breakdance\WPWidgets\WidgetsController;

    $widgets = $propertiesData['content']['filters']['widgets'] ?? [];

    foreach ($widgets as $widget) {
        WidgetsController::getInstance()->render($widget['widget'], $widget[$widget['widget']]);
    }

    ?>
</div>

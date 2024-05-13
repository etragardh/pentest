<div class='breakdance-woocommerce'>
    <?php
    /**
     * @var array $propertiesData
     */

    use Breakdance\WPWidgets\WidgetsController;

    $widgetData = $propertiesData['content']['content'];
    $widgetToRender = $widgetData['widget'] ?? false;

    if ($widgetToRender) {
        $widgetOptions = $widgetData[$widgetToRender] ?? [];
        WidgetsController::getInstance()->render($widgetToRender, $widgetOptions);
    }

    ?>
</div>

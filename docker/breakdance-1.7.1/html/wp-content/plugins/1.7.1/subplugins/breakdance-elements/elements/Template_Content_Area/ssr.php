<?php
/**
 * @var array $propertiesData
 */

$templateToRenderId = \Breakdance\Themeless\ThemelessController::getInstance()->popHierarchy();

if ($templateToRenderId) {
    echo (string) \Breakdance\Render\render($templateToRenderId);
} else {
    while (have_posts()) {
        the_post();
        the_content();
    }
}

?>
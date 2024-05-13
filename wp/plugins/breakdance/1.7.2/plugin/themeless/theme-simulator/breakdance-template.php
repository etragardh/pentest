<?php

namespace Breakdance\Themeless;

use function Breakdance\Render\render;

/**
 * For block-enabled themes, block rendering needs to run
 * before <head> so that blocks can add scripts and styles in wp_head().
 *
 * @see wp-includes/template-canvas.php Which is used by WP to render block-enabled themes
 */

$templateId = ThemelessController::getInstance()->popHierarchy();
// Template is rendered first, allowing its code to register assets for wp_head
$renderedTemplateHtml = render($templateId);

[
    'headerHtml' => $headerHtml,
    'emulateDocumentBeginningHtml' => $emulateDocumentBeginningHtml
] = get_header_for_theme_simulator_having_breakdance_template_for_request();


// In case header doesn't contain it already, output the HTML document beginning
// which contains wp_head() output – with scripts and styles added when rendering template
if ($emulateDocumentBeginningHtml) {
    outputHeadHtml();
}

// Footer needs to be rendered after wp_head
// @see \WP_Scripts::do_item Which registers certain assets to footer
$footerHtml = get_footer_for_theme_simulator_having_breakdance_template_for_request();

echo $headerHtml . $renderedTemplateHtml . $footerHtml;


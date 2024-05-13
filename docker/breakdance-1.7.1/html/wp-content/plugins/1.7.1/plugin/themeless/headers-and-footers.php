<?php

namespace Breakdance\Themeless;

use function Breakdance\isRequestFromBuilderIframe;
use function Breakdance\Themeless\ThemeDisabler\is_theme_disabled;

add_filter('pre_render_block', '\Breakdance\Themeless\override_block_theme_header_or_footer', PHP_INT_MAX, 3);

/**
 * @param string|null $pre_render
 * @param array{blockName: string, attrs:array<string, string>} $parsed_block
 * @param \WP_Block|null $parent_block
 * @return string|null
 */
function override_block_theme_header_or_footer($pre_render, $parsed_block, $parent_block)
{
    if ($parsed_block['blockName'] === 'core/template-part' && isset($parsed_block['attrs']['slug'])) {
        $slug = $parsed_block['attrs']['slug'];

        /** @var \WP_Block_Template[] $template_parts */
        $template_parts = get_block_templates(array(
            'theme' => wp_get_theme()->get_stylesheet(),
            'slug__in' => [$slug]
        ), 'wp_template_part');

        $template_part = reset($template_parts);

        if ($template_part !== false) {
            if ($template_part->area === WP_TEMPLATE_PART_AREA_HEADER) {
                $header_template_of_breakdance = get_breakdance_header_template_for_request();
                if ($header_template_of_breakdance !== false) {
                    return $header_template_of_breakdance;
                }
            } elseif ($template_part->area === WP_TEMPLATE_PART_AREA_FOOTER) {
                $footer_template_of_breakdance = get_breakdance_footer_template_for_request();
                if ($footer_template_of_breakdance !== false) {
                    return $footer_template_of_breakdance;
                }
            }
        }
    }

    return $pre_render;
}

/**
 * @return string
 */
function getBlockHeaderArea() {
    ob_start();
    // WP >= 5.9.0
    if (function_exists('block_header_area')) {
        block_header_area();
    }
    return (string) ob_get_clean();
}

/**
 * @return array{headerHtml: string, emulateDocumentBeginningHtml: bool}
 */
function get_header_for_theme_simulator_having_breakdance_template_for_request()
{
    if (is_theme_disabled()) {
        return [
            'headerHtml' => (string) get_breakdance_header_template_for_request(),
            'emulateDocumentBeginningHtml' => true,
        ];
    } elseif (current_theme_supports('block-templates')) {
        $header_template_of_breakdance = get_breakdance_header_template_for_request();

        return [
            'headerHtml' => $header_template_of_breakdance === false ? getBlockHeaderArea() : $header_template_of_breakdance,
            'emulateDocumentBeginningHtml' => true,
        ];
    } else {
        ob_start();
        get_header();

        return [
            'headerHtml' => (string) ob_get_clean(),
            'emulateDocumentBeginningHtml' => false,
        ];
    }
}

/**
 * @return string
 */
function get_footer_for_theme_simulator_having_breakdance_template_for_request()
{
    ob_start();
    $isThemeDisabled = is_theme_disabled();
    if ($isThemeDisabled) {
        echo (string) get_breakdance_footer_template_for_request();
        outputFootHtml();
    } else {
        if (current_theme_supports('block-templates')) {
            $footer_template_of_breakdance = get_breakdance_footer_template_for_request();
            if ($footer_template_of_breakdance !== false) {
                echo $footer_template_of_breakdance;
            } else {
                // WP >= 5.9.0
                if (function_exists('block_footer_area')) {
                    block_footer_area();
                }
            }
            outputFootHtml();
        } else {
            get_footer();
        }
    }

    return (string) ob_get_clean();
}


/**
 * @return bool
 */
function postTypeIsHeaderOrFooterOrTemplateOrGlobalBlockOrPopup() {
    return in_array(get_post_type(), BREAKDANCE_ALL_EDITABLE_POST_TYPES);
}

/**
 * @return string|false
 */
function get_breakdance_header_template_for_request() {

    if (isRequestFromBuilderIframe() && postTypeIsHeaderOrFooterOrTemplateOrGlobalBlockOrPopup()) {
        return "";
    }

    $header = _getTemplateForRequest(ThemelessController::getInstance()->headers);

    if ($header) {
        return \Breakdance\Render\render($header['id']);
    }

    return false;
}

/**
 * @return string|false
 */
function get_breakdance_footer_template_for_request() {

    if (isRequestFromBuilderIframe() && postTypeIsHeaderOrFooterOrTemplateOrGlobalBlockOrPopup()) {
        return "";
    }

    $footer = _getTemplateForRequest(ThemelessController::getInstance()->footers);

    if ($footer) {
        return \Breakdance\Render\render($footer['id']);
    }

    return false;
}

add_action("get_header", "\Breakdance\Themeless\override_get_header");

/**
 * @param string $headerName
 * @return void
 */
function override_get_header($headerName = '') {
    $header = get_breakdance_header_template_for_request();

    if ($header !== false) {
        outputHeadHtml();
        echo $header;

        $templates = ['header.php'];
        if ($headerName) {
            $templates[] = 'header-'.$headerName.".php";
        }

        remove_all_actions("wp_head");
        ob_start();
        locate_template($templates, true); // uses require_once, so it won't be loaded again by theme
        ob_get_clean();
    }

}

add_action("get_footer", "\Breakdance\Themeless\override_get_footer");

/**
 * @param string $footerName
 * @return void
 */
function override_get_footer($footerName = '') {

    $footer = get_breakdance_footer_template_for_request();

    if ($footer !== false) {

        echo $footer;
        outputFootHtml();

        $templates = ['footer.php'];
        if ($footerName) {
            $templates[] = 'footer-'.$footerName.".php";
        }
        remove_all_actions("wp_footer");
        ob_start();
        locate_template($templates, true);
        ob_get_clean();

    }

}

?>

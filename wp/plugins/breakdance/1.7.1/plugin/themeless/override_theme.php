<?php

namespace Breakdance\Themeless;

use function Breakdance\MaintenanceMode\visitorShouldSeeAMaintenancePage;
use function Breakdance\isRequestFromBuilderIframe;
use function Breakdance\Themeless\ThemeDisabler\is_zero_theme_enabled;

/**
 * fired by template_include
 * @param string $file_to_include
 * @return string
 */
function maybe_override_the_theme_with_a_breakdance_template($file_to_include)
{
    if (wordpressPermalinksAreBroken()) {
        $brokenPermalinksPath = plugin_dir_path(__FILE__) . 'theme-simulator/broken-permalinks-warning.php';
        return $brokenPermalinksPath;
    }

    if (visitorShouldSeeAMaintenancePage()) {
        $maintenanceTemplate = plugin_dir_path(__FILE__) . 'theme-simulator/maintenance-mode.php';
        return $maintenanceTemplate;
    }

    if (userIsEditingHeaderOrFooterOrTemplateOrGlobalBlockOrPopup()) {
        return plugin_dir_path(__FILE__)
            . 'theme-simulator/breakdance-no-template.php';
    }

    if (is_singular() && post_password_required()) {
        return plugin_dir_path(__FILE__)
            . 'theme-simulator/breakdance-post-password-required.php';
    }

    /* has the user chosen the blank canvas template? */
    if (is_singular()) {
        /**
         * @psalm-suppress InvalidGlobal
         */
        global $post;

        /**
         * @psalm-suppress MixedAssignment
         * @psalm-suppress MixedPropertyFetch
         * @psalm-suppress MixedArgument
         */
        $pageTemplate = get_post_meta($post->ID, '_wp_page_template', true);

        if ($pageTemplate === 'breakdance_blank_canvas') {
            return plugin_dir_path(__FILE__) . 'theme-simulator/breakdance-blank-canvas.php';
        }
    }

    /* ok, they didnt choose the blank canvas template. is there a template for the request setup at Breakdance -> Templates? */

    $templateForRequest
        = getTemplateForRequest(ThemelessController::getInstance()->templates);

    $isThemeDisabled = \Breakdance\Themeless\ThemeDisabler\is_theme_disabled();


    /** logic is as follows:
     *
     * loading the header and footer loads the one created in breakdance,
     * and then falls back to the theme if one doesn't exist
     *
     * is there a template that applies?
     *
     * regardless of whether the theme is enabled or not, the logic is the same:
     * load the header, render the template, load the footer
     *
     * does no template apply?
     *
     * if file to include is handled by another plugin, use that file: https://github.com/soflyy/breakdance/pull/4285
     *
     * otherwise...
     *
     * if the theme is enabled, just render the theme
     * if the theme is disabled, load the header, render the post (if no
     * breakdance content, fallback to the_content and make it pretty),
     * and finally load the footer
     *
     */


    if ($templateForRequest) {
        ThemelessController::getInstance()->buildTemplateHierarchyForRequest($templateForRequest['id']);
        return plugin_dir_path(__FILE__) . 'theme-simulator/breakdance-template.php';
    } else if (isFileToIncludeHandledByOtherPlugin($file_to_include)) {
        return $file_to_include;
    } else {
        $file_to_include = realpath($file_to_include);
        $parentThemeDirectory = realpath(get_template_directory());
        $currentThemeDirectory = realpath(get_stylesheet_directory());
        $fileToIncludeIsATemplateFromZeroTheme =
            str_starts_with($file_to_include, $parentThemeDirectory) ||
            str_starts_with($file_to_include, $currentThemeDirectory);

        if ($isThemeDisabled || (is_zero_theme_enabled() && !$fileToIncludeIsATemplateFromZeroTheme)) {
            return plugin_dir_path(__FILE__) . 'theme-simulator/breakdance-no-template.php';
        }
        else {
            return $file_to_include;
        }
    }
}

/**
 * @param string $file_to_include
 * @return boolean
 */
function isFileToIncludeHandledByOtherPlugin($file_to_include)
{
    $file_to_include = realpath(trim($file_to_include));

    /**
     * @psalm-suppress UndefinedConstant
     * @var string
     */
    $WP_PLUGIN_DIR = realpath((string) WP_PLUGIN_DIR);
    /**
     * @psalm-suppress UndefinedConstant
     * @var string
     */
    $__BREAKDANCE_DIR__ = realpath((string) __BREAKDANCE_DIR__);

    $WC_PLUGIN_DIR = '';
    if (class_exists('woocommerce')){
        /**
         * @psalm-suppress UndefinedConstant
         * @var string
         */
        $WC_PLUGIN_FILE = WC_PLUGIN_FILE;
        $WC_PLUGIN_DIR = realpath(dirname($WC_PLUGIN_FILE));
    }

    // If the template is for a plugin's file, and it's not ours, or WooCommerce's.
    // e.g, an "Event" page: "public/wp-content/plugins/the-events-calendar/src/views/v2/default-template.php"
    return str_starts_with($file_to_include, $WP_PLUGIN_DIR) &&
        // ignore our own files
        !str_starts_with($file_to_include, $__BREAKDANCE_DIR__) &&
        // ignore WooCommerce since we override all their templates. This is the only plugin we do that for.
        (!$WC_PLUGIN_DIR || !str_starts_with($file_to_include, $WC_PLUGIN_DIR));
}

/**
 * @return boolean
 */
function wordpressPermalinksAreBroken()
{
    return isRequestFromBuilderIframe() && http_response_code() === 404;
}


/**
 * @return boolean
 */
function userIsEditingHeaderOrFooterOrTemplateOrGlobalBlockOrPopup()
{
    return isRequestFromBuilderIframe() && postTypeIsHeaderOrFooterOrTemplateOrGlobalBlockOrPopup();
}

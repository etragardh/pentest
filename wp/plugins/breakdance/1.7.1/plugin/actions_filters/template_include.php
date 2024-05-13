<?php

namespace Breakdance\ActionsFilters;

/* Must execute last, since we need to buffer the output */
add_filter('template_include', 'Breakdance\ActionsFilters\template_include', 1000000);


/**
 * @return string|void
 */
function getAdminTemplate()
{
    /** @var string|false $adminTemplate */
    $adminTemplate = $_GET['breakdance'] ?? false;

    $templates = [
        'builder' => '../loader/loader.php',
        'templates' => '../themeless/manage-templates-spa/iframe.php',
        'design_library' => '../design-library/app/iframe.php',
        'regenerate-cache' => '../setup/iframe-regenerate-cache.php',
    ];

    $isValidTemplate = in_array($adminTemplate, array_keys($templates));

    if (!$isValidTemplate) return;

    return plugin_dir_path(__FILE__) . $templates[$adminTemplate];
}

/**
 * @param string $file_to_include
 * @return string|null
 */
function template_include($file_to_include)
{
    // WP checks if it's robots or favicon in "template-loader.php" but it doesn't catch it when the permalinks are in "plain", for some reason.
    // Details and explanation: https://github.com/soflyy/breakdance/pull/3982
    if (isset($_REQUEST['q']) && ($_REQUEST['q'] === 'robots.txt' || $_REQUEST['q'] === 'favicon.ico')) {
        return null;
    }

    $adminTemplate = getAdminTemplate();
    if ($adminTemplate) return $adminTemplate;

    do_action('breakdance_register_template_types_and_conditions');

    $didAjaxFire = \Breakdance\AJAX\see_if_this_is_an_ajax_at_any_url_request_and_if_so_fire_it();
    if ($didAjaxFire) {
        return null;
    }

    $file_to_include = \Breakdance\Themeless\maybe_override_the_theme_with_a_breakdance_template($file_to_include);

    if (!empty($file_to_include)) {
        \Breakdance\Render\getWordPressHtmlOutputWithHeaderAndFooterDependenciesAddedAndDisplayIt($file_to_include);
    }

    return null;
}

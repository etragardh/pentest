<?php

namespace Breakdance\WPUIForBuilder\Link;

if (isset($_GET['breakdance_wpuiforbuilder_link']) && $_GET['breakdance_wpuiforbuilder_link']) {
    add_action('admin_enqueue_scripts', '\Breakdance\WPUIForBuilder\Link\enqueueMediaScriptsAndStyles');
    add_action('admin_footer', '\Breakdance\WPUIForBuilder\Link\add_link_chooser_to_footer');
}

function enqueueMediaScriptsAndStyles()
{
    wp_enqueue_script('wplink');
    wp_enqueue_style('editor-buttons');

    wp_enqueue_script('breakdance-link-control', BREAKDANCE_PLUGIN_URL . "plugin/wpuiforbuilder/link/link.js", [], false, true);
    wp_enqueue_style('breakdance-link-control', BREAKDANCE_PLUGIN_URL . "plugin/wpuiforbuilder/link/link.css");
}

function add_link_chooser_to_footer()
{
    // Required to make wplink.js work
    echo '<textarea id="link-chooser"></textarea>';

    // Require the core editor class so we can call wp_link_dialog function to output the HTML.

    /**
     * @psalm-suppress UndefinedConstant
     * @psalm-suppress UnresolvableInclude
     */
    require_once ABSPATH . "wp-includes/class-wp-editor.php";

    \_WP_Editors::wp_link_dialog();
}

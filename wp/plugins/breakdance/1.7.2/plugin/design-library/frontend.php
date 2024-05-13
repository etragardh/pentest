<?php

namespace Breakdance\DesignLibrary;

function enqueueScripts()
{
    if (!isDesignLibraryEnabledForCurrentRequest()) return;

    /**
     * @psalm-suppress PossiblyFalseArgument
     */
    wp_enqueue_style('breakdance-design-library', plugin_dir_url(__FILE__) . 'design-library.css', false);
    /**
     * @psalm-suppress PossiblyFalseArgument
     */
    wp_enqueue_script('breakdance-design-library', plugin_dir_url(__FILE__) . 'design-library.js', false);
}
add_action('wp_enqueue_scripts', '\Breakdance\DesignLibrary\enqueueScripts');

/**
 * @param array<string, string> $atts
 * @param TreeNode $node
 * @param \Breakdance\Elements\Element $element
 * @return mixed
 */
function maybeRenderAttr($atts, $node, $element)
{
    if (!isDesignLibraryEnabledForCurrentRequest()) return $atts;

    /** @var bool */
    $disabled = $node['data']['properties']['settings']['design_library']['disabled'] ?? false;

    if ($disabled) return $atts;

    $copyableElements = getCopyableElements();

    /** @psalm-suppress DocblockTypeContradiction */
    if (in_array($element, $copyableElements)) {
        $atts['data-element'] = json_encode($node);
    }

    return $atts;
}
add_filter('breakdance_frontend_element_attributes', '\Breakdance\DesignLibrary\maybeRenderAttr', 10, 3);

// Allow Woo pages to be embedded within iframes.
function removeWcSendFrameOptionsHeader()
{
    if (isDesignLibraryEnabled()) {
        remove_action( 'template_redirect', 'wc_send_frame_options_header' );
    }
}
add_action('after_setup_theme', '\Breakdance\DesignLibrary\removeWcSendFrameOptionsHeader', 20);

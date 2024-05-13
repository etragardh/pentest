<?php

namespace Breakdance\ActionsFilters;

use Breakdance\Render\CircularRendererTracker;

add_filter('the_content', '\Breakdance\ActionsFilters\replace_the_content_with_breakdance_content', -2147483648);


/**
 * @return bool
 */
function isTheBreakdanceRendererCurrentlyRenderingThePostThatTheContentIsBeingCalledFor() {

    /**
     * @var \WP_Post|null
     */
    $post = get_post();

    if ($post === null) {
        return false;
    }

    if (CircularRendererTracker::getInstance()->currentlyRenderingPostOrLastRenderedPost == $post->ID) {
        return true;
    }

    /** @var string|null|false $ajax_action */
    $ajax_action = filter_input(INPUT_POST, 'action', FILTER_UNSAFE_RAW);

    /*
    if it's a server side render
    and triggeringDocument matches the post being rendered
    then the post is already open in the editor, and we don't want to render it again
    so just callback to the actual content
    (which is the same as the behavior on the frontend, from the above if statement)
    */
    if ($ajax_action === 'breakdance_server_side_render') {
        /** @var int|null|false $triggering_document_id */
        $triggering_document_id = filter_input(INPUT_POST, 'triggeringDocument', FILTER_VALIDATE_INT);

        if ($triggering_document_id === $post->ID) {
            return true;
        }
    }

    /*
    do the same for dynamic data as we do for server side render

    in theory this isn't needed because the post_content dynamic data field is only available for templates
    but who knows what other dynamic fields might get registered that could ultimately call the_content
    */
    if ($ajax_action === 'breakdance_dynamic_data_get') {
        /** @var int|null|false $id */
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if ($id === $post->ID) {
            return true;
        }
    }


    return false;


    /*
    if something calls the the_content, it wants the_content for the current post ID

    if that content was designed with Breakdance, we should give them the Breakdance content,
    not the default WP "the_content" content

    but if we are already rendering the Breakdance content for that post, then they must really want
    the actual content.

    this is necessary for WooCo - TODO: show the issue in Loom
    */

}


/**
 * @param string $content
 * @return string
 */
function replace_the_content_with_breakdance_content($content)
{

    /**
     * @var \WP_Post|null
     */
    $post = get_post();

    if ($post === null || isTheBreakdanceRendererCurrentlyRenderingThePostThatTheContentIsBeingCalledFor()) {
        return $content;
    }

    $renderedPost = \Breakdance\Render\render($post->ID);

    if ($renderedPost) {
        remove_filters_for_the_content();

        /*
        we shouldn't ever need to restore the filters because
        this should happen at the bottom of the chain. any calls to the_content
        that took place during the render happened before we got to this point
        */

        return $renderedPost;
    } else {
        return $content;
    }
}

/**
 * @param string $content
 * @return string
 */
function simulate_the_content($content) {

    $content = (string) apply_filters("breakdance_singular_content", $content);

    if (!\Breakdance\Data\get_global_option('breakdance_settings_enable_simulate_the_content')) {
        return $content;
    }

    remove_filter('the_content', '\Breakdance\ActionsFilters\replace_the_content_with_breakdance_content', -2147483648);
    remove_filters_for_the_content();
    $content = (string) apply_filters('the_content', $content);

    /*
    we shouldn't ever need to restore the filters because
    this should happen at the bottom of the chain. any calls to the_content
    that took place during the render happened before we got to this point
    we call simulate_the_content right before we echo in breakdance-no-template.php
    and breakdance-blank-canvas.php
    */

    add_filter('the_content', '\Breakdance\ActionsFilters\replace_the_content_with_breakdance_content', -2147483648);

    return $content;
}


function remove_filters_for_the_content() {
    remove_filter('the_content', 'wpautop');
    remove_filter('the_content', 'shortcode_unautop'); // todo - do we really need this? elementor uses it, but we probably handle shortcodes differently.
    remove_filter('the_content', 'wptexturize');
    /** @psalm-suppress UndefinedFunction */
    remove_filter('the_content', 'wp_filter_content_tags');
}

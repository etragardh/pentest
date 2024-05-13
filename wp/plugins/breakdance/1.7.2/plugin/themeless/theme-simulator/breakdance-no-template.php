<?php

namespace Breakdance\Themeless;

$renderedHeader = get_breakdance_header_template_for_request();

// WP will populate $post on every request with the first post in the query (idk why)
// Checking if it's singular prevents calling "render" on it for no reason
// Helps to fix: https://github.com/soflyy/breakdance/issues/3936 and fixes: https://github.com/soflyy/breakdance/issues/3428
if (is_singular()) {
    global $post;

    $renderedPost = $post ? \Breakdance\Render\render(
        $post->ID
    ) : "";

    if ($renderedPost) {
        $content = \Breakdance\ActionsFilters\simulate_the_content($renderedPost);
    } else {
        $content = \Breakdance\Themeless\Fallbacks\fallback_defaults_or_the_content();
    }
} else {
    $content = \Breakdance\Themeless\Fallbacks\fallback_defaults_or_the_content();
}

$renderedFooter = get_breakdance_footer_template_for_request();

outputHeadHtml();
echo $renderedHeader;

echo $content;

echo $renderedFooter;

outputFootHtml();

?>

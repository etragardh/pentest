<?php

namespace Breakdance\Themeless;

global $post;

$renderedPost = \Breakdance\Render\render(
    $post->ID
);

outputHeadHtml();

if ($renderedPost) {
    echo \Breakdance\ActionsFilters\simulate_the_content($renderedPost);
} else {
    /*
    todo - should we show an error telling the user they chose the '[Breakdance] No Header / Footer' template to render this post,
    but there is no Breakdance content to display?
    */
}

outputFootHtml();

?>

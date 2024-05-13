<?php

/**
 * @var array $propertiesData
 * @var boolean $renderOnlyIndividualPosts this one is used for "load more" ajax and comes from pagination.php
 */

use function Breakdance\Util\WP\isAnyArchive;
use function Breakdance\WpQueryControl\setupIsotopeFilterBar;

require_once __DIR__ . "/post-loop-builder.php";

$renderOnlyIndividualPosts = $renderOnlyIndividualPosts ?? false;

showWarningInBuilderForImproperUseOfPaginationAndCustomQueriesOnArchives(
    $propertiesData['content']['query']['query'] ?? false,
    $propertiesData['content']['pagination']['pagination'] ?? false,
    isAnyArchive()
);

$actionData = ['propertiesData' => $propertiesData];

global $post;
$initialGlobalPost = $post;

$loop = getWpQuery($propertiesData);

$layout = (string) ($propertiesData['design']['list']['layout'] ?? '');
$filterbar = setupIsotopeFilterBar([
    'settings' => $propertiesData['content']['filter_bar'] ?? [],
    'design' => $propertiesData['design']['filter_bar'] ?? [],
    'query' => $loop
]);
do_action("breakdance_posts_loop_before_loop", $actionData);

output_before_the_loop($renderOnlyIndividualPosts, $filterbar, $layout);

do_the_loop($loop, $layout, $filterbar, $propertiesData, $actionData);

output_after_the_loop($renderOnlyIndividualPosts, $filterbar, $layout, $propertiesData);

do_action("breakdance_posts_loop_after_loop", $actionData);

\Breakdance\EssentialElements\Lib\PostsPagination\getPostsPaginationFromProperties(
    $propertiesData,
    $loop->max_num_pages,
    $layout,
    \Breakdance\Util\getDirectoryPathRelativeToPluginFolder(__FILE__)
);

do_action("breakdance_posts_loop_after_pagination", $actionData);

wp_reset_postdata();

// If these IDs don't match after resetting the postdata,
// this is a nested post loop, so we need to set the
// post data back to the original value
$currentPostId = $post instanceof \WP_Post ? $post->ID : $post;
$initialPostId = $initialGlobalPost instanceof \WP_Post ? $initialGlobalPost->ID : $initialGlobalPost;
if ($currentPostId && $currentPostId !== $initialPostId) {
    $GLOBALS['post'] = $initialGlobalPost;
}

<?php

namespace Breakdance\Compat;

// For an in-depth explanation: https://github.com/soflyy/breakdance/pull/4144
// RankMathSeo redirects attachments by default, even for ajax, so when we try to check if it's an ajax, the $_REQUEST
// contains no info (cuz it was redirected). Prevent that for our own ajax requests.
// From: RankMath\Frontend\attachment_redirect_urls
use function Breakdance\AJAX\see_if_this_is_an_ajax_at_any_url_request_and_if_so_fire_it;

add_filter('rank_math/frontend/attachment/redirect_url', '\Breakdance\Compat\fixRankMathSeoBreakingAttachmentPagesDueToRedirect');

/**
 * @param string $redirect
 * @return false|string
 */
function fixRankMathSeoBreakingAttachmentPagesDueToRedirect($redirect){
    if (see_if_this_is_an_ajax_at_any_url_request_and_if_so_fire_it()){
        return false;
    }

    return $redirect;
}

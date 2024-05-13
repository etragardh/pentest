<?php

namespace Breakdance;

/**
 * @param string $url
 * @param string $action
 * @param array $bodyArgs
 * @return array|\WP_Error
 */
function remotePostToWpAjax($url, $action, $bodyArgs = [])
{
    $ajaxPath = '/wp-admin/admin-ajax.php';
    $urlComponents = parse_url($url);
    /**
     * Rebuild the URL with the new path and the existing query string (if any)
     * @psalm-suppress PossiblyUndefinedArrayOffset
     * @psalm-suppress MixedArgument
     */
    $url = $urlComponents['scheme'] . '://' . $urlComponents['host'];

    if (isset($urlComponents['path'])) {
        // Remove the trailing slash from the path
        $url .= rtrim($urlComponents['path'], '/');
    }

    $url .= $ajaxPath;


    if (isset($urlComponents['query'])) {
        $url .= '?' . $urlComponents['query'];
    }

    return wp_remote_post($url, [
        // PHP max_execution_time default is 30
        'timeout' => 25,
        'body' => array_merge(['action' => $action], $bodyArgs)
    ]);
}

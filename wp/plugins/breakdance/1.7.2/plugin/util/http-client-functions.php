<?php

namespace Breakdance\Util\Http;

/**
 * @param string $url
 * @param array $args
 * @return mixed|false
 * @see wp_remote_get() for arguments references
 *
 */
function http_get_json($url, $args = [])
{
    $request = wp_remote_get($url, $args);

    if (is_wp_error($request)) {
        return false;
    }

    $response_body = wp_remote_retrieve_body($request);

    /** @var mixed $decoded_json */
    $decoded_json = json_decode($response_body, true);

    if ($decoded_json === null) {
        return false;
    }

    return $decoded_json;
}

<?php

namespace Breakdance\DesignLibrary;

use function Breakdance\remotePostToWpAjax;

/**
 * @param string $password
 */
function setPassword($password)
{
    \Breakdance\Data\set_global_option('design_library_password', sanitize_text_field($password));
}

/**
 * @return string
 */
function getPassword()
{
    /**
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     * @var string
     */
    return \Breakdance\Data\get_global_option('design_library_password') ?: '';
}

/**
 * @return bool
 */
function isPasswordProtected()
{
    return getPassword() !== '';
}

/**
 * @param string $password
 * @return bool
 */
function checkPassword($password)
{
    $valid = getPassword() === $password;

    /**
     * @psalm-suppress TooManyArguments
     * @var bool
     */
    return apply_filters('breakdance_design_library_check_password', $valid, $password);
}

/**
 * @param string $url
 * @param string $password
 * @return bool|array{error: string}
 */
function checkRemotePassword($url, $password)
{
    $request = remotePostToWpAjax($url, 'breakdance_design_lib_check_password', ['password' => $password]);

    if (is_wp_error($request)) {
        /** @var \WP_Error $request */
        $request = $request;
        return ['error' => $request->get_error_message()];
    }

    /** @var array{valid: bool} $body */
    $body = json_decode(wp_remote_retrieve_body($request), true);

    return (bool) $body['valid'];
}

/**
 * @param string $url
 * @param string $password
 */
function setPasswordForExternalDesignSet($url, $password)
{
    $providers = getRegisteredDesignSets();

    // Parse the URL to separate base URL and existing query string (if any)
    $parts = parse_url($url);
    $scheme = isset($parts['scheme']) ? $parts['scheme'] . '://' : '';
    $host = isset($parts['host']) ? $parts['host'] : '';
    $path = isset($parts['path']) ? $parts['path'] : '';

    $existingQueryString = isset($parts['query']) ? $parts['query'] : '';
    parse_str($existingQueryString, $queryParameters);

    // Replace or add the new password
    $queryParameters['password'] =  sanitize_text_field($password);

    $newQueryString = http_build_query($queryParameters);

    // Reconstruct the URL with the modified query string
    $newUrl = $scheme . $host . $path . ($newQueryString ? '?' . $newQueryString : '');

    // Iterate through the array
    foreach ($providers as $index => $provider) {
        if ($provider === $url) {
            $providers[$index] = $newUrl;
        }
    }

    setDesignSets($providers);
}

/**
 * @return string
 */
function getPasswordFromRequest()
{
    /** @var mixed */
    $password = filter_input(INPUT_GET, 'password', FILTER_UNSAFE_RAW) ?? '';
    if (!is_string($password)) return '';
    /** @var string */
    return sanitize_text_field($password);
}

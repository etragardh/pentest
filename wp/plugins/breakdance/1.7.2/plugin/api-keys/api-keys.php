<?php

namespace Breakdance\APIKeys;

class APIKeysController
{

    use \Breakdance\Singleton;

    /**
     * @var ApiKey[]
     */
    public $apiKeys = [];

    /**
     * @param ApiKey $apiKey
     * @return void
     */
    public function registerKey($apiKey)
    {
        $this->apiKeys[] = $apiKey;
    }

}

/**
 * Add an API Key to the admin dashboard
 * @param ApiKey $apiKey
 */
function registerKey($apiKey)
{
    APIKeysController::getInstance()->registerKey($apiKey);
}

/**
 * @return array<array-key, string>
 */
function getAllKeys()
{
    /** @var array<array-key, string>|false $keys */
    $keys = \Breakdance\Data\get_global_option('api_keys');
    return $keys ?: [];
}

/**
 * Get an specific API Key
 * @param string $apiKey
 * @return string
 */
function getKey($apiKey)
{
    $keys = getAllKeys();

    return $keys[$apiKey] ?? '';
}

/**
 * Persist all API Keys to the database
 * Any keys that are not in the given array will be removed from the database.
 * @param string[] $allKeys
 */
function setAllKeys($allKeys)
{
    \Breakdance\Data\set_global_option(
        'api_keys',
        $allKeys
    );
}

/**
 * Persist API Key to the database
 * @param string $apiKey
 * @param string $value
 */
function setKey($apiKey, $value)
{
    $keys = getAllKeys();
    $keys[$apiKey] = $value;

    setAllKeys($keys);
}

/**
 * Validate Facebook App ID
 * @psalm-suppress InvalidScalarArgument
 * @psalm-suppress MixedAssignment
 * @psalm-suppress MixedPropertyFetch
 * @psalm-suppress MixedArgument
 * @psalm-suppress PossiblyNullArgument
 * @param string $appId
 * @return ActionSuccess|ActionError
 */
function validateFacebookAppId($appId)
{
    // https://developers.facebook.com/docs/graph-api/guides/error-handling
    // if valid, we should get back a valid `id` object property
    // VALID TEST APP ID 113869198637480
    // INVALID TEST APP ID 454908589441465

    $url = 'https://graph.facebook.com/' . $appId;
    $body = wp_remote_retrieve_body(wp_remote_get($url));
    $response = json_decode($body);

    if (!is_wp_error($body) && isset($response->id)) {
        return ['type' => 'success', 'message' => ''];
    } else {
        return ['type' => 'error', 'message' => ''];
    }
}

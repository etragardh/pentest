<?php

namespace Breakdance\Licensing\Events;

/**
 * @param string $userId
 * @param string $eventType
 * @param array $eventProperties
 */
function log($userId, $eventType, $eventProperties = [])
{
    $api_url = 'https://breakdance.com/wp-json/licensing-and-usage/v1/event';

    $body = json_encode([
        'user_id' => $userId,
        'event_type' => $eventType,
        'event_properties' => $eventProperties
    ]);

    $args = [
        'method'      => 'POST',
        'headers'     => array(
            'Content-Type' => 'application/json',
        ),
        'body'        => $body,
        'data_format' => 'body',
    ];

    wp_remote_request($api_url, $args);
}

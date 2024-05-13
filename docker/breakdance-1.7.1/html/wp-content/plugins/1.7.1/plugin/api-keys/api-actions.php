<?php

namespace Breakdance\ApiKeysActions;

use function \Breakdance\APIKeys\getKey;

$facebookApiKey = (string) getKey(BREAKDANCE_FACEBOOK_APP_ID_NAME);

if ($facebookApiKey) {
    add_action('wp_head', function () use ($facebookApiKey) {
        echo '<meta property="fb:app_id" content="' . $facebookApiKey . '" />';
    }, 100000000);

    add_action('wp_body_open', function () {
        echo '<div id="fb-root"></div>';
    }, 100000000);
}

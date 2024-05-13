<?php

namespace Breakdance\Licensing\Events;

add_action(
    'breakdance_activated',
    function () {
        $domain = str_replace('www.', '', parse_url(get_site_url(), PHP_URL_HOST));

        log(
            getOwauid(),
            'Plugin Activated',
            [
                'domain' => $domain
            ]
        );
    }
);

/**
 * @return string
 */
function getOwauid()
{
    /** @psalm-suppress TypeDoesNotContainType */
    return BREAKDANCE_OWAUID ?? 'before-owauid';
}

<?php

namespace Breakdance\BrowseMode;

const BREAKDANCE_BROWSE_MODE_QUERY_PARAM_NAME = 'breakdance_browser';

/**
 * @return boolean
 */
function isRequestFromBrowserIframe()
{
    if (isset($_GET[BREAKDANCE_BROWSE_MODE_QUERY_PARAM_NAME]) && $_GET[BREAKDANCE_BROWSE_MODE_QUERY_PARAM_NAME]) {
        return true;
    } else {
        return false;
    }
}


add_filter('breakdance_loaded', function() {
    add_filter(
        'wp_redirect',
        /**
         * @param string $location
         * @return string
         */
        function ($location) {
            if (isRequestFromBrowserIframe()) {
                return add_query_arg(BREAKDANCE_BROWSE_MODE_QUERY_PARAM_NAME, true, $location);
            }

            return $location;
        }
    );
});




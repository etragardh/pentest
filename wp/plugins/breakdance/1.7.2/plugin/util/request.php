<?php

/*
https://developer.wordpress.org/reference/functions/stripslashes_deep/

WordPress ignores the built-in PHP magic quotes setting and the value of get_magic_quotes_gpc() and will always add magic quotes (even after the feature is removed from PHP in 5.4).

WordPress does this because too much core and plugin code have come to rely on the quotes being there, so disabling quotes on the superglobals (as is done in both the “Basic Example” and “Good Coding Practice” examples above) is likely to cause security holes.
 */

namespace Breakdance\Util {
    /**
     * @return bool
     */
    function is_post_request() {
        return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}


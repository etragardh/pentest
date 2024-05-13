<?php

namespace Breakdance\Sessions;

!is_admin() && !wp_doing_ajax()  && add_action('wp', 'Breakdance\Sessions\trackViewAndSessionCounts');

/**
 * @return void
 */
function trackViewAndSessionCounts() {
    /** @var string|false $disable_page_and_session_tracking_cookies */
    $disable_page_and_session_tracking_cookies = \Breakdance\Data\get_global_option('breakdance_settings_disable_view_tracking_cookies');

    if ($disable_page_and_session_tracking_cookies === 'yes') {
        // Delete cookies from browser if they haven't already been deleted
        // We don't want to keep sending the "delete cookies pls" cookies
        if (isset($_COOKIE['breakdance_view_count']) ||
            isset($_COOKIE['breakdance_session_count']) ||
            isset($_COOKIE['breakdance_last_session_id'])
        ) {

            setcookie('breakdance_view_count', '', time() - 3600);
            setcookie('breakdance_session_count', '', time() - 3600);
            setcookie('breakdance_last_session_id', '', time() - 3600);
        }

        return;
    }

    if (!session_id()) {
        session_start();
    }
    $sessionId = session_id();
    $lastSessionId = (string) ($_COOKIE['breakdance_last_session_id'] ?? '');
    if (!is_singular() && !is_page() && !is_single() && !is_archive() && !is_home() && !is_front_page()) {
        return;
    }
    $pageViewCount = (int) ($_COOKIE['breakdance_view_count'] ?? 0);
    $sessionCount = (int) ($_COOKIE['breakdance_session_count'] ?? 0);
    $pageViewCount += 1;
    if ($lastSessionId !== $sessionId) {
        $sessionCount += 1;
    }

    // Send cookie headers to the browser
    setcookie('breakdance_view_count', (string) $pageViewCount);
    setcookie('breakdance_session_count', (string) $sessionCount);
    setcookie('breakdance_last_session_id', (string) $sessionId);

    // Update the cookie values for the current request
    $_COOKIE['breakdance_view_count'] = (string) $pageViewCount;
    $_COOKIE['breakdance_session_count'] = (string) $sessionCount;
    $_COOKIE['breakdance_last_session_id'] = (string) $sessionId;

}

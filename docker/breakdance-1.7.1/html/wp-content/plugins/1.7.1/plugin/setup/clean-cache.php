<?php

namespace Breakdance\Setup;

use function Breakdance\Data\get_global_option;
use function Breakdance\Data\set_global_option;
use function Breakdance\Render\clearAllCssCachesAndDeleteCachedFiles;
use function Breakdance\Render\generateCacheForGlobalSettings;

// We can't use "upgrader_process_complete" because a user may just replace the plugin folder with a new one
add_action('breakdance_loaded', "\Breakdance\Setup\cleanCacheWhenCacheCountChanges");

function cleanCacheWhenCacheCountChanges()
{
    // the plugin will always be updated via the admin. No need to run this for every frontend request
    if (!is_admin()) return;

    $optionName = 'last_clear_css_cache_flag_value';
    $currentClearCacheFlag = defined('__BREAKDANCE_CLEAR_CSS_CACHE_FLAG__') ? (int)__BREAKDANCE_CLEAR_CSS_CACHE_FLAG__ : false;

    if (is_multisite()) {
        /**
         * @psalm-suppress NullArgument wp specifies it can be null
         *  @var string | false $lastCleanCacheFlag
         */
        $lastCleanCacheFlag = get_network_option(null, "breakdance_$optionName");

        if ($lastCleanCacheFlag && (int)$lastCleanCacheFlag === $currentClearCacheFlag) {
            return;
        }

        if ($lastCleanCacheFlag === false) {
            /**
             * @psalm-suppress NullArgument wp specifies it can be null
             */
            add_network_option(null, "breakdance_$optionName", $currentClearCacheFlag);
        } else {
            /**
             * @psalm-suppress NullArgument wp specifies it can be null
             */
            update_network_option(null, "breakdance_$optionName", $currentClearCacheFlag);
        }

        /** @var int[] $sitesId */
        $sitesId = get_sites([
            'fields' => 'ids',
            // does anyone have an install with more than 400 sites?
            'number' => 400
        ]);

        // clear cache for each individual site on the network
        foreach ($sitesId as $siteId) {
            switch_to_blog($siteId);

            clearAllCssCachesAndDeleteCachedFiles();
            generateCacheForGlobalSettings();

            restore_current_blog();
        }
    } else {
        /** @var integer | false $lastCleanCacheFlag */
        $lastCleanCacheFlag = get_global_option($optionName);

        if ($lastCleanCacheFlag && (int)$lastCleanCacheFlag === $currentClearCacheFlag) {
            return;
        }

        clearAllCssCachesAndDeleteCachedFiles();
        generateCacheForGlobalSettings();

        set_global_option($optionName, $currentClearCacheFlag);

    }
}

<?php

namespace Breakdance\DesignLibrary;

use function Breakdance\BrowseMode\isRequestFromBrowserIframe;
use function Breakdance\isRequestFromBuilderIframe;
use function Breakdance\remotePostToWpAjax;
use function Breakdance\Themeless\getTemplateSettingsFromDatabase;
use function Breakdance\Util\validateUrl;

class CacheWhetherDesignLibraryIsEnabled
{

    use \Breakdance\Singleton;

    /**
     * @var boolean
     */
    public $enabled = false;

    function __construct()
    {
        $isEnabled = isDesignLibraryEnabled();
        $isCopyBtnEnabled = isCopyButtonOnFrontendEnabled();
        $isAdmin = \Breakdance\Permissions\hasPermission('full');

        $isDesignLibraryModal = isRequestFromDesignLibraryModal();
        $isRequestFromBuilderOrBrowseMode = isRequestFromBuilderIframe() || isRequestFromBrowserIframe();

        // Rules for showing the copy button:
        // 1. Never show the copy button in the builder or browse mode iframes
        // 2. In the Design Library modal, if the user is an admin of the site (This Website Design Set)
        // 3. Or if the user is not an admin but Design Library mode is enabled
        // 4. Or if a password is set and the user has entered the correct password
        // 4. On the frontend, if the copy button is enabled.

        // *Notes*
        // The design library modal is a special case because it could be a different domain or the same domain (this website set)

        if ($isRequestFromBuilderOrBrowseMode) {
            $this->enabled = false;
            return;
        }

        if ($isDesignLibraryModal) {
            if ($isAdmin) {
                $this->enabled = true;
                return;
            }

            if (!$isEnabled) {
                $this->enabled = false;
                return;
            }

            if (isPasswordProtected() && !checkPassword(getPasswordFromRequest())) {
                $this->enabled = false;
                return;
            }

            $this->enabled = true;
            return;
        }

        $this->enabled = $isCopyBtnEnabled;
    }

}

/**
 * @return bool
 */
function isDesignLibraryEnabled()
{
    return \Breakdance\Data\get_global_option('is_copy_from_frontend_enabled') == 'yes';
}

/**
 * @return bool
 */
function isCopyButtonOnFrontendEnabled()
{
    return \Breakdance\Data\get_global_option('is_copy_button_on_frontend_enabled') == 'yes';
}

/**
 * @return bool
 */
function doesDesignLibraryRelyOnGlobalSettings()
{
    return \Breakdance\Data\get_global_option('design_library_relies_on_global_settings') == 'yes';
}

/**
 * @return bool
 */
function isDesignLibraryEnabledForCurrentRequest()
{
    return CacheWhetherDesignLibraryIsEnabled::getInstance()->enabled;
}

/**
 * @return bool
 */
function isRequestFromDesignLibraryModal()
{
    if (!isset($_GET['breakdance'])) {
        return false;
    }

    return $_GET['breakdance'] === 'design-library';
}

/**
 * @return string
 */
function getDesignLibraryUrl()
{
    $base_url = home_url();

    if (isPasswordProtected()) {
        return $base_url . '?password=' . getPassword();
    }

    return $base_url;
}

/**
 * @return string[]
 */
function getCopyableElements()
{
    $elements = [
        'EssentialElements\Section',
        'EssentialElements\HeaderBuilder',
    ];

    /** @var string[] */
    return apply_filters('breakdance_design_library_copyable_elements', $elements);
}

/**
 * Save providers to the database and turn commas into line breaks
 * @param array|string $providers
 * @return void
 */
function setDesignSets($providers)
{
    $providers = array_unique(array_map('esc_url', wp_parse_list($providers)));
    \Breakdance\Data\set_global_option('design_sets', $providers);
}

/**
 * @return string[]
 */
function getRegisteredDesignSets()
{
    /** @var string[]|false */
    $designSets = \Breakdance\Data\get_global_option('design_sets');
    return is_array($designSets) ? $designSets : [];
}

/**
 * @return array{name: string, url: string, type?: string, isLocal?: boolean}[]
 */
function getDesignProviders()
{
    $response = wp_remote_request('https://breakdance.com/wp-content/uploads/breakdance/design_sets/design_library_providers.json');

    $localSite = [
        [
            "name" => "This Website",
            "url" => getDesignLibraryUrl(),
            "type" => "ui_kit",
            'isLocal' => true,
        ],
    ];


    if (is_wp_error($response)){
        $providers = $localSite;
    } else {
        /** @var array $response */
        $response = $response;

        /** @var int|null $code */
        $code = $response['response']['code'] ?? null;

        if ($code !== 200){
            $providers = $localSite;
        } else {
            /** @var array[] $remoteProviders */
            $remoteProviders = json_decode(wp_remote_retrieve_body($response), true) ;

            /** @var array{name: string, url: string, type?: string, isLocal?: boolean}[] $providers */
            $providers = array_merge(
                $localSite,
                $remoteProviders
            );
        }
    }

    $designSets = array_filter(getValidDesignSets(), fn($url) => !empty($url));

    if (count($designSets)) {
        $designSets = array_map(
            function($url) {
                $set = getDesignSetRemoteData($url); // this is cached
                return [
                    "name" => $set['name'] ?? $url,
                    "url" => $url,
                ];
            },
            $designSets
        );
    }

    return array_merge($providers, $designSets ?: []);
}

/**
 * @return string[]
 */
function getInvalidDesignSets()
{
    $providers = getRegisteredDesignSets();

    return array_filter(
        $providers,
        fn($provider) => !isValidDesignSet($provider)
    );
}

/**
 * @return string[]
 */
function getValidDesignSets()
{
    $providers = getRegisteredDesignSets();

    return array_filter(
        $providers,
        fn($provider) => isValidDesignSet($provider)
    );
}

/**
 * @param string $url
 * @return DesignSetData|array{error: string}
 */
function getDesignSetRemoteData($url)
{
    /** @var array<string, mixed> */
    static $cache = [];

    if (isset($cache[$url])) {
        /** @var DesignSetData */
        return $cache[$url];
    }

    $request = remotePostToWpAjax($url, 'breakdance_get_design_set');

    if (is_wp_error($request)) {
        /** @var \WP_Error $request */
        return ['error' => $request->get_error_message()];
    }

    if (is_array($request) && (!isset($request['response']['code']) || $request['response']['code'] !== 200)) {
        return ['error' => 'Unable to retrieve website'];
    }

    $body = wp_remote_retrieve_body($request);

    /** @var mixed */
    $data = json_decode($body);

    if (!is_object($data)) {
        return ['error' => 'Unable to decode data from website'];
    }

    $cache[$url] = (array) $data;

    /** @var DesignSetData */
    return (array) $data;
}

/**
 * @param boolean $includeDraft
 * @return array
 */
function getArgumentsForDesignSetPostsQuery($includeDraft)
{
    return [
        'post_type' => 'any',
        'numberposts' => -1,
        'post_status' => $includeDraft ? ['draft', 'publish'] : 'publish',
        'meta_query' => [
            'relation' => 'AND',
            [
                'relation' => 'OR',
                [
                    'key' => '_breakdance_hide_in_design_set',
                    'compare' => 'NOT EXISTS',
                ],
                [
                    'key' => '_breakdance_hide_in_design_set',
                    'compare' => '!=',
                    'value' => '1',
                ],
            ],
            [
                'key' => 'breakdance_data',
                'compare' => 'EXISTS',
            ],
        ],
    ];
}

/**
 * @return DesignSetPost[]
 */
function getPostsForDesignSet()
{
    $isAdmin = \Breakdance\Permissions\hasPermission('full');

    /** @var \WP_Post[] */
    $posts = get_posts(getArgumentsForDesignSetPostsQuery(true));

    return array_map(function ($post) {
        /** @var string */
        $rawTags = get_post_meta($post->ID, '_breakdance_tags', true);
        /** @var string[] */
        $tags = wp_parse_list($rawTags);
        /** @var string */
        $url = get_permalink($post->ID);

        return [
            'id' => $post->ID,
            'name' => $post->post_title ?: '(no title)',
            'url' => $url,
            'tags' => $tags,
        ];
    }, $posts);
}

/**
 * @param string $url
 * @return bool
 */
function isValidDesignSet($url)
{
    if ($url === '') {
        return false;
    }

    if (!validateUrl($url)) {
        return false;
    }

    $response = getDesignSetRemoteData($url);

    if (array_key_exists('error', $response)) {
        return false;
    }

    if (!$response) {
        return false;
    }

    if (empty($response['enabled'])) {
        return false;
    }

    return true;
}

/**
 * @param int[] $templateIds
 * @return int[]
 */
function removeFallbacksFromTemplateIdsList($templateIds)
{
    return array_values(
        array_filter(
            $templateIds,
            /**
             * @param int $id
             */
            function ($id) {
                $settings = getTemplateSettingsFromDatabase($id);
                return !($settings['fallback'] ?? false);
            }
        )
    );
}

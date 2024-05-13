<?php

namespace Breakdance\Data;

use EssentialElements\MissingElement;
use function Breakdance\Admin\get_env;
use function Breakdance\AvailableDependencyFiles\getReusableDependenciesUrls;
use function Breakdance\DesignLibrary\getDesignLibraryData;
use function Breakdance\DynamicData\get_dynamic_data_post_type;
use function Breakdance\Preferences\get_preferences;
use function Breakdance\Util\get_menu_page_url;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_load_document',
        '\Breakdance\Data\load_document',
        'edit'
    );
});

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_load_builder_elements',
        '\Breakdance\Data\load_builder_elements',
        'edit',
        true
    );
});

/**
 * @return array
 */
function load_builder_elements(){
    /**
     * @psalm-suppress UndefinedFunction
     */
    return ['elements' => \Breakdance\Elements\get_elements_for_builder()];
}

/**
 * @return Permission|null
 * @throws \Exception
 */
function getDocumentPermission()
{
    // If impersonate is set, we load the builder with Client mode enabled.
    $impersonate = isset($_COOKIE['breakdance_impersonate']);

    if ($impersonate) {
        // Then we clear cookie, so it only happens once.
        setcookie('breakdance_impersonate', '', strtotime('-1 hour'));
        return \Breakdance\Permissions\getPermissionData('edit');
    }

    return \Breakdance\Permissions\getUserPermission();
}

/**
 * @param array{data:array{type:string},children:mixed}[] $tree_leaves
 * @return string[]
 */
function get_tree_elements($tree_leaves)
{
    $elements = [];

    foreach ($tree_leaves as $leaf) {
        $elements[] = $leaf['data']['type'];

        if (isset($leaf['children']) && is_array($leaf['children'])) {

            /**
             * @var array{data:array{type:string},children:mixed}[]
             */
            $leafChildren = $leaf['children'];
            $elements = [
                ...$elements,
                ...get_tree_elements($leafChildren)
            ];
        }
    }

    return $elements;
}

/**
 * @return array
 * @throws \Exception
 */
function load_document()
{
    $load = [
        'builderElements' => [],
        'dynamicFields' => [],
    ];

    if (isset($_POST['id'])) {
        $id = (int) $_POST['id'];

        /**
         * @var \WP_Post|null
         */
        $post = get_post($id);

        if (!$post) {
            return ['errorType' => 'post_does_not_exist', 'backToAdminUrl' => admin_url()];
        }

        $postType = get_post_type($id);

        if (!\Breakdance\Permissions\isPostTypeAllowed($postType)) {
            return ['errorType' => 'post_type_is_restricted', 'backToAdminUrl' => admin_url(), 'postType' => $postType];
        }

        if (in_array($postType, BREAKDANCE_BANNED_POST_TYPES)) {
            return ['errorType' => 'post_type_is_banned', 'backToAdminUrl' => admin_url(), 'postType' => $postType];
        }

        if ($post->post_status === 'trash') {
            return ['errorType' => 'post_is_in_trash',  'backToAdminUrl' => admin_url()];
        }

        $tree = get_tree($id);

        $tree_elements = $tree ? array_unique(get_tree_elements($tree['root']['children'])) : [];

        $document = [
            'tree' => $tree,
            'documentMeta' => \Breakdance\AjaxEndpoints\getDocumentMetaFromPost($post),
        ];

        /** @var TemplatePreviewableItem|false $lastPreviewedItem */
        $lastPreviewedItem = get_meta($id, 'template_last_previewed_item') ?: false;
        // verify that the post still exists. It may have been deleted since it was last selected
        $lastPreviewedItem = $lastPreviewedItem && doesPostExistAndIsNotTrashedFromUrl($lastPreviewedItem['url'])
                ? $lastPreviewedItem
                : false;

        /** @var TemplatePreviewableItem[]|false $templatePreviewableItems */
        $templatePreviewableItems = \Breakdance\Themeless\getTemplatePreviewableItems($id, $lastPreviewedItem);

        /**
         * @psalm-suppress UndefinedFunction
         * @psalm-suppress MixedArgument
         * @var array
         */
        $elements = array_values(
            array_filter(
                \Breakdance\Elements\get_elements_for_builder(),
                /**
                 * @psalm-suppress MixedInferredReturnType
                 * @param array{slug:string} $el
                 * @return bool
                 */
                function ($el) use ($tree_elements) {
                    /**
                     * @psalm-suppress UndefinedClass
                     * @psalm-suppress MixedAssignment
                     *
                     * @var string
                     */
                    $missingElementSlug = MissingElement::slug();

                    return in_array($el['slug'], $tree_elements) ||
                        // An element in the Tree may be missing, so we always have to send MissingElement
                         $el['slug'] === $missingElementSlug;
                }
            ),
        );

        $dynamicDataPostType = get_dynamic_data_post_type();
        $dynamicFields = \Breakdance\DynamicData\get_dynamic_fields_for_builder($dynamicDataPostType);

        $load = array_merge($load, [
            'builderElements' => $elements,
            'document' => $document,
            'documentSpecificNeededUrls' => get_needed_urls_for_post($id, $templatePreviewableItems, $lastPreviewedItem),
            'templatePreview' => ['previewableItems' => $templatePreviewableItems, 'lastPreviewedItem' => $lastPreviewedItem],
            'dynamicFields' => $dynamicFields,
        ]);
    }

    $element_categories = \Breakdance\Elements\get_element_categories();

    $selectors
    = \Breakdance\ClassesSelectors\getSelectorsDataForBuilder(); // TODO - whats going on here;

    $conditions = \Breakdance\Conditions\get_conditions_for_builder();

    $available_documents = \Breakdance\AjaxEndpoints\get_available_documents();

    $plugins = \Breakdance\PluginsAPI\get_plugins_for_builder();

    $fonts = \Breakdance\Fonts\get_fonts_for_builder();

    $builtinBreakpoints = \Breakdance\Config\Breakpoints\get_builtin_breakpoints();

    /** @psalm-suppress MixedAssignment */
    $maybePresets = \Breakdance\Data\get_global_option('presets_for_elements');
    /** @psalm-suppress MixedAssignment */
    $presets = $maybePresets !== false ? json_decode(
        (string) $maybePresets,
        true
    ) : [];


    $globalSettingsControlsAndTemplate = [
        'controls' => \Breakdance\GlobalSettings\globalSettingsControlSections(),
        'template' => \Breakdance\GlobalSettings\globalSettingsCssTemplate(),
        'propertyPathsToWhitelistInFlatProps' => \Breakdance\GlobalSettings\globalPropertyPathsToWhitelistInFlatProps(),
    ];

    $elementExternalProperties = [
        'attributes' => \Breakdance\Elements\FilteredGets\externalAttributes(),
        'dependencies' => \Breakdance\Elements\FilteredGets\externalDependencies(),
        'actions' => \Breakdance\Elements\FilteredGets\externalActions()
    ];

    $twigMacros = \Breakdance\Elements\getTwigMacrosData(false);
    $twigMacrosHash = md5(json_encode($twigMacros));

    $load = array_merge($load, [
        'environment' => get_env(),
        'elementPresets' => $presets,
        'globalSettings' => get_global_settings_array(),
        'selectors' => $selectors,
        'preferences' => get_preferences(),
        'neededData' => get_needed_data(),
        'builderElementCategories' => $element_categories,
        'availableDocuments' => $available_documents,
        'availableMediaSizes' => \Breakdance\Media\Sizes\getAvailableSizes(),
        'availablePermissions' => \Breakdance\Permissions\getPermissions(),
        'availableWordpressPlugins' => getAvailablePlugins(),
        'permission' => getDocumentPermission(),
        'elementsExternalAttrsDepsAndActions' => $elementExternalProperties,
        'conditions' => $conditions,
        'plugins' => $plugins,
        'fonts' => $fonts,
        'globalSettingsControlsAndTemplate' => $globalSettingsControlsAndTemplate,
        'builtinBreakpoints' => $builtinBreakpoints,
        'twigMacros' => $twigMacros,
        'twigMacrosHash' => $twigMacrosHash,
        'designLibrary' => getDesignLibraryData(),
        'subscriptionMode' => \Breakdance\Subscription\getSubscriptionMode()
    ]);

    return $load;
}

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_load_global_settings',
        '\Breakdance\Data\load_global_settings',
        'edit'
    );
});

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_load_selectors',
        '\Breakdance\Data\load_selectors',
        'edit'
    );
});

/**
 * @return array
 */
function load_global_settings()
{
    return [
        'globalSettings' => get_global_settings_array(),
    ];
}


/**
 * @param string $url
 * @return bool
 */
function doesPostExistAndIsNotTrashedFromUrl($url){
    $postStatus = get_post_status(url_to_postid($url));

    return  $postStatus && $postStatus !== 'trash';
}

/**
 * WP doesn't have a way to get plugins "slugs"
 * And using other proxies like the plugin's title is error prone since those can change at anytime
 * So we gotta create a list ourselves checking if the classes exist
 * @return array
 */
function getAvailablePlugins(){
    $activePlugins = [];

    if (class_exists('woocommerce', false)){
        $activePlugins[] = 'WooCommerce';
    }

    if (class_exists('acf', false)){
        $activePlugins[] = 'ACF';
    }

    if (defined('RWMB_VER')){
        $activePlugins[] = 'MetaBox';
    }

    if (function_exists('FWP')) {
        $activePlugins[] = 'FacetWP';
    }

    if (function_exists('wp_grid_builder')) {
        $activePlugins[] = 'WP Grid Builder';
    }

    return $activePlugins;
}

/**
 * @return CSSSelector[]
 */
function load_selectors()
{
    return \Breakdance\ClassesSelectors\getSelectorsDataForBuilder()['selectors'];
}

/**
 * @return array
 */
function get_global_settings_array()
{
    /** @var array $global_settings */
    $global_settings = json_decode(
        (string) get_global_option('global_settings_json_string'),
        true,
    );

    if (json_last_error() !== JSON_ERROR_NONE) {
        return [];
    }

    return $global_settings;
}

/**
 * @return bool
 */
function is_cross_domain()
{
    /**
     * @var array|mixed $admin_origin
     */
    $admin_origin = wp_parse_url(admin_url());

    /**
     * @var array|mixed
     */
    $home_origin = wp_parse_url(home_url());

    $cross_domain = is_array($admin_origin)
        && is_array($home_origin) &&
        isset($admin_origin['host']) &&
        isset($home_origin['host']) &&
        strtolower((string) $admin_origin['host']) !== strtolower((string) $home_origin['host']);

    return $cross_domain;
}

/**
 * @return array
 */
function get_needed_data()
{
    $linkChooserUrl = get_admin_url() . "index.php?breakdance_wpuiforbuilder_link=true";
    $tinyMceUrl = get_admin_url() . "index.php?breakdance_wpuiforbuilder_tinymce=true";

    $browseModeAllowedUrls = [
        home_url('/')
    ];

    if (is_ssl() && !is_cross_domain()) {
        $browseModeAllowedUrls[] = home_url('/', 'https');
    }

    /** @psalm-suppress UndefinedConstant */
    $version = (string) __BREAKDANCE_VERSION;

    return [
        'breakdanceVersion' => $version,
        'homeUrl' => get_home_url(),
        'adminUrl' => get_admin_url(),
        'wpContentUrl' => content_url(),
        'linkChooserUrl' => $linkChooserUrl,
        'tinyMceUrl' => $tinyMceUrl,
        'breakdanceSettingsUrl' => get_admin_url() . 'admin.php?page=breakdance_settings',
        'elementsPluginUrl' => defined('BREAKDANCE_ELEMENTS_PLUGIN_URL') ? BREAKDANCE_ELEMENTS_PLUGIN_URL : null,
        'elementsReusableDependenciesUrls' => getReusableDependenciesUrls(),
        'adminLoginUrl' => wp_login_url(),
        'browseModeAllowedUrls' => $browseModeAllowedUrls,
        'isBuilderFirstTimeVisit' => get_global_option('onboarding_builder_fist_time_visit') !== "true"
    ];
}

/**
 * @param int $post_id
 * @param false|TemplatePreviewableItem[] $previewableItems
 * @param false|TemplatePreviewableItem $lastPreviewedItem
 * @return array
 */
function get_needed_urls_for_post($post_id, $previewableItems, $lastPreviewedItem)
{
    $postType = get_post_type($post_id);
    $iframeUrl = !in_array($postType, (array) BREAKDANCE_DYNAMIC_DATA_PREVIEW_POST_TYPES, true) && $lastPreviewedItem ? $lastPreviewedItem['url'] : (string) get_permalink($post_id);
    // without some context it turns the '&' in the URL to an '&amp'; breaking our back-to-WP link
    $backToWordpressUrl = get_edit_post_link($post_id, 'hey_there');

    /**
     * @psalm-suppress RedundantConditionGivenDocblockType
     */
    if (!in_array($postType, (array) BREAKDANCE_DYNAMIC_DATA_PREVIEW_POST_TYPES, true) &&
        !$lastPreviewedItem &&
        $previewableItems &&
        count($previewableItems) > 0 &&
        isset($previewableItems[0])
    ) {
        // this logic is mirrored in the config store's "setTemplatePreview"
        $iframeUrl = $previewableItems[0]['url'];
    }

    $postUrl = (string) get_permalink($post_id);

    /** @var string[] $BREAKDANCE_ALL_EDITABLE_POST_TYPES */
    $BREAKDANCE_ALL_EDITABLE_POST_TYPES = BREAKDANCE_ALL_EDITABLE_POST_TYPES;
    /** @var int|false $activeEditableBreakdancePostArrayKey */
    $activeEditableBreakdancePostArrayKey = array_search($postType, $BREAKDANCE_ALL_EDITABLE_POST_TYPES, true);

    if ($activeEditableBreakdancePostArrayKey !== false) {
        $postUrl = false;

        $backToWordpressUrl = get_menu_page_url($BREAKDANCE_ALL_EDITABLE_POST_TYPES[(int) $activeEditableBreakdancePostArrayKey]);
    }

    if ($postType === BREAKDANCE_ACF_BLOCK_POST_TYPE) {
        $parentPostId = (int) get_post_meta($post_id, 'breakdance_acf_content_parent', true);
        $backToWordpressUrl = get_edit_post_link($parentPostId, '');
    }

    $mediaChooserUrl = get_admin_url() . "index.php?breakdance_wpuiforbuilder_media=true&post_id=". $post_id;

    return [
        'backToWordPressUrl' => $backToWordpressUrl,
        'frontend' => $postUrl ?: false,
        'mediaChooserUrl' => $mediaChooserUrl,
        'iframeUrl' => $iframeUrl,
    ];
}

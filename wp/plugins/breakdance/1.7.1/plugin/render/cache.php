<?php

namespace Breakdance\Render;

use Breakdance\Filesystem\Consts;
use function Breakdance\Data\delete_global_option;
use function Breakdance\Data\get_global_settings_array;
use function Breakdance\Filesystem\clear_bucket_contents;
use function Breakdance\Filesystem\HelperFunctions\get_file_url;

class DependencyCache {

    use \Breakdance\Singleton;

    /** @var "global"|"post" */
    public $whereToCache = "global";

    /** @var ElementDependencyWithoutConditions */
    public $postDependencies = EMPTY_DEPENDENCIES;

    /** @var ElementDependencyWithoutConditions */
    public $globalDependencies = EMPTY_DEPENDENCIES;

    /**
     * @param ElementDependencyWithoutConditions $dependenciesToAppend
     */
    public function append($dependenciesToAppend)
    {
        if ($this->whereToCache === "global") {
            $mergedDeps = dependencies_array_merge_recursive_without_conditions([$this->globalDependencies, $dependenciesToAppend]);
            $this->globalDependencies = removeDuplicatedAndEmptyDependencies($mergedDeps);
        } else {
            $mergedDeps = dependencies_array_merge_recursive_without_conditions([$this->postDependencies, $dependenciesToAppend]);
            $this->postDependencies = removeDuplicatedAndEmptyDependencies($mergedDeps);
        }
    }
}

/**
 * @param int $postId
 * @return PostGeneratedCssFilePaths
 *
 * @side-effect writes the cache to disk and saves the URLs in the DB
 */
function generateCacheForPost($postId)
{
    DependencyCache::getInstance()->whereToCache = "post";

    $cssNamespace = 'post-' . ((string) $postId);

    $renderedNodes = getRenderedNodes($postId);

    if (!$renderedNodes) {
        return [];
    }

    /** @var PostGeneratedCssFilePaths $cache */
    $cache = [];

    /* get default css for used elements */
    /** @psalm-suppress RedundantCondition */
    if (INCLUDE_DEFAULT_CSS_RULES_IN_POST_CSS_CACHE) {
        $defaultCssRules = [];

        foreach ($renderedNodes['defaultCss'] as $defaultCss) {
            $defaultCssRules[$defaultCss['slug']] = $defaultCss['css'];
        }

        $defaultCssRules = array_values($defaultCssRules);
        $defaultCss = writeCssFileFromRules($defaultCssRules, $cssNamespace . '-defaults');

        $cache['postDefaultsCssFilePath'] = appendHashToFilePathAsUrlQueryForCacheBusting($defaultCss['filename'], $defaultCss['content']);
    }
    // now the default css rules are unique-ified - when they come out of the rendering, if an element is used 10x on a page, its rule will be here 10x
    /* ---------------------------------- */
    $postCss = writeCssFileFromRules($renderedNodes['cssRules'], $cssNamespace);
    $cache['postCssFilePath'] = appendHashToFilePathAsUrlQueryForCacheBusting($postCss['filename'], $postCss['content']);

    savePostCssCacheToWpDb($postId, $cache);

    /* process_font lawl */
    $dependenciesToCache = DependencyCache::getInstance()->postDependencies;

    \Breakdance\Data\set_meta($postId, 'breakdance_dependency_cache', $dependenciesToCache);

    /* --- */

    return $cache;
}

/**
 * @param int $postId
 * @return array{cssCache:PostGeneratedCssFilePaths,dependencyCache:ElementDependenciesAndConditions|false}
 *
 * returns string array of cached CSS filenames associated with $postId
 */
function getPostCache($postId)
{
    /** @var false|PostGeneratedCssFilePaths $cssCache */
    $cssCache = \Breakdance\Data\get_meta($postId, 'breakdance_css_file_paths_cache');

    /** @var ElementDependenciesAndConditions|false */
    $dependencyCache = \Breakdance\Data\get_meta($postId, 'breakdance_dependency_cache');

    if (!$cssCache || !$dependencyCache) {
        $cssCache = generateCacheForPost($postId);

        /** @var ElementDependenciesAndConditions */
        $dependencyCache = \Breakdance\Data\get_meta($postId, 'breakdance_dependency_cache');
    }

    return [
        'cssCache' => getPostsGeneratedCssFilePathsAsUrls($cssCache),
        'dependencyCache' => $dependencyCache
    ];
}

/**
 * @param int $postId
 * @param PostGeneratedCssFilePaths $postCssCache
 */
function savePostCssCacheToWpDb(int $postId, $postCssCache)
{
    \Breakdance\Data\set_meta($postId, 'breakdance_css_file_paths_cache', $postCssCache);
}

/**
 * @return array{cssCache:GlobalGeneratedCssFilePaths,dependencyCache:ElementDependenciesAndConditions|false}
 *
 * returns string array of cached CSS filenames for global styles, selectors, etc.
 */
function getGlobalSettingsCache()
{
    $globalCssCache = loadGlobalCssCacheFromWpDb();

    /** @var ElementDependenciesAndConditions|false */
    $dependencyCache = \Breakdance\Data\get_global_option('dependency_cache');

    if (!$globalCssCache || !$dependencyCache) {
        $globalCssCache = generateCacheForGlobalSettings();

        /** @var ElementDependenciesAndConditions */
        $dependencyCache = \Breakdance\Data\get_global_option('dependency_cache');
    }

    return [
        'cssCache' => getGlobalGeneratedCssFilePathsAsUrls($globalCssCache),
        'dependencyCache' => $dependencyCache
    ];

}

/**
 * @param GlobalGeneratedCssFilePaths $filePaths
 * @return GlobalGeneratedCssFilePaths
 * TODO wtf is with these type names
 */
function getGlobalGeneratedCssFilePathsAsUrls($filePaths){
    $cacheWithFullUrls = [];

    foreach ($filePaths as $key => $filePath){
        $cacheWithFullUrls[$key] = get_file_url(Consts::BREAKDANCE_FS_BUCKET_CSS, $filePath);
    }

    return $cacheWithFullUrls;
}


/**
 * @param PostGeneratedCssFilePaths $filePaths
 * @return PostGeneratedCssFilePaths
 * TODO wtf is with these type names
 */
function getPostsGeneratedCssFilePathsAsUrls($filePaths){
    $cacheWithFullUrls = [];

    foreach ($filePaths as $key => $filePath){
        $cacheWithFullUrls[$key] = get_file_url(Consts::BREAKDANCE_FS_BUCKET_CSS, $filePath);
    }

    return $cacheWithFullUrls;
}




/**
 * @return GlobalGeneratedCssFilePaths
 *
 * returns an array of strings which are URLs to the generated css files
 */
function generateCacheForGlobalSettings()
{
    DependencyCache::getInstance()->whereToCache = "global";

    $elementDefaultCss = getDefaultCssForAllElements();
    $elementDefaultCssFilename = writeCssFile($elementDefaultCss, 'elements');

    /**
     * @var CSSSelector[]
     */
    $selectors = json_decode((string) \Breakdance\Data\get_global_option('breakdance_classes_json_string'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $selectors = [];
    }

    $selectorsCss = cssForSelectors($selectors);
    $selectorCssFilename = writeCssFile($selectorsCss, 'selectors');

    /**
     * @var PropertiesData
     */
    $globalSettings = get_global_settings_array();

    $globalSettingsCss = cssForGlobalStyles(
        $globalSettings,
        \Breakdance\GlobalSettings\globalSettingsCssTemplate(),
        \Breakdance\GlobalSettings\globalPropertyPathsToWhitelistInFlatProps()
    );

    $globalSettingsCssFilename = writeCssFile($globalSettingsCss, 'global-settings');

    /** @var GlobalGeneratedCssFilePaths $cache */
    $cache = [
        'globalSettingsCssFilePath' => appendHashToFilePathAsUrlQueryForCacheBusting($globalSettingsCssFilename, $globalSettingsCss),
        'globalSelectorsCssFilePath' => appendHashToFilePathAsUrlQueryForCacheBusting($selectorCssFilename, $selectorsCss),
    ];

    if (!INCLUDE_DEFAULT_CSS_RULES_IN_POST_CSS_CACHE) {
        $cache['defaultCssForAllElementsFilePath'] = appendHashToFilePathAsUrlQueryForCacheBusting($elementDefaultCssFilename, $elementDefaultCss);
    }

    saveGlobalCssCacheToWpDb($cache);


    /** @psalm-suppress MixedAssignment */
    $globalSettingsScripts = readFromArrayByPath($globalSettings, 'settings.code.scripts');

    if (isset($globalSettingsScripts) && is_array($globalSettingsScripts)) {

        /**
         * @var ElementDependencyWithoutConditions
         */
        $globalDependenciesInlineScripts = [
            /**
             * @psalm-suppress InvalidArgument
             */
            'inlineScripts' => array_column($globalSettingsScripts, 'code')
        ];

        DependencyCache::getInstance()->append($globalDependenciesInlineScripts);
    }

    $dependenciesToCache = DependencyCache::getInstance()->globalDependencies;
    \Breakdance\Data\set_global_option('dependency_cache', $dependenciesToCache);

    return $cache;
}

/**
 * @param string $filePath
 * @param string $content
 * @return string
 */
function appendHashToFilePathAsUrlQueryForCacheBusting($filePath, $content){
    return "{$filePath}?v=" . md5($content);
}

/**
 * @param string $url
 * @param string $content
 * @return string
 */
function appendVersionToUrlForCacheBusting($url)
{
    /** @psalm-suppress UndefinedConstant */
    $version = (string)__BREAKDANCE_VERSION;

    if (
        $version === '%%VERSION%%' ||
        // only modify our own resources
        !str_starts_with($url, content_url())
    ) {
        return $url;
    }

    $query = parse_url($url, PHP_URL_QUERY);

    if ($query) {
        return "{$url}&bd_ver=" . $version;
    } else {
        return "{$url}?bd_ver=" . $version;
    }
}

/**
 * @param GlobalGeneratedCssFilePaths $globalCssCache
 */
function saveGlobalCssCacheToWpDb($globalCssCache) {
    \Breakdance\Data\set_global_option('global_css_cache', $globalCssCache);
}

/**
 * @return false|GlobalGeneratedCssFilePaths
 */
function loadGlobalCssCacheFromWpDb() {
    /** @var false|GlobalGeneratedCssFilePaths $option */
    $option = \Breakdance\Data\get_global_option('global_css_cache');

    return $option;
}

function deleteGlobalCssAndDependenciesCacheFromWpDb()
{
    delete_global_option('dependency_cache');
    delete_global_option('global_css_cache');
}

function clearAllCssCachesAndDeleteCachedFiles(){
    deleteGlobalCssAndDependenciesCacheFromWpDb();

    // delete all cache data for all posts
    // https://developer.wordpress.org/reference/functions/delete_metadata/#comment-2844
    delete_metadata('post', 0, 'breakdance_dependency_cache', false, true);
    delete_metadata('post', 0, 'breakdance_css_file_paths_cache', false, true);

    clear_bucket_contents(Consts::BREAKDANCE_FS_BUCKET_CSS);
}



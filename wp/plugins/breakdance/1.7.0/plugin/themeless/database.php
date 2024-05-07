<?php

namespace Breakdance\Themeless;

/**
 * @return Template[]
 */
function getTemplatesFromDb()
{
    return array_map(
        '\Breakdance\Themeless\postToTemplate',
        getTemplatesAsWPPosts()
    );
}

/**
 * @return Template[]
 */
function getHeadersFromDb()
{
    return array_map(
        '\Breakdance\Themeless\postToTemplate',
        getHeadersAsWPPosts()
    );
}



/**
 * @return Template[]
 */
function getFootersFromDb()
{
    return array_map(
        '\Breakdance\Themeless\postToTemplate',
        getFootersAsWPPosts()
    );
}

/**
 * @return Template[]
 */
function getPopupsFromDb()
{
    return array_map(
        '\Breakdance\Themeless\postToTemplate',
        getPopupsAsWPPosts()
    );
}

/**
 * @param int $id
 * @param bool $decodeAsObject
 * @return TemplateSettings
 */
function getTemplateSettingsFromDatabase($id, $decodeAsObject = false)
{
    /** @var TemplateSettings $templateSettings */
    $templateSettings = json_decode((string) \Breakdance\Data\get_meta(
        $id,
        'breakdance_template_settings'
    ), !$decodeAsObject);

    return $templateSettings;
}

/**
 * @param string $postType
 * @param bool $trashed
 * @param array $extraArgs
 * @return int[]|\WP_Post[]
 */
function getTemplatesPostByPostType($postType, $trashed = false, $extraArgs = []){
    return get_posts(
        array_merge(
            [
                'post_type' => $postType,
                'posts_per_page' => -1,
                'post_status' => $trashed ? 'trash' : 'publish',
            ],
            $extraArgs
        )
    );
}

/**
 * @param bool $trashed
 * @param array $extraArgs
 * @return \WP_Post[]
 */
function getTemplatesAsWPPosts(bool $trashed = false, $extraArgs = [])
{
    /** @var \WP_Post[] */
    return getTemplatesPostByPostType(BREAKDANCE_TEMPLATE_POST_TYPE, $trashed, $extraArgs);
}

/**
 * @param bool $trashed
 * @param array $extraArgs
 * @return \WP_Post[]
 */
function getHeadersAsWPPosts(bool $trashed = false, $extraArgs = [])
{
    /** @var \WP_Post[] */
    return getTemplatesPostByPostType(BREAKDANCE_HEADER_POST_TYPE, $trashed, $extraArgs);

}


/**
 * @param bool $trashed
 * @param array $extraArgs
 * @return \WP_Post[]
 */
function getFootersAsWPPosts(bool $trashed = false, $extraArgs = [])
{
    /** @var \WP_Post[] */
    return getTemplatesPostByPostType(BREAKDANCE_FOOTER_POST_TYPE, $trashed, $extraArgs);
}

/**
 * @param bool $trashed
 * @param array $extraArgs
 * @return \WP_Post[]
 */
function getGlobalBlocksAsWpPosts(bool $trashed = false, $extraArgs = [])
{
    /** @var \WP_Post[] */
    return getTemplatesPostByPostType(BREAKDANCE_BLOCK_POST_TYPE, $trashed, $extraArgs);

}


/**
 * @param bool $trashed
 * @param array $extraArgs
 * @return \WP_Post[]
 */
function getPopupsAsWPPosts($trashed = false, $extraArgs = [])
{
    /** @var \WP_Post[] */
    return getTemplatesPostByPostType(BREAKDANCE_POPUP_POST_TYPE, $trashed, $extraArgs);
}

/**
 * @param \WP_Post $post
 * @return Template
 */
function postToTemplate($post) {
    return
        [
            'id' => $post->ID,
            'settings' => getTemplateSettingsFromDatabase($post->ID),
        ];
}

<?php

namespace Breakdance\OEmbed;

use function Breakdance\String\lower;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_oembed',
        function() {
            $url = (string) filter_input(INPUT_POST, 'url');
            return retrieveOEmbed($url);
        },
        'edit'
    );
});

/**
 * @param WPOEmbed $oembed
 * @param string $sourceUrl
 * @param string|null $embedUrl
 * @return OEmbed
 */
function parseOEmbed($oembed, $sourceUrl, $embedUrl = null) {
    $provider = lower($oembed['provider_name']);

    $oembedData = [
        'title'     => $oembed['title'],
        'provider'  => $provider,
        'url'       => $sourceUrl,
        'embedUrl'  => $embedUrl,
        'thumbnail' => $oembed['thumbnail_url'],
        'format'    => $oembed['type'],
        'type'      => 'oembed',
    ];

    if ($provider === "youtube"){
        preg_match(
            // https://stackoverflow.com/a/27728417/5993042
            "/^.*(?:(?:youtu\.be\/|v\/|vi\/|u\/\w\/|embed\/|shorts\/)|(?:(?:watch)?\?v(?:i)?=|\&v(?:i)?=))([^#\&\?]*).*/",
            $sourceUrl,
            $matches
        );

        if (isset($matches[1])){
            $oembedData['videoId'] = $matches[1];
        }
    } elseif($provider === 'vimeo'){
        preg_match(
        // https://github.com/regexhq/vimeo-regex/blob/master/index.js
            "/(http|https)?:\/\/(www\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|)(\d+)(?:|\/\?)/",
            $sourceUrl,
            $matches
        );

        if (isset($matches[4])){
            $oembedData['videoId'] = $matches[4];
        }
    }

    return $oembedData;
}

/**
 * @param string $url
 * @return array|OEmbed
 */
function retrieveOEmbed($url)
{
    // Set the Referer header to the site URL, Vimeo
    // requires this for videos that are domain restricted
    add_filter('oembed_remote_get_args', '\Breakdance\OEmbed\addRefererToRequestHeaders', 10, 2);

    $oembed = _wp_oembed_get_object();
    $args = [];

    $siteOEmbed = get_oembed_response_data_for_url($url, $args);

    if ($siteOEmbed) {
        /** @var WPOEmbed|false $oembedData */
        $oembedData = $siteOEmbed;
    } else {
        /** @var WPOEmbed|false $oembedData */
        $oembedData = $oembed->get_data($url);
    }

    if (!$oembedData) {
        return ['error' => 'Could not retrieve video from ' . $url];
    }

    $oembedDataArray = (array) $oembedData;

    $html = $oembed->data2html((object) $oembedData, $url);
    /**
     * @psalm-suppress MixedAssignment
     * @psalm-suppress TooManyArguments
     * @var string
     */
    $oembedDataArray['html'] = apply_filters('oembed_result', $html, $url, $args);

    if (!$oembedDataArray['html']) {
        return ['error' => 'Could not retrieve video from ' . $url];
    }

    // Strip iframe URL from HTML tag
    $embedUrl = null;

    if (preg_match('/src="([^"]+)/', $oembedDataArray['html'], $matches)) {
        if (isset($matches[1])) {
            $embedUrl = $matches[1];
        }
    }

    return parseOEmbed($oembedDataArray, $url, $embedUrl);
}

/**
 * @param array{headers: array} $args
 * @return array{headers: array{Referer: string}}
 */
function addRefererToRequestHeaders($args) {
    $args['headers']['Referer'] = home_url();
    return $args;
}

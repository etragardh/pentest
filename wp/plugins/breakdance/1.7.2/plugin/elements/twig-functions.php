<?php

namespace Breakdance\PluginsAPI;

// We only need this for the Builder code to enable Content Editable
// Do nothing on the PHP side, since we don't want that attribute in the frontend HTML
// That's why this is a Twig function. To have different results in JS/PHP
use Breakdance\Themeless\PopupController;

\Breakdance\PluginsAPI\PluginsController::getInstance()->registerTwigFunction(
    'dataContentEditablePropertyPath',
    'Breakdance\PluginsAPI\content_editable_php',
    '
    (path) => {
        if (path) {
            return `data-content-editable-property-path="${path}"`;
        }
    }
    '
);

// Do nothing
/**
 * @return string
 */
function content_editable_php()
{
    return '';
}

// Find if `data` in any of the breakpoints matches the value
\Breakdance\PluginsAPI\PluginsController::getInstance()->registerTwigFunction(
    'at_any_media_query',
    'Breakdance\Render\propertyHasValueAtAnyBreakpoint',
    '
    // TODO I think this implementation is not exactly the same as the PHP version
    // When there are no breakpoints it may not work
        function(data, value) {
            if (typeof data !== "object" || data === null) {
                return false;
            }

            const breakpointValues = Object.values(
                window.Breakdance.stores.configStore.breakpoints
            );

            return !!breakpointValues.find(
                breakpointValue => data[breakpointValue.id] === value
            );
        }
       ',
    // We can't memoize this because it relies of Breakpoints, which can be edited
    false
);

\Breakdance\PluginsAPI\PluginsController::getInstance()->registerTwigFunction(
    'debug',
    'Breakdance\PluginsAPI\debug',
    '(...args) => { console.log("[twig]", ...args); }',
    false
);

/**
 * @param mixed $val
 */
function debug($val)
{
    if (function_exists('ray')) {
        call_user_func_array('ray', func_get_args());
    }
    print_r($val);
}

\Breakdance\PluginsAPI\PluginsController::getInstance()->registerTwigFunction(
    'get_breakpoint_max_width',
    'Breakdance\PluginsAPI\getBreakpointMaxWidth',
    '
        function(val) {
            const breakpoints = window.Breakdance.stores.configStore.breakpoints;
            const found = breakpoints.find(b => b.id === val);
            return found?.maxWidth;
        }
       ',
    // We can't memoize this because it relies of Breakpoints, which can be edited
    false
);

/**
 * @param string $val
 * @return int|null
 */
function getBreakpointMaxWidth($val)
{
    $breakpoints = \Breakdance\Config\Breakpoints\get_breakpoints();
    $found = array_search($val, array_column($breakpoints, 'id'));
    return $breakpoints[$found]['maxWidth'] ?? null;
}

//

/**
 * @return array
 */
function getImagePlaceholder()
{
    return [
        'url' => "data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' width='540' height='540' viewBox='0 0 140 140'%3e%3cpath d='M0 0h140v140H0z' fill='%23e5e7eb'/%3e%3cpath d='M88 82.46H51.8v-4.52l6.74-6.74a1.13 1.13 0 011.6 0l5.23 5.23 12.76-12.77a1.13 1.13 0 011.6 0L88 71.91z' fill='%23e5e7eb'/%3e%3cpath d='M89.48 52.32H50.29a4.52 4.52 0 00-4.52 4.52V84a4.53 4.53 0 004.52 4.52h39.19A4.52 4.52 0 0094 84V56.84a4.52 4.52 0 00-4.52-4.52zm-33.16 5.27a5.28 5.28 0 11-5.27 5.28 5.27 5.27 0 015.27-5.28zM88 82.46H51.8v-4.52l6.74-6.74a1.13 1.13 0 011.6 0l5.23 5.23 12.76-12.77a1.13 1.13 0 011.6 0L88 71.91z' fill='%23d1d5db'/%3e%3c/svg%3e",
        'width' => 540,
        'height' => 540,
        'orientation' => "portrait",
        'placeholder' => true,
    ];
}

$placeholder = json_encode(getImagePlaceholder());

\Breakdance\PluginsAPI\PluginsController::getInstance()->registerTwigFunction(
    'getImagePlaceholder',
    'Breakdance\PluginsAPI\getImagePlaceholder',
    '() => { return ' . $placeholder . '; }',
    false
);

// HTML Extras

/**
 * Creates a data URI (RFC 2397).
 *
 * @url https://github.com/twigphp/html-extra
 * @param string $data
 * @param string $mime
 * @return false|string
 */
function data_uri($data, $mime = 'text/plain')
{
    if ($data) {
        $repr = 'data:' . $mime;

        if (str_starts_with($mime, 'text/') || str_starts_with($mime, 'image/svg')) {
            $cleanData = preg_replace('/<!--(.|\s)*?-->/', '', $data); // Remove HTML comments
            $repr .= ',' . rawurlencode($cleanData);
        } else {
            $repr .= ';base64,' . base64_encode($data);
        }

        return $repr;
    }
    return false;

}

\Breakdance\PluginsAPI\PluginsController::getInstance()->registerTwigFunction(
    'data_uri',
    'Breakdance\PluginsAPI\data_uri',
    '(data, mime = "text/plain") => {
        if(data) {
            let repr = "data:" + mime;

            if (mime.startsWith("text/") || mime.startsWith("image/svg")) {
                const cleanData = data.replace(/<!--(.|\s)*?-->/, ""); // Remove HTML comments
                repr += "," + encodeURIComponent(cleanData);
            } else {
                repr += ";base64," + btoa(data);
            }

            return repr;
        }
        return "";

    }',
    false
);

/**
 * @param string $mimeType
 * @return string
 */
function mime_to_extension($mimeType)
{
    $availableMimeTypes = wp_get_mime_types();
    $found = array_search($mimeType, $availableMimeTypes, true);
    if (!$found) {
        return '';
    }

    // If multiple extensions exist for a mime type,
    // lets return them as a comma separated string
    return str_replace('|', ', ', (string) $found);
}

$mimeTypesKeyedByMimeType = array_flip(wp_get_mime_types());
\Breakdance\PluginsAPI\PluginsController::getInstance()->registerTwigFunction(
    'mime_to_extension',
    'Breakdance\PluginsAPI\mime_to_extension',
    '(mimeType) => { const mimeTypes = ' . json_encode($mimeTypesKeyedByMimeType) . '; return mimeTypes[mimeType]?.replaceAll("|", ", ");}',
    true
);

/**
 * @return string
 */
function getBreakdanceElementsPluginUrl()
{
    /**
     * @var string $BREAKDANCE_ELEMENTS_PLUGIN_URL
     * @psalm-suppress UndefinedConstant
     */
    $BREAKDANCE_ELEMENTS_PLUGIN_URL = BREAKDANCE_ELEMENTS_PLUGIN_URL;

    return defined('BREAKDANCE_ELEMENTS_PLUGIN_URL') ? $BREAKDANCE_ELEMENTS_PLUGIN_URL : '';
}

\Breakdance\PluginsAPI\PluginsController::getInstance()->registerTwigFunction(
    'getBreakdanceElementsPluginUrl',
    'Breakdance\PluginsAPI\getBreakdanceElementsPluginUrl',
    '() => { return "' . getBreakdanceElementsPluginUrl() . '"; }',
    true
);

/**
 * @param string $popupId
 * @return void
 */
function renderPopup($popupId) {
    PopupController::getInstance()->registerPopup($popupId);
}

\Breakdance\PluginsAPI\PluginsController::getInstance()->registerTwigFunction(
    'renderPopup',
    'Breakdance\PluginsAPI\renderPopup',
    '() => {}',
    false
);

// WP - Do Action
\Breakdance\PluginsAPI\PluginsController::getInstance()->registerTwigFunction(
    'do_action',
    'do_action',
    '() => { return ""; }',
    true
);

\Breakdance\PluginsAPI\PluginsController::getInstance()->registerTwigFunction(
    'wp_home_url',
    'home_url',
    '() => { return window.Breakdance.stores.configStore.neededData.homeUrl; }',
    false
);

<?php

namespace Breakdance\Media;

/**
 * @param array<string, string> $upload_mimes
 * @return array<string, string>
 * @throws \Exception
 */
function allowSvgs($upload_mimes) {

    if (\Breakdance\Permissions\hasPermission('full')) {
        $upload_mimes['svg'] = 'image/svg+xml';
        $upload_mimes['svgz'] = 'image/svg+xml';
    }

    return $upload_mimes;
}


/**
 * @param array{type: string} $wp_check_filetype_and_ext
 * @param string $file
 * @param string $filename
 * @param array<string, string> $mimes
 * @param string|false $real_mime
 * @return array|mixed
 */
function checkSvgFileType($wp_check_filetype_and_ext, $file, $filename, $mimes, $real_mime)
{
    if (!$wp_check_filetype_and_ext['type']) {
        /** @var array{ext: string|false, type: string|false} $check_filetype */
        $check_filetype = wp_check_filetype($filename, $mimes);
        $ext = $check_filetype['ext'];
        $type = $check_filetype['type'];
        $proper_filename = $filename;
        if ($type && 0 === strpos($type, 'image/') && 'svg' !== $ext) {
            $ext = false;
            $type = false;
        }
        $wp_check_filetype_and_ext = compact('ext', 'type', 'proper_filename');
    }
    return $wp_check_filetype_and_ext;
}

if (\Breakdance\Data\get_global_option('breakdance_settings_enable_svg_uploads') === 'yes') {
    add_filter('upload_mimes', 'Breakdance\Media\allowSvgs');
    add_filter('wp_check_filetype_and_ext', 'Breakdance\Media\checkSvgFileType', 10, 5);
}

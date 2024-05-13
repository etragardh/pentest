<?php

namespace Breakdance\Media\Resizer;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_resize_image',
        'Breakdance\Media\Resizer\endpoint',
        'edit',
        true,
        [
            'args' => [
                'id' => FILTER_VALIDATE_INT,
                'width' => FILTER_VALIDATE_INT,
                'height' => FILTER_VALIDATE_INT,
            ],
            'optional_args' => ['width', 'height']
        ]
    );
});

/**
 * @param string $filename
 * @param array{width: int|null, height: int|null} $size
 * @return string
 */
function getResizedImageUrl($filename, $size)
{
    $parts = array_filter([$size['width'], $size['height']], 'is_numeric');
    $suffix = implode('-', $parts);

    $ext  = pathinfo($filename, PATHINFO_EXTENSION);
    $name = basename($filename, ".$ext");

    return "{$name}-{$suffix}.{$ext}";
}

/**
 * @param string $url
 * @param array{width: int|null, height: int|null} $size
 * @return array{width: int, height: int, url: string}
 */
function resizeImage($url, $size)
{
    /** @var array{basedir: string, baseurl: string, error: string|false, path: string, url: string} $uploads */
    $uploads = wp_upload_dir();
    $resizeImagePath = $uploads['path'] . "/" . getResizedImageUrl($url, $size);
    $resizeImageUrl = $uploads['url'] . '/' . getResizedImageUrl($url, $size);

    $image = wp_get_image_editor($url);

    if (is_wp_error($image)) {
        throw new \Exception("Unable to edit this image.");
    }

    /** @psalm-suppress PossiblyUndefinedMethod */
    $image->resize($size['width'], $size['height']);
    /**
     * @var array{path: string, file: string, width: int, height: int, mime-type: string} $output
     * @psalm-suppress PossiblyUndefinedMethod
     */
    $output = $image->save($resizeImagePath);

    return array_merge(
        $output,
        ['url' => $resizeImageUrl]
    );
}

/**
 * @param int $id
 * @param int|null $width
 * @param int|null $height
 * @return array{ data: array{ width: int, height: int, orientation: string, url: string } }
 * @throws \Exception
 */
function endpoint($id, $width = null, $height = null)
{
    $size = ['width' => $width, 'height' => $height];
    $url = wp_get_attachment_url($id);

    if (!$url) {
        throw new \Exception("Image not found");
    }

    $metadata = resizeImage($url, $size);
    $orientation = $metadata["height"] > $metadata["width"] ? 'portrait' : 'landscape';

    /** @var array{ data: array{ width: int, height: int, orientation: string, url: string } } */
    $data = [
        'data' => [
            'url' => $metadata['url'],
            'width' => $metadata['width'],
            'height' => $metadata['height'],
            'orientation' => $orientation
        ]
    ];

    return $data;
}

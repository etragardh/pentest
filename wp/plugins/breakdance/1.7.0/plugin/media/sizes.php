<?php

namespace Breakdance\Media\Sizes;

use function Breakdance\Media\Metadata\getMediaSrcset;

add_filter('image_size_names_choose', '\Breakdance\Media\Sizes\imageSizeNamesChoose');
/**
 * @param array<string, string> $defaultSizes
 * @return array<array-key, string>
 */
function imageSizeNamesChoose($defaultSizes)
{
    /** @var array<string, WPRegisteredSize> $sizes */
    $sizes = wp_get_additional_image_sizes();
    $customSizes = ['medium_large' => __('Medium Large')];

    foreach ($sizes as $key => $value) {
        $name = ucwords(str_replace('_', ' ', $key));
        $customSizes[$key] = __($name);
    }

    return array_merge($defaultSizes, $customSizes);
}

/**
 * @return ImageSize[]
 */
function getAvailableSizes()
{
    /** @var array<string, WPRegisteredSize> $registered_sizes */
    $registered_sizes = wp_get_registered_image_subsizes();

    $sizes = array_map(function ($size, $slug) {
        $label = getLabel($size, $slug);
        $subLabel = getSubLabel($size, $slug);

        return [
            'slug' => $slug,
            'label' => $label,
            'subLabel' => $subLabel,
            'width' => $size['width'],
            'height' => $size['height']
        ];
    }, $registered_sizes, array_keys($registered_sizes));

    return array_merge(
        [
            ['slug' => 'full', 'label' => 'Full']
        ],
        $sizes
    );
}

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_image_sizes',
        'Breakdance\Media\Sizes\endpoint',
        'edit',
        true,
        ['args' => ['id' => FILTER_VALIDATE_INT]]
    );
});

/**
 * @param int $id
 * @return array{data: array<array-key, array<array-key, string>>|false|null}
 */
function endpoint($id)
{
    /** @var WPImageMetadata|false $metadata */
    $metadata = wp_get_attachment_metadata($id);

    if (!$metadata) return ['data' => null];

    /** @var string[] $sizes */
    $sizes = array_keys($metadata['sizes']);
    $sizes[] = 'full';
    $srcset = array_map(
        fn ($size) => getMediaSrcset($id, $size),
        $sizes
    );

    return ['data' => array_combine($sizes, $srcset)];
}

/**
 * @param WPRegisteredSize $size
 * @param string $slug
 * @return array|string|string[]
 */
function getLabel($size, $slug)
{
    $name = ucwords(str_replace('_', ' ', $slug));
    $name = str_replace("Wooc", "WooC", $name); // "WooCommerce", not "Woocommerce"
    return $name;
}

/**
 * @param WPRegisteredSize $size
 * @param string $slug
 * @return string
 */
function getSubLabel($size, $slug)
{
    $parts = [];
    $widthAngHeight = "{$size['width']}x{$size['height']}";

    if ($size['width'] === 0) {
        $parts[] = "{$size['height']}h";
    } else if ($size['height'] === 0) {
        $parts[] = "{$size['width']}w";
    } else if ($slug !== $widthAngHeight) {
        $parts[] = $widthAngHeight;
    }

    if ($size['crop']) {
        $parts[] = 'cropped';
    } else {
        $parts[] = "constrained proportions";
    }

    return implode(' - ', $parts);
}

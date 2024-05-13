<?php

/**
 * WordPress is not consistent regarding media data.
 *
 * @psalm-type Size = array{
 *   width: int,
 *   height: int,
 *   url: string,
 *   orientation:"landscape"|"portrait"
 * }
 *
 */

/**
 * This is the format we use in the frontend.
 *
 * @psalm-type Media = array{
 *   id: int,
 *   filename: string,
 *   alt: string,
 *   caption: string,
 *   url: string,
 *   sizes?: Size[],
 *   attributes: array<array-key, string>
 * }
 *
 */

/**
 * wp_get_attachment_metadata()
 *
 * @psalm-type WPImageMetadata = array{
 *   width: int,
 *   height: int,
 *   file: string,
 *   sizes: array<array-key, array{ width: int, height: int, file: string, mime-type: string }>
 * }
 */

/**
 * wp_get_additional_image_sizes()
 *
 * @psalm-type WPRegisteredSize = array{
 *   width: int,
 *   height: int,
 *   crop: boolean
 * }
 */

 /**
 *
 * Breakdance\Media\Sizes\getAvailableSizes()
 *
 * @psalm-type ImageSize = array{
 *  slug: string,
 *  label: array|string|string[],
 *  subLabel?: string,
 *  width?: int,
 *  height?: int
 * }
 */

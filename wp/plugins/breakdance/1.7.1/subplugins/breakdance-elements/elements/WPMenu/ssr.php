<?php
/**
 * @var array $propertiesData
 */

$id = $propertiesData['content']['menu']['menu'] ?? null;

wp_nav_menu([
    'menu' => $id,
    'container' => null,
    'items_wrap' => '%3$s',
    'walker' => new AwesomeMenuWalker,
    'fallback_cb' => false
]);

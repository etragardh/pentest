<?php
/**
 * @var array $propertiesData
 */

$customUrl = $propertiesData['content']['button']['custom_url'] ?? null;
$urlToLike = $propertiesData['content']['button']['url_to_like'] ?? null;

$url = $urlToLike && $customUrl ? $propertiesData['content']['button']['custom_url'] : get_permalink();

echo $url;


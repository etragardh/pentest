<?php
/**
 * @var array $propertiesData
 */

$customUrl = $propertiesData['content']['button']['custom_url'] ?? null;
$urlToLike = $propertiesData['content']['button']['url_to_like'] ?? null;

echo $url = $urlToLike && $customUrl ? $customUrl : get_permalink();

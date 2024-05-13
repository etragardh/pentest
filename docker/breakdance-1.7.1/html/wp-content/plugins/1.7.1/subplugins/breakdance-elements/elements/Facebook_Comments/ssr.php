<?php
/**
 * @var array $propertiesData
 */

if ($propertiesData['content']['comments']['comment_on'] == 'custom_url' && $propertiesData['content']['comments']['custom_url']) {
    echo $propertiesData['content']['comments']['custom_url'];
} else {
    echo get_permalink();
}

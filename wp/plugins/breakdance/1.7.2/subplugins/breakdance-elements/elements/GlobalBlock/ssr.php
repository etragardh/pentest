<?php
/**
 * @var array $propertiesData
 * @var int|null $repeaterItemNodeId
 */

$blockId = $propertiesData['content']['content']['block'] ?? null;

if ($blockId) {
    echo \Breakdance\Render\render($blockId, $repeaterItemNodeId);
} else {
    if ($_REQUEST['triggeringDocument'] ?? true) {
        echo '<div class="breakdance-empty-ssr-message">Choose a Global Block from the dropdown.</div>';
    } else {
        echo "<!-- Breakdance error: $blockId not found -->";
    }
}

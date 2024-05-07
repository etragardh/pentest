<?php

namespace Breakdance\Util\ErrorHTML;

/**
 * @param string $html
 * @return string
 */
function wrapWithError($html) {

    ob_start();
    echo "<div style='padding: 20px; border: 5px solid #aa0000; background-color: #ffcccc; color: black; font-family: sans-serif;'>";
    echo $html;
    echo "</div>";
    return (string) ob_get_clean();

}

/**
 * @param int $postId
 * @param int[] $renderStack
 * @param bool $addEditLink
 * @return string
 */
function wrapInRedAndAddEditLink($postId, $renderStack, $addEditLink = false) {
    $editLinkUrl = get_edit_post_link($postId, 'asdf');
    $editLink = "<a href='$editLinkUrl' force-allow-clicks target='_blank'>edit</a>";
    $editLinkString = $addEditLink ? " ($editLink)" : "";
    $valuesCount = array_count_values($renderStack);
    if (isset($valuesCount[$postId]) && $valuesCount[$postId] > 1) {
        return "<span style='background-color: #880000; color: #ffcccc;'>$postId</span>" . $editLinkString;
    } else {
        return $postId . $editLinkString;
    }

}


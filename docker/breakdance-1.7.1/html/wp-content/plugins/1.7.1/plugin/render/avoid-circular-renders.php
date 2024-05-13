<?php

namespace Breakdance\Render;

use function Breakdance\Util\ErrorHTML\wrapInRedAndAddEditLink;
use function Breakdance\Util\ErrorHTML\wrapWithError;

class CircularRendererTracker {

    use \Breakdance\Singleton;

    /**
     * @var int[]
     */
    public $renderStack = [];

    /**
     * @var int
     */
    public $currentlyRenderingPostOrLastRenderedPost = 0;

    /**
     * @param int $postId
     * @return int[]|false
     */
    function startRender($postId) {

        $this->currentlyRenderingPostOrLastRenderedPost = $postId;

        if (in_array($postId, $this->renderStack)) {
            return array_merge($this->renderStack, [$postId]);
        }

        $this->renderStack[] = $postId;

        return false;

    }

    function endRender() {
        array_pop($this->renderStack);

        if (count($this->renderStack) === 0){
            $this->currentlyRenderingPostOrLastRenderedPost = 0;
        }
    }
}

/**
 * @param int[] $renderStack
 * @return string
 */
function getCircularRenderErrorAsHtml($renderStack) {

    ob_start();

    echo "The renderer is stuck in an inifite loop, because...<br />";

    for ($i = 0; $i < count($renderStack); $i++) {
        $postId = $renderStack[$i];
        $postId2 = $renderStack[$i + 1];

        if ($i < count($renderStack) - 1) {
            $postIdMaybeWrappedInRed = wrapInRedAndAddEditLink($postId, $renderStack);
            $postId2MaybeWrappedInRed = wrapInRedAndAddEditLink($postId2, $renderStack, true);
            echo "'post' with ID $postIdMaybeWrappedInRed renders 'post' {$postId2MaybeWrappedInRed}<br />";
        }

    }

    echo "And so on...<br /><br />

    <small><i>a 'post' could be a Breakdance template, global block, WP post, page, or any other content</i></small>";

    return wrapWithError(ob_get_clean());


}

<?php

namespace Breakdance\WooCommerce;

/**
 * @param string $pageName
 * @param string $elementLabel
 * @param string $pageLabel
 */
function getErrorMessageForWooElementPageInWrongPage($pageName, $elementLabel, $pageLabel){
    $pageId = wc_get_page_id($pageName);

    $line1 = <<<HTML
    The <b>"$elementLabel"</b> element can only be added to the <b>WooCommerce $pageLabel Page</b>.<br /><br />
    HTML;
    $line2 = <<<HTML
    Set the <b>WooCommerce $pageLabel Page</b> in the WP admin at <b>WooCommerce &gt; Settings &gt; Advanced &gt; Page Setup</b>.
    HTML;
    if ($pageId === -1){
        echo <<<HTML
            <div class="breakdance-empty-ssr-message breakdance-empty-ssr-message-error">
                <div>
                    $line1
                    No page has been set as the <b>WooCommerce $pageLabel Page</b>.<br /><br />
                    $line2
                </div>
            </div>
        HTML;
    }
    else {
        $page = get_post($pageId);
        $pageTitle = $page && is_object($page) ? '"' . $page->post_title . '"' : "";

        echo <<<HTML
            <div class="breakdance-empty-ssr-message breakdance-empty-ssr-message-error">
                <div>
                    $line1
                    The <b>WooCommerce $pageLabel Page</b> is currently set to <b>$pageTitle (ID: $pageId)</b>.<br /><br />
                    $line2
            </div>
        HTML;
    }
}

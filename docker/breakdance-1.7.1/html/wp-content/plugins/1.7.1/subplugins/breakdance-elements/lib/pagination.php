<?php

namespace Breakdance\EssentialElements\Lib\PostsPagination;

use Breakdance\Render\ScriptAndStyleHolder;
use function Breakdance\isRequestFromBuilderSsr;
use function Breakdance\Util\WP\isAnyArchive;
use function Breakdance\WpQueryControl\getPage;

/**
 * inspired by https://www.wpbeginner.com/wp-themes/how-to-add-numeric-pagination-in-your-wordpress-theme
 *
 * @param int $maxNumberOfPages
 * @param int $currentPage
 * @param boolean $showAllPageNumbers
 * @param boolean $includePrevNext
 * @param string|null $previousText
 * @param string|null $nextText
 */
function getNumberedAndOrPrevNextPagination(
    $maxNumberOfPages,
    $currentPage,
    $showAllPageNumbers,
    $includePrevNext,
    $previousText = null,
    $nextText = null
) {
    $paged = getPagedOrOneForBuilder();

    if ($maxNumberOfPages <= 1) {
        return;
    }

    $pageNumbers = [];

    if ($showAllPageNumbers) {
        // fill up links with all page numbers
        while (count($pageNumbers) < $maxNumberOfPages) {
            $pageNumbers[] = count($pageNumbers) + 1;
        }
    } else {
        /** Add current page to the array */
        if ($currentPage >= 1) {
            $pageNumbers[] = $currentPage;
        }

        /** Add the pages around the current page to the array */
        if ($currentPage >= 3) {
            $pageNumbers[] = $currentPage - 1;
            $pageNumbers[] = $currentPage - 2;
        }

        if (($currentPage + 2) <= $maxNumberOfPages) {
            $pageNumbers[] = $currentPage + 2;
            $pageNumbers[] = $currentPage + 1;
        }
    }

    $numbersPrevNextClass = $includePrevNext ? ' bde-posts-navigation-numbers-prev-next' : '';

    echo '<div class="bde-posts-pagination bde-posts-navigation-numbers' . $numbersPrevNextClass . '">';

    $classNameActive = 'class="bde-posts-navigation-numbers--active"';
    $classNameEllipses = 'class="bde-posts-navigation-numbers--ellipses"';
    $classNameActiveEl = 'class="is-active"';

    if ($includePrevNext) {
        echo '<div class="bde-posts-prev-next-link">';
        echo getPreviousPostLink($previousText, $paged) ?? null;
        echo '</div>';
    }

    echo "<ul>";

    /** Link to first page, plus ellipses if necessary */
    if (!in_array(1, $pageNumbers) && !$showAllPageNumbers) {
        $class = $currentPage === 1 ? $classNameActive : '';
        $classEl = $currentPage === 1 ? $classNameActiveEl : '';
        $pageUrl = esc_url(get_pagenum_link(1));

        echo "<li {$class}><a {$classEl} href='{$pageUrl}'>1</a></li>";

        if (!in_array(2, $pageNumbers)) {
            echo "<li {$classNameEllipses}><span>…</span></li>";
        }
    }

    /** Link to current page, plus 2 pages in either direction if necessary */
    sort($pageNumbers);
    foreach ($pageNumbers as $pageNumber) {
        $class = $currentPage == $pageNumber ? $classNameActive : '';
        $classEl = $currentPage === $pageNumber ? $classNameActiveEl : '';
        $pageUrl = esc_url(get_pagenum_link($pageNumber));

        echo "<li {$class}><a {$classEl} href='{$pageUrl}'>{$pageNumber}</a></li>";
    }

    /** Link to last page, plus ellipses if necessary */
    if (!in_array($maxNumberOfPages, $pageNumbers) && !$showAllPageNumbers) {
        if (!in_array($maxNumberOfPages - 1, $pageNumbers)) {
            echo "<li {$classNameEllipses}><span>…</span></li>";
        }

        $class = $currentPage == $maxNumberOfPages ? $classNameActive : '';
        $classEl = $currentPage == $maxNumberOfPages ? $classNameActiveEl : '';
        $pageUrl = esc_url(get_pagenum_link($maxNumberOfPages));

        echo "<li {$class}><a {$classEl} href='{$pageUrl}'>{$maxNumberOfPages}</a></li>";
    }
    echo '</ul>';

    if ($includePrevNext) {
        echo '<div class="bde-posts-prev-next-link">';
        echo getNextPostLink($nextText, $paged, $maxNumberOfPages) ?? null;
        echo '</div>';
    }

    echo '</div>';
}

/**
 * @return int|mixed
 */
function getPagedOrOneForBuilder()
{
    $paged = getPage();

    // make the page 1 to show both "prev" and "next". Makes styling easier
    return isRequestFromBuilderSsr() ? 1 : $paged;
}

/**
 * @param string|null $previousText
 * @param string|null $nextText
 * @param int $maxNumberOfPages
 */
function getPreviousNextPagination($previousText, $nextText, $maxNumberOfPages)
{

    $currentPage = getPagedOrOneForBuilder();

    echo "<div class='bde-posts-pagination bde-posts-pagination-prevnext bde-posts-prev-next-link'>";
    echo getPreviousPostLink($previousText, $currentPage) ?? null;
    echo getNextPostLink($nextText, $currentPage, $maxNumberOfPages) ?? null;
    echo "</div>";
}

/**
 * This logic is copied WP's "get_previous_posts_link" and "get_next_posts_link" because they don't work for single posts
 */

/**
 * @param string $label
 * @param int $paged
 * @return string|null
 */
function getPreviousPostLink($label, $paged)
{
    $attr = apply_filters('previous_posts_link_attributes', '');

    if ($paged <= 0) {
        return null;
    }

    $nextPage = (int)$paged - 1;
    if ($nextPage < 1) {
        $nextPage = 1;
    }

    if (!$label) {
        $label = '&laquo; Previous';
    }

    return '<a href="' . esc_url(get_pagenum_link($nextPage)) . "\" $attr>" . preg_replace('/&([^#])(?![a-z]{1,8};)/i', '&#038;$1', $label) . '</a>';
}

/**
 * @param string|null $label
 * @param int $paged
 * @param int $max_page
 * @return string|null
 */
function getNextPostLink($label, $paged, $max_page)
{
    $attr = apply_filters('next_posts_link_attributes', '');

    if (!$paged) {
        $paged = 1;
    }

    $nextPage = (int)$paged + 1;

    if (!$max_page || $max_page < $nextPage) {
        return null;
    }

    if (!$label) {
        $label = 'Next &raquo;';
    }

    return '<a href="' . esc_url(get_pagenum_link($nextPage)) . "\" $attr>" . preg_replace('/&([^#])(?![a-z]{1,8};)/i', '&#038;$1', $label) . '</a>';
}

/********/

/**
 * @param string|null $buttonText
 * @param array $buttonDesign
 */
function getLoadMorePagination($buttonText, $buttonDesign, $ssrFilePath)
{
    echo "<div class='bde-posts-pagination' data-ssr-path='$ssrFilePath'>";
    echo \Breakdance\Elements\AtomV1Button\renderFormButton($buttonText, 'bde-posts-pagination-loadmore-button', $buttonDesign, 'custom', '');
    echo '</div>';
}

function getInfiniteScrollLoadIcon($showLoadingIcon, $ssrFilePath)
{
    $forceShow = $showLoadingIcon ? "style='display: block;'" : '';

    echo <<<HTML
    <div
      class="bde-posts-pagination-infinite-loader-wrapper bde-posts-pagination button-atom__icon-wrapper"
      data-ssr-path="$ssrFilePath"
    >
         <svg $forceShow class="breakdance-form-loader" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle opacity="0.25" fill="none" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path opacity="0.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>
HTML;
}

/**
 * @param QueryControlParams['custom']|array $customWpQuery
 * @param int $maxNumberOfPages
 * @return int
 */
function getMaxNumberOfPages($customWpQuery, $maxNumberOfPages)
{
    $postsPerPage = $customWpQuery['postsPerPage'] ?? null;
    $totalPosts = $customWpQuery['totalPosts'] ?? null;

    if ($postsPerPage && $totalPosts && $maxNumberOfPages > 1) {
        // total posts can limit the max number of pages. e.g; $maxNumberOfPages can be 10, but if:
        // $postsPerPage = 6. $totalPosts = 20. Then $maxNumberOfPages should be 4 instead.
        $maxNumberOfPagesForTotalPosts = intval(ceil($totalPosts / $postsPerPage));

        return $maxNumberOfPagesForTotalPosts > $maxNumberOfPages
            ? $maxNumberOfPages
            : $maxNumberOfPagesForTotalPosts;
    }

    return $maxNumberOfPages;
}

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_posts_pagination_load_more',
        '\Breakdance\EssentialElements\Lib\PostsPagination\getMorePosts',
        'none',
        false,
        [
            'args' => [
                'postId' => FILTER_VALIDATE_INT,
                'elementId' => FILTER_VALIDATE_INT,
                'paged' => FILTER_VALIDATE_INT,
                'ssrFilePath' => FILTER_UNSAFE_RAW,
            ],
        ],
        true
    );
});

$renderOnlyIndividualPosts = false;

/**
 * @param int $postId
 * @param int $elementId
 * @param int $paged
 * @return array[]|null
 */
function getMorePosts($postId, $elementId, $page, $ssrFilePath)
{
    global $renderOnlyIndividualPosts;

    do_action('breakdance_register_template_types_and_conditions');

    $node = \Breakdance\Render\getNodeById($postId, $elementId);

    if (!$node) {
        return null;
    }

    global $paged;

    $paged = $page;

    /** @var array */
    $propertiesData = $node['data']['properties'];
    $renderOnlyIndividualPosts = true;

    ob_start();
    require(WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $ssrFilePath);

    $dependencies = ScriptAndStyleHolder::getInstance()->dependencies;

    return [
        'data' =>
        [
            'html' => ob_get_clean(),
            'maxNumberOfPages' => getMaxNumberOfPages(
                $propertiesData['content']['query']['query']['custom'] ?? [],
                // comes from the $ssr file
                $loop->max_num_pages ?? 0
            ),
            'inlineScripts' => $dependencies['inlineScripts'] ?? []
        ],
    ];
}

/**
 * @param array $propertiesData
 * @param int $maxNumPages
 * @param string $layout
 * @param string $ssrFilePath
 * @return void
 */
function getPostsPaginationFromProperties($propertiesData, $maxNumPages, $layout, $ssrFilePath)
{
    $isotopeEnabled = $propertiesData['content']['filter_bar']['enable'] ?? false;

    if ($isotopeEnabled) return;

    // don't allow pagination with custom queries and pagination
    if (isAnyArchive() && ($propertiesData['content']['query']['query'] ?? false)) {
        return;
    }

    if (!apply_filters('breakdance_pagination_render_pagination', true, $propertiesData, $maxNumPages, $layout)) {
        return;
    }

    $paged = getPage();
    global $renderOnlyIndividualPosts;

    $pagination = $propertiesData['content']['pagination'] ?? [];
    if (empty($pagination)) {
        return;
    }
    $selectedPagination = $pagination['pagination'];

    /**
     * We can't access the default WP query for the archive with dynamic pagination since it's an AJAX
     */
    if (
        isAnyArchive() &&
        ($selectedPagination === "load_more" || $selectedPagination === "infinite")
    ) {
        if (isRequestFromBuilderSsr()) {
            echo "<div class='bde-ssr-error'>Dynamic pagination isn't allowed in Archives.</div> ";
        }

        $selectedPagination = 'numbersprevnext';
    }

    if ($selectedPagination) {
        $customWpQuery = $contentProps['query']['query']['custom'] ?? [];
        $maxNumberOfPages = getMaxNumberOfPages($customWpQuery, $maxNumPages);
        $pageNumber = $paged ?: 1;

        switch ($selectedPagination) {
            case 'numbers':
                getNumberedAndOrPrevNextPagination(
                    $maxNumberOfPages,
                    $pageNumber,
                    $pagination['show_all_page_numbers'] ?? false,
                    false,
                );
                break;
            case 'prevnext':
                getPreviousNextPagination(
                    $pagination['previous_text'] ?? null,
                    $pagination['next_text'] ?? null,
                    $maxNumberOfPages
                );
                break;
            case 'numbersprevnext':
                getNumberedAndOrPrevNextPagination(
                    $maxNumberOfPages,
                    $pageNumber,
                    $pagination['show_all_page_numbers'] ?? false,
                    true,
                    $pagination['previous_text'] ?? null,
                    $pagination['next_text'] ?? null
                );
                break;
            case 'load_more':
                if ($layout !== "slider" && $maxNumberOfPages > $pageNumber && !$renderOnlyIndividualPosts) {
                    getLoadMorePagination(
                        $pagination['load_more_text'] ?? 'Load more',
                        $propertiesData['design']['pagination']['load_more_button'] ?? [],
                        $ssrFilePath
                    );
                }
                break;
            case 'infinite':
                if ($layout !== "slider" && $maxNumberOfPages > $pageNumber && !$renderOnlyIndividualPosts) {
                    getInfiniteScrollLoadIcon(
                        $propertiesData['design']['pagination']['show_loading_icon_in_builder'],
                        $ssrFilePath
                    );
                }
                break;
        }
    }
}

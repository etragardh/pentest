<?php

use function Breakdance\WpQueryControl\getFilterAttributesForPost;


function do_the_loop($loop, $layout, $filterbar, $propertiesData, $actionData)
{
    $loopIndex = 0;

    while ($loop->have_posts()) {
        $loopIndex++;

        $block = getBlockForLoopIndex($propertiesData, $loopIndex);

        if ($block['type'] !== 'static') {
            $loop->the_post();
        }

        $itemClasses = get_item_classes($layout);

        $postTag = $propertiesData['content']['repeated_block']['tag'] ?? 'article';
        $attrs = getFilterAttributesForPost($filterbar, $itemClasses);

        render_individual_post(
            $actionData,
            $postTag,
            $attrs,
            $block['id']
        );
    }
}


function render_individual_post($actionData, $postTag, $attrs, $blockId)
{

    do_action("breakdance_posts_loop_before_post", $actionData);
?>
    <<?php echo $postTag; ?> <?php echo $attrs; ?>>
        <?php
        if ($blockId) {
            $postId = get_the_ID();
            echo \Breakdance\Render\render($blockId, $postId);
        } else {
            if ($_REQUEST['triggeringDocument'] ?? true) {
                echo '<div class="breakdance-empty-ssr-message">Choose a Global Block from the dropdown.</div>';
            } else {
                echo "<!-- Breakdance error: $blockId not found -->";
            }
        }
        ?>
    </<?php echo $postTag; ?>>
<?php
    do_action("breakdance_posts_loop_after_post", $actionData);
}

function getBlockForLoopIndex($propertiesData, $loopIndex)
{

    if ($propertiesData['content']['repeated_block']['advanced']['static_items'] ?? false) {
        $staticItemBlock = getBlockIdForLoopIndex($propertiesData['content']['repeated_block']['advanced']['static_items'], $loopIndex);

        if ($staticItemBlock) {
            return [
                'type' => 'static',
                'id' => $staticItemBlock,
            ];
        }
    }

    if ($propertiesData['content']['repeated_block']['advanced']['alternates'] ?? false) {
        $alternateBlock = getBlockIdForLoopIndex($propertiesData['content']['repeated_block']['advanced']['alternates'], $loopIndex);

        if ($alternateBlock) {
            return [
                'type' => 'alternate',
                'id' => $alternateBlock,
            ];
        }
    }

    $blockId = $propertiesData['content']['repeated_block']['global_block'] ?? false;

    return [
        'type' => 'default',
        'id' => $blockId,
    ];
}

/**
 * @param array{repeat?:boolean,global_block?:int,position?:int,frequency?:int}[]
 * @param int $loopIndex
 * @return false|int
 */
function getBlockIdForLoopIndex($blocksPropertiesData, $loopIndex)
{

    $blockId = false;

    foreach ($blocksPropertiesData as $alternate) {

        $position = $alternate['position'] ?? false;
        $global_block = $alternate['global_block'] ?? false;
        $frequency = $alternate['frequency'] ?? $position;

        if ($frequency <= 1) {
            /*
            frequency of 1 or less makes no sense, and will timeout the server when using static item position because it'll cause
            the posts loop to never finish since it'll just keep outputting static items at every position - i.e. an infinite loop
            */
            $frequency = 10000;
        }

        if ($position === $loopIndex) {
            $blockId = $global_block;
            break;
        }

        if ($alternate['repeat'] && $loopIndex > $position) {

            $distanceFromStartingPosition = $loopIndex - $position;

            if ($distanceFromStartingPosition % $frequency === 0) {
                $blockId = $global_block;
                break;
            }
        }
    }

    return $blockId ? $blockId : false;
}

function get_item_classes($layout)
{

    $itemClasses = 'ee-post';

    if ($layout == 'slider') {
        $itemClasses .= ' swiper-slide';
    }

    return $itemClasses;
}

function getWpQuery($propertiesData)
{

    $paged = ($propertiesData['content']['pagination']['pagination'] ?? false) ? \Breakdance\WpQueryControl\getPage() : 0;

    $argsFromQuery = \Breakdance\WpQueryControl\getWpQueryArgumentsFromWpQueryControlProperties(
        $propertiesData['content']['query']['query'] ?? null,
        [
            'paged' => $paged > 1 ? $paged : null,
        ]
    );

    return new \WP_Query($argsFromQuery);
}


function output_before_the_loop($renderOnlyIndividualPosts, $filterbar, $layout)
{

    $wrapperClass = 'ee-posts';

    if (!$renderOnlyIndividualPosts) {
        \Breakdance\WpQueryControl\renderIsotoperFilterBar($filterbar);

        if ($filterbar['enable'] ?? false) {
            $wrapperClass .= ' ee-posts-isotope';
        }

        if ($layout == "slider") {
            $wrapperClass .= ' swiper-wrapper';
            \Breakdance\WpQueryControl\renderSwiperContainer();
        }

        echo "<div class=\"{$wrapperClass} ee-posts-{$layout}\">";
    }
}

function output_after_the_loop($renderOnlyIndividualPosts, $filterbar, $layout, $propertiesData)
{
    if (!$renderOnlyIndividualPosts) {
        \Breakdance\WpQueryControl\renderIsotopeFooter($filterbar);

        echo "</div>"; // close wrapper

        if ($layout == "slider") {
            \Breakdance\WpQueryControl\closeSwiperContainer($propertiesData['design']['list']['slider'] ?? []);
        }
    }
}

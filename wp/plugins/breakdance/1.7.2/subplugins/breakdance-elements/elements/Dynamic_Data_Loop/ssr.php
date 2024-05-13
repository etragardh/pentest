<?php


/*
these function are copy-paste duplicates from the Post Loop Builder element
*/

require_once __DIR__ . "/dyn-data-loop.php";
 
/**
 * @var array $propertiesData
 */
$fieldSlug = $propertiesData['content']['field']['repeater_field'] ?? false;
$blockId = $propertiesData['content']['repeated_block']['global_block'] ?? -1;
$postTag = $propertiesData['content']['repeated_block']['tag'] ?? 'article';

/** @var \Breakdance\DynamicData\RepeaterField $field */
$field = \Breakdance\DynamicData\DynamicDataController::getInstance()->getField($fieldSlug);

$layout = (string) ($propertiesData['design']['list']['layout'] ?? '');
if ($layout == "list") {
    $wrapperClass = 'bde-dynamic-repeater-list';
} else if ($layout == "slider") {
    $wrapperClass = 'bde-dynamic-repeater-slider swiper-wrapper';
} else {
    $wrapperClass = 'bde-dynamic-repeater-grid';
}

if ($field) {
    $isOption = $field->field['is_option_page'] ?? false;
    $postId = $isOption ? 'option' : get_the_ID();
    $swiperClass = ($layout == 'slider' ? ' swiper-slide' : '');

    $loopIndex = 1;
    echo '<div class="ee-posts bde-dynamic-repeater bde-dynamic-repeater-%%ID%% ' . $wrapperClass . '">';
    while ($field->hasSubFields($postId)) {

        $block = getBlockForLoopIndex2($propertiesData, $loopIndex);

        if ($block['type'] === 'static') {

            // stop the loop index from incrementing for static items

            if ($field instanceof \Breakdance\DynamicData\MetaboxGroupField) {
                /* with metabox, currentIndex is incremented when calling hasSubFields above */
                $field->decrementCurrentIndexByOne();
            } elseif ($field instanceof \Breakdance\DynamicData\AcfRepeaterField && function_exists('acf_get_loop')) {
                /*
                with acf the loop is incremented when you call the has_sub_fields function, 
                cuz that ultimately calls the_row
                so the following was cargo-culted in here from reading the_row in the ACF codebase...
                it does the same thing as the_row, but in reverse (they call $i++, we call $i--)
                */
                $acfI = acf_get_loop('active', 'i');
                $acfI--;
                acf_update_loop('active', 'i', $acfI);
            }
        }

        echo '<' . $postTag . ' class="ee-post bde-dynamic-repeater-item' . $swiperClass . '">';
        echo \Breakdance\Render\render($block['id'], "{$postId}-{$loopIndex}");
        echo '</' . $postTag . '>';
        $loopIndex++;
    }
    echo '</div>';
}

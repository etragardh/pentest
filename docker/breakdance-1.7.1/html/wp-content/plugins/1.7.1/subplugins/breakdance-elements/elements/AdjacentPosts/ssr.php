<?php
/**
 * @var array $propertiesData
 */

$arrow = (bool) ($propertiesData['content']['content']['disable_arrow'] ?? false); // Arrow toggle
$posttitle = (bool) ($propertiesData['content']['content']['disable_post_title'] ?? false); // Post Title toggle
$in_same_term = (bool) ($propertiesData['content']['content']['same_taxonomy_only'] ?? false); // same taxonomy toggle
$taxonomy = (string) ($propertiesData['content']['content']['taxonomy'] ?? 'category');

$prev_right = (bool) ($propertiesData['design']['layout']['previous_on_right'] ?? false); // Previous On Right toggle

$labels = (bool) ($propertiesData['content']['content']['labels']['disable'] ?? false); // labels toggle
$label_previous = (string) ($propertiesData['content']['content']['labels']['previous'] ?? 'Previous Post'); // label previous
$label_next = (string) ($propertiesData['content']['content']['labels']['next'] ?? 'Next Post'); // label next
$label_position = (string) ($propertiesData['design']['layout']['label_position'] ?? ''); // column or column-reverse

$default_arrow_prev = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M256 504C119 504 8 393 8 256S119 8 256 8s248 111 248 248-111 248-248 248zm116-292H256v-70.9c0-10.7-13-16.1-20.5-8.5L121.2 247.5c-4.7 4.7-4.7 12.2 0 16.9l114.3 114.9c7.6 7.6 20.5 2.2 20.5-8.5V300h116c6.6 0 12-5.4 12-12v-64c0-6.6-5.4-12-12-12z"></path></svg>';
$default_arrow_next = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!-- Font Awesome Free 5.15.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M256 8c137 0 248 111 248 248S393 504 256 504 8 393 8 256 119 8 256 8zM140 300h116v70.9c0 10.7 13 16.1 20.5 8.5l114.3-114.9c4.7-4.7 4.7-12.2 0-16.9l-114.3-115c-7.6-7.6-20.5-2.2-20.5 8.5V212H140c-6.6 0-12 5.4-12 12v64c0 6.6 5.4 12 12 12z"></path></svg>';

if ($prev_right) {
    $arrow_prev = (string) ($propertiesData['design']['arrow']['custom_icon']['previous']['svgCode'] ?? $default_arrow_next );
    $arrow_next = (string) ($propertiesData['design']['arrow']['custom_icon']['next']['svgCode'] ?? $default_arrow_prev );
} else {
    $arrow_prev = (string) ($propertiesData['design']['arrow']['custom_icon']['previous']['svgCode'] ?? $default_arrow_prev );
    $arrow_next = (string) ($propertiesData['design']['arrow']['custom_icon']['next']['svgCode'] ?? $default_arrow_next );
}
$prev_post = get_previous_post($in_same_term, '', $taxonomy);
$next_post = get_next_post($in_same_term, '', $taxonomy);
$next_post_permalink_raw = $next_post instanceof WP_Post ? get_permalink( $next_post->ID ) : false;
$prev_post_permalink_raw = $prev_post instanceof WP_Post ? get_permalink( $prev_post->ID ) : false;
$next_post_permalink_str = is_string($next_post_permalink_raw) ? $next_post_permalink_raw : '';
$prev_post_permalink_str = is_string($prev_post_permalink_raw) ? $prev_post_permalink_raw : '';

$prev_title = (string) apply_filters( 'the_title', $prev_post instanceof WP_Post ? $prev_post->post_title : '' );
$next_title = (string) apply_filters( 'the_title', $next_post instanceof WP_Post ? $next_post->post_title : '' );


if ( empty( $prev_post ) && empty( $next_post )) {
    return 'No posts found';
}

if ( $prev_post instanceof WP_Post ) { ?>

<a rel="prev" class="ee-adjacentposts-prev <?php if (empty( $next_post )) { echo 'ee-adjacentposts-full';} ?>" href="<?php echo $prev_post_permalink_str; ?>">
      <?php if ( (!$arrow)) { ?>
        <div class="ee-adjacentposts-icon">
            <?php echo $arrow_prev ?>
        </div>
      <?php } ?>
    <div class="ee-adjacentposts-content">
        <?php if ($label_position == "bottom") { ?>
            <?php if (!$posttitle) { ?> <h4 class="ee-adjacentposts-title"><?php echo (string) apply_filters( 'the_title', $prev_post->post_title );?></h4> <?php } ?>
            <?php if (!$labels) { ?><span class="ee-adjacentposts-label"><?php echo $label_previous; ?></span> <?php } ?>
        <?php } else { ?>
            <?php if (!$labels) { ?><span class="ee-adjacentposts-label"><?php echo $label_previous; ?></span> <?php } ?>
            <?php if (!$posttitle) { ?> <h4 class="ee-adjacentposts-title"><?php echo (string) apply_filters( 'the_title', $prev_post->post_title );?></h4> <?php } ?>
        <?php } ?>
    </div>
</a>
<?php }

if ( $next_post instanceof WP_Post ) { ?>

<a rel="next" class="ee-adjacentposts-next <?php if (empty( $prev_post )) { echo 'ee-adjacentposts-full';} ?>" href="<?php echo $next_post_permalink_str; ?>">
    <div class="ee-adjacentposts-content">
        <?php if ($label_position == "bottom") { ?>
            <?php if (!$posttitle) { ?> <h4 class="ee-adjacentposts-title"><?php echo (string) apply_filters( 'the_title', $next_post->post_title );?></h4> <?php } ?>
            <?php if (!$labels) { ?><span class="ee-adjacentposts-label"><?php echo $label_next; ?></span> <?php } ?>
        <?php } else { ?>
            <?php if (!$labels) { ?><span class="ee-adjacentposts-label"><?php echo $label_next; ?></span> <?php } ?>
            <?php if (!$posttitle) { ?> <h4 class="ee-adjacentposts-title"><?php echo (string) apply_filters( 'the_title', $next_post->post_title );?></h4> <?php } ?>
        <?php } ?>
    </div>
    <?php if ( (!$arrow)) { ?>
        <div class="ee-adjacentposts-icon">
            <?php echo $arrow_next ?>
        </div>
      <?php } ?>
</a>
<?php }

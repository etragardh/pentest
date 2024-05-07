<?php
/**
 * @var array $propertiesData
 */

global $post;

$metaData = $propertiesData['content']['content']['meta_data'] ?? [];

if ($post && !empty($metaData)) {

    // Get author's display name
    $display_name = get_the_author_meta( 'display_name', $post->post_author );
    // If display name is not available then use nickname as display name
    if ( empty( $display_name ) ) $display_name = get_the_author_meta( 'nickname', $post->post_author );
    // Get link to the author archive page
    $user_posts = get_author_posts_url( get_the_author_meta( 'ID' , $post->post_author));

    foreach ($metaData as $data) {

        // common datas
        $datatype = (string)($data['type'] ?? '');
        $databefore = (string)($data['before_text'] ?? '');
        $dataicon = (string)($data['icon']['svgCode'] ?? '');
        $datalink = (bool)($data['link'] ?? false);

        // author datas
        $dataavatar = (bool)($data['avatar'] ?? false);

        // date datas
        $datadateformat = (string)($data['date_format'] ?? 'F jS, Y');
        $datacustdateformat = (string)($data['custom_date_format'] ?? 'F jS, Y');
        if ($datadateformat == "custom") {
            $datadate = $datacustdateformat;
        } else {
            $datadate = $datadateformat;
        }

        // Terms datas
        $datataxo = (string)($data['taxonomy'] ?? '');
        $datataxocount = $data['count'] ?? null;

        // Comments datas
        $dataCustnocom = (string)($data['no_comments'] ?? 'No Comments');
        $dataCustOnecom = (string)($data['one_comment'] ?? 'One Comment');
        $dataCustCom = (string)($data['comments'] ?? '% Comments');

        // custom
        $dataCustom = (string)($data['custom'] ?? '');
        $dataCustomURL = (string)($data['custom_url'] ?? '');

        if ($datatype == "author") { ?>
            <li class="ee-postmeta-author-wrap">
                <?php if (!empty($datalink)) { ?><a href="<?php echo $user_posts ?>"><?php } ?>
                    <?php if (!empty($dataavatar)) { ?>
                        <div class="ee-postmeta-avatar"><?php echo get_avatar($post->post_author); ?></div><?php } ?>
                    <?php if (!empty($dataicon) && empty($dataavatar)) { ?>
                        <div class="ee-postmeta-icon breakdance-icon-atom"><?php echo $dataicon; ?></div><?php } ?>
                    <span class="ee-postmeta-author"><?php if (!empty($databefore)) { ?><span
                            class="ee-postmeta-before"><?php echo $databefore; ?></span><?php }
                        echo $display_name; ?></span>
                    <?php if (!empty($datalink)) { ?></a> <?php } ?>
            </li>
            <?php
        }

        if ($datatype == "date") { ?>
            <li class="ee-postmeta-date-wrap">
                <?php if (!empty($datalink)) { ?><a
                    href="<?php echo get_day_link(get_post_time('Y'), get_post_time('m'), get_post_time('j')); ?>"><?php } ?>
                    <?php if (!empty($dataicon)) { ?>
                        <div class="ee-postmeta-icon breakdance-icon-atom"><?php echo $dataicon; ?></div><?php } ?>
                    <span class="ee-postmeta-date"><?php if (!empty($databefore)) { ?><span
                            class="ee-postmeta-before"><?php echo $databefore; ?></span><?php }
                        the_time($datadate); ?></span>
                    <?php if (!empty($datalink)) { ?></a> <?php } ?>
            </li>
            <?php
        }

        if ($datatype == "comments") { ?>
            <li class="ee-postmeta-comments-wrap">
                <?php if (!empty($datalink)) { ?><a href="<?php comments_link(); ?>"><?php } ?>
                    <?php if (!empty($dataicon)) { ?>
                        <div class="ee-postmeta-icon breakdance-icon-atom"><?php echo $dataicon; ?></div><?php } ?>
                    <span class="ee-postmeta-comments"><?php if (!empty($databefore)) { ?><span
                            class="ee-postmeta-before"><?php echo $databefore; ?></span><?php }
                        comments_number($dataCustnocom, $dataCustOnecom, $dataCustCom); ?></span>
                    <?php if (!empty($datalink)) { ?></a> <?php } ?>
            </li>
            <?php
        }

        if ($datatype == "terms") {
            if (!empty($dataicon)) {
                $termicon = '<div class="ee-postmeta-icon breakdance-icon-atom">' . $dataicon . '</div>';
            }
            if (!empty($databefore)) {
                $termbefore = '<span class="ee-postmeta-before">' . $databefore . '</span>';
            }
            $terms = get_the_terms(get_the_ID(), $datataxo);
            if ($terms && !is_wp_error($terms)) {
                $term_links = array();
                foreach (array_slice($terms, 0, $datataxocount) as $term) {
                    if (!empty($datalink)) {
                        $term_links[] = '<span class="ee-postmeta-term"><a href="' . esc_attr(get_term_link($term->slug, $datataxo)) . '">' . __($term->name) . '</span></a>';
                    } else {
                        $term_links[] = '<span class="ee-postmeta-term">' . __($term->name) . '</span>';
                    }
                }
                $all_terms = join($term_links);
                echo '<li class="ee-postmeta-terms-wrap">' . ($termicon ?? '') . ($termbefore ?? '') . __($all_terms) . '</li>';
            }
        }

        if ($datatype == "custom") { ?>
            <li class="ee-postmeta-custom-wrap">
                <?php if ((!empty($dataCustomURL)) && (!empty($datalink))) { ?><a
                    href="<?php echo $dataCustomURL ?>"><?php } ?>
                    <?php if (!empty($dataicon)) { ?>
                        <div class="ee-postmeta-icon breakdance-icon-atom"><?php echo $dataicon; ?></div><?php } ?>
                    <span class="ee-postmeta-custom"><?php if (!empty($databefore)) { ?><span
                            class="ee-postmeta-before"><?php echo $databefore; ?></span><?php }
                        echo $dataCustom ?></span>
                    <?php if (!empty($datalink)) { ?></a> <?php } ?>
            </li>
            <?php
        }
    }

}

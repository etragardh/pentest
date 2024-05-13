<?php

global $post;

$profilpic = $propertiesData['content']['content']['image'] ?? null; // profil picture toggle
$authorname = $propertiesData['content']['content']['name'] ?? null; // author name toggle
$htmltag = $propertiesData['content']['content']['name_html_tag'] ?? null; // HTML tag author name
$linkimage = $propertiesData['content']['content']['link_on_name_image'] ?? null; // Link on Text and Image
$authorbio = $propertiesData['content']['content']['biography'] ?? null; // author bio toggle
$postslink = $propertiesData['content']['content']['all_posts_link'] ?? null; // all posts link toggle
$poststext = $propertiesData['content']['content']['all_posts_link_text'] ?? null; // all posts link text
$websitelink = $propertiesData['content']['content']['website_link'] ?? null; // website link toggle
$websitetext = $propertiesData['content']['content']['website_link_text'] ?? null; // website link text

// Detect if it is a single post with a post author
//if ( is_single() && isset( $post->post_author ) ) {

// Get author's display name
$display_name = get_the_author_meta('display_name', $post->post_author ?? false);

// If display name is not available then use nickname as display name
if (empty($display_name)) {
    $display_name = get_the_author_meta('nickname', $post->post_author ?? false);
}

if (empty($htmltag)) {
    $htmltag = "h2";
}
// default html tag for author name

// Get author's biographical information or description
$user_description = get_the_author_meta('user_description', $post->post_author ?? false);

// Get author's website URL
$user_website = get_the_author_meta('url', $post->post_author ?? false);

// Get link to the author archive page
$user_posts = get_author_posts_url(get_the_author_meta('ID', $post->post_author ?? false));

if ($linkimage == "website") {
    $linkimage = $user_website;
} else if ($linkimage == "allposts") {
    $linkimage = $user_posts;
} else {
    $linkimage = null;
}

if ($profilpic) {
    if (!empty($linkimage) && isset($post->post_author)) {?>
      <a href="<?php echo $linkimage ?>" target="blank" class="ee-author-avatar"><?php echo get_avatar($post->post_author, 300); ?></a>
    <?php
    }
    else if ($post->post_author ?? false) { ?>
      <div class="ee-author-avatar"><?php echo get_avatar($post->post_author, 300); ?></div>
      <?php
    }
}
?>

<div class="ee-author-description">

<?php if ($authorname) {?>
<?php if (!empty($linkimage)) {?>
<a href="<?php echo $linkimage ?>" target="blank" class="ee-author-name-link"><<?php echo $htmltag; ?> class="ee-author-name"><?php echo $display_name ?></<?php echo $htmltag; ?>></a>
<?php } else {?>
<<?php echo $htmltag; ?> class="ee-author-name"><?php echo $display_name ?></<?php echo $htmltag; ?>>
<?php }
}?>

<?php if ($authorbio) {?>
<div class="ee-author-bio"><?php echo nl2br($user_description) ?></div>
<?php }?>

<?php if (($postslink) or ($websitelink)) {?>
<div class="ee-author-links">
<?php }?>
<?php if ($postslink) {

    $buttonPostsContent['text'] = $poststext ? $poststext : "View all posts";
    $buttonPostsContent['link'] = $user_posts;
    echo \Breakdance\Elements\AtomV1Button\render($buttonPostsContent, 'ee_author-button-posts', $propertiesData['design']['links']['all_posts'] ?? [], 'primary');

}?>

<?php if ($websitelink) {
    $buttonLinkContent['text'] = $websitetext ? $websitetext : "Website";
    $buttonLinkContent['link'] = $user_website;
    echo \Breakdance\Elements\AtomV1Button\render($buttonLinkContent, 'ee_author-button-website', $propertiesData['design']['links']['website'] ?? [], 'secondary');
}?>

<?php if (($postslink) or ($websitelink)) {?>
</div>
<?php }?>

</div>

<?php

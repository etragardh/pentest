<?php
if ( ! oxygen_vsb_current_user_can_access() ) {
    die ( 'Security check' );
}

global $oxy_ajax_post_id;
$oxy_ajax_post_id = intval($_REQUEST['post_id']);

$result = oxygen_vsb_cache_page_css(intval( $_REQUEST['post_id'] ));
if ($result) {
    $message = __("CSS cache generated successfully.","oxygen");
}
else {
    $message = __("CSS cache not generated.","oxygen");
}
echo "<div>" . $message . " Post ID: " . intval($_REQUEST['post_id'])." - " . get_the_title(intval($_REQUEST['post_id'])) . "</div>";

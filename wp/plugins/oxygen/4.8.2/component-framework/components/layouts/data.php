<?php
// TODO review the security aspect

if (!is_user_logged_in() || !oxygen_vsb_current_user_can_access()) {
	die();
}

$nonce  	= $_REQUEST['nonce'];
$post_id 	= $_REQUEST['post_id'];

// check nonce
if ( ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
	// This nonce is not valid.
	die( 'Security check' );
}

// get passed parameters
$name 	 = $_REQUEST['component_name'] . '_ajax';

$options = file_get_contents('php://input');
$options = json_decode( $options, true );

if ( ! $options ) {
	_e("Can't get component options", "component-theme");
	die();
};

$shortcode = $executed_shortcode = "";

$shortcode = "[" . $name . " ";

foreach ( $options['original'] as $param => $value) {
	$shortcode .= "$param=\"$value\" ";
}

$shortcode .= "]";

$executed_shortcode = do_shortcode( $shortcode );


if ( $executed_shortcode == $shortcode ) {
	_e("Can't execute the shortcode. Make sure tag and parameters are correct.", "component-theme");
}
else {
	echo $executed_shortcode;
}
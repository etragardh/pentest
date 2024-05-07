<?php
	// TODO review the security aspect

	if (!is_user_logged_in() || !current_user_can( 'manage_options' )) {
	    die();
		}

	$nonce  	= $_REQUEST['nonce'];
	$post_id 	= $_REQUEST['post_id'];

	// check nonce
	if ( ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
	    // This nonce is not valid.
	    die( 'Security check' );
	}

	$shortcode = file_get_contents('php://input');
	$shortcode = ct_sign_oxy_dynamic_shortcode(array($shortcode));
	$shortcode = do_shortcode($shortcode);

	echo $shortcode;
	
	die();

?>

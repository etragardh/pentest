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

	$options = file_get_contents('php://input');
	$options = json_decode( $options, true );

	$result = false;

	// eval oxygen dynamic data
	if(isset($options['original']) && isset($options['original']['conditions'])) {
		$result = OxygenConditions::eval_condition($options['original']['conditions'])?1:0;
	}
		
	header('Content-Type: application/json');
	if($result !== false) {
		$output = array('result' => $result);
	}
	else {
		$output = array();
	}
  	echo json_encode( $output );
	die();

?>

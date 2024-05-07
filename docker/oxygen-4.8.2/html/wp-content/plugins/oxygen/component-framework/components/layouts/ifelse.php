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
	
	$conditions = file_get_contents('php://input');
	$conditions = json_decode( $conditions, true );
	
	global $OxygenConditions;
	if (isset($OxygenConditions)) {
		$result = $OxygenConditions->global_conditions_result($conditions);
	}
	else {
		$result = "";
	}
	//TODO: security check for conditions
	
	// echo JSON
  	header('Content-Type: application/json');
  	echo json_encode( array('value' => $result) );
	die();
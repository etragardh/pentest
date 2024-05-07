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
	
	$instance = array();
	
	$component_json = file_get_contents('php://input');
	$component 		= json_decode( $component_json, true );
	$options 		= $component['options']['original'];

	//var_dump($options["instance"]);
	
	if ( is_array( $options['instance'] ) ) {
		$instance = $options['instance'];
	}

	if ( $GLOBALS['wp_widget_factory']->widgets[$options['class_name']] ) {
		wp_enqueue_scripts();

		// woocommerce styles should be present in the <head> already
		wp_dequeue_style('woocommerce-layout');
		wp_dequeue_style('woocommerce-smallscreen');
		wp_dequeue_style('woocommerce-general');

		the_widget( $options['class_name'], $instance );
		
		$wp_scripts = wp_scripts();
		$wp_styles  = wp_styles();
		wp_print_scripts( $wp_scripts->queue );
		wp_print_styles( $wp_styles->queue );
	}
	else {
		printf( __("<b>Error!</b><br/> No '%s' widget registered in this installation", "component-theme"), $options['class_name'] );
	}
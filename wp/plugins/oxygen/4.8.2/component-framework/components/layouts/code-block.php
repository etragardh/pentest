<?php
	// TODO review the security aspect
	
	if (!is_user_logged_in() || !oxygen_vsb_current_user_can_access()) {
        die();
 	}

 	$nonce  	= $_REQUEST['nonce'];
	$post_id 	= intval( $_REQUEST['post_id'] );
	
	// check nonce
	if ( ! isset( $nonce, $post_id) || ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
	    // This nonce is not valid.
	    die( 'Security check' );
	}
 	
	// get all data JSON
	$data = file_get_contents('php://input');

	// encode and separate tree from options
	$data = json_decode($data, true);

	$code = oxygen_base64_decode_for_json($data['code']);

	// check for code
	if ( $code ) { 

		// make sure errors are shwon
		$error_reporting = error_reporting(E_ERROR | E_WARNING | E_PARSE);
		$display_errors = ini_get('display_errors');
		ini_set('display_errors', 1); 

		eval( ' ?>' . $code . '<?php ' );

		// set errors params back
		ini_set('display_errors', $display_errors); 
		error_reporting($error_reporting);

		// woocommerce singular product specific
		if(class_exists('WooCommerce')) {

			if(is_woocommerce()) {

				if(is_product()) {
					?>
					<script type="text/javascript">
						jQuery('body').addClass('woocommerce');
					</script>
					<style>
						.woocommerce-product-gallery {
							opacity: 1 !important;
						}

						.woocommerce-Tabs-panel:not(:nth-child(2)) {
							display: none;
						}

					</style>
					<?php
				}
			}	
		}
	}
	else {
		_e('No code found', 'component-theme');
	}
?>
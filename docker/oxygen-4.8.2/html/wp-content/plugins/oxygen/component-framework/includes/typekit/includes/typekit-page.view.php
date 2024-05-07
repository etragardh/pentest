<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if(isset($_POST) && sizeof($_POST) > 0) {

	if (defined('CT_FREE')) {
		die( 'Not Allowed');
	}

	if ( !isset($_POST['oxygen_vsb_typekit_token_nonce']) || !wp_verify_nonce( $_POST['oxygen_vsb_typekit_token_nonce'], 'oxygen_vsb_typekit_token' )) {
	    die( 'Security check' );
	}
}

if ( isset( $_POST["ct_typekit_token"] ) ) {
	update_option( "ct_typekit_token", trim( $_POST["ct_typekit_token"] ) );
}

$token = get_option( "ct_typekit_token", "" );

if ( isset( $_POST["ct_typekit_kit_id"] ) && $token ) {
	update_option( "ct_typekit_kit_id", trim( $_POST["ct_typekit_kit_id"] ) );

	$kit_response = wp_remote_get( 'https://typekit.com/api/v1/json/kits/'.trim( $_POST["ct_typekit_kit_id"] ).'/published?token='.$token, 
			array( 	'timeout' => 120, 
					'httpversion' => '1.1' ) );
	$kit_data = json_decode( $kit_response['body'], true );
	$kit_data = $kit_data['kit'];

	$domain = $_SERVER["SERVER_NAME"];

	// add current domain to the kit if needed
	if ( is_array( $kit_data["domains"] ) && !in_array( $domain, $kit_data["domains"] ) ) {
		
		if ( count( $kit_data["domains"] ) < 10 ) {
			$kit_data["domains"][] = $domain;

			$typekit_response = wp_remote_post( 'https://typekit.com/api/v1/json/kits/'.trim( $_POST["ct_typekit_kit_id"] ).'?token='.$token, 
				array( 	'timeout' => 120, 
						'httpversion' => '1.1',
						'body' => array( 
									'domains' => $kit_data["domains"], 
								) 
						) 
				);

			// publish the kit
			$typekit_response = wp_remote_post( 'https://typekit.com/api/v1/json/kits/'.trim( $_POST["ct_typekit_kit_id"] ).'/publish?token='.$token);

			$response_data = json_decode( $typekit_response['body'], true );

			if ( $response_data['published'] ) { ?>

				<div class="notice notice-warning">
					<p><?php printf( __("We have added <b>%s</b> to the kit's list of allowed domains. It may take up to 5 minutes for Typekit's servers to update and the fonts to be available for use with Oxygen.", "component-theme"), $domain ); ?></p>
				</div>

			<?php }
		}
		else { ?>

			<div class="notice notice-warning">
				<p><?php printf( __("<b>%s</b> cannot be added to the list of allowed domains due to Typekit's 10 domain per kit limit. Please login to your Typekit account, remove a domain from the kit, and manually add <b>%s</b> to the kit.", "component-theme"), $domain ); ?></p>
			</div>

		<?php }
	}
}

// Get user kits
if ( $token ) {
	$response = wp_remote_get( 'https://typekit.com/api/v1/json/kits?token='.$token, 
		array( 	'timeout' => 120, 
				'httpversion' => '1.1' ) );

	$response = json_decode( $response["body"], true );
}

//echo "<pre>";
//var_dump( $response );
//echo "</pre>";

?>

<div class="oxygen-vsb-settings-container">

	<h2><?php _e("Adobe Typekit Options", "component-theme"); ?></h2>
	<?php do_action('oxygen_vsb_before_settings_page');?>

	<div class="oxygen-vsb-settings-info-div">
		<a class="oxygen-vsb-settings-info-button" href="https://oxygenbuilder.com/documentation/other/using-adobe-fonts/" target="_blank">
			<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="info-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
			class="svg-inline--fa fa-info-circle fa-w-16 fa-2x" width="20px">
				<path fill="currentColor" d="M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 110c23.196 0 42 18.804 42 42s-18.804 42-42 42-42-18.804-42-42 18.804-42 42-42zm56 254c0 6.627-5.373 12-12 12h-88c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h12v-64h-12c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h64c6.627 0 12 5.373 12 12v100h12c6.627 0 12 5.373 12 12v24z" class=""></path>
			</svg>&nbsp;
			<?php _e("Instructions on setting up Typekit with Oxygen", "component-theme"); ?>
		</a>
	</div>

	<?php if ( isset( $response["errors"] ) && $response["errors"] ) : ?>
		
		<div id="message" class="error notice below-h2">
		<?php foreach ( $response["errors"] as $error ) : ?>
			<p><?php echo $error; ?></p>
		<?php endforeach; ?>
		</div>
	
	<?php endif; ?>

	<form action="" method="post">
		<?php wp_nonce_field( 'oxygen_vsb_typekit_token', 'oxygen_vsb_typekit_token_nonce' ); ?>
		<p>
			<?php _e( "Typekit API Token", "component-theme" ); ?> <input type="text" size="45" name="ct_typekit_token" value="<?php echo $token; ?>"> 
		</p>

		<div>
			<?php if ( isset( $response["kits"] ) && $response["kits"] ) : 

				$kit_id = get_option( "ct_typekit_kit_id", "" ); 
				
				?>
				
				<h3><?php _e("Kit to use with Oxygen", "component-theme"); ?></h3>

				<select name="ct_typekit_kit_id">
					<option value=""></option>
					<?php foreach ( $response["kits"] as $kit ) : 

						$kit_response = wp_remote_get( 'https://typekit.com/api/v1/json/kits/'.$kit["id"].'?token='.$token, 
										array( 	'timeout' => 120, 
												'httpversion' => '1.1' ) );

						$kit_data = json_decode( $kit_response['body'], true );
						$kit_data = $kit_data['kit'];
						?>
						<option value="<?php echo $kit["id"]; ?>" <?php selected( $kit_id, $kit["id"] );?>>
							<?php echo $kit_data["name"] . " (" . implode(", ", $kit_data["domains"]) . ")"; ?>
						</option>
					<?php endforeach; ?>
				</select>
			<?php endif; ?>
		</div>

		<p class="submit">
			<input type="submit" class="button button-primary" value="<?php _e("Add Typekit API Token", "component-theme"); ?>" name="submit">
		</p>
	
	</form>
</div>
<?php

if( !class_exists( 'EDD_SL_Plugin_Updater_Oxygen' ) ) {
	include( dirname( __FILE__ ) . '/EDD_SL_Plugin_Updater_Oxygen.php' );
}
 
Class OxygenMainPluginUpdater {

	public $oxygen_url 	= "https://oxygenbuilder.com";
	public $edd_updater;
	
	/**
	 * Add the actions in the constructor
	 * 
	 * @since 1.0
	 */

	function __construct( $args ) {

		$this->prefix 		= $args["prefix"];
		$this->plugin_name 	= $args["plugin_name"]; // should be exact as EDD item name
		$this->priority 	= $args["priority"];
		$this->license_text = (isset($args["license_text"])) ? $args["license_text"] : __('Enter your license key to get updates', 'component-theme');

		add_action( 'admin_init', array( $this, 'init'), 0 );
		add_action( 'admin_init', array( $this, 'activate_license' ) );
		add_action( 'oxygen_license_admin_screen', array( $this, 'license_screen' ), $this->priority );
	}

	
	/**
	 * Initialize EDD_SL_Plugin_Updater_Oxygen class
	 * 
	 * @since 1.0
	 */

	function init() {

		// retrieve our license key from the DB
		$license_key = trim( get_option( $this->prefix . 'license_key' ) );

		// setup the updater
		$this->edd_updater = new EDD_SL_Plugin_Updater_Oxygen( 
			$this->oxygen_url, 
			plugin_dir_path( dirname( dirname( dirname( __FILE__ ) ) ) ) . "functions.php", // main plugin file, specify for each add-on
			array( 
				'version' 	=> CT_VERSION, 				// current version number
				'license' 	=> $license_key, 		// license key (used get_option above to retrieve from DB)
				'item_name' => $this->plugin_name, 	// name of this plugin
				'author' 	=> 'Soflyy'  			// author of this plugin
			)
		);

		// unset transient so it can be regenerated
		if (isset($_REQUEST['force-check'])) {
			delete_transient('edd_get_version_'.$this->plugin_name.$license_key);
		}
	}


	/**
	 * License screen HTML output
	 * 
	 * @since 1.0
	 */

	function license_screen() {

		$license 	= get_option( $this->prefix . 'license_key' );
		$status 	= get_option( $this->prefix . 'license_status' );

		if ($license!="") {
			$type = "password";
		}
		else {
			$type = "text";
		}

		?>
		<div class="oxygen-license-wrap <?php echo $this->prefix . 'license-wrap'; ?>">
			<h3><?php echo $this->plugin_name; ?></h3>
			<form method="post" action="">
			
				<?php wp_nonce_field( $this->prefix . 'submit_license', $this->prefix . 'license_nonce_field' ); ?>
				
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<td>
								<input id="<?php echo $this->prefix; ?>license_key" name="<?php echo $this->prefix; ?>license_key" type="<?php echo $type; ?>" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
								<label for="<?php echo $this->prefix; ?>license_key"><?php echo $status; ?></label>
								<p class="description"><?php echo $this->license_text ?></p>
							</td>
						</tr>
					</tbody>
				</table>	
				<?php submit_button( __("Save Oxygen License","oxygen"), "primary", $this->prefix."submit_license" ); ?>
			
			</form>
		</div>
		<?php
	}


	/**
	 * Send license key to OxygenApp.com EDD to activate license
	 * 
	 * @since 1.0
	 */

	function activate_license() {

		// listen for our activate button to be clicked
		if( isset( $_POST[$this->prefix."submit_license"] ) ) {

			$user = wp_get_current_user();
			delete_transient('oxygen-token-check-user-' . $user->ID);

			// run a quick security check 
		 	if( ! wp_verify_nonce( $_POST[$this->prefix . 'license_nonce_field'], $this->prefix . 'submit_license' ) ) 	
				return;

			update_option( $this->prefix . 'license_key', trim( $_POST[$this->prefix . 'license_key'] ) );

			// retrieve the license from the database
			$license = trim( get_option( $this->prefix . 'license_key' ) );

			// data to send in our API request
			$api_params = array( 
				'edd_action'=> 'activate_license', 
				'license' 	=> $license, 
				'item_name' => urlencode( $this->plugin_name ), // the name of our product in EDD
				'url'       => home_url()
			);
			
			// Call the custom API.
			$response = wp_remote_get( add_query_arg( $api_params, $this->oxygen_url ), array( 'timeout' => 15, 'sslverify' => false ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) )
				return false;

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// make old theme license to shown valid
			if (isset($license_data->error) && $license_data->error == "item_name_mismatch") {
				// check hash
				$site_hash = $license_data->site_hash;
				$site_url  = trailingslashit($this->clean_site_url(home_url()));
				if ( $license_data->sites->{$site_hash} == $site_url ) {
					$license_data->license = "valid";
				}
			}

			// if valid license update the hash
			if ( isset( $license_data->site_hash ) && $license_data->license == "valid" ) {
				update_option( $this->prefix . 'license_site_hash', $license_data->site_hash );
			}

			update_option( $this->prefix . 'license_status', $license_data->license );

			oxygen_vsb_check_is_agency_bundle();
		}
	}


	/**
	 * Send license key to OxygenApp.com EDD to deactivate license
	 * Not used anywhere though
	 * 
	 * @since 1.0
	 */

	function deactivate_license() {

		// listen for our activate button to be clicked
		if( isset( $_POST[$this->prefix.'license_deactivate'] ) ) {

			// run a quick security check 
		 	if( ! wp_verify_nonce( $_POST[$this->prefix . 'license_nonce_field'], $this->prefix . 'submit_license' ) )
				return;

			// retrieve the license from the database
			$license = trim( get_option( $this->prefix . 'license_key' ) );

			// data to send in our API request
			$api_params = array( 
				'edd_action'=> 'deactivate_license', 
				'license' 	=> $license, 
				'item_name' => urlencode( $this->plugin_name ), // the name of our product in EDD
				'url'       => home_url()
			);
			
			// Call the custom API.
			$response = wp_remote_get( add_query_arg( $api_params, $this->oxygen_url ), array( 'timeout' => 15, 'sslverify' => false ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) )
				return false;

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			
			// $license_data->license will be either "deactivated" or "failed"
			if( $license_data->license == 'deactivated' )
				delete_option( $this->prefix . 'license_status' );

		}
	}

	/**
	 * Taken from https://github.com/wp-premium/edd-software-licensing/blob/master/edd-software-licenses.php
     *
	 * Lowercases site URL's, strips HTTP protocols and strips www subdomains.
	 *
	 * @param string $url
	 * @return string
	 */
	
	function clean_site_url( $url ) {
		$url = strtolower( $url );
		
		// strip www subdomain
		$url = str_replace( array( '://www.', ':/www.' ), '://', $url );
	
		// strip protocol
		$url = str_replace( array( 'http://', 'https://', 'http:/', 'https:/' ), '', $url );
	
		$port = parse_url( $url, PHP_URL_PORT );
		if( $port ) {
			// strip port number
			$url = str_replace( ':' . $port, '', $url );
		}
		
		return sanitize_text_field( $url );
	}

}

// instantinate the classes
global $oxygen_main_updater;
$oxygen_main_updater = new OxygenMainPluginUpdater( array(
		"prefix" 		=> "oxygen_",
		"plugin_name" 	=> "Oxygen",
		"priority" 		=> 5
) );


add_filter( 'update_plugins_oxygenbuilder.com', function( $update, array $plugin_data, string $plugin_file, $locales ) {
	
	// only check this plugin
    if ( $plugin_file !== 'oxygen/functions.php' ) {
        return $update;
    }

    // already done update check elsewhere
    if ( ! empty( $update ) ) {
        return $update;
    }

	global $oxygen_main_updater;
	if (!$oxygen_main_updater->edd_updater) {
		$oxygen_main_updater->init();
	}
    $version_info = $oxygen_main_updater->edd_updater->api_request( 'plugin_latest_version', array( 'slug' => 'functions' ) );

	// check if this is a license for this exact plugin
	if ( !$version_info || $version_info->name !== $oxygen_main_updater->edd_updater->get_api_data()["item_name"] ) {
		return $update;    
	}

	$is_update_available = false;

	if ( false !== $version_info && is_object( $version_info ) && isset( $version_info->new_version ) ) {
		if( version_compare( CT_VERSION, $version_info->new_version, '<' ) ) {
			$is_update_available = true;
		}
	}
	
    if ( ! $is_update_available ) {
		return false;
    }
	
    return [
        'slug' 		=> $version_info->slug,
        'version' 	=> $version_info->new_version,
        'url' 		=> $version_info->url,
        'package' 	=> $version_info->package,
    ];
}, 10, 4 );
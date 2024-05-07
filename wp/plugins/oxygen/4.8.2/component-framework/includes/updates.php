<?php 

function oxygen_vsb_update_3_6() {

    if ( !get_option("oxygen_vsb_update_3_6") && oxygen_vsb_is_touched_install() ) {

        // check user license to whether enable Edit Mode option ot not
        oxygen_vsb_check_is_agency_bundle();

        // need to update universal.css to apply new Columns Padding Global Styles
        oxygen_vsb_cache_universal_css();

        // make sure this fires only once
        add_option("oxygen_vsb_update_3_6", true);

    };
}
add_action("admin_init", "oxygen_vsb_update_3_6");


function oxygen_vsb_update_3_7() {

    if ( !get_option("oxygen_vsb_update_3_7") ) {
    
        if ( oxygen_vsb_is_touched_install() ) {
            add_option("oxygen_options_autoload", "yes");
        }
        else {
            add_option("oxygen_options_autoload", "no");
        }

        add_option("oxygen_vsb_update_3_7", true);
    };
}
add_action("admin_init", "oxygen_vsb_update_3_7", 1);


function oxygen_vsb_update_4_0() {

    if ( !get_option("oxygen_vsb_update_4_0_shortcodes_signed") && oxygen_vsb_is_touched_install() ) {
        add_action( 'admin_notices', 'shortcodes_to_json_notice' );
    };
}
add_action("admin_init", "oxygen_vsb_update_4_0", 1);

function shortcodes_to_json_notice() { 

	$page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : false;

	if ($page == 'oxygen_vsb_sign_shortcodes') {
		return;
	}

	?>
    <div class="notice notice-warning">
        <p><?php _e( 'Oxygen is now using JSON instead of WordPress shortcodes to store your designs.', 'oxygen' );
         		echo ' <a href="'.get_admin_url().'admin.php?page=oxygen_vsb_sign_shortcodes">';
         		_e( 'Please sign shortcodes to convert them to new format right away.', 'oxygen' ); 
         		echo "</a>";
         	?>
        </p>
    </div>
<?php }

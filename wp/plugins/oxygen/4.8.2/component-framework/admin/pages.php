<?php
/**
 * Add Dashboard pages/subpages for different settings
 *
 */


/**
 * Main Page
 * 
 * @since 0.2.0
 */

add_action('admin_menu', 'ct_dashboard_main_page');

function ct_dashboard_main_page(){

	if(!oxygen_vsb_current_user_can_full_access()) {
		return;
	}

	$homePageView = add_menu_page( 	'Oxygen', // page <title>
					'Oxygen', // menu item name
					'read', // capability
					'ct_dashboard_page', // get param
					'ct_oxygen_home_page_view',
					'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48c3ZnIHdpZHRoPSIzODFweCIgaGVpZ2h0PSIzODVweCIgdmlld0JveD0iMCAwIDM4MSAzODUiIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+ICAgICAgICA8dGl0bGU+VW50aXRsZWQgMzwvdGl0bGU+ICAgIDxkZXNjPkNyZWF0ZWQgd2l0aCBTa2V0Y2guPC9kZXNjPiAgICA8ZGVmcz4gICAgICAgIDxwb2x5Z29uIGlkPSJwYXRoLTEiIHBvaW50cz0iMC4wNiAzODQuOTQgMzgwLjgwNSAzODQuOTQgMzgwLjgwNSAwLjYyOCAwLjA2IDAuNjI4Ij48L3BvbHlnb24+ICAgIDwvZGVmcz4gICAgPGcgaWQ9IlBhZ2UtMSIgc3Ryb2tlPSJub25lIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+ICAgICAgICA8ZyBpZD0iT3h5Z2VuLUljb24tQ01ZSyI+ICAgICAgICAgICAgPG1hc2sgaWQ9Im1hc2stMiIgZmlsbD0id2hpdGUiPiAgICAgICAgICAgICAgICA8dXNlIHhsaW5rOmhyZWY9IiNwYXRoLTEiPjwvdXNlPiAgICAgICAgICAgIDwvbWFzaz4gICAgICAgICAgICA8ZyBpZD0iQ2xpcC0yIj48L2c+ICAgICAgICAgICAgPHBhdGggZD0iTTI5Ny41MDgsMzQ5Ljc0OCBDMjc1LjQ0MywzNDkuNzQ4IDI1Ny41NTYsMzMxLjg2IDI1Ny41NTYsMzA5Ljc5NiBDMjU3LjU1NiwyODcuNzMxIDI3NS40NDMsMjY5Ljg0NCAyOTcuNTA4LDI2OS44NDQgQzMxOS41NzMsMjY5Ljg0NCAzMzcuNDYsMjg3LjczMSAzMzcuNDYsMzA5Ljc5NiBDMzM3LjQ2LDMzMS44NiAzMTkuNTczLDM0OS43NDggMjk3LjUwOCwzNDkuNzQ4IEwyOTcuNTA4LDM0OS43NDggWiBNMjIyLjMwNCwzMDkuNzk2IEMyMjIuMzA0LDMxMi4wMzkgMjIyLjQ0NywzMTQuMjQ3IDIyMi42MzksMzE2LjQ0MSBDMjEyLjMzLDMxOS4wOTIgMjAxLjUyOCwzMjAuNTA1IDE5MC40MDMsMzIwLjUwNSBDMTE5LjAxLDMyMC41MDUgNjAuOTI5LDI2Mi40MjMgNjAuOTI5LDE5MS4wMzEgQzYwLjkyOSwxMTkuNjM4IDExOS4wMSw2MS41NTcgMTkwLjQwMyw2MS41NTcgQzI2MS43OTQsNjEuNTU3IDMxOS44NzcsMTE5LjYzOCAzMTkuODc3LDE5MS4wMzEgQzMxOS44NzcsMjA2LjgzMyAzMTcuMDIsMjIxLjk3OCAzMTEuODE1LDIzNS45OSBDMzA3LjE3OSwyMzUuMDk3IDMwMi40MDQsMjM0LjU5MiAyOTcuNTA4LDIzNC41OTIgQzI1NS45NzQsMjM0LjU5MiAyMjIuMzA0LDI2OC4yNjIgMjIyLjMwNCwzMDkuNzk2IEwyMjIuMzA0LDMwOS43OTYgWiBNMzgwLjgwNSwxOTEuMDMxIEMzODAuODA1LDg2LjA0MiAyOTUuMzkyLDAuNjI4IDE5MC40MDMsMC42MjggQzg1LjQxNCwwLjYyOCAwLDg2LjA0MiAwLDE5MS4wMzEgQzAsMjk2LjAyIDg1LjQxNCwzODEuNDMzIDE5MC40MDMsMzgxLjQzMyBDMjEyLjQ5OCwzODEuNDMzIDIzMy43MDgsMzc3LjYwOSAyNTMuNDU2LDM3MC42NTcgQzI2NS44NDUsMzc5LjY0MSAyODEuMDM0LDM4NSAyOTcuNTA4LDM4NSBDMzM5LjA0MiwzODUgMzcyLjcxMiwzNTEuMzMgMzcyLjcxMiwzMDkuNzk2IEMzNzIuNzEyLDI5Ni4wOTIgMzY4Ljk4OCwyODMuMjgzIDM2Mi41ODQsMjcyLjIxOSBDMzc0LjI1MSwyNDcuNTc1IDM4MC44MDUsMjIwLjA1OCAzODAuODA1LDE5MS4wMzEgTDM4MC44MDUsMTkxLjAzMSBaIiBpZD0iRmlsbC0xIiBmaWxsPSIjMDBCM0MxIiBtYXNrPSJ1cmwoI21hc2stMikiPjwvcGF0aD4gICAgICAgIDwvZz4gICAgPC9nPjwvc3ZnPg==' ); 

	add_action( 'load-' . $homePageView, 'ct_oxygen_admin_home_page_css' );

	add_submenu_page( 	'ct_dashboard_page', 
						'Home', 
						'Home', 
						'read', 
						'ct_dashboard_page');
}

function ct_oxygen_admin_home_page_css() {
	add_action( 'admin_enqueue_scripts', 'ct_oxygen_enqueue_admin_home_page_css' );
}

function ct_oxygen_enqueue_admin_home_page_css() {
	wp_enqueue_style("oxy-admin-screen-home", CT_FW_URI."/admin/oxy-admin-screen-home.css");
}

function ct_oxygen_home_page_view() {
	if ( !oxygen_vsb_current_user_can_access() )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    include(plugin_dir_path(__FILE__)."oxy-admin-screen-home.php");
    
}

add_action('admin_menu', 'ct_install_wiz_page');

function ct_install_wiz_page() {

	if(!oxygen_vsb_current_user_can_access()) {
		return;
	}

	$ctInstallWizCallback = add_submenu_page( 	'', //ct_dashboard_page to show it in the sub-menu
						'Install Wizard', 
						'Install Wizard', 
						'read', 
						'ct_install_wiz', 
						'ct_install_wiz_callback' );

	add_action( 'load-' . $ctInstallWizCallback, 'ct_oxygen_install_wiz_page_css' );


}

function ct_oxygen_install_wiz_page_css() {
	add_action( 'admin_enqueue_scripts', 'ct_oxygen_enqueue_install_wiz_page_css' );
}

function ct_oxygen_enqueue_install_wiz_page_css() {
	wp_enqueue_style("oxy-admin-screen-install-wiz", CT_FW_URI."/admin/oxy-admin-screen-install-wiz.css");
}

function ct_install_wiz_callback() {
	if ( !oxygen_vsb_current_user_can_access() )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    include(plugin_dir_path(__FILE__)."oxy-admin-screen-install-wiz.php");
    
}

add_action('admin_menu', 'ct_templates_page');

function ct_templates_page() {

	if(!oxygen_vsb_current_user_can_access()) {
		return;
	}

	if(oxygen_vsb_get_user_edit_mode() == "edit_only") {
		
		add_menu_page( 	'Oxygen', // page <title>
					'Templates', // menu item name
					'read', // capability
					'edit.php?post_type=ct_template', // get param
					'',
					'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48c3ZnIHdpZHRoPSIzODFweCIgaGVpZ2h0PSIzODVweCIgdmlld0JveD0iMCAwIDM4MSAzODUiIHZlcnNpb249IjEuMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+ICAgICAgICA8dGl0bGU+VW50aXRsZWQgMzwvdGl0bGU+ICAgIDxkZXNjPkNyZWF0ZWQgd2l0aCBTa2V0Y2guPC9kZXNjPiAgICA8ZGVmcz4gICAgICAgIDxwb2x5Z29uIGlkPSJwYXRoLTEiIHBvaW50cz0iMC4wNiAzODQuOTQgMzgwLjgwNSAzODQuOTQgMzgwLjgwNSAwLjYyOCAwLjA2IDAuNjI4Ij48L3BvbHlnb24+ICAgIDwvZGVmcz4gICAgPGcgaWQ9IlBhZ2UtMSIgc3Ryb2tlPSJub25lIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+ICAgICAgICA8ZyBpZD0iT3h5Z2VuLUljb24tQ01ZSyI+ICAgICAgICAgICAgPG1hc2sgaWQ9Im1hc2stMiIgZmlsbD0id2hpdGUiPiAgICAgICAgICAgICAgICA8dXNlIHhsaW5rOmhyZWY9IiNwYXRoLTEiPjwvdXNlPiAgICAgICAgICAgIDwvbWFzaz4gICAgICAgICAgICA8ZyBpZD0iQ2xpcC0yIj48L2c+ICAgICAgICAgICAgPHBhdGggZD0iTTI5Ny41MDgsMzQ5Ljc0OCBDMjc1LjQ0MywzNDkuNzQ4IDI1Ny41NTYsMzMxLjg2IDI1Ny41NTYsMzA5Ljc5NiBDMjU3LjU1NiwyODcuNzMxIDI3NS40NDMsMjY5Ljg0NCAyOTcuNTA4LDI2OS44NDQgQzMxOS41NzMsMjY5Ljg0NCAzMzcuNDYsMjg3LjczMSAzMzcuNDYsMzA5Ljc5NiBDMzM3LjQ2LDMzMS44NiAzMTkuNTczLDM0OS43NDggMjk3LjUwOCwzNDkuNzQ4IEwyOTcuNTA4LDM0OS43NDggWiBNMjIyLjMwNCwzMDkuNzk2IEMyMjIuMzA0LDMxMi4wMzkgMjIyLjQ0NywzMTQuMjQ3IDIyMi42MzksMzE2LjQ0MSBDMjEyLjMzLDMxOS4wOTIgMjAxLjUyOCwzMjAuNTA1IDE5MC40MDMsMzIwLjUwNSBDMTE5LjAxLDMyMC41MDUgNjAuOTI5LDI2Mi40MjMgNjAuOTI5LDE5MS4wMzEgQzYwLjkyOSwxMTkuNjM4IDExOS4wMSw2MS41NTcgMTkwLjQwMyw2MS41NTcgQzI2MS43OTQsNjEuNTU3IDMxOS44NzcsMTE5LjYzOCAzMTkuODc3LDE5MS4wMzEgQzMxOS44NzcsMjA2LjgzMyAzMTcuMDIsMjIxLjk3OCAzMTEuODE1LDIzNS45OSBDMzA3LjE3OSwyMzUuMDk3IDMwMi40MDQsMjM0LjU5MiAyOTcuNTA4LDIzNC41OTIgQzI1NS45NzQsMjM0LjU5MiAyMjIuMzA0LDI2OC4yNjIgMjIyLjMwNCwzMDkuNzk2IEwyMjIuMzA0LDMwOS43OTYgWiBNMzgwLjgwNSwxOTEuMDMxIEMzODAuODA1LDg2LjA0MiAyOTUuMzkyLDAuNjI4IDE5MC40MDMsMC42MjggQzg1LjQxNCwwLjYyOCAwLDg2LjA0MiAwLDE5MS4wMzEgQzAsMjk2LjAyIDg1LjQxNCwzODEuNDMzIDE5MC40MDMsMzgxLjQzMyBDMjEyLjQ5OCwzODEuNDMzIDIzMy43MDgsMzc3LjYwOSAyNTMuNDU2LDM3MC42NTcgQzI2NS44NDUsMzc5LjY0MSAyODEuMDM0LDM4NSAyOTcuNTA4LDM4NSBDMzM5LjA0MiwzODUgMzcyLjcxMiwzNTEuMzMgMzcyLjcxMiwzMDkuNzk2IEMzNzIuNzEyLDI5Ni4wOTIgMzY4Ljk4OCwyODMuMjgzIDM2Mi41ODQsMjcyLjIxOSBDMzc0LjI1MSwyNDcuNTc1IDM4MC44MDUsMjIwLjA1OCAzODAuODA1LDE5MS4wMzEgTDM4MC44MDUsMTkxLjAzMSBaIiBpZD0iRmlsbC0xIiBmaWxsPSIjMDBCM0MxIiBtYXNrPSJ1cmwoI21hc2stMikiPjwvcGF0aD4gICAgICAgIDwvZz4gICAgPC9nPjwvc3ZnPg==' ); 
		
		return;
	}

	add_submenu_page( 	'ct_dashboard_page', 
						'Templates', 
						'Templates', 
						'read', 
						'edit.php?post_type=ct_template');

}

add_action( 'admin_enqueue_scripts', 'ct_templates_admin_scripts' );

function ct_templates_admin_scripts($hook) {

	if (!oxygen_vsb_current_user_can_full_access()) {
		return;
	}

	global $post;

	if(!is_object($post) || !property_exists($post, 'post_type')) {
		return;
	}

    if ( $hook == 'post.php' || $hook == 'edit.php' ) {
        if ( 'ct_template' === $post->post_type ) {
        	wp_register_script('ct_template_edit_add', CT_FW_URI.'/admin/ct_template_edit_add.js', array(), CT_VERSION);
        	wp_localize_script(
				'ct_template_edit_add', 
				'ct_template_add_reusable_link', 
				array ( "value" => add_query_arg( array('post_type'=>'ct_template', 'is_reusable'=>'true'), admin_url('post-new.php')))
			);
            wp_enqueue_script(  'ct_template_edit_add' );
        }
    }
	
}


/**
 * Export/Import
 * 
 * @since 0.2.1
 */

add_action('admin_menu', 'ct_export_import_page', 11);

function ct_export_import_page() {

	if(!oxygen_vsb_current_user_can_full_access()) {
		return;
	}
	
	add_submenu_page( 	'ct_dashboard_page', 
						'Export & Import', 
						'Export & Import', 
						'read', 
						'ct_export_import', 
						'ct_export_import_callback' );
}

add_action('admin_menu', 'ct_admin_settings', 12);


function oxygen_vsb_process_signature_validation_toggle($val) {
	if(get_option('oxygen_vsb_enable_signature_validation') !== 'true' && $val === 'true') {
		set_transient('oxygen-vsb-enabled-shortcode-signing', true);
	}

	return $val;
}

/**
 * CSS Management Page
 * 
 * @since 4.x
 */

/* Save handler */
add_action('wp_ajax_oxy_save_css_from_admin', 'ct_css_management_save_handler');

function ct_css_management_save_handler() {
	if( !$_POST['stylesheets'] ) return;

	update_option( "ct_style_sheets", ct_filter_content( 'style_sheets_admin', json_decode( stripslashes($_POST["stylesheets"]), true ) ) );

	update_option( "ct_hot_reload_check", "reload" );

	oxygen_vsb_cache_universal_css();

	wp_die();
}

/* Hot reload handler */
// 1. We need to add an option to enable or disable hot reload on the front-end
// 2. If hot reload is enabled, add a script to the head that uses an interval to make an ajax
//    request (action: 'oxy_css_hot_reload_check'). If the request returns 'reload', the script
//    should reload the page. 
// This way, the hot reload only runs when new changes have been saved, as we're setting the 'ct_hot_reload'
// flag when stylesheets are saved in the admin stylesheet editor, and resetting the flag after the page has been
// reloaded with the latest changes.

add_action('wp_ajax_oxy_css_hot_reload_update_transient', 'oxy_css_hot_reload_update_transient');

function oxy_css_hot_reload_update_transient() {
	set_transient("ct_hot_reload_enabled", "true", 10 /*seconds*/);
	echo get_transient("ct_hot_reload_enabled");
	wp_die();
}

add_action('wp_ajax_oxy_css_hot_reload_check', 'ct_css_management_hot_reload_check_handler');

function ct_css_management_hot_reload_check_handler() {
	$shouldReload = get_option( "ct_hot_reload_check", "noreload" );

	if( $shouldReload == "reload" ) {
		echo "reload";
		update_option( "ct_hot_reload_check", "noreload" );
	} else {
		echo "noreload";
	}

	wp_die();
}

add_action("wp_ajax_oxy_css_toggle_hot_reload", "ct_css_toggle_hot_reload");

function ct_css_toggle_hot_reload() {
	$hotReloadEnabled = get_transient("ct_hot_reload_enabled");

	if( $hotReloadEnabled == "true" ) {
		delete_transient("ct_hot_reload_enabled");
		echo false;
	} else {
		set_transient("ct_hot_reload_enabled", "true", 10 /*seconds*/);
		echo true;
	}

	wp_die();
}

add_action("wp_ajax_oxy_css_toggle_stylesheet_edit_lock", "oxy_css_toggle_stylesheet_edit_lock");

function oxy_css_toggle_stylesheet_edit_lock() {
	$lock_stylesheets = get_option("ct_lock_stylesheets_in_builder", false);

	if( $lock_stylesheets ) {
		update_option("ct_lock_stylesheets_in_builder", false);
		echo false;
	} else {
		update_option("ct_lock_stylesheets_in_builder", true);
		echo true;
	}

	wp_die();
}

add_action('admin_menu', 'ct_css_management_page', 11);

function ct_css_management_page() {

	if(!oxygen_vsb_current_user_can_full_access()) {
		return;
	}
	
	add_submenu_page( 	'ct_dashboard_page', 
						'Stylesheets', 
						'Stylesheets', 
						'read', 
						'ct_css_management', 
						'ct_css_management_callback' );
}

function ct_css_management_callback() {

	if ( is_oxygen_edit_post_locked() && !get_option("ct_lock_stylesheets_in_builder") ) {

		?>
		<div class="notice notice-warning">
			<p>Oxygen is open in another tab.</p><p>To avoid your changes being overwritten, please close Oxygen or enable "Lock Sheets" in the stylesheet editor settings.</p><p>If "Lock Sheets" is enabled, you will only be able to edit Oxygen stylesheets from this screen.</p>		
		</div>
		<?php
	}

	$stylesheets = get_option( "ct_style_sheets", array() );

	wp_enqueue_script("oxygen-codemirror-6", 			CT_FW_URI . '/vendor/codemirror6/editor.bundle.js', array(), false, false);
	wp_enqueue_script("ct-css-management",				CT_FW_URI . "/admin/ct_css_management.js", array(), false, false);
	
	// load alpinejs with defer attr
	wp_enqueue_script("alpinejs", 						CT_FW_URI . "/vendor/alpinejs/alpinejs.3.10.5.js", array(), false, false);
	add_filter( 'script_loader_tag', function ( $tag, $handle ) {
		if ( 'alpinejs' !== $handle ) return $tag;
		return str_replace( ' src', ' defer="defer" src', $tag );
	}, 10, 2 );

	$codemirror_theme = get_option("oxygen_vsb_codemirror_theme", 'gruvboxDark');
	$old_themes = ['default', 'oneDarkTheme', 'dracula', 'midnight', 'eclipse ']; // space in the 'eclipse ' was a typo, but don't remove it as people has 
	if (in_array($codemirror_theme, $old_themes)) {
		$codemirror_theme = 'gruvboxDark';
	}

	?>
	<script>
		window.globalCodeMirrorTheme = '<?php echo $codemirror_theme; ?>';
		window.stylesheets = <?php echo json_encode($stylesheets); ?>;
		window.hotReloadStatus = "<?php echo get_transient("ct_hot_reload_enabled"); ?>";
		window.oxyNonceCodeMirror = "<?php echo wp_create_nonce( 'oxygen-nonce-set-codemirror-theme' ); ?>";
		// keep transient active
		setInterval( () => {
			if (window.hotReloadStatus) {
				fetch(ajaxurl, {
					method: "POST",
					credentials: "same-origin",
					headers: new Headers({"Content-Type": "application/x-www-form-urlencoded"}),
					body: "action=oxy_css_hot_reload_update_transient"
				})
			}
		}, 5000);
	</script>
	<div class='ovsb-css-mgmt' x-data="ovsbCSS()" x-init="currentTheme='<?php echo get_option("oxygen_vsb_codemirror_theme", 'dracula'); ?>'; setupCodemirror(); setupShortcuts(); lockStylesheetsInBuilder = '<?php echo get_option('ct_lock_stylesheets_in_builder'); ?>'">
		<div 
		class="ovsb-css-mgmt-saving-notice"
		:class="sheetsSavingNotice == 'show' && 'ovsb-css-mgmt-saving-notice-show'">
			<span class="dashicons dashicons-update"></span>
			Saving...
		</div>
		<div 
		class="ovsb-css-mgmt-saved-notice"
		:class="sheetsSavedNotice == 'show' && 'ovsb-css-mgmt-saved-notice-show'">
			<span class="dashicons dashicons-yes"></span>
			Sheets Saved
		</div>
		<div class="ovsb-css-mgmt__left">
			<div class="ovsb-css-mgmt__controlbar">
				<button 
				:class="sheetsSavingNotice == 'show' && 'ovsb-css-mgmt-disabled'"
				title="Save all sheets"
				aria-label="Save all sheets"
				@click="saveSheets()">
					<span class="dashicons dashicons-yes"></span>
				</button>
				<button 
				title="Delete selected sheet"
				aria-label="Delete selected sheet"
				@click="if( !selectedSheet ) return; if( confirm('Delete this sheet (' + getSheetById(selectedSheet).name + ') permanently? This cannot be undone.') ) stylesheets = removeSheetById(selectedSheet)">
					<span class="dashicons dashicons-trash"></span>
				</button>
				<button 
				title="Add new sheet"
				aria-label="Add new sheet"
				@click="let newSheetName = String(prompt('Please enter a name for your new stylesheet.')); if( newSheetName != 'null' ) stylesheets = createNewSheet(newSheetName)">
					<span class="dashicons dashicons-plus"></span>
				</button>
			</div>
			<ul class="ovsb-css-mgmt__list">
			<template x-for="sheet in getSheetsButNotFolders()">

					<li 
						:class="{
							'ovsb-css-mgmt__item--selected' : selectedSheet == sheet.id, 
							'dashicons-before dashicons-media-code' : !sheet.parent, 
							'dashicons-before dashicons-open-folder' : sheet.parent,
							'ovsb-css-mgmt__folder' : sheet.folder
						}"
						class="ovsb-css-mgmt__item"
						@click.stop="selectedSheet = sheet.id; sheet.css ? updateCodemirror(b64_to_utf8(sheet.css)) : updateCodemirror('')">
							<span x-show="sheet.parent" x-text="getFolderName(sheet.parent) + '&nbsp/&nbsp'">--</span>
							<a 
								x-text="sheet.name" 
								href="#"
								class="ovsb-css-mgmt__sheet"
								@click.stop.prevent="selectedSheet = sheet.id; sheet.css ? updateCodemirror(b64_to_utf8(sheet.css)) : updateCodemirror('')"></a>
							<span x-show="sheet.modified">*</span>
					</li>

			</template>
			</ul>
		</div>
		<div class="ovsb-css-mgmt__right"
		:class="fullScreen && 'ovsb-css-mgmt__right--fullscreen'">
			<div class="ovsb-css-mgmt__controlbar">
				<p x-show="selectedSheet" x-text="'Editing ' + getSheetById(selectedSheet)?.name"></p>
				<button 
					class="ovsb-css-mgmt__button"
					title="Toggle full screen editor"
					aria-label="Toggle full screen editor"
					@click="fullScreen = !fullScreen">
						<span x-show="!fullScreen" class="dashicons dashicons-editor-expand"></span>
						<span x-show="fullScreen" class="dashicons dashicons-editor-contract"></span>
				</button>
				<button 
					class="ovsb-css-mgmt__button"
					title="Open editor settings"
					aria-label="Open editor settings"
					@click="settingsPopup = !settingsPopup"
					:aria-expanded="settingsPopup">
						<span class="dashicons dashicons-admin-settings"></span>
				</button>
				<div 
					class="ovsb-css-mgmt__settings-popup" 
					x-show="settingsPopup" 
					@click.outside="settingsPopup = false"
					@keyup.escape="settingsPopup = false">
					<button 
					@click="toggleHotReload()" 
					:class="hotReloadWait == true && 'ovsb-css-mgmt-disabled'"
					class="ovsb-css-mgmt__hotreload"
					:aria-pressed="hotReloadEnabled ? 'true' : 'false'">
						Hot Reload
						<div 
						class="ovsb-css-mgmt__toggle-wrapper"
						:class="hotReloadEnabled && 'ovsb-css-mgmt__toggle-wrapper--on'">
							<div class="ovsb-css-mgmt__toggle-circle"></div>
						</div>
					</button>
					<button 
					@click="toggleStylesheetsEditLock()" 
					:class="lockStylesheetsInBuilderWait == true && 'ovsb-css-mgmt-disabled'"
					class="ovsb-css-mgmt__hotreload"
					:aria-pressed="lockStylesheetsInBuilder ? 'true' : 'false'">
						Lock Sheets
						<div 
						class="ovsb-css-mgmt__toggle-wrapper"
						:class="lockStylesheetsInBuilder && 'ovsb-css-mgmt__toggle-wrapper--on'">
							<div class="ovsb-css-mgmt__toggle-circle"></div>
						</div>
					</button>
				</div>
			</div>
			<div id="ovsb-css-codemirror" style="width: 100%;height: 85vh;"></div>
		</div>
	</div>
	<?php
}

/**
 * Settings page
 *
 * @since 2.0
 */ 

function oxygen_vsb_register_settings() {
   add_option( 'oxygen_vsb_history_limit', false );
   register_setting( 'oxygen_vsb_options_group', 'oxygen_vsb_history_limit', array(
       'type' => "number",
       'sanitize_callback' => function ($limit) {
           if ($limit && $limit < 50) {
               $limit = 50;
           }
           return $limit;
       },
       'default' => false
   ));
   
   add_option( 'oxygen_vsb_preview_dropdown_limit', 100 );
   register_setting( 'oxygen_vsb_options_group', 'oxygen_vsb_preview_dropdown_limit' );

   add_option( 'oxygen_vsb_preview_dropdown_exclude_non_public', "true" );
   register_setting( 'oxygen_vsb_options_group', 'oxygen_vsb_preview_dropdown_exclude_non_public' );
   
   add_option( 'oxygen_vsb_enable_selector_detector', false );
   register_setting( 'oxygen_vsb_options_group', 'oxygen_vsb_enable_selector_detector' );


   add_option( 'oxygen_vsb_enable_default_designsets', 'true' );
   register_setting( 'oxygen_vsb_options_group_library', 'oxygen_vsb_enable_default_designsets' );

   add_option( 'oxygen_vsb_enable_3rdp_designsets', false );
   register_setting( 'oxygen_vsb_options_group_library', 'oxygen_vsb_enable_3rdp_designsets' ); 

   add_option( 'oxygen_vsb_enable_connection', false );
   register_setting( 'oxygen_vsb_options_group_library', 'oxygen_vsb_enable_connection' );    

   add_option( 'oxygen_vsb_google_maps_api_key', "" );
   register_setting( 'oxygen_vsb_options_group', 'oxygen_vsb_google_maps_api_key' );

   // added with "register_activation_hook"
   register_setting( 'oxygen_vsb_options_group_cache', 'oxygen_vsb_universal_css_cache' );

   add_option( 'oxygen_vsb_show_all_acf_fields', 'true' );
   register_setting( 'oxygen_vsb_options_group', 'oxygen_vsb_show_all_acf_fields' );

   add_option( 'oxygen_vsb_enable_google_fonts_cache', "true" );
   register_setting( 'oxygen_vsb_options_group', 'oxygen_vsb_enable_google_fonts_cache' );

   add_option( 'oxygen_vsb_enable_ie_layout_improvements', "true" );
   register_setting( 'oxygen_vsb_options_group', 'oxygen_vsb_enable_ie_layout_improvements' );
   
   add_option( 'oxygen_vsb_load_aos_in_head', false );
   register_setting( 'oxygen_vsb_options_group', 'oxygen_vsb_load_aos_in_head' );

   add_option( 'oxygen_vsb_enable_signature_validation', false );
   register_setting( 'oxygen_vsb_options_group_security', 'oxygen_vsb_enable_signature_validation', 'oxygen_vsb_process_signature_validation_toggle' );

   add_option( 'oxygen_vsb_enable_signature_frontend_errors', 'false' );
   register_setting( 'oxygen_vsb_options_group_security', 'oxygen_vsb_enable_signature_frontend_errors' );

   add_option( 'oxygen_vsb_disable_emojis', "false" );
   register_setting( 'oxygen_vsb_options_group_bloat_eliminator', 'oxygen_vsb_disable_emojis' );

   add_option( 'oxygen_vsb_disable_jquery_migrate', "false" );
   register_setting( 'oxygen_vsb_options_group_bloat_eliminator', 'oxygen_vsb_disable_jquery_migrate' );

   add_option( 'oxygen_vsb_disable_embeds', "false" );
   register_setting( 'oxygen_vsb_options_group_bloat_eliminator', 'oxygen_vsb_disable_embeds' );

   add_option( 'oxygen_vsb_disable_google_fonts', "" );
   register_setting( 'oxygen_vsb_options_group_bloat_eliminator', 'oxygen_vsb_disable_google_fonts' );

   add_option( 'oxygen_vsb_use_css_for_google_fonts', "" );
   register_setting( 'oxygen_vsb_options_group_bloat_eliminator', 'oxygen_vsb_use_css_for_google_fonts' );

   add_option( 'oxygen_vsb_number_of_latest_revisions', "20" );
   register_setting( 'oxygen_vsb_options_revisions', 'oxygen_vsb_number_of_latest_revisions' );

   add_option( 'oxygen_vsb_number_of_daily_revisions', "7" );
   register_setting( 'oxygen_vsb_options_revisions', 'oxygen_vsb_number_of_daily_revisions' );

   add_option( 'oxygen_vsb_block_category_label', "" );
   register_setting( 'oxygen_vsb_options_group_gutenberg', 'oxygen_vsb_block_category_label' );

   add_option( 'oxygen_vsb_full_page_block_category_label', "" );
   register_setting( 'oxygen_vsb_options_group_gutenberg', 'oxygen_vsb_full_page_block_category_label' );

   add_option( 'oxygen_vsb_options_users_access_list', array() );
   add_option( 'oxygen_vsb_options_users_access_enable_elements', array() );
   add_option( 'oxygen_vsb_options_users_access_advanced_tab', array() );
   add_option( 'oxygen_vsb_options_users_access_drag_n_drop', array() );
   add_option( 'oxygen_vsb_options_users_access_enabled_elements', array() );
   add_option( 'oxygen_vsb_options_users_access_disable_classes', array() );
   add_option( 'oxygen_vsb_options_users_access_disable_ids', array() );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_users_access_list' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_users_access_enable_elements' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_users_access_advanced_tab' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_users_access_drag_n_drop' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_users_access_enabled_elements' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_users_access_reusable_parts' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_users_access_design_library' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_users_access_disable_classes' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_users_access_disable_ids' );

   add_option( 'oxygen_vsb_options_role_access_enable_elements', array() );
   add_option( 'oxygen_vsb_options_role_access_advanced_tab', array() );
   add_option( 'oxygen_vsb_options_role_access_drag_n_drop', array() );
   add_option( 'oxygen_vsb_options_role_access_enabled_elements', array() );
   add_option( 'oxygen_vsb_options_role_access_disable_classes', array() );
   add_option( 'oxygen_vsb_options_role_access_disable_ids', array() );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_role_access_enable_elements' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_role_access_advanced_tab' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_role_access_drag_n_drop' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_role_access_enabled_elements' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_role_access_reusable_parts' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_role_access_design_library' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_role_access_disable_classes' );
   register_setting( 'oxygen_vsb_options_group_client_control', 'oxygen_vsb_options_role_access_disable_ids' );

   // Access related settings
   if(!defined('CT_FREE')) {
	    add_filter('editable_roles', 'oxygen_vsb_remove_admin_role');
		$roles = get_editable_roles();
		remove_filter('editable_roles', 'oxygen_vsb_remove_admin_role');
		foreach($roles as $role => $item) {
			add_option( "oxygen_vsb_access_role_$role", false);
	   		register_setting( 'oxygen_vsb_options_group_client_control', "oxygen_vsb_access_role_$role");
		}
	}

	// related to post type settings

	global $ct_ignore_post_types;
	$postTypes = get_post_types();
	
	if(is_array($ct_ignore_post_types) && is_array($postTypes)) {
		$postTypes = array_diff($postTypes, $ct_ignore_post_types);
	}
	
	foreach($postTypes as $key => $item) {
		add_option( "oxygen_vsb_ignore_post_type_$key", false);
   		register_setting( 'oxygen_vsb_options_group_client_control', "oxygen_vsb_ignore_post_type_$key");
	}

}
add_action( 'admin_init', 'oxygen_vsb_register_settings' );

function oxygen_vsb_process_source_site() {
	
	if(!oxygen_vsb_current_user_can_access()) {
		return;
	}

	$oxygen_vsb_source_sites = get_option('oxygen_vsb_source_sites');


	if(	isset($_GET['page']) && $_GET['page'] == 'oxygen_vsb_settings' &&
		isset($_GET['tab']) && $_GET['tab'] == 'library_manager' &&
		isset($_GET['action']) && $_GET['action'] == 'add_source_site') {

		if(!isset($_REQUEST['add_3rdp_designset']) || !wp_verify_nonce($_REQUEST['add_3rdp_designset'])) {
			return;
		}

		$source_key = isset($_REQUEST['oxygen_vsb_source_key'])?$_REQUEST['oxygen_vsb_source_key']:false;
		$source_key = base64_decode($source_key);
		$exploded = explode("\n", $source_key);
		$valid = true;
		$source_site_label = isset($exploded[1])?$exploded[1]:false;
		$source_site_url = isset($exploded[0])?$exploded[0]:false;
		$source_site_access = isset($exploded[2])?$exploded[2]:false;

		$valid = $valid && $source_site_label && $source_site_url;

		if(!$valid) {
			set_transient('oxygen-vsb-admin-error-transient', 'invalid Design set key');
		}

		if($valid && sizeof($oxygen_vsb_source_sites) > 0) {
			// check if source site label or url already exists.
			if(isset($oxygen_vsb_source_sites[sanitize_title($source_site_label)])) {
				// TODO: add some notice here
				$valid = false;
				set_transient('oxygen-vsb-admin-error-transient', 'Design set with the same title already exists');
			}

			// check if sourcesite url already exists
			if(array_search($source_site_url, $oxygen_vsb_source_sites)) {
				// TODO: add some notice here
				$valid = false;
				set_transient('oxygen-vsb-admin-error-transient', 'Design set with the same url already exists');
			}
		}

		if($valid) {

			// attempt to connect to the source site and check if the access is valid

			$url = $source_site_url.'/wp-json/oxygen-vsb-connection/v1/addrequest/';


			$args = array(
			  'headers' => array(
			    'oxygenclientversion' => '3.7rc1',
			    'auth' => md5($source_site_access)
			  ),
			  'timeout' => 15,
			);

			$result = wp_remote_request($url, $args);

			$status = wp_remote_retrieve_response_code($result);
			
			if ( is_wp_error( $result ) ) {
			    set_transient('oxygen-vsb-admin-error-transient', $result->get_error_message());
			} 
			elseif($status !== 200) {

				set_transient('oxygen-vsb-admin-error-transient', wp_remote_retrieve_response_message($result));
				
			} 
			else {

				$result = json_decode($result['body'], true);
			

				if(is_array($result) && isset($result['access']) && intval($result['access']) === 1) {
					$oxygen_vsb_source_sites[sanitize_title($source_site_label)] = array('label' => sanitize_text_field($source_site_label), 'url' => sanitize_url($source_site_url), 'accesskey' => ($source_site_access === false ? '' : sanitize_text_field($source_site_access)));
					
					update_option('oxygen_vsb_source_sites', $oxygen_vsb_source_sites);

				}
				else {
					// put some notice;
					set_transient('oxygen-vsb-admin-error-transient', 'Access to the design set is denied');
				}
			}
		}

		wp_redirect(add_query_arg(array('page' => 'oxygen_vsb_settings', 'tab' => 'library_manager'), get_admin_url().'admin.php'));
		exit();
	}

	if(	isset($_GET['page']) && $_GET['page'] == 'oxygen_vsb_settings' &&
		isset($_GET['tab']) && $_GET['tab'] == 'library_manager' &&
		isset($_GET['delete']) && isset($oxygen_vsb_source_sites[$_GET['delete']])) {

		if(isset($_GET['delete_3rdp_designset']) && wp_verify_nonce($_GET['delete_3rdp_designset'])) {

			unset($oxygen_vsb_source_sites[$_GET['delete']]);

			update_option('oxygen_vsb_source_sites', $oxygen_vsb_source_sites);

			wp_redirect(add_query_arg(array('page' => 'oxygen_vsb_settings', 'tab' => 'library_manager'), get_admin_url().'admin.php'));
			exit();
		}
	}
}


add_action( 'admin_init', 'oxygen_vsb_process_source_site');

function oxygen_vsb_remove_admin_role($all_roles) {
	
	if(isset($all_roles['administrator'])) {
		unset($all_roles['administrator']);
	}

	return $all_roles;
}

function oxygen_vsb_options_page() {

	$tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : false;
?>
<div class="wrap">
	<h2 class="oxygen-vsb-settings-title">Oxygen Settings</h2>
	<h2 class="nav-tab-wrapper">
	    <a href="?page=oxygen_vsb_settings&tab=general" class="nav-tab<?php echo ($tab === false || $tab == 'general') ? ' nav-tab-active':'';?>">General</a>
	    <a href="?page=oxygen_vsb_settings&tab=client_control" class="nav-tab<?php echo $tab == 'client_control'?' nav-tab-active':'';?>">Client Control</a>
	    <a href="?page=oxygen_vsb_settings&tab=security_manager" class="nav-tab<?php echo $tab == 'security_manager'?' nav-tab-active':'';?>">Security</a>
	    <a href="?page=oxygen_vsb_settings&tab=svg_manager" class="nav-tab<?php echo $tab == 'svg_manager'?' nav-tab-active':'';?>">SVG Sets</a>
	    <a href="?page=oxygen_vsb_settings&tab=typekit_manager" class="nav-tab<?php echo $tab == 'typekit_manager'?' nav-tab-active':'';?>">Typekit</a>
	   	<?php
	    	if(!defined('CT_FREE')) {
	    ?>
	    <a href="?page=oxygen_vsb_settings&tab=license_manager" class="nav-tab<?php echo $tab == 'license_manager'?' nav-tab-active':'';?>">License</a>
	    <?php
	    	}
	    ?>
	    <a href="?page=oxygen_vsb_settings&tab=cache" class="nav-tab<?php echo $tab == 'cache'?' nav-tab-active':'';?>">CSS Cache</a>
        <a href="?page=oxygen_vsb_settings&tab=bloat" class="nav-tab<?php echo $tab == 'bloat'?' nav-tab-active':'';?>">Bloat Eliminator</a>

        <a href="?page=oxygen_vsb_settings&tab=library_manager" class="nav-tab<?php echo $tab == 'library_manager'?' nav-tab-active':'';?>">Library</a>
        <a href="?page=oxygen_vsb_settings&tab=revisions" class="nav-tab<?php echo $tab == 'revisions'?' nav-tab-active':'';?>">Revisions</a>

        <?php if(class_exists('Oxygen_Gutenberg')): ?>
            <a href="?page=oxygen_vsb_settings&tab=gutenberg" class="nav-tab<?php echo $tab == 'gutenberg'?' nav-tab-active':'';?>">Gutenberg</a>
        <?php endif;?>
	</h2>
	<?php

		switch($tab) {
			case false:
			case 'general':
				oxygen_vsb_options_general_page();
			break;

			case 'library_manager':
				oxygen_vsb_options_library_manager();
			break;

			case 'client_control':
				oxygen_vsb_options_client_control_page();
			break;

			case 'security_manager':
				oxygen_vsb_options_security_manager_page();
			break;

			case 'svg_manager':
				ct_svg_sets_callback();
			break;

			case 'typekit_manager':
				global $oxygenTypekitInstance;
				$oxygenTypekitInstance->typekit_page_callback();
			break;

			case 'license_manager':
				ct_license_page_callback();
			break;

			case 'cache':
				ct_cache_page_callback();
			break;
			case 'bloat':
				oxygen_vsb_options_bloat_eliminator_page();
			break;
			case 'revisions':
				oxygen_vsb_options_revisions();
			break;
			case 'gutenberg':
				oxygen_vsb_options_gutenberg();
			break;

		}

	?>

	  
</div>

<?php

}

function add_3rdp_designset_callback() {
	?>
	<div class="oxygen-vsb-settings-container">
		<h2>Add Source Sites</h2>
		<form method="post" action="?page=oxygen_vsb_settings&tab=library_manager&action=add_source_site">
			<div>
				<?php wp_nonce_field(-1, 'add_3rdp_designset');?>
				<label for="oxygen_vsb_source_key">Site Key</label>
				<input type="text" value="" name="oxygen_vsb_source_key" id="oxygen_vsb_source_key" />
			</div>
			<?php submit_button('Add Source Site'); ?>
		</form>
	</div>
	<?php
}

function oxygen_vsb_options_library_manager() {
	?>

	<div class="oxygen-vsb-settings-container">

		<h2>Library</h2>
			
		<form method="post" action="options.php">
		<?php settings_fields( 'oxygen_vsb_options_group_library' ); ?>
	    <?php do_settings_sections( 'oxygen_vsb_options_group_library' ); ?>
	      	<div>
	      		<input type="checkbox" id="oxygen_vsb_enable_default_designsets" name="oxygen_vsb_enable_default_designsets" value="true" <?php checked(get_option('oxygen_vsb_enable_default_designsets'), "true"); ?>>
	      		<label for="oxygen_vsb_enable_default_designsets"><?php _e("Enable Default Design Sets","oxygen"); ?></label>
	      	</div>
	      	<?php
	      		$oxygen_vsb_enable_3rdp_designsets = get_option('oxygen_vsb_enable_3rdp_designsets');
	      	?>
	      	<div>
	      		<input type="checkbox" id="oxygen_vsb_enable_3rdp_designsets" name="oxygen_vsb_enable_3rdp_designsets" value="true" <?php checked($oxygen_vsb_enable_3rdp_designsets, "true"); ?>>
	      		<label for="oxygen_vsb_enable_3rdp_designsets"><?php _e("Enable 3rd Party Design Sets","oxygen"); ?></label>
	      		

	  		<?php 
			if($oxygen_vsb_enable_3rdp_designsets == 'true') {
				$oxygen_vsb_source_sites = get_option('oxygen_vsb_source_sites');

		  		?>
			  	<div id="oxygen_vsb_3rdp_designsets_container">
			  		<ul>
						<?php
						if(is_array($oxygen_vsb_source_sites))
							foreach($oxygen_vsb_source_sites as $key=>$item) {
								if(isset($item['system'])) {
									continue;
								}
							?>
								<li><?php echo sanitize_text_field($item['label']);?> <a href="<?php 
									echo wp_nonce_url(
										add_query_arg(
											array(
												'page'	=>	'oxygen_vsb_settings',
												'tab'	=>	'library_manager',
												'delete'=>	sanitize_text_field($key)
											),
											get_admin_url().'admin.php'
										), 
										-1, 
										'delete_3rdp_designset'
									);

									?>">Remove</a>
								</li>
							<?php		
							}
						?>
					</ul>
					<a class="oxygen-vsb-settings-button" href="<?php echo add_query_arg('page', 'add_3rdp_designset', get_admin_url().'admin.php');?>">Add Design Set</a>
			  	</div>
			<?php
			}
			?>
	      	</div>
	      	<div>
	      		<input type="checkbox" id="oxygen_vsb_enable_connection" name="oxygen_vsb_enable_connection" value="true" <?php checked(get_option('oxygen_vsb_enable_connection'), "true"); ?>><label for="oxygen_vsb_enable_connection"> <?php _e("Make this WordPress Install a Design Set","oxygen"); ?></label>
	      		<div id="oxygen_vsb_connection_panel">
	      			<?php do_action('oxygen_vsb_connection_panel'); ?>
	      		</div>
	      	</div>
				

				<?php submit_button('Save Changes'); ?>
		</form>

	</div>

	<?php 
	
}

function oxygen_vsb_options_general_page() {
	?>
	<div class="oxygen-vsb-settings-container">
		<h2>General</h2>
		
	  <form method="post" action="options.php">
	  <?php settings_fields( 'oxygen_vsb_options_group' ); ?>
      <?php do_settings_sections( 'oxygen_vsb_options_group' ); ?>
		  <table>
			  <tr valign="top">
				  <th scope="row"><label for="oxygen_vsb_history_limit">History Limit (min: 50)</label></th>
				  <td><input type="number" id="oxygen_vsb_history_limit" name="oxygen_vsb_history_limit" min="50" value="<?php echo esc_attr(get_option('oxygen_vsb_history_limit')); ?>"></td>
			  </tr>
			  <tr valign="top">
				  <th scope="row"><label for="oxygen_vsb_preview_dropdown_limit">Preview Dropdown Limit</label></th>
				  <td><input type="number" id="oxygen_vsb_preview_dropdown_limit" name="oxygen_vsb_preview_dropdown_limit" value="<?php echo esc_attr(get_option('oxygen_vsb_preview_dropdown_limit')); ?>"></td>
			  </tr>
			  <tr valign="top">
				  <th scope="row"><label for="oxygen_vsb_preview_dropdown_exclude_non_public"><?php _e("Exclude Non-Public Post Types From Preview","oxygen"); ?></label></th>
				  <td><input type="checkbox" id="oxygen_vsb_preview_dropdown_exclude_non_public" name="oxygen_vsb_preview_dropdown_exclude_non_public" value="true" <?php checked(get_option('oxygen_vsb_preview_dropdown_exclude_non_public'), "true"); ?>></td>
			  </tr>
			  <tr valign="top">
				  <th scope="row"><label for="oxygen_vsb_enable_selector_detector"><?php _e("Enable Selector Detector","oxygen"); ?></label></th>
				  <td><input type="checkbox" id="oxygen_vsb_enable_selector_detector" name="oxygen_vsb_enable_selector_detector" value="true" <?php checked(get_option('oxygen_vsb_enable_selector_detector'), "true"); ?>></td>
			  </tr>
			  <tr valign="top">
				  <th scope="row"><label for="oxygen_vsb_google_maps_api_key"><?php _e("Google Maps API key","oxygen"); ?></label></th>
				  <td><input type="text" id="oxygen_vsb_google_maps_api_key" name="oxygen_vsb_google_maps_api_key" value="<?php echo esc_attr(get_option('oxygen_vsb_google_maps_api_key')); ?>"></td>
			  </tr>
              <tr valign="top">
                  <th scope="row"><label for="oxygen_vsb_show_all_acf_fields"><?php _e("Show all ACF fields in the Dynamic Data Dialog","oxygen"); ?></label></th>
                  <td><input type="checkbox" id="oxygen_vsb_show_all_acf_fields" name="oxygen_vsb_show_all_acf_fields" value="true" <?php checked(get_option('oxygen_vsb_show_all_acf_fields'), "true"); ?>></td>
              </tr>
              <tr valign="top">
                  <th scope="row"><label for="oxygen_vsb_enable_google_fonts_cache"><?php _e("Cache list of Google Fonts","oxygen"); ?></label></th>
                  <td><input type="checkbox" id="oxygen_vsb_enable_google_fonts_cache" name="oxygen_vsb_enable_google_fonts_cache" value="true" <?php checked(get_option('oxygen_vsb_enable_google_fonts_cache'), "true"); ?>></td>
              </tr>
               <tr valign="top">
                  <th scope="row"><label for="oxygen_vsb_enable_ie_layout_improvements"><?php _e("Enable Layout Improvements for IE 10+","oxygen"); ?></label></th>
                  <td><input type="checkbox" id="oxygen_vsb_enable_ie_layout_improvements" name="oxygen_vsb_enable_ie_layout_improvements" value="true" <?php checked(get_option('oxygen_vsb_enable_ie_layout_improvements'), "true"); ?>></td>
              </tr>
			  <tr valign="top">
                  <th scope="row"><label for="oxygen_vsb_load_aos_in_head"><?php _e("Load AOS styles in the head of page","oxygen"); ?></label></th>
                  <td><input type="checkbox" id="oxygen_vsb_load_aos_in_head" name="oxygen_vsb_load_aos_in_head" value="true" <?php checked(get_option('oxygen_vsb_load_aos_in_head'), "true"); ?>></td>
              </tr>
		  </table>

		  <?php submit_button(); ?>
	  </form>

	</div>

	<?php
}

function oxygen_vsb_options_gutenberg() {
    ?>
    <h2>Gutenberg</h2>

    <form method="post" action="options.php">
		<?php settings_fields( 'oxygen_vsb_options_group_gutenberg' ); ?>
		<?php do_settings_sections( 'oxygen_vsb_options_group_gutenberg' ); ?>
        <table>
            <tr valign="top">
                <th scope="row"><label for="oxygen_vsb_block_category_label">Label For Block Category in Gutenberg</label></th>
                <td><input type="text" id="oxygen_vsb_block_category_label" name="oxygen_vsb_block_category_label" value="<?php echo esc_attr(get_option('oxygen_vsb_block_category_label')); ?>" placeholder="Oxygen Blocks"></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="oxygen_vsb_full_page_block_category_label">Label For Full Page Block Category in Gutenberg</label></th>
                <td><input type="text" id="oxygen_vsb_full_page_block_category_label" name="oxygen_vsb_full_page_block_category_label" value="<?php echo esc_attr(get_option('oxygen_vsb_full_page_block_category_label')); ?>" placeholder="Oxygen Full Page Blocks"></td>
            </tr>
        </table>

		<?php submit_button(); ?>
    </form>
    <?php
}

function oxygen_vsb_options_bloat_eliminator_page() {
	?>

	<div class="oxygen-vsb-settings-container">

	    <h2>Bloat Eliminator</h2>

	    <form method="post" action="options.php">
			<?php settings_fields( 'oxygen_vsb_options_group_bloat_eliminator' ); ?>
			<?php do_settings_sections( 'oxygen_vsb_options_group_bloat_eliminator' ); ?>
	        <table>
	            <tr valign="top">
	                <th scope="row"><label for="oxygen_vsb_disable_emojis"><?php _e("Disable WP Emojis","oxygen"); ?></label></th>
	                <td><input type="checkbox" id="oxygen_vsb_disable_emojis" name="oxygen_vsb_disable_emojis" value="true" <?php checked(get_option('oxygen_vsb_disable_emojis'), "true"); ?>></td>
	                <td><label for="oxygen_vsb_disable_emojis"><?php _e("Disables built-in WordPress JavaScript for rendering Emojis."); ?></label></td>
	            </tr>
	            <tr valign="top">
	                <th scope="row"><label for="oxygen_vsb_disable_jquery_migrate"><?php _e("Disable jQuery Migrate","oxygen"); ?></label></th>
	                <td><input type="checkbox" id="oxygen_vsb_disable_jquery_migrate" name="oxygen_vsb_disable_jquery_migrate" value="true" <?php checked(get_option('oxygen_vsb_disable_jquery_migrate'), "true"); ?>></td>
	                <td><label for="oxygen_vsb_disable_jquery_migrate"><?php _e("Disables the ability to run deprecated jQuery code on the current jQuery version."); ?></label></td>
	            </tr>
	            <tr valign="top">
	                <th scope="row"><label for="oxygen_vsb_disable_embeds"><?php _e("Disable Embeds","oxygen"); ?></label></th>
	                <td><input type="checkbox" id="oxygen_vsb_disable_embeds" name="oxygen_vsb_disable_embeds" value="true" <?php checked(get_option('oxygen_vsb_disable_embeds'), "true"); ?>></td>
	                <td><label for="oxygen_vsb_disable_embeds"><?php _e("Disables the automatic embedding of some content (YouTube videos, Tweets, etc.,) when pasting the URL into your blog posts."); ?></label></td>
	            </tr>
	            <tr valign="top">
	                <th scope="row"><label for="oxygen_vsb_disable_google_fonts"><?php _e("Disable Google Fonts","oxygen"); ?></label></th>
	                <td><input type="checkbox" id="oxygen_vsb_disable_google_fonts" name="oxygen_vsb_disable_google_fonts" value="true" <?php checked(get_option('oxygen_vsb_disable_google_fonts'), "true"); ?>></td>
	                <td><label for="oxygen_vsb_disable_google_fonts"><?php _e("Disables Google Fonts for your entire site."); ?></label></td>
	            </tr>
	            <tr valign="top">
	                <th scope="row"><label for="oxygen_vsb_use_css_for_google_fonts"><?php _e("Disable Webfont.js","oxygen"); ?></label></th>
	                <td><input type="checkbox" id="oxygen_vsb_use_css_for_google_fonts" name="oxygen_vsb_use_css_for_google_fonts" value="true" <?php checked(get_option('oxygen_vsb_use_css_for_google_fonts'), "true"); ?>></td>
	                <td><label for="oxygen_vsb_use_css_for_google_fonts"><?php _e("Use CSS for Google Fonts instead of Webfont.js"); ?></label></td>
	            </tr>
	        </table>

			<?php submit_button(); ?>
	    </form>

	</div>

	<?php
}

function oxygen_vsb_options_revisions() {
	?>

	<div class="oxygen-vsb-settings-container">

	    <h2>Revisions</h2>

	    <form method="post" action="options.php">
			<?php settings_fields( 'oxygen_vsb_options_revisions' ); ?>
			<?php do_settings_sections( 'oxygen_vsb_options_revisions' ); ?>
	       
			<table>
				<tr valign="top">
					<th scope="row"><label for="oxygen_vsb_number_of_latest_revisions"><?php _e("Maximum number of latest revisions", "oxygen"); ?></label></th>
					<td>
						<input type="text" 
							id="oxygen_vsb_number_of_latest_revisions" 
							name="oxygen_vsb_number_of_latest_revisions" 
							value="<?php echo esc_attr(get_option('oxygen_vsb_number_of_latest_revisions')); ?>">
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="oxygen_vsb_number_of_daily_revisions"><?php _e("Maximum number of daily revisions", "oxygen"); ?></label></th>
					<td>
						<input type="text" 
							id="oxygen_vsb_number_of_daily_revisions" 
							name="oxygen_vsb_number_of_daily_revisions" 
							value="<?php echo esc_attr(get_option('oxygen_vsb_number_of_daily_revisions')); ?>">
					</td>
				</tr>
			</table>

			<?php submit_button(); ?>
	    </form>

		<h2>Delete ALL Revisions</h2>

		<p><?php _e("This can not be undone. Type “delete” in the textbox below to confirm.", "oxygen"); ?></p>

		<input type='text' name='confirmation' id="delete-all-revisions-confirmation" placeholder='delete'></input><br/><br/>

		<input type="button" name="delete-all-revisions" id="delete-all-revisions" class="button button-primary" value="Delete"
			data-revisions-nonce="<?php echo wp_create_nonce( 'oxygen-nonce-revisions');?>"><br/><br/>

		<div id="delete-revisions-result"></div>

	</div>

	<?php
}


function oxygen_vsb_options_client_control_page() {

	$users_access_list = get_option("oxygen_vsb_options_users_access_list", array());
	if (!is_array($users_access_list)) $users_access_list = array();
	$users_access_enable_elements = get_option("oxygen_vsb_options_users_access_enable_elements", array());
	$users_access_advanced_tab = get_option("oxygen_vsb_options_users_access_advanced_tab", array());
	$users_access_drag_n_drop = get_option("oxygen_vsb_options_users_access_drag_n_drop", array());
	$users_access_enabled_elements = get_option("oxygen_vsb_options_users_access_enabled_elements", array());
	$users_access_reusable_parts = get_option("oxygen_vsb_options_users_access_reusable_parts", array());
	$users_access_design_library = get_option("oxygen_vsb_options_users_access_design_library", array());
	$users_access_disable_classes = get_option("oxygen_vsb_options_users_access_disable_classes", array());
	$users_access_disable_ids = get_option("oxygen_vsb_options_users_access_disable_ids", array());

	$role_access_enable_elements = get_option("oxygen_vsb_options_role_access_enable_elements", array());
	$role_access_advanced_tab = get_option("oxygen_vsb_options_role_access_advanced_tab", array());
	$role_access_drag_n_drop = get_option("oxygen_vsb_options_role_access_drag_n_drop", array());
	$role_access_enabled_elements = get_option("oxygen_vsb_options_role_access_enabled_elements", array());
	$role_access_reusable_parts = get_option("oxygen_vsb_options_role_access_reusable_parts", array());
	$role_access_design_library = get_option("oxygen_vsb_options_role_access_design_library", array());
	$role_access_disable_classes = get_option("oxygen_vsb_options_role_access_disable_classes", array());
	$role_access_disable_ids = get_option("oxygen_vsb_options_role_access_disable_ids", array());

	if ( !is_array($role_access_enable_elements)) {
		$role_access_enable_elements = array();
	}
	if ( !is_array($role_access_advanced_tab)) {
		$role_access_advanced_tab = array();
	}
	if ( !is_array($role_access_drag_n_drop)) {
		$role_access_drag_n_drop = array();
	}
	if ( !is_array($role_access_enabled_elements)) {
		$role_access_enabled_elements = array();
	}
	if ( !is_array($role_access_reusable_parts)) {
		$role_access_reusable_parts = array();
	}
	if ( !is_array($role_access_design_library)) {
		$role_access_design_library = array();
	}
	if ( !is_array($role_access_disable_classes)) {
		$role_access_disable_classes = array();
	}
	if ( !is_array($role_access_disable_ids)) {
		$role_access_disable_ids = array();
	}
	if ( !is_array($users_access_enabled_elements)) {
		$users_access_enabled_elements = array();
	}

	global $oxygen_vsb_components;

	$all_oxygen_components = array();
	foreach ($oxygen_vsb_components as $component) {
		
		if (!isset($component->options) || !is_array($component->options)) continue;
		if (!isset($component->options['tag']) || !isset($component->options['name'])) continue;
		if (in_array($component->options['tag'], array('ct_widget','ct_sidebar','ct_li','ct_svg_icon'))) continue;
		
		$all_oxygen_components[$component->options['tag']] = $component->options['name'];
	}

	// Composite Elements
	$OxygenCompositeElements = new OxygenCompositeElements();
	
	if (!empty($OxygenCompositeElements->composite_elements) && is_array($OxygenCompositeElements->composite_elements->components)) {
		foreach ($OxygenCompositeElements->composite_elements->components as $component) {
			if ( !isset($component->min_version) || version_compare(CT_VERSION, $component->min_version) === -1 ) {
				continue;
			}
			if ( !isset($component->location) ) {
				continue;
			}
			if (isset($component->id) && isset($component->name)) {
				$all_oxygen_components[$component->id."-".$component->page] = $component->name;
			}
		}
	}
	?>

	<div class="oxygen-vsb-settings-container">

	<h2>Role Manager</h2>
	<?php do_action('oxygen_vsb_before_settings_page');?>
	<p>
		<strong>Important Security Warning:</strong> Oxygen's Code Block element can execute any PHP code. A malicious user could use this to do literally anything to the site. Therefore, do not grant Oxygen access to untrusted users. <a target="_blank" href="https://oxygenbuilder.com/documentation/other/client-control/#security">Click here to learn more.</a>
	</p>

	<form method="post" action="options.php">
		<?php settings_fields( 'oxygen_vsb_options_group_client_control' ); ?>
	    <?php do_settings_sections( 'oxygen_vsb_options_group_client_control' ); ?>
		<table id="oxygen_vsb_access_role_settings">
		<?php 
			
			add_filter('editable_roles', 'oxygen_vsb_remove_admin_role');
			$roles = get_editable_roles();
			remove_filter('editable_roles', 'oxygen_vsb_remove_admin_role');
			
			foreach($roles as $role => $item) {
				?>
				<tr valign="top" class="oxygen_role_access_settings_row">
					<th scope="row"><label for="oxygen_vsb_access_role_<?php echo esc_attr($role);?>"><?php echo esc_html($item['name']); ?></label></th>
					<td>
						<select name="oxygen_vsb_access_role_<?php echo esc_attr($role);?>" id="oxygen_vsb_access_role_<?php echo esc_attr($role);?>" class="oxygen_vsb_access_role_select">
							<option value="false" >No Access</option>
							<?php if (oxygen_vsb_is_agency_bundle()) : ?>
							<option value="edit_only" <?php selected(get_option("oxygen_vsb_access_role_$role"), "edit_only"); ?>>Edit Only</option>
							<?php endif; ?>
							<option value="true" <?php selected(get_option("oxygen_vsb_access_role_$role"), "true"); ?>>Full Access</option>
						</select>
						<div class="oxygen_role_access_edit_only_sub_options">
							<label><input type="checkbox" class="oxygen_vsb_options_role_access_enable_elements" name="oxygen_vsb_options_role_access_enable_elements[<?php echo esc_attr($role);?>][]" <?php if (isset($role_access_enable_elements[$role])) checked($role_access_enable_elements[$role][0], "true"); ?> value="true"/>Enable Elements<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>Allows user to access a specific element in the +Add Pane</span></label>
							<div>
								<select name="oxygen_vsb_options_role_access_enabled_elements[<?php echo esc_attr($role); ?>][]" class="oxygen_user_access_enabled_elements" multiple="multiple">
									<?php foreach ($all_oxygen_components as $tag => $name) : ?>
									<option value="<?php echo $tag; ?>" <?php if(is_array($role_access_enabled_elements) && isset($role_access_enabled_elements[$role]) && is_array($role_access_enabled_elements[$role])) selected(in_array($tag, $role_access_enabled_elements[$role])); ?>><?php echo $name; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<label><input type="checkbox" class="oxygen_vsb_options_role_access_advanced_tab" name="oxygen_vsb_options_role_access_advanced_tab[<?php echo esc_attr($role);?>][]" <?php  if (isset($role_access_advanced_tab[$role])) checked($role_access_advanced_tab[$role][0], "true"); ?> value="true"/>Enable Advanced Tab</label><br/>
							<label><input type="checkbox" class="oxygen_vsb_options_role_access_drag_n_drop" name="oxygen_vsb_options_role_access_drag_n_drop[<?php echo esc_attr($role);?>][]" <?php if (isset($role_access_drag_n_drop[$role])) checked($role_access_drag_n_drop[$role][0], "true"); ?> value="true"/>Enable Drag & Drop</label><br/>
							<label><input type="checkbox" class="oxygen_vsb_options_role_access_reusable_parts" name="oxygen_vsb_options_role_access_reusable_parts[<?php echo esc_attr($role);?>][]" <?php  if (isset($role_access_reusable_parts[$role])) checked($role_access_reusable_parts[$role][0], "true"); ?> value="true"/>Enable Reusable Parts</label><br/>
							<label><input type="checkbox" class="oxygen_vsb_options_role_access_design_library" name="oxygen_vsb_options_role_access_design_library[<?php echo esc_attr($role);?>][]" <?php  if (isset($role_access_design_library[$role])) checked($role_access_design_library[$role][0], "true"); ?> value="true"/>Enable Design Library</label><br/>
							<label><input type="checkbox" class="oxygen_vsb_options_role_access_disable_classes" name="oxygen_vsb_options_role_access_disable_classes[<?php echo esc_attr($role);?>][]" <?php  if (isset($role_access_disable_classes[$role])) checked($role_access_disable_classes[$role][0], "true"); ?> value="true"/>Disable Classes</label><br/>
							<label><input type="checkbox" class="oxygen_vsb_options_role_access_disable_ids" name="oxygen_vsb_options_role_access_disable_ids[<?php echo esc_attr($role);?>][]" <?php  if (isset($role_access_disable_ids[$role])) checked($role_access_disable_ids[$role][0], "true"); ?> value="true"/>Disable IDs</label>
						</div>
					</td>
				</tr>
				<?php
			}
		 ?>
		</table>

		<h2>Per User Access</h2>
		<?php do_action('oxygen_vsb_before_settings_page');?>
		<p>
			This override the above role-based restrictions. 
		</p>

		<div id="oxygen_user_access_table">
			<div id="oxygen_user_access_placeholder">
					<a href="#" class="oxygen_user_access_remove_user" title="<?php _e("Remove Access Rule","oxygen"); ?>"></a>
					Grant
					<select name="oxygen_user_access_user_select" class="oxygen_user_access_user_select">
					<option value=""></option>
					<?php 

					$users_limit = 5000;
					$main_users = array();
					$other_users = array();
					
					$main_users = get_users( array(
						"role__in" => array('administrator','editor'),
						"number" => $users_limit
					));

					if (count($main_users) < $users_limit) {
						$number = $users_limit - count($main_users);
						$other_users = get_users( array(
							"role__not_in" => array('administrator','editor'),
							"number" => $number
						));
					}

					$registered_users = array_merge($main_users, $other_users);

					foreach($registered_users as $registered_user) : 
						if ($registered_user->ID==get_current_user_id()){
							continue;
						}
						?>
						<option value="<?php echo $registered_user->ID; ?>"><?php echo $registered_user->data->user_login; ?></option>
					<?php endforeach; ?>
					</select>
					access level
					<select class="oxygen_user_access_level_select">
						<option value="false">No Access</option>
						<?php if (oxygen_vsb_is_agency_bundle()) : ?>
						<option value="edit_only">Edit Only</option>
						<?php endif; ?>
						<option value="full">Full Access</option>
					</select>
				<div class="oxygen_user_access_edit_only_sub_options">
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_enable_elements" value="true"/>Enable Elements<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>Allows user to access a specific element in the +Add Pane</span></label>
					<div>
						<select name="" class="oxygen_user_access_enabled_elements" multiple="multiple">
							<?php foreach ($all_oxygen_components as $tag => $name) : ?>
								<option value="<?php echo $tag; ?>"><?php echo $name; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_advanced_tab" value="true"/>Enable Advanced Tab</label><br/>
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_drag_n_drop" value="true"/>Enable Drag & Drop</label><br/>
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_reusable_parts" value="true"/>Enable Reusable Parts</label><br/>
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_design_library" value="true"/>Enable Design Library</label><br/>
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_disable_classes" value="true"/>Disable Classes</label><br/>
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_disable_ids" value="true"/>Disable IDs</label>
				</div>
			</div>
			<?php foreach ($users_access_list as $user_id => $user_with_access) : ?>
			<div class="oxygen-user-access-settings-row">
				<a href="#" class="oxygen_user_access_remove_user" title="<?php _e("Remove Access Rule","oxygen"); ?>"></a>
				Grant
				<select name="oxygen_user_access_user_select" class="oxygen_user_access_user_select">
				<?php foreach($registered_users as $registered_user) : 
					if ($registered_user->ID==get_current_user_id()){
						continue;
					}
					?>
					<option value="<?php echo $registered_user->ID; ?>" <?php selected($registered_user->ID, $user_id); ?>">
						<?php echo $registered_user->data->user_login; ?>
					</option>
				<?php endforeach; ?>
				</select>
				access level
				<select name="oxygen_vsb_options_users_access_list[<?php echo esc_attr($user_id); ?>][]" class="oxygen_user_access_level_select">
					<option value="false">No Access</option>
					<?php if (oxygen_vsb_is_agency_bundle()) : ?>
					<option value="edit_only" <?php selected($users_access_list[$user_id][0], "edit_only"); ?>>Edit Only</option>
					<?php endif; ?>
					<option value="true" <?php selected($users_access_list[$user_id][0], "true"); ?>>Full Access</option>
				</select></br>
				<div class="oxygen_user_access_edit_only_sub_options">
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_enable_elements" name="oxygen_vsb_options_users_access_enable_elements[<?php echo esc_attr($user_id); ?>][]" <?php if (isset($users_access_enable_elements[$user_id])) checked($users_access_enable_elements[$user_id][0], "true"); ?> value="true"/>Enable Elements<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>Allows user to access a specific element in the +Add Pane</span></label>
					<div>
						<select name="oxygen_vsb_options_users_access_enabled_elements[<?php echo esc_attr($user_id); ?>][]" class="oxygen_user_access_enabled_elements" multiple="multiple">
							<?php foreach ($all_oxygen_components as $tag => $name) : ?>
							<option value="<?php echo $tag; ?>" <?php if(isset($users_access_enabled_elements[$user_id]) && is_array($users_access_enabled_elements[$user_id])) selected(in_array($tag, $users_access_enabled_elements[$user_id])); ?>><?php echo $name; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_advanced_tab" name="oxygen_vsb_options_users_access_advanced_tab[<?php echo esc_attr($user_id); ?>][]" <?php  if (isset($users_access_advanced_tab[$user_id])) checked($users_access_advanced_tab[$user_id][0], "true"); ?> value="true"/>Enable Advanced Tab</label><br/>
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_drag_n_drop" name="oxygen_vsb_options_users_access_drag_n_drop[<?php echo esc_attr($user_id); ?>][]" <?php  if (isset($users_access_drag_n_drop[$user_id])) checked($users_access_drag_n_drop[$user_id][0], "true"); ?> value="true"/>Enable Drag & Drop</label><br/>
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_reusable_parts" name="oxygen_vsb_options_users_access_reusable_parts[<?php echo esc_attr($user_id); ?>][]" <?php  if (isset($users_access_reusable_parts[$user_id])) checked($users_access_reusable_parts[$user_id][0], "true"); ?> value="true"/>Enable Reusable Parts</label><br/>
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_design_library" name="oxygen_vsb_options_users_access_design_library[<?php echo esc_attr($user_id); ?>][]" <?php  if (isset($users_access_design_library[$user_id])) checked($users_access_design_library[$user_id][0], "true"); ?> value="true"/>Enable Design Library</label><br/>
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_disable_classes" name="oxygen_vsb_options_users_access_disable_classes[<?php echo esc_attr($user_id); ?>][]" <?php  if (isset($users_access_disable_classes[$user_id])) checked($users_access_disable_classes[$user_id][0], "true"); ?> value="true"/>Disable Classes</label><br/>
					<label><input type="checkbox" class="oxygen_vsb_options_users_access_disable_ids" name="oxygen_vsb_options_users_access_disable_ids[<?php echo esc_attr($user_id); ?>][]" <?php  if (isset($users_access_disable_ids[$user_id])) checked($users_access_disable_ids[$user_id][0], "true"); ?> value="true"/>Disable IDs</label>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
		<table>
			<tr>
				<a id="oxygen_user_access_add_user" href="#"><?php _e("Add User", "oxygen"); ?></a>
			</tr>
		</table>

		<h2>Post Type Manager</h2>
	
		<p>Hide Oxygen metabox on the following post types:</p>
		<table>
		<?php 
			
			global $ct_ignore_post_types;
			$postTypes = get_post_types();
			
			if(is_array($ct_ignore_post_types) && is_array($postTypes)) {
				$postTypes = array_diff($postTypes, $ct_ignore_post_types);
			}
			
			foreach($postTypes as $key => $item) {
				?>
				<tr valign="top">
					<td><input type="checkbox" id="oxygen_vsb_ignore_post_type_<?php echo esc_attr($key);?>" name="oxygen_vsb_ignore_post_type_<?php echo esc_attr($key);?>" value="true" <?php checked(get_option("oxygen_vsb_ignore_post_type_$key"), "true"); ?>></td>
					<td><label for="oxygen_vsb_ignore_post_type_<?php echo esc_attr($key);?>"><?php echo esc_html($item); ?></label></td>
				</tr>
				<?php
			}
	
		 ?>
		</table>
		 <?php submit_button(); ?>
	  </form>

	</div>

	<?php
}

function oxygen_vsb_options_security_manager_page() {
	?>
	<div class="oxygen-vsb-settings-container">
		<h2>Shortcode Signing</h2>
		<div class="oxygen-vsb-settings-info-div">
			<a class="oxygen-vsb-settings-info-button" href="https://oxygenbuilder.com/documentation/other/security/" target="_blank">
				<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="info-circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
				class="svg-inline--fa fa-info-circle fa-w-16 fa-2x" width="20px">
					<path fill="currentColor" d="M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 110c23.196 0 42 18.804 42 42s-18.804 42-42 42-42-18.804-42-42 18.804-42 42-42zm56 254c0 6.627-5.373 12-12 12h-88c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h12v-64h-12c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h64c6.627 0 12 5.373 12 12v100h12c6.627 0 12 5.373 12 12v24z" class=""></path>
				</svg>&nbsp;
				<?php _e("Learn about Shortcode signing", "oxygen");?>
			</a>
		</div>
		
	  <form method="post" action="options.php">
	  <?php settings_fields( 'oxygen_vsb_options_group_security' ); ?>
      <?php do_settings_sections( 'oxygen_vsb_options_group_security' ); ?>
		  <table>
			 
			  <tr valign="top">
				  <td><input type="checkbox" id="oxygen_vsb_enable_signature_validation" name="oxygen_vsb_enable_signature_validation" value="true" <?php checked(get_option('oxygen_vsb_enable_signature_validation'), "true"); ?>></td>
				  <td><label for="oxygen_vsb_enable_signature_validation"><?php _e("Check Oxygen's shortcodes for a valid signature before executing.","oxygen"); ?> </label></td>
			  </tr>
			  <tr valign="top">
				  <td><input type="checkbox" id="oxygen_vsb_enable_signature_frontend_errors" name="oxygen_vsb_enable_signature_frontend_errors" value="true" <?php checked(get_option('oxygen_vsb_enable_signature_frontend_errors'), "true"); ?>></td>
				  <td><label for="oxygen_vsb_enable_signature_frontend_errors"><?php _e("Show invalid shortcode signature warnings on the front end.","oxygen"); ?> </label></td>
			  </tr>

		  </table>

		  <div class="oxygen-vsb-settings-flex">
		  	<div class="oxygen-vsb-settings-flex-column">
				<?php submit_button(); ?>
			</div>
		  	<div class="oxygen-vsb-settings-flex-column">
		  		<a class="oxygen-vsb-settings-button" href="<?php echo add_query_arg('page', 'oxygen_vsb_sign_shortcodes', get_admin_url().'admin.php');?>">Sign All Shortcodes</a>
			</div>		  	
		  </div>
	  </form>

	</div>

	<?php
}



function ct_cache_page_callback() {
	?>

	<div class="oxygen-vsb-settings-container">

		<h2>CSS Cache</h2>
		
		<div>If your site has been migrated, or changes to global styles aren't showing on the front of your site, you will likely need to regenerate Oxygen's CSS cache.</div>

		<form action="<?php echo add_query_arg('page', 'oxygen_vsb_regenerate_css', get_admin_url().'admin.php');?>" method="POST">

			<?php wp_nonce_field( 'oxygen_vsb_regenerate_css' ); ?>

			<p>
				<strong><?php _e('Post Types to regenerate:', 'oxygen');?></strong>
			</p>
			<table>
			<?php 
				
				global $ct_ignore_post_types;
				$postTypes = get_post_types();
				
				$ignore_post_types = $ct_ignore_post_types;

				$ct_template_key = array_search('ct_template', $ignore_post_types);

				if($ct_template_key !== false) {
					unset($ignore_post_types[$ct_template_key]);
				}

				if(is_array($ignore_post_types) && is_array($postTypes)) {
					$postTypes = array_diff($postTypes, $ignore_post_types);
				}
				
				foreach($postTypes as $key => $item) {
					?>
					<tr valign="top">
						<td>
							<input type="checkbox" 
								class="oxy_css_regeneration_post_type" 
								id="oxy_css_regeneration_post_type_<?php echo esc_attr($key);?>" 
								name="oxy_css_regeneration_post_type[]" 
								value="<?php echo esc_attr($key);?>" <?php checked($key == 'page' || $key == 'ct_template' || $key == 'oxy_user_library'); ?>>
						</td>
						<td>
							<label for="oxy_css_regeneration_post_type_<?php echo esc_attr($key);?>">
								<?php echo esc_html($item); ?>
							</label>
						</td>
					</tr>
					<?php
				}

			?>
			</table>
			<p>
				<strong><?php _e('Posts that have:', 'oxygen');?></strong>
			</p>
			<table>
				<tr>
					<td>
						<input type="checkbox" 
							id="oxy_css_regeneration_ct_builder_shortcodes"
							name="oxy_css_regeneration_meta_key[]" 
							value="ct_builder_shortcodes" checked>
					</td>
					<td>
						<label for="oxy_css_regeneration_ct_builder_shortcodes">
							Shortcodes
						</label>
					</td>
				</tr>
				<tr>
					<td>
						<input type="checkbox" 
							id="oxy_css_regeneration_ct_builder_json"
							name="oxy_css_regeneration_meta_key[]" 
							value="ct_builder_json" checked>
					</td>
					<td>
						<label for="oxy_css_regeneration_ct_builder_json">
							JSON
						</label>
					</td>
				</tr>
			</table>
			
			<p class="submit">
				<input type="submit" class="oxygen-vsb-settings-button" value="Go to Regeneration Page"/>
			</p>
		</form>
		
		
		<div id="oxy-cache-result"></div>

		<h2>Danger Zone - Advanced Options</h2>

		<p><?php _e("<strong>WARNING!</strong> - CSS caching should remain enabled for most sites. These settings should not be modified unless you're advised to do so by Oxygen support staff, or you fully understand the risks associated with doing so."); ?></p>

		<form method="post" action="options.php">
			<?php settings_fields( 'oxygen_vsb_options_group_cache' ); ?>
	    	<?php do_settings_sections( 'oxygen_vsb_options_group_cache' ); ?>
			<table>
				<tr valign="top">
					<td id="oxy-cache-setting" class="oxy-disable-admin-button">
						<b><label for="oxygen_vsb_universal_css_cache"><?php _e("Enable CSS Caching","oxygen"); ?></label></b>
						<input type="checkbox" id="oxygen_vsb_universal_css_cache" name="oxygen_vsb_universal_css_cache" value="true" <?php checked(get_option('oxygen_vsb_universal_css_cache'), "true"); ?>>
					</td>
					<td>
						<a href="javascript:void(0)" onClick="if( confirm('Disabling Oxygen\'s CSS caching is not supported or recommended and may cause issues with performance and functionality on your site.') ) document.querySelector('#oxy-cache-setting').classList.remove('oxy-disable-admin-button')">Unlock</a>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>

	</div>

	<?php
}

function oxygen_vsb_regenerate_css() {
	
	if(!oxygen_vsb_current_user_can_access()) {
		return;
	}

	add_submenu_page(null, 'Oxygen Regenerate CSS', 'Oxygen  Regenerate CSS', 'read', 'oxygen_vsb_regenerate_css', 'oxygen_vsb_regenerate_css_page');

}
add_action('admin_menu', 'oxygen_vsb_regenerate_css', 15);

function oxygen_vsb_regenerate_css_page() {

	?>

	<h2><?php _e("Regenerate CSS", "oxygen"); ?></h2>

	<?php

	if ( !isset( $_REQUEST['_wpnonce'] ) ||
		!wp_verify_nonce( $_REQUEST['_wpnonce'], "oxygen_vsb_regenerate_css") ) {
		?><p>No valid nonce found.</p><?php
		return;
	}

	if ( isset( $_REQUEST['oxy_css_regeneration_meta_key'] ) &&
		is_array( $_REQUEST['oxy_css_regeneration_meta_key'] ) ) {
		$meta_keys = $_REQUEST['oxy_css_regeneration_meta_key'];
	}
	else {
		?><p>There is NO posts to regenerate.</p><?php
		return;
	}

	if ( isset( $_REQUEST['oxy_css_regeneration_post_type'] ) &&
		is_array( $_REQUEST['oxy_css_regeneration_post_type'] ) ) {
		$post_types = $_REQUEST['oxy_css_regeneration_post_type'];
	}
	else {
		?><p>There is NO posts to regenerate.</p><?php
		return;
	}

	$meta_query = array();

	if ( count( $meta_keys ) > 1 ) {
		$meta_query['relation'] = 'OR';
	}

	foreach ( $meta_keys as $meta_key ) {
		$meta_query[] = array(
			'key'     => $meta_key,
			'value'   => '',
			'compare' => '!=',
		);
	}

	$posts = array();

	$query = new WP_Query(array( 
		'posts_per_page' => -1,
		'fields' => 'ids',
		'post_type' => $post_types,
		'meta_query' => $meta_query
	));
		
	foreach ($query->posts as $post) {
		$posts[] = $post; 
	}
	?>

	<script type="text/javascript">
		var oxyPostsToRegenerateCSS = [<?php echo implode(",", $posts); ?>];
	</script>

	<p>
		There is <?php echo count($posts); ?> posts to regenerate.
	</p>
	
	<p class="submit">
		<input type="submit" id="oxy-cache-generate" class="button button-primary" value="Start regeneration"/>
	</p>
		
	<div id="oxy-cache-result"></div>

	<?php
}


function ct_admin_settings() {
	
	if(!oxygen_vsb_current_user_can_full_access()) {
		return;
	}

	$oxygen_vsb_settings = add_submenu_page(
			'ct_dashboard_page',
			'Settings',
			'Settings',
			'read',
			'oxygen_vsb_settings',
			'oxygen_vsb_options_page');

	add_submenu_page(null, 'Add 3rd Party Design Set', 'Add 3rd Party Design Set', 'manage_options', 'add_3rdp_designset', 'add_3rdp_designset_callback');
		
	add_action( 'load-' . $oxygen_vsb_settings, 'oxygen_vsb_settings_page_onload' );
}



function oxygen_vsb_settings_page_onload() {
	add_action( 'admin_enqueue_scripts', 'oxygen_vsb_settings_page_css' );
}

function oxygen_vsb_settings_page_css() {
	wp_enqueue_style("oxy-admin-settings-page", CT_FW_URI."/admin/oxy-settings-page.css");
}

function ct_license_page_callback() { 

	if(defined('CT_FREE')) {
		return;
	}

	?>
	
	<div class="oxygen-vsb-settings-container">

		<h2><?php _e("License Keys", "component-theme"); ?></h2>
	
		<?php 
		
		/**
		 * Hook for addons to show things in Oxygen admin
		 *
		 * 10 - Oxygen
		 * 20 - Selector Detector
		 * 30 - Typekit
		 *
		 * @since 1.4
		 */
		
		do_action("oxygen_license_admin_screen");
		
		?>

	</div>

	
<?php }

function oxygen_vsb_register_signing_page() {
	
	if(!oxygen_vsb_current_user_can_access()) {
		return;
	}

	add_submenu_page(null, 'Oxygen Sign Shortcodes', 'Oxygen Sign Shortcodes', 'read', 'oxygen_vsb_sign_shortcodes', 'oxygen_vsb_sign_shortcodes_page');

}

add_action('admin_menu', 'oxygen_vsb_register_signing_page', 15);

function oxygen_vsb_sign_shortcodes_page() {
	wp_nonce_field( 'oxygen_vsb_sign_shortcodes', 'oxygen_vsb_sign_shortcodes_nonce' );

	?>
	
	<p>
		<strong><?php _e('Please backup your site before using this tool.', 'oxygen');?></strong>
	</p>
	<p>
		<label for="site_backup_confirmation"><input type="checkbox" value="1" id="site_backup_confirmation"> <?php _e('I have made a complete backup of my site.', 'oxygen');?></label>
	</p>

	<p>
		<strong><?php _e('Select the post types.', 'oxygen');?></strong>
	</p>
	<table>
	<?php 
		
		global $ct_ignore_post_types;
		$postTypes = get_post_types();
		
		$ignore_post_types = $ct_ignore_post_types;

		$ct_template_key = array_search('ct_template', $ignore_post_types);

		if($ct_template_key !== false) {
			unset($ignore_post_types[$ct_template_key]);
		}

		if(is_array($ignore_post_types) && is_array($postTypes)) {
			$postTypes = array_diff($postTypes, $ignore_post_types);
		}
		
		foreach($postTypes as $key => $item) {
			?>
			<tr valign="top">
				<td><input type="checkbox" class="oxygen_vsb_ignore_post_type" id="oxygen_vsb_ignore_post_type_<?php echo esc_attr($key);?>" name="oxygen_vsb_ignore_post_type[]" value="<?php echo esc_attr($key);?>" <?php checked($key == 'page' || $key == 'ct_template' || $key == 'oxy_user_library'); ?>></td>
				<td><label for="oxygen_vsb_ignore_post_type_<?php echo esc_attr($key);?>"><?php echo esc_html($item); ?></label></td>
			</tr>
			<?php
		}

	 ?>
	</table>

	<p>
		<button id="start-signing-process">Start shortcodes signing process</button>
	</p>
	<div id="upgrade-output">

	</div>
	<script>
	
		jQuery(document).ready(function($) {
			var stepCount = 0;
			var parent = $('#upgrade-output');
			function processMessages(response, step) {


				if(response['messages']) {
			

					response['messages'].forEach(function(message, index) {
						
						var msgBlock = $('<div>').html(message);
						
						parent.append(msgBlock);	

					});

	
				}



			}

			function processSigning(step, pageindex, pageIDs) {

				if(step > 1000) {
					var msgBlock = $('<div>').html('Completed');
					
					parent.append(msgBlock);	
					return;
				}

				var postTypes = [];

				jQuery('.oxygen_vsb_ignore_post_type').each(
					function(item) { 
						if(jQuery(this).prop('checked')) { 
							postTypes.push(jQuery(this).val());
						}
					}
				);

				var data = {
					'action': 'oxygen_vsb_signing_process',
					'nonce': jQuery('#oxygen_vsb_sign_shortcodes_nonce').val(),
					'postTypes': postTypes
				};

				if(typeof(step) !== 'undefined') {
					data['step'] = step;
				}

				if(typeof(pageindex) !== 'undefined') {
					data['index'] = pageindex;
				}

				if(typeof(pageIDs) !== 'undefined') {
					data['page_ids'] = pageIDs;
				}

				jQuery.post(ajaxurl, data, function(response) {
					
					if(response['messages']) {
						processMessages(response, step);
					}

					if(typeof(response['step']) !== 'undefined') {
						
						if(typeof(response['index']) !== 'undefined' && typeof(response['page_ids']) !== 'undefined') {
							processSigning(parseInt(response['step']), parseInt(response['index']), response['page_ids']);
						}
						else if(typeof(response['index']) !== 'undefined') {
							processSigning(parseInt(response['step']), parseInt(response['index']));
						}
						else {
							processSigning(parseInt(response['step']));
						}
					}

				});
			}

			$('#start-signing-process').on('click', function() {
				if(!$('#site_backup_confirmation').prop('checked')) {
					alert('<?php _e('Please back up your site and then check the box.', 'oxygen');?>');
					return;
				}

				$('#upgrade-output').html('');
				processSigning();

			});

		});

	</script>

	<?php
}

add_action('admin_footer', 'edit_with_oxygen_buttons_for_edit_mode');
function edit_with_oxygen_buttons_for_edit_mode() {

	if (oxygen_vsb_current_user_can_full_access()) {
		return;
	}
	
	global $post;
	$screen = get_current_screen();

	// Post edit mode locked
	$post_locked = get_post_meta( $post->ID, 'oxygen_lock_post_edit_mode', true );
	if ($post_locked=="true") { ?>
		
		<a class="oxygen-edit-mode-button oxygen-edit-mode-button-disabled" href="#" title="<?php _e("Editing is locked for this post.", "oxygen"); ?>">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/Oxygen-O.png'/>
		</a>
		<?php if (isset($screen->id)&&($screen->id=="post"||$screen->id=="page"||$screen->id=="ct_template")) {?>
		<a class="oxygen-edit-mode-button-non-gutenberg page-title-action oxygen-edit-mode-button-disabled" href="#" title="<?php _e("Editing is locked for this post.", "oxygen"); ?>">
			<?php _e("Edit with Oxygen","oxygen"); ?>
		</a>
		<?php }

		return;
	}

	$edit_with_oxygen_link = oxygen_add_posts_quick_action_link(array(), $post, "array");

	$template_locked = false;
	if (isset($edit_with_oxygen_link["template"])) {
		$template_locked = get_post_meta( $edit_with_oxygen_link["template"], 'oxygen_lock_post_edit_mode', true );
	}

	if (is_oxygen_edit_post_locked()||$template_locked) { 
		
		if ($template_locked) {
			$text = __("Editing is locked for template.", "oxygen");
		} else {
			$text = __("Oxygen is open in another tab or by another user.", "oxygen");
		}
		
		?>
		
		<a class="oxygen-edit-mode-button oxygen-edit-mode-button-disabled" href="#" title="<?php echo $text; ?>">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/Oxygen-O.png'/>
		</a>
		<?php if (isset($screen->id)&&($screen->id=="post"||$screen->id=="page"||$screen->id=="ct_template")) {?>
		<a class="oxygen-edit-mode-button-non-gutenberg page-title-action oxygen-edit-mode-button-disabled" href="#" title="<?php echo $text; ?>">
			<?php _e("Edit with Oxygen","oxygen"); ?>
		</a>
		<?php }
		
		return;
	}

	if (!isset($edit_with_oxygen_link["url"])||!isset($edit_with_oxygen_link["text"])) {
		return;
	}	

	$extra_class = "";
	if ($edit_with_oxygen_link["text"]=="Edit Template") {
		$extra_class = "oxygen-edit-mode-button-template";
	}

	?>
	<a class="oxygen-edit-mode-button <?php echo $extra_class; ?>" href="<?php echo $edit_with_oxygen_link["url"] ?>" title="<?php echo $edit_with_oxygen_link["text"] ?>">
		<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/Oxygen-O.png'/>
	</a>
	<?php if (isset($screen->id)&&($screen->id=="post"||$screen->id=="page"||$screen->id=="ct_template")) {?>
	<a class="oxygen-edit-mode-button-non-gutenberg page-title-action <?php echo $extra_class; ?>" href="<?php echo $edit_with_oxygen_link["url"] ?>" title="<?php echo $edit_with_oxygen_link["text"] ?>">
		<?php echo $edit_with_oxygen_link["text"]; ?>
	</a>
	<?php }
}

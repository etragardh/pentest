<?php

require_once("includes/ajax.php");

require_once("admin/client-control.php");

require_once("includes/tree-shortcodes.php");
require_once("includes/templates.php");
require_once("includes/wpml-support.php");
require_once("includes/cache.php");
require_once("includes/aos/aos.php");
require_once("includes/scripts/scripts.php");

require_once("includes/typekit/oxygen-typekit.php");
require_once("includes/selector-detector/selector-detector.php");
require_once("includes/acf/oxygen-acf-integration.php");
require_once("includes/metabox/oxygen-metabox-integration.php");
require_once("includes/toolset/oxygen-toolset.php");
require_once("includes/revisions.php");
require_once("includes/oxygen-connection.php");
require_once("includes/conditions.php");
require_once("includes/composite-elements.php");
require_once("includes/updates.php");

// init media queries sizes
$media_tablet_width = oxygen_vsb_get_breakpoint_width('tablet');
$media_phone_landscape_width = oxygen_vsb_get_breakpoint_width('phone-landscape');
$media_phone_portrait_width = oxygen_vsb_get_breakpoint_width('phone-portrait');

global $media_queries_list;
$media_queries_list = array (
	"default" 	=> array(
		"maxSize" 	=> "100%",
		"title" 	=> "Full Screen"
	),

	"page-width" 	=> array(
		"maxSize" 	=> "", // set when actually use
		"title" 	=> "Page container and below"
	),

	"tablet" => array(
		"maxSize" 	=> ($media_tablet_width - 1) . 'px',
		"title" 	=> "Less than {$media_tablet_width}px"
	),
	"phone-landscape" => array(
		"maxSize" 	=> ($media_phone_landscape_width - 1) . 'px',
		"title" 	=> "Less than {$media_phone_landscape_width}px"
	),
	"phone-portrait" => array(
		"maxSize" 	=> ($media_phone_portrait_width - 1) . 'px',
		"title" 	=> "Less than {$media_phone_portrait_width}px"
	),
);

global $media_queries_list_above;
$media_queries_list_above = array (
	"default" => array(
		"minSize" 	=> "100%",
		"title" 	=> "Full Screen"
	),

	"page-width" => array(
		"minSize" 	=> "", // set when actually use
		"title" 	=> "Above page container"
	),

	"tablet" => array(
		"minSize" 	=> $media_tablet_width . 'px',
		"title" 	=> "At or above {$media_tablet_width}px"
	),
	"phone-landscape" => array(
		"minSize" 	=> $media_phone_landscape_width . 'px',
		"title" 	=> "At or above {$media_phone_landscape_width}px"
	),
	"phone-portrait" => array(
		"minSize" 	=> $media_phone_portrait_width . 'px',
		"title" 	=> "At or above {$media_phone_portrait_width}px"
	),
);

require_once("api/elements-api.php");

function oxy_data_requests() {

	if ( !wp_doing_ajax() ) {
		return false;
	}

	if (isset($_REQUEST['oxygen_iframe']) && $_REQUEST['oxygen_iframe'] == 'true') {
		return true;
	}

	$data_actions = array(
		"oxy_get_components_templates",
		"oxy_load_elements_presets",
		"set_oxygen_edit_post_lock_transient",
		"oxy_get_google_fonts",
		"ct_get_svg_icon_sets",
	);

	if ( isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], $data_actions) ) {
		return true;
	}

	if ( isset( $_REQUEST['call_type'] ) && $_REQUEST['call_type'] == "get_items_from_source" ) {
		return true;
	}

	return false;
}

require_once("components/component.class.php");

// Instantanate elements with presets to make 'oxygen_vsb_element_presets_defaults' filter to work on AJAX
if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == "oxy_load_elements_presets" ) {
	include_once("components/classes/comments-list.class.php");
	include_once("components/classes/easy-posts.class.php");
	include_once("components/classes/menu-pro.class.php");
}

if ( isset( $_REQUEST['name'] ) && $_REQUEST['name'] == "composite-elements" ) {
	require_once("admin/updater/edd-updater-composite-elements.php");
}

require_once("admin/updater/edd-updater.php");
require_once("admin/updater/edd-updater-composite-elements.php");

if ( !oxy_data_requests() ) {

	require_once("admin/cpt-templates.php");
	require_once("admin/admin.php");
	require_once("admin/pages.php");
	require_once("admin/svg-icons.php");
	require_once("admin/import-export.php");

	require_once("signature.class.php");
	require_once("components/css-util.class.php");

	// Add components in certain order
	include_once("components/classes/section.class.php");
	include_once("components/classes/div-block.class.php");
	include_once("components/classes/new-columns.class.php");
	include_once("components/classes/headline.class.php");
	include_once("components/classes/text-block.class.php");
	include_once("components/classes/rich-text.class.php");
	include_once("components/classes/link-text.class.php");
	include_once("components/classes/link-wrapper.class.php");
	include_once("components/classes/link-button.class.php");
	include_once("components/classes/image.class.php");
	include_once("components/classes/video.class.php");
	include_once("components/classes/svg-icon.class.php");
	include_once("components/classes/fancy-icon.class.php");
	include_once("components/classes/code-block.class.php");
	include_once("components/classes/inner-content.class.php");
	include_once("components/classes/slide.class.php");
	include_once("components/classes/menu.class.php");
	include_once("components/classes/shortcode.class.php");
	include_once("components/classes/nestable-shortcode.class.php");
	include_once("components/classes/comments-list.class.php");
	include_once("components/classes/comment-form.class.php");
	include_once("components/classes/login-form.class.php");
	include_once("components/classes/search-form.class.php");
	include_once("components/classes/tabs-contents.class.php");
	include_once("components/classes/tab.class.php");
	include_once("components/classes/tab-content.class.php");
	include_once("components/classes/toolset-view.class.php");

	// Helpers
	include_once("components/classes/header.class.php");
	include_once("components/classes/header-row.class.php");
	include_once("components/classes/header-row-center.class.php");
	include_once("components/classes/header-row-left.class.php");
	include_once("components/classes/header-row-right.class.php");
	include_once("components/classes/social-icons.class.php");
	include_once("components/classes/testimonial.class.php");
	include_once("components/classes/icon-box.class.php");
	include_once("components/classes/pricing-box.class.php");
	include_once("components/classes/progress-bar.class.php");

	include_once("components/classes/easy-posts.class.php");

	include_once("components/classes/gallery.class.php");

	include_once("components/classes/slider.class.php");
	include_once("components/classes/tabs.class.php");
	include_once("components/classes/superbox.class.php");
	include_once("components/classes/toggle.class.php");

	include_once("components/classes/map.class.php");
	include_once("components/classes/soundcloud.class.php");
	include_once("components/classes/modal.class.php");

	// not shown in fundamentals
	include_once("components/classes/reusable.class.php");
	include_once("components/classes/selector.class.php");
	include_once("components/classes/span.class.php");
	include_once("components/classes/widget.class.php");
	include_once("components/classes/sidebar.class.php");

	include_once("includes/oxygen-dynamic-shortcodes.php");
	include_once("includes/oxygen-bloat-eliminator.php");

	include_once("components/classes/dynamic-list.class.php");
}

// New API Elements since 3.0+
include_once("components/classes/menu-pro.class.php");
include_once("components/classes/site-nav.class.php");
include_once("components/classes/shape-divider.class.php");

function oxygen_can_activate_builder_compression(){
    // If PHP version is below 7 and Asset CleanUp plugin is present, don't activate zlib compression
    return !( version_compare(PHP_VERSION, '7.1') < 0 && defined('WPACU_PLUGIN_VERSION') );
}

if ( isset( $_GET['ct_builder'] ) && $_GET['ct_builder'] && oxygen_can_activate_builder_compression() ) {
    // zlib output_compression automatically compresses the output buffer at the moment it's flushed.
    // Most server configurations support zlib compression, but it's not a problem if it doesn't.
    ini_set("zlib.output_compression", "On");
}


if ( isset( $_GET['debugger'] ) && $_GET['debugger'] ) {
	define("debugger", true);
}

add_action('admin_menu', 'oxygen_vsb_add_setup_wizard');

function oxygen_vsb_add_setup_wizard() {

	if(isset($_GET['page']) && sanitize_text_field($_GET['page']) == 'oxygen-vsb-setup') {
		add_dashboard_page( '', '', 'manage_options', 'oxygen-vsb-setup', 'oxygen_vsb_setup_wizard_content' );
		wp_enqueue_style( 'oxygen_vsb_setup_wizard_styles', CT_FW_URI . "/admin/setup_wizard.css");
	}
}

add_action( 'admin_notices', 'oxygen_vsb_admin_notice' ); 

function oxygen_vsb_admin_notice() {
    if( get_transient( 'oxygen-vsb-admin-notice-transient' ) ) {
        ?>
        <div class="updated notice is-dismissible">
            <p><?php echo get_transient( 'oxygen-vsb-admin-notice-transient' )?></p>
        </div>
        <?php
        /* Delete transient, only display this notice once. */
        delete_transient( 'oxygen-vsb-admin-notice-transient' );
    }

    if( get_transient( 'oxygen-vsb-admin-error-transient' ) ) {
        ?>
        <div class="updated error is-dismissible">
            <p><?php echo get_transient( 'oxygen-vsb-admin-error-transient' )?></p>
        </div>
        <?php
        /* Delete transient, only display this error once. */
        delete_transient( 'oxygen-vsb-admin-error-transient' );
    }
    
    if(get_transient('oxygen-vsb-enabled-shortcode-signing')) {
    	?>
		<div class="updated notice is-dismissible">
            <p><?php _e( 'Now that signature verification is enabled, you should re-sign all of your shortcodes.', 'component-theme' ); ?></p>
            <p><a href="<?php echo add_query_arg('page', 'oxygen_vsb_sign_shortcodes', get_admin_url().'admin.php');?>"><?php _e( 're-sign all of your shortcodes', 'component-theme' ); ?></a></p>
        </div>
    	<?php
    	delete_transient( 'oxygen-vsb-enabled-shortcode-signing' );	
    }

    $ver = (float)phpversion();
	
	if($ver < 5.6) {
		?>
		<div class="updated error is-dismissible">
            <p>Error: your PHP version must be 5.6 or above to use Oxygen. Please contact your web hosting provider.</p>

			<p>Multiple years have passed since the PHP Group ceased support for versions of PHP below 5.6. If your web hosting provider's default PHP version is still below 5.6, you should switch to a modern, reliable, and secure web host.</p>
        </div>

		<?php
	}

    
}



function oxygen_vsb_is_touched_install() {

	$touched = false;

	if(get_option('ct_components_classes')) {
		$touched = true;
	}
	if(!$touched && get_option('ct_custom_selectors')) {
		$touched = true;
	}
	
	if(!$touched && get_option('ct_global_settings')) {
		$touched = true;
	}
	
	if(!$touched && get_option('ct_style_folders')) {
		$touched = true;
	}
	
	if(!$touched && get_option('ct_style_sets')) {
		$touched = true;
	}
	
	if(!$touched && get_option('ct_style_sheets')) {
		$touched = true;
	}

	return $touched;
	
}

function oxygen_vsb_setup_wizard_content() {
	global $ct_source_sites;
	?>
	<div id='oxygen-setup-wizard' class='oxygen-metabox'>
		<div class='inside'>

			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/oxygen-logo-white-2.png' class='oxygen-setup-wizard-logo' />

			<div class='oxygen-wizard-wrapper'>

				<div class='oxygen-wizard-title'>
					<h1><?php esc_html_e( 'Welcome to Oxygen.', 'component-theme' ); ?></h1>
					<h1><?php esc_html_e( 'Please choose an installation type.', 'component-theme' ); ?></h1>
				</div>

				<div class='oxygen-wizard-content'>
					
					<div class='oxygen-wizard-install-types'>

						<div class='oxygen-wizard-install-type'>
							<h4><?php esc_html_e( 'Premade Website', 'component-theme' ); ?></h4>
							<h2><?php esc_html_e( 'Recommended', 'component-theme' ); ?></h2>
							<p><?php esc_html_e( 'Load a complete, premade website from our design library, then customize.', 'component-theme' ); ?></p>
							<div class="oxygen-wizard-button-bar">
							<?php
								$browse_library = add_query_arg('page', 'ct_install_wiz', get_admin_url());
								$default_install = $browse_library;

								if(isset($ct_source_sites['atomic'])) {
									$default_install = add_query_arg('default', 'atomic', $default_install);
								}
							?>
								<a href="<?php echo $default_install;?>" class="oxygen-wizard-button"><?php esc_html_e( 'Default Install', 'component-theme' ); ?></a>
								<a href="<?php echo $browse_library;?>" class="oxygen-wizard-button"><?php esc_html_e( 'Browse Library &raquo;', 'component-theme' ); ?></a>
							</div>
						</div>

						<div class='oxygen-wizard-install-type'>
							<h4><?php esc_html_e( 'Blank Installation', 'component-theme' ); ?></h4>
							<h2><?php esc_html_e( 'For Pro Designers', 'component-theme' ); ?></h2>
							<p><?php esc_html_e( 'Start with a completely blank canvas and build something from scratch.', 'component-theme' ); ?></p>
							<a href="<?php echo esc_url( admin_url() ); ?>" class="oxygen-wizard-button"><?php esc_html_e( 'Blank Install', 'component-theme' ); ?></a>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>

	<?php
}

add_action('admin_init', 'oxygen_vsb_redirect_setup_wizard');

function oxygen_vsb_redirect_setup_wizard() {

	$isActivated = get_option('oxygen-vsb-activated');
	if(!$isActivated && get_transient('oxygen-vsb-just-activated') == '1') {
		update_option( 'oxygen_vsb_enable_signature_validation', "true" );
		delete_transient('oxygen-vsb-just-activated');
		add_option('oxygen-vsb-activated', true);
		wp_safe_redirect( admin_url( 'index.php?page=oxygen-vsb-setup' ) );
		exit;
	}
}

/**
 * Hook for addons to add fundamental components
 *
 * @since 1.4
 */
do_action("oxygen_after_add_components");


/**
 * Run plugin setup
 * 
 * @since 0.3.3
 * @author Ilya K.
 */

function ct_plugin_setup() {

	if(!get_option("oxygen_vsb_global_colors")) {
		global $oxygen_vsb_global_colors;
		$oxygen_vsb_global_colors = oxy_get_global_colors();

		add_option("oxygen_vsb_global_colors", $oxygen_vsb_global_colors);
	}

	/**
	 * Setup default SVG Set
	 * 
	 */
	
	$svg_sets_names = get_option("ct_svg_sets_names", array() );

	if ( empty( $svg_sets_names ) ) {

		$sets = array(
			"fontawesome" => "Font Awesome",
			"linearicons" => "Linearicons"
		);
		
		foreach ($sets as $key => $name) {
			
			// import default file	
			$file_content = file_get_contents( CT_FW_PATH . "/admin/includes/$key/symbol-defs.svg" );

			$xml = simplexml_load_string($file_content);

			foreach($xml->children() as $def) {
				if($def->getName() == 'defs') {

					foreach($def->children() as $symbol) {
						
						if($symbol->getName() == 'symbol') {
							$symbol['id'] = str_replace(' ', '', $name).$symbol['id'];
							
						}
					}
				}
				
			}

			$xml_string = $xml->asXML();
			$xml_array = str_split($xml_string, 800000);
			$svg_sets_names[$name] = count($xml_array);

			foreach ($xml_array as $id => $value) {
				$svg_sets[$name." ".$id] = $value;
			}
		}

		$svg_sets_old = get_option("ct_svg_sets", array() );

		// add sets saved before 3.8
		if (is_array($svg_sets_old)) {
			foreach ($svg_sets_old as $name => $set) {
				// it will be single part sets, so add a zero to the end and 1 as parts count
				$svg_sets[$name." 0"] = $set;
				$svg_sets_names[$name] = 1;
			}
		}

		if (is_array($svg_sets)) {
			foreach($svg_sets as $key => $set) {
				// save SVG sets to DB
				update_option("ct_svg_sets_".$key, $set, get_option("oxygen_options_autoload") );
			}
			update_option("ct_svg_sets_names", $svg_sets_names, get_option("oxygen_options_autoload") );
		}
	}
}
add_action('admin_init', 'ct_plugin_setup', 9);

/**
 * Gather all sets from various wp_options rows into one object
 * 
 * @since 3.8
 * @author Ilya K.
 */

function oxy_get_svg_sets() {
	
	$svg_sets_names = get_option("ct_svg_sets_names", array() );
	$svg_sets = array();

	foreach ($svg_sets_names as $set_name => $number_of_parts) {
		$svg_sets[$set_name] = "";
		for ($i=0; $i < $number_of_parts; $i++) { 
			$svg_sets[$set_name] .= get_option("ct_svg_sets_".$set_name." ".$i, true );
		}
	}

	return $svg_sets;
	
}


/**
 * Echo all components styles in one <style>
 * 
 * @since 0.1.6
 */

function ct_footer_styles_hook() {
	
	ob_start();
	do_action("ct_footer_styles");
	$ct_footer_css = ob_get_clean();

	if ( defined("SHOW_CT_BUILDER") && defined("OXYGEN_IFRAME") ) {
		echo "<style type=\"text/css\" id=\"ct-footer-css\">\r\n";
		echo $ct_footer_css;
		echo "</style>\r\n";
	}
}


function ct_wp_link_dialog() {
    require_once ABSPATH . "wp-includes/class-wp-editor.php";
	_WP_Editors::wp_link_dialog();
}


/**
 * Check if we are in builder mode
 * 
 * @since 0.1
 * @author Ilya K.
 */

function ct_is_show_builder() {

	// check if builder activated
    if ( isset( $_GET['ct_builder'] ) && $_GET['ct_builder'] ) {

		if ( !is_user_logged_in()) { 
		   auth_redirect();
		}
		
		if(!oxygen_vsb_current_user_can_access()) {
			wp_die(__('You do not have sufficient permissions to edit the layout', 'oxygen'));
		}

		define("SHOW_CT_BUILDER", true);

    	add_action("wp_footer", "ct_wp_link_dialog");
		add_action("wp_head", "ct_footer_styles_hook");
		
		add_filter("document_title_parts", "ct_builder_wp_title", 10, 1);
    }

    // check if we are in iframe
    if ( isset( $_GET['oxygen_iframe'] ) && $_GET['oxygen_iframe'] ) {
    	define("OXYGEN_IFRAME", true);
    }

    // good place do define global classes list
	global $oxygen_vsb_css_classes;
	$oxygen_vsb_css_classes = get_option("ct_components_classes", array());

	global $oxygen_vsb_global_colors;
	$oxygen_vsb_global_colors = oxy_get_global_colors();
}
add_action('init','ct_is_show_builder', 1 );


/**
 * Callback for 'document_title_parts' filter
 *
 * @since ?
 * @author ?
 */

function ct_builder_wp_title( $title = array() ) {
 	$title['title'] = __( 'Oxygen Visual Editor', 'component-theme' ).(isset($title['title'])?' - '.$title['title']:'');
    return $title;
}

/**
 * Check if user has rights to open this post/page in builder
 * 
 * @since 1.0.1
 * @author Ilya K.
 */

function ct_check_user_caps() {

	// check if builder activated
    if ( isset( $_GET['ct_builder'] ) && $_GET['ct_builder'] ) {

    	// check if user is logged in
    	if ( !is_user_logged_in() ) {
			auth_redirect();
		}
		
		global $post;

		// if user can edit this post
		if ( $post !== null && ! oxygen_vsb_current_user_can_access() ) {
			auth_redirect();
		}
    }
}
add_action('wp','ct_check_user_caps', 1 );

function ct_oxygen_admin_menu() {

	if (!oxygen_vsb_current_user_can_access()) {
		return;
	}

	if (is_oxygen_edit_post_locked()) {
		return;
	}

	//check if this post type is set to be ignored
	$post_type = get_post_type();
	$ignore = get_option('oxygen_vsb_ignore_post_type_'.$post_type, false);

	if($ignore == "true") {
		return;
	}

	global $wp_admin_bar, $wp_the_query;

	$post = $wp_the_query->get_queried_object();

	if(is_admin())
		return;

	if(!oxygen_vsb_current_user_can_access()) {
		return;
	}

	if ( is_object( $post ) && isset( $post->ID ) && get_post_meta( $post->ID, 'oxygen_lock_post_edit_mode', true )=="true" && oxygen_vsb_get_user_edit_mode() == "edit_only" ) {
        return;
    }

	$post_id = false;
	$template = false;
	$is_template = false;
	// get archive template
	if ( is_archive() || is_search() || is_404() || is_home() || is_front_page() ) {

		if ( is_front_page() ) {
			$post_id 	= get_option('page_on_front');
		}
		else if ( is_home() ) {
			$post_id 	= get_option('page_for_posts');
		}
		else 
		{
			$template 	= ct_get_archives_template();

			if($template) {
				$is_template = true;
			}
		}
	} 
	
	if($post_id || (!$template && is_singular())) {
		
		if($post_id == false)
			$post_id = $post->ID;

		$ct_other_template = get_post_meta( $post_id, "ct_other_template", true );
		
		$template = false;
		
		if(!empty($ct_other_template) && $ct_other_template > 0) { // no template is specified
			// try getting default template
			$template = get_post($ct_other_template);
		}
		elseif($ct_other_template != -1) { // try getting default template if not explicitly set to not use any template at all
			if(intval($post_id) == intval(get_option('page_on_front')) || intval($post_id) == intval(get_option('page_for_posts'))) {
				$template = ct_get_archives_template( $post_id );

				if(!$template) {  // if not template is set to apply to front page or blog posts page, then use the generic page template, as these are pages
					$template = ct_get_posts_template( $post_id );
				}
			}
			else {
				$template = ct_get_posts_template( $post_id );

			}
		}

		if($template) {
			$is_template = true;
		} else {
			$is_template = false;
		}

	} elseif(!$template) {

		$template 	= ct_get_archives_template();

		if($template) {
			$is_template = true;
		}
	}
	
	$contains_inner_content = false;
	if($is_template) {
		$json = get_post_meta( $template->ID, "ct_builder_json", true );
		if ( $json ) {
			$contains_inner_content = (strpos($json, '"name":"ct_inner_content"') !== false);
		}
		else {
			$shortcodes = get_post_meta( $template->ID, "ct_builder_shortcodes", true );
			
			if($shortcodes) {
				$contains_inner_content = (strpos($shortcodes, '[ct_inner_content') !== false);
			}
		}
	}

	if($is_template) {
		if(is_object($post)) {
			
			if ( isset( $post->ID ) ) {
				$json = get_post_meta($post->ID, 'ct_builder_json', true);
				$postShortcodes = get_post_meta($post->ID, 'ct_builder_shortcodes', true);
			}

			if($contains_inner_content && ($postShortcodes || oxygen_json_has_elements($json) )) {
				if (get_post_meta( $post->ID, 'oxygen_lock_post_edit_mode', true )=="true" && oxygen_vsb_get_user_edit_mode() == "edit_only") {
					return;
				}
				$wp_admin_bar->add_menu( array( 'id' => 'oxygen_admin_bar_menu', 'title' => __( 'Oxygen', 'component-theme' ), 'href' => FALSE ) );
				$wp_admin_bar->add_menu( array( 'id' => 'edit_post_template', 'parent' => 'oxygen_admin_bar_menu', 'title' => __( 'Edit with Oxygen', 'component-theme' ), 'href' => esc_url(ct_get_post_builder_link( $post->ID )).(($contains_inner_content)?'&ct_inner=true':'')) );
			}
			else {
				if (get_post_meta( $template->ID, 'oxygen_lock_post_edit_mode', true )=="true" && oxygen_vsb_get_user_edit_mode() == "edit_only") {
					return;
				}
				$wp_admin_bar->add_menu( array( 'id' => 'oxygen_admin_bar_menu', 'title' => __( 'Oxygen', 'component-theme' ), 'href' => FALSE ) );
				$wp_admin_bar->add_menu( array( 'id' => 'edit_template', 'parent' => 'oxygen_admin_bar_menu', 'title' => __( 'Edit '.$template->post_title.' Template', 'component-theme' ), 'href' => esc_url(get_edit_post_link( $template->ID )) ) );
			}
		}
	}
	else {
		if(is_object($post)) {
			if (get_post_meta( $post->ID, 'oxygen_lock_post_edit_mode', true )=="true" && oxygen_vsb_get_user_edit_mode() == "edit_only") {
				return;
			}
			$wp_admin_bar->add_menu( array( 'id' => 'oxygen_admin_bar_menu', 'title' => __( 'Oxygen', 'component-theme' ), 'href' => FALSE ) );
			$wp_admin_bar->add_menu( array( 'id' => 'edit_post_template', 'parent' => 'oxygen_admin_bar_menu', 'title' => __( 'Edit with Oxygen', 'component-theme' ), 'href' => esc_url(ct_get_post_builder_link( $post->ID ))) );
		}
	}

}

add_action( 'admin_bar_menu', 'ct_oxygen_admin_menu', 1000 );

/**
 * Set CT parameters to recognize on fronted and builder
 * 
 * @since 0.2.0
 * @author Ilya K.
 */

function ct_editing_template() {

    if ( get_post_type() == "ct_template" ) {

    	define("OXY_TEMPLATE_EDIT", true);
		
		// below returns nothing since 2.0 Do we need to remove this?
    	$template_type = get_post_meta( get_the_ID(), 'ct_template_type', true );

    	if ( $template_type != "reusable_part" ) {
    		define("CT_TEMPLATE_EDIT", true);	
    	}

    	if ( $template_type == "archive" ) {
    		define("CT_TEMPLATE_ARCHIVE_EDIT", true);	
    	}

    	if ( $template_type == "single_post" ) {
    		define("CT_TEMPLATE_SINGLE_EDIT", true);	
    	}
    }
}
add_action('wp','ct_editing_template', 1 );


/**
 * Get current request URL
 * 
 * @since ?
 * @author gagan goraya
 */

function ct_get_current_url($more_query) {

	$request_uri = '';

	$request = explode('?', $_SERVER["REQUEST_URI"]);

	if(isset($request[1])) {
		$request_uri = $_SERVER["REQUEST_URI"].'&'.$more_query;
	}
	else {
		$request_uri = $_SERVER["REQUEST_URI"].'?'.$more_query;	
	}

	//$pageURL = 'http';
	//if ((isset($_SERVER["HTTPS"]) && !empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"]) != 'off' ) ||
	//	(isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] == 443)) {
	//	$pageURL .= "s";
	//}
	//$pageURL .= "://";
	//if ($_SERVER["SERVER_PORT"] != "80") {
	//  $pageURL .= $_SERVER["HTTP_HOST"].":".$_SERVER["SERVER_PORT"].$request_uri;
	//} else {
	//  $pageURL .= $_SERVER["HTTP_HOST"].$request_uri;
	//}

	$pageURL = '//'.$_SERVER["HTTP_HOST"].$request_uri;
	
	return $pageURL;
}


/**
 * Include Scripts and Styles for frontend and builder
 * 
 * @since 0.1
 * @author Ilya K.
 */

function ct_enqueue_scripts() {

	// includes minified normalize.css and style.css files
	wp_enqueue_style("oxygen", CT_FW_URI . "/oxygen.css", array(), CT_VERSION );

	wp_enqueue_script("jquery");

	/**
	 * Add-on hook for scripts that should be displayed both frontend and builder
	 *
	 * @since 1.4
	 */
	do_action("oxygen_enqueue_scripts");


	if ( !defined("SHOW_CT_BUILDER") ) {

		// anything beyond this is for builder
		return;
	}

	// include Unslider
	wp_enqueue_style ( 'oxygen-unslider', 		CT_FW_URI . '/vendor/unslider/unslider.css');

	// Font Loader
	wp_enqueue_script("font-loader", "//ajax.googleapis.com/ajax/libs/webfont/1/webfont.js", array(), false, false);

	wp_enqueue_script('underscore');

	// WordPress Media
	if ( !defined("OXYGEN_IFRAME") ) {
		wp_enqueue_media();
	}

	// link manager
	wp_enqueue_script( 'wplink' );
	wp_enqueue_style( 'editor-buttons' );

	// add Gravity Forms if registered
	wp_enqueue_script( 'gform_gravityforms' );

	// FontAwesome
	wp_enqueue_style("font-awesome", "//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css", array(), '4.3.0');

	// AngularJS
	wp_enqueue_script("angular", 			CT_FW_URI . "/vendor/angular/angular.min.js", array(), '1.8.2', false);
	wp_enqueue_script("angular-animate", 	CT_FW_URI . "/vendor/angular/angular-animate.min.js", array(), '1.8.2', false);

	// Select2
    wp_enqueue_style ( 'select2', CT_FW_URI . "/vendor/select2/select2.min.css" );

	// fuse.js
	wp_enqueue_script("fuse", 					CT_FW_URI . "/vendor/fuse/fuse.min.js");
	
	if ( !defined("OXYGEN_IFRAME") ) {
		wp_enqueue_script( 'oxygen-codemirror-6', 		CT_FW_URI . '/vendor/codemirror6/editor.bundle.js', array(), false, false);
	}
	
	if(defined("debugger")) {
		wp_enqueue_script( 'oxygen-unslider', 		CT_FW_URI . '/vendor/unslider/unslider-min.js', array(), false, false);
		wp_enqueue_script( 'oxygen-event-move', 	CT_FW_URI . '/vendor/unslider/jquery.event.move.js', array(), false, false);
		wp_enqueue_script( 'oxygen-event-swipe', 	CT_FW_URI . '/vendor/unslider/jquery.event.swipe.js', array(), false, false);
		wp_enqueue_script( 'select2', CT_FW_URI . "/vendor/select2/select2.full.min.js", array( 'jquery' ), false, false );
		wp_enqueue_script("ct-common-directives",			CT_FW_URI . "/angular/common.directives.js", array(), CT_VERSION);
	}
	else {
		wp_enqueue_script("ct-common-directives",			CT_FW_URI . "/angular/common.directives.min.js", array(), CT_VERSION);
	}

	if (oxygen_vsb_current_user_can_full_access()) {
		wp_enqueue_script("ct-dynamic-data-directive",			CT_FW_URI . "/angular/dynamic-data.directive.js", array(), CT_VERSION);
	}

	// iframe files
	if ( defined("OXYGEN_IFRAME") ) {

		wp_enqueue_style ("ct-iframe", 						CT_FW_URI . "/toolbar/UI/css/iframe.css");

		if(defined("debugger")) {
			// drag-and-drop-lists library
			wp_enqueue_script("ct-angular-dragdroplist", CT_FW_URI . "/vendor/angular-drag-and-drop-lists/angular-drag-and-drop-lists.min.js");

			// Libraries for Undo-Redo
			wp_enqueue_script("ct-deep-diff",   CT_FW_URI . "/vendor/deep-diff/deep-diff.js", array(), CT_VERSION);
			wp_enqueue_script("ct-object-path", CT_FW_URI . "/vendor/object-path/object-path.js", array(), CT_VERSION);
			wp_enqueue_script("ct-undomanager", CT_FW_URI . "/vendor/undomanager/undomanager.js", array(), CT_VERSION);

			wp_enqueue_script("ct-angular-main", 				CT_FW_URI . "/angular/controllers/controller.main.js", 			array(), CT_VERSION);
			wp_enqueue_script("ct-angular-tree", 				CT_FW_URI . "/angular/controllers/controller.tree.js", 			array(), CT_VERSION);
			wp_enqueue_script("ct-angular-states", 				CT_FW_URI . "/angular/controllers/controller.states.js", 		array(), CT_VERSION);
			wp_enqueue_script("ct-angular-navigation", 			CT_FW_URI . "/angular/controllers/controller.navigation.js", 	array(), CT_VERSION);
			wp_enqueue_script("ct-angular-columns", 			CT_FW_URI . "/angular/controllers/controller.columns.js", 		array(), CT_VERSION);
			wp_enqueue_script("ct-angular-ajax", 				CT_FW_URI . "/angular/controllers/controller.ajax.js", 			array(), CT_VERSION);
			wp_enqueue_script("ct-angular-header-builder", 		CT_FW_URI . "/angular/controllers/controller.header.js",		array(), CT_VERSION);
			wp_enqueue_script("ct-angular-classes", 			CT_FW_URI . "/angular/controllers/controller.classes.js", 		array(), CT_VERSION);
			wp_enqueue_script("ct-angular-options", 			CT_FW_URI . "/angular/controllers/controller.options.js", 		array(), CT_VERSION);
			wp_enqueue_script("ct-angular-presets", 			CT_FW_URI . "/angular/controllers/controller.presets.js", 		array(), CT_VERSION);
			wp_enqueue_script("ct-angular-conditions", 			CT_FW_URI . "/angular/controllers/controller.conditions.js", 	array(), CT_VERSION);
			wp_enqueue_script("ct-angular-fonts", 				CT_FW_URI . "/angular/controllers/controller.fonts.js", 		array(), CT_VERSION);
			wp_enqueue_script("ct-angular-svg", 				CT_FW_URI . "/angular/controllers/controller.svg.js", 			array(), CT_VERSION);
			wp_enqueue_script("ct-angular-css",					CT_FW_URI . "/angular/controllers/controller.css.js", 			array(), CT_VERSION);
			wp_enqueue_script("ct-angular-templates",			CT_FW_URI . "/angular/controllers/controller.templates.js", 	array(), CT_VERSION);
			wp_enqueue_script("ct-angular-media-queries",		CT_FW_URI . "/angular/controllers/controller.media-queries.js", array(), CT_VERSION);
			wp_enqueue_script("ct-angular-api",					CT_FW_URI . "/angular/controllers/controller.api.js", 			array(), CT_VERSION);
			wp_enqueue_script("ct-angular-drag-drop",			CT_FW_URI . "/angular/controllers/controller.dragdroplists.js", array(), CT_VERSION);
			wp_enqueue_script("ct-angular-undo-redo",			CT_FW_URI . "/angular/controllers/controller.undoredo.js", 		array(), CT_VERSION);
			wp_enqueue_script("ct-angular-autounits",			CT_FW_URI . "/angular/controllers/controller.autounits.js", 	array(), CT_VERSION);
			wp_enqueue_script("ct-angular-shortcuts",			CT_FW_URI . "/angular/controllers/controller.shortcuts.js", 	array(), CT_VERSION);
			wp_enqueue_script("ct-angular-copypaste",			CT_FW_URI . "/angular/controllers/controller.copypaste.js", 	array(), CT_VERSION);
			wp_enqueue_script("ct-angular-directives",			CT_FW_URI . "/angular/builder.directives.js", 					array(), CT_VERSION);
			wp_enqueue_script("ct-angular-slider-directive", 	CT_FW_URI . "/angular/slider.directive.js",						array(), CT_VERSION);
		}
		else {
			wp_enqueue_script("ct-angular-main", 				CT_FW_URI . "/angular/controllers/controller.main.min.js", 			array(), CT_VERSION);	
			wp_enqueue_script("ct-angular-drag-drop",			CT_FW_URI . "/angular/controllers/iframe.min.js", array(), CT_VERSION);
		}
		
		// wp_enqueue_script("ct-angular-directives",			CT_FW_URI . "/angular/builder.directives.js", 					array(), CT_VERSION);
		// wp_enqueue_script("ct-angular-slider-directive", 	CT_FW_URI . "/angular/slider.directive.js",						array(), CT_VERSION);


		wp_enqueue_script( 'oxygen-aos', 	CT_FW_URI . '/vendor/aos/aos.js', array(), CT_VERSION);
		wp_enqueue_style ( 'oxygen-aos', 	CT_FW_URI . '/vendor/aos/aos.css');

		wp_enqueue_script("oxy-emmet",	CT_FW_URI . "/vendor/@emmetio/emmet.js", array(), CT_VERSION, true);

		/**
		 * Add-on hook
		 *
		 * @since 1.4
		 */
		do_action("oxygen_enqueue_iframe_scripts");
	}
	else {
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_script(
	        'iris',
	        admin_url( 'js/iris.min.js' ),
	        array( 'jquery-ui-draggable', 'jquery-ui-slider', 'media-editor' ),
	        false,
	        1
	    );
	    
		wp_enqueue_script(
	        'wp-color-picker',
	        admin_url( 'js/color-picker.min.js' ),
	        array( 'iris' ),
	        false,
	        1
	    );
	    wp_enqueue_script(
	        'ct-color-picker',
	        CT_FW_URI . '/vendor/alpha-color-picker/color-picker.min.js',
	        array( 'iris', 'wp-color-picker' ),
	        false,
	        1
	    );

	    $colorpicker_l10n = array(
	        'clear' => __( 'Clear' ),
	        'defaultString' => __( 'Default' ),
	        'pick' => __( 'Select Color' ),
	        'current' => __( 'Current Color' ),
	    );
	    wp_localize_script( 'ct-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );

	    wp_enqueue_script(
			'alpha-color-picker',
			CT_FW_URI . '/vendor/alpha-color-picker/alpha-color-picker.js', // Update to where you put the file.
			array( 'jquery', 'ct-color-picker' ), // You must include these here.
			null,
			true
		);

		wp_enqueue_style(
			'alpha-color-picker',
			CT_FW_URI . '/vendor/alpha-color-picker/alpha-color-picker.css', // Update to where you put the file.
			array( 'wp-color-picker' ) // You must include these here.
		);

		wp_enqueue_script(
			'popper',
			CT_FW_URI . '/vendor/tippy/popper.min.js', 
			null,
			true
		);

		wp_enqueue_script(
			'tippy',
			CT_FW_URI . '/vendor/tippy/tippy-bundle.umd.js', 
			null,
			true
		);

		if(defined("debugger")) {
			wp_enqueue_script("ct-angular-ui", 			CT_FW_URI . "/angular/controllers/controller.ui.js", array('alpha-color-picker'), CT_VERSION, false);
			wp_enqueue_script("ct-slider-controller", 	CT_FW_URI . "/angular/controllers/controller.slider.js", 		array(), CT_VERSION, false);
			// jQuery menu aim to be used in the library add plus
			wp_enqueue_script('ct-jquery-menu-aim', CT_FW_URI . "/vendor/jquery-menu-aim/jquery.menu-aim.js", array('jquery'), CT_VERSION, false);
		}
		else {
			wp_enqueue_script("ct-angular-ui", 			CT_FW_URI . "/angular/controllers/ui.min.js", array('alpha-color-picker'), CT_VERSION);
		}

		wp_enqueue_script("oxy-structure-pane",	CT_FW_URI . "/toolbar/UI/js/structure-pane.js", array(), CT_VERSION, true);

		/**
		 * Add-on hook
		 *
		 * @since 1.4
		 */
		do_action("oxygen_enqueue_ui_scripts");
	}

	$options = oxy_get_ajax_vars();

	wp_localize_script( "ct-angular-main", 'CtBuilderAjax', $options);
	wp_localize_script( "ct-angular-ui", 'CtBuilderAjax', $options);
	//wp_localize_script( "wplink", 'ajaxurl', $options['ajaxUrl']);
}
add_action( 'wp_enqueue_scripts', 'ct_enqueue_scripts' );


function oxy_get_ajax_vars() {

	// Add some variables needed for AJAX requests
	global $post;
	global $wp_query, $wp_the_query;

	$post_to_edit = oxy_edit_post($post);

	$template_title = "";
	$original_post_id = 0;
	if ($post_to_edit['post_id'] !== $post->ID) {
		$original_post_id = $post->ID;
		$post = get_post( $post_to_edit['post_id'], OBJECT );
		setup_postdata( $post );
		// update $wp_the_query cause it is used in WP_Admin_Bar we utilize to get correct WP Admin link
		$wp_the_query = new WP_Query(array(
								'p'         => $post_to_edit['post_id'],
								'post_type' => 'ct_template'
							));
    	define("OXY_TEMPLATE_EDIT", true);
		define("CT_TEMPLATE_EDIT", true);
		$template_title = $post->post_title;
		// look for a parent template
		$post_to_edit = oxy_edit_post($post);
	}

	$options = array ( 
		'builderLink'	=> $post_to_edit['link'],
		'templateID'	=> $post_to_edit['post_id'],
		'ajaxUrl' 		=> admin_url( 'admin-ajax.php' ),
		'permalink' 	=> get_permalink(),
		'frontendURL' 	=> get_permalink(),
		'query' 		=> $wp_query->query,
		'googleMapsAPIKey' => get_option('oxygen_vsb_google_maps_api_key', ''),
		'templateTitle' => $template_title ? $template_title : "",
		'originalPostID' => $original_post_id ? $original_post_id : "",
	);

	$options = apply_filters('oxygen_vsb_builder_options', $options);

	// verify http vs https for hosts that force https
	$siteurl = get_option('siteurl');
	$is_https = (strpos($siteurl, "https://") === 0) ? "1" : "0";

	if ($is_https==="0") {
		$options['frontendURL'] = str_replace("https://", "http://", $options['frontendURL']);
	}

	/* Load the admin bar class code ready for instantiation */
	require_once( ABSPATH . WPINC . '/class-wp-admin-bar.php' );
	$admin_bar_class = apply_filters( 'wp_admin_bar_class', 'WP_Admin_Bar' );
	if ( class_exists( $admin_bar_class ) ) {
		$admin_bar = new $admin_bar_class;
		wp_admin_bar_edit_menu($admin_bar);
		$options['adminURL'] = $admin_bar->get_node('edit')->href;
	}
	else {
		$options['adminURL'] = admin_url();
	}

	// make ajax urls (used to render stuff in builder) protocol agnostic
	$options['ajaxUrl'] 	= str_replace(array('http://','https://'), "//", $options['ajaxUrl']);
	$options['permalink'] 	= str_replace(array('http://','https://'), "//", $options['permalink']);
	
	if($post) {
		$postid = $post->ID;

		if (is_front_page()) {
			$postid 		= get_option('page_on_front');
		}
		else if(is_home()) {
			$postid 		= get_option('page_for_posts');
		}

		$nonce = wp_create_nonce( 'oxygen-nonce-' . $postid );

		$options['postId'] 	= $postid;
		$options['nonce'] 	= $nonce;
	}

	if ( defined("OXY_TEMPLATE_EDIT") ) {
		$options["oxyTemplate"] = true;
	}

    if ( defined("OXY_TEMPLATE_EDIT") && !defined("CT_TEMPLATE_EDIT") ) {
        $options["oxyReusable"] = true;
    }

	// below 3 constanst never defined since 2.0
	if ( defined("CT_TEMPLATE_EDIT") ) {
		$options["ctTemplate"] = true;
	}

	if ( defined("CT_TEMPLATE_ARCHIVE_EDIT") ) {
		$options["ctTemplateArchive"] = true;
	}

	if ( defined("CT_TEMPLATE_SINGLE_EDIT") ) {
		$options["ctTemplateSingle"] = true;
	}

	$options["ctSiteUrl"] 			= get_home_url();
	$options["oxyFrameworkURI"] 	= CT_FW_URI;

	global $ct_component_categories;
	if ( isset($ct_component_categories) && $post && $post->post_type != 'oxy_user_library' ) {
		$options["componentCategories"] = $ct_component_categories;
	}

	global $oxygen_vsb_classic_designsets;
	if(isset($oxygen_vsb_classic_designsets)) {
		$options["classicDesignsets"] = $oxygen_vsb_classic_designsets;
	}

	// provide the meta keys to the builder
	global $wpdb, $oxygen_meta_keys;

	$query = "
        SELECT DISTINCT($wpdb->postmeta.meta_key) 
        FROM $wpdb->postmeta WHERE $wpdb->postmeta.meta_key NOT LIKE('\_%');
    ";

    $meta = $wpdb->get_results($query);

	if(is_array($meta)) {

		function oxygen_return_meta_keys($val) {
			return $val->meta_key;
		}

		$oxygen_meta_keys = array_map('oxygen_return_meta_keys', $meta);
	}

	$options["oxygenMetaKeys"] = $oxygen_meta_keys;

	// add taxonomies list
	$options["taxonomies"] = get_taxonomies();

	// tabs elements
	$options["componentsWithTabs"] = apply_filters("oxygen_component_with_tabs", array());

	// access level
	if (oxygen_vsb_current_user_can_full_access()) {
		$options["userCanFullAccess"] = "true";
	}
	else {
		$user_edit_mode = oxygen_vsb_get_user_edit_mode();
		if ($user_edit_mode === "edit_only" ) {
			$options["userEditOnly"] = "true";
		}
	}

	if (oxygen_vsb_user_can_drag_n_drop()) {
		$options["userCanDragNDrop"] = "true";
	}
	
	/**
	 * Filter to pass things to Oxygen builder Angular core
	 *
	 * @author Ilya K.
	 * @since 3.0
	 */

	$options = apply_filters("oxygen_builder_options", $options);

	// shortcode fixer var
	$options['fixShortcodes'] = isset( $_GET['fix_shortcodes'] ) && $_GET['fix_shortcodes'] == 'true' ? true : false;

	return $options;
}


function oxy_load_ajax_vars() {

	if ( isset( $_GET['action'] ) && $_GET['action'] === 'oxy_load_ajax_vars' ) {
		echo json_encode(oxy_get_ajax_vars());
		die();
	}
}
add_action( 'template_redirect', 'oxy_load_ajax_vars', 9 );


/**
 * Output all Oxygen generated styles: number of cached CSS files or dynamic xlink
 *
 * @since 2.2.1
 * @author Ilya K.
 */

function oxy_print_cached_css() {

	//If the post has the meta key 'tcb2_ready' from the Thrive Leads plugin, don't run the below code - this allows the Thrive builder to load instead
	if ( 
		( !empty( $_GET['tve'] ) && $_GET['tve'] === 'true' ) 
		||
		( get_post_meta ( get_the_ID(), 'tcb2_ready', true ) && 
		  in_array('thrive-leads/thrive-leads.php', apply_filters('active_plugins', get_option('active_plugins') ) ) 
		) 
		) {
		return;
	}

	if ( !defined("SHOW_CT_BUILDER") ) {

		global $wp_current_filter;
		// remove 'wp_head' from current filters
		array_pop( $wp_current_filter );
		// push fake filter name to be popped later instead of actual 'wp_head'
		array_push( $wp_current_filter, "wp_head_fake");

		global $oxygen_vsb_css_styles;
		$oxygen_vsb_css_styles = new WP_Styles;

		do_action("ct_builder_start");
		do_action("ct_builder_end");

		/**
		 * Add-on hook
		 *
		 * @since 1.4
		 */
		do_action("oxygen_enqueue_frontend_scripts");

		// check whether to load universal css or not
		if ( get_option("oxygen_vsb_universal_css_cache")=='true' && get_option("oxygen_vsb_universal_css_cache_success")==true 
			 // TODO: check if there are other cases that may load universal CSS into builder
			 && (!isset($_REQUEST['action']) || stripslashes($_REQUEST['action']) !== 'ct_render_widget') ) {
			
			$universal_css_url = get_option('oxygen_vsb_universal_css_url');
			$universal_css_url = add_query_arg("cache", get_option("oxygen_vsb_last_save_time"), $universal_css_url);

			if(is_admin()) $universal_css_url = str_replace("universal.css", "universal_admin.css", $universal_css_url);

			// check if to load dynamic xlink or cached CSS files
			if (!oxygen_vsb_load_cached_css_files()) {
				$oxygen_vsb_css_styles->add("oxygen-styles", ct_get_current_url('xlink=css&nouniversal=true') );
				$oxygen_vsb_css_styles->enqueue(array('oxygen-styles'));
			}

			$oxygen_vsb_css_styles->add("oxygen-universal-styles",  $universal_css_url);
			$oxygen_vsb_css_styles->enqueue(array('oxygen-universal-styles'));
		}
		else {
			// check if to load dynamic xlink or cached CSS files
			if (!oxygen_vsb_load_cached_css_files()) {
				$oxygen_vsb_css_styles->add("oxygen-styles", ct_get_current_url( 'xlink=css' ) );
				$oxygen_vsb_css_styles->enqueue(array('oxygen-styles'));
			}
		}

		// output styles
		$oxygen_vsb_css_styles->do_items();
	}
}
add_action( 'wp_head', 'oxy_print_cached_css', 999999 );


/**
 * Way to add custom CSS styles for styling iframe
 * 
 * @author Ilya K.
 * @since 2.4
 */

function oxygen_vsb_iframe_styles() {

	if ( !defined("OXYGEN_IFRAME") ) {
		return;
	}

	$iframe_css = apply_filters("oxygen_iframe_styles", "");

	echo $iframe_css;
}
add_action( 'wp_head', 'oxygen_vsb_iframe_styles' );


/**
 * Init
 * 
 * @since 0.2.5
 */

function ct_init() {

	// check if builder activated
    if ( defined("SHOW_CT_BUILDER") ) {
    	add_action("ct_builder_ng_init", "ct_init_default_values");
    	add_action("ct_builder_ng_init", "ct_init_not_css_options");
    	add_action("ct_builder_ng_init", "ct_init_options_white_list");
    	add_action("ct_builder_ng_init", "ct_init_allowed_empty_options_list");
    	add_action("ct_builder_ng_init", "ct_init_nice_names");
    	add_action("ct_builder_ng_init", "ct_init_settings");
    	add_action("ct_builder_ng_init", "ct_init_components_classses");
    	add_action("ct_builder_ng_init", "ct_init_custom_selectors");
    	add_action("ct_builder_ng_init", "ct_init_style_sheets");
    	add_action("ct_builder_ng_init", "ct_init_api_components");
    	add_action("ct_builder_ng_init", "ct_init_folders");
    	add_action("ct_builder_ng_init", "ct_init_elegant_custom_fonts");
    	add_action("ct_builder_ng_init", "ct_init_global_colors");
    	add_action("ct_builder_ng_init", "ct_init_element_presets");
    	add_action("ct_builder_ng_init", "ct_init_undo_redo");
    	add_action("ct_builder_ng_init", "ct_init_google_fonts");
    	add_action("ct_builder_ng_init", "ct_init_user_enabled_elements");
    	add_action("ct_builder_ng_init", "ct_init_codemirror_theme");
    	
    	add_action("ct_builder_ng_init", "ct_components_tree_init", 100 );
    	
    	if(class_exists('ACF')) {
    		add_action("ct_builder_ng_init", "ct_acf_repeater_data");
    	}

    	// Include Toolbar
    	if ( !defined("OXYGEN_IFRAME") ) {
			require_once("toolbar/toolbar.class.php");
    	}
    } else {
	    // user is attempting to see a post old version (revision)
	    if( isset($_REQUEST['oxy_preview_revision']) && !oxygen_vsb_current_user_can_access() ) {
		    unset( $_REQUEST['oxy_preview_revision'] );
	    }
    }
}
add_action('init','ct_init', 2);


/**
 * Get list of all components
 *
 * @since 0.4.0
 * @author Ilya K.
 */

function ct_init_api_components() {

	global $experimental_components;

	// $components = htmlspecialchars( json_encode( $components ) );
	$experimental_components = htmlspecialchars( json_encode( $experimental_components ) );
	echo "experimental_components=$experimental_components;";
}


/**
 * Make folders structure availbale on frontend
 *
 * @since 0.4.0
 */

function ct_init_folders() {

	global $oxygen_add_plus;

	$output = json_encode( $oxygen_add_plus );
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "folders=$output;";
}


/**
 * Make folders structure availbale on frontend
 *
 * @since 0.4.0
 */

function ct_init_elegant_custom_fonts() {

	if (version_compare(PHP_VERSION, '8.0.0') >= 0 && class_exists('ECF_Plugin')) {
		$rm = new \ReflectionMethod('ECF_Plugin', 'get_font_families');
		if (!$rm->isStatic()) {
			return;
		}
	}

	if (class_exists('ECF_Plugin')) {
		$font_family_list = ECF_Plugin::get_font_families();
	} else {
		$font_family_list = "";
	}

	$output = json_encode( $font_family_list );
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "elegantCustomFonts=$output;";
}


/**
 * Output Global colors
 *
 * @since 2.1
 */

function ct_init_global_colors() {

	global $oxygen_vsb_global_colors;

	$global_colors = $oxygen_vsb_global_colors;

	$output = json_encode( $global_colors );
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "globalColorSets=$output;";
}


/**
 * Keep it for all the versions above 3.2!
 * 
 * Clear out old default presets by 'name'
 */

function oxygen_vsb_presets_update_3_3() {

	$defaults = apply_filters("oxygen_vsb_element_presets_defaults", array());
	$presets = get_option("oxygen_vsb_element_presets", $defaults);

	if ( !get_option("oxygen_vsb_element_presets") ) {
		add_option("oxygen_vsb_presets_updated_3_3", true);
		return;
	};

    if ( !get_option("oxygen_vsb_presets_updated_3_3") ) {

	    $default_pro_menu_presets_3_2 = array();

	    include(CT_FW_PATH."/components/classes/menu-pro/menu-pro-default-presets_3_2.php");

	    foreach ($default_pro_menu_presets_3_2 as $element_name => $default_presets) {
			foreach ($default_presets as $key => $default_preset) {
				$index = array_search($default_preset, $presets[$element_name]);
				if ($index !== false) {
					unset($presets[$element_name][$index]);
				}
			}
			// re-index array to keep JSON clean from indexes and not overwrite other presets
			$presets[$element_name] = array_values($presets[$element_name]);
		}

	    // merge latest presets
	    $presets = array_merge_recursive($defaults, $presets);
		update_option("oxygen_vsb_element_presets", $presets, get_option("oxygen_options_autoload"));
	    add_option("oxygen_vsb_presets_updated_3_3", true);
	}
}

function oxygen_vsb_presets_update_3_4() {

    if ( get_option("oxygen_vsb_presets_updated_3_4") ) {
    	return;
	}

	$defaults = apply_filters("oxygen_vsb_element_presets_defaults", array());
	$presets = get_option("oxygen_vsb_element_presets", $defaults);

	// Add old user's Easy Posts presets before Oxygen 3.4
	$old_presets = get_option("oxygen_vsb_easy_posts_templates", false);
    if (is_array($old_presets)) {
    	if (!isset($presets['oxy_posts_grid'])){
    		$presets['oxy_posts_grid'] = array();
    	}
    	foreach ($old_presets as $key => $preset) {
			$presets['oxy_posts_grid'][] = array(
				"name" => $preset['name'],
				"options" => array(
					"original" => array(
						"code-php" => base64_decode($preset['code_php']),
						"code-css" => base64_decode($preset['code_css'])
					)
				)
			);
		}
    }

    // Add old user's Comments List presets before Oxygen 3.4
    $old_presets = get_option("oxygen_vsb_comments_list_templates", array());
    if (is_array($old_presets)) {
	    if (!isset($presets['oxy_comments'])) {
			$presets['oxy_comments'] = array();
	    }
	    foreach ($old_presets as $key => $preset) {
			$presets['oxy_comments'][] = array(
				"name" => $preset['name'],
				"options" => array(
					"original" => array(
						"code-php" => base64_decode($preset['code_php']),
						"code-css" => base64_decode($preset['code_css'])
					)
				),
			);
		}
    }
	
	update_option("oxygen_vsb_element_presets", $presets, get_option("oxygen_options_autoload"));
	add_option("oxygen_vsb_presets_updated_3_4", true);
};

/**
 * Keep default presets up to date as it may change with new releases
 * 
 * @since 3.3
 * @author Ilya K.
 */

function oxygen_vsb_sync_default_presets() {

	// keep this function forever
	oxygen_vsb_presets_update_3_3();

	$defaults = apply_filters("oxygen_vsb_element_presets_defaults", array());
	$presets = get_option("oxygen_vsb_element_presets", $defaults);

    foreach ($defaults as $element_name => $default_presets) {
        foreach ($default_presets as $default_preset_key => $default_preset) {
			
			// defaults already exist for this element
			if (   isset($presets[$element_name]) && 
				is_array($presets[$element_name])) {             
	            
	            $default_preset_found = false;

	            foreach ($presets[$element_name] as $preset_key => $preset) {
	            	
	            	// use slug as unique identificator
	                if (isset($preset['slug']) && isset($default_preset['slug']) &&
	                	$preset['slug'] === $default_preset['slug']) {
	                    
	                    // update preset to latest
	                    $presets[$element_name][$preset_key] = $defaults[$element_name][$default_preset_key];

	                	$default_preset_found = true;
	                }
	            }

	            if (!$default_preset_found && isset($default_preset['slug'])) {
	            	array_push($presets[$element_name], $defaults[$element_name][$default_preset_key]);
	            }
	        }

	        // no defaults exist for this element, add entire defaults array
	        else {
	        	$presets[$element_name] = $defaults[$element_name];
	        }
        }
	}
	
	// Remove some presets
	$to_remove = array('timeline-template','timeline-template2','timeline-template3');
	if (isset($presets['oxy_posts_grid']) && is_array($presets['oxy_posts_grid'])) {
		foreach ($presets['oxy_posts_grid'] as $key => $preset) {
			if (isset($preset['slug']) && in_array($preset['slug'],$to_remove)) {
				unset($presets['oxy_posts_grid'][$key]);
			}
		}
		// re-index array to keep JSON clean from indexes
		$presets['oxy_posts_grid'] = array_values($presets['oxy_posts_grid']);
	}

    update_option("oxygen_vsb_element_presets", $presets, get_option("oxygen_options_autoload"));

	// keep this function forever
	oxygen_vsb_presets_update_3_4();
}


/**
 * Output Element presets
 *
 * @since 3.2
 */

function ct_init_element_presets() {

	// keep these functions forever
	oxygen_vsb_presets_update_3_3();
	oxygen_vsb_sync_default_presets();

	$defaults = apply_filters("oxygen_vsb_element_presets_defaults", array());
	$presets = get_option("oxygen_vsb_element_presets", $defaults);

	if ( !is_array($presets) ) {
		$presets = array();
	}

	$output = json_encode( $presets );
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "elementPresets=$output;";
}

function ct_init_undo_redo() {
    $limit = intval(get_option('oxygen_vsb_history_limit'));
    echo "historyLimit=$limit;";
}

function ct_recursive_repeater_data($field, $parent = false) {

	$repeaters = array();
	$sub_fields = array();
	if(isset($field['sub_fields'])) {
		foreach($field['sub_fields'] as $subkey => $subField) {
			if($subField['type'] == 'repeater') {
				$repeaters = array_merge($repeaters, ct_recursive_repeater_data($subField, $field['key']));
			} else {
				$sub_fields[] = $subField;
			}
		}
	}

	$repeaters[$field['key']] = array('label' => $field['label'], 'fields' => $sub_fields, 'parent' => $parent);

	return $repeaters;
}

function ct_acf_repeater_data() {

	$field_groups = acf_get_field_groups();
	$repeaters = array();
	
	foreach ( $field_groups as $field_group ) {
		$fields = acf_get_fields( $field_group );

		foreach($fields as $field) {
			
			if($field['type'] == 'repeater') {
				
				$repeaters = array_merge($repeaters, ct_recursive_repeater_data($field));

				
			}
		}

	}
	
	$output = json_encode($repeaters);
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "acfRepeaters=$output;";

}


/**
 * Output Google Fonts cache
 *
 * @since 2.1
 */

function ct_init_google_fonts() {

	$cache_enabled = get_option("oxygen_vsb_enable_google_fonts_cache", false);

	if ( !$cache_enabled ) {
		echo "googleFontsCache=false;";
	}
	else {
		// check if cache exist
		$google_fonts_cache = get_option("oxygen_vsb_google_fonts_cache", false);
		if ( $google_fonts_cache ) {
			echo "googleFontsCache=true;";
		}
		else {
			echo "googleFontsCache=false;";
		}
	}
}

function ct_init_codemirror_theme() {
	
	$codemirror_theme = get_option("oxygen_vsb_codemirror_theme", 'materialDark');
	$codemirror_wrap = get_option("oxygen_vsb_codemirror_wrap", 'false');

	$old_themes = ['default', 'oneDarkTheme', 'dracula', 'midnight', 'eclipse ']; // space in the 'eclipse ' was a typo, but don't remove it as people has 
	if (in_array($codemirror_theme, $old_themes)) {
		$codemirror_theme = 'materialDark';
	}

	echo "globalCodeMirrorTheme='$codemirror_theme';";
	echo "globalCodeMirrorWrap='$codemirror_wrap';";
}

/**
 * Get categories, pages, components
 *
 * @since 1.0.1
 * @author Ilya K.
 */

function ct_get_base() {

	if ( ! defined("SHOW_CT_BUILDER") ) {
		return;
	}
	
	global $oxygen_add_plus;
	global $experimental_components;
	global $ct_source_sites;

	$experimental_components = array();
	foreach($ct_source_sites as $key => $source) {

		$experimental_components[$key] = array(
			'id' => 0,
			'name' => $source['label'],
			'type' => 'folder',
			'fresh' => true,
			'items' => array()//$json_components
		);
	}

	$experimental = array();
	$experimental["id"]		= "experimental";
	$experimental["name"]	= "Design Sets";
	$experimental["children"] = $experimental_components;

	$libraryCats = array();
	$libraryCats["id"] 	 		= "categories";
	$libraryCats["name"] 		= "Categories";
	$libraryCats["children"] 	= array();
	$installedSet = false;
	$installedSetIndex = get_option('ct_last_installed_default_data', false);
	global $ct_source_sites;
	if($installedSetIndex && isset($ct_source_sites[$installedSetIndex])) {
	
        $installedSetLabel = $ct_source_sites[$installedSetIndex]['label'];
		ob_start();?>

			<div class="oxygen-add-section-subsection" ng-click="iframeScope.openLoadFolder('<?php echo sanitize_title($installedSetLabel) ;?>-0', '<?php echo esc_attr($installedSet) ;?>', true, $event)">
				<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/small-generic.svg" class="oxygen-add-section-subsection-icon">
				<?php echo esc_html($installedSetLabel) ;?>									<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/small-arrow.svg">
			</div>

		<?php

		$installedSet = array('key' => $installedSetIndex, 'code' => ob_get_clean());

	}

	$children =	array(
			"experimental" => $experimental,
			 );
	
	if(is_array($installedSet)) {
		if (!isset($installedSet['installedSet'])) {
			$installedSet['installedSet'] = '';
		}
		$children = array_merge(array($installedSet['installedSet'] => $installedSet), $children);
	}

	$oxygen_add_plus = array(
			"status" 	=> false,
			"library" => array(
							"name" 	=> "Library",
							"children" => $children));

}
add_action("wp", "ct_get_base");


/**
 *
 * 
 * @since 2.0
 * @author Gagan
 */

function ct_init_default_values() {

	$components = apply_filters( "ct_component_default_values", array() );

	$output = json_encode($components);
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "defaultValues = $output;";

	$components_defaults = apply_filters( "ct_component_default_params", array() );

	$components = array_map( function($value) {
		return [];
	}, $components_defaults);
	$components["all"] = [];

	$output = json_encode($components);
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "defaultOptions=$output;";
}


/**
 * Output array of all not CSS options for each component
 *
 * @since 0.3.2
 */

function ct_init_not_css_options() {

	$components = apply_filters( "ct_not_css_options", array() );

	$output = json_encode($components);
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "notCSSOptions = $output;";
}


/**
 * Output white list options to be used for media/states/clases 
 *
 * @since 2.0
 */

function ct_init_options_white_list() {

	$options = apply_filters( "oxy_options_white_list", CT_Component::$options_white_list );

	$defaults = apply_filters( "ct_component_default_params", array() );
	$all_defaults = call_user_func_array('array_merge', array_values($defaults));

	$options_with_units = array();
	foreach ($options as $option) {
		if (
			// option is not a unit itself
			strpos($option,"-unit")===false &&
			// has no unit for it added
		 	!in_array($option."-unit", $options) &&
			// unit actually exist, i.e. it is not a "color" or some other unitless option
			isset($all_defaults[$option."-unit"])
			) {
				$options_with_units[] = $option . "-unit";
		}
	}

	$options = array_merge($options_with_units, $options);

	$output = json_encode($options);
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "optionsWhiteList = $output;";

	$components = apply_filters( "oxy_options_white_list_no_media", CT_Component::$options_white_list_no_media );

	$output = json_encode($components);
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "optionsWhiteListNoMedia = $output;";
}


/**
 * Output options that can be be unset/empty
 *
 * @since 2.0
 */

function ct_init_allowed_empty_options_list() {

	$components = apply_filters( "oxy_allowed_empty_options_list", CT_Component::$allowed_empty_options_list );

	$output = json_encode($components);
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "allowedEmptyOptions=$output;";
}


/**
 * Pass Components Tree JSON to ng-init directive
 *
 * @since 0.1
 */

function ct_components_tree_init() {

	echo "init();";
}


/**
 * Output Components nice names
 *
 * @since 0.1.2
 */

function ct_init_nice_names() {

	$names = apply_filters( "ct_components_nice_names", array() );

	$names['root'] = "Root";

	$output = json_encode($names);
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "niceNames = $output;";
}


/**
 * Output Page and Global Settings
 *
 * @since 0.1.3
 */

function ct_init_settings() { 

	//update_post_meta( get_the_ID(), "ct_page_settings", array() );
	$post_meta = get_post_meta( get_the_ID(), "ct_page_settings", true );
	if (!is_array($post_meta)) {
		$post_meta = array();
	}
	$page_settings = array_replace_recursive( 
				array(
					"max-width" => "",
					"overlay-header-above" => "",
					"aos" => array(
						'type' 						=> '',
						'duration' 					=> '',
						'easing' 					=> '',
						'offset' 					=> '',
						'delay' 					=> '',
						'anchor-placement' 			=> '',
						'once' 						=> '',
						'mirror' 					=> '',
						'disable'					=> '',
					),
					"scripts" => array(
						'scroll_to_hash' 			=> '',
						'scroll_to_hash_time' 		=> '',
						'scroll_to_hash_offset' 	=> '',
					)
				),
				$post_meta
			);
	$output = json_encode( $page_settings, JSON_FORCE_OBJECT );
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "pageSettingsMeta = $output;";

	// template page settings
	$output = json_encode( ct_get_page_settings(true), JSON_FORCE_OBJECT );
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "pageSettings = $output;";

	// Global settings
	$output = json_encode(ct_get_global_settings());
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "globalSettings = $output;";

	// Global defaults
	$output = json_encode(ct_get_global_settings(true));
	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "globalSettingsDefaults = $output;";

	$disable_google_fonts = get_option("oxygen_vsb_disable_google_fonts");
	echo "disableGoogleFonts = '$disable_google_fonts';";

}


/**
 * Output CSS Classes
 *
 * @since 0.1.7
 */

function ct_init_components_classses() { 
	
	$classes = ct_get_components_classes();

	$output = json_encode( $classes, JSON_FORCE_OBJECT );

	$output = htmlspecialchars( $output, ENT_QUOTES );

	echo "classes = $output;";
}

function ct_get_components_classes($return_js = false) {

	global $oxygen_vsb_css_classes;

	$classes = $oxygen_vsb_css_classes;

	if ( ! is_array( $classes ) )
		return array();
	
	// base64_decode the custom-css and custom-js
	$classes = ct_base64_decode_selectors($classes, $return_js);

	return $classes;
}


/**
 * base64 decode classes and custom selectors custom ccs/js
 *
 * @since 1.3
 * @author Ilya/Gagan
 */

function ct_base64_decode_selectors($selectors, $return_js = false) {

	$selecotrs_js = array();

	foreach($selectors as $key => $class) {
		foreach($class as $statekey => $state) {
			if($statekey == 'media') {
				foreach($state as $bpkey => $bp) {
					foreach($bp as $bpstatekey => $bpstate) {
						if(isset($bpstate['custom-css']) && strpos($bpstate['custom-css'], ' ') === false && strpos($bpstate['custom-css'], ':') === false)
		  					$selectors[$key][$statekey][$bpkey][$bpstatekey]['custom-css'] = base64_decode($bpstate['custom-css']);
		  				if(isset($bpstate['custom-js'])) {
		  					if(strpos($bpstate['custom-js'], ' ') === false && strpos($bpstate['custom-js'], '(') === false)
		  						$selectors[$key][$statekey][$bpkey][$bpstatekey]['custom-js'] = base64_decode($bpstate['custom-js']);
		  					// output js to the footer
		  					$classes_js[implode("_", array($key, $statekey, $bpkey, $bpstatekey))] = $states[$key][$mediakey][$mediastatekey]['custom-js'];	
		  				}
					}
				}
			}
			else {
		  		if(isset($class[$statekey]['custom-css']) && strpos($class[$statekey]['custom-css'], ' ') === false && strpos($class[$statekey]['custom-css'], ':') === false)
		  			$selectors[$key][$statekey]['custom-css'] = base64_decode($class[$statekey]['custom-css']);
		  		if(isset($class[$statekey]['custom-js'])) {
		  			if(strpos($class[$statekey]['custom-js'], ' ') === false && strpos($class[$statekey]['custom-js'], '(') === false)
						$selectors[$key][$statekey]['custom-js'] = base64_decode($class[$statekey]['custom-js']);
		  			
		  			// output js to the footer
		  			$selecotrs_js[implode("_", array($key, $statekey))] = $selectors[$key][$statekey]['custom-js'];
		  		}
		  	}
	  	}
  	}

  	if($return_js)
  		return $selecotrs_js;
  	else
  		return $selectors;
}


/**
 * Init custom selectors styles
 *
 * @since 1.3
 */

function ct_init_custom_selectors() {
	
	//update_option( "ct_custom_selectors", array() );
	$selectors = get_option( "ct_custom_selectors", array() );

	// make sure this is an array if we have empty string saved somehow
	if ($selectors == "") {
		$selectors = array();
	}

	$selectors = ct_base64_decode_selectors($selectors);

	$selectors = json_encode( $selectors, JSON_FORCE_OBJECT );
	$selectors = htmlspecialchars( $selectors, ENT_QUOTES );
	
	echo "customSelectors = $selectors;";

	$style_sets = get_option( "ct_style_sets", array() );

	// make sure this is an array if we have empty string saved somehow
	if ($style_sets == "") {
		$style_sets = array();
	}

	// if it does not contain default style set, add it
	if(!isset($style_sets['Uncategorized Custom Selectors']) || !isset($style_sets['Uncategorized Custom Selectors']['key'])) {
		$style_sets['Uncategorized Custom Selectors'] = array(
			'key' => 'Uncategorized Custom Selectors'
		);
	}

	$style_sets = json_encode( $style_sets, JSON_FORCE_OBJECT );
	$style_sets = htmlspecialchars( $style_sets, ENT_QUOTES );

	echo "styleSets=$style_sets;";

	$style_folders = get_option( "ct_style_folders", array() );

	// make sure this is an array if we have empty string saved somehow
	if ($style_folders == "") {
		$style_folders = array();
	}

	$style_folders = json_encode( $style_folders, JSON_FORCE_OBJECT );
	$style_folders = htmlspecialchars( $style_folders, ENT_QUOTES );
	
	echo "styleFolders = $style_folders;";
}

/**
 * retreive shortcodes
 * Don't need to check for JSON inside this function.
 * Remove in some future release 
 *
 * @since 1.3
 */

function ct_template_shortcodes() {

	global $oxygen_vsb_css_files_to_load;
	if (!is_array($oxygen_vsb_css_files_to_load)){ 	
		$oxygen_vsb_css_files_to_load = array();
	}

	$post_id = false;
	$template = false;
	$is_template = false;
	// get archive template
	if ( is_archive() || is_search() || is_404() || is_home() || is_front_page() ) {

		if ( is_front_page() ) {
			$post_id 	= get_option('page_on_front');
		}
		else if ( is_home() ) {
			$post_id 	= get_option('page_for_posts');
		}
		else
		{
			$template 	= ct_get_archives_template();

			$shortcodes = $template?get_post_meta( $template->ID, "ct_builder_shortcodes", true ):false;

			if($template) {
				$is_template = true;
			}
		}
	} 
	//else
	// get single template
	if($post_id || (!$template && is_singular())) {
		// get post type
		if($post_id == false)
			$post_id = get_the_ID();

		$oxygen_vsb_css_files_to_load[] = $post_id;

		$ct_other_template = get_post_meta( $post_id, "ct_other_template", true );
		
		$template = false;
		
		if(!empty($ct_other_template) && $ct_other_template > 0) { // no template is specified
			// try getting default template
			$template = get_post($ct_other_template);
		}
		elseif($ct_other_template != -1) { // try getting default template if not explicitly set to not use any template at all
			if(intval($post_id) == intval(get_option('page_on_front')) || intval($post_id) == intval(get_option('page_for_posts'))) {
				$template = ct_get_archives_template( $post_id );
				if(!$template) {
					$template = ct_get_posts_template( $post_id );
				}
			}
			else {
				$template = ct_get_posts_template( $post_id );
			}
		}

		if($template) {
			$is_template = true;
		} else {
			// does not even have a default template
			// then use it as a standalone custom view
			if(isset($_REQUEST['oxy_preview_revision']) && is_numeric($_REQUEST['oxy_preview_revision'])) {
				$shortcodes = Oxygen_Revisions::get_post_meta_db( null, null, true, null, OBJECT, $_REQUEST['oxy_preview_revision'] )->meta_value;
			} else {
				$shortcodes = get_post_meta( $post_id, "ct_builder_shortcodes", true );
            }
			if( class_exists('Oxygen_Gutenberg') && get_post_meta( $post_id, 'ct_oxygenberg_full_page_block', true ) == '1' ) {
				$post = get_post($post_id);
				$shortcodes = do_blocks( $post->post_content );
			}
		}

	} elseif(!$template) {

		$template 	= ct_get_archives_template();
		$shortcodes = $template?get_post_meta( $template->ID, "ct_builder_shortcodes", true ):false;

		if($template) {
			$is_template = true;
		}
	}

	// if it is a template, traverse the family tree 
	if($is_template) {

		$tree = array();
		
		$templateID = $template->ID;

		// update global template var
		global $ct_template_id;
		$ct_template_id = $template->ID;

		$oxygen_vsb_css_files_to_load[] = get_the_ID();
		$oxygen_vsb_css_files_to_load[] = $ct_template_id;
		
		// in case, its a preview of a template using the given preview link, then enforce the usage of the template
		if(isset($_REQUEST['screenshot_template']) && is_numeric($_REQUEST['screenshot_template'])) {
			$templateID = intval($_REQUEST['screenshot_template']);
		}
		// the following also takes care of the shortcode signature validation
		$combinedCodes = oxygen_get_combined_shortcodes($templateID);

		$tree['children'] = $combinedCodes['content'];
	
		$shortcodes_json = json_encode($tree);
	
		$shortcodes = components_json_to_shortcodes($shortcodes_json);
	}

	// in case it is a request to generate a screenshot for a single component, then the rendered page should not be wrapped with the outer template
	if(!$is_template && isset($_REQUEST['render_component_screenshot']) && stripslashes($_REQUEST['render_component_screenshot']) == 'true' && isset($_REQUEST['selector'])) {
		
		$shortcodes = get_post_meta( $post_id, "ct_builder_shortcodes", true );
		
	}

	if($shortcodes)
		return $shortcodes;
	else
		return false;

}

/**
 * Don't need to check for JSON inside this function.
 * Remove in some future release 
 */

function oxygen_get_combined_shortcodes($template, $retainInnerContent = false) {

	if(isset($_REQUEST['oxy_preview_template_revision']) && is_numeric($_REQUEST['oxy_preview_template_revision'])) {
		$revision = Oxygen_Revisions::get_post_meta_db( $template, null, true, null, OBJECT, $_REQUEST['oxy_preview_template_revision'] );
		if ($revision && isset($revision->meta_value)) {
			$shortcodes = $revision->meta_value;
		}
		else {
			$shortcodes = "";
		}
	} else {
		$shortcodes = get_post_meta( $template, "ct_builder_shortcodes", true );
	}
	$shortcodes = parse_shortcodes($shortcodes, false);
	// does this template inherits another template
	$parent = get_post_meta( $template, "ct_parent_template", true);
	
	if($parent) {

		global $ct_parent_template_id;
		$ct_parent_template_id = $parent;

		global $oxygen_vsb_css_files_to_load;
		$oxygen_vsb_css_files_to_load[] = $parent;

		// embed $shortcodes inside parent's shortcodes
		// first get the parent's shortcodes
		$parent_shortcodes = oxygen_get_combined_shortcodes($parent); // this takes care of multilevels

		//$parent_shortcodes = parse_shortcodes( $parent_shortcodes ); // validity

		//recursively obfuscate_ids: ct_id and ct_parent of all elements in $parsed, also obfuscate_selectors
		$ctDepthParser = new CT_Depth_Parser();

		$prepared_outer_content = ct_prepare_outer_template($parent_shortcodes['content'], $ctDepthParser);
		
		$parent_shortcodes['content'] = $prepared_outer_content['content'];

		$container_id = $prepared_outer_content['container_id'];

		// REPLACE inner_content shortcode altogether with the inner components

		$parent_id = $prepared_outer_content['parent_id'];
		
		if(!empty($shortcodes['content'])) {
			$shortcodes['content'] = ct_prepare_inner_content($shortcodes['content'], $container_id, $ctDepthParser->getDepths());
			if($retainInnerContent) {
				$parent_shortcodes['content'] = ct_embed_inner_content($parent_shortcodes['content'], $shortcodes['content']);
			}
			else {
				$parent_shortcodes['content'] = ct_replace_inner_content($parent_shortcodes['content'], $shortcodes['content']);
			}
		}
		
		return $parent_shortcodes;
	}

	return $shortcodes;
}

/**
 * retreive shortcodes
 *
 * @since 1.3
 */

function ct_template_json() {

	global $oxygen_vsb_css_files_to_load;
	if (!is_array($oxygen_vsb_css_files_to_load)){ 	
		$oxygen_vsb_css_files_to_load = array();
	}

	$post_id = false;
	$template = false;
	$is_template = false;

	// get archive template
	if ( is_archive() || is_search() || is_404() || is_home() || is_front_page() ) {

		if ( is_front_page() ) {
			$post_id = get_option('page_on_front');
		}
		else if ( is_home() ) {
			$post_id = get_option('page_for_posts');
		}
		else {
			$template = ct_get_archives_template();
			$json = $template ? get_post_meta( $template->ID, "ct_builder_json", true ) : false;
			if ($template) {
				$is_template = true;
			}
		}
	} 

	// get single template
	if ( $post_id || (!$template && is_singular() ) ) {
		// get post type
		if ( $post_id == false ) {
			$post_id = get_the_ID();
		}

		$oxygen_vsb_css_files_to_load[] = $post_id;
		$ct_other_template = get_post_meta( $post_id, "ct_other_template", true );
		$template = false;
		
		if(!empty($ct_other_template) && $ct_other_template > 0) { // no template is specified
			// try getting default template
			$template = get_post($ct_other_template);
		}
		elseif($ct_other_template != -1) { // try getting default template if not explicitly set to not use any template at all
			if(intval($post_id) == intval(get_option('page_on_front')) || intval($post_id) == intval(get_option('page_for_posts'))) {
				$template = ct_get_archives_template( $post_id );
				if ( !$template ) {
					$template = ct_get_posts_template( $post_id );
				}
			}
			else {
				$template = ct_get_posts_template( $post_id );
			}
		}

		if($template) {
			$is_template = true;
		} else {
			// does not even have a default template
			// then use it as a standalone custom view
			if(isset($_REQUEST['oxy_preview_revision']) && is_numeric($_REQUEST['oxy_preview_revision'])) {
				$json = Oxygen_Revisions::get_post_meta_db( null, null, true, null, OBJECT, $_REQUEST['oxy_preview_revision'] )->meta_value;
				$json = oxygen_safe_convert_old_shortcodes_to_json($json);
			} else {
				$json = get_post_meta( $post_id, "ct_builder_json", true );
            }
			if( class_exists('Oxygen_Gutenberg') && get_post_meta( $post_id, 'ct_oxygenberg_full_page_block', true ) == '1' ) {
				$post = get_post($post_id);
				$json = do_blocks( $post->post_content );
			}
		}

	} elseif(!$template) {

		$template 	= ct_get_archives_template();
		$json = $template ? get_post_meta( $template->ID, "ct_builder_json", true ) : false;

		if($template) {
			$is_template = true;
		}
	}

	// if it is a template, traverse the family tree 
	if ($is_template) {

		$tree = array();
		$templateID = $template->ID;

		// update global template var
		global $ct_template_id;
		$ct_template_id = $template->ID;

		$oxygen_vsb_css_files_to_load[] = get_the_ID();
		$oxygen_vsb_css_files_to_load[] = $ct_template_id;
		
		// in case, its a preview of a template using the given preview link, then enforce the usage of the template
		if(isset($_REQUEST['screenshot_template']) && is_numeric($_REQUEST['screenshot_template'])) {
			$templateID = intval($_REQUEST['screenshot_template']);
		}
		// the following also takes care of the shortcode signature validation
		$combinedCodes = oxygen_get_combined_tree($templateID);
		$tree['children'] = $combinedCodes;
		$json = json_encode($tree);
	}

	// in case it is a request to generate a screenshot for a single component, then the rendered page should not be wrapped with the outer template
	if( !$is_template && isset($_REQUEST['render_component_screenshot']) && stripslashes($_REQUEST['render_component_screenshot']) == 'true' && isset($_REQUEST['selector'])) {
		$json = get_post_meta( $post_id, "ct_builder_json", true );
	}

	if ( $json ) {
		return $json;
	}
	else {
		return false;
	}
}

function oxygen_get_combined_tree($template_id, $retainInnerContent = false) {

	if(isset($_REQUEST['oxy_preview_template_revision']) && is_numeric($_REQUEST['oxy_preview_template_revision'])) {
		$revision = Oxygen_Revisions::get_post_meta_db( $template_id, null, true, null, OBJECT, $_REQUEST['oxy_preview_template_revision'] );
		if ($revision && $revision->meta_value) {
			$json = oxygen_safe_convert_old_shortcodes_to_json($revision->meta_value);
		}
		else {
			$json = array();
		}
	} else {
		$json = get_post_meta( $template_id, "ct_builder_json", true );
	}

	if ($json) {
		// already have a JSON saved
		$components_tree = json_decode($json, true);
	}
	else {
		// no JSON yet, create it from shortcodes on the fly
		$shortcodes = get_post_meta( $template_id, "ct_builder_shortcodes", true );
		$tree = parse_shortcodes($shortcodes, false, false);
		$tree['content'] = ct_base64_encode_decode_tree($tree['content'], true);
		$components_tree = array(
			'id' => 0,
			'name' => 'root',
			'depth' => 0,
			'children' => $tree['content']
		);
		update_post_meta($template_id, 'ct_builder_json', addslashes(json_encode($components_tree, JSON_UNESCAPED_UNICODE)));
	}

	// does this template inherits another template
	$parent_id = get_post_meta( $template_id, "ct_parent_template", true);
	
	if ($parent_id) {

		global $ct_parent_template_id;
		$ct_parent_template_id = $parent_id;

		global $oxygen_vsb_css_files_to_load;
		$oxygen_vsb_css_files_to_load[] = $parent_id;

		// embed $shortcodes inside parent's shortcodes
		// first get the parent's shortcodes
		$parent_tree = oxygen_get_combined_tree($parent_id); // this takes care of multilevels

		//recursively obfuscate_ids: ct_id and ct_parent of all elements in $parsed, also obfuscate_selectors
		$ctDepthParser = new CT_Depth_Parser();

		$prepared_outer_content = ct_prepare_outer_template_json($parent_tree['children'] ?? $parent_tree, $ctDepthParser);
		
		$parent_tree = $prepared_outer_content['content'];
		$container_id = $prepared_outer_content['container_id'];

		// REPLACE inner_content shortcode altogether with the inner components
		if($components_tree && count($components_tree['children']) > 0) {
			$components_tree = ct_prepare_inner_content($components_tree['children'], $container_id, $ctDepthParser->getDepths());
			if($retainInnerContent) {
				$parent_tree = ct_embed_inner_content($parent_tree, $components_tree);
			}
			else {
				$parent_tree = ct_replace_inner_content($parent_tree, $components_tree);
			}
		}

		return $parent_tree;
	}

	return $components_tree;
}

/**
 * Init style sheets
 *
 * @since 0.3.4
 * @author gagan goraya
 */

function ct_init_style_sheets() {
	
	$output = htmlspecialchars( oxyegn_get_style_sheets_json(), ENT_QUOTES );

	echo "styleSheets = $output;";
}

function oxyegn_get_style_sheets_json() {
	
	$style_sheets = get_option( "ct_style_sheets", array() ); 

	// it was returning 'string (0) ""' first time, don't know why
	if ( !is_array( $style_sheets ) )
		$style_sheets = array();
	
	$newSheets = array();
	$id = 0;

	foreach($style_sheets as $value) {
		if(is_array($value) && isset($value['id']) && intval($value['id']) > $id) {
			$id = intval($value['id']);
		}
	}
	//base 64 decode
	foreach($style_sheets as $key => $value) {
		if(!is_array($value)) { // if it is the old style sheets data
			$newSheets[] = array( 'id' => ++$id, 'name' => $key, 'css' => base64_decode($value), 'parent' => 0, 'status' => 1 );
		}
		else {
			if(isset($value['css'])) {
				$value['css'] = base64_decode($value['css']);
			}
			
			$newSheets[] = $value;
		}
	}
	
	return json_encode( $newSheets );
}

function oxyegn_get_style_sheets_callback() {
	echo oxyegn_get_style_sheets_json();
	die();
}
add_action('wp_ajax_oxy_get_style_sheets', 'oxyegn_get_style_sheets_callback');


/**
 * Place the page width media at right position
 *
 * @author Ilya K.
 * @since 2.0
 */

function ct_sort_media_queries($only_global=false) {

	global $media_queries_list;

	$page_width_added = false;

	foreach ( $media_queries_list as $media_name => $media ) {
		
		if ( $media_name == "default" || $media_name == "page-width" )
			continue;

		if ( (intval($media_queries_list[$media_name]['maxSize']) >= oxygen_vsb_get_page_width($only_global)) || $page_width_added ) {
			$medias[$media_name] = $media_queries_list[$media_name];
		}
		else {
			$medias['page-width'] = $media_queries_list['page-width'];
			$medias[$media_name] = $media_queries_list[$media_name];
			$page_width_added = true;
		} 
	}

	if (!$page_width_added) {
		$medias['page-width'] = $media_queries_list['page-width'];
	}

	return $medias;
}


/**
 * Get current Page Settings > Page width, fallback to Global Settings
 *
 * @author Ilya K.
 * @since 2.2
 */

function oxygen_vsb_get_media_query_size($media_name, $above=false) {

	if ($above) {
		global $media_queries_list_above;
		$size = $media_queries_list_above[$media_name]['minSize'];
	}
	else {
		global $media_queries_list;
		$size = $media_queries_list[$media_name]['maxSize'];
	}

	$size = str_replace("px", "", $size);
	return $size;
}


/**
 * Get breakpoint width param value from global settings, fallback to default width
 *
 * @author Abdelouahed E.
 * @since 3.2
 */

function oxygen_vsb_get_breakpoint_width($media_name) {
	static $breakpoints = null;
	
	if ($breakpoints == null) {
		$settings = ct_get_global_settings();
		$defaults = ct_get_global_settings(true);
		
		$breakpoints = $defaults['breakpoints'];
		
		if (isset($settings['breakpoints']) && is_array($settings['breakpoints'])) {
			foreach ($settings['breakpoints'] as $name=>$size) {
				$size = intval($size);
				if( $size ) {
					$breakpoints[$name] = $size;
				}
			}
		}
	}
	
	$width = 0;
	if (isset($breakpoints[$media_name])) {
		$width = $breakpoints[$media_name];
	}
	
	return $width;
}


/**
 * Get current Page Width param value, fallback to global Page Width 
 *
 * @author Ilya K.
 * @since 2.0
 */

function oxygen_vsb_get_page_width($only_global=false) {

	if ($only_global) {
		// ignore page settings
		$page_settings = array();
	}
	else {
		$page_settings = ct_get_page_settings();
	}
	$global_settings = ct_get_global_settings();

	if ( isset($page_settings['max-width']) && $page_settings['max-width'] != "" ) {
		return $page_settings['max-width'];
	}
	else {
		return $global_settings['max-width'];
	}

}


/**
 * Parse color option and if global color(id) return #hex or rgba() value for it
 *
 * @author Ilya K.
 * @since 2.1
 */

function oxygen_vsb_get_global_color_value($color_option) {

	global $oxygen_vsb_global_colors;

    if (!$color_option) {
        return $color_option;
    }

	if (is_array($color_option)) {
        return $color_option;
    }

    if (strpos($color_option, "color(")!==0) {
        return $color_option;
    }

    // get the value inside the parentheses
    // '/\(([^)]+)\)/'
	preg_match('/\(([^)]+)\)/', $color_option, $match);

    if (!$match) {
        return $color_option;
    }

    $color = $match[1];

    if (!$color) {
        return $color_option;
    }

    // find color by name
    if( isset($oxygen_vsb_global_colors['colors']) ) {
        foreach ($oxygen_vsb_global_colors['colors'] as $key => $value) {
            if ($value['id']==$color) {
                return $value['value'];
            }
        }
    }

    return $color_option;
}


/**
 * Callback to use in preg_replace_callback to replcae color(x) to real colors 
 *
 * @author Ilya K.
 * @since 2.1
 */

function oxygen_vsb_parce_global_colors_callback($matches) {
	return oxygen_vsb_get_global_color_value($matches[0]);
}


/**
 * Output saved CSS styles to frontend
 *
 * @since 0.1.3
 */

function ct_css_styles() {

	$css = "";

	// backward compatibility for 2.0
	$version = get_option("oxygen_vsb_universal_css_latest_version");
	if ($version!=="2.1") {
		echo oxygen_vsb_get_defaults_styles();
	}

	/**
	 * Check if need to include CSS for classes
	 *
	 * @since 2.0
	 */

	if ( !isset( $_REQUEST['nouniversal'] ) || stripslashes( $_REQUEST['nouniversal'] ) != 'true' ) {
		$css .= oxygen_vsb_get_defaults_styles(); // this has been added to universal.css in 2.1
	}

	// Below is only for frontend
	if ( defined("SHOW_CT_BUILDER") ) {
		echo $css;
		return;
	}
	
	$page_settings 	= ct_get_page_settings();

	if ( isset($page_settings['max-width']) && $page_settings['max-width'] != "" ) {
		$css .= "div.ct-section-inner-wrap, div.oxy-header-container{\r\n  max-width: ".$page_settings['max-width']."px;\r\n}\r\n";
	}
	
	// Overlay Header
	if (isset($page_settings['overlay-header-above'])&&$page_settings['overlay-header-above']!='never'&&$page_settings['overlay-header-above']!='') {

		if ($page_settings['overlay-header-above']!='always') {
			global $media_queries_list_above;
			$min_size = $media_queries_list_above[$page_settings['overlay-header-above']]['minSize'];
			$css .= "@media (min-width: $min_size) {";
		}

		$css .= ".oxy-header.oxy-overlay-header, 
				body.oxy-overlay-header .oxy-header {
					position: absolute;
					left: 0;
					right: 0;
					z-index: 20;
				}
				body.oxy-overlay-header .oxy-header:not(.oxy-sticky-header-active),
				body.oxy-overlay-header .oxy-header:not(.oxy-sticky-header-active) .oxy-header-row {
					background-color: initial !important;
				}";

		$css .= "body.oxy-overlay-header .oxy-header .oxygen-hide-in-overlay{
				display: none;
			}";

		$css .= "body.oxy-overlay-header .oxy-header .oxygen-only-show-in-overlay{
				display: block;
			}";
		
		if ($page_settings['overlay-header-above']!='always') {
			$css .= "}";
		}
	}

	if ( !isset( $_REQUEST['nouniversal'] ) || stripslashes( $_REQUEST['nouniversal'] ) != 'true' ) {
		$css .= oxygen_vsb_get_global_styles();
		$css .= oxygen_vsb_get_classes_styles();
	}
	
	// output CSS
	echo $css;
}
add_action("ct_footer_styles", "ct_css_styles");


/**
 * Function to generate classes CSS output
 *
 * @since 2.0
 * @author Ilya K.
 */

function oxygen_vsb_get_classes_styles() {

	global $media_queries_list;
	global $oxygen_vsb_css_classes;

	$css 			= "";
	$classes 		= $oxygen_vsb_css_classes;
	$page_width 	= oxygen_vsb_get_page_width(true);
	$styleFolders 	= get_option( "ct_style_folders");

	if ( is_array( $classes ) ) {
		foreach ( $classes as $class => $states ) {
			//if the parent folder is disabled?
			if(!(!isset($states['parent']) || !isset($styleFolders[$states['parent']]) || intval($styleFolders[$states['parent']]['status']) === 1)) {
				continue;
	    	}
	    	// if set under disabled uncategorized
			if(isset($states['parent']) && intval($states['parent']) === -1) {
    			continue;
			}

			$style = "";
			foreach ( $states as $state => $options ) {

				if (in_array($state, array("set_name", "key", "parent", "status", "friendly_name"))) {
					continue;
				}	

				if ( $state == 'media' ) {

					$sorted_media_queries_list = ct_sort_media_queries(true);

					foreach ( $sorted_media_queries_list as $media_name => $media ) {

						if ($media_name == "page-width" && isset($page_width)) {
							$max_width = $page_width.'px';
						}
						else {
							$max_width = $media_queries_list[$media_name]['maxSize'];
						}

						if ( isset($options[$media_name]) && $media_name != "default") {

							$style .= "@media (max-width: $max_width) {\n";
								foreach ( $options[$media_name] as $media_state => $media_options ) {
									$style .= ct_generate_class_states_css($class, $media_state, $media_options, $media_name, false, $states);
								}
							$style .= "}\n\n";
						}
					}
				}
				else {
					$style = ct_generate_class_states_css($class, $state, $options).$style;
				}
			}

			$css .= $style;
		}
	}

	global $oxygen_aos_classes;
	update_option("oxygen_aos_classes", $oxygen_aos_classes);

	return $css;
}


function oxy_get_closest_breakpoint_value($param, $media_name, $state_name = 'original', $all_styles = array()) {

	$found = false;
	$sorted_media_queries_list = array_reverse(ct_sort_media_queries(true));

	foreach ( $sorted_media_queries_list as $current_media_name => $current_media) {

		if ($found) {
			if (isset($all_styles['media'][$current_media_name][$state_name][$param])) {
				return $all_styles['media'][$current_media_name][$state_name][$param];
			}
		}

		if ($media_name == $current_media_name) {
			$found = true;
		}
	}
	
	if ( isset($all_styles[$state_name][$param]) ) {
		return $all_styles[$state_name][$param];
	}

	return false;
}

function ct_generate_class_states_css( $class, $state, $options, $is_media = false, $is_selector = false, $all_styles = array()) {
	
	global $fake_properties;
	global $oxygen_aos_classes;
	global $oxygen_vsb_css_classes;
	//global $font_families_list;
	$css = "";

	global $media_queries_list;
	$media_queries_list["page-width"]["maxSize"] = oxygen_vsb_get_page_width(true).'px';

	$components_defaults = apply_filters("ct_component_default_params", array() );
	$defaults = call_user_func_array('array_merge', array_values($components_defaults));
	$global_settings 	= get_option("ct_global_settings");

	if ( !$is_selector ) {
		if ( $state != 'original' ) {
			$css .= ".$class:$state{\r\n";
		}
		else {
			$css .= ".$class {\r\n";
		}
	}
	else {
		if ( $state != 'original' ) {
			$css .= "$class:$state{\r\n";
		}
		else {
			$css .= "$class{\r\n";	
		}
	}

	$content_included = false;

	// save original options before adding units
	$no_units_options = $options;

	// handle units
	if(is_array($options)) {
		foreach ( $options as $name => $value ) {
			// handle unit options
			if ( isset($defaults[$name.'-unit']) && $defaults[$name.'-unit'] ) {

				if ( isset($options[$name.'-unit']) && $options[$name.'-unit'] ) {
					// set to auto
					if ( $options[$name.'-unit'] == 'auto' ) {
						$options[$name] = 'auto';
					}
					// or add unit
					else {
						$options[$name] .= $options[$name.'-unit'];
					}
				}
				// check higher breakpoints
				else if ( $is_media && $unit = oxy_get_closest_breakpoint_value($name.'-unit', $is_media, $state, $all_styles) ) {
					$options[$name] .= $unit;
				}
				else {
					$options[$name] .= $defaults[$name.'-unit'];
				}
			}
			else {
	            if ( $options[$name] == 'auto' ) {
	            	$name = str_replace("-unit", "", $name);
	                $options[$name] = 'auto';
	            }
	            if ($name == 'container-padding-top'||
                        $name == 'container-padding-bottom'||
                        $name == 'container-padding-left'||
                        $name == 'container-padding-right') {
                        $unit = isset( $options[$name.'-unit'] ) ? $options[$name.'-unit'] : $global_settings['sections'][$name.'-unit'];
						if ( $is_media ) {
							$media_unit = oxy_get_closest_breakpoint_value($name.'-unit', $is_media, $state, $all_styles);
							if ($media_unit) {
								$unit = $media_unit;
							}
						}
                        if ( $options[$name] ) {
                            $options[$name] .= $unit;
                        }
                    }
			}
		}
	}

	// handle background-position option
	if ( (isset($options['background-position-left']) && $options['background-position-left']) || (isset($options['background-position-top']) && $options['background-position-top']) ) {

		$left = array_key_exists('background-position-left', $options ) ? $options['background-position-left'] : "0%";
		$top  = array_key_exists('background-position-top', $options ) ? $options['background-position-top'] : "0%";
		$options['background-position'] = $left . " " . $top;
	}

	// handle background-size option
	if ( isset($options['background-size']) && $options['background-size'] == "manual" ) {

		$width = array_key_exists( 'background-size-width', $options ) ? $options['background-size-width'] : "auto";
		$height = array_key_exists( 'background-size-height', $options ) ? $options['background-size-height'] : "auto";
		$options['background-size'] = $width . " " . $height;
	}

	// handle box-shadow options
	if ( isset($options['box-shadow-color']) ) {

		$inset 	= (isset($options['box-shadow-inset']) && $options['box-shadow-inset']=='inset') 		? $options['box-shadow-inset']." " : "";
		$hor 	= (isset($options['box-shadow-horizontal-offset'])) 	? $options['box-shadow-horizontal-offset']."px " : "";
		$ver 	= (isset($options['box-shadow-vertical-offset'])) 		? $options['box-shadow-vertical-offset']."px " : "";
		$blur 	= (isset($options['box-shadow-blur'])) 					? $options['box-shadow-blur']."px " : "0px ";
		$spread = (isset($options['box-shadow-spread'])) 				? $options['box-shadow-spread']."px " : "";
				
		$options['box-shadow'] = $inset.$hor.$ver.$blur.$spread.oxygen_vsb_get_global_color_value($options['box-shadow-color']);
	}

	// handle text-shadow options
	if ( isset($options['text-shadow-color']) ) {

		$hor 	= (isset($options['text-shadow-horizontal-offset'])) 	? $options['text-shadow-horizontal-offset']."px " : "";
		$ver 	= (isset($options['text-shadow-vertical-offset'])) 		? $options['text-shadow-vertical-offset']."px " : "";
		$blur 	= (isset($options['text-shadow-blur'])) 				? $options['text-shadow-blur']."px " : "0px ";
				
		$options['text-shadow'] = $hor.$ver.$blur.oxygen_vsb_get_global_color_value($options['text-shadow-color']);
	}

	/**
	 * Handle specific Icon styles to support classes
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	$all_classes = $oxygen_vsb_css_classes;

	if (isset($options['icon-style'])||isset($options['icon-color'])||isset($options['icon-background-color'])||isset($options['icon-padding'])||isset($options['icon-size'])) {

		// save the class name to apply later
		$classname = $css;

		if (!$is_media && $state == "original") {

			$iconStyle 			= (isset($options['icon-style'])) ? $options['icon-style'] : $components_defaults['ct_fancy_icon']['icon-style'];
			$iconColor 			= (isset($options['icon-color'])) ? oxygen_vsb_get_global_color_value($options['icon-color']) : $components_defaults['ct_fancy_icon']['icon-color'];
			$iconBackgroundColor= (isset($options['icon-background-color'])) ? oxygen_vsb_get_global_color_value($options['icon-background-color']) : $components_defaults['ct_fancy_icon']['icon-background-color'] ;
			$iconPadding 		= (isset($options['icon-padding'])) ? $options['icon-padding'] : $components_defaults['ct_fancy_icon']['icon-padding'] . $components_defaults['ct_fancy_icon']['icon-padding-unit'];
			$iconSize 			= (isset($options['icon-size'])) ? $options['icon-size'] : $components_defaults['ct_fancy_icon']['icon-size'] . $components_defaults['ct_fancy_icon']['icon-size-unit'];
		}
		else {
			$iconStyle 			= (isset($all_classes[$class]['original']['icon-style'])) ? $all_classes[$class]['original']['icon-style'] : $components_defaults['ct_fancy_icon']['icon-style'];
			$iconColor 			= oxygen_vsb_get_global_color_value($options['icon-color']);
			$iconBackgroundColor= oxygen_vsb_get_global_color_value($options['icon-background-color']);
			$iconPadding 		= $options['icon-padding'];
			$iconSize 			= $options['icon-size'];
		}

		if ( $iconStyle == "1") {
			$css .= "border: 1px solid;\r\n";
		}
					
		if ($iconStyle == "2") {
			if ( isset($iconBackgroundColor) ) {
				$css .= "background-color: " . $iconBackgroundColor . ";\r\n";
				$css .= "border: 1px solid " . $iconBackgroundColor . ";\r\n";
			}
		}

		if ( $iconStyle == "1" || $iconStyle == "2") {
			$css .= "padding: " . $iconPadding . ";";
		}

		if ( $iconColor ) {
		 	$css .= "color: " . $iconColor . ";";
		}

		$css .= "}";

		if ( $iconSize ) {
			$css .= str_replace("{","",$classname).">svg {";
		 	$css .= "width: " . $iconSize . ";";
		 	$css .= "height: " . $iconSize . ";";
		 	$css .= "}";
		}

		// add classname back so options below also work fine
		$css .= $classname;
	}


	/**
	 * Handle specific Button styles to support classes
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	$all_classes = $oxygen_vsb_css_classes;

	if (isset($options['button-style'])||isset($options['button-color'])||isset($options['button-text-color'])||isset($options['button-size'])) {

		// save the class name to apply later
		$classname = $css;

		if (!$is_media && $state == "original") {
			$buttonStyle 		= (isset($options['button-style'])) ? $options['button-style'] : $components_defaults['ct_link_button']['button-style'];
			$buttonColor 		= (isset($options['button-color'])) ? oxygen_vsb_get_global_color_value($options['button-color']) : $components_defaults['ct_link_button']['button-color'];
			$buttonTextColor 	= (isset($options['button-text-color'])) ? oxygen_vsb_get_global_color_value($options['button-text-color']) : $components_defaults['ct_link_button']['button-text-color'] ;
			$buttonSize 		= (isset($options['button-size'])) ? $options['button-size'] : $components_defaults['ct_link_button']['button-size'];
		}
		else {
			$buttonStyle 		= isset($all_classes[$class]['original']['button-style']) ? $all_classes[$class]['original']['button-style'] : $components_defaults['ct_link_button']['button-style'];
			$buttonColor 		= oxygen_vsb_get_global_color_value($options['button-color']);
			$buttonTextColor 	= oxygen_vsb_get_global_color_value($options['button-text-color']);
			$buttonSize 		= $options['button-size'];
		}

		if ( $buttonStyle == 1 && isset($buttonColor)) {
			$css .= "background-color :" . $buttonColor . ";\r\n";
			$css .= "border: 1px solid " .  $buttonColor . ";\r\n";
			if(isset($options['button-text-color'])) {
				$css .= "color: " . $buttonTextColor . ";\r\n";
			}
		}

		if ( $buttonStyle == 2 ) {
			$css .= "background-color: transparent;\r\n";
			if ( isset($buttonColor) ) {
				$css .= "border: 1px solid " .  $buttonColor . ";\r\n";
				$css .= "color: " . $buttonColor . ";\r\n";
			}
		}

		if ( isset($options['button-size']) || isset($options['button-style']) ) {
			$substracted = $buttonStyle == 2 ? 1 : 0;
			$css .= "padding: " . (intval($buttonSize)-$substracted) . 'px ' . (intval($buttonSize)*1.6-$substracted) . "px;\r\n";
		}

		$css .= "}";

		// add classname back so options below also work fine
		$css .= $classname;
	}

	// loop all other options
	if(is_array($options)) {
		$css .=  ct_getBackgroundLayersCSS($options);
		$css .= CT_Component::getTransformCSS($options, $defaults);
		foreach ( $options as $name => $value ) {

			// skip units
			if ( strpos( $name, "-unit") ) {
				continue;
			}

			// skip empty values
			if ( $value === "" ) {
				continue;
			}

			if ( $name == "font-family") {

				if ( is_array($value) && $value[0] == 'global' ) {
						$settings 	= get_option("ct_global_settings");
						$value 		= isset($settings['fonts'][$value[1]]) ? $settings['fonts'][$value[1]]: '';
					}

				//$font_families_list[] = $value;

				else if ( strpos($value, ",") === false && strtolower($value) != "inherit") {
					$value = "'$value'";
				}
			}

			// update options array values if there was modifications
			$options[$name] = $value;

			if (strpos($name, "aos-")===0 && isset($options["aos-enable"]) && $options["aos-enable"] !== 'false') {
				if (!is_array($oxygen_aos_classes)) {
					$oxygen_aos_classes = array();
				}
				if (!isset($oxygen_aos_classes[$class]) || !is_array($oxygen_aos_classes[$class])) {
					$oxygen_aos_classes[$class] = array();
				}
				$oxygen_aos_classes[$class][$name] = $value;
			}

			// skip fake properties
			if (is_array($fake_properties) && in_array( $name, $fake_properties ) ) {
				continue;
			}

			// add flex later for innerwrap. since 2.0
			if ( in_array($name, ["display","flex-direction","flex-wrap","align-items","align-content","justify-content"]) &&
				 !$is_selector ) {
				continue;
			}

			if($name == 'background-image' || $name == 'background-size' || $name == 'transform') {
				continue; // this is being taken care of by the ct_getBackgroundLayersCSS function
			}

			// handle image urls
			// if ( $name == "background-image") {
				
			// 	$value = "url(".do_shortcode($value).")";
			// 	// trick for overlay color
	  //           if ( isset( $options['overlay-color'] ) ) {
	  //               $value = "linear-gradient(" . oxygen_vsb_get_global_color_value($options['overlay-color']) . "," . oxygen_vsb_get_global_color_value($options['overlay-color']) . "), " . $value;
	  //           }
			// }
			
			// add quotes for content for :before and :after
			if ( $name == "content" ) {
				//$value = addslashes( $value );
				$value = str_replace('"', '\"', $value);
				$value = "\"$value\"";
				$content_included = true;
			}

			// css filter property
			if ( $name == "filter" && $options["filter-amount-".$value] ) {
				$value .= "(".$options["filter-amount-".$value].")";
			} 
			else if ( $name == "filter" ) {
				continue;
			}

			// finally add to CSS
			if ($name != "background-layers") {
			
				$not_css_options = apply_filters( "ct_not_css_options", array() );
				// merge into one dimensional array
				$not_css_options = call_user_func_array('array_merge', array_values($not_css_options));
				// remove duplicates (does it make it faster?)
        		$not_css_options = array_unique($not_css_options);

        		// we failed and added "transition-duration" to "not css options" list in Gallery component
        		// so remove it from "not css options" here
        		$index = array_search("transition_duration", $not_css_options);
        		if ($index !== false) { 
        			unset($not_css_options[$index]);
        		}

				// This is the place to check NOT CSS OPTIONS
				if (  is_array( $not_css_options ) && 
					  in_array( str_replace("-", "_", $name), $not_css_options ) ) {
					// do nothing
				}
				else {
					// add to a class rules
					$css .= " $name:".oxygen_vsb_get_global_color_value($value).";\r\n";
				}
			}

			if ($name == "-webkit-font-smoothing") {
				$css .=  '-moz-osx-font-smoothing' . ":" . ($value === 'antialiased' ? 'grayscale' : 'unset') . ";";
			}

		}
	}
	
	if ( !$content_included && ( $state == "before" || $state == "after" ) && !$is_media ) {
		$css .= "  content:\"\";\r\n";
	}

	// add custom CSS to the end
	if ( isset($options["custom-css"]) && $options["custom-css"] ) {
		if( strpos($options["custom-css"], ' ') === false 
		 	&& strpos($options["custom-css"], ':') === false
		 	&& strpos($options["custom-css"], ';') === false  ) {
			// this is most probably base 64 encoded css (old data)
			$css .= base64_decode( $options["custom-css"] ) . "\r\n";	
		}
		else {
			$options["custom-css"] = preg_replace_callback(
				            "/color\(\d+\)/",
				            "oxygen_vsb_parce_global_colors_callback",
				            $options["custom-css"]);
			$css .= $options["custom-css"] . "\r\n";	
		}
	}

	$css .= "}\r\n";

	// handle container padding for classes
	if ( isset($options['container-padding-top']) 	 ||
		 isset($options['container-padding-right'])  ||
		 isset($options['container-padding-bottom']) ||
		 isset($options['container-padding-left'])  ) {

		$css .= ".$class .ct-section-inner-wrap {\r\n";
		
		if ( isset($options['container-padding-top']) ) {
			$css .= "padding-top: " . $options['container-padding-top'] . ";\r\n";
		}
		if ( isset($options['container-padding-right']) ) {
			$css .= "padding-right: " . $options['container-padding-right'] . ";\r\n";
		}
		if ( isset($options['container-padding-bottom']) ) {
			$css .= "padding-bottom: " . $options['container-padding-bottom'] . ";\r\n";
		}
		if ( isset($options['container-padding-left']) ) {
			$css .= "padding-left: " . $options['container-padding-left'] . ";\r\n";
		}

		$css .= "}\r\n";
	}
	
	$pre_styles = "";
	
	// flex options since 2.0
	if ( isset($options['display']) && (!isset($options['flex-direction']) || $options['flex-direction'] != 'grid')) {
		$pre_styles .= "display:" . $options['display'] . ";\r\n";
	}

	$reverse = (isset($options['flex-reverse']) && $options['flex-reverse'] == 'reverse') ? "-reverse" : "";
	if ( isset($options['flex-direction']) && $options['flex-direction'] != 'grid') {
		$pre_styles .= "flex-direction:" . $options['flex-direction'] . $reverse . ";\r\n";
	}
	if ( isset($options['flex-wrap']) ) {
		$pre_styles .= "flex-wrap:" . $options['flex-wrap'] . ";\r\n";
	}
	if ( isset($options['align-items']) ) {
		$pre_styles .= "align-items:" . $options['align-items'] . ";\r\n";
	}
	if ( isset($options['align-content']) ) {
		$pre_styles .= "align-content:" . $options['align-content'] . ";\r\n";
	}
	if ( isset($options['justify-content']) ) {
		$pre_styles .= "justify-content:" . $options['justify-content'] . ";\r\n";
	}
	if ( isset($options['gap']) ) {
		$pre_styles .= "gap:" . $options['gap'] . ";\r\n";
	}

	$pre_styles .= CT_Component::getGridCSS($no_units_options, $options, $defaults, "!important");
	
	if($pre_styles != '' && !$is_selector) {
		
		if ( $state != 'original' ) {
			$css .= ".$class:not(.ct-section):not(.oxy-easy-posts):$state,\r\n";
			if ( is_pseudo_element($state) ) {
				$css .= ".$class.oxy-easy-posts .oxy-posts:$state,\r\n";
				$css .= ".$class.ct-section .ct-section-inner-wrap:$state{\r\n";
			}
			else {
				$css .= ".$class.oxy-easy-posts:$state .oxy-posts,\r\n";
				$css .= ".$class.ct-section:$state .ct-section-inner-wrap{\r\n";
			}
		}
		else {
			$css .= ".$class:not(.ct-section):not(.oxy-easy-posts),\r\n";
			$css .= ".$class.oxy-easy-posts .oxy-posts,\r\n";
			$css .= ".$class.ct-section .ct-section-inner-wrap{\r\n";
		}
		
		$css .= $pre_styles;
		$css .= "}\r\n";
	}

	if(!$is_selector) {
		$css .= CT_Component::getGridChildCSS($options, $class, $state, false, "is class");
	}

	/**
	 * Make it possible to apply custom classes logic from components Classes or other places
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	$css = apply_filters("oxygen_user_classes_output", $css, $class, $state, $options, $is_media, $is_selector, $components_defaults);

	return $css;
}


/**
 * Function to generate components defaults CSS output
 *
 * @since 2.1
 * @author Ilya K.
 */

function oxygen_vsb_get_defaults_styles() {

	global $fake_properties;
	
	// Global settings
	$global_settings = ct_get_global_settings();

	$components_defaults = apply_filters("ct_component_default_params", array() );

	ob_start();

	// Output all components default styles
	foreach ( $components_defaults as $component_name => $values ) {
		
		$component_name = str_replace( "_", "-", $component_name );
		$styles = "";
		
		if(is_array($values)) {
			foreach ( $values as $name => $value ) {

				// Output only some options since 2.0
				if ( !in_array($name, array("margin-top","margin-bottom","padding-left","text-decoration","max-width","position","width","display","flex-direction", "flex-wrap", "align-items","justify-content","text-align","background-size","background-repeat",
					// icon defaults
					"icon-color"
					))) {
					continue;
				}
				if ($name=="display"&&$value=="block") {
					continue;
				}
				if ($name=="width"&&$value!="100") {
					continue;
				}
				if ($name=="max-width"&&$value!="100") {
					continue;
				}
				if ($name=="position"&&$value=="static") {
					continue;
				}
				if ($name=="text-decoration"&&$value=="none"&&$component_name!="ct-link"&&$component_name!="ct-link-button") {
					continue;
				}
				if (($name=="margin-top"||$name=="margin-bottom"||$name=="padding-left")&&$component_name!="ct-ul") {
					continue;
				}
				if (($name=="background-size"||$name=="background-repeat")&&$component_name!="ct-section") {
					continue;
				}

				// old output before 2.0
				// skip uints
				if ( strpos( $name, "-unit") ) {
					continue;
				}

				// skip empty values
				if ( $value === "" ) {
					continue;
				}

				// skip fake properties
				if ( in_array( $name, $fake_properties ) ) {
					continue;
				}

				// apply for inner wrap
				if ($component_name=="ct-section" && in_array($name, array("display","flex-direction", "flex-wrap", "align-items","justify-content"))) {
					continue;
				} 

				// handle global fonts
				if ( $name == "font-family" && is_array( $value ) ) {
					$value = $global_settings['fonts'][$value[1]];

					if ( strpos($value, ",") === false && strtolower($value) != "inherit" ) {
						$value = "'$value'";
					}
				}

				// handle unit options
				if ( isset($values[$name.'-unit']) && $values[$name.'-unit'] ) {
					// set to auto
					if ( $values[$name.'-unit'] == 'auto' ) {
						$value = 'auto';
					}
					// or add unit
					else {
						$value .= $values[$name.'-unit'];
					}
				}

				$name = str_replace("icon-", "", $name);

				if ( $value !== "" ) {
					$styles .= "$name:$value;\r\n";
				}

			}
		}

		if ($styles!=="") {
			echo ( $component_name == "ct-paragraph" ) ? ".$component_name p {\r\n" : ".$component_name {\r\n";
			echo $styles;
			echo "}\r\n";
		}

		if ( $component_name == "ct-fancy-icon" ) {
			echo ".$component_name>svg {\r\n";
			echo "width:".$values['icon-size'].$values['icon-size-unit'].";";
			echo "height:".$values['icon-size'].$values['icon-size-unit'].";";
			echo "}\r\n";
		}

		if ( $component_name == "ct-link-button" ) {
			echo ".$component_name {\r\n";
			echo "background-color: " . $values['button-color'] . ";\r\n";
			echo "border: 1px solid " .  $values['button-color'] . ";\r\n";
			echo "color: " . $values['button-text-color'] . ";\r\n";
			$substracted = $values['button-style'] == 2 ? 1 : 0;
			echo "padding: " . (intval($values['button-size'])-$substracted) . 'px ' . (intval($values['button-size'])*1.6-$substracted) . "px;\r\n";
			echo "}\r\n";
		}

		if ($component_name=="ct-section") {
			echo ".$component_name>.ct-section-inner-wrap {\r\n";

			// flex since 2.0
			echo "display:" . $values['display'] . ";\r\n";
			echo "flex-direction:" . $values['flex-direction'] . ";\r\n";
			echo "align-items:" . $values['align-items'] . ";\r\n";
			//echo "justify-content:" . $values['justify-content'] . ";\r\n";

			echo "}\r\n";
		}
	}

	if (get_option("oxygen_vsb_enable_ie_layout_improvements")==="true") {
		echo "@media screen and (-ms-high-contrast: active), (-ms-high-contrast: none) {
			.ct-div-block,
			.oxy-post-content,
			.ct-text-block,
			.ct-headline,
			.oxy-rich-text,
			.ct-link-text { max-width: 100%; }
			img { flex-shrink: 0; }
			body * { min-height: 1px; }
		}";
	}

	/**
	 * Make it possible to add defaults from components Class or any other place
	 *
	 * @since 2.0
	 * @author Ilya K.
	 */

	do_action("oxygen_default_classes_output");

	$css = ob_get_clean();

	return $css;
}


/**
 * Function to generate global settigns CSS output
 *
 * @param bool $universal_admin true to generate CSS for universal_admin.css
 * @return string global styles css
 *
 * @since 2.0
 * @author Ilya K.
 */

function oxygen_vsb_get_global_styles() {

	/* unscope global css from gutenberg */
    //$cache_being_generated = isset($_REQUEST['action']) && $_REQUEST['action'] == 'oxygen_vsb_generate_universal_css_by_ajax' ? true: false;
	//if ((is_admin() && !$cache_being_generated) || $universal_admin) {
	//	$gutenberg_hack = true;
	//} else {
	//	$gutenberg_hack = false;
	//}
	$gutenberg_hack = false;

	$global_settings = ct_get_global_settings();

	$css = "";

	// default page width taken from global settings
	$css .= ".ct-section-inner-wrap, .oxy-header-container{\r\n  max-width: ".$global_settings["max-width"]."px;\r\n}\r\n";

	$text_font 		= $global_settings['fonts']['Text'];
	$display_font 	= $global_settings['fonts']['Display'];

	if ( strpos($text_font, ",") === false) {
		$text_font = "'$text_font'";
	}
	if ( strpos($display_font, ",") === false) {
		$display_font = "'$display_font'";
	}

	// Global settings since 2.0
	// Body Text
	if ($gutenberg_hack == true) {		
		$css .= ".editor-styles-wrapper {";
	} else {
		$css .= "body {";
	}

    $css .= "font-family: ".$text_font.";";
    $css .= "}";
    $css .= "body {";
	$css .= "line-height: ".$global_settings["body_text"]["line-height"].";";
	$css .= "font-size: ".$global_settings["body_text"]["font-size"].$global_settings["body_text"]["font-size-unit"].";";
	$css .= "font-weight: ".$global_settings["body_text"]["font-weight"].";";
	$css .= "color: ".oxygen_vsb_get_global_color_value($global_settings["body_text"]["color"]).";";
	$css .= "}";

	$css .= ".oxy-nav-menu-hamburger-line {";
	$css .= "background-color: ".oxygen_vsb_get_global_color_value($global_settings["body_text"]["color"]).";";
	$css .= "}";

	// Headings
	if ($gutenberg_hack == true) {		
		$css .= ".editor-styles-wrapper h1, .editor-styles-wrapper h2, .editor-styles-wrapper h3, .editor-styles-wrapper h4, .editor-styles-wrapper h5, .editor-styles-wrapper h6 {";
	} else {
		$css .= "h1, h2, h3, h4, h5, h6 {";
	}
    $css .= "font-family: ".$display_font.";";
    
    if (!empty($global_settings["headings"]["H1"]["font-size"])) { 
    	$css .= "font-size: ".$global_settings["headings"]["H1"]["font-size"].$global_settings["headings"]["H1"]["font-size-unit"].";";
    }
    if (!empty($global_settings["headings"]["H1"]["font-weight"])) { 
    	$css .= "font-weight: ".$global_settings["headings"]["H1"]["font-weight"].";";
    }
    if (!empty($global_settings["headings"]["H1"]["line-height"])) { 
    	$css .= "line-height: ".$global_settings["headings"]["H1"]["line-height"].";";
    }
    if (!empty($global_settings["headings"]["H1"]["color"])) { 
    	$css .= "color: ".oxygen_vsb_get_global_color_value($global_settings["headings"]["H1"]["color"]).";";
    }
    
    $css .= "}";

    $selector = "h2, h3, h4, h5, h6";

    foreach($global_settings["headings"] as $heading => $options) { 

		$heading_css = "";
		
		if ($heading=="H1") {
			continue;
		}
		
		if (!empty($options["font-size"])) {
			$heading_css .= "font-size: ".$options["font-size"].$options["font-size-unit"].";";
		}
		if (!empty($options["font-weight"])) {
			$heading_css .= "font-weight: ".$options["font-weight"].";";
		}
		if (!empty($options["line-height"])) {
			$heading_css .= "line-height: ".$options["line-height"].";";
		}
		if (!empty($options["color"])) {
			$heading_css .= "color: ".oxygen_vsb_get_global_color_value($options["color"]).";";
		}
		
		if ( $heading_css !== "" ) {

			if ($gutenberg_hack == true) {
				$gutenberg_hack_selector = $selector;
				$gutenberg_hack_selector = str_replace("h2", ".editor-styles-wrapper h2", $gutenberg_hack_selector);
				$gutenberg_hack_selector = str_replace("h3", ".editor-styles-wrapper h3", $gutenberg_hack_selector);
				$gutenberg_hack_selector = str_replace("h4", ".editor-styles-wrapper h4", $gutenberg_hack_selector);
				$gutenberg_hack_selector = str_replace("h5", ".editor-styles-wrapper h5", $gutenberg_hack_selector);
				$gutenberg_hack_selector = str_replace("h6", ".editor-styles-wrapper h6", $gutenberg_hack_selector);
				$css .= $gutenberg_hack_selector . "{";
			} else {
				$css .= $selector . "{";
			}
		
			$css .= $heading_css;
			$css .= "}";
		}

		// update selector
		$selector = str_replace(strtolower($heading).", ", "", $selector);
	}
	
	$links = array(
		"all" => "a",
		"text_link" => ".ct-link-text",
		"link_wrapper" => ".ct-link",
		"button" => ".ct-link-button"
	);

	if ($gutenberg_hack == true) {		
		$links['all'] .= ".editor-styles-wrapper a";
	}

	foreach($links as $key => $selector) { 
	
		// Links
		$links_css = "";
		if (isset($global_settings["links"][$key]["color"]) && $global_settings["links"][$key]["color"] !== "") {
			$links_css .= "color: ".oxygen_vsb_get_global_color_value($global_settings["links"][$key]["color"]).";";
		}
		if (isset($global_settings["links"][$key]["font-weight"]) && $global_settings["links"][$key]["font-weight"] !== "") {
			$links_css .= "font-weight: ".$global_settings["links"][$key]["font-weight"].";";
		}
		if (isset($global_settings["links"][$key]["text-decoration"]) ) {
			$links_css .= "text-decoration: ".$global_settings["links"][$key]["text-decoration"].";";
		}
		if (isset($global_settings["links"][$key]["border-radius"]) ) {
			$links_css .= "border-radius: ".$global_settings["links"][$key]["border-radius"].$global_settings["links"][$key]["border-radius-unit"].";";
		}

		if ( $links_css !== "" ) {
			$css .= $selector." {";
			$css .= $links_css;
			$css .= "}";
		}

		$links_css = "";
		if (isset($global_settings["links"][$key]["hover_color"]) && $global_settings["links"][$key]["hover_color"] !== "") {
			$links_css .= "color: ".oxygen_vsb_get_global_color_value($global_settings["links"][$key]["hover_color"]).";";
		}
		if (isset($global_settings["links"][$key]["hover_text-decoration"]) && $global_settings["links"][$key]["hover_text-decoration"] ) {
			$links_css .= "text-decoration: ".$global_settings["links"][$key]["hover_text-decoration"].";";
		}

		if ( $links_css !== "" ) {
			$css .= $selector.":hover {";
			$css .= $links_css;
			$css .= "}";
		}
	}

	// Sections container padding
	$css .= ".ct-section-inner-wrap {\r\n";
			
			if ( isset($global_settings['sections']['container-padding-top']) && $global_settings['sections']['container-padding-top'] ) {
				$css .= "padding-top: " . $global_settings['sections']['container-padding-top'] . $global_settings['sections']['container-padding-top-unit'] . ";\r\n";
			}
			if ( isset($global_settings['sections']['container-padding-right']) && $global_settings['sections']['container-padding-right'] ) {
				$css .= "padding-right: " . $global_settings['sections']['container-padding-right'] . $global_settings['sections']['container-padding-right-unit'] . ";\r\n";
			}
			if ( isset($global_settings['sections']['container-padding-bottom']) && $global_settings['sections']['container-padding-bottom'] ) {
				$css .= "padding-bottom: " . $global_settings['sections']['container-padding-bottom'] . $global_settings['sections']['container-padding-bottom-unit'] . ";\r\n";
			}
			if ( isset($global_settings['sections']['container-padding-left']) && $global_settings['sections']['container-padding-left'] ) {
				$css .= "padding-left: " . $global_settings['sections']['container-padding-left'] . $global_settings['sections']['container-padding-left-unit'] . ";\r\n";
			}
	$css .= "}";


	// Sections container padding
	$css .= ".ct-new-columns > .ct-div-block {\r\n";
			
		if ( isset($global_settings['columns']['padding-top']) && $global_settings['columns']['padding-top'] ) {
			$css .= "padding-top: " . $global_settings['columns']['padding-top'] . $global_settings['columns']['padding-top-unit'] . ";\r\n";
		}
		if ( isset($global_settings['columns']['padding-right']) && $global_settings['columns']['padding-right'] ) {
			$css .= "padding-right: " . $global_settings['columns']['padding-right'] . $global_settings['columns']['padding-right-unit'] . ";\r\n";
		}
		if ( isset($global_settings['columns']['padding-bottom']) && $global_settings['columns']['padding-bottom'] ) {
			$css .= "padding-bottom: " . $global_settings['columns']['padding-bottom'] . $global_settings['columns']['padding-bottom-unit'] . ";\r\n";
		}
		if ( isset($global_settings['columns']['padding-left']) && $global_settings['columns']['padding-left'] ) {
			$css .= "padding-left: " . $global_settings['columns']['padding-left'] . $global_settings['columns']['padding-left-unit'] . ";\r\n";
		}
$css .= "}";

	// Sections container padding
	$css .= ".oxy-header-container {\r\n";
			
			if ( isset($global_settings['sections']['container-padding-right']) && $global_settings['sections']['container-padding-right'] ) {
				$css .= "padding-right: " . $global_settings['sections']['container-padding-right'] . $global_settings['sections']['container-padding-right-unit'] . ";\r\n";
			}
			if ( isset($global_settings['sections']['container-padding-left']) && $global_settings['sections']['container-padding-left'] ) {
				$css .= "padding-left: " . $global_settings['sections']['container-padding-left'] . $global_settings['sections']['container-padding-left-unit'] . ";\r\n";
			}
	$css .= "}";


	// make columns fullwidth on mobile
	$css .= "@media (max-width: 992px) {
				.ct-columns-inner-wrap {
					display: block !important;
				}
				.ct-columns-inner-wrap:after {
					display: table;
					clear: both;
					content: \"\";
				}
				.ct-column {
					width: 100% !important;
					margin: 0 !important;
				}
				.ct-columns-inner-wrap {
					margin: 0 !important;
				}
			}\r\n";

	return $css;
}


/**
 * Check if state is pseudo-element by it's name
 *
 * @since 0.4.0
 * @author Ilya K.
 */

function is_pseudo_element( $name ) {
	
	if ( 
            strpos($name, "before")       === false &&
            strpos($name, "after")        === false &&
            strpos($name, "first-letter") === false &&
            strpos($name, "first-line")   === false &&
            strpos($name, "selection")    === false
        ) 
    {
        return false;
    }
    else {
        return true;
    }
}

/**
 * Generate font familes list to load
 *
 * @since  0.2.3
 */

function ct_get_font_families_string( $font_families, $global_settings, $url=false ){

	if ( ! $font_families ) {
		return "";
	}

	// filter array for duplicate values
	$font_families = array_unique( $font_families );

	// filter array for empty values
	$font_families = array_filter( $font_families );

	$web_safe_fonts = array(
			'inherit',
			'Inherit',
			'Georgia, serif',
			'Times New Roman, Times, serif',
			'Arial, Helvetica, sans-serif',
			'Arial Black, Gadget, sans-serif',
			'Tahoma, Geneva, sans-serif',
			'Verdana, Geneva, sans-serif',
			'Courier New, Courier, monospace'
		);

	// don't load web safe fonts
	$font_families = array_diff( $font_families, $web_safe_fonts );

	// don't load typekit fonts
	$typekit_fonts = get_option("oxygen_vsb_latest_typekit_fonts", array());

	// get rid of Global Fonts names for TypeKit fonts to keep only font family name
	$typekit_fonts_slugs = array_map( function( $font ) {
		return $font['slug'];
	}, $typekit_fonts);
	
	$font_families = array_map( function( $font ) use($typekit_fonts_slugs) {
		
		$position = strpos($font,'::');
		if ($position!==false) {
			$font_exploded = explode("::",$font); // $font[0] - Font Family, $font[1] - Global Font name
			$key = array_search($font_exploded[0], $typekit_fonts_slugs);
			if ($key!==false) {
				$font = substr($font, 0, $position);
			}
		}
		return $font;

	}, $font_families);

	$font_families = array_unique( $font_families );

	foreach ($typekit_fonts as $typekit_font) {
		$key = array_search($typekit_font['slug'], $font_families);
		if ($key!==false) {
			unset ($font_families[$key]);
		}
	}
	
	// don't load ECF fonts
    global $ECF_Plugin;
	if (is_a($ECF_Plugin, 'ECF_Plugin')) {
        $ecf_fonts = $ECF_Plugin->get_font_families();
        if (!is_array($ecf_fonts)) {
            $ecf_fonts = array();
        }
	} else {
		$ecf_fonts = array();
	}

	foreach ($ecf_fonts as $ecf_font) {
		$key = array_search($ecf_font, $font_families);
		if ($key!==false) {
			unset ($font_families[$key]);
		}
	}

	// add font weights
	$font_families = array_map( 
		function( $font ) use( $global_settings, $ecf_fonts, $typekit_fonts, $web_safe_fonts ) {
			// check options for global fonts
			$font = explode("::",$font); // $font[0] - Font Family, $font[1] - Global Font name

			// don't load global fonts other than Google Fonts
			if ( in_array($font[0], $ecf_fonts) || 
				 in_array($font[0], $typekit_fonts) || 
				 in_array($font[0], $web_safe_fonts) ) {
				return "";
			}

			if (isset($global_settings['fontsOptions']) && isset($font[1])){
				if (isset($global_settings['fontsOptions'][$font[1]]) &&
					is_array($global_settings['fontsOptions'][$font[1]])) {
					$weights = ":";
					foreach ($global_settings['fontsOptions'][$font[1]] as $key => $value) {
						if ($value=='true') {
							$weights .= $key . ",";
						}
					}
					if ($weights==":") {
						$weights = ':100,200,300,400,500,600,700,800,900';						
					}
				}
				else {
					$weights = ':100,200,300,400,500,600,700,800,900';
				}
			}
			else {
				// default font weights
				$weights = ':100,200,300,400,500,600,700,800,900';
			}
						
			return $font[0] . $weights;
		}, $font_families );

	// filter array for empty values
	$font_families = array_filter( $font_families, function( $font ) {
						return $font !== '';
					});

	if ( $url ) {
		// replace spaces with "+"
		$font_families = array_map( function( $font ) {
							return str_replace(" ", "+", $font);
						}, $font_families );

		$font_families = implode("|", $font_families);
	}
	else {
		// add "" quotes
		$font_families = array_map( function( $font ) {
							return '"' . $font . '"';
						}, $font_families );		

		// create fonts string to pass into JS
		$font_families = implode(",", $font_families);
	}

	return $font_families;
}


/**
 * Echo all components JS like web fonts etc
 * 
 * @since 0.1.9
 */

function ct_footer_script_hook() {
	echo "<script type=\"text/javascript\" id=\"ct-footer-js\">";
		do_action("ct_footer_js");
	echo "</script>";


	$footer_js = ct_get_components_classes(true);
	if(is_array($footer_js)) {
		foreach($footer_js as $key => $val) {
			echo "<script type=\"text/javascript\" id=\"$key\">";
				echo $val;
			echo "</script>";		
		}
	}

}
add_action("wp_footer", "ct_footer_script_hook", 20);


/**
 * Displays a warning for non-chrome browsers in the builder
 * 
 * @since 0.3.4
 * @author gagan goraya
 */

function ct_chrome_modal() {

	if ( defined("SHOW_CT_BUILDER") )  {
		$dismissed = get_option("ct_chrome_modal", false );

		$warningMessage = __("<h2><span class='ct-icon-warning'></span> Warning: we recommend Google Chrome when designing pages</h2><p>The designs you create using Oxygen will work properly in all modern browsers including but not limited to Chrome, Firefox, Safari, and Internet Explorer/Edge.</p><p>But for the best, most stable experience when using Oxygen to design pages, we recommend using Google Chrome.</p><p>We've done most of our testing with Chrome and expect that you will encounter minor bugs in the builder when using Firefox or Safari. Please report those to us by e-mailing at support@oxygenapp.com.</p><p>We have no intention of making the builder work well in Internet Explorer.</p><p>Again, this message only applies to the builder itself. The pages you create with Oxygen will render correctly in all modern browsers.</p><p>Best Regards,<br />The Oxygen Team</p>", 'component-theme' );

		$hideMessage = __("hide this notice", 'component-theme' );

		if(!$dismissed) {


			echo "<div ng-click=\"removeChromeModal(\$event)\" class=\"ct-chrome-modal-bg\"><div class=\"ct-chrome-modal\"><a href=\"#\" class=\"ct-chrome-modal-hide\">".$hideMessage."</a>"."</div></div>";

		?>
			<script type="text/javascript">
			
				jQuery(document).ready(function(){
					var warningMessage = "<?php echo $warningMessage; ?>";
					
			        var isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
			        
			        var chromeModalWrap = jQuery('.ct-chrome-modal-bg');

			        if(isChrome) {
			        	chromeModalWrap.remove();
					}
			        else {
						chromeModalWrap.css('display', 'block');
			           	var chromeModal = jQuery('.ct-chrome-modal');
			            chromeModal.append(warningMessage);
			        }

			    });
			
			</script>

			<?php
		}
	}

}

//add_action("wp_footer", "ct_chrome_modal");



/**
 * Fix for <p></p> tags around component shortocdes
 * 
 * @since 0.1.6
 */

//remove_filter("the_content", "wpautop");

/**
 * Turn off wptexturize https://codex.wordpress.org/Function_Reference/wptexturize
 * 
 * @since 0.1.6
 */

add_filter("run_wptexturize", "__return_false");


/**
 * Add support for certain WordPress features
 * 
 * @since 0.2.3
 */

function ct_theme_support() {

	add_theme_support("menus"); 
	add_theme_support("post-thumbnails");
	add_theme_support("title-tag");
}
add_action("init", "ct_theme_support");


/**
 * Add support for certain WordPress features
 * 
 * @since 2.0
 */

function oxygen_vsb_woo_theme_support() {
	add_theme_support("woocommerce");
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action("after_setup_theme", "oxygen_vsb_woo_theme_support"); 


/**
 * Uses a dedicated template to render CSS only that can be loaded from external links
 * or Oxygen main template to show builder or builder designed page
 *
 * @author gagan goraya
 * @since 0.3.4
 */

function ct_css_output( $template ) {
	
	$new_template = '';
	
	if ( $template != get_page_template() && $template != get_index_template() ) {
		global $ct_replace_render_template;
		$ct_replace_render_template = $template;
	}

	if ( isset( $_REQUEST['xlink'] ) && stripslashes( $_REQUEST['xlink'] ) == 'css' ) {
		if ( file_exists( dirname( __FILE__) . '/csslink.php' ) ) {
			$new_template = dirname( __FILE__ ) . '/csslink.php';
		}
	}
	else {
		// if there is saved template or if we are in builder mode

			if ( defined("OXYGEN_IFRAME") ) {
				$new_template =  plugin_dir_path( __FILE__ ) . "/oxygen-iframe-template.php";
			}
			else
			if ( file_exists(plugin_dir_path( __FILE__ ) . "/oxygen-main-template.php") ) {
				$new_template =  plugin_dir_path( __FILE__ ) . "/oxygen-main-template.php";
			}
	}
	
	if ( '' != $new_template ) {
		return $new_template;
	}
		
	return $template;
}
add_filter( 'template_include', 'ct_css_output', 99 );

function ct_determine_render_template( $template ) {
	
	if ( defined( "SHOW_CT_BUILDER" ) ) {
		return get_index_template();
	}

	$post_id 	 = get_the_ID();
	$custom_view = false;

	if ( !is_archive() ) {

		if(isset($_REQUEST['oxy_preview_revision']) && is_numeric($_REQUEST['oxy_preview_revision'])) {
			$custom_view = Oxygen_Revisions::get_post_meta_db( $post_id, null, true, null, OBJECT, $_REQUEST['oxy_preview_revision'] )->meta_value;
		} else {
			$shortcodes = get_post_meta( $post_id, "ct_builder_shortcodes", true );
			$json = get_post_meta( $post_id, "ct_builder_json", true );
			$custom_view = oxygen_json_has_elements($json) || $shortcodes;
		}
	}
	
	if ( $custom_view || ct_template_output( true ) ) {
		return get_page_template();
	}
	
	return $template;
}
add_filter( 'template_include', 'ct_determine_render_template', 98 );


function ct_eval_condition_template( $template ) {

	$new_template = '';

	if(isset($_REQUEST['action']) && stripslashes($_REQUEST['action']) == 'ct_eval_condition') {
		$nonce  	= $_REQUEST['nonce'];
		$post_id 	= $_REQUEST['post_id'];
		
		// check nonce
		if ( ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
		    // This nonce is not valid.
		    die( 'Security check' );
		}
		
		if ( file_exists(dirname( __FILE__) . '/components/layouts/' . 'condition.php') ) {
			$new_template = dirname( __FILE__) . '/components/layouts/' . 'condition.php';
		}
	}

	if ( '' != $new_template ) {
			return $new_template ;
		}

	return $template;
}

add_filter( 'template_include', 'ct_eval_condition_template', 100 );


/**
 * Render plain shortcode like [oxygen] data shortcode
 *
 * @author Ilya K.
 * @since 3.5
 */

function oxy_render_attribute_dyanmic_data( $template ) {

	$new_template = '';

	if(isset($_REQUEST['action']) && stripslashes($_REQUEST['action']) == 'oxy_render_attribute_dyanmic_data') {
		$nonce  	= $_REQUEST['nonce'];
		$post_id 	= $_REQUEST['post_id'];
		
		// check nonce
		if ( ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
		    // This nonce is not valid.
		    die( 'Security check' );
		}
		
		if ( file_exists(dirname( __FILE__) . '/components/layouts/render-attribute-dyanmic-data.php') ) {
			$new_template = dirname( __FILE__) . '/components/layouts/render-attribute-dyanmic-data.php';
		}
	}

	if ( '' != $new_template ) {
			return $new_template ;
		}

	return $template;
}

add_filter( 'template_include', 'oxy_render_attribute_dyanmic_data', 100 );


/**
 * Registers all the widgets to be rendered to the WP globals
 *
 * @author gagan goraya
 * @since 0.3.4
 */
	
function ct_register_widgets( ) {
	global $_wp_sidebars_widgets, $shortcode_tags;

	if(!(isset($_wp_sidebars_widgets['ct-virtual-sidebar'])))
		$_wp_sidebars_widgets['ct-virtual-sidebar'] = array();

	$content = ct_template_output(true);

	// Find all registered tag names in $content.
	preg_match_all( '@\[([^<>&/\[\]\x00-\x20=]++)@', $content, $matches );
	$tagnames = array_intersect( array_keys( $shortcode_tags ), $matches[1] );

	if(!array_search('ct_widget', $tagnames))
		return;
	
	$pattern = get_shortcode_regex( array('ct_widget') );
	
	preg_match_all( "/".$pattern."/", $content, $matches );

	foreach($matches[3] as $widgetOptions) {
		preg_match('@\"id_base\":\"([^\"]*)\"@', $widgetOptions, $opMatches);
		array_push($_wp_sidebars_widgets['ct-virtual-sidebar'], $opMatches[1]);
	}
}
add_filter( 'template_redirect', 'ct_register_widgets', 19 );


/**
 * Add Cache-Control headers to force page refresh 
 * on browser's back button click
 *
 * @since 0.4.0
 * @author Ilya K.
 */

function ct_add_headers() {

	if ( defined("SHOW_CT_BUILDER") ) {
		header_remove("Cache-Control");
		header("Cache-Control: no-cache, no-store, must-revalidate, max-age=0"); // HTTP 1.1.
	}
}
add_action( 'send_headers', 'ct_add_headers' );


/**
 * Add 'oxygen-body' class for frontend only
 *
 * @since 0.4.0
 * @author Ilya K.
 */

function ct_body_class($classes) {

	if ( ! defined("SHOW_CT_BUILDER") ) {
		$classes[] = 'oxygen-body';
	}
	else {
		$classes[] = 'oxygen-builder-body';	
	}
	
	if ( oxygen_vsb_get_user_edit_mode() == "edit_only" ) {
		$classes[] = 'oxygen-edit-only-mode';
	}

	if ( oxygen_vsb_user_has_enabled_elements() ) {
		$classes[] = 'oxygen-has-enabled-elements';
	}

	if ( oxygen_vsb_user_can_use_design_library() ) {
		$classes[] = 'oxygen-can-use-design-library';
	}

	if ( oxygen_vsb_user_can_use_reusable_parts() ) {
		$classes[] = 'oxygen-can-use-reusable-parts';
	}

	if ( oxygen_vsb_user_can_use_advanced_tab() ) {
		$classes[] = 'oxygen-can-use-advanced-tabs';
	}

	return $classes;
}
add_filter('body_class', 'ct_body_class');

function ct_admin_body_class($classes) {

	if ( oxygen_vsb_get_user_edit_mode() == "edit_only" ) {
		$classes .= ' oxygen-edit-only-mode';
	}

	return $classes;
}
add_filter('admin_body_class', 'ct_admin_body_class');


/**
 * Loading webfonts for the front end, in the <head> section
 *
 * @since 0.3.4
 * @author gagan goraya
 */

function add_web_font($shortcodes=false) {

	if ( defined("SHOW_CT_BUILDER") ) {
		return;
	}

	if ( get_option("oxygen_vsb_disable_google_fonts")=='true' ) {
		return;
	}

	global $header_font_families;
	$header_font_families = array();

	$global_settings = ct_get_global_settings();

	// add default globals
	foreach ( $global_settings['fonts'] as $key => $value ) {
		$header_font_families[] = $value."::".$key;
	}

	// loop all globals in case google fonts were added somewhere
	foreach ($global_settings as $sub_key => $sub_section) {
		// skip 'fonts'
		if ($sub_key == 'fonts') continue;
		if (!is_array($sub_section)) continue;

		foreach ($sub_section as $key => $value) {
            if (strpos($key, "font-family") !== false) {
                $header_font_families[] = $value;
            }
        }
	}

	$json = ct_template_json(true);
	if ( oxygen_json_has_elements( $json ) ) {
		
		if ( strpos($json, '"name":"ct_inner_content"') !== false ) {
			if(isset($_REQUEST['oxy_preview_revision']) && is_numeric($_REQUEST['oxy_preview_revision'])) {
				$inner_contnet_json = Oxygen_Revisions::get_post_meta_db( get_the_ID(), null, true, null, OBJECT, $_REQUEST['oxy_preview_revision'] )->meta_value;
				$inner_contnet_json = oxygen_safe_convert_old_shortcodes_to_json($inner_contnet_json);
			} else {
				$inner_contnet_json = get_post_meta( get_the_ID(), "ct_builder_json", true );
			}
			if ( oxygen_json_has_elements( $inner_contnet_json) ) {
				$json .= $inner_contnet_json;
			}
		}

		preg_match_all( '@font-family":"[^"]*@', $json, $matches );

		if ( isset( $matches[0] ) ) {
			foreach ( $matches[0] as $match ) {
				$match = str_replace('"',"",$match);
				$match = explode(":",$match);
				$header_font_families[] = "$match[1]";
			}
		}
		
	}
	else {
		if (!$shortcodes){
			$shortcodes = ct_template_shortcodes();
		}
		
		global $shortcode_tags;

		// Find all registered tag names in $content.
		preg_match_all( '@\[([^<>&/\[\]\x00-\x20=]++)@', $shortcodes, $matches );
		$tagnames = array_intersect( array_keys( $shortcode_tags ), $matches[1] );
		$pattern  = get_shortcode_regex( $tagnames );

		// replace inner content with shortcodes
		$shortcodes = preg_replace_callback( "/$pattern/", 'oxy_replace_inner_content', $shortcodes );

		// Find all registered tag names in the new updated shortcodes
		preg_match_all( '@\[([^<>&/\[\]\x00-\x20=]++)@', $shortcodes, $matches );
		$tagnames = array_intersect( array_keys( $shortcode_tags ), $matches[1] );
		$pattern  = get_shortcode_regex( $tagnames );

		// obfuscate any oxy conditions, dynamic data
		$shortcodes = ct_obfuscate_shortcode($shortcodes);

		/**
		 * Filter for addons to parse more shortcodes if needed
		 *
		 * @since 3.1
		 * @author Ilya K.
		 */
		
		$shortcodes = apply_filters("oxygen_font_families_check_shortcodes", $shortcodes);

		$i = 0;
		while(strpos($shortcodes, '[') !== false) {
			$i++;
			$new_shortcodes = preg_replace_callback( "/$pattern/", 'get_shortcode_font', $shortcodes );
			// content will stop to change when all shortcodes parsed
			if ($new_shortcodes!==$shortcodes) {
				// update content and continue parsing
				$shortcodes = $new_shortcodes;
			}
			else {
				// all parsed, stop the loop
				break;
			}
			// bulletproof way to stop the loop, I doubt anyone will have 100000+ shortcodes on one page 
			if ($i > 100000) break;
		}
	}
	
	// class based fonts
	$classes = get_option( "ct_components_classes", array() );
	
	if (!$classes) {
		$classes = array();
	}

	// and also custom selectors fonts
	$selectors = get_option( "ct_custom_selectors", array() );
	
	if (!$selectors) {
		$selectors = array();
	}
	
	$classes = array_merge($classes,$selectors);

	if(is_array($classes)) {
		foreach($classes as $key => $class) {
			foreach($class as $statekey => $state) {
				if($statekey == 'media') {
					foreach($state as $bpkey => $bp) {
						foreach($bp as $bpstatekey => $bpstate) {
							foreach($bpstate as $property_key => $value) {
								if(strpos($property_key, 'font-family')!==false) {
									if ( is_array( $value ) ) {
										// handle global fonts
										if ( $value[0] == 'global' ) {

											$settings 	= get_option("ct_global_settings"); 
											$value 		= isset($settings['fonts'][$value[1]])?$settings['fonts'][$value[1]]."::".$value[1]:'';
										}
									}
									else {
										$value = htmlspecialchars_decode($value, ENT_QUOTES);
									}

									// skip empty values
									if ( $value === "" ) {
										continue;
									}

									// make font family accessible for web fonts loader
									$header_font_families[] = "$value";
								}
							}
						}
					}
				}
				else {
					if (is_array($class[$statekey])) {
						foreach ($class[$statekey] as $property_key => $value) {	
					  		if(strpos($property_key, 'font-family')!==false) {
								$value = $class[$statekey][$property_key];
								if ( is_array( $value ) ) {
									// handle global fonts
									if ( $value[0] == 'global' ) {
										
										$settings 	= get_option("ct_global_settings"); 
										$value 		= isset($settings['fonts'][$value[1]])?$settings['fonts'][$value[1]]."::".$value[1]:'';
									}
								}
								else {
									$value = htmlspecialchars_decode($value, ENT_QUOTES);
								}

								// skip empty values
								if ( $value === "" ) {
									continue;
								}

								// make font family accessible for web fonts loader
								$header_font_families[] = "$value";			  			
					  		}
					  	}
					}
			  	}
		  	}
	  	}
	}
	
	/**
	 * Filter for addons to load more Google Fonts if needed
	 *
	 * @since 3.0
	 * @author Ilya K.
	 */
	
	$header_font_families = apply_filters("oxygen_header_font_families", $header_font_families);

	if ( get_option('oxygen_vsb_use_css_for_google_fonts') != true ) {

		// Webfonts.js
		$font_families = ct_get_font_families_string( $header_font_families, $global_settings );

		if ( $font_families ) {
			echo "
			<script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/webfont/1/webfont.js'></script>
			<script type=\"text/javascript\">
			WebFont.load({
				google: {
					families: [$font_families]
				}
			});
			</script>
			";
		}
	}
	else {
		
		// CSS stylesheet
		$family = ct_get_font_families_string( $header_font_families, $global_settings, true );
		
		if ( $family ) {
			echo '<link rel="preload" as="style" href="https://fonts.googleapis.com/css?family='.$family.'" >'."\r\n";
			echo '<link rel="stylesheet" href="https://fonts.googleapis.com/css?family='.$family.'">'."\r\n";
		}
	}
	
}

function oxy_replace_inner_content($match) {
	if (strpos($match[2], "ct_inner_content")===0) {
		if(isset($_REQUEST['oxy_preview_revision']) && is_numeric($_REQUEST['oxy_preview_revision'])) {
			$shortcodes = Oxygen_Revisions::get_post_meta_db( get_the_ID(), null, true, null, OBJECT, $_REQUEST['oxy_preview_revision'] )->meta_value;
		} else {
			$shortcodes = get_post_meta( get_the_ID(), "ct_builder_shortcodes", true );
		}
		return $shortcodes;
	}
	else {
		return $match[0];
	}
}

function get_shortcode_font($m) {
	
	global $header_font_families;
	
	$parsed_atts= shortcode_parse_atts( $m[3] );

	if (!isset($parsed_atts['ct_options'])) {
		return substr($m[0], 1, -1);
	}
	$decoded_atts = json_decode( $parsed_atts['ct_options'], true );
	
	
	if(!is_array($decoded_atts))
		return substr($m[0], 1, -1);
	
	$states = array();

	// get states styles (original, :hover, ...) from shortcode atts
	foreach ( $decoded_atts as $key => $state_params ) {
		if ( is_array( $state_params ) ) {
			$states[$key] = $state_params;
		}
	}

	foreach ( $states as $key => $atts ) {
		
		//echo $key."\n";
		if ( in_array($key, array("classes", "name", "selector") ) ) {
			continue;
		}

		if( $key == 'media') {

			foreach($atts as $bpkey => $bp) {
				foreach($bp as $bpstatekey => $bpstate) {
					foreach($bpstate as $property_key => $value) {
						if(strpos($property_key, 'font-family')!==false) {
							if ( is_array( $value ) ) {
								// handle global fonts
								if ( $value[0] == 'global' ) {
									
									$settings 	= get_option("ct_global_settings"); 
									$value 		= $settings['fonts'][$value[1]];
								}
							}
							else {
								$value = htmlspecialchars_decode($value, ENT_QUOTES);
							}

							// skip empty values
							if ( $value === "" ) {
								continue;
							}

							// make font family accessible for web fonts loader
							$header_font_families[] = "$value";
		  				}
					}
				}
			}
		}
		else {
			// loop trough properties (background, color, ...)
			foreach ( $atts as $prop => $value ) {					

				if ( is_array( $value ) ) {
					// handle global fonts
					if ( $prop == "font-family" && $value[0] == 'global' ) {
						
						$settings = get_option("ct_global_settings"); 
						// make it so deleted global fonts don't generate PHP notices, and so we can track the reason in CSS output
						$value = isset($settings['fonts'][$value[1]])?$settings['fonts'][$value[1]]:"No Global Font: ".$value[1];
					}
				}
				else {
					$value = htmlspecialchars_decode($value, ENT_QUOTES);
				}

				// skip empty values
				if ( $value === "" ) {
					continue;
				}

				// make font family accessible for web fonts loader
				if (strpos($prop, 'font-family')!==false && is_string( $value ) ) {
					$header_font_families[] = "$value";
				}

			} // endforeach
		}
		
	}
	
	return substr($m[0], 1, -1);
	
}
add_action( 'oxygen_enqueue_scripts', 'add_web_font', 10 );

/**
 * Set site hash if not exist
 */

function oxygen_update_license_hash() {

	//delete_option("oxygen_license_updated");
	if ( ! get_option("oxygen_license_updated") ) {
		
		$old = get_option( 'oxygen_license_key' );

		if ( $old ) {

			global $oxygen_edd_updater;
			
			update_option( 'oxygen_license_key', '' );
			$oxygen_edd_updater->activate_license();

			update_option( 'oxygen_license_key', $old );
			$oxygen_edd_updater->activate_license();
		}
		else {
			update_option( 'oxygen_license_key', '' );
		}

		update_option("oxygen_license_updated", true);
	}
}
add_action( 'after_setup_theme', 'oxygen_update_license_hash' );


/**
 * Get global settings
 *
 * @since 1.1.1
 * @author Ilya K.
 */

function ct_get_global_settings($return_defaults = false) {

	// get saved settings
	//update_option("ct_global_settings",array());
	$settings = get_option("ct_global_settings",array());
	
	// defaults
	$defaults = array ( 
				"fonts" => array(
						'Text' 		=> 'Open Sans',
						'Display' 	=> 'Source Sans Pro' 
					),
				"indicateParents" => 'true',
				"classSuggestionsLimit" => '5',
				"headings" => array( 
						'H1' => array( 'font-size' => '36', 'font-size-unit' => 'px', 'font-weight' => 700, 'color' => '' ),
						'H2' => array( 'font-size' => '30', 'font-size-unit' => 'px', 'font-weight' => '', 'color' => '' ),
						'H3' => array( 'font-size' => '24', 'font-size-unit' => 'px', 'font-weight' => '', 'color' => '' ),
						'H4' => array( 'font-size' => '20', 'font-size-unit' => 'px', 'font-weight' => '', 'color' => '' ),
						'H5' => array( 'font-size' => '18', 'font-size-unit' => 'px', 'font-weight' => '', 'color' => '' ),
						'H6' => array( 'font-size' => '16', 'font-size-unit' => 'px', 'font-weight' => '', 'color' => '' ),
						
					),
				"body_text" => 
					array( 
						'font-size' 		=> '16', 
						'font-size-unit' 	=> 'px', 
						'font-weight' 		=> '400', 
						'line-height' 		=> '1.6', 
						'color' 			=> '#404040' 
					),
				"links" => array(
					"all" => 
						array( 
							'color' 			=> '#0074db',
							'font-weight' 		=> '', 
							'text-decoration'	=> 'none',
							'hover_color' 		=> '',
							'hover_text-decoration' => 'none'						
						),
					"text_link" => 
						array( 
							'color' 			=> '',
							'font-weight' 		=> '', 
							'text-decoration'	=> '',
							'hover_color' 		=> '',
							'hover_text-decoration' => ''						
						),
					"link_wrapper" => 
						array( 
							'color' 			=> '',
							'font-weight' 		=> '', 
							'text-decoration'	=> '',
							'hover_color' 		=> '',
							'hover_text-decoration' => ''						
						),
					"button" => 
						array( 
							'font-weight' 			=> '', 
							'border-radius' 		=> '3',
							'border-radius-unit' 	=> 'px',
						),
					),
				"sections" => 
					array( 
						'container-padding-top' 		=> '75',
						'container-padding-top-unit' 	=> 'px',
						'container-padding-bottom' 		=> '75',
						'container-padding-bottom-unit' => 'px',
						'container-padding-left' 		=> '20',
						'container-padding-left-unit' 	=> 'px',
						'container-padding-right' 		=> '20',
						'container-padding-right-unit' 	=> 'px',
					),
				"columns" => 
					array( 
						'padding-top' 			=> '20',
						'padding-top-unit' 		=> 'px',
						'padding-bottom' 		=> '20',
						'padding-bottom-unit' 	=> 'px',
						'padding-left' 			=> '20',
						'padding-left-unit' 	=> 'px',
						'padding-right' 		=> '20',
						'padding-right-unit' 	=> 'px',
					),
				"max-width" => 1120,
				"breakpoints" => array(
					'tablet' => 992,
					'phone-landscape' => 768,
					'phone-portrait' => 480,
				),
				"aos" => array(
						'type' 						=> '',
						'duration' 					=> '',
						'easing' 					=> '',
						'offset' 					=> '',
						'delay' 					=> '',
						'anchor-placement' 			=> '',
						'once' 						=> '',
						'mirror' 					=> '',
						'disable'					=> '',
					),
				"scripts" => array(
						"scroll_to_hash" 			=> '',
						"scroll_to_hash_time"		=> '',
						'scroll_to_hash_offset' 	=> '',
				)
			);

	// since 3.0
	$defaults = apply_filters("oxy_global_settings_defaults", $defaults);

	if ($return_defaults) {
		return $defaults;
	}
	else {
		return wp_parse_args($settings, $defaults);
	}
}

/**
 * Get global settings
 *
 * @since 2.1s
 * @author Ilya K.
 */

function oxy_get_global_colors($return_defaults = false) {

	// get saved settings
	//update_option("oxygen_vsb_global_colors",array());
	$settings = get_option("oxygen_vsb_global_colors",array());
	
	// defaults
	$defaults = array ( 
				"colorsIncrement" => 0,
				"setsIncrement" => 1,
				"colors" => array(
					// no colors by default
				),
				"sets" => array(
					// the only default Color Set
					array(
						"id" => 0,
						"name" => __("Global Colors","oxygen")
					),
				)
			);

	return wp_parse_args($settings, $defaults);
}


/**
 * Get page settings
 *
 * @since 1.1.1
 * @author Ilya K.
 */

function ct_get_page_settings($only_template=false) {

	global $oxy_ajax_post_id;

	if ($oxy_ajax_post_id) {
		$id = $oxy_ajax_post_id;
	}
	else {
		$id = get_the_ID();
	}
	
	$defaults = array(
				"max-width" => "",
				"overlay-header-above" => "",
				"aos" => array(
						'type' 						=> '',
						'duration' 					=> '',
						'easing' 					=> '',
						'offset' 					=> '',
						'delay' 					=> '',
						'anchor' 					=> '',
						'anchor-placement' 			=> '',
						'once' 						=> '',
						'disable'					=> '',
					),
				"scripts" => array(
						'scroll_to_hash' 			=> '',
						'scroll_to_hash_time' 		=> '',
						'scroll_to_hash_offset' 	=> '',
					)
			);

	$page_settings = get_post_meta( $id, "ct_page_settings", true );
	if ( !is_array($page_settings) ) {
		$page_settings = array();
	}
	
	// if page rendered with a template get the template settings as well
	$template_settings = $defaults;
	global $ct_template_id;

	// fix to get parent template id in builder
	if (defined("SHOW_CT_BUILDER") && get_post_type()=="ct_template") {
		$ct_template_id = get_post_meta( $id, "ct_parent_template", true);
	}

	if (isset($ct_template_id)) {

		// if template has a parent
		$parent_settings = $defaults;
		global $ct_parent_template_id;
		if (isset($ct_parent_template_id)) {
			$parent_settings = get_post_meta( $ct_parent_template_id, "ct_page_settings", true );
			if (!is_array($parent_settings)) {
				$parent_settings = $defaults;
			}
			$parent_settings = array_replace_recursive( 
				$defaults,
				oxygen_array_filter_recursive($parent_settings)
			);
		}
		else {
			$parent_settings = $defaults;
		}

		$template_settings = get_post_meta( $ct_template_id, "ct_page_settings", true );
		if (!is_array($template_settings)) {
			$template_settings = array();
		}

		$template_settings = array_replace_recursive( 
			$parent_settings,
			oxygen_array_filter_recursive($template_settings)
		);
	}
	else {
		$template_settings = $defaults;
	}

	if ($only_template) {

		return $template_settings;
	}

	// finally return 
	$settings = array_replace_recursive( 
		$template_settings,
		oxygen_array_filter_recursive($page_settings)
	);

	return $settings;
}

/**
 * Helper function to remove empty values from multidimensional array
 *
 * @since 2.2
 * @author https://stackoverflow.com/a/21319233/2198798
 */

function oxygen_array_filter_recursive($array) {
   foreach ($array as $key => &$value) {
      if (empty($value)) {
         unset($array[$key]);
      }
      else {
         if (is_array($value)) {
            $value = oxygen_array_filter_recursive($value);
            if (empty($value)) {
               unset($array[$key]);
            }
         }
      }
   }

   return $array;
}


/**
 * Minify CSS
 *
 * @since 1.1.1
 * @author Ilya K.
 */

function oxygen_css_minify( $css ) {
	
	// Remove comments
	$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);

	// Remove space after colons
	$css = str_replace(': ', ':', $css);

	// Remove new lines and tabs
	$css = str_replace(array("\r\n", "\r", "\n", "\t"), '', $css);

	// Remove excessive spaces
	$css = str_replace(array("     ", "    ", "   ", "  "), ' ', $css);

	// Remove space near commas
	$css = str_replace(', ', ',', $css);
	$css = str_replace(' ,', ',', $css);

	// Remove space before/after brackets
	$css = str_replace('{ ', '{', $css);
	$css = str_replace('} ', '}', $css);
	$css = str_replace(' {', '{', $css);
	$css = str_replace(' }', '}', $css);

	// Remove last semicolon
	$css = str_replace(';}', '}', $css);

	// Remove spaces after semicolon
	$css = str_replace('; ', ';', $css);

	return $css;
}

/**
 * Return body class string in response to GET request
 *
 * @since 1.4
 * @author Ilya K. 
 */

function ct_get_body_class() {

	if ( isset( $_GET['ct_get_body_class'] ) && $_GET['ct_get_body_class'] ) {
		echo join( ' ', get_body_class() );
		die();
	}
}
add_action( 'template_redirect', 'ct_get_body_class', 9 );


/**
 * returns the query_vars for the provided single ID
 * used the logic from $wp->process_request()
 *
 * @since 0.3.4
 * @author gagan goraya
 */

function get_query_vars_from_id($id = false) {

	if(!$id)
		return array();
	
	global $wp_rewrite, $wp;

	$public_query_vars = $wp->public_query_vars;
	$private_query_vars = $wp->private_query_vars;

	$permalink = get_permalink($id);
	$extra_query_vars = '';

	// if permalinks not enabeld
	if(!get_option('permalink_structure')) {
		list($temp, $extra_query_vars) = explode('?', $permalink);
	}

	$query_vars = array();
	$post_type_query_vars = array();

	if ( !is_array( $extra_query_vars ) && !empty( $extra_query_vars )) {
		parse_str( $extra_query_vars, $extra_query_vars );
	}
	// Process PATH_INFO, REQUEST_URI, and 404 for permalinks.

	// Fetch the rewrite rules.
	$rewrite = $wp_rewrite->wp_rewrite_rules();

	if ( ! empty($rewrite) ) {
		// If we match a rewrite rule, this will be cleared.
		$error = '404';
		

		$pathinfo = isset( $_SERVER['PATH_INFO'] ) ? $_SERVER['PATH_INFO'] : '';
		list( $pathinfo ) = explode( '?', $pathinfo );
		$pathinfo = str_replace( "%", "%25", $pathinfo );

		list( $req_uri ) = explode( '?', $_SERVER['REQUEST_URI'] );
		$req_uri = str_replace(get_site_url(), '', $permalink);
		
		$home_path = trim( parse_url( home_url(), PHP_URL_PATH ), '/' );
		$home_path_regex = sprintf( '|^%s|i', preg_quote( $home_path, '|' ) );

		// Trim path info from the end and the leading home path from the
		// front. For path info requests, this leaves us with the requesting
		// filename, if any. For 404 requests, this leaves us with the
		// requested permalink.
		$req_uri = str_replace($pathinfo, '', $req_uri);
		$req_uri = trim($req_uri, '/');
		$req_uri = preg_replace( $home_path_regex, '', $req_uri );
		$req_uri = trim($req_uri, '/');
		$pathinfo = trim($pathinfo, '/');
		$pathinfo = preg_replace( $home_path_regex, '', $pathinfo );
		$pathinfo = trim($pathinfo, '/');
		
		

		// The requested permalink is in $pathinfo for path info requests and
		//  $req_uri for other requests.
		if ( ! empty($pathinfo) && !preg_match('|^.*' . $wp_rewrite->index . '$|', $pathinfo) ) {
			$request = $pathinfo;
		} else {
			// If the request uri is the index, blank it out so that we don't try to match it against a rule.
			if ( $req_uri == $wp_rewrite->index )
				$req_uri = '';
			$request = $req_uri;
		}

		// Look for matches.
		$request_match = $request;
		if ( empty( $request_match ) ) {

			// An empty request could only match against ^$ regex
			if ( isset( $rewrite['$'] ) ) {
				$matched_rule = '$';
				$query = $rewrite['$'];
				$matches = array('');
			}
		} else {

			foreach ( (array) $rewrite as $match => $query ) {
				// If the requesting file is the anchor of the match, prepend it to the path info.
				if ( ! empty($req_uri) && strpos($match, $req_uri) === 0 && $req_uri != $request )
					$request_match = $req_uri . '/' . $request;

				if ( preg_match("#^$match#", $request_match, $matches) ||
					preg_match("#^$match#", urldecode($request_match), $matches) ) {

					if ( $wp_rewrite->use_verbose_page_rules && preg_match( '/pagename=\$matches\[([0-9]+)\]/', $query, $varmatch ) ) {
						// This is a verbose page match, let's check to be sure about it.
						$page = get_page_by_path( $matches[ $varmatch[1] ] );
						if ( ! $page ) {
					 		continue;
						}

						$post_status_obj = get_post_status_object( $page->post_status );
						if ( ! $post_status_obj->public && ! $post_status_obj->protected
							&& ! $post_status_obj->private && $post_status_obj->exclude_from_search ) {
							continue;
						}
					}

					// Got a match.
					$matched_rule = $match;
					break;
				}
			}

		}

		if ( isset( $matched_rule ) ) {
			// Trim the query of everything up to the '?'.
			$query = preg_replace("!^.+\?!", '', $query);

			// Substitute the substring matches into the query.
			$query = addslashes(WP_MatchesMapRegex::apply($query, $matches));

			$matched_query = $query;

			// Parse the query.
			parse_str($query, $perma_query_vars);

			// If we're processing a 404 request, clear the error var since we found something.
			if ( '404' == $error )
				unset( $error, $_GET['error'] );
		}

		// If req_uri is empty or if it is a request for ourself, unset error.
		if ( empty($request) || $req_uri == 'index.php' || strpos($_SERVER['PHP_SELF'], 'wp-admin/') !== false ) {
			unset( $error, $_GET['error'] );
		}
	}

	$public_query_vars = apply_filters( 'query_vars', $public_query_vars );

	foreach ( get_post_types( array(), 'objects' ) as $post_type => $t ) {
		if ( is_post_type_viewable( $t ) && $t->query_var ) {
			$post_type_query_vars[$t->query_var] = $post_type;
		}
	}

	foreach ( $public_query_vars as $wpvar ) {

		if ( isset( $extra_query_vars[$wpvar] ) )
			$query_vars[$wpvar] = $extra_query_vars[$wpvar];
		elseif ( isset( $_POST[$wpvar] ) )
			$query_vars[$wpvar] = $_POST[$wpvar];
		elseif ( isset( $_GET[$wpvar] ) )
			$query_vars[$wpvar] = $_GET[$wpvar];
		elseif ( isset( $perma_query_vars[$wpvar] ) )
			$query_vars[$wpvar] = $perma_query_vars[$wpvar];

		if ( !empty( $query_vars[$wpvar] ) ) {
			if ( ! is_array( $query_vars[$wpvar] ) ) {
				$query_vars[$wpvar] = (string) $query_vars[$wpvar];
			} else {
				foreach ( $query_vars[$wpvar] as $vkey => $v ) {
					if ( !is_object( $v ) ) {
						$query_vars[$wpvar][$vkey] = (string) $v;
					}
				}
			}

			if ( isset($post_type_query_vars[$wpvar] ) ) {
				$query_vars['post_type'] = $post_type_query_vars[$wpvar];
				$query_vars['name'] = $query_vars[$wpvar];
			}
		}
	}

	// Convert urldecoded spaces back into +
	foreach ( get_taxonomies( array() , 'objects' ) as $taxonomy => $t )
		if ( $t->query_var && isset( $query_vars[$t->query_var] ) )
			$query_vars[$t->query_var] = str_replace( ' ', '+', $query_vars[$t->query_var] );

	// Don't allow non-public taxonomies to be queried from the front-end.
	if ( ! is_admin() ) {
		foreach ( get_taxonomies( array( 'public' => false ), 'objects' ) as $taxonomy => $t ) {
			/*
			 * Disallow when set to the 'taxonomy' query var.
			 * Non-public taxonomies cannot register custom query vars. See register_taxonomy().
			 */
			if ( isset( $query_vars['taxonomy'] ) && $taxonomy === $query_vars['taxonomy'] ) {
				unset( $query_vars['taxonomy'], $query_vars['term'] );
			}
		}
	}

	// Limit publicly queried post_types to those that are publicly_queryable
	if ( isset( $query_vars['post_type']) ) {
		$queryable_post_types = get_post_types( array('publicly_queryable' => true) );
		if ( ! is_array( $query_vars['post_type'] ) ) {
			if ( ! in_array( $query_vars['post_type'], $queryable_post_types ) )
				unset( $query_vars['post_type'] );
		} else {
			$query_vars['post_type'] = array_intersect( $query_vars['post_type'], $queryable_post_types );
		}
	}

	// Resolve conflicts between posts with numeric slugs and date archive queries.
	$query_vars = wp_resolve_numeric_slug_conflicts( $query_vars );

	foreach ( (array) $private_query_vars as $var) {
		if ( isset($extra_query_vars[$var]) )
			$query_vars[$var] = $extra_query_vars[$var];
	}

	if ( isset($error) )
		$query_vars['error'] = $error;

	
	$query_vars = apply_filters( 'request', $query_vars );

	return $query_vars;
}


/**
 * This is used to offset the IDs of outer template, when inner_content component is used
 *
 * @since 1.2.0
 * @author Gagan S Goraya.
 */

function obfuscate_ids($matches) {
	return $matches[1].((intval($matches[2]) > 0)?(intval($matches[2])+100000):0);
}

function obfuscate_selectors($matches) {
	$id =  intval(substr($matches[2], strrpos($matches[2], '_')+1 , strlen($matches[2])-strrpos($matches[2], '_')-1));
	$prefix = substr($matches[2] , 0, strrpos($matches[2], '_')+1);
	return $matches[1].$prefix.(($id > 0)?($id+100000):0).'_post_';
}


/**
 * Gradient helper functions
 *
 * @since 2.1
 * @author Gagan S Goraya.
 */

function ct_bgLayersFilterCallback($color) {
	return isset($color['value']) && strlen($color['value']) > 0;
}

function ct_BgLayersMapColorStrings($color) {
	return $color['value'] . (isset($color['position']) && !empty($color['position']) ? ' ' . $color['position'] . $color['position-unit']: '');
}

function ct_map_global_gradient_colors($color) {
	$color['value'] = oxygen_vsb_get_global_color_value($color['value']);
	return $color;
}

function ct_filterGradientColors($color) {
	return isset($color['value']) &&  $color['value'];
}


/**
 * Generate CSS output for background gradient settings
 *
 * @since 1.2.0
 * @author Gagan S Goraya.
 */

function ct_getBackgroundLayersCSS($stateOptions, $default_atts = false) {

	$bgColor = isset($options['background-color']) ? oxygen_vsb_get_global_color_value($options['background-color']) : '';
	$styles = array();
	$backgroundSize = array();
	$gradientColors = array();

	if(isset($stateOptions['gradient']) && isset($stateOptions['gradient']['colors'])) {
		$gradientColors = $stateOptions['gradient']['colors'];
	}

	$gradientColors = array_filter($gradientColors, 'ct_filterGradientColors');
	$gradientColors = array_map('ct_map_global_gradient_colors', $gradientColors);
		
	$styleBuffer = '';

	if(sizeof($gradientColors) > 0) {

		if(isset($stateOptions['gradient']['gradient-type']) && $stateOptions['gradient']['gradient-type'] == 'radial') {

			$styleBuffer .= ' radial-gradient(';

			$radialParams = '';

			if(isset($stateOptions['gradient']['radial-shape']) && $stateOptions['gradient']['radial-shape']) {
				$radialParams .= ' '.$stateOptions['gradient']['radial-shape'];
			}

			if(isset($stateOptions['gradient']['radial-size']) && $stateOptions['gradient']['radial-size']) {
				$radialParams .= ' '.$stateOptions['gradient']['radial-size'];
			}

			if(isset($stateOptions['gradient']['radial-position-left']) && $stateOptions['gradient']['radial-position-left']) {
				$radialParams .= ' at '.$stateOptions['gradient']['radial-position-left'].(isset($stateOptions['gradient']['radial-position-left-unit'])?$stateOptions['gradient']['radial-position-left-unit'] : 'px');

				if(isset($stateOptions['gradient']['radial-position-top']) && $stateOptions['gradient']['radial-position-top']) {
					$radialParams .= ' '.$stateOptions['gradient']['radial-position-top'].(isset($stateOptions['gradient']['radial-position-top-unit'])?$stateOptions['gradient']['radial-position-top-unit'] : 'px');
				}
			}

			if(strlen($radialParams) > 0) {
				$styleBuffer .= $radialParams.', ';
			}
		}
		else {
			$styleBuffer .= ' linear-gradient(';

			if(isset($stateOptions['gradient']['linear-angle']) && !empty($stateOptions['gradient']['linear-angle'])) {
				$styleBuffer .= $stateOptions['gradient']['linear-angle'].'deg, ';
			}
		}


		if($gradientColors) {

			$filteredColors = array_filter($gradientColors, 'ct_bgLayersFilterCallback');
			$colorStrings = array_map('ct_BgLayersMapColorStrings', $filteredColors);

			// if it is a single color, repeat it once to show a solid layer
			if(sizeof($colorStrings) === 1) {
				array_push($colorStrings, $colorStrings[0]);
			}

			$styleBuffer .= implode(', ', $colorStrings);
		}

		$styleBuffer .= ')';

		if(strlen($styleBuffer) > 0) {
			array_push($styles, $styleBuffer);
			array_push($backgroundSize, 'auto');
		}
	}

	if(isset($stateOptions['overlay-color']) && !empty($stateOptions['overlay-color'])) {
		array_push($styles, 'linear-gradient(' .oxygen_vsb_get_global_color_value($stateOptions['overlay-color']). ', '.oxygen_vsb_get_global_color_value($stateOptions['overlay-color']).')');
		array_push($backgroundSize, 'auto');
	}
	
	if(isset($stateOptions['background-size']) && strlen(trim($stateOptions['background-size'])) > 0) {

		$styleBuffer = '';	
		if($stateOptions['background-size'] == 'manual') {
			if(isset($stateOptions['background-size-width']) && strlen(trim($stateOptions['background-size-width'])) > 0) {
				$styleBuffer .= ' '.trim($stateOptions['background-size-width']).trim($stateOptions['background-size-width-unit']);
			}
			else {
				$styleBuffer .= ' auto';
			}
			
			if(isset($stateOptions['background-size-height']) && strlen(trim($stateOptions['background-size-height'])) > 0) {
				$styleBuffer .= ' '.trim($stateOptions['background-size-height']).trim($stateOptions['background-size-height-unit']);
			}
			else {
				$styleBuffer .= ' auto';
			}
		}
		else {
			$styleBuffer .= ' ' . $stateOptions['background-size'];
		}

		if(strlen($styleBuffer) > 0) {
			array_push($backgroundSize, $styleBuffer);
		}
	}
	else { 
		$backgroundSize = array(); // if no size is specified, let all fall back to default, dont worry about size for gradient and overlay, those were just fillers
	}

	if(isset($stateOptions['background'])) {
		array_push($styles, 'url('.$stateOptions['background'].')');
	}

	if(isset($stateOptions['background-image'])) {
		array_push($styles, 'url('.do_shortcode($stateOptions['background-image']).')');
	}

	$image = implode(', ', $styles);
	$style = "";

	if ($image!=='') {
		$style = 'background-image:' . implode(', ', $styles) .';';
	}

	// if(strlen($style) > 0 ) {
	// 	if($bgColor) {
	// 		$style .= ', linear-gradient(' .$bgColor. ', '.$bgColor.')';
	// 	}

	// 	$style .= ';';
	// }

	if(sizeof($backgroundSize) > 0) {
		$style .= 'background-size:' . implode(', ', $backgroundSize) . ';';
	}
		
	return $style;
}


/**
 * This is used to offset the depths of inner_content shortcodes when it has to be contained within an outer template
 *
 * @since 1.2.0
 * @author Gagan S Goraya.
 */

function ct_offsetDepths($matches) {
	global $ct_offsetDepths_source;
	//print_r($matches);
	$tag = $matches[2];

	$depth = is_numeric($matches[3])?intval($matches[3]):1;
	$newdepth = $depth;
	// if tag has a trailing _, remove it
	if(substr($tag, strlen($tag)-1, 1) == '_')
		$tag = substr($tag, 0, strlen($tag)-1);

	if(isset($ct_offsetDepths_source[$tag])) {
		$newdepth += $ct_offsetDepths_source[$tag];
	}

	return $matches[1].$tag.(($newdepth > 1)?'_'.$newdepth:'');
	
}

function ct_undoOffsetDepths($matches) {
	global $ct_offsetDepths_source;
	//print_r($matches);
	$tag = $matches[2];
	$depth = is_numeric($matches[3])?intval($matches[3]):1;
	$newdepth = $depth;
	// if tag has a trailing _, remove it
	if(substr($tag, strlen($tag)-1, 1) == '_')
		$tag = substr($tag, 0, strlen($tag)-1);

	if(isset($ct_offsetDepths_source[$tag])) {
		$newdepth -= $ct_offsetDepths_source[$tag];
	}
	return $matches[1].$tag.(($newdepth > 1)?'_'.$newdepth:'');
	
}

function set_ct_offsetDepths_source($parent_id, $shortcodes) {

	global $ct_offsetDepths_source;
	$ct_offsetDepths_source = array();
	$last_parent_id = false;
	$matches = array();
	while($parent_id > 0 && $parent_id !== $last_parent_id) {
		
		preg_match_all("/\[(ct_[^\s\[\]\d]*)[_]?([0-9]?)[^\]]*ct_id[\"|\']?:$parent_id\,[\"|\']?ct_parent[\"|\']?:(\d*)\,/", $shortcodes, $matches);
		
		$last_parent_id = $parent_id;
		$parent_id = intval($matches[3][0]);
		$depth = is_numeric($matches[2][0])?intval($matches[2][0]):1;
		$tag = $matches[1][0];

		// if tag has a trailing _, remove it
		if(substr($tag, strlen($tag)-1, 1) == '_')
			$tag = substr($tag, 0, strlen($tag)-1);
		//echo $tag."  ".$depth."  ".$parent_id."\n";

		if(isset($ct_offsetDepths_source[$tag]) ) {
			if($ct_offsetDepths_source[$tag] < $depth) {
				$ct_offsetDepths_source[$tag] = $depth;
			}
		}
		else {
			$ct_offsetDepths_source[$tag] = $depth;
		}

	}
}


/**
 * If post/page has Oxygen template applied return empty stylesheet URL, so theme functions.php never run   
 *
 * @since 1.4
 * @author Ilya K.
 */

function ct_disable_theme_load( $stylesheet_dir ) {
	// disable theme entirely for now
	return "fake";
}

// Need to remove for both parent and child themes
add_filter("template_directory", "ct_disable_theme_load", 1, 1);
add_filter("stylesheet_directory", "ct_disable_theme_load", 1, 1);

// WP 6.4 extra fix
add_action( 'setup_theme', function () {
	$GLOBALS['wp_template_path']   = null;
	$GLOBALS['wp_stylesheet_path'] = null;
} );


/**
 * Filter template name so plugins don't confuse Oxygen with any other theme  
 *
 * @since 1.4.1
 * @author Ilya K.
 */

function ct_oxygen_template_name($template) {
	return "oxygen-is-not-a-theme";
}
add_filter("template", "ct_oxygen_template_name");


/**
 * Disable theme validation
 *
 * @since 1.4.1
 * @author Ilya K.
 */

add_filter("validate_current_theme", "__return_false");


/**
 * @param string $type Type of content to filter.  Current options: page_settings, global_settings, style_sheets, 'classes', 'custom_selectors', 'style_sets'
 * @param array $content
 *
 * @return array Filtered array of content
 */
function ct_filter_content( $type, $content = array() ) {

	$filter_keys = false;
	switch ( $type ) {
		case 'page_settings':
			$allowed_content = array( 
				'max-width' => 'sanitize_text_field',
				'aos' => array(
						'/^.*$/' => 'sanitize_text_field'
					),
				'scripts' => array(
					'/^.*$/' => 'sanitize_text_field',
				),
				'/^.*$/' => 'sanitize_text_field',
			);
			$filter_keys = false;
			break;
		case 'global_settings':
			$allowed_content = array(
				'fonts' => array(
					'Text' => 'sanitize_text_field',
					'Display' => 'sanitize_text_field',
					'/^.*$/' => 'sanitize_text_field'
				),
				'fontsOptions' => array(
					'/^.*$/' => array(
						'/^.*$/' => 'sanitize_text_field'
					),
				),
				'scripts' => array(
					'/^.*$/' => 'sanitize_text_field',
				),
				'indicateParents' => 'sanitize_text_field',
				'classSuggestionsLimit' => 'sanitize_text_field',
				'headings' => array(
					'/^.*$/' => array(
                        '/^.*-unit$/' => 'ct_filter_option_unit',  // unit fields
						'/^.*$/' => 'sanitize_text_field'
					),
				),
				'body_text' => array(
                    '/^.*-unit$/' => 'ct_filter_option_unit',  // unit fields
					'/^.*$/' => 'sanitize_text_field'
				),
				'links' => array(
					'/^.*$/' => array(
                        '/^.*-unit$/' => 'ct_filter_option_unit',  // unit fields
						'/^.*$/' => 'sanitize_text_field'
					),
				),
				'sections' => array(
                    '/^.*-unit$/' => 'ct_filter_option_unit',  // unit fields
					'/^.*$/' => 'sanitize_text_field'
				),
				'columns' => array(
					'/^.*$/' => 'sanitize_text_field'
				),
				'aos' => array(
					'/^.*$/' => 'sanitize_text_field'
				),
				'woo' => array(
                    '/^.*-unit$/' => 'ct_filter_option_unit',  // unit fields
					'/^.*$/' => 'sanitize_text_field'
				),
				'max-width' => 'sanitize_text_field',
				'breakpoints' => array(
					'/^.*$/' => 'sanitize_text_field'
				)
			);
			$filter_keys = false;
			break;
		case 'style_sheets':
			$allowed_content = array(
				'/^.*$/' => array(
					'css' => 'base64_encode',
					'id' => 'intval',
					'name' => 'sanitize_html_class',
					'status' => 'intval',
					'parent' => 'intval',
					'folder' => 'intval',
					'/^.*$/' => 'sanitize_text_field'
				)
			);
			//$filter_keys     = 'sanitize_html_class';
			break;
			case 'style_sheets_admin':
				$allowed_content = array(
					'/^.*$/' => array(
						//'css' => 'base64_encode', already received encoded
						'id' => 'intval',
						'name' => 'sanitize_html_class',
						'status' => 'intval',
						'parent' => 'intval',
						'folder' => 'intval',
						'/^.*$/' => 'sanitize_text_field'
					)
				);
				//$filter_keys     = 'sanitize_html_class';
				break;
		case 'classes':
			$allowed_content = array(
				'/^.*$/' => array(  // Class name
					'key' => 'sanitize_html_class',
					'parent' => 'sanitize_text_field',
					'media' => array(
						'/^.*$/' => array(  // breakpoints
							'/^.*$/' => array(  // States
								'/font-family$/'  => 'font-family',
								'transform' => array(
									'/^.*$/' => array(
										'/^.*$/' => 'sanitize_text_field'
									)
								),
								'grid-child-rules' => array(
									'/^.*$/' => array(
										'/^.*$/' => 'sanitize_text_field'
									)
								),
								'grid-all-children-rule' => array(
									'/^.*$/' => 'sanitize_text_field'
								),
								'gradient' => array(
									'colors' => array(
										'/^.*$/' => array(
											'/^.*$/' => 'sanitize_text_field'
										)
									),
									'/^.*$/' => 'sanitize_text_field',
								),
                                '/^.*-unit$/' => 'ct_filter_option_unit',  // unit fields
								'/^.*$/' => 'sanitize_text_field',  // Arbitrary fields
							)
						)
					),
					'/^.*$/' => array(  // States
						'/font-family$/'  => 'font-family',
						'transform' => array(
							'/^.*$/' => array(
								'/^.*$/' => 'sanitize_text_field'
							)
						),
						'grid-child-rules' => array(
							'/^.*$/' => array(
								'/^.*$/' => 'sanitize_text_field'
							)
						),
						'grid-all-children-rule' => array(
							'/^.*$/' => 'sanitize_text_field'
						),
						'gradient' => array(
							'colors' => array(
								'/^.*$/' => array(
									'/^.*$/' => 'sanitize_text_field'
								)
							),
							'/^.*$/' => 'sanitize_text_field',
						),
						'/^.*-unit$/' => 'ct_filter_option_unit',  // unit fields
						'/^.*$/' => 'sanitize_text_field',  // Arbitrary fields
					)
				)
			);
			$filter_keys = 'sanitize_text_field';
			break;
		case 'custom_selectors':
			$allowed_content = array(
				'/^.+$/' => array(  // Class name
					'key' => 'sanitize_text_field',
					'status' => 'intval',
					'set_name' => 'sanitize_text_field',
					'friendly_name' => 'sanitize_text_field',
					'parent' => 'sanitize_text_field',
					'media' => array(
						'/^.*$/' => array(  // breakpoints
							'/^.*$/' => array(  // States
								'/font-family$/'  => 'font-family',
								'transform' => array(
									'/^.*$/' => array(
										'/^.*$/' => 'sanitize_text_field'
									)
								),
								'gradient' => array(
									'colors' => array(
										'/^.*$/' => array(
											'/^.*$/' => 'sanitize_text_field'
										)
									),
									'/^.*$/' => 'sanitize_text_field',
								),
                                '/^.*-unit$/' => 'ct_filter_option_unit',  // unit fields
								'/^.*$/' => 'sanitize_text_field',  // Arbitrary fields
							),
						)
					),
					'/^.*$/' => array(  // States
						'/font-family$/' => 'font-family',
						'transform' => array(
							'/^.*$/' => array(
								'/^.*$/' => 'sanitize_text_field'
							)
						),
						'gradient' => array(
							'colors' => array(
								'/^.*$/' => array(
									'/^.*$/' => 'sanitize_text_field'
								)
							),
							'/^.*$/' => 'sanitize_text_field',
						),
						'/^.*-unit$/' => 'ct_filter_option_unit',  // unit fields
						'/^.*$/' => 'sanitize_text_field',  // Arbitrary fields
					)
				)
			);
			$filter_keys = 'sanitize_text_field';
			break;
		case 'style_sets':
			$allowed_content = array(
				'/^.*$/' => array(
					'key' => 'sanitize_text_field',
					'parent' => 'sanitize_text_field',
					'status' => 'intval'
				)
			);
			$filter_keys = 'sanitize_text_field';
			break;
		case 'style_folders':
			$allowed_content = array(
				'/^.*$/' => array(
					'key' => 'sanitize_text_field',
					'status' => 'intval'
				)
			);
			$filter_keys = 'sanitize_text_field';
			break;
		case 'easy_posts_templates':
		case 'comments_list_templates':
			$allowed_content = array('/^.*$/' => array(
					'code_css' => 'base64_encode',
					'code_php' => 'base64_encode',
					'name' => 'sanitize_text_field',
					'/^.*$/' => 'sanitize_text_field'
				));
			$filter_keys = false;
			break;
		case 'typekit_fonts':
			$allowed_content = array( 
				'/^.*$/' => array(
					'/^.*$/' => 'sanitize_text_field'
				),
			);
			$filter_keys = false;
			break;
		case 'global_colors':
			$allowed_content = array( 
				'/^.*$/' => array(
					'global' => array(
						'/^.*$/' => 'sanitize_text_field'
					),
					'sets' => array(
						'/^.*$/' => array(
							'/^.*$/' => 'sanitize_text_field'
						)
					),
				),
			);
			$filter_keys = false;
			break;
		case 'element_presets':
			$allowed_content = array( 
				'/^.*$/' => array(
					'/^.*$/' => 'sanitize_text_field',
					'/^.*$/' => array(
						'/^.*$/' => 'sanitize_text_field',
						'/^.*$/' => array(
							'/^.*$/' => 'sanitize_text_field',
							'/^.*$/' => array(
								'/^.*$/' => 'sanitize_text_field'
							)
						)
					)
				),
			);
			$filter_keys = false;
			break;
		default:
		    $allowed_content = array();
            $filter_keys = false;

	}
	// Allow plugins to adjust the filters of content
	$allowed_content =  apply_filters( 'oxygen_vsb_component_filter_content_allowed', $allowed_content, $type, $content, $filter_keys );
	
	$new_content = ct_filter_array_recursive( $content, $allowed_content, $filter_keys );
	
	// Allow plugins to expand content that are allowed to be used
	return apply_filters( 'oxygen_vsb_component_filter_content', $new_content, $type, $content, $filter_keys );
}

/**
 * Filter option units
 */
function ct_filter_option_unit($data)
{
    if ($data !== ' ') {
        $data = sanitize_text_field($data);
    }

    return $data;
}

/**
 * Filter a single piece of content
 * @param string $data Content to be filtered
 * @param string|boolean $filter Name of callable function to use for filtering
 *
 * @return bool|mixed Filtered content
 */
function ct_filter_single_content( $data, $filter ) {
	if($filter == 'unset') {
		return '';
	}
	elseif($filter == 'font-family') {
		if ( is_array($data) ) {
			return ct_filter_array_recursive($data, array('/^.*$/' => 'sanitize_text_field'));
		}
		else {
			return sanitize_text_field($data);
		}
	}
	elseif ( is_callable( $filter ) ) {
		return call_user_func( $filter, $data );
	} elseif ( false === $filter ) {
		return false;
	}
	return $data;
}

/**
 * Recursively filter $data array with functions in $filter array
 *
 * @param string|array $data Array to be filtered
 * @param string|array $filter Array containing filters
 * @param string|boolean $filter_keyname Function to call to filter name of keys or false to not filter
 *
 * @return array Filtered array
 */
function ct_filter_array_recursive( $data, $filter, $filter_keyname = false ) {

	if ( is_array( $filter ) ) {
		$new_data = array();
		foreach ( $filter as $filter_key => $filter_value ) {
			// Walk filter array matching regexp and absolute matches)
			if ( isset( $data[ $filter_key ] ) ) {
				// Handle literal filters
				if ( isset( $filter_keyname ) && is_callable( $filter_keyname ) ) {
					$new_key = call_user_func( $filter_keyname, $filter_key );
				} else {
					$new_key = $filter_key;
				}

				if ( is_array( $filter_value ) ) {
					$new_data[ $new_key ] = ct_filter_array_recursive( $data[ $filter_key ], $filter_value, $filter_keyname );
				} else {
					$new_data[ $new_key ] = ct_filter_single_content( $data[ $filter_key ], $filter_value );
				}
			} elseif ( '/' === $filter_key[0] && is_array($data) && sizeof($data) > 0) {
				// Key regexp
				$matched_keys = preg_grep( $filter_key, array_keys( $data ) );
				foreach ( $matched_keys as $key ) {
					if ( isset( $filter_keyname ) && is_callable( $filter_keyname ) ) {
						$new_key = call_user_func( $filter_keyname, $key );
					} else {
						$new_key = $key;
					}
					if ( !isset( $new_data[ $new_key ] ) ) {
					    // Only allow entry to be filtered by first match
						$new_data[ $new_key ] = ct_filter_array_recursive( $data[ $key ], $filter_value, $filter_keyname );
					}
				}
			}

		}
	} else {
		return ct_filter_single_content( $data, $filter );
	}
	return $new_data;

}

function ct_resolve_oxy_url($matches) {
	
	return $matches[1].$matches[2].$matches[3].do_shortcode("[oxygen ".$matches[4].$matches[5]."]");
}

/**
 * Helper to obfuscate shortcodes
 * 
 * @since 3.2
 * @author Abdelouahed E.
 */
function ct_obfuscate_shortcode($content) {
    if (!empty($content)) {
        $pattern = '/(\")(url|src|map_address|alt|background-image|oxycode|value|name|attachment_id)(\":\"[^\"]*)\[oxygen ([^\]]*)\]([^\"\[\s]*)/i';
        
        $count = 0; // safety switch
        while(strpos($content, '[oxygen ') !== false && $count < 9) {
            $count++;
			$content = preg_replace_callback($pattern, 'ct_obfuscate_oxy_url', $content);
        }
    }
    
    return $content;
}
 
/**
 * Helper to safely do shortcodes (see ct_template_output)
 * 
 * @since 3.1
 * @author Abdelouahed E.
 */
function ct_do_shortcode($content) {
    $content = ct_obfuscate_shortcode($content);

	if (oxygen_doing_oxygen_elements()) {	
	
		global $oxygen_doing_oxygen_elements;
		
		$oxygen_doing_oxygen_elements = false;
		$output = do_shortcode($content);
		$oxygen_doing_oxygen_elements = true;
		
		return $output;
	}
    
    return do_shortcode( $content );
}

function ct_obfuscate_oxy_url($matches) {
	
	return $matches[1].$matches[2].$matches[3].'+oxygen'.base64_encode($matches[4]).'+'.$matches[5];
}

function ct_deobfuscate_oxy_url($matches) {
	return '[oxygen '.base64_decode($matches[1]).']';
}

/**
 * Listen for a template check, return proper flag and exit the script
 *
 * @since 1.4
 * @author Ilya K.
 */

function ct_has_oxygen_template() {
	if ( isset( $_GET["has_oxygen_template"] ) && $_GET["has_oxygen_template"] ) {
		echo ( ct_template_output(true) ) ? "true" : "false";
		die;
	}
}
//add_action("wp", "ct_has_oxygen_template");


/**
 * Hook to run on Oxygen plugin activation
 *
 * @since 1.4.1
 * @author Ilya K.
 */

function oxygen_activate_plugin() {

	set_transient('oxygen-vsb-just-activated', '1');

	// Register CPT the right way
	ct_add_templates_cpt(); // it also hooked into 'init'
	flush_rewrite_rules();
	// set flag
	update_option("oxygen_rewrite_rules_updated", "1");

	Oxygen_Bloat_Eliminator::plugin_activated();
	// generate universal.css
	add_option( 'oxygen_vsb_universal_css_cache', "true" );
	if (!get_option("oxygen_vsb_last_save_time")) {
		oxygen_vsb_cache_universal_css();
	}

	// setup preset defaults (may be it is better to setup other defaults here as well)
	$defaults = apply_filters("oxygen_vsb_element_presets_defaults", array());
	if (!get_option("oxygen_vsb_element_presets")) {
		// don't need to upgrade presets if no presets exist, so assume it is already updated
		add_option("oxygen_vsb_presets_updated_3_3", true);
	};
	add_option("oxygen_vsb_element_presets", $defaults);
}
register_activation_hook( CT_PLUGIN_MAIN_FILE, 'oxygen_activate_plugin' );
// flush rules on deactivation
register_deactivation_hook( CT_PLUGIN_MAIN_FILE, 'flush_rewrite_rules' );

add_action( 'wp_insert_post', 'ct_post_meta_on_new_reusable' );

function ct_post_meta_on_new_reusable( $post_id ) {
    $post_type = get_post_type($post_id);

    if($post_type === 'ct_template') {
    	$is_reusable = isset($_REQUEST['is_reusable'])?true: false;

    	if($is_reusable) {
    		add_post_meta( $post_id, 'ct_template_type', 'reusable_part' );
    	}
    
    }

}


/**
 * Get all Stylesheets CSS
 * Taken from csslink.php and wraped in a function
 *
 * @since 2.0
 * @author Ilya K.
 */

function oxygen_vsb_get_stylesheet_styles() {
	
	$styles = "";
	$style_sheets = get_option( "ct_style_sheets", array() );

	if ( is_array( $style_sheets ) ) {

		foreach( $style_sheets as $key => $value ) {

			if(!is_array($value)) { // if it is the old style sheets data
				$styles .= base64_decode( $style_sheets[$key] );
			}
			else {
				$disabled = false;
				
				if( !$disabled && isset($style_sheets[$key]['parent']) && intval($style_sheets[$key]['parent']) === -1) {
					$disabled = true;
				}

				if( !$disabled && isset($style_sheets[$key]['parent']) && $style_sheets[$key]['parent'] !== 0 ) {
					// get the parent
					foreach($style_sheets as $item) {
						if($item['id'] === $style_sheets[$key]['parent']) { // this is the parent
							if($item['status'] === 0) {
								$disabled = true;
							}
						}
					}
				}

				if(!$disabled && isset($style_sheets[$key]['css'])) {
					$styles .= preg_replace_callback(
					            "/color\(\d+\)/",
					            "oxygen_vsb_parce_global_colors_callback",
					            base64_decode( $style_sheets[$key]['css'] ));
				}
			}
		}
	}

	return $styles;
}


/**
 * Get all Custom Selectors CSS
 * Taken from csslink.php and wraped in a function
 *
 * @since 2.0
 * @author Ilya/Gagan
 */

function oxygen_vsb_get_custom_selectors_styles() {

	global $media_queries_list;

	$selectors = get_option( "ct_custom_selectors" );
	$styleSets = get_option( "ct_style_sets" );
	$styleFolders = get_option( "ct_style_folders");
	$css = "";

	if ( is_array( $selectors ) ) {
		foreach ( $selectors as $selector => $states ) {

			if(!(

				(!isset($states['set_name']) || !isset($styleSets[$states['set_name']]) || !isset($styleSets[$states['set_name']]['parent']) || !isset($styleFolders[$styleSets[$states['set_name']]['parent']]) || !isset($styleFolders[$styleSets[$states['set_name']]['parent']]['status']) || intval($styleFolders[$styleSets[$states['set_name']]['parent']]['status']) === 1)
				
			)) {
				continue;
			}

			if(isset($styleSets[$states['set_name']]) && isset($styleSets[$states['set_name']]['parent']) && intval($styleSets[$states['set_name']]['parent']) === -1) {
				continue;
			}

			foreach ( $states as $state => $options ) {

				if (in_array($state, array("set_name", "key", "parent", "status", "friendly_name"))) {
					continue;
				}	

				if ( $state == 'media' ) {

					$sorted_media_queries_list = ct_sort_media_queries(true);
					
					foreach ( $sorted_media_queries_list as $media_name => $media ) {

						if ($media_name == "page-width") {
							$max_width = oxygen_vsb_get_page_width(true).'px';
						}
						else {
							$max_width = $media_queries_list[$media_name]['maxSize'];
						}
						
						if ( isset($options[$media_name]) && $options[$media_name] && $media_name != "default") {
							$css .= "@media (max-width: $max_width) {\n";
							foreach ( $options[$media_name] as $media_state => $media_options ) {
								$css .= ct_generate_class_states_css($selector, $media_state, $media_options, $media_name, true, $states);
							}
							$css .= "}\n\n";
						}
					}
				}
				else {
					$css = ct_generate_class_states_css($selector, $state, $options, false, true).$css;
				}
			}
		}
	}

	return $css;
}

function oxy_vsb_empty_shortcode($atts=null, $content=null, $name=null) {
	echo "";
} 
add_shortcode("oxy-empty-shortcode", "oxy_vsb_empty_shortcode");


/**
 * Output custom HTML attributes 
 *
 * @since 3.4
 * @author Ilya K.
 */

function oxygen_vsb_custom_attributes($options) {

	if ( !isset( $options["custom_attributes"] ) || !is_array( $options["custom_attributes"] ) ) {
		return "";
	}

	$attrs = "";

	foreach ( $options["custom_attributes"] as $attr ) {

		// this might containt dynamic data
		$value = do_shortcode($attr['value']);
		$name  = do_shortcode($attr['name']);


		if ( !isset( $name ) || trim( $name ) == "" ) {
			continue;
		}

		if ( isset( $options['target'] ) && !empty( $options['target'] ) && $name == 'target' ) {
			continue;
		} 

		if ( !isset( $value ) ) {
			$value = "";
		}

		if ( preg_match("/[^a-z-_]+/i", $name) ) {
			continue;
		}
		
        $value = str_replace('"', "&quot;", $value);
        $value = str_replace("'", "&apos;", $value);
        $value = str_replace("<", "&lt;", $value);
        $value = str_replace(">", "&gt;", $value);
		
		$attrs .= " $name=\"$value\"";
	}

	echo $attrs;
}   
add_action("oxygen_vsb_component_attr", "oxygen_vsb_custom_attributes");

function oxygen_vsb_check_is_agency_bundle() {

	$license_key = trim( get_option( 'oxygen_license_key' ) );

	$url = "https://oxygenbuilder.com/";
    $args = array(
        "body" => array(
            "oxy_check_bundle_lisence" => $license_key,
		),
		"timeout" => 30
    );

	$response = wp_remote_get( $url, $args );
	
	if ( is_wp_error( $response ) ) {
		update_option("oxygen_vsb_is_agency_bundle", false);
		return;
	}
		
	$body = wp_remote_retrieve_body($response);

	$data = json_decode($body, true);
	
    if (!empty($data["is_bundle"])) {
		update_option("oxygen_vsb_is_agency_bundle", $data["is_bundle"]);
	}
	else {
		update_option("oxygen_vsb_is_agency_bundle", false);
	}
	
	if (!empty($data["is_composite_bundle"])) {
		update_option("oxygen_vsb_is_composite_elements_agency_bundle", $data["is_composite_bundle"]);
	}
	else {
		update_option("oxygen_vsb_is_composite_elements_agency_bundle", false);
	}

}

function oxygen_vsb_is_agency_bundle() {
	return get_option("oxygen_vsb_is_agency_bundle", false);
}

function oxygen_vsb_is_composite_elements_agency_bundle() {
	return get_option("oxygen_vsb_is_composite_elements_agency_bundle", false);
}

function oxygen_has_not_registered_shortcodes($shortcodes) {
	
	global $shortcode_tags;

	// Exclude Shortcode and Shortcode Wrapper from check
	$shortcodes = preg_replace('/\[ct_shortcode.*ct_shortcode\]/i', "", $shortcodes);
	$shortcodes = preg_replace('/\[ct_nestable_shortcode_.*ct_nestable_shortcode_\d*\]/iU', "", $shortcodes);

	preg_match_all( '@\[([^<>&/\[\]\x00-\x20=]++)@', $shortcodes, $matches);
	$not_registered_shortcodes = array_diff( $matches[1], array_keys( $shortcode_tags ) );

	return $not_registered_shortcodes;
}

function oxygen_doing_oxygen_elements() {
	
	global $oxygen_doing_oxygen_elements;
	
	if (isset($oxygen_doing_oxygen_elements) && $oxygen_doing_oxygen_elements == true) {
		return true;
	}
	else {
		return false;
	}
}

// Fix WP5.8 new title tag fallback causing title duplication
add_action('wp_head', function() {

	remove_action('wp_head', '_block_template_viewport_meta_tag', 0);

	$plugins_found = false;
	
	// check if Yoast Seo is active
	if ( defined( 'WPSEO_FILE' )  ) {
		$plugins_found = true;
	}

	// check if Rank Math Seo is active
	else if ( class_exists('RankMath') ) {
		$plugins_found = true;
	}

	if ( $plugins_found ) {
    	remove_action('wp_head', '_block_template_render_title_tag', 1);
	}

}, PHP_INT_MIN);


function oxygen_get_authors() {

	$args = [];

	// Capability queries were only introduced in WP 5.9.
	if ( version_compare( $GLOBALS['wp_version'], '5.9', '<' ) ) {
		$args['who'] = 'authors';
	} else {
		$args['capability'] = ['edit_posts'];
	}

	return get_users( $args );
}

function oxy_get_global_font_name($option) {

	if ( !is_array( $option ) ) {
		return $option;
	}

	if ( $option[0] == 'global' ) {
		$settings 	= get_option("ct_global_settings"); 
		$option 	= isset( $settings['fonts'][$option[1]] ) ? $settings['fonts'][$option[1]] : $option[1];
	}
	
	return $option;
}

/**
 * Helper function to actually know if there is Oxygen elements present
 * 
 * @author Ilya K.
 * @since 4.0.4
 */

function oxygen_json_has_elements( $json ) {

	if ( !is_array( $json ) ) {
		$json = json_decode( $json, true );
	}

	if ( 
		 isset( $json['children'] ) && 
	  is_array( $json['children'] ) && 
	     count( $json['children'] ) > 0 
	) {
		return true;
	}

	return false;
}

/**
 * Check if hot reload is enabled and output appropriate scripts
 * 
 * @author Elijah M.
 * @since 4.4
 */
if( get_transient('ct_hot_reload_enabled') === "true" ) {
	add_action( 'wp_head', 'oxy_hot_reload_script' );
}

function oxy_hot_reload_script() {
	// Add check to run this only on front-end
	if( is_admin() || isset( $_GET["ct_builder"] ) || !current_user_can( 'manage_options' ) ) return;

	?>
	<script>
		let ajaxurl = "<?php echo admin_url("admin-ajax.php"); ?>";
		let reloadInstructions = "noreload";

		setInterval( () => {
			let fetchReloadInstructions = fetch(ajaxurl, {
												method: "POST",
												credentials: "same-origin",
												headers: new Headers({"Content-Type": "application/x-www-form-urlencoded"}),
												body: "action=oxy_css_hot_reload_check"
											})
											.then( response => { return response.text() } )
											.then( result => reloadInstructions = result );

			if( reloadInstructions == "reload" ) {
				location.reload();
			}
		}, 1000);
	</script>
	<?php
}
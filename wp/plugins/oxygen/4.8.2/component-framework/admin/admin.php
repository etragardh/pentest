<?php 

/**
 * Get Frontend Builder post link by post ID
 *
 * @since 0.4.0
 * @author Ilya K.
 */

function ct_get_post_builder_link($post_id) {

	$link = get_permalink( $post_id );
	if ( force_ssl_admin() ) {
		$link = str_replace("http://", "https://", $link);
	}
	return add_query_arg( 'ct_builder', 'true', $link );
}

/**
 * Get full URL to load builder including inner content if exist
 *
 * @since 4.8
 * @author Ilya K.
 */

function oxy_get_builder_url($post_id){

	$json = get_post_meta( $post_id, "ct_builder_json", true );
    $contains_inner_content = false;
    
	if ( $json ) {
		$contains_inner_content = (strpos($json, '"name":"ct_inner_content"') !== false);
	}
	else {
		$shortcodes = get_post_meta( $post_id, "ct_builder_shortcodes", true );
		if($shortcodes) {
			$contains_inner_content = (strpos($shortcodes, '[ct_inner_content') !== false);
		}
	}
	
    return ct_get_post_builder_link( $post_id ).(($contains_inner_content)?'&ct_inner=true':'');
}


/**
 * Hide admin bar if frontend builder launched
 *
 * @since 0.1
 */

function ct_hide_admin_bar() {

    if ( defined("SHOW_CT_BUILDER") ) {
    	add_filter('show_admin_bar', '__return_false');
    }
}
add_action('init','ct_hide_admin_bar');


/**
 * Load scripts and styles for Component theme elements in WordPress dashboard
 *
 * @since 0.2.0
 */

function ct_enqueue_admin_scripts( $hook ) {

	// load css on all pages
    wp_enqueue_style ("oxygen-vars",	CT_FW_URI . "/oxygen.variables.css");
	wp_enqueue_style ( 'ct-admin-style', CT_FW_URI . "/admin/admin.css" );
    
    // load specific scrpits only here 
    if ( 'post.php' != $hook && 'post-new.php' != $hook && 'edit.php' != $hook && 'oxygen_page_oxygen_vsb_settings' != $hook 
        && 'oxygen_page_ct_export_import' != $hook && 'oxygen_page_ct_css_management' != $hook) {
        return;
    }

    $screen = get_current_screen();

    // include only on Views screen and Oxygen > Settings
    if ( $screen->post_type == "ct_template" || 'oxygen_page_oxygen_vsb_settings' == $hook ) {
        wp_enqueue_script( 'select2', CT_FW_URI . "/vendor/select2/select2.full.min.js", array( 'jquery' ) );
    	wp_enqueue_style ( 'select2', CT_FW_URI . "/vendor/select2/select2.min.css" );
    }

    wp_enqueue_script( 'ct-admin-script', CT_FW_URI . "/admin/admin.js" );

    if (  'oxygen_page_oxygen_vsb_settings' == $hook ) {
        wp_enqueue_style ( 'my-styles', CT_FW_URI . "/admin/oxygen_vsb_settings.css" );
    }
    if (  'oxygen_page_ct_export_import' == $hook ) {
        wp_enqueue_style ( 'my-styles', CT_FW_URI . "/admin/ct_export_import.css" );
    }
    if (  'oxygen_page_ct_css_management' == $hook ) {
        wp_enqueue_style ( 'my-styles', CT_FW_URI . "/admin/ct_css_management.css" );
    }

}
add_action( 'admin_enqueue_scripts', 'ct_enqueue_admin_scripts' );

/**
 * Output shortcodes to meta box content
 * 
 * @since 0.4.0
 * @author Ilya K.
 */

function ct_shortcodes_save_meta_box( $post_id ) {
	
	// Check if our nonce is set
	if ( ! isset( $_POST['ct_shortcode_meta_box_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid
	if ( ! wp_verify_nonce( $_POST['ct_shortcode_meta_box_nonce'], 'ct_shortcode_meta_box' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions
	if ( !oxygen_vsb_current_user_can_access() ) {
		return;
	}

	/* OK, it's safe for us to save the data now */
    
    $existing_json = get_post_meta( $post_id, 'ct_builder_json', true );
    $user_json = trim( wp_unslash( $_POST['ct_builder_json'] ) );

    // if JSON already exist in the DB and user save empty JSON assume it intentional deletion
    if ( $existing_json && empty($user_json) ) {
        update_post_meta( $post_id, 'ct_builder_shortcodes', "" );
        update_post_meta( $post_id, 'ct_builder_json', "" );

        return;
    }

    $json = $user_json;

    if( !empty( $json )) {
        $components_tree_json_for_shortcodes = json_decode($json, true);
        // base64 encode js,css code and (wrapping_shortcode in case of nestable_shortcode component) in the IDs
		if(isset($components_tree_json_for_shortcodes['children'])) {
			$components_tree_json_for_shortcodes['children'] = ct_base64_encode_decode_tree($components_tree_json_for_shortcodes['children']);
		}
		$components_tree_json_for_shortcodes = json_encode($components_tree_json_for_shortcodes);
        // Generate the shortcodes from JSON data
	    $shortcodes = components_json_to_shortcodes( $components_tree_json_for_shortcodes );
    } else {
	    // if no JSON is sent when saving the page from wp-admin, try processing the shortcodes.
	    $shortcodes = trim( wp_unslash( $_POST['ct_builder_shortcodes'] ) );
	    // Parse shortcodes into Oxygen content array and then back again.
	    // This forces the shortcodes to be re-signed as well as running them through all of the content specific filters
	    $components = parse_shortcodes( $shortcodes, true, false );
	    $shortcodes = parse_components_tree( $components['content'] );
    }

    update_post_meta( $post_id, 'ct_builder_shortcodes', $shortcodes );
	if( !empty( $json )) {
        // No need to save empty JSON
        update_post_meta( $post_id, 'ct_builder_json', addslashes($json) );
	}
    
    // Lock Post In Edit Mode
    $oxygen_lock_post_edit_mode = isset($_POST['oxygen_lock_post_edit_mode']) ? $_POST['oxygen_lock_post_edit_mode'] : "";
    update_post_meta( $post_id, 'oxygen_lock_post_edit_mode', $oxygen_lock_post_edit_mode );    
}
add_action( 'save_post', 'ct_shortcodes_save_meta_box' );



/**
 * Check add-ons versions
 *
 * @since 1.5
 * @author Ilya K.
 */

function oxygen_check_addons_versions() {

	define("REQUIRED_OSD_VERSION", "1.1");

	if ( defined('OSD_VERSION') && version_compare( OSD_VERSION, REQUIRED_OSD_VERSION ) < 0) {
		remove_action( 'plugins_loaded', 'oxygen_selector_detector_init' );
		add_action( 'admin_notices', 'oxygen_osd_addon_wrong_version' );
	}

}
//add_action("plugins_loaded", "oxygen_check_addons_versions", 0);


/**
 * Admin notice if Oxygen Selector Detector version is not compatible
 *
 * @since 1.5
 * @author Ilya K.
 */

function oxygen_osd_addon_wrong_version() {
	
	$classes = 'notice notice-error';
	$message = __( 'Your Oxygen Selector Detector version is not supported. Minimal required Selector Detector version is:', 'oxygen' );

	printf( '<div class="%1$s"><p>%2$s <b>%3$s</b></p></div>', $classes, $message, REQUIRED_OSD_VERSION ); 
}


/**
 * Enqueues the main script and the full Oxygen generated markup in a frontend
 * variable so the script can access it synchronously, as suggested by Yoast
 *
 * @since 2.0
 * @author Emmanuel & Ilya
 */

function oxygen_vsb_yoast_compatibility() {

	// check if Yoast Seo is active
	if ( !is_plugin_active( "wordpress-seo/wp-seo.php" ) && !is_plugin_active( "wordpress-seo-premium/wp-seo-premium.php" ) ) {
		return;
	}

	global $pagenow;
	global $post;

	// save global $post to restore later
	$saved_post = $post;

	// exclude templates
	if (is_object($post) && $post->post_type=="ct_template") {
		return;
	}

	if( 'post.php' == $pagenow && !is_null( $post ) ) {

        $json = get_post_meta( $post->ID, 'ct_builder_json', true );
        if (!$json) {
            $markup = ct_do_shortcode( get_post_meta( $post->ID, 'ct_builder_shortcodes', true ) );
        }
        else {
            global $oxygen_doing_oxygen_elements;
		    $oxygen_doing_oxygen_elements = true;
            $markup = do_oxygen_elements( json_decode($json, true) );
        }

		wp_enqueue_script( 'ysco-oxygen-analysis', plugins_url( '/js/yoast-seo-compatibility.js', __FILE__ ), array( 'jquery' ), false, true );
		wp_localize_script( 'ysco-oxygen-analysis', 'ysco_data', array(
			'oxygen_markup' => $markup
		) );
	}

	// restore original global post
	$post = $saved_post;
}
add_action( 'admin_enqueue_scripts', 'oxygen_vsb_yoast_compatibility', 11 );



/**
 * Gray out WP themes to let user know they doesn't matter
 *
 * @since 2.2
 * @author CSS: Elijah, Hook: Ilya
 */

function oxygen_vsb_disable_themes_css() {

  $current_screen = get_current_screen();

  // add for Themes screen only
  if ( $current_screen->id != "themes") {
  	return;
  }

  echo '<style>
    .theme-screenshot img {
    	filter: grayscale(100%) brightness(0.5);
    }
    .theme-actions .button,
	    .theme-actions .button:hover {
	    background-color: #F1F1F1;
	    color: #DDDDDD;
	    text-shadow: none;
	    border-color: #ccc;
	    box-shadow: none;
    }
    .oxy-notice {
	    border-left: 4px solid #6036ca;
	    padding: 11px 15px;
    }
  </style>';
}
add_action('admin_head', 'oxygen_vsb_disable_themes_css');


/**
 * Show admin notice on Themes screen
 *
 * @since 2.2
 * @author Ilya K.
 */ 

function oxygen_vsb_themes_screen_notice() {

	$current_screen = get_current_screen();

	// add for Themes screen only
	if ( $current_screen->id != "themes") {
	  	return;
	}
	?>
    <div class="notice notice-warning oxy-notice">
        <p><?php printf(
                    __( 'You\'re using <a href="%s">Oxygen</a> to design your site, which entirely disables the WordPress theme system. The active theme is never loaded, and has no impact on your site\'s performance or appearance.', 'oxygen' ),
                	menu_page_url('ct_dashboard_page', false)
                ); ?></p>
    </div>
<?php }
add_action( 'admin_notices', 'oxygen_vsb_themes_screen_notice' );

/**
 * Add edit with Oxygen quick action link
 * 
 * @param string[] $actions An array of row action links.
 * @param WP_Post  $post    The post object.
 * 
 * @return string[]
 * 
 * @since 3.3
 * @author Abdelouahed E.
 */
function oxygen_add_posts_quick_action_link($actions, $post, $return_type = "filter") {
    $post_ID = $post->ID;
    $post_type = $post->post_type;
    
    if (!oxygen_vsb_current_user_can_access()) {
        return $actions;
    }
    
    if (get_option("oxygen_vsb_ignore_post_type_{$post_type}") == 'true') {
        return $actions;
    }
    
    // check if Oxygen is open somewhere and threfore blocked
    if (is_oxygen_edit_post_locked()) {
        return $actions;
    }

    // check if post blocked manually for "edit only" users
	if ($return_type == "filter" && get_post_meta( $post_ID, 'oxygen_lock_post_edit_mode', true )=="true" && oxygen_vsb_get_user_edit_mode() == "edit_only") {
        return $actions;
    }
    
    $edit_link_href = '';
    $edit_link_text = __("Edit with Oxygen", "oxygen");
    
    if ($post_type == "ct_template") {
        $template_type = get_post_meta($post_ID, 'ct_template_type', true);
        
        $edit_link_href = ct_get_post_builder_link($post_ID);
        
        if ($template_type !== 'reusable_part') {
            $parent_template_inner = false;
            $parent_template = get_post_meta($post_ID, 'ct_parent_template', true);
            
            if ($parent_template) {
                $json = get_post_meta($parent_template, 'ct_builder_json', true);
                if ( $json ) {
                    if ( strpos($json, '"name":"ct_inner_content"') !== false ) {
                        $parent_template_inner = true;
                    }
                }
                else {
                    $shortcodes = get_post_meta($parent_template, 'ct_builder_shortcodes', true);
                    if ($shortcodes && strpos($shortcodes, '[ct_inner_content') !== false) {
                        $parent_template_inner = true;
                    }
                }
            }
        
            if ($parent_template_inner) {
                $edit_link_href = add_query_arg('ct_inner', 'true', $edit_link_href);
            }
        }
    } else if ($post_type == "oxy_user_library") {
        $edit_link_href = ct_get_post_builder_link($post_ID);
    } else {
        // Get post template
        $post_template = intval(get_post_meta($post_ID, 'ct_other_template', true));
        
        // Check if we should edit the post or it's template
        $edit_template = false;
        $post_editable = false;
        $template_inner = false;
        
        if ($post_template == 0) { // default template
            // Get default template
            $default_template = null;
            
            if (get_option('page_for_posts') == $post_ID || get_option('page_on_front') == $post_ID ) {
                $default_template = ct_get_archives_template( $post_ID );
            }
            
            if (empty($default_template)) {
                $default_template = ct_get_posts_template($post_ID);
            }
            
            if ($default_template) {
                
                $json = get_post_meta($default_template->ID, 'ct_builder_json', true);
                if ( $json ) {
                    if ( strpos($json, '"name":"ct_inner_content"') !== false ) {
                        $post_editable = true;
                        $template_inner = true;
                    } else {
                        $edit_template = $default_template->ID;
                    }
                }
                else {
                    $shortcodes = get_post_meta($default_template->ID, 'ct_builder_shortcodes', true);
                    if ($shortcodes && strpos($shortcodes, '[ct_inner_content') !== false) {
                        $post_editable = true;
                        $template_inner = true;
                    } else {
                        $edit_template = $default_template->ID;
                    }
                }

            } else {
                $post_editable = true;
            }
        } else if ($post_template == -1) { // None
            $post_editable = true;
        } else { // Custom template
            
            $json = get_post_meta($post_template, 'ct_builder_json', true);
            if ( $json ) {
                if ( strpos($json, '"name":"ct_inner_content"') !== false ) {
                    $post_editable = true;
                    $template_inner = true;
                } else {
                    $edit_template = $post_template;
                }
            }
            else {    
                $shortcodes = get_post_meta($post_template, 'ct_builder_shortcodes', true);
                if ($shortcodes && strpos($shortcodes, '[ct_inner_content') !== false) {
                    $post_editable = true;
                    $template_inner = true;
                } else {
                    $edit_template = $post_template;
                }
            }
        }
        
        // Generate edit link
        if ($post_editable) {
            $edit_link_href = ct_get_post_builder_link($post_ID);
            
            if ($template_inner) {
                $edit_link_href = add_query_arg('ct_inner', 'true', $edit_link_href);
            }
        } else if ($edit_template) {
            // check if template blocked manually for "edit only" users
            if ($return_type == "filter" && get_post_meta( $edit_template, 'oxygen_lock_post_edit_mode', true )=="true" && oxygen_vsb_get_user_edit_mode() == "edit_only") {
                return $actions;
            }
            $edit_link_href = ct_get_post_builder_link($edit_template);
            $edit_link_text = __("Edit Template", "oxygen");
        }
    }
    
    if( $edit_link_href && $edit_link_text ) {
        $actions['oxy_edit'] = sprintf('<a class="edit" href="%s">%s</a>', $edit_link_href, $edit_link_text);
    }
    
    if ( $return_type == "filter" ) {
        return $actions;
    }

    if ( $return_type == "array" ) {
        return array(
            "url" => $edit_link_href,
            "text" => $edit_link_text,
            "template" => $edit_template
        );
    }

}

add_filter('page_row_actions', 'oxygen_add_posts_quick_action_link', 10, 2);
add_filter('post_row_actions', 'oxygen_add_posts_quick_action_link', 10, 2);


function oxy_edit_post($post) {
    
    $post_ID = $post->ID;
    $post_type = $post->post_type;
    
	if (get_post_meta( $post_ID, 'oxygen_lock_post_edit_mode', true )=="true" && oxygen_vsb_get_user_edit_mode() == "edit_only") {
        return "";
    }

    $edit_template = false;
    $post_editable = false;
    $template_inner = false;
    
    $edit_link_href = '';
    
    if ($post_type == "ct_template") {
        $template_type = get_post_meta($post_ID, 'ct_template_type', true);
        
        $edit_link_href = ct_get_post_builder_link($post_ID);
        
        if ($template_type !== 'reusable_part') {
            $parent_template_inner = false;
            $parent_template = get_post_meta($post_ID, 'ct_parent_template', true);
            
            if ($parent_template) {
                $json = get_post_meta($parent_template, 'ct_builder_json', true);
                if ( $json ) {
                    if ( strpos($json, '"name":"ct_inner_content"') !== false ) {
                        $parent_template_inner = true;
                    }
                }
                else {
                    $shortcodes = get_post_meta($parent_template, 'ct_builder_shortcodes', true);
                    if ($shortcodes && strpos($shortcodes, '[ct_inner_content') !== false) {
                        $parent_template_inner = true;
                    }
                }
            }
        
            if ($parent_template_inner) {
                $edit_link_href = add_query_arg('ct_inner', 'true', $edit_link_href);
            }
        }
    } else if ($post_type == "oxy_user_library") {
        $edit_link_href = ct_get_post_builder_link($post_ID);
    } else {
        // Get post template
        $post_template = intval(get_post_meta($post_ID, 'ct_other_template', true));
        
        // Check if we should edit the post or it's template
        if ($post_template == 0) { // default template
            // Get default template
            $default_template = null;
            
            if (get_option('page_for_posts') == $post_ID || get_option('page_on_front') == $post_ID ) {
                $default_template = ct_get_archives_template( $post_ID );
            }
            
            if (empty($default_template)) {
                $default_template = ct_get_posts_template($post_ID);
            }
            
            if ($default_template) {
                
                $json = get_post_meta($default_template->ID, 'ct_builder_json', true);
                if ( $json ) {
                    if ( strpos($json, '"name":"ct_inner_content"') !== false ) {
                        $post_editable = true;
                        $template_inner = true;
                    } else {
                        $edit_template = $default_template->ID;
                    }
                }
                else {
                    $shortcodes = get_post_meta($default_template->ID, 'ct_builder_shortcodes', true);
                    if ($shortcodes && strpos($shortcodes, '[ct_inner_content') !== false) {
                        $post_editable = true;
                        $template_inner = true;
                    } else {
                        $edit_template = $default_template->ID;
                    }
                }

            } else {
                $post_editable = true;
            }
        } else if ($post_template == -1) { // None
            $post_editable = true;
        } else { // Custom template
            
            $json = get_post_meta($post_template, 'ct_builder_json', true);
            if ( $json ) {
                if ( strpos($json, '"name":"ct_inner_content"') !== false ) {
                    $post_editable = true;
                    $template_inner = true;
                } else {
                    $edit_template = $post_template;
                }
            }
            else {    
                $shortcodes = get_post_meta($post_template, 'ct_builder_shortcodes', true);
                if ($shortcodes && strpos($shortcodes, '[ct_inner_content') !== false) {
                    $post_editable = true;
                    $template_inner = true;
                } else {
                    $edit_template = $post_template;
                }
            }
        }
        
        // Generate edit link
        if ($post_editable) {
            $edit_link_href = ct_get_post_builder_link($post_ID);
            
            if ($template_inner) {
                $edit_link_href = add_query_arg('ct_inner', 'true', $edit_link_href);
            }
        } else if ($edit_template) {
            // check if template blocked manually for "edit only" users
            if (get_post_meta( $edit_template, 'oxygen_lock_post_edit_mode', true )=="true" && oxygen_vsb_get_user_edit_mode() == "edit_only") {
                return "";
            }
            $edit_link_href = ct_get_post_builder_link($edit_template);
        }
    }
    
    return array(
        "link" => $edit_link_href, 
        "post_id" => $edit_template ? $edit_template : $post_ID 
    );
}


/**
 * Set edit lock transient when user use "Edit with Oxygen" link in the adminbar
 *
 * @author Ilya K.
 * @since 3.3
 */

function oxygen_vsb_post_edit_lock_adminbar() { 

    if (!is_admin_bar_showing()) {
        return;
    }
    ?>
    jQuery(document).ready(function($){
        jQuery("#wp-admin-bar-edit_post_template > a").click(function(){
            jQuery.post( '<?php echo admin_url('admin-ajax.php'); ?>', { 
                action: 'set_oxygen_edit_post_lock_transient',
                post_id: <?php echo get_the_ID(); ?>,
                nonce: '<?php echo wp_create_nonce( 'oxygen-nonce-' . get_the_ID() ); ?>'
            })
        });
    });
<?php }
add_action("ct_footer_js", "oxygen_vsb_post_edit_lock_adminbar" );


/**
 * Set special body class in case post edit locked in Oxygen
 *
 * @since 3.3
 * @author Ilya K.
 */

function oxy_admin_body_class($classes) {

    if (isset($_REQUEST['ignore_post_lock'])) {
        return $classes;
    }

    if ( is_oxygen_edit_post_locked() ) {
        $classes .= ' oxygen-edit-post-locked';
    }

    global $post;
    if ( $post && is_oxygen_edit_post_locked($post->ID) ) {
        $classes .= ' oxygen-edit-post-locked-current';
    }

    return $classes;
}
add_filter('admin_body_class', 'oxy_admin_body_class');

// Rank Math SEO Compatibility code.
/**
 * Enqueues the script needed to analyze the content.
 */
add_action( 'rank_math/admin/enqueue_scripts', function() {
    global $post, $pagenow;
    
    // exclude templates
	if ( is_object( $post ) && 'ct_template' === $post->post_type ) {
		return;
    }

    if( 'post.php' !== $pagenow || is_null( $post ) ) {
        return;
    }
    
    // save global $post to restore later
	$saved_post = $post;

    $json = get_post_meta( $post->ID, 'ct_builder_json', true );
    if (!$json) {
        $markup = ct_do_shortcode( get_post_meta( $post->ID, 'ct_builder_shortcodes', true ) );
    }
    else {
        global $oxygen_doing_oxygen_elements;
		$oxygen_doing_oxygen_elements = true;
        $markup = do_oxygen_elements( json_decode($json, true) );
    }
    
    wp_enqueue_script(
        'rank-math-oxygen-analysis',
        plugins_url( '/js/rank-math-compatibility.js', __FILE__ ),
        [ 'wp-hooks', 'rank-math-analyzer' ],
        false,
        true
    );
    wp_localize_script(
        'rank-math-oxygen-analysis',
        'rm_data',
        [ 'oxygen_markup' => $markup ]
    );

    // restore original global post
	$post = $saved_post;

} );

// Rank Math Filter to include images added in Oxygen Builder in the Sitemap.
add_filter( 'rank_math/sitemap/content_before_parse_html_images', function( $content, $post_id ) {

    $json = get_post_meta( $post_id, 'ct_builder_json', true );
    if (!$json) {
        $oxygen_markup = ct_do_shortcode( get_post_meta( $post_id, 'ct_builder_shortcodes', true ) );
    }
    else {
        global $oxygen_doing_oxygen_elements;
		$oxygen_doing_oxygen_elements = true;
        $oxygen_markup = do_oxygen_elements( json_decode($json, true) );
    }

    $content = $content . $oxygen_markup;

    return $content;
}, 10, 2 );

// Fix for Rank Math breaks the comment reply
add_filter( 'rank_math/frontend/remove_reply_to_com', '__return_false' );

// Force customizer to be available for block themes
add_action( 'customize_register', function(){/*empty function*/}, 999 );
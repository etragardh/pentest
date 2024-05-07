<?php

/*
Plugin Name: Oxygen Connection
Author: Louis
Author URI: https://oxygenapp.com
Description: connection
Version: 1.0
*/

Class OXY_VSB_Connection {

	function __construct() {


		$enable_connection = get_option('oxygen_vsb_enable_connection', false);

		if(!$enable_connection) {
			return;
		}

		add_filter('body_class', array($this, 'ct_connection_body_class'));

		add_action( 'add_meta_boxes', array($this, 'ct_connection_page_category_meta_box' ));

		//On post save, save plugin's data
		add_action('save_post', array($this, 'ct_connection_metabox_save'));

		add_action( 'init', array($this, 'oxy_vsb_scapi_challenge_return' ));

		add_action( 'template_redirect', array($this, 'ct_block_element_post_type'));

		add_action( 'init', array($this, 'ct_connection_element_post_type' ));
		add_action('admin_menu', array($this, 'ct_block_library_page'), 10);
		
		add_action('admin_menu', array($this, 'oxygen_vsb_connection_register_options_page'), 15);

		add_action( 'admin_init', array($this, 'oxygen_vsb_connection_register_settings' ));
		add_action('oxygen_vsb_connection_panel', array($this, 'oxygen_vsb_connection_callback'));
		add_action('wp_ajax_oxygen_qa_process', array($this, 'oxygen_connection_qa_process'));
		add_action( 'admin_enqueue_scripts', array($this, 'oxygen_vsb_admin_script'));
		add_action( 'wp_enqueue_scripts', array($this, 'oxygen_vsb_screenshot_script'));
		add_action('rest_api_init', array($this, 'oxygen_vsb_connection_register_routes'));
		add_action('wp_ajax_oxygen_connection_screenshot', array($this, 'oxygen_connection_screenshot'));

		//add_action('template_redirect', array($this, 'scapitest'));

	}

	
    function ct_block_element_post_type ($template) {
        if (is_singular('oxy_user_library')) {
        	
        	$current_user = wp_get_current_user();

        	if(isset($_REQUEST['render_component_screenshot']) || user_can( $current_user, 'administrator' ) || oxygen_vsb_current_user_can_full_access() ) {
        		return $template;
        	}

           global $wp_query;
           $wp_query->posts = [];
           $wp_query->post = null;
           $wp_query->set_404();
           status_header(404);
           nocache_headers();
           wp_redirect(home_url());
           exit();
        }
    }


	function ct_connection_element_post_type() {

		$labels = array (
			'name' => 'Blocks',
			'singular_name' => 'Block',
			'menu_name' => 'Block Library',
			'all_items' => 'Block Library',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New Block',
			'edit_item' => '',
			'new_item' => '',
			'view_item' => '',
			'search_items' => '',
			'not_found' => '',
			'not_found_in_trash' => '',
			'parent_item_colon' => '',
			'name_admin_bar' => 'Library Block',
	    );

	    $args = array (
		    'description' => '',
		    'public' => true,
		    'publicly_queryable' => true,
		    'show_ui' => true,
		    'has_archive' => false,
		    'capability_type' => 'post',
		    'hierarchical' => false,
		    'rewrite' => false,
		    'query_var' => true,
		    'menu_position' => '',
		    'show_in_menu' => 'some_arbitrary',
		    'show_in_nav_menus' => false,
		    'show_in_admin_bar' => true,
		    'show_in_rest' => false,
		    'exclude_from_search' => true,
		    'rewrite_withfront' => true,
		    'supports' => 
			    array (
			      0 => 'title',
			    ),
		    'labels' => $labels,
		);

	    register_post_type( 'oxy_user_library', $args );
	}


	function ct_block_library_page() {

		if(!oxygen_vsb_current_user_can_full_access()) {
			return;
		}

		add_submenu_page( 	'ct_dashboard_page', 
							'Block Library', 
							'Block Library', 
							'read', 
							'edit.php?post_type=oxy_user_library');
		
	}

	function ct_connection_body_class($classes) {
		
		if ( defined("SHOW_CT_BUILDER") ) {
			$classes[] = 'ct_connection_active';
		}

		return $classes;
	}


	/* related to pages categories */

	function ct_connection_page_category_meta_box() {

		if(!oxygen_vsb_current_user_can_full_access()) {
			return;
		}

		$screen = get_current_screen();
		// check if this post type is set to be ignored
		$ignore = get_option('oxygen_vsb_ignore_post_type_'.$screen->post_type, false);

		if($ignore == "true") {
			return;
		}

		add_meta_box(
			'ct_connection_metabox',
			__( 'Oxygen - Design Set Options', 'component-theme' ),
			array($this, 'ct_connection_page_category_box_callback'),
			array('page', 'ct_template', 'oxy_user_library'),
			'normal',
			'high'
		);

	}

	

	function ct_connection_page_category_box_callback() {
		
		global $post;
		
		wp_enqueue_script( 'oxygen_vsb_connection_admin');

		wp_nonce_field( 'ct_connection_metabox', 'ct_connection_metabox_nonce' );

		$ct_connection_use_sections = get_post_meta($post->ID, '_ct_connection_use_sections', true);
		$ct_connection_use_page = get_post_meta($post->ID, '_ct_connection_use_page', true);
		$ct_connection_use_default = get_post_meta($post->ID, '_ct_connection_use_default', true);
		$ct_connection_page_category = get_post_meta($post->ID, '_ct_connection_page_category', true);

		$categories = array(
			'home' => 'Home',
			'content' => 'Content',
			'pricing' => 'Pricing',
			'about' => 'About',
			'contact' => 'Contact',
			'onepagelanding' => 'One-Page & Landing'
		);
		wp_enqueue_media();
		?>
		<div class="oxygen-connection-checkboxes-section">
		<?php
		if( $post->post_type != 'oxy_user_library') {
		?>
			<div class="oxygen-metabox-control-group">
				<label class="connection-attributes-label" for="ct_connection_use_default">
					<input type="checkbox" name="ct_connection_use_default" id="ct_connection_use_default" value='true' <?php echo $ct_connection_use_default? 'checked' : '';?> />
					Include this <?php echo $post->post_type == 'ct_template' ? 'template' : 'page'; ?> in the default setup
				</label>
				<br />
				<label class="connection-attributes-label" for="ct_connection_use_sections">
					<input type="checkbox" name="ct_connection_use_sections" id="ct_connection_use_sections" value='true' <?php echo $ct_connection_use_sections? 'checked' : '';?> />
					Include the sections in this <?php echo $post->post_type == 'ct_template' ? 'template' : 'page'; ?> in the library
				</label>
				<br />
				<label class="connection-attributes-label" for="ct_connection_use_page">
					<input type="checkbox" name="ct_connection_use_page" id="ct_connection_use_page" value='true' <?php echo $ct_connection_use_page? 'checked' : '';?> />
					Include this entire <?php echo $post->post_type == 'ct_template' ? 'template' : 'page'; ?> in the library
				</label>
				
				
			</div>

		<?php 
		}
		if($post->post_type == 'page') {
		?>
			<div id="ct-connection-category" <?php echo $ct_connection_use_page? '' : ' style="display:none;"';?>>
				<label class="post-attributes-label" for="ct_connection_page_category">Page Category</label>
				<select name="ct_connection_page_category" id="ct_connection_page_category">
					<option value="">(none)</option>
					<?php
					
					$category = get_post_meta($post->ID, '_ct_connection_page_category', true);
					
					foreach($categories as $val=>$label) {
						?>
							<option value="<?php echo esc_attr($val);?>" <?php echo ($val === $category)?"selected":'';?>><?php echo esc_html($label);?></option>
						<?php
					}

					?>
				</select>
			</div>

		<?php
		} 

		if($post->post_type == 'oxy_user_library') {
		?>
			<div>
				<label class="post-attributes-label" for="ct_connection_page_category">Block Category</label>
				<select name="ct_connection_page_category" id="ct_connection_page_category" class="ct_connection_block_category">
					<option value="">(none)</option>
					<?php
					
					$category = get_post_meta($post->ID, '_ct_connection_page_category', true);
					
					global $ct_component_categories;

					foreach($ct_component_categories as $item) {
						?>
							<option value="<?php echo esc_attr($item);?>" <?php echo ($item === $category)?"selected":'';?>><?php echo esc_html($item);?></option>
						<?php
					}

					?>
				</select>

				<ul id="ct-connection-block-warning" <?php echo (!$category || empty($category))?'':'style="display: none"'; ?>>
					<li class="ct-connection-notification-error" style="margin-left: 10px">
						Uncategorized Blocks will not be included in the library
					</li>
				</ul>
			</div>

		<?php
		} 
		?>
		</div>

		<div class="oxygen-vsb-apply-template-label">Screenshots</div>
		<div class="oxygen-connection-screenshots-section">
			<ul id="ct-connection-screenshot-messages">
				<li class="ct-connection-notification" style='display:none'>
					Screenshots successfully generated
				</li>
		<?php

		$screenshots_generated = get_post_meta($post->ID, '_ct_connection_screenshots_generated', true);

		if(!$screenshots_generated) {
			?>
				<li class="ct-connection-notification-error">
					Warning: Screenshots never generated
				</li>

			<?php
		} else {
			?>
				<li class="ct-connection-notification">
					Screenshots last generated at: <?php echo esc_html($screenshots_generated);?>
				</li>


			<?php
			$screenshots_updated = (strtotime($post->post_modified) < strtotime($screenshots_generated));
		?>
			
				<li class="ct-connection-notification <?php echo $screenshots_updated?'':'ct-connection-notification-error';?>">
					This page has <span><?php echo $screenshots_updated ? 'not ':'';?></span>been edited since screenshots were last generated
				</li>
		
		
		<?php
		}
		?>
			</ul>
		<?php
		if($post->post_type == 'ct_template') {
			$ct_connection_page_screenshot = get_post_meta($post->ID, '_ct_connection_page_screenshot', true);
			$ct_connection_template_screenshot_url = get_post_meta($post->ID, '_ct_connection_template_screenshot_url', true);
		?>
			<div class="oxygen-metabox-screenshot-inputs">
				<div class="oxygen-metabox-control-group">
					<label for="ct_connection_page_screenshot">Specify Your Custom Screenshot URL</label>
					<input type="text" name="ct_connection_page_screenshot" class="oxygen-vsb-metabox-input" id="oxygen_vsb_site_screenshot" value="<?php echo esc_attr($ct_connection_page_screenshot);?>" />

				</div>
			</div>
		<?php
		}

		else {
			$oxy_custom_screenshot = get_post_meta($post->ID, 'oxy_custom_screenshot', true);
		?>
			<div class="oxygen-metabox-screenshot-inputs">
				<div class="oxygen-metabox-control-group">
					<label for="ct_connection_template_screenshot_url">
					<?php _e("Specify Your Custom Screenshot URL", "oxygen"); ?>
					<input type="text" name="oxy_custom_screenshot" class="oxygen-vsb-metabox-input" id="oxy_custom_screenshot" value="<?php echo esc_attr($oxy_custom_screenshot);?>" /></label>
				</div>
			</div>
		<?php
		}
		
		
		wp_nonce_field( 'ct_connection_generate_screenshot', 'ct_connection_generate_screenshot_nonce' );
		?>
			<p>
				<button class="button button-primary" id='oxygen-connection-generate-screenshots' disabled data-processing='Processing Screenshots...' data-postId='<?php echo esc_attr($post->ID);?>'>Generate Screenshots</button>
			</p>
		
		<?php
		$this->oxygen_vsb_connection_media_browser('upload_image_button', 'oxygen_vsb_site_screenshot');

		?>

		</div>
		<?php
	}

	function ct_connection_metabox_save($post_id){

 		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
		    return $post_id;


		if (!isset($_POST['ct_connection_metabox_nonce']) || !wp_verify_nonce( $_POST['ct_connection_metabox_nonce'], 'ct_connection_metabox' )) {
			return $post_id;
		}


		  // Check permissions to edit pages and/or posts
		  if ( isset($_POST['post_type']) && ('page' == sanitize_text_field($_POST['post_type']) ||  'post' == sanitize_text_field($_POST['post_type']))) {
		    if ( !current_user_can( 'edit_page', $post_id ) || !current_user_can( 'edit_post', $post_id ))
		      return $post_id;
		  }
		  
		  $ct_connection_page_category = isset($_POST['ct_connection_page_category']) ? sanitize_text_field($_POST['ct_connection_page_category']): false;
		  $ct_connection_use_page = isset($_POST['ct_connection_use_page']) ? sanitize_text_field($_POST['ct_connection_use_page']): false;
		  $ct_connection_use_default = isset($_POST['ct_connection_use_default']) ? sanitize_text_field($_POST['ct_connection_use_default']): false;
		  $ct_connection_use_sections = isset($_POST['ct_connection_use_sections']) ? sanitize_text_field($_POST['ct_connection_use_sections']): false;
		  $ct_connection_page_screenshot = isset($_POST['ct_connection_page_screenshot']) ? sanitize_text_field($_POST['ct_connection_page_screenshot']): false;
		  $ct_connection_template_screenshot_url = isset($_POST['ct_connection_template_screenshot_url']) ? sanitize_text_field($_POST['ct_connection_template_screenshot_url']): false;
		  $oxy_custom_screenshot = isset($_POST['oxy_custom_screenshot']) ? sanitize_text_field($_POST['oxy_custom_screenshot']): false;

		  if($ct_connection_page_category) {
			update_post_meta($post_id, '_ct_connection_page_category', $ct_connection_page_category); 
		  }
		  else {
			delete_post_meta($post_id, '_ct_connection_page_category');
		  }


		  if($ct_connection_use_page) {
			update_post_meta($post_id, '_ct_connection_use_page', $ct_connection_use_page); 
		  }
		  else {
			delete_post_meta($post_id, '_ct_connection_use_page');
		  }

		  if($ct_connection_use_default) {
			update_post_meta($post_id, '_ct_connection_use_default', $ct_connection_use_default); 
		  }
		  else {
			delete_post_meta($post_id, '_ct_connection_use_default');
		  }

		  if($ct_connection_use_sections) {
			update_post_meta($post_id, '_ct_connection_use_sections', $ct_connection_use_sections); 
		  }
		  else {
			delete_post_meta($post_id, '_ct_connection_use_sections');
		  }

		  if($ct_connection_page_screenshot) {
			update_post_meta($post_id, '_ct_connection_page_screenshot', $ct_connection_page_screenshot); 
		  }
		  else {
			delete_post_meta($post_id, '_ct_connection_page_screenshot');
		  }

		  if($ct_connection_template_screenshot_url) {
			update_post_meta($post_id, '_ct_connection_template_screenshot_url', $ct_connection_template_screenshot_url); 
		  }
		  else {
			delete_post_meta($post_id, '_ct_connection_template_screenshot_url');
		  }

		  if($oxy_custom_screenshot) {
			update_post_meta($post_id, 'oxy_custom_screenshot', $oxy_custom_screenshot); 
		  }
		  else {
			delete_post_meta($post_id, 'oxy_custom_screenshot');
		  }

	}

	


	function oxygen_vsb_connection_register_settings() {

		if(!oxygen_vsb_current_user_can_access()) {
			return;
		}

	   add_option( 'oxygen_vsb_site_screenshot', '');

	   register_setting( 'oxygen_vsb_options_group', 'oxygen_vsb_color_lookup_table', array($this, 'oxygen_vsb_color_lookup_table_convert') );

	   add_option( 'oxygen_vsb_screenshot_generate_url', '');
	   

	   register_setting( 'oxygen_vsb_options_group_library', 'oxygen_vsb_screenshot_generate_url' );

	   register_setting( 'oxygen_vsb_options_group_library', 'oxygen_vsb_site_screenshot', array('sanitize_callback' => array($this, 'oxygen_vsb_site_screenshot_validation')) );

	   //register_setting( 'oxygen_vsb_connection_options_group', 'oxygen_vsb_connection_access_key' );

	   $oxygen_vsb_connection_access_key = get_option('oxygen_vsb_connection_access_key', false);

	   if($oxygen_vsb_connection_access_key === false || isset($_REQUEST['connectionkey'])) {
			// generate a new access key
			$oxygen_vsb_connection_access_key = base64_encode(openssl_random_pseudo_bytes(9));

			// and store it		
	  		update_option('oxygen_vsb_connection_access_key', $oxygen_vsb_connection_access_key);
	  		
	  	}
	  	if(isset($_REQUEST['connectionkey'])) {
	  		wp_redirect(add_query_arg(array('page' => 'oxygen_vsb_settings', 'tab' => 'library_manager'), get_admin_url().'admin.php'));
	  		exit();
	  	}
		

	}



	function oxygen_vsb_site_screenshot_validation($data) {
		return esc_url($data);
	}

	function oxygen_vsb_color_lookup_table_convert($data) {
		
		if(empty($data)) {
            return $data;
		}
        
        $data = explode("\r\n", $data);

		$processedData = array();

		foreach($data as $item) {
			if(strpos($item, '=') < 0 || strripos($item, '=') !== strpos($item, '=')) {
				continue;
			}

			$exploded = explode('=', $item);

			$processedData[trim($exploded[0])] = trim($exploded[1]);
		}

		return $processedData;

	}

	function oxygen_vsb_connection_callback() {
		
		wp_enqueue_script( 'oxygen_vsb_connection_admin');
		wp_enqueue_media();
	?>
	  

		  <?php 
		  	wp_nonce_field( 'ct_connection_generate_screenshot', 'ct_connection_generate_screenshot_nonce' );
		  	$oxygen_vsb_connection_access_key = get_option('oxygen_vsb_connection_access_key', '');

		  	$oxygen_vsb_screenshot_generate_url = get_option('oxygen_vsb_screenshot_generate_url', '');
		  	$oxygen_vsb_site_screenshot = get_option('oxygen_vsb_site_screenshot', '');

		  	$url = get_site_url();
		  	$title = get_bloginfo( 'name' );
		  	
		  	?>
			<?php add_thickbox(); ?>
		  	<div style="margin-top: 20px">
		  		<div id="site-key-tooltip-container" style="margin-bottom: 7px;">
		  			<label style="margin-bottom: 12px;">Site Key <a href="#" id="site-key-tooltip-action">?</a></label>
		  			<div id="site_key_tooltip" class="hidden">
		  				<strong>How to Use This Design Set on Another Oxygen + WordPress Install</strong>

						<ol>
							<li>Login to the WP admin for the install
							</li>
							<li>Go to <i>Oxygen &gt; Settings &gt; Library</i>
							</li>
							<li>Check the <i>Enable 3rd Party Design Sets</i> box and save settings.
							</li>
							<li>Click <i>+Add Design Set</i> and paste in this <i>Site Key</i>.
							</li>
						</ol>
					</div>
		  		</div>
		  		<textarea style="margin-top: 0px; margin-bottom: 0px; height: 70px;"><?php echo base64_encode($url."\n".$title."\n".$oxygen_vsb_connection_access_key);?></textarea>
		  		<br />
		  		<small><a id="regenerate-key" href="<?php echo add_query_arg(array('page' => 'oxygen_vsb_settings', 'tab' => 'library_manager', 'connectionkey' => 'new'), get_admin_url().'admin.php');?>">Regenerate Key</a></small>
		  		<script type="text/javascript">
		  			jQuery(document).ready(function($){
		  				$('#site-key-tooltip-action').on('click', function(e) {
		  					e.preventDefault();
		  					e.stopPropagation();
		  					$('#site_key_tooltip').toggleClass('hidden');

		  					if(!$('#site_key_tooltip').hasClass('hidden')) {
			  					setTimeout(function() {
			  						$('body').one('click', function(e) {
				  						
				  						e.preventDefault();
				  						e.stopPropagation();
				  						$('#site_key_tooltip').addClass('hidden');
				  					
				  					})
			  					}, 500);
			  				}

		  				})

		  				$('#regenerate-key').on('click', function(e) {
		  					if(!confirm('This will revoke access to the users having the old key')) {
		  						e.preventDefault();
		  					}
		  				})

		  			});
		  		</script>
		  	</div>
		  	
			
			<div style="margin-top: 20px;">
			  <label for="oxygen_vsb_site_screenshot">URL to Site Screenshot</label><br>
			  <input type="text" id="oxygen_vsb_site_screenshot" name="oxygen_vsb_site_screenshot" value="<?php echo esc_attr($oxygen_vsb_site_screenshot); ?>" style="width: 300px;"/>
			  <small><a href="#" id="upload_image_button" style="margin-right: 8px;">media library</a></small>
			  <small><a href="#TB_inline?width=600&height=50&inlineId=generate_screenshot" id="upload_image_button" class="thickbox">auto-generate</a></small>
			</div>

			<div id="generate_screenshot" style="display:none;">
				<div style="padding:15px">
				  <label for="oxygen_vsb_screenshot_generate_url">Generate Screenshot from URL</label>
				  <input type="text" id="oxygen_vsb_screenshot_generate_url" name="oxygen_vsb_screenshot_generate_url" value="<?php echo esc_attr($oxygen_vsb_screenshot_generate_url); ?>" /> <button class="button" id='oxygen-connection-generate-screenshots' disabled data-processing='Processing Screenshot...'>Generate Site Screenshot</button>
				 </div>
			</div>


		  
			<div style="margin-top: 20px;">
		  		<p></p>
				<b>How to Create Your Own Design Library Elements</b>
				<ul style=" list-style: initial; margin-left: 25px; margin-top: 11px;">
					<li>Create elements at <a href="<?php echo get_admin_url().'edit.php?post_type=oxy_user_library';?>">Oxygen &gt; Block Library</a>.</li>
					<li>Find your elements in Oxygen at <i>+Add &gt; Library &gt; Sandbox</i>.</li>
					<li><a href="https://oxygenbuilder.com/documentation/design-library/user-design-library/" target="_blank">Watch the full tutorial with more details »</a></li>
				</ul>
			</div>

		  <p style="display:none"><a href="<?php echo add_query_arg('page', 'oxygen_vsb_qa', get_admin_url().'admin.php');?>">Experimental QA check tool >></a></p>
	  

	<?php
		$this->oxygen_vsb_connection_media_browser('upload_image_button', 'oxygen_vsb_site_screenshot');
	}


	function oxygen_vsb_connection_media_browser($buttonID, $fieldID) {
		?>
		<script>
		(function() {
			jQuery(document).ready(function() {
				var fileFrame = null;
				jQuery('#<?php echo $buttonID;?>').on('click', function( event ){
					
					event.preventDefault();
					
					// Create the media frame.
					fileFrame = wp.media.frames.fileFrame = wp.media({
						title: 'Select an image to upload',
						button: {
							text: 'Use this image',
						},
						multiple: false	
					});
					
					fileFrame.on( 'select', function() {
						
						attachment = fileFrame.state().get('selection').first().toJSON();
						console.log(attachment);
						console.log($( '#<?php echo $fieldID;?>' ));
						$( '#<?php echo $fieldID;?>' ).val( attachment.url );

					});

					fileFrame.open();
				});
				
			})
		})()

	</script>
	<?php
	}

	function oxygen_vsb_connection_register_options_page() {
		
		$qa_page = add_submenu_page(null, 'Oxygen QA Check', 'Oxygen QA Check', 'manage_options', 'oxygen_vsb_qa', array($this, 'oxygen_vsb_qa_check_page'));

		add_action( 'admin_print_styles-' . $qa_page, array($this, 'oxygen_vsb_qa_enqueue_style') );
	}

	
	function oxygen_vsb_qa_enqueue_style() {
		wp_enqueue_style( 'oxygen_vsb_qa_style', plugin_dir_url( __FILE__ ) . "admin/qa.css");
	}

	function oxygen_vsb_qa_check_page() {
		?>
		<p>
			<button id="start-qa-process">Process</button>
		</p>
		<div id="qa-output">

		</div>
		<script>
		
			jQuery(document).ready(function($) {
				var stepCount = 0;

				function processMessages(response, step, depth, parent) {

					if(typeof(depth) === 'undefined') {
						depth = 0;
					}

					if(typeof(parent) === 'undefined') {
						parent = $('#qa-output');
					}

					if(response['messages']) {
						
						var container;

						if(depth > 0 && step > 0) {

							container = $("<div></div>");

							if(response['messages'].length > 1) {
								container.addClass('lqa-shortcode-errors');

							}
							else {
								container.addClass('lqa-shortcode-noerrors');
							}

							if(step > 2) {
								container.addClass('lqa-class-count');
							}
							else {
								container.addClass('lqa-shortcode');
							}

							parent.append(container);

						}
						

						response['messages'].forEach(function(message, index) {
							
							var msgBlock = $('<div>').html(message);
							
							if(!step) {
								msgBlock.addClass('lqa-generic-error');
							}

							if(step === 1 || step === 2) {
								if(index === 0) {
									msgBlock.addClass('lqa-element-info');
								}
								else if(index > 0) {
									if(container.hasClass('lqa-shortcode-errors') && container.children('.lqa-errors').length === 0) {
										container.append('<div class="lqa-errors"></div>');
									}
								}
							}
							else if(step === 3 || step ===4) {
								if(index > 0) {
									msgBlock.addClass('lqa-error');
								}
							}

							if(container) {
								if(container.children('.lqa-errors').length > 0) {
									container.children('.lqa-errors').append(msgBlock.addClass('lqa-error'));
								}
								else
									container.append(msgBlock);	
							}
							else {
								parent.append(msgBlock);
							}

						});

					}

					if(response['children']) {
						depth++;
						response['children'].forEach(function(child) {
							processMessages(child, step, depth)
						})
					}

				}

				function processQAStuff(step, pageindex) {
					
					var data = {
						'action': 'oxygen_qa_process'
					};

					if(typeof(step) !== 'undefined') {
						data['step'] = step;
					}

					if(typeof(pageindex) !== 'undefined') {
						data['index'] = pageindex;
					}

					if(typeof(pageindex) === 'undefined') {
						
						if(!step) {
							$('#qa-output').append("<h2 class='lqa-stage'>Stage 1 - General Checks</h2>");
						}

						if(step == 1) {
							$('#qa-output').append("<h2 class='lqa-stage'>Stage 2 - Shortcode Checks</h2>");
						}

						if(step == 3) {
							$('#qa-output').append("<h2 class='lqa-stage'>Stage 3 - ID/Class Active Checks</h2>");
						}
					}

					jQuery.post(ajaxurl, data, function(response) {
						
						if(response['messages']) {
							processMessages(response, step);
						}

						if(typeof(response['step']) !== 'undefined') {
							
							if(typeof(response['index']) !== 'undefined') {
								processQAStuff(parseInt(response['step']), parseInt(response['index']));
							}
							else {
								processQAStuff(parseInt(response['step']));
							}
						}

					});
				}

				$('#start-qa-process').on('click', function() {
					$('#qa-output').html('');
					processQAStuff();

				});

			});

		</script>

		<?php
	}


	function oxygen_connection_qa_process() {

		if(!oxygen_vsb_current_user_can_access()) {
			return;
		}

		// step 0 = global checks, 1 = shortcode checks, 2 = ID class active checks
		$step = isset($_REQUEST['step'])?intval($_REQUEST['step']): 0;

		$response = array();

		switch($step) {

			case 0:
				$response = $this->oxygen_connection_qa_global_checks();
				$response['step'] = 1;
			break;

			case 1:
			case 2:
				$isTemplate = ($step == 2);
				$response = $this->oxygen_connection_qa_shortcodes($isTemplate);
				
				if(isset($response['index'])) {
					$response['step'] = $isTemplate?2:1; // if we are still looping through posts, i.e., next index is available then repeat this step
				}
				else {
					if(!$isTemplate) { // if it has been about pages
						$response['step'] = 2; // move to next step, i.e., to templates
					}
					elseif($isTemplate) { // if it is already a template
						$response['step'] = 3; // move to stage 3
					}
				}

			break;

			case 3:
			case 4:
				$isTemplate = ($step == 4);
				$response = $this->oxygen_connection_qa_shortcodes($isTemplate, true);
				
				if(isset($response['index'])) {
					$response['step'] = $isTemplate?4:3; // if we are still looping through posts, i.e., next index is available then repeat this step
				}
				else {
					if(!$isTemplate) { // if it has been about pages
						$response['step'] = 4; // move to next step, i.e., to templates
					}
				}
			break;

		}
		
		header('Content-Type: application/json');
		
		echo json_encode($response);

		die();
	}

	function oxygen_connection_qa_global_checks() {

		$messages = array();
		// stylesheets should not be present
		$styleSheets = get_option('ct_style_sheets');
		if(is_array($styleSheets) && sizeof($styleSheets) > 0) {
			$messages[] = 'Stylesheets are present';
		}

		//non allowed global styles should not be present
		$globalSettings = get_option('ct_global_settings');

		if(isset($globalSettings['max_width'])) {
			unset($globalSettings['max_width']);
		}

		if(isset($globalSettings['links']) && isset($globalSettings['links']['all'])) {
			unset($globalSettings['links']['all']);

			if(sizeof($globalSettings['links']) === 0) {
				unset($globalSettings['links']);
			}
		}

		if(isset($globalSettings['fonts'])) {
			unset($globalSettings['fonts']);
		}

		if(sizeof($globalSettings) > 0) {
			$messages[] = 'Non-allowed global styles are present';
		}

		return array('messages' => $messages);
	}


	function oxygen_connection_qa_shortcodes($isTemplate = false, $activeChecks = false) {
		
		$response = array('messages' => array(), 'children' => array());

		$type = $isTemplate?"ct_template":"page";

		$pages = get_posts(array("post_type" => array($type), 'numberposts' => -1));
		
		
		if(sizeof($pages) > 0) {

			// get index from request, if not available set it to zero
			$index = isset($_REQUEST['index'])?intval($_REQUEST['index']): 0;
			
			// get $pages[$index], get the shortcodes, do the process
			$page = $pages[$index];
			
			//$response['messages'][] = 'Processing '.($isTemplate?'Template':'Page').' with ID = '.$page->ID;
			// get the shortcodes of the page
			$shortcodes = get_post_meta($page->ID, 'ct_builder_shortcodes', true);

			$shortcodes = parse_shortcodes($shortcodes, false);

			

			if(is_array($shortcodes['content'])) {
				if($activeChecks) {
					$response['children'] = $this->oxygen_vsb_connection_qa_active_checks($shortcodes['content'], $page->ID);
				}
				else {
					$classes = get_option('ct_components_classes');
					$response['children'] = $this->oxygen_vsb_connection_qa_components($shortcodes['content'], $page->ID, $classes, true);
				}
			}


			if($index < sizeof($pages) - 1) {
				$index++;
				$response['index'] = $index;
			}
		}
		else {
			$response['messages'][]= 'no '.($isTemplate?'Template':'Page').'(s) found';
		}

		return $response;
	}

	function oxygen_vsb_connection_qa_active_checks($shortcodes, $pageID) {

		$output = array();
		$names = apply_filters( "ct_components_nice_names", array() );
		foreach($shortcodes as $shortcode) {

			// recursively go through the child elements and prepare a map of classes used, number of occurances, if that class is active
			$occurances = array();
			if(isset($shortcode['children'])) {
				$occurances = $this->oxygen_vsb_connection_qa_class_occurances($shortcode['children'], $occurances);
			}

			foreach($occurances as $class => $occurance) {
				$response = array('messages' => array());
				if(is_array($occurance) && sizeof($occurance) > 1) {
					$response['messages'][] = ".$class is used ".sizeof($occurance)." times inside ".(isset($shortcode['options']['selector'])?" <a href='".ct_get_post_builder_link( $pageID )."&focus=".$shortcode['options']['selector']."' class='lqa-element-id'>#".$shortcode['options']['selector']."</a>":"");

					foreach($occurance as $item) {
						if(!$item['status'] || empty($item['status'])) {
							$response['messages'][] = "class is not active by default on #".$item['id'];
						}
					}

					$output[] = $response;
				}
			}
		}

		return $output;
	}

	function oxygen_vsb_connection_qa_class_occurances($children, $occurances) {

		foreach($children as $child) {
			if(isset($child['options']['classes']) && is_array($child['options']['classes'])) {
				foreach($child['options']['classes'] as $class) {
					if(isset($occurances[$class])) {
						$occurances[$class][] = array('id'=>$child['id'], 'status'=>isset($child['options']['activeselector']) && $child['options']['activeselector'] == $class);
					}
					else {
						$occurances[$class] = array(array('id'=>$child['id'], 'status'=>isset($child['options']['activeselector']) && $child['options']['activeselector'] == $class));
					}
				}
			}

			if(isset($child['children'])) {
				$occurances = $this->oxygen_vsb_connection_qa_class_occurances($child['children'], $occurances);
			}
		}

		return $occurances;
	}

	function oxygen_vsb_connection_qa_components($children, $pageID, $classes, $rootLevel = false) {

		$output = array();
		
		foreach($children as $child) {

			$response = array('messages' => array());

			// do all the processing here
			// throw error if
			
			// it is a reusable
			if( $child['name'] === 'ct_reusable' ) {
				$response['messages'][] = 'It is a reusable';
			}
			
			// it is a codeblock
			if( $child['name'] === 'ct_code_block' ) {
				$response['messages'][] = 'It is a codeblock';
			}

			//If a Social Icons element is used, ensure the icon size is a multiple of 4, if not, output "icon size is not a multiple of 4"

			if( isset($child['options']['ct_content']) ) {
				// compare with banned words
				$bannedWords = array('arse',
					'ass',
					'asshole',
					'bastard',
					'bitch',
					'bollocks',
					'child-fucker',
					'Christ on a bike',
					'Christ on a cracker',
					'crap',
					'cunt',
					'damn',
					'swear word',
					'faggot',
					'frigger',
					'fuck',
					'gay',
					'goddamn',
					'godsdamn',
					'hell',
					'holy shit',
					'jesus',
					'Jesus',
					'Judas Priest',
					'motherfucker',
					'nigga',
					'nigger',
					'sex',
					'shit',
					'shit ass',
					'shitass',
					'son of a bitch',
					'son of a motherless goat',
					'son of a whore',
					'sweet Jesus',
					'twat');

				foreach($bannedWords as $word) {
					if(strpos(trim(strtolower($child['options']['ct_content'])), trim(strtolower($word))) !== false) {
						$response['messages'][] = "Banned word ($word) found in content";
					}
				}

			}

			$allowed = array('original', 'hover', 'before', 'after', 'media'); 

			if(is_array($child['options'])) {
			
				foreach($child['options'] as $key => $option) {

					if(!in_array($key, $allowed)) {
						continue;
					}

					if($key === 'media') {
						foreach($option as $mediaKey => $media) {
							foreach($media as $stateKey => $state) {
								$response['messages'] = array_merge($response['messages'], $this->oxygen_vsb_qa_properties($child['name'], $state, $stateKey, $mediaKey));
							}
						}
					}
					else {
						$response['messages'] = array_merge($response['messages'], $this->oxygen_vsb_qa_properties($child['name'], $option, $key));
					}
				}
			}

			if(isset($child['options']['classes']) && is_array($child['options']['classes'])) {
				
				foreach($child['options']['classes'] as $className) {
					
					$class = isset($classes[$className]) ? $classes[$className] : false;
					
					if(!$class || !is_array($class)) {
						continue;
					}

					foreach($class as $key => $option) {

						if($key === 'media') {
							foreach($option as $mediaKey => $media) {
								foreach($media as $stateKey => $state) {
									$response['messages'] = array_merge($response['messages'], $this->oxygen_vsb_qa_properties($child['name'], $state, $stateKey, $className, $mediaKey));
								}
							}
						}
						else {
							$response['messages'] = array_merge($response['messages'], $this->oxygen_vsb_qa_properties($child['name'], $option, $key, $className));
						}
					}

				}
			}

			$names = apply_filters( "ct_components_nice_names", array() );
			
			$response['messages'] = array_merge(array("<span class='lqa-element-name'>".(isset($child['options']['nicename'])?$child['options']['nicename']:$names[$child['name']]." (#".$child['id'].")")."</span> <span class='lqa-element-type'>".$names[$child['name']]."</span>".(isset($child['options']['selector'])?" <a href='".ct_get_post_builder_link( $pageID )."&focus=".$child['options']['selector']."' class='lqa-element-id'>#".$child['options']['selector']."</a>":"")), $response['messages']);
			
			
			// if($rootLevel) {
			// 	$response['messages'] = array_merge(array('Processing Component with ID#'.$child['id']), $response['messages']);
			// }


			// states: original, hover, before, after
			// media:

				

				

			// bad word found in ct_content


			if(isset($child['children'])) {
				$response['children'] = $this->oxygen_vsb_connection_qa_components($child['children'], $pageID, $classes);
			}

			$output[] = $response;
			//$messages[] = array('depth' => $rootLevel?0:1, 'text' => 'done component '.$child['id']);
		}

		return $output;
	}


	function oxygen_vsb_qa_properties($tag, $state, $stateKey, $className = false, $mediaKey = false) {
		
		$messages = array();
		$error = false;

		foreach($state as $key => $item) {
			$error = false;
			$property = $key;

			// font is set explicitly
			if( $key === 'font-family' ) {

				if(!is_array($item) && $item !== 'Inherit') {
					$error = 'font is set explicilty';
				}

			}

			// go through all the properties and see if any of the items have unit as a post-fix and the unit is set to em
			if( substr($key, strlen($key)-5, 5) === '-unit' && $item === 'em') {
				$error = 'unit has been set to em';
				$property = substr($key, 0, strlen($key)-5);
			}



			//background image URL does not begin with https://*.oxy.host (.oxy-main-oijsd)
			
			if($key === 'background-image') {
				
				if(!preg_match('/https\:\/\/(.*).oxy.host/', $item)) {
					$error = 'URL does not begin with https://*.oxy.host';
				}

			}

			//image URL does not begin with https://*.oxy.host
			if($key === 'src') {

				if(!preg_match('/https\:\/\/(.*).oxy.host/', $item)) {

					$error = 'URL does not begin with https://*.oxy.host';
				}

			}

			// custom CSS is present in id
			if($key === 'custom-css' && !empty($item)) {
				$error = 'custom CSS is set';
			}

			// custom JS is present in id
			if($key === 'custom-js' && !empty($item)) {
				$error = 'custom JS is set';
			}

			// if is social icons and icon size is not a multiple of 4
			if($tag === 'oxy_social_icons' && $key === 'icon-size' && (intval($item) % 4) !== 0) {
				$error = 'value is not a multiple of 4';
			}

			if($error) {
				$messages[] = $error.' for property '.$property. ' of state ('.$stateKey.')'.($mediaKey?' of breakpoint ('.$mediaKey.')':''.($className?" class: (.$className)":''));
			}
			
			
		}

		// if is social icons and icon size is not specified
		if($tag === 'oxy_social_icons' && !isset($state['icon-size'])) {
			$error = 'value is not a multiple of 4';
			$messages[] = $error.' for property icon-size of state ('.$stateKey.')'.($mediaKey?' of breakpoint ('.$mediaKey.')':''.($className?" of class $className":''));
		}

		return $messages;
	}

	function oxygen_vsb_admin_script() {
		
		wp_register_script( 'oxygen_vsb_connection_admin', plugin_dir_url( __DIR__ ) . "admin/js/oxygen_connection_admin.js", array('jquery'));
	}


	function oxygen_vsb_screenshot_script() {
		
		if(isset($_REQUEST['render_component_screenshot']) && stripslashes($_REQUEST['render_component_screenshot']) == 'true') {
				$selector  = isset($_REQUEST['selector'])?sanitize_text_field($_REQUEST['selector']): '';
				$post = get_post();
				show_admin_bar(false);
			?>
			<script>
				var oxygen_vsb_selectiveRenderingParams = {
					selector: '<?php echo $selector;?>',
					post_id: <?php echo $post->ID;?>
				}
			</script>
		<?php
			wp_enqueue_script( 'oxygen_vsb_screenshot_script', plugin_dir_url( __DIR__ ) . "admin/js/oxygen_connection_script.js", array('jquery'));
		}

	}

	function oxygen_vsb_connection_register_routes() {

		register_rest_route('oxygen-vsb-connection/v1', '/screenshot/', array(
			'methods' => 'GET',
			'callback' => array($this, 'oxygen_vsb_return_website_screenshot'),
			'permission_callback' => array($this, 'oxygen_vsb_access_key_check')
			));

		register_rest_route('oxygen-vsb-connection/v1', '/templates/', array(
			'methods' => 'GET',
			'callback' => array($this, 'oxygen_vsb_connection_templates'),
			'permission_callback' => array($this, 'oxygen_vsb_access_key_check')
			));

		// register_rest_route('oxygen-vsb-connection/v1', '/templates/(?P<accesskey>[a-zA-Z0-9-]+)', array(
		// 	'methods' => 'GET',
		// 	'callback' => array($this, 'oxygen_vsb_connection_templates'),
		// 	));

		register_rest_route('oxygen-vsb-connection/v1', '/stylesheets/', array(
			'methods' => 'GET',
			'callback' => array($this, 'oxygen_vsb_connection_stylesheets'),
			'permission_callback' => array($this, 'oxygen_vsb_access_key_check')
			));

		// register_rest_route('oxygen-vsb-connection/v1', '/stylesheets/(?P<accesskey>[a-zA-Z0-9-]+)', array(
		// 	'methods' => 'GET',
		// 	'callback' => array($this, 'oxygen_vsb_connection_stylesheets'),
		// 	));

		register_rest_route('oxygen-vsb-connection/v1', '/stylesets/', array(
			'methods' => 'GET',
			'callback' => array($this, 'oxygen_vsb_connection_stylesets'),
			'permission_callback' => array($this, 'oxygen_vsb_access_key_check')
			));

		// register_rest_route('oxygen-vsb-connection/v1', '/stylesets/(?P<accesskey>[a-zA-Z0-9-]+)', array(
		// 	'methods' => 'GET',
		// 	'callback' => array($this, 'oxygen_vsb_connection_stylesets'),
		// 	));

		register_rest_route('oxygen-vsb-connection/v1', '/settings/', array(
			'methods' => 'GET',
			'callback' => array($this, 'oxygen_vsb_connection_settings'),
			'permission_callback' => array($this, 'oxygen_vsb_access_key_check')
			));

		// register_rest_route('oxygen-vsb-connection/v1', '/settings/(?P<accesskey>[a-zA-Z0-9-]+)', array(
		// 	'methods' => 'GET',
		// 	'callback' => array($this, 'oxygen_vsb_connection_settings'),
		// 	));

		register_rest_route('oxygen-vsb-connection/v1', '/classes/', array(
			'methods' => 'GET',
			'callback' => array($this, 'oxygen_vsb_connection_classes'),
			'permission_callback' => array($this, 'oxygen_vsb_access_key_check')
			));

		// register_rest_route('oxygen-vsb-connection/v1', '/classes/(?P<accesskey>[a-zA-Z0-9-]+)', array(
		// 	'methods' => 'GET',
		// 	'callback' => array($this, 'oxygen_vsb_connection_classes'),
		// 	));


		register_rest_route('oxygen-vsb-connection/v1', '/colors/', array(
			'methods' => 'GET',
			'callback' => array($this, 'oxygen_vsb_connection_colors'),
			'permission_callback' => array($this, 'oxygen_vsb_access_key_check')
			));

		// register_rest_route('oxygen-vsb-connection/v1', '/colors/(?P<accesskey>[a-zA-Z0-9-]+)', array(
		// 	'methods' => 'GET',
		// 	'callback' => array($this, 'oxygen_vsb_connection_colors'),
		// 	));

		register_rest_route('oxygen-vsb-connection/v1', '/selectors/', array(
			'methods' => 'GET',
			'callback' => array($this, 'oxygen_vsb_connection_selectors'),
			'permission_callback' => array($this, 'oxygen_vsb_access_key_check')
			));
		
		// register_rest_route('oxygen-vsb-connection/v1', '/selectors/(?P<accesskey>[a-zA-Z0-9-]+)', array(
		// 	'methods' => 'GET',
		// 	'callback' => array($this, 'oxygen_vsb_connection_selectors'),
		// 	));

		register_rest_route('oxygen-vsb-connection/v1', '/components/', array(
			'methods' => 'GET',
			'callback' => array($this, 'oxygen_vsb_connection_components'),
			'permission_callback' => array($this, 'oxygen_vsb_access_key_check')
			));

		register_rest_route('oxygen-vsb-connection/v1', '/addrequest/', array(
			'methods' => 'GET',
			'callback' => array($this, 'oxygen_vsb_connection_addrequest'),
			'permission_callback' => array($this, 'oxygen_vsb_access_key_check')
			));

		register_rest_route('oxygen-vsb-connection/v1', '/items/', array(
			'methods' => 'GET',
			'callback' => array($this, 'oxygen_vsb_connection_items'),
			'permission_callback' => array($this, 'oxygen_vsb_access_key_check')
			));

		// register_rest_route('oxygen-vsb-connection/v1', '/items/(?P<accesskey>[a-zA-Z0-9-]+)', array(
		// 	'methods' => 'GET',
		// 	'callback' => array($this, 'oxygen_vsb_connection_items'),
		// 	));

		register_rest_route('oxygen-vsb-connection/v1', '/components/(?P<id>\d+)/(?P<page>\d+)', array(
			'methods' => 'GET',
			'callback' => array($this, 'oxygen_vsb_connection_component'),
			'permission_callback' => array($this, 'oxygen_vsb_access_key_check')
			));

		register_rest_route('oxygen-vsb-connection/v1', '/componentsclasses/(?P<id>\d+)/(?P<page>\d+)', array(
			'methods' => 'GET',
			'callback' => array($this, 'oxygen_vsb_connection_component_classes'),
			'permission_callback' => array($this, 'oxygen_vsb_access_key_check')
			));

		// register_rest_route('oxygen-vsb-connection/v1', '/componentsclasses/(?P<id>\d+)/(?P<page>\d+)/(?P<accesskey>[a-zA-Z0-9-]+)', array(
		// 	'methods' => 'GET',
		// 	'callback' => array($this, 'oxygen_vsb_connection_component_classes'),
		// 	));

		register_rest_route('oxygen-vsb-connection/v1', '/pages/', array(
			'methods' => 'GET',
			'callback' => array($this, 'oxygen_vsb_connection_pages'),
			'permission_callback' => array($this, 'oxygen_vsb_access_key_check')
			));

		register_rest_route('oxygen-vsb-connection/v1', '/allpages/', array(
			'methods' => 'GET',
			'callback' => array($this, 'oxygen_vsb_connection_pages'),
			'permission_callback' => array($this, 'oxygen_vsb_access_key_check')
			));

		// register_rest_route('oxygen-vsb-connection/v1', '/allpages/(?P<accesskey>[a-zA-Z0-9-]+)', array(
		// 	'methods' => 'GET',
		// 	'callback' => array($this, 'oxygen_vsb_connection_pages'),
		// 	));

		register_rest_route('oxygen-vsb-connection/v1', '/pages/(?P<id>\d+)', array(
			'methods' => 'GET',
			'callback' => array($this, 'oxygen_vsb_connection_page'),
			'permission_callback' => array($this, 'oxygen_vsb_access_key_check')
			));

		register_rest_route('oxygen-vsb-connection/v1', '/pagesclasses/(?P<id>\d+)', array(
			'methods' => 'GET',
			'callback' => array($this, 'oxygen_vsb_connection_page_classes'),
			'permission_callback' => array($this, 'oxygen_vsb_access_key_check')
			));
		
		// register_rest_route('oxygen-vsb-connection/v1', '/pagesclasses/(?P<id>\d+)/(?P<accesskey>[a-zA-Z0-9-]+)', array(
		// 	'methods' => 'GET',
		// 	'callback' => array($this, 'oxygen_vsb_connection_page_classes'),
		// 	));

	}


	function oxygen_vsb_access_key_check($request) {
		
		$headers = $request->get_headers();
		
		$accessKey = isset($headers['auth']) && isset($headers['auth'][0])?sanitize_text_field($headers['auth'][0]):false;

		$storedKey = get_option('oxygen_vsb_connection_access_key');

		if(	$accessKey && $storedKey && hash_equals($accessKey, md5($storedKey)) ) {
			return true;
		} elseif($storedKey === false && $accesskey === false) {
			return true;
		}

		return false;
	}

	// function oxygen_vsb_connection_error() {
	// 	return array('error' => 'access denied');
	// }

	// function oxygen_vsb_connection_access($accessKey) {

	// 	$storedKey = get_option('oxygen_vsb_connection_access_key');

	// 	if(	$accessKey && $storedKey && hash_equals($accessKey, md5($storedKey)) ) {
	// 		return array('access' => 1);
	// 	}

	// 	header("HTTP/1.1 401 Unauthorized");
	// 	die();
	// 	return false;
	// }

	function oxygen_vsb_connection_addrequest($request) {
		return array('access' => 1);
	}

	function oxygen_vsb_return_website_screenshot($request) {
		return array('screenshot' => get_option('oxygen_vsb_site_screenshot')?get_option('oxygen_vsb_site_screenshot'):'http://via.placeholder.com/600x400?text=no+screenshot');
	}

	function oxygen_vsb_connection_templates($request) {

		$headers = $request->get_headers();
		$supportsVariableColors = isset($headers['oxygenclientversion']);
		
		$templates = get_posts(array("post_type" => "ct_template", 'numberposts' => -1));

		$return_templates = array();

		foreach ($templates as $template) {

			$use_page_default = get_post_meta($template->ID, '_ct_connection_use_default', true);
			
			if(!$use_page_default) {
				continue;
			}

			$return_template['ID'] = $template->ID;
			$return_template['post_title'] = $template->post_title;
			
			$return_template['builder_shortcodes'] = get_post_meta($template->ID, 'ct_builder_shortcodes', true);

			$shortcodes = parse_shortcodes($return_template['builder_shortcodes'], false);

			$return_template['applied_classes'] = $this->oxygen_vsb_connection_applied_classes($shortcodes);

			if(function_exists('oxygen_vsb_get_global_color_value') && !$supportsVariableColors) { // check for variable colors and replace with color values 
				$return_template['builder_shortcodes'] = preg_replace_callback('/color\((\d*)\)/', array($this, 'oxygen_vsb_connection_global_color_match'), $return_template['builder_shortcodes']);
			}

			$return_template['template_type'] = get_post_meta($template->ID, 'ct_template_type', true);
			
			$return_template['template_order'] = get_post_meta($template->ID, 'ct_template_order', true);
			$return_template['parent_template'] = get_post_meta($template->ID, 'ct_parent_template', true);

			$return_template['template_single_all'] = get_post_meta($template->ID, 'ct_template_single_all', true);
			$return_template['template_post_types'] = get_post_meta($template->ID, 'ct_template_post_types', true);
			$return_template['use_template_taxonomies'] = get_post_meta($template->ID, 'ct_use_template_taxonomies', true);
			
			
			// map slugs to ids
			if($return_template['use_template_taxonomies']) {
				$return_template['template_taxonomies'] = get_post_meta($template->ID, 'ct_template_taxonomies', true);

				foreach($return_template['template_taxonomies']['values'] as $key => $val) {
					// get slug for the id
					$term = get_term($val);
					if($term) {
						$return_template['template_taxonomies']['values'][$key] = $term->slug;
					}

				}
			}

			$return_template['template_apply_if_post_of_parents'] = get_post_meta($template->ID, 'ct_template_apply_if_post_of_parents', true);
			$return_template['template_post_of_parents'] = get_post_meta($template->ID, 'ct_template_post_of_parents', true);
			$return_template['template_all_archives'] = get_post_meta($template->ID, 'ct_template_all_archives', true);
			$return_template['template_apply_if_archive_among_taxonomies'] = get_post_meta($template->ID, 'ct_template_apply_if_archive_among_taxonomies', true);
			
			if($return_template['template_apply_if_archive_among_taxonomies']) {
				$return_template['template_archive_among_taxonomies'] = get_post_meta($template->ID, 'ct_template_archive_among_taxonomies', true);

				foreach($return_template['template_archive_among_taxonomies'] as $key => $val) {
					
					// get slug for the id
					if(is_numeric($val)) {

						$term = get_term($val);
						
						if($term) {
							$return_template['template_archive_among_taxonomies'][$key] = array('taxonomy' => $term->taxonomy, 'slug' => $term->slug);
						}

					}

				}
			}
			
			$return_template['template_apply_if_archive_among_cpt'] = get_post_meta($template->ID, 'ct_template_apply_if_archive_among_cpt', true);
			$return_template['template_archive_post_types'] = get_post_meta($template->ID, 'ct_template_archive_post_types', true);
			// $return_template['template_apply_if_archive_among_authors'] = get_post_meta($template->ID, 'ct_template_apply_if_archive_among_authors', true);
			// $return_template['template_authors_archives'] = get_post_meta($template->ID, 'ct_template_authors_archives', true);
			$return_template['template_date_archive'] = get_post_meta($template->ID, 'ct_template_date_archive', true);
			$return_template['template_front_page'] = get_post_meta($template->ID, 'ct_template_front_page', true);
			$return_template['template_blog_posts'] = get_post_meta($template->ID, 'ct_template_blog_posts', true);
			$return_template['template_search_page'] = get_post_meta($template->ID, 'ct_template_search_page', true);
			$return_template['template_404_page'] = get_post_meta($template->ID, 'ct_template_404_page', true);
			$return_template['template_index'] = get_post_meta($template->ID, 'ct_template_index', true);

			$return_template['ct_template_inner_content'] = get_post_meta($template->ID, 'ct_template_inner_content', true);

			$return_templates[] = $return_template;
		}


		return $return_templates;


	}


	function oxygen_vsb_connection_stylesheets($request) {
		$stylesheets = get_option('ct_style_sheets');
		return $stylesheets;
	}


	function oxygen_vsb_connection_stylesets($request) {
		$stylesets = get_option('ct_style_sets');
		return $stylesets;
	}



	function oxygen_vsb_connection_settings($request) {

		$settings = get_option('ct_global_settings');

		$headers = $request->get_headers();
		$supportsVariableColors = isset($headers['oxygenclientversion']);

		if(function_exists('oxygen_vsb_get_global_color_value') && !$supportsVariableColors) { // check for variable colors and replace with color values 
			$settings = $this->oxygen_vsb_connection_replace_global_colors($settings);
		}

		return $settings;
	}



	function oxygen_vsb_connection_classes($request) {

		$classes = get_option('ct_components_classes');
		$headers = $request->get_headers();
		$supportsVariableColors = isset($headers['oxygenclientversion']);

		if(function_exists('oxygen_vsb_get_global_color_value') && !$supportsVariableColors) { // check for variable colors and replace with color values 
			$classes = $this->oxygen_vsb_connection_replace_global_colors($classes);
		}
		return $classes;
	}


	function oxygen_vsb_connection_colors($request) {

		$colors = get_option('oxygen_vsb_global_colors');
		$strippedColors = array();

		if(is_array($colors)) {
			foreach($colors['colors'] as $key => $color) {
				// strip off all the unnecessary stuff
				$strippedColors[] = array(
					'id' => $color['id'],
					'name' => $color['name'],
					'value' => $color['value']
				);
			}
		}

		$result =  array(
				'colors' => $strippedColors
			);


		// if color lookup table is available, return that as well

		$oxygen_vsb_color_lookup_table = get_option('oxygen_vsb_color_lookup_table', false);

		if(is_array($oxygen_vsb_color_lookup_table)) {
			$result['lookuptable'] = $oxygen_vsb_color_lookup_table;
		}

		return $result;

	}


	function oxygen_vsb_connection_selectors($request) {

		$selectors = get_option('ct_custom_selectors');

		$headers = $request->get_headers();
		$supportsVariableColors = isset($headers['oxygenclientversion']);

		if(function_exists('oxygen_vsb_get_global_color_value') && !$supportsVariableColors) { // check for variable colors and replace with color values 
			$selectors = $this->oxygen_vsb_connection_replace_global_colors($selectors);
		}

		return $selectors;
	}

	function oxygen_vsb_recursively_replace_reusable($children) {

		foreach($children as $key => $item) {

			if($item['name'] == 'ct_reusable') {
				$children[$key]['shortcodes'] = get_post_meta($item['options']['view_id'], 'ct_builder_shortcodes', true);
			}
			if(isset($item['options']['view_id'])) {
				$post = get_post($item['options']['view_id']);

				$children[$key]['post_title'] = $post->post_title;
				$children[$key]['menu_order'] = $post->menu_order;

				if($item['children']) {
					$children[$key]['children'] = $this->oxygen_vsb_recursively_replace_reusable($item['children']);
				}
			}

		}

		return $children;
	}

	function oxygen_vsb_connection_component_classes($request) {

		$headers = $request->get_headers();
		$supportsVariableColors = isset($headers['oxygenclientversion']);

		$params = $request->get_params();

		$id = intval($params['id']);
		$page = intval($params['page']);
		
		$json = get_post_meta( $page, "ct_builder_json", true );

		if ($json) {
			$json = json_decode( $json, true );
			$tree = array();
			$tree['content'] = $json['children'];
		}
		else {
			$shortcodes = get_post_meta($page, 'ct_builder_shortcodes', true);
			$tree = parse_shortcodes($shortcodes, false);
		}
		
		$globalColors = get_option('oxygen_vsb_global_colors', array());

		if(intval($id) === 0) {
			$post = get_post(intval($page));
			$item = array(
				'name' => 'ct_div_block',
				'id' => 0,
				'options' => array('nicename' => $post->post_title),
				'children' => $tree['content'],
			);

			return $this->oxygen_vsb_process_component($item, $supportsVariableColors, $globalColors);
		}
		else {

			foreach($tree['content'] as $key => $item) {
				
				if($item['id'] == $id) {

					return $this->oxygen_vsb_process_component($item, $supportsVariableColors, $globalColors);
				}
			}
		}

		return false;

	}


	function oxygen_vsb_process_component($item, $supportsVariableColors, $globalColors) {
		// recursively go through the component and replace any re-usable with the corresponding content
		if($item['children']) {
			$item['children'] = $this->oxygen_vsb_recursively_replace_reusable($item['children']);
		}

		$appliedClasses = $this->oxygen_vsb_connection_applied_classes(array('content' => array($item)));

		$appliedGlobalColors = array();

		if($supportsVariableColors) { // if client supports variable colors
			$appliedGlobalColors = $this->oxygen_vsb_connection_extract_global_colors(array($item), $globalColors);	
		} else if(function_exists('oxygen_vsb_get_global_color_value')) { // if contains variable colors, but client does not support
			$item = $this->oxygen_vsb_connection_replace_global_colors(array($item));
			$item = $item[0];
		}
		
		$allClasses = get_option( "ct_components_classes" );
		
		$appliedClasses = array_intersect_key($allClasses, $appliedClasses);

		// find applied global colors in the applied classes
		if($supportsVariableColors) { // if client supports variable colors
			$appliedGlobalColors = $this->oxygen_vsb_connection_extract_global_colors($appliedClasses, $globalColors) + $appliedGlobalColors;
		} else if(function_exists('oxygen_vsb_get_global_color_value')) { // if contains variable colors, but client does not support
			$appliedClasses = $this->oxygen_vsb_connection_replace_global_colors($appliedClasses);
		}

		$result =  array('component' => $item,
			'classes' => $appliedClasses,
			'colors' => $appliedGlobalColors);


		$oxygen_vsb_color_lookup_table = get_option('oxygen_vsb_color_lookup_table', false);
		if(is_array($oxygen_vsb_color_lookup_table)) {
			$result['lookuptable'] = $oxygen_vsb_color_lookup_table;
		}

		return $result;
	}


	function oxygen_vsb_connection_extract_classes($children) {

		$classes = array();

		foreach($children as $child) {

			if(isset($child['options']['classes'])) {
				foreach($child['options']['classes'] as $item) {
					if(is_string($item)) {
						$classes[$item] = false;
					}
				}
			}

			if(isset($child['children'])) {
				$classes = array_merge($classes, $this->oxygen_vsb_connection_extract_classes($child['children']));
			}
		}

		return $classes;
	}

	function oxygen_vsb_connection_replace_global_colors($settings) {
	   
		foreach($settings as $key => $item) {
			if(is_string($item)) {
				$settings[$key] = preg_replace_callback('/color\((\d*)\)/', array($this, 'oxygen_vsb_connection_global_color_match'), $item); // replaced value
			}
			else if(is_array($item)) {
				$settings[$key] = $this->oxygen_vsb_connection_replace_global_colors($settings[$key]);
			}
		}

		return $settings;

	}

	function oxygen_vsb_connection_extract_global_colors($children, $globalColors) {

		$colors = array();

		foreach($children as $key => $item) {

			if(is_string($item)) {
				$matches = array();

				$children[$key] = preg_match_all('/color\((\d*)\)/', $item, $matches); 
				
				foreach($matches[1] as $match) {
					if(isset($globalColors['colors'])) {
						foreach($globalColors['colors'] as $gColor) {
							if($gColor['id'] == intval($match)) {
								$colors[intval($match)] = array(
									'id' => $gColor['id'],
									'name' => $gColor['name'],
									'value' => $gColor['value']
								);
								break;
							}
						}	
					}
					
				}
			}
			else if(is_array($children[$key])) {
				$colors = $this->oxygen_vsb_connection_extract_global_colors($children[$key], $globalColors) + $colors;
			}
		}
		
		return $colors;
	}

	function oxygen_vsb_connection_applied_classes($shortcodes) {
		
		//$shortcodes = parse_shortcodes($shortcodes);
		$classes = array();
		if(isset($shortcodes['content'])) {
			$classes = $this->oxygen_vsb_connection_extract_classes($shortcodes['content']);
		}
		return $classes;
	}

	function oxygen_vsb_connection_page_classes($request) {

		$headers = $request->get_headers();
		$supportsVariableColors = isset($headers['oxygenclientversion']);

		$params = $request->get_params();

		$page = intval($params['id']);
		
		$json = get_post_meta( $page, "ct_builder_json", true );

		if ($json) {
			$json = json_decode( $json, true );
			$tree = array();
			$tree['content'] = $json['children'];
		}
		else {
			$shortcodes = get_post_meta($page, 'ct_builder_shortcodes', true);
			$tree = parse_shortcodes($shortcodes, false);
		}

		$globalColors = get_option('oxygen_vsb_global_colors', array());

		foreach($tree['content'] as $key => $item) {

			// recursively go through the component and replace any re-usable with the corresponding content
			if(isset($tree['content'][$key]['children'])) {
				$tree['content'][$key]['children'] = $this->oxygen_vsb_recursively_replace_reusable($item['children']);
			}

		}

		$appliedClasses = $this->oxygen_vsb_connection_applied_classes($tree);

		$appliedGlobalColors = array();

		if($supportsVariableColors) { // if client supports variable colors
			$appliedGlobalColors = $this->oxygen_vsb_connection_extract_global_colors($tree, $globalColors);
		} else if(function_exists('oxygen_vsb_get_global_color_value')) { // if contains variable colors, but client does not support
			$tree = $this->oxygen_vsb_connection_replace_global_colors($tree);
		}

		$allClasses = get_option( "ct_components_classes" );

		$appliedClasses = array_intersect_key($allClasses, $appliedClasses);

		// find applied global colors in the applied classes
		if($supportsVariableColors) { // if client supports variable colors
			$appliedGlobalColors = $this->oxygen_vsb_connection_extract_global_colors($appliedClasses, $globalColors) + $appliedGlobalColors;
		} else if(function_exists('oxygen_vsb_get_global_color_value')) { // if contains variable colors, but client does not support
			$appliedClasses = $this->oxygen_vsb_connection_replace_global_colors($appliedClasses);
		}

		$result =  array(
			'components' => $tree['content'],
			'classes' => $appliedClasses,
			'colors' => $appliedGlobalColors)
		;


		$oxygen_vsb_color_lookup_table = get_option('oxygen_vsb_color_lookup_table', false);
		if(is_array($oxygen_vsb_color_lookup_table)) {
			$result['lookuptable'] = $oxygen_vsb_color_lookup_table;
		}

		return $result;

	}

	function oxygen_vsb_connection_component($request) {
		
		$params = $request->get_params();

		$id = intval($params['id']);
		$page = intval($params['page']);
		
		
		$shortcodes = get_post_meta($page, 'ct_builder_shortcodes', true);

		$shortcodes = parse_shortcodes($shortcodes, false);

		foreach($shortcodes['content'] as $key => $item) {
			
			if($item['id'] == $id) {

				// recursively go through the component and replace any re-usable with the corresponding content
				if($item['children']) {
					$item['children'] = $this->oxygen_vsb_recursively_replace_reusable($item['children']);

				}

				return $item;
			}
		}

		return false;

	}


	function oxygen_vsb_connection_items($request) {
		return $this->oxygen_vsb_connection_components($request, true);
	}

	function oxygen_vsb_connection_global_color_match($matches) {
		return oxygen_vsb_get_global_color_value($matches[0]);
	}

	function oxygen_vsb_connection_components($request, $getPages = false) {

		/* generate components on the fly from all the pages on the site */

		$pages = get_posts(array("post_type" => array("page", "ct_template", "oxy_user_library"), 'numberposts' => -1));

		$returnPages = array();

		$components = array();

		$globalColors = get_option('oxygen_vsb_global_colors', array());

		foreach ($pages as $page) {

			$ct_preview_url = false;

			if($page->post_type === 'ct_template') {
				$ct_preview_url = get_post_meta($page->ID, 'ct_preview_url', true);
			}

			$screenshots = get_post_meta($page->ID, 'oxygen_vsb_components_screenshots', true);
			
			$json = get_post_meta( $page->ID, "ct_builder_json", true );

			if ($json) {
				$json = json_decode( $json, true );
				$tree = array();
				$tree['content'] = $json['children'];
				// to keep some old logic to work
				$shortcodes = "shortcodes";
			}
			else {
				$shortcodes = get_post_meta($page->ID, 'ct_builder_shortcodes', true);
				$tree = parse_shortcodes($shortcodes, false);
			}

			if( 
				$getPages

				// if it is set to be included as a whole page in the library
				&& ($page->post_type != 'oxy_user_library' && get_post_meta($page->ID, '_ct_connection_use_page', true))
				
				// && avoid reusable parts
				&& 'reusable_part' !== get_post_meta($page->ID, 'ct_template_type', true)) {

				$providedScreenshot = $page->post_type == 'ct_template' ? get_post_meta($page->ID, '_ct_connection_page_screenshot', true) : false;

				$returnPage = array(
					'id' => $page->ID,
					'name' => $page->post_title,
					'source' => get_site_url(),
					'url' => $ct_preview_url?$ct_preview_url:get_permalink($page->ID),
					'type' => $page->post_type,
					'screenshot_url' => $providedScreenshot?$providedScreenshot:((is_array($screenshots) && isset($screenshots['page']))?$screenshots['page']:'http://via.placeholder.com/600x100?text=no+screenshot')
				);

				$returnPage['custom_screenshot'] = get_post_meta($page->ID, 'oxy_custom_screenshot', true);

				$page_category = get_post_meta($page->ID, '_ct_connection_page_category', true);
				
				if($page_category) {
					$returnPage['category'] = $page_category;
				}
				else {
					$returnPage['category'] = 'Uncategorized';
				}


				if(!$shortcodes || empty($shortcodes)) {
					continue;
				}

				//if($page_category) {
					$returnPages[] = $returnPage;
				//}
				
			}
			
			// if it is not set to have its sections included in the library
			if(!get_post_meta($page->ID, '_ct_connection_use_sections', true) && $page->post_type != 'oxy_user_library') {
				continue;
			}


			if(is_array($tree['content']) && sizeof($tree['content']) > 0) {

				if($page->post_type == 'oxy_user_library' && sizeof($tree['content']) > 1) {
					$newItem = array(
						'name' => 'ct_div_block',
						'id' => 0,
						'options' => array()
					);
					$comp = $this->oxygen_vsb_get_component_ready(0, $newItem, $page, $screenshots, $ct_preview_url);
					if($comp)
						$components[] =  $comp;

				} else {

					foreach($tree['content'] as $key => $item) {
						if($item['name'] == 'ct_section' || $item['name'] == 'oxy_header' || $item['name'] == 'ct_div_block') {
							$comp = $this->oxygen_vsb_get_component_ready($key, $item, $page, $screenshots, $ct_preview_url);
							if($comp)
								$components[] =  $comp;
						}
					}
				}

			}


		}

		if($getPages) {
			return array(
				'components' => $components,
				'pages'	=> $returnPages
			);
		}
		
		return $components;
		
	}

	function oxygen_vsb_get_component_ready($key, $item, $page, $screenshots, $ct_preview_url) {
		$section = array();
		$nicename = '';

		if(isset($item['options']['nicename'])) {
			$nicename = $item['options']['nicename'];
		}
		else { // derive a nicename
			if($page->post_type == 'oxy_user_library') {
				$nicename = $page->post_title;
			}
			else {
				$nicename = 'Component '.$key;
			}
		}

		$section['id'] = $item['id'];
		$section['name'] = $nicename;

		$preview_query_args = array(
		    'render_component_screenshot' => 'true'
		);

		if($page->post_type == 'oxy_user_library') {
			$page_category = get_post_meta($page->ID, '_ct_connection_page_category', true);

			if($page_category) {
				$section['category']  = $page_category;
			}
			else {
				return false; // uncategorized wont be included
				$section['category']  = 'Uncategorized';
			}

		} else {

			if(isset($item['options']['ct_category'])) {
				$section['category'] = $item['options']['ct_category'];
			}
			else {
				return false; // uncategorized wont be included
				$section['category'] = 'Uncategorized';
			}

			$preview_query_args['selector'] = $item['options']['selector'];
		}
		
		$section['source'] = get_site_url();


		


		$url = add_query_arg($preview_query_args, ($ct_preview_url && !empty($ct_preview_url))?$ct_preview_url:get_permalink($page->ID));
		
		if($ct_preview_url && !empty($ct_preview_url)) {
			$url = add_query_arg(array(
				'screenshot_template' => $page->ID
			), $url);
		}


		$section['url'] = $url;
		if($page->post_type == 'oxy_user_library') {
			$section['screenshot_url'] = (is_array($screenshots) && isset($screenshots['page']))?$screenshots['page']:'http://via.placeholder.com/600x100?text=no+screenshot';
			$section['custom_screenshot'] = get_post_meta($page->ID, 'oxy_custom_screenshot', true);
		}
		else {
			$section['screenshot_url'] = (is_array($screenshots) && isset($screenshots[$item['id']]))?$screenshots[$item['id']]:'http://via.placeholder.com/600x100?text=no+screenshot';
			if(isset($item['options']['screenshot_url'])) {
				$section['custom_screenshot'] = $item['options']['screenshot_url'];
			}
		}

		$section['page'] = $page->ID;

		return $section;
	}


	function oxygen_vsb_connection_page($request) {

		$params = $request->get_params();

		$page = intval($params['id']);
		
		$shortcodes = get_post_meta($page, 'ct_builder_shortcodes', true);

		$shortcodes = parse_shortcodes($shortcodes, false);

		foreach($shortcodes['content'] as $key => $item) {

			// recursively go through the component and replace any re-usable with the corresponding content
			if($shortcodes['content'][$key]['children']) {
				$shortcodes['content'][$key]['children'] = $this->oxygen_vsb_recursively_replace_reusable($item['children']);
			}

		}

		return $shortcodes['content'];

	}


	function oxygen_vsb_connection_pages($request) {

		$headers = $request->get_headers();
		$supportsVariableColors = isset($headers['oxygenclientversion']);

		$pages = get_posts(array("post_type" => "page", 'numberposts' => -1));
		$return_pages = array();

		foreach ($pages as $page) {
			
			$use_page_default = get_post_meta($page->ID, '_ct_connection_use_default', true);
			
			if(!$use_page_default) {
				continue;
			}

			$return_page = json_decode(json_encode($page), true);

			$return_page['builder_shortcodes'] = get_post_meta($page->ID, 'ct_builder_shortcodes', true);

			$shortcodes = parse_shortcodes($return_page['builder_shortcodes'], false);

			$return_page['applied_classes'] = $this->oxygen_vsb_connection_applied_classes($shortcodes);

			if(function_exists('oxygen_vsb_get_global_color_value') && !$supportsVariableColors) { // check for variable colors and replace with color values 
				$return_page['builder_shortcodes'] = preg_replace_callback('/color\((\d*)\)/', array($this, 'oxygen_vsb_connection_global_color_match'), $return_page['builder_shortcodes']);
			}

			$return_page['other_template'] = get_post_meta($page->ID, 'ct_other_template', true);

			$return_pages[] = $return_page;

		}


		return $return_pages;

	}

	function oxygen_connection_resize_screenshot($result) {

		$image = wp_get_image_editor($result['file']);
		
		if ( ! is_wp_error( $image ) ) {
			
			$size = $image->get_size();

		    $image->resize( 520, 520 * $size['height'] / $size['width'], false );

		    $image->resize( 520, 800, array('center', 'top'));

		    $oName = strrchr($result['file'], '/');

		    $oName = ltrim($oName, '/');

		    $name = 'resized-'.$oName;

		    $path = str_replace($oName, $name, $result['file']);
		    
			$image->save( $path ); // a new smaller version is saved.

			$result['url'] = str_replace($oName, $name, $result['url']);

		}

		return $result;
	}

	function oxygen_connection_screenshot() {

		if ( !wp_verify_nonce( $_POST['ct_connection_generate_screenshot_nonce'], 'ct_connection_generate_screenshot' )) {
		    die();
		}


		$apiurl = 'https://xejdaxc4wb.execute-api.us-east-2.amazonaws.com/prod/';

		$scapisecret = $this->getSCAPIToken();

		ob_start();
		$something_failed = false;
		
		$renderURL = isset($_REQUEST['renderURL'])?esc_url($_REQUEST['renderURL']):false;

		$postId = isset($_REQUEST['postId'])?intval($_REQUEST['postId']): false;

		if(!$postId) {
			// generate the home page screenshot and return the url

			$url = add_query_arg(array(
			    'render_component_screenshot' => 'true'
			), $renderURL?$renderURL:get_home_url());

			$success = false;
			$count = 0;
			while(!$success && $count < 5 ) {
				$count++;
				$get = wp_remote_get( $apiurl.'?url='.urlencode($url), array('timeout' => 15, 'headers' => array( 'Auth' => $scapisecret)) );

				$response_code = wp_remote_retrieve_response_code($get);
				if( $response_code === 401) {
					$scapisecret = $this->getSCAPIToken(true);
					continue;
				}

				$type = wp_remote_retrieve_header( $get, 'content-type' );

				$size = wp_remote_retrieve_header($get, 'content-length');

				$isImage = ($type == 'image/png');

				$success = $isImage && is_numeric($size) && intval($size) > 1200;

				if ($success) {

					$result = wp_upload_bits( 'site_screenshot.png', '', wp_remote_retrieve_body( $get ) );

					$result = $this->oxygen_connection_resize_screenshot($result);

					header('Content-Type: application/json');

					echo json_encode( array('url' => $result['url'] ) );
				}
			}
			

			die();
		}
		
		$post = get_post($postId);

		$ct_preview_url = false;

		if($post->post_type === 'ct_template') {
			$ct_preview_url = get_post_meta($postId, 'ct_preview_url', true);
		}

		$shortcodes = get_post_meta($postId, 'ct_builder_shortcodes', true);
		$shortcodes = parse_shortcodes($shortcodes);

		if(is_array($shortcodes['content']) && $post->post_type !== 'oxy_user_library') {
			echo "\n Component Screenshots \n";
			$componentIndex = isset($_REQUEST['componentIndex'])?intval($_REQUEST['componentIndex']): 0;

			if($componentIndex === 0) {
				$screenshots = array();
			}
			else {
				$screenshots = get_post_meta($postId, 'oxygen_vsb_components_screenshots', true);
			}

			$loopIndex = 0;
			foreach($shortcodes['content'] as $key => $item) {

				if($item['name'] == 'ct_section' || $item['name'] == 'oxy_header' || $item['name'] == 'ct_div_block') {
					
					if($loopIndex !== $componentIndex) {
						$loopIndex++;
						continue;
					}
					

					$url = add_query_arg(array(
					    'render_component_screenshot' => 'true',
					    'selector' => $item['options']['selector']
					), ($ct_preview_url && !empty($ct_preview_url))?$ct_preview_url:get_permalink($postId));
					
					if($ct_preview_url && !empty($ct_preview_url)) {
						$url = add_query_arg(array(
							'screenshot_template' => $post->ID
						), $url);
					}
					
					$success = false;
					$count = 0;
					while(!$success && $count < 5 ) {
						$count++;
						
						$get = wp_remote_get( $apiurl.'?url='.urlencode($url), array('timeout' => 15, 'headers' => array( 'Auth' => $scapisecret)) );
						
						$response_code = wp_remote_retrieve_response_code($get);
						
						if( $response_code === 401) {
							$scapisecret = $this->getSCAPIToken(true);
							continue;
						}

						$type = wp_remote_retrieve_header( $get, 'content-type' );
						$size = wp_remote_retrieve_header($get, 'content-length');

						$isImage = ($type == 'image/png');

						$isOfSize = is_numeric($size) && intval($size) > 1200;

						if($isImage && !$isOfSize) {
							
							// could it be, because the component was empty?
							// is it empty?
							if((!isset($item['options']) || !isset($item['options']['ct_content']) || trim($item['options']['ct_content']) == '') && (!isset($item['children']) || !is_array($item['children']) || sizeof($item['children']) < 1)) {
								
								// yes empty (and the image is blank, not because of any glitch with the screenshot API), give it a free pass
								$isOfSize = true;
							}
						}

						$success = $isImage && $isOfSize;
					
						if ($success) {
							$size = wp_remote_retrieve_header($get, 'content-length');
							
							$result = wp_upload_bits( 'component-screenshot-'.$postId.'-'.$item['id'].'.png', '', wp_remote_retrieve_body( $get ) );

							$result = $this->oxygen_connection_resize_screenshot($result);

							$screenshots[$item['id']] = $result['url'];
							echo $result['url']."\n";
						}
					}
					
					if(!$success && !$something_failed) {
						$debug_output = ob_get_clean();
						$something_failed = true;
						$componentRepeat = isset($_REQUEST['componentRepeat'])?intval($_REQUEST['componentRepeat']): 0;

						if($componentRepeat > 3) {
							$results['error'] = true;
						}
						else {
							$results = array('componentIndex' => $componentIndex, 'componentRepeat' => $componentRepeat + 1);
						}

						@header( 'Content-type: application/json' );
						echo json_encode( $results );
						die();
					}
					else {
						update_post_meta($postId, 'oxygen_vsb_components_screenshots', $screenshots);
						$debug_output = ob_get_clean();
						$results = array('componentIndex' => $componentIndex+1, 'componentShot' => $result['url']);

						@header( 'Content-type: application/json' );
						echo json_encode( $results );
						die();
					}

					$loopIndex++;
				}
				
			}
			
		}
		



			// screenshot for the whole page
			if($renderURL) {
				$ct_preview_url = $renderURL;
			}

			$url = add_query_arg(array(
				'render_component_screenshot' => 'true'
			), ($ct_preview_url && !empty($ct_preview_url))?$ct_preview_url:get_permalink($postId));

			if($ct_preview_url && !empty($ct_preview_url)) {
				$url = add_query_arg(array(
					'screenshot_template' => $post->ID
				), $url);
			}

			$success = false;
			$count = 0;

			while(!$success && $count < 5 ) {

				$count++;

				$get = wp_remote_get( $apiurl.'?url='.urlencode($url), array('timeout' => 15, 'headers' => array( 'Auth' => $scapisecret)) );

				$response_code = wp_remote_retrieve_response_code($get);
				if( $response_code === 401) {
					$scapisecret = $this->getSCAPIToken(true);
					continue;
				}

				$type = wp_remote_retrieve_header( $get, 'content-type' );

				$size = wp_remote_retrieve_header($get, 'content-length');

				$isImage = ($type == 'image/png');

				$success = $isImage && is_numeric($size) && intval($size) > 1200;

				if ($success) {
					$result = wp_upload_bits( 'page-screenshot-'.$postId.'.png', '', wp_remote_retrieve_body( $get ) );

					$result = $this->oxygen_connection_resize_screenshot($result);


					$screenshots['page'] = $result['url'];

					// this also needs to be assigned to the database in case its a template
					$post_type = get_post_type($postId);

					if($post_type == 'ct_template') {
						update_post_meta($postId, '_ct_connection_page_screenshot', $screenshots['page']);
					}
				}
			}

			if(!$success && !$something_failed) {
				$something_failed = true;
			}



		echo "\n Final Screenshots \n";
		

		update_post_meta($postId, 'oxygen_vsb_components_screenshots', $screenshots);
		update_post_meta($postId, '_ct_connection_screenshots_generated', date("Y-m-d H:i:s") );
		
		$debug_output = ob_get_clean();
		
		$results = array(
			'screenshots' => $screenshots
		);
		
		if($something_failed) {
			$results['error'] = true;
		}
		
		@header( 'Content-type: application/json' );
		echo json_encode( $results );
		
		die();
	}





function remoteGetSCAPI($body) {

	$url = 'https://xejdaxc4wb.execute-api.us-east-2.amazonaws.com/prod/auth';

	$response = wp_remote_post( $url, array(
		'method' => 'POST',
		'timeout' => 45,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking' => true,
		'headers' => array('Content-Type' => 'application/json' ),
		'body' => json_encode($body),
		'cookies' => array()
	    )
	);

	if ( is_wp_error( $response ) ) {
		$error_message = $response->get_error_message();
		return array(
			true,
			$error_message
		);
	   
	} else {
		$body = wp_remote_retrieve_body($response);

	  	return array(
	   		null,
	   		json_decode($body, true)
	   	);
	}

}



function storeSCAPITokensToDB($data, $refresh = false) {

		$tokens = get_option('oxy_vsb_scapi_tokens', array());

		if(!is_array($tokens)) {
			$tokens = array(); // in case of corrupt data
		}

		if(isset($data['AuthenticationResult'])) {
			
			$tokens['AccessToken'] = $data['AuthenticationResult']['AccessToken'];
			$tokens['IdToken'] =  $data['AuthenticationResult']['IdToken'];
			$tokens['timestamp'] = time();

			if($refresh) {
				$tokens['RefreshToken'] = $data['AuthenticationResult']['RefreshToken'];
				$tokens['refreshTimestamp'] = time();
			}
			
		}

		
		update_option('oxy_vsb_scapi_tokens', $tokens);

	}

	function oxy_vsb_scapi_challenge_return() {

		if(isset($_REQUEST['scapichallenge'])) {

			$scapiChallenge = sanitize_text_field($_REQUEST['scapichallenge']);

			$uniq = get_transient('oxy_vsb_scapi_challenge');

			header("Content-Type:application/json");
			
			$result = array();
			if(hash_equals($uniq, $scapiChallenge)) {
				$result['success'] = true;
			} else {
				$result['success'] = false;
			}
			echo json_encode($result);
			
			exit();
		}
	}

	function getSCAPIToken($forceRefresh = false) {

		$tokens = get_option('oxy_vsb_scapi_tokens', false);
		$license = get_option('oxygen_license_key', false);
		$site = get_site_url();

		$errors = array();

		if(is_array($tokens)) {
			$thatTime = $tokens['timestamp'];
			$thatRefreshTime = $tokens['refreshTimestamp'];
			$timediff = time()-$thatTime;
			$refreshTimediff = time()-$thatRefreshTime;
			
			if(!$forceRefresh && $timediff < 3500) { // 3500 is about to be an hour
				// echo "got existing token";
				return $tokens['IdToken'];

			} elseif($refreshTimediff < 2500000) { //2500000 is about to be 30 days
				//send refresh token to the register endpoint and receive access tokens

				$refreshToken = $tokens['RefreshToken'];

				$response = $this->remoteGetSCAPI(array(
					'action' => 'refreshTokens',
					'RefreshToken' => $refreshToken
				));

				if(!$response[0]) {
					// echo "got refreshed token";
					
					if(isset($response[1]['AuthenticationResult']) && isset($response[1]['AuthenticationResult']['IdToken'])) {

						$this->storeSCAPITokensToDB($response[1]);
						return $response[1]['AuthenticationResult']['IdToken'];
					} elseif(isset($response[1]['errorMessage'])) {
						$errors[] = $response[1]['errorMessage'];
					}
				}

			} 

			// if timediff is greater than 30 days or refresh attempt failed
			
			//send md5 of the license key to the register endpoint and receive access tokens
			
			if($license) {
				$response = $this->remoteGetSCAPI(array(
					'action' => 'reAuthenticate',
					'license' => md5($license),
					'site' => $site
				));
				
				if(!$response[0]) {
					// echo "got reauthenticated token";
					
					if(isset($response[1]['AuthenticationResult']) && isset($response[1]['AuthenticationResult']['IdToken'])) {

						$this->storeSCAPITokensToDB($response[1], true);
						return $response[1]['AuthenticationResult']['IdToken'];
					} elseif(isset($response[1]['errorMessage'])) {
						$errors[] = $response[1]['errorMessage'];
					}

				}
			}
			
		} 

		// if reached so far, that means that we do not already have any tokens stored, so register
		if($license) {
			// set up a transient response that the online API will access in order to determine that the site is available online
			$scapiChallenge = uniqid();
			set_transient('oxy_vsb_scapi_challenge', $scapiChallenge);


			//send license key, scapiChallenge, and site url (it cannot be a localhost or 127.0.0.1) to the register endpoint and receive access tokens

			$response = $this->remoteGetSCAPI(array(
				'action' => 'generateAccess',
				'license' => $license,
				'scapiChallenge' => $scapiChallenge,
				'site' => $site
			));
			
			if(!$response[0]) {
				// echo "got new authentication token";
				if(isset($response[1]['AuthenticationResult']) && isset($response[1]['AuthenticationResult']['IdToken'])) {

					$this->storeSCAPITokensToDB($response[1], true);
					return $response[1]['AuthenticationResult']['IdToken'];
				}  elseif(isset($response[1]['errorMessage'])) {
					$errors[] = $response[1]['errorMessage'];
				}

			}

		}
	
		if(sizeof($errors) < 1) {
			$errors[] = 'A valid Oxygen license is required';
		}


		@header( 'Content-type: application/json' );
		echo json_encode( array(
			'error' => true,
			'errorMessages' => $errors,
		) );
		die();


	}



	/*Screeshot API authentication function*/

	

	// function scapitest($template) {

	// 	if(isset($_REQUEST['scapitest'])) {

	// 		$token = $this->getSCAPIToken();

	// 		if(is_array($token)) { // error messages
	// 			print_r($token);
	// 		}
	// 		else {
	// 			echo $token;
	// 		}

	// 		exit();
	// 	}
	// 	return $template;
	// }

}
$oxy_vsb_connection = new OXY_VSB_Connection();

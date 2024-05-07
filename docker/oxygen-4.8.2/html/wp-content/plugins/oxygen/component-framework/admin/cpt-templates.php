<?php

/**
 * Register "Templates" Custom Post Type
 * 
 * @since 0.2.0
 */

add_action( 'init', 'ct_add_templates_cpt' );

function ct_add_templates_cpt() {

	if(!oxygen_vsb_current_user_can_access()) {
		return;
	}

	$labels = array(
		'name'               => _x( 'Templates', 'post type general name', 'component-theme' ),
		'singular_name'      => _x( 'Template', 'post type singular name', 'component-theme' ),
		'menu_name'          => _x( 'Templates', 'admin menu', 'component-theme' ),
		'name_admin_bar'     => _x( 'Template', 'add new on admin bar', 'component-theme' ),
		'add_new'            => _x( 'Add New Template', 'template', 'component-theme' ),
		'add_new_item'       => __( 'Add New Template', 'component-theme' ),
		'new_item'           => __( 'New Template', 'component-theme' ),
		'edit_item'          => __( 'Edit Template', 'component-theme' ),
		'view_item'          => __( 'View Template', 'component-theme' ),
		'all_items'          => __( 'Templates', 'component-theme' ),
		'search_items'       => __( 'Search Templates', 'component-theme' ),
		'parent_item_colon'  => __( 'Parent Templates:', 'component-theme' ),
		'not_found'          => __( 'No templates found.', 'component-theme' ),
		'not_found_in_trash' => __( 'No templates found in Trash.', 'component-theme' )
	);

	$args = array(
		'exclude_from_search' => true, 
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'has_archive'		 => true,
		'show_ui'            => true,
		'show_in_menu'       => 'ct_template',
		'rewrite' 			 => false,
		'query_var'          => true,
		'capability_type'    => 'post',
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title' )
	);

	register_post_type( 'ct_template', $args );

	// flush rewrite rules if needed
	$flag = get_option("oxygen_rewrite_rules_updated");
	if ($flag !== "1") {
		flush_rewrite_rules();
		update_option("oxygen_rewrite_rules_updated", "1");
	}
}


/**
 * Remove all unnecessary UI elements on Template edit page
 * 
 * @since 0.2.0
 */

add_filter( 'get_sample_permalink_html', 	'ct_template_remove_permalink' );
//add_filter( 'pre_get_shortlink', 			'ct_template_remove_shortlink', 10, 2 );

function ct_template_remove_permalink( $return ) {
	global $post;
	if (!$post) return $return;
    return 'ct_template' === get_post_type( $post->ID ) ? '' : $return;
}

function ct_template_remove_shortlink( $false, $post_id ) {
	global $post;
    return 'ct_template' === get_post_type( $post_id ) ? '' : $false;
}


/**
 * Hide 'ct_template' from being viewed on frontend
 * 
 * @since 0.2.0
 */

add_action( 'template_redirect', 'ct_check_templates_post');

function ct_check_templates_post() {
	global $post;

	if(!isset($post) || !isset($post->ID))
		return;

	$post_type = get_post_type( $post->ID );

	if ( $post_type == 'ct_template' && !defined("SHOW_CT_BUILDER") 
		&& ( empty($_REQUEST['xlink']) || stripslashes($_REQUEST['xlink']) != 'css' )
		&& ( empty($_REQUEST['action']) || stripslashes($_REQUEST['action']) != 'ct_exec_code' )
		&& ( empty($_REQUEST['action']) || stripslashes($_REQUEST['action']) != 'ct_render_shortcode' )
		&& ( empty($_REQUEST['action']) || stripslashes($_REQUEST['action']) != 'oxy_render_nav_menu' ) ) {
		wp_redirect( esc_url_raw( get_edit_post_link( $post->ID, "" ) ));
		exit;
	}
}

/**
 * Add custom columns to Views CPT table
 * 
 * @since 0.2.3
 */

function ct_custom_views_columns($columns) {

	// save date and uset value to use later
	$date = $columns['date'];
	unset($columns['date']);

    // add type
    $columns['ct_template_type'] = __( 'Template Type', 'component-theme' );

    // add date back
    $columns['date'] = $date;

    return $columns;
}

function ct_custom_view_column( $column, $post_id ) {
    switch ( $column ) {

        case 'ct_template_type' :
            
            $type = get_post_meta( $post_id , 'ct_template_type' , true );

           	if ( $type == "reusable_part") {
           		_e( 'Re-usable part', 'component-theme' );
           	}

           	else {
           		_e( 'Template', 'component-theme' );
           	}

            break;
    }
}

add_filter( 'manage_ct_template_posts_columns', 'ct_custom_views_columns' );
add_action( 'manage_ct_template_posts_custom_column' , 'ct_custom_view_column', 10, 2 );


/**
 * Add view meta box for all CPTs
 * 
 * @since 0.4.0
 * @author Ilya K.
 */

function ct_oxygen_meta_box() {

	if(!oxygen_vsb_current_user_can_full_access()) {
		return;
	}

	$screen = get_current_screen();
	// check if this post type is set to be ignored
	$ignore = get_option('oxygen_vsb_ignore_post_type_'.$screen->post_type, false);

	if($ignore == "true") {
		return;
	}

	$post_types 	= get_post_types( '', 'objects' ); 
	$exclude_types 	= array( "nav_menu_item", "revision" );

	foreach ( $post_types as $post_type ) {

		if (in_array($post_type->name, $exclude_types)){
			continue;
		}

		global $wp_version;
		
		$num_version = 9999;


		if(is_numeric($wp_version)) {

			$num_version = floatval($wp_version);

		}
		else {

			if(strpos($wp_version, '-')) {
				
				$exploded = explode('-', $wp_version);
				$num_version = $exploded[0];
				
			}
			else {
				$num_version = $wp_version;
			}

			// could be more than one decimals in the string
			$exploded = explode('.', $num_version);

			if(is_numeric($exploded[0])) {
				$num_version = floatval($exploded[0].(isset($exploded[1])?'.'.$exploded[1]:''));
			}
			else {
				$num_version = 9999;
			}
		}

		add_meta_box(
			'ct_views_cpt',
			__( 'Oxygen', 'component-theme' ),
			'ct_view_meta_box_callback',
			$post_type->name,
			($num_version >= 5 ? 'normal' : 'advanced'),
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'ct_oxygen_meta_box' );


function ct_view_taxonomies_selector($field_name, $selected_items, $alloption = false) {
	?>
	<select name="<?php echo esc_attr($field_name); ?>[]" id="<?php echo esc_attr($field_name); ?>" multiple="multiple">
		<option value="<?php echo __( "all_taxonomies" ); ?>"
			<?php if ( in_array( "all_taxonomies", $selected_items ) ) echo 'selected="selected"'; ?>>
			<?php _e( "All Taxonomies", "component-theme" ); ?>
		</option>
		<?php 

		// get default post categories
		$default_categories = get_categories(array('hide_empty' 	=> 0));

		?>
			<optgroup label="<?php echo __('Categories', 'component-theme'); ?>">
				<?php 
				if($alloption) { ?>
				<option value="<?php echo __( "all_categories" ); ?>"
					<?php if ( in_array( "all_categories", $selected_items ) ) echo 'selected="selected"'; ?>>
					<?php _e( "All Categories", "component-theme" ); ?>
				</option>

				<?php
				}

				 foreach ( $default_categories as $category ) : ?>
					<option value="<?php echo (!$alloption?'category,':'').esc_attr( $category->term_id ); ?>" 
						<?php if ( in_array( (!$alloption?'category,':'').$category->term_id, $selected_items ) ) echo 'selected="selected"'; ?>>
						<?php echo sanitize_text_field( $category->name ); ?>
					</option>
				<?php endforeach; ?>

			</optgroup>

		<?php
		// get default post tags
		$default_tags = get_tags(array('hide_empty' 	=> 0));

		?>
			<optgroup label="<?php echo __('Tags', 'component-theme'); ?>">
				<?php 
				if($alloption) { ?>
				<option value="<?php echo __( "all_tags" ); ?>"
					<?php if ( in_array( "all_tags", $selected_items ) ) echo 'selected="selected"'; ?>>
					<?php _e( "All Tags", "component-theme" ); ?>
				</option>

				<?php
				} 

				foreach ( $default_tags as $tag ) : ?>
					<option value="<?php echo (!$alloption?'tag,':'').esc_attr( $tag->term_id ); ?>" 
						<?php if ( in_array( (!$alloption?'tag,':'').$tag->term_id, $selected_items ) ) echo 'selected="selected"'; ?>>
						<?php echo sanitize_text_field( $tag->name ); ?>
					</option>
				<?php endforeach; ?>

			</optgroup>

		<?php

		// get custom taxonomies
		$args = array(
				"_builtin" => false
			);

		$taxonomies = get_taxonomies( $args, 'object' );

		foreach ( $taxonomies as $taxonomy ) : 

			$args = array(
				'hide_empty' 	=> 0,
				'taxonomy' 		=> $taxonomy->name,
			);

			$categories = get_categories( $args );

			if ( !isset($selected_items[$taxonomy->name]) || !$selected_items[$taxonomy->name] ) {
				$selected_items[$taxonomy->name] = array();
			}

			?>

			<optgroup label="<?php echo sanitize_text_field( $taxonomy->labels->name . " (" . $taxonomy->name . ")" ); ?>">
				<?php 
				if($alloption) { ?>
				<option value="<?php echo esc_attr( "all_".$taxonomy->name ); ?>"
					<?php if ( in_array( "all_".$taxonomy->name, $selected_items ) ) echo 'selected="selected"'; ?>>
					<?php _e( "All ", "component-theme" ); echo sanitize_text_field( $taxonomy->labels->name ); ?>
				</option>

				<?php
				}

				foreach ( $categories as $category ) : ?>
					<option value="<?php echo (!$alloption?$category->taxonomy.',':'').esc_attr( $category->term_id ); ?>" 
						<?php if ( in_array( (!$alloption?$category->taxonomy.',':'').$category->term_id, $selected_items ) ) echo 'selected="selected"'; ?>>
						<?php echo sanitize_text_field( $category->name ); ?>
					</option>
				<?php endforeach; ?>

			</optgroup>
		
		<?php endforeach; ?>

	</select>
	<script type="text/javascript">
		jQuery("#<?php echo esc_attr($field_name); ?>").select2({
			placeholder: "Choose taxonomies...",
		});
	</script>
	<?php
}


/**
 * Output views to meta box content
 * 
 * @since 0.4.0
 * @author Ilya K.
 */

function ct_view_meta_box_callback( $post ) {

	global $wpdb;
	// Add a nonce field so we can check for it later
	wp_nonce_field( 'ct_view_meta_box', 'ct_view_meta_box_nonce' );

	$screen = get_current_screen();

	if ($screen->post_type != "ct_template") {

		// generic view
		if ( get_option( 'page_for_posts' ) == $post->ID || get_option( 'page_on_front' ) == $post->ID ) {
			$generic_view = ct_get_archives_template( $post->ID ); // true, for exclude templates of type inner_content

			if(!$generic_view) {  // if not template is set to apply to front page or blog posts page, then use the generic page template, as these are pages
				$generic_view = ct_get_posts_template( $post->ID );
			}
		}
		else {

			$generic_view = ct_get_posts_template( $post->ID ); // true, exclude templates of type inner_content

			// if(!$generic_view) {
			// 	$generic_view = ct_get_archives_template( $post->ID );
			// }
		}

		$ct_other_template = get_post_meta( $post->ID, 'ct_other_template', true );
		// check if the other template contains ct_inner_content
		$shortcodes = false;
		$json = false;
		if($ct_other_template && $ct_other_template > 0) {
			$json = 	  get_post_meta($ct_other_template, 'ct_builder_json', true);
			$shortcodes = get_post_meta($ct_other_template, 'ct_builder_shortcodes', true);
		} elseif ( $generic_view && $ct_other_template != -1) {
			$json = 	  get_post_meta($generic_view->ID, 'ct_builder_json', true);
			$shortcodes = get_post_meta($generic_view->ID, 'ct_builder_shortcodes', true);
		}
		
		// wp_query to get all the post type 

		$templates = $wpdb->get_results(
		    "SELECT id, post_title
		    FROM $wpdb->posts as post
		    WHERE post_type = 'ct_template'
		    AND post.post_status IN ('publish')"
		);

		$show_edit_button = false;
		$option_is_selected = false;
		$editing_block = false;

		if ($screen->post_type =='oxy_user_library'){
		    $editing_block = true;
		    $templates = [];
        }


		ob_start();

		?>

			<label style="margin-top: 10px; <?php if($editing_block) echo 'display: none;'; ?>"><?php _e("Render Page Using Template", "component-theme");?><div class="oxy-tooltip"><div class="oxy-tooltip-text">Manage templates from the Oxygen » Templates screen.</div></div>

				<select name="ct_other_template" id="ct_parent_template">
					<?php
						$selected_template = false;

						if($editing_block){
						    ?>
                            <option value="-1" ><?php _e( "None", "component-theme" ); ?></option>
                            <?php
                        }else{

                            if($generic_view) {
                                // check if the template contains ct_inner_content
								$json = get_post_meta( $generic_view->ID, "ct_builder_json", true );
								if ( $json ) {
									$contains_inner_content = (strpos($json, '"name":"ct_inner_content"') !== false);
								}
								else {
									$shortcodes = '';
									$shortcodes = get_post_meta($generic_view->ID, 'ct_builder_shortcodes', true);
									$contains_inner_content = (strpos($shortcodes, '[ct_inner_content') !== false);
								}

                                if(empty($ct_other_template)) {
                                    $option_is_selected = true;
                                    if($contains_inner_content) {
                                        $show_edit_button = true;
                                    }
                                }

                                $has_a_parent = get_post_meta( $generic_view->ID, 'ct_parent_template', false );
                            ?>
                            <option value="-1" ><?php _e( "None", "component-theme" ); ?></option>
                            <option <?php echo $contains_inner_content?'data-inner=true':'';?> <?php echo $has_a_parent?'data-parent=true':'';?> value="0" data-template-id="<?php echo intval($generic_view->ID); ?>"  <?php echo empty($ct_other_template)?'selected':'';?> >Default (<?php echo sanitize_text_field( $generic_view->post_title ); ?>)</option>
                            <?php
                            }
                            else {
                                ?>
                            <option value="0"></option>
                                <?php
                            }
						}
						foreach($templates as $template) {
							if(intval($ct_other_template) == $template->id) {
								$selected_template = $template;
							}

							// do not display re-usables
							$ct_template_type = get_post_meta($template->id, 'ct_template_type', true);
							
							if($ct_template_type && $ct_template_type =='reusable_part') {
								continue;
							}

							// do not display type = inner_content
							$template_inner_content = get_post_meta($template->id, 'ct_template_inner_content', true);

							if($template_inner_content) {
								continue;
							}

								
							$is_selected_template = intval($ct_other_template) == $template->id;

							// check if the template contains ct_inner_content
							// don't modify $json nor $shortcodes vars
							$json_to_check = get_post_meta( $template->id, "ct_builder_json", true );
							if ( $json_to_check ) {
								$contains_inner_content = (strpos($json, '"name":"ct_inner_content"') !== false);
							}
							else {
								$codes = '';
								$codes = get_post_meta($template->id, 'ct_builder_shortcodes', true);
								$contains_inner_content = (strpos($codes, '[ct_inner_content') !== false);
							}

							if($is_selected_template) {
								$shortcodes = '';
								$shortcodes = get_post_meta($template->id, 'ct_builder_shortcodes', true);

								$json = get_post_meta($template->id, 'ct_builder_json', true);

								$option_is_selected = true;
								if($contains_inner_content) {
									$show_edit_button = true;
								}
							}


							$has_a_parent = get_post_meta( $template->id, 'ct_parent_template', false );

							?>
							<option <?php echo $contains_inner_content?'data-inner=true':'';?> <?php echo $has_a_parent?'data-parent=true':'';?> value="<?php echo intval( $template->id ); ?>" <?php echo $is_selected_template?'selected':'';?>><?php echo sanitize_text_field( $template->post_title ); ?></option>
							<?php
							
						}
					?>
				</select>
			</label>

			<?php do_action('ct_after_parent_template_selector'); ?>
			
			<hr style="margin-top: 10px; margin-bottom: 10px;" />

			<?php
			
				$parent_dropdown = ob_get_clean();

				// if the parent template to be edited inherits another template? then add the parameter to the edit link
				$template_id = intval($ct_other_template) !== 0?$ct_other_template:($generic_view?$generic_view->ID:0);

				$parent_of_template = get_post_meta( $template_id, 'ct_parent_template', false );

				?>
				
				<?php if (!is_oxygen_edit_post_locked()) : ?>
				<div id="ct-edit-template-builder-parent-wrap" <?php echo (!$show_edit_button && $option_is_selected) ? "":"style='display:none' " ?>>
					<a id="ct-edit-template-builder-parent" class="button button-primary"
						data-site-url="<?php echo site_url();?>" 
						data-parent-template="<?php echo $ct_other_template?intval($ct_other_template):0;?>" 
						data-current-post-id="<?php echo $post->ID; ?>"
						data-current-post-nonce="<?php echo wp_create_nonce( 'oxygen-nonce-' . $post->ID );?>"
						href="<?php echo esc_url(ct_get_post_builder_link( $template_id )); echo $parent_of_template?'&ct_inner=true':''; ?>" >
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/oxygen.svg">
						<?php printf( __("Edit Template", "oxygen") ); ?>
					</a>
					<p><?php _e("This post is being rendered by an Oxygen template. To edit this post directly, add an Inner Content element to the template.", "oxygen"); ?></p>
				</div>
				<?php else: ?>
				<div id="ct-edit-template-builder-parent-wrap" <?php echo (!$show_edit_button && $option_is_selected) ? "":"style='display:none' " ?>>
					<?php echo __("Oxygen is open in another tab or by another user.", "oxygen"); ?><br/>
					<?php echo __("Please close the other instance of the builder and refresh this page to edit.", "oxygen"); ?><br/>
					<a id="oxygen-open-anyway-link" class="oxygen-open-anyway-link-parent"
						data-site-url="<?php echo site_url();?>" 
						data-parent-template="<?php echo $ct_other_template?intval($ct_other_template):0;?>" 
						data-current-post-id="<?php echo $post->ID; ?>"
						data-current-post-nonce="<?php echo wp_create_nonce( 'oxygen-nonce-' . $post->ID );?>"
						href="<?php echo esc_url(ct_get_post_builder_link( $template_id )); echo $parent_of_template?'&ct_inner=true':''; ?>" >
						<?php echo __("Open Anyway", "oxygen"); ?>
						</a>
					<p><?php _e("This post is being rendered by an Oxygen template. To edit this post directly, add an Inner Content element to the template.", "oxygen"); ?></p>
				</div>
				<?php endif; ?>

				<?php if (is_oxygen_edit_post_locked()) : ?>
				<div class='oxygen-edit-template-button-or-message oxygen-open-anyway-link-post'
					<?php echo (!$show_edit_button && $option_is_selected) ? "style='display:none' ":"" ?>>
					<div class='oxygen-edit-button-message'>
						<?php echo __("Oxygen is open in another tab or by another user.", "oxygen"); ?><br/>
						<?php echo __("Please close the other instance of the builder and refresh this page to edit.", "oxygen"); ?><br/>
						<a id="oxygen-open-anyway-link" class="oxygen-open-anyway-link"
							data-parent-template="<?php echo $ct_other_template?intval($ct_other_template):0;?>" 
							data-current-post-id="<?php echo $post->ID; ?>"
							data-current-post-nonce="<?php echo wp_create_nonce( 'oxygen-nonce-' . $post->ID );?>"
							<?php 
								$url = esc_url(ct_get_post_builder_link( $post->ID ));
								if ($json) {
									if ( ( strpos($json, '"name":"ct_inner_content"') !== false ) && 
										intval($ct_other_template) !== -1 ) {
										$url .= "&ct_inner=true";
									}
								}
								else if (
									( $shortcodes && strpos($shortcodes, '[ct_inner_content') !== false ) && 
									intval($ct_other_template) !== -1 ) {
									$url .= "&ct_inner=true";
								}
							?>
							href="<?php echo $url; ?>">
							<?php echo __("Open Anyway", "oxygen"); ?>
						</a>
					</div>
				</div>
				<?php else : ?>
				<div class='oxygen-edit-template-button-or-message'>
					<a id="ct-edit-template-builder" class="button button-primary"
						<?php echo (!$show_edit_button && $option_is_selected) ? "style='display:none' ":"" ?>
						data-parent-template="<?php echo $ct_other_template?intval($ct_other_template):0;?>" 
						data-current-post-id="<?php echo $post->ID; ?>"
						data-current-post-nonce="<?php echo wp_create_nonce( 'oxygen-nonce-' . $post->ID );?>"
						<?php 
								$url = esc_url(ct_get_post_builder_link( $post->ID ));
								if ($json) {
									if ( ( strpos($json, '"name":"ct_inner_content"') !== false ) && 
										intval($ct_other_template) !== -1 ) {
										$url .= "&ct_inner=true";
									}
								}
								else if (
									( $shortcodes && strpos($shortcodes, '[ct_inner_content') !== false ) && 
									intval($ct_other_template) !== -1 ) {
									$url .= "&ct_inner=true";
								}
							?>
						href="<?php echo $url; ?>">
						<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/oxygen.svg">
						<?php printf( __("Edit with Oxygen", "oxygen") ); ?>
					</a>
					<div id='oxygen-save-first-message'>
						<?php echo __("Please save before editing with Oxygen.", "oxygen"); ?>
					</div>
				</div>
			<?php endif; ?>

			<?php
				echo $parent_dropdown;
				wp_nonce_field( 'ct_shortcode_meta_box', 'ct_shortcode_meta_box_nonce' );
			?>
				<?php if (oxygen_vsb_is_agency_bundle()) : ?>
				<p>
					<?php $post_locked = get_post_meta( $post->ID, 'oxygen_lock_post_edit_mode', true ); ?>
					<label><input <?php checked($post_locked, "true") ?> type="checkbox" name="oxygen_lock_post_edit_mode" value="true"><?php _e( "Lock Post In Edit Mode", "oxygen" ); ?></label>
				</p>
				<?php endif; ?>
				<p>
					<span id="ct-toggle-shortcodes"><?php _e( "Shortcodes (read only)", "oxygen" ); ?></span>
				</p>
				<div id="ct-builder-shortcodes" style="display:none">
					<textarea readonly="true" class="widefat" rows="8" name="ct_builder_shortcodes" id ="ct_builder_shortcodes"><?php 
						echo htmlentities( get_post_meta( $post->ID, 'ct_builder_shortcodes', true ) ); 
					?></textarea>
				</div>
				<p>
					<span id="ct-toggle-json"><?php _e( "JSON", "oxygen" ); ?></span>
				</p>
				<div id="ct-builder-json" style="display:none">
					<textarea class="widefat" rows="8" name="ct_builder_json" id ="ct_builder_json"><?php 
						echo htmlentities(  get_post_meta( $post->ID, 'ct_builder_json', true ) ); 
					?></textarea>
				</div>
		
		<?php Oxygen_Revisions::render_revisions_list( $post->ID );
	}
	// Button only for "Templates"
	else { 
		
		$ct_template_type = get_post_meta( $post->ID, 'ct_template_type', true);

		
			$ct_parent_template = get_post_meta( $post->ID, 'ct_parent_template', true );
			$shortcodes = '';
			$json = '';
			
			if($ct_parent_template && $ct_parent_template > 0) {
				$shortcodes = get_post_meta($ct_parent_template, 'ct_builder_shortcodes', true);	
				$json = get_post_meta($ct_parent_template, 'ct_builder_json', true);
			}
			
			?>
			
			<?php if (is_oxygen_edit_post_locked()) : ?>
			<div class='oxygen-edit-template-button-or-message'>
				<div class='oxygen-edit-button-message'>
					<?php echo __("Oxygen is open in another tab or by another user.", "oxygen"); ?><br/>
					<?php echo __("Please close the other instance of the builder and refresh this page to edit.", "oxygen"); ?><br/>
					<a id="oxygen-open-anyway-link"
						<?php 
						$url = esc_url(ct_get_post_builder_link( $post->ID ));
						if ($json) {
							if ( ( strpos($json, '"name":"ct_inner_content"') !== false ) ) {
								$url .= "&ct_inner=true";
							}
						}
						else if (
							( $shortcodes && strpos($shortcodes, '[ct_inner_content') !== false ) ) {
								$url .= "&ct_inner=true";
							}
						?>
						href="<?php echo $url; ?>">
						<?php echo __("Open Anyway", "oxygen"); ?>
					</a>
				</div>
			</div>
			<?php else : ?>
			<div class='oxygen-edit-template-button-or-message'>
				<a id="ct-edit-template-builder" class="button button-primary"
					data-parent-template="<?php echo $ct_parent_template?intval($ct_parent_template):0;?>" 
					data-current-post-id="<?php echo $post->ID; ?>"
					data-current-post-nonce="<?php echo wp_create_nonce( 'oxygen-nonce-' . $post->ID );?>"
					<?php 
						$url = esc_url(ct_get_post_builder_link( $post->ID ));
						if ($json) {
							if ( ( strpos($json, '"name":"ct_inner_content"') !== false ) ) {
								$url .= "&ct_inner=true";
							}
						}
						else if (
							( $shortcodes && strpos($shortcodes, '[ct_inner_content') !== false ) ) {
								$url .= "&ct_inner=true";
							}
					?>
					href="<?php echo $url; ?>" 
					>
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/oxygen.svg">
					<?php printf( __("Edit with Oxygen", "component-theme"), sanitize_text_field( get_the_title() ) ); ?>
				</a>
				<div id='oxygen-save-first-message'>
					<?php echo __("Your template settings have changed.<br />Please save before editing with Oxygen.", "component-theme"); ?>
				</div>
			</div>
			<?php endif; ?>

			<?php

			// Add a nonce field so we can check for it later.
			wp_nonce_field( 'ct_shortcode_meta_box', 'ct_shortcode_meta_box_nonce' );
		if($ct_template_type != 'reusable_part') {

				$templates = $wpdb->get_results(
				    "SELECT id, post_title
				    FROM $wpdb->posts as post
				    WHERE post_type = 'ct_template'
				    AND post.post_status IN ('publish')"
				);
			?>
			<div class='oxygen-metabox-control-group'>
				<label style="margin-top: 10px;"><?php _e("Inherit design from other template", "component-theme");?>
					<br>
					<select name="ct_parent_template" id="ct_parent_template" data-parent-template="<?php echo (!$ct_parent_template || intval($ct_parent_template) === 0)?'0':intval($ct_parent_template);?>">
						<option value="0" <?php echo (!$ct_parent_template || intval($ct_parent_template) === 0)?'selected':'';?>><?php _e('None', 'component-theme'); ?></option>
						<?php
						foreach($templates as $template) {
							if(intval($ct_parent_template) == $template->id) {
								$selected_template = $template;
							}

							// do not display re-usables
							$ct_template_type = get_post_meta($template->id, 'ct_template_type', true);

							if(!($ct_template_type && $ct_template_type =='reusable_part') && intval($template->id) != $post->ID) {
									
								$json = get_post_meta($template->id, 'ct_builder_json', true);
								if ( $json ) {
									$has_inner_content = (strpos($json, '"name":"ct_inner_content"') !== false);
								}
								else {
									$shortcodes = '';
									$shortcodes = get_post_meta($template->id, 'ct_builder_shortcodes', true);
									$has_inner_content = (strpos($shortcodes, '[ct_inner_content') !== false);
								} 
								
								// check, if self is inherited by any of the templates at any level
								$its_parent = get_post_meta( intval($template->id), 'ct_parent_template', true );
								$is_inherited = false;
								$count = 0; // fail safe
								while($its_parent !== false && $count < 9) {
									if(intval($its_parent) == intval($post->ID)) {
										$is_inherited = true;
										break;
									}
									$count++;
									$its_parent = get_post_meta( intval($its_parent), 'ct_parent_template', true );
								}

								if($has_inner_content && !$is_inherited) {
								?>
									<option <?php echo $has_inner_content?'data-inner=true':'';?> value="<?php echo intval( $template->id ); ?>" <?php echo (intval($ct_parent_template) == $template->id)?'selected':'';?>><?php echo sanitize_text_field( $template->post_title ); ?></option>
								<?php
								}
							}
						}
						?>
					</select>
				</label>
			</div>

			<?php
				/**
				 * Builder shortcodes
				 */
			
				
				$template_post_types 	= get_post_meta( $post->ID, 'ct_template_post_types', true );

				if(!is_array($template_post_types)) {
					$template_post_types = array();
				}

				$template_single_all 	= get_post_meta( $post->ID, 'ct_template_single_all', true );


				$categories_all 		= sanitize_text_field(get_post_meta( $post->ID, 'ct_template_categories_all', true ));
				$tags_all 				= sanitize_text_field(get_post_meta( $post->ID, 'ct_template_tags_all', true ));
				$custom_taxonomies_all 	= sanitize_text_field(get_post_meta( $post->ID, 'ct_template_custom_taxonomies_all', true ));
				
				$use_taxonomies 		= get_post_meta( $post->ID, 'ct_use_template_taxonomies', true );
				$template_raw_taxonomies 	= get_post_meta( $post->ID, 'ct_template_taxonomies', true );
				$template_taxonomies = array();

				if(is_array($template_raw_taxonomies) && isset($template_raw_taxonomies['names'])) {
					foreach($template_raw_taxonomies['names'] as $key => $name) {
						$template_taxonomies[$key] = $name.",".$template_raw_taxonomies['values'][$key];
					}
				}

				// backward compatibility
				// if(isset($template_taxonomies['values']) && isset($template_taxonomies['names'])) {
				// 	$template_taxonomies = $template_taxonomies['values'];
				// }

				$template_apply_if_post_of_parents = get_post_meta( $post->ID, 'ct_template_apply_if_post_of_parents', true );
				$template_post_of_parents 	= get_post_meta( $post->ID, 'ct_template_post_of_parents', true );
				if(!is_array($template_post_of_parents)) {
					$template_post_of_parents = array();
				}
				$template_post_of_parents = implode(',', $template_post_of_parents);


				$template_all_archives = get_post_meta( $post->ID, 'ct_template_all_archives', true );

				$template_apply_if_archive_among_taxonomies = get_post_meta( $post->ID, 'ct_template_apply_if_archive_among_taxonomies', true );
				$template_archive_among_taxonomies 	= get_post_meta( $post->ID, 'ct_template_archive_among_taxonomies', true );
				if ( !$template_archive_among_taxonomies ) {
					$template_archive_among_taxonomies = array();
				}

				$template_apply_if_archive_among_cpt = get_post_meta( $post->ID, 'ct_template_apply_if_archive_among_cpt', true );
				$template_archive_post_types 	= get_post_meta( $post->ID, 'ct_template_archive_post_types', true );
				if ( !$template_archive_post_types ) {
					$template_archive_post_types = array();
				}
				
				$template_apply_if_archive_among_authors = get_post_meta( $post->ID, 'ct_template_apply_if_archive_among_authors', true );
				$template_authors_archives 	   = get_post_meta( $post->ID, 'ct_template_authors_archives', true );
				if ( !$template_authors_archives ) {
					$template_authors_archives = array();
				}

				// index
				$template_index 		= get_post_meta( $post->ID, 'ct_template_index', true );

				// front page
				$template_front_page 	= get_post_meta( $post->ID, 'ct_template_front_page', true );

				// blog posts
				$template_blog_posts 	= get_post_meta( $post->ID, 'ct_template_blog_posts', true );

				// date archive
				$template_date_archive 	= get_post_meta( $post->ID, 'ct_template_date_archive', true );

				// search result
				$template_search_page 	= get_post_meta( $post->ID, 'ct_template_search_page', true );

				// Inner Content
				$template_inner_content 		= get_post_meta( $post->ID, 'ct_template_inner_content', true );
				
				// 404 page
				$template_404_page 		= get_post_meta( $post->ID, 'ct_template_404_page', true );


				$order = get_post_meta( $post->ID, 'ct_template_order', true );
				?>




		<?php wp_enqueue_script( 'accordion' ); ?>
		<div class='oxygen-vsb-apply-template-label'><?php _e("Where does this template apply?","component-theme"); ?></div>
		<div class="accordion-container oxygen-vsb-template-accordion">
			<ul class="outer-border">
				<li class="control-section accordion-section" id="oxygen-template-application-singular">
					<h3 class="accordion-section-title" tabindex="0">
						<?php _e('Singular', 'component-theme');?>
						<span class="screen-reader-text">Press return or enter to open this section</span>
					</h3>
					<div class="accordion-section-content ">
						<div class="inside">
							<div class="oxygen-metabox-control-group">
								<label>
									<input type="checkbox" name="ct_template_single_all" value="true" <?php checked( $template_single_all, "true"); ?>>
									<?php _e("All Post Types","component-theme"); ?>
								</label>
								<br>
								<?php 

								// get all types available for install
								$post_types 	= get_post_types( '', 'objects' );
								$exclude_types 	= array( "ct_template", "nav_menu_item", "revision" );

								foreach ( $post_types as $post_type ) : 

									if ( in_array ( $post_type->name, $exclude_types ) ) {
										continue;
									} ?>
								
									<label>
										<input type="checkbox" name="ct_template_post_types[]" value="<?php echo esc_attr( $post_type->name ); ?>" 
											<?php if ( in_array( $post_type->name, $template_post_types ) ) echo 'checked="checked"'; ?>>
										<?php echo sanitize_text_field( $post_type->label ); ?>
									</label><br/>
								
								<?php endforeach; ?>
							</div>
							
							<hr />

							<div class="oxygen-metabox-control-group">
								<label>
									<input type="checkbox" name="ct_use_template_taxonomies" value="true" 
										<?php if ( $use_taxonomies ) echo 'checked="checked"'; ?>>
									<?php _e("Only apply if taxonomized as all of the following", "component-theme"); ?>
								</label>
								
								<?php ct_view_taxonomies_selector('ct_template_taxonomies', $template_taxonomies) ?>
							</div>

							<div class="oxygen-metabox-control-group">
								<label>
									<input type="checkbox" name="ct_template_apply_if_post_of_parents" value="true" 
										<?php if ( $template_apply_if_post_of_parents ) echo 'checked="checked"'; ?>>
									<?php _e("Only apply if parent ID is one of the following", "component-theme"); ?>
								</label>
								<br>
								<input type="text" name="ct_template_post_of_parents" placeholder="<?php _e("Separate multiple page IDs with commas", "component-theme"); ?>" id="ct_template_post_of_parents" class='oxygen-vsb-metabox-input' value="<?php echo esc_attr($template_post_of_parents);?>" />
							</div>
						</div>
					</div>
				</li>
				<li class="control-section accordion-section" id="oxygen-template-application-archive">
					<h3 class="accordion-section-title" tabindex="0">
						<?php _e('Archive', 'component-theme');?>						
						<span class="screen-reader-text">Press return or enter to open this section</span>
					</h3>
					<div class="accordion-section-content ">
						<div class="inside">
							<div class="oxygen-metabox-control-group">
								<label>
									<input type="checkbox" name="ct_template_all_archives" value="true" 
										<?php if ( $template_all_archives ) echo 'checked="checked"'; ?>>
									<?php _e("All Archives", "component-theme"); ?>
								</label>

							</div>

							<div class="oxygen-metabox-control-group">

								<label>
									<input type="checkbox" name="ct_template_apply_if_archive_among_taxonomies" value="true" 
										<?php if ( $template_apply_if_archive_among_taxonomies ) echo 'checked="checked"'; ?>>
									<?php _e("Taxonomies", "component-theme"); ?>
								</label>
								<?php ct_view_taxonomies_selector('ct_template_archive_among_taxonomies', $template_archive_among_taxonomies, true) ?>
							</div>

							<div class="oxygen-metabox-control-group">

								<label>
									<input type="checkbox" name="ct_template_apply_if_archive_among_cpt" value="true" 
										<?php if ( $template_apply_if_archive_among_cpt ) echo 'checked="checked"'; ?>>
									<?php _e("Post Types", "component-theme"); ?>
								</label>

								<select name="ct_template_archive_post_types[]" id="ct_template_archive_post_types" multiple="multiple">
									<option value="<?php echo __( "all_posttypes" ); ?>"
										<?php if ( in_array( "all_posttypes", $template_archive_post_types ) ) echo 'selected="selected"'; ?>>
										<?php _e( "All Custom Post Types", "component-theme" ); ?>
									</option>
								<?php $custom_post_types = get_post_types();
									$exclude_types 	= array( "ct_template", "nav_menu_item", "revision", "page" );
									foreach($custom_post_types as $item) {
										if(!in_array($item, $exclude_types)) {
											?>
											<option value="<?php echo esc_attr( $item ); ?>" 
												<?php if ( in_array( $item, $template_archive_post_types ) ) echo 'selected="selected"'; ?>>
												<?php echo sanitize_text_field( $item ); ?>
											</option>
											<?php
										}
									}
								?>

								</select>
								<script type="text/javascript">
									jQuery("#ct_template_archive_post_types").select2({
										placeholder: "Choose custom post types...",
									});
								</script>
							</div>


							<div class="oxygen-metabox-control-group">
								<label>
									<input type="checkbox" name="ct_template_apply_if_archive_among_authors" value="true" 
										<?php if ( $template_apply_if_archive_among_authors ) echo 'checked="checked"'; ?>>
									<?php _e("Authors", "component-theme"); ?>
								</label>

								<select name="ct_template_authors_archives[]" id="ct_template_authors_archives" multiple="multiple">
									<option value="<?php echo __( "all_authors" ); ?>"
										<?php if ( in_array( "all_authors", $template_authors_archives ) ) echo 'selected="selected"'; ?>>
										<?php _e( "All Authors", "component-theme" ); ?>
									</option>
									<?php 
									// get all users to loop
									$authors = oxygen_get_authors();

									foreach ( $authors as $author ) : ?>

										<option value="<?php echo esc_attr( $author->ID ); ?>" 
											<?php if ( in_array( $author->ID, $template_authors_archives ) ) echo 'selected="selected"'; ?>>
											<?php echo sanitize_text_field( $author->user_login ); ?>
										</option>
									
									<?php endforeach; ?>

								</select>
								<script type="text/javascript">
									jQuery('#ct_template_authors_archives').select2({
										placeholder: "Choose authors...",
									});
								</script>
							</div>

							<div class="oxygen-metabox-control-group">
								<label>
									<input type="checkbox" name="ct_template_date_archive" value="true" 
										<?php if ( $template_date_archive ) echo 'checked="checked"'; ?>>
									<?php _e("Date", "component-theme"); ?>
								</label>
							</div>
						</div>
					</div>
				</li>
				<li class="control-section accordion-section" id="oxygen-template-application-other">
					<h3 class="accordion-section-title" tabindex="0">
						<?php _e('Other', 'component-theme');?>						
						<span class="screen-reader-text">Press return or enter to open this section</span>
					</h3>
					<div class="accordion-section-content ">
						<div class="inside">
							<div class="oxygen-metabox-control-group">
								<label>
									<input type="checkbox" name="ct_template_front_page" value="true" 
										<?php if ( $template_front_page ) echo 'checked="checked"'; ?>>
									<?php _e("Front Page", "component-theme"); ?>
								</label><br/>
								
								<label>
									<input type="checkbox" name="ct_template_blog_posts" value="true" 
										<?php if ( $template_blog_posts ) echo 'checked="checked"'; ?>>
									<?php _e("Blog Posts Index", "component-theme"); ?>
								</label><br/>


								<label>
									<input type="checkbox" name="ct_template_search_page" value="true" 
										<?php if ( $template_search_page ) echo 'checked="checked"'; ?>>
									<?php _e("Search Results", "component-theme"); ?>
								</label><br/>

								<label>
									<input type="checkbox" name="ct_template_404_page" value="true" 
										<?php if ( $template_404_page ) echo 'checked="checked"'; ?>>
									<?php _e("404", "component-theme"); ?>
								</label><br/>

								<label>
									<input type="checkbox" name="ct_template_inner_content" value="true" 
										<?php if ( $template_inner_content ) echo 'checked="checked"'; ?>>
									<?php _e("Inner Content", "component-theme"); ?>
								</label><br/>


								<label>
									<input type="checkbox" name="ct_template_index" value="true" 
										<?php if ( $template_index ) echo 'checked="checked"'; ?>>
									<?php _e("Catch All", "component-theme"); ?>
								</label>
							</div>
						</div>
					</div>
				</li>
			</ul>
		</div>
		<div class="oxygen-metabox-control-group">
			<label>
				<div class='oxygen-vsb-template-priority'>
					<?php _e("Template Priority ", "component-theme");?>
					<div class="oxy-tooltip"><div class="oxy-tooltip-text">If multiple templates could apply, the template with the highest priority number will be used.</div></div>
				</div>

				<input type="text" name="ct_template_order" class='oxygen-vsb-metabox-input oxygen-vsb-template-order' value="<?php echo esc_html($order); ?>">
			</label>
		</div>
			<?php
		}
			$shortcodes = get_post_meta( $post->ID, 'ct_builder_shortcodes', true );
		?>
		<?php if (oxygen_vsb_is_agency_bundle()) : ?>
		<p>
			<?php $post_locked = get_post_meta( $post->ID, 'oxygen_lock_post_edit_mode', true ); ?>
			<label><input <?php checked($post_locked, "true") ?> type="checkbox" name="oxygen_lock_post_edit_mode" value="true"><?php _e( "Lock Post In Edit Mode", "oxygen" ); ?></label>
		</p>
		<?php endif; ?>
		<p>
			<span id="ct-toggle-shortcodes"><?php _e( "Shortcodes (read only)", "component-theme" ); ?></span>
		</p>
		<div id="ct-builder-shortcodes" style="display:none">
			<textarea readonly="true" class="widefat" rows="8" name="ct_builder_shortcodes" id="ct_builder_shortcodes"><?php echo htmlentities( $shortcodes, ENT_COMPAT, "UTF-8" ); ?></textarea>
		</div>
		<p>
			<span id="ct-toggle-json"><?php _e( "JSON", "oxygen" ); ?></span>
		</p>
		<div id="ct-builder-json" style="display:none">
			<textarea class="widefat" rows="8" name="ct_builder_json" id ="ct_builder_json"><?php echo htmlentities(  get_post_meta( $post->ID, 'ct_builder_json', true ) ); ?></textarea>
		</div>

	<?php
        Oxygen_Revisions::render_revisions_list( $post->ID, true );
	}

}


/**
 * Output views to meta box content
 * 
 * @since 0.4.0
 * @author Ilya K.
 */

function ct_view_save_meta_box( $post_id ) {
	
	// Check if our nonce is set
	if ( ! isset( $_POST['ct_view_meta_box_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid
	if ( ! wp_verify_nonce( $_POST['ct_view_meta_box_nonce'], 'ct_view_meta_box' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( !oxygen_vsb_current_user_can_access() ) {
		return;
	}

	/* OK, it's safe for us to save the data now */

	if (isset($_POST['ct_use_inner_content'])) {
		$ct_use_inner_content = sanitize_text_field($_POST['ct_use_inner_content']);

		update_post_meta( $post_id, 'ct_use_inner_content', $ct_use_inner_content);
	}

	if (isset($_POST['ct_parent_template'])) {
		$ct_parent_template = sanitize_text_field($_POST['ct_parent_template']);
		
		if($ct_parent_template == '0' || intval($ct_parent_template) == $post_id) {
			delete_post_meta( $post_id, 'ct_parent_template');
		}
		else {
			update_post_meta( $post_id, 'ct_parent_template', $ct_parent_template);
		}
	}



	if(isset($_POST['ct_other_template'])) {
		
		$ct_other_template = sanitize_text_field($_POST['ct_other_template']);
		if (is_numeric($ct_other_template)) {
			if ( $ct_other_template !== 0 )
				update_post_meta( $post_id, 'ct_other_template', $ct_other_template);
			else
				delete_post_meta(  $post_id, 'ct_other_template' );
		}
	} else {
		delete_post_meta(  $post_id, 'ct_other_template' );
	}
	






// template type
	

	/**
	 * Archive View
	 */
	
	// post types
	
	$template_archive_post_types_all 	= isset($_POST['ct_template_archive_post_types_all']) ? sanitize_text_field($_POST['ct_template_archive_post_types_all']) : false;
	//$sanitizedValues = array_filter($_POST['ct_template_archive_post_types'], 'ctype_digit');
	// categories
	$template_categories_all 			= isset($_POST['ct_template_categories_all']) ? sanitize_text_field($_POST['ct_template_categories_all']) : false;
	$template_categories 				= (isset($_POST['ct_template_categories']) && is_array($_POST['ct_template_categories'])) ? array_map('sanitize_text_field', $_POST['ct_template_categories']): array();
	
	// tags
	$template_tags_all 					= isset($_POST['ct_template_tags_all']) ? sanitize_text_field($_POST['ct_template_tags_all']) : false;
	$template_tags 						= (isset($_POST['ct_template_tags']) && is_array($_POST['ct_template_tags'])) ? array_map('sanitize_text_field', $_POST['ct_template_tags']): array();
	
	// custom taxonomies
	$template_custom_taxonomies_all 	= isset($_POST['ct_template_custom_taxonomies_all']) ? sanitize_text_field($_POST['ct_template_custom_taxonomies_all']) : false;
	$template_custom_taxonomies 		= (isset($_POST['ct_template_custom_taxonomies']) && is_array($_POST['ct_template_custom_taxonomies'])) ? array_map('sanitize_text_field', $_POST['ct_template_custom_taxonomies']): array();

	// authors archives
	$template_authors_archives_all 		= isset($_POST['ct_template_authors_archives_all']) ? sanitize_text_field($_POST['ct_template_authors_archives_all']) : false;

	// index
	$template_index 					= isset($_POST['ct_template_index']) ? sanitize_text_field($_POST['ct_template_index']) : false;

	// front page
	$template_front_page 				= isset($_POST['ct_template_front_page']) ? sanitize_text_field($_POST['ct_template_front_page']) : false;

	// blog posts
	$template_blog_posts 				= isset($_POST['ct_template_blog_posts']) ? sanitize_text_field($_POST['ct_template_blog_posts']) : false;

	// date archive
	$template_date_archive 				= isset($_POST['ct_template_date_archive']) ? sanitize_text_field($_POST['ct_template_date_archive']) : false;

	// search result
	$template_search_page 				= isset($_POST['ct_template_search_page']) ? sanitize_text_field($_POST['ct_template_search_page']) : false;

	// Inner Content
	$template_inner_content 			= isset($_POST['ct_template_inner_content']) ? sanitize_text_field($_POST['ct_template_inner_content']) : false;

	// 404 page
	$template_404_page 					= isset($_POST['ct_template_404_page']) ? sanitize_text_field($_POST['ct_template_404_page']) : false;


	$template_all_archives	= isset($_POST['ct_template_all_archives']) ? sanitize_text_field($_POST['ct_template_all_archives']) : false;

	$template_apply_if_archive_among_taxonomies	= isset($_POST['ct_template_apply_if_archive_among_taxonomies']) ? sanitize_text_field($_POST['ct_template_apply_if_archive_among_taxonomies']) : false;
	$template_archive_among_taxonomies 	= (isset($_POST['ct_template_archive_among_taxonomies']) && is_array($_POST['ct_template_archive_among_taxonomies'])) ? array_map('sanitize_text_field', $_POST['ct_template_archive_among_taxonomies']): array();

	$template_apply_if_archive_among_cpt	= isset($_POST['ct_template_apply_if_archive_among_cpt']) ? sanitize_text_field($_POST['ct_template_apply_if_archive_among_cpt']) : false;
	$template_archive_post_types 		= (isset($_POST['ct_template_archive_post_types']) && is_array($_POST['ct_template_archive_post_types'])) ? array_map('sanitize_text_field', $_POST['ct_template_archive_post_types']): array();
	

	$template_apply_if_archive_among_authors	= isset($_POST['ct_template_apply_if_archive_among_authors']) ? sanitize_text_field($_POST['ct_template_apply_if_archive_among_authors']) : false;
	$template_authors_archives 	= (isset($_POST['ct_template_authors_archives']) && is_array($_POST['ct_template_authors_archives'])) ? array_map('sanitize_text_field', $_POST['ct_template_authors_archives']): array();

	
	/**
	 * Single View
	 */
	
	$template_single_all 	= isset($_POST['ct_template_single_all']) ? sanitize_text_field( $_POST['ct_template_single_all'] ) : false;
	
	// post types
	$template_post_types 	= isset($_POST['ct_template_post_types']) && is_array($_POST['ct_template_post_types']) ? array_map('sanitize_text_field', $_POST['ct_template_post_types']): array();
	$template_exclude_ids 	= isset($_POST['ct_template_exclude_ids']) ? sanitize_text_field( $_POST['ct_template_exclude_ids'] ) : false;

	// ids
	$template_include_ids 	= isset($_POST['ct_template_include_ids']) ? sanitize_text_field( $_POST['ct_template_include_ids'] ) : false;

	// taxonomies
	
	// $taxonomy_names 		= (isset($_POST['ct_taxonomy_names']) && is_array($_POST['ct_taxonomy_names'])) ? array_map('sanitize_text_field', $_POST['ct_taxonomy_names']): array();
	// $taxonomy_values 		= (isset($_POST['ct_taxonomy_values']) && is_array($_POST['ct_taxonomy_values'])) ? array_map('sanitize_text_field', $_POST['ct_taxonomy_values']): array();

	// unset($taxonomy_names[0]);
	// unset($taxonomy_values[0]);
	
	// $template_taxonomies 	= array(
	// 								'names' 	=> $taxonomy_names,
	// 								'values' 	=> $taxonomy_values );
	

	$use_taxonomies 		= isset($_POST['ct_use_template_taxonomies']) ? sanitize_text_field( $_POST['ct_use_template_taxonomies'] ) : false;
	$template_raw_taxonomies 	= (isset($_POST['ct_template_taxonomies']) && is_array($_POST['ct_template_taxonomies'])) ? array_map('sanitize_text_field', $_POST['ct_template_taxonomies']): array();
	$template_taxonomies = array('names' => array(), 'values' => array());

	foreach($template_raw_taxonomies as $key => $val) {
		$exploded = explode( ',', $val );
		$template_taxonomies['names'][$key] = $exploded[0];
		$template_taxonomies['values'][$key] = $exploded[1];
	}


	$template_apply_if_post_of_parents	= isset($_POST['ct_template_apply_if_post_of_parents']) ? sanitize_text_field($_POST['ct_template_apply_if_post_of_parents']) : false;
	
	$template_post_of_parents = isset($_POST['ct_template_post_of_parents']) ? array_map('sanitize_text_field', explode(',', $_POST['ct_template_post_of_parents'])) : array();
	
	$template_order = isset($_POST['ct_template_order']) ? intval($_POST['ct_template_order']) : 0;

	
	/**
	 * Update Post Meta
	 */
	 
	

	/**
	 * Archive View
	 */
	
	// post types
	
	if ( $template_archive_post_types_all ) {
		update_post_meta( $post_id, 'ct_template_archive_post_types_all', $template_archive_post_types_all );
	}
	else {
		update_post_meta( $post_id, 'ct_template_archive_post_types_all', "");
	}

	// categories
	update_post_meta( $post_id, 'ct_template_categories', $template_categories );
	if ( $template_categories_all ) {
		update_post_meta( $post_id, 'ct_template_categories_all', $template_categories_all );
	}
	else {
		update_post_meta( $post_id, 'ct_template_categories_all', "");
	}
	
	// tags
	update_post_meta( $post_id, 'ct_template_tags', $template_tags );
	if ( $template_tags_all ) {
		update_post_meta( $post_id, 'ct_template_tags_all', $template_tags_all );
	}
	else {
		update_post_meta( $post_id, 'ct_template_tags_all', "");
	}
	
	// custom taxonomy
	update_post_meta( $post_id, 'ct_template_custom_taxonomies', $template_custom_taxonomies );
	if ( $template_custom_taxonomies_all ) {
		update_post_meta( $post_id, 'ct_template_custom_taxonomies_all', $template_custom_taxonomies_all );
	}
	else {
		update_post_meta( $post_id, 'ct_template_custom_taxonomies_all', "" );
	}

	// authors archives
	
	if ( $template_authors_archives_all ) {
		update_post_meta( $post_id, 'ct_template_authors_archives_all', $template_authors_archives_all );
	}
	else {
		update_post_meta( $post_id, 'ct_template_authors_archives_all', "" );
	}

	// index
	update_post_meta( $post_id, 'ct_template_index', $template_index );

	// front page
	update_post_meta( $post_id, 'ct_template_front_page', $template_front_page );

	// blog posts
	update_post_meta( $post_id, 'ct_template_blog_posts', $template_blog_posts );

	// date archive
	update_post_meta( $post_id, 'ct_template_date_archive', $template_date_archive );

	// search result
	update_post_meta( $post_id, 'ct_template_search_page', $template_search_page);

	// Inner Content
	update_post_meta( $post_id, 'ct_template_inner_content', $template_inner_content);

	// 404 page
	update_post_meta( $post_id, 'ct_template_404_page', $template_404_page);


	if ( $template_all_archives ) {
		update_post_meta( $post_id, 'ct_template_all_archives', $template_all_archives );
	}
	else {
		update_post_meta( $post_id, 'ct_template_all_archives', "" );
	}

	update_post_meta( $post_id, 'ct_template_archive_among_taxonomies', $template_archive_among_taxonomies );
	if ( $template_apply_if_archive_among_taxonomies ) {
		update_post_meta( $post_id, 'ct_template_apply_if_archive_among_taxonomies', $template_apply_if_archive_among_taxonomies );
	}
	else {
		update_post_meta( $post_id, 'ct_template_apply_if_archive_among_taxonomies', "" );
	}

	update_post_meta( $post_id, 'ct_template_archive_post_types', $template_archive_post_types );
	if ( $template_apply_if_archive_among_cpt ) {
		update_post_meta( $post_id, 'ct_template_apply_if_archive_among_cpt', $template_apply_if_archive_among_cpt );
	}
	else {
		update_post_meta( $post_id, 'ct_template_apply_if_archive_among_cpt', "" );
	}

	update_post_meta( $post_id, 'ct_template_authors_archives', $template_authors_archives );
	if ( $template_apply_if_archive_among_authors ) {
		update_post_meta( $post_id, 'ct_template_apply_if_archive_among_authors', $template_apply_if_archive_among_authors );
	}
	else {
		update_post_meta( $post_id, 'ct_template_apply_if_archive_among_authors', "" );
	}

	

	/**
	 * Single View
	 */
	
	update_post_meta( $post_id, 'ct_template_single_all', 			$template_single_all );
	
	// post types
	update_post_meta( $post_id, 'ct_template_post_types', 			$template_post_types );
	update_post_meta( $post_id, 'ct_template_exclude_ids', 			$template_exclude_ids );
	update_post_meta( $post_id, 'ct_template_include_ids', 			$template_include_ids );
	
	update_post_meta( $post_id, 'ct_template_taxonomies', 			$template_taxonomies );

	// custom taxonomy
	update_post_meta( $post_id, 'ct_template_taxonomies', $template_taxonomies );
	if ( $use_taxonomies ) {
		update_post_meta( $post_id, 'ct_use_template_taxonomies', $use_taxonomies );
	}
	else {
		update_post_meta( $post_id, 'ct_use_template_taxonomies', "" );
	}


	update_post_meta( $post_id, 'ct_template_post_of_parents', $template_post_of_parents );
	if ( $template_apply_if_post_of_parents ) {
		update_post_meta( $post_id, 'ct_template_apply_if_post_of_parents', $template_apply_if_post_of_parents );
	}
	else {
		update_post_meta( $post_id, 'ct_template_apply_if_post_of_parents', "" );
	}


	update_post_meta( $post_id, 'ct_template_order', $template_order );




	/**
	 * Redirect to builder to edit inner content
	 */
	if (isset($_POST["ct_redirect_inner_content"]) && $_POST["ct_redirect_inner_content"] == "true") {
		
		// redirect to builder
		wp_redirect( esc_url_raw(ct_get_post_builder_link( $post_id )).'&ct_inner=true' );
		exit;
	}
	elseif(isset($_POST["ct_redirect_to_builder"]) && $_POST["ct_redirect_to_builder"] == "true") {
		wp_redirect( esc_url_raw(ct_get_post_builder_link( $post_id )) );
		exit;
	}
	elseif(isset($_POST["ct_redirect_to_template"]) && is_numeric($_POST["ct_redirect_to_template"])) {
		wp_redirect( esc_url_raw(ct_get_post_builder_link( intval($_POST["ct_redirect_to_template"]) )) );
		exit;
	}

	/**
	 * Redirect to builder to create a view
	 */
	if (isset($_POST["ct_custom_view_on_create_copy"]) && $_POST["ct_custom_view_on_create_copy"] == "true") {
		
		$other_template = intval( $_POST["ct_other_template"] );

		if($other_template > 0) {
			$shortcodes = get_post_meta( $other_template, "ct_builder_shortcodes", true );
			$json = get_post_meta( $other_template, "ct_builder_json", true );
		}
		else {
			$template 	= ct_get_posts_template( $post_id );
			$shortcodes = get_post_meta( $template->ID, "ct_builder_shortcodes", true );
			$json = get_post_meta( $template->ID, "ct_builder_json", true );
		}

		// if the shortcodes contain a ct_inner_content element, remove it
		$shortcodes = preg_replace("/\[ct_inner_content[^\]]*\]\[\/ct_inner_content\]/i", '', $shortcodes);

		// set post shortcodes to view shortcodes
		update_post_meta( $post_id, 'ct_builder_shortcodes', $shortcodes);
		update_post_meta( $post_id, 'ct_builder_json', $json);

		// reset the page settings to use custom view
		update_post_meta( $post_id, 'ct_render_post_using', 'custom_template' );
		delete_post_meta(  $post_id, 'ct_other_template' );

		// redirect to builder
		wp_redirect( esc_url_raw(ct_get_post_builder_link( $post_id )) );
		exit;
	}

	if ( isset( $_POST["ct_create_custom_view"] ) ) {

		// redirect to builder
		wp_redirect( esc_url_raw(ct_get_post_builder_link( $post_id )) );
		exit;
	};
}
add_action( 'save_post', 'ct_view_save_meta_box' );



/**
 * Output views to meta box content
 * 
 * @since 0.4.0
 * @author Ilya K.
 */

function ct_view_order_meta_box_callback( $post ) {

	// Add a nonce field so we can check for it later
	wp_nonce_field( 'ct_view_order_meta_box', 'ct_view_order_meta_box_nonce' );

	$order = get_post_meta( $post->ID, 'ct_template_order', true );

	_e("Order ", "component-theme");

	?>

	<input type="text" name="ct_template_order" value="<?php echo esc_html($order); ?>">
	<p class="description"><?php _e("Templates with highest order has a priority when multiple templates applies."); ?></p>
	<?php
}


/**
 * Output views to meta box content
 * 
 * @since 0.4.0
 * @author Ilya K.
 */

function ct_view_order_save_meta_box( $post_id ) {

	if(!oxygen_vsb_current_user_can_access()) {
		return;
	}

	// Check if our nonce is set
	if ( ! isset( $_POST['ct_view_order_meta_box_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid
	if ( ! wp_verify_nonce( $_POST['ct_view_order_meta_box_nonce'], 'ct_view_order_meta_box' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( isset( $_POST["ct_template_order"] ) ) {
		update_post_meta( $post_id, 'ct_template_order', intval($_POST["ct_template_order"]) );
	};
}


/**
 * Add a select with all view types to filter
 * 
 * @since 0.4.0
 * @author Ilya K.
 */

add_action( 'restrict_manage_posts', 'ct_views_filter_dropdown' );
function ct_views_filter_dropdown() {
	
	global $typenow;
	
	$taxonomy = $typenow.'_type';
	
	if( $typenow == "ct_template" ) {
	
		$types = array(
           	
           	"reusable_part" =>
           		__( 'Re-usable part', 'component-theme' ),
           	
           	"template" =>
           		__( 'Template', 'component-theme' ),

		);
        
        echo "<select name=\"ct_template_type\">";
        echo "<option value=\"\">All templates types</option>";
        
        foreach( $types as $name => $title ) {

            if ( isset( $_GET['ct_template_type'] ) && $name === $_GET['ct_template_type' ] ) {
                $selected = ' selected ';
            } else {
                $selected = '';
            }

            echo "<option $selected value=\"" . esc_attr( $name ) . "\">" . esc_html( $title ) .  "</option>";
        }

        echo "</select>";
	}
}


/**
 * Filter views based on type selected by user
 * 
 * @since 0.4.0
 * @author Ilya K.
 */

function ct_views_filter( $query )
{
    global $typenow;
    global $pagenow;

    if( $pagenow == 'edit.php' && $typenow == 'ct_template' && isset($_GET['ct_template_type']) && $_GET['ct_template_type'] )
    {

    	$keyword = sanitize_text_field($_GET['ct_template_type']);
    	
        $query->query_vars['meta_query'] = array(
		    array(
		       'key' => 'ct_template_type',
		       'compare' => ($keyword === 'template') ? 'NOT EXISTS' : 'EXISTS'
		    )
		);

    }

}
add_filter( 'parse_query', 'ct_views_filter' );


/**
 * Add order column to views list table
 * 
 * @since 0.4.0
 * @author Ilya K.
 */

function ct_views_order_column( $columns ) {

	// save date and uset value to use later
	$date = $columns['date'];
	unset($columns['date']);
    
    // add type
    $columns['ct_view_order'] = __( 'Order', 'component-theme' );

    // add date back
    $columns['date'] = $date;

    return $columns;
}
add_filter( 'manage_ct_template_posts_columns', 'ct_views_order_column' );


/**
 * Add order value to views order column
 * 
 * @since 0.4.0
 * @author Ilya K.
 */

function ct_views_order_value( $column, $post_id ) {
    switch ( $column ) {

        case 'ct_view_order' :
        	$template_order = get_post_meta( $post_id, 'ct_template_order', true );
            echo is_numeric( $template_order ) ? intval($template_order) : 0;
        break;
    }
}
add_action( 'manage_ct_template_posts_custom_column' , 'ct_views_order_value', 10, 2 );


/**
 * Make view order column sortable
 * 
 * @since 0.4.0
 * @author Ilya K.
 */

function ct_views_order_sortable( $columns ) {
   
    $columns['ct_view_order'] = 'ct_view_order';

    return $columns;
}
add_filter( 'manage_edit-ct_template_sortable_columns', 'ct_views_order_sortable' );


/**
 * Sort views by order column
 * 
 * @since 0.4.0
 * @author Ilya K.
 */

function ct_views_order_sort( $query ) {
    
    if( ! is_admin() )
        return;
 
    $orderby = $query->get( 'orderby');
 
    if( 'ct_view_order' == $orderby ) {
        $query->set('meta_key','ct_template_order');
        $query->set('orderby','meta_value_num');
    }
}
add_action( 'pre_get_posts', 'ct_views_order_sort' );


/**
 * Output post types in post type column of the views list table
 * 
 * @since 0.4.0
 * @author Ilya K.
 */

function ct_views_type_value( $column, $post_id ) {
	$post_types = '';
    switch ( $column ) {

        case 'ct_post_type' :

        	$template_type = get_post_meta( $post_id, 'ct_template_type', true );
            
            if ( $template_type == "single_post" ) {
            	$post_types = get_post_meta( $post_id, 'ct_template_post_types', true );
            }

            if ( $template_type == "archive" ) {
            	$post_types = get_post_meta( $post_id, 'ct_template_archive_post_types', true );
            }

            if ( isset( $post_types ) && is_array( $post_types ) ) {
            	$post_types = implode(", ", $post_types);
            }

            echo sanitize_text_field( $post_types );
        
        break;
    }
}
add_action( 'manage_ct_template_posts_custom_column' , 'ct_views_type_value', 10, 2 );


function ct_views_taxonomies_value( $column, $post_id ) {
    switch ( $column ) {

        case 'ct_post_taxonomies' :

        	$template_type = sanitize_text_field(get_post_meta( $post_id, 'ct_template_type', true ));

            if ( $template_type == "archive" ) {
            	
            	$categories 		= get_post_meta( $post_id, 'ct_template_categories', true );
            	$categories 		= is_array($categories) ? array_map('sanitize_text_field', $categories): array();


				$tags 				= get_post_meta( $post_id, 'ct_template_tags', true );
				$tags = is_array($tags) ? array_map('sanitize_text_field', $tags): array();

				$custom_taxonomies 	= get_post_meta( $post_id, 'ct_template_custom_taxonomies', true );
				$custom_taxonomies 	= is_array($custom_taxonomies) ? array_map('sanitize_text_field', $custom_taxonomies): array();

				$authors_archives 	= get_post_meta( $post_id, 'ct_template_authors_archives', true );
				$authors_archives 	= is_array($authors_archives) ? array_map('sanitize_text_field', $authors_archives): array();

				$categories_all 		= sanitize_text_field(get_post_meta( $post_id, 'ct_template_categories_all', true ));
				$tags_all 				= sanitize_text_field(get_post_meta( $post_id, 'ct_template_tags_all', true ));
				$custom_taxonomies_all 	= sanitize_text_field(get_post_meta( $post_id, 'ct_template_custom_taxonomies_all', true ));
				$authors_archives_all 	= sanitize_text_field(get_post_meta( $post_id, 'ct_template_authors_archives_all', true ));
            }

			if ( isset($categories_all) && $categories_all ) {
				_e("All Categories", "component-theme");
				echo "<br/>";
			}
			else
			if ( isset( $categories ) && is_array( $categories ) ) {

				foreach ( $categories as $id ) {
					$category = get_term_by( "id", $id, "category" ); 
					$category_names[] = $category->name;
				}

				if(isset($category_names) && is_array($category_names)) {
					_e("Categories: ", "component-theme");
					echo implode(", ", $category_names);
					echo "<br/>";
				}
				
			}

			if ( isset($tags_all) && $tags_all ) {
				_e("All Tags", "component-theme");
				echo "<br/>";
			}
			else
			if ( isset( $tags ) && is_array( $tags ) ) {
				
				foreach ( $tags as $id ) {
					$tag = get_term_by( "id", $id, "post_tag" );
					$tag_names[] = $tag->name;
				}
				if(isset($tag_names) && is_array($tag_names)) {
					_e("Tags: ", "component-theme");
					echo implode(", ", $tag_names);
					echo "<br/>";
				}
			}

            if ( isset($custom_taxonomies_all) && $custom_taxonomies_all ) {
            	_e("All Custom Taxonomies", "component-theme");
            	echo "<br/>";
            }
			else
			if ( isset( $custom_taxonomies ) && is_array( $custom_taxonomies ) ) {
				
				$taxonomy_names = array();
				$all_terms = array();     	
				foreach ( $custom_taxonomies as $id ) {

					//var_dump(strpos( $id, "all_"));

					// all certain taxonomy terms
					if ( strpos( $id, "all_") === 0 ) {
						_e("All ", "component-theme");
						echo str_replace("all_", "", $id)."<br/>";

						// save to exclude later
						$all_terms[] = str_replace("all_", "", $id);
					}
					// single term
					else {
						$term = get_term($id);
						$taxonomy_names[$term->taxonomy][] = $term->name;
					}
				}

				foreach ( $taxonomy_names as $name => $temrs ) {
					
					if (in_array($name, $all_terms))
						continue;

					echo $name .": ". implode(", ", $temrs);
					echo "<br/>";
				}
			}

			if ( isset($authors_archives_all) && $authors_archives_all ) {
				_e("All Authors", "component-theme");
				echo "<br/>";
			}
			else
			if ( isset($authors_archives) && is_array( $authors_archives ) ) {
				
				foreach ( $authors_archives as $id ) {
					$author = get_user_by("id", $id);
					$author_names[] = $author->user_nicename;
				}

				if(isset($author_names) && is_array($author_names)) {
					_e("Authors: ", "component-theme");
					echo implode(", ", $author_names);
					echo "<br/>";
				}
			}
        
        break;
    }
}
add_action( 'manage_ct_template_posts_custom_column' , 'ct_views_taxonomies_value', 10, 2 );

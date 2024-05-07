<?php 


/**
 * Replace for add_shortcode() since migration to JSNO
 *  
 */

function add_oxygen_element($tag, $callback) {

	global $oxygen_registered_elements;
	
	if ( !is_array($oxygen_registered_elements)) {
		$oxygen_registered_elements = [];
	}

	$oxygen_registered_elements[ $tag ] = $callback;
}

/**
 * Replace for do_shortcodes() since migration to JSON
 * 
 */

function do_oxygen_elements($node) {

	if (isset($node['name']) && $node['name']=='root' && array_key_exists( 'children', $node)) {
		$node = $node['children'];
	}

	global $oxygen_registered_elements;

	$html = "";

	if (!is_array($node)) {
		return ct_do_shortcode($node);
	}

	foreach ( $node as $child ) {

		if (!isset($child['name'])) {
			continue;
		}

		$tag = $child['name'];
		$options = array ('ct_options' => json_encode($child['options']));
		
		$children = (isset($child['children'])) ? $child['children'] : array();
		$content = (isset($child['options']['ct_content'])) ? $child['options']['ct_content'] : "";

		if ($children && $content) {
			foreach ($children as $grand_child) {
				$parced_grand_child = do_oxygen_elements(array($grand_child));
				$placeholder_id = ($grand_child['id']>=100000) ? $grand_child['id'] % 100000 : $grand_child['id'];
				$content = str_replace("<span id=\"ct-placeholder-{$placeholder_id}\"></span>", $parced_grand_child, $content);
			}
		}
		else if ($children) {
			$content = $children;
		}

		if ( isset($oxygen_registered_elements[ $tag ]) ) {
			
			if ( ! is_callable( $oxygen_registered_elements[ $tag ] ) ) {
				/* translators: %s: Shortcode tag. */
				$message = sprintf( __( 'Attempting to parse a shortcode without a valid callback: %s' ), $tag );
				_doing_it_wrong( __FUNCTION__, $message, '4.3.0' );
				return "";
			}
			
			$return = apply_filters( 'pre_do_shortcode_tag', false, $tag, $options, array() );
			if ( false !== $return ) {
				return $return;
			}
			
			$output = call_user_func( $oxygen_registered_elements[ $tag ], $options, $content, $tag );
			
			$output = apply_filters( 'do_shortcode_tag', $output, $tag, $options, array() );

			$html .= $output;
		}
	}


	return $html;
}

/**
 * Get template applied to the post
 *
 * @return string [HTML] or bool false
 * @since 0.2.0
 */

function ct_template_output( $as_shortcodes = false ) {

	$json = ct_template_json();
	$tree = json_decode($json, true);

	if (isset($tree['children'])) {

		if ( $as_shortcodes ) {
			return $json;
		}

		global $oxygen_doing_oxygen_elements;
		$oxygen_doing_oxygen_elements = true;

		$html = do_oxygen_elements($tree['children']);

		return $html;
	}

	// Fallback to Shortcodes
	
	$shortcodes = ct_template_shortcodes();
	
	// return shortcodes
	if ( $shortcodes && $as_shortcodes ) {
		return $shortcodes;
	}

	// return rendered HTML
	if ( $shortcodes ) {

		$content = ct_do_shortcode( $shortcodes );

		return $content;
	} 
	else {
		return false;
	}
}



/**
 * Return post custom keys as an array
 *
 * @since 1.5
 * @author Emmanuel Laborin
 */

function ct_get_post_meta_keys( $post_id ) {

	$custom_field_keys = get_post_custom_keys( $post_id );
	$user_keys = array();

	// Exclude WordPress and Oxygen internal Meta Keys
	if(is_array($custom_field_keys)) {
		foreach ( $custom_field_keys as $key ) {
			$valuet = trim( $key );
			if( substr($valuet, 0, 1 ) === '_' || substr($valuet, 0, 3 ) === 'ct_' ) continue;
			array_push( $user_keys, $key );
		}
	}

	return $user_keys;
}


/**
 * Look for post's template and start buffering content if found on frontend
 * 
 * @since 0.2.0
 */

function ct_templates_buffer_start() { 

	// only for frontend
	if ( defined("SHOW_CT_BUILDER") ) {
		return false;
	}

	add_filter("stylesheet", "ct_disable_theme_load", 1, 1);

	global $template_content;
	global $oxy_buffer_started;

	$oxy_buffer_started = false;

	// generate template output
	$template_content = ct_template_output();

	// support for elementor plugin
	/*if ( isset( $_REQUEST['elementor-preview'] ) ) {
		$template_content = ct_template_output();
	}*/

	if ( $template_content !== false ) {
		// all native post output go to buffer
		ob_start();
		$oxy_buffer_started = true;
		
	} else {
		
		global $ct_replace_render_template;
		
		if(!isset($ct_replace_render_template) || 
			$ct_replace_render_template == get_single_template() ||
			$ct_replace_render_template == get_page_template() ||
			$ct_replace_render_template == get_index_template()) {

			// this also shoud go to buffer
			ob_start();

			// default content loop
			// Start the loop.
			while ( have_posts() ) : the_post();
			
			?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header>
						<?php the_title( '<h1>', '</h1>' ); ?>
					</header>

					<div>
						<?php
						the_content();

						wp_link_pages( array(
							'before'      => '<div><span>' . __( 'Pages:', 'oxygen' ) . '</span>',
							'after'       => '</div>',
							'link_before' => '<span>',
							'link_after'  => '</span>',
							'pagelink'    => '<span>' . __( 'Page', 'oxygen' ) . ' </span>%',
							'separator'   => '<span>, </span>',
						) );
						?>
					</div>

				</article>
				<?php
				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}

				// End of the loop.
			endwhile;

			// save output for later
			$template_content = ob_get_clean();
		}
	}
}


/**
 * Stop buffering native content
 * 
 * @since 0.2.0
 */

function ct_templates_buffer_end() { 

	// only for frontend
	if ( defined("SHOW_CT_BUILDER") ) {
		return false;
	}

	remove_filter("stylesheet", "ct_disable_theme_load", 1, 1);

	global $template_content;
	global $oxy_buffer_started;

	if ( $template_content !== false && ob_get_length()>0) {
		// clear buffer with native content
		ob_clean();
	}

	if ( $oxy_buffer_started !== false ) {
		ob_end_clean();
	}
}

add_action('ct_builder_start', 	'ct_templates_buffer_start');
add_action('ct_builder_end', 	'ct_templates_buffer_end');


/**
 * Output template settings
 * 
 * @since 0.2.0
 */

function ct_template_builder_settings() { 
	
	?>
	<div class='oxygen-control-wrapper'>
		<div class='oxygen-control oxygen-template-previewing-control oxygen-editing-list-control'>
			<label class='oxygen-control-label'><?php _e("Editing","oxygen"); ?></label>
			<div class="oxygen-select oxygen-select-box-wrapper">
				<div class="oxygen-select-box">
					<div class="oxygen-select-box-current" >
						{{iframeScope.currentPreview}}
					</div>
					<div class="oxygen-select-box-dropdown"></div>
				</div>
				<div class="oxygen-select-box-options">
					<div class="oxygen-select-box-option">
						<input type="text" value="" placeholder="<?php _e("Filter...", "oxygen"); ?>" spellcheck="false"
							ng-model="currentlyEditingFilter"
							ng-change="iframeScope.loadEditingList()"/>
					</div>
					<div class="oxygen-select-box-option" title="<?php _e("Edit", "oxygen"); ?>"
						ng-repeat="post in iframeScope.editingList"
						ng-click="iframeScope.changePreview(post);">
						{{post.post_title}}<span>{{post.type}}</span>
					</div>
				</div>
			</div>
		</div>
	</div>&nbsp;
	
	<div class='oxygen-control-wrapper'
		ng-show="iframeScope.ajaxVar.oxyTemplate && !iframeScope.ajaxVar.oxyReusable">
		<div class='oxygen-control oxygen-template-previewing-control'>
			<label class='oxygen-control-label'><?php _e("Previewing","oxygen"); ?></label>
			<div class="oxygen-select oxygen-select-box-wrapper">
				<div class="oxygen-select-box">
					<div class="oxygen-select-box-current" title="{{iframeScope.previewType == 'post' ? iframeScope.template.postData.post_title : iframeScope.template.postData.term_name}}">{{iframeScope.previewType == 'post' ? iframeScope.template.postData.post_title : iframeScope.template.postData.term_name}}</div>
					<div class="oxygen-select-box-dropdown"></div>
				</div>
				<div class="oxygen-select-box-options">
					<div class="oxygen-select-box-option">
						<input ng-model="postsFilter" type="text" value="" placeholder="<?php _e("Filter...", "oxygen"); ?>" spellcheck="false"/>
					</div>
					<div class="oxygen-select-box-option" title="<?php _e("Preview this post", "oxygen"); ?>"
						ng-repeat="post in iframeScope.template.postsList | filter:postsFilter | limitTo:20"
						ng-click="iframeScope.loadTemplatesTerm(post.id, 'post');">
						{{post.title}}
					</div>
					<div class="oxygen-select-box-option" title="<?php _e("Preview this post", "oxygen"); ?>"
						ng-repeat="term in iframeScope.template.termsList | filter:postsFilter | limitTo:20"
						ng-click="iframeScope.loadTemplatesTerm(term.id, 'term');">
						{{term.title}}
					</div>
					<div class="oxygen-select-box-option oxygen-nothing-found-option"
						ng-show="!iframeScope.template.termsList.length && !iframeScope.template.postsList.length">
						<?php _e("No items to preview"); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php 

}


function ct_get_inner_content_template() {
	global $temp_count;

	if(!isset($temp_count) || !is_numeric($temp_count)) {
		$temp_count = 1;
	}
	else {
		$temp_count++;
	}

	if($temp_count > 1) {
		return false;
	}


	$args = array(
		'posts_per_page'	=> -1,
		'orderby' 			=> 'meta_value_num',
		'meta_key'			=> 'ct_template_order',
		'order' 			=> 'DESC',
		'post_type' 		=> 'ct_template',
		'post_status' 		=> 'publish'
	);

	$templates = new WP_Query( $args );

	foreach ( $templates->posts as $template ) {

		// check if all posts applies
		$applies = get_post_meta( $template->ID, 'ct_template_inner_content', true );

		if($applies) {
			return $template;
		}

	}
}

/**
 * Get post's template based on template settings
 *
 * @since  0.2.0
 */

function ct_get_posts_template( $post_id ) {

	if ( ! is_numeric( $post_id ) || $post_id <= 0 ) {
		return false;
	}

	$current_post_type = get_post_type( $post_id );

	$args = array(
		'posts_per_page'	=> -1,
		'orderby' 			=> 'meta_value_num',
		'meta_key'			=> 'ct_template_order',
		'order' 			=> 'DESC',
		'post_type' 		=> 'ct_template',
		'post_status' 		=> 'publish',
		// 'meta_query' => array(
		// 	array(
		// 		'key'     => 'ct_template_type',
		// 		'value'   => 'single_post',
		// 	),
		// ),
	);

	$templates = new WP_Query( $args );

	foreach ( $templates->posts as $template ) {
		// ignore type inner_content
		$template_inner_content = get_post_meta($template->ID, 'ct_template_inner_content', true);
		if($template_inner_content) {				
			continue;
		}


		// check if all posts applies
		$all_posts = get_post_meta( $template->ID, 'ct_template_single_all', true );
		
		if ( $all_posts ) {
			return $template;
		}
		else {

			// get post types
			$post_types = get_post_meta( $template->ID, 'ct_template_post_types', true );

			// check if current post type is added for template
			if ( is_array( $post_types ) && in_array( $current_post_type, $post_types ) ) {

				$match = true;
				// taxonomies
				$use_taxonomies = get_post_meta( $template->ID, 'ct_use_template_taxonomies', true );
				$if_parent_among = get_post_meta( $template->ID, 'ct_template_apply_if_post_of_parents', true );
				
				if ( $use_taxonomies ) {	

					$template_taxonomies = get_post_meta( $template->ID, 'ct_template_taxonomies', true );

					if ( $template_taxonomies ) {

						foreach ( $template_taxonomies['names'] as $key => $value ) {

							// 'post_tag' taxonomy set as 'tag' in the template metabox settings
							// we cannot rename it for backward compatibilty
							// use workaround below
							if ( $value == 'tag' ) {
								 $value = 'post_tag';
							}

							$post_values = wp_get_post_terms( $post_id, $value, array('fields' => 'ids') );

							$template_value = $template_taxonomies['values'][$key];

							if ( !is_array($post_values) || !in_array( $template_value, $post_values ) ) {
								$match = false;
							}
						}
						
					}
				}

				if ( $if_parent_among ) {	

					$parents = get_post_meta( $template->ID, 'ct_template_post_of_parents', true );
					$parent_id = wp_get_post_parent_id( $post_id );

					if(!in_array($parent_id, $parents)) {
						$match = false;
					}
				}
				
				if ( $match ) {
					return $template;
				}
			}
		}
	}

	//check for index template
	$args = array(
		'posts_per_page'	=> -1,
		'order' 			=> 'DESC',
		'orderby'    		=> 'meta_value_num',
		'meta_key' 			=> 'ct_template_order',
		'post_type' 		=> 'ct_template',
		'post_status' 		=> 'publish',
		'meta_query' => array(
			array(
				'key'     => 'ct_template_index',
				'value'   => 'true',
			),
		),
	);

	$templates = new WP_Query( $args );

	foreach ( $templates->posts as $template ) {
		if ( get_post_meta( $template->ID, 'ct_template_index', true ) ) {
			return $template;
		}
	}

	return false;
}


/**
 * Get archive's template based on templates settings
 *
 * @since  0.2.1
 */

function ct_get_archives_template( $post_id = false) {

	// Get all archive templates
	$args = array(
		'posts_per_page'	=> -1,
		'order' 			=> 'DESC',
		'orderby'    		=> 'meta_value_num',
		'meta_key' 			=> 'ct_template_order',
		'post_type' 		=> 'ct_template',
		'post_status' 		=> 'publish',
		// 'meta_query' => array(
		// 	array(
		// 		'key'     => 'ct_template_type',
		// 		'value'   => 'archive',
		// 	),
		// ),
	);

	$templates = new WP_Query( $args );

	$catch_all_templates = array();

	$is_front_page = ($post_id && get_option( 'page_on_front' ) == $post_id ) || is_front_page();

	foreach ( $templates->posts as $template ) {
		
		// ignore type inner_content 
		
		$template_inner_content = get_post_meta($template->ID, 'ct_template_inner_content', true);
		if($template_inner_content) {
			continue;
		}
		

		// 404
		if ( is_404() && get_post_meta( $template->ID, 'ct_template_404_page', true ) ) {
			return $template;
		}

		// Check what is the current archive
		// Post types

		$applies_to_all_archives = get_post_meta( $template->ID, 'ct_template_all_archives', true );
		
		if($applies_to_all_archives && !$is_front_page) {
			return $template;
		}

		$applies_to_post_types = get_post_meta( $template->ID, 'ct_template_apply_if_archive_among_cpt', true );

		if ( $applies_to_post_types && is_post_type_archive() ) {
			
			// check specific post type
			$queried_object = get_queried_object();
			$post_types = get_post_meta( $template->ID, 'ct_template_archive_post_types', true );

			if ( is_array( $post_types ) && (in_array('all_posttypes', $post_types) || (isset($queried_object->name) && in_array( $queried_object->name, $post_types )) )) {
				return $template;
			}
			
		}

		$applies_to_taxonomies = get_post_meta( $template->ID, 'ct_template_apply_if_archive_among_taxonomies', true );
		if($applies_to_taxonomies) {
			$taxonomies = get_post_meta( $template->ID, 'ct_template_archive_among_taxonomies', true );
			// Categories
			if ( is_category() ) {

				// check specific categories
				$category_id = get_cat_ID( single_cat_title("", false ) );

				if ( is_array( $taxonomies ) && (in_array( 'all_taxonomies', $taxonomies ) || in_array( 'all_categories', $taxonomies ) || in_array( $category_id, $taxonomies )) ) {
					return $template;
				}
			}
			
			// Tags
			if ( is_tag() ) {

				// check specific tags
				$tag_id = get_query_var('tag_id');

				if ( is_array( $taxonomies ) && (in_array( 'all_taxonomies', $taxonomies ) || in_array( 'all_tags', $taxonomies ) || in_array( $tag_id, $taxonomies )) ) {
					return $template;
				}
			}

			// Custom Taxonomy
			if ( is_tax() ) {

				$queried_object = get_queried_object();
				$term_id = $queried_object->term_id;
				$taxonomy = $queried_object->taxonomy;
				
				if ( is_array( $taxonomies ) && (in_array( 'all_taxonomies', $taxonomies ) || in_array( 'all_'. $taxonomy, $taxonomies ) || in_array( $term_id, $taxonomies )) ) {
					return $template;
				}

			}
		}

		$applies_to_authors = get_post_meta( $template->ID, 'ct_template_apply_if_archive_among_authors', true );
		// Author archive
		if ( $applies_to_authors && is_author() ) {

			// check specific post type
			$author 	= get_the_author_meta('ID');
			$authors 	= get_post_meta( $template->ID, 'ct_template_authors_archives', true );

			if ( is_array( $authors ) && (in_array('all_authors', $authors) || in_array( $author, $authors )) ) {
				return $template;
			}
		}

		// Front Page
		
		if ( $is_front_page && get_post_meta( $template->ID, 'ct_template_front_page', true ) ) {
			return $template;
		}	

		// Blog Posts
		// backend
		if ( $post_id ) {
			if ( get_option( 'page_for_posts' ) == $post_id && get_post_meta( $template->ID, 'ct_template_blog_posts', true ) ) {
				return $template;
			}	
		}
		// frontend
		else {
			if ( is_home() && get_post_meta( $template->ID, 'ct_template_blog_posts', true ) ) {
				return $template;
			}
		}

		// Date
		if ( is_date() && get_post_meta( $template->ID, 'ct_template_date_archive', true ) ) {
			return $template;
		}

		// Search
		if ( is_search() && get_post_meta( $template->ID, 'ct_template_search_page', true ) ) {
			return $template;
		}


	}

	//check for index template
	$args = array(
		'posts_per_page'	=> -1,
		'order' 			=> 'DESC',
		'orderby'    		=> 'meta_value_num',
		'meta_key' 			=> 'ct_template_order',
		'post_type' 		=> 'ct_template',
		'post_status' 		=> 'publish',
		'meta_query' => array(
			array(
				'key'     => 'ct_template_index',
				'value'   => 'true',
			),
		),
	);

	$templates = new WP_Query( $args );

	foreach ( $templates->posts as $template ) {
		if ( get_post_meta( $template->ID, 'ct_template_index', true ) ) {
			return $template;
		}
	}
	// finally return false
	return false;
}


/**
 * Get template's post based on template settings
 *
 * @return Object [Modified WP_Post]
 * @since  0.2.0
 * @author Ilya K.
 */

function ct_get_templates_post( $template_id, $current_post_id = false, $option = false, $catch_all = false ) {

	if ( ! is_numeric( $template_id ) || $template_id <= 0 ) {
		return array();
	}

	$new_post_key = 0;

	// look in cache
	$posts = wp_cache_get("ct_archive_template_posts_" . $template_id );
	
	if ( !$posts || !$posts[$new_post_key] ) {

		/**
		 * Query arguments 
		 */

		$limit = get_option('oxygen_vsb_preview_dropdown_limit');

		$post_type_args = array();
		if ( get_option('oxygen_vsb_preview_dropdown_exclude_non_public') ) {
			$post_type_args["publicly_queryable"] = true;
		}

		if ( !$limit || !is_numeric($limit) ) {
			$limit = -1;
		}

		$args = array(
			'posts_per_page'	=> $limit,
			'order'				=> 'DESC'
		);

		/**
		 * Get all template's meta
		 */
		
		$all_posts = get_post_meta( $template_id, 'ct_template_single_all', true );

		$is_catch_all = $catch_all || get_post_meta( $template_id, 'ct_template_index', true ) || get_post_meta($template_id, 'ct_template_inner_content', true);

		if ( $all_posts ||  $is_catch_all) {
			
			$query_posts 	= array(); 
			$post_types 	= get_post_types( $post_type_args, 'objects' );
			$exclude_types 	= array( "ct_template", "nav_menu_item", "revision" );
			
			foreach ( $post_types as $post_type ) {
				if ( in_array ( $post_type->name, $exclude_types ) ) {
					continue;
				}
				$query_posts[] = $post_type->name;
			}

			// add a "page" post type in case it was removed with "publicly_queryable" check
			if (!in_array("page", $query_posts)) {
				$query_posts[] = "page";
			}

			$args['post_type'] = $query_posts;
		}
		else {

			// Post types
			$post_types = get_post_meta( $template_id, 'ct_template_post_types', true );

			// don't query if there is no posts
			if ( !$post_types ) {
				return false;
			}

			$post_status = array( 'publish' );

			if ( in_array("attachment", $post_types ) ) {
				$post_status[] = 'inherit';
			}

			// add to args
			$args['post_type'] 		= $post_types;
			$args['post_status'] 	= $post_status;

			// Exclude IDs
			$exclude_ids = get_post_meta( $template_id, 'ct_template_exclude_ids', true );
			$exclude_ids = explode(",", $exclude_ids);

			// add to args
			if ( $exclude_ids ) {
				$args['post__not_in'] 	= $exclude_ids;
			}

			// taxonomies
			$use_taxonomies = get_post_meta( $template_id, 'ct_use_template_taxonomies', true );
			if ( $use_taxonomies ) {	

				$template_taxonomies = get_post_meta( $template_id, 'ct_template_taxonomies', true );

				if ( $template_taxonomies ) {
					
					$args['tax_query']['relation'] = 'AND';

					foreach ( $template_taxonomies['names'] as $key => $value ) {

						// 'post_tag' taxonomy set as 'tag' in the template metabox settings
						// we cannot rename it for backward compatibilty
						// use workaround below
						if ( $value == 'tag' ) {
							 $value = 'post_tag';
						}
						
						$args['tax_query'][] = array(
												'taxonomy' => $value,
												'field'    => 'id',
												'terms'    => $template_taxonomies['values'][$key],
											);
					}
				}
				
			}

			$if_parent_among = get_post_meta( $template_id, 'ct_template_apply_if_post_of_parents', true );
			
			if ( $if_parent_among ) {	

				$parents = get_post_meta( $template_id, 'ct_template_post_of_parents', true );
				
				if(is_array($parents)) {
					$args['post_parent__in'] = $parents;
				}
			}
		}

		//var_dump($args);

		$args['orderby'] = 'date';
		$args['order'] = 'DESC';

		// Make a query
		$query = new WP_Query( $args );
		$posts = $query->posts;
		
		// append permalinks as well
		foreach($posts as $key => $postitem) {
			$posts[$key]->permalink = get_permalink($postitem->ID);
		}
		
		//var_dump($posts);

		// save in cache
		wp_cache_set("ct_archive_template_posts_" . $template_id, $posts );
	}

	/** 
	 * Check for previous/next post query
	 *
	 * @deprecated 0.3.3
	 */
	
	if ( $current_post_id && $option ) {
		
		foreach ( $posts as $key => $post ) {
			// find current post
			if ( $current_post_id == $post->ID ) {
				
				if ( $option == 'previous' ) {
					$new_post_key = $key - 1;
				}

				if ( $option == 'next' ) {
					$new_post_key = $key + 1;
				}
			}
		}

		// loop posts
		if ( $new_post_key < 0 ) {
			$new_post_key = sizeof( $posts ) - 1;
		}

		if ( $new_post_key > sizeof( $posts ) - 1 ) {
			$new_post_key = 0;
		}
	}

	// if not loading any special post return all posts ids and titles
	if ( !$current_post_id ) {
		
		$posts_ids_titles = array();
		$posts_list = array();
		
		$count = 0;
		foreach ( $posts as $post ) {
			
			// unless it is a woocommerce shop page, which is essentially a redirect to products archive
			if(class_exists('WooCommerce') && get_option( 'woocommerce_shop_page_id' ) == $post->ID)
				continue;

			if(empty($post->post_title)) {
				continue;
			}
			
			$posts_list[] = array (
					"id"	=> $post->ID,
					"title" => $post->post_title
				);

			$count++;
		}

		// return list of all posts for preview
		return array (
					"postsList" => $posts_list
				);
	}
	else {

		foreach ( $posts as $post ) {
			if ( $post->ID == $current_post_id ) {

				// update some values
				$filtered_post = ct_filter_post_object( $post, true ); // since its a template preivew, we dont want anything to do with the post's custom view
				
				// return post data
				return array (
							"postData" => $filtered_post
						);
			}
		}		
	}

	return array();
}


/**
 * Get template's terms
 *
 * @since  0.2.2
 * @author Ilya K.
 */

function ct_get_templates_term( $template_id, $term_id = false, $option = false, $catch_all = false ) {

	if ( ! is_numeric( $template_id ) || $template_id <= 0 ) {
		return array();
	}

	$new_term_key = 0;

	// look in cache
	$terms = wp_cache_get("ct_archive_template_terms" . $template_id );
	
	if ( !$terms || !$terms[$new_term_key] ) {
		$limit = get_option('oxygen_vsb_preview_dropdown_limit');
		$post_type_args = array();
		if ( get_option('oxygen_vsb_preview_dropdown_exclude_non_public') ) {
			$post_type_args["publicly_queryable"] = true;
		}

		$count = 0;
		// get all saved terms
		// post types
		$all_archives = get_post_meta( $template_id, 'ct_template_all_archives', true );
		$is_catch_all = $catch_all || get_post_meta( $template_id, 'ct_template_index', true ) || get_post_meta($template_id, 'ct_template_inner_content', true);


		$post_types = array();
		$authors = array();
		if( $all_archives || $is_catch_all) {

			// get all taxonomies and their terms, categories, tags
			$taxonomies = get_taxonomies(array('_builtin' => false));

			foreach($taxonomies as $key => $val) {
				$taxonomies[$key] = 'all_'.$val;
			}

			$taxonomies[] = 'all_categories';
			$taxonomies[] = 'all_tags';
			
			if(!$limit || $count < $limit) {
				// get all post types
				$custom_post_types = get_post_types($post_type_args);
				$exclude_types 	= array( "ct_template", "nav_menu_item", "revision", "page" );
			
				foreach($custom_post_types as $item) {
					
					if($limit && $count >= $limit) {
						break;
					}

					if(!in_array($item, $exclude_types)) {
						$post_types[] = $item;
						$count++;
					}
				}
			}

			if(!$limit || $count < $limit) {
				// get all authors

				$all_authors = oxygen_get_authors();
				
				foreach ( $all_authors as $author )  {

					if($limit && $count >= $limit) {
						break;
					}

					$authors[] = $author->ID;
					$count++;
				}
			}
		}

		if(!$limit || $count < $limit) {

			if(!$all_archives)	{
				$taxonomies = array();	
				$use_taxonomies = get_post_meta( $template_id, 'ct_template_apply_if_archive_among_taxonomies', true );

				if($use_taxonomies) {
					$taxonomies = get_post_meta( $template_id, 'ct_template_archive_among_taxonomies', true );
				}

				$post_types = array();
				$use_posttypes = get_post_meta( $template_id, 'ct_template_apply_if_archive_among_cpt', true );

				if($use_posttypes) {
					$post_types = get_post_meta( $template_id, 'ct_template_archive_post_types', true );

					// if all post types, then gather all post types into the array
					if((!$limit || $count < $limit) && in_array('all_posttypes', $post_types)) {

						$custom_post_types = get_post_types($post_type_args);
						$exclude_types 	= array( "ct_template", "nav_menu_item", "revision", "page" );
						foreach($custom_post_types as $item) {

							if($limit && $count >= $limit) {
								break;
							}

							if(!in_array($item, $exclude_types)) {
								$post_types[] = esc_attr( $item );
								$count++;
							}
						}


					}
				}

				$authors = array();
				$use_authors = get_post_meta( $template_id, 'ct_template_apply_if_archive_among_authors', true );

				if($use_authors) {

					$authors = get_post_meta( $template_id, 'ct_template_authors_archives', true );


					if((!$limit || $count < $limit) && in_array('all_authors', $authors)) {
						$authors = array();

						$all_authors = oxygen_get_authors();

						foreach ( $all_authors as $author ) {
							
							if($limit && $count >= $limit) {
								break;
							}

							$authors[] = esc_attr( $author->ID );
							$count++;
						}
					}
				}
			}
		}

		// Other Archives
		 
	 	// index
		$template_index 		= get_post_meta( $template_id, 'ct_template_index', true );

		// front page
		$template_front_page 	= get_post_meta( $template_id, 'ct_template_front_page', true );

		// blog posts
		$template_blog_posts 	= get_post_meta( $template_id, 'ct_template_blog_posts', true );

		// date archive
		$template_date_archive 	= $all_archives || get_post_meta( $template_id, 'ct_template_date_archive', true );

		// search result
		$template_search_page 	= get_post_meta( $template_id, 'ct_template_search_page', true );

		// 404 page
		//$template_404_page 		= get_post_meta( $template_id, 'ct_template_404_page', true );


		/**
		 * Collect all terms to $terms array
		 */
		
		$terms = array();

		ct_add_term_posts( $terms, 'post_types', $post_types );

		ct_add_term_posts( $terms, 'authors', $authors );
		

		// Custom taxonomies
		
		if ( is_array( $taxonomies ) ) {

			if((!$limit || $count < $limit) &&  (in_array("all_categories", $taxonomies) || in_array("all_taxonomies", $taxonomies) || $is_catch_all)) {
				$categories = array();
				$categories_list = get_categories(array(
					'number' => $limit 
				));

				foreach ( $categories_list as $category ) {
					if($limit && $count >= $limit) {
						break;
					}
					$categories[] = $category->term_id;
					$count++;
				}

				ct_add_term_posts( $terms, 'category', $categories );
			}
			
			if((!$limit || $count < $limit) &&  (in_array("all_tags", $taxonomies) || in_array("all_taxonomies", $taxonomies) || $is_catch_all)) {
				$tags = array();
				$tags_list = get_tags(array(
					'hide_empty' => 0, 
					'number' => $limit 
				));
				
				foreach ( $tags_list as $tag ) {
					if($limit && $count >= $limit) {
						break;
					}
					$tags[] = $tag->term_id;
					$count++;
				}

				ct_add_term_posts( $terms, 'post_tag', $tags );
			}
			
			// check "all_{tax_name}" option
			$args = array(
				"_builtin" => false
			);

			$taxonomies_list = get_taxonomies( $args, 'object' );

			$terms_names = array();
			
			if((!$limit || $count < $limit)) {
				foreach ( $taxonomies_list as $tax ) {

					if ((!$limit || $count < $limit) && (in_array( "all_".$tax->name, $taxonomies ) || in_array("all_taxonomies", $taxonomies) || $is_catch_all )) {

						$args = array(
							'hide_empty' 	=> 0,
							'taxonomy' 		=> $tax->name,
							'number' 		=> $limit 
						);

						// add all $tax terms
						$categories_list = get_categories( $args );
						foreach ( $categories_list as $category ) {
							if($limit && $count >= $limit) {
								break;
							}
							$terms_names[$tax->name][] = $category->term_id;
							$count++;
						}
					}
				}
			}

			// add individual terms 
			if(!$limit || $count < $limit) {
				foreach ( $taxonomies as $tax_id ) {
					// exclude "all_{tax_name}" options from list
					if ((!$limit || $count < $limit) && strpos( $tax_id, "all_") !== 0) {
						
						if($limit && $count >= $limit) {
							break;
						}

						$term = get_term( $tax_id );

						if ( !isset($terms_names[$term->taxonomy] ) ) {
							$terms_names[$term->taxonomy] = array();
						}
						
						if ( !in_array( $tax_id, $terms_names[$term->taxonomy] ) ) {
							$terms_names[$term->taxonomy][] = $tax_id;
							$count++;
						}
					}
				}
			}

			// add terms
			
			foreach ( $terms_names as $name => $ids ) {
				ct_add_term_posts( $terms, $name, $ids );
			}
			
		}

		// Other Archives 
		
		if ((!$limit || $count < $limit) && ($template_index || $is_catch_all)) {
			ct_add_term_posts( $terms, "index");
			$count++;
		}
		if ((!$limit || $count < $limit) && ($template_front_page || $is_catch_all)) {
			ct_add_term_posts( $terms, "front_page");
			$count++;
		}
		if ((!$limit || $count < $limit) && ($template_date_archive || $is_catch_all)) {
			ct_add_term_posts( $terms, "date_archive");
			$count++;
		}
		if ((!$limit || $count < $limit) && ($template_blog_posts || $is_catch_all)) {
			ct_add_term_posts( $terms, "blog_posts");
			$count++;
		}
		if ((!$limit || $count < $limit) && ($template_search_page || $is_catch_all)) {
			ct_add_term_posts( $terms, "search_page");
			$count++;
		}
		// if ( $template_404_page ) {
		// 	ct_add_term_posts( $terms, "404_page");
		// }

		// Filter all posts data
		

		foreach ( $terms as $term_key => $term ) {
			if ( is_array( $term["term_posts"] ) ) {
				foreach ( $term["term_posts"] as $post_key => $post ) {
					$terms[$term_key]["term_posts"][$post_key] = ct_filter_post_object( $post );
				}
			}
		}

		// Save to cache
		wp_cache_set("ct_archive_template_terms" . $template_id, $terms );
	}

	// if not loading any special term return all terms
	if ( !$term_id ) {
		
		$terms_list = array();
		
		//$limit = get_option('oxygen_vsb_preview_dropdown_limit');
		//$count = 0;

		foreach ( $terms as $term ) {
			
			//if( !$limit || !is_numeric($limit) || $count < intval($limit) ) {
				$terms_list[] = array (
									"id"	=> $term["term_id"],
									"title" => $term["term_name"]
								);
			//}

			//$count++;
		}


			

		// return list of all posts for preview
		return array (
					"termsList" => $terms_list
				);
	}
	else {

		foreach ( $terms as $term ) {

			if ( $term["term_id"] == $term_id ) {

				// return post data
				return array (
							"postData" => $term
						);
			}
		}		
	}

	return array();
}


/**
 * Add all term posts to $posts variable
 *
 * @since  0.2.2
 * @author Ilya K.
 */

function ct_add_term_posts( &$terms, $taxonomy_name, $term_ids = false ) {

	if ( $term_ids !== false ) {

		if ( ! is_array( $term_ids ) ) {
			return;
		}

		// get term posts
		foreach ( $term_ids as $term_id ) {

			// lets get archive links for each term
			$permalink = '';
			
			if ( $taxonomy_name == "post_types" ) {

				/**
				 *	No, we do not need preview for an archive of all pages
				 *	because, there is no such link for the WP frontend.
				 */	 
				if( $term_id == 'page' )
					continue;

				$term 		= get_post_type_object($term_id);
				$term_name 	= $term->label;
				
				$args = array(
					'post_type' 	=> $term_id, // post types here like 'product' or 'post'
					'post_status' 	=> 'publish',
				);

				if($term_id == 'post')
					$permalink = get_option( 'page_for_posts' ) ? get_page_link( get_option( 'page_for_posts' ) ) : get_home_url();
				else
					$permalink = get_post_type_archive_link($term_id);
			}
			elseif ( $taxonomy_name == "authors" ) {
				
				$term = get_user_by( 'id', $term_id );
				$term_name = $term->user_nicename;

				$args = array(
					'post_type' 	=> 'any', // post types here like 'product' or 'post'
					'post_status' 	=> 'publish',
					'post_author' 	=> $term_id
				);

				$permalink = get_author_posts_url($term_id);

				$term_id = "author_".$term_id; // add author identifier
			}
			else {

				// get term data
				$term = get_term_by('id', $term_id, $taxonomy_name);
				$term_name = $term->name;
				
				$args = array(
					'post_type' 	=> 'any',
					'post_status' 	=> 'publish',
					'tax_query' 	=> array (
											array (
												'taxonomy' 	=> $taxonomy_name,
												'terms' 	=> $term_id
											)
										)
					);

				if( $taxonomy_name == "category" ) {
					$permalink = get_category_link($term_id);
				}
				else {
					$permalink = get_term_link(intval($term_id));
				}
			}

			// query posts
			$query = new WP_Query( $args );

			// convert to array
			$term_posts = (array) $query->posts;
			
			$terms[] = array (
					"term_id" 		=> $term_id,
					"term_name" 	=> $term_name,
					"term_posts" 	=> $term_posts,
					"term" 			=> json_encode($term),
					"permalink"		=> $permalink
				);
		}
	}
	// Other archives (Date, Blog posts, Index...)
	else {
		
		$term_id = $taxonomy_name;

		if ( $taxonomy_name == "index" ) {
			
			$term_name 	= __("Index", "oxygen");
			$args 		= array();
			$permalink 	= get_home_url(null, '/'); //????
		}
		
		if ( $taxonomy_name == "date_archive" ) {
			
			$term_name 	= __("Date Archive", "oxygen") . date(" (Y/M)");
			$args 		= array();
			$permalink 	= get_month_link("",""); // current year, current month
		}

		if ( $taxonomy_name == "front_page" ) {

			if ( get_option( 'page_on_front' ) ) {
				
				$term_name 	= __("Front Page", "oxygen");
				$args 		= array();
				$permalink 	= get_permalink( get_option( 'page_on_front' ) );
			}
			else {
				return false;
			}
		}

		if ( $taxonomy_name == "blog_posts" ) {
			
			if ( get_option( 'page_for_posts' ) ) {

				$term_name 	= __("Blog Posts", "oxygen");
				$args 		= array('post_type'=>'post'); // unless the post type is specified, it will also load component templates and any other custom post types
				$permalink 	= get_page_link( get_option( 'page_for_posts' ) );
			}
			else {
				return false;
			}
		}

		if ( $taxonomy_name == "search_page" ) {
			
			$term_name 	= __("Search Page", "oxygen");
			$args 		= array();
			$permalink 	= get_search_link("post");
		}

		if ( $taxonomy_name == "404_page" ) {
			
			$term_name 	= __("404 Page", "oxygen");
			$args 		= array();
			$permalink 	= get_home_url( null, "absoltely_incredible_not_possible_to_exist_in_real_world_url_that_will_always_output_404_error_page" );
		}

		// query posts
		$query = new WP_Query( $args );

		// convert to array
		$term_posts = (array) $query->posts;

		if( empty( $term ) ) $term = array(); // To avoid PHP Notice: Undefined variable

		$terms[] = array (
				"term_id" 		=> $term_id,
				"term_name" 	=> $term_name,
				"term_posts" 	=> $term_posts,
				"term" 			=> json_encode($term),
				"permalink"		=> $permalink
			);
	}
}


/**
 * Go trough the post object and replace some values
 *
 * @since  0.2.0
 * @author Ilya K.
 * @return Object [Modified WP_Post]
 */

function ct_filter_post_object( $post_object, $no_custom_view = false, $preview_post_id = false ) {

	// update post author id to nicename
	$post_object->post_author = get_the_author_meta("user_nicename", $post_object->post_author );

	// get components tree based on shortcodes
	/* New Way */
	
	$json = get_post_meta(  $post_object->ID, 'ct_builder_json', true );
	$tree = json_decode($json, true);
	
	if ($tree) {
		$post_object->post_tree = $tree['children'];
		
		global $oxygen_doing_oxygen_elements;
		$oxygen_doing_oxygen_elements = true;
		$post_object->post_content = do_oxygen_elements($tree);
	}
	else {
		$shortcodes = get_post_meta($post_object->ID, "ct_builder_shortcodes", true);
		$tree 		= parse_shortcodes($shortcodes);
		$post_object->post_tree = $tree['content'];
	}


	// check if content is made in builder or not
	if ( ! isset($tree['is_shortcode']) || ! $tree['is_shortcode'] ) {
		// add filter for regular text posts
		// add_filter("the_content", "wpautop");
		// $post_object->post_content = apply_filters("the_content", $post_object->post_content );
		$post_object->post_content = do_shortcode($post_object->post_content );
	}
	elseif( !$no_custom_view ) {
		
		// update post content with shortcodes rendered
		// $post_object->post_content = apply_filters("the_content", $shortcodes );

		if($preview_post_id !== false && is_numeric($preview_post_id)) {
			
			global $oxygen_preview_post_id, $wp_query;

			$oxygen_preview_post_id = $preview_post_id;

            if(isset($preview_post_id) && is_numeric($preview_post_id)) {
                $query_vars = array('p' => $preview_post_id, 'post_type' => 'any');
                $wp_query = new WP_Query($query_vars);
            }

		}

		if ($shortcodes) {
			$post_object->post_content = ct_do_shortcode($shortcodes);
		}

	}
	
	//remove_filter("the_content", "wpautop");
	
	// fix for oEmbed stuff
	global $wp_embed;

	// Add the fetched posts ID and add it to the global object
	$wp_embed->post_ID = $post_object->ID;

	// Execute the [embed] shortcode
	$wp_embed->run_shortcode( $post_object->post_content );

	// Execute the oEmbed handlers for plain links on the own line
	$wp_embed->autoembed( $post_object->post_content );

	return $post_object;
}
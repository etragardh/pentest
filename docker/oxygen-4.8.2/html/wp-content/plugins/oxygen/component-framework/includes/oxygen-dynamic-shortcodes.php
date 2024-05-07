<?php

/*
 * Oxygen Dynamic Shortcodes
 * Author:      Louis
*/

class Oxygen_VSB_Dynamic_Shortcodes {

	private $query;

	function oxygen_vsb_add_shortcode() {
		add_shortcode('oxygen', array($this, 'oxygen_vsb_dynamic_shortcode'));
		add_action( 'oxygen_enqueue_iframe_scripts', array( $this, 'oxygen_vsb_custom_dynamic_data_init' ) );
	}

	function init_custom_dynamic_data() {
		$this->custom_dynamic_datas = apply_filters( 'oxygen_custom_dynamic_data', array() );
	}

	function oxygen_vsb_dynamic_shortcode($atts, $content = null) {


		// validation will go here
		global $oxygen_signature;

		if(!$oxygen_signature->verify_signature( 'oxygen', $atts, null )) {
			return "";
		}

		// replace single quotes in atts
		foreach($atts as $key => $item) {
			$atts[$key] = str_replace('__SINGLE_QUOTE__', "'", $item);
		}

		global $wp_query;
		global $oxy_vsb_use_query;

		if(isset($oxy_vsb_use_query) && is_object($oxy_vsb_use_query)) {

			$this->query = $oxy_vsb_use_query;


		} else {

			global $oxygen_preview_post_id;

			if(isset($oxygen_preview_post_id) && is_numeric($oxygen_preview_post_id)) {
				$query_vars = array('p' => $oxygen_preview_post_id, 'post_type' => 'any');
			}
			else {
				$query_vars = $wp_query->query_vars;	
			}

			$this->query = new WP_Query($query_vars);

			if(!is_page() && $this->query->have_posts()) {
				$this->query->the_post();
			}
		}
		
		$handler = 'oxygen_'.$atts['data'];

		if( substr( $atts['data'], 0, 7 ) == "custom_" ) {
			$handler = 'oxygen_custom';
		}

		if (method_exists($this, $handler)) {

			$ksesDynamicData = apply_filters("oxygen_vsb_kses_dynamic_data", false, $handler);

			$dirtyOutput = call_user_func(array($this, $handler), $atts);

			$output = $ksesDynamicData ? wp_kses_post( $dirtyOutput ) : $dirtyOutput;

		} else {

			return "No such function ".$handler;

		}

		/* if link parameter is set, wrap output with an <a> tag and set the link URL to whatever is returned by the function with the name of the value of the link parameter */
		if (isset($atts['link'])) {
		
			$link_handler = 'oxygen_'.$atts['link'];

		if (isset($link_handler) && method_exists($this, $link_handler)) {
			$link_output = call_user_func(array($this, $link_handler), $atts);

				if ($link_output) {
					$output = "<a href='".$link_output."'>".$output."</a>";
				} 
			} 
		}

		$output = apply_filters('oxygen_vsb_after_oxy_shortcode_render', $output);

		if(!isset($oxy_vsb_use_query) || !is_object($oxy_vsb_use_query)) {
			wp_reset_query();
		}

		return $output;

	}

	function oxygen_acfreparray($atts) {
		
		if(class_exists('ACF')) {

			// get into the loop for ACF Settings Page Repeater's first instance. Didn't find a better place to out this fix
			if(!get_row() && have_rows($atts['repeater'], 'option', true)) {
				the_row();
			};

			return get_sub_field($atts['field']);
		}
		
		return '';
	}

	function oxygen_metaboxreparray($atts) {

		$value = "";

		if(class_exists('RWMB_Loader')) {

			global $meta_box_current_group_fields;

			if ( isset($atts['field']) && isset($meta_box_current_group_fields[$atts['field']]) ) {

				$group = rwmb_get_field_settings( $atts['repeater'] );

				if ( isset($group['fields']) && is_array($group['fields']) ) {
					foreach ($group['fields'] as $field) {
						if ( isset( $field['id'] ) && $field['id'] == $atts['field'] ) {

							$value = $meta_box_current_group_fields[$atts['field']];
							
							switch ( $field['type'] ) {
								case 'image':
								case 'image_upload':
								case 'image_advanced':
									if (is_array($value)) {
										$value = wp_get_attachment_image_url($value[0]);
									}
									break;
								
								default:
									if (is_array($value)) {
										$value = implode(",", $value);
									}
									break;

							}
						}
					}
				}
			}
		}
		
		return $value;
	}

	function oxygen_phpfunction($atts) {

		$my_function = $atts['function'];

		$args = (isset($atts['arguments'])) ? $atts['arguments'] : "";
		$args = explode(',', $args);

		if(function_exists($my_function)) {
			
			return call_user_func_array($my_function, $args);
			
		} else {
			return 'function does not exist';
		}

	}

	function oxygen_title($atts) {
		return get_the_title();
	}

	function oxygen_id($atts) {
		return get_the_ID();
	}

	function oxygen_post_type($atts) {
		global $post;
		return $post->post_type;
	}

	function oxygen_post_terms($atts) {

		$separator = isset($atts['separator']) ? $atts['separator'] : ", ";
		$taxonomy = isset($atts['taxonomy']) ? $atts['taxonomy'] : "";

		$term_obj_list = get_the_terms( get_the_ID(), $taxonomy );
		$terms_string = join($separator, wp_list_pluck($term_obj_list, 'slug'));

		return $terms_string;
	
	}

	function oxygen_content($atts) {

		global $post;

		if($post) {
			// we need to stop the first very iteration of the content, in case the content is a guttenberg layout, containing itself
			$is_gutenberg = get_post_meta($post->ID, 'ct_oxygenberg_full_page_block', true);

			if($is_gutenberg) {
				return '';
			}
		}

		// This is for the admin section, Look up in the function call history to see if we are rendering this shortcode from within a gutenberg block. If so, return an empty string.
		if(is_admin()) {
			$dbt = debug_backtrace();
			foreach ($dbt as $debug_item) if( isset($debug_item['function']) && ( $debug_item['function'] == 'render_gutenberg_block' || $debug_item['function'] == 'generate_gutenberg_script' ) ) { return ''; };
		}

		ob_start();
		// When called "do_shortcode" from within the edit post/page in WordPress backend, we are not in the loop and the_content() returns an empty string
		if( !in_the_loop() ) {
			// When permalinks are set to "plain", global $post variable is null
			if( is_null( $post ) && !empty( $_GET[ 'post' ] ) ) $post = get_post( filter_var( $_GET[ 'post' ], FILTER_SANITIZE_NUMBER_INT) );
			// Simulate a loop
			setup_postdata( $post, null, false );
		}
		the_content();
		return ob_get_clean();

	}

	function oxygen_archive_title($atts) {
		return get_the_archive_title();
	}

	function oxygen_archive_description($atts) {
		return get_the_archive_description();
	}

	function oxygen_excerpt($atts) {
		return get_the_excerpt();
	}

	function oxygen_terms($atts) {
		$separator = isset($atts['separator']) ? $atts['separator'] : "";
		$taxonomy = isset($atts['taxonomy']) ? $atts['taxonomy'] : "";

		if(!taxonomy_exists($taxonomy)) {
			return '';
		}

		return get_the_term_list(get_the_ID(), $taxonomy, null, $separator, null );
	}

	function oxygen_featured_image($atts) {

		$size = isset($atts['size'])?$atts['size']:'post-thumbnail';


		if (strpos($size, ",")!==FALSE) {
			$size = explode(',', $size);
		}

		// user can either pass size as 200,100, i.e. width,height, or a registered thumbnail size, i.e. "large" or whatever

		$thumbnail = get_the_post_thumbnail_url(null, $size);

		if (!$thumbnail) {
			return isset($atts['default'])?$atts['default']:'';
		} else {
			return $thumbnail;
		}

	}

	function oxygen_featured_image_id($atts) {
		return get_post_thumbnail_id();
	}

	function oxygen_featured_image_title($atts) {
		return @get_post(get_post_thumbnail_id())->post_title;
	}

	function oxygen_featured_image_caption($atts) {
		return @get_post(get_post_thumbnail_id())->post_excerpt;
	}

	function oxygen_featured_image_alt($atts) {
	    return @get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true );
	}


	function oxygen_comments_link($atts) {
	    return get_comments_link();
	}





	function oxygen_comments_number($atts) {
		$zero = $atts['zero'];
		$one = $atts['one'];
		$more = $atts['more'];

		ob_start();

		if ($zero && $one && $more) {
			comments_number( $zero, $one, $more );
		} else {
			comments_number();
		}

		return ob_get_clean();
	}

	function oxygen_meta($atts) {
		return get_post_meta(get_the_ID(), $atts['key'], true);
	}

	function oxygen_date($atts) {
		$format = isset($atts['format'])?$atts['format']:'';
		return get_the_date($format);
	}

	function oxygen_permalink($atts) {
		return get_permalink();
	}

	function oxygen_author($atts) {
		return get_the_author();
	}

	function oxygen_author_website_url($atts) {
		return get_the_author_meta('url');
	}

	function oxygen_author_posts_url($atts) {
		return get_author_posts_url(get_the_author_meta('ID'));
	}

	function oxygen_author_bio($atts) {
		return get_the_author_meta('description');
	}

	function oxygen_author_pic($atts) {
		return get_avatar_url(get_the_author_meta('email'), $atts);
	}

	function oxygen_author_meta($atts) {

		if ( isset( $atts['meta_key'] ) ) {
			$key = $atts['meta_key'];
		}
		elseif ( isset( $atts['key'] ) ) {
			$key = $atts['key'];
		}
		else {
			$key = "";
		}

		return get_the_author_meta($key);
	}

	function oxygen_bloginfo($atts) {
		return get_bloginfo($atts['show']);
	}



	function oxygen_get_userdata($id) {
		if (!$id) {
			$id = get_current_user_id();
		}

		$userdata = get_userdata($id);

		return $userdata;
	}


	function oxygen_user($atts) {

		$id = isset($atts['id']) ? $atts['id'] : "";

		$userdata = $this->oxygen_get_userdata($id);
		if($userdata)
			return $userdata->display_name ? $userdata->display_name : $userdata->user_nicename;
		else
			return '';

	}

	function oxygen_user_website_url($atts) {

		$userdata = $this->oxygen_get_userdata($atts['id']);

		if($userdata)
			return $userdata->user_url;
		else
			return '';

	}

	function oxygen_user_bio($atts) {

		$userdata = $this->oxygen_get_userdata($atts['id']);

		if($userdata)
			return $userdata->user_description;
		else
			return '';

	}

	function oxygen_user_pic($atts) {

		$userdata = $this->oxygen_get_userdata($atts['id']);
		
		return get_avatar_url($userdata->user_email, $atts['size']);

	}

	function oxygen_user_meta($atts) {

		$id = isset($atts['id']) ? $atts['id'] : "";

		$userdata = $this->oxygen_get_userdata($id);

		if ( isset( $atts['meta_key'] ) ) {
			$key = $atts['meta_key'];
		}
		elseif ( isset( $atts['key'] ) ) {
			$key = $atts['key'];
		}
		else {
			$key = "";
		}

		return get_user_meta($userdata->ID, $key, true);

	}


	function oxygen_vsb_custom_dynamic_data_init() {
		wp_localize_script( 'ct-angular-main', 'custom_dynamic_data', array(
			'data' => apply_filters( 'oxygen_custom_dynamic_data', array() )
		) );
	}

	function oxygen_custom( $atts ) {
		$name = explode( '_', $atts['data'], 2 )[1];
		foreach ( $this->custom_dynamic_datas as $custom_dynamic_data ) {
			if( $custom_dynamic_data['data'] == $name && is_callable( $custom_dynamic_data['handler'] ) ) {
				return call_user_func( $custom_dynamic_data['handler'], $atts );
			}
		}
		return '';

	}
}

$oxygen_VSB_Dynamic_Shortcodes = new Oxygen_VSB_Dynamic_Shortcodes();

$oxygen_VSB_Dynamic_Shortcodes->oxygen_vsb_add_shortcode();

add_action("wp", array($oxygen_VSB_Dynamic_Shortcodes, "init_custom_dynamic_data"));
<?php
	// TODO review the security aspect

	if (!is_user_logged_in() || !oxygen_vsb_current_user_can_access()) {
	    die();
		}

	$nonce  	= $_REQUEST['nonce'];
	$post_id 	= $_REQUEST['post_id'];

	// check nonce
	if ( ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
	    // This nonce is not valid.
	    die( 'Security check' );
	}
		
	// get passed parameters
	$name 	 = $_REQUEST['shortcode_name'];

	$options = file_get_contents('php://input');
	$options = json_decode( $options, true );

	if ( ! $options ) {
		_e("Can't get shortcode options", "component-theme");
		die();
	};

	$shortcode = $executed_shortcode = "";

	// Handle WP Embed
	if ( $name == "embed" ) {

		global $wp_embed;
		echo wp_oembed_get( $options['original']['url'] );
		die();
	}

	if( $name == 'ct_toolset_view' ) {

        $shortcode = '[wpv-view name="' . $options['original']['view'] . '"]';
        $executed_shortcode = do_shortcode( $shortcode );

    } else if ( $options['original']['full_shortcode'] != "" ) {

    	$shortcode = $options['original']['full_shortcode'];

		if(stripos($shortcode, '[oxygen')  !== false) {
			$shortcode = ct_sign_oxy_dynamic_shortcode(array($shortcode));
		}

    	if( isset( $options['queryOptions'] ) ) {
    		global $oxy_vsb_use_query, $oxygen_vsb_components;
            
            $old_query = false;

            if($oxy_vsb_use_query) {
                $old_query = $oxy_vsb_use_query;
            }

            $query = $oxygen_vsb_components['repeater']->setQuery($options['queryOptions']);
           
            $oxy_vsb_use_query = $query; //$this->query;
            
            if ($query->have_posts()) {
                
                $query->the_post();

            }

            if(class_exists('ACF') && $options['acfRepeaterFields'] && is_array($options['acfRepeaterFields'])) {

            	foreach($options['acfRepeaterFields'] as $acfRepeaterField) {

            		if(have_rows($acfRepeaterField) ) {
            			the_row();
            		}
            	}
            }

			if(class_exists('RWMB_Loader') && isset($options['metaboxGroupFields']) && !empty($options['metaboxGroupFields'])) {
                $group = rwmb_meta($options['metaboxGroupFields'][0]);
                global $meta_box_current_group_fields;
				$meta_box_current_group_fields = $group[0];
            }
            
            $executed_shortcode = do_shortcode( $shortcode );

            
            
            // reset query to previous state
            if($old_query) {
                
                $oxy_vsb_use_query = $old_query;

                $oxy_vsb_use_query->reset_postdata();
            }
    	}
    	else {
			$executed_shortcode = do_shortcode( $shortcode );
		}

	}
	else {

		// handle shortcode component
		if ( $name == "ct_shortcode" && isset($options['original']['shortcode_tag']) ) {

			$name = $options['original']['shortcode_tag'];
			unset( $options['original']['shortcode_tag'] );
		}
		
		$shortcode = "[" . $name . " ";

		foreach ( $options['original'] as $param => $value) {
			$shortcode .= "$param=\"$value\" ";
		}

		$shortcode .= "]";

		$executed_shortcode = do_shortcode( $shortcode );
	}

	if ( $executed_shortcode == $shortcode ) {
		_e("Can't execute the shortcode. Make sure tag and parameters are correct.", "component-theme");
	}
	else {
		// initilize global $wp_styles if not exist
		wp_styles();

		// print scripts and styles if added by shortcode
		do_action("wp_print_footer_scripts");
		
		do_action("wp_footer");

		?>
		<div id="ct-shortcode-links-scripts"><?php
		echo $executed_shortcode;
		?></div><?php
	}

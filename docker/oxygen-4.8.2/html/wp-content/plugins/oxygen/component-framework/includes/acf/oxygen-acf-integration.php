<?php

/**
 * Advanced Custom Fields integration for Oxygen
 *
 * @since 2.1
 */
class oxygen_acf_integration {

	private $options;

	function __construct(){
		add_action( 'init', array( $this, 'init' ), 0 );
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
	}

	function init() {
		if( !defined('ACF_VERSION') || !class_exists( 'acf' ) || intval( explode( '.', ACF_VERSION )[0] ) < 5 ) {
			// do nothing 
		} else {
			$this->options = array( 'google_maps_key' => get_option( 'oxygen_vsb_google_maps_api_key' ) );
			add_filter( 'oxygen_custom_dynamic_data', array( $this, 'init_dynamic_data' ), 10, 1 );
			add_filter( 'acf/settings/google_api_key', array( $this, 'set_google_maps_key' ) );
			if( !empty( $_GET['oxy_download_acf_file'] ) ) $this->download_protected_file( intval( $_GET['oxy_download_acf_file'] ) );
		}
	}

	function load_textdomain() {
        load_plugin_textdomain( 'oxygen-acf', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
    }

	function download_protected_file( $attachment_id ) {
	    global $wpdb;
	    if( !is_user_logged_in() ) {
	        _e( 'Only logged in users are allowed to download this file', 'oxygen-acf' );
	        exit;
        }
	    // Use the standard WP attachment url instead of outputting the file directly
        wp_redirect( wp_get_attachment_url( $attachment_id ) );
        exit;
    }

	function set_google_maps_key() {
		return !empty( $this->options['google_maps_key'] ) ? $this->options['google_maps_key'] : '';
	}

	function init_dynamic_data( $dynamic_data ) {
		global $post;

		/**
		 * Check if we are editing an Oxygen template. Oxygen templates can be applied to
		 * several post_types, so we have to check which post types it is configured to render
		 * and grab the field groups related to each of them.
		 */
		$post_type = isset($post->post_type) ? $post->post_type : false;
		$template_type = isset($post->ID) ? get_post_meta($post->ID, 'ct_template_type', true) : false;

		$show_all_acf_fields = get_option("oxygen_vsb_show_all_acf_fields")=='true';

		if($show_all_acf_fields) {
            $field_groups = acf_get_field_groups();
        }
        elseif( 'ct_template' === $post_type && $template_type != 'reusable_part'){
			// We have an Oxygen template, let's see what post_types it works for
			$post_types = get_post_meta( $post->ID, 'ct_template_post_types', true );
			$field_groups = [];
			if( $show_all_acf_fields ) {
				// passing no parameter returns all ACF field groups
				$field_groups = array_merge( $field_groups, acf_get_field_groups( ) );
			} else {
				// For every post_type, grab it's ACF field groups
				if( is_array( $post_types ) ) foreach ( $post_types as $post_type_tmp ) {
					$field_groups = array_merge( $field_groups, acf_get_field_groups( array('post_type' => $post_type_tmp ) ) );
				}
 			}

			// ACF field groups can be applied to several post_types too, so we can end with duplicated field groups here
			// Let's strip out the duplicates
			$field_groups = array_unique( $field_groups, SORT_REGULAR );
		}
        else {
			// Not an Oxygen template
			$field_groups = acf_get_field_groups( array('post_id' => $post->ID ) );
		}

		// Grab each individual fields from the field groups
		$fields = array();
		foreach ( $field_groups as $field_group ) {
			$tmp = acf_get_fields( $field_group );
			$fields = array_merge( $tmp, $fields );
		}

		// Generate the settings for each field type
		$all_options = array_reduce( $fields, array( $this, "add_button" ), array() );

		if( count( $all_options ) > 0 ) {
		    array_unshift( $all_options, array('name' => __( 'Select the ACF field', 'oxygen-acf' ), 'type' => 'heading') );
            $acf_content = array(
                'name'      => __( 'Advanced Custom Field', 'oxygen-acf' ),
                'mode'       => 'content',
                // Available modes: 'content', 'custom-field', 'link' and 'image'
                'position'   => 'Post',
                // Available positions: 'Post', 'Author', 'User', 'Featured Image', 'Current User', 'Archive' 'Blog Info'
                'data'       => 'acf_content',
                'handler'    => array( $this, 'acf_content_handler' ),
                // Must be a callable
                'properties' => $all_options
            );
            $dynamic_data[]   = $acf_content;
        }

		$options_for_url = array_reduce( $fields, array( $this, "add_url_button" ), array() );

        if( count( $options_for_url ) > 0 ) {
            // Dynamic Data modal modes "custom-field", "link" and "image" are expected to return an URL

            $acf_image = array(
                'name' => __( 'Advanced Custom Field', 'oxygen-acf' ),
                'mode' => 'image',
                // Available modes: 'content', 'custom-field', 'link' and 'image'
                'position' => 'Post',
                // Available positions: 'Post', 'Author', 'User', 'Featured Image', 'Current User', 'Archive' 'Blog Info'
                'data' => 'acf_image',
                'handler' => array($this, 'acf_url_handler'),
                // Must be a callable
                'properties' => $options_for_url
            );
            $dynamic_data[] = $acf_image;

            $acf_link = array(
                'name' => __( 'Advanced Custom Field', 'oxygen-acf' ),
                'mode' => 'link',
                // Available modes: 'content', 'custom-field', 'link' and 'image'
                'position' => 'Post',
                // Available positions: 'Post', 'Author', 'User', 'Featured Image', 'Current User', 'Archive' 'Blog Info'
                'data' => 'acf_link',
                'handler' => array($this, 'acf_url_handler'),
                // Must be a callable
                'properties' => $options_for_url
            );
            $dynamic_data[] = $acf_link;

            $acf_custom_field = array(
                'name' => __( 'Advanced Custom Field', 'oxygen-acf' ),
                'mode' => 'custom-field',
                // Available modes: 'content', 'custom-field', 'link' and 'image'
                'position' => 'Post',
                // Available positions: 'Post', 'Author', 'User', 'Featured Image', 'Current User', 'Archive' 'Blog Info'
                'data' => 'acf_custom_field',
                'handler' => array($this, 'acf_url_handler'),
                // Must be a callable
                'properties' => $options_for_url
            );
            $dynamic_data[] = $acf_custom_field;
        }

        $options_for_image_id = array_reduce( $fields, array( $this, "add_image_id_button" ), array() ); 
        
        if( count( $options_for_image_id ) > 0 ) { 
            $acf_image_id_field = array(
                'name' => __( 'Advanced Custom Field', 'oxygen' ),
                'mode' => 'image-id',
                'position' => 'Post',
                'data' => 'acf_image_id',
                'handler' => array($this, 'acf_image_id_handler'),
                'properties' => $options_for_image_id
            );
            $dynamic_data[] = $acf_image_id_field;
        }

		return $dynamic_data;
	}

    function add_button( $result, $option ) {
        $invalid_url_field_types = array( 'accordion', 'message', 'repeater', 'flexible_content' );
        $properties = [];
        $is_settings_page = $this->is_settings_page_feild($option['parent']);

        switch( $option['type'] ) {
            case 'image':
                $properties[] = array(
                    'name'     => __( 'Please select what you want to insert', 'oxygen-acf' ),
                    'data'      => 'insert_type',
                    'type'      => 'select',
                    'options'   => array(
                        __( 'Image element', 'oxygen-acf' ) =>'image_element',
                        __( 'Image URL', 'oxygen-acf' ) => 'image_url',
                        __( 'Image Title', 'oxygen-acf' ) => 'image_title',
                        __( 'Image Caption', 'oxygen-acf' ) => 'image_caption'
                    ),
                    'nullval'   => 'image_element'
                );
                $properties[] = array(
                    'name'=> __( 'Size', 'oxygen-acf' ),
                    'data'=> 'size',
                    'type'=> 'select',
                    'options'=> array(
                        __( 'Thumbnail', 'oxygen-acf' ) => 'thumbnail',
                        __( 'Medium', 'oxygen-acf' ) => 'medium',
                        __( 'Medium Large', 'oxygen-acf' ) => 'medium_large',
                        __( 'Large', 'oxygen-acf' ) => 'large',
                        __( 'Original', 'oxygen-acf' ) => 'full'
                    ),
                    'nullval' => 'medium',
                    'change'=> 'scope.dynamicDataModel.width = ""; scope.dynamicDataModel.height = ""',
                    'show_condition' => "dynamicDataModel.insert_type == 'image_element'"
                );
                $properties[] = array(
                    'name' => __( 'or', 'oxygen-acf' ),
                    'type' => 'label',
                    'show_condition' => 'dynamicDataModel.insert_type == \'image_element\''
                );
                $properties[] = array(
                    'name' => __( 'Width', 'oxygen-acf' ),
                    'data' => 'width',
                    'type' => 'text',
                    'helper'=> true,
                    'change' => "scope.dynamicDataModel.size = scope.dynamicDataModel.width+'x'+scope.dynamicDataModel.height",
                    'show_condition' => "dynamicDataModel.insert_type == 'image_element'"
                );
                $properties[] = array(
                    'name' => __( 'Height', 'oxygen-acf' ),
                    'data' => 'height',
                    'type' => 'text',
                    'helper' => true,
                    'change' => "scope.dynamicDataModel.size = scope.dynamicDataModel.width+'x'+scope.dynamicDataModel.height",
                    'show_condition' => 'dynamicDataModel.insert_type == \'image_element\''
                );
                break;
            case 'text':
            case 'number':
            case 'range':
            case 'email':
            case 'password':
                $properties[] = array(
                    'name' => __( 'Include prepend and append text (if configured)', 'oxygen-acf' ),
                    'data' => 'include_prepend_append',
                    'type' => 'checkbox',
                    'value' => 'yes'
                );
                break;
            case 'select':
            case 'checkbox':
                if( isset($option[ 'multiple' ]) && $option[ 'multiple' ] != 0 ) {
                    $properties[] = array(
                        'name' => __('Separator (for multiple options fields)', 'oxygen-acf'),
                        'data' => 'separator',
                        'type' => 'text'
                    );
                }
            // No break
            case 'radio':
            case 'button_group':
                if( $option[ 'return_format' ] == 'array' ) {
                    $properties[] = array(
                        'name' => __('Display (for fields configured as Return Format = Both)', 'oxygen-acf'),
                        'data' => 'display',
                        'type' => 'select',
                        'options' => array(
                            __('Label', 'oxygen-acf') => 'label',
                            __('Value', 'oxygen-acf') => 'value'
                        ),
                        'nullval' => 'value',
                    );
                }
                break;
            case 'true_false':
                $properties[] = array(
                    'name' => __( 'If set, output custom on/off text instead of true/false', 'oxygen-acf' ),
                    'data' => 'output_custom_onoff',
                    'type' => 'checkbox',
                    'value' => 'yes'
                );
                break;
            case 'file':
                $properties[] = array(
                    'name' => __( 'Output type', 'oxygen-acf' ),
                    'data' => 'output_type',
                    'type' => 'select',
                    'options'=> array(
                        __( 'Url', 'oxygen-acf' ) => 'url',
                        __( 'Attachment ID', 'oxygen-acf' ) => 'attachment_id'
                    ),
                    'nullval' => 'url'
                );
                $properties[] = array(
                    'name' => __( 'Link', 'oxygen-acf' ),
                    'data' => 'file_link',
                    'type' => 'checkbox',
                    'value' => 'yes',
                    'show_condition' => 'dynamicDataModel.output_type == \'url\''
                );
                $properties[] = array(
                    'name' => __( 'Link text (file name will be used if left empty)', 'oxygen-acf' ),
                    'data' => 'link_text',
                    'type' => 'text',
                    'show_condition' => 'dynamicDataModel.file_link == \'yes\''
                );
                $properties[] = array(
                    'name' => __( 'Prevent file downloads from logged-out users', 'oxygen-acf' ),
                    'data' => 'protect_file',
                    'type' => 'checkbox',
                    'value' => 'yes',
                    'show_condition' => 'dynamicDataModel.file_link == \'yes\''
                );
                break;
            case 'page_link':
                $properties[] = array(
                    'name' => __( 'Separator (if multiple links are provided)', 'oxygen-acf' ),
                    'data' => 'separator',
                    'type' => 'text'
                );
            // no break
            case 'url':
                $properties[] = array(
                    'name' => __( 'Link', 'oxygen-acf' ),
                    'data' => 'url_link',
                    'type' => 'checkbox',
                    'value' => 'yes'
                );
                $properties[] = array(
                    'name' => __( 'Link text (URL will be used if left empty)', 'oxygen-acf' ),
                    'data' => 'link_text',
                    'type' => 'text',
                    'show_condition' => 'dynamicDataModel.url_link == \'yes\''
                );
                $properties[] = array(
                    'name' => __( 'Open in new Tab', 'oxygen-acf' ),
                    'data' => 'new_tab',
                    'type' => 'checkbox',
                    'value' => 'yes',
                    'show_condition' => 'dynamicDataModel.url_link == \'yes\''
                );
                break;
            case "link":
                $properties[] = array(
                    'name' => __( 'Link', 'oxygen-acf' ),
                    'data' => 'link_link',
                    'type' => 'checkbox',
                    'value' => 'yes'
                );
                $properties[] = array(
                    'name' => __( 'Link text (URL will be used if left empty)', 'oxygen-acf' ),
                    'data' => 'link_text',
                    'type' => 'text',
                    'show_condition' => 'dynamicDataModel.link_link == \'yes\''
                );
                $properties[] = array(
                    'name' => __('Open in new Tab', 'oxygen-acf' ),
                    'data' => 'new_tab',
                    'type' => 'checkbox',
                    'value' => 'yes',
                    'show_condition' => 'dynamicDataModel.link_link == \'yes\''
                );
                $properties[] = array(
                    'name' => __( 'Force link text and target values if already set in the Edit Post page', 'oxygen-acf' ),
                    'data' => 'force_values',
                    'type' => 'checkbox',
                    'value' => 'yes',
                    'show_condition' => 'dynamicDataModel.link_link == \'yes\''
                );
                break;
            case 'gallery':
                $properties[] = array(
                    'name' => __( 'Output type', 'oxygen-acf' ),
                    'data' => 'output_type',
                    'type' => 'select',
                    'options'=> array(
                        __( 'Images ID list', 'oxygen-acf' ) => 'images_id_list',
                        __( 'WordPress Gallery', 'oxygen-acf' ) => 'wp_gallery'
                    ),
                    'nullval' => 'wp_gallery'
                );
                $properties[] = array(
                    'name' => __( 'Separator', 'oxygen-acf' ),
                    'data' => 'separator',
                    'type' => 'text',
                    'show_condition' => 'dynamicDataModel.output_type == \'images_id_list\''
                );
                break;
            case 'date_time_picker':
                $label = __( 'PHP Date Format. Defaults to Y-m-d H:i:s', 'oxygen-acf' );
            // no break
            case 'date_picker':
                if( !isset( $label ) ) $label = __( 'PHP Date Format. Defaults to Y-m-d', 'oxygen-acf' );
                $properties[] = array(
                    'name' => $label,
                    'data' => 'format',
                    'type' => 'text'
                );
                break;
            case 'relationship':
            case 'post_object':
                $properties[] = array(
                    'name' => __( 'Separator (if multiple post objects are provided)', 'oxygen-acf' ),
                    'data' => 'separator',
                    'type' => 'text'
                );
                $properties[] = array(
                    'name'     => __( 'Please select what you want to insert', 'oxygen-acf' ),
                    'data'      => 'insert_type',
                    'type'      => 'select',
                    'options'   => array(
                        __( 'Post Object ID', 'oxygen-acf' ) => 'post_id',
                        __( 'Post URL', 'oxygen-acf' ) => 'post_url'
                    ),
                    'nullval'   => 'post_id',
                    'change' => 'scope.dynamicDataModel.post_link = false'
                );
                $properties[] = array(
                    'name' => __( 'Link', 'oxygen-acf' ),
                    'data' => 'post_link',
                    'type' => 'checkbox',
                    'value' => 'yes',
                    'show_condition' => 'dynamicDataModel.insert_type == \'post_url\''
                );
                $properties[] = array(
                    'name' => __( 'Link text (URL will be used if left empty)', 'oxygen-acf' ),
                    'data' => 'link_text',
                    'type' => 'text',
                    'show_condition' => 'dynamicDataModel.post_link == \'yes\''
                );
                $properties[] = array(
                    'name' => __( 'Open in new Tab', 'oxygen-acf' ),
                    'data' => 'new_tab',
                    'type' => 'checkbox',
                    'value' => 'yes',
                    'show_condition' => 'dynamicDataModel.post_link == \'yes\''
                );
                break;
            case 'taxonomy':
                $properties[] = array(
                    'name' => __( 'Separator (if multiple terms are provided)', 'oxygen-acf' ),
                    'data' => 'separator',
                    'type' => 'text'
                );
                $properties[] = array(
                    'name'     => __( 'Please select what you want to insert', 'oxygen-acf' ),
                    'data'      => 'insert_type',
                    'type'      => 'select',
                    'options'   => array(
                        __( 'Term ID', 'oxygen-acf' ) => 'term_id',
                        __( 'Term URL', 'oxygen-acf' ) => 'term_url'
                    ),
                    'nullval'   => 'term_id',
                    'change' => 'scope.dynamicDataModel.taxonomy_link = false'
                );
                $properties[] = array(
                    'name' => __( 'Link', 'oxygen-acf' ),
                    'data' => 'taxonomy_link',
                    'type' => 'checkbox',
                    'value' => 'yes',
                    'show_condition' => 'dynamicDataModel.insert_type == \'term_url\''
                );
                $properties[] = array(
                    'name' => __( 'Link text (URL will be used if left empty)', 'oxygen-acf' ),
                    'data' => 'link_text',
                    'type' => 'text',
                    'show_condition' => 'dynamicDataModel.taxonomy_link == \'yes\''
                );
                $properties[] = array(
                    'name' => __( 'Open in new Tab', 'oxygen-acf' ),
                    'data' => 'new_tab',
                    'type' => 'checkbox',
                    'value' => 'yes',
                    'show_condition' => 'dynamicDataModel.taxonomy_link == \'yes\''
                );
                break;
            case 'group':
                // Generate the settings for each sub fields
                $inner_properties = array_reduce( $option['sub_fields'], array( $this, "add_button" ) );
                $properties[] = array(
                    'name' => $option['label'],
                    'data' => $option['name'],
                    'type' => 'button',
                    'properties' => $inner_properties
                );
                break;
        }
        if( !empty( $option['label'] ) && !in_array( $option[ 'type' ], $invalid_url_field_types ) ) {
            $result[] = array(
                'name' => $option['label'],
                'data' => $option['name'],
                'type' => 'button',
                'properties' => $properties,
                'settings_page' => $is_settings_page,
            );
        }
        return $result;
    }

    function add_url_button( $result, $option ) {
	    $valid_url_field_types = array( 'image', 'text', 'file', 'page-link', 'url', 'link', 'email', 'group' );
	    $properties = [];
        $is_settings_page = $this->is_settings_page_feild($option['parent']);

        switch( $option['type'] ) {
            case 'image':
                $properties[] = array(
                    'name' => __('Size', 'oxygen-acf'),
                    'data' => 'size',
                    'type' => 'select',
                    'options' => array(
                        __('Thumbnail', 'oxygen-acf') => 'thumbnail',
                        __('Medium', 'oxygen-acf') => 'medium',
                        __('Medium Large', 'oxygen-acf') => 'medium_large',
                        __('Large', 'oxygen-acf') => 'large',
                        __('Original', 'oxygen-acf') => 'full'
                    ),
                    'nullval' => 'medium',
                    'change' => 'scope.dynamicDataModel.width = ""; scope.dynamicDataModel.height = ""'
                );
                $properties[] = array(
                    'name' => __('or', 'oxygen-acf'),
                    'type' => 'label'
                );
                $properties[] = array(
                    'name' => __('Width', 'oxygen-acf'),
                    'data' => 'width',
                    'type' => 'text',
                    'helper' => true,
                    'change' => "scope.dynamicDataModel.size = scope.dynamicDataModel.width+'x'+scope.dynamicDataModel.height"
                );
                $properties[] = array(
                    'name' => __('Height', 'oxygen-acf'),
                    'data' => 'height',
                    'type' => 'text',
                    'helper' => true,
                    'change' => "scope.dynamicDataModel.size = scope.dynamicDataModel.width+'x'+scope.dynamicDataModel.height"
                );
                break;
            case 'group':
                // Generate the settings for each sub fields
                $inner_properties = array_reduce( $option['sub_fields'], array( $this, "add_url_button" ) );
                $properties[] = array(
                    'name' => $option['label'],
                    'data' => $option['name'],
                    'type' => 'button',
                    'properties' => $inner_properties,
                    'settings_page' => $is_settings_page,
                );
                break;
        }

        if( !empty( $option['label'] ) && in_array( $option[ 'type' ], $valid_url_field_types ) ) {
            $result[] = array(
                'name' => $option['label'],
                'data' => $option['name'],
                'type' => 'button',
                'properties' => $properties,
                'settings_page' => $is_settings_page,
            );
        }
        
        return $result;
    }

    function add_image_id_button( $result, $option ) {
	    $valid_url_field_types = array( 'image');
        $is_settings_page = $this->is_settings_page_feild($option['parent']);

        if ( !isset( $option['type'] ) ) {
            return array();
        }

        if (!isset($option['multiple'])) {
            $option['multiple'] = false;
        }

        if( !empty( $option['name'] ) && !$option['multiple'] && isset( $option[ 'type' ] ) && in_array( $option[ 'type' ], $valid_url_field_types ) ) {
            $result[] = array(
                'name' => $option['label'],
                'data' => $option['name'],
                'type' => 'button',
                'properties' => [],
                'settings_page' => $is_settings_page,
            );
        }
        
        return $result;
    }

	function acf_content_handler( $atts ){

        global $wpdb;
        $field = $this->get_field_by_path( $atts['settings_path'] ?? null, $atts['settings_page'] ?? null );
		$output = '';
		if( empty( $field ) ) return $output;

		switch( $field['type'] ) {
			case 'text':
			case 'textarea':
			case 'wysiwyg':
            case 'password':
            case 'range':
            case 'oembed':
            case 'color_picker':
            case 'time_picker':
            case 'number':
            case 'email':
				$output = $field['value'];
				break;
            case 'date_time_picker':
                $format = $field['return_format'];// 'Y-m-d H:i:s';
                // no break
            case 'date_picker':
                if( !isset( $format ) ) $format = $field['return_format']; //'Y-m-d';
                $date = date_create_from_format( $format, $field['value'] );
                if ($date) {
                    $format = empty( $atts[ 'format' ] ) ? $format : $atts[ 'format' ];
                    $output = $date->format( $format );
                }
                break;
            case 'select':
            case 'checkbox':
            case 'radio':
            case 'button_group':
                $separator = isset( $atts[ 'separator' ] ) ? $atts[ 'separator' ] : '';
                $display = isset( $atts[ 'display' ] ) ? $atts[ 'display' ] : 'value';
                if( ! is_array( $field['value'] ) ) {
                    $output = sanitize_text_field( $field['value'] );
                } else {
                    if( isset( $field['value']['value'] ) ) {
                        $output = sanitize_text_field( $field['value'][$display] );
                    } else {
                        $output = array();
                        foreach ( $field['value'] as $value ) {
                            if( ! is_array( $value ) ) {
                                $output[] = $value;
                            } else {
                                $output[] = $value[$display];
                            }
                        }
                        $output = join( $separator, $output );
                    }
                }
                break;
            case 'url':
            case 'page_link':
                $separator = "";
                if( is_array( $field["value"] ) && count( $field["value"] ) > 1 ) $separator = !empty( $atts['separator'] ) ? $atts['separator'] : " ";

                if( !is_array( $field["value"] ) ) $field["value"] = array( $field["value"] ); //normalize to array

                $output_array = array();

                foreach ( $field["value"] as $value ) {
                    if( empty( $atts['url_link'] ) ) {
                        $output_array[] = sanitize_text_field( $value );
                    } else {
                        $link_text = !empty( $atts['link_text'] ) ? $atts['link_text'] : $value;
                        $output_array[] = '<a href="' . esc_url_raw( $value ) . '"' . ( empty( $atts['new_tab'] ) ? '' : ' target="_blank"' ) . '>' . $link_text . '</a>';
                    }
                }
                $output = join( $separator, $output_array );
                break;
            case 'link':
                if( empty( $atts['link_link'] ) ) {
                    $output = sanitize_text_field( is_array( $field["value"] ) ? $field["value"]["url"] : $field["value"] );
                } else {
                    if( is_array( $field["value"] ) && !empty( $field["value"]["title"] ) ) $link_text = $field["value"]["title"];
                    else if( is_array( $field["value"] ) ) $link_text = $field["value"]["url"];
                    else $link_text = $field["value"];

                    $link_text = ( !empty( $atts['link_text'] ) && !empty( $atts['force_values'] ) ) || ( !empty( $atts['link_text'] ) && !is_array( $field["value"] ) )  ? $atts['link_text'] : $link_text;
                    if( is_array( $field["value"] ) && $field["value"]["target"] ) $atts['new_tab'] = "yes";
                    $output = '<a href="' . esc_url_raw( is_array( $field["value"] ) ? $field["value"]["url"] : $field["value"] ) . '"' . ( empty( $atts['new_tab'] ) ? '' : ' target="_blank"' ) . '>' . $link_text . '</a>';
                }
                break;
            case 'true_false':
                if( !empty( $atts['output_custom_onoff'] ) && !empty( $field['ui_on_text'] ) && !empty( $field['ui_off_text'] ) ){
                    $output = sanitize_text_field( $field['value'] ? $field['ui_on_text'] : $field['ui_off_text'] );
                } else $output = $field['value'] ? 'true' : 'false';
                break;
			case 'google_map':
				if( empty( $this->options['google_maps_key'] ) ) {
					$output = __( 'No Google Maps Key set', 'oxygen-acf' );
				}else{
					$output = '<div class="acf-map" id="map_' . sanitize_text_field( $field['ID'] ) . '">';
					$output .= '<div class="marker" data-lat="' . sanitize_text_field( $field['value']['lat'] ) .'" data-lng="' . sanitize_text_field( $field['value']['lng'] ) . '"></div>';
					$output .= '</div>';
					$output .= file_get_contents( __DIR__ . '/public/google_map.html' );

					$output = str_replace( 'OXYGEN_GOOGLE_MAPS_KEY', sanitize_text_field( $this->options['google_maps_key'] ), $output );
					$output = str_replace( 'OXYGEN_MAP_ID', 'map_' . sanitize_text_field( $field['ID'] ), $output );
				}
				break;
			case 'image':
                $image_id = null;
                $image_url = null;
                $image_attachment = null;
                $image_size = explode( 'x', empty($atts['size']) ? '' : strtolower($atts['size']) );
                if( count($image_size) == 1 ){
                    $image_size = $image_size[0];
                }else{
                    $image_size = array_map( 'intval', $image_size );
                }
                if( empty( $image_size ) ) $image_size = "medium";
                if( is_array( $field['value'] ) ) {
                    // Field is configured as "Return Value" = "Image Array" in ACF
                    $image_id = isset($field['value']['ID']) ? $field['value']['ID'] : 0;
                    $image_url = wp_get_attachment_image_src( $image_id, $image_size )[0];
                    $image_attachment = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE ID='%d';", $image_id ),ARRAY_A );

                } else if( is_numeric( $field['value'] ) ) {
                    // Field is configured as "Return Value" = "Image ID" in ACF
                    $image_id = $field['value'];
                    $image_url = wp_get_attachment_image_src( $image_id, $image_size )[0];
                    $image_attachment = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE ID='%d';", $image_id ),ARRAY_A );
                } else {
                    // Field is configured as "Return Value" = "Image URL" in ACF
                    $url = str_replace(home_url(), '', $field['value']);
                    $image_attachment = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE guid LIKE '%%%s';", $url ),ARRAY_A );
                    $image_id = $image_attachment['ID'];
                    $image_url = wp_get_attachment_image_src( $image_id, $image_size )[0];
                }

                if( empty( $atts['insert_type'] ) ) $atts['insert_type'] = 'image_element';
                switch( $atts['insert_type'] ){
                    case "image_element":
                        $output = '<img src="' . $image_url . '"/>';
                        break;
                    case "image_url":
                        $output = $image_url;
                        break;
                    case "image_title":
                        $output = $image_attachment['post_title'];
                        break;
                    case "image_caption":
                        $output = $image_attachment['post_excerpt'];
                        break;
                }
				break;
            case 'file':
                $output_type = $atts['output_type'];
                $attachment_id = null;
                $attachment_url = null;
                if( is_string( $field['value'] ) ) {
                    $attachment_id = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $field['value'] ))[0];
                    $attachment_url = $field['value'];
                } else if( is_int( $field['value'] ) ) {
                    $attachment_id = $field['value'];
                    $attachment_url = $wpdb->get_col($wpdb->prepare("SELECT guid FROM $wpdb->posts WHERE ID=%d;", $field['value'] ))[0];
                } else {
                    $attachment_id = $field['value']['ID'];
                    $attachment_url = $field['value']['url'];
                }

                if( $output_type == 'attachment_id' ){
                    $output = $attachment_id;

                } else {

                    if( empty( $atts['file_link'] ) ) {
                        $output = $attachment_url;
                    } else {
                        $link_text = !empty( $atts['link_text'] ) ? $atts['link_text'] : basename( $attachment_url );
                        $final_url = !empty( $atts['protect_file'] ) ? get_site_url() . "?oxy_download_acf_file=" . $attachment_id : $attachment_url;
                        $output = '<a href="' . $final_url . '" target="_blank">' . $link_text . '</a>';
                    }
                }
                break;
            case 'gallery':
                $image_ids = [];

                // Prevent Invalid argument supplied for foreach() error
                if( empty($field['value']) ) break;

                foreach( $field['value'] as $image ) {
                    $image_ids[] = $image['ID'];
                }
                if( empty($atts['output_type']) || $atts['output_type'] == 'images_id_list' ) {
                    $output = implode( empty($atts['separator']) ? ',' : $atts['separator'], $image_ids);
                } else {
                    $output = do_shortcode( '[gallery ids="' . implode(',', $image_ids) . '"]' );
                }
                break;
            case 'relationship':
            case 'post_object':
                $separator = "";
                if( is_array( $field["value"] ) && count( $field["value"] ) > 1 ) $separator = !empty( $atts['separator'] ) ? $atts['separator'] : " ";

                if( !is_array( $field["value"] ) ) $field["value"] = array( $field["value"] );

                $output_array = array();

                foreach ( $field["value"] as $value ) {

                    if( is_int( $value ) ) {
                        $value = get_post( $value );
                    }

                    if( $atts['insert_type'] == 'post_url' ) {
                        if( empty( $atts['post_link'] ) ) {
                            $output_array[] = get_permalink( $value->ID );
                        } else {
                            $link_text = !empty( $atts['link_text'] ) ? $atts['link_text'] : $value->post_title;
                            $output_array[] = '<a href="' . get_permalink( $value->ID ) . '"' . ( empty( $atts['new_tab'] ) ? '' : ' target="_blank"' ) . '>' . $link_text . '</a>';
                        }

                    } else {
                        $output_array[] = $value->ID;
                    }

                }
                $output = join( $separator, $output_array );
                break;
            case 'taxonomy':
                $separator = "";
                if( is_array( $field["value"] ) && count( $field["value"] ) > 1 ) $separator = !empty( $atts['separator'] ) ? $atts['separator'] : " ";

                if( !is_array( $field["value"] ) ) $field["value"] = array( $field["value"] );

                $output_array = array();

                foreach ( $field["value"] as $value ) {

                    if( is_int( $value ) ) {
                        $value = get_term( $value );
                    }

                    if( $atts['insert_type'] == 'term_url' ) {
                        if( empty( $atts['taxonomy_link'] ) ) {
                            $output_array[] = get_term_link( $value->term_id );
                        } else {
                            $link_text = !empty( $atts['link_text'] ) ? $atts['link_text'] : $value->post_title;
                            $output_array[] = '<a href="' . get_term_link( $value->term_id ) . '"' . ( empty( $atts['new_tab'] ) ? '' : ' target="_blank"' ) . '>' . $link_text . '</a>';
                        }

                    } else {
                        $output_array[] = $value->term_id;
                    }

                }
                $output = join( $separator, $output_array );
                break;
		}

		if( !empty( $atts['include_prepend_append'] ) ) {
		    if( !empty( $field['prepend'] ) ) $output = $field['prepend'] . $output;
            if( !empty( $field['append'] ) ) $output .= $field['append'];
        }

		return $output;
	}

	function acf_url_handler( $atts ){

	    global $wpdb;
        $field = $this->get_field_by_path( $atts['settings_path'], $atts['settings_page'] );
		$output = '';
		if( empty( $field ) ) return $output;

		switch( $field['type'] ) {
			case 'image':
                $image_id = null;
                $image_url = null;
                $image_attachment = null;
                $image_size = explode( 'x', empty($atts['size']) ? '' : strtolower($atts['size']) );
                if( count($image_size) == 1 ){
                    $image_size = $image_size[0];
                }else{
                    $image_size = array_map( 'intval', $image_size );
                }
                if( empty( $image_size ) ) $image_size = "medium";
                if( is_array( $field['value'] ) ) {
                    // Field is configured as "Return Value" = "Image Array" in ACF
                    $image_id = isset($field['value']['ID']) ? $field['value']['ID'] : 0;
                    $image_url = wp_get_attachment_image_src( $image_id, $image_size )[0];
                } else if( is_numeric( $field['value'] ) ) {
                    // Field is configured as "Return Value" = "Image ID" in ACF
                    $image_id = $field['value'];
                    $image_url = wp_get_attachment_image_src( $image_id, $image_size )[0];
                } else {
                    // Field is configured as "Return Value" = "Image URL" in ACF
                    $url = wp_make_link_relative( $field['value'] );

                    if( empty(  $url  )  ) {
                        return "";
                    }                    

                    $image_attachment = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE guid LIKE '%%%s';", $url ),ARRAY_A );
                    $image_id = $image_attachment['ID'];
                    $image_url = wp_get_attachment_image_src( $image_id, $image_size )[0] ?? null;
                }
                $output = $image_url;
				break;
            case 'text':
            case 'url':
            case 'page_link':
                $output = is_array( $field["value"] ) ? $field["value"][0] : $field["value"];
                break;
            case 'email':
                $output = 'mailto:' . ( is_array( $field["value"] ) ? $field["value"][0] : $field["value"] );
                break;
            case 'link':
                $output = is_array( $field["value"] ) ? $field["value"]["url"] : $field["value"];
                break;
            case 'file':
                if( is_string( $field['value'] ) ) {
                    $output = $field['value'];
                } else if( is_int( $field['value'] ) ) {
                    $output = $wpdb->get_col($wpdb->prepare("SELECT guid FROM $wpdb->posts WHERE ID=%d;", $field['value'] ))[0];
                } else {
                    $output = $field['value']['url'];
                }
                break;
            case 'post_object':
                if( is_array( $field['value'] ) ) $field['value'] = $field['value'][0];


                if( is_int( $field['value'] ) ) {
                    $field['value'] = get_post( $field['value'] );
                }

                $output = get_permalink( $field['value']->ID );
                break;
		}
		return esc_url_raw( $output );
	}


    function acf_image_id_handler( $atts ){
        global $wpdb;
        $field = $this->get_field_by_path( $atts['settings_path'], $atts['settings_page'] );
		$output = '';
		if( empty( $field ) ) return $output;

		switch( $field['type'] ) {
			case 'image':
                $image_id = null;
                $image_attachment = null;

                if( is_array( $field['value'] ) ) {
                    // Field is configured as "Return Value" = "Image Array" in ACF
                    $image_id = isset($field['value']['ID']) ? $field['value']['ID'] : 0;
                } else if( is_numeric( $field['value'] ) ) {
                    // Field is configured as "Return Value" = "Image ID" in ACF
                    $image_id = $field['value'];
                } else {
                    // Field is configured as "Return Value" = "Image URL" in ACF
                    $url = wp_make_link_relative( $field['value'] );
                    $image_attachment = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE guid LIKE '%%%s';", $url ),ARRAY_A );
                    $image_id = $image_attachment['ID'];
                }
                $output = $image_id;
				break;
		}
		return $output;
    }

    /**
     * Common helper to get field by settings path, issue #1642
     * 
     * @since 3.1
     * @author Abdelouahed E.
     */
    function get_field_by_path( $settings_path, $is_settings_page = false ){
        $deep_field = false;
        $path = explode( '/', $settings_path );

        $option = $is_settings_page && $is_settings_page !== "false" ? "option" : 0;

        // In case of a field inside a group, dig through the inner group fields
        if( count( $path ) > 1 ) {
            reset_rows();
            $deep_field = true;
            for( $i = 0; $i < count( $path ) - 1; $i++ ){
                have_rows( $path[ $i ], $option );
                if ( have_rows( $path[ $i ], $option )) {
                    the_row();
                }
            }
        }

        if( $deep_field == true ) {
            // sub-field name is the last element of the array
            $field = get_sub_field_object( $path[ count( $path ) - 1 ]);
        } else {
            $field = get_field_object( $path[0]);
        }

        if ( (!isset($field['value']) || empty($field['value'])) && $is_settings_page && $is_settings_page !== "false") {
            if( $deep_field == true ) {
                // sub-field name is the last element of the array
                $field = get_sub_field_object( $path[ count( $path ) - 1 ], 'option' );
            } else {
                $field = get_field_object( $path[0], 'option' );
            }
        }

        return $field;
    }

    function is_settings_page_feild( $group_id ) {

        $is_settings_page = false;

        $field_group = acf_get_field_group( $group_id );

        if ($field_group && is_array($field_group['location'])) {
            foreach ($field_group['location'] as $location_type) {
                foreach ($location_type as $location) {
                    if ($location['param'] == "options_page") {
                        $is_settings_page = true;
                        break;
                    }
                }
            }
        }

        return $is_settings_page;

    }
}

new oxygen_acf_integration();

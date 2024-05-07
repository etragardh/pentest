<?php

/**
 * Metabox integration for Oxygen
 *
 * @since 3.9
 */

class OxygenMetaBoxIntegration {

	private $options;
    private $groups;

	function __construct(){
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	function init() {
		if ( class_exists('RWMB_Loader') ) { 
            $this->groups = array();
			add_filter( 'oxygen_custom_dynamic_data', array( $this, 'init_dynamic_data' ), 10, 1 );
            add_action( 'ct_builder_ng_init', array( $this, 'group_fields_data' ) );
		}
	}
    
    function recursive_group_data($field, $parent = false) {

        $groups = array();
        $sub_fields = array();
        if ( isset( $field['fields'] ) ) {
            foreach ( $field['fields'] as $sub_field ) {
                if ( $sub_field['type'] == 'group' ) {
                    // TODO: add support for sub groups
                    //$groups = array_merge($groups, $this->recursive_group_data($sub_field, $field['id']));
                } else {
                    $sub_fields[] = $sub_field;
                }
            }
        }
    
        $groups[$field['id']] = array(
            'label'  => $field['name'], 
            'fields' => $sub_fields, 
            'parent' => $parent 
        );
    
        return $groups;
    }

    function group_fields_data() {

        $meta_boxes = apply_filters( 'rwmb_meta_boxes', array() );
        
        foreach ( $meta_boxes as $meta_box ) {

            // exclude MetaBox default fields groups
            if ( in_array( $meta_box['id'], array("rwmb-user-register","rwmb-user-login","rwmb-user-lost-password","rwmb-user-reset-password","rwmb-user-info") ) ){
                continue;
            }

            $fields = $meta_box['fields'];

            foreach ($fields as $field) {
                if ($field['type'] == 'group' && isset($field['clone']) && $field['clone'] == true) {
                    $this->groups = array_merge($this->groups, $this->recursive_group_data($field));
                }
            }
        }

        $output = json_encode($this->groups);
        $output = htmlspecialchars( $output, ENT_QUOTES );
    
        echo "metaBoxGroupFields=$output;";
    
    }

    function init_dynamic_data( $dynamic_data ) {
		
        $meta_boxes = apply_filters( 'rwmb_meta_boxes', array() );
		$fields = array();

        foreach ( $meta_boxes as $meta_box ) {

            if ( !isset( $meta_box['id'] ) ) {
                continue;
            }

            // excllude MetaBox default fields groups
            if ( in_array( $meta_box['id'], array("rwmb-user-register","rwmb-user-login","rwmb-user-lost-password","rwmb-user-reset-password","rwmb-user-info") ) ){
                continue;
            }

            $tmp = $meta_box['fields'];
            
            $settings_pages = isset($meta_box['settings_pages']) ? $meta_box['settings_pages'] : false;
            if ($settings_pages) {
                $tmp = array_map(function($field) use($settings_pages) {
                    $field['settings_pages'] = $settings_pages;
                    return $field;
                }, $tmp);
            }

            $fields = array_merge( $tmp, $fields );

        }

        // Generate the settings for each field type
        $all_options = array_reduce( $fields, array( $this, "add_button" ), array() );

        if( count( $all_options ) > 0 ) {
            array_unshift( $all_options, array('name' => __( 'Select the Meta Box field', 'oxygen' ), 'type' => 'heading') );
            $metabox_content = array(
                'name'      => __( 'Meta Box Field', 'oxygen' ),
                'mode'       => 'content',
                // Available modes: 'content', 'custom-field', 'link' and 'image'
                'position'   => 'Post',
                // Available positions: 'Post', 'Author', 'User', 'Featured Image', 'Current User', 'Archive' 'Blog Info'
                'data'       => 'metabox_content',
                'handler'    => array( $this, 'metabox_content_handler' ),
                // Must be a callable
                'properties' => $all_options
            );
            $dynamic_data[]   = $metabox_content;
        }

        $options_for_url = array_reduce( $fields, array( $this, "add_url_button" ), array() );

        if( count( $options_for_url ) > 0 ) {
            // Dynamic Data modal modes "custom-field", "link" and "image" are expected to return an URL

            $metabox_image = array(
                'name' => __( 'Meta Box Field', 'oxygen' ),
                'mode' => 'image',
                // Available modes: 'content', 'custom-field', 'link' and 'image'
                'position' => 'Post',
                // Available positions: 'Post', 'Author', 'User', 'Featured Image', 'Current User', 'Archive' 'Blog Info'
                'data' => 'metabox_image',
                'handler' => array($this, 'metabox_url_handler'),
                // Must be a callable
                'properties' => $options_for_url
            );
            $dynamic_data[] = $metabox_image;

            $metabox_link = array(
                'name' => __( 'Meta Box Field', 'oxygen' ),
                'mode' => 'link',
                // Available modes: 'content', 'custom-field', 'link' and 'image'
                'position' => 'Post',
                // Available positions: 'Post', 'Author', 'User', 'Featured Image', 'Current User', 'Archive' 'Blog Info'
                'data' => 'metabox_link',
                'handler' => array($this, 'metabox_url_handler'),
                // Must be a callable
                'properties' => $options_for_url
            );
            $dynamic_data[] = $metabox_link;

            $metabox_custom_field = array(
                'name' => __( 'Meta Box Field', 'oxygen' ),
                'mode' => 'custom-field',
                // Available modes: 'content', 'custom-field', 'link' and 'image'
                'position' => 'Post',
                // Available positions: 'Post', 'Author', 'User', 'Featured Image', 'Current User', 'Archive' 'Blog Info'
                'data' => 'metabox_custom_field',
                'handler' => array($this, 'metabox_url_handler'),
                // Must be a callable
                'properties' => $options_for_url
            );
            $dynamic_data[] = $metabox_custom_field;
        }
           
        $options_for_image_id = array_reduce( $fields, array( $this, "add_image_id_button" ), array() ); 
        
        if( count( $options_for_image_id ) > 0 ) { 
            $metabox_image_id_field = array(
                'name' => __( 'Meta Box Field', 'oxygen' ),
                'mode' => 'image-id',
                'position' => 'Post',
                'data' => 'metabox_image_id',
                'handler' => array($this, 'metabox_image_id_handler'),
                'properties' => $options_for_image_id
            );
            $dynamic_data[] = $metabox_image_id_field;
        }
           
        return $dynamic_data;
	}

    function add_button( $result, $option ) {

        $invalid_url_field_types = array( 'accordion', 'message', 'repeater', 'flexible_content' );
        $properties = [];

        if (!isset($option['type'])) {
            return array();
        }

        if ( !in_array( $option['type'], array('taxonomy', 'taxonomy_advanced') ) ) {
            $properties[] = array(
                'name' => __( 'Use default output format (this will convert text element to Shortcode element)', 'oxygen' ),
                'data' => 'use_default_output_format',
                'type' => 'checkbox',
                'value' => 'yes',
                'show_condition' => 'dynamicDataModel.triggered_from_text_element==true',
            );
        }

        switch( $option['type'] ) {

            // no options
            case 'password':
            case 'number':
            case 'email':
            case 'sidebar':
                break;

            case 'text':
            case 'slider':
                $properties[] = array(
                    'name' => __( 'Include prepend and append text (if configured)', 'oxygen' ),
                    'data' => 'include_prepend_append',
                    'type' => 'checkbox',
                    'value' => 'yes'
                );
                break;
            
            case 'image':
            case 'image_advanced':
            case 'image_upload':
                $properties[] = array(
                    'name' => __('Separator (for multiple images)', 'oxygen'),
                    'data' => 'separator',
                    'type' => 'text'
                );
            case 'single_image':
                $properties[] = array(
                    'name'     => __( 'Please select what you want to insert', 'oxygen' ),
                    'data'      => 'insert_type',
                    'type'      => 'select',
                    'options'   => array(
                        __( 'Image element', 'oxygen' ) =>'image_element',
                        __( 'Image URL', 'oxygen' ) => 'image_url',
                        __( 'Image Title', 'oxygen' ) => 'image_title',
                        __( 'Image Caption', 'oxygen' ) => 'image_caption'
                    ),
                    'nullval'   => 'image_element'
                );
                $properties[] = array(
                    'name'=> __( 'Size', 'oxygen' ),
                    'data'=> 'size',
                    'type'=> 'select',
                    'options'=> array(
                        __( 'Thumbnail', 'oxygen' ) => 'thumbnail',
                        __( 'Medium', 'oxygen' ) => 'medium',
                        __( 'Medium Large', 'oxygen' ) => 'medium_large',
                        __( 'Large', 'oxygen' ) => 'large',
                        __( 'Original', 'oxygen' ) => 'full'
                    ),
                    'nullval' => 'medium',
                    'change'=> 'scope.dynamicDataModel.width = ""; scope.dynamicDataModel.height = ""',
                    'show_condition' => "dynamicDataModel.insert_type == 'image_element'"
                );
                $properties[] = array(
                    'name' => __( 'or', 'oxygen' ),
                    'type' => 'label',
                    'show_condition' => 'dynamicDataModel.insert_type == \'image_element\''
                );
                $properties[] = array(
                    'name' => __( 'Width', 'oxygen' ),
                    'data' => 'width',
                    'type' => 'text',
                    'helper'=> true,
                    'change' => "scope.dynamicDataModel.size = scope.dynamicDataModel.width+'x'+scope.dynamicDataModel.height",
                    'show_condition' => "dynamicDataModel.insert_type == 'image_element'"
                );
                $properties[] = array(
                    'name' => __( 'Height', 'oxygen' ),
                    'data' => 'height',
                    'type' => 'text',
                    'helper' => true,
                    'change' => "scope.dynamicDataModel.size = scope.dynamicDataModel.width+'x'+scope.dynamicDataModel.height",
                    'show_condition' => 'dynamicDataModel.insert_type == \'image_element\''
                );
                $properties[] = array(
                    'name' => __( 'Max Width (%)', 'oxygen' ),
                    'data' => 'max_width',
                    'type' => 'text',
                    'show_condition' => "dynamicDataModel.insert_type == 'image_element'"
                );
                break;
            
            case 'select':
            case 'select_advanced':
                $properties[] = array(
                    'name'      => __( 'Return value or label?', 'oxygen' ),
                    'data'      => 'display',
                    'type'      => 'select',
                    'options'   => array(
                        __( 'Value', 'oxygen' ) => 'value',
                        __( 'Label', 'oxygen' ) => 'label'
                    ),
                );
            case 'image_select':
                if( isset($option[ 'multiple' ]) && $option[ 'multiple' ] != 0 ) {
                    $properties[] = array(
                        'name' => __('Separator (for multiple options fields)', 'oxygen'),
                        'data' => 'separator',
                        'type' => 'text'
                    );
                }
                break;
            case 'checkbox_list':
            case 'autocomplete':
                $properties[] = array(
                    'name'      => __( 'Return value or label?', 'oxygen' ),
                    'data'      => 'display',
                    'type'      => 'select',
                    'options'   => array(
                        __( 'Value', 'oxygen' ) => 'value',
                        __( 'Label', 'oxygen' ) => 'label'
                    ),
                );
            case 'fieldset_text':
            case 'text_list':
            case 'key_value':
                $properties[] = array(
                    'name' => __('Separator (for keys and values)', 'oxygen'),
                    'data' => 'separator',
                    'type' => 'text',
                    'show_condition' => 'dynamicDataModel.use_default_output_format!=\'yes\''
                );
                $properties[] = array(
                    'name' => __( 'Separator (for multiple rows)', 'oxygen' ),
                    'data' => 'cloned_separator',
                    'type' => 'text'
                );
                break;
            case 'checkbox':
            case 'radio':
            case 'button_group':
                $properties[] = array(
                    'name' => __('Separator (for multiple options fields)', 'oxygen'),
                    'data' => 'separator',
                    'type' => 'text',
                    'show_condition' => 'dynamicDataModel.use_default_output_format!=\'yes\''
                );
                $properties[] = array(
                    'name'      => __( 'Return value or label?', 'oxygen' ),
                    'data'      => 'display',
                    'type'      => 'select',
                    'options'   => array(
                        __( 'Value', 'oxygen' ) => 'value',
                        __( 'Label', 'oxygen' ) => 'label'
                    ),
                );
                // no options?
                break;
            case 'switch':
                $properties[] = array(
                    'name' => __( 'If set, output custom on/off text instead of true/false', 'oxygen' ),
                    'data' => 'output_custom_onoff',
                    'type' => 'checkbox',
                    'value' => 'yes'
                );
                break;
            case 'file':
            case 'file_advanced':
            case 'file_upload':
                $properties[] = array(
                    'name' => __( 'Output type', 'oxygen' ),
                    'data' => 'output_type',
                    'type' => 'select',
                    'options'=> array(
                        __( 'Url', 'oxygen' ) => 'url',
                        __( 'Attachment ID', 'oxygen' ) => 'attachment_id'
                    ),
                    'nullval' => 'url'
                );
                $properties[] = array(
                    'name' => __( 'Separator (if multiple terms are provided)', 'oxygen' ),
                    'data' => 'separator',
                    'type' => 'text'
                );
            case 'file_input':
                $properties[] = array(
                    'name' => __( 'Link', 'oxygen' ),
                    'data' => 'file_link',
                    'type' => 'checkbox',
                    'value' => 'yes',
                    'show_condition' => 'dynamicDataModel.output_type == \'url\''
                );
                $properties[] = array(
                    'name' => __( 'Link text (file name will be used if left empty)', 'oxygen' ),
                    'data' => 'link_text',
                    'type' => 'text',
                    'show_condition' => 'dynamicDataModel.file_link == \'yes\''
                );
                $properties[] = array(
                    'name' => __( 'Prevent file downloads from logged-out users', 'oxygen' ),
                    'data' => 'protect_file',
                    'type' => 'checkbox',
                    'value' => 'yes',
                    'show_condition' => 'dynamicDataModel.file_link == \'yes\''
                );
                break;
            case 'url':
                $properties[] = array(
                    'name' => __( 'Link', 'oxygen' ),
                    'data' => 'url_link',
                    'type' => 'checkbox',
                    'value' => 'yes'
                );
                $properties[] = array(
                    'name' => __( 'Link text (URL will be used if left empty)', 'oxygen' ),
                    'data' => 'link_text',
                    'type' => 'text',
                    'show_condition' => 'dynamicDataModel.url_link == \'yes\''
                );
                $properties[] = array(
                    'name' => __( 'Open in new Tab', 'oxygen' ),
                    'data' => 'new_tab',
                    'type' => 'checkbox',
                    'value' => 'yes',
                    'show_condition' => 'dynamicDataModel.url_link == \'yes\''
                );
                break;
            case 'datetime':
            case 'datetime-local':
                $label = __( 'PHP Date Format. Defaults to Y-m-d H:i:s', 'oxygen' );
                $properties[] = array(
                    'name' => $label,
                    'data' => 'format',
                    'type' => 'text'
                );
                break;
            case 'month':
                $label = __( 'PHP Date Format. Defaults to Y-m', 'oxygen' );
                $properties[] = array(
                    'name' => $label,
                    'data' => 'format',
                    'type' => 'text'
                );
                break;
            case 'week':
                $label = __( 'PHP Date Format. Defaults to W Y', 'oxygen' );
                $properties[] = array(
                    'name' => $label,
                    'data' => 'format',
                    'type' => 'text'
                );
                break;
            case 'date':
                $label = __( 'PHP Date Format. Defaults to Y-m-d', 'oxygen' );
                $properties[] = array(
                    'name' => $label,
                    'data' => 'format',
                    'type' => 'text'
                );
                break;
            case 'post':
                $properties[] = array(
                    'name' => __( 'Separator (if multiple post objects are provided)', 'oxygen' ),
                    'data' => 'separator',
                    'type' => 'text'
                );
                $properties[] = array(
                    'name'     => __( 'Please select what you want to insert', 'oxygen' ),
                    'data'      => 'insert_type',
                    'type'      => 'select',
                    'options'   => array(
                        __( 'Post Object ID', 'oxygen' ) => 'post_id',
                        __( 'Post URL', 'oxygen' ) => 'post_url'
                    ),
                    'nullval'   => 'post_id',
                    'change' => 'scope.dynamicDataModel.post_link = false'
                );
                $properties[] = array(
                    'name' => __( 'Link', 'oxygen' ),
                    'data' => 'post_link',
                    'type' => 'checkbox',
                    'value' => 'yes',
                    'show_condition' => 'dynamicDataModel.insert_type == \'post_url\''
                );
                $properties[] = array(
                    'name' => __( 'Link text (URL will be used if left empty)', 'oxygen' ),
                    'data' => 'link_text',
                    'type' => 'text',
                    'show_condition' => 'dynamicDataModel.post_link == \'yes\''
                );
                $properties[] = array(
                    'name' => __( 'Open in new Tab', 'oxygen' ),
                    'data' => 'new_tab',
                    'type' => 'checkbox',
                    'value' => 'yes',
                    'show_condition' => 'dynamicDataModel.post_link == \'yes\''
                );
                break;
            case 'user':
                $properties[] = array(
                    'name' => __( 'Separator (if multiple user objects are provided)', 'oxygen' ),
                    'data' => 'separator',
                    'type' => 'text'
                );
                $properties[] = array(
                    'name'     => __( 'Please select what you want to insert', 'oxygen' ),
                    'data'      => 'insert_type',
                    'type'      => 'select',
                    'options'   => array(
                        __( 'User Object ID', 'oxygen' ) => 'user_id',
                        __( 'User URL', 'oxygen' ) => 'user_url'
                    ),
                    'nullval'   => 'user_id',
                    'change' => 'scope.dynamicDataModel.post_link = false'
                );
                $properties[] = array(
                    'name' => __( 'Link', 'oxygen' ),
                    'data' => 'user_link',
                    'type' => 'checkbox',
                    'value' => 'yes',
                    'show_condition' => 'dynamicDataModel.insert_type == \'user_url\''
                );
                $properties[] = array(
                    'name' => __( 'Link text (URL will be used if left empty)', 'oxygen' ),
                    'data' => 'link_text',
                    'type' => 'text',
                    'show_condition' => 'dynamicDataModel.post_link == \'yes\''
                );
                $properties[] = array(
                    'name' => __( 'Open in new Tab', 'oxygen' ),
                    'data' => 'new_tab',
                    'type' => 'checkbox',
                    'value' => 'yes',
                    'show_condition' => 'dynamicDataModel.post_link == \'yes\''
                );
                break;
            case 'taxonomy':
            case 'taxonomy_advanced':
                $properties[] = array(
                    'name' => __( 'Use default output format (this will convert text element to Shortcode element)', 'oxygen' ),
                    'data' => 'use_default_taxonomy_output_format',
                    'type' => 'checkbox',
                    'value' => 'yes',
                    'show_condition' => 'dynamicDataModel.triggered_from_text_element==true',
                );
                $properties[] = array(
                    'name' => __( 'Separator (if multiple terms are provided)', 'oxygen' ),
                    'data' => 'separator',
                    'type' => 'text',
                    'show_condition' => 'dynamicDataModel.use_default_taxonomy_output_format!=\'yes\''
                );
                $properties[] = array(
                    'name'     => __( 'Please select what you want to insert', 'oxygen' ),
                    'data'      => 'insert_type',
                    'type'      => 'select',
                    'options'   => array(
                        __( 'Term ID', 'oxygen' ) => 'term_id',
                        __( 'Term URL', 'oxygen' ) => 'term_url',
                        __( 'Term Name', 'oxygen' ) => 'term_name'
                    ),
                    'nullval'   => 'term_id',
                    'change' => 'scope.dynamicDataModel.taxonomy_link = false'
                );
                $properties[] = array(
                    'name' => __( 'Link', 'oxygen' ),
                    'data' => 'taxonomy_link',
                    'type' => 'checkbox',
                    'value' => 'yes',
                    'show_condition' => 'dynamicDataModel.insert_type == \'term_url\''
                );
                $properties[] = array(
                    'name' => __( 'Link text (URL will be used if left empty)', 'oxygen' ),
                    'data' => 'link_text',
                    'type' => 'text',
                    'show_condition' => 'dynamicDataModel.taxonomy_link == \'yes\''
                );
                $properties[] = array(
                    'name' => __( 'Open in new Tab', 'oxygen' ),
                    'data' => 'new_tab',
                    'type' => 'checkbox',
                    'value' => 'yes',
                    'show_condition' => 'dynamicDataModel.taxonomy_link == \'yes\''
                );
                break;
            case 'group':
                // Generate the settings for each sub fields
                $inner_properties = array_reduce( $option['fields'], array( $this, "add_button" ) );
                $properties[] = array(
                    'name' => $option['name'],
                    'data' => $option['name'],
                    'type' => 'button',
                    'properties' => $inner_properties
                );
                break;
        }

        if ( isset( $option['clone'] ) && $option['clone'] == 'true' ) {
            $properties[] = array(
                'name' => __( 'Separator (if field is cloned)', 'oxygen' ),
                'data' => 'cloned_separator',
                'type' => 'text'
            );
        }

        if ( isset($option['settings_pages']) && is_array($option['settings_pages']) ) {
            $properties[] = array(
                'name' => __( 'Settings Page', 'oxygen' ),
                'data'      => "settings_page",
                'type'      => 'select',
                'options'   => array_combine($option['settings_pages'],$option['settings_pages']),
            );
        }

        if( !empty( $option['name'] ) && !in_array( $option[ 'type' ], $invalid_url_field_types ) ) {
            $result[] = array(
                'name' => $option['name'],
                'data' => isset($option['id']) ? $option['id'] : "",
                'type' => 'button',
                'metabox_field_type' => $option['type'],
                'settings_pages' => isset($option['settings_pages']) ? $option['settings_pages'] : "",
                'settings_page' => isset($option['settings_pages'][0]) ? $option['settings_pages'][0] : "",
                'properties' => $properties
            );
        }

        return $result;
    }

    function add_url_button( $result, $option ) {
	    $valid_url_field_types = array( 'image', 'image_upload', 'image_advanced', 'single_image', 'text', 'file', 'file_upload', 'file_advanced', 'file_input', 'email', 'url', 'group', 'tel', 'taxonomy_advanced' );
	    $properties = [];

        if ( !isset( $option['type'] ) ) {
            return array();
        }

        switch( $option['type'] ) {
            case 'image':
            case 'image_upload':
            case 'image_advanced':
            case 'single_image':
                $properties[] = array(
                    'name' => __('Size', 'oxygen'),
                    'data' => 'size',
                    'type' => 'select',
                    'options' => array(
                        __('Thumbnail', 'oxygen') => 'thumbnail',
                        __('Medium', 'oxygen') => 'medium',
                        __('Medium Large', 'oxygen') => 'medium_large',
                        __('Large', 'oxygen') => 'large',
                        __('Original', 'oxygen') => 'full'
                    ),
                    'nullval' => 'medium',
                    'change' => 'scope.dynamicDataModel.width = ""; scope.dynamicDataModel.height = ""'
                );
                $properties[] = array(
                    'name' => __('or', 'oxygen'),
                    'type' => 'label'
                );
                $properties[] = array(
                    'name' => __('Width', 'oxygen'),
                    'data' => 'width',
                    'type' => 'text',
                    'helper' => true,
                    'change' => "scope.dynamicDataModel.size = scope.dynamicDataModel.width+'x'+scope.dynamicDataModel.height"
                );
                $properties[] = array(
                    'name' => __('Height', 'oxygen'),
                    'data' => 'height',
                    'type' => 'text',
                    'helper' => true,
                    'change' => "scope.dynamicDataModel.size = scope.dynamicDataModel.width+'x'+scope.dynamicDataModel.height"
                );
                break;
            case 'group':
                // Generate the settings for each sub fields
                $inner_properties = array_reduce( $option['fields'], array( $this, "add_url_button" ) );
                $properties[] = array(
                    'name' => $option['name'],
                    'data' => isset($option['id']) ? $option['id'] : "",
                    'type' => 'button',
                    'settings_pages' => isset($option['settings_pages']) ? $option['settings_pages'] : "",
                    'settings_page' => isset($option['settings_pages'][0]) ? $option['settings_pages'][0] : "",
                    'properties' => $inner_properties
                );
                break;
        }

        if (!isset($option['multiple'])) {
            $option['multiple'] = false;
        }

        if( !empty( $option['name'] ) && !$option['multiple'] && isset( $option[ 'type' ] ) && in_array( $option[ 'type' ], $valid_url_field_types ) ) {
            $result[] = array(
                'name' => $option['name'],
                'data' => isset($option['id']) ? $option['id'] : "",
                'type' => 'button',
                'settings_pages' => isset($option['settings_pages']) ? $option['settings_pages'] : "",
                'settings_page' => isset($option['settings_pages'][0]) ? $option['settings_pages'][0] : "",
                'properties' => $properties
            );
        }

        return $result;
    }

    function add_image_id_button( $result, $option ) {
	    $valid_url_field_types = array( 'image', 'image_upload', 'image_advanced', 'single_image' );
	    $properties = [];

        if ( !isset( $option['type'] ) ) {
            return array();
        }

        if (!isset($option['multiple'])) {
            $option['multiple'] = false;
        }

        if( !empty( $option['name'] ) && !$option['multiple'] && isset( $option[ 'type' ] ) && in_array( $option[ 'type' ], $valid_url_field_types ) ) {
            $result[] = array(
                'name' => $option['name'],
                'data' => isset($option['id']) ? $option['id'] : "",
                'type' => 'button',
                'settings_pages' => isset($option['settings_pages']) ? $option['settings_pages'] : "",
                'settings_page' => isset($option['settings_pages'][0]) ? $option['settings_pages'][0] : "",
                'properties' => $properties
            );
        }

        return $result;
    }

    function metabox_content_handler( $atts ){

        $field = $this->get_field_by_path( $atts['settings_path'], $atts['settings_page'] );
		if( empty( $field ) ) return '';

        // don't loop cloned fields if default outut format used
        if ( isset($atts['use_default_output_format']) && $atts['use_default_output_format'] == 'yes') {
            return $this->metabox_content_handler_switch($field, $field['value'], $atts);
        }

        if (isset($field['clone']) && $field['clone']=='true') {
            
            $output_array = array();
            $separator = isset($atts['cloned_separator']) ? $atts['cloned_separator'] : " ";

            if ($separator=="&lt;br&gt;" || $separator=="&lt;br/&gt;" || $separator=="&lt;br /&gt;"){
                $separator = "<br>";
            }
            
            if (is_array($field['value'])) {
                foreach ($field['value'] as $value) {
                    $output_array[] = $this->metabox_content_handler_switch($field, $value, $atts);
                }
            }
            $output = join( $separator, $output_array );
            return $output;
        }

        if (!isset($field['value'])) {
            $field['value'] = "";
        }

        return $this->metabox_content_handler_switch($field, $field['value'], $atts);
    }

    function metabox_content_handler_switch( $field, $field_value, $atts ){

        global $wpdb;
		$output = '';

        if ( isset($atts['use_default_output_format']) && $atts['use_default_output_format'] == 'yes') {
            ob_start();
            if ($atts['settings_page']) {
                $settings_pages = MBSP\Factory::get( array($atts['settings_page']), 'normal' );
                if (isset($settings_pages[$atts['settings_page']])){
                    $option_name = $settings_pages[$atts['settings_page']]->__get('option_name');
                    rwmb_the_value( $field['id'], array(
                        'object_type' => 'setting',
                    ), $option_name );
                }
            }
            else {
                rwmb_the_value( $field['id'] );
            }
            $output = ob_get_clean();
            return $output;
        }

        if ( !isset($field['type']) ) {
            return $output;
        }

		switch( $field['type'] ) {
            case 'background':
                //var_dump($field_value);
                break;

			case 'text':
			case 'textarea':
			case 'wysiwyg':
            case 'password':
            case 'slider':
            case 'oembed':
            case 'color':
            case 'time':
            case 'range':
            case 'number':
            case 'email':
            case 'tel':
				$output = $field_value;
				break;
            case 'datetime':
                $format = isset($field['return_format']) ? $field['return_format'] : ""; // 'Y-m-d H:i:s';
            // no break
            case 'date':
                if( !isset( $format ) ) $format = $field['php_format']; //'Y-m-d';
                if ( isset($field['timestamp']) && $field['timestamp'] == true ) {
                    $timestamp = $field_value;
                } else {
                    $timestamp = strtotime( $field_value );
                }
                if ($timestamp===false) {
                    $output = __("Wrong date input","oxygen");
                }
                else {
                    $output = date( empty( $atts[ 'format' ] ) ? $format : $atts[ 'format' ], $timestamp );
                }
                break;
            case 'datetime-local':
                $default_format = 'Y-m-d H:i:s';
                if ( isset($field['timestamp']) && $field['timestamp'] == true ) {
                    $timestamp = $field_value;
                } else {
                    $timestamp = strtotime( $field_value );
                }
                if ($timestamp===false) {
                    $output = __("Wrong date input","oxygen");
                }
                else {
                    $output = date( empty( $atts[ 'format' ] ) ? $default_format : $atts[ 'format' ], $timestamp );
                }
                break;
            case 'month':
                $default_format = 'Y-m';
                if ( isset($field['timestamp']) && $field['timestamp'] == true ) {
                    $timestamp = $field_value;
                } else {
                    $timestamp = strtotime( $field_value );
                }
                if ($timestamp===false) {
                    $output = __("Wrong date input","oxygen");
                }
                else {
                    $output = date( empty( $atts[ 'format' ] ) ? $default_format : $atts[ 'format' ], $timestamp );
                }
                break;
            case 'week':
                $default_format = 'W Y';
                if ( isset($field['timestamp']) && $field['timestamp'] == true ) {
                    $timestamp = $field_value;
                } else {
                    $timestamp = strtotime( $field_value );
                }
                if ($timestamp===false) {
                    $output = __("Wrong date input","oxygen");
                }
                else {
                    $output = date( empty( $atts[ 'format' ] ) ? $default_format : $atts[ 'format' ], $timestamp );
                }
                break;
            case 'key_value':

                if ( $atts['use_default_output_format'] == 'yes') {
                    ob_start();
                    if ($atts['settings_page']) {
                        $settings_pages = MBSP\Factory::get( array($atts['settings_page']), 'normal' );
                        if (isset($settings_pages[$atts['settings_page']])){
                            $option_name = $settings_pages[$atts['settings_page']]->__get('option_name');
                            rwmb_the_value( $field['id'], array(
                                'object_type' => 'setting',
                            ), $option_name );
                        }
                    }
                    else {
                        rwmb_the_value( $field['id'] );
                    }
                    $output = ob_get_clean();
                    break;
                }
                
                $separator = isset( $atts[ 'separator' ] ) ? $atts[ 'separator' ] : '';
                $display = isset( $atts[ 'display' ] ) ? $atts[ 'display' ] : 1;

                if ($separator=="&lt;br&gt;" || $separator=="&lt;br/&gt;" || $separator=="&lt;br /&gt;"){
                    $separator = "<br>";
                }

                if( ! is_array( $field_value ) ) {
                    $output = sanitize_text_field( $field_value );
                } else {
                    if( isset( $field_value['value'] ) ) {
                        $output = sanitize_text_field( $field_value[$display] );
                    } else {
                        $output = array();
                        foreach ( $field_value as $value ) {
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
            case 'select':
            case 'select_advanced':
            case 'image_select':
            case 'checkbox':
            case 'checkbox_list':
            case 'radio':
            case 'button_group':
            case 'autocomplete':
            case 'fieldset_text':
            case 'text_list':

                if ($field['type']=='fieldset_text'||$field['type']=='text_list') {
                    if( is_array( $field_value ) ) {
                        $field_value = array_filter($field_value);
                    }
                }

                $separator = isset( $atts[ 'separator' ] ) ? $atts[ 'separator' ] : '';
                $display = isset( $atts[ 'display' ] ) ? $atts[ 'display' ] : 'value';

                if ($separator=="&lt;br&gt;" || $separator=="&lt;br/&gt;" || $separator=="&lt;br /&gt;"){
                    $separator = "<br>";
                }

                if ($field['type']=='checkbox' && $display=='label' && $field_value) {
                    $field_value = $field['name'];
                }

                if( ! is_array( $field_value ) ) {
                    if ($display=='label' && isset($field['options'][$field_value]) ) {
                        $field_value = $field['options'][$field_value];
                    }
                    $output = sanitize_text_field( $field_value );
                } else {
                    if( isset( $field_value['value'] ) ) {
                        $output = sanitize_text_field( $field_value[$display] );
                    } else {
                        $output = array();
                        foreach ( $field_value as $value ) {
                            if( ! is_array( $value ) ) {
                                if ($display=='label' && isset($field['options'][$value]) ) {
                                    $output[] = $field['options'][$value];
                                }
                                else {
                                    $output[] = $value;
                                }
                            } else {
                                $output[] = $value[$display];
                            }
                        }
                        $output = join( $separator, $output );
                    }
                }
                break;
            case 'url':
                $separator = "";
                if( is_array( $field_value ) && count( $field_value ) > 1 ) $separator = !empty( $atts['separator'] ) ? $atts['separator'] : " ";

                if( !is_array( $field_value ) ) $field_value = array( $field_value ); //normalize to array

                $output_array = array();

                foreach ( $field_value as $value ) {
                    if( empty( $atts['url_link'] ) ) {
                        $output_array[] = sanitize_text_field( $value );
                    } else {
                        $link_text = !empty( $atts['link_text'] ) ? $atts['link_text'] : $value;
                        $output_array[] = '<a href="' . esc_url_raw( $value ) . '"' . ( empty( $atts['new_tab'] ) ? '' : ' target="_blank"' ) . '>' . $link_text . '</a>';
                    }
                }
                
                if ($separator=="&lt;br&gt;" || $separator=="&lt;br/&gt;" || $separator=="&lt;br /&gt;"){
                    $separator = "<br>";
                }

                $output = join( $separator, $output_array );
                break;
            case 'switch':
                if( !empty( $atts['output_custom_onoff'] ) && !empty( $field['on_label'] ) && !empty( $field['off_label'] ) ){
                    $output = sanitize_text_field( $field_value ? $field['on_label'] : $field['off_label'] );
                } else $output = $field_value ? 'true' : 'false';
                break;
            case 'osm':
                $output = $field_value;
                break;
			case 'map':
				if( empty( $this->options['api_key'] ) && empty( $field['api_key'] ) ) {
					$output = __( 'No Google Maps Key set', 'oxygen' );
				}else{
                    $output = $field_value;
				}
				break;
            case 'image':
            case 'image_upload':
            case 'image_advanced':
                
                $output_array = array();
                $separator = !empty( $atts['separator'] ) ? $atts['separator'] : " ";
                
                if ($separator=="&lt;br&gt;" || $separator=="&lt;br/&gt;" || $separator=="&lt;br /&gt;"){
                    $separator = "<br>";
                }

                if (is_array($field_value)) {
                    foreach ($field_value as $value) {
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
                        if( is_array( $value ) ) {
                            $image_url = wp_get_attachment_image_src( $value['ID'], $image_size )[0];
                            $image_attachment = get_post($value['ID']);
                        }
                        else {
                            $image_url = wp_get_attachment_image_src( $value, $image_size )[0];
                            $image_attachment = get_post($value);
                        }
        
                        if( empty( $atts['insert_type'] ) ) $atts['insert_type'] = 'image_element';

                        switch( $atts['insert_type'] ){
                            case "image_element":
                                $styles = "";
                                if (isset($atts['max_width'])) {
                                    $styles = "style=\"max-width:{$atts['max_width']}%\"";
                                }
                                $output_array[] = "<img $styles src=\"$image_url\"/>";
                                break;
                            case "image_url":
                                $output_array[] = $image_url;
                                break;
                            case "image_title":
                                $output_array[] = $image_attachment->post_title;
                                break;
                            case "image_caption":
                                $output_array[] = $image_attachment->post_excerpt;
                                break;
                            default:
                                $output_array[] = '<img src="' . $image_url . '"/>';
                                break;
                        }
                    }
                    $output = join( $separator, $output_array );
                }
                break;
            case 'single_image':
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
                if( is_array( $field_value ) ) {
                    $image_url = wp_get_attachment_image_src( $field_value['ID'], $image_size )[0];
                    $image_attachment = get_post($field_value['ID']);
                }
                else {
                    $image_url = wp_get_attachment_image_src( $field_value, $image_size )[0];
                    $image_attachment = get_post($field_value);
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
                        $output = $image_attachment->post_title;
                        break;
                    case "image_caption":
                        $output = $image_attachment->post_excerpt;
                        break;
                }
				break;
            case 'file_input':
                $attachment_id = isset($field_value['ID']) ? $field_value['ID'] : "";
                $attachment_url = isset($field_value['url']) ? $field_value['url'] : "";
                if( empty( $atts['file_link'] ) ) {
                    $output = $field_value;
                } else {
                    $link_text = !empty( $atts['link_text'] ) ? $atts['link_text'] : basename( $attachment_url );
                    $final_url = !empty( $atts['protect_file'] ) ? get_site_url() . "?oxy_download_metabox_file=" . $attachment_id : $attachment_url;
                    $output = '<a href="' . $final_url . '" target="_blank">' . $link_text . '</a>';
                }
                break;
            case 'file':
            case 'file_advanced':
            case 'file_upload':
                $output_array = array();
                $separator = !empty( $atts['separator'] ) ? $atts['separator'] : " ";

                if ($separator=="&lt;br&gt;" || $separator=="&lt;br/&gt;" || $separator=="&lt;br /&gt;"){
                    $separator = "<br>";
                }

                if (is_array($field_value)) {
                    foreach ($field_value as $value) {

                        $attachment_id = $value['ID'];
                        $attachment_url = $value['url'];
                        if( isset($atts['output_type']) && $atts['output_type'] == 'attachment_id' ){
                            $output_array[] = $attachment_id;
                        } else {
                            if( empty( $atts['file_link'] ) ) {
                                $output_array[] = $attachment_url;
                            } else {
                                $link_text = !empty( $atts['link_text'] ) ? $atts['link_text'] : basename( $attachment_url );
                                $final_url = !empty( $atts['protect_file'] ) ? get_site_url() . "?oxy_download_metabox_file=" . $attachment_id : $attachment_url;
                                $output_array[] = '<a href="' . $final_url . '" target="_blank">' . $link_text . '</a>';
                            }
                        }
                    }
                    $output = join( $separator, $output_array );
                }
                break;
            case 'video':
                $output = "";
                if (is_array($field_value)) {
                    foreach ($field_value as $value) {
                        $output .= 
                        "<video width=\"{$value['dimensions']['width']}\" height=\"{$value['dimensions']['height']}\" controls>
                            <source src=\"{$value['src']}\" type=\"{$value['type']}\">
                        </video>";
                    }
                }
                break;
            case 'post':
                $separator = "";
                if( is_array( $field_value ) && count( $field_value ) > 1 ) $separator = !empty( $atts['separator'] ) ? $atts['separator'] : " ";

                if( !is_array( $field_value ) ) $field_value = array( $field_value );

                $output_array = array();

                foreach ( $field_value as $value ) {

                    $value = get_post( $value );

                    if( isset($atts['insert_type']) && $atts['insert_type'] == 'post_url' ) {
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
                
                if ($separator=="&lt;br&gt;" || $separator=="&lt;br/&gt;" || $separator=="&lt;br /&gt;"){
                    $separator = "<br>";
                }

                $output = join( $separator, $output_array );
                break;
            case 'user': 

                $separator = "";
                if( is_array( $field_value ) && count( $field_value ) > 1 ) $separator = !empty( $atts['separator'] ) ? $atts['separator'] : " ";

                if( !is_array( $field_value ) ) $field_value = array( $field_value );

                $output_array = array();

                foreach ( $field_value as $value ) {

                    if( $atts['insert_type'] == 'user_url' ) {
                        if( empty( $atts['user_link'] ) ) {
                            $output_array[] = get_author_posts_url( $value );
                        } else {
                            $link_text = !empty( $atts['link_text'] ) ? $atts['link_text'] : get_the_author_meta('display_name', $value);
                            $output_array[] = '<a href="' . get_author_posts_url( $value ) . '"' . ( empty( $atts['new_tab'] ) ? '' : ' target="_blank"' ) . '>' . $link_text . '</a>';
                        }

                    } else {
                        $output_array[] = $value;
                    }

                }
                
                if ($separator=="&lt;br&gt;" || $separator=="&lt;br/&gt;" || $separator=="&lt;br /&gt;"){
                    $separator = "<br>";
                }

                $output = join( $separator, $output_array );
                break;
            case 'taxonomy': 
            case 'taxonomy_advanced': 

                if ( isset($atts['use_default_taxonomy_output_format']) && $atts['use_default_taxonomy_output_format'] == 'yes') {
                    ob_start();
                    echo "<ul>";
                    $terms = array();
                    if( !is_array( $field_value ) ) {
                        $terms[] = $field_value;
                    }
                    else {
                        $terms = $field_value;
                    }
                    foreach ( $terms as $value ) {

                        $value = get_term( $value );
    
                        if( isset($atts['insert_type']) && $atts['insert_type'] == 'term_url' ) {
                            if( empty( $atts['taxonomy_link'] ) ) {
                                $li = get_term_link( $value->term_id );
                            } else {
                                $link_text = !empty( $atts['link_text'] ) ? $atts['link_text'] : $value->name;
                                $li = '<a href="' . get_term_link( $value->term_id ) . '"' . ( empty( $atts['new_tab'] ) ? '' : ' target="_blank"' ) . '>' . $link_text . '</a>';
                            }
    
                        } else {
                            $li = $value->term_id;
                        }

                        echo "<li>$li</li>";

                    }
                    echo "</ul>";
                    $output = ob_get_clean();
                    break;
                }
                $separator = "";
                $terms = array();
                if( !is_array( $field_value ) ) {
                    $terms[] = $field_value;
                }
                else {
                    $terms = $field_value;
                }

                if( is_array( $terms ) && count( $terms ) > 1 ) {
                    $separator = !empty( $atts['separator'] ) ? $atts['separator'] : " ";
                    if ($separator=="&lt;br&gt;" || $separator=="&lt;br/&gt;" || $separator=="&lt;br /&gt;"){
                        $separator = "<br>";
                    }
                }

                $output_array = array();

                foreach ( $terms as $value ) {

                    $value = get_term( $value );

                    if (is_wp_error($value)) {
                        continue;
                    }

                    if( $atts['insert_type'] == 'term_url' ) {
                        if( empty( $atts['taxonomy_link'] ) ) {
                            $output_array[] = get_term_link( $value->term_id );
                        } else {
                            $link_text = !empty( $atts['link_text'] ) ? $atts['link_text'] : $value->name;
                            $output_array[] = '<a href="' . get_term_link( $value->term_id ) . '"' . ( empty( $atts['new_tab'] ) ? '' : ' target="_blank"' ) . '>' . $link_text . '</a>';
                        }

                    } else if( $atts['insert_type'] == 'term_name' ) {
                        $output_array[] = $value->name;
                    } else {
                        $output_array[] = $value->term_id;
                    }

                }

                $output = join( $separator, $output_array );
                break;
            case 'sidebar':
                dynamic_sidebar($field_value);
                break;
		}

		if( !empty( $atts['include_prepend_append'] ) ) {
		    if( !empty( $field['prepend'] ) ) $output = $field['prepend'] . $output;
            if( !empty( $field['append'] ) ) $output .= $field['append'];
        }

		return $output;
	}


    function metabox_url_handler( $atts ){
	    global $wpdb;
        $field = $this->get_field_by_path( $atts['settings_path'], $atts['settings_page'] );
		$output = '';
		if( empty( $field ) ) return $output;
		if( !isset( $field['type'] ) ) return $output;

		switch( $field['type'] ) {
			case 'image':
            case 'image_upload':
            case 'image_advanced':
            case 'single_image':

                if (!is_array($field['value']) || $field['type'] == "single_image"){
                    $field['value'] = array($field['value']);
                }

                foreach ($field['value'] as $value) {
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
                    if( is_array( $value ) ) {
                        if (isset($value['ID'])) {
                            $image_id = $value['ID'];
                        }
                        else {
                            foreach(array('original_image','full_url','name') as $image_key) {
                                if (!isset($value[$image_key])) {
                                    continue;
                                }
                                $url = str_replace(home_url(), '', $value[$image_key]);
                                $image_attachment = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE guid LIKE '%%%s';", $url ),ARRAY_A );
                                if ($image_attachment['ID']) {
                                    $image_id = $image_attachment['ID'];
                                    break;
                                }
                            }
                        }

                        $image_url = wp_get_attachment_image_src( $image_id, $image_size )[0];
                    }
                    else {
                        $image_url = wp_get_attachment_image_src( $value, $image_size )[0];
                    }
                    $output = $image_url;
                    // use only first image if there are multiple
                    break;
                }
				break;
            case 'text':
            case 'url':
                $output = is_array( $field["value"] ) ? $field["value"][0] : $field["value"];
                break;
            case 'email':
                $output = 'mailto:' . ( is_array( $field["value"] ) ? $field["value"][0] : $field["value"] );
                break;
            case 'tel':
                    $output = 'tel:' . ( is_array( $field["value"] ) ? $field["value"][0] : $field["value"] );
                    break;
            case 'file':
            case 'file_upload':
            case 'file_advanced': 
            case 'file_input':
                if( is_string( $field['value'] ) ) {
                    $output = $field['value'];
                } else if( is_int( $field['value'] ) ) {
                    $output = $wpdb->get_col($wpdb->prepare("SELECT guid FROM $wpdb->posts WHERE ID=%d;", $field['value'] ))[0];
                } else if( is_array($field['value'])) {
                    foreach ($field['value'] as $value) {
                        $output = isset($value['url']) ? $value['url'] : "";
                        break; // only need the first one
                    }
                } else {
                    $output = isset($field['value']['url']) ? $field['value']['url'] : "";
                }
                break;
            case 'post':
                if( is_array( $field['value'] ) ) $field['value'] = $field['value'][0];

                if( is_int( $field['value'] ) ) {
                    $field['value'] = get_post( $field['value'] );
                }

                $output = get_permalink( $field['value']->ID );
                break;
            case 'taxonomy_advanced':
                $terms = array();
                if( !is_array( $field['value'] ) ) {
                    $terms[] = $field['value'];
                }
                else {
                    $terms = $field['value'];
                }
                foreach ( $terms as $value ) {
                    $value = get_term( $value );
                    $output = get_term_link( $value->term_id );
                    break; // only look for the first one
                }
                break;
		}
		return esc_url_raw( $output );
	}


    function metabox_image_id_handler( $atts ){
	    global $wpdb;
        $field = $this->get_field_by_path( $atts['settings_path'], $atts['settings_page'] );
		$output = '';
		if( empty( $field ) ) return $output;
		if( !isset( $field['type'] ) ) return $output;

		switch( $field['type'] ) {
			case 'image':
            case 'image_upload':
            case 'image_advanced':
            case 'single_image':

                if (!is_array($field['value']) || $field['type'] == "single_image"){
                    $field['value'] = array($field['value']);
                }

                foreach ($field['value'] as $value) {
                    $image_id = null;
                    $image_attachment = null;
                    if( is_array( $value ) ) {
                        if (isset($value['ID'])) {
                            $image_id = $value['ID'];
                        }
                        else {
                            foreach(array('original_image','full_url','name') as $image_key) {
                                if (!isset($value[$image_key])) {
                                    continue;
                                }
                                $url = str_replace(home_url(), '', $value[$image_key]);
                                $image_attachment = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE guid LIKE '%%%s';", $url ),ARRAY_A );
                                if ($image_attachment['ID']) {
                                    $image_id = $image_attachment['ID'];
                                    break;
                                }
                            }
                        }

                    }
                    else {
                        $image_id = $value;
                    }
                    $output = $image_id;
                    // use only first image if there are multiple
                    break;
                }
				break;
		}

		return $output;
    }


    static function get_field_by_path( $field_path, $settings_page = "" ){

        if ($settings_page) {

            $settings_pages = MBSP\Factory::get( array($settings_page), 'normal' );
            if (isset($settings_pages[$settings_page])){
                $option_name = $settings_pages[$settings_page]->__get('option_name');
            }
            else {
                $option_name = "";
            }

            $settings = get_option($option_name,array());
            
            if (strpos($field_path, '/')) {

                $path = explode( '/', $field_path );
                $group_id = $path[0];
                $field_id = $path[sizeof($path)-1];
                $group = rwmb_get_field_settings( $group_id, array(
                    'object_type' => 'setting',
                ), $option_name);

                if ( is_array($group['fields']) ) {
                    foreach ($group['fields'] as $sub_field) {
                        if ($sub_field['id']==$field_id) {
                            $field = $sub_field;
                            break;
                        }
                    }
                }

                if ( is_array($settings[$group_id]) ) {
                    $field['value'] = isset( $settings[$group_id][$field_id] ) ? $settings[$group_id][$field_id] : '';
                }

                return $field;
            }
            else {
            
                $field = rwmb_get_field_settings( $field_path, array(
                    'object_type' => 'setting',
                ), $option_name);

                if (isset($settings[$field_path])) {
                    $field['value'] = $settings[$field_path];
                }
                else {
                    $field_path = "";
                }
            }

            return $field;
        };

        if (strpos($field_path, '/')) {

            $path = explode( '/', $field_path );
            $group_id = $path[0];
            $field_id = $path[sizeof($path)-1];
            $group = rwmb_get_field_settings( $group_id );
            $group_value = rwmb_meta( $group_id );

            if ( is_array($group['fields']) ) {
                foreach ($group['fields'] as $sub_field) {
                    if ($sub_field['id']==$field_id) {
                        $field = $sub_field;
                        break;
                    }
                }
            }

            $field['value'] = isset( $group_value[$field_id] ) ? $group_value[$field_id] : '';

        }
        else {
            $field = rwmb_get_field_settings( $field_path );
            $field['value'] = rwmb_meta( $field_path );
        }

        return $field;
    }
}


new OxygenMetaBoxIntegration();
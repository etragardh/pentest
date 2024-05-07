<?php
/**
 * Toolset integration for Oxygen
 *
 * @since 2.1
 */
class oxygen_toolset {
    private $types_helper;

	function __construct(){
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
	}

	function init() {
		if( ! defined( 'TYPES_VERSION' ) || intval( explode( '.', TYPES_VERSION )[0] ) < 3 ) {
            // do nothing
		} else {
			require 'toolset-types-helper.php';
			add_filter( 'oxygen_custom_dynamic_data', array( $this, 'init_dynamic_data' ), 10, 1 );
		}
	}

	function load_textdomain() {
		load_plugin_textdomain( 'oxygen-toolset', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	function init_dynamic_data( $dynamic_data ) {
		global $post;

		$this->types_helper = new Toolset_Types_Helper();
		$groups_and_fields = $this->types_helper->get_field_groups();

        // Toolset API returns group fields, we need the fields only
        $fields = array();
        foreach ( $groups_and_fields as $key => $value ) {

            if( 'ct_template' !== $post->post_type ) {
                // Check the "domains" a Types group should be applied to and compare with the current post type
                // in order to show it in the dynamic data dialog or not
                $args = array(
                    'name'        => str_replace('types-posts-','',$key),
                    'post_type'   => 'wp-types-group',
                    'post_status' => 'publish',
                    'numberposts' => 1
                );
                $group_id = get_posts($args)[0]->ID;
                $group_domains = get_post_meta( $group_id, '_wp_types_group_post_types', true);
                $group_domains = array_filter( explode(",", $group_domains) );
                if( !in_array( $post->post_type, $group_domains ) ) continue;
            } else {
                // For each Oxygen template, we must see if any of the configured post types matches any of the
                // Toolset Types Field group configured post types
                $oxygen_template_post_types = get_post_meta( $post->ID, 'ct_template_post_types', true );
                $args = array(
                    'name'        => str_replace('types-posts-','',$key),
                    'post_type'   => 'wp-types-group',
                    'post_status' => 'publish',
                    'numberposts' => 1
                );
                $group_id = get_posts($args)[0]->ID;
                $types_group_post_types = explode( ',' , get_post_meta(  $group_id, '_wp_types_group_post_types', true ) );
                $found = false;
                if (is_array($oxygen_template_post_types)) {
                    foreach ($oxygen_template_post_types as $oxygen_template_post_type) {
                        if( in_array( $oxygen_template_post_type, $types_group_post_types ) ) {
                            $found = true;
                            break;
                        }
                    }
                }
                // avoid adding the field group if not found
                if( !$found ) continue;
            }

            foreach ( $value['fields'] as $key => $field ) {
                $fields[] = $field;
            }
        }

        // Generate the settings for each field type
        $all_options = array_reduce( $fields, array( $this, "add_button" ), array() );

        $options_for_url_and_images = array_reduce( $fields, array( $this, "add_button_for_url_or_image" ), array() );

        if( count( $all_options ) > 0 ) {
            array_unshift($all_options, array('name' => __('Select the Toolset Types field', 'oxygen-toolset'), 'type' => 'heading'));
            $types_content = array(
                'name' => __('Toolset Types Field', 'oxygen-toolset'),
                'mode' => 'content',
                // Available modes: 'content', 'custom-field', 'link' and 'image'
                'position' => 'Post',
                // Available positions: 'Post', 'Author', 'User', 'Featured Image', 'Current User', 'Archive' 'Blog Info'
                'data' => 'types_content',
                'handler' => array($this, 'types_content_handler'),
                // Must be a callable
                'properties' => $all_options
            );
            $dynamic_data[] = $types_content;

            $types_content_meta = array(
                'name' => __('Toolset Types Field', 'oxygen-toolset'),
                'mode' => 'custom-field',
                // Available modes: 'content', 'custom-field', 'link' and 'image'
                'position' => 'Post',
                // Available positions: 'Post', 'Author', 'User', 'Featured Image', 'Current User', 'Archive' 'Blog Info'
                'data' => 'types_content_cpt',
                'handler' => array($this, 'types_content_handler_meta'),
                // Must be a callable
                'properties' => $all_options
            );
            $dynamic_data[] = $types_content_meta;
        }

        if( count( $options_for_url_and_images ) > 0 ) {
            $types_content_url = array(
                'name'      => __( 'Toolset Types Field', 'oxygen-toolset' ),
                'mode'       => 'link',
                // Available modes: 'content', 'custom-field', 'link' and 'image'
                'position'   => 'Post',
                // Available positions: 'Post', 'Author', 'User', 'Featured Image', 'Current User', 'Archive' 'Blog Info'
                'data'       => 'types_content_link',
                'handler'    => array( $this, 'types_content_handler_meta' ),
                // Must be a callable
                'properties' => $options_for_url_and_images
            );
            $dynamic_data[]   = $types_content_url;

            $types_content_image = array(
                'name'      => __( 'Toolset Types Field', 'oxygen-toolset' ),
                'mode'       => 'image',
                // Available modes: 'content', 'custom-field', 'link' and 'image'
                'position'   => 'Post',
                // Available positions: 'Post', 'Author', 'User', 'Featured Image', 'Current User', 'Archive' 'Blog Info'
                'data'       => 'types_content_img',
                'handler'    => array( $this, 'types_content_handler_meta' ),
                // Must be a callable
                'properties' => $options_for_url_and_images
            );
            $dynamic_data[]   = $types_content_image;
        }

        return $dynamic_data;

	}

    function add_button( $result, $option ) {

        $types_attributes = $this->types_helper->get_attributes_for_field_type( $option[ 'parameters' ]['metaType'] );

        $properties = $this->convert_types_attributes_to_oxygen_properties( $types_attributes, $option[ 'parameters' ]['metaType'] );

        $properties = $this->maybe_override_properties( $properties, $option[ 'parameters' ]['metaType'] );

        // Checkboxes field types doesn't support multiple instances
        if( !in_array( $option['parameters']['metaType'], array('checkbox', 'checkboxes') ) ) {
            // All multiple instances fields allow to specify an index or a separator
            $properties[] = array(
                'name' => __( 'This field has multiple instances', 'oxygen-toolset' ),
                'data' => 'is_multiple',
                'type' => 'checkbox',
                'value' => 'yes'
            );
            $properties[] = array(
                'name' => __( 'Index of the element to show (zero-based)', 'oxygen-toolset' ),
                'data' => 'index',
                'type' => 'text',
                'show_condition' => 'dynamicDataModel.is_multiple == \'yes\''
            );
            $properties[] = array(
                'name' => __( 'Or, specify a separator to show all the values', 'oxygen-toolset' ),
                'data' => 'separator',
                'type' => 'text',
                'show_condition' => 'dynamicDataModel.is_multiple == \'yes\''
            );
        }

        if( !empty( $option['name'] ) ) {
            $result[] = array(
                'name' => $option['name'],
                'data' => $option['parameters']['field'],
                'type' => 'button',
                'properties' => $properties
            );
        }

	    return $result;
    }

    function add_button_for_url_or_image( $result, $option ) {

        $valid_url_field_types = array( 'image', 'file', 'audio', 'textfield', 'url', 'video' );

        $types_attributes = $this->types_helper->get_attributes_for_field_type( $option[ 'parameters' ]['metaType'] );

        $properties = $this->convert_types_attributes_to_oxygen_properties( $types_attributes, $option[ 'parameters' ]['metaType'] );

        $properties = $this->maybe_override_properties( $properties, $option[ 'parameters' ]['metaType'] );

        if( !empty( $option['name'] ) && in_array( $option[ 'parameters' ]['metaType'], $valid_url_field_types ) ) {
            $result[] = array(
                'name' => $option['name'],
                'data' => $option['parameters']['field'],
                'type' => 'button',
                'properties' => $properties
            );
        }

        return $result;
    }

    private function maybe_override_properties( $properties, $field_type ) {

	    // Some Toolset's Field shortcode builder properties doesn't match the real attributes
        // needed in the actual shortcode, we need to manually define them:
	    switch( $field_type ) {
	        case 'checkboxes':
	            $properties = array();
                $properties[] = array(
                    'name'     => __( 'Output mode', 'oxygen-toolset' ),
                    'data'      => 'output',
                    'type'      => 'select',
                    'options'   => array( __( 'Raw', 'oxygen-toolset' ) => 'raw', __( 'Normal', 'oxygen-toolset' ) => 'normal' )
                );
                $properties[] = array(
                    'name' => __( 'Separator', 'oxygen-toolset' ),
                    'data' => 'separator',
                    'type' => 'text'
                );
                $properties[] = array(
                    'name' => __( 'Optional zero-based index number. To display the checked valued of the nth checkbox in the group.', 'oxygen-toolset' ),
                    'data' => 'option',
                    'type' => 'text'
                );
                $properties[] = array(
                    'name'     => __( 'State. This is only valid if an index number is specified.', 'oxygen-toolset' ),
                    'data'      => 'output',
                    'type'      => 'select',
                    'options'   => array( __( 'Unchecked', 'oxygen-toolset' ) => 'unchecked', __( 'Checked', 'oxygen-toolset' ) => 'checked' )
                );
	            break;
            case 'checkbox':
            $properties = array();
            $properties[] = array(
                'name'     => __( 'Output mode', 'oxygen-toolset' ),
                'data'      => 'output',
                'type'      => 'select',
                'options'   => array( __( 'Raw', 'oxygen-toolset' ) => 'raw', __( 'Normal', 'oxygen-toolset' ) => 'normal' )
            );
            $properties[] = array(
                'name'     => __( 'State.', 'oxygen-toolset' ),
                'data'      => 'output',
                'type'      => 'select',
                'options'   => array( __( 'Unchecked', 'oxygen-toolset' ) => 'unchecked', __( 'Checked', 'oxygen-toolset' ) => 'checked' )
            );
            break;
            case 'radio':
                $properties = array();
                $properties[] = array(
                    'name'     => __( 'Output mode', 'oxygen-toolset' ),
                    'data'      => 'output',
                    'type'      => 'select',
                    'options'   => array( __( 'Raw', 'oxygen-toolset' ) => 'raw', __( 'Normal', 'oxygen-toolset' ) => 'normal' )
                );
                break;
            case 'date':
                array_pop( $properties );
                $properties[2]['options']['Custom'] = "";
                $properties[] = array(
                    'name' => __( 'Custom format (Any valid WordPress date format). Backslash escaping is not safe because of technical reasons. Please use % for escaping instead, it will be handled as \. If you need to output %, use %%. ', 'oxygen-toolset' ),
                    'data' => 'format',
                    'type' => 'text',
                    'show_condition' => 'dynamicDataModel.format != \'F j, Y\' && dynamicDataModel.format != \'F j, Y g:i a\' && dynamicDataModel.format != \'d/m/y\''
                );
                break;
            case 'image':
                // Image fields are the biggest issue, we have to override all the properties for this one too
                $properties = array();
                $properties[] = array(
                    'name'     => __( 'Size. Width and Height will be ignored if size is set.', 'oxygen-toolset' ),
                    'data'      => 'size',
                    'type'      => 'select',
                    'options'   => array( __( 'Full', 'oxygen-toolset' ) => 'full', __( 'Large', 'oxygen-toolset' ) => 'large', __( 'Medium', 'oxygen-toolset' ) => 'medium', __( 'Thumbnail', 'oxygen-toolset' ) => 'thumbnail' ),
                    'change'    => 'scope.dynamicDataModel.width = ""; scope.dynamicDataModel.height = ""'
                );
                $properties[] = array(
                    'name' => __( 'Width', 'oxygen-toolset' ),
                    'data' => 'width',
                    'type' => 'text',
                    'change' => "scope.dynamicDataModel.size = scope.dynamicDataModel.width+'x'+scope.dynamicDataModel.height; scope.dynamicDataModel.size=''"
                );
                $properties[] = array(
                    'name' => __( 'Height', 'oxygen-toolset' ),
                    'data' => 'height',
                    'type' => 'text',
                    'change' => "scope.dynamicDataModel.size = scope.dynamicDataModel.width+'x'+scope.dynamicDataModel.height; scope.dynamicDataModel.size=''"
                );
                $properties[] = array(
                    'name' => __( 'Output IMG tag or raw image URL', 'oxygen-toolset' ),
                    'data' => 'url',
                    'type' => 'select',
                    'options'   => array( __( 'Image URL', 'oxygen-toolset' ) => 'true', __( 'IMG Tag', 'oxygen-toolset' ) => 'false' )
                );

                break;
        }
        return $properties;
    }

    private function convert_types_attributes_to_oxygen_properties( $types_attributes, $field_type ) {
	    $properties = Array();

        foreach ( $types_attributes as $field_name => $props ) {
            // Toolset Types Shortcode Generator Fields are "radio", "text" or "group" types only
            switch( $props[ 'type' ] ) {
                case 'radio':
                    // We can't render HTML in the dynamic data dialog dropdowns, so in the specific case of skype
                    // we won't use the labels provided by Toolset Types, which contains HTML. We use the keys instead.
                    if( $field_type == 'skype' ) {
                        foreach ($props['options'] as $key => $value) {
                            if (strpos($props['options'][$key], 'skypeassets.com') !== false) {
                                $props['options'][$key] = $key;
                            }
                        }
                    }
                    $properties[] = array(
                        'name'     => $props[ 'label' ],
                        'data'      => $field_name,
                        'type'      => 'select',
                        'options'   => array_flip( $props[ 'options' ] ),
                        'nullval'   => isset( $props[ 'defaultValue'] ) ? $props[ 'defaultValue'] : ''
                    );
                    break;
                case 'text':
                    $properties[] = array(
                        'name' => empty( $props[ 'label' ] ) ? empty( $props[ 'pseudolabel' ] ) ? $props[ 'description' ] : $props[ 'pseudolabel' ] : $props[ 'label' ],
                        'data' => $field_name,
                        'type' => 'text'
                    );
                    break;
                case 'group':
                    $properties[] = array(
                        'name' => empty( $props[ 'description' ] ) ? $props[ 'label' ] : $props[ 'description' ],
                        'type' => 'label'
                    );
                    $properties = array_merge( $properties, $this->convert_types_attributes_to_oxygen_properties( $props[ 'fields' ], $field_type ) );
                    break;
            }
        }

        return $properties;
    }

    function types_content_handler( $atts ){
	    // Intercept ajax calls to change behavior when we are within Oxygen Builder
        if( !empty( $_REQUEST[ 'action' ] ) && $_REQUEST[ 'action' ] == 'ct_render_shortcode' ) {
            // Never autoplay within oxygen builder
            if( !empty( $atts[ 'autoplay' ] ) ) $atts[ 'autoplay' ] = 'off';
        }
	    $field = $atts['settings_path'];
        unset( $atts['data'] );
	    unset( $atts['settings_path'] );
	    $output = types_render_field( $field, $atts );
	    return $output;
    }

    function types_content_handler_meta( $atts ){
        $field = $atts['settings_path'];
        unset( $atts['data'] );
        unset( $atts['settings_path'] );
        // Force raw output
        $atts['output'] = 'raw';
        $output = types_render_field( $field, $atts );
        return $output;
    }

}

new oxygen_toolset();

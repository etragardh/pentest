<?php

/**
 * Toolset Types Helper Class for Oxygen
 *
 * Toolset doesn't provide a public API other than the function to render the data. This
 * class is a helper to grab the custom fields data from the internal Types functions.
 *
 */
class Toolset_Types_Helper {

    private $types_shortcode_generator;
    private $reflected_class;
    private $field_attributes;

    function __construct() {

        $this->types_shortcode_generator = new Types_Shortcode_Generator();
        $this->types_shortcode_generator->initialize();
        $this->types_shortcode_generator->register_builtin_groups();
        $this->field_attributes = null;

        // Hack / Reverse-engineer to deal with private class members from external code
        try {
            $this->reflected_class = new ReflectionClass( get_class( $this->types_shortcode_generator ) );
        } catch ( ReflectionException $ex ) {
            $this->reflected_class = null;
        }
    }

    public function get_field_groups() {

        if( !empty( $this->reflected_class ) ) {
            $dialog_groups = $this->reflected_class->getProperty( 'dialog_groups' );
            $dialog_groups->setAccessible( true );
        } else {
            return array();
        }
        return $dialog_groups->getValue( $this->types_shortcode_generator );
    }

    public function get_attributes_for_field_type( $field_type ) {

        if( is_null( $this->field_attributes ) ) {
            $this->get_field_attributes();
        }

        if( isset( $this->field_attributes[ $field_type ][ 'displayOptions' ][ 'fields' ] ) ) {
            return $this->field_attributes[ $field_type ][ 'displayOptions' ][ 'fields' ];
        } else return Array();
    }

    private function get_field_attributes() {

        if( !empty( $this->reflected_class ) ) {
            $field_attributes = $this->reflected_class->getMethod( 'get_fields_expected_attributes' );
            $field_attributes->setAccessible( true );
        } else {
            $this->field_attributes = array();
        }
        $this->field_attributes = $field_attributes->invoke( $this->types_shortcode_generator );
    }

}

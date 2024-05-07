<?php 

/**
 * Get Repeater instance and return rendered HTML
 * 
 * @since 2.0+
 * @author Gagan
 */

oxygen_vsb_ajax_request_header_check();

header('Content-Type: application/json');

$component_json = file_get_contents('php://input');
$component 		= json_decode( $component_json, true );
$options 		= $component['options']['original'];
$models 		= $component['models'];

$parentQuery 		= isset($component['queryOptions']) ? $component['queryOptions'] : false;;
$acfRepeaterFields 	= isset($component['acfRepeaterFields']) ? $component['acfRepeaterFields'] : false;
$metaBoxFields 		= isset($component['metaBoxGroupFields']) ? $component['metaBoxGroupFields'] : false;
$repeaterFields 	= $acfRepeaterFields ? $acfRepeaterFields : $metaBoxFields;


global $oxygen_vsb_components, $ct_for_builder;

// flag for ajax request coming via builder
$ct_for_builder = true;

/**
 * in case the incoming shortcodes in advanced query are not signed, 
 * they need to be provided a valid signature in order to render a preview, 
 * as they were just inserted and Oxygen will not assign 
 * signatures to them until the page is saved
 */

if($options['wp_query'] === 'advanced') {
	
	function assign_signatures_on_fly(&$children) {
		foreach ($children as $key => $value) {
			if(is_array($value)) {
            	assign_signatures_on_fly($children[$key]);
            }
			elseif (stripos($value, '[oxygen') !== false) {
                $children[$key] = ct_sign_oxy_dynamic_shortcode(array($value));
            }            
		}
	}

	assign_signatures_on_fly($options['wp_query_advanced']);
}

$response = $oxygen_vsb_components['repeater']->parse_shortcodes_map($models, $options, $parentQuery, $repeaterFields);

echo json_encode($response);

die();
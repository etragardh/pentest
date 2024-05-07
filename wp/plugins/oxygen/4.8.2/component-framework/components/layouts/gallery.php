<?php 

/**
 * Get Easy Posts instance and return rendered HTML
 * Editing something here also edit it in ajax.php!
 * 
 * @since 2.0
 * @author Ilya K.
 */

oxygen_vsb_ajax_request_header_check();

$component_json = file_get_contents('php://input');
$component 		= json_decode( $component_json, true );
$options 		= $component['options']['original'];

// inside Repeater
if (isset($component['repeaterFields'][0])) {

    $group = rwmb_meta($component['repeaterFields'][0]);
    $repeaterListIndex = $component['repeaterListIndex'] || 0; // get first repeater iteration if inside repeater and no index set

    if (isset($group[$repeaterListIndex])) {        
        global $meta_box_current_group_fields;
        $meta_box_current_group_fields = $group[$repeaterListIndex];
    }
}

$options['preview'] = true;
// add selector to proper CSS generation
$options['selector'] = $component['options']['selector'];

global $oxygen_vsb_components;
echo $oxygen_vsb_components['gallery']->shortcode($options);
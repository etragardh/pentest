<?php 

/**
 * Get Comments List instance and return rendered HTML
 * Editing something here also edit it in ajax.php!
 * 
 * @since 2.0
 * @author Ilya K.
 */

oxygen_vsb_ajax_request_header_check();

$component_json = file_get_contents('php://input');
$component 		= json_decode( $component_json, true );
$options 		= $component['options']['original'];

global $oxygen_signature;

$shortcode_atts = array(
	'preview' => 'true',
	'ct_options' => "{\"original\":{\"code-php\":\"".base64_encode($options['code-php'])."\",\"code-css\":\"".base64_encode($options['code-css'])."\"}}",
);

// Generate signature
$signature = $oxygen_signature->generate_signature_shortcode_string( 'oxy_comments', $shortcode_atts, '');
// Generate output
$shortcode = "[oxy_comments {$signature} preview=true ct_options='{$shortcode_atts['ct_options']}']";

echo do_shortcode($shortcode);
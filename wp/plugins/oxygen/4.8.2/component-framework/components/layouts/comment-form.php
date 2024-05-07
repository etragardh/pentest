<?php 

/**
 * Get Comment Form instance and return rendered HTML.
 * Editing something here also edit it in ajax.php!
 * 
 * @since 2.0
 * @author Ilya K.
 */

oxygen_vsb_ajax_request_header_check();

global $oxygen_signature;

$shortcode_atts = array(
	'preview' => 'true',
	'ct_options' => '{}'
);

// Generate signature
$signature = $oxygen_signature->generate_signature_shortcode_string( 'oxy_comment_form', $shortcode_atts, '');
// Generate output
$shortcode = "[oxy_comment_form {$signature} preview=true ct_options='{$shortcode_atts['ct_options']}']";

echo do_shortcode($shortcode);

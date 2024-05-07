<?php

/**
 * Callback to show "SVG Sets" on settings page
 *
 * @since 0.2.1
 */

function ct_svg_sets_callback() {
	
	$svg_sets_names = get_option("ct_svg_sets_names", array() );
	$builtin_sets = array(
		"fontawesome" => "Font Awesome",
		"linearicons" => "Linearicons"
	);

	// Cache version validation
	$svg_sets_rebuild = false;
	$svg_sets_version = '4.8.1';
	$svg_sets_cache_version = get_option("ct_svg_sets_version", '0');
	
	if( version_compare($svg_sets_cache_version, $svg_sets_version, '<') ) {
		update_option("ct_svg_sets_version", $svg_sets_version, get_option("oxygen_options_autoload"));
		$svg_sets_rebuild = true;
	}
	
	if ( $svg_sets_rebuild || empty( $svg_sets_names ) ) {

		foreach ($builtin_sets as $key => $name) {
			
			// import default file	
			$file_content = file_get_contents( CT_FW_PATH . "/admin/includes/$key/symbol-defs.svg" );

			$xml = simplexml_load_string($file_content);

			foreach($xml->children() as $def) {
				if($def->getName() == 'defs') {

					foreach($def->children() as $symbol) {
						
						if($symbol->getName() == 'symbol') {
							$symbol['id'] = str_replace(' ', '', $name).$symbol['id'];
							
						}
					}
				}
				
			}
			
			$xml_string = $xml->asXML();
			$xml_array = str_split($xml_string, 800000);
			$svg_sets_names[$name] = count($xml_array);

			foreach ($xml_array as $id => $value) {
				$svg_sets[$name." ".$id] = $value;
			}

		}

		if (is_array($svg_sets)) {
			foreach($svg_sets as $key => $set) {
				// save SVG sets to DB
				update_option("ct_svg_sets_".$key, $set, get_option("oxygen_options_autoload") );
			}
			update_option("ct_svg_sets_names", $svg_sets_names, get_option("oxygen_options_autoload") );
		}
		
	}


	$sets = oxy_get_svg_sets();

	// check if user wants to delete an SVG set
	if ( isset( $_POST['ct_delete_svg_set'] ) ) {

		if (defined('CT_FREE')) {
			die( 'Not Allowed');
		}

		$set_to_delete = sanitize_text_field( $_POST['ct_delete_svg_set'] );

		if( isset( $svg_sets_names[$set_to_delete] ) && FALSE === array_search( $set_to_delete, $builtin_sets ) ) {
			// we don't split custom users' sets, so it always ends with 0
			delete_option("ct_svg_sets_".$set_to_delete." 0");
			unset( $svg_sets_names[$set_to_delete] );
			update_option("ct_svg_sets_names", $svg_sets_names, get_option("oxygen_options_autoload") );
		}

	} elseif ( isset( $_FILES['ct_svg_set_file'] ) ) { // check if user sumbit any file

		if (defined('CT_FREE')) {
			die( 'Not Allowed');
		}
		
		$containsSymbols = true;

		// check file type
		$file_type = $_FILES['ct_svg_set_file']['type'];
		
		if ( $file_type != "image/svg+xml" ) {
			
			_e("<b>Wrong file type</b>. Please make sure you upload '.svg' file", "component-theme");
		}
		else {
			
			// get content
			$file_content = file_get_contents( $_FILES['ct_svg_set_file']['tmp_name'] );

			// get set's name
			$post_set_name = sanitize_text_field( $_POST['ct_svg_set_name'] );

			// check name
			$set_name_base 	= ( $post_set_name ) ? $post_set_name : "SVG Set";
			$set_name 		= $set_name_base;
			$set_number		= "1";

			while ( isset( $svg_sets[$set_name] ) ) {
				
				$set_number++;
				$set_name = $set_name_base . " " . $set_number;
			}

			$xml = simplexml_load_string($file_content);

			foreach($xml->children() as $def) {
				if($def->getName() == 'defs') {
					foreach($def->children() as $symbol) {
						//echo $symbol->getName()."\n";
						if($symbol->getName() == 'symbol') {
							$symbol['id'] = str_replace(' ', '', $set_name).$symbol['id'];
						}
						else {
							$containsSymbols = false;
						}
					}
				} else {
					$containsSymbols = false;
				}
			}
			
			$file_content = $xml->asXML();
			
			if($containsSymbols) {
				
				// add uploaded .svg file content to sets
				// we don't split custom users' sets, so it always ends with 0
				$result = update_option("ct_svg_sets_".$set_name." 0", $file_content, get_option("oxygen_options_autoload") );
				
				// save SVG sets to DB
				if( FALSE === $result ) {
					_e("<b>Error</b>. Couldn't add the SVG icon set. Try increasing the 'max_allowed_packet' database setting or use smaller file.", "component-theme");
				}
				else {
					$svg_sets_names = get_option("ct_svg_sets_names", array());
					// we don't split custom users' sets, so it has 1 part only
					$svg_sets_names[$set_name] = 1;

					update_option("ct_svg_sets_names", $svg_sets_names, get_option("oxygen_options_autoload") );
				}
			}
			else {
				_e("<b>Wrong file format</b>. The .svg file does not contain any symbol definitions", "component-theme");
			}
		}

	}

	require('views/svg-sets-page.php');
}
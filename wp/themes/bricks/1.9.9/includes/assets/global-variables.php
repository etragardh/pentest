<?php
namespace Bricks;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Assets_Global_Variables {
	public function __construct() {
		add_action( 'add_option_bricks_global_variables', [ $this, 'updated' ], 10, 2 );
		add_action( 'update_option_bricks_global_variables', [ $this, 'updated' ], 10, 2 );
	}

	public function updated( $old_value, $value ) {
		self::generate_css_file( $value );
	}

	public static function generate_css_file( $global_variables ) {
		$file_name     = 'global-variables.min.css';
		$css_file_path = Assets::$css_dir . "/$file_name";

		if ( $global_variables ) {
			$css = Assets::format_variables_as_css( $global_variables );
			$css = Assets::minify_css( $css );

			$file = fopen( $css_file_path, 'w' );
			fwrite( $file, $css );
			fclose( $file );

			do_action( 'bricks/generate_css_file', 'global-variables', $file_name );

			return $file_name;
		} else {
			if ( file_exists( $css_file_path ) ) {
				unlink( $css_file_path );
			}
		}
	}
}

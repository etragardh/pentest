<?php

Class OXYGEN_VSB_Signature {
	private $option_name = 'oxygen_private_key';

	private $shortcode_arg_prefix = 'ct_sign_';

	private $private_key = null;

	private $key_length = 32;

	private $algo = 'sha256';

	/**
	 * Generate Oxygen private key that is used for signing shortcodes
	 *
	 * @return string Oxygen private key
	 */
	function generate_key() {
		$key = wp_generate_password( $this->key_length, true, true );
		add_option( $this->option_name, $key );
		return $key;
	}

	/**
	 * Load the Oxygen private key
	 * If it does not exist it will be generated
	 *
	 * @return string Oxygen private key
	 */
	function get_key() {
		if ( $this->private_key !== null ) {
			// Only query for key once per request
			return $this->private_key;
		} else {
			$key = get_option( $this->option_name, false );
			if ( false === $key ) {
				// If we don't have an existing key, create one
				$key = $this->generate_key();
			}
			$this->private_key = $key;
		}
		return $key;
	}

	/**
	 * Return the complete signature name made up of prefix + algorithm name
	 *
	 * @param string $alg Hash algorithm to override signature name with
	 *
	 * @return string Complete signature argument name
	 */
	function get_shortcode_signature_arg( $alg = null ) {
		if ( null === $alg ) {
			$alg = $this->algo;
		}
		return $this->shortcode_arg_prefix . $alg;

	}

	/**
	 * Verify signature that is stored in $args array
	 *
	 * @param null $name
	 * @param array $args
	 * @param null $content
	 *
	 * @return bool
	 */
	function verify_signature( $name = null, $args = array(), $content = null ) {

		$enabledVerification = get_option('oxygen_vsb_enable_signature_validation');

		if(!$enabledVerification) {
			return true;
		}

		$key = $this->get_key();
		// Extract signature from args
		$signature_arg = $this->get_shortcode_signature_arg();
		if ( !empty( $args[ $signature_arg ] ) ) {
			$signature = $args[ $signature_arg ];
			unset( $args[ $signature_arg ] );
			$hash = hash_hmac( $this->algo, serialize( array( $name, wp_unslash( $args ), wp_unslash( $content ) ) ), $key );
			if ( true === hash_equals( $hash, $signature ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Generate a hash/signature of the name, args, and content
	 *
	 * @param null $name
	 * @param array $args
	 * @param null $content
	 *
	 * @return false|string
	 */
	function generate_signature( $name = null, $args = array(), $content = null ) {
		$key = $this->get_key();
		// Extract signature from args
		$signature_arg = $this->get_shortcode_signature_arg();
		if ( !empty( $args[ $signature_arg ] ) ) {
			unset( $args[ $signature_arg ] );
		}
		// Generally the hash is checked against data from the DB which will be unslashed so we normalize here.
		$hash = hash_hmac( $this->algo, serialize( array( $name, wp_unslash( $args ), wp_unslash( $content ) ) ), $key );

		return $hash;
	}

	/**
	 * Helper function to return the string that can be included in a shortcode containing the signature
	 *
	 * @param $name
	 * @param array $args
	 * @param null|string $content
	 *
	 * @return string
	 */
	function generate_signature_shortcode_string( $name, $args = array(), $content = null ) {
		$hash = $this->generate_signature( $name, $args, $content );
		$signature_arg = $this->get_shortcode_signature_arg();
		$output = "{$signature_arg}='{$hash}'";

		return $output;
	}
}

$oxygen_signature = new OXYGEN_VSB_Signature();
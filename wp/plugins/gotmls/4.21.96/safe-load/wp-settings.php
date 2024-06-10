<?php
if (is_file("../../../../wp-includes/version.php"))
	require_once("../../../../wp-includes/version.php");
if (is_file("../../../../wp-includes/default-constants.php"))
	require_once("../../../../wp-includes/default-constants.php");              
if ( !defined('WP_DEBUG_DISPLAY') )
 define( 'WP_DEBUG_DISPLAY', true );

if (!function_exists("apply_filters")) {
function apply_filters($filter, $value) {
	return $value;
}}
if (!function_exists("wp_load_translations_early")) {
function wp_load_translations_early() {
	return false;
}}
if (!function_exists("wp_debug_backtrace_summary")) {
function wp_debug_backtrace_summary() {
	return false;
}}
if (!function_exists("is_multisite")) {
function is_multisite() {
	return false;
}}
if (!function_exists("is_wp_error")) {
function is_wp_error() {
	return false;
}}
if (!function_exists("mbstring_binary_safe_encoding")) {
function mbstring_binary_safe_encoding( $reset = false ) {
    static $encodings = array();
    static $overloaded = null;
 
    if ( is_null( $overloaded ) )
        $overloaded = function_exists( 'mb_internal_encoding' ) && ( ini_get( 'mbstring.func_'.'overload' ) & 2 );
 
    if ( false === $overloaded )
        return;
 
    if ( ! $reset ) {
        $encoding = mb_internal_encoding();
        array_push( $encodings, $encoding );
        mb_internal_encoding( 'ISO-8859-1' );
    }
 
    if ( $reset && $encodings ) {
        $encoding = array_pop( $encodings );
        mb_internal_encoding( $encoding );
    }
}}
if (!function_exists("reset_mbstring_encoding")) {
function reset_mbstring_encoding() {
    mbstring_binary_safe_encoding( true );
}}
if (!function_exists("_wp_filter_build_unique_id")) {
function _wp_filter_build_unique_id($tag, $function, $priority) {
        global $wp_filter;
        static $filter_id_count = 0;

        if ( is_string($function) )
                return $function;

        if ( is_object($function) ) {
                // Closures are currently implemented as objects
                $function = array( $function, '' );
        } else {
                $function = (array) $function;
        }

        if (is_object($function[0]) ) {
                // Object Class Calling
                if ( function_exists('spl_object_hash') ) {
                        return spl_object_hash($function[0]) . $function[1];
                } else {
                        $obj_idx = get_class($function[0]).$function[1];
                        if ( !isset($function[0]->wp_filter_id) ) {
                                if ( false === $priority )
                                        return false;
                                $obj_idx .= isset($wp_filter[$tag][$priority]) ? count((array)$wp_filter[$tag][$priority]) : $filter_id_count;
                                $function[0]->wp_filter_id = $filter_id_count;
                                ++$filter_id_count;
                        } else {
                                $obj_idx .= $function[0]->wp_filter_id;
                        }

                        return $obj_idx;
                }
        } elseif ( is_string( $function[0] ) ) {
                // Static Calling
                return $function[0] . '::' . $function[1];
        }
}}

if (!function_exists("add_filter")) {
function add_filter( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
        global $wp_filter, $merged_filters;

        $idx = _wp_filter_build_unique_id($tag, $function_to_add, $priority);
        $wp_filter[$tag][$priority][$idx] = array('function' => $function_to_add, 'accepted_args' => $accepted_args);
        unset( $merged_filters[ $tag ] );
        return true;
}}
if (!function_exists("has_filter")) {
function has_filter($tag, $function_to_check = false) {
        global $wp_filter;
        $has = ! empty( $wp_filter[ $tag ] );
        if ( $has ) {
                $exists = false;
                foreach ( $wp_filter[ $tag ] as $callbacks ) {
                        if ( ! empty( $callbacks ) ) {
                                $exists = true;
                                break;
                        }
                }
                if ( ! $exists )
                        $has = false;
        }
        if ( false === $function_to_check || false == $has )
                return $has;
        if ( !$idx = _wp_filter_build_unique_id($tag, $function_to_check, false) )
                return false;
        foreach ( (array) array_keys($wp_filter[$tag]) as $priority )
                if ( isset($wp_filter[$tag][$priority][$idx]) )
                        return $priority;
        return false;
}}

if (is_file("../../../../wp-includes/wp-db.php"))
	require_once("../../../../wp-includes/wp-db.php");

$wpdb = new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST );

if (!function_exists("delete_option")) {
function delete_option($index) {
	global $wpdb, $table_prefix;
	$wpdb->delete($table_prefix."options", array( 'option_name' => "'$index'"));
}}

if (!function_exists("update_option")) {
function update_option($index, $value = "") {
	global $wpdb, $table_prefix;
	if (is_array($value))
		$value = serialize($value);
	$return = $wpdb->update($table_prefix."options", array('option_value' => $value), array('option_name' => $index));
	return $return;
}}

if (!function_exists("get_option")) {
function get_option($index, $value = array()) {
	global $wpdb, $table_prefix;
	$qry = "SELECT option_value FROM {$table_prefix}options WHERE option_name = '$index'";
	$return = $wpdb->get_var( $qry );
	if (@unserialize($return) && is_array(@unserialize($return)))
		return unserialize($return);
	else
		return $return;
}}

if (!function_exists("admin_url")) {
function admin_url($path = '') {
	$url = get_option('siteurl').'wp-admin/';
	if ($path && is_string($path))
		$url .= ltrim($path, '/');
	return $url;
}}

if (!function_exists("_deep_replace")) {
function _deep_replace( $search, $subject ) {
    $subject = (string) $subject;
 
    $count = 1;
    while ( $count ) {
        $subject = str_replace( $search, '', $subject, $count );
    }
 
    return $subject;
}}

if (!function_exists("valid_unicode")) {
function valid_unicode( $i ) {
    return ( 0x9 == $i || 0xa == $i || 0xd == $i ||
            ( 0x20 <= $i && $i <= 0xd7ff ) ||
            ( 0xe000 <= $i && $i <= 0xfffd ) ||
            ( 0x10000 <= $i && $i <= 0x10ffff ) );
}}

if (!function_exists("wp_kses_normalize_entities2")) {
function wp_kses_normalize_entities2( $matches ) {
	if ( empty( $matches[1] ) ) {
		return '';
	}

	$i = $matches[1];
	if ( valid_unicode( $i ) ) {
		$i = str_pad( ltrim( $i, '0' ), 3, '0', STR_PAD_LEFT );
		$i = "&#$i;";
	} else {
		$i = "&amp;#$i;";
	}

	return $i;
}}

if (!function_exists("wp_kses_normalize_entities3")) {
function wp_kses_normalize_entities3( $matches ) {
	if ( empty( $matches[1] ) ) {
		return '';
	}

	$hexchars = $matches[1];
	return ( ! valid_unicode( hexdec( $hexchars ) ) ) ? "&amp;#x$hexchars;" : '&#x' . ltrim( $hexchars, '0' ) . ';';
}}

if (!function_exists("wp_kses_named_entities")) {
function wp_kses_named_entities($matches) {
	global $allowedentitynames;
	if (empty($matches[1]))
		return '';
	$i = $matches[1];
	return (is_array($allowedentitynames) && in_array($i, $allowedentitynames, true)) ?  "&$i;" : "&amp;$i;";
}}

if (!function_exists("wp_kses_normalize_entities")) {
function wp_kses_normalize_entities($string) {
	// Disarm all entities by converting & to &amp;
	$string = str_replace( '&', '&amp;', $string );
	$string = preg_replace_callback( '/&amp;([A-Za-z]{2,8}[0-9]{0,2});/', 'wp_kses_named_entities', $string );
	$string = preg_replace_callback( '/&amp;#(0*[0-9]{1,7});/', 'wp_kses_normalize_entities2', $string );
	$string = preg_replace_callback( '/&amp;#[Xx](0*[0-9A-Fa-f]{1,6});/', 'wp_kses_normalize_entities3', $string );
	return $string;
}}

<?php
$popup_controls = \Bricks\Popups::get_controls();

/**
 * Use only popup controls of type 'separator' or with key 'css' or 'themeStyle'
 *
 * To avoid showing popuplimit controls, etc.
 *
 * @since 1.8.2
 */
$popup_controls = array_filter(
	$popup_controls,
	function( $control, $key ) {
		return ( strpos( $key, 'popupLimit' ) === false && ( $control['type'] === 'separator' || isset( $control['css'] ) ) || isset( $control['themeStyle'] ) );
	},
	ARRAY_FILTER_USE_BOTH
);

return [
	'name'     => 'popup',
	'controls' => $popup_controls,
];

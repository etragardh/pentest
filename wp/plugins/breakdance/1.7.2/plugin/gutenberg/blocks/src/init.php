<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package CGB
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * Assets enqueued:
 * 1. blocks.style.build.css - Frontend + Backend.
 * 2. blocks.build.js - Backend.
 * 3. blocks.editor.build.css - Backend.
 *
 * @uses {wp-blocks} for block type registration & related functions.
 * @uses {wp-element} for WP Element abstraction — structure of blocks.
 * @uses {wp-i18n} to internationalize the block's text.
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */
function breakdance_global_block_assets()
{
	// phpcs:ignore
	// Register block styles for both frontend + backend.
	wp_register_style(
		'breakdance-global-block-css', // Handle.
		plugins_url('dist/blocks.style.build.css', dirname(__FILE__)), // Block style CSS.
		is_admin() ? ['wp-editor'] : [], // Dependency to include the CSS after it.
		(string) filemtime(plugin_dir_path(__DIR__) . 'dist/blocks.style.build.css') // Version: File modification time.
	);

	// Register block editor script for backend.
	wp_register_script(
		'breakdance-global-block-js', // Handle.
		plugins_url('/dist/blocks.build.js', dirname(__FILE__)), // Block.build.js: We register the block here. Built with Webpack.
		array('wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor'), // Dependencies, defined above.
		(string) filemtime(plugin_dir_path(__DIR__) . 'dist/blocks.build.js'), // Version: filemtime — Gets file modification time.
		true// Enqueue the script in the footer.
	);

	// Register block editor styles for backend.
	wp_register_style(
		'breakdance-global-block-editor-css', // Handle.
		plugins_url('dist/blocks.editor.build.css', dirname(__FILE__)), // Block editor CSS.
		array('wp-edit-blocks'), // Dependency to include the CSS after it.
		(string) filemtime(plugin_dir_path(__DIR__) . 'dist/blocks.editor.build.css') // Version: File modification time.
	);

	// WP Localized globals. Use dynamic PHP stuff in JavaScript via `cgbGlobal` object.
	wp_localize_script(
		'breakdance-global-block-js',
		'breakdanceGlobalBlock', // Array containing dynamic data for a JS Global.
		[
			// TODO: This array might be huge one day, probably a good idea to use ajax.
			'blocks' => get_posts([
				'post_type' => BREAKDANCE_BLOCK_POST_TYPE,
				'showposts' => -1
			]),
			'blockPreviewUrl' => home_url('?breakdance=true&breakdance_preview=true&page_id=%%BLOCKID%%&breakdance_iframe=true'),
			'blockPostTypeUrl' => admin_url('admin.php?page='.BREAKDANCE_BLOCK_POST_TYPE)
			// Add more data here that you want to access from `cgbGlobal` object.
		]
	);

	/**
	 * Register Gutenberg block on server-side.
	 *
	 * Register the block on server-side to ensure that the block
	 * scripts and styles for both frontend and backend are
	 * enqueued when the editor loads.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type#enqueuing-block-scripts
	 * @since 1.16.0
	 */
	register_block_type(
		'breakdance/global-block', [
			'attributes' => [
				'blockId' => [
					'default' => '',
					'type' => 'string'
				]
			],
			// Enqueue blocks.style.build.css on both frontend & backend.
			'style' => 'breakdance-global-block-css',
			// Enqueue blocks.build.js in the editor only.
			'editor_script' => 'breakdance-global-block-js',
			// Enqueue blocks.editor.build.css in the editor only.
			'editor_style' => 'breakdance-global-block-editor-css',
			'render_callback' => '\Breakdance\Gutenberg\breakdance_block_render_callback',
		]
	);
}

// Hook: Block assets.
add_action('init', 'breakdance_global_block_assets');

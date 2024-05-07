<?php
namespace Bricks\Integrations\Rank_Math;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Rank_Math {
	public function __construct() {}

	public static function register() {
		if ( ! class_exists( 'RankMath' ) ) {
			return;
		}

		$instance = new self();

		add_action( 'wp_enqueue_scripts', [ $instance, 'wp_enqueue_scripts' ], 10 );
		add_action( 'admin_enqueue_scripts', [ $instance, 'wp_enqueue_scripts' ], 10 );

		add_filter( 'rank_math/sitemap/content_before_parse_html_images', [ $instance, 'add_bricks_content_for_parse_html_images' ], 10, 2 );
	}

	/**
	 * Feed Rank Math with the rendered Bricks data to build the images sitemap
	 *
	 * @since 1.5.5
	 */
	public function add_bricks_content_for_parse_html_images( $content, $post_id ) {
		// Set the post_id in 'Database' to avoid errors
		\Bricks\Database::$page_data['preview_or_post_id'] = $post_id;

		if ( ! \Bricks\Helpers::render_with_bricks( $post_id ) ) {
			return $content;
		}

		$bricks_data = get_post_meta( $post_id, BRICKS_DB_PAGE_CONTENT, true );

		// Page has Bricks data: Render it & feed Rank Math logic
		if ( $bricks_data ) {
			return \Bricks\Frontend::render_data( $bricks_data );
		}

		return $content;
	}

	/**
	 * Add Bricks integration with Rank Math to the builder
	 *
	 * @since 1.3.2
	 */
	public function wp_enqueue_scripts( $hook_suffix ) {
		if ( bricks_is_builder() || ( is_admin() && $hook_suffix == 'post.php' ) ) {
			wp_enqueue_script( 'bricks-rank-math', BRICKS_URL_ASSETS . 'js/integrations/rank-math.min.js', [], filemtime( BRICKS_PATH_ASSETS . 'js/integrations/rank-math.min.js' ) );

			if ( is_admin() ) {
				wp_localize_script(
					'bricks-rank-math',
					'bricksRankMath',
					[
						'postId'           => get_the_ID(),
						'nonce'            => wp_create_nonce( 'bricks-nonce' ),
						'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
						'renderWithBricks' => \Bricks\Helpers::is_post_type_supported() && \Bricks\Helpers::render_with_bricks()
					]
				);
			}
		}
	}
}

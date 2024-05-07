<?php
namespace Bricks;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Element_Svg extends Element {
	public $category = 'media';
	public $name     = 'svg';
	public $icon     = 'ti-vector';
	public $tag      = 'svg';

	public function get_label() {
		return 'SVG';
	}

	public function get_keywords() {
		return [ 'image' ];
	}

	public function set_controls() {
		$this->controls['file'] = [
			'tab'  => 'content',
			'type' => 'svg',
		];

		$this->controls['height'] = [
			'tab'      => 'content',
			'label'    => esc_html__( 'Height', 'bricks' ),
			'type'     => 'number',
			'units'    => true,
			'css'      => [
				[
					'property' => 'height',
				],
			],
			'required' => [ 'file', '!=', '' ],
		];

		$this->controls['width'] = [
			'tab'      => 'content',
			'label'    => esc_html__( 'Width', 'bricks' ),
			'type'     => 'number',
			'units'    => true,
			'css'      => [
				[
					'property' => 'width',
				],
			],
			'required' => [ 'file', '!=', '' ],
		];

		$this->controls['strokeWidth'] = [
			'tab'      => 'content',
			'label'    => esc_html__( 'Stroke width', 'bricks' ),
			'type'     => 'number',
			'min'      => 1,
			'css'      => [
				[
					'property'  => 'stroke-width',
					'selector'  => ' *',
					'important' => true,
				]
			],
			'required' => [ 'file', '!=', '' ],
		];

		$this->controls['stroke'] = [
			'tab'      => 'content',
			'label'    => esc_html__( 'Stroke color', 'bricks' ),
			'type'     => 'color',
			'css'      => [
				[
					'property'  => 'stroke',
					'selector'  => ' :not([stroke="none"])',
					'important' => true,
				]
			],
			'required' => [ 'file', '!=', '' ],
		];

		$this->controls['fill'] = [
			'tab'      => 'content',
			'label'    => esc_html__( 'Fill', 'bricks' ),
			'type'     => 'color',
			'css'      => [
				[
					'property'  => 'fill',
					'selector'  => ' :not([fill="none"])',
					'important' => true,
				]
			],
			'required' => [ 'file', '!=', '' ],
		];
	}

	public function render() {
		$svg_path = ! empty( $this->settings['file']['id'] ) ? get_attached_file( $this->settings['file']['id'] ) : false;
		$svg      = $svg_path ? Helpers::file_get_contents( $svg_path ) : false;

		if ( ! $svg ) {
			return $this->render_element_placeholder( [ 'title' => esc_html__( 'No SVG selected.', 'bricks' ) ] );
		}

		// Render SVG + root attriutes (ID, classes, etc.)
		echo self::render_svg( $svg, $this->attributes['_root'] );
	}

	public static function _render_builder() { ?>
		<script type="text/x-template" id="tmpl-bricks-element-svg">
			<icon-svg v-if="settings.file" :name="name" :iconSettings="settings"></icon-svg>
			<div v-else v-html="renderElementPlaceholder()"></div>
		</script>
		<?php
	}
}

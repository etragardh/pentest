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
		$this->controls['source'] = [
			'label'       => esc_html__( 'Source', 'bricks' ),
			'type'        => 'select',
			'placeholder' => esc_html__( 'File', 'bricks' ),
			'inline'      => true,
			'options'     => [
				''            => esc_html__( 'File', 'bricks' ),
				'dynamicData' => esc_html__( 'Dynamic data', 'bricks' ),
				'code'        => esc_html__( 'Code', 'bricks' ),
			],
		];

		$this->controls['file'] = [
			'type'     => 'svg',
			'required' => [ 'source', '=', '' ],
		];

		$this->controls['dynamicData'] = [
			'label'    => esc_html__( 'Dynamic data', 'bricks' ),
			'type'     => 'text',
			'inline'   => true,
			'desc'     => esc_html__( 'Supported field types', 'bricks' ) . ': ' . esc_html__( 'File', 'bricks' ) . ', ' . esc_html__( 'Image', 'bricks' ),
			'required' => [ 'source', '=', 'dynamicData' ],
		];

		// Allow adding SVG code if code execution is not disabled
		$user_can_execute_code = Capabilities::current_user_can_execute_code();
		$execution_allowed     = apply_filters( 'bricks/code/allow_execution', ! Database::get_setting( 'executeCodeDisabled', false ) );

		if ( $user_can_execute_code && $execution_allowed ) {
			$this->controls['code'] = [
				'label'    => esc_html__( 'Code', 'bricks' ),
				'type'     => 'code',
				'desc'     => sprintf(
					esc_html__( 'Please ensure that the SVG code you paste in here does not contain any potentially malicious code. You can run it first through a free online SVG cleaner like %s', 'bricks' ),
					'<a href="https://svgomg.net/" target="_blank">https://svgomg.net/</a>'
				),
				'required' => [ 'source', '=', 'code' ],
			];
		} else {
			$this->controls['codeExecutionNotAllowedInfo'] = [
				'content'  => esc_html__( 'Code execution not allowed.', 'bricks' ),
				'type'     => 'info',
				'required' => [ 'source', '=', 'code' ],
			];
		}

		$this->controls['height'] = [
			'label' => esc_html__( 'Height', 'bricks' ),
			'type'  => 'number',
			'units' => true,
			'css'   => [
				[
					'property' => 'height',
				],
			],
		];

		$this->controls['width'] = [
			'label' => esc_html__( 'Width', 'bricks' ),
			'type'  => 'number',
			'units' => true,
			'css'   => [
				[
					'property' => 'width',
				],
			],
		];

		$this->controls['strokeWidth'] = [
			'label' => esc_html__( 'Stroke width', 'bricks' ),
			'type'  => 'number',
			'min'   => 1,
			'css'   => [
				[
					'property'  => 'stroke-width',
					'selector'  => ' *',
					'important' => true,
				]
			],
		];

		$this->controls['stroke'] = [
			'label' => esc_html__( 'Stroke color', 'bricks' ),
			'type'  => 'color',
			'css'   => [
				[
					'property'  => 'stroke',
					'selector'  => ' :not([stroke="none"])',
					'important' => true,
				]
			],
		];

		$this->controls['fill'] = [
			'label' => esc_html__( 'Fill', 'bricks' ),
			'type'  => 'color',
			'css'   => [
				[
					'property'  => 'fill',
					'selector'  => ' :not([fill="none"])',
					'important' => true,
				]
			],
		];
	}

	public function render() {
		$svg    = '';
		$source = $this->settings['source'] ?? 'file';

		// Default: Get SVG from file ID
		if ( $source === 'file' && ! empty( $this->settings['file']['id'] ) ) {
			$svg_path = get_attached_file( $this->settings['file']['id'] );
			$svg      = $svg_path ? Helpers::file_get_contents( $svg_path ) : false;
		}

		// Get SVG from dynamic data
		if ( $source === 'dynamicData' && ! empty( $this->settings['dynamicData'] ) ) {
			$svg_data = $this->render_dynamic_data_tag( $this->settings['dynamicData'], 'image' );
			$file_id  = ! empty( $svg_data[0] ) && is_numeric( $svg_data[0] ) ? $svg_data[0] : false;

			if ( $file_id ) {
				$svg_path = get_attached_file( $file_id );
				$svg      = $svg_path ? Helpers::file_get_contents( $svg_path ) : false;
			}
		}

		// Get SVG HTML from Code element (to run it through all the required sanitisations)
		elseif ( $source === 'code' && ! empty( $this->settings['code'] ) ) {
			if ( class_exists( '\Bricks\Element_Code' ) ) {
				$code = new Element_Code(
					[
						'settings' => [
							'code'        => $this->settings['code'],
							'executeCode' => true,
							'noRootForce' => true, // To ensure only the svg code is returned (without any div wrapper)
						],
					]
				);

				ob_start();
				$code->load();
				$code->init();
				$svg = ob_get_clean();
			}
		}

		if ( ! $svg ) {
			return $this->render_element_placeholder( [ 'title' => esc_html__( 'No SVG selected.', 'bricks' ) ] );
		}

		// Render SVG + root attriutes (ID, classes, etc.)
		echo self::render_svg( $svg, $this->attributes['_root'] );
	}
}

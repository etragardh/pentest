<?php

/**
 * Comment Form Component Class
 *
 * @since 1.5
 */

Class CT_Data_Comment_Form extends CT_Component {

	function __construct( $options ) {

		// run initialization
		$this->init( $options );

		// remove component button from fundamentals folder
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );

		// add component button to WordPress > Data folder
		add_action("ct_toolbar_data_folder", array( $this, "component_button") );

		add_filter( 'template_include', array( $this, 'ct_data_single_template'), 100 );

		// Add shortcodes
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
		add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );
		add_shortcode( $this->options['tag'] . '_ajax', array( $this, 'add_shortcode_ajax' ) );
		add_oxygen_element( $this->options['tag'] . '_ajax', array( $this, 'add_shortcode_ajax' ) );
	}


	/**
	 * Add a [ct_data_comment_form] shortcode to WordPress
	 *
	 * @since 1.5
	 */

	function add_shortcode( $atts, $content, $name ) {

		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
            return '';
        }

		$options = $this->set_options( $atts );

		ob_start();

		if ( have_posts() ) the_post();

		echo "<".esc_attr($options['tag'])." id=\"".esc_attr($options['selector'])."\" class=\"".esc_attr($options['classes'])."\" "; do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); echo ">";
		//comment_form();
		comments_template();
		echo "</".esc_attr($options['tag']).">";

		return ob_get_clean();
	}

	/**
	 * Add a [ct_data_comment_form_ajax] shortcode to WordPress
	 *
	 * @since 1.5
	 */

	function add_shortcode_ajax( $atts ) {

		ob_start();

		if ( have_posts() ) the_post();

		//comment_form();
		comments_template();

		return ob_get_clean();
	}

	/**
	 * Add WordPress folder button
	 *
	 * @since 1.5
	 */

	function component_button() { 
		
		if (oxygen_hide_element_button($this->options['tag'])) {
			return;
		}
		
		?>

		<div class="oxygen-add-section-element"
 			data-searchid="<?php echo strtolower( preg_replace('/\s+/', '_', sanitize_text_field( $this->options['name'] ) ) ) ?>"
			ng-click="iframeScope.addComponent('<?php echo esc_attr($this->options['tag']); ?>','data')">
			<img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata.svg' />
			<?php echo esc_html($this->options['name']); ?>
		</div>

	<?php }

	/**
	 * This function hijacks the template to return special template that renders the code results
	 * for the ct_data_title element to load the content into the builder for preview.
	 *
	 * @since 1.5
	 */

	function ct_data_single_template( $template ) {

		$new_template = '';

		if(isset($_REQUEST['action']) && stripslashes($_REQUEST['action']) == 'ct_render_data_component') {
			$nonce  	= $_REQUEST['nonce'];
			$post_id 	= $_REQUEST['post_id'];

			// check nonce
			if ( ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
				// This nonce is not valid.
				die( 'Security check' );
			}

			if ( file_exists(dirname(dirname( __FILE__)) . '/layouts/' . 'data.php') ) {
				$new_template = dirname(dirname( __FILE__)) . '/layouts/' . 'data.php';
			}
		}

		if ( '' != $new_template ) {
			return $new_template ;
		}

		return $template;
	}

}

global $oxygen_vsb_components;
$oxygen_vsb_components['data_comment_form']  = new CT_Data_Comment_Form (

	array(
		'name' 		=> 'Comment Form',
		'tag' 		=> 'ct_data_comment_form',
		'data_type' => true,
		'params' 	=> array(
			array(
				"type" 			=> "tag",
				"heading" 		=> __("Tag"),
				"param_name" 	=> "tag",
				"hidden"		=> true,
				"value" 		=> array (
					"div" => "DIV",
				),
				"css" 			=> false,
			),
			array(
				"type" 			=> "flex-layout",
				"heading" 		=> __("Layout Child Elements", "oxygen"),
				"param_name" 	=> "flex-direction",
				"css" 			=> true,
			),
			array(
				"type" 			=> "checkbox",
				"heading" 		=> __("Allow multiline"),
				"param_name" 	=> "flex-wrap",
				"value" 		=> "nowrap",
				"true_value" 	=> "wrap",
				"false_value" 	=> "nowrap",
				"condition" 	=> "flex-direction=row"
			),
			array(
				"type" => "positioning",
			),
			array(
				"type" 			=> "measurebox",
				"heading" 		=> __("Width"),
				"param_name" 	=> "width",
				"value" 		=> "",
			),
			array(
				"type" 			=> "colorpicker",
				"heading" 		=> __("Background color"),
				"param_name" 	=> "background-color",
			),
		),
		'advanced' => false,
	)
);

<?php 

/**
 * CT_Toolset_View Class
 *
 * @since 2.1
 */

Class CT_Toolset_View extends CT_Component {

	function __construct( $options ) {

		// run initialization
		$this->init( $options );
		
		// change button place
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );

        add_filter( 'template_include', array( $this, 'ct_shortcode_single_template'), 100 );

		// Add shortcodes
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
		add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );

		add_action( "init", array( $this, "maybe_show_component" ), 10 );

	}

	function maybe_show_component() {
	    // At this point, all external plugin's constants are defined
		if( defined( 'WPV_VERSION' ) && version_compare( WPV_VERSION, '2.6' ) >= 0 ) {
		    // Add this component button
			add_action("ct_toolbar_data_folder", array( $this, "component_button"), 8 );
		}

    }

	/**
	 * Add a [ct_toolset_view] shortcode to WordPress
	 *
	 * @since 2.1
	 */

    function add_shortcode( $atts, $content ) {

        $options = $this->set_options( $atts );

        ob_start();

        $full_shortcode = '[wpv-view name="' . $options['view'] . '"]';

        ?><<?php echo esc_attr($options['tag']) ?> id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo esc_attr($options['classes']); ?>"><?php echo do_shortcode( $full_shortcode ); ?></<?php echo esc_attr($options['tag']) ?>><?php

        return ob_get_clean();
    }

	
	/**
	 * Add Dynamic Data folder button
	 *
	 * @since 2.1
	 */
	
	function component_button() { 

		if (oxygen_hide_element_button('ct_toolset_view')) {
			return;
		}
		
		?>

        <div class="oxygen-add-section-element"
			data-searchid="<?php echo strtolower( preg_replace('/\s+/', '_', sanitize_text_field( $this->options['name'] ) ) ) ?>"
             ng-click="iframeScope.addComponent('ct_toolset_view');">
            <img src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/add-icons/dynamicdata.svg' />
            <?php _e("Toolset View","oxygen"); ?>
        </div>

	<?php }

    function ct_shortcode_single_template( $template ) {

        $new_template = '';

        if(isset($_REQUEST['action']) && stripslashes($_REQUEST['action']) == 'ct_render_shortcode') {
            $nonce  	= $_REQUEST['nonce'];
            $post_id 	= $_REQUEST['post_id'];

            // check nonce
            if ( ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
                // This nonce is not valid.
                die( 'Security check' );
            }

            if ( file_exists(dirname(dirname( __FILE__)) . '/layouts/' . 'shortcode.php') ) {
                $new_template = dirname(dirname( __FILE__)) . '/layouts/' . 'shortcode.php';
            }
        }

        if ( '' != $new_template ) {
            return $new_template ;
        }

        return $template;
    }

}

$args = array(
    'post_type'   => 'view',
    'post_status' => 'publish',
    'numberposts' => -1
);
$views_object = get_posts($args);
$views_array = [];
foreach ($views_object as $item) {
    $views_array[ $item->post_name ] = $item->post_title;
}

global $oxygen_vsb_components;
$oxygen_vsb_components['toolset_view'] = new CT_Toolset_View ( array(
		'name' 		=> 'Toolset View',
		'tag' 		=> 'ct_toolset_view',
		'shortcode'	=> true,
		'params' 	=> array(
							array(
								"param_name" 	=> "view",
								"value" 		=> $views_array,
								"type" 			=> "view_dropdown",
								"heading" 		=> __("Toolset View","oxygen"),
                                "css" 			=> false,
							),
							array(
								"type" 			=> "tag",
								"heading" 		=> __("Tag"),
								"param_name" 	=> "tag",
								"value" 		=> array (
													"div" => "DIV",
													"p" => "P",
													"h1" => "H1",
													"h2" => "H2",
													"h3" => "H3",
													"h4" => "H4",
													"h5" => "H5",
													"h6" => "H6",
													"section" 	=> "section",
													"footer" 	=> "footer",
													"header" 	=> "header",
													"article" 	=> "article",
													"main" 		=> "main",
													"figcaption"=> "figcaption",
													"time" 		=> "time",
													"summary" 	=> "summary",
													"details" 	=> "details",
													"aside" 	=> "aside",
													"figure" 	=> "figure",
													"hgroup" 	=> "hgroup",
													"mark" 		=> "mark",
													"nav" 		=> "nav",
												),
								"css" 			=> false,
							),
						),
		'advanced' => array(
					"other" => array(
						"values" 	=> array (
							'placeholder-width-unit' => 'px',
							'placeholder-height-unit' => 'px',
							)
					),
                    'allow_shortcodes' => true,
				)
		)
	);

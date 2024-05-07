<?php


Class CT_Video extends CT_Component {

	function __construct( $options ) {

		// run initialization
		$this->init( $options );
		
		// Add shortcode
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
		add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );

		// change component button place
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
		add_action("oxygen_basics_components_visual", array( $this, "component_button" ) );
	}

	/**
	 * Add a [ct_video] shortcode to WordPress
	 *
	 * @since 1.5
	 */

	function add_shortcode( $atts, $content = null, $name = null ) {

		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		$options = $this->set_options( $atts );
		$lazy = $options['lazy'] ? 'loading="' . $options['lazy'] . '"' : null;

        ob_start();
        
        ?><div id="<?php echo esc_attr($options['selector']) ?>" class="<?php echo esc_attr($options['classes']); ?>" <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>>
        <?php
        	if($options['use_custom'] !== '1') {

        		$embed_src = $this->getYoutubeVimeoEmbedUrl(do_shortcode($options['src']));
        ?>
        <div class="oxygen-vsb-responsive-video-wrapper"><iframe <?php echo $lazy; ?> src="<?php echo esc_attr($embed_src); ?>" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>
        <?php
        	}
        	else {
        ?>
		<div class="oxygen-vsb-responsive-video-wrapper oxygen-vsb-responsive-video-wrapper-custom"><?php echo oxygen_base64_decode_for_json($options['custom_code']); ?></div>
		<?php
			}
		?>
        </div><?php

		$outputContent = ob_get_clean();

		$outputContent = apply_filters('oxygen_vsb_after_component_render', $outputContent, $this->options, $name);

        return $outputContent;
	}
	

	/**
     * Parse YouTube/Vimeo page urls to embeddable links
     * 
     * @since 2.1
     * @author Gagan (ported to PHP by Ilya K.)
     */

    function getYoutubeVimeoEmbedUrl($url) {

        if( !$url || trim($url) === '' ) {
            return $url;
        }

        $pattern = "/(youtube\.com|youtu\.be|vimeo\.com)\/(watch\?v\=)?(.*)/";
        preg_match($pattern, $url, $matches);

        if($matches[1] && $matches[3] && strpos($matches[3], '/') === false) {
            if($matches[1] == 'youtube.com' || $matches[1] == 'youtu.be') {
                if (sizeof(explode('&', $matches[3]))>1) {
                    $matches[3] = explode('&',$matches[3])[0];
                }
                return 'https://www.youtube.com/embed/' . $matches[3];
            }
            else if($matches[1] == 'vimeo.com') {
                return 'https://player.vimeo.com/video/' . $matches[3];
            }
        }
        else {
            return $url;
        }
    }
}

/**
 * Create Video Component Instance
 * 
 * @since 1.5
 */

global $oxygen_vsb_components;
$oxygen_vsb_components['video'] = new CT_Video ( 

		array( 
			'name' 		=> 'Video',
			'tag' 		=> 'ct_video',
			'params' 	=> array(
					array(
						"type" 			=> "textfield",
						"heading" 		=> __("YouTube / Vimeo URL", "oxygen"),
						"param_name" 	=> "src",
						"value" 		=> "https://www.youtube.com/watch?v=7yae8GvpPVo",
						"css"			=> false,
						"dynamicdatacode"	=>	'<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesCustomFieldMode" callback="iframeScope.insertShortcodeToSrc">data</div>'
					),
					array(
						"param_name"	=> "embed_src",
						"value"			=> "https://www.youtube.com/embed/7yae8GvpPVo",
						"css"			=> false,
						"hidden"		=> true
					),
					array(
						"type" 			=> "radio",
						"heading" 		=> __("Video Aspect Ratio", "oxygen"),
						"param_name" 	=> "video-padding-bottom",
						"value" 		=> array(
											'75%' 	=> __("4:3 (standard)", "oxygen"),
											'56.25%' 	=> __("16:9 (standard widescreen)", "oxygen"),
											'41.84%' 	=> __("21:9 (Cinematic widescreen)", "oxygen")
										),
						"default"		=> '56.25%',
						"css"			=> false,
						"line_breaks"	=> true,
					),
					array(
						"type" 			=> "checkbox",
						"heading" 		=> __("Embed Iframe", "oxygen"),
						"param_name" 	=> "use-custom",
						"value" 		=> "0",
						"true_value" 	=> "1",
						"false_value" 	=> "0",
						"label" 		=> __("Manually Paste Iframe Code", "oxygen"),
						"css" 			=> false,
					),

					array(
						"type" 			=> "textarea",
						"heading" 		=> __("Custom Code Here"),
						"param_name" 	=> "custom-code",
						"value" 		=> "",
						"css" 			=> false,
						"condition" 	=> "use-custom=1"
					),

					array(
						"type" 			=> "checkbox",
						"label" 		=> __("Lazy Load", "oxygen"),
						"param_name" 	=> "lazy",
						"value" 		=> "",
						"true_value" 	=> "lazy",
						"false_value" 	=> "",
						"css" 			=> false
					),
					
				)
		)
);

?>

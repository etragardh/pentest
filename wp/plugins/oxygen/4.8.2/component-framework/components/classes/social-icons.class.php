<?php 

/**
 * Social Icons component
 *
 * @since 2.0
 * @author Ilya K.
 */

Class Oxy_Social_Icons extends CT_Component {

	var $social_icons_svg_added = false;
	var $networks = array('facebook', 'instagram', 'twitter', 'linkedin', 'rss', 'youtube');
	var $network_colors = array(
		'facebook' 	=> '#3b5998',
		'instagram' => '#c32aa3',
		'twitter' 	=> '#00b6f1',
		'linkedin' 	=> '#007bb6',
		'rss' 		=> '#ee802f',
		'youtube' 	=> '#ff0000'
	);
	var $network_hover_colors = array(
		'facebook' 	=> '#5b79b8',
		'instagram' => '#e34ac3',
		'twitter' 	=> '#20d6ff',
		'linkedin' 	=> '#209bd6',
		'rss' 		=> '#ffa04f',
		'youtube' 	=> '#ff4444'
	);

	function __construct( $options ) {

		// run initialization
		$this->init( $options );

		// Add shortcodes
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
		add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );

		// change component button place
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
		add_action("oxygen_helpers_components_composite", array( $this, "component_button" ) );

		// add specific styles
		add_filter("oxy_component_css_styles", array( $this, "css_styles" ), 10, 4);

		// include only for builder
		if (isset( $_GET['oxygen_iframe'] )) {
			add_action("wp_footer", array( $this, "svg_output") );
		}
	}


	/**
	 * Add a [oxy_social_icons] shortcode to WordPress
	 *
	 * @since 2.0
	 */

	function add_shortcode( $atts, $content = null, $name = null ) {

		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}
		
		// add SVG to footer only once
		if ($this->social_icons_svg_added === false) {
			add_action("wp_footer", array( $this, "svg_output") );
			$this->social_icons_svg_added = true;
		}

		$options = $this->set_options( $atts );
		$icons_html = '';
		foreach ($this->networks as $network) {

			$blank = '';

			if ($options['icon_style'] == 'blank') {
				$blank = '-blank';
			}

			if ($options['icon_'.$network]) {
				$icons_html .= "<a href='".$options['icon_'.$network]."' target='".$options['icon_link_target']."' class='oxy-social-icons-".$network."'><svg><title>".$options['title_'.$network]."</title><use xlink:href='#oxy-social-icons-icon-".$network.$blank."'></use></svg></a>";
			}
		}

		ob_start();
		
		?><div id="<?php echo esc_attr($options['selector']); ?>" class="<?php echo esc_attr($options['classes']); ?>" <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>><?php echo $icons_html; ?></div><?php

		return ob_get_clean();
	}

	
	/**
	 * Output icons SVG 
	 *
	 * @since 2.0
	 */

	function svg_output() { ?>

		<svg style="position: absolute; width: 0; height: 0; overflow: hidden;" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
		   <defs>
		      <symbol id="oxy-social-icons-icon-linkedin" viewBox="0 0 32 32">
		         <title>linkedin</title>
		         <path d="M12 12h5.535v2.837h0.079c0.77-1.381 2.655-2.837 5.464-2.837 5.842 0 6.922 3.637 6.922 8.367v9.633h-5.769v-8.54c0-2.037-0.042-4.657-3.001-4.657-3.005 0-3.463 2.218-3.463 4.509v8.688h-5.767v-18z"></path>
		         <path d="M2 12h6v18h-6v-18z"></path>
		         <path d="M8 7c0 1.657-1.343 3-3 3s-3-1.343-3-3c0-1.657 1.343-3 3-3s3 1.343 3 3z"></path>
		      </symbol>
		      <symbol id="oxy-social-icons-icon-facebook" viewBox="0 0 32 32">
		         <title>facebook</title>
		         <path d="M19 6h5v-6h-5c-3.86 0-7 3.14-7 7v3h-4v6h4v16h6v-16h5l1-6h-6v-3c0-0.542 0.458-1 1-1z"></path>
		      </symbol>
		      <symbol id="oxy-social-icons-icon-pinterest" viewBox="0 0 32 32">
		         <title>pinterest</title>
		         <path d="M16 2.138c-7.656 0-13.863 6.206-13.863 13.863 0 5.875 3.656 10.887 8.813 12.906-0.119-1.094-0.231-2.781 0.050-3.975 0.25-1.081 1.625-6.887 1.625-6.887s-0.412-0.831-0.412-2.056c0-1.925 1.119-3.369 2.506-3.369 1.181 0 1.756 0.887 1.756 1.95 0 1.188-0.756 2.969-1.15 4.613-0.331 1.381 0.688 2.506 2.050 2.506 2.462 0 4.356-2.6 4.356-6.35 0-3.319-2.387-5.638-5.787-5.638-3.944 0-6.256 2.956-6.256 6.019 0 1.194 0.456 2.469 1.031 3.163 0.113 0.137 0.131 0.256 0.094 0.4-0.106 0.438-0.338 1.381-0.387 1.575-0.063 0.256-0.2 0.306-0.463 0.188-1.731-0.806-2.813-3.337-2.813-5.369 0-4.375 3.175-8.387 9.156-8.387 4.806 0 8.544 3.425 8.544 8.006 0 4.775-3.012 8.625-7.194 8.625-1.406 0-2.725-0.731-3.175-1.594 0 0-0.694 2.644-0.863 3.294-0.313 1.206-1.156 2.712-1.725 3.631 1.3 0.4 2.675 0.619 4.106 0.619 7.656 0 13.863-6.206 13.863-13.863 0-7.662-6.206-13.869-13.863-13.869z"></path>
		      </symbol>
		      <symbol id="oxy-social-icons-icon-youtube" viewBox="0 0 32 32">
		         <title>youtube</title>
		         <path d="M31.681 9.6c0 0-0.313-2.206-1.275-3.175-1.219-1.275-2.581-1.281-3.206-1.356-4.475-0.325-11.194-0.325-11.194-0.325h-0.012c0 0-6.719 0-11.194 0.325-0.625 0.075-1.987 0.081-3.206 1.356-0.963 0.969-1.269 3.175-1.269 3.175s-0.319 2.588-0.319 5.181v2.425c0 2.587 0.319 5.181 0.319 5.181s0.313 2.206 1.269 3.175c1.219 1.275 2.819 1.231 3.531 1.369 2.563 0.244 10.881 0.319 10.881 0.319s6.725-0.012 11.2-0.331c0.625-0.075 1.988-0.081 3.206-1.356 0.962-0.969 1.275-3.175 1.275-3.175s0.319-2.587 0.319-5.181v-2.425c-0.006-2.588-0.325-5.181-0.325-5.181zM12.694 20.15v-8.994l8.644 4.513-8.644 4.481z"></path>
		      </symbol>
		      <symbol id="oxy-social-icons-icon-rss" viewBox="0 0 32 32">
		         <title>rss</title>
		         <path d="M4.259 23.467c-2.35 0-4.259 1.917-4.259 4.252 0 2.349 1.909 4.244 4.259 4.244 2.358 0 4.265-1.895 4.265-4.244-0-2.336-1.907-4.252-4.265-4.252zM0.005 10.873v6.133c3.993 0 7.749 1.562 10.577 4.391 2.825 2.822 4.384 6.595 4.384 10.603h6.16c-0-11.651-9.478-21.127-21.121-21.127zM0.012 0v6.136c14.243 0 25.836 11.604 25.836 25.864h6.152c0-17.64-14.352-32-31.988-32z"></path>
		      </symbol>
		      <symbol id="oxy-social-icons-icon-twitter" viewBox="0 0 32 32">
		         <title>twitter</title>
		         <path d="M32 7.075c-1.175 0.525-2.444 0.875-3.769 1.031 1.356-0.813 2.394-2.1 2.887-3.631-1.269 0.75-2.675 1.3-4.169 1.594-1.2-1.275-2.906-2.069-4.794-2.069-3.625 0-6.563 2.938-6.563 6.563 0 0.512 0.056 1.012 0.169 1.494-5.456-0.275-10.294-2.888-13.531-6.862-0.563 0.969-0.887 2.1-0.887 3.3 0 2.275 1.156 4.287 2.919 5.463-1.075-0.031-2.087-0.331-2.975-0.819 0 0.025 0 0.056 0 0.081 0 3.181 2.263 5.838 5.269 6.437-0.55 0.15-1.131 0.231-1.731 0.231-0.425 0-0.831-0.044-1.237-0.119 0.838 2.606 3.263 4.506 6.131 4.563-2.25 1.762-5.075 2.813-8.156 2.813-0.531 0-1.050-0.031-1.569-0.094 2.913 1.869 6.362 2.95 10.069 2.95 12.075 0 18.681-10.006 18.681-18.681 0-0.287-0.006-0.569-0.019-0.85 1.281-0.919 2.394-2.075 3.275-3.394z"></path>
		      </symbol>
		      <symbol id="oxy-social-icons-icon-instagram" viewBox="0 0 32 32">
		         <title>instagram</title>
		         <path d="M16 2.881c4.275 0 4.781 0.019 6.462 0.094 1.563 0.069 2.406 0.331 2.969 0.55 0.744 0.288 1.281 0.638 1.837 1.194 0.563 0.563 0.906 1.094 1.2 1.838 0.219 0.563 0.481 1.412 0.55 2.969 0.075 1.688 0.094 2.194 0.094 6.463s-0.019 4.781-0.094 6.463c-0.069 1.563-0.331 2.406-0.55 2.969-0.288 0.744-0.637 1.281-1.194 1.837-0.563 0.563-1.094 0.906-1.837 1.2-0.563 0.219-1.413 0.481-2.969 0.55-1.688 0.075-2.194 0.094-6.463 0.094s-4.781-0.019-6.463-0.094c-1.563-0.069-2.406-0.331-2.969-0.55-0.744-0.288-1.281-0.637-1.838-1.194-0.563-0.563-0.906-1.094-1.2-1.837-0.219-0.563-0.481-1.413-0.55-2.969-0.075-1.688-0.094-2.194-0.094-6.463s0.019-4.781 0.094-6.463c0.069-1.563 0.331-2.406 0.55-2.969 0.288-0.744 0.638-1.281 1.194-1.838 0.563-0.563 1.094-0.906 1.838-1.2 0.563-0.219 1.412-0.481 2.969-0.55 1.681-0.075 2.188-0.094 6.463-0.094zM16 0c-4.344 0-4.887 0.019-6.594 0.094-1.7 0.075-2.869 0.35-3.881 0.744-1.056 0.412-1.95 0.956-2.837 1.85-0.894 0.888-1.438 1.781-1.85 2.831-0.394 1.019-0.669 2.181-0.744 3.881-0.075 1.713-0.094 2.256-0.094 6.6s0.019 4.887 0.094 6.594c0.075 1.7 0.35 2.869 0.744 3.881 0.413 1.056 0.956 1.95 1.85 2.837 0.887 0.887 1.781 1.438 2.831 1.844 1.019 0.394 2.181 0.669 3.881 0.744 1.706 0.075 2.25 0.094 6.594 0.094s4.888-0.019 6.594-0.094c1.7-0.075 2.869-0.35 3.881-0.744 1.050-0.406 1.944-0.956 2.831-1.844s1.438-1.781 1.844-2.831c0.394-1.019 0.669-2.181 0.744-3.881 0.075-1.706 0.094-2.25 0.094-6.594s-0.019-4.887-0.094-6.594c-0.075-1.7-0.35-2.869-0.744-3.881-0.394-1.063-0.938-1.956-1.831-2.844-0.887-0.887-1.781-1.438-2.831-1.844-1.019-0.394-2.181-0.669-3.881-0.744-1.712-0.081-2.256-0.1-6.6-0.1v0z"></path>
		         <path d="M16 7.781c-4.537 0-8.219 3.681-8.219 8.219s3.681 8.219 8.219 8.219 8.219-3.681 8.219-8.219c0-4.537-3.681-8.219-8.219-8.219zM16 21.331c-2.944 0-5.331-2.387-5.331-5.331s2.387-5.331 5.331-5.331c2.944 0 5.331 2.387 5.331 5.331s-2.387 5.331-5.331 5.331z"></path>
		         <path d="M26.462 7.456c0 1.060-0.859 1.919-1.919 1.919s-1.919-0.859-1.919-1.919c0-1.060 0.859-1.919 1.919-1.919s1.919 0.859 1.919 1.919z"></path>
		      </symbol>
		      <symbol id="oxy-social-icons-icon-facebook-blank" viewBox="0 0 32 32">
		         <title>facebook-blank</title>
		         <path d="M29 0h-26c-1.65 0-3 1.35-3 3v26c0 1.65 1.35 3 3 3h13v-14h-4v-4h4v-2c0-3.306 2.694-6 6-6h4v4h-4c-1.1 0-2 0.9-2 2v2h6l-1 4h-5v14h9c1.65 0 3-1.35 3-3v-26c0-1.65-1.35-3-3-3z"></path>
		      </symbol>
		      <symbol id="oxy-social-icons-icon-rss-blank" viewBox="0 0 32 32">
		         <title>rss-blank</title>
		         <path d="M29 0h-26c-1.65 0-3 1.35-3 3v26c0 1.65 1.35 3 3 3h26c1.65 0 3-1.35 3-3v-26c0-1.65-1.35-3-3-3zM8.719 25.975c-1.5 0-2.719-1.206-2.719-2.706 0-1.488 1.219-2.712 2.719-2.712 1.506 0 2.719 1.225 2.719 2.712 0 1.5-1.219 2.706-2.719 2.706zM15.544 26c0-2.556-0.994-4.962-2.794-6.762-1.806-1.806-4.2-2.8-6.75-2.8v-3.912c7.425 0 13.475 6.044 13.475 13.475h-3.931zM22.488 26c0-9.094-7.394-16.5-16.481-16.5v-3.912c11.25 0 20.406 9.162 20.406 20.413h-3.925z"></path>
		      </symbol>
		      <symbol id="oxy-social-icons-icon-linkedin-blank" viewBox="0 0 32 32">
		         <title>linkedin-blank</title>
		         <path d="M29 0h-26c-1.65 0-3 1.35-3 3v26c0 1.65 1.35 3 3 3h26c1.65 0 3-1.35 3-3v-26c0-1.65-1.35-3-3-3zM12 26h-4v-14h4v14zM10 10c-1.106 0-2-0.894-2-2s0.894-2 2-2c1.106 0 2 0.894 2 2s-0.894 2-2 2zM26 26h-4v-8c0-1.106-0.894-2-2-2s-2 0.894-2 2v8h-4v-14h4v2.481c0.825-1.131 2.087-2.481 3.5-2.481 2.488 0 4.5 2.238 4.5 5v9z"></path>
		      </symbol>
		      <symbol id="oxy-social-icons-icon-pinterest-blank" viewBox="0 0 32 32">
		         <title>pinterest</title>
		         <path d="M16 2.138c-7.656 0-13.863 6.206-13.863 13.863 0 5.875 3.656 10.887 8.813 12.906-0.119-1.094-0.231-2.781 0.050-3.975 0.25-1.081 1.625-6.887 1.625-6.887s-0.412-0.831-0.412-2.056c0-1.925 1.119-3.369 2.506-3.369 1.181 0 1.756 0.887 1.756 1.95 0 1.188-0.756 2.969-1.15 4.613-0.331 1.381 0.688 2.506 2.050 2.506 2.462 0 4.356-2.6 4.356-6.35 0-3.319-2.387-5.638-5.787-5.638-3.944 0-6.256 2.956-6.256 6.019 0 1.194 0.456 2.469 1.031 3.163 0.113 0.137 0.131 0.256 0.094 0.4-0.106 0.438-0.338 1.381-0.387 1.575-0.063 0.256-0.2 0.306-0.463 0.188-1.731-0.806-2.813-3.337-2.813-5.369 0-4.375 3.175-8.387 9.156-8.387 4.806 0 8.544 3.425 8.544 8.006 0 4.775-3.012 8.625-7.194 8.625-1.406 0-2.725-0.731-3.175-1.594 0 0-0.694 2.644-0.863 3.294-0.313 1.206-1.156 2.712-1.725 3.631 1.3 0.4 2.675 0.619 4.106 0.619 7.656 0 13.863-6.206 13.863-13.863 0-7.662-6.206-13.869-13.863-13.869z"></path>
		      </symbol>
		      <symbol id="oxy-social-icons-icon-youtube-blank" viewBox="0 0 32 32">
		         <title>youtube</title>
		         <path d="M31.681 9.6c0 0-0.313-2.206-1.275-3.175-1.219-1.275-2.581-1.281-3.206-1.356-4.475-0.325-11.194-0.325-11.194-0.325h-0.012c0 0-6.719 0-11.194 0.325-0.625 0.075-1.987 0.081-3.206 1.356-0.963 0.969-1.269 3.175-1.269 3.175s-0.319 2.588-0.319 5.181v2.425c0 2.587 0.319 5.181 0.319 5.181s0.313 2.206 1.269 3.175c1.219 1.275 2.819 1.231 3.531 1.369 2.563 0.244 10.881 0.319 10.881 0.319s6.725-0.012 11.2-0.331c0.625-0.075 1.988-0.081 3.206-1.356 0.962-0.969 1.275-3.175 1.275-3.175s0.319-2.587 0.319-5.181v-2.425c-0.006-2.588-0.325-5.181-0.325-5.181zM12.694 20.15v-8.994l8.644 4.513-8.644 4.481z"></path>
		      </symbol>
		      <symbol id="oxy-social-icons-icon-twitter-blank" viewBox="0 0 32 32">
		         <title>twitter</title>
		         <path d="M32 7.075c-1.175 0.525-2.444 0.875-3.769 1.031 1.356-0.813 2.394-2.1 2.887-3.631-1.269 0.75-2.675 1.3-4.169 1.594-1.2-1.275-2.906-2.069-4.794-2.069-3.625 0-6.563 2.938-6.563 6.563 0 0.512 0.056 1.012 0.169 1.494-5.456-0.275-10.294-2.888-13.531-6.862-0.563 0.969-0.887 2.1-0.887 3.3 0 2.275 1.156 4.287 2.919 5.463-1.075-0.031-2.087-0.331-2.975-0.819 0 0.025 0 0.056 0 0.081 0 3.181 2.263 5.838 5.269 6.437-0.55 0.15-1.131 0.231-1.731 0.231-0.425 0-0.831-0.044-1.237-0.119 0.838 2.606 3.263 4.506 6.131 4.563-2.25 1.762-5.075 2.813-8.156 2.813-0.531 0-1.050-0.031-1.569-0.094 2.913 1.869 6.362 2.95 10.069 2.95 12.075 0 18.681-10.006 18.681-18.681 0-0.287-0.006-0.569-0.019-0.85 1.281-0.919 2.394-2.075 3.275-3.394z"></path>
		      </symbol>
		      <symbol id="oxy-social-icons-icon-instagram-blank" viewBox="0 0 32 32">
		         <title>instagram</title>
		         <path d="M16 2.881c4.275 0 4.781 0.019 6.462 0.094 1.563 0.069 2.406 0.331 2.969 0.55 0.744 0.288 1.281 0.638 1.837 1.194 0.563 0.563 0.906 1.094 1.2 1.838 0.219 0.563 0.481 1.412 0.55 2.969 0.075 1.688 0.094 2.194 0.094 6.463s-0.019 4.781-0.094 6.463c-0.069 1.563-0.331 2.406-0.55 2.969-0.288 0.744-0.637 1.281-1.194 1.837-0.563 0.563-1.094 0.906-1.837 1.2-0.563 0.219-1.413 0.481-2.969 0.55-1.688 0.075-2.194 0.094-6.463 0.094s-4.781-0.019-6.463-0.094c-1.563-0.069-2.406-0.331-2.969-0.55-0.744-0.288-1.281-0.637-1.838-1.194-0.563-0.563-0.906-1.094-1.2-1.837-0.219-0.563-0.481-1.413-0.55-2.969-0.075-1.688-0.094-2.194-0.094-6.463s0.019-4.781 0.094-6.463c0.069-1.563 0.331-2.406 0.55-2.969 0.288-0.744 0.638-1.281 1.194-1.838 0.563-0.563 1.094-0.906 1.838-1.2 0.563-0.219 1.412-0.481 2.969-0.55 1.681-0.075 2.188-0.094 6.463-0.094zM16 0c-4.344 0-4.887 0.019-6.594 0.094-1.7 0.075-2.869 0.35-3.881 0.744-1.056 0.412-1.95 0.956-2.837 1.85-0.894 0.888-1.438 1.781-1.85 2.831-0.394 1.019-0.669 2.181-0.744 3.881-0.075 1.713-0.094 2.256-0.094 6.6s0.019 4.887 0.094 6.594c0.075 1.7 0.35 2.869 0.744 3.881 0.413 1.056 0.956 1.95 1.85 2.837 0.887 0.887 1.781 1.438 2.831 1.844 1.019 0.394 2.181 0.669 3.881 0.744 1.706 0.075 2.25 0.094 6.594 0.094s4.888-0.019 6.594-0.094c1.7-0.075 2.869-0.35 3.881-0.744 1.050-0.406 1.944-0.956 2.831-1.844s1.438-1.781 1.844-2.831c0.394-1.019 0.669-2.181 0.744-3.881 0.075-1.706 0.094-2.25 0.094-6.594s-0.019-4.887-0.094-6.594c-0.075-1.7-0.35-2.869-0.744-3.881-0.394-1.063-0.938-1.956-1.831-2.844-0.887-0.887-1.781-1.438-2.831-1.844-1.019-0.394-2.181-0.669-3.881-0.744-1.712-0.081-2.256-0.1-6.6-0.1v0z"></path>
		         <path d="M16 7.781c-4.537 0-8.219 3.681-8.219 8.219s3.681 8.219 8.219 8.219 8.219-3.681 8.219-8.219c0-4.537-3.681-8.219-8.219-8.219zM16 21.331c-2.944 0-5.331-2.387-5.331-5.331s2.387-5.331 5.331-5.331c2.944 0 5.331 2.387 5.331 5.331s-2.387 5.331-5.331 5.331z"></path>
		         <path d="M26.462 7.456c0 1.060-0.859 1.919-1.919 1.919s-1.919-0.859-1.919-1.919c0-1.060 0.859-1.919 1.919-1.919s1.919 0.859 1.919 1.919z"></path>
		      </symbol>
		   </defs>
		</svg>
	
	<?php }


	/**
	 * Output specific CSS styles
	 *
	 * @since 2.0
	 */

	function css_styles($styles, $atts, $selector, $class_obj) { 

		// Make sure we only fire this once for this exact Class
		if ( get_class($class_obj) !== get_class($this) ) {
			return $styles;
		}

		// Repeater ID fix
		if ($this->in_repeater_cycle()) return;
        $selector = $this->get_corrected_element_selector($selector);

		$options = $atts['original'];

		if ( !isset( $options["icon-style"] ) ) {
			$options["icon-style"] = "";
		}

		ob_start(); ?>

		<?php echo $selector; ?>.oxy-social-icons {
			<?php if ( isset( $options["icon-layout"] ) ) : ?>
			flex-direction: <?php echo $options["icon-layout"]; ?>;
			<?php endif; ?>
			<?php if ( isset( $options["icon-space-between-icons"] ) ) : ?>
			margin-right: -<?php echo $options["icon-space-between-icons"]; ?>px;
			margin-bottom: -<?php echo $options["icon-space-between-icons"]; ?>px;
			<?php endif; ?>
		}

		<?php echo $selector; ?>.oxy-social-icons a {
			font-size: <?php echo $options["icon-size"]; ?>px;
			<?php if ( isset( $options["icon-space-between-icons"] ) ) : ?>
			margin-right: <?php echo $options["icon-space-between-icons"]; ?>px;
			margin-bottom: <?php echo $options["icon-space-between-icons"]; ?>px;
			<?php endif; ?>
			<?php if ($options["icon-style"] == 'circle') {
				echo "border-radius: 50%;";
			} else if ($options["icon-style"] == 'square') {
				echo "border-radius: 0;";
			} else {
				echo  $options["icon-style"];
			} ?>
			<?php if ($options["icon-style"] != 'blank' && isset($options["icon-background-color"])) { ?>
				background-color: <?php echo oxygen_vsb_get_global_color_value($options["icon-background-color"]); ?>;
			<?php } ?>
		}
		
		<?php if ($options["icon-style"] != 'blank' && isset($options["icon-background-hover-color"])) { ?>
		<?php echo $selector; ?>.oxy-social-icons a:hover {		
			background-color: <?php echo oxygen_vsb_get_global_color_value($options["icon-background-hover-color"]); ?>;
		}
		<?php } ?>

		<?php

		if ( isset( $options["icon-use-brand-colors"] ) && $options["icon-use-brand-colors"]=="yes") {
			if ($options["icon-style"] != "blank") {
				foreach ($this->network_colors as $network => $color) { ?>
					<?php echo $selector; ?>.oxy-social-icons a.oxy-social-icons-<?php echo $network; ?> {
						background-color: <?php echo $color; ?>;
					}
					<?php echo $selector; ?>.oxy-social-icons a.oxy-social-icons-<?php echo $network; ?>:hover {
						background-color: <?php echo $this->network_hover_colors[$network]; ?>;
					}
					<?php 
					$options['icon-color'] = '#fff';
            		$options['icon-hover-color'] = '#fff';
				}
			} else {
				foreach ($this->network_colors as $network => $color) { ?>
					<?php echo $selector; ?>.oxy-social-icons a.oxy-social-icons-<?php echo $network; ?> svg {
						color: <?php echo oxygen_vsb_get_global_color_value($color); ?>;
					}
					<?php echo $selector; ?>.oxy-social-icons a.oxy-social-icons-<?php echo $network; ?>:hover  svg {
						color: <?php echo oxygen_vsb_get_global_color_value($this->network_hover_colors[$network]); ?>;
					}
				<?php }
			}

		} ?>

		<?php echo $selector; ?>.oxy-social-icons a svg {
			<?php if ($options["icon-style"] != 'blank') { ?>
				width: 0.5em;
				height: 0.5em;
			<?php } else { ?>
				width: 1em;
				height: 1em;
			<?php } ?>
			<?php if (isset($options["icon-color"])) : ?>
			color: <?php echo oxygen_vsb_get_global_color_value($options["icon-color"]); ?>;
			<?php endif; ?>
		}

		<?php if (isset($options["icon-hover-color"])) : ?>
		<?php echo $selector; ?>.oxy-social-icons a:hover svg {
			color: <?php echo oxygen_vsb_get_global_color_value($options["icon-hover-color"]); ?>;
		}
		<?php endif; ?>
		
		<?php return $styles . ob_get_clean(); 
	}
}


// Create instance
global $oxygen_vsb_components;
$oxygen_vsb_components['social_icons'] = new Oxy_Social_Icons( array( 
			'name' 		=> __('Social Icons','oxygen'),
			'tag' 		=> 'oxy_social_icons',
			'params' 	=> array(
				array(
					"type" 			=> "textfield",
					"heading" 		=> __("Facebook","oxygen"),
					"param_name" 	=> "icon-facebook",
					"value" 		=> "",
					"css" 			=> false,
				),
				array(
					"type" 			=> "textfield",
					"heading" 		=> __("Facebook Link Title","oxygen"),
					"param_name" 	=> "title-facebook",
					"value" 		=> "Visit our Facebook",
					"css" 			=> false,
				),
				array(
					"type" 			=> "textfield",
					"heading" 		=> __("Instagram","oxygen"),
					"param_name" 	=> "icon-instagram",
					"value" 		=> "",
					"css" 			=> false,
				),
				array(
					"type" 			=> "textfield",
					"heading" 		=> __("Instagram Link Title","oxygen"),
					"param_name" 	=> "title-instagram",
					"value" 		=> "Visit our Instagram",
					"css" 			=> false,
				),
				array(
					"type" 			=> "textfield",
					"heading" 		=> __("Twitter","oxygen"),
					"param_name" 	=> "icon-twitter",
					"value" 		=> "",
					"css" 			=> false,
				),
				array(
					"type" 			=> "textfield",
					"heading" 		=> __("Twitter Link Title","oxygen"),
					"param_name" 	=> "title-twitter",
					"value" 		=> "Visit our Twitter",
					"css" 			=> false,
				),
				array(
					"type" 			=> "textfield",
					"heading" 		=> __("LinkedIn","oxygen"),
					"param_name" 	=> "icon-linkedin",
					"value" 		=> "",
					"css" 			=> false,
				),
				array(
					"type" 			=> "textfield",
					"heading" 		=> __("LinkedIn Link Title","oxygen"),
					"param_name" 	=> "title-linkedin",
					"value" 		=> "Visit our LinkedIn",
					"css" 			=> false,
				),
				array(
					"type" 			=> "textfield",
					"heading" 		=> __("RSS","oxygen"),
					"param_name" 	=> "icon-rss",
					"value" 		=> "",
					"css" 			=> false,
				),
				array(
					"type" 			=> "textfield",
					"heading" 		=> __("RSS Link Title","oxygen"),
					"param_name" 	=> "title-rss",
					"value" 		=> "Visit our RSS feed",
					"css" 			=> false,
				),
				array(
					"type" 			=> "textfield",
					"heading" 		=> __("YouTube","oxygen"),
					"param_name" 	=> "icon-youtube",
					"value" 		=> "",
					"css" 			=> false,
				),
				array(
					"type" 			=> "textfield",
					"heading" 		=> __("YouTube Link Title","oxygen"),
					"param_name" 	=> "title-youtube",
					"value" 		=> "Visit our YouTube channel",
					"css" 			=> false,
				),
				array(
					"type" 			=> "radio",
					"heading" 		=> __("Link Target", "oxygen"),
					"param_name" 	=> "icon-link-target",
					"value" 		=> array(
										"_blank" 	=> __("New Tab", "oxygen"),
										"_self"		=> __("Current Tab", "oxygen"),
									),
					"css"			=> false,
				),
				array(
					"type" 			=> "radio",
					"heading" 		=> __("Layout", "oxygen"),
					"param_name" 	=> "icon-layout",
					"value" 		=> array(
										"row" 		=> __("Row", "oxygen"),
										"column"	=> __("Column", "oxygen"),
									),
					"css"			=> false,
				),
				array(
					"type" 			=> "slider-measurebox",
					"heading" 		=> __("Icon Size","oxygen"),
					"param_name" 	=> "icon-size",
					"value" 		=> "50",
					"min"			=> "10",
					"max"			=> "100",
					"param_units" 	=> 'px',
					"css" 			=> false,
				),
				array(
					"type" 			=> "slider-measurebox",
					"heading" 		=> __("Space Between Icons","oxygen"),
					"param_name" 	=> "icon-space-between-icons",
					"value" 		=> "10",
					"min"			=> "0",
					"max"			=> "60",
					"param_units" 	=> 'px',
					"css" 			=> false,
				),
				array(
					"type" 			=> "colorpicker",
					"heading" 		=> __("Background Color", "oxygen"),
					"param_name" 	=> "icon-background-color",
					"value" 		=> "",
					"css" 			=> false,
					"condition" 	=> "icon-use-brand-colors=no"
				),
				array(
					"type" 			=> "colorpicker",
					"heading" 		=> __("Icon Color", "oxygen"),
					"param_name" 	=> "icon-color",
					"value" 		=> "",
					"css" 			=> false,
					"condition" 	=> "icon-use-brand-colors=no"
				),
				array(
					"type" 			=> "colorpicker",
					"heading" 		=> __("Background Hover Color", "oxygen"),
					"param_name" 	=> "icon-background-hover-color",
					"value" 		=> "",
					"css" 			=> false,
					"condition" 	=> "icon-use-brand-colors=no"
				),
				array(
					"type" 			=> "colorpicker",
					"heading" 		=> __("Icon Hover Color", "oxygen"),
					"param_name" 	=> "icon-hover-color",
					"value" 		=> "",
					"css" 			=> false,
					"condition" 	=> "icon-use-brand-colors=no"
				),
				array(
					"type" 			=> "checkbox",
					"heading" 		=> __("Use brand colors","oxygen"),
					"param_name" 	=> "icon-use-brand-colors",
					"value" 		=> "no",
					"true_value" 	=> "yes",
					"false_value" 	=> "no",
					"css" 			=> false,
				),
				array(
					"type" 			=> "radio",
					"heading" 		=> __("Icon Style", "oxygen"),
					"param_name" 	=> "icon-style",
					"value" 		=> array(
										"blank"		=> __("Blank", "oxygen"),
										"circle" 	=> __("Circle", "oxygen"),
										"square"	=> __("Square", "oxygen"),
									),
					"css"			=> false,
				),
			),
			'advanced' 	=> array(
					"other" => array(
						"values" => array(
							)
					)
			),
			'not_css_params' => array(
				'title_facebook',
				'title_instagram',
				'title_twitter',
				'title_linkedin',
				'title_rss',
				'title_youtube'
			)

		)
);
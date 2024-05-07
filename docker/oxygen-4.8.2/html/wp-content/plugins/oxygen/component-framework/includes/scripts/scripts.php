<?php

/**
 * Various Scripts available in Oxygen
 *
 * @since 2.2
 * @author Ilya K.
 */

Class Oxygen_Scripts {

	private $script_loaded = false;

	/**
	 * Consrtuctor
	 * 
	 * @since 2.2
	 * @author Ilya K.
	 */

	function __construct() {

		add_action("oxygen_vsb_page_settings_tabs", 	array($this, "page_settings_tab"));
		add_action("oxygen_vsb_page_settings_content", 	array($this, "page_settings"));
		add_action("oxygen_vsb_global_styles_tabs", 	array($this, "global_settings_tab"));
		add_action("oxygen_vsb_settings_content", 		array($this, "global_settings"));

		add_action("wp_footer", array($this, "frontend_scripts") );
		add_action("wp_footer", array($this, "builder_scripts") );
	}


	/**
	 * Add a Tab to Manage > Settings > Page Settings
	 * 
	 * @since 2.2
	 * @author Ilya K.
	 */
	    
	function page_settings_tab() {
		
		global $oxygen_toolbar;

		$oxygen_toolbar->settings_child_tab(__("Scripts", "oxygen"), "page", "scripts", "advanced/js.svg");
	}


	/**
	 * Output Scripts Page Settings
	 * 
	 * @since 2.2
	 * @author Ilya K.
	 */
	    
	function page_settings() { ?>
		
		<div ng-show="isShowChildTab('settings','page','scripts')">
			<?php require_once "page-scripts.view.php"; ?>
		</div>
	
	<?php }


	/**
	 * Output Scripts Global Settings Tab
	 * 
	 * @since 2.2
	 * @author Ilya K.
	 */
	    
	function global_settings_tab() {
		
		global $oxygen_toolbar;

		$oxygen_toolbar->settings_child_tab(__("Scripts", "oxygen"), "default-styles", "scripts", "advanced/js.svg");
	
	}


	/**
	 * Output Scripts Global Settings
	 * 
	 * @since 2.2
	 * @author Ilya K.
	 */
	    
	function global_settings() { ?>
		
		<div ng-if="isShowChildTab('settings','default-styles','scripts')">
			<?php require_once "global-scripts.view.php"; ?>
		</div>
	
	<?php }


	/**
	 * Output Scripts to wp_footer on frontend
	 * 
	 * @since 2.2
	 * @author Ilya K.
	 */
	
	function frontend_scripts() {

		// don't load in Builder UI
		if (defined('SHOW_CT_BUILDER') || defined('OXYGEN_IFRAME')) {
			return;
		}

		$page_settings 		= ct_get_page_settings();
		$global_settings 	= ct_get_global_settings();
		
		/**
		 * Smooth Scroll to Hash Links
		 */

		if ( isset($page_settings['scripts']) && is_array($page_settings['scripts'])) {
			$show_script = $page_settings['scripts']['scroll_to_hash'];
			$time = $page_settings['scripts']['scroll_to_hash_time'];
			$offset = $page_settings['scripts']['scroll_to_hash_offset'];
		}
		
		if ($show_script!=='true') {
			$show_script = $global_settings['scripts']['scroll_to_hash'];
			$time = $global_settings['scripts']['scroll_to_hash_time'];
		}

		if (!isset($offset)||trim($offset)=="") {
			$offset = isset($global_settings['scripts']['scroll_to_hash_offset']) ? $global_settings['scripts']['scroll_to_hash_offset'] : "0";
		}

		if (!$time) {
			$time = "1000";
		}

		if (!$offset) {
			$offset = "0";
		}

		$time = esc_attr($time);
		$time = intval($time);

		if ($show_script==="true") : ?><script>jQuery(document).on('click','a[href*="#"]',function(t){if(jQuery(t.target).closest('.wc-tabs').length>0){return}if(jQuery(this).is('[href="#"]')||jQuery(this).is('[href="#0"]')||jQuery(this).is('[href*="replytocom"]')){return};if(location.pathname.replace(/^\//,"")==this.pathname.replace(/^\//,"")&&location.hostname==this.hostname){var e=jQuery(this.hash);(e=e.length?e:jQuery("[name="+this.hash.slice(1)+"]")).length&&(t.preventDefault(),jQuery("html, body").animate({scrollTop:e.offset().top-<?php echo $offset; ?>},<?php echo $time; ?>))}});</script><?php endif;

	}


	/**
	 * Output Scripts to wp_footer in builder
	 * 
	 * @since 2.2
	 * @author Ilya K.
	 */
	
	function builder_scripts() {

		// don't load in Builder UI
		if (!defined('SHOW_CT_BUILDER') || !defined('OXYGEN_IFRAME')) {
			return;
		} 

		/**
		 * Smooth Scroll to Hash Links
		 */
		
		?><script>
			let clickcount = 0, dbcltimeout = null;
			jQuery('html').on('click', '.oxygen-scroll-to-hash-links a[href*="#"]:not([href="#"]):not([href="#0"])', 
				function(t) {
					// logic to ignore double clicks
					clickcount++;
					if(clickcount > 1 && dbcltimeout) {
						clearTimeout(dbcltimeout);
						dbcltimeout = null
						return;
					}
					dbcltimeout = setTimeout(function() {
						var time=jQuery('body').attr('data-oxygen-scroll-to-hash-links'),offset=jQuery('body').attr('data-oxygen-scroll-to-hash-links-offset');if(!time)time=1000;if(!offset)offset=0;if(location.pathname.replace(/^\//,"")==this.pathname.replace(/^\//,"")&&location.hostname==this.hostname){var e=jQuery(this.hash);(e=e.length?e:jQuery("[name="+this.hash.slice(1)+"]")).length&&(t.preventDefault(),jQuery("html, body").animate({scrollTop:e.offset().top-offset},parseInt(time)))}
						clearTimeout(dbcltimeout);
						dbcltimeout = null
					}.bind(this), 300);
				}
			);
			</script><?php

	}

}

$oxygen_vsb_scripts = new Oxygen_Scripts();
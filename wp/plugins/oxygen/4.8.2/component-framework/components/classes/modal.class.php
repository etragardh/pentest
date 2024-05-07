<?php

Class CT_Modal extends CT_Component {

	var $js_added = false;

	function __construct( $options ) {

		// run initialization
		$this->init( $options );

		// Add shortcodes
		add_shortcode( $this->options['tag'], array( $this, 'add_shortcode' ) );
		add_oxygen_element( $this->options['tag'], array( $this, 'add_shortcode' ) );

        for ( $i = 2; $i <= 16; $i++ ) {
            add_shortcode( $this->options['tag'] . "_" . $i, array( $this, 'add_shortcode' ) );
        }

		// change component button place
		remove_action("ct_toolbar_fundamentals_list", array( $this, "component_button" ) );
		add_action("oxygen_helpers_components_interactive", array( $this, "component_button" ) );

		// add specific options
		add_action("ct_subtab_level_1_component_settings", array( $this, "modal_settings"), 10 );
	}


	/**
	 * Add a [ct_modal] shortcode to WordPress
	 *
	 */

	function add_shortcode( $atts, $content, $name ) {

		if ( ! $this->validate_shortcode( $atts, $content, $name ) ) {
			return '';
		}

		// add JavaScript code only once and only if shortcode is present
		if ($this->js_added === false) {
			add_action( 'wp_footer', array( $this, 'output_js' ) );
			$this->js_added = true;
		}

		$options = $this->set_options( $atts );

		ob_start();

		?>
            <div tabindex="-1" class="oxy-modal-backdrop <?php if(isset($options['modal_position'])) echo esc_attr( $options['modal_position'] ); ?> <?php if(isset($options['close_modal_on_backdrop_click']) && $options['close_modal_on_backdrop_click'] == 'no' ) echo 'oxy-not-closable'; ?>"
                <?php if(isset($options['backdrop_color'])) echo 'style="background-color: ' . oxygen_vsb_get_global_color_value( esc_attr( $options['backdrop_color'] ) ) . ';"'; ?>

                <?php if(isset($options['trigger'])) echo 'data-trigger="' . esc_attr( $options['trigger'] ) . '"'; ?>
                <?php if(isset($options['trigger_selector'])) echo 'data-trigger-selector="' . esc_attr( $options['trigger_selector'] ) . '"'; ?>
                <?php if(isset($options['trigger_time'])) echo 'data-trigger-time="' . esc_attr( $options['trigger_time'] ) . '"'; ?>
                <?php if(isset($options['trigger_time_unit'])) echo 'data-trigger-time-unit="' . esc_attr( $options['trigger_time_unit'] ) . '"'; ?>
                <?php if(isset($options['close_automatically'])) echo 'data-close-automatically="' . esc_attr( $options['close_automatically'] ) . '"'; ?>
                <?php if(isset($options['close_after_time'])) echo 'data-close-after-time="' . esc_attr( $options['close_after_time'] ) . '"'; ?>
                <?php if(isset($options['close_after_time_unit'])) echo 'data-close-after-time-unit="' . esc_attr( $options['close_after_time_unit'] ) . '"'; ?>
                <?php if(isset($options['trigger_scroll_amount'])) echo 'data-trigger_scroll_amount="' . esc_attr( $options['trigger_scroll_amount'] ) . '"'; ?>
                <?php if(isset($options['trigger_scroll_direction'])) echo 'data-trigger_scroll_direction="' . esc_attr( $options['trigger_scroll_direction'] ) . '"'; ?>
	            <?php if(isset($options['scroll_to_selector'])) echo 'data-scroll_to_selector="' . esc_attr( $options['scroll_to_selector'] ) . '"'; ?>
	            <?php if(isset($options['time_inactive'])) echo 'data-time_inactive="' . esc_attr( $options['time_inactive'] ) . '"'; ?>
	            <?php if(isset($options['time_inactive_unit'])) echo 'data-time-inactive-unit="' . esc_attr( $options['time_inactive_unit'] ) . '"'; ?>
	            <?php if(isset($options['number_of_clicks'])) echo 'data-number_of_clicks="' . esc_attr( $options['number_of_clicks'] ) . '"'; ?>
	            <?php if(isset($options['close_on_esc'])) echo 'data-close_on_esc="' . esc_attr( $options['close_on_esc'] ) . '"'; ?>
	            <?php if(isset($options['number_of_page_views'])) echo 'data-number_of_page_views="' . esc_attr( $options['number_of_page_views'] ) . '"'; ?>
                <?php if(isset($options['close_after_form_submit'])) echo 'data-close-after-form-submit="' . esc_attr( $options['close_after_form_submit'] ) . '"'; ?>
                <?php if(isset($options['open_again'])) echo 'data-open-again="' . esc_attr( $options['open_again'] ) . '"'; ?>
                <?php if(isset($options['open_again_after_days'])) echo 'data-open-again-after-days="' . esc_attr( $options['open_again_after_days'] ) . '"'; ?>
            >

                <<?php echo esc_attr($options['tag'])?> id="<?php echo esc_attr($options['selector']) ?>" class="<?php if(isset($options['classes'])) echo esc_attr($options['classes']); ?>" <?php do_action("oxygen_vsb_component_attr", $options, $this->options['tag']); ?>><?php echo (is_array($content)) ? do_oxygen_elements( $content ) : do_shortcode( $content ); ?></<?php echo esc_attr($options['tag'])?>>
            </div>
        <?php

		return ob_get_clean();
	}


    function modal_settings() {
        global $oxygen_toolbar;
        ?>

        <div class='oxygen-control-row' ng-hide="!isShowTab('ct_modal','closing')">
            <div class='oxygen-control-wrapper'>
                <div class='oxygen-control'>
                    <a href="#" class="oxygen-ghost-button" ng-click="iframeScope.insertModalCloseButton()"><?php echo __("Insert Close Button", "oxygen"); ?></a>
                </div>
            </div>
            <ul class="oxygen-control-label">
                <li><?php echo __( "Elements with 'oxy-close-modal' class will act as a close button." ) ?></li>
                <li><?php echo __( 'Close the popup with JS - use the <span class="code">oxyCloseModal()</span> function. <a id="oxyCloseModalDetailsButton" href="javascript:jQuery(\'#oxyCloseModalDetails\').css(\'opacity\', 1); jQuery(\'#oxyCloseModalDetailsButton\').hide();">Details</a>' ) ?></li>
            </ul>
            <div id="oxyCloseModalDetails" class="oxygen-sidebar-code-editor-wrap">
                <?php 
                    global $oxygen_toolbar;
                    $oxygen_toolbar->codemirror6_script("custom-js","oxy-custom-js-cm6","javascript");
                ?>
                <div id="oxy-custom-js-cm6" class="oxy-code-cm6"></div>
            </div>
        </div>

        <div class='oxygen-control-row'  ng-hide="!isShowTab('ct_modal','modal_styles')">
            <div class='oxygen-control-wrapper oxygen-control-wrapper-center'>
                <label class='oxygen-control-label'><?php _e("Modal Position","oxygen"); ?></label>
                <div class='oxygen-control'>
                    <div class='oxygen-icon-button-list modal-position'>
                        <?php $oxygen_toolbar->icon_button_list_button(
                            'modal_position','upper_left','modal/top-left.svg','modal/top-left-hover.svg'); ?>
                        <?php $oxygen_toolbar->icon_button_list_button(
						    'modal_position','top','modal/top.svg','modal/top-hover.svg'); ?>
                        <?php $oxygen_toolbar->icon_button_list_button(
                            'modal_position','upper_right','modal/top-right.svg','modal/top-right-hover.svg'); ?>
                    </div>
                    <div class='oxygen-icon-button-list modal-position'>
                        <?php $oxygen_toolbar->icon_button_list_button(
                            'modal_position','left','modal/left.svg','modal/left-hover.svg'); ?>
                        <?php $oxygen_toolbar->icon_button_list_button(
                            'modal_position','center','modal/centered.svg','modal/centered-hover.svg'); ?>
                        <?php $oxygen_toolbar->icon_button_list_button(
                            'modal_position','right','modal/right.svg','modal/right-hover.svg'); ?>
                    </div>
                    <div class='oxygen-icon-button-list modal-position'>
                        <?php $oxygen_toolbar->icon_button_list_button(
                            'modal_position','bottom_left','modal/bottom-left.svg','modal/bottom-left-hover.svg'); ?>
                        <?php $oxygen_toolbar->icon_button_list_button(
                            'modal_position','bottom','modal/bottom.svg','modal/bottom-hover.svg'); ?>
                        <?php $oxygen_toolbar->icon_button_list_button(
                            'modal_position','bottom_right','modal/bottom-right.svg','modal/bottom-right-hover.svg'); ?>
                    </div>
                </div>
            </div>
        </div>

    <?php }

	/**
	 * Output JS for Modals
	 *
	 * @since 2.2
	 * @author Emmanuel
	 */

	function output_js() { ?>

		<script type="text/javascript">

            // Initialize Oxygen Modals
            jQuery(document).ready(function() {

                function showModal( modal ) {
                    var $modal = jQuery( modal );
                    $modal.addClass("live");
                    var modalId = $modal[0].querySelector('.ct-modal').id;
                    
                    var focusable = modal.querySelector('a[href]:not([disabled]), button:not([disabled]), textarea:not([disabled]), input[type="text"]:not([disabled]), input[type="radio"]:not([disabled]), input[type="checkbox"]:not([disabled]), select:not([disabled])');

                    if(focusable) {
                        setTimeout(() => {
                            focusable.focus();    
                        }, 500);
                    } else {
                        setTimeout(() => {
                        $modal.focus();
                        }, 500)
                    }

                    // Check if this modal can be shown according to settings and last shown time
                    // Current and last time in milliseconds
                    var currentTime = new Date().getTime();
                    var lastShownTime = localStorage && localStorage['oxy-' + modalId + '-last-shown-time'] ? JSON.parse( localStorage['oxy-' + modalId + '-last-shown-time'] ) : false;
                    // manual triggers aren't affected by last shown time
                    if( $modal.data( 'trigger' ) != 'user_clicks_element' ) {
                        switch( $modal.data( 'open-again' ) ) {
                            case 'never_show_again':
                                // if it was shown at least once, don't show it again
                                if( lastShownTime !== false ) return;
                                break;
                            case 'show_again_after':
                                var settingDays = parseInt( $modal.data( 'open-again-after-days' ) );
                                var actualDays = ( currentTime - lastShownTime ) / ( 60*60*24*1000 );
                                if( actualDays < settingDays ) return;
                                break;
                            default:
                                //always show
                                break;
                        }
                    }

                    // Body manipulation to prevent scrolling while modal is active, and maintain scroll position.
                    document.querySelector('body').style.top = `-${window.scrollY}px`;
                    document.querySelector('body').classList.add('oxy-modal-active');
                    
                    // save current time as last shown time
                    if( localStorage ) localStorage['oxy-' + modalId + '-last-shown-time'] = JSON.stringify( currentTime );

                    // trick to make jQuery fadeIn with flex
                    $modal.css("display", "flex");
                    $modal.hide();
                    // trick to force AOS trigger on elements inside the modal
                    $modal.find(".aos-animate").removeClass("aos-animate").addClass("aos-animate-disabled");

                    // show the modal
                    $modal.fadeIn(250, function(){
                        // trick to force AOS trigger on elements inside the modal
                        $modal.find(".aos-animate-disabled").removeClass("aos-animate-disabled").addClass("aos-animate");
                    });


                    if( $modal.data( 'close-automatically' ) == 'yes' ) {
                        var time = parseInt( $modal.data( 'close-after-time' ) );
                        if( $modal.data( 'close-after-time-unit' ) == 'seconds' ) {
                            time = parseInt( parseFloat( $modal.data( 'close-after-time' ) ) * 1000 );
                        }
                        setTimeout( function(){
                            hideModal(modal);
                        }, time );
                    }

                    // close modal automatically after form submit (Non-AJAX)
                    if( $modal.data( 'close-after-form-submit' ) == 'yes' && $modal.data("trigger") == "after_specified_time" ) {

                        // WPForms
                        // WPForms replaces the form with a confirmation message on page refresh
                        if( $modal.find(".wpforms-confirmation-container-full").length > 0 ) {
                            setTimeout(function () {
                                hideModal(modal);
                            }, 3000);
                        }

                        // Formidable Forms
                        // Formidable Forms replaces the form with a confirmation message on page refresh
                        if( $modal.find(".frm_message").length > 0 ) {
                            setTimeout(function () {
                                hideModal(modal);
                            }, 3000);
                        }

                        // Caldera Forms
                        // Caldera Forms replaces the form with a confirmation message on page refresh
                        if( $modal.find(".caldera-grid .alert-success").length > 0 ) {
                            setTimeout(function () {
                                hideModal(modal);
                            }, 3000);
                        }

                    }
                }

                window.oxyShowModal = showModal;

                var hideModal = function ( modal ) {

                    // Body manipulation for scroll prevention and maintaining scroll position
                    var scrollY = document.querySelector('body').style.top;
                    document.querySelector('body').classList.remove('oxy-modal-active');
                    document.querySelector('body').style.top = '';
                    window.scrollTo(0, parseInt(scrollY || '0') * -1);

                    // The function may be called by third party code, without argument, so we must close the first visible modal
                    if( typeof modal === 'undefined' ) {
                        var openModals = jQuery(".oxy-modal-backdrop.live");
                        if( openModals.length == 0 ) return;
                        modal = openModals[0];
                    }

                    var $modal = jQuery( modal );
                    // refresh any iframe so media embedded this way is stopped
                    $modal.find( 'iframe').each(function(index){
                        this.src = this.src;
                    });
                    // HTML5 videos can be stopped easily
                    $modal.find( 'video' ).each(function(index){
                        this.pause();
                    });
                    // If there are any forms in the modal, reset them
                    $modal.find("form").each(function(index){
                        this.reset();
                    });

                    $modal.find(".aos-animate").removeClass("aos-animate").addClass("aos-animate-disabled");

                    $modal.fadeOut(400, function(){
                        $modal.removeClass("live");
                        $modal.find(".aos-animate-disabled").removeClass("aos-animate-disabled").addClass("aos-animate");
                    });
                };

                window.oxyCloseModal = hideModal;

                jQuery( ".oxy-modal-backdrop" ).each(function( index ) {

                    var modal = this;

                    (function( modal ){
                        var $modal = jQuery( modal );
						
						var exitIntentFunction = function( e ){
                            if( e.target.tagName == 'SELECT' ) { return; }
							if( e.clientY <= 0 ) {
								showModal( modal );
								document.removeEventListener( "mouseleave", exitIntentFunction );
								document.removeEventListener( "mouseout", exitIntentFunction );
							}
						}

                        switch ( jQuery( modal ).data("trigger") ) {

                            case "on_exit_intent":
                                document.addEventListener( "mouseleave", exitIntentFunction, false);
								document.addEventListener( "mouseout", exitIntentFunction, false);
                                break;

                            case "user_clicks_element":
                                jQuery( jQuery( modal ).data( 'trigger-selector' ) ).click( function( event ) {
                                    showModal( modal );
                                    event.preventDefault();
                                } );
                                break;

                            case "after_specified_time":
                                var time = parseInt( jQuery( modal ).data( 'trigger-time' ) );
                                if( jQuery( modal ).data( 'trigger-time-unit' ) == 'seconds' ) {
                                    time = parseInt( parseFloat( jQuery( modal ).data( 'trigger-time' ) ) * 1000 );
                                }
                                setTimeout( function(){
                                    showModal( modal );
                                }, time );
                                break;

                            case "after_scrolled_amount":
                                window.addEventListener("scroll", function scrollDetection(){
                                    var winheight= window.innerHeight || (document.documentElement || document.body).clientHeight;
                                    var docheight = jQuery(document).height();
                                    var scrollTop = window.pageYOffset || (document.documentElement || document.body.parentNode || document.body).scrollTop;
                                    var isScrollUp = false;
                                    var oxyPreviousScrollTop = parseInt( jQuery( modal ).data( 'previous_scroll_top' ) );
                                    if( !isNaN( oxyPreviousScrollTop ) ) {
                                        if( oxyPreviousScrollTop > scrollTop) isScrollUp = true;
                                    }
                                    jQuery( modal ).data( 'previous_scroll_top', scrollTop );
                                    var trackLength = docheight - winheight;
                                    var pctScrolled = Math.floor(scrollTop/trackLength * 100);
                                    if( isNaN( pctScrolled ) ) pctScrolled = 0;

                                    if(
                                        ( isScrollUp && jQuery( modal ).data( 'trigger_scroll_direction' ) == 'up' ) ||
                                        ( !isScrollUp && jQuery( modal ).data( 'trigger_scroll_direction' ) == 'down' && pctScrolled >= parseInt( jQuery( modal ).data( 'trigger_scroll_amount' ) ) )
                                    ) {
                                        showModal( modal );
                                        window.removeEventListener( "scroll", scrollDetection );
                                    }
                                }, false);
                                break;
                            case "on_scroll_to_element":
                                window.addEventListener("scroll", function scrollDetection(){
                                    var $element = jQuery( jQuery( modal ).data( 'scroll_to_selector' ) );
                                    if( $element.length == 0 ) {
                                        window.removeEventListener( "scroll", scrollDetection );
                                        return;
                                    }

                                    var top_of_element = $element.offset().top;
                                    var bottom_of_element = $element.offset().top + $element.outerHeight();
                                    var bottom_of_screen = jQuery(window).scrollTop() + jQuery(window).innerHeight();
                                    var top_of_screen = jQuery(window).scrollTop();

                                    if ((bottom_of_screen > bottom_of_element - $element.outerHeight() /2 ) && (top_of_screen < top_of_element + $element.outerHeight() /2 )){
                                        showModal( modal );
                                        window.removeEventListener( "scroll", scrollDetection );
                                    }
                                }, false);
                                break;
                            case "after_number_of_clicks":
                                document.addEventListener("click", function clickDetection(){
                                    var number_of_clicks = parseInt( jQuery( modal ).data( 'number_of_clicks' ) );

                                    var clicks_performed = isNaN( parseInt( jQuery( modal ).data( 'clicks_performed' ) ) ) ? 1 :  parseInt( jQuery( modal ).data( 'clicks_performed' ) ) + 1;

                                    jQuery( modal ).data( 'clicks_performed', clicks_performed );

                                    if ( clicks_performed == number_of_clicks ){
                                        showModal( modal );
                                        document.removeEventListener( "click", clickDetection );
                                    }
                                }, false);
                                break;
                            case "after_time_inactive":
                                var time = parseInt( jQuery( modal ).data( 'time_inactive' ) );
                                if( jQuery( modal ).data( 'time-inactive-unit' ) == 'seconds' ) {
                                    time = parseInt( parseFloat( jQuery( modal ).data( 'time_inactive' ) ) * 1000 );
                                }
                                var activityDetected = function(){
                                    jQuery( modal ).data( 'millis_idle', 0 );
                                };
                                document.addEventListener( "click", activityDetected);
                                document.addEventListener( "mousemove", activityDetected);
                                document.addEventListener( "keypress", activityDetected);
                                document.addEventListener( "scroll", activityDetected);

                                var idleInterval = setInterval(function(){
                                    var millis_idle = isNaN( parseInt( jQuery( modal ).data( 'millis_idle' ) ) ) ? 100 :  parseInt( jQuery( modal ).data( 'millis_idle' ) ) + 100;
                                    jQuery( modal ).data( 'millis_idle', millis_idle );
                                    if( millis_idle > time ){
                                        clearInterval( idleInterval );
                                        document.removeEventListener( "click", activityDetected );
                                        document.removeEventListener( "mousemove", activityDetected );
                                        document.removeEventListener( "keypress", activityDetected );
                                        document.removeEventListener( "scroll", activityDetected );
                                        showModal( modal );
                                    }
                                }, 100);
                                break;

                            case "after_number_of_page_views":
                                var modalId = modal.querySelector('.ct-modal').id;
                                var pageViews = localStorage && localStorage['oxy-' + modalId + '-page-views'] ? parseInt( localStorage['oxy-' + modalId + '-page-views'] ) : 0;
                                pageViews++;
                                if( localStorage ) localStorage['oxy-' + modalId + '-page-views'] = pageViews;
                                if( parseInt( jQuery( modal ).data( 'number_of_page_views' ) ) == pageViews ) {
                                    if( localStorage ) localStorage['oxy-' + modalId + '-page-views'] = 0;
                                    showModal( modal );
                                }
                                break;

                        }

                        // add event handler to close modal automatically after AJAX form submit
                        if( $modal.data( 'close-after-form-submit' ) == 'yes' ) {

                            // Contact Form 7
                            if (typeof wpcf7 !== 'undefined') {
                                $modal.find('div.wpcf7').each(function () {
                                    var $form = jQuery(this).find('form');
                                    this.addEventListener('wpcf7submit', function (event) {
                                        if (event.detail.contactFormId == $form.attr("id")) {
                                            setTimeout(function () {
                                                hideModal(modal);
                                            }, 3000);
                                        }
                                    }, false);
                                });
                            }

                            // Caldera Forms
                            document.addEventListener( "cf.submission", function(event){
                                // Pending, Caldera AJAX form submissions aren't working since Oxygen 2.2, see: https://github.com/soflyy/oxygen/issues/1638
                            });

                            // Ninja Forms
                            jQuery(document).on("nfFormSubmitResponse", function(event, response){
                                // Only close the modal if the event was triggered from a Ninja Form inside the modal
                                if( $modal.find("#nf-form-" + response.id + "-cont").length > 0 ) {
                                    setTimeout(function () {
                                        hideModal(modal);
                                    }, 3000);
                                }
                            });

                        }

                    })( modal );

                });

                // handle clicks on modal backdrop and on .oxy-close-modal
                jQuery("body").on('click touchend', '.oxy-modal-backdrop, .oxy-close-modal', function( event ) {

                    var $this = jQuery( this );
                    var $target = jQuery( event.target );

                    // Click event in the modal div and it's children is propagated to the backdrop
                    if( !$target.hasClass( 'oxy-modal-backdrop' ) && !$this.hasClass( 'oxy-close-modal' ) ) {
                        //event.stopPropagation();
                        return;
                    }

                    if( $target.hasClass( 'oxy-modal-backdrop' ) && $this.hasClass( 'oxy-not-closable' ) ) {
                        return;
                    }

                    if( $this.hasClass( 'oxy-close-modal' ) ) event.preventDefault();

                    var $modal = $this.hasClass( 'oxy-close-modal' ) ? $this.closest('.oxy-modal-backdrop') : $this;
                    hideModal( $modal[0] );
                });

                jQuery(document).keyup( function(e){
                    if( e.key == 'Escape' ){
                        jQuery(".oxy-modal-backdrop:visible").each(function(index){
                            if( jQuery(this).data("close_on_esc") == 'on' ) hideModal(this);
                        });
                    }
                } );

            });

		</script>

	<?php }

// End CT_Modal class
}


// Create modal instance
global $oxygen_vsb_components;
$oxygen_vsb_components['modal'] = new CT_Modal( array(
		'name' 		=> 'Modal',
		'tag' 		=> 'ct_modal',
		'tabs'      => array(
            'trigger' => array(
                'heading' => __( 'Trigger', 'oxygen' ),
                'params' => array(
                    array(
                        "type" 			=> "dropdown",
                        "heading" 		=> "Show when",
                        "param_name" 	=> "trigger",
                        "value" 		=> array (
                            "on_exit_intent" 		=> "On exit intent",
                            "user_clicks_element" => "User clicks element",
                            "after_specified_time" => "After specified time",
                            "after_time_inactive" => "After time inactive",
                            "on_scroll_to_element" => "On scroll to element",
                            "after_number_of_clicks" => "After number of clicks",
                            "after_number_of_page_views" => "After number of page views",
                            "after_scrolled_amount" => "On page scroll",
                        ),
                        "default"       => "after_specified_time",
                        "css"			=> false,
                    ),
	                array(
		                "type" 			=> "dropdown",
		                "heading" 		=> "Scroll direction",
		                "param_name" 	=> "trigger_scroll_direction",
		                "value" 		=> array (
			                "down" 		=> "Down",
			                "up" => "Up",
		                ),
		                "default"       => "down",
		                "css"			=> false,
		                "condition"		=> "trigger=after_scrolled_amount",
	                ),
	                array(
		                "type" 			=> "textfield",
		                "heading" 		=> __("Number of page views"),
		                "param_name" 	=> "number_of_page_views",
		                "value" 		=> "3",
		                "css" 			=> false,
		                "condition"		=> "trigger=after_number_of_page_views",
	                ),
	                array(
		                "type" 			=> "textfield",
		                "heading" 		=> __("Number of clicks"),
		                "param_name" 	=> "number_of_clicks",
		                "value" 		=> "3",
		                "css" 			=> false,
		                "condition"		=> "trigger=after_number_of_clicks",
	                ),
                    array(
                        "type" 			=> "textfield",
                        "heading" 		=> __("Percentage of page needed to scroll"),
                        "param_name" 	=> "trigger_scroll_amount",
                        "value" 		=> "50",
                        "css" 			=> false,
                        "condition"		=> "trigger=after_scrolled_amount&&trigger_scroll_direction=down",
                    ),
	                array(
		                "type" 			=> "selector",
		                "heading" 		=> __("Scroll to element"),
		                "param_name" 	=> "scroll_to_selector",
		                "value" 		=> "",
		                "css" 			=> false,
		                "condition"		=> "trigger=on_scroll_to_element",
	                ),
                    array(
                        "type" 			=> "selector",
                        "heading" 		=> __("Trigger selector"),
                        "param_name" 	=> "trigger_selector",
                        "value" 		=> "",
                        "css" 			=> false,
                        "condition"		=> "trigger=user_clicks_element",
                    ),
                    array(
                        "type" 			=> "measurebox",
                        "heading" 		=> __("Open Modal After:"),
                        "param_name" 	=> "trigger_time",
                        "param_units"   => "seconds,milliseconds",
                        "value" 		=> "5",
                        "css" 			=> false,
                        "condition"		=> "trigger=after_specified_time",
                    ),
	                array(
		                "type" 			=> "measurebox",
		                "heading" 		=> __("Time inactive:"),
		                "param_name" 	=> "time_inactive",
		                "param_units"   => "seconds,milliseconds",
		                "value" 		=> "60",
		                "css" 			=> false,
		                "condition"		=> "trigger=after_time_inactive",
	                ),
                    array(
                        "param_name" 	=> "trigger_time-unit",
                        "value" 		=> "seconds",
                        "hidden" 		=> true
                    ),
	                array(
		                "param_name" 	=> "time_inactive-unit",
		                "value" 		=> "seconds",
		                "hidden" 		=> true
	                ),
                    array(
                        "type" 			=> "dropdown",
                        "heading" 		=> "After Modal Is Shown:",
                        "param_name" 	=> "open_again",
                        "value" 		=> array (
                            "always_show" 		=> "Show again on every page load",
                            "never_show_again" => "Never show again",
                            "show_again_after" => "Show again after:",
                        ),
                        "default"       => "always_show",
                        "condition"		=> "trigger!=user_clicks_element",
                        "css"			=> false,
                    ),
                    array(
                        "type" 			=> "measurebox",
                        "heading" 		=> __("Show Again After:"),
                        "param_name" 	=> "open_again_after_days",
                        "param_units" 	=> "days",
                        "value" 		=> "3",
                        "css" 			=> false,
                        "condition"		=> "open_again=show_again_after",
                    ),
                    array(
                        "param_name" 	=> "open_again_after_-unit",
                        "value" 		=> "days",
                        "hidden" 		=> true
                    ),
                )
            ),
            'content_styles' => array(
                'heading' => __( 'Content Styles', 'oxygen' ),
                'params' => array(
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
                        "value" 		=> "",
                        "true_value" 	=> "wrap",
                        "false_value" 	=> "",
                        "condition" 	=> "flex-direction=row"
                    ),
                    array(
                        "type" => "positioning",
                    ),
                    array(
                        "type" 			=> "colorpicker",
                        "heading" 		=> __("Text Color", "oxygen"),
                        "param_name" 	=> "color",
                    ),
                    array(
                        "type" 			=> "colorpicker",
                        "heading" 		=> __("Background Color", "oxygen"),
                        "param_name" 	=> "background-color",
                    ),
                )
            ),
            'modal_styles' => array(
                'heading' => __( 'Modal Styles', 'oxygen' ),
                'params' => array(
                    array(
                        "type" 			=> "measurebox",
                        "heading" 		=> __("Width", "oxygen"),
                        "param_name" 	=> "width",
                        "param_units"   => "%,px,vw",
                        "value"         => "70"
                    ),
                    array(
                        "param_name" 	=> "width-unit",
                        "value" 		=> "%",
                        "hidden"		=> true,
                        "css" 			=> false
                    ),
                    array(
                        "type" 			=> "colorpicker",
                        "heading" 		=> __("Backdrop Color", "oxygen"),
                        "param_name" 	=> "backdrop-color",
                        "value"         => "rgba(0,0,0,0.5)"
                    ),
                )
            ),
            'closing' => array(
                'heading' => __( 'Closing', 'oxygen' ),
                'params' => array(
                    array(
                        "type" 			=> "radio",
                        "heading" 		=> "Close Modal Automatically",
                        "param_name" 	=> "close_automatically",
                        "value" 		=> array(
                            'yes'       => __("Yes"),
                            'no'   	    => __("No")
                        ),
                        "default"       => 'no',
                        "css"			=> false,
                    ),
                    array(
                        "type" 			=> "measurebox",
                        "heading" 		=> __("Close modal after:"),
                        "param_name" 	=> "close_after_time",
                        "param_units" 	=> "seconds,milliseconds",
                        "value" 		=> "10",
                        "css" 			=> false,
                        "condition"		=> "close_automatically=yes",
                    ),
                    array(
                        "param_name" 	=> "close_after_time-unit",
                        "value" 		=> "seconds",
                        "hidden" 		=> true
                    ),
                    array(
                        "type" 			=> "radio",
                        "heading" 		=> "Close Modal On Ajax Form Submit",
                        "param_name" 	=> "close_after_form_submit",
                        "value" 		=> array(
                            'yes'       => __("Yes"),
                            'no'   	    => __("No")
                        ),
                        "default"       => 'no',
                        "css"			=> false,
                        "hidden"        => true
                    ),
	                array(
		                "type" 			=> "dropdown",
		                "heading" 		=> "Close on ESC key",
		                "param_name" 	=> "close_on_esc",
		                "value" 		=> array (
			                "on" 		=> "Yes",
			                "off" => "No",
		                ),
		                "default"       => "on",
		                "css"			=> false,
	                ),
                    array(
                        "type" 			=> "radio",
                        "heading" 		=> "Close Modal On Backdrop Click",
                        "param_name" 	=> "close_modal_on_backdrop_click",
                        "value" 		=> array(
                            'yes'       => __("Yes"),
                            'no'   	    => __("No")
                        ),
                        "default"       => 'yes',
                        "css"			=> false,
                    ),
                )
            ),
        ),
		'params' 	=> array(
			array(
				"param_name" 	=> "tag",
				"value" 		=> "div",
				"hidden" 		=> true
			),
			array(
				"type" 			=> "radio",
				"heading" 		=> "In-editor behavior",
				"param_name" 	=> "behavior",
				"value" 		=> array(
					1 	        => __("Inline"),
					2   	    => __("Live preview"),
					3   	    => __("Hidden")
				),
				"default"       => 1,
				"css"			=> false,
			),
		),
		/* set defaults */
		'advanced' 	=> array(
			'flex' => array(
				'values' 	=> array (
					'display' 		 => 'flex',
					'flex-direction' => 'column',
					'align-items' 	 => 'flex-start',
					'justify-content'=> '',
					'text-align' 	 => '',
					'flex-wrap' 	 => 'nowrap',
				)
			),
			'size' => array(
				'values' 	=> array (
					'width' 		 				=> '70',
					'width-unit' 					=> '%',
				)
			),
			'background' => array(
				'values' 	=> array (
					'background-color'  => '#FFFFFF',
					'background-size' 	=> 'cover',
				)
			),
			'allow_shortcodes'  => true,
		),

	)
);

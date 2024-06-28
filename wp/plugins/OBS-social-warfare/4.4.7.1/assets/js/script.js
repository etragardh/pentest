/**
 *
 * Social Warfare - The Javascript
 *
 * @since 1.0.0 | 01 JAN 2016 | Created
 * @since 3.4.0 | 19 OCT 2018 | Cleaned, Refactored, Simplified, Docblocked.
 * @since 3.6.0 | 22 APR 2019 | Removed Facebook share counts. These are now
 *                              fetched via PHP on the server side just like all
 *                              other social networks.
 * @package   SocialWarfare\Assets\JS\
 * @copyright Copyright (c) 2018, Warfare Plugins, LLC
 * @license   GPL-3.0+
 *
 *
 * This is the primary javascript file used by the Social Warfare plugin. It is
 * loaded both on the frontend and the backend. It is used to control all client
 * side manipulation of the HTML.
 *
 *
 * Table of Contents:
 *
 *     #1. Initialization Functions
 *        Property: socialWarfare.paddingTop
 *        Property: socialWarfare.paddingBottom
 *        Function: socialWarfare.initPlugin()
 *        Function: socialWarfare.establishPanels()
 *
 *     #2. Static Horizontal Button Panel Controls
 *        Function: socialWarfare.activateHoverStates()
 *        Function: socialWarfare.resetStaticPanel()
 *        Function: socialWarfare.handleButtonClicks()
 *
 *     #3. Floating Buttons Panel Controls
 *        Function: socialWarfare.createFloatHorizontalPanel()
 *        Function: socialWarfare.staticPanelIsVisible()
 *        Function: socialWarfare.updateFloatingButtons()
 *        Function: socialWarfare.toggleMobileButtons()
 *        Function: socialWarfare.toggleFloatingVerticalPanel()
 *        Function: socialWarfare.toggleFloatingHorizontalPanel()
 *        Function: socialWarfare.positionFloatSidePanel()
 *
 *     #4. Pinterest Image Hover Save Buttons
 *        Function: socialWarfare.enablePinterestSaveButtons()
 *        Function: socialWarfare.renderPinterestSaveButton()
 *        Function: socialWarfare.findPinterestBrowserSaveButtons()
 *        Function: socialWarfare.removePinterestBrowserSaveButtons()
 *
 *     #5. Facebook Share Count Functions
 *        Function: socialWarfare.fetchFacebookShares()
 *        Function: socialWarfare.parseFacebookShares()
 *
 *     #6. Utility/Helper Functions
 *        Function: socialWarfare.throttle()
 *        Function: socialWarfare.trigger()
 *        Function: socialWarfare.trackClick()
 *        Function: socialWarfare.checkListeners()
 *        Function: socialWarfare.establishBreakpoint()
 *        Function: socialWarfare.isMobile()
 *
 *
 * Javascript variables created on the server:
 *
 *     bool   	swpClickTracking (SWP_Script.php)
 *     bool   	socialWarfare.floatBeforeContent
 *     object 	swpPinIt
 *     string 	swp_admin_ajax
 *     string 	swp_post_url
 *     string 	swp_post_recovery_url
 *
 */


/**
 * The first thing we want to do is to declare our socialWarfare object. We are
 * going to use this object to store all functions that our plugin uses. This will
 * allow us to avoid any naming collisions as well as allowing us to keep things
 * more neatly organized.
 *
 */
window.socialWarfare = window.socialWarfare || {};


/**
 * This allows us to scope all variables and functions to within this anonymous
 * function. However, since we are using a global object, socialWarfare, we will
 * still be able to access our functions and variables from anywhere.
 *
 */
(function(window, $) {
	'use strict';

	if ( typeof $ != 'function' ) {
		if ( typeof jQuery == 'function' ) {
			var $ = jQuery;
		}

		else {
			console.log("Social Warfare requires jQuery, or $ as an alias of jQuery. Please make sure your theme provides access to jQuery before activating Social Warfare.");
			return;
		}
	}

	/**
	* Values from the server may be sent as strings, but may also be empty.
	* In this context, we are interested in strings with length only.
	*/
	function isString(maybeString) {
	  return typeof maybeString == 'string' && maybeString.length > 0;
	}

	/***************************************************************************
	 *
	 *
	 *    SECTION #1: INITIALIZATION FUNCTIONS
	 *
	 *
	 ***************************************************************************/


	/**
	 * These variables measure the amount of padding at the top and bottom of
	 * the page upon the dom loaded event. We grab these early on and keep them
	 * stored so that we can add 50 pixels of padding whenever the floating
	 * horizontal buttons are displayed. This will allow us to avoid having our
	 * buttons hover over menus or copyright information in the footer.
	 *
	 */
	socialWarfare.paddingTop    = parseInt($('body').css('padding-top'));
	socialWarfare.paddingBottom = parseInt($('body').css('padding-bottom'));


	/**
	 * Initializes the buttons provided that they exist.
	 *
	 * This function will activate the hover effects for the buttons, it will
	 * create the floting buttons, center vertically the side panel, handle
	 * and set up the button clicks, and monitor the scroll activity in order to
	 * show and hide any floating buttons.
	 *
	 * @param  void
	 * @return void
	 *
	 */
	socialWarfare.initPlugin = function() {
		$("body").css({
			paddingTop: socialWarfare.paddingTop,
			paddingBottom: socialWarfare.paddingBottom
		});

		socialWarfare.establishPanels();
		socialWarfare.establishBreakpoint();

		// Bail out if no buttons panels exist.
		if (!socialWarfare.panels.staticHorizontal && !socialWarfare.panels.floatingSide && !socialWarfare.panels.floatingHorizontal) {
			return;
		}

		socialWarfare.emphasizeButtons();
		socialWarfare.createFloatHorizontalPanel();
		socialWarfare.positionFloatSidePanel();
		socialWarfare.activateHoverStates();
		socialWarfare.handleButtonClicks();
		socialWarfare.updateFloatingButtons();
		socialWarfare.closeLightboxOverlay();
		socialWarfare.preloadPinterestImages();

		if (typeof swpPinIt == 'object' && swpPinIt.enabled == true) {
			socialWarfare.createHoverSaveButton();
			socialWarfare.triggerImageListeners();
		}


		/**
		 * In some instances, the click bindings were not being instantiated
		 * properly when they were run as the DOM was loaded. So we built this
		 * checkListeners() function to recheck every 2 seconds, 5 total times, to
		 * ensure that the buttons panel exist and activate the click bindings.
		 *
		 */
		setTimeout(function() {
			socialWarfare.checkListeners(0, 5)
		}, 2000);


		/**
		 * This will allow us to monitor whether or not the static horizontal
		 * buttons are inside the viewport as a user is scrolling the page. If
		 * they are not in the viewport, we will display the floating buttons.
		 * The throttle is to prevent it from firing non stop and causing the
		 * floating buttons to flicker.
		 *
		 */
		var time = Date.now();
		var scrollDelay = 50;
		$(window).on('scroll', function() {
			if ((time + scrollDelay - Date.now()) < 0) {
				socialWarfare.updateFloatingButtons();
				time = Date.now();
			}
		});
	}


	/**
	 * This will cause our resize event to wait until the user is fully done
	 * resizing the window prior to resetting and rebuilding the buttons and
	 * their positioning and re-initializing the plugin JS functions.
	 *
	 */
	var resizeWait;
	socialWarfare.onWindowResize = function(){
	  clearTimeout(resizeWait);
	  resizeWait = setTimeout(socialWarfare.initPlugin, 100 );
	}


	/**
	 * Finds each kind of buttons panel, if it exists, and stores it to the
	 * socialWarfare object for later reference. This is useful for reading data
	 * attributes of buttons panels without needing to fetch the panel every time.
	 *
	 * @return object The object which holds each of the kinds of buttons panels.
	 */
	socialWarfare.establishPanels = function() {
		//* Initialize the panels object with the three known panel types.
		socialWarfare.panels = {
			staticHorizontal: null,
			floatingSide: null,
			floatingHorizontal: null
		};

		// Set each type of panel as a jQuery object (with 0 or more panels)
		socialWarfare.panels.staticHorizontal = $(".swp_social_panel").not(".swp_social_panelSide");
		socialWarfare.panels.floatingSide = $(".swp_social_panelSide");

		return socialWarfare.panels;
	}


	/**
	 * A function to emphasize the first couple of buttons in the panel.
	 *
	 * @since  4.0.0 | 14 JUL 2019 | Created
	 * @param  void
	 * @return void
	 *
	 */
	socialWarfare.emphasizeButtons = function() {


		/**
		 * If the variable that was passed from the server with the setting
		 * isn't set, then bail early. Gracefully fail.
		 *
		 */
		if (typeof socialWarfare.variables.emphasizeIcons == 'undefined') {
			return;
		}


		/**
		 * Don't use this feature on mobile. This is a desktop only feature.
		 *
		 */
		if (socialWarfare.isMobile()) {
			return;
		}


		/**
		 * Loop through each buttons panel on the page. Within each panel, we'll
		 * loop through each of the buttons and emphasize them.
		 *
		 */
		jQuery(".swp_social_panel:not(.swp_social_panelSide)").each(function(i, panel){
			jQuery(panel).find(".nc_tweetContainer:not(.total_shares)").each( function(index, button) {
				if( index < socialWarfare.variables.emphasizeIcons) {

					var shareWidth     = jQuery(button).find(".swp_share").width();
					var iconWidth      = jQuery(button).find("i.sw").outerWidth();
					var iconTextWidth  = shareWidth + iconWidth + 35;
					var containerWidth = jQuery(button).width();
					var change         = 1 + ((shareWidth + 35) / containerWidth);

					if(change < 2) {
						jQuery(button)
							.addClass("swp_nohover")
							.css({'flex': '2 1 0%'})
							.find('.iconFiller')
							.width(iconTextWidth);
					} else {
						jQuery(button)
							.addClass("swp_nohover")
							.css({'flex': change  + ' 1 0%'})
							.find('.iconFiller')
							.width(iconTextWidth);
					}
				}
			});
		});
	}


	/***************************************************************************
	 *
	 *
	 *    SECTION #2: STATIC HORIZONTAL BUTTON PANEL CONTROLS
	 *
	 *
	 ***************************************************************************/


	/**
	 * This triggers the hover effect that you see when you hover over the
	 * buttons in the panel. It measures the space needed to expand the button
	 * to reveal the call to action for that network and then uses flex to
	 * expand it and to shrink the other buttons to make room for the expansion.
	 *
	 * @since 2.1.0
	 * @param  void
	 * @return void
	 *
	 */
	socialWarfare.activateHoverStates = function() {
		socialWarfare.trigger('pre_activate_buttons');

		$('.swp_social_panel:not(.swp_social_panelSide) .nc_tweetContainer').on('mouseenter', function() {

			if ($(this).hasClass('swp_nohover')) {
				return;
			}

			socialWarfare.resetStaticPanel();
			var termWidth      = $(this).find('.swp_share').outerWidth();
			var iconWidth      = $(this).find('i.sw').outerWidth();
			var containerWidth = $(this).width();
			var change         = 1 + ((termWidth + 35) / containerWidth);

			$(this).find('.iconFiller').width(termWidth + iconWidth + 25 + 'px');
			$(this).css("flex", change + ' 1 0%');
		});

		$('.swp_social_panel:not(.swp_social_panelSide)').on('mouseleave', socialWarfare.resetStaticPanel);
	}


	/**
	 * Resets the static panels to their default styles. After they've been
	 * expanded by activateHoverStates(), this function returns the buttons to
	 * their normal state once a user is no longer hovering over the buttons.
	 *
	 * @see activateHoverStates().
	 * @param  void
	 * @return void
	 *
	 */
	socialWarfare.resetStaticPanel = function() {
		$(".swp_social_panel:not(.swp_social_panelSide) .nc_tweetContainer:not(.swp_nohover) .iconFiller").removeAttr("style");
		$(".swp_social_panel:not(.swp_social_panelSide) .nc_tweetContainer:not(.swp_nohover)").removeAttr("style");
	}


	/**
	 * Handle clicks on the buttons that open share windows. It fetches the
	 * share link, it opens the share link into a new window, it sizes the
	 * popout window, and makes sure the user is able to share the content.
	 *
	 * This also handles sending the events to Google Analytics and Google Tag
	 * Manager if the user has that feature enabled.
	 *
	 * @since  1.0.0 | 01 JAN 2018 | Created
	 * @since  4.0.0 | 25 FEB 2020 | Added "Print" button functionality.
	 * @since  4.0.0 | 28 FEB 2020 | Added the Pinterest multi image section.
	 * @param  void
	 * @return bool Returns false on failure.
	 *
	 */
	socialWarfare.handleButtonClicks = function() {


		/**
		 * In order to avoid the possibility that this function may be called
		 * more than once, we remove all click handlers from our buttons prior
		 * to activating the new click handler. Prior to this, there were some
		 * unique instances where clicking on a button would cause multiple
		 * share windows to pop out.
		 *
		 */
		$('.nc_tweet, a.swp_CTT').off('click');
		$('.nc_tweet, a.swp_CTT').on('click', function(event) {


			/**
			 * This will intercept clicks that are made on the "Print" button
			 * and trigger the window.print() method opening the browser's print
			 * functionality.
			 *
			 */
			if($(this).parent('.swp_print').length > 0) {
				event.preventDefault();
				window.print();
				return;
			}


			/**
			 * This will intercept clicks that are made on the Pinterest button
			 * if the Pinterest button is set to display multiple images
			 * available to the user to choose from. This will trigger the
			 * lightbox overlay presenting the images to the user.
			 *
			 */
			if( true === $(this).hasClass('pinterest_multi_image_select') ) {
				event.preventDefault();
				socialWarfare.openMultiPinterestOverlay( $(this) );
				return;
			}


			/**
			 * This will intercept clicks that are made on the "More" button and
			 * load up our lightbox containing the buttons panels with all of
			 * the available buttons for the entire plugin.
			 *
			 */
			if($(this).parent('.swp_more').length > 0) {
				event.preventDefault();
				socialWarfare.openMoreOptions( $(this) );
				return;
			}


			/**
			 * Some buttons that don't have popout share windows can use the
			 * 'nopop' class to disable this click handler. This will then make
			 * that button behave like a standard link and allow the browser's
			 * default click handler to handle it. This is for things like the
			 * email button.
			 *
			 * This used to return false, but that cancels the default event
			 * from firing. The whole purpose of this exclusion is to allow the
			 * original event to fire so returning without a value allows it to
			 * work.
			 *
			 */
			if ($(this).hasClass('noPop')) {
				return event;
			}


			/**
			 * Our click handlers will use the data-link html attribute on the
			 * button as the share URL when opening the share window. Therefore,
			 * we need to make sure that this attribute exists.
			 *
			 */
			if ('undefined' == typeof $(this).data('link') && false === $(this).is('.swp-hover-pin-button')) {
				return event;
			}


			/**
			 * This needs to run after all of the bail out conditions above have
			 * been run. We don't want to preventDefault if a condition exists
			 * wherein we don't want to take over the event.
			 *
			 */
			event.preventDefault();


			/**
			 * Fetch the share link that we'll use to call the popout share
			 * windows and then declare the variables that we'll be using later.
			 *
			 */
			var href = $(this).data('link').replace('’', '\'');
			var height, width, top, left, instance, windowAttributes, network;


			/**
			 * These are the default dimensions that are used by most of the
			 * popout share windows. Additionally, a few of the windows have
			 * their own javascript that will resize the window dynamically
			 * once loaded.
			 *
			 */
			height = 270;
			width = 500;


			/**
			 * Pinterest, Buffer, and Flipboard use a different size than the
			 * rest so if it's one of those buttons, overwrite the defaults
			 * that we set above.
			 *
			 */
			if ($(this).is('.swp_pinterest a, .buffer_link a, .flipboard a, .swp-hover-pin-button')) {
				height = 550;
				width = 775;
			}


			/**
			 * If a button was clicked, use the data-network attribute to
			 * figure out which network is being shared. If it was a click
			 * to tweet that was clicked on, just use ctt as the network.
			 *
			 */
			if ($(this).hasClass('nc_tweet')) {
				network = $(this).parents('.nc_tweetContainer').data('network');
			} else if ($(this).hasClass('swp_CTT')) {
				network = 'ctt';
			}


			/**
			 * We'll measure the window and then run some calculations to ensure
			 * that our popout share window opens perfectly centered on the
			 * browser window.
			 *
			 */
			top = window.screenY + (window.innerHeight - height) / 2;
			left = window.screenX + (window.innerWidth - width) / 2;
			windowAttributes = 'height=' + height + ',width=' + width + ',top=' + top + ',left=' + left;
			instance = window.open(href, network, windowAttributes);

			// Active Google Analytics event tracking for the button click.
			socialWarfare.trackClick(network);
		});
	}


	/**
	 * The openMultiPinterestOverlay() function will control the overlay that
	 * appears on the screen when the user has multiple Pinterest images
	 * available to choose from.
	 *
	 * @since  4.0.0 | 28 FEB 2020 | Created
	 * @param  object element The Pinterest button DOM element that was clicked.
	 * @return void
	 *
	 */
	socialWarfare.openMultiPinterestOverlay = function( element ) {

		// If the overly has already been loaded once, just fade it in.
		if( $('.pinterest-overlay').length > 0 ) {
			$('.pinterest-overlay').fadeIn();
			$('.swp-lightbox-inner').scrollTop(0);
			return;
		}


		/**
		 * We'll use this variable to store the string of html as we build it out.
		 *
		 */
		var html = '';


		/**
		 * The pin data will be a JSON encoded array of data needed to build the
		 * Pinterest image selection overlay. It will contain the following data:
		 *
		 * description - The Pinterest Description
		 * url         - The processes, shareable link to the post.
		 * images      - An array of image URL's to populate the selection.
		 *
		 */
		var pin_data = element.data('pins');
		var pin_images = '';


		/**
		 * We'll loop through each available image that the user has provided
		 * and create a pinnable link to each one for the end user to select from.
		 *
		 */
		pin_data.images.forEach( function( image ) {

			// Build the share link for this image.
			var share_url = 'https://pinterest.com/pin/create/button/?url=' + pin_data.url +
			'&media=' + image +
			'&description=' + encodeURIComponent( pin_data.description );

			// Build out the HTML for the image and button.
			var pin_html = '';
			pin_html += '<div class="pin_image_select_wrapper">';
			pin_html += '<img class="pin_image" src="'+ image +'" />';
			pin_html += '<a class="swp-hover-pin-button" href="'+ share_url +'" data-link="'+ share_url +'">Save</a>';
			pin_html += '</div>';

			// Add all the images and buttons to the main html.
			pin_images += pin_html;
		});

		// Create the html for the lightbox overlay, the title, and the close button.
		html += '<div class="swp-lightbox-wrapper pinterest-overlay"><div class="swp-lightbox-inner">';
		html += '<i class="sw swp_pinterest_icon top_icon"></i>';
		html += '<div class="swp-lightbox-close"></div>';
		html += '<h5>Which image would you like to pin?</h5>';
		html += '<div class="pin_images_wrapper">';
		html += pin_images;
		html += '</div>';
		html += socialWarfare.buildPoweredByLink();
		html += '</div></div>';

		// Append it, and hide it first so that we can fade it in.
		$('body').append(html);
		$('.pinterest-overlay').hide().fadeIn();

		// Add the click handlers to the newly added elements (i.e. the buttons)
		socialWarfare.handleButtonClicks();


		/**
		 * Here we'll loop through all of the images, and find the shortest one.
		 * We'll then restrict all of them to be that height. This way all of the
		 * images in the row will be the same height.
		 *
		 * Since we're looking for the smalled image, we'll start with an
		 * insanely high number and every time we encounter one that is shorter,
		 * we'll overwrite the number with that one.
		 *
		 */
		var max_height = 999999;


		/**
		 * The images.load() will fire each and every time an image in the set
		 * is loaded. This means that if there are 5 images, it will fire off
		 * 5 separate times. However, we only want to fire after the last one is
		 * loaded. As such, we'll count the number of elements, and iterate a
		 * counter on each lap through the loop. Then once the counter reaches
		 * the total count of the images, we'll run our function. This means that
		 * it will only run one time after all of the images have loaded.
		 *
		 */
		var iteration = 0, images = $('.pinterest-overlay .pin_images_wrapper img');


		/**
		 * This will run when each image is loaded, so we'll filter below to
		 * only run when the last one is finished.
		 *
		 * This will cause each of the images to be the same exact height as one
		 * another while also always filling up 100% of the width of the overlay
		 * container. It's very similar to the Flick image layout effect.
		 *
		 */
		images.load( function() {

			// This will run after the last image has been loaded.
			if( ++iteration === images.length ) {

				// This will select all of the images and loop through them.
				images.each( function() {

					// Check if this image is shorter than the previous image.
					if( $(this).height() < max_height ) {

						// If it is shorter, this becomes our new "max height"
						max_height = $(this).height();
					}


				/**
				 * Once we've looped through all of the images and determined
				 * the shortest image, now we'll set the height of all of the
				 * taller images to match the shortest one. Now they are all the
				 * same height.
				 *
				 */
				}).promise().done( function() {

					// This will make all of the images the same height.
					images.height(max_height + 'px');

					// Calculate the number of horizontal rows.
					var number_of_rows = Math.ceil( images.length / 4 );

					// Loop through each row of images.
					for (i = 0; i < number_of_rows; i++) {

						// Select the images that are in this row.
						var current_row_images = images.slice( i * 4, i * 4 + 4 );

						// If a row only has 2 images, it will be a maximum of 50% filled.
						var max_allowable_width = current_row_images.length / 4;

						// Find the total width of the container element.
						var total_width = $('.pin_images_wrapper').width();

						// Find the total width of all of the images in this row.
						var total_images_width = 0;
						current_row_images.each( function() {
							total_images_width = total_images_width + $(this).width();
						});

						// Find the ratio between image width and container width.
						var ratio = total_width / total_images_width;


						/**
						 * This will set the width of each pin to our calculated
						 * value minus 1% since we add 1% of padding to the side.
						 *
						 */
						current_row_images.each( function() {
							var new_width = ( ( $(this).width() * ratio / total_width ) * 100) * max_allowable_width - 1;
							$(this).parent().width( new_width + '%' );
							$(this).height('auto');
						});


						/**
						 * Some of the image heights were off by as much as 2px
						 * from each other. With this, we'll just grab the height
						 * of the first image in the set and then set all the
						 * heights and widths to a static measurements.
						 *
						 */
						var height = current_row_images.first().height();
						current_row_images.each( function() {
							$(this).width( $(this).width() ).height(height);
						});
					}
				});
			}
		});

	}


	socialWarfare.buildPoweredByLink = function() {
		var html = '';
		if( true === socialWarfare.variables.powered_by_toggle ) {
			var anchor_tag_open = '';
			var anchor_tag_close = '';

			if( false !== socialWarfare.variables.affiliate_link ) {
				anchor_tag_open = '<a href="'+ socialWarfare.variables.affiliate_link +'" target="_blank">';
				anchor_tag_close = '</a>';
			}

			html = '<div class="swp_powered_by">'+ anchor_tag_open +'<span>Powered by</span> <img src="/wp-content/plugins/social-warfare/assets/images/admin-options-page/social-warfare-pro-light.png">'+ anchor_tag_close +'</div>';

		}
		return html;
	}


	/**
	 * If we preload the images prior to bringing up the overlay, they'll be
	 * ready so that we can run our resizing algorithms on them and get the
	 * overlay loaded instantly when called.
	 *
	 * @since  4.0.0 | 29 FEB 2020 | Created
	 * @param  void
	 * @return void
	 *
	 */
	socialWarfare.preloadPinterestImages = function() {

		// Bail if we don't have a Pinterest Multi-Image setup to preload.
		if( $('.pinterest_multi_image_select').length < 1 ) {
			return;
		}

		// Fetch the pin_data from the data attribute.
		var pin_data = $('.pinterest_multi_image_select').data('pins');

		// Loop through the Pinterest images and preload them.
		pin_data.images.forEach( function( image_url ) {
			var image_object = new Image();
			image_object.src = image_url;
		});
	}


	/**
	 * The openMoreOptions function will open up the lightbox panel that contains
	 * all of the social network buttons for the entire plugin allowing the user
	 * to choose from all available sharing options.
	 *
	 * @param  void
	 * @return void
	 *
	 */
	socialWarfare.openMoreOptions = function( element ) {

		// If the options already exist, just reopen them by fading them in.
		if( $('.swp-more-wrapper').length > 0 ) {
			$('.swp-more-wrapper').fadeIn();
			return;
		}

		// Fetch the post_id from the parent container, the buttons panel.
		var post_id = element.parents('.swp_social_panel').data('post-id');

		// Setup the data for the admin-ajax call to fetch the button's html.
		var data = {
			action: 'swp_buttons_panel',
			post_id: post_id,
			_ajax_nonce: swp_nonce
		};

		// Post the data to the admin-ajax and fetch the html.
		jQuery.post(swp_ajax_url, data, function(response){

			// Append the fetched html to the body.
			$('body').append(response);

			// Hide the appended html only so that we can fade it into view.
			$('.swp-lightbox-wrapper').hide().fadeIn();

			// Activate the hover states and button click handlers.
			socialWarfare.activateHoverStates();
			socialWarfare.handleButtonClicks();
		});
	}


	/**
	 * The closeLightboxOverlay() function will handle clicks on the red X in the
	 * corner of the more options box and will fade the lightbox out of view.
	 *
	 * @param  void
	 * @return void
	 *
	 */
	socialWarfare.closeLightboxOverlay = function() {

		// Handle clicks on the red X
		$('body').on('click','.swp-lightbox-close', function() {

			// Fade the lightbox out of view and scroll it to the top so that it
			// doesn't open midway down the viewport.
			$('.swp-lightbox-wrapper').fadeOut();
		});

		// Handle presses of the "Escape" button on the keyboard.
		$(document).on('keyup', function(e) {
			if (e.key === "Escape") {

				// Fade the lightbox out of view and scroll it to the top so that it
				// doesn't open midway down the viewport.
				$('.swp-lightbox-wrapper').fadeOut();
			}
		});

	}


	/***************************************************************************
	 *
	 *
	 *    SECTION #3: FLOATING BUTTONS PANEL CONTROLS
	 *
	 *
	 ***************************************************************************/


	/**
	*  Clones a copy of the static buttons to use as a floating panel.
	*
	* We clone a set of the static horizontal buttons so that when we create
	* the floating set we can make the position match exactly. This way when
	* they are showing up and disappearing, it will create the allusion that
	* the static buttons are just getting glued to the edge of the screen and
	* following along with the user as they scroll.
	*
	* @since  1.0.0 | 01 JAN 2016 | Created
	* @param  void
	* @return void
	*
	*/
	socialWarfare.createFloatHorizontalPanel = function() {

		//* If a horizontal panel does not exist, we can not create a bar.
		if (!socialWarfare.panels.staticHorizontal.length) {
			return;
		}

		var floatLocation       = socialWarfare.panels.staticHorizontal.data("float");
		var mobileFloatLocation = socialWarfare.panels.staticHorizontal.data("float-mobile");
		var backgroundColor     = socialWarfare.panels.staticHorizontal.data("float-color");
		var wrapper             = $('<div class="nc_wrapper swp_floating_horizontal_wrapper" style="background-color:' + backgroundColor + '"></div>');
		var barLocation         = '';

		//* .swp_social_panelSide is the side floater.
		if ($(".nc_wrapper").length) {
			$(".nc_wrapper").remove();
		}

		//* repeating the code above for the new selector.
		if ($(".swp_floating_horizontal_wrapper").length) {
			$(".swp_floating_horizontal_wrapper").remove();
		}

		//* No floating bars are used at all.
		if (floatLocation != 'top' && floatLocation != 'bottom' && mobileFloatLocation != "top" && mobileFloatLocation != "bottom") {
			return;
		}

		//* Set the location (top or bottom) of the bar depending on
		if (socialWarfare.isMobile()) {
			barLocation = mobileFloatLocation;
		} else {
			barLocation = floatLocation;
		}

		//* Assign a CSS class to the wrapper based on the float-mobile location.
		wrapper.addClass(barLocation).hide().appendTo('body');

		//* Save the new buttons panel to our ${panels} object.
		socialWarfare.panels.floatingHorizontal = socialWarfare.panels.staticHorizontal.first().clone();
		socialWarfare.panels.floatingHorizontal.addClass('nc_floater').appendTo(wrapper);
		socialWarfare.updateFloatingHorizontalDimensions();

		$(".swp_social_panel .swp_count").css({
			transition: "padding .1s linear"
		});
	}

  /**
   * Callback on window resize to update the width and position of a
   * floatingHorizontal panel.
   *
   */
	socialWarfare.updateFloatingHorizontalDimensions = function() {

		// If there is no static set to measure, just bail out.
		if (!socialWarfare.panels.staticHorizontal.length) {
			return;
		}


		// If there is no floating set, just bail.
		if(!socialWarfare.panels.floatingHorizontal) {
			return;
		}


		/**
		 * We'll create the default width and left properties here. Then we'll
		 * attempt to pull these properties from the actual panel that we are
		 * cloning below. If those measurements exist, we clone them. If not,
		 * we use these defaults.
		 *
		 */
		var width = "100%";
		var left  = 0;
		var panel = socialWarfare.panels.staticHorizontal;
		var parent = panel.parent();

		//* Ignore the invisible wrapper div, it has no width.
		if (parent.hasClass("swp-hidden-panel-wrap")) {
			parent = parent.parent();
		}

		if( 'undefined' !== typeof panel.offset().left ) {
			left = panel.offset().left;
		}

		if( 'undefined' !== typeof panel.width() ) {
			width = panel.width();
		}

		if( left == 0 ) {
			left = parent.offset().left;
		}

		//* The panel width is 'auto', which evaluates to 100%
		if (width == 100 || width == 0) {
			width = parent.width();
		}

		//* Give the bar panel the appropriate classname and put it in its wrapper.
		socialWarfare.panels.floatingHorizontal.css({
			width: width,
			left: left
		});
	}


	/**
	 * Determines if a set of static buttons is currenty visible on the screen.
	 *
	 * We will use this to determine whether or not we should display a set of
	 * floating buttons. Whenever the static buttons are visible, we hide the
	 * floating buttons. Whenever the static buttons are not visible, we show
	 * the floating buttons.
	 *
	 * @param  void
	 * @return bool True if a static set of buttons is visible on the screen, else false.
	 *
	 */
	socialWarfare.staticPanelIsVisible = function() {
		var visible = false;
		var scrollPos = $(window).scrollTop();

		//* Iterate each buttons panel, checking each to see if it is currently visible.
		$(".swp_social_panel").not(".swp_social_panelSide, .nc_floater").each(function(index) {
			var offset = $(this).offset();

			//* Do not display floating buttons before the horizontal panel.
			//* PHP json_encode() maps `true` to "1" and `false` to "".
			if (typeof socialWarfare.floatBeforeContent != 'undefined' && "1" != socialWarfare.floatBeforeContent) {
				var theContent = $(".swp-content-locator").parent();

				//* We are in sight of an "Above the content" panel.
				if (index === 0 && theContent.length && theContent.offset().top > (scrollPos + $(window).height())) {
					visible = true;
				}
			}

			//* Do not display floating buttons if a panel is currently visible.
			if ($(this).is(':visible') &&
					offset.top + $(this).height() > scrollPos &&
					offset.top < (scrollPos + $(window).height())) {

				visible = true;
			}
		});

		return visible;
	}


	/**
	 * Handler to toggle the display of either the side or bar floating buttons.
	 *
	 * We only show the floating buttons when the static horizontal buttons are
	 * not in the visible view port. This function is used to toggle their
	 * visibility when they need to be shown or hidden.
	 *
	 * @since  2.0.0 | 01 JAN 2016 | Created
	 * @param  void
	 * @return void
	 *
	 */
	socialWarfare.updateFloatingButtons = function() {
		// If buttons are on the page, there must be either a static horizontal
		if (socialWarfare.panels.staticHorizontal.length) {
			var panel = socialWarfare.panels.staticHorizontal;
		}

		// Or a side floating panel.
		else if (socialWarfare.panels.floatingSide.length) {
			var panel = socialWarfare.panels.floatingSide;
		}

		else {
			return;
		}

		// Adjust the floating bar
		var location = panel.data('float');

		if (true == socialWarfare.isMobile()) {
			var location = panel.data('float-mobile');
		}

		//* There are no floating buttons enabled, hide any that might exist.
		if (location == 'none') {
			return $(".nc_wrapper, .swp_floating_horizontal_wrapper, .swp_social_panelSide").hide();
		}

		if (socialWarfare.isMobile()) {
			socialWarfare.toggleMobileButtons();
			socialWarfare.toggleFloatingHorizontalPanel();
			return;
		}

		if (location == "right" || location == "left") {
			socialWarfare.toggleFloatingVerticalPanel();
		}

		if (location == "bottom" || location == "top") {
			socialWarfare.toggleFloatingHorizontalPanel();
		}
	}


	/**
	 * Toggle the visibilty of a mobile bar.
	 *
	 * @return void
	 *
	 */
	socialWarfare.toggleMobileButtons = function() {

		//* There are never any left/right floating buttons on mobile, so hide them.
		socialWarfare.panels.floatingSide.hide();

		var visibility = socialWarfare.staticPanelIsVisible() ? "collapse" : "visible";
		$(".nc_wrapper, .swp_floating_horizontal_wrapper").css("visibility", visibility);
	}


	/**
	 * Toggle the display of a side panel, depending on static panel visibility.
	 *
	 * @return void
	 *
	 */
	socialWarfare.toggleFloatingVerticalPanel = function() {
		var direction = '';
		var location = socialWarfare.panels.floatingSide.data("float");
		var visible  = socialWarfare.staticPanelIsVisible();
		var offset = "";

		//* This is on mobile and does not use side panels.
		if (socialWarfare.isMobile()) {
			return socialWarfare.panels.floatingSide.hide();
		}

		if (!socialWarfare.panels.floatingSide || !socialWarfare.panels.floatingSide.length) {
			// No buttons panel! Update `visible` to hide floaters.
			visible = true;
		}

		if (socialWarfare.panels.floatingSide.data("transition") == "slide") {
			direction = location;
			offset     = visible ? "-150px" : "5px";
			//* Update the side panel CSS with the direction and amount.
			socialWarfare.panels.floatingSide.css(direction, offset).show();
		}

		else {
			/**
			 * We had problems with the fading buttons flickering rather than having
			 * a smooth fade animation. The workaround was to manually control opacity,
			 * fade, and opacity again.
			 *
			 */
			if (visible) {
				socialWarfare.panels.floatingSide.css("opacity", 1)
					.fadeOut(300)
					.css("opacity", 0);
			}

			else {
				socialWarfare.panels.floatingSide.css("opacity", 0)
					.fadeIn(300)
					.css("display", "flex")
					.css("opacity", 1);
			}
		}
	}


	socialWarfare.hasReferencePanel = function() {
		return typeof socialWarfare.panels.staticHorizontal != 'undefined' &&
					  socialWarfare.panels.staticHorizontal.length > 0
	}


	/**
	 * Toggle the display of a floating bar, depending on static panel visibility.
	 *
	 * @return void
	 *
	 */
	socialWarfare.toggleFloatingHorizontalPanel = function() {
		if (!socialWarfare.hasReferencePanel()) {
			return;
		}

		// If there is no floating set, just bail.
		if(!socialWarfare.panels.floatingHorizontal) {
			return;
		}

		var panel = socialWarfare.panels.floatingHorizontal.first();
		var location = socialWarfare.isMobile() ? $(panel).data("float-mobile") : $(panel).data("float");
		var newPadding = (location == "bottom") ? socialWarfare.paddingBottom : socialWarfare.paddingTop;
		var paddingProp = "padding-" + location;

		if (location == 'off') {
			return;
		}

		//* Restore the padding to initial values.
		if (socialWarfare.staticPanelIsVisible()) {
			$(".nc_wrapper, .swp_floating_horizontal_wrapper").hide();


			if (socialWarfare.isMobile() && $("#wpadminbar").length) {
				$("#wpadminbar").css("top", 0);
			}
		}

		// Add some padding to the page so it fits nicely at the top or bottom.
		else {
			newPadding += 50;
			$(".nc_wrapper, .swp_floating_horizontal_wrapper").show();

			//* Compensate for the margin-top added to <html> by #wpadminbar.
			if (socialWarfare.isMobile() && location == 'top' && $("#wpadminbar").length) {
				$("#wpadminbar").css("top", panel.parent().height());
			}
		}

		//* Update padding to be either initial values, or to use padding for floatingHorizontal panels.
		$("body").css(paddingProp, newPadding);
	}


	/**
	 * This method is used to vertically center the floating buttons when they
	 * are positioned on the left or right of the screen.
	 *
	 * @since  3.4.0 | 18 OCT 2018 | Created
	 * @param  void
	 * @param  void All changes are made to the dom.
	 *
	 */
	socialWarfare.positionFloatSidePanel = function() {
		var panelHeight, windowHeight, offset;
		var sidePanel = socialWarfare.panels.floatingSide;


		/**
		 * If no such element exists, we obviously just need to bail out and
		 * not try to center anything.
		 *
		 */
		if (!sidePanel || !sidePanel.length) {
			return;
		}


		/**
		 * We don't need to center the side panel buttons if the position is set
		 * to top or bottom. This will isntead be directly controlled by the CSS
		 * that is associated with these classes.
		 *
		 */
		if( sidePanel.hasClass('swp_side_top') || sidePanel.hasClass('swp_side_bottom') ) {
			return;
		}


		/**
		 * We'll need the height of the panel itself and the height of the
		 * actual browser window in order to calculate how to center it.
		 *
		 */
		panelHeight = sidePanel.outerHeight();
		windowHeight = window.innerHeight;


		/**
		 * If for some reason the panel is actually taller than the window
		 * itself, just stick it to the top of the window and the bottom will
		 * just have to overflow past the bottom of the screen.
		 *
		 */
		if (panelHeight > windowHeight) {
			return sidePanel.css("top", 0);
		}


		/**
		 * Calculate the center position of panel and then apply the relevant
		 * CSS to the panel.
		 *
		 */
		offset = (windowHeight - panelHeight) / 2;
		sidePanel.css("top", offset);
	}


	/***************************************************************************
	 *
	 *
	 *    SECTION #4: PINTEREST IMAGE HOVER SAVE BUTTONS
	 *
	 *
	 ***************************************************************************/

	// Create a single instance of the save button and store it in socialWarfare.
		socialWarfare.createHoverSaveButton = function() {


	   /**
	    * This is a compatibility patch to make our plugin work nicely with the
	    * Thrive Architect plugin. This will do two things:
	    *
	    * 1. It will stop the Pinterest Save button from appearing on images
	    * when the post being loaded is inside of the Thrive Architect page
	    * builder/editor.
	    *
	    * 2. It will locate any old Save buttons that have been added previously
	    * that were then erroneously saved in the database. This way, whenever
	    * they edit a post, it will simultanously repair/remove the invalid
	    * markup that was stored in the database.
	    *
	    */
		if( $('.tve_editor_page').length ) {
			$('.sw-pinit-button').remove();
			$('.sw-pinit').each( function() {
				var inner_content = $('.sw-pinit').contents();
				$(this).replaceWith(inner_content);
			});
			return;
		}

		var button = $(document.createElement("a"));
		button.css("display: none");
		button.addClass("swp-hover-pin-button");
		button.text("Save");
		socialWarfare.hoverSaveButton = $(button);
		return button;
	}


	/**
	 * Find all images of the images that are in the content area by looking
	 * for the .swp-content-locator div which is an empty div that we add via
	 * the_content() hook just so that we can target it here. Then iterate
	 * through them and determine if we should add a Pinterest save button.
	 *
	 */
	socialWarfare.triggerImageListeners = function() {
		$(".swp-content-locator").parent().find("img").off('mouseenter', socialWarfare.renderPinterestSaveButton)
		$(".swp-content-locator").parent().find("img").on('mouseenter', socialWarfare.renderPinterestSaveButton)

		// We need to assign the hover callback to new images
		// loaded by ajax as the visitor scrolls through the page.
		setTimeout(socialWarfare.triggerImageListeners, 2000);
	}

	socialWarfare.getPinMedia = function( image ) {
		/**
		 * If the swpPinIt.image_source variable exists, it means that the user
		 * forces their custom Pinterest image instaed of the visitor's selection.
		 *
		 */
		if (isString(swpPinIt.image_source)) {
			return swpPinIt.image_source;
		}

		// Most images will have a src already defined, this gets top priority.
		if (isString(image.attr("src"))) {
			return image.attr("src");
		}

		// Otherise check common data-attributes for an image source.
		var dataSources = ['src', 'lazy-src', 'media'];
		var media = '';

		// Search for the first existing value and keep it if found.
		dataSources.some(function(maybeSource) {
			if (isString(image.data(maybeSource))) {
			  media = image.data(maybeSource);
			  return true;
			}
		})

		if (media == '') {
		  return;
		}

		// Use a jQuery image to guarantee we have an absolute path to the resource.
		// Pinterest throws an error when passed a relative path.
		var i = $("<img>");
		i.attr("src", media)
		return i.prop("src");
	}


	/**
	 * This is where we compute a description that will be used when the
	 * image is shared to Pinterest. In order of precedence, we will use the
	 * image's data-pin-description attribute, the custom Pinterest description
	 * for the post passed from the server, the image title, or the image
	 * description.
	 *
	 */
	socialWarfare.getPinDescription = function(image) {
    if (isString(image.data("pin-description"))) {
			return image.data("pin-description");
		}


		if (isString(swpPinIt.image_description)) {
			return swpPinIt.image_description;
		}

		// Try image Title or Alt text.
		if (isString(image.attr("title"))) {
			return image.attr("title");
		}

		if (isString(image.attr("alt"))) {
			return image.attr("alt");
		}

		// Default to the post title if nothing else is found.
		if (isString(swpPinIt.post_title)) {
			return swpPinIt.post_title;
		}
	}


	/**
	 * Adds the "Save" button to images when the option is enabled.
	 *
	 * This method will search and destroy any Pinterest save buttons that have
	 * been added by the Pinterest browser extension and then render the html
	 * needed to add our own proprietary Pinterest buttons on top of images.
	 *
	 * @param  void
	 * @return void
	 *
	 */
	socialWarfare.enablePinterestSaveButtons = function() {
		  /**
		   * Search and Destroy: This will find any Pinterest buttons that were
		   * added via their browser extension and then destroy them so that only
		   * ours are on the page.
		   *
		   */
		  jQuery('img').on('mouseenter', function() {
				var pinterestBrowserButtons = socialWarfare.findPinterestBrowserSaveButtons();
				if (typeof pinterestBrowserButtons != 'undefined' && pinterestBrowserButtons) {
					  socialWarfare.removePinterestBrowserSaveButtons(pinterestBrowserButtons);
				}
		  });
	}


  socialWarfare.toggleHoverSaveDisplay = function(image) {
	  var top = image.offset().top;
	  var left = image.offset().left;
	  var vMargin = 15;
	  var hMargin = 15;
	  var button_size = swpPinIt.button_size || 1;
	  var buttonHeight = 24;
	  var buttonWidth = 120;
	  // Known height from CSS is 34 px.
	  // Known width  from CSS is 120 px.

	   switch (swpPinIt.vLocation) {
		 case "top" :
			 top += vMargin;
			 break;

		 case "middle" :
			 var offset = image.height() / 2 - (vMargin / 2) - (buttonHeight / 2);
			 top += offset;
			 break;

		 case "bottom" :
			 top +=  image.height() - vMargin - buttonHeight;
			 break;
	   }


		switch (swpPinIt.hLocation) {
		  case "left" :
			  left += hMargin;
			  break;

		  case "center" :
			  var offset = image.width() / 2 - (hMargin / 2) - (buttonWidth / 2);
			  left += offset;
			  break;

		  case "right" :
			  left += image.width() - hMargin - buttonWidth;
			  break;
		}


	socialWarfare.hoverSaveButton.css({
		"top": top,
		"left": left,
		"transform": "scale(" + button_size + ")",
		"transform-origin": swpPinIt.vLocation + ' ' + swpPinIt.hLocation,
	});


	  // Entering the button from the image triggers mouseleave and mouseenter.
	  // Keep the button where it would otherwise disappear due to a mouseleave.
	  image.on("mouseleave", function(event) {

		  if (event.relatedTarget != null && event.relatedTarget.className == 'swp-hover-pin-button') {
			return;
		  }
		  $(".swp-hover-pin-button").remove();
	  });

	  $(document.body).append(socialWarfare.hoverSaveButton);
  }


	/**
	* This function renders the HTML needed to print the save buttons on the images.
	*
	* @param  void
	* @since  void
	*
	*/
	socialWarfare.renderPinterestSaveButton = function(event) {
	  if (event.relatedTarget && event.relatedTarget.className == 'swp-hover-pin-button') {
		return;
	  }

	  if ($(".swp-hover-pin-button").length > 0) {
		  return;
	  }

	  var image = $(event.target);
		  /**
		   * This disables the Pinterest save buttons on images that are anchors/links
		   * if the user has them disabled on them in the options page. So if this
		   * image is a link, we just bail out.
		   *
		   */
		  if (typeof swpPinIt.disableOnAnchors != undefined && swpPinIt.disableOnAnchors) {
				if (image.parents().filter("a").length) {
					  return;
				}
		  }

		  /**
		   * In the option page, the user can set a minimum width and a minimum
		   * height. Anything that isn't as large as these image dimensions will
		   * be skipped. This is a JS variable that is generated and output by
		   * the server.
		   *
		   */
		  if (image.outerHeight() < swpPinIt.minHeight || image.outerWidth() < swpPinIt.minWidth) {
				return;
		  }

		  /**
		   * We offer users the option to manually opt any image out of having a
		   * Pinterest save button on it by simply adding either the no_pin class
		   * or the no-pin class. There is also a checkbox in the media uploader
		   * that when checked will add one of these classes. If this image has
		   * one, skip it.
		   *
		   */
		  if (image.hasClass('no_pin') || image.hasClass('no-pin')) {
				return;
		  }

	  socialWarfare.toggleHoverSaveDisplay(image);

	  var description = socialWarfare.getPinDescription(image);
	  var media = socialWarfare.getPinMedia(image);
	  var shareLink = 'http://pinterest.com/pin/create/bookmarklet/?media=' + encodeURI(media) + '&url=' + encodeURI(document.URL) + '&is_video=false' + '&description=' + encodeURIComponent(description);

	  function openPinterestDialogue(event) {
      var offsetLeft = ($(window).width() - 775) / 2;
      var offsetTop = ($(window).height() - 550) / 2;
      var position = ',top=' + offsetTop + ',left=' + offsetLeft;

		  window.open(shareLink, 'Pinterest', 'width=775,height=550,status=0,toolbar=0,menubar=0,location=1,scrollbars=1' + position);
		  socialWarfare.trackClick('pin_image');
		  $(".swp-hover-pin-button").remove();
	  }

	  $(".swp-hover-pin-button").on("click", openPinterestDialogue);
	  // The elemnt and its event handlers are removed in toggleHoverSaveDisplay().
	}


	/**
	 * Looks for a "Save" button created by Pinterest addons.
	 *
	 * @param  void
	 * @return HTMLNode if the Pinterest button is found, else NULL.
	 *
	 */
	socialWarfare.findPinterestBrowserSaveButtons = function() {
		var pinterestRed, pinterestRed2019, pinterestZIndex, pinterestBackgroundSize, button, style;

		//* Known constants used by Pinterest.
		pinterestRed = "rgb(189, 8, 28)";
		pinterestRed2019 = "rgb(230, 0, 35)";
		pinterestZIndex = "8675309";
		pinterestBackgroundSize = "14px 14px";
		button = null;

		//* The Pinterest button is a <span/>, so check each span for a match.
		document.querySelectorAll("span").forEach(function(element, index) {
			style = window.getComputedStyle(element);

			if (style.backgroundColor == pinterestRed || style.backgroundColor == pinterestRed2019) {
				if (style.backgroundSize == pinterestBackgroundSize && style.zIndex == pinterestZIndex) {
					button = element;
				}
			}
		});

		return button;
	}


	/**
	 * Removes the "save" button created by Pinterest Browser Extension.
	 *
	 */
	socialWarfare.removePinterestBrowserSaveButtons = function(button) {
		var pinterestSquare, style, size;
		pinterestSquare = button.nextSibling;

		//* The sibling to the Pinterest button is always a span.
		if ( pinterestSquare != undefined && pinterestSquare.nodeName == 'SPAN') {
			style = window.getComputedStyle(pinterestSquare);
			size = "24px";

			//* If the sibling is indeed the correct Pinterest sibling, destory it all.
			if (style.width.indexOf(size) === 0 && style.height.indexOf(size) === 0) {
				pinterestSquare.remove()
			}
		}

		button.remove();
	}



		/***************************************************************************
		 *
		 *
		 *    SECTION #5: FACEBOOK SHARE COUNT FUNCTIONS
		 *
		 *
		 ***************************************************************************/


		/**
		 * Makes external requsts to fetch Facebook share counts. We fetch Facebook
		 * share counts via the frontened Javascript because their API has harsh
		 * rate limits that are IP Address based. So it's very easy for a website to
		 * hit those limits and recieve temporary bans from accessing the share count
		 * data. By using the front end, the IP Addresses are distributed to users,
		 * are therefore spread out, and don't hit the rate limits.
		 *
		 * @since  4.3.0 | 17 AUG 2023 | Updated Graph API endpoint to v17.0
		 * @since  4.4.6 | 17 JAN 2024 | Updated Graph API endpoint to v18.0
		 * @param  void
		 * @return void
		 *
		 */
		socialWarfare.fetchFacebookShares = function() {

			// Compile the API links
			var url1 = 'https://graph.facebook.com/v18.0/?fields=og_object{engagement}&id=' + swp_post_url;
			var url2 = swp_post_recovery_url ? 'https://graph.facebook.com/v18.0/?fields=og_object{engagement}&id=' + swp_post_recovery_url : '';

			// Record the tested URL's
			console.log('Facebook Share API: ' + url1 );
			console.log('Facebook Share API (recovery): ' + url2);

			// Use this to ensure that we wait until the API requests are done.
			$.when( $.get( url1 ), $.get( url2 ) )
			.then(function(response1, response2) {
				var shares, shares1, shares2, data;

				// Parse the shares and add them up into a running total.
				shares1 = socialWarfare.parseFacebookShares(response1[0]);
				shares2 = 0;
				if (swp_post_recovery_url) {
					shares2 = socialWarfare.parseFacebookShares(response2[0]);
				}

				// This will eliminate adding together duplicate data.
				shares = shares1;
				if( shares1 !== shares2 ) {
					shares = shares1 + shares2;
				}

				// Compile the data and send out the AJAX request to store the count.
				var data   = {
					action: 'swp_facebook_shares_update',
					post_id: swp_post_id,
					share_counts: shares
				};
				$.post(swp_admin_ajax, data, function(response) {
					console.log(response);
				});

			});
		}


		/**
		 * Sums the share data from a facebook API response. This is a utility
		 * function used by socialWarfare.fetchFacebookShares to allow easy access
		 * to parsing out the JSON response that we got from Facebook's API and
		 * converting it into an integer that reflects the tally of all activity
		 * on the URl in question including like, comments, and shares.
		 *
		 * @param  object response The API response received from Facebook.
		 * @return number The total shares summed from the request, or 0.
		 *
		 */
		socialWarfare.parseFacebookShares = function(response) {

			if ('undefined' === typeof response.og_object) {
				console.log('Facebook Shares: 0');
				return 0;
			}
			console.log('Facebook Shares: ' + response.og_object.engagement.count);
			return parseInt(response.og_object.engagement.count);

		}



	/***************************************************************************
	 *
	 *
	 *    SECTION #6: UTILITY/HELPER FUNCTIONS
	 *
	 *
	 ***************************************************************************/


	/**
	 * The throttle function is used to control how often an event can be fired.
	 * We use this exclusively to control how often scroll events go off. In some
	 * cases, the scroll event which controls when the floating buttons appear
	 * or disappear, was firing so often on scroll that the floating buttons were
	 * rapidly flickering in and out of view. This solves that.
	 *
	 * @param  integer   delay    How often in ms to allow the event to fire.
	 * @param  function  callback The function to run if the timeout period is expired.
	 * @return function           The callback function.
	 *
	 */
	// socialWarfare.throttle = function(delay, callback) {
	// 	var timeoutID = 0;
	// 	// The previous time `callback` was called.
	// 	var lastExec  = 0;
	//
	// 	function wrapper() {
	// 		var wrap    = this;
	// 		var elapsed = +new Date() - lastExec;
	// 		var args    = arguments;
	//
	// 		function exec() {
	// 			lastExec = +new Date();
	// 			callback.apply(wrap, args);
	// 		}
	//
	// 		function clear() {
	// 			timeoutID = 0;
	// 			lastExec = 0;
	// 		}
	//
	// 		timeoutID && clearTimeout(timeoutID);
	//
	// 		if (elapsed > delay) {
	// 			exec();
	// 		} else {
	// 			timeoutID = setTimeout(exec, delay - elapsed);
	// 		}
	// 	}
	//
	// 	if (socialWarfare.guid) {
	// 		wrapper.guid = callback.guid = callback.guid || socialWarfareguid++;
	// 	}
	//
	// 	console.log(wrapper)
	//
	// 	return wrapper;
	// };



	/**
	 * A simple wrapper for easily triggering DOM events. This will allow us to
	 * fire off our own custom events that our addons can then bind to in order
	 * to run their own functions in sequence with ours here.
	 *
	 * @param  string event The name of the event to trigger.
	 * @return void
	 *
	 */
	socialWarfare.trigger = function(event) {
		$(window).trigger($.Event(event));
	}


	/**
	 * Fire an event for Google Analytics and GTM.
	 *
	 * @since  2.4.0 | 18 OCT 2018 | Created
	 * @param  string event A string identifying the button being clicked.
	 * @return void
	 *
	 */
	socialWarfare.trackClick = function(event) {


		/**
		 * If click tracking has been enabled in the user settings, we'll
		 * need to send the event via Googel Analytics. The swpClickTracking
		 * variable will be dynamically generated via PHP and output in the
		 * footer of the page.
		 *
		 */
		if (true === swpClickTracking) {


			/**
			 * If Google Analytics is present on the page, we'll send the
			 * event via their object and methods.
			 *
			 */
			if ('function' == typeof ga) {
				ga('send', 'event', 'social_media', 'swp_' + event + '_share');
			}


			/**
			 * If Google Tag Manager is present on the page, we'll send the
			 * event via their object and methods.
			 *
			 */
			if ('object' == typeof dataLayer) {
				dataLayer.push({ 'event': 'swp_' + event + '_share' });
			}
		}
	}


	/**
	 * Checks to see if we have a buttons panel. If so, forces a re-run of the
	 * handleButtonClicks callback.
	 *
	 * @param  number count The current iteration of the loop cycle.
	 * @param  number limit The maximum number of iterations for the loop cycle.
	 * @return void or function handleButtonClicks().
	 *
	 */
	socialWarfare.checkListeners = function(count, limit) {


		/**
		 * Once we've checked for the buttons panel a certain number of times,
		 * we're simply going to bail out and stop checking. Right now, it is
		 * set to run 5 times for a total of 10 seconds.
		 *
		 */
		if (count > limit) {
			return;
		}


		/**
		 * The primary reason we are doing this is to ensure that a set of
		 * buttons does indeed exist when the click bindings are created. So
		 * this looks for the buttons and check's for their existence. If we
		 * find them, we fire off the handleButtonClicks() function.
		 *
		 */
		var panel = $('.swp_social_panel');
		if (panel.length > 0 && panel.find('.swp_pinterest')) {
			socialWarfare.handleButtonClicks();
			return;
		}


		/**
		 * If we haven't found any buttons panel, then after 2 more seconds,
		 * we'll fire off this function again until the limit has been reached.
		 *
		 */
		setTimeout(function() {
			socialWarfare.checkListeners(++count, limit)
		}, 2000);
	}


	/**
	 * Stores the user-defined mobile breakpoint in the socialWarfare object. In
	 * other functions, if the width of the current browser is smaller than this
	 * breakpoint, we will switch over and use the mobile options for the buttons
	 * panels.
	 *
	 */
	socialWarfare.establishBreakpoint = function() {
		var panel = $('.swp_social_panel');
		socialWarfare.breakpoint = 1100;

		if (panel.length && panel.data('min-width') || panel.data('min-width') == 0) {
			socialWarfare.breakpoint = parseInt( panel.data('min-width') );
		}
	}


	/**
	 * Checks to see if the current viewport is within the defined mobile
	 * breakpoint. The user sets a width in the options page. Any window
	 * viewport that is not as wide as that width will trigger isMobile to
	 * return as true.
	 *
	 */
	socialWarfare.isMobile = function() {
		return $(window).width() < socialWarfare.breakpoint;
	}

	/**
	 * Load the plugin once the DOM has been loaded.
	 *
	 */
	$(document).ready(function() {

		// This is what fires up the entire plugin's JS functionality.
		socialWarfare.initPlugin();
		socialWarfare.panels.floatingSide.hide();


		/**
		 * On resize, we're going to purge and re-init the entirety of the
		 * socialWarfare functions. This will fully reset all of the floating
		 * buttons which will allow for a clean transition if the size change
		 * causes the isMobile() check to flip from true to false or vica versa.
		 *
		 */
		$(window).on('resize', socialWarfare.onWindowResize);

		if ('undefined' !== typeof swpPinIt && swpPinIt.enabled) {
			socialWarfare.enablePinterestSaveButtons();
		}
	});

	/**
	* This reactivates and creates new image hover pin buttons when a page has
	* been loaded via AJAX. The 'load' event is the proper event that theme and
	* plugin creators are supposed to use when the AJAX load is complete.
	*
	*/
   $(window).on('load', function() {

	   if ('undefined' !== typeof swpPinIt && swpPinIt.enabled) {
		   socialWarfare.enablePinterestSaveButtons();
	   }
	   window.clearCheckID = 0;
   });

})(this, jQuery);

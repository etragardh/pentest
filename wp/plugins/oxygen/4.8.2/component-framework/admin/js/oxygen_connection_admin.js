jQuery(document).ready(function($) {
	$('#oxygen-connection-generate-screenshots').prop('disabled', false);

	$('input#ct_connection_use_page').on('change', function(e) {
		if($(e.target).prop('checked')) {
			$('div#ct-connection-category').css('display', '');
		} else {
			$('div#ct-connection-category').css('display', 'none');
		}
	});

	$('select.ct_connection_block_category').on('change', function(e) {
		if($(this).val() == '') {
			$('#ct-connection-block-warning').show();
		} else {
			$('#ct-connection-block-warning').hide();
		}
	});

	$('button#oxygen-connection-generate-screenshots').bind('click', function(e) {
		
		e.preventDefault();
		
		var button = $('button#oxygen-connection-generate-screenshots');
		var placeholder = button.text();
		
		button.prop('disabled', true);
		button.text(button.attr('data-processing'));

		var data = {
			'action': 'oxygen_connection_screenshot'
		};

		var renderURL = $('#oxygen_vsb_screenshot_generate_url').val();

		if(renderURL && renderURL.length > 0) {
			data['renderURL'] = renderURL;
		}
		
		if(button.attr('data-postId')) {
			data['postId'] = button.attr('data-postId');
		}

		var nonce = $('#ct_connection_generate_screenshot_nonce');
		data[nonce.attr('name')] = nonce.attr('value');

		function requestScreenshot() {
			jQuery.post(ajaxurl, data, function(response) {
				
				if(response && typeof(response['componentIndex']) != 'undefined') {
					data['componentIndex'] = parseInt(response['componentIndex']);
					
					if(response['componentRepeat']) {
						data['componentRepeat'] = response['componentRepeat'];
					}

					requestScreenshot();
					return;
				}

				button.prop('disabled', false);
				button.text(placeholder);

				// $('#ct-connection-screenshot-updated span').text('not ');
				// $('#ct-connection-screenshot-updated').removeClass('ct-connection-notification-error');
				$('#ct-connection-screenshot-messages > li').css('display', 'none');
				$('#ct-connection-screenshot-messages > li:first').css('display', '');


				if(response && response['url'] && $('input#oxygen_vsb_site_screenshot').length > 0) {

					$('input#oxygen_vsb_site_screenshot').val(response['url']);
					tb_remove();

				} else if(response && response['screenshots'] && response['screenshots']['page'] && $('input#oxygen_vsb_site_screenshot').length > 0) {

					$('input#oxygen_vsb_site_screenshot').val(response['screenshots']['page']);

				}
				 else if((response && response['error'])) {
					if(response['errorMessages']) {
						alert(response['errorMessages'].join("\n"));
					} else {	
						alert('One or more screenshot(s) have failed to generate. Please try again.')
					}
					tb_remove();
				}
			}).fail(function(response) {
				
				// error
				button.prop('disabled', false);
				button.text(placeholder);
				alert('No screenshots have been generated. Please try again.');
				tb_remove();
			});
		}

		requestScreenshot();


	});

});
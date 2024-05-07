document.addEventListener('DOMContentLoaded', function (e) {
	var feedbackFormWrapper = jQuery('#bricks-feedback-form-wrapper')
	var deactivationUrl

	if (!feedbackFormWrapper) {
		return
	}

	document.addEventListener('click', function (e) {
		// Close feedback form
		if (e.target.classList.contains('close') && e.target.closest('#bricks-feedback-form-wrapper')) {
			e.target.closest('#bricks-feedback-form-wrapper').classList.remove('show')
		}

		// Activate theme other than Bricks
		if (e.target.classList.contains('activate')) {
			deactivationUrl = e.target.href

			// Don't show for Bricks Child Theme
			if (deactivationUrl.indexOf('stylesheet=bricks-child') !== -1) {
				return
			}

			e.preventDefault()

			feedbackFormWrapper.addClass('show')
		}
	})

	// Toggle deactivation reasons
	jQuery(document).on('click', '#bricks-feedback-form input[type=radio]', function (e) {
		switch (this.value) {
			case 'found_better_plugin':
				jQuery('.bricks_reason_found_better_plugin').addClass('show')
				jQuery('.bricks_reason_how_to_use').removeClass('show')
				jQuery('.bricks_reason_other').removeClass('show')
				break

			case 'how_to_use':
				jQuery('.bricks_reason_found_better_plugin').removeClass('show')
				jQuery('.bricks_reason_how_to_use').addClass('show')
				jQuery('.bricks_reason_other').removeClass('show')
				break

			case 'other':
				jQuery('.bricks_reason_how_to_use').removeClass('show')
				jQuery('.bricks_reason_found_better_plugin').removeClass('show')
				jQuery('.bricks_reason_other').addClass('show')
				break
		}
	})

	// Submit feedback form
	jQuery(document).on('submit', '#bricks-feedback-form', function (e) {
		e.preventDefault()

		var formData = jQuery(this).serializeArray()
		var reason = ''
		var description = ''
		var referer = ''
		var version = ''

		formData.forEach(function (dataObj) {
			if (reason && description && referer && version) {
				return
			}

			if (!reason && dataObj.name === 'bricks_reason') {
				reason = dataObj.value
			}

			if (reason && dataObj.name === 'bricks_reason_' + reason) {
				description = dataObj.value
			}

			if (!referer && dataObj.name === 'referer') {
				referer = dataObj.value
			}

			if (!version && dataObj.name === 'version') {
				version = dataObj.value
			}
		})

		// Hide feedback form
		feedbackFormWrapper.removeClass('show')

		// Submit/skip feedback
		if (reason) {
			jQuery.ajax({
				method: 'POST',
				contentType: 'application/json',
				dataType: 'json',
				url: 'https://bricksbuilder.io/api/commerce/feedback/collect',
				data: JSON.stringify({
					reason: reason,
					description: description,
					referer: referer,
					version: version
				}),
				success: function (res) {
					if (deactivationUrl) {
						window.location.href = deactivationUrl
					} else {
						location.reload()
					}
				},
				error: function (res) {
					if (deactivationUrl) {
						window.location.href = deactivationUrl
					} else {
						location.reload()
					}
				}
			})
		}

		// Skip feedback
		else {
			if (deactivationUrl) {
				window.location.href = deactivationUrl
			} else {
				location.reload()
			}
		}
	})
})

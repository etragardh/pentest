/**
 * Bricks integration with the Rank Math plugin
 */
let bricksContentData = ''

function getBricksContentData() {
	return bricksContentData
}

function updateContentData() {
	jQuery.ajax({
		url: window.bricksRankMath.ajaxUrl,
		type: 'POST',
		data: {
			action: 'bricks_get_html_from_content',
			nonce: window.bricksRankMath.nonce,
			postId: window.bricksRankMath.postId
		},
		success: function (res) {
			if (res.data.html) {
				bricksContentData = res.data.html
				rankMathEditor.refresh('content')
			}
		},
		error: function (err) {
			console.error('Error updating content data:', err)
		}
	})
}

// To be used inside the builder when content is saved (needs a deeper integration)
function bricksRankMathAddContent(event) {
	let data = event.detail
	bricksContentData = data.content ? JSON.stringify(data.content) : ''
	rankMathEditor.refresh('content')
}

// Setup initial filter and content fetch on DOM load.
document.addEventListener('DOMContentLoaded', function () {
	if (!window.bricksRankMath || !window.bricksRankMath.renderWithBricks) {
		return
	}

	wp.hooks.addFilter('rank_math_content', 'bricks', getBricksContentData)
	updateContentData()
})

// Updated to handle content updates from the Bricks builder
document.addEventListener('bricksContentSaved', bricksRankMathAddContent)

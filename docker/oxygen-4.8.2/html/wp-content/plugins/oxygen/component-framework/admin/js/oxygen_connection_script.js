jQuery(document).ready(function($) {
	
	function recursiveHideandShow(item) {

		let parent = item.parent();

		if(parent.length > 0) {
			parent.css('display', '');
			item.siblings().css('display', 'none');
			item.css('display', '');
		}

		if(parent.parent().length > 0) {
			recursiveHideandShow(parent);
		}
	}

	$('body').prepend($('<div>').attr('id', 'oxy-screenshot-bg').css({background: '#ffffff', position: 'absolute', top: 0, left: 0, width: '100%', height: '100%', zIndex: -1}));
	
	
	if(window['oxygen_vsb_selectiveRenderingParams'] && window['oxygen_vsb_selectiveRenderingParams']['selector']) {
		
		var item = $('#'+window['oxygen_vsb_selectiveRenderingParams']['selector']);
		
		recursiveHideandShow(item);

		$('#oxy-screenshot-bg').css('display', '');
		$('body').css('min-height', '100px');

	}

});
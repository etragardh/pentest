jQuery(document).ready(function($){
	
	var originalButton = $('a.page-title-action');
	if(originalButton.length === 1) {
		var cloneButton = originalButton.clone();

		cloneButton.text('Add New Reusable Part').attr('href', ct_template_add_reusable_link.value);

		cloneButton.insertAfter(originalButton);
	}

});

	
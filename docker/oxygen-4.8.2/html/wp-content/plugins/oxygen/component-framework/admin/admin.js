jQuery(document).ready(function($) {

	// Init tabs to show
	var checked = $(".ct-template-anchor:checked").val();
	$("#ct_"+checked).addClass("ct-section-active");

	var checked = $(".ct-template-options-anchor:checked").val();
	$("#ct_"+checked).addClass("ct-section-active");

	// Switch template tabs on radio button click
	$(".ct-template-anchor").change( function(){
		
		var tab = $(this).val();

		$(".ct-template-section").removeClass("ct-section-active");
		$("#ct_"+tab).addClass("ct-section-active");
	});

	// Switch template options tabs on radio button click
	$(".ct-template-options-anchor").change( function(){
		
		var tab = $(this).val();

		$(".ct-template-options-section").removeClass("ct-section-active");
		$("#ct_"+tab).addClass("ct-section-active");
	});

	// Init taxonomies
	function switchTaxonomies() {
		var checked = $("#ct_use_template_taxonomies:checked").val();
		if ( checked ) {
			$(".ct-template-taxonomies").show("fast");
		}
		else {
			$(".ct-template-taxonomies").hide();
		}
	}
	switchTaxonomies();
	
	$("#ct_use_template_taxonomies").click( function(){
		switchTaxonomies();
	});


	// add taxonomies
	$(".ct-template-taxonomies").on("click", ".ct-add-taxonomy", function(){

		var placeholder = $("#ct-template-taxonomy-placeholder").html();

		$(".ct-template-taxonomies").append(placeholder);
	});

	// remove taxonomy
	$(".ct-template-taxonomies").on("click", ".ct-remove-taxonomy", function() {
		
		var taxonomy = $(this).parent(".ct-template-taxonomy");

		$(taxonomy).remove();
	});

	/**
	 * Show/hide builder shortcdes in Oxygen metabox for CPTs
	 */
	$("#ct-toggle-shortcodes").click(function() {
		$("#ct-builder-shortcodes").slideToggle("fast");
		$(this).toggleClass("ct-toggle-shortcodes-show");
	});

	$("#ct-toggle-json").click(function() {
		$("#ct-builder-json").slideToggle("fast");
		$(this).toggleClass("ct-toggle-json-show");
	});

    /**
     * Show/hide revision history in Oxygen metabox
     */
    $("#ct-toggle-revisions").click(function() {
        $("#ct-builder-revisions").slideToggle("fast");
        $(this).toggleClass("ct-toggle-revisions-show");
    });
	

	$('a#ct_create_custom_view_from').on('click', function(e) {
		e.preventDefault();
		var form = $(this).closest('form');
		form.append('<input type="hidden" name="ct_custom_view_on_create_copy" value="true" />');
		form.submit();
	//	$('input#ct_custom_view_on_create_copy').trigger('click');
	})

	$('a#ct_edit_inner_content').on('click', function(e) {
		e.preventDefault();
		var form = $(this).closest('form');
		form.append('<input type="hidden" name="ct_redirect_inner_content" value="true" />');
		form.submit();
	//	$('input#ct_custom_view_on_create_copy').trigger('click');
	})
	
	$('a#ct-edit-template-builder, a#ct-edit-template-builder-parent').on('click', function(e) {

		$.post( ajaxurl, { 
			action: 'set_oxygen_edit_post_lock_transient',
			post_id: $(this).data("current-post-id"),
			nonce: $(this).data("current-post-nonce") 
        } );
		
		var me = $(this);

		var parentSelector = $('select#ct_parent_template');
		
		if(parentSelector.length > 0) {
			var previousParent = parseInt($(this).attr('data-parent-template'));

			if(previousParent !== parseInt(parentSelector.val())) { // the parent Template has not changed
				
				e.preventDefault();
				var form = $(this).closest('form');
				
				if(me.attr('id') === 'ct-edit-template-builder-parent') {
					form.append('<input type="hidden" name="ct_redirect_to_template" value="'+parseInt(parentSelector.val())+'" />')
				} else {
					if(parentSelector.children('option:nth-child('+(parentSelector.prop('selectedIndex')+1)+')').attr('data-inner')) {
						form.append('<input type="hidden" name="ct_redirect_inner_content" value="true" />');
					}
					else {
						form.append('<input type="hidden" name="ct_redirect_to_builder" value="true" />');	
					}
				}
				
				if(wp.data) { // wp 5 way or the guttenberg way
					
					const {stores} = wp.data.use(function(){});
					
					var unsubscribe = stores['core/edit-post'].store.subscribe(function(e) { 
						if(stores['core/edit-post'].store.getState().metaBoxes.isSaving === false) {
							location.href = me.attr('href');
							unsubscribe();
						}
					});
 					// trigger the post update
					$('.editor-post-publish-button').trigger('click');
 				} else {
					form.submit();
				}
			}
		}
	})

	$('a#ct_delete_custom_view').on('click', function() {
		$('#ct_builder_shortcodes').val('');
		alert('You must save for changes to take effect. If you do not wish to delete, simply leave the page without saving.');
		/*var form = $(this).closest('form');
		form.append('<input type="hidden" name="ct_delete_custom_view" value="true" />');
		form.submit();*/
	});

	$("input.ct_render_post_using").on('change', function() {
		if($(this).val() === 'other_template') {
			$(".ct_template_option_panel:eq(0)").hide("fast");	
			$(".ct_template_option_panel:eq(1)").show("fast");
		} else {
			$(".ct_template_option_panel:eq(1)").hide("fast");	
			$(".ct_template_option_panel:eq(0)").show("fast");
		}
	});

	$("select#ct_parent_template").on('change', function(e) {

		var link = $('a#ct-edit-template-builder'),
			linkParent = $('a#ct-edit-template-builder-parent');

		if (link.length==0) {
			link = $('a.oxygen-open-anyway-link');
		}
		if (linkParent.length==0) {
			linkParent = $('a.oxygen-open-anyway-link-parent');
		}
		
		link.attr('href', link.attr('href').replace('&ct_inner=true', ''));
		linkParent.attr('href', linkParent.attr('data-site-url')+'?p='+(parseInt($(this).val()) !== 0? $(this).val():$(this).children('option:selected').attr('data-template-id'))+'&ct_builder=true');

		if($(this).children('option:nth-child('+($(this).prop('selectedIndex')+1)+')').attr('data-parent')) {
			
			linkParent.attr('href', linkParent.attr('href')+'&ct_inner=true');

		}

		if($(this).children('option:nth-child('+($(this).prop('selectedIndex')+1)+')').attr('data-inner')) {
			// if it is not a ct_template, then remove css display: none from the 'Edit with Oxygen' button
			if(!$('body').hasClass('post-type-ct_template')) {

				$('a#ct-edit-template-builder').css('display', '');
				$('.oxygen-open-anyway-link-post').css('display', '');

				$('div#ct-edit-template-builder-parent-wrap').css('display', 'none');
			}

			$('a#ct-edit-template-builder').attr('href', $('a#ct-edit-template-builder').attr('href')+'&ct_inner=true');

		}
		else {

			// if it is not a ct_template, then hide the 'Edit with Oxygen' button
			if(!$('body').hasClass('post-type-ct_template')) {

				$('a#ct-edit-template-builder').css('display', 'none');
				$('.oxygen-open-anyway-link-post').css('display', 'none');
				$('div#ct-edit-template-builder-parent-wrap').css('display', '');

				if(parseInt($(this).val()) === -1 ||
					parseInt($(this).val()) === 0 && $(this).children('option:nth-child('+($(this).prop('selectedIndex')+1)+')').text().trim() == '') {
					$('a#ct-edit-template-builder').css('display', '');
					$('.oxygen-open-anyway-link-post').css('display', '');
					$('div#ct-edit-template-builder-parent-wrap').css('display', 'none');
				}

			}
		}

	});

	$("input.ct_use_inner_content").on('change', function() {
		if($(this).val() === 'layout') {
			$(".ct-user-inner-content-layout").css("display", "inline-block");
		} else {
			$(".ct-user-inner-content-layout").css("display", "none");	
		}
	});

	// hide edit with oxygen button until published
	var newPostURL = "post-new.php",
		publishedPostURL = "post.php";

	if ( document.location.href.indexOf(newPostURL) > 0 ) {

		jQuery('#ct-edit-template-builder').hide();
		jQuery('#oxygen-save-first-message').show();

		// if "publish" or "save draft" buttons clicked
		jQuery('body').on('click', 'button, input[type=submit]', showEditWithOxygenButton);

		function showEditWithOxygenButton() {
			var urlDetector = setInterval(function(){
				if ( document.location.href.indexOf(publishedPostURL) > 0 ) {
					
					jQuery('#ct-edit-template-builder').show();
					jQuery('#oxygen-save-first-message').hide();

					jQuery(".oxygen-edit-mode-button").insertBefore(".edit-post-header__toolbar").show()

					// unbind click event as we don't need it anymore
					jQuery('body').off('click', 'button, input[type=submit]', showEditWithOxygenButton);

					clearInterval(urlDetector)
				}
			}, 500); // timeout needed to take this out of the flow, so the location.href being updated by that time 
		}
	}

	// hide edit with oxygen button for templates after changes are made to the template settings
	var $tsform = jQuery('.post-type-ct_template form#post #ct_views_cpt :input'), originaltsform = $tsform.serialize();

	jQuery('.post-type-ct_template form#post #ct_views_cpt :input').on('change input', function() {
		if ($tsform.serialize() !== originaltsform) {
			jQuery('#ct-edit-template-builder').hide();
			jQuery('#oxygen-save-first-message').show();
		} else {
			jQuery('#ct-edit-template-builder').show();
			jQuery('#oxygen-save-first-message').hide();
		}
	});

	$(".oxygen-preview-revision").on( "click", function() {
		var previewUrl = "";
		if( $(this).data('template') && $('#ct_preview_revision_select').length != 0 && oxygenPreviewPostsList.length > 0 ) {
        	var parameter = $(this).data('parameter');
        	var revision = $(this).data('revision');
            $('#ct_preview_revision_select').data( 'parameter', parameter )
            $('#ct_preview_revision_select').data( 'revision', revision )
            previewUrl = oxygenPreviewPostsList[ parseInt($('#ct_preview_revision_select').val()) ].permalink;
            var separator = previewUrl.indexOf('?') != -1 ? '&' : '?';
            previewUrl += separator + parameter + '=' + revision;
		} else {
        	previewUrl = $(this).data('permalink');
            var separator = previewUrl.indexOf('?') != -1 ? '&' : '?';
        	previewUrl += separator + $(this).data('parameter') + '=' + $(this).data('revision');
		}
        window.open( previewUrl, '_blank' );

	} );

    if( typeof oxygenPreviewPostsList !== 'undefined' ) {
    	var select = $('#ct_preview_revision_select');
		if( oxygenPreviewPostsList.length == 0 ) {
			select.hide();
			$('#ct_preview_revision_select_label').hide();
		}
		for(var i = 0; i < oxygenPreviewPostsList.length; i++){
            var o = new Option(oxygenPreviewPostsList[i].title, i);
            select.append(o);
		}
	}

	// disable "Publish"/"Update" buttons when post edit locked by Oxygen
	if ( $("body").hasClass("oxygen-edit-post-locked-current") ) {
	
		// Before Gutenberg way
		$("#publishing-action .button-primary, #save-action .button").addClass('oxy-disable-admin-button');

		// Gutenberg way
		setTimeout(function(){
			$(".editor-post-publish-button").addClass('oxy-disable-admin-button');
		}, 500);
	}

	$("#oxygen-open-anyway-link").on("click", function(){
		return confirm('Editing your site with Oxygen in multiple locations at once can result in data loss. Are you sure you want to proceed?');
	});

	// Client Control Panel > Users

	// Add New User UI to set accees level
	$("#oxygen_user_access_add_user").on("click", (e) => {
		e.preventDefault();
		let newUserSelect = $("#oxygen_user_access_placeholder").clone()
		
		newUserSelect.find("select").attr("name")
		newUserSelect.removeAttr("id")
		newUserSelect.addClass("oxygen-user-access-settings-row")
		newUserSelect.appendTo( "#oxygen_user_access_table" )
	})

	// Update access level select to contain correct user ID when you choose the user
	$("#oxygen_user_access_table").on("change", ".oxygen_user_access_user_select", (e) => {
		let userID = e.target.value,
			parentRow = $(e.target).closest(".oxygen-user-access-settings-row"),
			accessLevelSelect 		= parentRow.find(".oxygen_user_access_level_select"),
			enableElementsCheckbox 	= parentRow.find(".oxygen_vsb_options_users_access_enable_elements"),
			enableElementsSelect 	= parentRow.find(".oxygen_user_access_enabled_elements"),
			advancedTabCheckbox 	= parentRow.find(".oxygen_vsb_options_users_access_advanced_tab"),
			dragNDropCheckbox 		= parentRow.find(".oxygen_vsb_options_users_access_drag_n_drop"),
			reusablePartsCheckbox 	= parentRow.find(".oxygen_vsb_options_users_access_reusable_parts"),
			designLibraryCheckbox 	= parentRow.find(".oxygen_vsb_options_users_access_design_library"),
			disableClassesCheckbox 	= parentRow.find(".oxygen_vsb_options_users_access_disable_classes")

		accessLevelSelect.attr("name",`oxygen_vsb_options_users_access_list[${userID}][]`)
		enableElementsCheckbox.attr("name",`oxygen_vsb_options_users_access_enable_elements[${userID}][]`)
		enableElementsSelect.attr("name",`oxygen_vsb_options_users_access_enabled_elements[${userID}][]`)
		advancedTabCheckbox.attr("name",`oxygen_vsb_options_users_access_advanced_tab[${userID}][]`)
		dragNDropCheckbox.attr("name",`oxygen_vsb_options_users_access_drag_n_drop[${userID}][]`)
		reusablePartsCheckbox.attr("name",`oxygen_vsb_options_users_access_reusable_parts[${userID}][]`)
		designLibraryCheckbox.attr("name",`oxygen_vsb_options_users_access_design_library[${userID}][]`)
		disableClassesCheckbox.attr("name",`oxygen_vsb_options_users_access_disable_classes[${userID}][]`)
	})

	// "Edit Only" sub options select dropdown
	$("#oxygen_user_access_table").on("change", ".oxygen_user_access_level_select", (e) => {
		showHideEditOnlyOptions(e.target)
		$(".oxygen_vsb_options_users_access_enable_elements").each((index, element) => {
			showHideElementsSelect(element)
		})
	})

	$(".oxygen_user_access_level_select").each((index, element) => {
		showHideEditOnlyOptions(element)
	})

	function showHideEditOnlyOptions(element) {
		let accessLevel = element.value,
			subOptions = $(element).closest(".oxygen-user-access-settings-row").find(".oxygen_user_access_edit_only_sub_options")

		if (accessLevel == "edit_only"){
			subOptions.show()
		}
		else {
			subOptions.hide()
		}
	}

	// Elements select dropdown
	if (typeof $(".oxygen_user_access_enabled_elements").select2 == "function") {
		$(".oxygen-user-access-settings-row .oxygen_user_access_enabled_elements").select2({
			placeholder: "Add Elements...",
		});
	}

	$("#oxygen_user_access_table").on("change", ".oxygen_vsb_options_users_access_enable_elements", (e) => {
		showHideElementsSelect(e.target)
	})

	$(".oxygen-user-access-settings-row .oxygen_vsb_options_users_access_enable_elements").each((index, element) => {
		showHideElementsSelect(element)
	})

	function showHideElementsSelect(element) {
		let isChecked = $(element).is(":checked"),
			parentRow = $(element).closest(".oxygen-user-access-settings-row"),
			elementsSelect = parentRow.find(".oxygen_user_access_enabled_elements"),
			elementsSelect2 = parentRow.find(".select2"),
			accessLevelSelect = parentRow.find(".oxygen_user_access_level_select"),
			accessLevelOption = accessLevelSelect.find(":selected"),
			accessLevel = accessLevelOption[0] ? accessLevelOption[0].value : false
		
		if (!elementsSelect2.length) {
			parentRow.find(".oxygen_user_access_enabled_elements").select2({
				placeholder: "Add Elements...",
			});
			elementsSelect2 = parentRow.find(".select2");
		}
		
		if (isChecked && accessLevel == "edit_only") {
			elementsSelect.show()
			elementsSelect2.show()
		}
		else {
			elementsSelect.hide()
			elementsSelect2.hide()
		}
	}

	// Remove User
	$("#oxygen_user_access_table").on("click", ".oxygen_user_access_remove_user", (e) => {
		e.preventDefault();
		let parentRow = $(e.target).closest(".oxygen-user-access-settings-row")
		parentRow.remove()
	})

	// Edit with Oxygen alternative button for Edit Mode users
	if ( document.location.href.indexOf(publishedPostURL) > 0 ) {
		setTimeout(() => {
			$(".oxygen-edit-mode-button").insertBefore(".edit-post-header__toolbar").show()
		}, 1000);
		$(".oxygen-edit-mode-button-non-gutenberg").insertAfter(".wp-heading-inline").css("display","inline-block")
	}

	// Role based controls
	$("#oxygen_vsb_access_role_settings").on("change", ".oxygen_vsb_access_role_select", (e) => {
		showHideEditOnlyRoleOptions(e.target)
	})

	$(".oxygen_vsb_access_role_select").each((index, element) => {
		showHideEditOnlyRoleOptions(element)
	})

	function showHideEditOnlyRoleOptions(element) {
		let accessLevel = element.value,
			subOptions = $(element).closest(".oxygen_role_access_settings_row").find(".oxygen_role_access_edit_only_sub_options")

		if (accessLevel == "edit_only"){
			subOptions.show()
		}
		else {
			subOptions.hide()
		}
	}

	if (typeof $(".oxygen_role_access_enabled_elements").select2 == "function") {
		$(".oxygen_role_access_settings_row .oxygen_user_access_enabled_elements").select2({
			placeholder: "Add Elements...",
		});
	}

	$("#oxygen_vsb_access_role_settings").on("change", ".oxygen_vsb_options_role_access_enable_elements", (e) => {
		showHideRoleElementsSelect(e.target)
	})

	$(".oxygen_role_access_settings_row .oxygen_vsb_options_role_access_enable_elements").each((index, element) => {
		showHideRoleElementsSelect(element)
	})

	function showHideRoleElementsSelect(element){

		let isChecked = $(element).is(":checked"),
			parentRow = $(element).closest(".oxygen_role_access_settings_row"),
			elementsSelect = parentRow.find(".oxygen_role_access_enabled_elements"),
			elementsSelect2 = parentRow.find(".select2"),
			accessLevelSelect = parentRow.find(".oxygen_vsb_access_role_select"),
			accessLevelOption = accessLevelSelect.find(":selected"),
			accessLevel = accessLevelOption[0] ? accessLevelOption[0].value : false

			console.log(isChecked)
		
		if (!elementsSelect2.length) {
			parentRow.find(".oxygen_role_access_enabled_elements").select2({
				placeholder: "Add Elements...",
			});
			elementsSelect2 = parentRow.find(".select2");
		}
		
		if (isChecked && accessLevel == "edit_only") {
			elementsSelect.show()
			elementsSelect2.show()
		}
		else {
			elementsSelect.hide()
			elementsSelect2.hide()
		}
	}


	$("#delete-all-revisions").click(function(){
		
		var confirmation = $("#delete-all-revisions-confirmation").val();
		
		if (confirmation !== "delete") {
			alert("Type in 'delete' to proceed");
			return;
		}

		var data = {
			button: $(this),
			nonce:  $(this).data("revisions-nonce")
		}
		
		$(this).prop("disabled", true);
		
		deleteAllRevisions(data);
	})

	function deleteAllRevisions(data) {

		var responseOutputElement = $("#delete-revisions-result");
		if (responseOutputElement && responseOutputElement.html) {
			responseOutputElement.html("");
		}

		$.post( ajaxurl, { 
			action: 'delete_all_oxygen_revisions',
			nonce: data.nonce 
        }, function(response) {

			if (responseOutputElement && responseOutputElement.html) {
				responseOutputElement.html(response);
			}
			else {
				console.log(response)
			}

			data.button.prop("disabled", false);

		} );
	}

	$(".oxygen-delete-revision").click(function(e){

		if (!confirm("Are sure to delete revision?")) {
			e.preventDefault();
		}
		
	})

	$("#oxygen-delete-all-revisions").click(function(e){

		if (!confirm("Are sure to delete ALL posts revisions?")) {
			e.preventDefault();
		}
		
	})

});

<div class="oxygen-formatting-toolbar">
	<div class="oxygen-button-list"
		ng-if="isActiveActionTab('contentEditing')">
		
		<div class="oxygen-button-list-button"
			ng-edit-role='justifyleft' ct-edit-button title="<?php _e( 'Align Left', 'oxygen' ); ?>">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/icons_top-bar-text-align-left.svg">
		</div>

		<div class="oxygen-button-list-button"
			ng-edit-role='justifycenter' ct-edit-button title="<?php _e( 'Align Center', 'oxygen' ); ?>">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/icons_top-bar-text-align-center.svg">
		</div>

		<div class="oxygen-button-list-button"
			ng-edit-role='justifyright' ct-edit-button title="<?php _e( 'Align Right', 'oxygen' ); ?>">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/icons_top-bar-text-align-right.svg">
		</div>

		<div class="oxygen-button-list-button"
			ng-edit-role='justifyfull' ct-edit-button title="<?php _e( 'Justify', 'oxygen' ); ?>">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/icons_top-bar-text-align-justify.svg">
		</div>

		<div class="oxygen-button-list-button"
			ng-edit-role='bold' ct-edit-button title="<?php _e( 'Bold', 'oxygen' ); ?>">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/icons__bold-icon.svg">
		</div>

		<div class="oxygen-button-list-button"
			ng-edit-role='italic' ct-edit-button title="<?php _e( 'Italic', 'oxygen' ); ?>">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/icons_top-bar-text_style_italic_icon.svg">
		</div>

		<div class="oxygen-button-list-button"
			ng-edit-role='underline' ct-edit-button title="<?php _e( 'Underline', 'oxygen' ); ?>">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/icons_top-bar-text_style_underline_icon.svg">
		</div>

		<div class="oxygen-button-list-button"
			ng-edit-role='strikethrough ' ct-edit-button title="<?php _e( 'Strike', 'oxygen' ); ?>">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/icons_top-bar-text_style_strike_icon.svg">
		</div>

		<div class="oxygen-button-list-button"
			ng-edit-role='link' ct-edit-button title="<?php _e( 'Link', 'oxygen' ); ?>">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/icons__link-icon.svg">
		</div>

		<div class="oxygen-button-list-button"
			ng-edit-role='undo' ct-edit-button title="<?php _e( 'Undo', 'oxygen' ); ?>">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/icons__undo-icon.svg">
		</div>

		<div class="oxygen-button-list-button"
			ng-edit-role='redo' ct-edit-button title="<?php _e( 'Redo', 'oxygen' ); ?>">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/icons__redo-icon.svg">
		</div>

		<div class="oxygen-button-list-button oxygen-clear-format-button"
			ng-edit-role='removeFormat' ct-edit-button title="<?php _e( 'Remove Format', 'oxygen' ); ?>">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/icons__clearformat-icon.svg">
		</div>

		<div class="oxygen-button-list-button oxygen-wrap-span-button" title="<?php _e('Wrap with Span component', 'oxygen'); ?>"
			ng-show="iframeScope.component.active.name != 'ct_span'"
			ng-hide="builtinContentEditing"
			ng-mousedown="iframeScope.wrapWithSpan()">
			<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/icons__span-icon.svg">
		</div>

		<div class="oxygen-button-list-button oxygen-formatting-done" title="<?php _e('Done Editing', 'oxygen'); ?>"
			ng-mousedown="disableContentEdit()">
			<?php _e("Done", "oxygen"); ?>
		</div>

		<?php global $oxygen_meta_keys ?>

		<div class='oxygen-button-list-button oxygen-insert-data oxygen-toolbar-button' 
			title="<?php _e('Insert Data shortcode', 'oxygen'); ?>"
			ng-show="iframeScope.component.active.name != 'ct_span'"
			ng-hide="builtinContentEditing"
			ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.insertAtCursor">
			<img class="oxygen-button-two-images" src='<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/insert-data.svg' />
			<span><?php _e("Insert Data", "oxygen"); ?></span>
		</div>

	</div>
</div>
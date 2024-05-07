<script type="text/ng-template" id="styleSetMenu">
	<span class="ct-icon ct-delete-icon"
		ng-if="set.key !== 'Uncategorized Custom Selectors'"
		ng-click="$parent.deleteStyleSet(set.key)">
	</span>

</script>

<script type="text/ng-template" id="styleSelectorMenu">
	<span class="ct-icon ct-visible-icon"
	ng-click="iframeScope.highlightSelector(true,selector.key,$event)"
	title="<?php _e("Highlight selector", "component-theme"); ?>">
	</span>

	<span class="ct-icon ct-delete-icon"
		ng-click="iframeScope.deleteCustomSelector(selector.key,$event)">
	</span>

</script>
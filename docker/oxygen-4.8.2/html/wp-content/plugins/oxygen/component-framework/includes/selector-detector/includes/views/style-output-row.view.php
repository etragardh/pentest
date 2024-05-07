<div class="oxygen-control-row oxygen-control-row-bottom-bar"
	ng-show="!iframeScope.selectorDetector.mode&&(isSelectorComponent('ct_widget')||iframeScope.hasOxyDataInside())&&!isShowTab('oxy_posts_grid','templateCSS')&&!isShowTab('oxy_posts_grid','templatePHP')&&!isShowTab('oxy_posts_grid','count')&&!isShowTab('oxy_posts_grid','query')&&!isShowTab('oxy_posts_grid','postType')&&!isShowTab('oxy_posts_grid','filtering')&&!isShowTab('oxy_posts_grid','order')&&!isShowTab('commentsList', 'templateCSS')&&!isShowTab('commentsList', 'templatePHP')">
	<div class="oxygen-selector-detector-style-button"
		ng-click="enableSelectorDetectorMode()">
		<?php _e("Style Output", "oxygen"); ?>
	</div>
</div>
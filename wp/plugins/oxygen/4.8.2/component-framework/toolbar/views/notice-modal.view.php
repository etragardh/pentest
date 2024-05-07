<div id="ct-notice-modal"
	ng-class="{'ct-show-notice':iframeScope.noticeModalVisible}">
	<div class="ct-notice-modal-inner-wrap" ng-class="iframeScope.noticeClass">
		<div id="ct-notice-content"></div>
		<div class="ct-hide-notice-icon"
			ng-click="iframeScope.hideNoticeModal()"></div>
	</div>
</div>
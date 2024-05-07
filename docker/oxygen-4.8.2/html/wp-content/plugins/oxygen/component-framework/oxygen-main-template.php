<?php if ( defined("SHOW_CT_BUILDER") && oxygen_can_activate_builder_compression() ) :
	ob_start();
	remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );
endif; ?><!DOCTYPE html>
<html <?php language_attributes(); ?> <?php if ( defined("SHOW_CT_BUILDER") ) : ?>ng-app="CTFrontendBuilderUI"<?php endif; ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- WP_HEAD() START -->
<?php wp_head(); ?>
<!-- END OF WP_HEAD() -->
</head>
<?php
	$classes_list = isset($_REQUEST['ct_inner'])?'ct_inner':'';
	$oxygen_vsb_page_settings = ct_get_page_settings();
	if (isset($oxygen_vsb_page_settings['overlay-header-above'])&&$oxygen_vsb_page_settings['overlay-header-above']!=='never'&&$oxygen_vsb_page_settings['overlay-header-above']!=='') {
		$classes_list .= " oxy-overlay-header";
	}
	$classes_list .= " wp-embed-responsive";
?>
<body <?php body_class($classes_list); ?> <?php if ( defined("SHOW_CT_BUILDER") ) : ?>id="ct-controller-ui" ng-controller="ControllerUI"<?php endif; ?><?php do_action("oxygen_vsb_body_attr"); ?>>

<?php if ( !defined("SHOW_CT_BUILDER") ) wp_body_open(); ?>

<?php
	if ( defined("SHOW_CT_BUILDER") ) {
		$is_404 = false;

		$existingResponse = intval(http_response_code());

		if ($existingResponse == 404) { 
			$is_404 = true;
		}

		if ($is_404 === true) {
			?>
			<style>
			body.oxygen-builder-body {
				background: #272e35;
			}
					
			.oxy_permalink_warning_container {
				display: flex;
				align-items: center;
				justify-content: center;
				height: 100vh;
			}
					
			.oxy_permalink_warning {
				background-color: #272e35;
				color: white;
				margin-left: 150px;
				margin-right: auto;
				padding: 80px;
				-webkit-font-smoothing: subpixel-antialiased;
				display: flex;
				flex-direction: column;
				align-items: flex-start;
			}
					
			.oxy_permalink_warning h2 {
				margin-bottom: 40px;
				font-size: 36px;
				font-weight: var(--oxy-regular-font-weight);
				line-height: var(--oxy-big-line-height);
			}
					
			.oxy_permalink_warning h3 {
				font-weight: var(--oxy-regular-font-weight);
				font-size: 20;
				line-height: var(--oxy-big-line-height);
				margin-bottom: 30px;
			}
					
			.oxy_permalink_button {
				font-size: 20px;
				padding: 9px 16px;
				background: linear-gradient(180deg, #26A0F5 0%, #0C89E1 100%);
				box-shadow: 0 1px 2px 0 rgba(0,0,0,0.1);
				background-clip: padding-box;
				color: #fff;
				font-weight: var(--oxy-regular-font-weight);
				text-shadow: 1px 1px 1px rgba(0,0,0,0.3);
				display: inline-block;
				border-radius: 3px;
				text-decoration: none;
				color: white;
				margin-top: 20px;
				margin-bottom: 40px;
				padding: 25px 60px 25px 60px;
			}
				
		        .oxy_permalink_button:hover {
                                background: linear-gradient(180deg, #36B0FF 0%, #1C99F1 100%);
                        }
					
			</style>
	
			<div class="oxy_permalink_warning_container">
				<div class="oxy_permalink_warning">
	                		<h2> Oxygen encountered a 404 error while loading the builder. </h2>
	                		<h3>Please re-save your permalinks under Settings > Permalinks in the WordPress admin panel.</h3>
					<a href="<?php echo admin_url('options-permalink.php'); ?>" class="oxy_permalink_button">Go to Settings > Permalinks</a>
	            		</div>
			</div>
			<?php
			die();
		}
	}

?>

<?php if ( defined("SHOW_CT_BUILDER") ) : ?>
<script type="text/ng-template" id="ctDropDownTemplate">
	<div class="oxygen-select-box"
		ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, data.paramName)}">
		<div class="oxygen-select-box-current">{{data.pairs[iframeScope.getOption(data.paramName)]}}</div>
		<div class="oxygen-select-box-dropdown"></div>
	</div>
	<div class="oxygen-select-box-options">
		
		<div ng-repeat="(value, name) in data.pairs" class="oxygen-select-box-option" ng-click="iframeScope.setOptionModel(data.paramName, value)">
			{{name}}
		</div>
		
	</div>
</script>
<script type="text/ng-template" id="ctFontWeightTemplate">
	<div ng-attr-class="oxygen-control-wrapper{{data.className?' '+data.className:''}}" ng-attr-id='{{data.idName}}'>
		<div class="oxy-style-indicator"
			ng-class="{'oxygen-has-class-value':iframeScope.classHasOption(data.paramName)&&!IDHasOption(data.paramName),'oxygen-has-id-value':iframeScope.IDHasOption(data.paramName)}">
		</div>
		<label class='oxygen-control-label'><?php _e("Font Weight","oxygen"); ?></label>
		<div class='oxygen-control'>

			<div class="oxygen-select oxygen-select-box-wrapper" ng-include="'ctDropDownTemplate'" ng-init='data["pairs"]={"":"&nbsp;", "100":"100","200":"200","300":"300","400":"400","500":"500","600":"600","700":"700","800":"800","900":"900"}'>
			</div>
		</div>
	</div>
</script>
<script type="text/ng-template" id="ctFontFamilyTemplate">
	<label class='oxygen-control-label'><?php _e("Font Family","oxygen"); ?></label>
	<div class='oxygen-control'>
	
		<div class="oxygen-select oxygen-select-box-wrapper">
			<div class="oxygen-select-box"
				ng-class="{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, data.paramName)}">
				<div class="oxygen-select-box-current">{{iframeScope.getComponentFont(iframeScope.component.active.id, true, '', data.paramName)}}</div>
				<div class="oxygen-select-box-dropdown"></div>
			</div>
			<div class="oxygen-select-box-options">

				<div class="oxygen-select-box-option">
					<input type="text" value="" placeholder="<?php _e("Search...", "oxygen"); ?>" spellcheck="false"
						ng-model="iframeScope.fontsFilter"/>
				</div>
				<div class="oxygen-select-box-option"
					ng-click="iframeScope.setComponentFont(iframeScope.component.active.id, iframeScope.component.active.name, '', data.paramName);"
					title="<?php _e("Unset font", "oxygen"); ?>">
						<?php _e("Default", "oxygen"); ?>
				</div>
				<div class="oxygen-select-box-option"
					ng-repeat="(name,font) in iframeScope.globalSettings.fonts | filter:{font:iframeScope.fontsFilter}"
					ng-click="iframeScope.setComponentFont(iframeScope.component.active.id, iframeScope.component.active.name, ['global', name], data.paramName);"
					title="<?php _e("Apply global font", "oxygen"); ?>">
						{{name}} ({{font}})
				</div>
				<div class="oxygen-select-box-option"
					ng-repeat="name in ['Inherit'] | filter:iframeScope.fontsFilter"
					ng-click="iframeScope.setComponentFont(iframeScope.component.active.id, iframeScope.component.active.name, name, data.paramName);"
					title="<?php _e("Use parent element font", "oxygen"); ?>">
						Inherit
				</div>
				<div class="oxygen-select-box-option"
					ng-hide="iframeScope.globalFontExist(name)"
					ng-repeat="name in iframeScope.elegantCustomFonts | filter:iframeScope.fontsFilter | limitTo: 20"
					ng-click="iframeScope.setComponentFont(iframeScope.component.active.id, iframeScope.component.active.name, name, data.paramName);"
					title="<?php _e("Apply this font family", "oxygen"); ?>">
						{{name}}
				</div>
				<div class="oxygen-select-box-option"
					ng-hide="iframeScope.globalFontExist(font.name)"
					ng-repeat="font in iframeScope.typeKitFonts | filter:iframeScope.fontsFilter | limitTo: 20"
					ng-click="iframeScope.setComponentFont(iframeScope.component.active.id, iframeScope.component.active.name, font.slug, data.paramName);"
					title="<?php _e("Apply this font family", "oxygen"); ?>">
						{{font.name}}
				</div>
				<div class="oxygen-select-box-option"
					ng-hide="iframeScope.globalFontExist(font)"
					ng-repeat="font in iframeScope.webSafeFonts | filter:iframeScope.fontsFilter | limitTo: 20"
					ng-click="iframeScope.setComponentFont(iframeScope.component.active.id, iframeScope.component.active.name, font, data.paramName);"
					title="<?php _e("Apply this font family", "oxygen"); ?>">
						{{font}}
				</div>
				<div class="oxygen-select-box-option"
					ng-hide="iframeScope.globalFontExist(font.family)"
					ng-repeat="font in iframeScope.googleFontsList | filter:iframeScope.fontsFilter | limitTo: 20"
					ng-click="iframeScope.setComponentFont(iframeScope.component.active.id, iframeScope.component.active.name, font.family, data.paramName);"
					title="<?php _e('Apply this font family', 'oxygen'); ?>">
						{{font.family}}
				</div>

			</div>
			<!-- .oxygen-select-box-options -->
		</div>
		<!-- .oxygen-select.oxygen-select-box-wrapper -->
	</div>
</script>
<?php endif; ?>

	<?php do_action("ct_before_builder"); ?>
	<?php if ( defined("SHOW_CT_BUILDER") ) : ?>
    <div id="ct-ui-overlay"></div>
	<div id="ct-viewport-container" >
			<!-- Show/Hide Sidebar -->
			<div class="oxygen-hide-sidebar-wrapper">
				<div class="oxygen-hide-sidebar-button" ng-class="{active: showLeftSidebar}" ng-click="doHideLeftSidebar(true)">
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/hide-sidebar.svg" />
            	</div>
            	<div class="oxygen-show-sidebar-button" ng-class="{active: !showLeftSidebar, flashing: showButtonFlashing}" ng-click="doShowLeftSidebar(true)">
					<img src="<?php echo CT_FW_URI; ?>/toolbar/UI/oxygen-icons/toolbar-icons/show-sidebar.svg" />
            	</div>
			</div>
			<!-- End of Show/Hide Sidebar -->
		<iframe id="ct-artificial-viewport" data-src="<?php echo ct_get_current_url( "oxygen_iframe=true" ); ?>"></iframe>
		<div id="ct-viewport-ruller-wrap">
			<div id="ct-viewport-ruller">
				<label>0</label>
				<label>100</label>
				<label>200</label>
				<label>300</label>
				<label>400</label>
				<label>500</label>
				<label>600</label>
				<label>700</label>
				<label>800</label>
				<label>900</label>
				<label>1000</label>
				<label>1100</label>
				<label>1200</label>
				<label>1300</label>
				<label>1400</label>
				<label>1500</label>
				<label>1600</label>
				<label>1700</label>
				<label>1800</label>
				<label>1900</label>
				<label>2000</label>	
				<label>2100</label>	
				<label>2200</label>	
				<label>2300</label>	
				<label>2400</label>	
				<label>2500</label>	
				<label>2600</label>
				<label>2700</label>
				<label>2800</label>
				<label>2900</label>
			</div>
			<div id="ct-viewport-handle"></div>
		</div>
		<div id="oxygen-status-bar" ng-class="{'oxygen-status-bar-active':statusBarActive}">{{statusMessage}}</div>
	</div><!-- #ct-viewport-container -->
	<?php else: ?>
		<?php global $template_content; ?>
		<?php echo $template_content; ?>
	<?php endif; ?>
<!-- WP_FOOTER -->
<?php wp_footer(); ?>
<!-- /WP_FOOTER --> 
</body>
</html>
<?php
if ( defined("SHOW_CT_BUILDER") && oxygen_can_activate_builder_compression() ) {
    // Flush everything, compressed.
	ob_end_flush();
}
?>
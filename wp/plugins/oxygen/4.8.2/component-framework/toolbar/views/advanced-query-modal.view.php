<div ng-if="dialogForms['advancedquery']" id='ct-modal-if-conditions' class='ct-modal-advanced-query ct-global-conditions-add-modal ct-global-conditions-choose-operator oxygen-data-dialog'>
	
    <h1>
        <?php _e("Advanced Query", "oxygen"); ?>
        <svg class="oxygen-close-icon" ng-click="hideDialogWindow()"><use xlink:href="#oxy-icon-cross"></use></svg>
    </h1>



    <?php 
        include_once(CT_FW_PATH."/includes/advanced-query.php"); 
        Oxy_VSB_Advanced_Query::dialog();
    ?>
	
    <script type="text/javascript">
    	
    	// jQuery('.ct-modal-advanced-query').click(function() {
        //     // close
        //     jQuery(".ct-modal-advanced-query .oxygen-select").removeClass("oxygen-active-select");
        //     console.log('hit');
        // })

    	// jQuery('.ct-modal-advanced-query').on("click", ".oxygen-select", function(e) {
        //     $scope.toggleOxygenSelectBox(e, this);
        //     console.log('hit');
        // })
        
    </script>
</div>
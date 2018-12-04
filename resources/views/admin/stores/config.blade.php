@extends('admin.layouts.config_base')

@section('title',"Store Settings" )


{{-- settings ------------------------------------------------------------------------ --}}

@section('settings')

@include('admin.stores.config-settings');

@endsection

{{-- settings ------------------------------------------------------------------------ --}}


{{-- devices ------------------------------------------------------------------------- --}}

@section('devices')

@include('admin.stores.config-devices');

@endsection

{{-- ------------------------------------------------------------------------- devices --}}


{{-- market_place -------------------------------------------------------------------- --}}

@section('marketplace')

@include('admin.stores.config-marketplace');

@endsection

{{-- -------------------------------------------------------------------- market_place --}}


@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/bootstrap-table.min.css')) }}
    {{ Html::style(mix('assets/admin/css/bootstrap-select.css')) }}
    <style>
        
        .left   { text-align:left !important; }
        .center { text-align:center !important; }
        .right  { text-align:right !important; }
    
        .radio-bump { display:inline-table; width:100px; }
        .radio-bump-time { display:inline-table; width:120px; text-align:right; text-align:center; }
        
        .td-data { height:30px; }
        
        .a-back-mp { text-decoration:underline; margin-left:4px; color:#999; }
        
        /** devices ******************************************************************/
        #devices-table thead tr th div { font-weight:400; background:#eee; color:#333; text-align:center; }
        #devices-table tbody tr td { font-weight:200; color:#222; vertical-align:middle; }
        .fixed-table-toolbar .search input { width:280px; height:36px; text-align:center; font-size:16px; 
		      font-weight:200; color:#000; cursor:text; border:1px solid #5bc0de; border-radius:3px; }
        #devices-table .devices-td-config { vertical-align:middle; text-align:center; }
        .pagination-detail .pagination-info { font-weight:200; color:#888; }
        #devices-table .btn-icons { width:28px; height:22px; }
        /****************************************************************** devices **/
        
        /** switch ******************************************************************/
        /* The switch - the box around the slider */
        .switch { top:5px; position: relative; display: inline-block; width: 40px; height: 22px; }
        /* Hide default HTML checkbox */
        .switch input {display:none;}
        /* The slider */
        .switch .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; 
            background-color: #ccc; -webkit-transition: .4s; transition: .4s; }
        .switch .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 2px;
            bottom: 2px; background-color: white; -webkit-transition: .4s; transition: .4s; }
        .switch input:checked + .slider { background-color: #2196F3; }
        .switch input:focus + .slider { box-shadow: 0 0 1px #2196F3; }
        .switch input:checked + .slider:before { -webkit-transform: translateX(18px); 
            -ms-transform: translateX(18px); transform: translateX(18px); }
        /* Rounded sliders */
        .switch .slider.round { border-radius: 34px; }
        .switch .slider.round:before { border-radius: 50%; }
        /****************************************************************** switch **/
        
        /** market place buttons ****************************************************/
        #mp-buttons                                 { width:900px; margin: 0 auto; }
        #mp-buttons .mp-button                      { width:180px; height:160px; background:#fefefe; text-align:center; 
            white-space: nowrap; margin: 1em 0;
            border:1px solid #ccc; border-radius: 25px; box-shadow: 5px 5px 5px rgba(0, 0, 0, .3); }
        #mp-buttons .mp-button .mp-b-helper         { display: inline-block; height: 75%; vertical-align: middle; }
        #mp-buttons .mp-button #mp-b-footer         { height:25%; border-radius: 0px 0px 25px 25px;  }
        #mp-buttons .mp-button #mp-b-footer a span  { display:inline-table; height:38px; padding-top:5px; box-shadow: 0 -3px 3px -3px rgba(0, 0, 0, .3);
             font-family:Helvetica Neue; font-size:16px; font-weight:200; border:1px solid #ccc; color:#fff; opacity:0.6; }
        #mp-buttons .mp-button #mp-b-footer a:hover span { color:#666; opacity:0.9; }
        #mp-buttons .mp-button #mp-b-footer #more   { width:85px; border-radius: 0px 0px 0px 25px; background:#75b5f0; margin-left:1px;  }
        #mp-buttons .mp-button #mp-b-footer #add    { width:85px; border-radius: 0px 0px 25px 0px; background:#1ABB9C; }
        #mp-buttons .mp-button img                  { width:160px; vertical-align: middle; }
        /**************************************************** market place buttons **/
        
        .lbl-enable { min-height:40px; }
        
        /* errors */
        .parsley-required { color:red; }

    </style>
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/validation_config.js')) }}
    {{ Html::script(mix('assets/admin/js/bootstrap-table.min.js')) }}
    {{ Html::script(mix('assets/admin/js/bootstrap-select.min.js')) }}
    {{ Html::script(mix('assets/admin/js/jquery.mask.js')) }}
    {{ Html::script(mix('assets/admin/js/firebase-api.js')) }}
    <script>

    var token = { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }

    function timeConverter(UNIX_timestamp){
		function fillZero(n){ return n<10?"0"+n:n; }
        var a = new Date(UNIX_timestamp * 1000);
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var year = a.getFullYear();
        var month = months[a.getMonth()];
        var date = a.getDate();
        var hour = a.getHours();
        var min = a.getMinutes();
        var sec = a.getSeconds();
        var time = date + ' ' + month + ' ' + year + ' ' + fillZero(hour)  + ':' + fillZero(min) + ':' + fillZero(sec) ;
        return time;
    	}

    // remove device
    $(document).ready(function(){

        var $modal = $('#modalDeviceSettings');

        
        function loadExpeditors($value = null) {

        		var deviceGuid 		= $modal.find('#device-settings-device-guid').val();
            var deviceFunction = $modal.find("#device-settings-function").val();

            $modal.find("#device-settings-expeditor").prop('disabled', false);
    
            	if(deviceFunction == 'EXPEDITOR' || deviceFunction == 'BACKUP_EXPE') {
            		$modal.find("#device-settings-expeditor").html('').selectpicker('refresh');
            		$modal.find("#device-settings-expeditor").prop('disabled', true);
            		return
            	}
            
        		$.ajax({
			 	headers: token,
	            url: 'getExpeditors',
	            type: 'POST',
	            data: {
	            		storeGuid: "{{$store->store_guid}}",
	            		deviceGuid: deviceGuid
	            	},
	            success: function (expeditors) {
// 		            alert(expeditors.length)
		            $modal.find("#device-settings-expeditor").html('');
	            		for(var i=0; i<expeditors.length; i++) {
	            			$modal.find("#device-settings-expeditor").append('<option value="' + expeditors[i].id + '">' + expeditors[i].name + '</option>')
	            		}
	            		$modal.find("#device-settings-expeditor").selectpicker('refresh');
	            		if($value != null && $value != '') {
	            			$modal.find("#device-settings-expeditor").val($value.split(',')).selectpicker('refresh');
	            		}
	            }
            });
        }

        
        function loadParentsByFunction() {

        		var deviceGuid 		= $modal.find('#device-settings-device-guid').val();
            var deviceFunction 	= $modal.find("#device-settings-function").val();

        		$modal.find("#device-settings-parent-id").prop('disabled', false);
        	
            if(deviceFunction == 'EXPEDITOR' || deviceFunction == 'PREPARATION') {
            		$modal.find("#device-settings-parent-id").html('').selectpicker('refresh');
            		$modal.find("#device-settings-parent-id").prop('disabled', true);
                return
            }
            
        		$.ajax({
        		 	headers: token,
                url: 'getParentsByFunction',
                type: 'POST',
                data: {
                		storeGuid: "{{$store->store_guid}}",
                		deviceGuid: deviceGuid,
                		deviceFunction : deviceFunction
                	},
                success: function (parents) {
                		$modal.find("#device-settings-parent-id").html('');
                		for(var i=0; i<parents.length; i++) {
                			$modal.find("#device-settings-parent-id").append('<option value="' + parents[i].id + '">' + parents[i].name + '</option>')
                		}
        				$modal.find("#device-settings-parent-id").selectpicker('refresh');
                }
            });
        }


        function loadTransfers($value) {

        		var deviceGuid = $modal.find('#device-settings-device-guid').val();
        	
        		$.ajax({
			 	headers: token,
	            url: 'getTransfers',
	            type: 'POST',
	            data: {
	            		storeGuid: "{{$store->store_guid}}",
	            		deviceGuid: deviceGuid
	            	},
	            success: function (transfers) {
		            var transfersSelect = $modal.find("#device-settings-line-display-transfer-device-id").html('');
		            transfersSelect.append('<option value="0">Disabled</option>')
	            		for(var i=0; i<transfers.length; i++) {
	            			transfersSelect.append('<option value="' + transfers[i].id + '">' + transfers[i].name + '</option>')
	            		}
	            		transfersSelect.val($value).selectpicker('refresh');
	            }
            });
        }
		
        
        function getDeviceSettings(deviceGuid, deviceScreenId) {

        		$modal.find(".card-body").animate({ scrollTop: 0 });

            $.ajax({
			 	headers: token,
	            url: 'getDeviceSettings',
	            type: 'POST',
	            data: {
	            		deviceGuid: deviceGuid,
	            		deviceScreenId: deviceScreenId
	            	},
	            success: function (devices) {

		            if(devices !== undefined) {
			            
						$device = devices[0];
						$settingsLocal = devices["settings_local"][0];
						$settingsLineDisplay = devices["settings_line_display"][0]; // This will return an array with 4 indexes.

						// Hidden Fields
						$modal.find('#device-settings-store-guid').val($device.store_guid);
						$modal.find('#device-settings-device-guid').val($device.guid);
						$modal.find('#device-settings-device-screen-id').val($device.screen_id);
						
						$modal.find('#device-settings-name').val($device.name);
						$modal.find('#device-settings-id').val($device.id);
						$modal.find("#device-settings-function").val($device.function).selectpicker('refresh');

						// This must be loaded after "#device-settings-function" is loaded
						loadExpeditors($device.expeditor);
						loadParentsByFunction();

						$modal.find('#device-settings-host').val($device.xml_order);
						
						$modal.find('#device-settings-orders-columns').val($settingsLocal.display_orders_columns).selectpicker('refresh');
						$modal.find('#device-settings-sort-orders').val($settingsLocal.display_sort_orders).selectpicker('refresh');

						// Summary
						$modal.find('#device-settings-summary-enable').prop('checked', $settingsLocal.summary_enable);
						$modal.find('#device-settings-summary-type').val($settingsLocal.summary_type).selectpicker('refresh');

						// Order Status
						$modal.find('#device-settings-order-status-ontime').val($settingsLocal.display_order_status_ontime);
						$modal.find('#device-settings-order-status-almost').val($settingsLocal.display_order_status_almost);
						$modal.find('#device-settings-order-status-delayed').val($settingsLocal.display_order_status_delayed);

						// Line Display
						$modal.find('#device-settings-line-display-enable').prop('checked', $device.line_display);
						for(var $i = 0; $i < $settingsLineDisplay.length; $i++) {
							$modal.find("#device-settings-line-display-column-" + ($i+1) + "-text")
								.val($settingsLineDisplay[$i].column_name)
								.selectpicker('refresh');
							$modal.find("#device-settings-line-display-column-" + ($i+1) + "-percent")
								.val($settingsLineDisplay[$i].column_percent)
								.selectpicker('refresh');
						}
						loadTransfers($device.bump_transfer_device_id);
						lineDisplayDisable();

						// Order Header
						var topLeftDefault		= $settingsLocal.header_top_left != null ? $settingsLocal.header_top_left : "DESTINATION" ;
						var topRightDefault		= $settingsLocal.header_top_right != null ? $settingsLocal.header_top_right : "TABLE_NAME" ;
						var BottomLeftDefault	= $settingsLocal.header_bottom_left != null ? $settingsLocal.header_bottom_left : "ORDER_ID" ;
						var BottomRightDefault	= $settingsLocal.header_bottom_right != null ? $settingsLocal.header_bottom_right : "WAITING_TIME" ;

						$modal.find('#device-settings-order-header-top-left').val(topLeftDefault).selectpicker('refresh');
						$modal.find('#device-settings-order-header-top-right').val(topRightDefault).selectpicker('refresh');
						$modal.find('#device-settings-order-header-bottom-left').val(BottomLeftDefault).selectpicker('refresh');
						$modal.find('#device-settings-order-header-bottom-right').val(BottomRightDefault).selectpicker('refresh');
						
						// Anchor Dialog
						var anchorTimeValidNew = $settingsLocal.anchor_time_new != null && $settingsLocal.anchor_time_new != 0;
						var anchorTimeValidPri = $settingsLocal.anchor_time_prioritized != null && $settingsLocal.anchor_time_prioritized != 0;
						var anchorTimeValidDel = $settingsLocal.anchor_time_delayed != null && $settingsLocal.anchor_time_delayed != 0;
						var anchorTimeValidRea = $settingsLocal.anchor_time_ready != null && $settingsLocal.anchor_time_ready != 0;
						
						var anchorTimeNew 			= anchorTimeValidNew ? $settingsLocal.anchor_time_new : 15 ;
						var anchorTimePrioritized 	= anchorTimeValidPri ? $settingsLocal.anchor_time_prioritized : 15 ;
						var anchorTimeDelayed 	  	= anchorTimeValidDel ? $settingsLocal.anchor_time_delayed : 15 ;
						var anchorTimeReady			= anchorTimeValidRea ? $settingsLocal.anchor_time_ready : 15 ;
						
						$modal.find('#device-settings-anchor-seconds-new').val(anchorTimeNew).selectpicker('refresh');
						$modal.find('#device-settings-anchor-seconds-prioritized').val(anchorTimePrioritized).selectpicker('refresh');
						$modal.find('#device-settings-anchor-seconds-delayed').val(anchorTimeDelayed).selectpicker('refresh');
						$modal.find('#device-settings-anchor-seconds-ready').val(anchorTimeReady).selectpicker('refresh');

						$modal.find('#device-settings-anchor-enable-new').prop('checked', $settingsLocal.anchor_enable_new);
						$modal.find('#device-settings-anchor-enable-prioritized').prop('checked', $settingsLocal.anchor_enable_prioritized);
						$modal.find('#device-settings-anchor-enable-delayed').prop('checked', $settingsLocal.anchor_enable_delayed);
						$modal.find('#device-settings-anchor-enable-ready').prop('checked', $settingsLocal.anchor_enable_ready);

						// Printer
						$modal.find('#device-settings-printer-network-enable').prop('checked', $device.printer_ethernet_enable);
						$modal.find('#device-settings-printer-network-ip').val($device.printer_address);
						$modal.find('#device-settings-printer-network-port').val($device.printer_port);
						$modal.find('#device-settings-printer-network-new-enable').prop('checked', $device.printer_print_receives);
						$modal.find('#device-settings-printer-network-bump-enable').prop('checked', $device.printer_print_bumps);

						// On Load Handle Device Settings Features Slide Up/Down
						$modal.find('.device-settings-feature-title').each(function(){

							var $title = $(this);
							var $enable = $title.find('.device-settings-feature-enable');
							var $arrows = $title.find('.device-settings-arrow');
							
							var id = $title.attr('id');
							var $config = $('#' + id + "-config");

							$config.hide();
							
							$arrows.find('i.fa-angle-up').hide();
							$arrows.find('i.fa-angle-down').show();

							// Hide arrows for feature config
				 			if($enable.prop("checked") == false) { // checked can be undefined = true
				 				$arrows.hide();
				 			}

				        });

		            }
	            }
            });
        }


         // -- Handle Device Settings Features Slide Up/Down -------------------------------------------------- //
        $modal.find('.device-settings-feature-title').each(function(){
    
        		var $title = $(this);
        		var $enable = $title.find('.device-settings-feature-enable');
        		var $arrows = $title.find('.device-settings-arrow');
        		
        		var id = $title.attr('id');
        		var $config = $('#' + id + "-config");
        
        		function configSlideOpen() {
        			closeAllConfigs();
        			
        			$arrows.find('i.fa-angle-down').hide();
        			$arrows.find('i.fa-angle-up').fadeIn();
        			$config.slideDown();
        
        			var scrollTo = parseInt($title.attr('scroll-to'));
        
        			$modal.find(".card-body").animate({ scrollTop: scrollTo });
        		}
        
        		function configSlideClose() {
        			$arrows.find('i.fa-angle-up').hide();
        			$arrows.find('i.fa-angle-down').fadeIn();
        			$config.slideUp();
        		}
        		
        		$arrows.find('i').click(function(e){
        			e.preventDefault();
        			if($(this).hasClass('fa-angle-down')) {
        				configSlideOpen();
        			} else {
        				configSlideClose();
        			}
        		});
        
        		$enable.click(function() {
        			if($(this).prop("checked")) {
        				$arrows.fadeIn();
        				configSlideOpen();
        			} else {
        				configSlideClose();
        				$arrows.hide();
        			}
        		});
    
        });
     	// -------------------------------------------------- Handle Device Settings Features Slide Up/Down -- //
    
     	
     	function closeAllConfigs() {
            	$('.device-settings-feature-title').each(function(){
            		$(this).find('i.fa-angle-up').hide();
            		$(this).find('i.fa-angle-down').fadeIn();
            		$('#' + $(this).attr('id') + '-config').slideUp();
            	});
        	}
    
    
        	function lineDisplayDisable() {
        		var deviceFunction = $modal.find("#device-settings-function").val();
        		var $enable = $modal.find('#device-settings-line-display-enable');
        		if(deviceFunction == 'EXPEDITOR' || deviceFunction == 'BACKUP_EXPE') {
        			// Disabled Line Display and Transfer
                		$enable.prop('checked', false);
                		$modal.find('#device-settings-line-display-transfer-device-id').val(0).selectpicker('refresh');
                		// Hide divs
                		$modal.find('.device-settings-line-display-hide').hide('slow');
                		$modal.find('.device-settings-line-display-text').fadeTo("slow",0.3);
        		} else {
        			// Show divs
                		$modal.find('.device-settings-line-display-hide').show('slow');
                		$modal.find('.device-settings-line-display-text').fadeTo("slow",1);
        		}
        		if($enable.prop("checked") == false) { // checked can be undefined = true
        			$modal.find('.device-settings-line-display-arrow').hide();
        		} else {
        			$modal.find('.device-settings-line-display-arrow').show('slow');
        		}
        		closeAllConfigs();
        	}
    
    
        $modal.find("#device-settings-function").change(function(){
    			loadExpeditors();
        		loadParentsByFunction();
        		lineDisplayDisable();
        });
    
        
        $modal.find(".num-99999").keyup(function () {
            	if(this.value > 99999) {
            		this.value = this.value.substring(0,5);
            	} else {
            		this.value = this.value.replace(/[^0-9\.]/g,'');
            	}
        });
    
            
        $('#device-settings-printer-network-ip').mask('099.099.099.099');

        // Save Device Settings
        $modal.find('#device-settings-save').click(function(){
        		
			var formData = $modal.find('#device-settings-form').serializeArray();
		    var dataObj = {};
        		$(formData).each(function(i, field){
        		  dataObj[field.name] = field.value;
        		});

    			// Expeditors
        		var expeditors = $('#device-settings-expeditor option:selected');
            	var expeditorsSelected = [];
            	$(expeditors).each(function(index, expeditor){
            		expeditorsSelected.push([$(this).val()]);
            	});
    			dataObj['device-settings-expeditor'] = expeditorsSelected.length == 0 ? "" : expeditorsSelected.join() ;

    			// Anchor
    			dataObj['device-settings-anchor-enable-new'] 		= $('#device-settings-anchor-enable-new').prop("checked") ? 1 : 0;
    			dataObj['device-settings-anchor-enable-prioritized']	= $('#device-settings-anchor-enable-prioritized').prop("checked") ? 1 : 0;
    			dataObj['device-settings-anchor-enable-delayed']		= $('#device-settings-anchor-enable-delayed').prop("checked") ? 1 : 0;
    			dataObj['device-settings-anchor-enable-ready']		= $('#device-settings-anchor-enable-ready').prop("checked") ? 1 : 0;

    			// Summary
    			dataObj['device-settings-summary-enable'] = $('#device-settings-summary-enable').prop("checked") ? 1 : 0;

    			// Line Display
    			dataObj['device-settings-line-display-enable'] = $('#device-settings-line-display-enable').prop("checked") ? 1 : 0;

    			// Printer
    			dataObj['device-settings-printer-network-enable'] 		= $('#device-settings-printer-network-enable').prop("checked") ? 1 : 0;
    			dataObj['device-settings-printer-network-new-enable'] 	= $('#device-settings-printer-network-new-enable').prop("checked") ? 1 : 0;
    			dataObj['device-settings-printer-network-bump-enable'] 	= $('#device-settings-printer-network-bump-enable').prop("checked") ? 1 : 0;
			
			$.ajax({
			 	headers: token,
	            	url: 'updateDevice',
	            	type: 'POST',
	            	data: {
	            		device: dataObj
	            	},
	            	success: function (response) {
		            	
		            if(response["errorId"] === undefined) {
			            
			            $('#modalDeviceSettings').modal('hide');
			            
			            loadDevicesTable();

			            sendNotificationToFirebase();

		            } else {
		            		var $element = $modal.find('#' + response["errorId"]);
			            	
		            		$element.popover({
							title: "Error",
							content: response["errorMsg"]
			            	});
		            		$element.popover('show');

		            		$element.keyup(function(){
		        				$(this).popover('destroy');
		                	});
		                	$element.change(function(){
		                		$(this).popover('destroy'); // TODO: Update Bootstrap to fix this: "Cannot read property 'trigger' of null"
		                	});
		            }
	            }
		 	});
		});

        
        var columns = [
            				{ field: 'id', title: 'ID', class: 'devices-td-config' }, 
            				{ field: 'kds_station_name', title: 'KDS Station Name' }, 
            				{ field: 'serial_number', title: 'Serial Number' }, 
            				{ field: 'function', title: 'Function' }, 
            				{ field: 'parent_id', title: 'Parent ID' }, 
            				{ field: 'expeditor', title: 'Expeditor' }, 
            				{ field: 'last_update', title: 'Last Update', class: 'devices-td-config' }, 
            				{ field: 'app_version', title: 'Version', class: 'devices-td-config' },
            				{ field: 'license', title: 'License', class: 'devices-td-config' }, 
            				{ field: 'settings', title: 'Settings', class: 'devices-td-config' }, 
            				{ field: 'remove', title: 'Remove', class: 'devices-td-config' }
            			];

        
        var data = [];

        var deviceToRemoveGuid = "";
		var deviceToRemoveSerial = "";

		
		function loadDevicesTable() {

			$.ajax({
        			 	headers: token,
        	            url: 'loadDevicesTable',
        	            type: 'POST',
        	            data: {
        	            		storeGuid: "{{$store->store_guid}}"
        	            	},
        	            success: function (devices) {
            	            
        	            		data = [];
        	            		
        	            		for(var i=0; i<devices.length; i++) {

        						// License
            	            		var licenseHTML = "";
            	            		if (devices[i].split_screen_parent_device_id == 0) {
                	            		var checked = devices[i].license == 1 ? "checked=\"checked\"" : "";
            	            			licenseHTML = "<label class=\"switch\">" +
                                                    "<input class=\"device-license-login\" store_guid=\"" + devices[i].store_guid + "\" " +
                                                    		"guid=\"" + devices[i].guid + "\" type=\"checkbox\" " + 
                      									checked + " value=\"" + devices[i].license + "\">" +
                                                  "<span class=\"slider round\"></span>" +
                                               "</label>";
                	            }

            	            		// Settings
                    	        var settingsHTML = "<a class=\"btn btn-xs btn-warning btn-icons device-settings\" href=\"#\" " +
                    	        							"store_guid=\"" + devices[i].store_guid + "\" " +
                    	        							"guid=\"" + devices[i].guid + "\" screen_id=\"" + devices[i].screen_id + "\" " +
                                        				"data-toggle=\"modal\" data-target=\"#modalDeviceSettings\" data-title=\"Device Settings\">" +
                                                		"<i class=\"fa fa-cogs fa-lg\"></i>" +
                                                "</a>";

                             // Remove
                    	        var removeHTML = "";
                    	        if (devices[i].split_screen_parent_device_id == 0) {
                    	        		removeHTML = "<a class=\"btn btn-xs btn-danger btn-icons remove-device\" href=\"#\" " +
                            						"store_guid=\"" + devices[i].store_guid + "\" device_serial=\"" + devices[i].serial + "\" " +
                            						"device_guid=\"" + devices[i].guid + "\" device_name=\"" + devices[i].name + "\" " +
                                    				"data-toggle=\"modal\" data-target=\"#modalRemoveDevice\" data-title=\"Remove Device\" " +
                                    				">" +
                                    				"<i class=\"fa fa-trash fa-lg\"></i>" +
                                				 "</a>";
                    	        }

        	            			// Add Device Row
                	            data.push({ 
                    	            id: devices[i].id,
                    	            kds_station_name: devices[i].name,
                    	            serial_number: devices[i].serial.substring(0,8),
                    	            'function': devices[i].function,
                    	            parent_id: devices[i].parent_id == 0 ? "" : devices[i].parent_id,
                    	            expeditor: devices[i].expeditor,
                    	            last_update: timeConverter(devices[i].update_time),
                    	            app_version: devices[i].app_version,
                    	            license: licenseHTML,
                    	            settings: settingsHTML,
                                remove: removeHTML
                    	        });
                	            
            	            	}
						
        	            		// Build Table
        	            		$('#devices-table').bootstrapTable({ 
        	            			pagination: true,
        	            			search:true,
            	            		columns: columns
            	            	});

            	            	// Load Table (update data when it is reloaded)
        	            		$('#devices-table').bootstrapTable("load", data);

        	            		// Device License ------------------------------------------------------------------------------- //
        	            		var URL_BASE = window.location.protocol + "//" + window.location.host;
        	            		
        	            		$('.device-license-login').change(function(){
        	            			var theCkeck = $(this);
        	            			var store_guid = $(this).attr('store_guid');
        	            			var guid 	   = $(this).attr('guid');
        	            			var checking   = $(this).prop("checked");
        	            			
        	            			$.ajax({
        	            				headers: token,
        	            				type:'GET',
        	            			   	dataType: 'json',
        	            				url: URL_BASE + "/api/devices/active",
        	            	            data: {req: "DEVICES_ACTIVE", store_guid: store_guid, guid: guid, active: checking ? 1 : 0},
        	            	            success: function (response) {
        	            	            		if (response != true && response != "true") {
        	            	            			theCkeck.prop("checked", !checking);
        	            	            			$("#modal-error .modal-title").text("Action not permitted");
        	            	            			$("#modal-error .modal-body").text(response);
        	            	            			$('#modal-error').modal('show');
        	            	            		} else {
        	            	            			var numbers  = $('#license-info').text().split(": ")[1]; // Licenses: e.g. 1 / 3
        	            	            			var info  = numbers.split(" / "); // e.g. 1 / 3
        	            	            			var count = parseInt(info[0]) + (checking?1:-1);
        	            	            			$('#license-info').text("Licenses: " + count + " / " + info[1])
        	            	            		}
        	            	            }
        	            	        });
        	            		});
        	            		// ------------------------------------------------------------------------------- Device License //

        	            		// Device Settings ------------------------------------------------------------------------------- //
        	            		$(".device-settings").click(function(){
               	         	var deviceGuid = $(this).attr('guid');
               	         	var deviceScreenId = $(this).attr('screen_id');
               	         	getDeviceSettings(deviceGuid, deviceScreenId);
                			});
        	            		// ------------------------------------------------------------------------------- Device Settings //

        	            		// Remove Device --------------------------------------------------------------------------------- //
       	             	$('.remove-device').click(function(){
       	         			storeGuid = $(this).attr('store_guid');
       	         			deviceToRemoveSerial = $(this).attr('device_serial');
       	         			deviceToRemoveGuid = $(this).attr('device_guid');
       	         			var deviceName = $(this).attr('device_name');
       	         			$('#modalRemoveDevice #are-you-sure').html('Are you sure you want to remove the KDS Station ' + 
       	         					'\"<span style="color:red;">' + deviceName +  '\</span>"?')
       	         		});

            	        		$('#remove-device-confirm').click(function(){
            	        			if(deviceToRemoveSerial != "" && deviceToRemoveGuid != "") {
            	        				 $.ajax({
            	        					 	headers: token,
            	        			            url: 'removeDevice',
            	        			            type: 'POST',
            	        			            data: { 
            	        			            		storeGuid: storeGuid,
            	        			            		deviceSerial: deviceToRemoveSerial,
            	        			            		deviceGuid: deviceToRemoveGuid
            	        			            	},
            	        			            success: function (response) {
            	        							if(response !== "") {
            	        								alert(response);
            	        							} else {
            	        								location.reload();
            	        							}
            	        			            }
            	        				 });
            	        			}
            	        		});
            	        		// --------------------------------------------------------------------------------- Remove Device //
            	        		
        	            }
        		 });
		}

		// Load Devices Table
		loadDevicesTable();

		// Modal Close Button
	    $modal.find('#device-settings-close').click(function(){
	        $('.popover').popover('destroy');
	    		$('#modalDeviceSettings').modal('hide');
	    });

    });


    function showTwilio() {
		$('#mp-list').hide();
		$('#mp-twilio').fadeIn();
	}
	
	function showMarketplaceList() {
		$('#mp-twilio').hide();
		$('#mp-list').fadeIn();
	}

    $(document).ready(function(){

        	function setSwitchMarketPlaceEnable(obj) {
        		var configSwitchClass 	= $(obj).attr('config_switch');
        		var configMessageClass 	= $(obj).attr('config_msg');
        		var checking = $(obj).prop("checked");
        		setSwitchMarketPlaceCustom($('.'+configSwitchClass).find('.switch-mp-use-custom'));
        		if (checking) {
        			$('.'+configSwitchClass).fadeIn();
                	$('.'+configMessageClass).css('opacity',1);
//         			$('.'+configMessageClass).find('input').prop('disabled',false);
        		} else {
        			$('.'+configSwitchClass).hide();
        			$('.'+configMessageClass).css('opacity',0.3);
        			$('.'+configMessageClass).find('input').prop('disabled',true);
        		}
        		
        	}
    
        	function setSwitchMarketPlaceCustom(obj) {
        		var inputId 	= $(obj).attr('input_message_id');
        		var smsDefault = $('#'+inputId).attr('sms_default');
        		var smsCustom = $('#'+inputId).attr('sms_custom');
        		var checking = $(obj).prop("checked");
        		if (checking) {
        			$('#'+inputId).prop('disabled',false);
        			$('#'+inputId).val(smsCustom);
        		} else {
        			$('#'+inputId).prop('disabled',true);
        			$('#'+inputId).val(smsDefault);
        		}
        	}

        	$('.switch-mp-enable').change(function(){
        		setSwitchMarketPlaceEnable($(this));
        	});

        	$('.switch-mp-use-custom').change(function(){
        		setSwitchMarketPlaceCustom($(this));
        	});
        	
    });
    
    </script>
    
@endsection






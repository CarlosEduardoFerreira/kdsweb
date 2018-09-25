$(function(){
	
	/** GLOBALS **/
	var devicesIds = [];
	var startDatetime = moment().subtract(moment.duration("24:00:00"));
	var endDatetime = moment();
	
	
	/** filter devices ******************************************************/
	// all = true  (will check if all devices are checked)
	// all = false (will check if all devices are NOT checked)
	function allOrZeroFilterDevicesChecked(all) { 
		var sucess = true;
		$(".checkbox-device").each(function(){
			var checked = $(this).is(':checked');
			if(!$(this).is(':checked') && all) {
				sucess = false;
			} else if($(this).is(':checked') && !all) {
				sucess = false;
			}
		});
		return sucess;
	}
	
	// Set Counter on Report KDS Stations Filter Button
	function setFilterDevicesCounter() {
		var count = 0;
		$(".checkbox-device").each(function(){
			if($(this).is(':checked')) {
				count++;
			}
		});
		if($(".checkbox-device").length == count) {
			$('#showModalDevices').text("KDS Stations (All)");
		} else {
			$('#showModalDevices').text("KDS Stations (" + count + ")");
		}
	}
	
	// Click All KDS Stations Button
	$("#all-kds-stations").click(function(){
		if($(this).is(':checked')) {
			$(".checkbox-device").each(function(){
				$(this).prop('checked', 'checked');
			});
		} else {
			$(".checkbox-device").each(function(){
				$(this).prop('checked', '');
			});
		}
		showOrHideFilterButton();
	});
	
	// Click by KDS Station
	$(".checkbox-device").click(function(){
		if(allOrZeroFilterDevicesChecked(true)) {
			$("#all-kds-stations").prop('checked', 'checked');
		} else {
			$("#all-kds-stations").prop('checked', '');
		}
		showOrHideFilterButton();
	});
	
	
	function showOrHideFilterButton() {
		if(allOrZeroFilterDevicesChecked(false)) {
			$('#filter-devices').fadeOut('fast');
		} else {
			$('#filter-devices').fadeIn('slow');
		}
		$('#close-filter-devices').fadeOut('fast');
	}
	
	// Click Filter Button
	$('#filter-devices').click(function(){
		setFilterDevicesCounter();
		
		devicesIds = [];
		$(".checkbox-device").each(function(){
			if($(this).is(':checked')) {
				var deviceId = $(this).attr('deviceId');
				devicesIds.push(deviceId);
			}
		})
		
		drawTable(devicesIds);
	});
	/****************************************************** filter devices **/
	
	
	/** filter dates ********************************************************/
	$(function(){
		$('.daterangepicker').css({ 'box-shadow':'0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19)' });
	});
	
	$('input[name="daterange"]').daterangepicker({
	    opens: 'left',
	    timePicker: true,
	    startDate: startDatetime,
	    endDate: endDatetime,
	    locale: {
	      format: 'M/DD hh:mm A'
	    }
	  }, function(start, end, label) {
	    
	    startDatetime = start;
	    endDatetime   = end;
	    
	    drawTable(devicesIds);
	    
//	    alert("datetime filter: " + startDatetime.format('YYYY-MM-DD HH:mm') + ' to ' + endDatetime.format('YYYY-MM-DD HH:mm'));
	  });
	/******************************************************** filter dates **/

	
	/** report table ********************************************************/
    	google.charts.load('current', {'packages':['table']});
    	google.charts.setOnLoadCallback(drawTable);
    	
    	function drawTable(devicesIds = []) {
    		var data = new google.visualization.DataTable();
    		data.addColumn('string', 'KDS Station');
    		data.addColumn('string', 'Orders Quantity');
    		data.addColumn('string', 'Items Quantity');
    		data.addColumn('string', 'Order Prep. Avg. Time');
    		data.addColumn('string', 'Item Prep. Avg. Time');
    		data.addColumn('boolean', 'Active');
    		
    		$('#report_div').hide();
        $('#report-total').hide();
        $('#no-data').hide();
    		
    		$.ajax({
                url: 'reportByStation',
                data: { 
                		storeId: $('#store-id').val(), 
                		devicesIds: devicesIds, 
                		startDatetime: startDatetime.format('YYYY-MM-DD HH:mm'), 
                		endDatetime: endDatetime.format('YYYY-MM-DD HH:mm') 
                	},
                success: function (response) {

                		// Total 
                		var total_orders = 0;
                		var total_items  = 0;
                		var total_orders_avg_time = 0;
                		var total_items_avg_time  = 0;
                		var total_actives = 0;
                		
                		var i = 0;
                		
                		response.forEach(function(obj) {
                			
                			// Total Sum
                			total_orders += parseInt(obj.order_count);
                			total_items  += parseInt(obj.item_count);
                			total_orders_avg_time += parseInt(obj.order_avg_time);
                			total_items_avg_time  += parseInt(obj.item_avg_time);
                			total_actives += obj.active;
                			
                			var order_avg_time = moment.duration(parseInt(obj.order_avg_time), 'seconds').humanize();
                			if(obj.order_avg_time < 60) {
                				order_avg_time = parseInt(obj.order_avg_time) + " seconds";
                			}
                			var item_avg_time = moment.duration(parseInt(obj.item_avg_time), 'seconds').humanize();
                			if(obj.item_avg_time < 60) {
                				item_avg_time = parseInt(obj.item_avg_time) + " seconds";
                			}
                			
                			var device = [
                				String(obj.device_name),
                				String(obj.order_count),
                				String(obj.item_count),
                				order_avg_time,
                				item_avg_time,
                				obj.active == 1 ? true : false
                			];
                			data.addRow(device);
                			
                			// Columns width
                			data.setProperty(i, 0, 'style', 'width:20%');
                			data.setProperty(i, 1, 'style', 'width:15%');
                			data.setProperty(i, 2, 'style', 'width:15%');
                			data.setProperty(i, 3, 'style', 'width:20%');
                			data.setProperty(i, 4, 'style', 'width:20%');
                			data.setProperty(i, 5, 'style', 'width:10%');
                			
                			i++;
                		});
                		
                 	var table = new google.visualization.Table(document.getElementById('report_div'));
                		
                 	var cssClasses = {
             			headerRow: 'tblHeaderClass', 
             			hoverTableRow: 'tblHighlightClass',
             			oddTableRow: 'odd-row-style'
                 	};
                 	
                 	var pageSize = 1;
                 	
                 	var tableSettings = {
                 		'cssClassNames':cssClasses,
                 		allowHtml: true,
	            			showRowNumber: false,
	            			width: '100%',
	            			height: 'auto',
	            			// sortColumn: 0,
	            			// sortAscending: true,
	            			page: 'enable',
	            	        pageSize: pageSize,
	            	        pagingSymbols: { prev: 'prev', next: 'next' },
	            	        pagingButtonsConfiguration: 'auto'
                 	}
                 	
                 	$('#report-loading').hide();
                 	
                 	if(response.length == 0) {
	                		$('#no-data').fadeIn('slow');
	
	            		} else {
	            			
	            			table.draw(data, tableSettings);
	            			
	            			// Show Report
	            			$('#report_div').fadeIn('slow');
	            			$('#report-total').fadeIn('slow');
	            			
	            			// Fill Total
	            			var total_orders_avg_time_txt = moment.duration(total_orders_avg_time, 'seconds').humanize();
                			if(total_orders_avg_time < 60) {
                				total_orders_avg_time_txt = total_orders_avg_time + " seconds";
                			}
                			var total_items_avg_time_txt = moment.duration(total_items_avg_time, 'seconds').humanize();
                			if(total_items_avg_time < 60) {
                				total_items_avg_time_txt = total_items_avg_time + " seconds";
                			}
	            			$('#report-total #total-orders').text(total_orders);
	            			$('#report-total #total-items').text(total_items);
	            			$('#report-total #total-orders-avg-time').text(total_orders_avg_time_txt);
	            			$('#report-total #total-items-avg-time').text(total_items_avg_time_txt);
	            			$('#report-total #total-actives').text(total_actives + "/" + response.length);
	            			
	            			// Hover and Zebra
                     	$('#report_div table').addClass('table-hover table-striped');
                     	
                     	if(response.length < pageSize) {
                     		$('.goog-custom-button-collapse-left').hide();
                     		$('.goog-custom-button-collapse-right').hide();
                     	}
                     	
	            		}
                 	
                }
    		
    		});

    	}
    	/******************************************************** report table **/
    	
})

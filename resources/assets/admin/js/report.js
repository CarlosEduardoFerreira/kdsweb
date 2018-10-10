$(function(){
	
	/** GLOBALS **/
	var reportId 		= $('#report-default-id').val();
	var devicesIds 		= [];
	var startDatetime 	= moment().subtract(moment.duration("24:00:00"));
	var endDatetime 		= moment();
	var headers 			= [];
	
	
	/** filter choose report ************************************************/
	$('#' + reportId).prop('checked', 'checked');
	
	
	$('.choose-report').click(function(){
		
		// clear all checkbox
		$('.choose-report').each(function(){
			$(this).prop('checked', '');
		});
		
		// set reportId
		reportId = $(this).attr('for');
		$('#' + reportId).prop('checked', 'checked');
		
		// set button text
		$('#showModalChooseReport').text($(this).text());
		
		// build the report
		drawTable();
	});
	/************************************************ filter choose report **/
	
	
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
		
		drawTable();
	});
	/****************************************************** filter devices **/
	
	
	/** Refresh Button ******************************************************/
	$('#report-refresh-img').click(function(){
		drawTable();
	});
	/****************************************************** Refresh Button **/
	
	
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
	    
	    drawTable();
	    
//	    alert("datetime filter: " + startDatetime.format('YYYY-MM-DD HH:mm') + ' to ' + endDatetime.format('YYYY-MM-DD HH:mm'));
	  });
	/******************************************************** filter dates **/

	
	/** report table ********************************************************/
    	function startReport() {
    		google.charts.load('current', {'packages':['table']});
        	google.charts.setOnLoadCallback(drawTable);
    	}
    	
    	startReport();
    	
    	function drawTable() {
    		//alert('reportId: ' + reportId + " devicesIds:" + devicesIds);
    		
    		// Refresh button hide
    		$('#report-refresh-img').hide();
    		
    		/**
    		 *  headers
    		 *  0 = type used by google.visualization.DataTable()
    		 *  1 = header text
    		 *  2 = custom data type to handle on the iterate
    		 *  3 = total integer
    		 *  4 = column width percentage
    		 *  5 = text align
    		 */
    		headers = [];
    		
    		if(reportId == $('#report-0').val()) { 		// Quantity and Average Time by Order
        		
    			headers[0] = ["string",  "KDS Station", "text", 0, "25", "left"];
    			headers[1] = ["string",  "Orders Quantity", "sum", 0, "25", "center"];
    			headers[2] = ["string",  "Order Prep. Avg. Time", "time", 0, "30", "left"];
    			headers[3] = ["boolean", "Active", "active", 0, "20", "center"];
    			
    		} else if(reportId == $('#report-1').val()) { 	// Quantity and Average Time by Item
    			
    			headers[0] = ["string",  "KDS Station", "text", 0, "25", "left"];
    			headers[1] = ["string",  "Items Quantity", "sum", 0, "25", "center"];
    			headers[2] = ["string",  "Item Prep. Avg. Time", "time", 0, "30", "left"];
    			headers[3] = ["boolean", "Active", "active", 0, "20", "center"];
    			
    		} else if(reportId == $('#report-2').val()) {	// Quantity and Average Time by Item Name
    			headers[0] = ["string",  "KDS Station", "text", 0, "25", "left"];
    			headers[1] = ["string",  "Items Name", "text", 0, "30", "left"];
    			headers[2] = ["string",  "Items Quantity", "sum", 0, "15", "center"];
    			headers[3] = ["string",  "Item Prep. Avg. Time", "time", 0, "30", "left"];
    		}
    		
    		buildReport();
    		
    	}
    	
    	
    	function buildReport() {
    		var data = new google.visualization.DataTable();
    		
    		// Build table headers
    		for(var i_col = 0; i_col < headers.length; i_col++) {
    			data.addColumn(headers[i_col][0], headers[i_col][1]);
    			// clear total
    			headers[i_col][3] = 0;
    		}
    		
    		var pageSize = 10;
    		
    		$('#report_div').hide();
        $('#report-total').hide();
        $('#no-data').hide();
        $('#report-loading').show();
        
        function getValue(i_col, column_type, column_value, sum_total) {
        		
			if(column_type == "sum" || column_type == "time") {
				column_value = parseInt(column_value);
				if (sum_total) {
					headers[i_col][3] += column_value;
				}
			}
			
			if(column_type == "time") {
				column_value = convertTimeToRead(column_value);
				
			} else if(column_type == "active") {
				column_value = column_value == "true" ? true : false;
				if (sum_total) {
					headers[i_col][3] += column_value ? 1 : 0;
				}
				
			} else {
				column_value = String(column_value);
			}
			
			return column_value;
        }
            
        $.ajax({
            url: 'reportByStation',
            data: { 
            		storeId: $('#store-id').val(), 
            		reportId: reportId,
            		devicesIds: devicesIds, 
            		startDatetime: startDatetime.format('YYYY-MM-DD HH:mm'), 
            		endDatetime: endDatetime.format('YYYY-MM-DD HH:mm') 
            	},
            success: function (response) {
            		
            		var i_row = 0;
            		
            		var columns_count = headers.length
            		
            		var last_device = "";
            		
            		response.forEach(function(obj) {
            			
            			var device = [];
            			
            			// Set columns value/text
            			for(var i_col = 0; i_col < columns_count; i_col++) {
            				
            				var column_type		= headers[i_col][2];
            				var column_value 	= eval("obj.column_" + i_col);
            				
            				column_value = getValue(i_col, column_type, column_value, true);

            				if(reportId == $('#report-2').val() && i_col == 0) { // Quantity and Average Time by Item Name (Device Name)
            					if (i_row % pageSize != 0 && column_value == last_device)  {
            						column_value = "";
            					}
            				}
            				
            				device.push(column_value);
            			}
            			
            			last_device = obj.column_0; // Should be always the Device Name
            			
            			data.addRow(device);
            			
            			// Set columns width
            			for(var i_col = 0; i_col < columns_count; i_col++) {
            				var css_tr_color = "";
            				
            				if(reportId == $('#report-2').val()) { // Quantity and Average Time by Item Name (Device Name)
            					if(device[0] == "" && i_col == 0) {
            						css_tr_color = " background:#fefefe;";
            					}
            				}
            				
            				data.setProperty(i_row, i_col, 'style', "width:" + headers[i_col][4] + "%; " +
            						"text-align:" + headers[i_col][5] + " !important; " +
            						"padding-left:10px;" + css_tr_color);
            			}

            			i_row++;
            		});
            		
             	var table = new google.visualization.Table(document.getElementById('report_div'));
             	
             	var sort = 'enable';
             	if(reportId == $('#report-2').val()) { // Quantity and Average Time by Item Name (Device Name)
             		sort = 'disable';
             	}
            		
             	var cssClasses = {
         			headerRow: 'tblHeaderClass', 
         			hoverTableRow: 'tblHighlightClass',
         			oddTableRow: 'odd-row-style'
             	};
             	
             	var tableSettings = {
             		'cssClassNames':cssClasses,
             		allowHtml: true,
            			showRowNumber: false,
            			sort: sort,
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
            			
            			// Handle Total
            			$('.report-total-values').remove();
            			for(var i_col = 0; i_col < headers.length; i_col++) { // var i_col = 1 (Total Text does not sum)
            				
            				var column_type	= headers[i_col][2];
            				var total_value  = headers[i_col][3];
            				
            				if (column_type == "text") {
            					total_value = "";
            					if (i_col == 0) {
            						total_value = "Total";
            					}
            				
            				} else if (column_type == "active") {
	        					total_value = headers[i_col][3] + "/" + response.length;
	        					
	        				} else {
//	        					alert("1: " + total_value)
	        					total_value = getValue(i_col, column_type, total_value, false);
//	        					alert("2: " + total_value)
	        				}
            				
            				var td = $('<td width="' + headers[i_col][4] + '%" ' +
            						'class="report-total-tds report-total-values" ' +
            						'style="padding-left:10px; text-align:' + headers[i_col][5] + ';">' + 
            						total_value + '</td>');
            				$('#report-total-tr').append(td);
            			}
            			
            			// Hover and Zebra
                 	$('#report_div table').addClass('table-hover table-striped');
                 	
                 	if(response.length < pageSize) {
                 		$('.goog-custom-button-collapse-left').hide();
                 		$('.goog-custom-button-collapse-right').hide();
                 	}
                 	
            		}
             	
             	// Refresh button show
             	$('#report-refresh-img').fadeIn('slow');
             	
            }
    		
    		});
            
    	}
    	/******************************************************** report table **/
    	
    	
    	function convertTimeToRead(time) {
    		var sec_num = parseInt(time, 10);
    	    var hours   = Math.floor(sec_num / 3600);
    	    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    	    var seconds = sec_num - (hours * 3600) - (minutes * 60);

    	    hours = hours == 1 ? "1 hour" : (hours > 0 ? hours + " hours" : "");
    	    minutes = minutes == 1 ? "1 minute" : (minutes > 0 ? minutes + " minutes" : "");
    	    seconds = seconds == 1 ? "1 second" : (seconds > 0 ? seconds + " seconds" : "")
    	    
    	    return hours + " " + minutes + " " + seconds;
    	}
    	
})






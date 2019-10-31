$(function(){
	
	/** GLOBALS **/
	var reportId 		= $('#report-default-id').val();
	var devicesIds 		= [];
	var startDatetime 	= moment().subtract(moment.duration("24:00:00"));
	var endDatetime 		= moment();
	var headers 			= [];
	var perPage			= 10;
	
	
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
		startReport();
	});
	/************************************************ filter choose report **/
	
	
	/** per page filter *****************************************************/
	$('.per-page-dropdown a').click(function(){
		var selected = $(this).text();
		$('.per-page-dropdown #per-page-value').text(selected);
		
		// Set per page
		perPage = parseInt(selected);
		
		// build the report
		startReport();
    	});
	/***************************************************** per page filter **/
	
	
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
		});
		
		startReport();
	});
	/****************************************************** filter devices **/
	
	
	/** Refresh Button ******************************************************/
	$('#report-refresh-img').click(function(){
		startReport();
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
	    
	    startReport();
	    
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
			
			switch (reportId) {
				case $('#report-0').val():
					headers[0] = ["string",  "KDS Station", "text", 0, "25", "left"];
					headers[1] = ["string",  "Orders Quantity", "sum", 0, "25", "center"];
					headers[2] = ["string",  "Order Prep. Avg. Time", "time", 0, "30", "left"];
					break;
				
				case $('#report-1').val():
					headers[0] = ["string",  "KDS Station", "text", 0, "25", "left"];
					headers[1] = ["string",  "Items Quantity", "sum", 0, "25", "center"];
					headers[2] = ["string",  "Item Prep. Avg. Time", "time", 0, "30", "left"];
					break;
				
				case $('#report-2').val():
					headers[0] = ["string",  "KDS Station", "text", 0, "20", "left"];
					headers[1] = ["string",  "Items Name", "text", 0, "25", "left"];
					headers[2] = ["string",  "Items Quantity", "sum", 0, "15", "center"];
					headers[3] = ["string",  "Item Prep. Avg. Time", "time", 0, "20", "left"];
					headers[4] = ["string",  "Total Prep. Time", "time", 0, "20", "left"];
					break;
				
				case $('#report-3').val():
					headers[0] = ["string",  "KDS Station", "text", 0, "15", "left"];
					headers[1] = ["string",  "Category", "text", 0, "15", "left"];
					headers[2] = ["string",  "Items Name", "text", 0, "20", "left"];
					headers[3] = ["string",  "Items Qty", "sum", 0, "10", "center"];
					headers[4] = ["string",  "Item Prep. Avg. Time", "time", 0, "20", "left"];
					headers[5] = ["string",  "Total Prep. Time", "time", 0, "20", "left"];
					break;
			}
    		
    		buildReport();
    	}
    	
    	
    	function buildReport(exportReport = false) {
    		
    		var dataTable = new google.visualization.DataTable();
    		
    		// Build table headers
    		for(var i_col = 0; i_col < headers.length; i_col++) {
    			dataTable.addColumn(headers[i_col][0], headers[i_col][1]);
    			// clear total
    			headers[i_col][3] = 0;
    		}
		
    		$('#report_div').hide();
			$('#report-total').hide();
			$('#no-data').hide();
			$('#report-export-excel').hide();
			
			$('#report-loading').show();
			
			$.ajax({
				url: 'reportByStation',
				data: { 
						storeId: $('#store-id').val(),
						storeGuid: $('#store-guid').val(), 
						reportId: reportId,
						devicesIds: devicesIds, 
						startDatetime: startDatetime.format('YYYY-MM-DD HH:mm'), 
						endDatetime: endDatetime.format('YYYY-MM-DD HH:mm') 
					},
				success: function (response) {

					$('#report-loading').hide();
					
					if(response.length == 0) {
							$('#no-data').fadeIn('slow');
							
					} else if(response["error"] != undefined) {
						$('#no-data').fadeIn('slow');
						$('#modal-error').modal("show");
						$('#modal-error').find('.modal-body').css({'padding':'20px', 'font-size':'14px', 'font-weight':'300', 'letter-spacing':'2px',
							'text-align':'center', 'color':'red'});
						$('#modal-error').find('.modal-body').html(response["error"]["msg"]);

						} else {
							
							fillDataTable(response, dataTable);
							
							var cssClasses = {
								headerRow: 'tblHeaderClass', 
								hoverTableRow: 'tblHighlightClass',
								oddTableRow: 'odd-row-style'
							};
							
							var tableSettings = {
								'cssClassNames':cssClasses,
								allowHtml: true,
									showRowNumber: false,
									sort: reportId == $('#report-2').val() ? 'disable' : 'enable', // Quantity and Average Time by Item Name (Device Name)
									width: '100%',
									height: 'auto'
							}
							
							if(!exportReport) {
								tableSettings.page = 'enable';
								tableSettings.pageSize = perPage;
								tableSettings.pagingSymbols = { prev: 'prev', next: 'next' };
								tableSettings.pagingButtonsConfiguration = 'auto';
							}
							
							handleTable(exportReport, response, tableSettings, dataTable);
							
							// Export Report --------------------------------------------------------- //
							if(exportReport) {
								var id = 'KDS_'+getFilenameDatetime();
								
								var reportTable = $('#report_div table');
									reportTable.prop('id', id);
									
								TableExport.prototype.typeConfig.date.assert = function(value){return false;};
									
								var instance = new TableExport(reportTable, {
									formats: ['xlsx'],
									exportButtons: false,
									bootstrap: true,
									exportDataType: 'all'
								});
								
								if(instance.getExportData()[id] !== undefined) {
									xlsxExportData = instance.getExportData()[id]['xlsx'];
								}
								
								if(xlsxExportData !== undefined) {
									instance.export2file(
										xlsxExportData.data, 
										xlsxExportData.mimeType, 
										xlsxExportData.filename, 
										xlsxExportData.fileExtension
									);
								}
								
								buildReport();
							}
							// --------------------------------------------------------- Export Report //
							
						}
					
					// Refresh Button show
					$('#report-refresh-img').fadeIn('slow');
				},
					error : function (xhr, ajaxOptions, thrownError) {
						if(xhr.status == 401) { // {"error":"Unauthenticated."}
						location.href = "{{ route('admin.dashboard') }}";
					} else if(xhr.status == 504) { // {"error":"Gateway Time-out."}
						$('#report-loading').hide();
						$('#no-data').fadeIn('slow');
					}
					}
					
				});
    	}
    	
    	
    	function fillDataTable(response, dataTable) {
    		var i_row = 0;
    		var columns_count = headers.length;
			var last_device = "";
			var last_category = "";
		
    		response.forEach(function(obj) {
    			var device = [];
    			
    			// Set columns value/text
    			for(var i_col = 0; i_col < columns_count; i_col++) {
    				var column_type		= headers[i_col][2];
    				var column_value 	= eval("obj.column_" + i_col);
    				
    				// by Item Name
    				if (reportId == $('#report-2').val()) {
						switch (i_col) {
							case 0: // Device name
								if (i_row % perPage != 0 && column_value == last_device)  {
									column_value = "";
								}
								break;

							case 4:
								column_value = parseInt(obj.column_2) * parseInt(obj.column_3);
								break;
						}
					}
					
					// by Category
    				if (reportId == $('#report-3').val()) {
						switch (i_col) {
							case 0: // Device name
								if (i_row % perPage != 0 && column_value == last_device)  {
									column_value = "";
								}
								break;

							case 1: // Category
								if (i_row % perPage != 0 && column_value == last_category)  {
									column_value = "";
								}
								last_category = obj.column_1;
								break;

							case 2:
								if (column_value == null) column_value = "<strong>Total</strong>";
								break;

							case 5:
								column_value = parseInt(obj.column_3) * parseInt(obj.column_4);
								break;
						}

						if (column_value == null) column_value = "";
					}
    				
    				column_value = getValue(i_col, column_type, column_value, true);
    				device.push(column_value);
    			}
    			
    			last_device = obj.column_0; // Should be always the Device Name
    			
    			dataTable.addRow(device);
    			
    			// Set columns width
    			for(var i_col = 0; i_col < columns_count; i_col++) {
    				var css_tr_color = "";
    				
    				if(reportId == $('#report-2').val()) { // Quantity and Average Time by Item Name (Device Name)
    					if(device[0] == "" && i_col == 0) {
    						css_tr_color = " background:#fefefe;";
    					}
    				}
    				
    				dataTable.setProperty(i_row, i_col, 'style', "width:" + headers[i_col][4] + "%; " +
    						"text-align:" + headers[i_col][5] + " !important; " +
    						"padding-left:10px;" + css_tr_color);
    			}

    			i_row++;
    		});
    	}
    	
    	
    	function handleTable(exportReport, response, tableSettings, dataTable) {
    		var table = new google.visualization.Table(document.getElementById('report_div'));
    		
		table.draw(dataTable, tableSettings);
		
		if(!exportReport) {
			var reportHeight = response.length < perPage ? response.length : perPage;
			$('#report_div').css({ "height" : String((reportHeight * 40) + 80) + "px", "overflow-y" : "hidden" });
		}
			
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
				total_value = getValue(i_col, column_type, total_value, false);
			}
			
			var td = $('<td width="' + headers[i_col][4] + '%" ' +
					'class="report-total-tds report-total-values" ' +
					'style="padding-left:10px; text-align:' + headers[i_col][5] + ';">' + 
					total_value + '</td>');
			$('#report-total-tr').append(td);
		}
			
			// Hover and Zebra
     	$('#report_div table').addClass('table-hover table-striped');
     	
     	if(response.length < perPage) {
     		$('.goog-custom-button-collapse-left').hide();
     		$('.goog-custom-button-collapse-right').hide();
     	}
     	
     	// Export Button show
     	$('#report-export-excel').fadeIn();
    	}
    	

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
    	
	/******************************************************** report table **/
    	
    	
    	
    	// Export Report --------------------------------------------------------- //
	$('#report-export-excel').click(function() {
		buildReport(true);
	});
	// --------------------------------------------------------- Export Report //
    	
    	
	
    	function getFilenameDatetime() {
    	    var today = new Date();
    	    var y = today.getFullYear();
    	    var m = today.getMonth() + 1;
    	    var d = today.getDate();
    	    var h = today.getHours();
    	    var mi = today.getMinutes();
    	    var s = today.getSeconds();
    	    return y + "_" + m + "_" + d + "_" + h + "_" + mi + "_" + s;
    	}
    	
    	
    	function convertTimeToRead(time) {
    		var sec_num = parseInt(time, 10);
    	    var hours   = Math.floor(sec_num / 3600);
    	    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    	    var seconds = sec_num - (hours * 3600) - (minutes * 60);

    	    hours = hours == 1 ? "1 hr" : (hours > 0 ? hours + " hrs" : "");
    	    minutes = minutes == 1 ? "1 min" : (minutes > 0 ? minutes + " mins" : "");
    	    seconds = seconds == 1 ? "1 sec" : (seconds > 0 ? seconds + " secs" : "")
    	    
    	    return hours + " " + minutes + " " + seconds;
    	}
    	
})






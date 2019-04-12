$(document).ready(function() {
	
	var token = { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
	
	$('[name="auto_done_order_hourly"]').change(function(){
		setAMPM();
	});
	
	
	$('#auto_done_order_time_field').on('input',function(e){
		handleTime();
	});
	
	
	$('[name="auto_done_order_time_ampm"]').change(function(){
		handleTime();
	});
	
	
	setAMPM();
	
	
	// Save / Submit
	$('#btn-save-settings').click(function() {
		if (handleTime()) {
			validateStoreSettings();
		}
	});


	// Reset Store Key
    $('#btn-reset-key').click(function() {
        if (handleTime()) {
            $('#store_key').val("");
            $('#form-settings').submit();
        }
    });
	
	
	function setAMPM() {
		
		if ($('[name="auto_done_order_hourly"]:checked').val() == 1) {
			$('#auto_done_order_time_ampm').fadeOut('fast',function(){
				$(this).css('display','none');
			});
			
		} else {
			$('#auto_done_order_time_ampm').fadeIn('slow',function(){
				$(this).css('display','inline');
				
				var time = $('[name="auto_done_order_time"]').val();
				var colon = time.indexOf(':') > -1;
				if (colon) {
					var hours   = time.split(':')[0];
					var minutes = time.split(':')[1];
					if (parseInt(hours) > 12) {
						$("#auto_done_order_time_ampm select").val("PM");
					}
				}
			});
		}
	
		handleTime();
	}
	
	
	function submitForm() {
		$('#btn-save-settings').hide();
		
		sendNotificationToFirebase();
		
		setTimeout(function(){ $('#form-settings').submit(); }, 1000);
	}
	
	
	function validateStoreSettings() {
		
		$.ajax({
		 	headers: token,
            url: 'validateStoreSettings',
            type: 'POST',
            data: {
            		storeGuid: $('#store-guid').val(),
            		licensesQuantity: $('#licenses_quantity').val()
            	},
            success: function (error) {
            	
	            	var licenses_quantity_old = $('#licenses_quantity_old').val();
	        		var licenses_quantity = $('#licenses_quantity').val();
	        		
	        		if(licenses_quantity_old < licenses_quantity) {
	        			error["id"] 		= "licenses_quantity_old";
	        			error["title"] 	= "License Quantity";
	        			error["msg"] 	= "Increasing the license quantity will incur an additional cost. Are you sure you want to continue?";
	        		}
            	
	            	if(error["id"] == undefined) {
	            		submitForm();
	        			
	        		} else {

	        			$("#modal-error .modal-title").text(error["title"]);
	        			$("#modal-error .modal-body").text(error["msg"]);
	        			
	        			if(error["id"] == "licenses_quantity_old") {
	        				$('#modal-error').find('#btn-error-close').text("No");
	        				$('#modal-error').find('#btn-error-ok').text("Yes").show().click(function(){
	        					submitForm();
	        				});
	        			}
        				
        				$('#modal-error').modal('show');
	        		}
            }
        });
		
	}
	
	
	function handleTime() {
		reg = /^0[0-9]|1[0-9]|2[0-3]:[0-5][0-9]$/;
		
		$('#error-time').css('display','none');
		
		var time  = $('#auto_done_order_time_field').val();
		
		var colon = time.indexOf(':') > -1;
		if (colon) {
			var hours   = time.split(':')[0];
			var minutes = time.split(':')[1];
			
			var hourly = $('[name="auto_done_order_hourly"]:checked').val() == 1;
			if (!hourly && parseInt(hours) > 12) {
				hours = parseInt(hours) - 12;
			}
			
			hours = hours.toString().length == 1 ? "0" + hours : hours;
			//alert("hours: " + hours + " | hours.length: " + hours.length)
			time = hours + ":" + minutes;
			
			var valid = reg.test(time) && minutes.length == 2 && parseInt(hours) < 24 && parseInt(minutes) < 60;
			
			//alert("time: " + time + " | reg: " + reg.test(time) + " | valid: " + valid)
			
			if (valid) {
				var time24 = time;
				if (!hourly) {
					var ampm = $("#auto_done_order_time_ampm option:selected").val();
					if (ampm == "PM") {
						var hours24 = parseInt(hours) + 12;
						var time24  = hours24 + ":" + minutes;
					}
				}
				//alert("time: " + time + " | time24: " + time24)
				$('[name="auto_done_order_time"]').val(time24);
				$('#auto_done_order_time_field').val(time);
				return true;
			}
		}
		$('#error-time').css('display','inline');
		return false
	}


    function insertText(inputId, text) {
        var input = document.getElementById(inputId);

        //IE support
        if (document.selection) {
            input.focus();
            sel = document.selection.createRange();
            sel.text = text;
        }

        //MOZILLA and others
        else if (input.selectionStart || input.selectionStart == '0') {
            var startPos = input.selectionStart;
            var endPos = input.selectionEnd;
            input.value = input.value.substring(0, startPos)
                + text
                + input.value.substring(endPos, input.value.length);
        } else {
            input.value += text;
        }
    }

    function addStoreNameOnSms(inputId) {
        insertText(inputId, "[STORE_NAME]");
    }

    function addCustomerNameOnSms(inputId) {
        insertText(inputId, "[CUSTOMER_NAME]");
    }

    function addOrderIdOnSms(inputId) {
        insertText(inputId, "[ORDER_ID]");
    }

    $('#btn-start-add-store-name-key').click(function() {
        addStoreNameOnSms("sms_start_custom");
    });

    $('#btn-start-add-customer-name-key').click(function() {
        addCustomerNameOnSms("sms_start_custom");
    });

    $('#btn-start-add-order-id-key').click(function() {
        addOrderIdOnSms("sms_start_custom");
    });

    $('#btn-ready-add-store-name-key').click(function() {
        addStoreNameOnSms("sms_ready_custom");
    });

    $('#btn-ready-add-customer-name-key').click(function() {
        addCustomerNameOnSms("sms_ready_custom");
    });

    $('#btn-ready-add-order-id-key').click(function() {
        addOrderIdOnSms("sms_ready_custom");
    });

    $('#btn-done-add-store-name-key').click(function() {
        addStoreNameOnSms("sms_done_custom");
    });

    $('#btn-done-add-customer-name-key').click(function() {
        addCustomerNameOnSms("sms_done_custom");
    });

    $('#btn-done-add-order-id-key').click(function() {
        addOrderIdOnSms("sms_done_custom");
    });


});





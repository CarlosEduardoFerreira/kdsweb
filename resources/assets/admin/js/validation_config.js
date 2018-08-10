$(document).ready(function() {
	
	$('[name="auto_done_order_hourly"]').change(function(){
		setAMPM();
	});
	
	
	$('#auto_done_order_time_field').on('input',function(e){
		handleTime();
	});
	
	
	$('[name="auto_done_order_time_ampm"]').change(function(){
		handleTime();
	});
	
	
	// Start Calls --------------------------------------- //
	setAMPM();
	//---------------------------------------- Start Calls //
	
	
	$('#btn-save-settings').click(function() {
		if (handleTime()) {
			//alert('OK')
			$('#form-settings').submit();
		} else {
			//alert('ERROR')
		}
	});


    $('#btn-reset-key').click(function() {
        if (handleTime()) {
            $('#store_key').val(null);
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
	
});





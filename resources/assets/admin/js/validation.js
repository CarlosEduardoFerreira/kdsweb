


$('#btn-save-form').click(function(){
	var obj  = $(this).attr("obj");		// store, storegroup, reseller
	var edit = $(this).attr("edit");		// is edit?
	if (edit) {
		//alert("1 obj:" + obj + " edit:" + edit);
	}

	var submit = true;
	
	$('#main-form').find('input, select').each( function() {
		$(this).parent().find(".parsley-errors-list").find(".parsley-required").text("");
	});
	
	$('#main-form').find('input, select').each( function() {
	    if ($(this).prop('required')) {
	    		var value = "";
	    		var type  = $(this).attr("type");
	    		if (type == "text" || type == "email" || type == "password") {
	    			value = $(this).val() != null ? $(this).val().trim() : "";
	    		} else {
	    			value = $(this).find(" option:selected").text();
	    		}
	    		if (value == "") {
		        submit = false;
		        $(this).parent().find(".parsley-errors-list").find(".parsley-required").text("Please fill out this field.");
	    		}
	    }
	});
	
	if (submit) {
		var pass1 = $('#password').val();
		var pass2 = $('#password_confirmation').val();
		if (pass1 != pass2) {
			submit = false;
			$('#password_confirmation').parent().find(".parsley-errors-list").find(".parsley-required").text("Passwords does not match.");
		}
	}
	
	if (submit) {
		$('#main-form').submit();
	}
	
});






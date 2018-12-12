


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
		
		var id = $('#user_id').val();
		var obj = $('#user_obj').val();
		var email = $('#email').val();
		var username = $('#username').val();
		var user_apps = $('#user_apps').val();
		var user_envs = $('#user_envs').val();
		
		var URL_BASE = window.location.protocol + "//" + window.location.host;
		
		$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
		
		$.ajax({
			type:'GET',
		   	dataType: 'json',
			url: URL_BASE + "/api/register/validation",
	        data: { req: "REGISTER_VALIDATION",
	        		id: id,
	        		obj: obj,
	        		email: email,
	        		username: username,
	        		user_apps: user_apps,
	        		user_envs: user_envs
	        },
	        success: function (response) {
	        		if (response[0]["ERROR"] === undefined) {
	        			$('#main-form').submit();
	        		} else {
	        			var field = response[0]["FIELD"];
		        		var error = response[0]["ERROR"];
	        			
	        			if(field == 'user_apps' || field == 'user_envs') {
	        				$("#modal-error .modal-title").text("Required field");
	            			$("#modal-error .modal-body").text(error);
	            			$('#modal-error').modal('show');
	        			}
		        		
	        			$('#'+field).parent().find(".parsley-errors-list").find(".parsley-required").text(error);
		        }
	        }
	    });
	}
	
});






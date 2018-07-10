    //alert('teste 123');

	var URL_BASE = window.location.protocol + "//" + window.location.host;
    
    $('#country').change(function(){
	    	//alert('test 2');
	    	var countryID = $(this).val();    
	    	if(countryID){
	    		$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
	    		$.ajax({
	    		   type:'GET',
	    		   dataType: 'json',
	    		   url: URL_BASE + "/admin/location/get_state_list",
	    		   data:{country_id:countryID},
	    		   success:function(states){
	    			   $("#city").empty();
	    			   if(states){
		    	            $("#state").empty();
		    	            //$("#state").append('<option>Select</option>');
		    	            for(var i=0; i<states.length; i++) {
		    	            		$("#state").append("<option value='" + states[i].id + "'>" + states[i].name + "</option>");
		    	            }
		    	        }else{
		    	           $("#state").empty();
		    	        }
	    		   }
	    		});
	    	} else {
	    	    $("#state").empty();
	    	    $("#city").empty();
	    	}      
    });
    
    $('#country').change();

//    $('#state').on('change',function(){
//	    	var stateID = $(this).val();    
//	    	if(stateID){
//	    		$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
//	    		$.ajax({
//	    		   type:'GET',
//	    		   dataType: 'json',
//	    		   url: URL_BASE + "/admin/location/get_city_list",
//	    		   data:{state_id:stateID},
//	    		   success:function(cities){
//	    			   if(cities){
//		    	            $("#city").empty();
//		    	            $("#city").append('<option>Select</option>');
//		    	            for(var i=0; i<cities.length; i++) {
//		    	            		$("#city").append("<option value='" + cities[i].id + "'>" + cities[i].name + "</option>");
//		    	            }
//		    	        }else{
//		    	           $("#city").empty();
//		    	        }
//	    		   }
//	    		});
//	    	}else{
//	    	    $("#city").empty();
//	    	}
//    });
    
//    $("#country").val('231'); // 231 = United States
//    $("#state").val('3956'); // 3956 = New York
    
    
    
    
    
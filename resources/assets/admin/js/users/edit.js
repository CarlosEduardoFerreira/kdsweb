
$(document).ready(function(){
	
	var URL_BASE = window.location.protocol + "//" + window.location.host;
	
	$('.device-license-login').change(function(){
		var theCkeck = $(this);
		var guid 	 = $(this).attr('guid');
		var checking = $(this).prop("checked");
		//alert("guid: " + guid + "checked: " + checking);
		$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
		$.ajax({
			type:'GET',
		   	dataType: 'json',
			url: URL_BASE + "/api/devices/active",
            data: {req: "DEVICES_ACTIVE", guid: guid, active: checking ? 1 : 0},
            success: function (response) {
            //	alert(response);
            		if (response != true && response != "true") {
            			theCkeck.prop("checked", !checking);
            			$(".modal-title").text("Action not permitted");
            			$(".modal-body").text(response);
            			$('#modal-btn').click();
            		} else {
            			var numbers  = $('#license-info').text().split(": ")[1]; // Licenses: 1 / 3
            			var info  = numbers.split(" / "); // 1 / 3
            			var count = parseInt(info[0]) + (checking?1:-1);
            			$('#license-info').text("Licenses: " + count + " / " + info[1])
            		}
            }
        });
	});
	
});





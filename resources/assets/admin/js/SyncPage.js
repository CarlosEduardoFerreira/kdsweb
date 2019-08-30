class SyncPage {
	
	static getContent(url, $container, toolbar) {
		$.ajax({
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
			url: url,
			type: "GET",
			data : {},
			success: function(response){
				$container.html("");
				
				var $toolbarContainer = $("<div id=\"toolbarContainer\"></div>").css({'height':'50px', 'text-align':'right', 'margin-right':'20px'});
				if(toolbar != '') {
					var $toolbar = $(response).find(toolbar);
					$toolbarContainer.html($toolbar);
				}
				
				$container.append($toolbarContainer);
				
				var $content = $("<div id=\"sync-page-container\"><div style=\"width:95%;margin:10px auto;\">" + response + "</div></div>");
				
					$content.css({'min-height':'700px', 'margin-top':'20px', 'margin-bottom':'50px', 'margin-left':'20px',
    							  		'margin-right':'20px', 'padding-top':'10px', 'background-color':'#fff'});
				
				$container.append($content);
				
			},
			error : function (xhr, ajaxOptions, thrownError) {
    				if(xhr.status == 401) { // {"error":"Unauthenticated."}
    					location.href = "{{ route('admin.dashboard') }}";
    				} else {
						alert("error: " + xhr.status + " - " + xhr.responseText);
    				}
			}
		});
	}
		
}
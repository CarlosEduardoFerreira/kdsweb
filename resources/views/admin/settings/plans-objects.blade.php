
<div id="multi-select">

	<div class="column" id="column-plans" style="float:left;">
		<div class="title">Plans</div>
			
		<div class="items">
        		@foreach($plans as $plan)
        			<label class="item" data-parent-id="column-plans" data-guid="{{$plan->guid}}">
            			{{$plan->name}}
        			</label>
        		@endforeach
		</div>
	</div>
	
	
	<div class="column column-selected" id="column-center">
		<div class="title" id="selected-title" style="color:#1C8770;">&nbsp;</div>
		
		<div class="items column-center-items">
			<div class="no-items">Click on Plan or {{$objName}}</div>
		</div>
	</div>
	
	
	<div class="column" id="column-objects" style="float:right;">
		<div class="title">{{$objName}}s</div>
			
		<div class="items">
        		@foreach($objects as $object)
        			<label class="item" data-parent-id="column-objects" data-guid="{{$object->id}}">
        				{{$object->business_name}}
        			</label>
        		@endforeach
		</div>
	</div>

</div>


<input type="hidden" id="object-name" value="{{$objName}}">

<input type="hidden" id="clicked-guid" value="">
<input type="hidden" id="clicked-type" value="">


<style>
    #multi-select { font-weight:300; font-size:16px; text-align:center; }
    #multi-select .column { width:30%; display:inline-block; vertical-align:top; }
    #multi-select .column .title { margin-bottom:20px; color:#333; letter-spacing:2px; }
    #multi-select .column .items {  overflow-y:auto; height:400px; padding:10px; }
    #multi-select .column .items .item { padding-top:5px; width:100%; height:34px; cursor:pointer; font-weight:300; font-size:16px;
                                         border:1px solid #ddd; border-radius:5px; color:#000; }
    #multi-select .column .items .no-items { margin-top:50px; }
</style>


<script>

	var isSelected = false;

	var handleClick = function() {

		isSelected = true;
		
		$element = $(this); 
		
		$columns = $("#column-plans, #column-objects");

		if($element.parent().hasClass('column-center-items')) {
        		return;
    		}
    
    		var clickedColumnId = $element.attr('data-parent-id');
        var $clickedColumn  = $('#'+clickedColumnId);
		
        // Clicked Guid and Type
    		var clickedType = 'Plan';
    		if(clickedColumnId != 'column-plans') {
    			clickedType = "{{$objName}}";
    		}
    		$('#clicked-guid').val($element.attr('data-guid'));
        $('#clicked-type').val(clickedType);

        // Turn All Plans and Objects Draggable
        	$columns.attr('class', 'column column-selected');
    		$columns.find(".items .item").each(function(){
    			$(this).removeClass('no-drag');
        	});

    		// Turn Clicked Column NOT Draggable
    		$clickedColumn.attr("class", "column");
    		$clickedColumn.find('.items .item').attr("class", "item no-drag");
    
    		setCenterColumnTitle(clickedColumnId);
        
        $('#column-center .items').html('');

		getItems("{{ route('admin.settings.plans.getItemsSelected') }}", 'column-center');
		
		if(clickedType == 'Plan') {
			getItems("{{ route('admin.settings.plans.getItemsObjects') }}", 'column-objects');
			
		} else {
			getItems("{{ route('admin.settings.plans.getItemsPlans') }}", 'column-plans');
		}

		setDragAndDrop();

		setCSS("All");
		
        $element.css({'background':'#26b999', 'color':'#fff'});
	};

	$("#column-plans, #column-objects").find('.items .item').on('click', handleClick);

    $(document).mouseup(function(e) {
        if(isSelected) {
            if (!$(".item").is(e.target) && $(".item").has(e.target).length === 0 && // if do not click on item
                    !$(".tab-a").is(e.target) && $(".tab-a").has(e.target).length === 0) { // if do not click on tabs
            		isSelected = false;
        			getItemsAll();
            		$('#selected-title').html('&nbsp;');
            		$('#clicked-guid').val('');
            }
        }
    });

	
	function setDragAndDrop() {
		// Drag and Drop
        $(".column-selected .items").sortable({
            connectWith: ".column-selected .items",
            cancel: ".no-drag",
            start: function(e, info) {
                	info.item.css({ 'background':'#fff', 'cursor':'move', 'box-shadow':'0 10px 6px 0px #ccc' });
            },
            stop: function(e, info) {
            		var dragGuid = info.item.attr('data-guid');
                $this = this;
                
                	if(!isSelected) {
                		$($this).sortable('cancel');
    					info.item.css({ 'cursor':'pointer', 'box-shadow':'none' });
                		
                	} else {
                    validPlanXObject(dragGuid, function(response) {
                        if(!response['valid']) {
            					$($this).sortable('cancel');
            					info.item.css({ 'cursor':'pointer', 'box-shadow':'none' });
            					alert(response['error']);
            					return;
                        } else {
                        		$('.no-items').remove();
                            info.item.after(info.item.find(".item"));
                            info.item.css({ 'cursor':'pointer', 'box-shadow':'none' });
                            saveObject();
                            $("#column-plans, #column-objects").find('.items .item').on('click', handleClick);
                            checkNoItems();
                        }
                    });
                	}
            }
        });
	}


	function validPlanXObject(dragGuid, callback) {
		var clickedGuid = $('#clicked-guid').val();
		var clickedType = $('#clicked-type').val();

		$.ajax({
        		headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        		url: "{{ route('admin.settings.plans.validPlanXObject') }}",
        		type: "GET",
        		data : {
        			objName: "{{$objName}}",
        			guid: clickedGuid,
        			type: clickedType,
        			dragGuid: dragGuid
        		},
        		success: function(response) {
        			callback(response);
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


	function setCenterColumnTitle(clickedColumnId) {
		if(clickedColumnId == 'column-plans') {
    			$('#selected-title').text("Selected {{$objName}}s");
        } else {
        		$('#selected-title').text("Selected Plans");
        }
	}

	
    function getItems($url, columnId, all = false) {
    		var clickedGuid = $('#clicked-guid').val();
		var clickedType = $('#clicked-type').val();

		$('#'+columnId).find('.items').html($('<img src="/images/loading2.gif" width="120px" style="margin-top:100px;">'));

        	$.ajax({
        		headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        		url: $url,
        		type: "GET",
        		data : {
        			guid: clickedGuid,
        			type: clickedType,
        			objName: "{{$objName}}",
        			all: all
        		},
        		success: function(response) {
        			setTimeout(function(){
        				if(response == '') {
        					$('#'+columnId).find('.items').html('');
            		    		checkNoItems();
            		    		
    					} else {
                			$('#'+columnId).find('.items').html(response);
    
            				if(columnId != 'column-center') {
            					$('#'+columnId).find('.items .item').on('click', handleClick);
                			}
						
                			setCSS(columnId);
    					}
        			}, Math.floor(Math.random() * Math.floor(600)));
        		},
        		error : function (xhr, ajaxOptions, thrownError) {
        			alert("Error: Please Login again. \n\nError: " + xhr.status + " - " + xhr.responseText);
        			location.href = "{{ route('admin.dashboard') }}";
        		}
        	});
    }
    

    function getItemsAll() {
    		getItems("{{ route('admin.settings.plans.getItemsPlans') }}", 'column-plans', true);
    		getItems("{{ route('admin.settings.plans.getItemsSelected') }}", 'column-center', true);
    		getItems("{{ route('admin.settings.plans.getItemsObjects') }}", 'column-objects', true);
    }

    
	function saveObject() {
		var clickedGuid = $('#clicked-guid').val();
		var clickedType = $('#clicked-type').val();

		var selectedGuids = [];
		$('.column-center-items .item').each(function(){
			selectedGuids.push($(this).attr('data-guid'));
		});
		
		$.ajax({
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
			url: "{{ route('admin.settings.plans.updateObjects') }}",
			type: "PUT",
			data : {
				guid: clickedGuid,
				type: clickedType,
    				objName: "{{$objName}}",
				selectedGuids: selectedGuids
			},
			success: function(response) {
				// Do nothing
			},
			error : function (xhr, ajaxOptions, thrownError) {
				alert("Error: Please Login again. \n\nError: " + xhr.status + " - " + xhr.responseText)
				location.href = "{{ route('admin.dashboard') }}";
			}
		});
	}


    function checkNoItems() {

        	var msg = $('#clicked-type').val() == "Plan" ? $('#object-name').val()+"s" : "Plans";
        	
    		if(!isSelected) {
    			msg = '<div class="no-items">Click on Plan or ' + $('#object-name').val() + '</div>';
    			$('#selected-title').html('&nbsp;');
    		} else {
        		msg = '<div class="no-items">Drag and drop ' + msg + ' here</div>';
        	}

		if($('#column-center .items').find('.item').length == 0) {
			$('#column-center .items').html(msg);
		}
    }

	
	function setCSS(columnId) {
		var bgColor1 = '#f2f2f2';
		var bgColor2 = '#ffffff';
		var border1  = '1px solid #aaa';
		var border2  = '1px solid #ccc';

		if(columnId == 'column-plans' || columnId == 'All') {
        		$('#column-plans .items').css({ 'background':bgColor1, 'border':border1 });
        		$('#column-plans .items .item').css({ 'background':bgColor2, 'border':border2, 'color':'#000' });
		}

		if(columnId == 'column-center' || columnId == 'All') {
			$('#column-center .items').css({ 'background':'#fcfcfc', 'border':'1px solid #26b999' });
			$('#column-center .items .item').css({ 'background':bgColor2, 'border':border2, 'color':'#000' });
		}

		if(columnId == 'column-objects' || columnId == 'All') {
        		$('#column-objects .items').css({ 'background':bgColor1, 'border':border1 });
        		$('#column-objects .items .item').css({ 'background':bgColor2, 'border':border2, 'color':'#000' });
		}
	}

	
    checkNoItems();
    
	setCSS('All');
    
</script>













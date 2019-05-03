
<?php 
    $adm = $me->hasRole('administrator');
    $res = $me->hasRole('reseller');
    $stg = $me->hasRole('storegroup');
?>

<input type="hidden" id="me-adm" value="<?=$adm?>"/>
<input type="hidden" id="me-res" value="<?=$res?>"/>
<input type="hidden" id="me-stg" value="<?=$stg?>"/>

<input type="hidden" id="plans-count" value="<?=count($plans)?>"/>
<input type="hidden" id="base-plans-count" value="<?=count($basePlans)?>"/>

<table id="table-plans" class="table table-striped table-hover">
    	<thead>
        <tr>
            <th width="20%">Plan Name</th>
            <th width="10%"><?=($adm?"Cost":"Price")?></th>
            <th width="20%">Payment Type</th>
            <th width="20%">App Name</th>
            <th width="20%">Last Update</th>
            <th width="10%">Status</th>
        </tr>
    	</thead>
    	<tbody>
    		<?php
    		foreach($plans as $plan) {
    		    $owner = $plan->owner_id == ($adm ? 0 : $me->id);
    		    
    		    $class  = $owner ? "class=\"owner\"" : "";
    		    $cursor = $owner ? "style=\"cursor:pointer;\"" : "";
    		    $icon   = $owner ? "<i class=\"edit fa fa-edit\"></i>" : "<i class=\"edit fa fa-edit\" style=\"opacity:0;\"></i>";
    		?>
            	<tr <?=$class?> data-route="{{ route('admin.settings.plans.edit', ['guid' => $plan->guid]) }}" <?=$cursor?>>
                	<td><?=$icon?>{{$plan->name}}</td>
                	<td>{{$plan->cost}}</td>
                	<td>{{$plan->payment_type}}</td>
                	<td>{{$plan->app}}</td>
                	<td>{{$plan->update_time}}</td>
                	<td>
                		@if($plan->status)
                        	<span class="label label-primary">{{ __('views.admin.users.index.active') }}</span>
                    	@else
                        	<span class="label label-danger">{{ __('views.admin.users.index.inactive') }}</span>
                    	@endif
                	</td>
            	</tr>
        	<?php } ?>
        	
        	<tr id="tbody-no-plans" style="display:none;">
    			<td colspan="6" style="text-align:center;height:150px;padding-top:50px;font-size:14px;letter-spacing:2px;">
    				There are no Plans registered.
    			</td>
    		</tr>
    		
    	</tbody>
</table>


<?php include "assets/includes/modal.default.php"; ?>

<?php include "assets/includes/modal.delete.php"; ?>

<?php include "assets/includes/modal.error.php"; ?>


<style>
    #table-plans > tbody > tr:nth-child(odd) > td, .table-striped > tbody > tr:nth-child(odd) > th { background: #fcfcfc; }
    #table-plans > tbody > tr:nth-child(even) > td, .table-striped > tbody > tr:nth-child(even) > th { background: #f3f3f3; }
    #table-plans { margin-top:20px; }
    #table-plans th { font-size:16px; font-weight:200 !important; color:#666; padding-bottom:5px; }
    #table-plans tbody tr td { font-size:14px; font-weight:200; color:#111; padding-top:10px; }
    #table-plans tbody tr td .edit { margin-right:10px; color:#326c7c; }
    #table-plans tbody tr td .color-box { display:inline-block; width:5px; height:22px; background:#326c7c; margin-right:10px; opacity:0; }
</style>



<script>

	$(function(){

    		$('#settings-btn-new').show().click(function(){
    			if($('#base-plans-count').val() == 0 && !$('#me-adm').val()) {
				var res = $('#me-res').val();
    				var oneUp  = res ? 'System Administrator' : 'Reseller';
        			
    				var error  = "There are no Base Plans registered for this <?=$me->roles[0]->display?>.";
    					error += "<br>Contact your " + oneUp + ".";
    				$('#modal-error').find('#modal-error-title').html("Error New Plan");
				$('#modal-error').find('#modal-error-body').html("<div>" + error + "</div>");
				$('#modal-error').find('#modal-error-body').css({
					'font-weight':'300',
					'font-size':'14px',
					'letter-spacing':'2px',
					'text-align':'center'
				});
				$('#modal-error').modal('show');
    				
    			} else {
    				getPlanFormModal('New', "{{ route('admin.settings.plans.new') }}");
    			}
    		});

		$('#table-plans tbody tr').each(function(){
            	$(this).hover(
            		function(){
            			$(this).css({'opacity':'0.8'});
            		},
            		function(){
            			$(this).css({'opacity':'1'});
            		}
            	);
		});

	    $('#table-plans tbody tr.owner').click(function(){
	        	getPlanFormModal('Update', $(this).attr('data-route'));
	    });

	    hideShowTabs();
	});


	function hideShowTabs() {
		var adm = $('#me-adm').val();
		var res = $('#me-res').val();
		var stg = $('#me-stg').val();

		var plansCount = $('#plans-count').val();
		
		if(plansCount == 0) {
			$('#tbody-no-plans').show();
			$('.li-plans').fadeOut('fast');
			
		} else if(adm) {
			$('#li-plans-res').fadeIn('fast');
			
		} else if(res) {
			$('#li-plans-stg').fadeIn('fast');
			
		} else if(stg) {
			$('#li-plans-str').fadeIn('fast');
		}
	}

	
	function getPlanFormModal(action = 'New', routeURL) {
		$.ajax({
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        		url: routeURL,
        		type: "GET",
        		data : {},
        		success: function(response) {
        			var $content = $(response);
        			var $footer  = $(response).find('#plans-form-footer');
        			
        			$content.find('#plans-form-footer').remove();
        			
        			$('#modal-default').find('#modal-default-title').html(action == 'New' ? "New Plan" : "Edit Plan");
        			$('#modal-default').find('#modal-default-body').html($content);
        			$('#modal-default').find('#modal-default-footer').html($footer);

        			if(action == 'New') {
        				$('#modal-default #plan-btn-delete').hide();
        				setTimeout(function(){
            				$('#modal-default #plans-form-content input#name').focus();
            			}, 500);
        			}

				$('#modal-default').modal('show');

        			if(action == 'Update') {
        				$('#modal-default #plan-btn-delete').click(function(){
        					var url = "{{ route('admin.settings.plans.deletePlan') }}";
            				var guids = [$('#modal-default #guid').val()];
            				var itemText = "Plan";
            				var extraHtml = "Be aware, <b>all Plans</b> that use this Plan as Base Plan also will be deleted.";
    
            				new ModalDelete(url, guids, itemText, extraHtml, function(error) {
            					$('#modal-delete').modal('hide');
            					setTimeout(function(){
                					if(error == '') {
                						$('#modal-default').modal('hide');
                						setTimeout(function(){
                							AdminSettings.getContent("{{ route('admin.settings.plans') }}", $('#settings-container'));
                						}, 400);
                    				} else {
        								$('#modal-error').find('#modal-error-title').html("Error Delete Plan");
    			        					$('#modal-error').find('#modal-error-body').html("<div>" + error + "</div>");
    			        					$('#modal-error').modal('show');
                    				}
            					}, 400);
            				});
        				});
        			}
        
        			$('#modal-default #plan-btn-cancel').click(function(){
        				$('#modal-default').modal('hide');
        			});
        
        			$('#modal-default #plan-btn-save').click(function(){
        				if($('#plans-form #name').val() == '' || $('#plans-form #cost').val() == '') {
						alert("Plan Name and " + $('#lbl-cost-price').text() + " are required.");
					
					} else {
            				$('#modal-default').modal('hide');
            				
            				setTimeout(function(){
            					$.ajax({
            						headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        		        				url: $('#plans-form').attr('action'),
        		        				type: "POST",
        		        				data : $('#plans-form').serialize(),
        		        				success: function(response){
        		        					AdminSettings.getContent("{{ route('admin.settings.plans') }}", $('#settings-container'));
        		        				},
        		        				error : function (xhr, ajaxOptions, thrownError) {
        		        					if(xhr.status == 401) { // {"error":"Unauthenticated."}
        		            					location.href = "{{ route('admin.dashboard') }}";
        		            				} else {
        		        						alert("error: " + xhr.status + " - " + xhr.responseText);
        		            				}
        		        				}
            					});
            				}, 400);
					}
        			});
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

	
</script>













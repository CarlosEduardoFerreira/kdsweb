
<?php 
$adm = $me->hasRole('administrator');
$res = $me->hasRole('reseller');
$stg = $me->hasRole('storegroup');

$disabled = $stg ? "disabled" : "";
?>

<div id="plans-form-content" class="row">
    <div class="col-md-12 col-sm-12 col-xs-12" style="margin-top:30px;">

    		@if(!$plan->exists)
        		{{ Form::open(['route'=>['admin.settings.plans.insert'], 'id' => 'plans-form', 'method' => 'put', 
        			'class'=>'form-horizontal form-label-left']) }}
        @else
        		{{ Form::open(['route'=>['admin.settings.plans.update', $plan->guid], 'id' => 'plans-form', 'method' => 'put', 
        			'class'=>'form-horizontal form-label-left']) }}
        	@endif
        
        	<input type="hidden" id="guid" name="guid" value="{{ $plan->guid }}">
        
        	@if(!$adm)
    			<div class="form-group">
                <label class="control-label col-md-4 col-sm-4 col-xs-12" for="base-plan" >
                    Base Plan:
                    <span class="required">*</span>
                </label>
                <div class="col-lg-6">
                    <select name="base_plan" id="base-plan" class="form-control selectpicker" required <?=$disabled?>>
                    <?php 
                    $selectedPlan = $basePlans->first();
                        foreach($basePlans as $basePlan) {
                            $selected = "";
                            if($plan->base_plan == $basePlan->guid) {
                                $selectedPlan = $basePlan;
                                $selected = "selected";
                            }
                        		?>
                        		<option value="<?=$basePlan->guid?>" data-cost="<?=$basePlan->cost?>" <?=$selected ?>> <?=$basePlan->name?></option>
                    <?php } ?>
                    </select>
                    
                    <input type="hidden" id="base_plan_hidden" name="base_plan_hidden" value="{{ $selectedPlan->guid }}">
                    
                    <div style="margin-top:8px;font-weight:200;font-size:12px;font-style:italic;letter-spacing:1px;color:#666;">
                    		<?php 
                    		if(!$stg) {
                    		    echo "Base Plan Cost: $<span id=\"base-plan-cost\">$selectedPlan->cost</span>";
                    		}
                    		?>
                    		
    				</div>
                </div>
            </div>
        @endif

        <div class="form-group" style="padding-top:10px;">
            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="name" >
            		Plan Name:
                <span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
               
                	<input id="name" name="name" type="text" class="form-control col-lg-12" value="{{ $plan->name }}" required>
            		<ul class="parsley-errors-list filled"> <li class="parsley-required"></li> </ul>
                  
            </div>
        </div> 
        
        <div class="form-group" style="padding-top:15px;">
            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="cost" >
                <span id="lbl-cost-price"><?=$adm?"Cost":"Price"?></span>:
                <span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                	<input id="cost" name="cost" type="text" class="form-control money" value="{{ $plan->cost }}" required <?=$disabled?>>
                	<input type="hidden" id="cost_hidden" name="cost_hidden" value="{{ $plan->cost }}">
            		<ul class="parsley-errors-list filled"> <li class="parsley-required"></li> </ul>
            </div>
        </div>

        <div class="form-group" style="padding-top:5px;">
            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="payment_type" >
                Payment Type:
                <span class="required">*</span>
            </label>
            <div class="col-lg-3">
                <select id="payment-type" name="payment_type" class="form-control selectpicker" data-width="auto" required <?=$disabled?>>
                <?php 
                    $typeSelected = $payment_types[0];
                    foreach($payment_types as $payment_type) {
                        $selected = "";
                        if($plan->payment_type == $payment_type->guid) {
                            $typeSelected = $payment_type;
                            $selected = "selected";
                        }
                    		?>
                    		<option value="<?=$payment_type->guid?>" <?=$selected ?>> <?=$payment_type->name?></option>
                <?php } ?>
                </select>
                <input type="hidden" id="payment_type_hidden" name="payment_type_hidden" value="{{ $typeSelected->guid }}">
                <ul class="parsley-errors-list filled"> <li class="parsley-required"></li> </ul>
            </div>
        </div>
        
        @if($adm)
            <div class="form-group" style="padding-top:10px;">
                <label class="control-label col-md-4 col-sm-4 col-xs-12" for="app" >
                    App:
                    <span class="required">*</span>
                </label>
                <div class="col-lg-3">
                    <select name="app" id="app" class="form-control selectpicker" required>
                    <?php
                        foreach($apps as $app) {
                        		$selected = $plan->app == $app->guid ? "selected" : "";
                        		?>
                        		<option value="<?=$app->guid?>" <?=$selected ?>> <?=$app->name?></option>
                    <?php } ?>
                    </select>
                    <ul class="parsley-errors-list filled"> <li class="parsley-required"></li> </ul>
                </div>
            </div>
        @endif
        
		@if($plan->exists)
            <div class="form-group">
                <label class="control-label col-md-4 col-sm-4 col-xs-12" for="active" >
                    Active:
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
					<label class="switch">
                     	<input type="checkbox" id="status" name="status" @if($plan->status) checked="checked" @endif value="1">
                     	<span class="slider round" ></span>
                    	</label>
                </div>
            </div>
        @endif
        
        @if($adm || $stg)
            <div class="form-group" style="padding-top:4px;">
                <label class="control-label col-md-4 col-sm-4 col-xs-12" for="default" >
                    Default:
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
    				<label class="switch">
                     	<input type="checkbox" id="default" name="default" value="1" @if($plan->default) checked="checked" @endif>
                     	<span class="slider round" ></span>
                    	</label>
                    	<?php 
                        	$child = $me->hasRole('administrator') ? "Reseller" : ($me->hasRole('reseller') ? "Store Group" : "Store");
                    	?>
                    	<div style="float:right;margin-top:9px;margin-right:50px;font-weight:200;font-size:12px;font-style:italic;color:#999;">
                    		For new <?=$child?>s created.
    					</div>
                </div>
            </div>
        @endif

        <div id="plans-form-footer" class="col-lg-12">
        		@if($plan->exists)
    			<div class="col-lg-6" style="font-size:12px;text-align:left;font-style:italic;color:#aaa;">
				Last updated on <?=$plan->update_time?> 
				<br>
				by <?=$plan->update_user?>
			</div>
			@else
				<div class="col-lg-6" style="font-size:12px;text-align:left;font-style:italic;color:#aaa;"><br></div>
			@endif
			<div class="col-lg-6">
				<?php 
				    if( !$stg &&
				        $plan->guid != '0f10c86c-a8ed-42dd-9318-b13d692f435c' &&
				        $plan->guid != '66217032-ef6e-4f9f-ab1c-5ea0b70bffa6' &&
				        $plan->guid != '68a511d7-660a-4380-8d6a-c8f040485eb5') {
				?>
					<button class="btn btn-danger" id="plan-btn-delete" style="margin-right:15px;">Delete</button>
				<?php } ?>
                	<button class="btn btn-primary" id="plan-btn-cancel" style="margin-right:15px;">Cancel</button>
                	<button class="btn btn-success" id="plan-btn-save">Save</button>
            	</div>
        </div>
        
        {{ Form::close() }}
    </div>
</div>


<style>
    #plans-form-content { margin-bottom:20px; }
    
    #plans-form-content .form-group label,
    #plans-form-content .form-group input,
    #plans-form-content .bootstrap-select button,
    #plans-form-content .bootstrap-select ul li { font-size:16px; font-weight:300; letter-spacing:1px; }
    
    #plans-form-content .form-group input { border-radius:5px; width:280px; }
    #plans-form-content .bootstrap-select button { border-radius:5px; width:280px; }
    #plans-form-content .bootstrap-select ul li { border-radius:5px; width:280px; font-size:14px; }
    #plans-form-content #cost { width:150px; text-align:center; }
</style>


<script>
    $('#plans-form-content #cost').mask('000,000,000,000,000.00', {reverse: true});
    
    $('#plans-form-content .selectpicker').selectpicker('refresh');

    
    $('#base-plan').change(function(){
        var cost = $(this).children("option:selected").attr('data-cost');
		$('#base-plan-cost').text(cost);
	});
    
    $(document).ready(function()
                {
                  $("#cost").attr('maxlength','10');
                  $("#name").attr('maxlength','30');
                 });
</script>










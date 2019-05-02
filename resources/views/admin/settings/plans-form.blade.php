
<?php 
$adm = $me->hasRole('administrator');
$res = $me->hasRole('reseller');
$stg = $me->hasRole('storegroup');
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

        <div class="form-group">
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
            		Cost:
                <span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                	<input id="cost" name="cost" type="text" class="form-control col-md-7 col-xs-12" value="{{ $plan->cost }}" required>
            		<ul class="parsley-errors-list filled"> <li class="parsley-required"></li> </ul>
            </div>
        </div>

        <div class="form-group" style="padding-top:15px;">
            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="payment_type" >
                Payment Type:
                <span class="required">*</span>
            </label>
            <div class="col-lg-3">
                <select id="payment-type" name="payment_type" class="form-control selectpicker" data-width="auto" required>
                <?php if(isset($payment_types)) { ?>
                    @foreach($payment_types as $payment_type)
                    		<?php 
                    		$selected = $plan->payment_type == $payment_type->guid ? "selected" : "";
                    		?>
                    		<option value="{{$payment_type->guid}}" <?=$selected ?>> {{$payment_type->name}}</option>
                    @endforeach
                <?php } ?>
                </select>
                <ul class="parsley-errors-list filled"> <li class="parsley-required"></li> </ul>
            </div>
        </div>
        
        <div class="form-group" style="padding-top:4px;">
            <label class="control-label col-md-4 col-sm-4 col-xs-12" for="app" >
                App:
                <span class="required">*</span>
            </label>
            <div class="col-lg-3">
<!--                 		<input type="hidden" id="app-edit" value="{{ $plan->app }}"> -->
                <select name="app" id="app" class="form-control selectpicker" required>
                <?php if(isset($apps)) { ?>
                    @foreach($apps as $app)
                    		<?php 
                    		$selected = $plan->app == $app->guid ? "selected" : "";
                    		?>
                    		<option value="{{$app->guid}}" <?=$selected ?>> {{$app->name}}</option>
                    @endforeach
                <?php } ?>
                </select>
                <ul class="parsley-errors-list filled"> <li class="parsley-required"></li> </ul>
            </div>
        </div>
        
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
        
        @if($adm || $res)
            <div class="form-group" style="padding-top:4px;">
                <label class="control-label col-md-4 col-sm-4 col-xs-12" for="default" >
                    Default:
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
    				<label class="switch">
                     	<input type="checkbox" id="default" name="default" @if($plan->default) checked="checked" @endif value="1">
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
				<button class="btn btn-danger" id="plan-btn-delete" style="margin-right:15px;">Delete</button>
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
</script>










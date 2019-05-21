@extends('admin.layouts.admin')

<?php if ($obj == 'store') { ?>
    @if($user->exists)
    		@section('title',"Edit Store" )
    @else
    		@section('title',"New Store" )
    @endif
<?php } elseif ($obj == 'storegroup') { ?>
	@if($user->exists)
    		@section('title',"Edit Store Group" )
    @else
    		@section('title',"New Store Group" )
    @endif
<?php } elseif ($obj == 'reseller') { ?>
	@if($user->exists)
    		@section('title',"Edit Reseller" )
    @else
    		@section('title',"New Reseller" )
    @endif
<?php } ?>

@section('content')
    <div class="row" style="min-height:1100px;">
        <div class="col-md-12 col-sm-12 col-xs-12">
        		<?php if ($obj == 'store') { ?>
            		@if($user->exists)
                		{{ Form::open(['route'=>['admin.stores.update', $user->id], 'id' => 'main-form', 'method' => 'put','class'=>'form-horizontal form-label-left']) }}
                @else
                		{{ Form::open(['route'=>['admin.stores.insert'], 'id' => 'main-form', 'method' => 'put', 'class'=>'form-horizontal form-label-left']) }}
                @endif
            <?php } elseif ($obj == 'storegroup') { ?>
                @if($user->exists)
                		{{ Form::open(['route'=>['admin.storegroups.update', $user->id], 'id' => 'main-form', 'method' => 'put','class'=>'form-horizontal form-label-left']) }}
                @else
                		{{ Form::open(['route'=>['admin.storegroups.insert'], 'id' => 'main-form', 'method' => 'put','class'=>'form-horizontal form-label-left']) }}
                @endif
            <?php } elseif ($obj == 'reseller') { ?>
            		@if($user->exists)
                		{{ Form::open(['route'=>['admin.resellers.update', $user->id], 'id' => 'main-form', 'method' => 'put','class'=>'form-horizontal form-label-left']) }}
                @else
                		{{ Form::open(['route'=>['admin.resellers.insert'], 'id' => 'main-form', 'method' => 'put','class'=>'form-horizontal form-label-left']) }}
                @endif
            <?php } ?>
            
            		<input type="hidden" id="user_id" value="<?=$user->id?>">
            		<input type="hidden" id="user_obj" value="<?=$obj?>">
            
            <div class="form-group">
                <?php if ($me->roles[0]->id == 2 && $obj == 'storegroup') { ?>
                        <input type="hidden" id="parent_id" name="parent_id" value="<?=$me->id?>">
                			
                <?php } else if ($me->roles[0]->id == 3 && $obj == 'store') { ?>
                        <input type="hidden" id="parent_id" name="parent_id" value="<?=$me->id?>">
                        
                <?php } else if ($obj != 'reseller' && $me->id != $user->id) { ?>
                    
                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="parent_id">
                        <?php if ($obj == 'store') { ?>
                        		Store Group:
                        		<span class="required">*</span>
                        <?php } elseif ($obj == 'storegroup') { ?>
                        		Reseller:
                        		<span class="required">*</span>
                        <?php } ?>
                        </label>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <select id="parent_id" name="parent_id" class="form-control select2" style="width: 100%" required>
                            		<option></option>
                                	@foreach ($parents as $parent)
                                		<?php 
                                		$selected = $user->parent_id == $parent->id ? "selected" : "";
                                		?>
                                    	<option value="{{ $parent->id }}" <?=$selected?>>{{ $parent->business_name }}</option>
                                	@endforeach;
                            </select>
                            <ul class="parsley-errors-list filled"> <li class="parsley-required"></li> </ul>
                        </div>
                    
                <?php } else { ?>
                			<input type="hidden" id="parent_id" name="parent_id" value="<?=$user->parent_id?>">
                <?php } ?>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="business_name" >
                		<?php if ($obj == 'store') { ?>
                    		Legal Business Name:
                    <?php } elseif ($obj == 'storegroup') { ?>
                    		Store Group Name:
                    <?php } elseif ($obj == 'reseller') { ?>
                    		Reseller Name:
                    <?php } ?>
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="business_name" name="business_name" type="text" class="form-control col-md-7 col-xs-12" value="{{ $user->business_name }}" required>
                		<ul class="parsley-errors-list filled"> <li class="parsley-required"></li> </ul>
                </div>
            </div>

			<?php if ($obj == 'store') { ?>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="dba" >
                        		DBA: (Doing business as)
                        <span class="required">*</span>
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="dba" name="dba" type="text" class="form-control col-md-7 col-xs-12" value="{{ $user->dba }}" required>
                        <ul class="parsley-errors-list filled"> <li class="parsley-required"></li> </ul>
                    </div>
                </div>
    
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last_name" >
                        Last Name:
                        <span class="required">*</span>
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                         <input id="last_name" name="last_name" type="text" class="form-control col-md-7 col-xs-12" value="{{ $user->last_name }}" required>
                    		<ul class="parsley-errors-list filled"> <li class="parsley-required"></li> </ul>
                    </div>
                </div>
            <?php } ?>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name" >
                    <?php if ($obj == 'store') { ?>
                    		First Name:
                    <?php } elseif ($obj == 'storegroup') { ?>
                    		Contact Name:
                    <?php } elseif ($obj == 'reseller') { ?>
                    		Contact Name:
                    <?php } ?>
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="name" name="name" type="text" class="form-control col-md-7 col-xs-12" value="{{ $user->name }}" required>
                    <ul class="parsley-errors-list filled"> <li class="parsley-required"></li> </ul>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="email">
                    {{ __('views.admin.users.edit.email') }}
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="email" name="email" type="email" class="form-control col-md-7 col-xs-12" value="{{ $user->email }}" required>
                    <ul class="parsley-errors-list filled"> <li class="parsley-required"></li> </ul>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="phone_number" >
                    Contact Phone Number:
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="phone_number" name="phone_number" type="text" class="form-control col-md-7 col-xs-12" value="{{ $user->phone_number }}" required>
                		<ul class="parsley-errors-list filled"> <li class="parsley-required"></li> </ul>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="address" >
                    Address:
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="address" name="address" type="text" class="form-control col-md-7 col-xs-12" value="{{ $user->address }}" required>
                    <ul class="parsley-errors-list filled"> <li class="parsley-required"></li> </ul>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="address2" >
                    Address 2:
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="address2" name="address2" type="text" class="form-control col-md-7 col-xs-12" value="{{ $user->address2 }}">
                    <ul class="parsley-errors-list filled"> <li class="parsley-required"></li> </ul>
                </div>
            </div>

             <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="city" >
                    City:
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="city" name="city" type="text" class="form-control col-md-7 col-xs-12" value="{{ $user->city }}" required>
                    <ul class="parsley-errors-list filled"> <li class="parsley-required"></li> </ul>
                </div>
            </div>
            
             <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="state" >
                    State:
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                		<input type="hidden" id="state-edit" value="{{$user->state}}">
                    <select name="state" id="state" class="form-control" style="width:350px" required>
                    <?php if(isset($states)) { ?>
                        @foreach($states as $state)
                        		<?php 
                        		$selected = $user->state == $state->id ? "selected" : "";
                        		?>
                        		<option value="{{$state->id}}" <?=$selected ?>> {{$state->name}}</option>
                        @endforeach
                    <?php } ?>
                    </select>
                    <ul class="parsley-errors-list filled"> <li class="parsley-required"></li> </ul>
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="zipcode" >
                    Zip Code:
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="zipcode" name="zipcode" type="text" class="form-control col-md-7 col-xs-12" value="{{ $user->zipcode }}" required>
                    <ul class="parsley-errors-list filled"> <li class="parsley-required"></li> </ul>
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="country" >
                    Country:
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                		<select id="country" name="country" class="form-control" style="width:350px;" required>
                		<option></option>
                    @foreach($countries as $country)
                    		<?php 
                    		$selected = $user->country == $country->id ? "selected" : "";
                    		?>
                    		<option value="{{$country->id}}" <?=$selected ?>>{{$country->name}}</option>
                    @endforeach
                    </select>
                    <ul class="parsley-errors-list filled"> <li class="parsley-required"></li> </ul>
                </div>
            </div>
            
            <?php if ($obj == 'store') { ?>
            <div class="form-group">
            		<label class="control-label col-md-3 col-sm-3 col-xs-12" for="timezone" >
                    Timezone:
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                		<select id="timezone" name="timezone" class="form-control" style="width:350px;" required>
                		<?php 
                		$timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL); 
                		
                		foreach($timezones as $timezone) { 
                			$selected = $user->timezone == $timezone ? "selected" : "";
                    		?>
                    		<option value="<?=$timezone?>" <?=$selected ?>><?=$timezone?></option>
                		<?php } ?>
                		</select>
                </div>
			</div>
			<?php } ?>
			
			
            	<?php if ($obj == 'store') { ?>
            
            		<hr class="separator-1" />
            
                <div class="form-group" style="margin-top:30px;margin-bottom:20px;">
                		<label class="control-label col-md-3 col-sm-3 col-xs-12" for="user_apps">
                			App:
                			<span class="required">*</span>
                		</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <select id="user_apps" name="user_apps" class="selectpicker">
                        		<option></option>
                        		<?php
                        		foreach ($apps as $app) {
                            		$selected = $app->guid == $app_guid ? "selected" : "";
                            		?>
                                	<option value="<?=$app->guid?>" <?=$selected?>><?=$app->name?></option>
                            	<?php } ?>
                        </select>
                    </div>
                </div>
            
                <div class="form-group" style="margin-bottom:20px ;">
                		<label class="control-label col-md-3 col-sm-3 col-xs-12" for="user_envs">
                			Type:
                			<span class="required">*</span>
                		</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <select id="user_envs" name="user_envs" class="selectpicker">
                        		<option></option>
                        		<?php 
                        		foreach ($envs as $env) {
                            		$selected = $env->guid == $env_guid ? "selected" : "";
                            		?>
                                	<option value="<?=$env->guid?>" <?=$selected?>><?=$env->name?></option>
                            	<?php } ?>
                        </select>
                    </div>
                </div>
                
                <hr class="separator-1"/>
                
            <?php } ?>
			
			@if($user->exists)
                <div class="form-group" >
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="active" >
                        {{ __('views.admin.users.edit.active') }}
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="checkbox">
                            <label>
                                <input id="active" name="active" type="checkbox" @if($user->active) checked="checked" @endif value="1">
                            </label>
                        </div>
                    </div>
                </div>
            @endif

			<?php if ($obj == 'store') { ?>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="username" >
                        Username:
                        <span class="required">*</span>
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="username" name="username" type="text" class="form-control col-md-7 col-xs-12" value="{{ $user->username }}" required>
                        <ul class="parsley-errors-list filled"> <li class="parsley-required"></li> </ul>
                    </div>
                </div>
            <?php } ?>
			
			<?php 
			     $required = !isset($user->id) ? "required" : "";
			?>
			
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="password">
                    {{ __('views.admin.users.edit.password') }}
                	<span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="password" name="password" type="password" class="form-control col-md-7 col-xs-12" <?=$required?>>
                    <ul class="parsley-errors-list filled"> <li class="parsley-required"></li> </ul>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="password_confirmation">
                    {{ __('views.admin.users.edit.confirm_password') }}
                	<span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control col-md-7 col-xs-12">
                    <ul class="parsley-errors-list filled"> <li class="parsley-required"></li> </ul>
                </div>
            </div>

            <div class="form-group" style="text-align:right;padding-top:50px;padding-bottom:100px;">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <a class="btn btn-danger remove-store pull-left" href="#"
                       store_guid="{{$user->store_guid}}"
                       data-toggle="modal" data-target="#modalRemoveStore" data-title="Delete Store"
                       data-placement="top">Delete Store</a>
                    <a class="btn btn-primary" href="{{ URL::previous() }}" style="margin-right:50px;"> {{ __('views.admin.users.edit.cancel') }}</a>
                    <button id="btn-save-form" type="button" class="btn btn-success" obj="<?=$obj?>" edit="<?=$user->exists?>"> {{ __('views.admin.users.edit.save') }}</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
    
    
{{-- Modal Error ---------------------------------------------------------------------------------------------------- --}}
<div class="modal fade" id="modal-error" tabindex="-1" role="dialog" aria-labelledby="modalError" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        	<div class="modal-content">
        		<div class="modal-header">
        			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
            			<span aria-hidden="true">&times;</span>
            		</button>
        			<h5 class="modal-title">Error</h5>
        		</div>
        		<div class="modal-body">
        			{{-- Error message --}}
        		</div>
    			<div class="modal-footer">
    				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    			</div>
        	</div>
    </div>
</div>
{{-- ---------------------------------------------------------------------------------------------------- Modal Error --}}


{{-- Modal Remove Store -------------------------------------------------------------------------------------------- --}}
<div class="modal fade" id="modalRemoveStore" tabindex="-1" role="dialog" aria-labelledby="modalRemoveStore" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close hide-loading" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="modalLongTitle">Delete Store</h5>
            </div>
            <div id="are-you-sure" class="modal-body hide-loading">
            </div>
            <div class="modal-body hide-loading">
                <input id="confirm_remove_store" type="text" class="form-control">
            </div>
            <div id="loading" class="modal-body" style="display:none;height:100px;font-size:14px;text-align:center;">
                Deleting... <img src="/images/loading.gif" style="width:100px;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary hide-loading" data-dismiss="modal">Close</button>
                <button id="btn-remove-store-confirm" type="button" class="btn btn-danger hide-loading" disabled>Delete</button>
            </div>
        </div>
    </div>
</div>
{{-- -------------------------------------------------------------------------------------------- Modal Remove Store --}}
    
    
@endsection



@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/users/edit.css')) }}
    {{ Html::style(mix('assets/admin/css/bootstrap-select.css')) }}
    <style>
        .required { color:red; }
        hr.separator-1 { border:none; width:100%; height:20px; border-bottom:1px solid #C5CAE9; 
                    box-shadow:0 10px 10px -10px #9FA8DA;  }
    </style>
@endsection

@section('scripts')

    @parent
    {{ Html::script(mix('assets/admin/js/users/edit.js')) }}
    {{ Html::script(mix('assets/admin/js/location.js')) }}
    {{ Html::script(mix('assets/admin/js/validation.js')) }}
    {{ Html::script(mix('assets/admin/js/bootstrap-select.min.js')) }}

    {{ Html::script(mix('assets/admin/js/firebase-api.js')) }}

    <script>
        var token = { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        var storeToRemoveGuid = "";

        $('.remove-store').click(function(){
            storeToRemoveGuid = $(this).attr('store_guid');
            var storeName = $('#business_name').val();

            $('#modalRemoveStore #are-you-sure').html('If you are sure you want to delete the store ' +
                '\"<span style="color:red;">' + storeName +  '\</span>". Please write "delete" on the bellow field.')

            $('#confirm_remove_store').val("");
            $("#btn-remove-store-confirm").prop("disabled", true);
        });

        $('#btn-remove-store-confirm').click(function(){
            if(storeToRemoveGuid !== "") {
                $('#modalRemoveStore .hide-loading').hide();
                $('#modalRemoveStore #loading').show();

                $.ajax({
                    headers: token,
                    url: 'removeStore',
                    type: 'POST',
                    data: {
                        storeToRemoveGuid: storeToRemoveGuid
                    },
                    success: function (response) {
                        if(response !== "") {
                            console.log(response);
                            alert(response);

                        } else {
                            sendNotificationToFirebase();

                            setTimeout(function(){ window.location.href = "/admin/stores/0"; }, 3000);
                        }
                    }
                });
            }
        });

        $('#confirm_remove_store').on('input',function(e){
            $("#btn-remove-store-confirm").prop("disabled", e.currentTarget.value.toLowerCase() !== "delete");
        });
    </script>
@endsection










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
    <div class="row" style="min-height:750px;">
        <div class="col-md-12 col-sm-12 col-xs-12">
        		<?php if ($obj == 'store') { ?>
            		@if($user->exists)
                		{{ Form::open(['route'=>['admin.stores.update', $user->id], 'id' => 'form-store', 'method' => 'put','class'=>'form-horizontal form-label-left']) }}
                @else
                		{{ Form::open(['route'=>['admin.stores.insert'], 'id' => 'form-store', 'method' => 'put', 'class'=>'form-horizontal form-label-left']) }}
                @endif
            <?php } elseif ($obj == 'storegroup') { ?>
                @if($user->exists)
                		{{ Form::open(['route'=>['admin.storegroups.update', $user->id],'method' => 'put','class'=>'form-horizontal form-label-left']) }}
                @else
                		{{ Form::open(['route'=>['admin.storegroups.insert'],'method' => 'put','class'=>'form-horizontal form-label-left']) }}
                @endif
            <?php } elseif ($obj == 'reseller') { ?>
            		@if($user->exists)
                		{{ Form::open(['route'=>['admin.resellers.update', $user->id],'method' => 'put','class'=>'form-horizontal form-label-left']) }}
                @else
                		{{ Form::open(['route'=>['admin.resellers.insert'],'method' => 'put','class'=>'form-horizontal form-label-left']) }}
                @endif
            <?php } ?>
            
            <?php if ($me->roles[0]->id == 2 && $obj == 'storegroup') { ?>
                    <input type="hidden" id="parent_id" name="parent_id" value="<?=$me->id?>">
            
            <?php } else if ($me->roles[0]->id == 3 && $obj == 'store') { ?>
                    <input type="hidden" id="parent_id" name="parent_id" value="<?=$me->id?>">
                    
            <?php } else if ($obj != 'reseller') { ?>
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="parent_id">
                    <?php if ($obj == 'store') { ?>
                    		Store Group:
                    <?php } elseif ($obj == 'storegroup') { ?>
                    		Reseller:
                    <?php } ?>
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <select id="parent_id" name="parent_id" class="form-control select2" style="width: 100%" required>
                        		<option></option>
                            	@foreach ($parents as $parent)
                            		<?php 
                            		$selected = $user->parent_id == $parent->id ? "selected" : "";
                            		?>
                                	<option value="{{ $parent->id }}" <?=$selected?>>{{ $parent->name }}</option>
                            	@endforeach;
                        </select>
                        @if($errors->has('parent_id'))
                            <ul class="parsley-errors-list filled">
                                @foreach($errors->get('parent_id') as $error)
                                        <li class="parsley-required">{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            <?php } ?>

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
                    </div>
                </div>
    
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last_name" >
                        Last Name:
                        <span class="required">*</span>
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="last_name" name="last_name" type="text" class="form-control col-md-7 col-xs-12" value="{{ $user->last_name }}" required>
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
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="email">
                    {{ __('views.admin.users.edit.email') }}
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="email" name="email" type="email" class="form-control col-md-7 col-xs-12" value="{{ $user->email }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="phone_number" >
                    Contact Phone Number:
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="phone_number" name="phone_number" type="text" class="form-control col-md-7 col-xs-12" value="{{ $user->phone_number }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="address" >
                    Address:
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="address" name="address" type="text" class="form-control col-md-7 col-xs-12" value="{{ $user->address }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="address2" >
                    Address 2:
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="address2" name="address2" type="text" class="form-control col-md-7 col-xs-12" value="{{ $user->address2 }}">
                </div>
            </div>

             <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="city" >
                    City:
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="city" name="city" type="text" class="form-control col-md-7 col-xs-12" value="{{ $user->city }}" required>
                </div>
            </div>
            
             <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="state" >
                    State:
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                
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
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="zipcode" >
                    Zip Code:
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="zipcode" name="zipcode" type="text" class="form-control col-md-7 col-xs-12" value="{{ $user->zipcode }}" required>
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
                    		<option value="{{$country->id}}" <?=$selected ?>> {{$country->name}}</option>
                    @endforeach
                    </select>
                </div>
            </div>

           

			@if($user->exists)
                <div class="form-group">
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
                    </div>
                </div>
            <?php } ?>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="password">
                    {{ __('views.admin.users.edit.password') }}
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="password" name="password" type="password" class="form-control col-md-7 col-xs-12">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="password_confirmation">
                    {{ __('views.admin.users.edit.confirm_password') }}
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control col-md-7 col-xs-12">
                </div>
            </div>

                <div class="form-group" style="text-align:right;padding-top:20px;">
                    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                        <a class="btn btn-primary" href="{{ URL::previous() }}" style="margin-right:50px;"> {{ __('views.admin.users.edit.cancel') }}</a>
                        <button id="btn-save-form" type="submit" class="btn btn-success"> {{ __('views.admin.users.edit.save') }}</button>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection

@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/users/edit.css')) }}
    <style>
        .required { color:red; }
    </style>
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/users/edit.js')) }}
    {{ Html::script(mix('assets/admin/js/location.js')) }}
    {{ Html::script(mix('assets/admin/js/validation.js')) }}
@endsection

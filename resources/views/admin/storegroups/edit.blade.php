@extends('admin.layouts.admin')

@section('title',"Edit Store Group" )

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            {{ Form::open(['route'=>['admin.storegroups.update', $storegroup->id],'method' => 'put','class'=>'form-horizontal form-label-left']) }}
            
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="reseller_id">
                    Reseller:
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <select id="reseller_id" name="reseller_id" class="form-control select2" style="width: 100%" autocomplete="off">
                    		<option value="0">Select the Reseller</option>
                        	@foreach ($resellers as $reseller)
                        		<?php 
                        		$selected = $storegroup->parent_id == $reseller->id ? "selected" : "";
                        		?>
                            	<option value="{{ $reseller->id }}" <?=$selected?>>{{ $reseller->name }}</option>
                        	@endforeach;
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="business_name" >
                    Store Group Name:
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="business_name" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('business_name')) parsley-error @endif"
                           name="business_name" value="{{ $storegroup->business_name }}" required>
                    @if($errors->has('business_name'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('business_name') as $error)
                                    <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="name" >
                    Contact Name:
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="name" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('name')) parsley-error @endif"
                           name="name" value="{{ $storegroup->name }}" required>
                    @if($errors->has('name'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('name') as $error)
                                    <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="email">
                    {{ __('views.admin.users.edit.email') }}
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="email" type="email" class="form-control col-md-7 col-xs-12 @if($errors->has('email')) parsley-error @endif"
                           name="email" value="{{ $storegroup->email }}" required>
                    @if($errors->has('email'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('email') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="phone_number" >
                    Contact Phone Number:
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="phone_number" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('phone_number')) parsley-error @endif"
                           name="phone_number" value="{{ $storegroup->phone_number }}" required>
                    @if($errors->has('phone_number'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('phone_number') as $error)
                                    <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="address" >
                    Address:
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="address" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('address')) parsley-error @endif"
                           name="address" value="{{ $storegroup->address }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="address2" >
                    Address 2:
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="address2" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('address2')) parsley-error @endif"
                           name="address2" value="{{ $storegroup->address2 }}">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="country" >
                    Country:
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                
                		<select id="country" name="country" class="form-control" style="width:350px;">
                    @foreach($countries as $country)
                    		<?php 
                    		  $selected = $storegroup->country == $country->id ? "selected" : "";
                    		?>
                    		<option value="{{$country->id}}" <?=$selected ?>> {{$country->name}}</option>
                    @endforeach
                    </select>

                    @if($errors->has('country'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('country') as $error)
                                    <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="state" >
                    State:
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                
                    <select name="state" id="state" class="form-control" style="width:350px">
                    @foreach($states as $state)
                    		<?php 
                    		  $selected = $storegroup->state == $state->id ? "selected" : "";
                    		?>
                    		<option value="{{$state->id}}" <?=$selected ?>> {{$state->name}}</option>
                    @endforeach
                    </select>

                    @if($errors->has('state'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('state') as $error)
                                    <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="city" >
                    City:
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                
                    <select name="city" id="city" class="form-control" style="width:350px">
                    @foreach($cities as $city)
                    		<?php 
                    		$selected = $storegroup->city == $city->id ? "selected" : "";
                    		?>
                    		<option value="{{$city->id}}" <?=$selected ?>> {{$city->name}}</option>
                    @endforeach
                    </select>
                    
                    @if($errors->has('city'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('city') as $error)
                                    <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="zipcode" >
                    Zip Code:
                    <span class="required">*</span>
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="zipcode" type="text" class="form-control col-md-7 col-xs-12 @if($errors->has('zipcode')) parsley-error @endif"
                           name="zipcode" value="{{ $storegroup->zipcode }}" required>
                    @if($errors->has('zipcode'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('zipcode') as $error)
                                    <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            @if(!$storegroup->hasRole('administrator'))

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="active" >
                        {{ __('views.admin.users.edit.active') }}
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="checkbox">
                            <label>
                                <input id="active" type="checkbox" class="@if($errors->has('active')) parsley-error @endif"
                                       name="active" @if($storegroup->active) checked="checked" @endif value="1">
                                @if($errors->has('active'))
                                    <ul class="parsley-errors-list filled">
                                        @foreach($errors->get('active') as $error)
                                            <li class="parsley-required">{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </label>
                        </div>
                    </div>
                </div>
                
            @endif

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="password">
                    {{ __('views.admin.users.edit.password') }}
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="password" type="password" class="form-control col-md-7 col-xs-12 @if($errors->has('password')) parsley-error @endif"
                           name="password">
                    @if($errors->has('password'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('password') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="password_confirmation">
                    {{ __('views.admin.users.edit.confirm_password') }}
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="password_confirmation" type="password" class="form-control col-md-7 col-xs-12 @if($errors->has('password_confirmation')) parsley-error @endif"
                           name="password_confirmation">
                    @if($errors->has('password_confirmation'))
                        <ul class="parsley-errors-list filled">
                            @foreach($errors->get('password_confirmation') as $error)
                                <li class="parsley-required">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <a class="btn btn-primary" href="{{ URL::previous() }}"> {{ __('views.admin.users.edit.cancel') }}</a>
                    <button type="submit" class="btn btn-success"> {{ __('views.admin.users.edit.save') }}</button>
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
@endsection





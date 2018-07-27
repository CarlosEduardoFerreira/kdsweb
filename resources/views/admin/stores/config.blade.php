<?php
use SebastianBergmann\CodeCoverage\Report\PHP;
?>
@extends('admin.layouts.config_base')

@section('title',"Store Settings" )

<!-- ******************************** settings ******************************** -->

@section('settings')

    <div class="row" style="width:100%;min-height:700px;">
		
        <div class="col-md-12 col-sm-12 col-xs-12">
            {{ Form::open(['route'=>['admin.stores.updateSettings', $store->id], 'id' => 'form-settings', 'method' => 'put','class'=>'form-horizontal form-label-left']) }}
            
                <?php 
                    $server_address  = isset($settings->server_address)  ? $settings->server_address : "";
                    $server_username = isset($settings->server_username) ? $settings->server_username : "";
                    $server_password = isset($settings->server_password) ? $settings->server_password : "";
                    $socket_port     = isset($settings->socket_port) ? $settings->socket_port : "1111";
                    
                    $auto_done_order_hourly = isset($settings->auto_done_order_hourly) ? $settings->auto_done_order_hourly : "0";
                    $auto_done_order_time   = isset($settings->auto_done_order_time) ? $settings->auto_done_order_time : "0";
                    
                    $timezone    = isset($settings->timezone) ? $settings->timezone : "America/New_York";
                    $smart_order = isset($settings->smart_order) ? $settings->smart_order : "0";
                    
                    $licenses_quantity = isset($settings->licenses_quantity) ? $settings->licenses_quantity : "0"; 
                ?>
    			
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="server_address" >
                        Server Address:
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="server_address" name="server_address" type="text"
                        value="{{ $server_address }}" class="form-control col-md-7 col-xs-12">
                    </div>
                </div>
    
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="server_username" >
                        Server Username:
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="server_username" name="server_username" type="text"
                        value="{{ $server_username }}" class="form-control col-md-7 col-xs-12">
                    </div>
                </div>
    
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="server_password" >
                        Server Password:
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="server_password" name="server_password" type="password"
                        value="{{ $server_password }}" class="form-control col-md-7 col-xs-12">
                    </div>
                </div>
    
                <div class="divider" style="width:50%;margin:auto;margin-top:20px;margin-bottom:20px;"></div>
    
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="socket_port" >
                        Local Sync Socket Port:
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="socket_port" name="socket_port" type="number" style="width:100px;display:inline;text-align:center;"
                        value="{{ $socket_port }}" class="form-control" required>
                    </div>
                </div>
    
                <div class="divider" style="width:50%;margin:auto;margin-top:20px;margin-bottom:20px;"></div>
    
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="auto_bump_type" >
                        Automatic Bump Time:
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                    		<?php 
                        		$kdsTime = new DateTime();
                        		$kdsTime->setTimezone(new DateTimeZone($timezone));
                        		$kdsTime->setTimestamp($auto_done_order_time);
                        		$hours24 = $kdsTime->format('H:i');
                        		$hours = $kdsTime->format('H');
                        		$minutes = $kdsTime->format('i');
                        		$ampm = $kdsTime->format('A');
                        		$amSelected = $ampm == "AM" ? "selected" : "";
                        		$pmSelected = $ampm == "PM" ? "selected" : "";
                    		?>
                    		<style>
                    		  .time-tds { padding:20px; padding-top:0px; padding-bottom:0px;}
                    		  .time-tds-time { padding-right:5px; }
                    		  .time-tds-ampm { padding-left:5px; }
                    		</style>
                    		<table>
                    			<tr>
                    				<td class="time-tds">
                    					Daily: &nbsp; {{ Form::radio('auto_done_order_hourly', 0, true) }}
                    				</td>
                    				<td class="time-tds">
                    					Hourly: &nbsp; {{ Form::radio('auto_done_order_hourly', 1, $auto_done_order_hourly) }}
                    				</td>
                    				<td class="time-tds time-tds-time">
                    					<input type="hidden" name="auto_done_order_time" value="<?=$hours24?>">
                    					<input id="auto_done_order_time_field" name="auto_done_order_time_field" type="text" 
                    						onClick="this.setSelectionRange(0, this.value.length)" value="<?=($hours.":".$minutes)?>" 
                    						class="form-control" maxlength="5" style="width:70px;text-align:center;" required>
                    				</td>
                    				<td  class="time-tds time-tds-ampm">
                                		<select id="auto_done_order_time_ampm" name="auto_done_order_time_ampm" style="width:50px;height:30px;">
                                			<option <?=$amSelected?>>AM</option>
                                			<option <?=$pmSelected?>>PM</option>
                                		</select>
                    				</td>
                    			</tr>
                    		</table>
                    		<ul class="parsley-errors-list filled" style="padding-left:220px;padding-top:5px;height:20px;"> 
                    			<li id="error-time" class="parsley-required" style="display:none;">Invalid time or format.</li> 
                    		</ul>
                    </div>
                </div> <!-- End <div class="form-group"> -->
    
                <div class="divider" style="width:50%;margin:auto;margin-bottom:20px;"></div>
    
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="smart_order" >
                        Smart Order:
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <?php
                            $sel  = $smart_order ? ["","selected"] : ["selected",""];
                        ?>
                        <select style="width:60px;height:30px;" name="smart_order">
                            <option value="0" <?=$sel[0]?>>No</option>
                            <option value="1" <?=$sel[1]?>>Yes</option>
                       </select>
                    </div>
                </div>
    
                <div class="divider" style="width:50%;margin:auto;margin-top:20px;margin-bottom:20px;"></div>
    
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="licenses_quantity" >
                        Licenses Quantity:
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="licenses_quantity" name="licenses_quantity" type="number" style="width:100px;display:inline;text-align:center;"
                        value="{{ $licenses_quantity }}" class="form-control" required>
                    </div>
                </div>
            
                <div class="form-group" style="margin-bottom:100px;">
                    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3" style="text-align:right;">
                        <!-- <button type="submit" class="btn btn-success"> {{ __('views.admin.users.edit.save') }}</button> -->
                        <button id="btn-save-settings" type="button" class="btn btn-success">{{ __('views.admin.users.edit.save') }}</button>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>
@endsection

<!-- ******************************** devices ******************************** -->

@section('devices')
<div class="row" style="min-height:700px;">
    <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
           width="100%">
        <thead>
        <?php 
            $currentPage = 1;
            
            if($devices != []) {
                $currentPage = $devices->currentPage();
            }
        ?>
        <tr>
            <th>ID</th>
            <th>KDS Station Name</th>
            <th>Serial Number</th>
            <th>Function</th>
            <th>Parent ID</th>
            <th>Expeditor</th>
            <th>Last Update</th>
            <th>License</th>
        </tr>
        </thead>
        <tbody>

        @foreach($devices as $device)
            <tr>
                	<td class="td-data" style="vertical-align:middle;text-align:center;">{{ $device->id }}</td>
                	<td class="td-data" style="vertical-align:middle;">{{ $device->name}}</td>
                	<td class="td-data" style="vertical-align:middle;">{{ explode('-', $device->serial)[0] }}</td>
                	<td class="td-data" style="vertical-align:middle;">{{ $device->function }}</td>
                	<td class="td-data" style="vertical-align:middle;">{{ $device->parent_id == 0 ? "" : $device->parent_id }}</td>
                	<td class="td-data" style="vertical-align:middle;">{{ $device->expeditor }}</td>
				<td class="td-data" style="vertical-align:middle;">
				<?php 
        				$date = new DateTime();
        				$date->setTimestamp($device->update_time);
				?>
				{{ $date->format('m/d/Y H:i:s') }}
				</td>
                	<td class="td-data" style="vertical-align:middle;text-align:center;">
                		<?php if ($device->split_screen_parent_device_id == 0) { ?>
                		    <label class="switch">
                          <input class="device-license-login" guid="{{ $device->guid }}" type="checkbox" 
                          									@if($device->license) checked="checked" @endif value="1">
                          <span class="slider round"></span>
                        </label>
                		<?php } ?>
                	</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>


<!-- Button trigger modal -->
<button id="modal-btn" type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalCenter" style="display:none;"></button>

<!-- Modal -->
<div class="modal fade" id="modalCenter" tabindex="-1" role="dialog" aria-labelledby="modalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        	<div class="modal-content">
        		<div class="modal-header">
        			<h5 class="modal-title" id="modalLongTitle">Title</h5>
        		</div>
        		<div class="modal-body">
        			Message
        		</div>
    			<div class="modal-footer">
    				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
    			</div>
        	</div>
    </div>
</div>


@endsection



<!-- ******************************** market_place ******************************** -->

@section('marketplace')

<div class="row" id="mp-list" style="min-height:700px;">

	<div class="col-md-12 col-sm-12 col-xs-12">
        <div id="mp-buttons">
        
            	<div class="mp-button">
            		<span class="mp-b-helper"></span>
            		<img alt="Twilio" src="{{URL::asset('assets/img/twilio_logo.png')}}">
            		<div id="mp-b-footer">
            			<a href="http://www.twilio.com" target="_blank">
            				<span id="more">More</span>
            			</a>
            			<a href="javascript:showTwilio()">
            				<span id="add">Add</span>
            			</a>
            		</div>
            	</div>
            	
        </div>
    </div>
	
</div>


<div class="row" id="mp-twilio" style="min-height:700px;display:none;">
    <div class="col-md-12 col-sm-12 col-xs-12">
    
        <?php 
            $sms_start_enable      = isset($settings->sms_start_enable)  ? $settings->sms_start_enable : false;
            $sms_start_use_default = isset($settings->sms_start_use_default) ? $settings->sms_start_use_default : false;
            $sms_start_custom      = isset($settings->sms_start_custom) ? $settings->sms_start_custom : "";
            
            $sms_done_enable       = isset($settings->sms_done_enable)  ? $settings->sms_done_enable : false;
            $sms_done_use_default  = isset($settings->sms_done_use_default) ? $settings->sms_done_use_default : false;
            $sms_done_custom       = isset($settings->sms_done_custom) ? $settings->sms_done_custom : "";
        ?>
    
        {{ Form::open(['route'=>['admin.stores.updateSettings', $store->id], 'id' => 'form-market-place', 'method' => 'put','class'=>'form-horizontal form-label-left']) }}
        
        		<div style="margin-left:10px;padding-bottom:20px;">
        			<i class="fa fa-toggle-left" style="color:#999;"></i>
        			<a href="javascript:showMarketplaceList();" class="a-back-mp" >Back to Marketplace</a>
        		</div>
        		
        		<div class="mp-title" style="margin-left:100px;">
        			<img alt="Twilio" src="{{URL::asset('assets/img/twilio_logo.png')}}" style="width:150px;">
        		</div>
        		
        		<div class="divider" style="width:50%;margin:auto;margin-top:10px;margin-bottom:20px;"></div>
        
            <div class="form-group">
                	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="sms_start_enable" >
                    Order Started Message:
                	</label>
                	<div class="col-md-4 col-sm-4 col-xs-4">
        				<label class="switch">
        					<input class="switch-mp" type="checkbox" @if($sms_start_enable) checked="checked" @endif value="1">
        					<span class="slider round"></span>
        				</label>
				</div>
				<div>
					<label style="padding-right:20px;">Default</label>
					<label class="switch">
        					<input class="switch-mp" type="checkbox" @if(!$sms_start_use_default) checked="checked" @endif value="1">
        					<span class="slider round"></span>
        				</label>
        				<label style="padding-left:20px;">Custom</label>
        			</div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sms_start_custom">
                    Message:
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="sms_start_custom" name="sms_start_custom" type="text" style="display:inline-table;" 
                    		value="{{ $sms_start_custom }}" class="form-control col-md-8 col-xs-8">
                </div>
            </div>
            
            <div class="divider" style="width:50%;margin:auto;margin-top:20px;margin-bottom:20px;"></div>
            
            <div class="form-group">
                	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="sms_done_enable" >
                    Order Completed Message:
                	</label>
                	<div class="col-md-4 col-sm-4 col-xs-4">
        				<label class="switch">
        					<input class="switch-mp" type="checkbox" @if($sms_done_enable) checked="checked" @endif value="1">
        					<span class="slider round"></span>
        				</label>
				</div>
				<div>
					<label style="padding-right:20px;">Default</label>
					<label class="switch">
        					<input class="switch-mp" type="checkbox" @if(!$sms_done_use_default) checked="checked" @endif value="1">
        					<span class="slider round"></span>
        				</label>
        				<label style="padding-left:20px;">Custom</label>
        			</div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sms_done_custom" >
                    Message:
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="sms_done_custom" name="sms_done_custom" type="text" style="display:inline-table;" 
                    		value="{{ $sms_done_custom }}" class="form-control col-md-8 col-xs-8">
                </div>
            </div>
            
            <div class="divider" style="width:50%;margin:auto;margin-top:20px;margin-bottom:20px;"></div>
    		{{ Form::close() }}
	</div>
</div>

@endsection



@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/users/edit.css')) }}
    <style>
        .radio-bump { display:inline-table; width:100px; }
        .radio-bump-time { display:inline-table; width:120px; text-align:right; text-align:center; }
        
        .td-data { height:30px; }
        
        .a-back-mp { text-decoration:underline; margin-left:4px; color:#999; }
        
        /** market place buttons ****************************************************/
        #mp-buttons                                 { width:900px; margin: 0 auto; }
        #mp-buttons .mp-button                      { width:180px; height:160px; background:#fefefe; text-align:center; 
            white-space: nowrap; margin: 1em 0;
            border:1px solid #ccc; border-radius: 25px; box-shadow: 5px 5px 5px rgba(0, 0, 0, .3); }
        #mp-buttons .mp-button .mp-b-helper         { display: inline-block; height: 75%; vertical-align: middle; }
        #mp-buttons .mp-button #mp-b-footer         { height:25%; border-radius: 0px 0px 25px 25px;  }
        #mp-buttons .mp-button #mp-b-footer a span  { display:inline-table; height:38px; padding-top:5px; box-shadow: 0 -3px 3px -3px rgba(0, 0, 0, .3);
             font-family:Helvetica Neue; font-size:16px; font-weight:200; border:1px solid #ccc; color:#fff; }
        #mp-buttons .mp-button #mp-b-footer a:hover span { color:#666; }
        #mp-buttons .mp-button #mp-b-footer #more   { width:85px; border-radius: 0px 0px 0px 25px; background:#75b5f0; margin-left:1px;  }
        #mp-buttons .mp-button #mp-b-footer #add    { width:85px; border-radius: 0px 0px 25px 0px; background:#1ABB9C; }
        #mp-buttons .mp-button img                  { width:160px; vertical-align: middle; }
        /**************************************************** market place buttons **/
        
        /** switch ******************************************************************/
        /* The switch - the box around the slider */
        .switch { top:5px; position: relative; display: inline-block; width: 40px; height: 22px; }
        /* Hide default HTML checkbox */
        .switch input {display:none;}
        /* The slider */
        .switch .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; 
            background-color: #ccc; -webkit-transition: .4s; transition: .4s; }
        .switch .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 2px;
            bottom: 2px; background-color: white; -webkit-transition: .4s; transition: .4s; }
        .switch input:checked + .slider { background-color: #2196F3; }
        .switch input:focus + .slider { box-shadow: 0 0 1px #2196F3; }
        .switch input:checked + .slider:before { -webkit-transform: translateX(18px); 
            -ms-transform: translateX(18px); transform: translateX(18px); }
        /* Rounded sliders */
        .switch .slider.round { border-radius: 34px; }
        .switch .slider.round:before { border-radius: 50%; }
        /****************************************************************** switch **/
    </style>
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/users/edit.js')) }}
    {{ Html::script(mix('assets/admin/js/validation_config.js')) }}
    <script>
    function showTwilio() {
		$('#mp-list').hide();
		$('#mp-twilio').fadeIn();
	}
	function showMarketplaceList() {
		$('#mp-twilio').hide();
		$('#mp-list').fadeIn();
	}
    $(document).ready(function(){
    		
    });
    </script>
    
@endsection






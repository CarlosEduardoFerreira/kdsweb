@extends('admin.layouts.config_base')

@section('title',"Store Settings" )

<!-- ******************************** settings ******************************** -->

@section('settings')

    <div class="row" style="width:100%;min-height:700px;">

        <div class="col-md-12 col-sm-12 col-xs-12">
            {{ Form::open(['route'=>['admin.stores.updateSettings', $store->id],'method' => 'put','class'=>'form-horizontal form-label-left']) }}
            
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
                        <span class="radio-bump" name="auto_done_order_hourly">
                            Daily: &nbsp; {{ Form::radio('auto_done_order_hourly', 0, true) }}
                        </span>
                        <span class="radio-bump">
                            Hourly: &nbsp; {{ Form::radio('auto_done_order_hourly', 1, $auto_done_order_hourly) }}
                        </span>
                        <span class="radio-bump-time">
                            <select style="width:80px;height:30px;" name="auto_done_order_time">
                            <?php
                                $kdsTime = new DateTime();
                                $kdsTime->setTimezone(new DateTimeZone($timezone));
                                $kdsTime->setTimestamp($auto_done_order_time);
                                $kdsTime = $kdsTime->format('H:i');
                                $selected = "";
                                $found = false;
                                for($hours=0; $hours<24; $hours++) {
                                    for($mins=0; $mins<60; $mins+=30) {
                                        $optionTime = str_pad($hours,2,'0',STR_PAD_LEFT).':'.str_pad($mins,2,'0',STR_PAD_LEFT);
                                        if($kdsTime == $optionTime) {
                                            $selected = "selected";
                                            $found = true;
                                        } else {
                                            $selected = "";
                                        }
                                        echo "<option $selected>$optionTime</option>";
                                    }
                                }
                                if(!$found) {
                                    echo "<option selected>$kdsTime</option>";
                                }
                               ?>
                           </select>
                        </span>
                    </div>
                </div>
    
                <div class="divider" style="width:50%;margin:auto;margin-top:20px;margin-bottom:20px;"></div>
    
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
                        <button type="submit" class="btn btn-success"> {{ __('views.admin.users.edit.save') }}</button>
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


@section('styles')
    @parent
    {{ Html::style(mix('assets/admin/css/users/edit.css')) }}
    <style>
        .radio-bump { display:inline-table; width:100px; }
        .radio-bump-time { display:inline-table; width:120px; text-align:right; }
        
        .td-data { height:30px; }
        
        /* The switch - the box around the slider */
        .switch {
            top:5px;
            position: relative;
            display: inline-block;
            width: 40px;
            height: 22px;
        }
        
        /* Hide default HTML checkbox */
        .switch input {display:none;}
        
        /* The slider */
        .switch .slider {
          position: absolute;
          cursor: pointer;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background-color: #ccc;
          -webkit-transition: .4s;
          transition: .4s;
        }
        
        .switch .slider:before {
          position: absolute;
          content: "";
          height: 18px;
          width: 18px;
          left: 2px;
          bottom: 2px;
          background-color: white;
          -webkit-transition: .4s;
          transition: .4s;
        }
        
        .switch input:checked + .slider {
          background-color: #2196F3;
        }
        
        .switch input:focus + .slider {
          box-shadow: 0 0 1px #2196F3;
        }
        
        .switch input:checked + .slider:before {
          -webkit-transform: translateX(18px);
          -ms-transform: translateX(18px);
          transform: translateX(18px);
        }
        
        /* Rounded sliders */
        .switch .slider.round {
          border-radius: 34px;
        }
        
        .switch .slider.round:before {
          border-radius: 50%;
        }
    </style>
@endsection

@section('scripts')
    @parent
    {{ Html::script(mix('assets/admin/js/users/edit.js')) }}
@endsection

<div class="row" style="width:100%;min-height:700px;">
	
    <div class="col-md-12 col-sm-12 col-xs-12">
        {{ Form::open(['route'=>['admin.stores.updateSettings', $store->id], 'id' => 'form-settings', 'method' => 'put','class'=>'form-horizontal form-label-left']) }}
        
            <?php
                $store_key       = isset($settings->store_key) ? $settings->store_key : "";
                $server_address  = isset($settings->server_address)  ? $settings->server_address : "";
                $server_username = isset($settings->server_username) ? $settings->server_username : "";
                $server_password = isset($settings->server_password) ? $settings->server_password : "";
                $socket_port     = isset($settings->socket_port) ? $settings->socket_port : "1111";
                
                $auto_done_order_hourly = isset($settings->auto_done_order_hourly) ? $settings->auto_done_order_hourly : "0";
                $auto_done_order_time   = isset($settings->auto_done_order_time) ? $settings->auto_done_order_time : "0";
                
                $smart_order = isset($settings->smart_order) ? $settings->smart_order : "0";
                
                $licenses_quantity = isset($settings->licenses_quantity) ? $settings->licenses_quantity : "0";
            ?>
            
            <input type="hidden" id="store-guid" name="store-guid" value="{{$store->store_guid}}"/>

            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="server_address" >
                    Store Key:
                </label>
                <div class="col-md-4 col-sm-4 col-xs-11">
                    <input id="store_key" name="store_key" type="text"
                           value="{{ $store_key }}" class="form-control col-md-7 col-xs-12" readonly>
                </div>
                <div class="col-md-2 col-sm-2 col-xs-1">
                    <button id="btn-reset-key" type="button" class="btn btn-success">{{ __('views.admin.users.edit.reset') }}</button>
                </div>
            </div>
			
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
                    		$kdsTime->setTimezone(new DateTimeZone($store->timezone));
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
                    <input id="licenses_quantity" name="licenses_quantity" type="number" min="0" style="width:100px;display:inline;text-align:center;"
                    value="{{ $licenses_quantity }}" class="form-control" required>
                </div>
            </div>
        
            <div class="form-group" style="margin-top:30px;margin-bottom:100px;">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3" style="text-align:right;">
                    <!-- <button type="submit" class="btn btn-success"> {{ __('views.admin.users.edit.save') }}</button> -->
                    <button id="btn-save-settings" type="button" class="btn btn-success">{{ __('views.admin.users.edit.save') }}</button>
                </div>
            </div>
        {{ Form::close() }}
    </div>
</div>
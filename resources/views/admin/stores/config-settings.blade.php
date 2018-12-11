

<div class="row" id="store-settings-container">

	<div class="col-md-3 col-sm-3 col-xs-3" id="store-settings-menu">
	
		<div class="store-settings-toggle" id="store-settings-server-toggle">
            	<a data-toggle="collapse" data-parent="#store-settings-container" href="#store-settings-server">
            		Server
            	</a>
            	<span class="glyphicon"></span>
        	</div>
        	
        	<div class="store-settings-toggle" id="store-settings-smartorder-toggle">
            	<a data-toggle="collapse" data-parent="#store-settings-container" href="#store-settings-smartorder">
            		Smart Order
            	</a>
            	<span class="glyphicon"></span>
        	</div>
        	
        	<div class="store-settings-toggle" id="store-settings-bump-toggle">
            	<a data-toggle="collapse" data-parent="#store-settings-container" href="#store-settings-bump">
            		Automatic Bump
            	</a>
            	<span class="glyphicon"></span>
        	</div>
        	
        	<div class="store-settings-toggle" id="store-settings-licenses-toggle">
            	<a data-toggle="collapse" data-parent="#store-settings-container" href="#store-settings-licenses">
            		Licenses
            	</a>
            	<span class="glyphicon"></span>
        	</div>
        	
        	<div class="store-settings-toggle" id="store-settings-sync-toggle">
            	<a data-toggle="collapse" data-parent="#store-settings-container" href="#store-settings-sync">
            		Local Sync
            	</a>
            	<span class="glyphicon"></span>
        	</div>
        	
        	<div class="store-settings-toggle" id="store-settings-key-toggle">
            	<a data-toggle="collapse" data-parent="#store-settings-container" href="#store-settings-key">
            		Store Key
            	</a>
            	<span class="glyphicon"></span>
        	</div>
        	
	</div>
	
    <div class="col-md-9 col-sm-9 col-xs-9" id="store-settings-panel">
    
        {{ Form::open(['route'=>['admin.stores.updateSettings', $store->id], 'id' => 'form-settings', 
        					'method' => 'put','class'=>'form-horizontal form-label-left']) }}
        
            <?php
                $store_key       = isset($settings->store_key) ? $settings->store_key : "";
                $server_address  = isset($settings->server_address)  ? $settings->server_address : "";
                $server_username = isset($settings->server_username) ? $settings->server_username : "";
                $server_password = isset($settings->server_password) ? $settings->server_password : "";
                $socket_port     = isset($settings->socket_port) ? $settings->socket_port : "1111";
                
                $auto_done_order_hourly = isset($settings->auto_done_order_hourly) ? $settings->auto_done_order_hourly : "0";
                $auto_done_order_time   = isset($settings->auto_done_order_time) ? $settings->auto_done_order_time : "0";
                
                $smart_order = isset($settings->smart_order) ? $settings->smart_order : "0";
                $smart_order_hide_mode = isset($settings->smart_order_hide_mode) ? $settings->smart_order_hide_mode : "0";
                $smart_order_with_start = isset($settings->smart_order_with_start) ? $settings->smart_order_with_start : "0";
                
                $licenses_quantity = isset($settings->licenses_quantity) ? $settings->licenses_quantity : "0";
            ?>
            
            	<input type="hidden" id="store-guid" name="store-guid" value="{{$store->store_guid}}"/>
            	
            	{{-- Store Settings Server -------------------------------------------------------------------------- --}}
			<div id="store-settings-server" class="panel-collapse collapse" toggle="store-settings-server-toggle">

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
                
            	</div>
            	{{-- -------------------------------------------------------------------------- Store Settings Server --}}
			
			
			{{-- Store Settings Smart Order --------------------------------------------------------------------- --}}
			<div id="store-settings-smartorder" class="panel-collapse collapse" toggle="store-settings-smartorder-toggle">
			
				<div class="form-group">
				
        				<label class="control-label col-md-3 col-sm-3 col-xs-12">
        					Smart Order Enable:
        				</label>
                    	<div class="col-md-6 col-sm-6 col-xs-12">
                    		<label class="switch">
                         	<input type="checkbox" id="smart_order" name="smart_order" 
                         		class="device-settings-feature-enable" <?php echo $smart_order ? 'checked' : ''; ?> >
                         	<span class="slider round device-settings-switch-slider" ></span>
                        </label>
                    	</div>
                    	
                </div>
                
                <div class="form-group">
                    	
                    	<label class="control-label col-md-3 col-sm-3 col-xs-12 smart_order_functions">
        					Hide Items:
        				</label>
                    	<div class="col-md-6 col-sm-6 col-xs-12 smart_order_functions">
                    		<label class="switch">
                         	<input type="checkbox" id="smart_order_hide_mode" name="smart_order_hide_mode" 
                         		class="device-settings-feature-enable" <?php echo $smart_order_hide_mode ? 'checked' : ''; ?> >
                         	<span class="slider round device-settings-switch-slider" ></span>
                        </label>
                    	</div>
                </div>
                
                <div class="form-group">
                    	
                    	<label class="control-label col-md-3 col-sm-3 col-xs-12 smart_order_functions">
        					Start Button:
        				</label>
                    	<div class="col-md-6 col-sm-6 col-xs-12 smart_order_functions">
                    		<label class="switch">
                         	<input type="checkbox" id="smart_order_with_start" name="smart_order_with_start" 
                         		class="device-settings-feature-enable" <?php echo $smart_order_with_start ? 'checked' : ''; ?> >
                         	<span class="slider round device-settings-switch-slider" ></span>
                        </label>
                    	</div>
                	
                	</div>

            </div>
            {{-- --------------------------------------------------------------------- Store Settings Smart Order --}}


			{{-- Store Settings Auto Bump ----------------------------------------------------------------------- --}}
			<div id="store-settings-bump" class="panel-collapse collapse" toggle="store-settings-bump-toggle">
			
                	<div class="form-group">
                	
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="auto_bump_type" >
                        Repeat:
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
                    					Daily:
                    				</td>
                    				<td>
                    					{{ Form::radio('auto_done_order_hourly', 0, true) }}
                    				</td>
                    				<td class="time-tds" style="padding-left:50px;">
                    					Hourly:
                    				</td>
                    				<td>
                    					{{ Form::radio('auto_done_order_hourly', 1, $auto_done_order_hourly) }}
                    				</td>
                    			</tr>
                    		</table>
                    </div>
                    
                </div>
                
                
                	<div class="form-group" style="margin-top:20px;">
                	
                		<label class="control-label col-md-3 col-sm-3 col-xs-12" for="auto_bump_type" >
                        Time:
                    </label>
                    
                		<table>
                			<tr>
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
                	
            
            </div>
            {{-- ----------------------------------------------------------------------- Store Settings Auto Bump --}}


			{{-- Store Settings Licenses Quantity --------------------------------------------------------------- --}}
			<div id="store-settings-licenses" class="panel-collapse collapse" toggle="store-settings-licenses-toggle">

                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="licenses_quantity" >
                        Licenses Quantity:
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="licenses_quantity" name="licenses_quantity" type="number" min="0" style="width:100px;display:inline;text-align:center;"
                        value="{{ $licenses_quantity }}" class="form-control" required>
                    </div>
                </div>
            
            </div>
            {{-- --------------------------------------------------------------- Store Settings Licenses Quantity --}}
            
            
			{{-- Store Settings Local Sync ---------------------------------------------------------------------- --}}
			<div id="store-settings-sync" class="panel-collapse collapse" toggle="store-settings-sync-toggle">
			
                	<div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="socket_port" >
                        Local Sync Socket Port:
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="socket_port" name="socket_port" type="number" 
                        style="width:100px;display:inline;text-align:center;"
                        value="{{ $socket_port }}" class="form-control" required>
                    </div>
                	</div>
            	
            	</div>
			{{-- ---------------------------------------------------------------------- Store Settings Local Sync --}}
        
        
			{{-- Store Settings Key ----------------------------------------------------------------------------- --}}
			<div id="store-settings-key" class="panel-collapse collapse" toggle="store-settings-key-toggle">
			
            		<div class="form-group">
                    	<label class="control-label col-md-3 col-sm-3 col-xs-12" for="store_key" >
                        	Store Key:
                    	</label>
                    	<div class="col-md-4 col-sm-4 col-xs-11">
                        	<input id="store_key" name="store_key" type="text"
                               	value="{{ $store_key }}" class="form-control col-md-7 col-xs-12" readonly>
                    	</div>
                    	<div class="col-md-2 col-sm-2 col-xs-1">
                    		<button id="btn-reset-key" type="button" 
                    			class="btn btn-success">{{ __('views.admin.users.edit.reset') }}</button>
                    	</div>
                	</div>
                	
            	</div>
            	{{-- ----------------------------------------------------------------------------- Store Settings Key --}}
            	
            	
        {{ Form::close() }}
        
        <div id="store-settings-commit-buttons">
        		<button id="btn-save-settings" type="button" class="btn btn-success">{{ __('views.admin.users.edit.save') }}</button>
        </div>
        
    </div>
    
    
    
	
	
</div>





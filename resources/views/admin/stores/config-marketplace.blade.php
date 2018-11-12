<div class="row" id="mp-list" style="min-height:700px;">

	<div class="col-md-12 col-sm-12 col-xs-12">
        <div id="mp-buttons">
        
            	<div class="mp-button">
            		<span class="mp-b-helper"></span>
            		<img alt="Twilio" src="/images/twilio_logo.png">
            		<div id="mp-b-footer">
            			<a href="http://www.twilio.com" target="_blank">
            				<span id="more">More</span>
            			</a>
            			<a href="javascript:showTwilio()"> 
            				<span id="add">Edit</span>
            			</a>
            		</div>
            	</div>
            	  
        </div>
    </div>
	
</div>


<div class="row" id="mp-twilio" style="min-height:900px;display:none;">
    <div class="col-md-12 col-sm-12 col-xs-12">
    
        <?php 

            $sms_start_enable      = isset($settings->sms_start_enable)  ? $settings->sms_start_enable : 0;
            $sms_start_use_default = isset($settings->sms_start_use_default) ? $settings->sms_start_use_default : 1;
            $sms_start_custom      = isset($settings->sms_start_custom) ? $settings->sms_start_custom : "";
            
            $sms_ready_enable       = isset($settings->sms_ready_enable)  ? $settings->sms_ready_enable : 0;
            $sms_ready_use_default  = isset($settings->sms_ready_use_default) ? $settings->sms_ready_use_default : 1;
            $sms_ready_custom       = isset($settings->sms_ready_custom) ? $settings->sms_ready_custom : "";
            
            $sms_done_enable       = isset($settings->sms_done_enable)  ? $settings->sms_done_enable : 0;
            $sms_done_use_default  = isset($settings->sms_done_use_default) ? $settings->sms_done_use_default : 1;
            $sms_done_custom       = isset($settings->sms_done_custom) ? $settings->sms_done_custom : "";
            
            $sms_start_use_default = $sms_start_enable == 1 ? $sms_start_use_default : 1;
            $sms_ready_use_default = $sms_ready_enable == 1 ? $sms_ready_use_default : 1;
            $sms_done_use_default  = $sms_done_enable  == 1 ? $sms_done_use_default  : 1;

            $storeNameKey = "[STORE_NAME]";
            $storeNameValue = $store->business_name;
            $adminSettings->sms_order_start_message = str_replace($storeNameKey, $storeNameValue,
                $adminSettings->sms_order_start_message);

            $adminSettings->sms_order_ready_message = str_replace($storeNameKey, $storeNameValue,
                $adminSettings->sms_order_ready_message);

            $adminSettings->sms_order_done_message = str_replace($storeNameKey, $storeNameValue,
                $adminSettings->sms_order_done_message);

            $start_message = $sms_start_use_default == 1 ? $adminSettings->sms_order_start_message : $sms_start_custom;
            $ready_message = $sms_ready_use_default == 1 ? $adminSettings->sms_order_ready_message : $sms_ready_custom;
            $done_message  = $sms_done_use_default == 1  ? $adminSettings->sms_order_done_message : $sms_done_custom;
            
            $styleCustomStart = $sms_start_enable == 1 ? "" : "display:none;";
            $styleCustomReady = $sms_ready_enable == 1 ? "" : "display:none;";
            $styleCustomDone  = $sms_done_enable  == 1 ? "" : "display:none;";
            
            $styleMessageStart = $sms_start_enable == 1 ? "opacity:1;" : "opacity:0.3;";
            $styleMessageReady = $sms_ready_enable == 1 ? "opacity:1;" : "opacity:0.3;";
            $styleMessageDone  = $sms_done_enable  == 1 ? "opacity:1;" : "opacity:0.3;";
            
            //echo "sms_start_use_default: " . $sms_start_use_default;
        ?>
    
        {{ Form::open(['route'=>['admin.stores.updateTwilio', $store->id], 'id' => 'form-market-place', 'method' => 'put','class'=>'form-horizontal form-label-left']) }}
        
        		<div style="margin-left:10px;padding-bottom:20px;">
        			<i class="fa fa-toggle-left" style="color:#999;"></i>
        			<a href="javascript:showMarketplaceList();" class="a-back-mp" >Back to Marketplace</a>
        		</div>
        		
        		<div class="mp-title" style="margin-left:100px;margin-bottom:20px;">
        			<img alt="Twilio" src="/images/twilio_logo.png" style="width:150px;">
        		</div>
        		
        		<div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">
                    Account SID:
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12 ">
                    <input type="text" class="form-control col-md-8 col-xs-8" id="sms_account_sid" name="sms_account_sid" 
                    		value="{{ $settings->sms_account_sid }}" style="display:inline-table;">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">
                    Token:
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12 ">
                    <input type="text" class="form-control col-md-8 col-xs-8" id="sms_token" name="sms_token" 
                    		value="{{ $settings->sms_token }}" style="display:inline-table;">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">
                    Phone From:
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12 ">
                    <input type="text" class="form-control col-md-8 col-xs-8" id="sms_phone_from" name="sms_phone_from" 
                    		value="{{ $settings->sms_phone_from }}" style="display:inline-table;">
                </div>
            </div>
        		
        		<div class="divider" style="width:50%;margin:auto;margin-top:10px;margin-bottom:20px;"></div>
        
            <div class="form-group">
                	<label class="lbl-enable control-label col-md-3 col-sm-3 col-xs-12" for="sms_start_enable" >
                    Order Received Message:
                	</label>
                	<div class="col-md-4 col-sm-4 col-xs-4">
        				<label class="switch">
        					<input  type="checkbox" name="sms_start_enable" class="switch-mp switch-mp-enable"
        						config_switch="config-switch-start" config_msg="config-msg-start"
        						@if($sms_start_enable == 1) checked="checked" @endif value="1">
        					<span class="slider round"></span>
        				</label>
				</div>
				<div class="config-switch-start" style="<?=$styleCustomStart?>">
					<label style="padding-right:20px;">Default</label>
					<label class="switch">
        					<input type="checkbox" name="sms_start_use_default" class="switch-mp switch-mp-use-custom" 
        						input_message_id="sms_start_custom"
        						@if($sms_start_use_default == 0) checked="checked" @endif>
        					<span class="slider round"></span>
        				</label>
        				<label style="padding-left:20px;">Custom</label>
        			</div>
            </div>
            <div class="form-group config-msg-start" style="<?=$styleMessageStart?>">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sms_start_custom">
                    Message:
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12 ">
                    <input id="sms_start_custom" name="sms_start_custom" type="text" style="display:inline-table;" 
                        sms_default="{{ $adminSettings->sms_order_start_message }}" sms_custom="{{ $sms_start_custom }}" value="{{ $start_message }}" 
                        class="form-control col-md-8 col-xs-8" <?=$sms_start_use_default ? 'disabled' : '';?>>

					<button id="btn-start-add-store-name-key" type="button" class="btn btn-success" style="margin-top: 10px"><?php echo e(__('views.admin.users.edit.add_store_name')); ?></button>
					<button id="btn-start-add-customer-name-key" type="button" class="btn btn-success" style="margin-top: 10px"><?php echo e(__('views.admin.users.edit.add_customer_name')); ?></button>
					<button id="btn-start-add-order-id-key" type="button" class="btn btn-success" style="margin-top: 10px"><?php echo e(__('views.admin.users.edit.add_order_id')); ?></button>
                </div>
            </div>

            <div class="divider" style="width:50%;margin:auto;margin-top:20px;margin-bottom:20px;"></div>
            
            <div class="form-group">
                	<label class="lbl-enable control-label col-md-3 col-sm-3 col-xs-12" for="sms_ready_enable" >
                    Order Complete Message:
                	</label>
                	<div class="col-md-4 col-sm-4 col-xs-4">
        				<label class="switch">
        					<input type="checkbox" name="sms_ready_enable" class="switch-mp switch-mp-enable" 
        						config_switch="config-switch-ready" config_msg="config-msg-ready"
        						@if($sms_ready_enable == 1) checked="checked" @endif value="1">
        					<span class="slider round"></span>
        				</label>
				</div>
				<div class="config-switch-ready" style="<?=$styleCustomReady?>">
					<label style="padding-right:20px;">Default</label>
					<label class="switch">
        					<input type="checkbox" name="sms_ready_use_default" class="switch-mp switch-mp-use-custom"
        						input_message_id="sms_ready_custom"
        						@if($sms_ready_use_default == 0) checked="checked" @endif>
        					<span class="slider round"></span>
        				</label>
        				<label style="padding-left:20px;">Custom</label>
        			</div>
            </div>
            <div class="form-group config-msg-ready" style="<?=$styleMessageReady?>">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sms_ready_custom" >
                    Message:
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="sms_ready_custom" name="sms_ready_custom" type="text" style="display:inline-table;" 
                    		sms_default="{{ $adminSettings->sms_order_ready_message }}" sms_custom="{{ $sms_ready_custom }}" value="{{ $ready_message }}" 
                    		class="form-control col-md-8 col-xs-8" <?=$sms_ready_use_default ? 'disabled' : '';?>>

					<button id="btn-ready-add-store-name-key" type="button" class="btn btn-success" style="margin-top: 10px"><?php echo e(__('views.admin.users.edit.add_store_name')); ?></button>
					<button id="btn-ready-add-customer-name-key" type="button" class="btn btn-success" style="margin-top: 10px"><?php echo e(__('views.admin.users.edit.add_customer_name')); ?></button>
					<button id="btn-ready-add-order-id-key" type="button" class="btn btn-success" style="margin-top: 10px"><?php echo e(__('views.admin.users.edit.add_order_id')); ?></button>
                </div>
            </div>
            
            <div class="divider" style="width:50%;margin:auto;margin-top:20px;margin-bottom:20px;"></div>
            
            <div class="form-group">
                	<label class="lbl-enable control-label col-md-3 col-sm-3 col-xs-12" for="sms_done_enable" >
                    Ready For Pickup Message:
                	</label>
                	<div class="col-md-4 col-sm-4 col-xs-4">
        				<label class="switch">
        					<input type="checkbox" name="sms_done_enable" class="switch-mp switch-mp-enable"  
        						config_switch="config-switch-done" config_msg="config-msg-done"
        						@if($sms_done_enable == 1) checked="checked" @endif value="1">
        					<span class="slider round"></span>
        				</label>
				</div>
				<div class="config-switch-done" style="<?=$styleCustomDone?>">
					<label style="padding-right:20px;">Default</label>
					<label class="switch">
        					<input type="checkbox" name="sms_done_use_default" class="switch-mp switch-mp-use-custom" 
        						input_message_id="sms_done_custom"
        						@if($sms_done_use_default == 0) checked="checked" @endif>
        					<span class="slider round"></span>
        				</label>
        				<label style="padding-left:20px;">Custom</label>
        			</div>
            </div>
            <div class="form-group config-msg-done" style="<?=$styleMessageDone?>">
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sms_done_custom" >
                    Message:
                </label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <input id="sms_done_custom" name="sms_done_custom" type="text" style="display:inline-table;" 
                    		sms_default="{{ $adminSettings->sms_order_done_message }}" sms_custom="{{ $sms_done_custom }}" value="{{ $done_message }}" 
                    		class="form-control col-md-8 col-xs-8" <?=$sms_done_use_default ? 'disabled' : '';?>>

					<button id="btn-done-add-store-name-key" type="button" class="btn btn-success" style="margin-top: 10px"><?php echo e(__('views.admin.users.edit.add_store_name')); ?></button>
					<button id="btn-done-add-customer-name-key" type="button" class="btn btn-success" style="margin-top: 10px"><?php echo e(__('views.admin.users.edit.add_customer_name')); ?></button>
					<button id="btn-done-add-order-id-key" type="button" class="btn btn-success" style="margin-top: 10px"><?php echo e(__('views.admin.users.edit.add_order_id')); ?></button>
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom:100px;">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3" style="text-align:right;margin-top:20px;">
                    <button type="submit" class="btn btn-success"> {{ __('views.admin.users.edit.save') }} </button>
                </div>
            </div>
            
    		{{ Form::close() }}
	</div>
</div>
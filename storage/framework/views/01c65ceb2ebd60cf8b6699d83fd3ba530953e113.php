<?php $__env->startSection('title',"Store Settings" ); ?>

<!-- ******************************** settings ******************************** -->

<?php $__env->startSection('settings'); ?>

    <div class="row" style="width:100%;min-height:700px;">

        <div class="col-md-12 col-sm-12 col-xs-12">
            <?php echo e(Form::open(['route'=>['admin.stores.updateSettings', $store->id],'method' => 'put','class'=>'form-horizontal form-label-left'])); ?>

            
                <?php 
                    $server_address_  = isset($settings->server_address_)  ? $settings->server_address_ : "";
                    $server_username_ = isset($settings->server_username_) ? $settings->server_username_ : "";
                    $server_password_ = isset($settings->server_password_) ? $settings->server_password_ : "";
                    $socket_port_     = isset($settings->socket_port_) ? $settings->socket_port_ : "1111";
                    
                    $auto_done_order_hourly_ = isset($settings->auto_done_order_hourly_) ? $settings->auto_done_order_hourly_ : "0";
                    $auto_done_order_time_   = isset($settings->auto_done_order_time_) ? $settings->auto_done_order_time_ : "0";
                    
                    $timezone_    = isset($settings->timezone_) ? $settings->timezone_ : "America/New_York";
                    $smart_order_ = isset($settings->smart_order_) ? $settings->smart_order_ : "0";
                    
                    $licenses_quantity_ = isset($settings->licenses_quantity_) ? $settings->licenses_quantity_ : "0";
                ?>
    			
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="server_address" >
                        Server Address:
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="server_address" name="server_address" type="text"
                        value="<?php echo e($server_address_); ?>" class="form-control col-md-7 col-xs-12">
                    </div>
                </div>
    
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="server_username" >
                        Server Username:
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="server_username" name="server_username" type="text"
                        value="<?php echo e($server_username_); ?>" class="form-control col-md-7 col-xs-12">
                    </div>
                </div>
    
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="server_password" >
                        Server Password:
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="server_password" name="server_password" type="password"
                        value="<?php echo e($server_password_); ?>" class="form-control col-md-7 col-xs-12">
                    </div>
                </div>
    
                <div class="divider" style="width:50%;margin:auto;margin-top:20px;margin-bottom:20px;"></div>
    
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="socket_port" >
                        Local Sync Socket Port:
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="socket_port" name="socket_port" type="number" style="width:100px;display:inline;text-align:center;"
                        value="<?php echo e($socket_port_); ?>" class="form-control" required>
                    </div>
                </div>
    
                <div class="divider" style="width:50%;margin:auto;margin-top:20px;margin-bottom:20px;"></div>
    
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="auto_bump_type" >
                        Automatic Bump Time:
                    </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <span class="radio-bump" name="auto_done_order_hourly">
                            Daily: &nbsp; <?php echo e(Form::radio('auto_done_order_hourly', 0, true)); ?>

                        </span>
                        <span class="radio-bump">
                            Hourly: &nbsp; <?php echo e(Form::radio('auto_done_order_hourly', 1, $auto_done_order_hourly_)); ?>

                        </span>
                        <span class="radio-bump-time">
                            <select style="width:80px;height:30px;" name="auto_done_order_time">
                            <?php
                                $kdsTime = new DateTime();
                                $kdsTime->setTimezone(new DateTimeZone($timezone_));
                                $kdsTime->setTimestamp($auto_done_order_time_);
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
                            $sel  = $smart_order_ ? ["","selected"] : ["selected",""];
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
                        value="<?php echo e($licenses_quantity_); ?>" class="form-control" required>
                    </div>
                </div>
            
                <div class="form-group" style="margin-bottom:100px;">
                    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3" style="text-align:right;">
                        <button type="submit" class="btn btn-success"> <?php echo e(__('views.admin.users.edit.save')); ?></button>
                    </div>
                </div>
            <?php echo e(Form::close()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<!-- ******************************** devices ******************************** -->

<?php $__env->startSection('devices'); ?>
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
            <th>Function</th>
            <th>Parent ID</th>
            <th>Expeditor</th>
            <th>Last Update</th>
            <th>License</th>
        </tr>
        </thead>
        <tbody>

        <?php $__currentLoopData = $devices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $device): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                	<td><?php echo e($device->id_); ?></td>
                	<td><?php echo e($device->name_); ?></td>
                	<td><?php echo e($device->function_); ?></td>
                	<td><?php echo e($device->parent_id_ == 0 ? "" : $device->parent_id_); ?></td>
                	<td><?php echo e($device->expeditor_); ?></td>
				<td>
				<?php 
        				$date = new DateTime();
        				$date->setTimestamp($device->update_time_);
				?>
				<?php echo e($date->format('m/d/Y H:i:s')); ?>

				</td>
                	<td>
                    	<?php if($device->login_ == 1): ?>
                        <span class="label label-primary"><?php echo e(__('views.admin.users.index.active')); ?></span>
                    <?php else: ?>
                        <span class="label label-danger"><?php echo e(__('views.admin.users.index.inactive')); ?></span>
                    <?php endif; ?>
                	</td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('styles'); ?>
    ##parent-placeholder-bf62280f159b1468fff0c96540f3989d41279669##
    <?php echo e(Html::style(mix('assets/admin/css/users/edit.css'))); ?>

    <style>
        .radio-bump { display:inline-table; width:100px; }
        .radio-bump-time { display:inline-table; width:120px; text-align:right; }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    ##parent-placeholder-16728d18790deb58b3b8c1df74f06e536b532695##
    <?php echo e(Html::script(mix('assets/admin/js/users/edit.js'))); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.config_base', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
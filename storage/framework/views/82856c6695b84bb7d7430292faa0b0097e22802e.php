<?php $__env->startSection('title', "Store Group View"); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <table class="table table-striped table-hover">
            <tbody>

            <!--
            <tr>
                <th>storegroup:</th>
                <td><img src="<?php echo e($storegroup->avatar); ?>" class="user-profile-image"></td>
            </tr>
            -->

            <tr>
                <th>Store Group Name</th>
                <td><?php echo e($storegroup->business_name); ?></td>
            </tr>
            <tr>
                <th>Contact Name</th>
                <td><?php echo e($storegroup->name); ?></td>
            </tr>
            <tr>
                <th><?php echo e(__('views.admin.users.show.table_header_2')); ?></th>
                <td>
                    <a href="mailto:<?php echo e($storegroup->email); ?>">
                        <?php echo e($storegroup->email); ?>

                    </a>
                </td>
            </tr>
            <tr>
                <th>Contact Phone Number</th>
                <td><?php echo e($storegroup->phone_number); ?></td>
            </tr>
            <tr>
                <th>Address</th>
                <td><?php echo e($storegroup->address); ?></td>
            </tr>
            <tr>
                <th>Address 2</th>
                <td><?php echo e($storegroup->address); ?></td>
            </tr>
            <tr>
                <th>City</th>
                <td><?php echo e($storegroup->city); ?></td>
            </tr>
            <tr>
                <th>State</th>
                <td><?php echo e($storegroup->state); ?></td>
            </tr>
            <tr>
                <th>Country</th>
                <td><?php echo e($storegroup->country); ?></td>
            </tr>
            <tr>
                <th>Zip Code</th>
                <td><?php echo e($storegroup->zipcode); ?></td>
            </tr>

            <!--
            <tr>
                <th><?php echo e(__('views.admin.users.show.table_header_3')); ?></th>
                <td>
                    <?php echo e($storegroup->roles->pluck('name')->implode(',')); ?>

                </td>
            </tr>
            -->

            <tr>
                <th><?php echo e(__('views.admin.users.show.table_header_4')); ?></th>
                <td>
                    <?php if($storegroup->active): ?>
                        <span class="label label-primary"><?php echo e(__('views.admin.users.show.active')); ?></span>
                    <?php else: ?>
                        <span class="label label-danger"><?php echo e(__('views.admin.users.show.inactive')); ?></span>
                    <?php endif; ?>
                </td>
            </tr>

            <tr>
                <th>Username</th>
                <td><?php echo e($storegroup->username); ?></td>
            </tr>

            <!--
            <tr>
                <th><?php echo e(__('views.admin.users.show.table_header_5')); ?></th>
                <td>
                    <?php if($storegroup->confirmed): ?>
                        <span class="label label-success"><?php echo e(__('views.admin.users.show.confirmed')); ?></span>
                    <?php else: ?>
                        <span class="label label-warning"><?php echo e(__('views.admin.users.show.not_confirmed')); ?></span>
                    <?php endif; ?></td>
                </td>
            </tr>
            -->

            <tr>
                <th><?php echo e(__('views.admin.users.show.table_header_6')); ?></th>
                <td><?php echo e($storegroup->created_at); ?> (<?php echo e($storegroup->created_at->diffForHumans()); ?>)</td>
            </tr>

            <tr>
                <th><?php echo e(__('views.admin.users.show.table_header_7')); ?></th>
                <td><?php echo e($storegroup->updated_at); ?> (<?php echo e($storegroup->updated_at->diffForHumans()); ?>)</td>
            </tr>
            </tbody>
        </table>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
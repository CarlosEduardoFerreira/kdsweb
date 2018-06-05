<?php $__env->startSection('title', "Reseller View"); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <table class="table table-striped table-hover">
            <tbody>

            <!--
            <tr>
                <th>reseller:</th>
                <td><img src="<?php echo e($reseller->avatar); ?>" class="user-profile-image"></td>
            </tr>
            -->

            <tr>
                <th>Reseller Name</th>
                <td><?php echo e($reseller->business_name); ?></td>
            </tr>
            <tr>
                <th>Contact Name</th>
                <td><?php echo e($reseller->name); ?></td>
            </tr>
            <tr>
                <th><?php echo e(__('views.admin.users.show.table_header_2')); ?></th>
                <td>
                    <a href="mailto:<?php echo e($reseller->email); ?>">
                        <?php echo e($reseller->email); ?>

                    </a>
                </td>
            </tr>
            <tr>
                <th>Contact Phone Number</th>
                <td><?php echo e($reseller->phone_number); ?></td>
            </tr>
            <tr>
                <th>Address</th>
                <td><?php echo e($reseller->address); ?></td>
            </tr>
            <tr>
                <th>Address 2</th>
                <td><?php echo e($reseller->address); ?></td>
            </tr>
            <tr>
                <th>City</th>
                <td><?php echo e($reseller->city); ?></td>
            </tr>
            <tr>
                <th>State</th>
                <td><?php echo e($reseller->state); ?></td>
            </tr>
            <tr>
                <th>Country</th>
                <td><?php echo e($reseller->country); ?></td>
            </tr>
            <tr>
                <th>Zip Code</th>
                <td><?php echo e($reseller->zipcode); ?></td>
            </tr>

            <!--
            <tr>
                <th><?php echo e(__('views.admin.users.show.table_header_3')); ?></th>
                <td>
                    <?php echo e($reseller->roles->pluck('name')->implode(',')); ?>

                </td>
            </tr>
            -->

            <tr>
                <th><?php echo e(__('views.admin.users.show.table_header_4')); ?></th>
                <td>
                    <?php if($reseller->active): ?>
                        <span class="label label-primary"><?php echo e(__('views.admin.users.show.active')); ?></span>
                    <?php else: ?>
                        <span class="label label-danger"><?php echo e(__('views.admin.users.show.inactive')); ?></span>
                    <?php endif; ?>
                </td>
            </tr>

            <tr>
                <th>Username</th>
                <td><?php echo e($reseller->username); ?></td>
            </tr>

            <!--
            <tr>
                <th><?php echo e(__('views.admin.users.show.table_header_5')); ?></th>
                <td>
                    <?php if($reseller->confirmed): ?>
                        <span class="label label-success"><?php echo e(__('views.admin.users.show.confirmed')); ?></span>
                    <?php else: ?>
                        <span class="label label-warning"><?php echo e(__('views.admin.users.show.not_confirmed')); ?></span>
                    <?php endif; ?></td>
                </td>
            </tr>
            -->

            <tr>
                <th><?php echo e(__('views.admin.users.show.table_header_6')); ?></th>
                <td><?php echo e($reseller->created_at); ?> (<?php echo e($reseller->created_at->diffForHumans()); ?>)</td>
            </tr>

            <tr>
                <th><?php echo e(__('views.admin.users.show.table_header_7')); ?></th>
                <td><?php echo e($reseller->updated_at); ?> (<?php echo e($reseller->updated_at->diffForHumans()); ?>)</td>
            </tr>
            </tbody>
        </table>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
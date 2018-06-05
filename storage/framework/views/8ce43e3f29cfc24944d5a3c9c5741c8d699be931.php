<?php $__env->startSection('title', "Store View"); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <table class="table table-striped table-hover">
            <tbody>

            <!--
            <tr>
                <th>Store:</th>
                <td><img src="<?php echo e($store->avatar); ?>" class="user-profile-image"></td>
            </tr>
            -->

            <tr>
                <th>Legal Business Name</th>
                <td><?php echo e($store->business_name); ?></td>
            </tr>
            <tr>
                <th>DBA (Doing business as)</th>
                <td><?php echo e($store->dba); ?></td>
            </tr>
            <tr>
                <th>Last Name</th>
                <td><?php echo e($store->last_name); ?></td>
            </tr>
            <tr>
                <th>First Name</th>
                <td><?php echo e($store->name); ?></td>
            </tr>
            <tr>
                <th><?php echo e(__('views.admin.users.show.table_header_2')); ?></th>
                <td>
                    <a href="mailto:<?php echo e($store->email); ?>">
                        <?php echo e($store->email); ?>

                    </a>
                </td>
            </tr>
            <tr>
                <th>Contact Phone Number</th>
                <td><?php echo e($store->phone_number); ?></td>
            </tr>
            <tr>
                <th>Address</th>
                <td><?php echo e($store->address); ?></td>
            </tr>
            <tr>
                <th>City</th>
                <td><?php echo e($store->city); ?></td>
            </tr>
            <tr>
                <th>State</th>
                <td><?php echo e($store->state); ?></td>
            </tr>
            <tr>
                <th>Country</th>
                <td><?php echo e($store->country); ?></td>
            </tr>
            <tr>
                <th>Zip Code</th>
                <td><?php echo e($store->zipcode); ?></td>
            </tr>

            <!--
            <tr>
                <th><?php echo e(__('views.admin.users.show.table_header_3')); ?></th>
                <td>
                    <?php echo e($store->roles->pluck('name')->implode(',')); ?>

                </td>
            </tr>
            -->

            <tr>
                <th><?php echo e(__('views.admin.users.show.table_header_4')); ?></th>
                <td>
                    <?php if($store->active): ?>
                        <span class="label label-primary"><?php echo e(__('views.admin.users.show.active')); ?></span>
                    <?php else: ?>
                        <span class="label label-danger"><?php echo e(__('views.admin.users.show.inactive')); ?></span>
                    <?php endif; ?>
                </td>
            </tr>

            <tr>
                <th>Username</th>
                <td><?php echo e($store->username); ?></td>
            </tr>

            <!--
            <tr>
                <th><?php echo e(__('views.admin.users.show.table_header_5')); ?></th>
                <td>
                    <?php if($store->confirmed): ?>
                        <span class="label label-success"><?php echo e(__('views.admin.users.show.confirmed')); ?></span>
                    <?php else: ?>
                        <span class="label label-warning"><?php echo e(__('views.admin.users.show.not_confirmed')); ?></span>
                    <?php endif; ?></td>
                </td>
            </tr>
            -->

            <tr>
                <th><?php echo e(__('views.admin.users.show.table_header_6')); ?></th>
                <td><?php echo e($store->created_at); ?> (<?php echo e($store->created_at->diffForHumans()); ?>)</td>
            </tr>

            <tr>
                <th><?php echo e(__('views.admin.users.show.table_header_7')); ?></th>
                <td><?php echo e($store->updated_at); ?> (<?php echo e($store->updated_at->diffForHumans()); ?>)</td>
            </tr>
            </tbody>
        </table>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
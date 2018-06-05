<?php $__env->startSection('title', "Store Groups"); ?>

<!-- <?php $__env->startSection('title', __('views.admin.users.index.title')); ?> -->

<?php $__env->startSection('content'); ?>
    <div class="row" style="min-height:700px;">
    
    		<div style="text-align:right;padding:10px;">
    			<a class="btn btn-success" type="button" href="<?php echo e(route('admin.storegroups.new')); ?>">New</a>
    		</div>
    		
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
            <tr>
            	<th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('name',  __('views.admin.users.index.table_header_1'),['page' => $storegroups->currentPage()]));?></th>
                <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('email', __('views.admin.users.index.table_header_0'),['page' => $storegroups->currentPage()]));?></th>
                <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('active', __('views.admin.users.index.table_header_3'),['page' => $storegroups->currentPage()]));?></th>
                <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('created_at', __('views.admin.users.index.table_header_5'),['page' => $storegroups->currentPage()]));?></th>
                <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('updated_at', __('views.admin.users.index.table_header_6'),['page' => $storegroups->currentPage()]));?></th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>

            <?php $__currentLoopData = $storegroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $storegroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                		<td><?php echo e($storegroup->name); ?></td>
                    	<td><?php echo e($storegroup->email); ?></td>
                    <td>
                        <?php if($storegroup->active): ?>
                            <span class="label label-primary"><?php echo e(__('views.admin.users.index.active')); ?></span>
                        <?php else: ?>
                            <span class="label label-danger"><?php echo e(__('views.admin.users.index.inactive')); ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($storegroup->created_at); ?></td>
                    <td><?php echo e($storegroup->updated_at); ?></td>
                    <td>

                        <a class="btn btn-xs btn-info" href="<?php echo e(route('admin.storegroups.show', [$storegroup->id])); ?>" data-toggle="tooltip" data-placement="top" data-title="<?php echo e(__('views.admin.users.index.show')); ?>">
                            <i class="fa fa-eye"></i>
                        </a>

                        <a class="btn btn-xs btn-warning" href="<?php echo e(route('admin.storegroups.edit', [$storegroup->id])); ?>" data-toggle="tooltip" data-placement="top" data-title="<?php echo e(__('views.admin.users.index.edit')); ?>">
                            <i class="fa fa-pencil"></i>
                        </a>

                        
                            
                                    
                                
                            
                        

                        <?php if ($storegroup->role_id == 2 || $storegroup->role_id == 3) { ?>
                            <a class="btn btn-xs btn-primary" href="<?php echo e(route('admin.stores', ['storegroupId' => $storegroup->id])); ?>"
                            				data-toggle="tooltip" data-placement="top" data-title="Stores">
                                <i class="fa fa-cutlery"></i>
                            </a>
                        <?php } ?>

                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <div class="pull-right">
            <?php echo e($storegroups->links()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
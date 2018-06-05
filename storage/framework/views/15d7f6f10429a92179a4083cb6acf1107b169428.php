<?php $__env->startSection('title', "Resellers"); ?>

<?php $__env->startSection('content'); ?>
    <div class="row" style="min-height:700px;">
    
    		<div style="text-align:right;padding:10px;">
    			<a class="btn btn-success" type="button" href="<?php echo e(route('admin.resellers.new')); ?>">New</a>
    		</div>
    		
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
            <thead>
            <tr>
            	<th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('name',  __('views.admin.users.index.table_header_1'),['page' => $resellers->currentPage()]));?></th>
                <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('email', __('views.admin.users.index.table_header_0'),['page' => $resellers->currentPage()]));?></th>
                <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('active', __('views.admin.users.index.table_header_3'),['page' => $resellers->currentPage()]));?></th>
                <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('created_at', __('views.admin.users.index.table_header_5'),['page' => $resellers->currentPage()]));?></th>
                <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('updated_at', __('views.admin.users.index.table_header_6'),['page' => $resellers->currentPage()]));?></th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>

            <?php $__currentLoopData = $resellers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reseller): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                		<td><?php echo e($reseller->name); ?></td>
                    	<td><?php echo e($reseller->email); ?></td>
                    <td>
                        <?php if($reseller->active): ?>
                            <span class="label label-primary"><?php echo e(__('views.admin.users.index.active')); ?></span>
                        <?php else: ?>
                            <span class="label label-danger"><?php echo e(__('views.admin.users.index.inactive')); ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($reseller->created_at); ?></td>
                    <td><?php echo e($reseller->updated_at); ?></td>
                    <td>

                        <a class="btn btn-xs btn-info" href="<?php echo e(route('admin.resellers.show', [$reseller->id])); ?>" data-toggle="tooltip" data-placement="top" data-title="<?php echo e(__('views.admin.users.index.show')); ?>">
                            <i class="fa fa-eye"></i>
                        </a>

                        <a class="btn btn-xs btn-warning" href="<?php echo e(route('admin.resellers.edit', [$reseller->id])); ?>" data-toggle="tooltip" data-placement="top" data-title="<?php echo e(__('views.admin.users.index.edit')); ?>">
                            <i class="fa fa-pencil"></i>
                        </a>

                        
                            
                                    
                                
                            
                        

                        <?php if ($reseller->role_id == 2) { ?>
                            <a class="btn btn-xs btn-primary" href="<?php echo e(route('admin.storegroups', ['resellerId' => $reseller->id])); ?>"
                            				data-toggle="tooltip" data-placement="top" data-title="Store Groups">
                                <i class="fa fa-sitemap"></i>
                            </a>
                        <?php } ?>

                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <div class="pull-right">
            <?php echo e($resellers->links()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
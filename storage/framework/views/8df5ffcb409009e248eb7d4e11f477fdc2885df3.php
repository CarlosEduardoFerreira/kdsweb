<?php
    $title = Session::get('title')
?>

<?php $__env->startSection('title', $title); ?>

<!-- <?php $__env->startSection('title', __('views.admin.users.index.title')); ?> -->

<?php $__env->startSection('content'); ?>
    <div class="row">
        <table class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"
               width="100%">
            <thead>
            <tr>
            	<th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('name',  __('views.admin.users.index.table_header_1'),['page' => $users->currentPage()]));?></th>
                <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('email', __('views.admin.users.index.table_header_0'),['page' => $users->currentPage()]));?></th>
                <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('active', __('views.admin.users.index.table_header_3'),['page' => $users->currentPage()]));?></th>
                <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('created_at', __('views.admin.users.index.table_header_5'),['page' => $users->currentPage()]));?></th>
                <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('updated_at', __('views.admin.users.index.table_header_6'),['page' => $users->currentPage()]));?></th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>

            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                		<td><?php echo e($user->name); ?></td>
                    	<td><?php echo e($user->email); ?></td>
                    <td>
                        <?php if($user->active): ?>
                            <span class="label label-primary"><?php echo e(__('views.admin.users.index.active')); ?></span>
                        <?php else: ?>
                            <span class="label label-danger"><?php echo e(__('views.admin.users.index.inactive')); ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($user->created_at); ?></td>
                    <td><?php echo e($user->updated_at); ?></td>
                    <td>
                    		<?php
                    		  $role = $user->role_id;

                    		  $action = route('admin.storegroups', ['resellerId' => $user->id]);
                    		  $dataTitle  = "Store Groups";
                    		  $icon = "fa-sitemap";

                    		  if ($role == 'storegroup') {
                    		      $action = route('admin.stores', ['storegroupId' => $user->id]);
                    		      $dataTitle  = "Stores";
                    		      $icon = "fa-cutlery";
                    		  }
                    		  //echo "role : " . $role;
                    		?>

                            <a class="btn btn-xs btn-info" href="<?php echo e(route('admin.users.show', [$user->id])); ?>" data-toggle="tooltip" data-placement="top" data-title="<?php echo e(__('views.admin.users.index.show')); ?>">
                                <i class="fa fa-eye"></i>
                            </a>

                            <a class="btn btn-xs btn-warning" href="<?php echo e(route('admin.users.edit', [$user->id])); ?>" data-toggle="tooltip" data-placement="top" data-title="<?php echo e(__('views.admin.users.index.edit')); ?>">
                                <i class="fa fa-pencil"></i>
                            </a>

                            
                                
                                        
                                    
                                
                            

                            <?php if ($role == 'reseller' || $role == 'storegroup') { ?>
                                <a class="btn btn-xs btn-primary" href="<?php echo $action; ?>"
                                				data-toggle="tooltip" data-placement="top" data-title="<?php echo $dataTitle; ?>">
                                    <i class="fa <?php echo $icon; ?>"></i>
                                </a>
                            <?php } ?>

                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <div class="pull-right">
            <?php echo e($users->links()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.admin', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
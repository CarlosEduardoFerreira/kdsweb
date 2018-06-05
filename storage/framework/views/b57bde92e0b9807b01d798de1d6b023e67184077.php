<?php $__env->startSection('content'); ?>
    <div class="title m-b-md" id="main_title">
        <?php echo e(config('app.name')); ?>

    </div>
    <div class="m-b-md">
        Thank you for register.<br/>
        <br/>
        Provide your username for your reseller to start using KDS Portal.
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.welcome_first', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
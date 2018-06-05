<?php $__env->startSection('title', 'Page Expired'); ?>

<?php $__env->startSection('message'); ?>
    The page has expired due to inactivity. 
    <br/><br/>
    Going to Login Page...
    <script>
    		window.setTimeout('window.open("/", "_self")',2000); // miliseconds
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('errors::layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
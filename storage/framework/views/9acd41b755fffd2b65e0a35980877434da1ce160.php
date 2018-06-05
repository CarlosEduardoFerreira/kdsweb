<?php $__env->startSection('body_class','login'); ?>

<?php $__env->startSection('content'); ?>


<style>
    #mainlogin {
        margin:auto;
        margin-top:5%;
        width:350px;
    }

</style>

<div class="animate form login_content">

    <div id="mainlogin">

        <?php echo e(Form::open(['route' => 'login'])); ?>

            <h1><?php echo e(__('views.auth.login.header')); ?></h1>

            <div>
                <input id="email" type="email" class="form-control" name="email" value="<?php echo e(old('email')); ?>"
                       placeholder="<?php echo e(__('views.auth.login.input_0')); ?>" required autofocus>
            </div>
            <div>
                <input id="password" type="password" class="form-control" name="password"
                       placeholder="<?php echo e(__('views.auth.login.input_1')); ?>" required>
            </div>
            <div style="width:350px;padding-bottom:10px;">
                <div style="display:inline-table;width:270px;text-align:left;">
                    <input type="checkbox" name="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>> <?php echo e(__('views.auth.login.input_2')); ?>

                </div>
                <button class="btn btn-success" type="submit"><?php echo e(__('views.auth.login.action_0')); ?></button>
            </div>

            <?php if(session('status')): ?>
                <div class="alert alert-success">
                    <?php echo e(session('status')); ?>

                </div>
            <?php endif; ?>

            <?php if(!$errors->isEmpty()): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $errors->first(); ?>

                </div>
            <?php endif; ?>

            <div style="text-align:left;">
            		<div style="display:inline-table;width:200px;padding-left:10px;">
                    <a href="<?php echo e(route('password.request')); ?>"><?php echo e(__('views.auth.login.action_1')); ?></a>
                </div>
            </div>

            <div class="clearfix"></div>

            <!-- <div class="separator">
                <span><?php echo e(__('views.auth.login.message_0')); ?></span>
                <div>
                    <a href="<?php echo e(route('social.redirect', ['google'])); ?>" class="btn btn-success btn-google-plus">
                        <i class="fa fa-google-plus"></i>
                        Google+
                    </a>
                    <a href="<?php echo e(route('social.redirect', ['facebook'])); ?>" class="btn btn-success btn-facebook">
                        <i class="fa fa-facebook"></i>
                        Facebook
                    </a>
                    <a href="<?php echo e(route('social.redirect', ['twitter'])); ?>" class="btn btn-success btn-twitter">
                        <i class="fa fa-twitter"></i>
                        Twitter
                    </a>
                </div>
            </div> -->

            <?php if(config('auth.users.registration')): ?>
                <!-- <div class="separator">  -->
                		<!-- 
                    <p class="change_link"><?php echo e(__('views.auth.login.message_1')); ?>

                        <a href="<?php echo e(route('register')); ?>" class="to_register"> <?php echo e(__('views.auth.login.action_2')); ?> </a>
                    </p>
                    -->

                    <div class="clearfix"></div>
                    <br/>

                <!--
                    <div>
                        <div class="h1"><?php echo e(config('app.name')); ?></div>
                        <p>&copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. <?php echo e(__('views.auth.login.copyright')); ?></p>
                    </div>
                -->
                <!-- </div> -->
            <?php endif; ?>
        <?php echo e(Form::close()); ?>


    </div>



</div>

<style>
    #footerlogo {
        position: relative;
        width: 100%;
        left: -10px;
    }
    #tablelogo {
        width: 100%;
    }
    #tdslogo {
        width: 50%;
    }
</style>

<div id="footerlogo">
    <table id="tablelogo">
        <tr>
            <td class="tdslogo"><img src="kds_lines.png" style="height:70px;"/></td>
            <td class="tdslogo"> <img src="kds_logo.png" style="width:170px;"/> <td>
        </tr>
    </table>
</div>


<?php $__env->stopSection(); ?>



<?php $__env->startSection('styles'); ?>
    ##parent-placeholder-bf62280f159b1468fff0c96540f3989d41279669##

    <?php echo e(Html::style(mix('assets/auth/css/login.css'))); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('auth.layouts.auth', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
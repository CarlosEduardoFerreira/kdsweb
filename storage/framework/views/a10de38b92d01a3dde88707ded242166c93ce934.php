<?php $__env->startSection('body_class','nav-md'); ?>

<?php $__env->startSection('page'); ?>
    <div class="container body">
        <div class="main_container" style="height:100%;">
            <?php $__env->startSection('header'); ?>
                <?php echo $__env->make('admin.sections.navigation', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php echo $__env->make('admin.sections.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php echo $__env->yieldSection(); ?>

            <?php echo $__env->yieldContent('left-sidebar'); ?>

            <div class="right_col" role="main">

                <div style="height:150px;">
                	
                		<div style="display:inline-table;padding:10px;float:right;font-size:18px;"><?=$licenseInfo;?></div>

                    <div class="page-title" style="background:#ffffff;">

                        <div class="title_left" style="margin-left:20px;">
                            <h1 class="h3"><?php echo $__env->yieldContent('title'); ?></h1>
                        </div>

                        <?php if(Breadcrumbs::exists()): ?>
                            <div class="title_right">
                                <div class="pull-right">
                                    <?php echo Breadcrumbs::render(); ?>

                                </div>
                            </div>
                        <?php endif; ?>
						
                    </div>
                    
                    <ul class="nav nav-tabs" role="tablist" style="background:white;">
                        <li role="presentation" class="active"><a class="atabs" href="#settings" role="tab" data-toggle="tab">Settings</a></li>
                        <li role="presentation" ><a class="atabs" href="#devices" role="tab" data-toggle="tab">KDS Stations</a></li>
                        <!--<li role="presentation" ><a class="atabs" href="#licenses" role="tab" data-toggle="tab">Licenses</a></li>-->
                        
                    </ul>

                </div>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="settings">
                        <?php echo $__env->yieldContent('settings'); ?>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="devices">
                        <?php echo $__env->yieldContent('devices'); ?>
                    </div>
                    <!--<div role="tabpanel" class="tab-pane active" id="licenses">
                        <?php echo $__env->yieldContent('server'); ?>
                    </div>-->
                </div>

            </div>

            <footer>
                <?php echo $__env->make('admin.sections.footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </footer>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
    <?php echo e(Html::style(mix('assets/admin/css/admin.css'))); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <?php echo e(Html::script(mix('assets/admin/js/admin.js'))); ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0-alpha1/jquery.min.js"></script>
    <script type="text/javascript">

        $(document).ready(function(){

            $('.active a').css('background-color','#f7f7f7')

            $(".atabs").click(function(){
                $(".atabs").each(function(){
                    $(this).removeClass('active')
                    $(this).css('background-color','#ffffff')
                    $(this).css('border-bottom','1px solid #ddd')
                })
                $(this).css('background-color','#f7f7f7')
                $(this).css('border-bottom','1px solid #f7f7f7')
            })

        });

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
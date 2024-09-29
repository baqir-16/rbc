<?php $__env->startSection('title', 'System Update'); ?>
<?php $__env->startSection('content'); ?>
    <div class="box box-primary">
        <div id="update_notification" style="display:none;" class="alert alert-info">
            
                
            
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->startSection('title', 'Login Successful'); ?>

<?php $__env->startSection('content'); ?>

    <div class="hold-transition">
    <div class="login-box">
        <div class="login-logo">
            <h3><img class="img-responsive" style="padding-right:5px; text-align: left; padding-bottom:15px;" src="<?php echo asset('img/Axiata_Logo.png'); ?>"><br>Welcome To<b><br> Cyber Defence Portal</b></h3>
        </div>
    </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
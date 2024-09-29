<?php $__env->startSection('title', 'Edit User ' . $user->first_name); ?>

<?php $__env->startSection('content'); ?>
<div class="box box-primary">
  <div class="box-header with-border">
            <h3 class="box-title">Edit <?php echo e($user->first_name); ?></h3>

            <a href="<?php echo e(route('users.index')); ?>" class="btn btn-default btn-sm pull-right"> <i class="fa fa-arrow-left"></i> Back</a>

    </div>

    <div class="animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="box-body">
                        <?php echo Form::model($user, ['method' => 'PUT', 'route' => ['users.update',  $user->id ] ]); ?>

                            <?php echo $__env->make('user._form', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            <!-- Submit Form Button -->
                            <?php echo Form::submit('Save Changes', ['class' => 'btn btn-primary']); ?>

                        <?php echo Form::close(); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
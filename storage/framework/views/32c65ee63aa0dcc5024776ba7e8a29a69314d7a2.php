<?php $__env->startSection('title', 'Create'); ?>
<?php $__env->startSection('content'); ?>

        
        <div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Create</h3>
        <a href="<?php echo e(route('departments.index')); ?>" class="btn btn-default btn-sm pull-right"> <i class="fa fa-arrow-left"></i> Back</a>
    </div>
    <div class="animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="box-body">

            <?php echo Form::open(['route' => ['departments.store'] ]); ?>

                        <div class="form-group <?php if($errors->has('department')): ?> has-error <?php endif; ?>">
                            <?php echo Form::label('department', 'Create New Department'); ?>

                            <?php echo Form::text('department', null, ['class' => 'form-control', 'placeholder' => 'Write Department']); ?>

                            <?php if($errors->has('department')): ?> <p class="help-block"><?php echo e($errors->first('department')); ?></p> <?php endif; ?>
                        </div>
                        <br>
                <?php echo Form::submit('Create', ['class' => 'btn btn-primary']); ?>

            <?php echo Form::close(); ?>

        </div>
    </div>
  </div>
 </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
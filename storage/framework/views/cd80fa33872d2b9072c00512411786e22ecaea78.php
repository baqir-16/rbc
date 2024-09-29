<?php $__env->startSection('title', 'Edit Department'); ?>

<?php $__env->startSection('content'); ?>
        
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Edit</h3>
            <a href="<?php echo e(route('departments.index')); ?>" class="btn btn-default btn-sm pull-right"> <i class="fa fa-arrow-left"></i> Back</a>
        </div>
        <div class="animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="box-body">
                            <?php echo Form::model($department, ['method' => 'PUT', 'route' => ['departments.update',  $department->id ] ] ); ?>


                            <input type="hidden" name="id" value="<?php echo e($department->id); ?>">
                            <div class="form-group <?php if($errors->has('department')): ?> has-error <?php endif; ?>">
                                <?php echo Form::label('department', 'Edit Department'); ?>

                                <?php echo Form::text('department', $department->department, ['class' => 'form-control', 'placeholder' => 'Write Department']); ?>

                             <?php if($errors->has('department')): ?> <p class="help-block"><?php echo e($errors->first('department')); ?></p> <?php endif; ?>
                            </div>
                            <br>
                            <?php echo Form::submit('Save Changes', ['class' => 'btn btn-primary']); ?>

                            <?php echo Form::close(); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
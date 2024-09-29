<?php $__env->startSection('title', 'PMO Tasks'); ?>
<?php $__env->startSection('content'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('pmo_view')): ?>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Assign Roles</h3>
        <a href="<?php echo e(url('PMO?id='.$ticket_id)); ?>" class="btn btn-default btn-sm pull-right"> <i class="fa fa-arrow-left"></i> Back</a>
    </div>
    <div class="animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="box-body">
                        <?php echo Form::open(['method' => 'post', 'route' => 'PMO.store']); ?>

                        <div class="form-group <?php if($errors->has('tester_id')): ?> has-error <?php endif; ?>">
                            <?php echo Form::label('user[]', 'Tester'); ?>

                            <?php echo Form::select('tester_id', $tester, isset($tester) ? $tester->toArray() : null,  ['placeholder' => 'Assign Tester ', 'class' => 'form-control']); ?>

                            <?php if($errors->has('tester')): ?> <p class="help-block"><?php echo e($errors->first('tester')); ?></p> <?php endif; ?>
                        </div>
                        <div class="form-group <?php if($errors->has('analyst_id')): ?> has-error <?php endif; ?>">
                            <?php echo Form::label('user[]', 'Analyst'); ?>

                            <?php echo Form::select('analyst_id', $analyst, isset($analyst) ? $analyst->toArray() : null,  ['placeholder' => 'Assign Analyst ', 'class' => 'form-control']); ?>

                            <?php if($errors->has('analyst')): ?> <p class="help-block"><?php echo e($errors->first('analyst')); ?></p> <?php endif; ?>
                        </div>
                        <div class="form-group <?php if($errors->has('qa_id')): ?> has-error <?php endif; ?>">
                            <?php echo Form::label('user[]', 'Quality Assurance'); ?>

                            <?php echo Form::select('qa_id', $qa, isset($qa) ? $qa->toArray() : null,  ['placeholder' => 'Assign Quality Assurance', 'class' => 'form-control']); ?>

                            <?php if($errors->has('qa')): ?> <p class="help-block"><?php echo e($errors->first('qa')); ?></p> <?php endif; ?>
                        </div>
                        <div class="form-group <?php if($errors->has('hod_id')): ?> has-error <?php endif; ?>">
                            <?php echo Form::label('user[]', 'HOD'); ?>

                            <?php echo Form::select('hod_id', $hod, isset($hod) ? $hod->toArray() : null,  ['placeholder' => 'Assign HOD', 'class' => 'form-control']); ?>

                            <?php if($errors->has('hod')): ?> <p class="help-block"><?php echo e($errors->first('hod')); ?></p> <?php endif; ?>
                        </div>
                        <input type="hidden" name="id" value="<?php echo e($id); ?>">
                        <input type="hidden" name="ticket_id" value="<?php echo e($ticket_id); ?>">
                        <?php echo Form::submit('Create', ['class' => 'btn btn-primary']); ?>

                        <?php echo Form::close(); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
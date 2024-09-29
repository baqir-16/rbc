<?php $__env->startSection('title', 'Edit'); ?>
<?php $__env->startSection('content'); ?>
    <div class="box box-primary">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('pmo_view')): ?>
        <div class="box-header with-border">
            <h3 class="box-title">Edit</h3>
            <a href="<?php echo e(route('Ticket.index')); ?>" class="btn btn-default btn-sm pull-right"> <i class="fa fa-arrow-left"></i> Back</a>
        </div>
        <div class="animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="box-body">
                        <?php echo Form::open(['route' => ['Ticket.update', $ticket[0]->id], 'method' => 'PATCH']); ?>

                            <div class="form-group <?php if($errors->has('title')): ?> has-error <?php endif; ?>">
                                <?php echo Form::label('title', 'Title'); ?>

                                <?php echo Form::text('title', $ticket[0]->title, ['class' => 'form-control', 'placeholder' => 'Title of Ticket']); ?>

                                <?php if($errors->has('title')): ?> <p class="help-block"><?php echo e($errors->first('title')); ?></p> <?php endif; ?>
                            </div>
                            <div class="form-group <?php if($errors->has('department')): ?> has-error <?php endif; ?>">
                            <?php echo Form::label('department[]', 'Select Department'); ?>

                                <?php echo e(Form::select('department', $department->toArray(), $ticket[0]->department, ['placeholder' => 'Select Department', 'class' => 'form-control'])); ?>

                            <?php if($errors->has('department')): ?> <p class="help-block"><?php echo e($errors->first('department')); ?></p> <?php endif; ?>
                            </div>
                            <div class="form-group <?php if($errors->has('ref')): ?> has-error <?php endif; ?>">
                                <?php echo Form::label('ref', 'Reference No'); ?>

                                <?php echo Form::text('ref', $ticket[0]->ref, ['class' => 'form-control', 'placeholder' => 'Reference Number']); ?>

                                <?php if($errors->has('ref')): ?> <p class="help-block"><?php echo e($errors->first('ref')); ?></p> <?php endif; ?>
                            </div>
                            <div class="form-group <?php if($errors->has('opco_id')): ?> has-error <?php endif; ?>">
                                
                                
                                
                                <input type="hidden" name="opco_id" value=<?php echo e($_ENV['APP_OPCO_ID']); ?> class="form-control">
                            </div>
                            <div class="form-group <?php if($errors->has('module_id')): ?> has-error <?php endif; ?>">
                                <?php echo Form::label('modules[]', 'Modules'); ?>

                                <?php echo Form::select('module_id[]', $modules, $selected_modules, ['data-placeholder' => 'Choose Modules', 'class' => 'form-control select2', 'multiple' => 'multiple']); ?>

                                <?php if($errors->has('module_id')): ?> <p class="help-block">The module field is required.</p> <?php endif; ?>
                            </div>
                            <div class="form-group <?php if($errors->has('comments')): ?> has-error <?php endif; ?>">
                                <?php echo Form::label('comments', 'Comment'); ?>

                                <?php echo Form::textarea('comments', $comment[0]->comments, ['class' => 'form-control ckeditor', 'placeholder' => 'Write a comment...']); ?>

                                <?php if($errors->has('comments')): ?> <p class="help-block"><?php echo e($errors->first('comments')); ?></p> <?php endif; ?>
                            </div>
                            <?php $__env->startPush('scripts'); ?>
                                <script src="//cdn.ckeditor.com/4.6.2/standard/ckeditor.js"></script>
                            <?php $__env->stopPush(); ?>
                            <?php echo Form::submit('Update', ['class' => 'btn btn-primary']); ?>

                            <?php echo Form::close(); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
            <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<!-- Title of title Form Input -->
<div class="form-group <?php if($errors->has('title')): ?> has-error <?php endif; ?>">
    <?php echo Form::label('title', 'Title'); ?>

    <?php echo Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Title of Ticket']); ?>

    <?php if($errors->has('title')): ?> <p class="help-block"><?php echo e($errors->first('title')); ?></p> <?php endif; ?>
</div>

<div class="form-group <?php if($errors->has('department')): ?> has-error <?php endif; ?>">
                <?php echo Form::label('department[]', 'Select Department'); ?>

                    <?php echo e(Form::select('department', $department->toArray(), null, ['placeholder' => 'Select Department', 'class' => 'form-control'])); ?>

                <?php if($errors->has('department')): ?> <p class="help-block"><?php echo e($errors->first('department')); ?></p> <?php endif; ?>
</div>

<!-- Text Reference Form Input -->
<div class="form-group <?php if($errors->has('ref')): ?> has-error <?php endif; ?>">
    <?php echo Form::label('ref', 'Reference No'); ?>

    <?php echo Form::text('ref', null, ['class' => 'form-control', 'placeholder' => 'Reference Number']); ?>

    <?php if($errors->has('ref')): ?> <p class="help-block"><?php echo e($errors->first('ref')); ?></p> <?php endif; ?>
</div>

<div class="form-group <?php if($errors->has('opco_id')): ?> has-error <?php endif; ?>">
    
    
    
    <input type="hidden" name="opco_id" value=<?php echo e($_ENV['APP_OPCO_ID']); ?> class="form-control">

</div>

<div class="form-group <?php if($errors->has('module_id')): ?> has-error <?php endif; ?>">
    <?php echo Form::label('modules[]', 'Modules'); ?>

    <?php echo Form::select('module_id[]', $modules, isset($user) ? $modules->toArray() : null,  ['data-placeholder' => 'Choose Modules', 'class' => 'form-control select2', 'multiple' => 'multiple']); ?>

    <?php if($errors->has('module_id')): ?> <p class="help-block">The module field is required.</p> <?php endif; ?>
</div>

<style>
    .select2 {
        width: 100% !important;
    }
</style>
<!-- Text comment Form Input -->
<div class="form-group <?php if($errors->has('comments')): ?> has-error <?php endif; ?>">
    <?php echo Form::label('comments', 'Comment'); ?>

    <?php echo Form::textarea('comments', null, ['class' => 'form-control ckeditor', 'placeholder' => 'Write a comment...']); ?>

    <?php if($errors->has('comments')): ?> <p class="help-block"><?php echo e($errors->first('comments')); ?></p> <?php endif; ?>
</div>

<?php $__env->startPush('scripts'); ?>
    <script src="//cdn.ckeditor.com/4.6.2/standard/ckeditor.js"></script>
<?php $__env->stopPush(); ?>

<!-- Name Form Input -->
<div class="form-group <?php if($errors->has('name')): ?> has-error <?php endif; ?>">
    <?php echo Form::label('name', 'Name'); ?>

    <?php echo Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Name']); ?>

    <?php if($errors->has('name')): ?> <p class="help-block"><?php echo e($errors->first('name')); ?></p> <?php endif; ?>
</div>
<!-- UserName Form Input -->
<div class="form-group <?php if($errors->has('username')): ?> has-error <?php endif; ?>">
    <?php echo Form::label('username', 'Username'); ?>

    <?php echo Form::text('username', null, ['class' => 'form-control', 'placeholder' => 'Username']); ?>

    <?php if($errors->has('username')): ?> <p class="help-block"><?php echo e($errors->first('username')); ?></p> <?php endif; ?>
</div>
<!-- email Form Input -->
<div class="form-group <?php if($errors->has('email')): ?> has-error <?php endif; ?>">
    <?php echo Form::label('email', 'Email'); ?>

    <?php echo Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Email']); ?>

    <?php if($errors->has('email')): ?> <p class="help-block"><?php echo e($errors->first('email')); ?></p> <?php endif; ?>
</div>

<!-- password Form Input -->

    
    
    


<div class="form-group <?php if($errors->has('password')): ?> has-error <?php endif; ?>">
    <?php echo Form::label('password', 'Password'); ?>

    <?php echo Form::password('password', ['class' => 'form-control', 'placeholder' => 'Password', 'autocomplete' => 'off']); ?>

    <?php if($errors->has('password')): ?> <p class="help-block"><?php echo e($errors->first('password')); ?></p> <?php endif; ?>
</div>
<div class="form-group <?php if($errors->has('password_confirmation')): ?> has-error <?php endif; ?>">
    <?php echo Form::label('password_confirmation', 'Confirm Password'); ?>

    <?php echo Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => 'Confirm Password', 'autocomplete' => 'off']); ?>

    <?php if($errors->has('password_confirmation')): ?> <p class="help-block"><?php echo e($errors->first('password_confirmation')); ?></p> <?php endif; ?>
</div>


<div class="form-group <?php if($errors->has('opco_id')): ?> has-error <?php endif; ?>">
    
    
    <input type="hidden" name="opco_id" value=<?php echo e($_ENV['APP_OPCO_ID']); ?> class="form-control">
    <?php if($errors->has('opco')): ?> <p class="help-block"><?php echo e($errors->first('opco')); ?></p> <?php endif; ?>
</div>

<div class="form-group <?php if($errors->has('department')): ?> has-error <?php endif; ?>">
    <?php echo Form::label('department[]', 'Select Department'); ?>

    <?php echo e(Form::select('department', $department->toArray(), null, ['placeholder' => 'Select Department', 'class' => 'form-control'])); ?>

    <?php if($errors->has('department')): ?> <p class="help-block"><?php echo e($errors->first('department')); ?></p> <?php endif; ?>
</div>

<!-- Roles Form Input -->
<div class="form-group <?php if($errors->has('roles')): ?> has-error <?php endif; ?>">
    <?php echo Form::label('roles[]', 'Roles'); ?>

    <?php echo Form::select('roles[]', $roles, isset($user) ? $user->roles->pluck('id')->toArray() : null,  ['class' => 'form-control select2', 'multiple']); ?>

    <?php if($errors->has('roles')): ?> <p class="help-block"><?php echo e($errors->first('roles')); ?></p> <?php endif; ?>
</div>
<!-- Permissions -->
<?php if(isset($user)): ?>
    <?php echo $__env->make('shared._permissions', ['closed' => 'true', 'model' => $user ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php endif; ?>

<style>
    .select2 {
        width: 100% !important;
    }
</style>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit_users')): ?>
    <a href="<?php echo e(route($entity.'.edit', [str_singular($entity) => $id])); ?>" class="btn btn-xs btn-info">
        <i class="fa fa-edit"></i>Edit</a>
<?php endif; ?>

<?php if($item->status == Config::get('enums.user_status.Inactive')): ?>
    <?php echo Form::open(['method' => 'get', 'url' => 'activateuser/'.$id, 'style' => 'display: inline', 'onSubmit' => 'return confirm("Are yous sure wanted to activate it?")']); ?>

        <button type="submit" class="btn btn-xs btn-success">
            <i class="fa fa-trash"></i>Activate
        </button>
    <?php echo Form::close(); ?>

<?php elseif($item->status == Config::get('enums.user_status.Active')): ?>
    <?php echo Form::open(['method' => 'get', 'url' => 'deactivateuser/'.$id, 'style' => 'display: inline', 'onSubmit' => 'return confirm("Are yous sure wanted to deactivate it?")']); ?>

    <button type="submit" class="btn btn-xs btn-warning">
        <i class="fa fa-trash"></i>Deactivate
    </button>
    <?php echo Form::close(); ?>

<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete_users')): ?>
    <?php echo Form::open( ['method' => 'delete', 'url' => route($entity.'.destroy', ['user' => $id]), 'style' => 'display: inline', 'onSubmit' => 'return confirm("Are yous sure wanted to delete it?")']); ?>

        <button type="submit" class="btn-delete btn btn-xs btn-danger">
            <i class="fa fa-trash"></i>Delete
        </button>
    <?php echo Form::close(); ?>

<?php endif; ?>

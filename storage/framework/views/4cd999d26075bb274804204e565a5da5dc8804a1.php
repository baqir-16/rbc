<?php $__env->startSection('title', 'Departments'); ?>

<?php $__env->startSection('content'); ?>
        
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><?php echo e(count($results)); ?> Departments</h3>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('add_users')): ?>
                <a href="<?php echo e(route('departments.create')); ?>" class="btn btn-primary btn-sm btn-flat pull-right"> Create</a>
            <?php endif; ?>
        </div>
        <div class="box-body">
        <div class="table-responsive">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Department</th>
                    <th>Created Date</th>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit_users', 'delete_users')): ?>
                        <th class="text-center">Actions</th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e(($key ++)+1); ?></td>
                        <td><?php echo e($item->department); ?></td>
                        <td><?php echo e(date('d-m-Y', strtotime($item->created_at))); ?></td>
                      
                            <td class="text-center" width="15%;">
                                <a href="<?php echo e(route('departments.edit', [$item->id])); ?>" class="btn btn-xs btn-info"><i class="fa fa-edit"></i> Edit</a>
                                <?php echo Form::open( ['method' => 'delete', 'url' => route('departments.destroy', [$item->id]), 'style' => 'display: inline', 'onSubmit' => 'return confirm("Are you sure delete it?")']); ?>

                            
                                <?php echo Form::close(); ?>

                            </td>
                      
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

        </div>
    </div>
    </div>
<?php $__env->stopSection(); ?>

        <?php $__env->startSection('custom-scripts'); ?>
            <script>
                $(function () {
                    $('#example1').DataTable({
                        'paging'      : true,
                        'lengthChange': true,
                        'searching'   : true,
                        'ordering'    : true,
                        'info'        : true,
                        'autoWidth'   : true
                    });
                });
            </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
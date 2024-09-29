<?php $__env->startSection('title', 'Users'); ?>

<?php $__env->startSection('content'); ?>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"> <?php echo e($result->count()); ?> <?php echo e(str_plural('User', $result->count())); ?> </h3>

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('add_users')): ?>
                <a href="<?php echo e(route('users.create')); ?>" class="btn btn-primary btn-sm btn-flat pull-right"> Create Users</a>
            <?php endif; ?>
        </div>
        <div class="box-body  no-padding">
            <div class="box-body mailbox-messages">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit_users', 'delete_users')): ?>
                            <th class="text-center">Actions</th>
                        <?php endif; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $status_array = array_flip(Config::get('enums.user_status')) ?>
                    <?php $__currentLoopData = $result; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($key+1); ?></td>
                            <td><?php echo e($item->name); ?></td>
                            <td><?php echo e($item->email); ?></td>
                            <?php if(!empty($department[$item->department])): ?>
                                    <td><?php echo e($department[$item->department]); ?></td>
                                <?php else: ?>
                                    <td></td>
                                <?php endif; ?>
                            <td><?php echo e($item->roles->implode('name', ', ')); ?></td>
                            <td><?php echo e($status_array[$item->status]); ?></td>
                            <td><?php echo e($item->created_at); ?></td>

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit_users')): ?>
                                <td class="text-center">
                                    <?php echo $__env->make('shared._actions', [
                                        'entity' => 'users',
                                        'id' => $item->id
                                    ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('custom-scripts'); ?>
    <script type="text/javascript">
        $(function () {
            $('#example1').DataTable({
                'paging'      : true,
                'lengthChange': true,
                'searching'   : true,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : true
            });

            //        $('.mailbox-messages input[type="checkbox"]').iCheck({
            //            checkboxClass: 'icheckbox_flat-blue',
            //            radioClass: 'iradio_flat-blue'
            //       });

            //Enable check and uncheck all functionality
            $(".checkbox-toggle").click(function () {
                var clicks = $(this).data('clicks');
                if (clicks) {
                    //Uncheck all checkboxes
                    $(".mailbox-messages input[type='checkbox']").iCheck("uncheck");
                    $(".fa", this).removeClass("fa-check-square-o").addClass('fa-square-o');
                } else {
                    //Check all checkboxes
                    $(".mailbox-messages input[type='checkbox']").iCheck("check");
                    $(".fa", this).removeClass("fa-square-o").addClass('fa-check-square-o');
                }
                $(this).data("clicks", !clicks);
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->startSection('title', 'Tickets'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('tester_view')): ?>
        <!-- start .flash-message -->
        <div class="flash-message">
            <?php $__currentLoopData = ['danger', 'warning', 'success', 'info']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(Session::has('alert-' . $msg)): ?>
                    <p class="alert alert-<?php echo e($msg); ?>"><?php echo e(Session::get('alert-' . $msg)); ?> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <!-- end .flash-message -->

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Security Tester - Tasks </h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <?php if(count($streams) > 0): ?>
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Ticket Title</th>
                                <th>Module Title</th>
                                <th>Comment</th>
                                <th>Assigned Date & Time</th>
                                <th>Scheduled Date & Time</th>
                                <th class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $streams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $stream): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($stream->id); ?></td>
                                    <td><?php echo e($stream->tickets[0]->title); ?></td>
                                    <td><?php echo e($stream->modules[0]->name); ?></td>
                                    <td><?php echo e(isset($stream->comments[0]->comments) ? $stream->comments[0]->comments: ""); ?></td>
                                    <td><?php echo e($stream->tester_assigned_date); ?></td>
                                    <td><?php echo e($stream->tester_scheduled_date); ?></td>
                                    <td class="text-center">
                                        <?php if($status_arr[$key] == 1): ?>
                                            <a class="btn btn-primary btn-xs" disabled> Add Files</a>
                                            <a href="<?php echo e(url('testerdelup/'.$stream->id)); ?>" class="btn btn-danger btn-xs" data-toggle="example2" data-title="Are you sure you want to remove uploaded data?"> Delete</a>
                                            <a href="<?php echo e(url('forward2analyst/'.$stream->id)); ?>" class="btn btn-info btn-xs" data-toggle="example1" data-title="Are you sure?"> Forward</a>
                                            <a href="<?php echo e(url('testerview', ['id' => $stream->id])); ?>" class="btn btn-primary btn-xs"> View</a>
                                        <?php elseif($status_arr[$key] == 0): ?>
                                            <a href="<?php echo e(route('Tester.create', ['id' => $stream->id])); ?>" class="btn btn-primary btn-xs"> Add Files</a>
                                            <a class="btn btn-danger btn-xs" data-toggle="" data-title="Are you sure you want to remove uploaded data?" disabled> Delete</a>
                                            <?php if($no_file_status_arr[$key] == 1 || $stream->no_findings == Config::get('enums.active_status.Active')): ?>
                                                <a href="<?php echo e(url('forward2analyst/'.$stream->id)); ?>" class="btn btn-info btn-xs" data-toggle="example1" data-title="Are you sure?"> Forward</a>
                                                <a href="<?php echo e(url('testerview', ['id' => $stream->id])); ?>" class="btn btn-primary btn-xs"> View</a>
                                            <?php else: ?>
                                                <a class="btn btn-info btn-xs" disabled> Forward</a>
                                                <a class="btn btn-primary btn-xs" disabled> View</a>

                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <a href="<?php echo e(url('backward2pmo/'.$stream->id)); ?>" class="btn btn-warning btn-xs" data-toggle="example1" data-title="Are you sure?"> Backward</a>

                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-center">There is no task assigned to you at the moment!</p>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('custom-scripts'); ?>
    <script type="text/javascript">
        $('[data-toggle=example1]').confirmation({
            rootSelector: '[data-toggle=example1]',
            // other options
        });
        $('[data-toggle=example2]').confirmation({
            rootSelector: '[data-toggle=example2]',
            // other options
        });
    </script>
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
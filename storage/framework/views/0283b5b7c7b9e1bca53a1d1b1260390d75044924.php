<?php $__env->startSection('title', 'View Tickets'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('pmo_view')): ?>
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="row">
                <div class="col-md-12">
                    <a href="<?php echo e(route('Ticket.index')); ?>" class="btn btn-default btn-flat pull-right">Back</a>
                    <h3 class="box-title">Cyber PMO - Tasks</h3>
                 </div>
            </div>
       <div class="box-body">
        <div class="table-responsive">
            <?php if(count($streams) > 0): ?>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Ticket Ref. No</th>
                        <th>Task Title</th>
                        <th>Tester</th>
                        <th>Analyst</th>
                        <th>QA</th>
                        <th>HoD</th>
                        <th>Progress</th>
                        <th class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $streams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $stream): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($stream->id); ?></td>
                            <td><?php echo e($stream->tickets[0]->ref); ?></td>
                            <td><?php echo e($stream->modules[0]->name); ?></td>
                            <td><?php echo e($username_arr[$key][0]); ?></td>
                            <td><?php echo e($username_arr[$key][1]); ?></td>
                            <td><?php echo e($username_arr[$key][2]); ?></td>
                            <td><?php echo e($username_arr[$key][3]); ?></td>
                            <?php if($stream->status == 1): ?>
                            <td>PMO<div class="progress sm">
                                    <div class="progress-bar progress-bar-aqua" style="width: 1%"></div>
                                </div></td>
                            <?php elseif($stream->status == 2): ?>
                            <td>Tester<div class="progress sm">
                                    <div class="progress-bar progress-bar-aqua" style="width: 20%"></div>
                                </div></td>

                            <?php elseif($stream->status == 3): ?>
                                <td>Analyst <br><div class="progress sm">
                                        <div class="progress-bar progress-bar-aqua" style="width: 40%"></div> </div></td>

                            <?php elseif($stream->status == 4): ?>
                                <td>QA<div class="progress sm">
                                        <div class="progress-bar progress-bar-aqua" style="width: 60%"></div> </div></td>

                            <?php elseif($stream->status == 5): ?>
                                <td>HOD<div class="progress sm">
                                        <div class="progress-bar progress-bar-aqua" style="width: 80%"></div> </div></td>

                            <?php else: ?>
                                <td>Completed<div class="progress sm">
                                        <div class="progress-bar progress-bar-green" style="width: 100%"></div> </div></td>

                            <?php endif; ?>

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('pmo_view')): ?>
                            <td class="text-center">
                                <a href="<?php echo e(url('assign_tasks/'.$stream->id)); ?>" class="btn btn-primary btn-xs"> Assign</a>
                                <?php if($forward_status[$key]==1): ?>
                                    <?php if($stream->status == Config::get('enums.stream_status.PMO')): ?>
                                        <a class="btn btn-info btn-xs" data-toggle="example1" data-title="Are you sure?" href="<?php echo e(url('forward2tester/'.$stream->id)); ?>"> Forward</a>
                                    <?php else: ?>
                                        <a class="btn btn-info btn-xs"  disabled> Forward</a>
                                    <?php endif; ?>
                                <?php elseif($forward_status[$key]==0): ?>
                                    <a class="btn btn-info btn-xs" disabled> Forward</a>
                                <?php endif; ?>
                                
                            </td>
                             <?php endif; ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <div class="text-center">
                    <?php echo $streams->links();; ?>

                </div>
        </div>
        <?php else: ?>
            <p class="text-center">All tasks have been forwarded for the selected ticket!</p>

        <?php endif; ?>
    </div>
    </div>
 <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('custom-scripts'); ?>
    <script type="text/javascript">
        $('[data-toggle=example1]').confirmation({
            rootSelector: '[data-toggle=example1]',
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
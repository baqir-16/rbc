<?php $__env->startSection('title', 'Analyst'); ?>

<?php $__env->startSection('content'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('analyst_view')): ?>
<div class="box box-primary">
  <div class="box-header with-border">
      <h3 class="box-title"> Security Analyst - Tasks </h3>
        </div>
    <div class="box-body">
        <div class="table-responsive">
            <?php if(count($streams) > 0): ?>
            <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>Ticket Title</th>
                <th>Task Title</th>
                <th>Comment</th>
                <th>Assigned Date</th>
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
                <td><?php echo e($stream->analyst_assigned_date); ?></td>
                <td class="text-center" style="width:23%;">
                    <a href="<?php echo e(route('Report.index', ['id' => $stream['id']])); ?>" class="btn btn-primary btn-xs"> View Details</a>
                    <?php if($stream->no_findings == Config::get('enums.active_status.Active')): ?>
                        <a href="<?php echo e(url('forward2qa/'.$stream->id)); ?>" class="btn btn-info btn-xs" data-toggle="example1" data-title="Are you sure?"> Forward</a>
                    <?php elseif($issue_count_array[$key] == 0): ?>
                        <a class="btn btn-info btn-xs"  disabled> Forward</a>
                    <?php elseif($analyst_forward_status[$key] == 0): ?>
                        <a href="<?php echo e(url('forward2qa/'.$stream->id)); ?>" class="btn btn-info btn-xs" data-toggle="example1" data-title="Are you sure?"> Forward</a>
                    <?php elseif($analyst_forward_status[$key] > 0): ?>
                        <a class="btn btn-info btn-xs"  disabled> Forward</a>
                    <?php endif; ?>
                    <a href="<?php echo e(url('backward2tester/'.$stream->id)); ?>" class="btn btn-warning btn-xs" data-toggle="example1" data-title="Are you sure?"> Backward</a>
                    <a href="<?php echo e(url('revalidated/'.$stream->id)); ?>" class="btn btn-primary btn-xs"> Revalidate</a>
                </td>
              </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <div class="text-center">
            <?php echo $streams->links();; ?>

        </div>
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
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
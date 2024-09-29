<?php $__env->startSection('title', 'Create Ticket'); ?>
<?php $__env->startSection('content'); ?>

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
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('pmo_view')): ?>
    <div class="box-header with-border">
        <h3 class="box-title">List of Tickets </h3>  <a href="<?php echo e(route('Ticket.create')); ?>" class="btn btn-primary btn-sm btn-flat pull-right">  Create Ticket</a>
    </div>
    <div class="box-body">
    <div class="table-responsive">
    <?php if(count($tickets) > 0): ?>
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Departments</th>
                <th>Reference No</th>
                <th>Cyber PMO</th>
                <th>Comment</th>
                
                <th>Created Date</th>
                <th class="text-center">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($ticket->id); ?></td>
                    <td><?php echo e($ticket->title); ?></td>
                    <?php if(!empty($department[$ticket->department])): ?>
                                    <td><?php echo e($department[$ticket->department]); ?></td>
                                <?php else: ?>
                                    <td></td>
                                <?php endif; ?>
                    <td><?php echo e($ticket->ref); ?></td>
                    <td><?php echo e($ticket->user->username); ?></td>
                    <td><?php echo e($ticket->comments[0]->comments); ?></td>
                    
                    <td><?php echo e($ticket->created_at); ?></td>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('pmo_view')): ?>
                    <td class="text-center" style="width:23%;">
                        <a href="<?php echo e(route('PMO.index', ['id'=>$ticket->id])); ?>" class="btn btn-primary btn-xs">View</a>
                        <?php if($ticket_edit_status[$key] == true): ?>
                            <a href="<?php echo e(route('Ticket.edit', ['id'=>$ticket->id])); ?>" class="btn btn-info btn-xs">Edit</a>
                        <?php else: ?>
                            <a class="btn btn-info btn-xs" disabled>Edit</a>
                        <?php endif; ?>
                        <?php if($ticket_edit_status[$key] == true): ?>
                            <a href="<?php echo e(url('delTic/'.$ticket->id)); ?>" class="btn btn-danger btn-xs" data-toggle="delete" data-title="Are you sure?">Delete</a>
                        <?php else: ?>
                            <a class="btn btn-danger btn-xs" disabled>Delete</a>
                        <?php endif; ?>
                    </td>
                        <?php endif; ?>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
        <div class="text-center">
            <?php echo $tickets->links();; ?>

        </div>
    <?php else: ?>
        <p class="text-center">Nothing to display!</p>
    <?php endif; ?>
    </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('custom-scripts'); ?>
    <script type="text/javascript">
        $('[data-toggle=delete]').confirmation({
            rootSelector: '[data-toggle=delete]',
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
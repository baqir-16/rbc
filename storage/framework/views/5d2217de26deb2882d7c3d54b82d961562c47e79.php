<?php $__env->startSection('title', 'Create'); ?>

<?php $__env->startSection('content'); ?>

<div class="box box-primary">
  <div class="box-header with-border">
            <h3 class="box-title">Create</h3>
            <a href="<?php echo e(route('Ticket.index')); ?>" class="btn btn-default btn-sm pull-right"> <i class="fa fa-arrow-left"></i> Back</a>
      </div>

 <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('pmo_view')): ?>
<div class="animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="box-body">
            <?php echo Form::open(['route' => ['Ticket.store'] ]); ?>

                <?php echo $__env->make('Ticket._create', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <!-- Submit Form Button -->
                <?php echo Form::submit('Create', ['class' => 'btn btn-primary']); ?>

            <?php echo Form::close(); ?>

        </div>
    </div>
  </div>
 </div>
<?php endif; ?>
   </div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
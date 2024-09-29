<?php $__env->startSection('title', 'Tester '); ?>
<?php $__env->startSection('content'); ?>
    <div class="box">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Create Report</h3>
                <a href="<?php echo e(URL::previous()); ?>" class="btn btn-default btn-sm pull-right"> <i class="fa fa-arrow-left"></i> Back</a>
            </div>
            <?php echo e(Form::open(array('action'=>'AnalystController@issueNexpose', 'method' => 'post', 'files' => true, 'enctype'=>'multipart/form-data'))); ?>

            <div class="box-body">
                <div class="form-group">
                    <label>Select Risk Level</label>
                    <select class="form-control" name="risk">
                        <option value="1">Informational</option>
                        <option value="2">Low</option>
                        <option value="3">Medium</option>
                        <option value="4">High</option>
                        <option value="5">Critical</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Host</label>
                    <input type="text" class="form-control" name="host" placeholder="Enter ...">
                    <?php if($errors->has('host')): ?> <p class="help-block" style="color:red;">The host field is required.</p> <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>Select Category</label>
                    <select class="form-control" name="category">
                        <option value="1">IDP</option>
                        <option value="2">External</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Port</label>
                    <input type="text" class="form-control" name="port" placeholder="Enter ...">
                </div>
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" placeholder="Enter ...">
                    <?php if($errors->has('name')): ?> <p class="help-block" style="color:red;">The name field is required.</p> <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>OS</label>
                    <input type="text" class="form-control" name="OS" placeholder="Enter ...">
                </div>
                <div class="form-group">
                    <label>OS Version</label>
                    <input type="text" class="form-control" name="OS_version" placeholder="Enter ...">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" rows="3" name="description" placeholder="Enter ..."></textarea>
                </div>
                <div class="form-group">
                    <label>Fix</label>
                    <textarea class="form-control" rows="3" name="fix" placeholder="Enter ..."></textarea>
                </div>
                <div class="form-group">
                    <label>Summary</label>
                    <textarea class="form-control" rows="3" name="summary" placeholder="Enter ..."></textarea>
                </div>
            </div>
            <input type="hidden" name="stream_id" value="<?php echo e($stream_id); ?>">
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
            <?php echo e(Form::close()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
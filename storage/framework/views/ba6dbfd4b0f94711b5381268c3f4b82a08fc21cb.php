<?php $__env->startSection('title', 'Tester '); ?>
<?php $__env->startSection('content'); ?>
    <div class="box">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Create Report</h3>
                <a href="<?php echo e(URL::previous()); ?>" class="btn btn-default btn-sm pull-right"> <i class="fa fa-arrow-left"></i> Back</a>
            </div>
            <?php echo e(Form::open(array('action'=>'AnalystController@issuecsv', 'method' => 'post', 'files' => true, 'enctype'=>'multipart/form-data'))); ?>

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
                    <label>Select Category</label>
                    <select class="form-control" name="category">
                        <option value="1">IDP</option>
                        <option value="2">External</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Host</label>
                    <input type="text" class="form-control" name="host" placeholder="Enter ...">
                    <?php if($errors->has('host')): ?> <p class="help-block" style="color:red;">The host field is required.</p> <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>Protocol</label>
                    <input type="text" class="form-control" name="protocol" placeholder="Enter ...">
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
                    <label>Synopsis</label>
                    <input type="text" class="form-control" name="synopsis" placeholder="Enter ...">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" rows="3" name="description" placeholder="Enter ..."></textarea>
                </div>
                <div class="form-group">
                    <label>Solution</label>
                    <textarea class="form-control" rows="3" name="solution" placeholder="Enter ..."></textarea>
                </div>
                <div class="form-group">
                    <label>Technical Details</label>
                    <textarea class="form-control" rows="3" name="plugin_output" placeholder="Enter ..."></textarea>
                </div>
                <div class="form-group">
                    <label>Comment</label>
                    <textarea class="form-control" rows="3" name="comment" placeholder="Enter ..."></textarea>
                </div>
                <div class="form-group">
                    <label for="exampleInputFile">Upload Image</label>
                    <input type="file" id="exampleInputFile" name="img_filename[]" accept="image/*" multiple>
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
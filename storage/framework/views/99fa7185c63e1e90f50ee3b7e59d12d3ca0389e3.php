<?php $__env->startSection('title', 'Analyst '); ?>
<?php $__env->startSection('content'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('qa_view')): ?>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Review Report Details</h3>
                        <div class="box-tools">
                            <div class="input-group input-group-sm" style="width: 150px;">
                                <a href="<?php echo e(URL::previous()); ?>" class="btn btn-default btn-sm pull-right"> <i class="fa fa-arrow-left"></i> Back</a>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <b>Name: </b> <p><?php echo e($issue['name']); ?></p>
                            </li>
                            <li class="list-group-item">
                                <b>Port: </b> <p><?php echo e($issue['port']); ?></p>
                            </li>
                            <li class="list-group-item">
                                <b>Host: </b> <p><?php echo e($issue['host']); ?></p>
                            </li>
                            <li class="list-group-item">
                                <b>Technical Details: </b> <p><?php echo e($issue['plugin_output']); ?></p>
                            </li>
                            <li class="list-group-item">
                                <b>Risk: </b> <p><span class="label <?php echo e(($issue['risk']==Config::get('enums.severity_status.Informational')) ? "label-info" :
                          (((($issue['risk']==Config::get('enums.severity_status.Low'))  ? "label-success" :
                          (((($issue['risk']==Config::get('enums.severity_status.Medium'))  ? "bg-orange" :
                          (((($issue['risk']==Config::get('enums.severity_status.High'))  ? "bg-red" :
                          (((($issue['risk']==Config::get('enums.severity_status.Critical'))  ? "label-dark" :
                          "empty"))))))))))))); ?>"><?php echo e($enums[$issue['risk']]); ?></span></p>
                            </li>
                            <li class="list-group-item">
                                <b>Description: </b> <p><?php echo e($issue['description']); ?></p>
                            </li>
                            <li class="list-group-item">
                                <b>Solutions: </b> <p><?php echo e($issue['solution']); ?></p>
                            </li>
                        </ul>
                        <?php if(isset($issue['img_filename'])): ?>
                            <div class="form-group">
                                <div class="row">
                                    <?php $__currentLoopData = $issue['img_filename']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="col-md-4">
                                            <div class="thumbnail">
                                                <a href="<?php echo e(Config::get('remote.connections.MDB_SERVER.host_http').'/img/'.$img); ?>" target="_blank">
                                                    <img src="<?php echo e(Config::get('remote.connections.MDB_SERVER.host_http').'/img/'.$img); ?>" alt="<?php echo e($img); ?>" style="width:100%">
                                                    
                                                    
                                                    
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php echo e(Form::open(array('action'=>'QAController@store', 'method' => 'post', 'files' => true, 'enctype'=>'multipart/form-data'))); ?>

                    <div class="box-body">
                        <?php if($issue['reverify']==1): ?>
                        <div class="form-group">
                            <label>Verify / unVerify</label>
                            <select name="is_verified" class="form-control">
                                <option value="<?php echo e($issue['is_verified']); ?>" selected disabled hidden>Choose Option</option>
                                <option value="1">Verify</option>
                                <option value="0">unVerify</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Need Revalidation?</label>
                            <select name="revalidate" class="form-control">
                                <option value="<?php echo e($issue['revalidate']); ?>" selected disabled hidden>Choose Option</option>
                                <option value="1">ReValidate</option>
                                <option value="0">unRevalidate</option>
                            </select>
                        </div>
                        <?php if(!empty($all_comments[0])): ?>
                            <label>Previous Comments</label>
                            <?php $__currentLoopData = $all_comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <ul class="list-unstyled">
                                    <li style="border-bottom: 1px solid #eee; padding: 10px 10px;"><strong> <?php echo e($cm->username); ?> </strong> : <?php echo e($cm->comments); ?>

                                        <span class="pull-right"> | <?php echo e($cm->created_at); ?> |
                                            <?php if($cm->user_id == Auth::user()->id): ?>
                                                <a href="<?php echo e(url('removecomment', $cm->id)); ?>" class="btn btn-xs btn-danger"> X </a>
                                            <?php else: ?>
                                                <a href="#" class="btn btn-xs btn-danger" disabled> X </a>
                                            <?php endif; ?>
                                </span>
                                    </li>
                                </ul>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        <br>
                        <div class="form-group">
                            <label>Add New Comment</label>
                            <textarea class="form-control" rows="3" name="comment" placeholder="Enter ..."></textarea>
                        </div>
                        <input type="hidden" name="_id" value="<?php echo e($issue['_id']); ?>">
                        <input type="hidden" name="stream_id" value="<?php echo e($issue['stream_id']); ?>">
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php echo e(Form::close()); ?>

                </div>
            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
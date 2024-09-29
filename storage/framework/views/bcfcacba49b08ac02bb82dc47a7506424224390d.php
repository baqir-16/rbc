<?php $__env->startSection('title', 'Review '); ?>
<?php $__env->startSection('content'); ?>
    <link href="<?php echo e(asset("/plugins/timepicker/bootstrap-timepicker.min.css")); ?>" rel="stylesheet" type="text/css" />

    <!-- start .flash-message -->
    <div class="flash-message">
        <?php $__currentLoopData = ['danger', 'warning', 'success', 'info']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(Session::has('alert-' . $msg)): ?>
                <p class="alert alert-<?php echo e($msg); ?>"><?php echo e(Session::get('alert-' . $msg)); ?> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <!-- end .flash-message -->

    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Details</h3>
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
                            <b>Department: </b> <p><?php echo e($departments[$issue['department']]); ?></p>
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
                            <b>Description: </b> <p><?php echo e($issue['description']); ?></p>
                        </li>
                        <li class="list-group-item">
                            <b>Solutions: </b> <p><?php echo e($issue['solution']); ?></p>
                        </li>
                    </ul>
                </div>
                <?php echo e(Form::open(array('action'=>'RemofficerController@store', 'method' => 'post', 'files' => true, 'enctype'=>'multipart/form-data'))); ?>

                <div class="box-body">
                    <?php $status_array = array_flip(Config::get('enums.mdb_stream_status')); $remove = ['1', '2']; $status_array2 = array_diff_key($status_array, array_flip($remove));?>
                    <div class="form-group <?php if($errors->has('status')): ?> has-error <?php endif; ?>">
                        <?php echo Form::label('Status'); ?>

                        <?php echo Form::select('status', $status_array2, isset($issue['rem_officer_rem_status']) ? $issue['rem_officer_rem_status'] : null, ['class' => 'form-control status', 'onchange' => 'changeStatus(this.value)']); ?>

                        <?php if($errors->has('status')): ?> <p class="help-block">The status field is required.</p> <?php endif; ?>
                    </div>
                    <?php $risk_array = array_flip(Config::get('enums.severity_status')); ?>
                    <div class="form-group RiskSeverity <?php if($errors->has('risk')): ?> has-error <?php endif; ?>">
                        <?php echo Form::label('Risk'); ?>

                        <?php echo Form::select('risk', $risk_array, isset($issue['risk']) ? $issue['risk'] : null, ['class' => 'form-control']); ?>

                        <?php if($errors->has('risk')): ?> <p class="help-block">The risk field is required.</p> <?php endif; ?>
                    </div>
                    <div class="form-group CategoryVul <?php if($errors->has('vul_category_id')): ?> has-error <?php endif; ?>">
                        <?php echo Form::label('Category'); ?>

                        <?php echo Form::select('vul_category_id', $vul_categories, isset($issue['vul_category']) ? $issue['vul_category'] : null, ['class' => 'form-control']); ?>

                        <?php if($errors->has('vul_category_id')): ?> <p class="help-block">The category field is required.</p> <?php endif; ?>
                    </div>
                    <div class="form-group fixDate <?php if($errors->has('datetime')): ?> has-error <?php endif; ?>">
                        <label>Target Fix Date:</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-clock-o"></i>
                            </div>
                            <input type="text" class="form-control pull-right" id="datepicker1" name="datetime" value="<?php echo e(isset($issue['target_fix_date']) ? $issue['target_fix_date'] : null); ?>">
                        </div>
                        <?php if($errors->has('datetime')): ?> <p class="help-block">The datetime field is required.</p> <?php endif; ?>
                    </div>
                    <div class="form-group imgUpload">
                        <label for="exampleInputFile">Upload Image (POC)</label>
                        <input type="file" id="exampleInputFile" name="img_filename[]" class="form-control upload-file" accept="image/*" multiple>
                    </div>
                    <div class="form-group pdfUpload">
                        <label for="exampleInputFile">Upload PDF</label>
                        <input type="file" id="exampleInputFile" name="pdf_filename[]" class="upload-file" accept="application/pdf" multiple>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <?php if(isset($issue['img_filename'])): ?>
                                <?php $__currentLoopData = $issue['img_filename']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-4">
                                        <b>Analyst Vulnerability POC</b>
                                        <div class="thumbnail">
                                            <a href="<?php echo e(Config::get('remote.connections.MDB_SERVER.host_http').'/img/'.$img); ?>" target="_blank">
                                                <img src="<?php echo e(Config::get('remote.connections.MDB_SERVER.host_http').'/img/'.$img); ?>" alt="<?php echo e($img); ?>" style="width:100%">
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </div>
                        <div class="row">
                            <?php if(isset($issue['rem_officer_img_filename'])): ?>
                                <?php $__currentLoopData = $issue['rem_officer_img_filename']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-4" style="padding:30px;">
                                        <b>Remediation Officer Vulnerability POC</b>
                                        <div class="thumbnail">
                                            <a href="<?php echo e(Config::get('remote.connections.MDB_SERVER.host_http').'/img/'.$img); ?>" target="_blank">
                                                <img src="<?php echo e(Config::get('remote.connections.MDB_SERVER.host_http').'/img/'.$img); ?>" alt="<?php echo e($img); ?>" style="width:100%">
                                            </a>
                                            <a href="<?php echo e(url('remofficer_delete_img/'.$issue['_id'].'/'.$img.'/Remofficer')); ?>" class="btn btn-danger" data-toggle="example1" data-title="Are you sure?">Delete</a>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </div>
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
                        <label>Add Comment</label>
                        <textarea class="form-control" rows="3" name="comment" placeholder="Enter ..."></textarea>
                    </div>
                    <input type="hidden" name="_id" value="<?php echo e($issue['_id']); ?>">
                    <input type="hidden" name="stream_id" value="<?php echo e($issue['stream_id']); ?>">
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <?php if(isset($issue['vul_category'])): ?>
                            <button onclick="window.location='<?php echo e(url('forward', ['_id' => $issue['_id']])); ?>'"  type="button" class="btn btn-success btn-xs " style="height:35px">Remediated</button>
                        <?php else: ?>
                            <button  class="btn btn-success btn-xs " type="button" style="height:35px" disabled>Remediated</button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php echo e(Form::close()); ?>

            </div>
        </div>
    </div>
        <?php $__env->stopSection(); ?>
        <?php $__env->startSection('custom-scripts-2'); ?>
            <script>
                var currentStatus = $('.status').val();
                if(currentStatus === '<?php echo Config::get('enums.mdb_stream_status.Open') ?>'){
                    $('.imgUpload').show();
                    $('.pdfUpload').hide();
                    $('.RiskSeverity').show();
                    $('.CategoryVul').show();
                    $('.fixDate').show();
                }else if(currentStatus === '<?php echo Config::get('enums.mdb_stream_status.Exception') ?>'){
                    $('.imgUpload').hide();
                    $('.pdfUpload').show();
                    $('.RiskSeverity').show();
                    $('.CategoryVul').show();
                    $('.fixDate').show();
                }else if(currentStatus === '<?php echo Config::get('enums.mdb_stream_status.FP') ?>'){
                    $('.imgUpload').show();
                    $('.pdfUpload').hide();
                    $('.RiskSeverity').hide();
                    $('.CategoryVul').hide();
                    $('.fixDate').hide();
                }

                function changeStatus(val) {
                    if(val === '<?php echo Config::get('enums.mdb_stream_status.Open') ?>'){
                        $('.imgUpload').show();
                        $('.pdfUpload').hide();
                        $('.RiskSeverity').show();
                        $('.CategoryVul').show();
                        $('.fixDate').show();
                    }else if(val === '<?php echo Config::get('enums.mdb_stream_status.Exception') ?>'){
                        $('.imgUpload').hide();
                        $('.pdfUpload').show();
                        $('.RiskSeverity').show();
                        $('.CategoryVul').show();
                        $('.fixDate').show();
                    }else if(val === '<?php echo Config::get('enums.mdb_stream_status.FP') ?>'){
                        $('.imgUpload').show();
                        $('.pdfUpload').hide();
                        $('.RiskSeverity').hide();
                        $('.CategoryVul').hide();
                        $('.fixDate').hide();
                    }

                }

                $('#datepicker1').datepicker({
                    format: 'yyyy-mm-dd',
                    setDate: new Date(),
                    autoclose: true
                });

                <?php if(!isset($issue['target_fix_date'])){ ?>
                $("#datepicker1").datepicker().datepicker("setDate", new Date());
                <?php }else{ ?>
                $("#datepicker1").datepicker().datepicker("setDate", <?php echo $issue['target_fix_date'] ?>);
                <?php } ?>

                $('[data-toggle=example1]').confirmation({
                    rootSelector: '[data-toggle=example1]',
                    // other options
                });
            </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
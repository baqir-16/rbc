<?php $__env->startSection('title', 'Edit Ticket '); ?>

<?php $__env->startSection('content'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('tester_view')): ?>
    <link href="<?php echo e(asset("/bower_components/bootstrap-daterangepicker/daterangepicker.css")); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset("/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css")); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset("/bower_components/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css")); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset("/plugins/timepicker/bootstrap-timepicker.min.css")); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('css/dropzone.css')); ?>" rel="stylesheet">
    <style>
.upload-file {
    width: 100%;
    padding:20px;
    border: 1px solid #ddd !important;
    min-height: 65px!important;
}
</style>

    <?php if((int)$stream->module_id == 1): ?>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Upload Scanner Results</h3>
            <a href="<?php echo e(route('Tester.index', ['stream_id' => $stream['stream_id']])); ?>" class="btn btn-default btn-sm pull-right"> <i class="fa fa-arrow-left"></i> Back</a>
        </div>
        <div class="box-body">
        <?php echo e(Form::open(array('action'=>'TesterController@store', 'class' => '_dropzone', 'id' => 'uploadFiles', 'method' => 'post', 'files' => true, 'enctype'=>'multipart/form-data'))); ?>

            <?php echo e(csrf_field()); ?>

            <div class="form-group">
                <label>Date and Time:</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-clock-o"></i>
                    </div>
                    <input type="text" class="form-control pull-right" id="datetimepicker1" name="datetime">
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Comment</label>
                <textarea class="form-control" name="comment" rows="3" placeholder="Enter comment..."></textarea>
            </div>
            <div class="form-group">
                <input name="file[]" class="upload-file" type="file" accept=".nessus" multiple />
            </div>
            <input type="hidden" name="id" value="<?php echo e($stream->id); ?>">
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
                <input type="checkbox" id="nofile" name="nofile" style="margin-left: 1%"><label for="nofile" style="margin-left: 0.5%">No file upload</label>
            </div>
        <?php echo e(Form::close()); ?>

        </div>
    </div>
    <?php elseif((int)$stream->module_id == 2): ?>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Upload Scanner Results</h3>
            <a href="<?php echo e(route('Tester.index', ['stream_id' => $stream['stream_id']])); ?>" class="btn btn-default btn-sm pull-right"> <i class="fa fa-arrow-left"></i> Back</a>
        </div>
        <div class="box-body">
            <?php echo e(Form::open(array('action'=>'TesterController@storeXML', 'class' => '_dropzone', 'id' => 'uploadFiles', 'method' => 'post', 'files' => true, 'enctype'=>'multipart/form-data'))); ?>

            <?php echo e(csrf_field()); ?>

            <div class="form-group">
                <label>Date and Time:</label>
                <div class="input-group">
                    <div class="input-group-addon">
                        <i class="fa fa-clock-o"></i>
                    </div>
                    <input type="text" class="form-control pull-right" id="datetimepicker1" name="datetime">
                </div>
                <!-- /.input group -->
            </div>
            <div class="form-group">
                <label>Comment</label>
                <textarea class="form-control" name="comment" rows="3" placeholder="Enter comment..."></textarea>
            </div>
            <div class="form-group">
                <input name="file[]" type="file" class="upload-file" accept=".xml" multiple />
            </div>
            <input type="hidden" name="id" value="<?php echo e($stream->id); ?>">
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
                <input type="checkbox" id="nofile" name="nofile" style="margin-left: 1%"><label for="nofile" style="margin-left: 0.5%">No file upload</label>
            </div>
            <?php echo e(Form::close()); ?>

        </div>
    </div>
    <?php elseif((int)$stream->module_id == 3): ?>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Upload Scanner Results for Nexpose <span style="font-size:11px; color:red;"> (Note:File should be in in .csv format | Vulnerability Title, Severity/Vulnerability CVE and Ip_Address should not be empty)</span></h3>
                <a href="<?php echo e(route('Tester.index', ['stream_id' => $stream['stream_id']])); ?>" class="btn btn-default btn-sm pull-right"> <i class="fa fa-arrow-left"></i> Back</a>
            </div>
            <div class="box-body">
                <?php echo e(Form::open(array('action'=>'TesterController@storeNexpose', 'class' => '_dropzone', 'id' => 'uploadFiles', 'method' => 'post', 'files' => true, 'enctype'=>'multipart/form-data'))); ?>

                <?php echo e(csrf_field()); ?>

                <div class="form-group">
                    <label>Date and Time:</label>
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-clock-o"></i>
                        </div>
                        <input type="text" class="form-control pull-right" id="datetimepicker1" name="datetime">
                    </div>
                    <!-- /.input group -->
                </div>
                <div class="form-group">
                    <label>Comment</label>
                    <textarea class="form-control" name="comment" rows="3" placeholder="Enter comment..."></textarea>
                </div>
                <div class="form-group">
                    <input name="file" type="file" class="upload-file" accept=".csv" multiple/>
                </div>
                <input type="hidden" name="id" value="<?php echo e($stream->id); ?>">
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <input type="checkbox" id="nofile" name="nofile" style="margin-left: 1%"><label for="nofile" style="margin-left: 0.5%">No file upload</label>
                </div>
                <?php echo e(Form::close()); ?>

            </div>
        </div>
    <?php elseif((int)$stream->module_id == 4): ?>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Upload Scanner Results</h3>
                <a href="<?php echo e(route('Tester.index', ['stream_id' => $stream['stream_id']])); ?>" class="btn btn-default btn-sm pull-right"> <i class="fa fa-arrow-left"></i> Back</a>
            </div>
            <div class="box-body">
                <?php echo e(Form::open(array('action'=>'TesterController@storeAppscan', 'class' => '_dropzone', 'id' => 'uploadFiles', 'method' => 'post', 'files' => true, 'enctype'=>'multipart/form-data'))); ?>

                <?php echo e(csrf_field()); ?>

                <div class="form-group">
                    <label>Date and Time:</label>
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-clock-o"></i>
                        </div>
                        <input type="text" class="form-control pull-right" id="datetimepicker1" name="datetime">
                    </div>
                    <!-- /.input group -->
                </div>
                <div class="form-group">
                    <label>Comment</label>
                    <textarea class="form-control" name="comment" rows="3" placeholder="Enter comment..."></textarea>
                </div>
                <div class="form-group">
                    <input name="file[]" type="file" class="upload-file" accept=".xml" multiple />
                </div>
                <input type="hidden" name="id" value="<?php echo e($stream->id); ?>">
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <input type="checkbox" id="nofile" name="nofile" style="margin-left: 1%"><label for="nofile" style="margin-left: 0.5%">No file upload</label>
                </div>
                <?php echo e(Form::close()); ?>

            </div>
        </div>
    <?php elseif((int)$stream->module_id == 5): ?>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Upload Scanner Results</h3>
                <a href="<?php echo e(route('Tester.index', ['stream_id' => $stream['stream_id']])); ?>" class="btn btn-default btn-sm pull-right"> <i class="fa fa-arrow-left"></i> Back</a>
            </div>
            <div class="box-body">
                <?php echo e(Form::open(array('action'=>'TesterController@storeBurpsuite', 'class' => '_dropzone', 'id' => 'uploadFiles', 'method' => 'post', 'files' => true, 'enctype'=>'multipart/form-data'))); ?>

                <?php echo e(csrf_field()); ?>

                <div class="form-group">
                    <label>Date and Time:</label>
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-clock-o"></i>
                        </div>
                        <input type="text" class="form-control pull-right" id="datetimepicker1" name="datetime">
                    </div>
                    <!-- /.input group -->
                </div>
                <div class="form-group">
                    <label>Comment</label>
                    <textarea class="form-control" name="comment" rows="3" placeholder="Enter comment..."></textarea>
                </div>
                <div class="form-group">
                    <input name="file[]" type="file" class="upload-file" accept=".xml" multiple />
                </div>
                <input type="hidden" name="id" value="<?php echo e($stream->id); ?>">
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <input type="checkbox" id="nofile" name="nofile" style="margin-left: 1%"><label for="nofile" style="margin-left: 0.5%">No file upload</label>
                </div>
                <?php echo e(Form::close()); ?>

            </div>
        </div>
    <?php endif; ?>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('custom-scripts-2'); ?>
    <script>
        $(function () {
            $('#datetimepicker1').datetimepicker({
                defaultDate: moment(),
            });

            $('[name="nofile"]').change(function() {
                if ($(this).is(':checked')) {
                    $(".upload-file").prop('disabled', true);
                } else {
                    $(".upload-file").prop('disabled', false);
                };
            });
        })
    </script>

    
    <script type="text/javascript">
        Dropzone.autoDiscover = false;

        $(document).ready(function(){
            Dropzone.options.uploadFiles = {
                paramName: "file",  // The name that will be used to transfer the file
                maxFilesize: 100,   // MB
                acceptedFiles: ".csv,.xml,.xlsx",
                addRemoveLinks: true,
                dictCancelUpload: 'Cancel',
                url: 'post.php',
            };
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
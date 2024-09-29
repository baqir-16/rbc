<?php $__env->startSection('title', 'Edit Profile ' . $user->first_name); ?>
<?php $__env->startSection('content'); ?>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Edit <?php echo e($user->first_name); ?></h3>

            <a href="<?php echo e(route('profiles.index')); ?>" class="btn btn-default btn-sm pull-right"> <i class="fa fa-arrow-left"></i> Back</a>

        </div>

        <div class="animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="box-body">
                        <?php echo Form::model($user, ['method' => 'PUT', 'route' => ['profiles.update',  $user->id ] ,'files'=>true]); ?>

                        <!-- Name Form Input -->
                            <div class="col-md-12">
                                <ul class="list-group list-group-unbordered">
                                    <li class="list-group-item">
                                        <b>Profile Picture</b>
                                        <div class="user-panel">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <?php if(Auth::user()->avatar != "avatar.jpg"): ?>
                                                            <img id="image1" src="<?php echo e(Config::get('remote.connections.MDB_SERVER.host_http').'/profile_img/'.Auth::user()->avatar); ?>" alt="<?php echo e(Auth::user()->avatar); ?>" style="border:2px solid #a3d7ff;width:100px;height:100px" class="img-circle" >
                                                        <?php elseif(Auth::user()->avatar == "avatar.jpg"): ?>
                                                            <img id="image1" src="<?php echo asset('/profile'); ?>/<?php echo e(Auth::User()->avatar); ?>" class="img-circle" style="border:2px solid #a3d7ff; height:95px; width: 95px;" alt="User Image" />
                                                        <?php endif; ?>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="hide">
                                            <label class="hand-cursor  btn btn-default">
                                                <input type="file" id="exampleInputFile" name="img_filename" accept="image/*" name="avatar"  class="btn btn-default" onchange="readURL(this);"/>
                                                <span class="fa fa-camera"></span>
                                                <span class="photo_text hidden-xs"> Upload Picture</span>
                                            </label>
                                        </div>
                            <div class="form-group <?php if($errors->has('name')): ?> has-error <?php endif; ?>">
                                <?php echo Form::label('name', 'Name'); ?>

                                <?php echo Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Name']); ?>

                                <?php if($errors->has('name')): ?> <p class="help-block"><?php echo e($errors->first('name')); ?></p> <?php endif; ?>
                            </div>
                            <!-- UserName Form Input -->
                            <div class="form-group <?php if($errors->has('username')): ?> has-error <?php endif; ?>">
                                <?php echo Form::label('username', 'Username'); ?>

                                <?php echo Form::text('username', null, ['class' => 'form-control', 'placeholder' => 'Username', 'disabled' => 'true']); ?>

                                <?php if($errors->has('username')): ?> <p class="help-block"><?php echo e($errors->first('username')); ?></p> <?php endif; ?>
                            </div>
                            <div class="form-group <?php if($errors->has('password')): ?> has-error <?php endif; ?>">
                                <?php echo Form::label('password', 'New Password'); ?>

                                <?php echo Form::password('password', ['class' => 'form-control', 'placeholder' => 'New Password', 'autocomplete' => 'off']); ?>

                                <?php if($errors->has('password')): ?> <p class="help-block"><?php echo e($errors->first('password')); ?></p> <?php endif; ?>
                            </div>
                            <div class="form-group <?php if($errors->has('password_confirmation')): ?> has-error <?php endif; ?>">
                                <?php echo Form::label('password_confirmation', 'Confirm New Password'); ?>

                                <?php echo Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => 'Confirm New Password', 'autocomplete' => 'off']); ?>

                                <?php if($errors->has('password_confirmation')): ?> <p class="help-block"><?php echo e($errors->first('password_confirmation')); ?></p> <?php endif; ?>
                            </div>
                            <!-- email Form Input -->
                            <div class="form-group <?php if($errors->has('email')): ?> has-error <?php endif; ?>">
                                <?php echo Form::label('email', 'Email'); ?>

                                <?php echo Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Email']); ?>

                                <?php if($errors->has('email')): ?> <p class="help-block"><?php echo e($errors->first('email')); ?></p> <?php endif; ?>
                            </div>
                            <?php echo Form::submit('Save Changes', ['class' => 'btn btn-primary']); ?>

                            <?php echo Form::close(); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
<?php $__env->stopSection(); ?>
<script type="text/javascript">
    $(function() {

        // We can attach the `fileselect` event to all file inputs on the page
        $(document).on('change', ':file', function() {
            var input = $(this),
                numFiles = input.get(0).files ? input.get(0).files.length : 1,
                label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
            input.trigger('fileselect', [numFiles, label]);
        });

        // We can watch for our custom `fileselect` event like this
        $(document).ready( function() {
            $(':file').on('fileselect', function(event, numFiles, label) {

                var input = $(this).parents('.input-group').find(':text'),
                    log = numFiles > 1 ? numFiles + ' files selected' : label;

                if( input.length ) {
                    input.val(log);
                } else {
                    if( log ) alert(log);
                }

            });
        });

    });
</script>
<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#image1')
                    .attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
<style>
    #hide input[type=file] {
        display:none;
        margin:10px;
    }
    #hide input[type=file] + label {
        display:inline-block;
        margin:20px;
        padding: 4px 32px;
        background-color: #FFFFFF;
        border:solid 1px #666F77;
        border-radius: 6px;
        color:#666F77;
    }
    #hide input[type=file]:active + label {
        background-image: none;
        background-color:#2D6C7A;
        color:#FFFFFF;
    }
</style>
<script type="text/javascript">
    Dropzone.autoDiscover = false;

    $(document).ready(function(){
        Dropzone.options.uploadFiles = {
            paramName: "file",  // The name that will be used to transfer the file
            maxFilesize: 100,   // MB
            acceptedFiles: ".jpeg,.jpg,.png,.gif",
            addRemoveLinks: true,
            dictCancelUpload: 'Cancel',
            url: 'post.php',
        };
    });
</script>
<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
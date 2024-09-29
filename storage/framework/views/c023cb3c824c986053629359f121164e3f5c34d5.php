<?php $__env->startSection('title', 'Profile'); ?>

<?php $__env->startSection('content'); ?>
<div class="box box-primary">
  <div class="box-header">
      <div class="col-md-12">
      <h3 class="box-title"> User Profile - <strong><?php echo e(Auth::user()->name); ?></strong> </h3>

              <h6 class="pull-right">
                  <a href="<?php echo e(route('profiles.edit', Auth::user()->id)); ?>" class="btn btn-primary btn-xs">Edit Profile</a>
              </h6>
      </div>
    </div>

    <div class="box-body">
        <?php echo e(Form::open(array('action'=>'ProfileController@store', 'method' => 'post', 'files' => true, 'enctype'=>'multipart/form-data'))); ?>

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
                        <?php echo e(Form::close()); ?>

                    </li>
                    <li class="list-group-item">
                        <b>Name </b>
                        <p><?php echo e(Auth::user()->name); ?></p>
                    </li>
                    <li class="list-group-item">
                        <b>Username </b>
                        <p> <?php echo e(Auth::user()->username); ?></p>
                    </li>
                    <li class="list-group-item">
                        <b>Email </b>
                        <p> <?php echo e(Auth::user()->email); ?></p>
                    </li>
                    
                        
                        
                    
                    <li class="list-group-item">
                        <b>Assigned Role </b>
                        <p>  <td><?php echo e(Auth::user()->roles->implode('name', ', ')); ?></td></p>
                    </li>

                </ul>
        </div>
    </div>
    </div>

<?php $__env->stopSection(); ?>

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
<?php $__env->startSection('title', 'Login -'); ?>
<?php $__env->startSection('content'); ?>
<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <h2><img style="max-width: 150px; padding-right:5px; padding-bottom:15px;" src="<?php echo asset('img/axiata_logo.png'); ?>"><br><b>Cyber Defence Portal</b></h2>
    </div>
    <div class="login-box-body">
      <p class="login-box-msg">Sign in to Start Your Session</p>

      <div class="panel-body">
        <form class="form-horizontal" method="POST" action="<?php echo e(route('login')); ?>">
          <?php echo e(csrf_field()); ?>


          <div class="form-group<?php echo e($errors->has('username') ? ' has-error' : ''); ?>">
            <input type="text" class="form-control" name="username" placeholder="Username" value="<?php echo e(old('username')); ?>" required autofocus>
            <?php if($errors->has('username')): ?>
            <span class="help-block">
              <strong><?php echo e($errors->first('username')); ?></strong>
            </span>
            <?php endif; ?>
          </div>
          <div class="form-group<?php echo e($errors->has('password') ? ' has-error' : ''); ?>">

            <input id="password" type="password" class="form-control" name="password" placeholder="Password" autocomplete="off" required>

            <?php if($errors->has('password')): ?>
            <span class="help-block">
              <strong><?php echo e($errors->first('password')); ?></strong>
            </span>
            <?php endif; ?>
          </div>
          <div class="row">
            <div class="col-md-8">
              <div class="form-group">
                <div class="checkbox">
                  <label>
                    <input type="checkbox" name="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>> Remember Me
                  </label>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block btn-flat">
                  Login
                </button>
              </div>
            </div>
          </div>



        </form>
      </div>
    </div>
  </body>



<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
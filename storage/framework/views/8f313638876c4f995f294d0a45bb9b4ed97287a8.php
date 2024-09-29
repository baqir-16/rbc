<!-- Main Header -->
<header class="main-header">
  <!-- Logo -->
  <a href="<?php echo e(url('home')); ?>" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <span class="logo-mini"><img src="<?php echo e(asset("/img/axiata_logo_small.png")); ?>" width="20px"></span>
    <!-- logo for regular state and mobile devices -->
    <span class="logo-lg"><img src="<?php echo e(asset("/img/axiata_logo.png")); ?>" width="60px"></span>
  </a>
  <!-- Header Navbar: style can be found in header.less -->
  <nav class="navbar navbar-static-top">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
      <span class="sr-only">Toggle navigation</span>
    </a>

    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
        <?php if(auth()->guard()->guest()): ?>
            <li><a href="<?php echo e(route('login')); ?>">Login</a></li>
            <li><a href="<?php echo e(route('register')); ?>">Register</a></li>
        <?php else: ?>

        <!-- User Account: style can be found in dropdown.less -->
        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <?php if(Auth::user()->avatar != "avatar.jpg"): ?>
             <img src="<?php echo e(Config::get('remote.connections.MDB_SERVER.host_http').'/profile_img/'.Auth::user()->avatar); ?>" alt="<?php echo e(Auth::user()->avatar); ?>" style="width:30px;height:30px;" class="user-image" alt="User Image"/>
            <?php else: ?>
              <img id="image" src="<?php echo asset('/profile'); ?>/<?php echo e(Auth::User()->avatar); ?>" style="width:30px;height:30px;" class="user-image" alt="User Image" />
            <?php endif; ?>
            <span class="hidden-xs"><?php echo e(Auth::user()->name); ?> </span><span class="caret"></span>
          </a>
          <ul class="dropdown-menu">
            <!-- User image -->
            <li class="user-header">
              <?php if(Auth::user()->avatar != "avatar.jpg"): ?>
                <img src="<?php echo e(Config::get('remote.connections.MDB_SERVER.host_http').'/profile_img/'.Auth::user()->avatar); ?>" alt="<?php echo e(Auth::user()->avatar); ?>" style="width:100px;height:100px;" class="img-circle" alt="User Image"/>
              <?php else: ?>
                <img id="image" src="<?php echo asset('/profile'); ?>/<?php echo e(Auth::User()->avatar); ?>" style="width:100px;height:100px;" class="img-circle" alt="User Image" />
              <?php endif; ?>
              <p>
                <?php echo e(Auth::user()->name); ?> - Axiata
                <small>Member since <?php echo e(Auth::user()->created_at->format('d/m/Y')); ?></small>
              </p>
            </li>
            <!-- Menu Footer-->
            <li class="user-footer">
              <div class="pull-left">
                <a href="<?php echo e(route('profiles.index')); ?>" class="btn btn-default btn-flat">Profile</a>
              </div>
              <div class="pull-right">
                <a class="btn btn-default btn-flat" href="<?php echo e(route('logout')); ?>"
                    onclick="event.preventDefault();
                             document.getElementById('logout-form').submit();">
                    Logout
                    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST">
                        <?php echo e(csrf_field()); ?>

                    </form>
                </a>
              </div>
            </li>
          </ul>
        </li>
        <!-- Control Sidebar Toggle Button -->
        <li class="dropdown tasks-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
            <i class="fa fa-gears"></i>
          </a>
          <ul class="dropdown-menu">
            <li>
              <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin_view')): ?>
                <ul class="menu ">
                  <li><a href="<?php echo e(route('users.index')); ?>"><i class="fa fa-fw fa-circle-o text-aqua"></i> List Users</a></li>
                  <li><a href="<?php echo e(route('roles.index')); ?>"><i class="fa fa-fw fa-circle-o text-red"></i> List Roles</a></li>

                  <li><a href="<?php echo e(route('departments.index')); ?>"><i class="fa fa-fw fa-circle-o text-blue"></i> List Departments</a></li>

                  <li><a href="<?php echo e(route('vulncategories.index')); ?>"><span class="fa fa-fw fa-circle-o text-aqua"></span> <span> Vulnerability Categories</span></a></li>
                  <li><a href="<?php echo e(route('system_update.index')); ?>"><i class="fa fa-fw fa-circle-o text-yellow"></i> Update</a></li>
                </ul>
                
                  
                    
                    
                    
                  
                
                <p class="text-center" id="system-version"></p>
              <?php endif; ?>
            </li>
          </ul>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </nav>
</header>

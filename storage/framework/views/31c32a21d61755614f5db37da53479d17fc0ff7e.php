<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel">
      <div class="pull-left image">
          <?php if(Auth::user()->avatar != "avatar.jpg"): ?>
            <img src="<?php echo e(Config::get('remote.connections.MDB_SERVER.host_http').'/profile_img/'.Auth::user()->avatar); ?>" alt="<?php echo e(Auth::user()->avatar); ?>" style="width:30px;height:30px;" class="img-circle" alt="User Image" />
          <?php else: ?>
            <img id="image" src="<?php echo asset('/profile'); ?>/<?php echo e(Auth::User()->avatar); ?>" style="width:30px;height:30px;" class="img-circle" alt="User Image" />
          <?php endif; ?>
      </div>
      <div class="pull-left info">
        <p><?php echo e(Auth::user()->name); ?></p>
        <a href="<?php echo e(route('profiles.index')); ?>"><i class="fa fa-circle text-success"></i> <?php echo e(Auth::user()->roles->pluck('name')->first()); ?></a>
      </div>
    </div>
    <!-- search form -->
    <form action="#" method="get" class="sidebar-form">
      <div class="input-group">
        <input type="text" name="q" class="form-control" placeholder="Search...">
        <span class="input-group-btn">
              <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
              </button>
            </span>
      </div>
    </form>
    <!-- /.search form -->
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu" data-widget="tree">
      <li class="header">MAIN NAVIGATION</li>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('gciso_view')): ?>
            <li>
                <a><i class="fa fa-dashboard"></i><span>Dashboards</span></a>
                <ul class="treeview-menu">
                    <li><a href="<?php echo e(route('home.index')); ?>"><span>Internal</span></a></li>
                    <li><a href="<?php echo e(url('extdb')); ?>"><span>External</span></a></li>
                </ul>
            </li>
        
    <?php endif; ?>
    
        
        
          
          
            
          
        
        
            
            
            
            
        
      
    
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('pmo_view')): ?>
      <li class="treeview">
          <a href="#"><i class="fa fa-fw fa-star"></i> <span>Cyber PMO</span>
              <span class="pull-right-container">
                  <span class="fa fa-angle-left pull-right"></span>
              </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo e(route('Ticket.index')); ?>"><span class="fa fa-fw fa-circle-o text-blue"></span> <span>View Tickets</span></a></li>
              <li><a href="<?php echo e(url('progress_streams')); ?>"><span class="fa fa-fw fa-circle-o text-yellow"></span> <span>In Progress Tasks</span></a></li>
            <li><a href="<?php echo e(route('close.index')); ?>"><span class="fa fa-fw fa-circle-o text-yellow"></span> <span>Completed Tasks</span></a></li>
            <li><a href="<?php echo e(url('closed_streams')); ?>"><span class="fa fa-fw fa-circle-o text-red"></span> <span>Closed Tasks</span></a></li>
              <li><a href="<?php echo e(url('pdfreports')); ?>"><span class="fa fa-fw fa-circle-o"></span> <span>Update PDF</span></a></li>

          </ul>
      </li>
    <?php endif; ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('tester_view')): ?>
            <li><a href="<?php echo e(route('Tester.index')); ?>"><i class="fa fa-laptop"></i> <span>Security Tester</span></a></li>
    <?php endif; ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('analyst_view')): ?>
            <li><a href="<?php echo e(route('Analyst.index')); ?>"><i class="fa fa-edit"></i> <span>Security Analyst</span></a></li>
    <?php endif; ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('qa_view')): ?>
            <li><a href="<?php echo e(route('QA.index')); ?>"><i class="fa fa-check-square-o"></i> <span>Security Technical QA</span></a></li>
    <?php endif; ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('hod_view')): ?>
        <li><a href="<?php echo e(route('HoD.index')); ?>"><i class="fa fa-share"></i> <span>HoD</span></a></li>
    <?php endif; ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('remofficer_view')): ?>
        <li><a href="<?php echo e(route('Remofficer.index')); ?>"><i class="fa fa-question-circle-o"></i> <span>Remediation Officer</span></a></li>
    <?php endif; ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('rempmo_view')): ?>
        
        <li class="treeview">
            <a href="#"><i class="fa fa-pie-chart"></i><span>Remediation PMO</span>
                <span class="pull-right-container">
                    <span class="fa fa-angle-left pull-right"></span>
                </span>
            </a>
            <ul class="treeview-menu">
                <li><a href="<?php echo e(route('Rempmo.index')); ?>"><span class="fa fa-fw fa-circle-o text-yellow"></span><span>View Findings</span></a></li>
                <li><a href="<?php echo e(url('closed_findings')); ?>"><span class="fa fa-fw fa-circle-o text-green"></span><span>Closed Findings</span></a></li>
            </ul>
        </li>
    <?php endif; ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_history')): ?>
        <li><a href="<?php echo e(url('pdf_files')); ?>"><i class="fa fa-history"></i> <span> PDF Files History</span></a></li>
    <?php endif; ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('asset_owner')): ?>
            <li class="treeview">
                <a href="#"><i class="fa fa-pie-chart"></i><span>Asset Management</span>
                    <span class="pull-right-container">
                    <span class="fa fa-angle-left pull-right"></span>
                </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="<?php echo e(route('assetmng.index')); ?>"><span class="fa fa-fw fa-circle-o text-yellow"></span><span>List of Assets</span></a></li>
                    <li><a href="<?php echo e(url('ipindex')); ?>"><span class="fa fa-fw fa-circle-o text-green"></span><span>Unique IPs</span></a></li>
                </ul>
            </li>
    <?php endif; ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('gciso_view')): ?>
        <li class="treeview">
            <a href="#">
                <i class="fa fa-bar-chart"></i> <span>Remediation Reports</span>
                <span class="pull-right-container"></span>
            </a>
            <ul class="treeview-menu">
                <li><a href="<?php echo e(route('total_open_closed')); ?>">Open & Closed Statistics</a></li>
                <li><a href="<?php echo e(route('weekly_stats')); ?>">Weekly Statistics</a></li>
                <li><a href="<?php echo e(route('leaderboard')); ?>">Leaderboard</a></li>
            </ul>
        </li>
    <?php endif; ?>

    <?php if( Auth::user()->hasRole('Admin') ): ?>
    <?php else: ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ciso_view')): ?>
            <li><a href="<?php echo e(url('cisodb')); ?>"><i class="fa fa-dashboard"></i> <span>Ciso Dashboard</span></a></li>
    <?php endif; ?>
    <?php endif; ?>
    </ul>
  </section>
  <!-- /.sidebar -->
</aside>

<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('content'); ?>
   <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin_view')): ?>
   <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ciso_view')): ?>
        <div class="row">
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-maroon">
                    <div class="inner">
                        <h3><?php echo e($critical_risk); ?> <small> - Open Critical </small></h3>
                    </div>
                    <div class="inner down">
                        <h4><?php echo e($pending_rem_c); ?> <small> - Pending Remediated </small></h4>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="<?php echo e(url('showdetails', 1)); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3><?php echo e($high_risk); ?>

                            <small> - Open High </small></h3>
                    </div>
                    <div class="inner down">
                        <h4><?php echo e($pending_rem_h); ?> <small> - Pending Remediated </small></h4>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="<?php echo e(url('showdetails', 2)); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3><?php echo e($med_risk); ?>

                            <small> - Open Medium </small></h3>
                    </div>
                    <div class="inner down">
                        <h4><?php echo e($pending_rem_m); ?> <small> - Pending Remediated </small></h4>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="<?php echo e(url('showdetails', 3)); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3><?php echo e($low_risk); ?>

                            <small> - Open Low </small></h3>
                    </div>
                    <div class="inner down">
                        <h4><?php echo e($pending_rem_l); ?> <small> - Pending Remediated </small></h4>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="<?php echo e(url('showdetails', 4)); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            
            
            
            
            
            
            
            
            
            
            
            
        </div>
        <div class="row">
            <section class="col-lg-12 connectedSortable ui-sortable">
                <div class="box box-primary">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box-header ui-sortable-handle" style="cursor: move;">
                                <div class="row text-center">
                                    <div class="col-md-12">
                                        <h3>Vulnerability Exposure for the Past Two Months </h3>
                                        <div class="col-md-12">
                                            <div class="col-md-6 text-right">
                                                <p><strong style="color:#00a65a"> <i class="fa fa-area-chart"></i></strong> TOTAL CLOSED PER WEEK</p>
                                            </div>
                                            <div class="col-md-6 text-left">
                                                <p><strong style="color:#ff0000"><i class="fa fa-area-chart"></i></strong> TOTAL OPEN PER WEEK</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                </div>

                                <div class="box-body">
                                    <div class="chart">
                                        <canvas id="areaChart" style="height:250px"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer no-border">
                                <div class="row">
                                    <div class="col-xs-12 text-center">

                                        <div class="knob-label"><h5></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </section>
        </div>
        <!-- Main row -->
        <div class="row">
            <!-- Left col -->
            <section class="col-lg-12 connectedSortable ui-sortable">
                <div class="row">
                <div class="col-md-6">
                        <div class="box box-solid">
                            <div class="box-header ui-sortable-handle" style="cursor: move;">
                                <i class="fa fa-th"></i>
                                <h3 class="box-title">Total Open & Closed Findings</h3>

                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                </div>

                                <div class="box-body">
                                    <canvas id="pieChart" style="height:250px"></canvas>
                                </div>
                            </div>
                            <div class="box-footer no-border">
                                <div class="row">
                                    <div class="col-xs-6 text-center" style="border-right: 1px solid #f4f4f4">
                                        <div style="display:inline;width:60px;height:60px;"><canvas width="75" height="75" style="width: 60px; height: 60px;"></canvas><input type="text" class="knob" data-readonly="true" value="<?php echo e($open); ?>" data-width="60" data-height="60" data-fgcolor="#39CCCC" readonly="readonly" style="width: 34px; height: 20px; position: absolute; vertical-align: middle; margin-top: 20px; margin-left: -47px; border: 0px; background: none; font-style: normal; font-variant: normal; font-weight: bold; font-stretch: normal; font-size: 12px; line-height: normal; font-family: Arial; text-align: center; color: rgb(57, 204, 204); padding: 0px; -webkit-appearance: none;"></div>
                                        <div class="knob-label">Open Findings</div>
                                    </div>
                                    <div class="col-xs-6 text-center" style="border-right: 1px solid #f4f4f4">
                                        <div style="display:inline;width:60px;height:60px;"><canvas width="75" height="75" style="width: 60px; height: 60px;"></canvas><input type="text" class="knob" data-readonly="true" value="<?php echo e($close); ?>" data-width="60" data-height="60" data-fgcolor="#39CCCC" readonly="readonly" style="width: 34px; height: 20px; position: absolute; vertical-align: middle; margin-top: 20px; margin-left: -47px; border: 0px; background: none; font-style: normal; font-variant: normal; font-weight: bold; font-stretch: normal; font-size: 12px; line-height: normal; font-family: Arial; text-align: center; color: rgb(57, 204, 204); padding: 0px; -webkit-appearance: none;"></div>

                                        <div class="knob-label">Closed Findings</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="col-md-6">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#hosts" data-toggle="tab" aria-expanded="false">Affected Hosts per Category</a></li>
                            <li class=""><a href="#titles" data-toggle="tab" aria-expanded="true">Affected Hosts per Vulnerability</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane  active" id="hosts">
                                <div class="box-body">
                                    <div class="table-responsive">
                                        <table id="" class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th>Category Name</th>
                                                <th>Critical</th>
                                                <th>High</th>
                                                <th>Medium</th>
                                                <th>Low</th>
                                                
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $__currentLoopData = $cat_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e($cat['name']); ?></td>
                                                    <td><?php echo e($hosts_per_cat_array[$key][0]); ?></td>
                                                    <td><?php echo e($hosts_per_cat_array[$key][1]); ?></td>
                                                    <td><?php echo e($hosts_per_cat_array[$key][2]); ?></td>
                                                    <td><?php echo e($hosts_per_cat_array[$key][3]); ?></td>
                                                    
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="titles">
                                <div class="box-body">
                                    <div class="table-responsive table-striped">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th>Vulnerability Name</th>
                                                <th>Severity</th>
                                                <th>No. of Hosts Affected</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                $severity = array_flip(Config::get('enums.severity_status'));
                                            ?>
                                            <?php $__currentLoopData = $num_of_hosts_by_vuln_name; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td><?php echo e($item[0]); ?></td>
                                                    <td><?php echo e($severity[$item[1]]); ?></td>
                                                    <td><?php echo e($item[2]); ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Remediation Service Level Agreement (SLA)</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr style="background: #204dc4; color:#fff">
                                    <th>Level</th>
                                    <th> SLA (Running hours)  </th>
                                    <th>  Responsible </th>
                                    <th> Escalation </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td style="background-color:#980000; color:#fff;">Critical</td>
                                    <td>To be fixed by <?php echo e($pdfinfo[0]->c_hours); ?>  Hours </td>
                                    <td><?php echo e($pdfinfo[0]->c_responsible); ?> </td>
                                    <td>HoD - <?php echo e($pdfinfo[0]->c_escalation); ?> </td>
                                </tr>
                                <tr>
                                    <td class="bg-red">High</td>
                                    <td>To be fixed by <?php echo e($pdfinfo[0]->h_hours); ?> Hours  </td>
                                    <td><?php echo e($pdfinfo[0]->h_responsible); ?>  </td>
                                    <td>HoD - <?php echo e($pdfinfo[0]->h_escalation); ?> </td>
                                </tr>
                                <tr>
                                    <td class="bg-warning" style=" background-color:#e69534; color:#fff;">Medium</td>
                                    <td>To be fixed by <?php echo e($pdfinfo[0]->m_hours); ?> Hours </td>
                                    <td><?php echo e($pdfinfo[0]->m_responsible); ?>  </td>
                                    <td>HoD - <?php echo e($pdfinfo[0]->m_escalation); ?> </td>
                                </tr>
                                <tr>
                                    <td class="bg-success">Low</td>
                                    <td>To be fixed by <?php echo e($pdfinfo[0]->l_hours); ?> Hours or next scanning (Quarterly) cycle </td>
                                    <td><?php echo e($pdfinfo[0]->l_responsible); ?>  </td>
                                    <td>HoD - <?php echo e($pdfinfo[0]->l_escalation); ?> </td>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                            <div class="col-xs-12">
                           <div class="row">
                               <div class="table-responsive">
                                   <table class="table table-bordered table-striped table-hover text-center">
                                       <col>
                                       <colgroup span="2"></colgroup>
                                       <colgroup span="2"></colgroup>
                                       <tr style="background: #204dc4; color:#fff;">
                                        <th colspan="14" scope="colgroup"><h5> KPI for Critical and High Severity Findings</h5></th>
                                       </tr>
                                       <tr>
                                           <td rowspan="1" style="background: #204dc4; color:#fff;"></td>
                                           <td rowspan="1" style="background: #204dc4; color:#fff;"></td>
                                           <th colspan="4" scope="colgroup" style="background: #39bb91; color:#fff;">Within KPI</th>
                                           <th colspan="4" scope="colgroup" style="background: #d80b03; color:#fff;">Not Within KPI</th>
                                       </tr>
                                       <tr>
                                           <td rowspan="1" style="background: #204dc4; color:#fff;"></td>
                                           <td rowspan="1" style="background: #204dc4; color:#fff;"></td>
                                           <th colspan="2" style="background: #d80b03; color:#fff;">Open</th>
                                           <th colspan="2" style="background: #39bb91; color:#fff;">Closed</th>
                                           <th colspan="2" style="background: #d80b03; color:#fff;">Open</th>
                                           <th colspan="2" style="background: #39bb91; color:#fff;">Closed</th>
                                       </tr>
                                       <tr>
                                           <th scope="col" style="background: #204dc4; color:#fff;">#</th>
                                           <th scope="col" style="background: #204dc4; color:#fff;">Opco</th>
                                           <th scope="col" style="background: #9f1400; color:#fff;">Critical</th>
                                           <th scope="col" style="background: #d80b03; color:#fff;">High</th>
                                           <th scope="col" style="background: #9f1400; color:#fff;">Critical</th>
                                           <th scope="col" style="background: #d80b03; color:#fff;">High</th>
                                           <th scope="col" style="background: #9f1400; color:#fff;">Critical</th>
                                           <th scope="col" style="background: #d80b03; color:#fff;">High</th>
                                           <th scope="col" style="background: #9f1400; color:#fff;">Critical</th>
                                           <th scope="col" style="background: #d80b03; color:#fff;">High</th>
                                       </tr>
                                       <?php $__currentLoopData = (array)$kpi_opco_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $opco): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                           <tr>
                                               <td class="bg-info"><?php echo e($key+1); ?></td>
                                               <td><?php echo e($opco['opco']); ?></td>
                                                <?php
                                                    $w_kpi_o_c = 0;
                                                    $w_kpi_o_h = 0;
                                                    $w_kpi_c_c = 0;
                                                    $w_kpi_c_h = 0;
                                                   $nw_kpi_o_c = 0;
                                                   $nw_kpi_o_h = 0;
                                                   $nw_kpi_c_c = 0;
                                                   $nw_kpi_c_h = 0;
                                                ?>
                                               <?php $__currentLoopData = $kpi_opco_risk_array[$key][0]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $array): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                   <?php if($array['diff'] <= $pdfinfo[0]['c_hours']): ?>
                                                       <?php $w_kpi_o_c++; ?>
                                                   <?php elseif($array['diff'] > $pdfinfo[0]['c_hours']): ?>
                                                       <?php $nw_kpi_o_c++; ?>
                                                   <?php endif; ?>
                                               <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                               <?php $__currentLoopData = $kpi_opco_risk_array[$key][1]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $array): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                   <?php if($array['diff']<= $pdfinfo[0]['h_hours']): ?>
                                                       <?php $w_kpi_o_h++; ?>
                                                   <?php elseif($array['diff'] > $pdfinfo[0]['h_hours']): ?>
                                                       <?php $nw_kpi_o_h++; ?>
                                                   <?php endif; ?>
                                               <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                               <?php $__currentLoopData = $kpi_opco_risk_array[$key][2]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $array): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                   <?php if($array['diff'] > $pdfinfo[0]['c_hours'] ): ?>
                                                       <?php $w_kpi_c_c++; ?>
                                                   <?php elseif($array['diff'] <= $pdfinfo[0]['c_hours']): ?>
                                                       <?php $nw_kpi_c_c++; ?>
                                                   <?php endif; ?>
                                               <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                               <?php $__currentLoopData = $kpi_opco_risk_array[$key][3]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $array): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                   <?php if($array['diff'] > $pdfinfo[0]['h_hours']): ?>
                                                       <?php $w_kpi_c_h++; ?>
                                                   <?php elseif($array['diff'] <= $pdfinfo[0]['h_hours']): ?>
                                                       <?php $nw_kpi_c_h++; ?>
                                                   <?php endif; ?>
                                               <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                               <td class="bg-danger"><?php echo e($w_kpi_o_c); ?></td>
                                               <td class="bg-danger"><?php echo e($w_kpi_o_h); ?></td>
                                               <td class="bg-success"><?php echo e($w_kpi_c_c); ?></td>
                                               <td class="bg-success"><?php echo e($w_kpi_c_h); ?></td>
                                               <td class="bg-danger"><?php echo e($nw_kpi_o_c); ?></td>
                                               <td class="bg-danger"><?php echo e($nw_kpi_o_h); ?></td>
                                               <td class="bg-success"><?php echo e($nw_kpi_c_c); ?></td>
                                               <td class="bg-success"><?php echo e($nw_kpi_c_h); ?></td>
                                           </tr>
                                       <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                   </table>
                               </div>
                           </div>
                            </div>
                    </div>
                        </div>
                    </div>
                </div>
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Aging of Critical and High Vulnerabilities from Internal Scans</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12 connectedSortable ui-sortable">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover text-center">
                                        <col>
                                        <colgroup span="2"></colgroup>
                                        <colgroup span="2"></colgroup>
                                        <tr>
                                            <td rowspan="1" style="background: #204dc4; color:#fff;"></td>
                                            <td rowspan="1" style="background: #204dc4; color:#fff;"></td>
                                            <th colspan="6" scope="colgroup"  style="background: #9f1400; color:#fff;">Critical</th>
                                            <th colspan="6" scope="colgroup" style="background: #d80b03; color:#fff;">High</th>
                                        </tr>
                                        <tr>
                                            <td rowspan="1" style="background: #204dc4; color:#fff;"></td>
                                            <td rowspan="1" style="background: #204dc4; color:#fff;"></td>
                                            <th colspan="3" style="background: #d80b03; color:#fff;">Open</th>
                                            <th colspan="3" style="background: #39bb91; color:#fff;">Closed</th>
                                            <th colspan="3" style="background: #d80b03; color:#fff;">Open</th>
                                            <th colspan="3" style="background: #39bb91; color:#fff;">Closed</th>
                                        </tr>
                                        <tr>
                                            <th scope="col" style="background: #204dc4; color:#fff;">#</th>
                                            <th scope="col" style="background: #204dc4; color:#fff;">Opco</th>
                                            <th scope="col" style="background: #f5c257; color:#fff;">1-7 Days</th>
                                            <th scope="col" style="background: #d80b03; color:#fff;">8-30 Days</th>
                                            <th scope="col" style="background: #9f1400; color:#fff;">> 30 Days</th>
                                            <th scope="col" style="background: #f5c257; color:#fff;">1-7 Days</th>
                                            <th scope="col" style="background: #d80b03; color:#fff;">8-30 Days</th>
                                            <th scope="col" style="background: #9f1400; color:#fff;">> 30 Days</th>
                                            <th scope="col" style="background: #f5c257; color:#fff;">1-7 Days</th>
                                            <th scope="col" style="background: #d80b03; color:#fff;">8-30 Days</th>
                                            <th scope="col" style="background: #9f1400; color:#fff;">> 30 Days</th>
                                            <th scope="col" style="background: #f5c257; color:#fff;">1-7 Days</th>
                                            <th scope="col" style="background: #d80b03; color:#fff;">8-30 Days</th>
                                            <th scope="col" style="background: #9f1400; color:#fff;">> 30 Days</th>
                                        </tr>
                                        <?php $__currentLoopData = (array)$aging_opco_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $opco): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td class="bg-info"><?php echo e($key+1); ?></td>
                                                <td><?php echo e($opco['opco']); ?></td>
                                                <?php
                                                $c_o_aging_o = 0;
                                                $c_o_aging_e = 0;
                                                $c_o_aging_t = 0;
                                                $c_c_aging_o = 0;
                                                $c_c_aging_e = 0;
                                                $c_c_aging_t = 0;
                                                $h_o_aging_o = 0;
                                                $h_o_aging_e = 0;
                                                $h_o_aging_t = 0;
                                                $h_c_aging_o = 0;
                                                $h_c_aging_e = 0;
                                                $h_c_aging_t = 0;
                                                ?>

                                                <?php $__currentLoopData = $aging_opco_risk_array[$key][0]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $array): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($array['diff'] < 168): ?>
                                                        <?php $c_o_aging_o++; ?>
                                                    <?php elseif($array['diff'] > 168 && $array['diff'] < 720): ?>
                                                        <?php $c_o_aging_e++; ?>
                                                    <?php elseif($array['diff'] > 720): ?>
                                                        <?php $c_o_aging_t++; ?>
                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                <?php $__currentLoopData = $aging_opco_risk_array[$key][1]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $array): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($array['diff'] < 168): ?>
                                                        <?php $c_c_aging_o++; ?>
                                                    <?php elseif($array['diff'] > 168 && $array['diff'] < 720): ?>
                                                        <?php $c_c_aging_e++; ?>
                                                    <?php elseif($array['diff'] > 720): ?>
                                                        <?php $c_c_aging_t++; ?>
                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                <?php $__currentLoopData = $aging_opco_risk_array[$key][2]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $array): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($array['diff'] < 168): ?>
                                                        <?php $h_o_aging_o++; ?>
                                                    <?php elseif($array['diff'] > 168 && $array['diff'] < 720): ?>
                                                        <?php $h_o_aging_e++; ?>
                                                    <?php elseif($array['diff'] > 720): ?>
                                                        <?php $h_o_aging_t++; ?>
                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                <?php $__currentLoopData = $aging_opco_risk_array[$key][3]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $array): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($array['diff'] < 168): ?>
                                                        <?php $h_c_aging_o++; ?>
                                                    <?php elseif($array['diff'] > 168 && $array['diff'] < 720): ?>
                                                        <?php $h_c_aging_e++; ?>
                                                    <?php elseif($array['diff'] > 720): ?>
                                                        <?php $h_c_aging_t++; ?>
                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                <td class="bg-warning"><?php echo e($c_o_aging_o); ?></td>
                                                <td class="bg-danger"><?php echo e($c_o_aging_e); ?></td>
                                                <td style="background-color: #dcb8b8;"><?php echo e($c_o_aging_t); ?></td>
                                                <td class="bg-warning"><?php echo e($c_c_aging_o); ?></td>
                                                <td class="bg-danger"><?php echo e($c_c_aging_e); ?></td>
                                                <td style="background-color: #dcb8b8;"><?php echo e($c_c_aging_t); ?></td>
                                                <td class="bg-warning"><?php echo e($h_o_aging_o); ?></td>
                                                <td class="bg-danger"><?php echo e($h_o_aging_e); ?></td>
                                                <td style="background-color: #dcb8b8;"><?php echo e($h_o_aging_t); ?></td>
                                                <td class="bg-warning"><?php echo e($h_c_aging_o); ?></td>
                                                <td class="bg-danger"><?php echo e($h_c_aging_e); ?></td>
                                                <td style="background-color: #dcb8b8;"><?php echo e($h_c_aging_t); ?></td>

                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    <?php endif; ?>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('custom-scripts'); ?>
    <script>
        $(function () {
            /* ChartJS
             * -------
             * Here we will create a few charts using ChartJS
             */

            //--------------
            //- AREA CHART -
            //--------------

            // Get context with jQuery - using jQuery's .get() method.
            var areaChartCanvas = $('#areaChart').get(0).getContext('2d')
            // This will get the first returned node in the jQuery collection.
            var areaChart       = new Chart(areaChartCanvas)

            var areaChartData = {
                labels  : ['<?php echo e(date('d-M-Y', strtotime($w2))); ?>', '<?php echo e(date('d-M-Y', strtotime($w3))); ?>', '<?php echo e(date('d-M-Y', strtotime($w4))); ?>', '<?php echo e(date('d-M-Y', strtotime($w5))); ?>', '<?php echo e(date('d-M-Y', strtotime($w6))); ?>', '<?php echo e(date('d-M-Y', strtotime($w7))); ?>', '<?php echo e(date('d-M-Y', strtotime($w8))); ?>', '<?php echo e(date('d-M-Y', strtotime($w9))); ?>'],
                datasets: [
                    {
                        label               : 'Open',
                        fillColor           : '#dd4b39b0',
                        strokeColor         : '#dd4b39c4',
                        pointColor          : '#dd4b39c4',
                        pointStrokeColor    : '#f55046',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        data                :  [<?php echo e($owk1); ?>,<?php echo e($owk2); ?>,<?php echo e($owk3); ?>,<?php echo e($owk4); ?>,<?php echo e($owk5); ?>,<?php echo e($owk6); ?>,<?php echo e($owk7); ?>, <?php echo e($owk8); ?>]
                    },
                    {
                        label               : 'Close',
                        fillColor           : '#38a682c4',
                        strokeColor         : '#38a682c4',
                        pointColor          : '#38a682c4',
                        pointStrokeColor    : 'rgba(60,141,188,1)',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(60,141,188,1)',
                        data                :  [<?php echo e($cwk1); ?>,<?php echo e($cwk2); ?>,<?php echo e($cwk3); ?>,<?php echo e($cwk4); ?>,<?php echo e($cwk5); ?>,<?php echo e($cwk6); ?>,<?php echo e($cwk7); ?>, <?php echo e($cwk8); ?>]

                    }
                ]
            }

            var areaChartOptions = {
                //Boolean - If we should show the scale at all
                showScale               : true,
                //Boolean - Whether grid lines are shown across the chart
                scaleShowGridLines      : true,
                //String - Colour of the grid lines
                scaleGridLineColor      : 'rgba(0,0,0,.05)',
                //Number - Width of the grid lines
                scaleGridLineWidth      : 1,
                //Boolean - Whether to show horizontal lines (except X axis)
                scaleShowHorizontalLines: true,
                //Boolean - Whether to show vertical lines (except Y axis)
                scaleShowVerticalLines  : true,
                //Boolean - Whether the line is curved between points
                bezierCurve             : true,
                //Number - Tension of the bezier curve between points
                bezierCurveTension      : 0.3,
                //Boolean - Whether to show a dot for each point
                pointDot                : true,
                //Number - Radius of each point dot in pixels
                pointDotRadius          : 3,
                //Number - Pixel width of point dot stroke
                pointDotStrokeWidth     : 1,
                //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
                pointHitDetectionRadius : 20,
                //Boolean - Whether to show a stroke for datasets
                datasetStroke           : true,
                //Number - Pixel width of dataset stroke
                datasetStrokeWidth      : 2,
                //Boolean - Whether to fill the dataset with a color
                datasetFill             : true,
                //String - A legend template
                //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
                maintainAspectRatio     : true,
                //Boolean - whether to make the chart responsive to window resizing
                responsive              : true
            }

            //Create the line chart
            areaChart.Line(areaChartData, areaChartOptions)

            //-------------
            //- PIE CHART -
            //-------------
            // Get context with jQuery - using jQuery's .get() method.
            var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
            var pieChart       = new Chart(pieChartCanvas)
            var PieData        = [
                {
                    value    :<?php echo e($open); ?>,
                    color    : '#f56954',
                    highlight: '#f55046',
                    label    : 'Open'
                },
                {
                    value    :<?php echo e($close); ?>,
                    color    : '#38a682',
                    highlight: '#00a65a',
                    label    : 'Close'
                }
            ]
            var pieOptions     = {
                //Boolean - Whether we should show a stroke on each segment
                segmentShowStroke    : true,
                //String - The colour of each segment stroke
                segmentStrokeColor   : '#fff',
                //Number - The width of each segment stroke
                segmentStrokeWidth   : 2,
                //Number - The percentage of the chart that we cut out of the middle
                percentageInnerCutout: 50, // This is 0 for Pie charts
                //Number - Amount of animation steps
                animationSteps       : 100,
                //String - Animation easing effect
                animationEasing      : 'easeOutBounce',
                //Boolean - Whether we animate the rotation of the Doughnut
                animateRotate        : true,
                //Boolean - Whether we animate scaling the Doughnut from the centre
                animateScale         : false,
                //Boolean - whether to make the chart responsive to window resizing
                responsive           : true,
                // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
                maintainAspectRatio  : true,
                //String - A legend template
            }
            //Create pie or douhnut chart
            // You can switch between pie and douhnut using the method below.
            pieChart.Doughnut(PieData, pieOptions)


        })

        $('[data-toggle=example1]').confirmation({
            rootSelector: '[data-toggle=example1]',
            // other options
        });
        $('[data-toggle=example2]').confirmation({
            rootSelector: '[data-toggle=example2]',
            // other options
        });
        $('[data-toggle=example3]').confirmation({
            rootSelector: '[data-toggle=example3]',
            // other options
        });
        $('[data-toggle=example4]').confirmation({
            rootSelector: '[data-toggle=example4]',
            // other options
        });
        $('[data-toggle=example5]').confirmation({
            rootSelector: '[data-toggle=example5]',
            // other options
        });
        $('[data-toggle=example6]').confirmation({
            rootSelector: '[data-toggle=example6]',
            // other options
        });
        $('[data-toggle=example7]').confirmation({
            rootSelector: '[data-toggle=example7]',
            // other options
        });
        $(function () {
            $('#example1').DataTable({
                'paging'      : true,
                'lengthChange': true,
                'searching'   : true,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : true
            });

            $('#example2').DataTable({
                'paging'      : true,
                'lengthChange': true,
                'searching'   : true,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : true
            });

            $('#example3').DataTable({
                'paging'      : true,
                'lengthChange': true,
                'searching'   : true,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : true
            });

            $('#example4').DataTable({
                'paging'      : true,
                'lengthChange': true,
                'searching'   : true,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : true
            });

            $('#example5').DataTable({
                'paging'      : true,
                'lengthChange': true,
                'searching'   : true,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : true
            });

            $('#example6').DataTable({
                'paging'      : true,
                'lengthChange': true,
                'searching'   : true,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : true
            });
            $('#example7').DataTable({
                'paging'      : true,
                'lengthChange': true,
                'searching'   : true,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : true
            });

            $(".checkbox-toggle").click(function () {
    var clicks = $(this).data('clicks');
    if (clicks) {
    //Uncheck all checkboxes
    $(".mailbox-messages input[type='checkbox']").iCheck("uncheck");
    $(".fa", this).removeClass("fa-check-square-o").addClass('fa-square-o');
    } else {
    //Check all checkboxes
    $(".mailbox-messages input[type='checkbox']").iCheck("check");
    $(".fa", this).removeClass("fa-square-o").addClass('fa-check-square-o');
    }
    $(this).data("clicks", !clicks);
    });
    });
    </script>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
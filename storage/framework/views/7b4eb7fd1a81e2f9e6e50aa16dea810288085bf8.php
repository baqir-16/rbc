<?php $__env->startSection('title', 'HoD Sign Off '); ?>

<style>
    #list-group {
        margin-bottom: 0;
    }

    .list-group-item {
        border: 0 !important;
    }
</style>

<?php $__env->startSection('content'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('hod_view')): ?>
<div class="box box-primary">
    <div class="box-header">
    <div class="row">
        <div class="col-md-12">
            <h3 class="box-title"><strong>Preview of the Report</strong></h3>
            <a href="<?php echo e(route('HoD.index')); ?>" class="btn btn-default btn-flat pull-right">Back</a>
            <button type="button" href="<?php echo e(url('forward2sign_off/'.$sresults[0]->id)); ?>" data-toggle="example1" class="btn btn-success btn-flat pull-right">Sign Off</button>
        </div>
    </div>
    </div>
</div>
<div class="row">
<div class="col-md-12">
 <div class="box box-primary">
     <div class="box-body">
       <div class="container">
        <div class="row  axiata-logo">
            <div class="col-md-4 col-md-offset-4">
                <img src="../img/axiata.jpg" class="img-responsive center-block">
            </div>
        </div>
        <div class="row">
            <?php $__currentLoopData = $sresults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sresult): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-12 text-center">
                
                <h3><?php echo e($sresult->tickets[0]->title); ?></h3>
                <h3><a><?php echo e($sresult->modules[0]->name); ?></a> </h3>
                <h3><?php echo e($sresult->tickets[0]->ref); ?></h3>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
           
        <div class="row results-img">
            <div class="col-md-4 col-md-offset-4">
                <?php if(count($report_status) > 0): ?>
                <img src="../img/failed.png" class="img-responsive center-block">
                <?php else: ?>
                <img src="../img/passed.jpg" class="img-responsive center-block">
                <?php endif; ?>
            </div>
        </div>
           
           <?php $__currentLoopData = $sresults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sresult): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="row">
            <div class="col-md-12 text-center">
                
                <h3>Date prepared: <strong> <?php echo e($sresult->hod_assigned_date); ?> </strong> </h3>
                <h3> Type Of Test:</span> <strong> <?php echo e($sresult->modules[0]->name); ?> </strong></h3>
            </div><br>
        </div><br>
        <div class="row">
            <div class="table-responsive">
                <h3><b>Document Control</b> </h3>
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Role</th>
                        <th>Name</th>
                        <th>Data Task Assigned</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Security Tester</td>
                        <td><?php echo e($tester->name); ?></td>
                        <td><?php echo e($sresult->tester_assigned_date); ?></td>
                    </tr>
                    <tr>
                        <td>Security Analyst</td>
                        <td><?php echo e($analyst->name); ?></td>
                        <td><?php echo e($sresult->analyst_assigned_date); ?></td>
                    </tr>
                    <tr>
                        <td>Security QA</td>
                        <td><?php echo e($qa->name); ?></td>
                        <td><?php echo e($sresult->qa_assigned_date); ?></td>
                    </tr>
                    <tr>
                        <td>HoD</td>
                        <td><?php echo e($hod->name); ?></td>
                        <td><?php echo e($sresult->hod_assigned_date); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
           <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <div class="row">
                <div class="col-md-12">
                    <h3><b>Risks and Controls</b></h3>
                    <p>Kindly note that all critical and high findings are required to be remediated before go-live. Remediation of findings must be done before requesting a re-scan.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h3><b>Vulnerable Classification</b></h3>
                    <p>The classification of findings are based on the likelihood of occurrence and the magnitude of impact towards the business and the systems.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Level</th>
                                <th> Severity </th>
                                <th> Description</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>5</td>
                                <td style="background-color:#980000; color:#fff;">Critical</td>
                                <td>Vulnerability will likely only be exploited by script kiddies / low level attackers. May require to apply the missing patches and also develop specific code.
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td class="bg-red">High</td>
                                <td>Vulnerability will likely only be exploited by expert level attacker. May require development of specific code and/or be time consuming.
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td class="bg-warning" style=" background-color:#e69534; color:#fff;">Medium</td>
                                <td>Exploitation may be achieved by intermediate level intruder - customization of existing code may be required.</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td class="bg-success">Low</td>
                                <td>2Automated tools and/or public documentation exists that will make exploitation trivial.
                                </td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td class="bg-info">Informational</td>
                                <td>2Automated tools and/or public documentation exists that will make exploitation trivial.
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h3><b>Remediation Service Level Agreement (SLA):</b></h3>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
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
                                <td>To be fixed by <?php echo e($pdfinfo[0]->m_hours); ?> Hours or next scanning (Quarterly) cycle </td>
                                <td><?php echo e($pdfinfo[0]->l_responsible); ?>  </td>
                                <td>HoD - <?php echo e($pdfinfo[0]->l_escalation); ?> </td>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h3><b>Index of open issues:</b></h3>
                </div>
            </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <td>#</td>
                            <td> Vulnerability Title </td>
                            <td>Risk</td>
                            <td>Status</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $mresults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $mresult): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e(++$key); ?></td>

                            <td><?php echo e($mresult['name']); ?></td>
                            <td><span class="label <?php echo e(($mresult['risk']==Config::get('enums.severity_status.Informational')) ? "label-info" :
                              (((($mresult['risk']==Config::get('enums.severity_status.Low'))  ? "label-success" :
                              (((($mresult['risk']==Config::get('enums.severity_status.Medium'))  ? "bg-orange" :
                              (((($mresult['risk']==Config::get('enums.severity_status.High'))  ? "bg-red" :
                              (((($mresult['risk']==Config::get('enums.severity_status.Critical'))  ? "bg-red" :
                              "empty"))))))))))))); ?>"><?php echo e($enums[$mresult['risk']]); ?></span></td>
                            <td>Open</td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    <div class="row">
        <div class="col-md-12">
            <?php $__currentLoopData = $mdetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $mdetails): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <ul class="list-group list-group-unbordered" id="list-group">
            <?php if($mdetails['module_id'] == 1): ?>

                <li class="list-group-item">
                   <h3> <b><?php echo e(++$key); ?>. <?php echo e($mdetails['name']); ?> </b></h3>
                </li>
                <li class="list-group-item">
        <b>Severity: </b><span><td><span class="label <?php echo e(($mdetails['risk']==Config::get('enums.severity_status.Informational')) ? "label-info" :
              (((($mdetails['risk']==Config::get('enums.severity_status.Low'))  ? "label-success" :
              (((($mdetails['risk']==Config::get('enums.severity_status.Medium'))  ? "bg-orange" :
              (((($mdetails['risk']==Config::get('enums.severity_status.High'))  ? "bg-red" :
              (((($mdetails['risk']==Config::get('enums.severity_status.Critical'))  ? "bg-red" :
              "empty"))))))))))))); ?>"><?php echo e($enums[$mdetails['risk']]); ?></span></td></span>
                </li>
                <li class="list-group-item">
                    <b>Host: </b>
                    <p><?php if(isset($mdetails['url_scheme'])): ?><?php echo e($mdetails['url_scheme']); ?><?php endif; ?><?php echo e($mdetails['host']); ?></p>
                </li>
                <li class="list-group-item">
                    <b>Technical Details: </b>
                    <p><?php if(!empty($mdetails['plugin_output'])): ?> <?php echo e($mdetails['plugin_output']); ?> <?php endif; ?></p>
                </li>
                <li class="list-group-item">
                    <b>Port: </b>
                    <p> <?php echo e($mdetails['port']); ?></p>
                </li>
                <li class="list-group-item">
                    <b>Details of Issue:  </b>
                    <p><?php echo e($mdetails['description']); ?>

                    </p>
                </li>
                <li class="list-group-item">
                    <b>Recommendation Summary & Fix:  </b>
                    <p><?php echo e($mdetails['solution']); ?></p>
                </li>
                <li class="list-group-item">
                    <b>Reference:  </b>
                    <p>No reference is available.</p>
                </li>
                <?php if(isset($mdetails['img_filename'])): ?>
                    <?php $__currentLoopData = $mdetails['img_filename']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="list-group-item">
                            <b>Vulnerability PoC <?php echo e($key+1); ?>: </b>
                            <img src="<?php echo e(Config::get('remote.connections.MDB_SERVER.host_http').'/img/'.$img); ?>" alt="<?php echo e($img); ?>" style="width:100%">
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>

            <?php elseif($mdetails['module_id'] == 2): ?>

                    <li class="list-group-item">
                        <h3> <b><?php echo e(++$key); ?>. <?php echo e($mdetails['name']); ?> </b></h3>
                    </li>
                    <li class="list-group-item">
                        <b>Severity: </b><span><td><span class="label <?php echo e(($mdetails['risk']==Config::get('enums.severity_status.Informational')) ? "label-info" :
              (((($mdetails['risk']==Config::get('enums.severity_status.Low'))  ? "label-success" :
              (((($mdetails['risk']==Config::get('enums.severity_status.Medium'))  ? "bg-orange" :
              (((($mdetails['risk']==Config::get('enums.severity_status.High'))  ? "bg-red" :
              (((($mdetails['risk']==Config::get('enums.severity_status.Critical'))  ? "bg-red" :
              "empty"))))))))))))); ?>"><?php echo e($enums[$mdetails['risk']]); ?></span></td></span>
                    </li>
                    <li class="list-group-item">
                        <b>Host: </b>
                        <p><?php if(isset($mdetails['url_scheme'])): ?><?php echo e($mdetails['url_scheme']); ?><?php endif; ?><?php echo e(urldecode($mdetails['host'])); ?></p>
                    </li>
                    <li class="list-group-item">
                        <b>Impact: </b>
                        <p> <?php echo e($mdetails['Impact']); ?></p>
                    </li>
                    <li class="list-group-item">
                        <b>Module Name: </b>
                        <p> <?php echo e($mdetails['ModuleName']); ?></p>
                    </li>
                    <li class="list-group-item">
                        <b>Details: </b>
                        <p> <?php echo e($mdetails['Details']); ?></p>
                    </li>
                    <li class="list-group-item">
                        <b>Description:  </b>
                        <p><?php echo e($mdetails['Description']); ?></p>
                    </li>
                    <li class="list-group-item">
                        <b>Recommendation:  </b>
                        <p><?php echo e($mdetails['Recommendation']); ?>

                        </p>
                    </li>
                    <?php if(isset($mdetails['img_filename'])): ?>
                        <?php $__currentLoopData = $mdetails['img_filename']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="list-group-item">
                                <b>Vulnerability PoC <?php echo e($key+1); ?>: </b>
                                <img src="<?php echo e(Config::get('remote.connections.MDB_SERVER.host_http').'/img/'.$img); ?>" alt="<?php echo e($img); ?>" style="width:100%">
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>

                <?php elseif($mdetails['module_id'] == 3): ?>

                    <li class="list-group-item">
                        <h3> <b><?php echo e(++$key); ?>. <?php echo e($mdetails['name']); ?> </b></h3>
                    </li>
                    <?php if(!empty($mdetails['risk'])): ?>
                        <li class="list-group-item">
                            <b>Severity: </b><span><td><span class="label <?php echo e(($mdetails['risk']==Config::get('enums.severity_status.Informational')) ? "label-info" :
              (((($mdetails['risk']==Config::get('enums.severity_status.Low'))  ? "label-success" :
              (((($mdetails['risk']==Config::get('enums.severity_status.Medium'))  ? "bg-orange" :
              (((($mdetails['risk']==Config::get('enums.severity_status.High'))  ? "bg-red" :
              (((($mdetails['risk']==Config::get('enums.severity_status.Critical'))  ? "bg-red" :
              "empty"))))))))))))); ?>"><?php echo e($enums[$mdetails['risk']]); ?></span></td></span>
                        </li>
                    <?php endif; ?>
                    <li class="list-group-item">
                        <b>Host: </b>
                        <p><?php echo e($mdetails['host']); ?></p>
                    </li>
                    <li class="list-group-item">
                        <b>OS: </b>
                        <p><?php if(!empty($mdetails['OS'])): ?> <?php echo e($mdetails['OS']); ?> <?php endif; ?></p>
                    </li>
                    <li class="list-group-item">
                        <b>OS Version: </b>
                        <p><?php if(!empty($mdetails['OS_version'])): ?> <?php echo e($mdetails['OS_version']); ?> <?php endif; ?></p>
                    </li>
                    <li class="list-group-item">
                        <b>Description:  </b>
                        <p><?php if(!empty($mdetails['description'])): ?> <?php echo e($mdetails['description']); ?> <?php endif; ?></p>
                    </li>
                    <li class="list-group-item">
                        <b>Summary: </b>
                        <p><?php if(!empty($mdetails['summary'])): ?> <?php echo e($mdetails['summary']); ?> <?php endif; ?></p>
                    </li>
                    <li class="list-group-item">
                        <b>Fix: </b>
                        <p> <?php if(!empty($mdetails['fix'])): ?> <?php echo e($mdetails['fix']); ?> <?php endif; ?></p>
                    </li>
                    <?php if(!empty($mdetails['img_filename'])): ?>
                        <?php if(isset($mdetails['img_filename'])): ?>
                            <?php $__currentLoopData = $mdetails['img_filename']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="list-group-item">
                                    <b>Vulnerability PoC <?php echo e($key+1); ?>: </b>
                                    <img class="img-responsive" src="<?php echo e(Config::get('remote.connections.MDB_SERVER.host_http').'/img/'.$img); ?>" alt="<?php echo e($img); ?>" style="width:100%">
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    <?php endif; ?>

                <?php elseif($mdetails['module_id'] == 4): ?>

                    <li class="list-group-item">
                        <h3> <b><?php echo e(++$key); ?>. <?php echo e($mdetails['name']); ?> </b></h3>
                    </li>
                    <li class="list-group-item">
                        <b>Severity: </b><span><td><span class="label <?php echo e(($mdetails['risk']==Config::get('enums.severity_status.Informational')) ? "label-info" :
              (((($mdetails['risk']==Config::get('enums.severity_status.Low'))  ? "label-success" :
              (((($mdetails['risk']==Config::get('enums.severity_status.Medium'))  ? "bg-orange" :
              (((($mdetails['risk']==Config::get('enums.severity_status.High'))  ? "bg-red" :
              (((($mdetails['risk']==Config::get('enums.severity_status.Critical'))  ? "label-dark" :
              "empty"))))))))))))); ?>"><?php echo e($enums[$mdetails['risk']]); ?></span></td></span>
                    </li>
                    <li class="list-group-item">
                        <b>Host: </b>
                        <p><?php if(isset($mdetails['url_scheme'])): ?><?php echo e($mdetails['url_scheme']); ?><?php endif; ?><?php echo e($mdetails['host']); ?></p>
                    </li>
                    <li class="list-group-item">
                        <b>Port: </b>
                        <p> <?php echo e($mdetails['port']); ?></p>
                    </li>
                    <li class="list-group-item">
                    <b>Description:
                    <p> <?php if(!empty($mdetails['Description'])): ?> <?php echo e($mdetails['Description']); ?> <?php endif; ?></p></b>
                    </li>
                    <li class="list-group-item">
                    <b>Recommendation:  </b>
                        <p> <?php if(!empty($mdetails['Recommendation'])): ?> <?php echo e($mdetails['Recommendation']); ?> <?php endif; ?></p></b>
                    </p>
                    </li>
                    <?php if(isset($mdetails['img_filename'])): ?>
                        <?php $__currentLoopData = $mdetails['img_filename']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="list-group-item">
                                <b>Vulnerability PoC <?php echo e($key+1); ?>: </b>
                                <img src="<?php echo e(Config::get('remote.connections.MDB_SERVER.host_http').'/img/'.$img); ?>" alt="<?php echo e($img); ?>" style="width:100%">
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>

                <?php elseif($mdetails['module_id'] == 5): ?>

                    <li class="list-group-item">
                        <h3> <b><?php echo e(++$key); ?>. <?php echo e($mdetails['name']); ?> </b></h3>
                    </li>
                    <li class="list-group-item">
                        <b>Severity: </b><span><td><span class="label <?php echo e(($mdetails['risk']==Config::get('enums.severity_status.Informational')) ? "label-info" :
              (((($mdetails['risk']==Config::get('enums.severity_status.Low'))  ? "label-success" :
              (((($mdetails['risk']==Config::get('enums.severity_status.Medium'))  ? "bg-orange" :
              (((($mdetails['risk']==Config::get('enums.severity_status.High'))  ? "bg-red" :
              (((($mdetails['risk']==Config::get('enums.severity_status.Critical'))  ? "label-dark" :
              "empty"))))))))))))); ?>"><?php echo e($enums[$mdetails['risk']]); ?></span></td></span>
                    </li>
                    <li class="list-group-item">
                        <b>Host: </b>
                        <p><?php if(isset($mdetails['url_scheme'])): ?><?php echo e($mdetails['url_scheme']); ?><?php endif; ?><?php echo e($mdetails['host']); ?></p>
                    </li>
                    <li class="list-group-item">
                        <b>Background: </b>
                        <p> <?php if(!empty($mdetails['background'])): ?> <?php echo e($mdetails['background']); ?> <?php endif; ?></p>
                    </li>
                    <li class="list-group-item">
                        <b>Remediation: </b>
                        <p> <?php if(!empty($mdetails['remediation'])): ?> <?php echo e($mdetails['remediation']); ?> <?php endif; ?></p>
                    </li>
                    <?php if(isset($mdetails['img_filename'])): ?>
                        <?php $__currentLoopData = $mdetails['img_filename']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="list-group-item">
                                <b>Vulnerability PoC <?php echo e($key+1); ?>: </b>
                                <img src="<?php echo e(Config::get('remote.connections.MDB_SERVER.host_http').'/img/'.$img); ?>" alt="<?php echo e($img); ?>" style="width:100%">
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>

                <?php endif; ?>
                
                    
                    
                
            </ul>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
  <?php $__currentLoopData = $pdfinfo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="text-center">
                <h2><b>Methodology</b></h2>
                <h4><a><?php echo e($item->m_title); ?></a> </h4>
            </div>
            <p><?php echo $item->m_description; ?></p>
        </div>
        <h3><b>Disclaimer</b></h3>
        <p><?php echo $item->disclaimer; ?></p>
    </div><br><br>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
       <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <button type="button" href="<?php echo e(url('forward2sign_off/'.$sresults[0]->id)); ?>" style="width:100%; margin-bottom:30px;" data-toggle="example1" class="btn btn-success btn-flat pull-right">Sign Off</button>
        </div>
       </div>
    </div>
     </div>
     <?php endif; ?>
<?php $__env->stopSection(); ?>
 <?php $__env->startSection('custom-scripts'); ?>
     <script type="text/javascript">
         $('[data-toggle=example1]').confirmation({
             rootSelector: '[data-toggle=example1]',
         });
     </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
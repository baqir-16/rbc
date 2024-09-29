<?php $__env->startSection('title', 'Remediation PMO'); ?>
<?php $__env->startSection('content'); ?>

    <!-- start .flash-message -->
    <div class="flash-message">
        <?php $__currentLoopData = ['danger', 'warning', 'success', 'info']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(Session::has('alert-' . $msg)): ?>
                <p class="alert alert-<?php echo e($msg); ?>"><?php echo e(Session::get('alert-' . $msg)); ?> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <!-- end .flash-message -->

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('rempmo_view')): ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><strong>Remediation PMO (Closed Findings)</strong> - <?php echo e(str_plural('Total Number of Record', $result->count() )); ?>  <?php echo e($result->total()); ?> </h3>
                
            </div>
            <div class="box-body  no-padding">
                <div class="box-body mailbox-messages">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th width="25%">Name</th>
                            <?php if( Auth::user()->hasRole('Admin') ): ?>
                         <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin_view')): ?>
                            <th>Department</th>
                            <?php endif; ?>
                         <?php endif; ?>
                            <th width="25%">Category</th>
                            <th>Risk</th>
                            <th width="25%">Host</th>
                            <th width="25%">Port</th>
                            <th>Reported On</th>
                            <th>Closed On</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $result; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><input value="<?php echo e($item['_id']); ?>" type="checkbox"></td>
                                <td><?php if(!empty($item['name'])): ?> <?php echo e($item['name']); ?> <?php endif; ?></td>
                                <?php if( Auth::user()->hasRole('Admin') ): ?>
                                <?php if(!empty($departments[$item['department']])): ?>
                                    <td><?php echo e($departments[$item['department']]); ?></td>
                                <?php else: ?>
                                    <td></td>
                                <?php endif; ?>
                                <?php else: ?>
                                    <?php endif; ?>
                                <td><?php if(!empty($item['vul_category'])): ?> <?php echo e(isset($enums2[$item['vul_category']]) ? $enums2[$item['vul_category']]: ""); ?> <?php endif; ?></td>
                                <td><?php if(!empty($item['risk'])): ?> <span class="label <?php echo e(($item['risk']==Config::get('enums.severity_status.Informational')) ? "label-info" :
                              (((($item['risk']==Config::get('enums.severity_status.Low'))  ? "label-success" :
                              (((($item['risk']==Config::get('enums.severity_status.Medium'))  ? "bg-orange" :
                              (((($item['risk']==Config::get('enums.severity_status.High'))  ? "bg-red" :
                              (((($item['risk']==Config::get('enums.severity_status.Critical'))  ? "label-dark" :
                              "empty"))))))))))))); ?>"><?php echo e($enums[$item['risk']]); ?></span> <?php endif; ?></td>
                                <?php if($item['module_id'] == 3): ?>
                                    <td><?php if(!empty($item['ip_address'])): ?> <?php echo e($item['ip_address']); ?> <?php endif; ?></td>
                                <?php else: ?>
                                    <td style="width:20%;"> <?php if(isset($item['url_scheme'])): ?><?php echo e($item['url_scheme']); ?><?php endif; ?><?php echo e(substr(urldecode($item['host']), 0, 30)); ?></td>
                                <?php endif; ?>
                                <td style="width:5%;"> <?php if(!empty($item['port'])): ?> <?php echo e($item['port']); ?> <?php endif; ?></td>
                                <td style="width:10%;"><?php echo e(date('d/m/Y', substr($item['hod_signoff_date'],0,10))); ?></td>
                                <td style="width:10%;"><?php echo e(date('d/m/Y', substr($item['rem_pmo_closure_date'],0,10))); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('custom-scripts'); ?>
    <script>
        $(function () {
            $('#example1').DataTable({
                'dom'         : '<"top"flp<"clear">>rt<"bottom"ip<"clear">>',
                'paging'      : true,
                'lengthChange': true,
                'searching'   : true,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : true
            });

            //        $('.mailbox-messages input[type="checkbox"]').iCheck({
            //            checkboxClass: 'icheckbox_flat-blue',
            //            radioClass: 'iradio_flat-blue'
            //       });

            //Enable check and uncheck all functionality
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
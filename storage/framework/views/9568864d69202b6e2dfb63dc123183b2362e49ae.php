<?php $__env->startSection('title', 'Tester View Findings'); ?>
<?php $__env->startSection('content'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('tester_view')): ?>
        <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><?php echo e(str_plural('Total Number of Record', count($result))); ?>  <?php echo e(count($result)); ?> </h3>
                    <a href="<?php echo e(route('Tester.index')); ?>" class="btn  btn-default btn-sm btn-flat pull-right">Back</a>
                </div>


                <!-- /.box-header -->
                <div class="box-body mailbox-messages">

                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th width="25%">Name</th>
                            <th>Risk</th>
                            <th>Host</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php $__currentLoopData = $result; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <?php if($item['module_id'] == 1): ?>
                                    <td> <?php echo e(($key++)+1); ?></td>
                                    <td><?php if(!empty($item['name'])): ?> <?php echo e($item['name']); ?> <?php endif; ?></td>
                                    <td><?php if(!empty($item['risk'])): ?> <span class="label <?php echo e(($item['risk']==Config::get('enums.severity_status.Informational')) ? "label-info" :
                              (((($item['risk']==Config::get('enums.severity_status.Low'))  ? "label-success" :
                              (((($item['risk']==Config::get('enums.severity_status.Medium'))  ? "bg-orange" :
                              (((($item['risk']==Config::get('enums.severity_status.High'))  ? "bg-red" :
                              (((($item['risk']==Config::get('enums.severity_status.Critical'))  ? "label-dark" :
                              "empty"))))))))))))); ?>"><?php echo e($enums[$item['risk']]); ?></span> <?php endif; ?></td>
                                    <td><?php if(!empty($item['host'])): ?> <?php echo e($item['host']); ?> <?php endif; ?></td>

                                <?php elseif($item['module_id'] == 2): ?>
                                    <td> <?php echo e(($key++)+1); ?></td>
                                    <td><?php if(!empty($item['name'])): ?> <?php echo e($item['name']); ?> <?php endif; ?></td>
                                    <td><?php if(!empty($item['risk'])): ?> <span class="label <?php echo e(($item['risk']==Config::get('enums.severity_status.Informational')) ? "label-info" :
                              (((($item['risk']==Config::get('enums.severity_status.Low'))  ? "label-success" :
                              (((($item['risk']==Config::get('enums.severity_status.Medium'))  ? "bg-orange" :
                              (((($item['risk']==Config::get('enums.severity_status.High'))  ? "bg-red" :
                              (((($item['risk']==Config::get('enums.severity_status.Critical'))  ? "label-dark" :
                              "empty"))))))))))))); ?>"><?php echo e($enums[$item['risk']]); ?></span> <?php endif; ?></td>
                                    <td> <?php if(isset($item['url_scheme'])): ?> <?php echo e($item['url_scheme']); ?> <?php endif; ?><?php echo e(urldecode($item['host'])); ?></td>

                                <?php elseif($item['module_id'] == 3): ?>
                                    <td> <?php echo e(($key++)+1); ?></td>
                                    <td><?php if(!empty($item['name'])): ?> <?php echo e($item['name']); ?> <?php endif; ?></td>
                                    <td><?php if(!empty($item['risk'])): ?> <span class="label <?php echo e(($item['risk']==Config::get('enums.severity_status.Informational')) ? "label-info" :
                              (((($item['risk']==Config::get('enums.severity_status.Low'))  ? "label-success" :
                              (((($item['risk']==Config::get('enums.severity_status.Medium'))  ? "bg-orange" :
                              (((($item['risk']==Config::get('enums.severity_status.High'))  ? "bg-red" :
                              (((($item['risk']==Config::get('enums.severity_status.Critical'))  ? "label-dark" :
                              "empty"))))))))))))); ?>"><?php echo e($enums[$item['risk']]); ?></span> <?php endif; ?></td>
                                    <td><?php if(!empty($item['ip_address'])): ?> <?php echo e($item['ip_address']); ?> <?php endif; ?> <?php if(!empty($item['host'])): ?> <?php echo e($item['host']); ?> <?php endif; ?></td>

                                <?php elseif($item['module_id'] == 4): ?>
                                    <td> <?php echo e(($key++)+1); ?></td>
                                    <td><?php if(!empty($item['name'])): ?> <?php echo e($item['name']); ?> <?php endif; ?></td>
                                    <td><?php if(!empty($item['risk'])): ?> <span class="label <?php echo e(($item['risk']==Config::get('enums.severity_status.Informational')) ? "label-info" :
                              (((($item['risk']==Config::get('enums.severity_status.Low'))  ? "label-success" :
                              (((($item['risk']==Config::get('enums.severity_status.Medium'))  ? "bg-orange" :
                              (((($item['risk']==Config::get('enums.severity_status.High'))  ? "bg-red" :
                              (((($item['risk']==Config::get('enums.severity_status.Critical'))  ? "label-dark" :
                              "empty"))))))))))))); ?>"><?php echo e($enums[$item['risk']]); ?></span> <?php endif; ?></td>
                                    <td><?php if(!empty($item['host'])): ?> <?php echo e($item['host']); ?> <?php endif; ?></td>


                                <?php endif; ?>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('custom-scripts'); ?>
    <script>
        $(function () {
            $('#example1').DataTable({
                'paging'      : true,
                'lengthChange': true,
                'searching'   : true,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : true
            });
        });

    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
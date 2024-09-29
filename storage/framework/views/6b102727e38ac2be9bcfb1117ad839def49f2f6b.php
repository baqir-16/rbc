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

    <div class="box">
        <div class="box-header">
            <h3 class="box-title"><strong> Risk Details </strong>  </h3>
            <a href="<?php echo e(url('home')); ?>" class="btn  btn-default btn-sm btn-flat pull-right">Back</a>
        </div>
        <div class="box-body  no-padding">
            <div class="box-body mailbox-messages">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Opco</th>
                        <th width="25%">Name</th>
                        <th>Risk</th>
                        <th>Host</th>
                        <th class="text-center">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if($id == 1): ?>

                    <?php $__currentLoopData = $critical_risk_dt; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td> <?php echo e($opcos[$item['opco_id']]); ?></td>
                            <td width="25%"> <?php echo e($item['name']); ?></td>
                            <td><span class="label <?php echo e(($item['risk']==Config::get('enums.severity_status.Informational')) ? "label-info" :
              (((($item['risk']==Config::get('enums.severity_status.Low'))  ? "label-success" :
              (((($item['risk']==Config::get('enums.severity_status.Medium'))  ? "bg-orange" :
              (((($item['risk']==Config::get('enums.severity_status.High'))  ? "bg-red" :
              (((($item['risk']==Config::get('enums.severity_status.Critical'))  ? "label-dark" :
              "empty"))))))))))))); ?>"><?php echo e($enums[$item['risk']]); ?></span></td>
                            <td> <?php echo e(urldecode($item['host'])); ?></td>
                            <td class="text-center">
                                <div class="row">
                                    <?php if($item['module_id'] == 1): ?>
                                        <a href="<?php echo e(url('showcsv', ['_id' => $item['_id']])); ?>" type="button" class="btn btn-info btn-xs ">Details</a>

                                    <?php elseif($item['module_id'] == 2): ?>
                                        <a href="<?php echo e(url('showxml', ['_id' => $item['_id']])); ?>" type="button" class="btn btn-info btn-xs ">Details</a>

                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php elseif( $id == 2): ?>

                        <?php $__currentLoopData = $high_risk_dt; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td> <?php echo e($opcos[$item['opco_id']]); ?></td>
                                <td width="25%"> <?php echo e($item['name']); ?></td>
                                <td><span class="label <?php echo e(($item['risk']==Config::get('enums.severity_status.Informational')) ? "label-info" :
              (((($item['risk']==Config::get('enums.severity_status.Low'))  ? "label-success" :
              (((($item['risk']==Config::get('enums.severity_status.Medium'))  ? "bg-orange" :
              (((($item['risk']==Config::get('enums.severity_status.High'))  ? "bg-red" :
              (((($item['risk']==Config::get('enums.severity_status.Critical'))  ? "label-dark" :
              "empty"))))))))))))); ?>"><?php echo e($enums[$item['risk']]); ?></span></td>
                                <td> <?php echo e(urldecode($item['host'])); ?></td>
                                <td class="text-center">
                                    <div class="row">
                                        <?php if($item['module_id'] == 1): ?>
                                            <a href="<?php echo e(url('showcsv', ['_id' => $item['_id']])); ?>" type="button" class="btn btn-info btn-xs ">Details</a>

                                        <?php elseif($item['module_id'] == 2): ?>
                                            <a href="<?php echo e(url('showxml', ['_id' => $item['_id']])); ?>" type="button" class="btn btn-info btn-xs ">Details</a>

                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php elseif( $id == 3): ?>

                        <?php $__currentLoopData = $med_risk_dt; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td> <?php echo e($opcos[$item['opco_id']]); ?></td>
                                <td width="25%"> <?php echo e($item['name']); ?></td>
                                <td><span class="label <?php echo e(($item['risk']==Config::get('enums.severity_status.Informational')) ? "label-info" :
              (((($item['risk']==Config::get('enums.severity_status.Low'))  ? "label-success" :
              (((($item['risk']==Config::get('enums.severity_status.Medium'))  ? "bg-orange" :
              (((($item['risk']==Config::get('enums.severity_status.High'))  ? "bg-red" :
              (((($item['risk']==Config::get('enums.severity_status.Critical'))  ? "label-dark" :
              "empty"))))))))))))); ?>"><?php echo e($enums[$item['risk']]); ?></span></td>
                                <td> <?php echo e(urldecode($item['host'])); ?></td>
                                <td class="text-center">
                                    <div class="row">
                                        <?php if($item['module_id'] == 1): ?>
                                            <a href="<?php echo e(url('showcsv', ['_id' => $item['_id']])); ?>" type="button" class="btn btn-info btn-xs ">Details</a>

                                        <?php elseif($item['module_id'] == 2): ?>
                                            <a href="<?php echo e(url('showxml', ['_id' => $item['_id']])); ?>" type="button" class="btn btn-info btn-xs ">Details</a>

                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php elseif( $id == 4): ?>

                        <?php $__currentLoopData = $low_risk_dt; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td> <?php echo e($opcos[$item['opco_id']]); ?></td>
                                <td width="25%"> <?php echo e($item['name']); ?></td>
                                <td><span class="label <?php echo e(($item['risk']==Config::get('enums.severity_status.Informational')) ? "label-info" :
              (((($item['risk']==Config::get('enums.severity_status.Low'))  ? "label-success" :
              (((($item['risk']==Config::get('enums.severity_status.Medium'))  ? "bg-orange" :
              (((($item['risk']==Config::get('enums.severity_status.High'))  ? "bg-red" :
              (((($item['risk']==Config::get('enums.severity_status.Critical'))  ? "label-dark" :
              "empty"))))))))))))); ?>"><?php echo e($enums[$item['risk']]); ?></span></td>
                                <td> <?php echo e(urldecode($item['host'])); ?></td>
                                <td class="text-center">
                                    <div class="row">
                                        <?php if($item['module_id'] == 1): ?>
                                            <a href="<?php echo e(url('showcsv', ['_id' => $item['_id']])); ?>" type="button" class="btn btn-info btn-xs ">Details</a>

                                        <?php elseif($item['module_id'] == 2): ?>
                                            <a href="<?php echo e(url('showxml', ['_id' => $item['_id']])); ?>" type="button" class="btn btn-info btn-xs ">Details</a>

                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <?php $__currentLoopData = $pending_rem_dt; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td> <?php echo e($opcos[$item['opco_id']]); ?></td>
                                <td width="25%"> <?php echo e($item['name']); ?></td>
                                <td><span class="label <?php echo e(($item['risk']==Config::get('enums.severity_status.Informational')) ? "label-info" :
              (((($item['risk']==Config::get('enums.severity_status.Low'))  ? "label-success" :
              (((($item['risk']==Config::get('enums.severity_status.Medium'))  ? "bg-orange" :
              (((($item['risk']==Config::get('enums.severity_status.High'))  ? "bg-red" :
              (((($item['risk']==Config::get('enums.severity_status.Critical'))  ? "label-dark" :
              "empty"))))))))))))); ?>"><?php echo e($enums[$item['risk']]); ?></span></td>
                                <td> <?php echo e(urldecode($item['host'])); ?></td>
                                <td class="text-center">
                                    <div class="row">
                                        <?php if($item['module_id'] == 1): ?>
                                            <a href="<?php echo e(url('showcsv', ['_id' => $item['_id']])); ?>" type="button" class="btn btn-info btn-xs ">Details</a>
                                        <?php elseif($item['module_id'] == 2): ?>
                                            <a href="<?php echo e(url('showxml', ['_id' => $item['_id']])); ?>" type="button" class="btn btn-info btn-xs ">Details</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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

        function closeone(id,key) {
            var token = "<?php echo e(csrf_token()); ?>";
            $.ajax({
                type: 'GET',
                url: "<?php echo e(route('closeone')); ?>",
                data: {id: id, _token: token},
                success: function (data) {
                    location.reload()
                }
            });
        }

        function closeAll()
        {
            //event.stopPropagation(); // Stop stuff happening
            //event.preventDefault(); // Totally stop stuff happening
            var allVals = [];
            var token = "<?php echo e(csrf_token()); ?>";

            // Create a formdata object and add the files
            var data = new FormData();
            data.append('_token', token);

            $("input[type=checkbox]:checked").each(function(){
                allVals.push($(this).val());
            });
            data.append("allvals", allVals);
            // data.append("stream_id")
            $.ajax({
                url: "<?php echo e(route('closeAll')); ?>",
                type: 'POST',
                data: data,
                // data: {ids: allVals, _token: token},
                cache: false,
                processData: false, // Don't process the files
                contentType: false, // Set content type to false as jQuery will tell the server its a query string request
                success: function(data)
                {
                    console.log(data);
                    // Success so call function to process the form
                    // alert(data);
                    location.reload();
                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
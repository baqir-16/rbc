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
            <h3 class="box-title"><strong>Remediation PMO</strong> - <?php echo e(str_plural('Total Number of Record', $result->count() + $result1->count())); ?>  <?php echo e($result->total() + $result1->total()); ?> </h3>
            
        </div>
        <div class="box-body  no-padding">
            <div class="mailbox-controls">
                <button type="button" class="btn btn-info btn-sm btn-flat checkbox-toggle"><i class="fa fa-square-o"></i> Select All</button>
                <button  onclick="return closeAll();" type="button" class="btn  btn-success btn-sm btn-flat" >Close Selected</button>
            </div>
            <!-- /.box-header -->
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
                            <th>Risk</th>
                            <th width="25%">Host</th>
                            <th width="25%">Port</th>
                            <th>Reported On</th>
                            <th>Remediated?</th>
                            <th width="15%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $result; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><input value="<?php echo e($item['_id']); ?>" type="checkbox"></td>
                            <td> <?php echo e($item['name']); ?></td>
                             <?php if( Auth::user()->hasRole('Admin') ): ?>
                             <?php if($item['department'] != null): ?>
                            <?php if(!empty($departments[$item['department']])): ?>
                                    <td><?php echo e($departments[$item['department']]); ?></td>
                                <?php else: ?>
                                    <td></td>
                                <?php endif; ?>
                                <?php else: ?>
                                    <?php endif; ?>
                                    <?php else: ?>
                                <?php endif; ?>
                            <td><span class="label <?php echo e(($item['risk']==Config::get('enums.severity_status.Informational')) ? "label-info" :
              (((($item['risk']==Config::get('enums.severity_status.Low'))  ? "label-success" :
              (((($item['risk']==Config::get('enums.severity_status.Medium'))  ? "bg-orange" :
              (((($item['risk']==Config::get('enums.severity_status.High'))  ? "bg-red" :
              (((($item['risk']==Config::get('enums.severity_status.Critical'))  ? "label-dark" :
              "empty"))))))))))))); ?>"><?php echo e($enums[$item['risk']]); ?></span></td>
                            <td> <?php if(isset($item['url_scheme'])): ?><?php echo e($item['url_scheme']); ?><?php endif; ?><?php echo e(substr(urldecode($item['host']), 0, 30)); ?></td>
                            <td style="width:5%;"> <?php if(!empty($item['port'])): ?> <?php echo e($item['port']); ?> <?php endif; ?></td>
                            <td style="width:10%;"><?php echo e(date('d/m/Y', substr($item['hod_signoff_date'],0,10))); ?></td>

                            <td>
                                <span class="label label-primary">Yes</span>
                            </td>
                            <td width="10%">
                                <div class="row">
                                    <?php if($item['module_id'] == 1): ?>
                                        <a href="<?php echo e(route('Rempmo.show', ['_id' => $item['_id']])); ?>" type="button" class="btn btn-info btn-xs ">Details</a>
                                    <?php elseif($item['module_id'] == 2): ?>
                                        <a href="<?php echo e(url('pmoshowxml', ['_id' => $item['_id']])); ?>" type="button" class="btn btn-info btn-xs ">Details</a>
                                    <?php elseif($item['module_id'] == 3): ?>
                                        <a href="<?php echo e(url('pmoshownexpose', ['_id' => $item['_id']])); ?>" type="button" class="btn btn-info btn-xs ">Details</a>
                                    <?php elseif($item['module_id'] == 4): ?>
                                        <a href="<?php echo e(url('pmoshowappscan', ['_id' => $item['_id']])); ?>" type="button" class="btn btn-info btn-xs ">Details</a>
                                    <?php elseif($item['module_id'] == 5): ?>
                                        <a href="<?php echo e(url('pmoshowburp', ['_id' => $item['_id']])); ?>" type="button" class="btn btn-info btn-xs ">Details</a>
                                    <?php endif; ?>
                                    <a  href="<?php echo e(url('closeone1', ['_id' => $item['_id']])); ?>" type="button" class="btn btn-success btn-xs " data-toggle="example1" data-title="Are you sure?">Close</a>
                                        <a  onclick="openofficer('<?php echo e($item['_id']); ?>')" type="button" class="btn btn-danger btn-xs ">Open</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php $__currentLoopData = $result1; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><input value="<?php echo e($item['_id']); ?>" type="checkbox"></td>
                            <td> <?php echo e($item['name']); ?></td>
                            <?php $__currentLoopData = $opco_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opco): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if((int)$opco['id'] == $item['opco_id']): ?>
                                    <td><?php echo e($opco['opco']); ?></td>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <td><span class="label <?php echo e(($item['risk']==Config::get('enums.severity_status.Informational')) ? "label-info" :
              (((($item['risk']==Config::get('enums.severity_status.Low'))  ? "label-success" :
              (((($item['risk']==Config::get('enums.severity_status.Medium'))  ? "bg-orange" :
              (((($item['risk']==Config::get('enums.severity_status.High'))  ? "bg-red" :
              (((($item['risk']==Config::get('enums.severity_status.Critical'))  ? "label-dark" :
              "empty"))))))))))))); ?>"><?php echo e($enums[$item['risk']]); ?></span></td>
                            <td> <?php if(isset($item['url_scheme'])): ?><?php echo e($item['url_scheme']); ?><?php endif; ?><?php echo e(substr(urldecode($item['host']), 0, 30)); ?></td>
                            <td style="width:5%;"> <?php if(!empty($item['port'])): ?> <?php echo e($item['port']); ?> <?php endif; ?></td>
                            <td style="width:10%;"><?php echo e(date('d/m/Y', substr($item['hod_signoff_date'],0,10))); ?></td>
                            <td>
                                <span class="label label-primary">Yes</span>
                            </td>
                            <td width="10%">
                                <div class="row">
                                    <?php if($item['module_id'] == 1): ?>
                                        <a href="<?php echo e(route('Rempmo.show', ['_id' => $item['_id']])); ?>" type="button" class="btn btn-info btn-xs ">Details</a>
                                    <?php elseif($item['module_id'] == 2): ?>
                                        <a href="<?php echo e(url('pmoshowxml', ['_id' => $item['_id']])); ?>" type="button" class="btn btn-info btn-xs ">Details</a>
                                    <?php elseif($item['module_id'] == 4): ?>
                                        <a href="<?php echo e(url('pmoshowappscan', ['_id' => $item['_id']])); ?>" type="button" class="btn btn-info btn-xs ">Details</a>
                                    <?php endif; ?>
                                    <a  href="<?php echo e(url('closeone1', ['_id' => $item['_id']])); ?>" type="button" class="btn btn-success btn-xs " data-toggle="example1" data-title="Are you sure?">Close</a>
                                    <a  onclick="openofficer('<?php echo e($item['_id']); ?>')" type="button" class="btn btn-danger btn-xs ">Open</a>
                                </div>
                            </td>
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

        
            
            
                
                
                
                
                    
                
            
        

        $('[data-toggle=example1]').confirmation({
            rootSelector: '[data-toggle=example1]',
            // other options
        });


        function openofficer(id,key) {
            var token = "<?php echo e(csrf_token()); ?>";
            $.ajax({
                type: 'GET',
                url: "<?php echo e(route('openofficer')); ?>",
                data: {id: id, _token: token},
                success: function (data) {
                    location.reload()
                }
            });
        }
            function closeAll()
            {
                if(!confirm("Are You Sure to close this"))
                {
                    event.preventDefault();
                }

                else{
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


            }



    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
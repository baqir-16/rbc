<?php $__env->startSection('title', 'Remediation Officer'); ?>
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

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('remofficer_view')): ?>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title"><strong>Remediation Officer </strong> -  <?php echo e(str_plural('Total Number of Record', count($result) +  count($result1))); ?>  <?php echo e(count($result) +  count($result1)); ?> </h3>
                

            </div>
            <div class="box-body  no-padding">
                <div class="mailbox-controls">
                    <?php echo e(Form::open(array('action'=>'RemofficerController@updateRemediated', 'class' => '_dropzone', 'id' => 'upload_new_form', 'method' => 'post', 'files' => true, 'enctype'=>'multipart/form-data'))); ?>

                    <?php echo e(csrf_field()); ?>

                    <button type="button" class="btn btn-info btn-sm btn-flat checkbox-toggle"><i class="fa fa-square-o"></i> Select All</button>
                    
                    <a href="<?php echo e(url('exportOpco')); ?>" type="button" class="btn  btn-warning btn-sm btn-flat">Export CSV</a>
                    <label for="upload_new_file" class="btn btn-primary btn-sm btn-flat">Upload Remediated</label>
                    <input  name="file[]" type="file" class="hide" accept=".xlsx" multiple  id="upload_new_file">
                    
                    
                    
                    
                    
                    
                    <?php echo e(Form::close()); ?>

                    
                </div>
                <!-- /.box-header -->
                <div class="box-body mailbox-messages">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th width="20%">Name</th>
                            <?php if( Auth::user()->hasRole('Admin') ): ?>
                         <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin_view')): ?>
                            <th>Department</th>
                            <?php endif; ?>
                         <?php endif; ?>
                            <th>Status</th>
                            <th>Risk</th>
                            <th>Host</th>
                            <th>Port</th>
                            <th>Days Open</th>
                            <th>Reported On</th>
                            <th>Target Fix Date</th>
                            <th>Revalidation Date</th>
                            <th>Category</th>
                            <th class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $result; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(!empty($item['_id'])): ?>
                                <tr>

                                    <td><input value="<?php echo e($item['_id']); ?>" type="checkbox"></td>
                                    <td><?php if(isset($item['name'])): ?> <?php echo e($item['name']); ?> <?php endif; ?></td>
                                     <?php if( Auth::user()->hasRole('Admin') ): ?>
                                     <?php if($item['department'] != null): ?>
                                    <?php if(isset($departments[$item['department']])): ?>
                                    <td><?php echo e($departments[$item['department']]); ?></td>
                                <?php else: ?>
                                    <td></td>
                                <?php endif; ?>
                                <?php else: ?>
                                
                                <?php endif; ?>
                                <?php else: ?>
                                <?php endif; ?>
                                    <td><span class="label <?php echo e(($item['rem_officer_rem_status']==Config::get('enums.mdb_stream_status.Open')) ? "bg-red" : "empty"); ?>"><?php echo e($enums1[$item['rem_officer_rem_status']]); ?></span></td>
                                    <td><?php if(!empty($item['risk'])): ?> <span class="label <?php echo e(($item['risk']==Config::get('enums.severity_status.Informational')) ? "label-info" :
                              (((($item['risk']==Config::get('enums.severity_status.Low'))  ? "label-success" :
                              (((($item['risk']==Config::get('enums.severity_status.Medium'))  ? "bg-orange" :
                              (((($item['risk']==Config::get('enums.severity_status.High'))  ? "bg-red" :
                              (((($item['risk']==Config::get('enums.severity_status.Critical'))  ? "label-dark" :
                              "empty"))))))))))))); ?>"><?php echo e($enums[$item['risk']]); ?></span> <?php endif; ?></td>
                                    <?php if($item['module_id'] == 3): ?>
                                        <td><?php if(!empty($item['host'])): ?> <?php echo e($item['host']); ?> <?php endif; ?></td>
                                    <?php else: ?>
                                        <td style="width:20%;"> <?php if(isset($item['url_scheme'])): ?><?php echo e($item['url_scheme']); ?><?php endif; ?><?php echo e(substr(urldecode($item['host']), 0, 30)); ?></td>
                                    <?php endif; ?>
                                    <td style="width:5%;"> <?php if(!empty($item['port'])): ?> <?php echo e($item['port']); ?> <?php endif; ?></td>
                                    <td style="width:8%;"><?php echo e(isset($item['days_open']) ? $item['days_open'] : ''); ?></td>
                                    <td style="width:10%;"><?php echo e(date('d/m/Y', substr($item['hod_signoff_date'],0,10))); ?></td>
                                    <td style="width:10%;"><?php echo e((isset($item['target_fix_date']) ? $item['target_fix_date'] : "")); ?></td>
                                    <td style="width:8%;"><?php if(isset($item['rem_officer_revalidation_date'])): ?><?php echo e(date('d/m/Y', substr($item['rem_officer_revalidation_date'],0,10))); ?> <?php else: ?> <?php echo e($item['rem_officer_revalidation_date'] = ""); ?> <?php endif; ?></td>
                                    <td><?php if(!empty($item['vul_category'])): ?> <?php echo e($vul_categories[$item['vul_category']]); ?> <?php endif; ?></td>
                                    <td style="width:15%; text-align:center;">
                                        <div class="row">
                                            <?php if($item['module_id'] == 1): ?>
                                                <a href="<?php echo e(route('Remofficer.show', ['_id' => $item['_id']])); ?>" target="_blank" type="button" class="btn btn-info btn-xs ">Details</a>

                                            <?php elseif($item['module_id'] == 2): ?>
                                                <a href="<?php echo e(url('remshowxml', ['_id' => $item['_id']])); ?>" target="_blank" type="button" class="btn btn-info btn-xs ">Details</a>

                                            <?php elseif($item['module_id'] == 3): ?>
                                                <a href="<?php echo e(url('shownexpose', ['_id' => $item['_id']])); ?>" target="_blank" type="button" class="btn btn-info btn-xs ">Details</a>

                                            <?php elseif($item['module_id'] == 4): ?>
                                                <a href="<?php echo e(url('showappscan', ['_id' => $item['_id']])); ?>" target="_blank" type="button" class="btn btn-info btn-xs ">Details</a>

                                            <?php elseif($item['module_id'] == 5): ?>
                                                <a href="<?php echo e(url('showburp', ['_id' => $item['_id']])); ?>" target="_blank" type="button" class="btn btn-info btn-xs ">Details</a>

                                            <?php endif; ?>
                                            <?php if(isset($item['vul_category'])): ?>
                                                <a href="<?php echo e(url('forward', ['_id' => $item['_id']])); ?>" type="button" class="btn btn-success btn-xs ">Remediated</a>
                                            <?php else: ?>
                                                <a type="button" class="btn btn-success btn-xs " disabled>Remediated</a>
                                            <?php endif; ?>
                                        </div>
                                    </td>


                                </tr>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $result1; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(!empty($item['_id'])): ?>
                                <tr>

                                    <td><input value="<?php echo e($item['_id']); ?>" type="checkbox"></td>
                                    <td><?php if(!empty($item['name'])): ?> <?php echo e($item['name']); ?> <?php endif; ?></td>
                                    <td><span class="label <?php echo e(($item['rem_officer_rem_status']==Config::get('enums.mdb_stream_status.Exception')) ? "bg-orange" : "empty"); ?>"><?php echo e($enums1[$item['rem_officer_rem_status']]); ?></span></td>
                                    <td><?php if(!empty($item['risk'])): ?> <span class="label <?php echo e(($item['risk']==Config::get('enums.severity_status.Informational')) ? "label-info" :
                              (((($item['risk']==Config::get('enums.severity_status.Low'))  ? "label-success" :
                              (((($item['risk']==Config::get('enums.severity_status.Medium'))  ? "bg-orange" :
                              (((($item['risk']==Config::get('enums.severity_status.High'))  ? "bg-red" :
                              (((($item['risk']==Config::get('enums.severity_status.Critical'))  ? "label-dark" :
                              "empty"))))))))))))); ?>"><?php echo e($enums[$item['risk']]); ?></span> <?php endif; ?></td>
                                    <?php if($item['module_id'] == 3): ?>
                                        <td><?php if(!empty($item['host'])): ?> <?php echo e($item['host']); ?> <?php endif; ?></td>
                                    <?php else: ?>
                                        <td style="width:20%;"> <?php if(isset($item['url_scheme'])): ?><?php echo e($item['url_scheme']); ?><?php endif; ?><?php echo e(substr(urldecode($item['host']), 0, 30)); ?></td>
                                    <?php endif; ?>
                                    <td style="width:5%;"> <?php if(!empty($item['port'])): ?> <?php echo e($item['port']); ?> <?php endif; ?></td>
                                    <td style="width:8%;"><?php echo e(isset($item['days_open']) ? $item['days_open'] : ''); ?></td>
                                    <td style="width:10%;"><?php echo e(date('d/m/Y', substr($item['hod_signoff_date'],0,10))); ?></td>
                                    <td style="width:10%;"><?php echo e((isset($item['target_fix_date']) ? $item['target_fix_date'] : "")); ?></td>
                                    <td style="width:8%;"><?php if(isset($item['rem_officer_revalidation_date'])): ?><?php echo e(date('d/m/Y', substr($item['rem_officer_revalidation_date'],0,10))); ?> <?php else: ?> <?php echo e($item['rem_officer_revalidation_date'] = ""); ?> <?php endif; ?></td>
                                    <td><?php if(!empty($item['vul_category'])): ?> <?php echo e($vul_categories[$item['vul_category']]); ?> <?php endif; ?></td>
                                    <td style="width:15%; text-align:center;">
                                        <div class="row">
                                            <?php if($item['module_id'] == 1): ?>
                                                <a href="<?php echo e(route('Remofficer.show', ['_id' => $item['_id']])); ?>" target="_blank" type="button" class="btn btn-info btn-xs ">Details</a>

                                            <?php elseif($item['module_id'] == 2): ?>
                                                <a href="<?php echo e(url('remshowxml', ['_id' => $item['_id']])); ?>" target="_blank" type="button" class="btn btn-info btn-xs ">Details</a>

                                            <?php elseif($item['module_id'] == 3): ?>
                                                <a href="<?php echo e(url('remshownexpose', ['_id' => $item['_id']])); ?>" target="_blank" type="button" class="btn btn-info btn-xs ">Details</a>


                                            <?php elseif($item['module_id'] == 4): ?>
                                                <a href="<?php echo e(url('showappscan', ['_id' => $item['_id']])); ?>" target="_blank" type="button" class="btn btn-info btn-xs ">Details</a>

                                            <?php elseif($item['module_id'] == 5): ?>
                                                <a href="<?php echo e(url('showburp', ['_id' => $item['_id']])); ?>" target="_blank" type="button" class="btn btn-info btn-xs ">Details</a>


                                            <?php endif; ?>
                                        </div>
                                    </td>


                                </tr>
                            <?php endif; ?>
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

        
        
        
        
        
        
        
        
        
        
        

        function forwardAll()
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
                url: "<?php echo e(route('forwardAll')); ?>",
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

        document.getElementById("upload_new_file").onchange = function() {
            document.getElementById("upload_new_form").submit();
        };


    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
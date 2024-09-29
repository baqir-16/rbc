<?php $__env->startSection('title', 'Analyst'); ?>
<?php $__env->startSection('content'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('qa_view')): ?>
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
            <h3 class="box-title"><strong>QA Reverify - </strong> <?php echo e(str_plural('Total Number of Record', $result->count())); ?>  <?php echo e($result->total()); ?> </h3>
            <a href="<?php echo e(route('QA.index')); ?>" class="btn  btn-default btn-sm btn-flat pull-right">Back</a>
        </div>
        <div class="box-body  no-padding">
            <div class="mailbox-controls">
                <button type="button" class="btn btn-info btn-sm btn-flat checkbox-toggle"><i class="fa fa-square-o"></i> Select All</button>
                <button type="button" onclick="reverifyAll()" class="btn  btn-success btn-sm btn-flat">Reverify Selected</button>
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
                        <th>Analyst Revalidated?</th>
                        <th>QA Reverified?</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $result; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <?php if($item['module_id'] == 1): ?>

                            <td><input value="<?php echo e($item['_id']); ?>" type="checkbox"></td>
                            <td> <?php echo e($item['name']); ?></td>
                            <td><span class="label <?php echo e(($item['risk']==Config::get('enums.severity_status.Informational')) ? "label-info" :
                                                  (((($item['risk']==Config::get('enums.severity_status.Low'))  ? "label-success" :
                                                  (((($item['risk']==Config::get('enums.severity_status.Medium'))  ? "bg-orange" :
                                                  (((($item['risk']==Config::get('enums.severity_status.High'))  ? "bg-red" :
                                                  (((($item['risk']==Config::get('enums.severity_status.Critical'))  ? "label-dark" :
                                                   "empty"))))))))))))); ?>"><?php echo e($enums[$item['risk']]); ?></span></td>
                            <td> <?php echo e($item['host']); ?></td>
                                <td>
                                    <?php if($item['is_validated']==1): ?>
                                        <span class="label label-success">YES</span>
                                    <?php else: ?>
                                        <span class="label label-danger">No</span>
                                    <?php endif; ?>
                                </td>
                            <td id="item1_<?php echo e($key); ?>">
                                <?php if($item['reverify']==1): ?>
                                    <span class="label label-info">YES</span>
                                <?php else: ?>
                                    <span class="label label-danger">No</span>
                                <?php endif; ?>
                            </td>
                            <td><div class="btn-group">
                                    <a href="<?php echo e(route('Review.show', ['_id' => $item['_id']])); ?>" type="button" class="btn btn-info btn-xs ">Details</a>
                                    <button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                        <?php if($item['reverify']==0): ?>
                                            <li id="reverify_<?php echo e($key); ?>" type="button " onclick="reverify('<?php echo e($item['_id']); ?>', '<?php echo e($key); ?>')"><a href="#">reverify</a></li>
                                        <?php else: ?>
                                            <li id="reverify_<?php echo e($key); ?>" type="button " onclick="unreverify('<?php echo e($item['_id']); ?>', '<?php echo e($key); ?>')"><a href="#">unreverify</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </td>

                            <?php elseif($item['module_id'] == 2): ?>

                                <td><input value="<?php echo e($item['_id']); ?>" type="checkbox"></td>
                                <td> <?php echo e($item['name']); ?></td>
                                <td><span class="label <?php echo e(($item['risk']==Config::get('enums.severity_status.Informational')) ? "label-info" :
              (((($item['risk']==Config::get('enums.severity_status.Low'))  ? "label-success" :
              (((($item['risk']==Config::get('enums.severity_status.Medium'))  ? "bg-orange" :
              (((($item['risk']==Config::get('enums.severity_status.High'))  ? "bg-red" :
              (((($item['risk']==Config::get('enums.severity_status.Critical'))  ? "label-dark" :
              "empty"))))))))))))); ?>"><?php echo e($enums[$item['risk']]); ?></span></td>
                                <td> </td>
                                <td>
                                    <?php if($item['is_validated']==1): ?>
                                        <span class="label label-success">YES</span>
                                    <?php else: ?>
                                        <span class="label label-danger">No</span>
                                    <?php endif; ?>
                                </td>
                                <td id="item1_<?php echo e($key); ?>">
                                    <?php if($item['reverify']==1): ?>
                                        <span class="label label-info">YES</span>
                                    <?php else: ?>
                                        <span class="label label-danger">No</span>
                                    <?php endif; ?>
                                </td>
                                <td><div class="btn-group">
                                        <a href="<?php echo e(url('reviewxml', ['_id' => $item['_id']])); ?>" type="button" class="btn btn-info btn-xs ">Details</a>
                                        <button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            <span class="caret"></span>
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <?php if($item['reverify']==0): ?>
                                                <li id="reverify_<?php echo e($key); ?>" type="button " onclick="reverify('<?php echo e($item['_id']); ?>', '<?php echo e($key); ?>')"><a href="#">reverify</a></li>
                                            <?php else: ?>
                                                <li id="reverify_<?php echo e($key); ?>" type="button " onclick="unreverify('<?php echo e($item['_id']); ?>', '<?php echo e($key); ?>')"><a href="#">unreverify</a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </td>
                            <?php elseif($item['module_id'] == 3): ?>

                                <td><input value="<?php echo e($item['_id']); ?>" type="checkbox"></td>
                                <td> <?php echo e($item['name']); ?></td>
                                <td><span class="label <?php echo e(($item['risk']==Config::get('enums.severity_status.Informational')) ? "label-info" :
              (((($item['risk']==Config::get('enums.severity_status.Low'))  ? "label-success" :
              (((($item['risk']==Config::get('enums.severity_status.Medium'))  ? "bg-orange" :
              (((($item['risk']==Config::get('enums.severity_status.High'))  ? "bg-red" :
              (((($item['risk']==Config::get('enums.severity_status.Critical'))  ? "label-dark" :
              "empty"))))))))))))); ?>"><?php echo e($enums[$item['risk']]); ?></span></td>
                                <td> </td>
                                <td>
                                    <?php if($item['is_validated']==1): ?>
                                        <span class="label label-success">YES</span>
                                    <?php else: ?>
                                        <span class="label label-danger">No</span>
                                    <?php endif; ?>
                                </td>
                                <td id="item1_<?php echo e($key); ?>">
                                    <?php if($item['reverify']==1): ?>
                                        <span class="label label-info">YES</span>
                                    <?php else: ?>
                                        <span class="label label-danger">No</span>
                                    <?php endif; ?>
                                </td>
                                <td><div class="btn-group">
                                        <a href="<?php echo e(url('reviewnexpose', ['_id' => $item['_id']])); ?>" type="button" class="btn btn-info btn-xs ">Details</a>
                                        <button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            <span class="caret"></span>
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <?php if($item['reverify']==0): ?>
                                                <li id="reverify_<?php echo e($key); ?>" type="button " onclick="reverify('<?php echo e($item['_id']); ?>', '<?php echo e($key); ?>')"><a href="#">reverify</a></li>
                                            <?php else: ?>
                                                <li id="reverify_<?php echo e($key); ?>" type="button " onclick="unreverify('<?php echo e($item['_id']); ?>', '<?php echo e($key); ?>')"><a href="#">unreverify</a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </td>
                            <?php elseif($item['module_id'] == 4): ?>

                                <td><input value="<?php echo e($item['_id']); ?>" type="checkbox"></td>
                                <td> <?php echo e($item['name']); ?></td>
                                <td><span class="label <?php echo e(($item['risk']==Config::get('enums.severity_status.Informational')) ? "label-info" :
              (((($item['risk']==Config::get('enums.severity_status.Low'))  ? "label-success" :
              (((($item['risk']==Config::get('enums.severity_status.Medium'))  ? "bg-orange" :
              (((($item['risk']==Config::get('enums.severity_status.High'))  ? "bg-red" :
              (((($item['risk']==Config::get('enums.severity_status.Critical'))  ? "label-dark" :
              "empty"))))))))))))); ?>"><?php echo e($enums[$item['risk']]); ?></span></td>
                                <td> </td>
                                <td>
                                    <?php if($item['is_validated']==1): ?>
                                        <span class="label label-success">YES</span>
                                    <?php else: ?>
                                        <span class="label label-danger">No</span>
                                    <?php endif; ?>
                                </td>
                                <td id="item1_<?php echo e($key); ?>">
                                    <?php if($item['reverify']==1): ?>
                                        <span class="label label-info">YES</span>
                                    <?php else: ?>
                                        <span class="label label-danger">No</span>
                                    <?php endif; ?>
                                </td>
                                <td><div class="btn-group">
                                        <a href="<?php echo e(url('reviewappscan', ['_id' => $item['_id']])); ?>" type="button" class="btn btn-info btn-xs ">Details</a>
                                        <button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            <span class="caret"></span>
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <?php if($item['reverify']==0): ?>
                                                <li id="reverify_<?php echo e($key); ?>" type="button " onclick="reverify('<?php echo e($item['_id']); ?>', '<?php echo e($key); ?>')"><a href="#">reverify</a></li>
                                            <?php else: ?>
                                                <li id="reverify_<?php echo e($key); ?>" type="button " onclick="unreverify('<?php echo e($item['_id']); ?>', '<?php echo e($key); ?>')"><a href="#">unreverify</a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </td>
                            <?php elseif($item['module_id'] == 5): ?>

                                <td><input value="<?php echo e($item['_id']); ?>" type="checkbox"></td>
                                <td> <?php echo e($item['name']); ?></td>
                                <td><span class="label <?php echo e(($item['risk']==Config::get('enums.severity_status.Informational')) ? "label-info" :
              (((($item['risk']==Config::get('enums.severity_status.Low'))  ? "label-success" :
              (((($item['risk']==Config::get('enums.severity_status.Medium'))  ? "bg-orange" :
              (((($item['risk']==Config::get('enums.severity_status.High'))  ? "bg-red" :
              (((($item['risk']==Config::get('enums.severity_status.Critical'))  ? "label-dark" :
              "empty"))))))))))))); ?>"><?php echo e($enums[$item['risk']]); ?></span></td>
                                <td> </td>
                                <td>
                                    <?php if($item['is_validated']==1): ?>
                                        <span class="label label-success">YES</span>
                                    <?php else: ?>
                                        <span class="label label-danger">No</span>
                                    <?php endif; ?>
                                </td>
                                <td id="item1_<?php echo e($key); ?>">
                                    <?php if($item['reverify']==1): ?>
                                        <span class="label label-info">YES</span>
                                    <?php else: ?>
                                        <span class="label label-danger">No</span>
                                    <?php endif; ?>
                                </td>
                                <td><div class="btn-group">
                                        <a href="<?php echo e(url('reviewburp', ['_id' => $item['_id']])); ?>" type="button" class="btn btn-info btn-xs ">Details</a>
                                        <button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            <span class="caret"></span>
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <?php if($item['reverify']==0): ?>
                                                <li id="reverify_<?php echo e($key); ?>" type="button " onclick="reverify('<?php echo e($item['_id']); ?>', '<?php echo e($key); ?>')"><a href="#">reverify</a></li>
                                            <?php else: ?>
                                                <li id="reverify_<?php echo e($key); ?>" type="button " onclick="unreverify('<?php echo e($item['_id']); ?>', '<?php echo e($key); ?>')"><a href="#">unreverify</a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </td>

                                <?php endif; ?>
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

        function reverify(id,key) {
            var token = "<?php echo e(csrf_token()); ?>";
            $.ajax({
                type: 'GET',
                url: "<?php echo e(route('reverify')); ?>",
                data: {id: id, _token: token},
                success: function (data) {
                    $('#item1_'+key).html('<span class="label label-info">YES</span>');
                    $('#reverify_'+key).replaceWith('<li type="button" id="reverify_'+key+'" onclick="unreverify(\''+id+'\',\''+key+'\')"><a href="#">Unreverify</a></li>');
                }
            });
        }

        function unreverify(id,key) {
            var token = "<?php echo e(csrf_token()); ?>";
            $.ajax({
                type: 'GET',
                url: "<?php echo e(route('unreverify')); ?>",
                data: {id: id, _token: token},
                success: function (data) {
                    $('#item1_'+key).html('<span class="label label-danger">NO</span>');
                    $('#reverify_'+key).replaceWith('<li type="button" id="reverify_'+key+'" onclick="reverify(\''+id+'\',\''+key+'\')"><a href="#">Reverify</a></li>');
                }
            });
        }

        function revalidate(id) {
            var token = "<?php echo e(csrf_token()); ?>";
            $.ajax({
                type: 'GET',
                url: "<?php echo e(route('revalidate')); ?>",
                data: {id: id, _token: token},
                success: function (data) {
                    location.reload();
                }
            });
        }

        function unrevalidate(id) {
            var token = "<?php echo e(csrf_token()); ?>";
            $.ajax({
                type: 'GET',
                url: "<?php echo e(route('unrevalidate')); ?>",
                data: {id: id, _token: token},
                success: function (data) {
                    location.reload();
                }
            });
        }

        function reverifyAll()
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
                url: "<?php echo e(route('reverifyAll')); ?>",
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
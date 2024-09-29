<?php $__env->startSection('title', 'PDF Files'); ?>
<?php $__env->startSection('content'); ?>
    <div class="box box-primary">

        <div class="box-header with-border">
            <h3 class="box-title"> Generated PDF Files </h3>
        </div>
        <div class="box-body mailbox-messages">
            
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Ticket Ref. No</th>
                        <th>Ticket Title</th>
                        <th>Module</th>
                        <th>Sign Off Date</th>
                        <th class="text-center">Download</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $streams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $stream): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($stream->tickets[0]->ref); ?></td>
                            <td><?php echo e($stream->tickets[0]->title); ?></td>
                            <td><?php echo e($stream->modules[0]->name); ?></td>
                            <td><?php echo e($stream->hod_signoff_date); ?></td>
                            <td class="text-center">
                                <a href="<?php echo e(Config::get('remote.connections.MDB_SERVER.host_http')); ?>/pdf_reports/<?php echo e($stream->report_filename); ?>" download="<?php echo e($stream->report_filename); ?>" class="btn btn-info btn-xs">Download PDF</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <div class="text-center">
                    <?php echo $streams->links();; ?>

                </div>
        </div>
        
            
        
       
    </div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('custom-scripts'); ?>
    <script type="text/javascript">
        $('[data-toggle=example1]').confirmation({
            rootSelector: '[data-toggle=example1]',
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

        <?php if(isset($display_report)): ?>
            window.open('<?php echo e(Config::get('remote.connections.MDB_SERVER.host_http')); ?>/pdf_reports/<?php echo e($filename); ?>', '_blank');
        <?php endif; ?>
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
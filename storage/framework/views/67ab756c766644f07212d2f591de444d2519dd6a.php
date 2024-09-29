<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo $__env->yieldContent('title'); ?> <?php echo e(config('Axiata')); ?></title>
    <link rel="shortcut icon" href="<?php echo e(asset("/img/favicon-16x16.png")); ?>" type="image/x-icon">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- Styles -->
<!-- <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet"> -->

    <!-- for edit search -->
    <link rel="stylesheet" href="<?php echo e(asset("/bower_components/bootstrap/dist/css/bootstrap.min.css")); ?>">
    <link rel="stylesheet" href="<?php echo e(asset("/bower_components/font-awesome/css/font-awesome.min.css")); ?>">
    <link rel="stylesheet" href="<?php echo e(asset("/bower_components/Ionicons/css/ionicons.min.css")); ?>">
    <link rel="stylesheet" href="<?php echo e(asset("/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css")); ?>">
    <link href="<?php echo e(asset("/bower_components/admin-lte/dist/css/skins/_all-skins.min.css")); ?>">
    <link rel="stylesheet" href="<?php echo e(asset("bower_components/admin-lte/plugins/iCheck/flat/blue.css")); ?>">
    <link rel="stylesheet" href="<?php echo e(asset("/bower_components/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css")); ?>">
    <link href="<?php echo e(asset("/bower_components/admin-lte/dist/css/skins/skin-blue.min.css")); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset("/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css")); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset("/bower_components/bootstrap-daterangepicker/daterangepicker.css")); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset("/bower_components/bootstrap/dist/css/bootstrap.min.css")); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset("/bower_components/font-awesome/css/font-awesome.min.css")); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset("/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css")); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset("/bower_components/select2/dist/css/select2.min.css")); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset("/bower_components/admin-lte/dist/css/AdminLTE.min.css")); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset("/bower_components/admin-lte/dist/css/skins/skin-blue.min.css")); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset("/bower_components/morris.js/morris.css")); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset("/bower_components/jvectormap/jquery-jvectormap.css")); ?>" rel="stylesheet" type="text/css" />

    <link href="<?php echo e(asset("/css/font.css")); ?>" rel="stylesheet" type="text/css" />
    <link rel='stylesheet' id='theme-style-css' href='<?php echo e(url("css/style.css")); ?>' type='text/css' media='all' />
</head>
<body class="skin-blue sidebar-mini sidebar-collapse">
<div class="wrapper">

    <!-- Header -->
<?php echo $__env->make('layouts.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<!-- Sidebar -->
<?php echo $__env->make('layouts.sidebar', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">


    <!-- <section class="content-header">
        <h1>
          <?php echo e(isset($page_title) ? $page_title : "Page Title"); ?>

            <small><?php echo e(isset($page_description) ? $page_description : null); ?></small>
        </h1>
        <ol class="breadcrumb">
          <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
          <li class="active">Here</li>
        </ol>
        </section> -->

        <section class="content">
            <div id="flash-msg">
                <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>

            <?php echo $__env->yieldContent('content'); ?>
        </section>

    </div>

    <?php echo $__env->make('layouts.footer', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

</div>

<script src="<?php echo e(asset ("/bower_components/jquery/dist/jquery.min.js")); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset ("/bower_components/jquery-ui/jquery-ui.min.js")); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset ("/bower_components/jquery/dist/jquery.js")); ?>"></script>
<script src="<?php echo e(asset ("/bower_components/moment/min/moment.min.js")); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset ("/bower_components/bootstrap/dist/js/bootstrap.min.js")); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset("/bower_components/chart.js/Chart.js")); ?>"></script>
<script src="<?php echo e(asset ("/bower_components/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js")); ?>"></script>
<script src="<?php echo e(asset ("/bower_components/select2/dist/js/select2.full.min.js")); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset ("/bower_components/raphael/raphael.min.js")); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset ("/bower_components/morris.js/morris.min.js")); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset ("/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js")); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset ("/bower_components/admin-lte/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js")); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset ("/bower_components/admin-lte/plugins/jvectormap/jquery-jvectormap-world-mill-en.js")); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset ("/bower_components/jquery-knob/dist/jquery.knob.min.js")); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset ("/bower_components/bootstrap-daterangepicker/daterangepicker.js")); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset ("/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js")); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset ("/bower_components/admin-lte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js")); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset("/bower_components/admin-lte/plugins/timepicker/bootstrap-timepicker.min.js")); ?>"></script>
<script src="<?php echo e(asset("/bower_components/datatables.net/js/jquery.dataTables.min.js")); ?>"></script>
<script src="<?php echo e(asset("/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js")); ?>"></script>
<script src="<?php echo e(asset("/bower_components/bootstrap/dist/js/bootstrap-confirmation.js")); ?>"></script>
<script src="<?php echo e(asset ("\js\dropzone.js")); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset ("/bower_components/jquery-slimscroll/jquery.slimscroll.min.js")); ?>"></script>
<script src="<?php echo e(asset ("/bower_components/fastclick/lib/fastclick.js")); ?>"></script>

<script src="<?php echo e(asset("/bower_components/admin-lte/dist/js/pages/dashboard2.js")); ?>"></script>
<script src="<?php echo e(asset("/bower_components/admin-lte/dist/js/demo.js")); ?>"></script>
<script src="<?php echo e(asset ("/bower_components/admin-lte/dist/js/adminlte.min.js")); ?>" type="text/javascript"></script>
<script src="<?php echo e(asset("/bower_components/admin-lte/plugins/iCheck/icheck.min.js")); ?>"></script>


<script type="text/javascript">
    $(function () {
        $('.select2').select2()
    } )
</script>

<script>
    $(function () {
        // flash auto hide
        $('#flash-msg .alert').not('.alert-danger, .alert-important').delay(3000).slideUp(500);
    })
</script>


<script>
    $(document).ready(function() {
        $.ajax({
            type: 'GET',
            url: 'updater.check',
            async: false,
            success: function(response) {
                if(response != ''){
                    $('#update_notification').append('<strong>Update Available <span class="badge badge-pill badge-info">v. '+response+'</span></strong><a role="button" href="updater.update" class="btn btn-sm btn-warning pull-right">Update Now</a>');
                    $('#update_notification').show();
                }else{
                    $('#update_notification').append('<strong>No Update Available</strong>');
                    $('#update_notification').show();
                }
            }
        });

        $.ajax({
            type: 'GET',
            url: 'updater.currentVersion',
            async: false,
            success: function(response) {
                if(response.length < 10){
                    $("#system-version").html("<b>Version</b> <span class=\"system-version\">"+response+"</span>");
                    // $(".system-version").text(response);
                }
            }
        });
    });
</script>

<?php echo $__env->yieldContent("custom-scripts"); ?>
<?php echo $__env->yieldContent("custom-scripts-2"); ?>

</body>
</html>

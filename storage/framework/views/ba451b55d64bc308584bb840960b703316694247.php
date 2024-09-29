<!DOCTYPE html>
<html lang="<?php echo e(config('app.locale')); ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('title'); ?> <?php echo e(config('', 'Axiata')); ?></title>
    <link href="<?php echo e(asset("/bower_components/bootstrap/dist/css/bootstrap.min.css")); ?>" rel="stylesheet" type="text/css" />

    <link href="<?php echo e(asset("/bower_components/admin-lte/dist/css/AdminLTE.min.css")); ?>" rel="stylesheet" type="text/css" />

    <link href="<?php echo e(asset("/bower_components/admin-lte/dist/css/skins/skin-blue.min.css")); ?>" rel="stylesheet" type="text/css" />


    <style>
        .result-set { margin-top: 1em }
    </style>
    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>;
    </script>
</head>
<body>
    <div id="app">


        <div class="container">
            <div id="flash-msg">
                <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>
    <script src="<?php echo e(asset ("/bower_components/jquery/dist/jquery.min.js")); ?>"></script>
    <script src="<?php echo e(asset ("/bower_components/bootstrap/dist/js/bootstrap.min.js")); ?>" type="text/javascript"></script>
    <script src="<?php echo e(asset ("/bower_components/admin-lte/dist/js/adminlte.min.js")); ?>" type="text/javascript"></script>
    <!-- Scripts -->
    <script src="<?php echo e(asset('js/app.js')); ?>"></script>

    <?php echo $__env->yieldPushContent('scripts'); ?>

    <script>
        $(function () {
            // flash auto hide
            $('#flash-msg .alert').not('.alert-danger, .alert-important').delay(6000).slideUp(500);
        })
    </script>
</body>
</html>

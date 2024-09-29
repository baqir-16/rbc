<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('content'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ciso_view')): ?>
    <section class="content">
        
        <div class="row">
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-maroon">
                    <div class="inner">
                        <h3><?php echo e($critical_risk); ?></h3>
                        <p>Open Critical Issues</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="<?php echo e(url('showdetailsbyciso', [1, Auth::user()->opco_id])); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3><?php echo e($high_risk); ?><sup style="font-size: 20px"></sup></h3>
                        <p>Open High Issues</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="<?php echo e(url('showdetailsbyciso', [2, Auth::user()->opco_id])); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3><?php echo e($med_risk); ?></h3>
                        <p>Open Medium Issues</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="<?php echo e(url('showdetailsbyciso', [3, Auth::user()->opco_id])); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3><?php echo e($low_risk); ?></h3>
                        <p>Open Low Issues</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="<?php echo e(url('showdetailsbyciso', [4, Auth::user()->opco_id])); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>

    

    
    
    
    
    
    
    
    
    
    
    
    

    
    
    
    
    
    
    

        <div class="row">
            <section class="col-lg-12 connectedSortable ui-sortable">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Open External Scan Findings per OpCo</h3>
                    </div>
                    <div class="box-body no-padding">
                        <table class="table  table-striped table-condensed">
                            <tbody>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Opco</th>
                                <th>Critical</th>
                                <th>High</th>
                                <th>Medium</th>
                                <th>Low</th>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td><?php echo e($opco_array['opco']); ?></td>
                                <td><?php echo e($opco_risk_array[0]); ?></td>
                                <td><?php echo e($opco_risk_array[1]); ?></td>
                                <td><?php echo e($opco_risk_array[2]); ?></td>
                                <td><?php echo e($opco_risk_array[3]); ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#hosts" data-toggle="tab" aria-expanded="false">Affected Hosts per Category</a></li>
                        <li class=""><a href="#titles" data-toggle="tab" aria-expanded="true">Affected Hosts per Vulnerability</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane  active" id="hosts">
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table class="table   table-striped no-margin">
                                        <thead>
                                        <tr>
                                            <th>Category Name</th>
                                            <th>Affected Hosts</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $__currentLoopData = $cat_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($cat['name']); ?></td>
                                                <td><?php echo e($hosts_per_cat_array[$key]); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="titles">
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th>Vulnerability Name</th>
                                            <th width="20%">Affected Hosts</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $__currentLoopData = $unique_vuln_names; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($name['name']); ?></td>
                                                <td width="20%"><?php echo e($num_of_hosts_by_vuln_name[$key]); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                    
                    
                        
                        
                        
                        
                    
                    
                        
                            
                                
                                    
                                        
                                        
                                            
                                            
                                            
                                        
                                        
                                        
                                        
                                            
                                                
                                                
                                                
                                                    
                                                
                                            
                                        

                                        
                                    
                                
                            
                        
                        
                            
                                
                                    
                                        
                                        
                                            
                                            
                                            
                                        
                                        
                                        
                                        
                                            
                                                
                                                
                                                
                                                    
                                                
                                            
                                        
                                        
                                    
                                
                                
                            
                        

                        
                            
                                
                                    
                                        
                                        
                                            
                                            
                                            
                                        
                                        
                                        
                                        
                                            
                                                
                                                
                                                
                                                    
                                                
                                            
                                        
                                        
                                    
                                
                            
                        
                        
                            
                                
                                    
                                        
                                        
                                            
                                            
                                            
                                        
                                        
                                        
                                        
                                            
                                                
                                                
                                                
                                                    
                                                
                                            
                                        
                                        
                                    
                                
                            
                        
                    
                
            </section>
            <!-- /.Left col -->
            <!-- right col (We are only adding the ID to make the widgets sortable)-->

        </div>
        <div class="row">
            <section class="col-lg-12 connectedSortable ui-sortable">
                <div class="box box-solid">
                    <div class="box-header ui-sortable-handle" style="cursor: move;">
                        <i class="fa fa-th"></i>
                        <h3 class="box-title">Total Open & Closed Findings</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            
                        </div>

                        <div class="box-body"><div class="col-md-4 col-md-offset-4">
                            <canvas id="pieChart" style="height:250px"></canvas>
                        </div></div>
                    </div>
                    <div class="box-footer no-border">
                        <div class="row">
                            <div class="col-xs-4 col-md-offset-2 text-center" style="border-right: 1px solid #f4f4f4">
                                <div style="display:inline;width:60px;height:60px;"><canvas width="75" height="75" style="width: 60px; height: 60px;"></canvas><input type="text" class="knob" data-readonly="true" value="<?php echo e($open); ?>" data-width="60" data-height="60" data-fgcolor="#39CCCC" readonly="readonly" style="width: 34px; height: 20px; position: absolute; vertical-align: middle; margin-top: 20px; margin-left: -47px; border: 0px; background: none; font-style: normal; font-variant: normal; font-weight: bold; font-stretch: normal; font-size: 12px; line-height: normal; font-family: Arial; text-align: center; color: rgb(57, 204, 204); padding: 0px; -webkit-appearance: none;"></div>
                                <div class="knob-label">Open Findings</div>
                            </div>
                            <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                <div style="display:inline;width:60px;height:60px;"><canvas width="75" height="75" style="width: 60px; height: 60px;"></canvas><input type="text" class="knob" data-readonly="true" value="<?php echo e($close); ?>" data-width="60" data-height="60" data-fgcolor="#39CCCC" readonly="readonly" style="width: 34px; height: 20px; position: absolute; vertical-align: middle; margin-top: 20px; margin-left: -47px; border: 0px; background: none; font-style: normal; font-variant: normal; font-weight: bold; font-stretch: normal; font-size: 12px; line-height: normal; font-family: Arial; text-align: center; color: rgb(57, 204, 204); padding: 0px; -webkit-appearance: none;"></div>
                                <div class="knob-label">Closed Findings</div>
                            </div>

                        </div>
                    </div>
                </div>

                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
            </section>
        </div>
    </section>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('custom-scripts'); ?>
    <script>
        $(function () {
            /* ChartJS
             * -------
             * Here we will create a few charts using ChartJS
             */

            //--------------
            //- AREA CHART -
            //--------------
            //
            // // Get context with jQuery - using jQuery's .get() method.
            // var areaChartCanvas = $('#areaChart').get(0).getContext('2d')
            // // This will get the first returned node in the jQuery collection.
            // var areaChart       = new Chart(areaChartCanvas)
            //
            // var areaChartData = {
            //     labels  : ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            //     datasets: [
            //         {
            //             label               : 'High',
            //             fillColor           : 'rgba(210, 214, 222, 1)',
            //             strokeColor         : 'rgba(210, 214, 222, 1)',
            //             pointColor          : 'rgba(210, 214, 222, 1)',
            //             pointStrokeColor    : '#c1c7d1',
            //             pointHighlightFill  : '#fff',
            //             pointHighlightStroke: 'rgba(220,220,220,1)',
            //             data                : [65, 59, 80, 81, 56, 55, 40]
            //         },
            //         {
            //             label               : 'Medium',
            //             fillColor           : 'rgba(60,141,188,0.9)',
            //             strokeColor         : 'rgba(60,141,188,0.8)',
            //             pointColor          : '#3b8bba',
            //             pointStrokeColor    : 'rgba(60,141,188,1)',
            //             pointHighlightFill  : '#fff',
            //             pointHighlightStroke: 'rgba(60,141,188,1)',
            //             data                : [28, 48, 40, 19, 86, 27, 90]
            //         },
            //         {
            //             label               : 'Low',
            //             fillColor           : 'rgba(60,141,188,0.9)',
            //             strokeColor         : 'rgba(60,141,188,0.8)',
            //             pointColor          : '#3b8bba',
            //             pointStrokeColor    : 'rgba(60,141,188,1)',
            //             pointHighlightFill  : '#fff',
            //             pointHighlightStroke: 'rgba(60,141,188,1)',
            //             data                : [28, 48, 40, 19, 86, 27, 90]
            //         }
            //
            //     ]
            // }
            //
            // var areaChartOptions = {
            //     //Boolean - If we should show the scale at all
            //     showScale               : true,
            //     //Boolean - Whether grid lines are shown across the chart
            //     scaleShowGridLines      : false,
            //     //String - Colour of the grid lines
            //     scaleGridLineColor      : 'rgba(0,0,0,.05)',
            //     //Number - Width of the grid lines
            //     scaleGridLineWidth      : 1,
            //     //Boolean - Whether to show horizontal lines (except X axis)
            //     scaleShowHorizontalLines: true,
            //     //Boolean - Whether to show vertical lines (except Y axis)
            //     scaleShowVerticalLines  : true,
            //     //Boolean - Whether the line is curved between points
            //     bezierCurve             : true,
            //     //Number - Tension of the bezier curve between points
            //     bezierCurveTension      : 0.3,
            //     //Boolean - Whether to show a dot for each point
            //     pointDot                : false,
            //     //Number - Radius of each point dot in pixels
            //     pointDotRadius          : 4,
            //     //Number - Pixel width of point dot stroke
            //     pointDotStrokeWidth     : 1,
            //     //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
            //     pointHitDetectionRadius : 20,
            //     //Boolean - Whether to show a stroke for datasets
            //     datasetStroke           : true,
            //     //Number - Pixel width of dataset stroke
            //     datasetStrokeWidth      : 2,
            //     //Boolean - Whether to fill the dataset with a color
            //     datasetFill             : true,
            //     //String - A legend template
            //     //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
            //     maintainAspectRatio     : true,
            //     //Boolean - whether to make the chart responsive to window resizing
            //     responsive              : true
            // }
            //
            // //Create the line chart
            // areaChart.Line(areaChartData, areaChartOptions)


            //-------------
            //- PIE CHART -
            //-------------
            // Get context with jQuery - using jQuery's .get() method.
            var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
            var pieChart       = new Chart(pieChartCanvas)
            var PieData        = [
                {
                    value    :<?php echo e($open); ?>,
                    color    : '#f56954',
                    highlight: '#f55046',
                    label    : 'Open'
                },
                {
                    value    :<?php echo e($close); ?>,
                    color    : '#38a682',
                    highlight: '#00a65a',
                    label    : 'Close'
                }
            ]
            var pieOptions     = {
                //Boolean - Whether we should show a stroke on each segment
                segmentShowStroke    : true,
                //String - The colour of each segment stroke
                segmentStrokeColor   : '#fff',
                //Number - The width of each segment stroke
                segmentStrokeWidth   : 2,
                //Number - The percentage of the chart that we cut out of the middle
                percentageInnerCutout: 50, // This is 0 for Pie charts
                //Number - Amount of animation steps
                animationSteps       : 100,
                //String - Animation easing effect
                animationEasing      : 'easeOutBounce',
                //Boolean - Whether we animate the rotation of the Doughnut
                animateRotate        : true,
                //Boolean - Whether we animate scaling the Doughnut from the centre
                animateScale         : false,
                //Boolean - whether to make the chart responsive to window resizing
                responsive           : true,
                // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
                maintainAspectRatio  : true,
                //String - A legend template
            }
            //Create pie or douhnut chart
            // You can switch between pie and douhnut using the method below.
            pieChart.Doughnut(PieData, pieOptions)

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

    </script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
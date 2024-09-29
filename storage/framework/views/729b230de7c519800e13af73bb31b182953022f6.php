<?php $__env->startSection('title', 'Report'); ?>
<?php echo $__env->yieldContent('css'); ?>
    <style>
        .left{
            width:50%;
            float:left;
        }
        .right{
            width:50%;
            float:right;
        }
    </style>
<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="left">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><strong>Remediation Status for Open & Closed Findings</strong></h3>
                </div>
                <div class="box-body no-padding">
                    <div class="box-body mailbox-messages" style="margin-bottom: 30%">
                        <table id="" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Total Findings</th>
                                <th>Total Open</th>
                                <th>Total Closed</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><?php echo e($total_all); ?></td>
                                <td><?php echo e($total_open); ?></td>
                                <td><?php echo e($total_closed); ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="right">
            <div class="col-sm-12">
            <div class="card card-success">
                <div class="card-header">
                    
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="barChart_total" style="height:300px"></canvas>
                    </div>
                </div>
            </div>
        </div></div>
    </div>

    
        
            
        
        
            
                
                    
                        
                            
                            
                            
                        
                    
                    
                        
                            
                            
                            
                        
                    
                
            
        
    

    
        
            
        
        
            
                
            
        
    

    <div class="container-fluid">
        <div class="left">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><strong>Number of Open Vulnerability Pending Remediation by Opco</strong></h3>
                </div>
                <div class="box-body  no-padding">
                    <div class="box-body mailbox-messages">
                        <table id="" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>OpCo</th>
                                <th style="background-color: red">Critical</th>
                                <th style="background-color: pink">High</th>
                                <th style="background-color: orange">Medium</th>
                                <th style="background-color: lightblue">Low</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $opco_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $opco): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($opco->opco); ?></td>
                                    <td style="background-color: red"><?php echo e($open_crit[$key]); ?></td>
                                    <td style="background-color: pink"><?php echo e($open_high[$key]); ?></td>
                                    <td style="background-color: orange"><?php echo e($open_med[$key]); ?></td>
                                    <td style="background-color: lightblue"><?php echo e($open_low[$key]); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><b>Total</b></td>
                                <td style="background-color: red"><b><?php echo e($open_crit[count($open_crit)-1]); ?></b></td>
                                <td style="background-color: pink"><b><?php echo e($open_high[count($open_high)-1]); ?></b></td>
                                <td style="background-color: orange"><b><?php echo e($open_med[count($open_med)-1]); ?></b></td>
                                <td style="background-color: lightblue"><b><?php echo e($open_low[count($open_low)-1]); ?></b></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="right">
            <div class="card card-success">
                <div class="card-header">
                    
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="barChart_openbyopco" style="height:500px"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
        
            
        
        
            
                
                    
                    
                        
                        
                        
                        
                        
                    
                    
                    
                    
                        
                            
                            
                            
                            
                            
                        
                    
                        
                            
                            
                            
                            
                            
                        
                    
                
            
        
    

    
        
            
        
        
            
                
            
        
    

    <div class="container-fluid">
        <div class="left">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><strong>Remediation Status for Number of Closed by OpCo</strong></h3>
                </div>
                <div class="box-body  no-padding">
                    <div class="box-body mailbox-messages">
                        <table id="" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>OpCo</th>
                                <th style="background-color: red">Critical</th>
                                <th style="background-color: pink">High</th>
                                <th style="background-color: orange">Medium</th>
                                <th style="background-color: lightblue">Low</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $opco_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $opco): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($opco->opco); ?></td>
                                    <td style="background-color: red"><?php echo e($closed_crit[$key]); ?></td>
                                    <td style="background-color: pink"><?php echo e($closed_high[$key]); ?></td>
                                    <td style="background-color: orange"><?php echo e($closed_med[$key]); ?></td>
                                    <td style="background-color: lightblue"><?php echo e($closed_low[$key]); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><b>Total</b></td>
                                <td style="background-color: red"><b><?php echo e($closed_crit[count($closed_crit)-1]); ?></b></td>
                                <td style="background-color: pink"><b><?php echo e($closed_high[count($closed_high)-1]); ?></b></td>
                                <td style="background-color: orange"><b><?php echo e($closed_med[count($closed_med)-1]); ?></b></td>
                                <td style="background-color: lightblue"><b><?php echo e($closed_low[count($closed_low)-1]); ?></b></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="right">
            <div class="card card-success">
                <div class="card-header">
                    
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="barChart_closedbyopco" style="height:500px"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
        
            
        
        
            
                
                    
                    
                        
                        
                        
                        
                        
                    
                    
                    
                    
                        
                            
                            
                            
                            
                            
                        
                    
                        
                            
                            
                            
                            
                            
                        
                    
                
            
        
    

    
        
            
        
        
            
                
            
        
    

    <div class="container-fluid">
        <div class="left">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><strong>Remediation Status for Number of Open Critical & High by OpCo</strong></h3>
                </div>
                <div class="box-body  no-padding">
                    <div class="box-body mailbox-messages">
                        <table id="" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>OpCo</th>
                                <th>Total Number of Open Critical & High</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $opco_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $opco): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($opco->opco); ?></td>
                                    <td><?php echo e($open_high_crit[$key]); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><b>Total</b></td>
                                <td><b><?php echo e($open_high_crit[count($open_high_crit)-1]); ?></b></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="right">
            <div class="card card-success">
                <div class="card-header">
                    
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="barChart_opencritandhighbyopco" style="height:500px"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
        
            
        
        
            
                
                    
                        
                            
                            
                        
                    
                    
                    
                        
                            
                            
                        
                    
                        
                            
                            
                        
                    
                
            
        
    

    
        
            
        
        
            
                
            
        
    
<?php $__env->stopSection(); ?>
<?php $__env->startSection('custom-scripts'); ?>
    <script>
        <?php
            //open high & critical
            $open_high_crit_array = [];
            foreach($opco_array as $key => $opco){
                array_push($open_high_crit_array, $open_high_crit[$key]);
            }

            //open
            $open_low_array = [];
            for($i=0; $i<max(array_keys($open_low)); $i++){
                array_push($open_low_array, $open_low[$i]);
            }

            $open_med_array = [];
            for($i=0; $i<max(array_keys($open_med)); $i++){
                array_push($open_med_array, $open_med[$i]);
            }

            $open_high_array = [];
            for($i=0; $i<max(array_keys($open_high)); $i++){
                array_push($open_high_array, $open_high[$i]);
            }

            $open_crit_array = [];
            for($i=0; $i<max(array_keys($open_crit)); $i++){
                array_push($open_crit_array, $open_crit[$i]);
            }

            //closed
            $closed_low_array = [];
            for($i=0; $i<max(array_keys($closed_low)); $i++){
                array_push($closed_low_array, $closed_low[$i]);
            }

            $closed_med_array = [];
            for($i=0; $i<max(array_keys($closed_med)); $i++){
                array_push($closed_med_array, $closed_med[$i]);
            }

            $closed_high_array = [];
            for($i=0; $i<max(array_keys($closed_high)); $i++){
                array_push($closed_high_array, $closed_high[$i]);
            }

            $closed_crit_array = [];
            for($i=0; $i<max(array_keys($closed_crit)); $i++){
                array_push($closed_crit_array, $closed_crit[$i]);
            }
        ?>
        $(function () {
            //Remediation Status for Open & Closed Findings
            var barChartData1 = {
                labels  : ['Total Findings', 'Total Open', 'Total Closed'],
                datasets: [{
                    fillColor           : 'rgba(210, 214, 222, 1)',
                    strokeColor         : 'rgba(210, 214, 222, 1)',
                    pointColor          : 'rgba(210, 214, 222, 1)',
                    pointStrokeColor    : '#c1c7d1',
                    pointHighlightFill  : '#fff',
                    pointHighlightStroke: 'rgba(220,220,220,1)',
                    data                : [<?php echo $total_all ?>, <?php echo $total_open ?>, <?php echo $total_closed ?>],
                }]
            }

            var barChartCanvas1                   = $('#barChart_total').get(0).getContext('2d')
            var barChart1                         = new Chart(barChartCanvas1)
            var barChartData1                     = barChartData1

            barChartData1.datasets[0].fillColor   = '#00a65a'
            barChartData1.datasets[0].strokeColor = '#00a65a'
            barChartData1.datasets[0].pointColor  = '#00a65a'

            var barChartOptions1                  = {
                //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
                scaleBeginAtZero        : true,
                //Boolean - Whether grid lines are shown across the chart
                scaleShowGridLines      : true,
                //String - Colour of the grid lines
                scaleGridLineColor      : 'rgba(0,0,0,.05)',
                //Number - Width of the grid lines
                scaleGridLineWidth      : 1,
                //Boolean - Whether to show horizontal lines (except X axis)
                scaleShowHorizontalLines: true,
                //Boolean - Whether to show vertical lines (except Y axis)
                scaleShowVerticalLines  : true,
                //Boolean - If there is a stroke on each bar
                barShowStroke           : true,
                //Number - Pixel width of the bar stroke
                barStrokeWidth          : 2,
                //Number - Spacing between each of the X value sets
                barValueSpacing         : 50,
                //Number - Spacing between data sets within X values
                barDatasetSpacing       : 1,
                //Boolean - whether to make the chart responsive
                responsive              : true,
                maintainAspectRatio     : true,
            }
            barChart1.Bar(barChartData1, barChartOptions1)

//======================================================================================================================

            //Remediation Status for Number of Open Critical & High by OpCo
            var barChartData2 = {
                labels  : <?php echo json_encode($opco_only_array) ?>,
                datasets: [{
                    fillColor           : 'rgba(210, 214, 222, 1)',
                    strokeColor         : 'rgba(210, 214, 222, 1)',
                    pointColor          : 'rgba(210, 214, 222, 1)',
                    pointStrokeColor    : '#c1c7d1',
                    pointHighlightFill  : '#fff',
                    pointHighlightStroke: 'rgba(220,220,220,1)',
                    data                : <?php echo json_encode($open_high_crit_array) ?>
                }]
            }

            var barChartCanvas2                   = $('#barChart_opencritandhighbyopco').get(0).getContext('2d')
            var barChart2                         = new Chart(barChartCanvas2)
            var barChartData2                     = barChartData2

            barChartData2.datasets[0].fillColor   = '#00a65a'
            barChartData2.datasets[0].strokeColor = '#00a65a'
            barChartData2.datasets[0].pointColor  = '#00a65a'

            var barChartOptions2                  = {
                scaleBeginAtZero        : true,
                scaleShowGridLines      : true,
                scaleGridLineColor      : 'rgba(0,0,0,.05)',
                scaleGridLineWidth      : 1,
                scaleShowHorizontalLines: true,
                scaleShowVerticalLines  : true,
                barShowStroke           : true,
                barStrokeWidth          : 2,
                barValueSpacing         : 10,
                barDatasetSpacing       : 1,
                responsive              : true,
                maintainAspectRatio     : true,
            }
            barChart2.Bar(barChartData2, barChartOptions2)

//======================================================================================================================

            //Remediation Status for Number of Closed by OpCo
            var barChartData3 = {
                labels  : <?php echo json_encode($opco_only_array) ?>,
                datasets: [{
                    data    : <?php echo json_encode($closed_crit_array) ?>
                },
                {
                    data    : <?php echo json_encode($closed_high_array) ?>
                },
                {
                    data    : <?php echo json_encode($closed_med_array) ?>
                },
                {
                    data    : <?php echo json_encode($closed_low_array) ?>
                }]
            }

            var barChartCanvas3                   = $('#barChart_closedbyopco').get(0).getContext('2d')
            var barChart3                         = new Chart(barChartCanvas3)
            var barChartData3                     = barChartData3

            barChartData3.datasets[0].fillColor   = 'red'
            barChartData3.datasets[0].strokeColor = 'red'
            barChartData3.datasets[0].pointColor  = 'red'

            barChartData3.datasets[1].fillColor   = 'pink'
            barChartData3.datasets[1].strokeColor = 'pink'
            barChartData3.datasets[1].pointColor  = 'pink'

            barChartData3.datasets[2].fillColor   = 'orange'
            barChartData3.datasets[2].strokeColor = 'orange'
            barChartData3.datasets[2].pointColor  = 'orange'

            barChartData3.datasets[3].fillColor   = 'lightblue'
            barChartData3.datasets[3].strokeColor   = 'lightblue'
            barChartData3.datasets[3].pointColor   = 'lightblue'

            var barChartOptions3                  = {
                scaleBeginAtZero        : true,
                scaleShowGridLines      : true,
                scaleGridLineColor      : 'rgba(0,0,0,.05)',
                scaleGridLineWidth      : 1,
                scaleShowHorizontalLines: true,
                scaleShowVerticalLines  : true,
                barShowStroke           : true,
                barStrokeWidth          : 2,
                barValueSpacing         : 10,
                barDatasetSpacing       : 1,
                responsive              : true,
                maintainAspectRatio     : true
            }
            barChart3.Bar(barChartData3, barChartOptions3)

//======================================================================================================================

            //Remediation Status for Number of Open by OpCo
            var barChartData4 = {
                labels  : <?php echo json_encode($opco_only_array) ?>,
                datasets: [{
                    data    : <?php echo json_encode($open_crit_array) ?>
                },
                {
                    data    : <?php echo json_encode($open_high_array) ?>
                },
                {
                    data    : <?php echo json_encode($open_med_array) ?>
                },
                {
                    data    : <?php echo json_encode($open_low_array) ?>
                }]
            }

            var barChartCanvas4                   = $('#barChart_openbyopco').get(0).getContext('2d')
            var barChart4                         = new Chart(barChartCanvas4)
            var barChartData4                     = barChartData4

            barChartData4.datasets[0].fillColor   = 'red'
            barChartData4.datasets[0].strokeColor = 'red'
            barChartData4.datasets[0].pointColor  = 'red'

            barChartData4.datasets[1].fillColor   = 'pink'
            barChartData4.datasets[1].strokeColor = 'pink'
            barChartData4.datasets[1].pointColor  = 'pink'

            barChartData4.datasets[2].fillColor   = 'orange'
            barChartData4.datasets[2].strokeColor = 'orange'
            barChartData4.datasets[2].pointColor  = 'orange'

            barChartData4.datasets[3].fillColor   = 'lightblue'
            barChartData4.datasets[3].strokeColor   = 'lightblue'
            barChartData4.datasets[3].pointColor   = 'lightblue'

            var barChartOptions4                  = {
                scaleBeginAtZero        : true,
                scaleShowGridLines      : true,
                scaleGridLineColor      : 'rgba(0,0,0,.05)',
                scaleGridLineWidth      : 1,
                scaleShowHorizontalLines: true,
                scaleShowVerticalLines  : true,
                barShowStroke           : true,
                barStrokeWidth          : 2,
                barValueSpacing         : 10,
                barDatasetSpacing       : 1,
                responsive              : true,
                maintainAspectRatio     : true
            }
            barChart4.Bar(barChartData4, barChartOptions4)
      })
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
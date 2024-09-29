<?php $__env->startSection('title', 'Weekly Statistics'); ?>
<?php $__env->startSection('content'); ?>
    <div class="box">
        <div class="box-header">
            <h3 class="box-title"><strong>Total Number of Open Critical/High Findings</strong> </h3>
        </div>

        <div class="box-body  no-padding">
            <div class="box-body mailbox-messages">
                <?php echo e(Form::open(array('url'=>'/weekly_stats_submit1', 'method' => 'get'))); ?>

                    <span>Date From: <input type="text" id="datepickerFrom1" name="from1"></span>
                    <span>Date To: <input type="text" id="datepickerTo1" name="to1"></span>
                    <button type="submit" class="btn  btn-flat btn-sm btn-primary">Search</button>
                <?php echo e(Form::close()); ?>

                <table id="" class="table table-bordered table-striped">
                    <thead><br>
                        <tr>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php if(isset($countTotalOfCritHigh)): ?>
                                <td><?php echo e($countTotalOfCritHigh); ?></td>
                            <?php else: ?>
                                <td>No records found.</td>
                            <?php endif; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-header">
            <h3 class="box-title"><strong>Total Number of Closed Findings Group By OpCo</strong> </h3>
        </div>
        <div class="box-body  no-padding">
            <div class="box-body mailbox-messages">
                <?php echo e(Form::open(array('url'=>'/weekly_stats_submit2', 'method' => 'get'))); ?>

                <span>Date From: <input type="text" id="datepickerFrom2" name="from2"></span>
                <span>Date To: <input type="text" id="datepickerTo2" name="to2"></span>
                <button type="submit" class="btn btn-flat btn-sm btn-primary">Search</button>
                <?php echo e(Form::close()); ?>

                <table id="" class="table table-bordered table-striped text-center">
                    <thead><br>
                    <tr>
                        <th class="label-primary">OpCo</th>
                        <th style="background: #9f1400; color:#fff;">Critical</th>
                        <th   class="label-danger">High</th>
                        <th  class="label-warning">Medium</th>
                        <th  class="label-success">Low</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(isset($closed_low)): ?>
                        <?php $__currentLoopData = $opco_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $opco): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($opco->opco); ?></td>
                                <td style="background-color: #dcb8b8;"><?php echo e($closed_crit[$key]); ?></td>
                                <td   class="bg-danger"><?php echo e($closed_high[$key]); ?></td>
                                <td class="bg-warning"><?php echo e($closed_med[$key]); ?></td>
                                <td class="bg-success"><?php echo e($closed_low[$key]); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><b>Total</b></td>
                            <td style="background-color: #dcb8b8;"><b><?php echo e($closed_crit[count($closed_crit)-1]); ?></b></td>
                            <td   class="bg-danger"><b><?php echo e($closed_high[count($closed_high)-1]); ?></b></td>
                            <td class="bg-warning"><b><?php echo e($closed_med[count($closed_med)-1]); ?></b></td>
                            <td class="bg-success"><b><?php echo e($closed_low[count($closed_low)-1]); ?></b></td>
                        </tr>
                    <?php else: ?>
                        <td>No records found.</td>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="box">
        <div class="box-header">
            <h3 class="box-title"><strong>Total Number of Open Findings Group By OpCo</strong> </h3>
        </div>
        <div class="box-body  no-padding">
            <div class="box-body mailbox-messages">
                <?php echo e(Form::open(array('url'=>'/weekly_stats_submit4', 'method' => 'get'))); ?>

                <span>Date From: <input type="text" id="datepickerFrom4" name="from4"></span>
                <span>Date To: <input type="text" id="datepickerTo4" name="to4"></span>
                <button type="submit" class="btn btn-flat btn-sm btn-primary">Search</button>
                <?php echo e(Form::close()); ?>

                <table id="" class="table table-bordered table-striped text-center">
                    <thead><br>
                    <tr>
                        <th class="label-primary">OpCo</th>
                        <th style="background: #9f1400; color:#fff;">Critical</th>
                        <th   class="label-danger">High</th>
                        <th  class="label-warning">Medium</th>
                        <th  class="label-success">Low</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if(isset($opened_low)): ?>
                        <?php $__currentLoopData = $opco_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $opco): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($opco->opco); ?></td>
                                <td style="background-color: #dcb8b8;"><?php echo e($opened_crit[$key]); ?></td>
                                <td   class="bg-danger"><?php echo e($opened_high[$key]); ?></td>
                                <td class="bg-warning"><?php echo e($opened_med[$key]); ?></td>
                                <td class="bg-success"><?php echo e($opened_low[$key]); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><b>Total</b></td>
                            <td style="background-color: #dcb8b8;"><b><?php echo e($opened_crit[count($opened_crit)-1]); ?></b></td>
                            <td   class="bg-danger"><b><?php echo e($opened_high[count($opened_high)-1]); ?></b></td>
                            <td class="bg-warning"><b><?php echo e($opened_med[count($opened_med)-1]); ?></b></td>
                            <td class="bg-success"><b><?php echo e($opened_low[count($opened_low)-1]); ?></b></td>
                        </tr>
                    <?php else: ?>
                        <td>No records found.</td>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
        
            
        
        
            
                
                
                
                
                
                
                    
                    
                        
                        
                        
                        
                        
                    
                    
                    
                    
                        
                            
                                
                                
                                
                                
                                
                            
                        
                    
                        
                    
                    
                
            
        
    
<?php $__env->stopSection(); ?>
<?php $__env->startSection('custom-scripts'); ?>
    <script>
        $( function() {
            $( "#datepickerFrom1" ).datepicker("setDate", new Date());
            $( "#datepickerTo1" ).datepicker("setDate", new Date());
            $( "#datepickerFrom2" ).datepicker("setDate", new Date());
            $( "#datepickerTo2" ).datepicker("setDate", new Date());
            $( "#datepickerFrom3" ).datepicker("setDate", new Date());
            $( "#datepickerTo3" ).datepicker("setDate", new Date());
            $( "#datepickerFrom4" ).datepicker("setDate", new Date());
            $( "#datepickerTo4" ).datepicker("setDate", new Date());
        } );
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
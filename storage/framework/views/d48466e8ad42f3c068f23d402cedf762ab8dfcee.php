<?php $__env->startSection('title', 'Report'); ?>
<?php echo $__env->yieldContent('css'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><strong>Closed & Pending Tasks Based on Roles</strong></h3>
                </div>
                <div class="box-body no-padding">
                    <div class="box-body">
                        <table id="" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Roles</th>
                                <th>Total Pending</th>
                                <th>Total Closed</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                    <td>PMO</td>
                                    <td><?php echo e(count($task)); ?></td>
                                    <td><?php echo e(count($task2) + count($task3) + count($task4) + count($task5) + count($task6)); ?></td>
                            </tr>
                            <tr>
                                <td>Tester</td>
                                <td><?php echo e(count($task2)); ?></td>
                                <td><?php echo e(count($task3) + count($task4) + count($task5) + count($task6)); ?></td>
                            </tr>
                            <tr>
                                <td>Analyst</td>
                                <td><?php echo e(count($task3)); ?></td>
                                <td><?php echo e(count($task4) + count($task5) + count($task6)); ?></td>
                            </tr>
                            <tr>
                                <td>QA</td>
                                <td><?php echo e(count($task4)); ?></td>
                                <td><?php echo e(count($task5) + count($task6)); ?></td>
                            </tr>
                            <tr>
                                <td>HoD</td>
                                <td><?php echo e(count($task5)); ?></td>
                                <td><?php echo e(count($task6)); ?></td>
                            </tr>
                            <tr>
                                <td>Rem Officer</td>
                                <td><?php echo e(count($task7)); ?></td>
                                <td><?php echo e(count($task8)); ?></td>
                            </tr>
                            <tr>
                                <td>Rem PMO</td>
                                <td><?php echo e(count($task8)); ?></td>
                                <td><?php echo e(count($task9)); ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
        </div>

        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><strong>Closed & Pending Tasks For Each User</strong></h3>
            </div>
            <div class="box-body no-padding">
                <div class="box-body">
                    <table id="" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Total Pending</th>
                            
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $pmo_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pmo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($pmo['name']); ?></td>
                                <td>PMO</td>
                                <td><?php echo e(count($pmo) - 1); ?></td>
                                
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $tester_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tester): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($tester['name']); ?></td>
                                <td>Tester</td>
                                <td><?php echo e(count($tester) - 1); ?></td>
                                
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $analyst_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $analyst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($analyst['name']); ?></td>
                                <td>Analyst</td>
                                <td><?php echo e(count($analyst) - 1); ?></td>
                                
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $qa_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $qa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($qa['name']); ?></td>
                                <td>QA</td>
                                <td><?php echo e(count($qa) - 1); ?></td>
                                
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $hod_array; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($hod['name']); ?></td>
                                <td>HoD</td>
                                <td><?php echo e(count($hod) - 1); ?></td>
                                
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
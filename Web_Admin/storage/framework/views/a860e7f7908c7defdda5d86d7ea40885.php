<?php $__env->startSection('content'); ?>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-header bg-primary py-2">
                       <h3 class="m-0 text-white"> <?php echo e(__('Delivery Cost Price')); ?></h3>
                    </div>
                    <form action="<?php echo e(route('deliveryCost.store')); ?>" method="post">
                        <?php echo csrf_field(); ?>
                        <div class="card-body">
                            <?php
                                $websetting = App\Models\WebSetting::first();
                                $currency = $websetting->currency ?? config('enums.currency')
                            ?>
                            <div class="row">
                                <div class="col-sm-4">
                                    <label class="m-0"><?php echo e(__('Delivery Charge')); ?></label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><?php echo e($currency); ?></span>
                                        </div>
                                        <input type="number" class="form-control" name="cost"
                                            value="<?php echo e($cost ? $cost->cost : 0); ?>" required>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label class="m-0"><?php echo e(__('Delivery Fee')); ?></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><?php echo e($currency); ?></span>
                                        </div>
                                        <input type="number" class="form-control" name="fee_cost"
                                            value="<?php echo e($cost ? $cost->fee_cost : 100); ?>" required>
                                    </div>
                                    <small class="text-gray"><?php echo e(__('Default delivery fee')); ?> 100 </small>
                                </div>

                                <div class="col-sm-4">
                                    <label class="m-0"><?php echo e(__('Minimum order amount')); ?></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><?php echo e($currency); ?></span>
                                        </div>
                                        <input type="number" class="form-control" name="minimum_cost"
                                            value="<?php echo e($cost ? $cost->minimum_cost : 00); ?>" required>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card-footer py-2">
                            <button type="submit" class="btn btn-primary px-4"><?php echo e(__('Save And Update')); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\deliveryCost\index.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row h-100vh align-items-center">
            <div class="col-md-8 col-lg-6 col-sm-12 m-auto">
                <form action="<?php echo e(route('invoiceManage.update', $invoice?->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary py-2">
                            <h3 class="text-white m-0"><?php echo e(__('Select Printer Type')); ?></h3>
                        </div>
                        <div class="card-body pb-3">
                            <div class="d-flex justify-content-start flex-wrap" style="gap: 24px">
                                <div class="printerBox p-2">
                                    <input type="radio" class="btn-check" name="type" value="regular" id="regular" <?php echo e($invoice?->type == 'regular' ? 'checked' : ''); ?>>
                                    <label for="regular" class="printer_type">
                                        <i class="fa fa-print icon"></i>
                                        <div class="title mt-2"><?php echo e(__('Regular Printer')); ?></div>
                                        <div class="chackBox">
                                            <div class="dot"></div>
                                        </div>
                                    </label>
                                </div>
                                <div class="printerBox p-2">
                                    <input type="radio" class="btn-check" name="type" value="pos" id="pos" <?php echo e($invoice?->type == 'pos' ? 'checked' : ''); ?>>
                                    <label for="pos" class="printer_type">
                                        <i class="fas fa-receipt icon"></i>
                                        <div class="title mt-2"><?php echo e(__('Pos Printer')); ?></div>
                                        <div class="chackBox">
                                            <div class="dot"></div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer py-3 ">
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary rounded-0"><?php echo e(__('Save And Update')); ?></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\invoice-manage.blade.php ENDPATH**/ ?>
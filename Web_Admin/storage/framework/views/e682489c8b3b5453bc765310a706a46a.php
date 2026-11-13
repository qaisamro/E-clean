<?php $__env->startSection('content'); ?>
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-primary" data-toggle="modal" data-target="#AddSubProduct">
                <i class="fa fa-plus"></i> <?php echo e(__('Add_New_Sub_Product')); ?>

            </button>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center py-2">
                        <h3 class="m-0"><?php echo e(__('All_sub_products_under')); ?> <?php echo e($product->name_bn ?? $product->name); ?> <?php echo e(('product')); ?></h3>
                        <a href="<?php echo e(route('product.index')); ?>" class="btn btn-secondary"><?php echo e(__('Back')); ?></a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="myTable">
                                <thead>
                                    <tr>
                                        <th><?php echo e(__('Name')); ?></th>
                                        <th><?php echo e(__('Price')); ?></th>
                                        <th><?php echo e(__('Description')); ?></th>
                                        <th><?php echo e(__('Action')); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $product->subProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subProduct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($subProduct->name_bn ?? $subProduct->name); ?></td>
                                            <td>
                                                <?php echo e(currencyPosition($subProduct->price ? $subProduct->price : '00')); ?>

                                            </td>
                                            <td>
                                                <?php echo e($subProduct->description); ?>

                                            </td>

                                            <td>
                                                <button class="btn btn-primary btn-sm" data-toggle="modal"
                                                    data-target="#EditSubProduct<?php echo e($subProduct->id); ?>">
                                                    <i class="far fa-edit"></i>
                                                </button>
                                                <a href="<?php echo e(route('product.subproduct.delete', $subProduct->id)); ?>"
                                                    class="btn btn-danger btn-sm delete-confirm">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="EditSubProduct<?php echo e($subProduct->id); ?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"><?php echo e(__('Edit_Sub_Product')); ?></h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="<?php echo e(route('product.subproduct.update', $subProduct->id)); ?>"
                                                        method="POST" enctype="multipart/form-data">
                                                        <?php echo csrf_field(); ?>
                                                        <div class="modal-body">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="mb-3">
                                                                <label for=""><?php echo e(__('Product') . ' ' . __('Name')); ?></label>
                                                                <input class="form-control" type="text" name="name_bn"
                                                                    value="<?php echo e($subProduct->name_bn); ?>" required />
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for=""><?php echo e(__('Price')); ?></label>
                                                                <input class="form-control" type="text" name="price"
                                                                    placeholder="<?php echo e(__('Price_Add...')); ?>"
                                                                    value="<?php echo e($subProduct->price); ?>" onkeypress="onlyNumber(event)" required />
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for=""><?php echo e(__('Discount') . ' ' . __('Price')); ?></label>
                                                                <input class="form-control" type="text" name="discount_price"
                                                                    value="<?php echo e($subProduct->discount_price); ?>" onkeypress="onlyNumber(event)" />
                                                            </div>
                                                            <div>
                                                                <label for=""><?php echo e(__('Description')); ?></label>
                                                                <textarea class="form-control" name="description" rows="3">
                                                                <?php echo e($subProduct->description); ?>

                                                            </textarea>
                                                            </div>
                                                            <input type="hidden" name="variant_id"
                                                                value="<?php echo e($subProduct->variant_id); ?>" />
                                                            <input type="hidden" name="service_id"
                                                                value="<?php echo e($subProduct->service_id); ?>" />
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal"><?php echo e(__('Close')); ?></button>
                                                            <button type="submit" class="btn btn-primary"><?php echo e(__('Update')); ?></button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="AddSubProduct">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo e(__('Add_Sub_Product')); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?php echo e(route('product.subproduct.store', $product->id)); ?>" method="POST"
                    enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <label class="mb-1"><?php echo e(__('Product') . ' ' . __('Name')); ?></label>
                        <input class="form-control" name="name_bn" type="text" placeholder="<?php echo e(__('Product') . ' ' . __('Name')); ?>" required/>

                        <label class="mb-1 mt-3"><?php echo e(__('Product') . ' ' . __('Price')); ?></label>
                        <input name="price" type="text" class="form-control"
                            placeholder="<?php echo e(__('Product') . ' ' . __('Price')); ?>" onkeypress="onlyNumber(event)" required/>

                        <label class="mb-1 mt-3"><?php echo e(__('Discount') . ' ' . __('Price')); ?></label>
                        <input name="discount_price" type="text" class="form-control"
                            placeholder="<?php echo e(__('Discount') . ' ' . __('Price')); ?>" onkeypress="onlyNumber(event)"/>

                        <div>
                            <label class="mb-1 mt-3"><?php echo e(__('Description')); ?></label>
                            <textarea name="description" required class="form-control" placeholder="<?php echo e(__('Description')); ?>"></textarea>
                        </div>
                        <input type="hidden" name="variant_id" value="<?php echo e($product->variant_id); ?>" />
                        <input type="hidden" name="service_id" value="<?php echo e($product->service_id); ?>" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(__('Close')); ?></button>
                        <button type="submit" class="btn btn-primary"><?php echo e(__('Save')); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            function onlyNumber(evt) {
                var chars = String.fromCharCode(evt.which);
                if (!(/[0-9.]/.test(chars))) {
                    evt.preventDefault();
                }
            }
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\sub-product\index.blade.php ENDPATH**/ ?>
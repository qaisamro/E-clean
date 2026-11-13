<?php $__env->startSection('title', __('language_edit')); ?>
<?php $__env->startSection('content'); ?>
    <div class="container-fluid my-4">
        <div class="row">
            <div class="col-lg-9 mt-2 mx-auto ">
                <form action="<?php echo e(route('language.update', $language->id)); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="card border-0 shadow-sm rounded-12 mb-5">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                            <h3 class="m-0"><?php echo e(__('Edit_Language')); ?></h3>
                            <a href="<?php echo e(route('language.index')); ?>" class="btn btn-danger">
                                <?php echo e(__('Back')); ?>

                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="" class="mb-0"><?php echo e(__('Title')); ?></label>
                                    <input type="text" name="title" value="<?php echo e($language->title); ?>"
                                        class="form-control mb-2" />
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="text-danger"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <input type="hidden" name="name" value="<?php echo e($language->name); ?>" />
                                </div>
                                <div class="col-md-6">
                                    <label for="" class="mb-0"><?php echo e(__('Flag')); ?></label>
                                    <input type="file" name="image" class="form-control mb-2" />
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="text-danger"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>

                            <table class="table table-hover">
                                <thead class="table-bg-color">
                                    <tr>
                                        <th><?php echo e(__('Key')); ?></th>
                                        <th><?php echo e(__('Value')); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                      
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $languageData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <?php echo e($key); ?>

                                                <input type="hidden" name="key[]" value="<?php echo e($key); ?>">
                                            </td>
                                            <td class="py-2">
                                                <input type="text" class="languageInput"
                                                    name="data[<?php echo e($key); ?>]" value="<?php echo e($value); ?>"
                                                    placeholder="value for <?php echo e($language->title); ?>">
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                            <div class="submitBtn shadow-sm">
                                <button type="submit" class="btn btn-primary"><?php echo e(__('update_and_save')); ?></button>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <style>
        .submitBtn {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 5px;
            border-top: 1px solid #eceff1;
            z-index: 2;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\language\edit.blade.php ENDPATH**/ ?>
<?php $__env->startSection('title', __('add_language')); ?>
<?php $__env->startSection('content'); ?>
    <div class="container-fluid my-4">
        <div class="row">
            <div class="col-lg-8 mt-2 mx-auto ">
                <form action="<?php echo e(route('language.store')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="card border-0 shadow-sm">
                        <div class="card-header">
                            <h3 class="m-0"><?php echo e(__('create_new_language')); ?></h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-4">
                                    <label class="mb-2"><?php echo e(__('Title')); ?></label>
                                    <input class="form-control" type="text" name="title"
                                        placeholder="<?php echo e(__('enter_your_language_title')); ?>" title="<?php echo e(__('title')); ?>"
                                        value="" />
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
                                </div>
                                <div class="col-lg-4">
                                    <label class="mb-2"><?php echo e(__('short_name')); ?>

                                        <small>(<?php echo e(__('only_allow_english_characters')); ?>)</small></label>
                                    <input name="name" oninput="this.value=this.value.replace(/[^a-z]/gi,'')"
                                        class="form-control" placeholder="<?php echo e(__('example')); ?>: <?php echo e(__('bn')); ?>" autocomplete="off"
                                        required />
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
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
                                <div class="col-lg-4">
                                    <label for="" class="mb-2"><?php echo e(__('Flag')); ?></label>
                                    <input type="file" name="Image" class="form-control mb-2" />
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
                        </div>
                        <div class="card-footer d-flex justify-content-between flex-wrap gap-2 ">
                            <a href="<?php echo e(route('language.index')); ?>" class="btn btn-danger"><?php echo e(__('back')); ?></a>
                            <button type="submit" class="btn common-btn"><?php echo e(__('submit')); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\language\create.blade.php ENDPATH**/ ?>
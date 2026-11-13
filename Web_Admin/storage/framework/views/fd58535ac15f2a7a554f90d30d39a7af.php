<?php $__env->startSection('content'); ?>
    <div class="container-fluid mt-5">
        <div class="col-sm-6 p-md-0 mt-2 mt-sm-0 d-flex">
            <a href="<?php echo e(route('web.faq.list')); ?>" class="btn btn-primary mb-1"><i class="fa fa-arrow-left"></i>
                <?php echo e(__('Back')); ?></a>
        </div>

        <div class="row">
            <div class="col-xl-7 col-xxl-7 col-lg-7 m-auto">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"><?php echo e(__('Edit') . ' ' . __('FAQ')); ?></h4>
                    </div>
                    <div class="card-body">
                        <?php
                            $content = json_decode($faq->content, true);
                            $content =
                                is_array($content) && count($content) > 0
                                    ? $content[0]
                                    : ['ques' => '', 'answer' => ''];
                        ?>
                        <form action="<?php echo e(route('web.faq.update', $faq->slug)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <div class="basic-form">
                                <div class="form-group">
                                    <label>Category <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-control" required>
                                        <option value="">Select Category</option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($category->id); ?>"
                                                <?php echo e($faq->faq_category_id == $category->id ? 'selected' : ''); ?>>
                                                <?php echo e($category->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </select>
                                </div>
                                <div class="form-group mt-3">
                                    <label><?php echo e(__('Question')); ?> <span class="text-danger">*</span></label>
                                    <input type="text" name="question" class="form-control"
                                        placeholder="<?php echo e(__('Enter')); ?> <?php echo e(__('Question')); ?>"
                                        value="<?php echo e(old('question', $content['ques'] ?? '')); ?>" required>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['question'];
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
                                <div class="form-group mt-3">
                                    <label><?php echo e(__('Answer')); ?> <span class="text-danger">*</span></label>
                                    <textarea name="answer" class="form-control" placeholder="<?php echo e(__('Enter')); ?> <?php echo e(__('Answer')); ?>" rows="5"
                                        required><?php echo e(old('answer', $content['answer'] ?? '')); ?></textarea>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['answer'];
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
                                <div class="form-group mt-3">
                                    <label>Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control" required>
                                        <option value="active" <?php echo e($faq->status == 'active' ? 'selected' : ''); ?>>Active
                                        </option>
                                        <option value="inactive" <?php echo e($faq->status == 'inactive' ? 'selected' : ''); ?>>
                                            Inactive</option>
                                    </select>
                                </div>
                                <div class="form-group text-right mt-3 mb-0">
                                    <button type="submit" class="btn btn-primary"><?php echo e(__('Update')); ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\website\faq\edit.blade.php ENDPATH**/ ?>
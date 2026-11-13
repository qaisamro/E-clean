<?php $__env->startSection('content'); ?>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header py-2 d-flex align-items-center justify-content-between">
                        <h2 class="card-title m-0"><?php echo e(__('All') . ' ' . __('FAQs')); ?></h2>
                        <div class="d-flex justify-content-end">
                            <form class="" action="<?php echo e(route('web.faq.list')); ?>" method="GET">
                                <div class="d-flex align-items-center gap-2">
                                    <select name="category_id" class="form-control" onchange="this.form.submit()">
                                        <option value="">All Categories</option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($category->id); ?>"
                                                <?php echo e(request('category_id') == $category->id ? 'selected' : ''); ?>>
                                                <?php echo e($category->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </select>
                                    <a href="<?php echo e(route('web.faq.create')); ?>" class="btn btn-primary">
                                        <?php echo e(__('Add_New')); ?>

                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body pt-2">

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped <?php echo e(session()->get('local')); ?>" id="myTable">
                                <thead>
                                    <tr>
                                        <th scope="col"><?php echo e(__('SL')); ?></th>
                                        <th scope="col">Category</th>
                                        <th scope="col"><?php echo e(__('Question')); ?></th>
                                        <th scope="col"><?php echo e(__('Answer')); ?></th>
                                        <th scope="col">Status</th>
                                        <th scope="col"><?php echo e(__('Action')); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <?php
                                            $items = json_decode($faq->content, true);
                                            $items = is_array($items) ? $items : [];
                                            $item = count($items) > 0 ? $items[0] : ['ques' => '', 'answer' => ''];
                                            $serial = $index + 1;
                                            $faqSlug = $faq->slug ?? 'faq-' . $faq->id;
                                        ?>

                                        <tr>
                                            <td class="bg-color py-1"><?php echo e($serial); ?></td>
                                            <td class="bg-color py-1">
                                                <?php echo e($faq->category ? $faq->category->name : 'Uncategorized'); ?>

                                            </td>
                                            <td class="bg-color py-1"><?php echo e($item['ques'] ?? ''); ?></td>
                                            <td class="bg-color py-1"><?php echo e(Str::limit($item['answer'] ?? '', 60)); ?></td>
                                            <td class="bg-color py-1">
                                                <form action="<?php echo e(route('web.faq.status.toggle', $faq->id)); ?>" method="POST" class="d-inline">
                                                    <?php echo csrf_field(); ?>
                                                    <label class="switch">
                                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?>
                                                        <a class="visitorMessage">toggle</a>
                                                        <?php else: ?>
                                                        <input type="checkbox"
                                                            <?php echo e($faq->status == 'active' ? 'checked' : ''); ?> onchange="this.closest('form').submit()">
                                                        <span class="slider round"></span>
                                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                    </label>
                                                </form>
                                            </td>
                                            <td class="bg-color p-1">
                                                <a href="<?php echo e(route('web.faq.edit', $faqSlug)); ?>"
                                                    class="btn btn-sm btn-primary mb-1">
                                                    <i class="far fa-edit"></i>
                                                </a>

                                                <a href="<?php echo e(route('web.faq.delete', $faqSlug)); ?>"
                                                    class="btn btn-sm btn-danger delete-confirm mb-1">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">
                                                <?php echo e(__('No FAQs found')); ?>

                                            </td>
                                        </tr>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\website\faq\index.blade.php ENDPATH**/ ?>
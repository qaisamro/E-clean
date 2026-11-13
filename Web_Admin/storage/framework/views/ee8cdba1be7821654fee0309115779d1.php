<?php $__env->startSection('title', __('languages')); ?>
<?php $__env->startSection('content'); ?>
    <style>
        .language-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            padding: 14px;
            border-bottom: 1px dotted #eee;
            background: #fff;
            margin-top: 6px;
            border-radius: 8px;
        }

        .languageInput {
            width: 100%;
            min-height: 34px;
            padding: 0 8px;
            border: 1px solid #cad1d7;
            border-radius: 4px;
            outline: 0;
        }

        .languageInput:focus {
            border-color: var(--theme-color);
        }
    </style>
    <div class="container-fluid my-4">
        <div class="row">
            <div class="col-xl-8 col-lg-9 mt-2 mx-auto ">
                <div class="card border-0 rounded shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center bg-primary">
                        <h3 class="m-0"><?php echo e(__('languages')); ?></h3>
                        <a class="btn btn-secondary" href="<?php echo e(route('language.create')); ?>"><?php echo e(__('create_new_language')); ?></a>
                    </div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="language-item shadow-sm">
                        <div class="d-flex gap-2 flex-wrap">
                            <div style="min-width: 160px">
                                <small class="text-black-50 d-block fst-italic dark-text-color" style="line-height: 0.7;">
                                    <?php echo e(__('Title')); ?>

                                </small>
                                <strong class="fs-6"><?php echo e($language->title); ?></strong>
                            </div>

                            <div style="min-width: 160px">
                                <small class="text-black-50 d-block fst-italic dark-text-color" style="line-height: 0.7;">
                                    <?php echo e(__('Name')); ?>

                                </small>
                                <strong><?php echo e($language->name); ?></strong>
                            </div>
                            <div>
                                <small class="text-black-50 d-block fst-italic pb-1 dark-text-color"
                                    style="line-height: 0.7;">
                                    <?php echo e(__('Flag')); ?>

                                </small>
                                <img src="<?php echo e($language->thumbnail?->file); ?>" alt="<?php echo e($language->image); ?>" width="35">
                            </div>
                        </div>
                        <div>
                            <a href="<?php echo e(route('language.edit', $language->id)); ?>" class="btn common-btn btn-sm">
                                <i class="fa fa-edit" aria-hidden="true"></i>
                            </a>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($language->name != 'en'): ?>
                                <a class="delete-confirm btn btn-danger btn-sm"
                                    href="<?php echo e(route('language.delete', $language->id)); ?>">
                                    <i class="fa fa-trash"></i>
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        $('.delete-confirm').on('click', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            Swal.fire({
                title: "هل أنت متأكد؟",
                text: "لن تتمكن من التراجع عن هذا الإجراء!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#00B894',
                cancelButtonColor: '#d33',
                confirmButtonText: 'تأكيد',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            })
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\language\index.blade.php ENDPATH**/ ?>
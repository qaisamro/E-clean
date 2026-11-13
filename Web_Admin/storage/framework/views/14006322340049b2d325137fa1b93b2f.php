<div>
    <div class="basic-form">
        <form <?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?> action="#" <?php else: ?> <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check($route)): ?> action="<?php echo e(route($route, $updateId)); ?>"<?php endif; ?> <?php endif; ?> method="POST" enctype="multipart/form-data"> <?php echo csrf_field(); ?>
            <?php echo e($slot); ?>


            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check($route)): ?>
            <div class="form-group text-right mt-3 mb-0">
                <button <?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?> type="button" <?php else: ?> type="submit" <?php endif; ?> class="btn btn-primary mb-2 <?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?> visitorMessage <?php endif; ?>"><?php echo e(__($type)); ?></button>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>
<?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\components\form.blade.php ENDPATH**/ ?>
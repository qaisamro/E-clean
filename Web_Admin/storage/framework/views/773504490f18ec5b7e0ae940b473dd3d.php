<?php $__env->startSection('content'); ?>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="w-100">
                            <h2 class="float-left"><?php echo e(__('Edit Customer')); ?></h2>
                            <div class="text-right">
                                <a class="btn btn-light" href="<?php echo e(route('customer.index')); ?>"> <?php echo e(__('Back')); ?> </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo e(route('customer.update', $customer->id)); ?>" method="POST" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <div class="row">
                                <div class="col-12 col-md-6 mb-2">
                                    <label for="first_name"><?php echo e(__('First_Name')); ?> <strong class="text-danger">*</strong></label>
                                    <input type="text" class="form-control" name="first_name" id="first_name"
                                        value="<?php echo e(old('first_name', optional($customer->user)->first_name)); ?>"
                                        placeholder="<?php echo e(__('First_Name')); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['first_name'];
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

                                <div class="col-12 col-md-6 mb-2">
                                    <label for="last_name"><?php echo e(__('Last_Name')); ?></label>
                                    <input type="text" class="form-control" name="last_name" id="last_name"
                                        value="<?php echo e(old('last_name', optional($customer->user)->last_name)); ?>"
                                        placeholder="<?php echo e(__('Last_Name')); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['last_name'];
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

                                <div class="col-12 col-md-6 mb-2">
                                    <label for="mobile"><?php echo e(__('Mobile_number')); ?> <strong class="text-danger">*</strong></label>
                                    <input type="text" class="form-control" name="mobile" id="mobile"
                                        value="<?php echo e(old('mobile', optional($customer->user)->mobile)); ?>"
                                        placeholder="<?php echo e(__('Mobile_number')); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['mobile'];
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

                                <div class="col-12 col-md-6 mb-2">
                                    <label for="email"><?php echo e(__('Email')); ?></label>
                                    <input type="email" class="form-control" name="email" id="email"
                                        value="<?php echo e(old('email', optional($customer->user)->email)); ?>"
                                        placeholder="<?php echo e(__('Email')); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
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

                                <div class="col-12 col-md-6 mb-2">
                                    <label for="password">
                                        <?php echo e(__('New_Password')); ?>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('role', 'root|admin')): ?>
                                            <span class="badge bg-danger ms-1" title="<?php echo e(__('Admin only')); ?>"><?php echo e(__('admin')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <small class="text-muted d-block"><?php echo e(__('Leave blank to keep current')); ?></small>
                                    </label>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('role', 'root|admin')): ?>
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="password" id="password"
                                            placeholder="<?php echo e(__('New_Password')); ?>">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordEdit" tabindex="-1" aria-label="Toggle password visibility">
                                            <i class="fa fa-eye" id="togglePasswordEditIcon"></i>
                                        </button>
                                    </div>
                                    <?php else: ?>
                                    <input type="password" class="form-control" name="password" id="password" disabled placeholder="<?php echo e(__('Admin only')); ?>">
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password'];
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

                                <div class="col-12 col-md-6 mb-2">
                                    <label for="password_confirmation"><?php echo e(__('Confirm_Password')); ?></label>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('role', 'root|admin')): ?>
                                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation"
                                        placeholder="<?php echo e(__('Confirm_Password')); ?>">
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['password_confirmation'];
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

                                <div class="col-12 col-md-6 mb-2 py-2">
                                    <label for="profile_photo"><?php echo e(__('Profile_Photo')); ?></label>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(optional($customer->user)->profilePhotoPath): ?>
                                        <div class="mb-2">
                                            <img src="<?php echo e($customer->user->profilePhotoPath); ?>"
                                                alt="<?php echo e($customer->user->name); ?>"
                                                width="80" height="80"
                                                class="rounded-circle border">
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <input type="file" class="form-control-file" name="profile_photo" id="profile_photo"
                                        accept="image/jpg,image/jpeg,image/png,image/webp">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['profile_photo'];
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

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['customer.edit', 'customer.update'])): ?>
                                <div class="col-12 col-md-6 mb-2 py-2 d-flex align-items-end">
                                    <button type="submit"
                                        class="btn btn-primary w-100 mt-2 <?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?> visitorMessage <?php endif; ?>">
                                        <?php echo e(__('Update')); ?>

                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                        </form>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('customer.delete')): ?>
                        <hr class="my-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between"
                            style="gap:14px; background:#fff5f5; border:1px solid #fecaca; border-radius:14px; padding:14px 16px;">
                            <div>
                                <div class="text-danger" style="font-weight:800; font-size:14px;"><?php echo e(__('Delete')); ?> — <?php echo e(optional($customer->user)->first_name); ?> <?php echo e(optional($customer->user)->last_name); ?></div>
                                <small class="text-muted d-block mt-1">حذف نهائي: سيتم حذف العميل وكل طلباته وعناوينه وأجهزته. لا يمكن التراجع.</small>
                            </div>
                            <form action="<?php echo e(route('customer.delete', $customer->id)); ?>" method="POST"
                                onsubmit="return confirm('هل أنت متأكد من حذف العميل نهائياً؟ سيتم حذف جميع طلباته وعناوينه.');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-danger d-inline-flex align-items-center gap-2" style="border-radius:10px; font-weight:700;">
                                    <i class="fa fa-trash"></i> <?php echo e(__('Delete')); ?>

                                </button>
                            </form>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        (function () {
            const toggle = document.getElementById('togglePasswordEdit');
            if (!toggle) return;
            const field = document.getElementById('password');
            const icon = document.getElementById('togglePasswordEditIcon');
            const confirm = document.getElementById('password_confirmation');
            toggle.addEventListener('click', function () {
                const show = field.type === 'password';
                field.type = show ? 'text' : 'password';
                if (confirm) confirm.type = show ? 'text' : 'password';
                icon.classList.toggle('fa-eye', !show);
                icon.classList.toggle('fa-eye-slash', show);
            });
        })();
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\customers\edit.blade.php ENDPATH**/ ?>
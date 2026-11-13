<?php $__env->startSection('content'); ?>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="w-100">
                            <h2 class="float-left"><?php echo e(__('Add Customer')); ?></h2>
                            <div class="text-right">
                                <a class="btn btn-light" href="<?php echo e(route('customer.index')); ?>"> <?php echo e(__('Back')); ?> </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form <?php if (\Illuminate\Support\Facades\Blade::check('role', 'root|admin')): ?> <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('customer.store')): ?> action="<?php echo e(route('customer.store')); ?>" <?php endif; ?> <?php endif; ?> method="POST" enctype="multipart/form-data"> <?php echo csrf_field(); ?>
                            <div class="row">
                                <div class="col-12 col-md-6 mb-2">
                                    <label for=""><?php echo e(__('First_Name')); ?> <strong class="text-danger">*</strong></label>
                                    <input type="text" class="form-control" name="first_name"
                                        value="<?php echo e(old('first_name')); ?>" placeholder="<?php echo e(__('First_Name')); ?>">
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
                                    <label for=""><?php echo e(__('Last_Name')); ?> <strong class="text-danger">*</strong></label>
                                    <input type="text" class="form-control" name="last_name"
                                        value="<?php echo e(old('last_name')); ?>" placeholder="<?php echo e(__('Last_Name')); ?>">
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
                                    <label for=""><?php echo e(__('Mobile_number')); ?> <strong class="text-danger">*</strong></label>
                                    <input type="text" class="form-control" name="mobile" value="<?php echo e(old('mobile')); ?>"
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
                                    <label for=""><?php echo e(__('Email')); ?></label>
                                    <input type="email" class="form-control" name="email"
                                        value="<?php echo e(old('email')); ?>" placeholder="<?php echo e(__('Email')); ?>">
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
                                    <label for=""><?php echo e(__('Password')); ?> <strong class="text-danger">*</strong></label>
                                    <div class="d-flex  align-items-center inputBox">
                                        <div class="input w-100 position-relative">
                                            <input type="password" id="password" class="form-control" name="password"
                                                placeholder="******">
                                            <span class="eye" onclick="myFunction()">
                                                <i class="fa fa-eye-slash"></i>
                                            </span>
                                        </div>
                                    </div>
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
                                    <label for=""><?php echo e(__('Confirm_Password')); ?></label>
                                    <div class="d-flex  align-items-center inputBox">
                                        <div class="input w-100 position-relative">
                                            <input type="password" class="form-control" name="password_confirmation"
                                                placeholder="******" id="confirmPassword">
                                            <span class="eye" onclick="confirmPassword()">
                                                <i class="fa fa-eye-slash"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-2 py-2">
                                    <label for=""><?php echo e(__('Profile_Photo')); ?></label>
                                    <input type="file" class="form-control-file" name="profile_photo">
                                </div>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('customer.store')): ?>
                                <div class="col-12 col-md-6 mb-2 py-2">
                                    <label for=""></label>
                                    <button class="btn btn-primary w-100 mt-2  <?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?> visitorMessage <?php endif; ?>"><?php echo e(__('Submit')); ?></button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .eye {
            position: absolute;
            right: 8px;
            top: 11px;
            cursor: pointer;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
    <script>
        function myFunction() {
            var x = document.getElementById("password");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }

        function confirmPassword() {
            var x = document.getElementById("confirmPassword");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\customers\create.blade.php ENDPATH**/ ?>
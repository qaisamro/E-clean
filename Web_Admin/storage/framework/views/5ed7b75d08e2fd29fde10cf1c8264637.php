<?php $__env->startSection('content'); ?>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-lg-12">
                <form <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('notification.send')): ?> action="<?php echo e(route('notification.send')); ?>" <?php endif; ?> method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="card">
                        <div class="card-header bg-primary py-2">
                            <h3 class="card-title m-0 text-white"><?php echo e(__('Send_Notifications')); ?></h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="mb-1"><?php echo e(__('Title')); ?></label>
                                <input name="title" class="form-control" rows="4"
                                    placeholder="<?php echo e(__('Title') . ' ' . __('Notification')); ?>..." />
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <small class="text-danger"><?php echo e($message); ?></small>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="form-group mb-2">
                                <label class="mb-1"><?php echo e(__('Message')); ?></label>
                                <textarea name="message" class="form-control" rows="4"
                                    placeholder="<?php echo e(__('Notification') . ' ' . __('Message')); ?>..."></textarea>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <small class="text-danger"><?php echo e($message); ?></small>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('notification.send')): ?>
                                <div class="d-flex justify-content-end mt-2">
                                    <button type="submit" class="btn btn-primary"><?php echo e(__('Send') . ' ' . __('Message')); ?></button>
                                </div>
                            <?php endif; ?>
                            <hr class="my-3">
                            <div class="d-flex justify-content-end align-items-center">
                                <span class="font-weight-600 mr-1"><?php echo e(__('Device_Type')); ?>:</span>
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle text-capitalize" type="button"
                                        id="triggerId" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                        style="width: 150px">
                                        <?php echo e(__(request()->device_type) ?? __('All')); ?>

                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="triggerId">
                                        <a class="dropdown-item"
                                            href="<?php echo e(route('notification.index', 'device_type=all')); ?>"><?php echo e(__('All')); ?></a>
                                        <a class="dropdown-item"
                                            href="<?php echo e(route('notification.index', 'device_type=android')); ?>"><?php echo e(__('Android')); ?></a>
                                        <a class="dropdown-item"
                                            href="<?php echo e(route('notification.index', 'device_type=ios')); ?>"><?php echo e(__('Ios')); ?></a>
                                    </div>
                                </div>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['customer'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <small class="text-danger"><?php echo e($message); ?></small>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <div class="table-responsive-md mt-2">
                                <table class="table table-bordered table-striped table-hover  notification_table">
                                    <thead>
                                        <tr>
                                            <th class="px-0 text-center" style="width: 42px">
                                                <input type="checkbox" onclick="toggle(this);" />
                                            </th>
                                            <th><?php echo e(__('Name')); ?></th>
                                            <th><?php echo e(__('Phone_number')); ?></th>
                                            <th><?php echo e(__('Email')); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('notification.send')): ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td class="py-2 px-0 text-center">
                                                        <input type="checkbox" name="customer[]" value="<?php echo e($customer->id); ?>">
                                                    </td>
                                                    <td class="py-2"><?php echo e($customer->user->name); ?></td>
                                                    <td class="py-2"><?php echo e($customer->user->mobile); ?></td>
                                                    <td><?php echo e($customer->user->email); ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        function toggle(source) {
            var checkboxes = document.querySelectorAll('input[type="checkbox"]');
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i] != source)
                    checkboxes[i].checked = source.checked;
            }
        }

        $('.notification_table tr').click(function(event) {
            if (event.target.type !== 'checkbox') {
                $(':checkbox', this).trigger('click');
            }
        });

        // function check() {
        //     var checkboxes = document.querySelectorAll('input[type="checkbox"]');
        //     for (var i = 0; i < checkboxes.length; i++) {
        //         checkboxes[i].checked = true;
        //     }
        // }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\notifications\index.blade.php ENDPATH**/ ?>
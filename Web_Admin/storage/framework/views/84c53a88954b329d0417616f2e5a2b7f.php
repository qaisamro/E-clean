

<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header py-2 d-flex align-items-center justify-content-between mb-3">
                    <h2 class="card-title m-0"><?php echo e(__('Setup')); ?> <?php echo e($user->name); ?> <?php echo e(__('Permissions')); ?></h2>
                    <div class="d-flex justify-content-end" >
                        <a href="<?php echo e(route('admin.index')); ?>" class="btn btn-secondary"><?php echo e(__('Back')); ?></a>
                    </div>
                </div>
                <div class="row px-4 pb-4">
                    
                    <div class="col-12 col-sm-6 col-lg-6 col-xl-4 mb-4">
                        <div class="card p-2" style="min-height: 12em;">
                            <h2><?php echo e(__('Services')); ?></h2>
                            <div class="row">
                                <div class="col-6 mb-2"><?php echo e(__('List Show')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('service.index', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-6 mb-2"><?php echo e(__('Create')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('service.create', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-6 mb-2"><?php echo e(__('Store')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('service.store', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-6 mb-2"><?php echo e(__('Edit')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('service.edit', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-6 mb-2"><?php echo e(__('Update')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('service.update', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-6 mb-2"><?php echo e(__('Status Update')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('service.status.toggle', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="submit">
                                <button class="btn btn-danger px-2 py-1" data-toggle="modal" data-target="#serviceModal"><?php echo e(__('Edit Permission')); ?></button>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="serviceModal">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h3 class="modal-title"><?php echo e(__('Edit Service Permissions')); ?></h3>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>

                                        <div class="modal-body pt-0">
                                            <form action="<?php echo e(route('admin.set-permission', $user->id)); ?>" method="POST"> <?php echo csrf_field(); ?>
                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'List Show','user' => $user,'name' => 'service_index','permission' => 'service.index'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Create','user' => $user,'name' => 'service_create','permission' => 'service.create'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Store','user' => $user,'name' => 'service_store','permission' => 'service.store'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Edit','user' => $user,'name' => 'service_edit','permission' => 'service.edit'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Update','user' => $user,'name' => 'service_update','permission' => 'service.update'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Status Update','user' => $user,'name' => 'service_status_toggle','permission' => 'service.status.toggle'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <div class="text-right mt-3">
                                                    <button class="btn btn-secondary" type="button" data-dismiss="modal"><?php echo e(__('Cancel')); ?></button>
                                                    <button class="btn btn-danger <?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?> visitorMessage <?php endif; ?>" type="submit"><?php echo e(__('Save Permissions')); ?></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-12 col-sm-6 col-lg-6 col-xl-4 mb-4">
                        <div class="card p-2" style="min-height: 12em;">
                            <h2><?php echo e(__('Additional_Services')); ?></h2>
                            <div class="row">
                                <div class="col-6 mb-2"><?php echo e(__('List Show')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('additional.index', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-6 mb-2"><?php echo e(__('Create')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('additional.create', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-6 mb-2"><?php echo e(__('Store')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('additional.store', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-6 mb-2"><?php echo e(__('Edit')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('additional.edit', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-6 mb-2"><?php echo e(__('Update')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('additional.update', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-6 mb-2"><?php echo e(__('Status Update')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('additional.status.toggle', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="submit">
                                <button class="btn btn-danger px-2 py-1" data-toggle="modal" data-target="#additionalModal"><?php echo e(__('Edit Permission')); ?></button>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="additionalModal">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h3 class="modal-title"><?php echo e(__('Edit Additional Service Permissions')); ?></h3>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>

                                        <div class="modal-body pt-0">
                                            <form action="<?php echo e(route('admin.set-permission', $user->id)); ?>" method="POST"> <?php echo csrf_field(); ?>
                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'List Show','user' => $user,'name' => 'additional_index','permission' => 'additional.index'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Create','user' => $user,'name' => 'additional_create','permission' => 'additional.create'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Store','user' => $user,'name' => 'additional_store','permission' => 'additional.store'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Edit','user' => $user,'name' => 'additional_edit','permission' => 'additional.edit'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Update','user' => $user,'name' => 'additional_update','permission' => 'additional.update'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Status Update','user' => $user,'name' => 'additional_status_toggle','permission' => 'additional.status.toggle'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <div class="text-right mt-3">
                                                    <button class="btn btn-secondary" type="button" data-dismiss="modal"><?php echo e(__('Cancel')); ?></button>
                                                    <button class="btn btn-danger <?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?> visitorMessage <?php endif; ?>" type="submit"><?php echo e(__('Save Permissions')); ?></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-12 col-sm-6 col-lg-6 col-xl-4 mb-4">
                        <div class="card p-2" style="min-height: 12em;">
                            <h2><?php echo e(__('Notifications')); ?></h2>
                            <div class="row">
                                <div class="col-12 mb-2"><?php echo e(__('Notification Panel')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('notification.index', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-12 mb-2"><?php echo e(__('Send Notification')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('notification.send', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="submit">
                                <button class="btn btn-danger px-2 py-1" data-toggle="modal" data-target="#notificationModal"><?php echo e(__('Edit Permission')); ?></button>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="notificationModal">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h3 class="modal-title"><?php echo e(__('Edit Notifications Permissions')); ?></h3>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>

                                        <div class="modal-body pt-0">
                                            <form action="<?php echo e(route('admin.set-permission', $user->id)); ?>" method="POST"> <?php echo csrf_field(); ?>
                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Notification Panel','user' => $user,'name' => 'notification_index','permission' => 'notification.index'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Send Notification','user' => $user,'name' => 'notification_send','permission' => 'notification.send'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <div class="text-right mt-3">
                                                    <button class="btn btn-secondary" type="button" data-dismiss="modal"><?php echo e(__('Cancel')); ?></button>
                                                    <button class="btn btn-danger <?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?> visitorMessage <?php endif; ?>" type="submit"><?php echo e(__('Save Permissions')); ?></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-12 col-sm-6 col-lg-6 col-xl-4 mb-4">
                        <div class="card p-2" style="min-height: 12em;">
                            <h2><?php echo e(__('Customers')); ?></h2>
                            <div class="row">
                                <div class="col-6 mb-2"><?php echo e(__('List Show')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('customer.index', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-6 mb-2"><?php echo e(__('Create')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('customer.create', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-6 mb-2"><?php echo e(__('Show')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('customer.show', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-6 mb-2"><?php echo e(__('Store')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('customer.store', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-6 mb-2"><?php echo e(__('Edit')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('customer.edit', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-6 mb-2"><?php echo e(__('Update')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('customer.update', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="submit">
                                <button class="btn btn-danger px-2 py-1" data-toggle="modal" data-target="#customerModal"><?php echo e(__('Edit Permission')); ?></button>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="customerModal">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h3 class="modal-title"><?php echo e(__('Edit Customer Permissions')); ?></h3>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>

                                        <div class="modal-body pt-0">
                                            <form action="<?php echo e(route('admin.set-permission', $user->id)); ?>" method="POST"> <?php echo csrf_field(); ?>
                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'List Show','user' => $user,'name' => 'customer_index','permission' => 'customer.index'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Create','user' => $user,'name' => 'customer_create','permission' => 'customer.create'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Show','user' => $user,'name' => 'customer_show','permission' => 'customer.show'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Store','user' => $user,'name' => 'customer_store','permission' => 'customer.store'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Edit','user' => $user,'name' => 'customer_edit','permission' => 'customer.edit'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Update','user' => $user,'name' => 'customer_update','permission' => 'customer.update'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <div class="text-right mt-3">
                                                    <button class="btn btn-secondary" type="button" data-dismiss="modal"><?php echo e(__('Cancel')); ?></button>
                                                    <button class="btn btn-danger <?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?> visitorMessage <?php endif; ?>" type="submit"><?php echo e(__('Save Permissions')); ?></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-12 col-sm-6 col-lg-6 col-xl-4 mb-4">
                        <div class="card p-2" style="min-height: 12em;">
                            <h2><?php echo e(__('Products')); ?></h2>
                            <div class="row">
                                <div class="col-6 mb-2"><?php echo e(__('List Show')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('product.index', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-6 mb-2"><?php echo e(__('Create')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('product.create', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-6 mb-2"><?php echo e(__('Store')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('product.store', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-6 mb-2"><?php echo e(__('Edit')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('product.edit', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-6 mb-2"><?php echo e(__('Update')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('product.update', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-6 mb-2"><?php echo e(__('Status Update')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('product.status.toggle', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="submit">
                                <button class="btn btn-danger px-2 py-1" data-toggle="modal" data-target="#productModal"><?php echo e(__('Edit Permission')); ?></button>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="productModal">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h3 class="modal-title"><?php echo e(__('Edit Products Permissions')); ?></h3>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>

                                        <div class="modal-body pt-0">
                                            <form action="<?php echo e(route('admin.set-permission', $user->id)); ?>" method="POST"> <?php echo csrf_field(); ?>
                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'List Show','user' => $user,'name' => 'product_index','permission' => 'product.index'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Create','user' => $user,'name' => 'product_create','permission' => 'product.create'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Store','user' => $user,'name' => 'product_store','permission' => 'product.store'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Edit','user' => $user,'name' => 'product_edit','permission' => 'product.edit'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Update','user' => $user,'name' => 'product_update','permission' => 'product.update'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Status Update','user' => $user,'name' => 'product_status_toggle','permission' => 'product.status.toggle'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <div class="text-right mt-3">
                                                    <button class="btn btn-secondary" type="button" data-dismiss="modal"><?php echo e(__('Cancel')); ?></button>
                                                    <button class="btn btn-danger <?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?> visitorMessage <?php endif; ?>" type="submit"><?php echo e(__('Save Permissions')); ?></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-12 col-sm-6 col-lg-6 col-xl-4 mb-4">
                        <div class="card p-2" style="min-height: 12em;">
                            <h2><?php echo e(__('Banners')); ?></h2>
                            <div class="row">
                                <div class="col-6 mb-2"><?php echo e(__('List Show')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('banner.promotional', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-6 mb-2"><?php echo e(__('Store')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('banner.store', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-6 mb-2"><?php echo e(__('Edit')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('banner.edit', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-6 mb-2"><?php echo e(__('Update')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('banner.update', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-6 mb-2"><?php echo e(__('Delete')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('banner.destroy', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-6 mb-2"><?php echo e(__('Status Update')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('banner.status.toggle', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="submit">
                                <button class="btn btn-danger px-2 py-1" data-toggle="modal" data-target="#bannerModal"><?php echo e(__('Edit Permission')); ?></button>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="bannerModal">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h3 class="modal-title"><?php echo e(__('Edit Banner Permissions')); ?></h3>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>

                                        <div class="modal-body pt-0">
                                            <form action="<?php echo e(route('admin.set-permission', $user->id)); ?>" method="POST"> <?php echo csrf_field(); ?>
                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'List Show','user' => $user,'name' => 'banner_promotional','permission' => 'banner.promotional'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Store','user' => $user,'name' => 'banner_store','permission' => 'banner.store'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Edit','user' => $user,'name' => 'banner_edit','permission' => 'banner.edit'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Update','user' => $user,'name' => 'banner_update','permission' => 'banner.update'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Delete','user' => $user,'name' => 'banner_destroy','permission' => 'banner.destroy'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Status Update','user' => $user,'name' => 'banner_status_toggle','permission' => 'banner.status.toggle'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <div class="text-right mt-3">
                                                    <button class="btn btn-secondary" type="button" data-dismiss="modal"><?php echo e(__('Cancel')); ?></button>
                                                    <button class="btn btn-danger <?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?> visitorMessage <?php endif; ?>" type="submit"><?php echo e(__('Save Permissions')); ?></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-12 col-sm-6 col-lg-6 col-xl-4 mb-4">
                        <div class="card p-2" style="min-height: 12em;">
                            <h2><?php echo e(__('Orders')); ?></h2>
                            <div class="row">
                                <div class="col-6 mb-2"><?php echo e(__('All Orders')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('order.index', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-6 mb-2"><?php echo e(__('Order Details')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('order.show', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-12 mb-2"><?php echo e(__('Change Order Status')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('order.status.change', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-6 mb-2"><?php echo e(__('Print Labels')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('order.print.labels', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-6 mb-2"><?php echo e(__('Print Invoice')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('order.print.invioce', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="submit">
                                <button class="btn btn-danger px-2 py-1" data-toggle="modal" data-target="#orderModal"><?php echo e(__('Edit Permission')); ?></button>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="orderModal">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h3 class="modal-title"><?php echo e(__('Edit order Permissions')); ?></h3>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>

                                        <div class="modal-body pt-0">
                                            <form action="<?php echo e(route('admin.set-permission', $user->id)); ?>" method="POST"> <?php echo csrf_field(); ?>
                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'All Orders','user' => $user,'name' => 'order_index','permission' => 'order.index'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Order Details','user' => $user,'name' => 'order_show','permission' => 'order.show'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Change Order Status','user' => $user,'name' => 'order_status_change','permission' => 'order.status.change'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Print Labels','user' => $user,'name' => 'order_print_labels','permission' => 'order.print.labels'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Print Invoice','user' => $user,'name' => 'order_print_invioce','permission' => 'order.print.invioce'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <div class="text-right mt-3">
                                                    <button class="btn btn-secondary" type="button" data-dismiss="modal"><?php echo e(__('Cancel')); ?></button>
                                                    <button class="btn btn-danger <?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?> visitorMessage <?php endif; ?>" type="submit"><?php echo e(__('Save Permissions')); ?></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    

                    
                    <div class="col-12 col-sm-6 col-lg-6 col-xl-4 mb-4">
                        <div class="card p-2" style="min-height: 12em;">
                            <h2><?php echo e(__('Coupons')); ?></h2>
                            <div class="row">
                                <div class="col-6 mb-2"><?php echo e(__('List Show')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('coupon.index', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-6 mb-2"><?php echo e(__('Create')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('coupon.create', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-6 mb-2"><?php echo e(__('Store')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('coupon.store', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-6 mb-2"><?php echo e(__('Edit')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('coupon.edit', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-6 mb-2"><?php echo e(__('Update')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('coupon.update', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="submit">
                                <button class="btn btn-danger px-2 py-1" data-toggle="modal" data-target="#couponModal"><?php echo e(__('Edit Permission')); ?></button>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="couponModal">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h3 class="modal-title"><?php echo e(__('Edit Coupons Permissions')); ?></h3>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>

                                        <div class="modal-body pt-0">
                                            <form action="<?php echo e(route('admin.set-permission', $user->id)); ?>" method="POST"> <?php echo csrf_field(); ?>
                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'List Show','user' => $user,'name' => 'coupon_index','permission' => 'coupon.index'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Create','user' => $user,'name' => 'coupon_create','permission' => 'coupon.create'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Store','user' => $user,'name' => 'coupon_store','permission' => 'coupon.store'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Edit','user' => $user,'name' => 'coupon_edit','permission' => 'coupon.edit'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Update','user' => $user,'name' => 'coupon_update','permission' => 'coupon.update'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <div class="text-right mt-3">
                                                    <button class="btn btn-secondary" type="button" data-dismiss="modal"><?php echo e(__('Cancel')); ?></button>
                                                    <button class="btn btn-danger <?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?> visitorMessage <?php endif; ?>" type="submit"><?php echo e(__('Save Permissions')); ?></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-12 col-sm-6 col-lg-6 col-xl-4 mb-4">
                        <div class="card p-2" style="min-height: 12em;">
                            <h2>إدارة المحلات</h2>
                            <div class="row">
                                <div class="col-6 mb-2"><?php echo e(__('List Show')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('vendors.index', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="submit">
                                <button class="btn btn-danger px-2 py-1" data-toggle="modal" data-target="#vendorsModal"><?php echo e(__('Edit Permission')); ?></button>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="vendorsModal">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h3 class="modal-title">تعديل صلاحيات إدارة المحلات</h3>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>

                                        <div class="modal-body pt-0">
                                            <form action="<?php echo e(route('admin.set-permission', $user->id)); ?>" method="POST"> <?php echo csrf_field(); ?>
                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'List Show','user' => $user,'name' => 'vendors_index','permission' => 'vendors.index'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <div class="text-right mt-3">
                                                    <button class="btn btn-secondary" type="button" data-dismiss="modal"><?php echo e(__('Cancel')); ?></button>
                                                    <button class="btn btn-danger <?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?> visitorMessage <?php endif; ?>" type="submit"><?php echo e(__('Save Permissions')); ?></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-12 col-sm-6 col-lg-6 col-xl-4 mb-4">
                        <div class="card p-2" style="min-height: 12em;">
                            <h2>الصفحات والسياسات</h2>
                            <div class="row">
                                <div class="col-6 mb-2"><?php echo e(__('List Show')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('web.setting', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="submit">
                                <button class="btn btn-danger px-2 py-1" data-toggle="modal" data-target="#sitePagesModal"><?php echo e(__('Edit Permission')); ?></button>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="sitePagesModal">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h3 class="modal-title">تعديل صلاحيات الصفحات والسياسات</h3>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>

                                        <div class="modal-body pt-0">
                                            <form action="<?php echo e(route('admin.set-permission', $user->id)); ?>" method="POST"> <?php echo csrf_field(); ?>
                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'List Show','user' => $user,'name' => 'web_setting','permission' => 'web.setting'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <div class="text-right mt-3">
                                                    <button class="btn btn-secondary" type="button" data-dismiss="modal"><?php echo e(__('Cancel')); ?></button>
                                                    <button class="btn btn-danger <?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?> visitorMessage <?php endif; ?>" type="submit"><?php echo e(__('Save Permissions')); ?></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    

                    
                    <div class="col-12 col-sm-6 col-lg-6 col-xl-4 mb-4">
                        <div class="card p-2" style="min-height: 12em;">
                            <h2><?php echo e(__('Profile')); ?></h2>
                            <div class="row">
                                <div class="col-6 mb-2"><?php echo e(__('View Profile')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('profile.index', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-6 mb-2"><?php echo e(__('Edit')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('profile.edit', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-6 mb-2"><?php echo e(__('Update')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('profile.update', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-12 mb-2"><?php echo e(__('Change_Password')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('profile.change-password', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="submit">
                                <button class="btn btn-danger px-2 py-1" data-toggle="modal" data-target="#profileModal"><?php echo e(__('Edit Permission')); ?></button>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="profileModal">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h3 class="modal-title"><?php echo e(__('Edit Profile Permissions')); ?></h3>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>

                                        <div class="modal-body pt-0">
                                            <form action="<?php echo e(route('admin.set-permission', $user->id)); ?>" method="POST"> <?php echo csrf_field(); ?>
                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'View Profile','user' => $user,'name' => 'profile_index','permission' => 'profile.index'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Edit','user' => $user,'name' => 'profile_edit','permission' => 'profile.edit'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Update','user' => $user,'name' => 'profile_update','permission' => 'profile.update'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Change Password','user' => $user,'name' => 'profile_change-password','permission' => 'profile.change-password'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <div class="text-right mt-3">
                                                    <button class="btn btn-secondary" type="button" data-dismiss="modal"><?php echo e(__('Cancel')); ?></button>
                                                    <button class="btn btn-danger <?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?> visitorMessage <?php endif; ?>" type="submit"><?php echo e(__('Save Permissions')); ?></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-12 col-sm-6 col-lg-6 col-xl-4 mb-4">
                        <div class="card p-2" style="min-height: 12em;">
                            <h2><?php echo e(__('Dashboard')); ?></h2>
                            <div class="row">
                                <div class="col-12 mb-2"><?php echo e(__('Calculations')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('dashboard.calculation', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-12 mb-2"><?php echo e(__('Show Revenue')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('dashboard.revenue', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-12 mb-2"><?php echo e(__('Show Overview')); ?>:
                                    <div class="text-right float-right">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array('dashboard.overview', $permissions)): ?>
                                        <span class="badge py-1 badge-success ml-2"><?php echo e(__('Yes')); ?></span>
                                        <?php else: ?>
                                        <span class="badge py-1 badge-danger ml-2"><?php echo e(__('No')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="submit">
                                <button class="btn btn-danger px-2 py-1" data-toggle="modal" data-target="#dashboardModal"><?php echo e(__('Edit Permission')); ?></button>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="dashboardModal">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                        <h3 class="modal-title"><?php echo e(__('Edit Dashboard Permissions')); ?></h3>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        </div>

                                        <div class="modal-body pt-0">
                                            <form action="<?php echo e(route('admin.set-permission', $user->id)); ?>" method="POST"> <?php echo csrf_field(); ?>
                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Calculations','user' => $user,'name' => 'dashboard_calculation','permission' => 'dashboard.calculation'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Show Revenue','user' => $user,'name' => 'dashboard_revenue','permission' => 'dashboard.revenue'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <?php if (isset($component)) { $__componentOriginal9f72a82b6c0c712f66dae8734831b53a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a = $attributes; } ?>
<?php $component = App\View\Components\PermissionInput::resolve(['title' => 'Show Overview','user' => $user,'name' => 'dashboard_overview','permission' => 'dashboard.overview'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('permission-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\PermissionInput::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $attributes = $__attributesOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__attributesOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a)): ?>
<?php $component = $__componentOriginal9f72a82b6c0c712f66dae8734831b53a; ?>
<?php unset($__componentOriginal9f72a82b6c0c712f66dae8734831b53a); ?>
<?php endif; ?>

                                                <div class="text-right mt-3">
                                                    <button class="btn btn-secondary" type="button" data-dismiss="modal"><?php echo e(__('Cancel')); ?></button>
                                                    <button class="btn btn-danger <?php if (\Illuminate\Support\Facades\Blade::check('role', 'visitor')): ?> visitorMessage <?php endif; ?>" type="submit"><?php echo e(__('Save Permissions')); ?></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .submit{
        position: absolute;
        right: 10px;
        bottom: 10px;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\root\admin\show.blade.php ENDPATH**/ ?>
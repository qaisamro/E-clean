

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="header pt-5">

            <div class="header-body mt--4">
                <div class="row align-items-center pb-4">
                    <div class="col-lg-6 col-7">
                        <h6 class="h2 d-inline-block"><?php echo e(__('Dashboard')); ?></h6>
                        <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                                <li class="breadcrumb-item "><a href="<?php echo e(route('root')); ?>"><i
                                            class="fa fa-home text-primary"></i></a></li>
                                <li class="breadcrumb-item active" aria-current="page"> <?php echo e(__('Dashboard')); ?> </li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!-- Card stats (Income removed — three cards, each opens its data modal) -->
                <div class="row">
                    <div class="col-xl-4 col-lg-6">
                        <button type="button" class="card card-stats mb-4 mb-xl-0 ec-stat-card w-100 border-0 text-start"
                            data-toggle="modal" data-target="#usersModal"
                            aria-label="<?php echo e(__('Users')); ?>">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col mt-3 text-right">
                                        <h4 class="card-title text-uppercase text-muted mb-0 d-flex align-items-center justify-content-end"><?php echo e(__('Users')); ?>

                                            <i class="fas fa-external-link-alt ms-2"></i>
                                        </h4>
                                        <span class="display-3 text-dark font-weight-bold mb-0">
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('dashboard.calculation')): ?>
                                                <?php echo e($customers->count()); ?>

                                            <?php else: ?>
                                                00
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <div class="card-icon">
                                        <div class="icon icon-shape text-white shadow">
                                            <img width="80" src="<?php echo e(asset('images/icons/user.svg')); ?>" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </button>
                    </div>

                    <div class="col-xl-4 col-lg-6">
                        <button type="button" class="card card-stats mb-4 mb-xl-0 ec-stat-card w-100 border-0 text-start"
                            data-toggle="modal" data-target="#productsModal"
                            aria-label="<?php echo e(__('Products')); ?>">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col mt-3 text-right">
                                        <h4 class="card-title text-uppercase text-muted mb-0 d-flex align-items-center justify-content-end"><?php echo e(__('Products')); ?>

                                            <i class="fas fa-external-link-alt ms-2"></i>
                                        </h4>
                                        <span class="display-3 text-dark font-weight-bold mb-0">
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('dashboard.calculation')): ?>
                                                <?php echo e($products->count()); ?>

                                            <?php else: ?>
                                                00
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <div class="card-icon">
                                        <div class="icon icon-shape text-white shadow">
                                            <img width="80" src="<?php echo e(asset('images/icons/items.svg')); ?>" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </button>
                    </div>

                    <div class="col-xl-4 col-lg-6">
                        <button type="button" class="card card-stats mb-4 mb-xl-0 ec-stat-card w-100 border-0 text-start"
                            data-toggle="modal" data-target="#servicesModal"
                            aria-label="<?php echo e(__('Services')); ?>">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col mt-3 text-right">
                                        <h4 class="card-title text-uppercase text-muted mb-0 d-flex align-items-center justify-content-end"><?php echo e(__('Services')); ?>

                                            <i class="fas fa-external-link-alt ms-2"></i>
                                        </h4>
                                        <span class="display-3 text-dark font-weight-bold mb-0">
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('dashboard.calculation')): ?>
                                                <?php echo e($services->count()); ?>

                                            <?php else: ?>
                                                00
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <div class="card-icon">
                                        <div class="icon icon-shape text-white shadow">
                                            <img width="80" src="<?php echo e(asset('images/icons/services.svg')); ?>"
                                                alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card ec-overview" style="border-radius: 10px; border-bottom: 4px solid var(--theme-color);">

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('dashboard.overview')): ?>
                        <div class="row p-3">
                            <div class="col-lg-6 col-4 mb-3">
                                <span class="ec-stat-ic float-left mr-2" style="--ic:#028090"><i class="fas fa-users"></i></span>
                                <div>
                                    <h3 class="m-0 text-dark"><?php echo e($customers->count()); ?></h3>
                                    <span class="txt-1"><?php echo e(__('Users')); ?></span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-4 mb-3">
                                <span class="ec-stat-ic float-left mr-2" style="--ic:#156172"><i class="fas fa-shopping-cart"></i></span>
                                <div>
                                    <h3 class="m-0 text-dark"><?php echo e($confirmOrder); ?></h3>
                                    <span class="txt-1"><?php echo e(__('Orders')); ?></span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-4 mb-3">
                                <span class="ec-stat-ic float-left mr-2" style="--ic:#F59E0B"><i class="fas fa-hourglass-half"></i></span>
                                <div>
                                    <h3 class="m-0 text-dark"><?php echo e($pendingOrder); ?></h3>
                                    <span class="txt-1"><?php echo e(__('Pending')); ?></span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-4 mb-3">
                                <span class="ec-stat-ic float-left mr-2" style="--ic:#00A896"><i class="fas fa-sync-alt"></i></span>
                                <div>
                                    <h3 class="m-0 text-dark"><?php echo e($onPregressOrder); ?></h3>
                                    <span class="txt-1"><?php echo e(__('On_progress')); ?></span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-4">
                                <span class="ec-stat-ic float-left mr-2" style="--ic:#16A34A"><i class="fas fa-check-circle"></i></span>
                                <div>
                                    <h3 class="m-0 text-dark"><?php echo e($completeOrder); ?></h3>
                                    <span class="txt-1"><?php echo e(__('Delivered')); ?></span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-4 ">
                                <span class="ec-stat-ic float-left mr-2" style="--ic:#E63946"><i class="fas fa-ban"></i></span>
                                <div>
                                    <h3 class="m-0 text-dark"><?php echo e($cancelledOrder); ?></h3>
                                    <span class="txt-1"><?php echo e(__('Cancel_Order')); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="row p-3">
                            <div class="col-lg-6 col-4 mb-3">
                                <span class="ec-stat-ic float-left mr-2" style="--ic:#028090"><i class="fas fa-users"></i></span>
                                <div>
                                    <h3 class="m-0 text-dark">00</h3>
                                    <span class="txt-1"><?php echo e(__('Users')); ?></span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-4 mb-3">
                                <span class="ec-stat-ic float-left mr-2" style="--ic:#156172"><i class="fas fa-shopping-cart"></i></span>
                                <div>
                                    <h3 class="m-0 text-dark">00</h3>
                                    <span class="txt-1"><?php echo e(__('Orders')); ?></span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-4 mb-3">
                                <span class="ec-stat-ic float-left mr-2" style="--ic:#F59E0B"><i class="fas fa-hourglass-half"></i></span>
                                <div>
                                    <h3 class="m-0 text-dark">00</h3>
                                    <span class="txt-1"><?php echo e(__('Pending')); ?></span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-4 mb-3">
                                <span class="ec-stat-ic float-left mr-2" style="--ic:#00A896"><i class="fas fa-sync-alt"></i></span>
                                <div>
                                    <h3 class="m-0 text-dark">00</h3>
                                    <span class="txt-1"><?php echo e(__('On_progress')); ?></span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-4">
                                <span class="ec-stat-ic float-left mr-2" style="--ic:#16A34A"><i class="fas fa-check-circle"></i></span>
                                <div>
                                    <h3 class="m-0 text-dark">00</h3>
                                    <span class="txt-1"><?php echo e(__('Delivered')); ?></span>
                                </div>
                            </div>
                            <div class="col-lg-6 col-4 ">
                                <span class="ec-stat-ic float-left mr-2" style="--ic:#E63946"><i class="fas fa-ban"></i></span>
                                <div>
                                    <h3 class="m-0 text-dark">00</h3>
                                    <span class="txt-1"><?php echo e(__('Cancel_Order')); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="usersModal" tabindex="-1" role="dialog" aria-labelledby="usersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <div class="d-flex align-items-center w-100">
                        <span class="ec-modal-icon" style="--ic:#028090"><i class="fas fa-users"></i></span>
                        <div>
                            <h5 class="modal-title mb-0 font-weight-bold" id="usersModalLabel"><?php echo e(__('Users')); ?></h5>
                            <small class="text-muted"><?php echo e($customers->count()); ?> سجل</small>
                        </div>
                        <button type="button" class="close ms-auto" data-dismiss="modal" aria-label="<?php echo e(__('Close')); ?>">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="modal-body pt-3">
                    <div class="ec-modal-scroll table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th style="width:52px">#</th>
                                    <th><?php echo e(__('Name')); ?></th>
                                    <th><?php echo e(__('Mobile')); ?></th>
                                    <th><?php echo e(__('Email')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('dashboard.calculation')): ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($index + 1); ?></td>
                                            <td><?php echo e(optional($customer->user)->first_name); ?> <?php echo e(optional($customer->user)->last_name); ?></td>
                                            <td dir="ltr"><?php echo e(optional($customer->user)->mobile ?? '—'); ?></td>
                                            <td dir="ltr"><?php echo e(optional($customer->user)->email ?? '—'); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr><td colspan="4" class="text-center text-muted py-4">لا توجد بيانات</td></tr>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center text-muted py-4">لا تملك صلاحية عرض البيانات</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-dismiss="modal"><?php echo e(__('Close')); ?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="productsModal" tabindex="-1" role="dialog" aria-labelledby="productsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <div class="d-flex align-items-center w-100">
                        <span class="ec-modal-icon" style="--ic:#156172"><i class="fas fa-box-open"></i></span>
                        <div>
                            <h5 class="modal-title mb-0 font-weight-bold" id="productsModalLabel"><?php echo e(__('Products')); ?></h5>
                            <small class="text-muted"><?php echo e($products->count()); ?> سجل</small>
                        </div>
                        <button type="button" class="close ms-auto" data-dismiss="modal" aria-label="<?php echo e(__('Close')); ?>">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="modal-body pt-3">
                    <div class="ec-modal-scroll table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th style="width:52px">#</th>
                                    <th><?php echo e(__('Product_name')); ?></th>
                                    <th><?php echo e(__('Service')); ?></th>
                                    <th><?php echo e(__('Price')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('dashboard.calculation')): ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($index + 1); ?></td>
                                            <td><?php echo e($product->name); ?></td>
                                            <td><?php echo e($product->service?->name ?? '—'); ?></td>
                                            <td><?php echo e(currencyPosition($product->discount_price ?: $product->price)); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr><td colspan="4" class="text-center text-muted py-4">لا توجد بيانات</td></tr>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center text-muted py-4">لا تملك صلاحية عرض البيانات</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-dismiss="modal"><?php echo e(__('Close')); ?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="servicesModal" tabindex="-1" role="dialog" aria-labelledby="servicesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <div class="d-flex align-items-center w-100">
                        <span class="ec-modal-icon" style="--ic:#F59E0B"><i class="fas fa-concierge-bell"></i></span>
                        <div>
                            <h5 class="modal-title mb-0 font-weight-bold" id="servicesModalLabel"><?php echo e(__('Services')); ?></h5>
                            <small class="text-muted"><?php echo e($services->count()); ?> سجل</small>
                        </div>
                        <button type="button" class="close ms-auto" data-dismiss="modal" aria-label="<?php echo e(__('Close')); ?>">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="modal-body pt-3">
                    <div class="ec-modal-scroll table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th style="width:52px">#</th>
                                    <th><?php echo e(__('Service_Name')); ?></th>
                                    <th><?php echo e(__('Description')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('dashboard.calculation')): ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($index + 1); ?></td>
                                            <td><?php echo e($service->name); ?></td>
                                            <td><?php echo e(\Illuminate\Support\Str::limit($service->description ?? '—', 90)); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr><td colspan="3" class="text-center text-muted py-4">لا توجد بيانات</td></tr>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center text-muted py-4">لا تملك صلاحية عرض البيانات</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-dismiss="modal"><?php echo e(__('Close')); ?></button>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('styles'); ?>
        <style>
            .ec-stat-card { cursor: pointer; border-radius: 16px; overflow: hidden; text-decoration: none !important; transition: transform .18s ease, box-shadow .18s ease; }
            .ec-stat-card:hover { transform: translateY(-3px); box-shadow: 0 1.5rem 2rem rgba(2, 128, 144, .18) !important; }
            .ec-stat-card:focus-visible { outline: 3px solid #028090; outline-offset: 2px; }
            .ec-modal-icon { --ic:#028090; flex: 0 0 auto; width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, var(--ic), #083744); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 19px; margin-inline-end: 12px; box-shadow: 0 6px 14px rgba(2, 128, 144, .35); }
            .ec-modal-scroll { max-height: 60vh; overflow-y: auto; }
        </style>
    <?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\dashboard\index.blade.php ENDPATH**/ ?>
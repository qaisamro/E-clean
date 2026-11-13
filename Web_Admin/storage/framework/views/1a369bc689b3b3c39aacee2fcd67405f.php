<?php $__env->startSection('content'); ?>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center py-2 justify-content-between">
                        <h2 class="card-title m-0"><?php echo e(__('All Customers')); ?></h2>
                        <div>
                            <form action="<?php echo e(route('customer.index')); ?>" method="GET">
                                <ul class=" nav d-flex justify-content-end">
                                    <li class="nav-item ml-2 mr-md-0">
                                        <input type="text" name='search' placeholder="<?php echo e(__("Search")); ?>"
                                            value="<?php echo e(request('search')); ?>" class="form-control" />
                                    </li>
                                    <li class="nav-item ml-2 mr-md-0">
                                        <button type="submit" class="btn btn-info">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </li>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('customer.create')): ?>
                                    <li class="nav-item ml-2 mr-md-0">
                                        <a href="<?php echo e(route('customer.create')); ?>" class="btn btn-primary">
                                            <i class="fa fa-plus"></i> <?php echo e(__('Add Customer')); ?>

                                        </a>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </form>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped <?php echo e(session()->get('local')); ?>" id="myTable">
                                <thead>
                                    <tr>
                                        <th scope="col"><?php echo e(__('Name')); ?></th>
                                        <th scope="col"><?php echo e(__('Mobile')); ?></th>
                                        <th scope="col"><?php echo e(__('Address')); ?></th>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['customer.show', 'customer.edit'])): ?>
                                        <th scope="col"><?php echo e(__('Action')); ?></th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                             <td><?php echo e(optional($customer->user)->first_name); ?> <?php echo e(optional($customer->user)->last_name); ?></td>
                                                <td><?php echo e(optional($customer->user)->mobile); ?></td>
                                                <td><?php echo e(optional($customer->addresses->first())->address_line ?? '—'); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(optional($customer->addresses->first())->area): ?> , <?php echo e(optional($customer->addresses->first())->area); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></td>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['customer.show', 'customer.edit', 'customer.delete'])): ?>
                                            <td>
                                                <a href="<?php echo e(route('customer.show', $customer->id)); ?>"
                                                    class="btn btn-primary py-1 px-2" title="عرض">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="<?php echo e(route('customer.edit', $customer->id)); ?>"
                                                    class="btn btn-info py-1 px-2" title="تعديل">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('customer.delete')): ?>
                                                <form action="<?php echo e(route('customer.delete', $customer->id)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف العميل نهائياً؟ سيتم حذف جميع طلباته وعناوينه.');">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-danger py-1 px-2" title="حذف نهائي">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        td {
            padding: 5px 10px !important;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
    <script>
        $('.delete-confirm').on('click', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لن تتمكن من التراجع عن هذا الإجراء!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#00B894',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، احذفه!',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            })
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\customers\index.blade.php ENDPATH**/ ?>
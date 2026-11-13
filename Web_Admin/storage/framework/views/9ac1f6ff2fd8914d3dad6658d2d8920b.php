<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="m-0">إدارة المحلات (Vendors)</h2>
            <a href="<?php echo e(route('vendors.create')); ?>" class="btn btn-primary"><i class="fa fa-plus"></i> إضافة متجر</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="myTable">
                    <thead>
                        <tr>
                            <th>الشعار</th>
                            <th>اسم المحل</th>
                            <th>العنوان</th>
                            <th>المدينة</th>
                            <th>رقم الجوال (للدخول)</th>
                            <th>الحالة</th>
                            <th>«شركاؤنا الموثوقون» بالتطبيق</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><img src="<?php echo e($vendor->logoPath); ?>" width="60" height="60" style="object-fit:cover;border-radius:8px"></td>
                            <td><?php echo e($vendor->name); ?></td>
                            <td><?php echo e($vendor->address); ?></td>
                            <td><?php echo e($vendor->city ?: '—'); ?></td>
                            <td><span class="badge badge-light" style="font-size:14px"><?php echo e($vendor->phone); ?></span><br><small class="text-muted">الدخول به</small></td>
                            <td>
                                <form action="<?php echo e(route('vendors.toggle', $vendor->id)); ?>" method="POST" style="display:inline">
                                    <?php echo csrf_field(); ?>
                                    <label class="switch"><input type="checkbox" <?php echo e($vendor->is_active?'checked':''); ?> onchange="this.form.submit()"><span class="slider round"></span></label>
                                </form>
                            </td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! \App\Support\VendorAccount::isStoreStaff(auth()->user())): ?>
                                    <form action="<?php echo e(route('vendors.togglePartner', $vendor->id)); ?>" method="POST" style="display:inline">
                                        <?php echo csrf_field(); ?>
                                        <label class="switch" title="إظهار/إخفاء من قسم «شركاؤنا الموثوقون» في التطبيق">
                                            <input type="checkbox" <?php echo e($vendor->show_in_partners?'checked':''); ?> onchange="this.form.submit()"><span class="slider round"></span>
                                        </label>
                                    </form>
                                <?php else: ?>
                                    <span class="badge <?php echo e($vendor->show_in_partners ? 'badge-success' : 'badge-secondary'); ?>"><?php echo e($vendor->show_in_partners ? 'يظهر' : 'مخفي'); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <a href="<?php echo e(route('vendors.edit', $vendor->id)); ?>" class="btn btn-sm btn-warning" title="تعديل"><i class="fa fa-edit"></i></a>
                                    <form action="<?php echo e(route('vendors.destroy', $vendor->id)); ?>" method="POST" class="d-inline vendor-delete-form" data-vendor-name="<?php echo e($vendor->name); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="button" class="btn btn-sm btn-danger btn-delete-vendor" title="حذف المتجر">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<style>
    .ec-delete-popup { border-radius: 18px !important; font-family: inherit; }
    .ec-delete-title { font-size: 22px; font-weight: 700; color: #1e293b; }
    .ec-delete-text { font-size: 15px; color: #475569; line-height: 1.8; }
    .ec-delete-name {
        display: inline-block; background: #fee2e2; color: #b91c1c; font-weight: 700;
        padding: 4px 14px; border-radius: 20px; font-size: 16px; margin: 8px 0;
        border: 1px solid #fca5a5;
    }
    .ec-delete-note {
        background: #f8fafc; border: 1px solid #e2e8f0; border-right: 4px solid #ef4444;
        border-radius: 8px; padding: 10px 14px; margin-top: 12px; font-size: 13px;
        color: #64748b; text-align: right;
    }
    .ec-delete-popup .swal2-actions { gap: 10px; }
</style>
<script>
    $(function () {
        var confirmText = 'نعم، حذف المتجر';
        var busyHtml = '<i class="fa fa-spinner fa-spin"></i> جارٍ الحذف…';

        // Delegated so it keeps working after DataTables re-renders the rows.
        $(document).on('click', '.btn-delete-vendor', function (e) {
            e.preventDefault();

            var $btn = $(this);
            var $form = $btn.closest('form');
            var vendorName = $form.data('vendor-name') || 'هذا المتجر';

            // Never submit the same row twice.
            if ($btn.data('busy')) { return; }

            // Graceful fallback if SweetAlert is unavailable for any reason.
            if (typeof Swal === 'undefined') {
                if (window.confirm('هل تريد حذف متجر ' + vendorName + ' نهائياً؟')) {
                    $form.submit();
                }
                return;
            }

            Swal.fire({
                title: '<span class="ec-delete-title">حذف المتجر؟</span>',
                html: '<div class="ec-delete-text">هل أنت متأكد من رغبتك في حذف متجر<br>'
                    + '<span class="ec-delete-name"><i class="fa fa-store" style="margin-left:6px"></i>'
                    + $('<div>').text(vendorName).html() + '</span><br>'
                    + '<span class="ec-delete-note"><strong style="color:#ef4444">'
                    + '<i class="fa fa-exclamation-triangle" style="margin-left:5px"></i> تنبيه:</strong> '
                    + 'سيتم حذف بيانات المتجر نهائياً ولا يمكن التراجع عن هذا الإجراء بعد تنفيذه.'
                    + '</span></div>',
                icon: 'warning',
                iconColor: '#ef4444',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fa fa-trash" style="margin-left:6px"></i> ' + confirmText,
                cancelButtonText: '<i class="fa fa-times" style="margin-left:6px"></i> إلغاء',
                reverseButtons: true,
                focusCancel: true,
                allowOutsideClick: false,
                buttonsStyling: false,
                customClass: {
                    popup: 'ec-delete-popup',
                    confirmButton: 'btn btn-danger px-4',
                    cancelButton: 'btn btn-secondary px-4'
                },
                didOpen: function () {
                    // ESC / clicking the backdrop must not look like a submit.
                    $btn.removeData('busy');
                }
            }).then(function (result) {
                if (!result.isConfirmed) { return; }

                $btn.data('busy', true).prop('disabled', true).html(busyHtml);
                $form.trigger('submit');
            });
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\vendors\index.blade.php ENDPATH**/ ?>
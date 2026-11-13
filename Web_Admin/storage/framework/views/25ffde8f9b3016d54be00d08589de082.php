<?php $__env->startSection('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('web/css/admin-identity.css?v=21')); ?>" type="text/css">
<style>
    .cjr-stats { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 14px; margin-bottom: 20px; }
    .cjr-stat { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; box-shadow: 0 1px 2px rgba(23,43,77,.05); }
    .cjr-stat .cjr-label { font-size: .8rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: .04em; }
    .cjr-stat .value { font-size: 1.75rem; font-weight: 800; color: #172b4d; margin-top: 6px; }
    .cjr-stat.pending .value { color: #d97706; }
    .cjr-stat.approved .value { color: #16a34a; }
    .cjr-stat.rejected .value { color: #dc2626; }
    .cjr-stat.total .value { color: #0f9d98; }
    .cjr-table { width: 100%; border-collapse: collapse; font-size: .85rem; }
    .cjr-table thead th { text-align: start; padding: 12px 16px; background: #fbfcfe; border-bottom: 1px solid #e2e8f0; color: #64748b; font-size: .72rem; font-weight: 800; text-transform: uppercase; letter-spacing: .03em; }
    .cjr-table tbody td { padding: 12px 16px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; color: #172b4d; }
    .cjr-table tbody tr:hover td { background: #fbfcfe; }
    .cjr-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 999px; font-size: .7rem; font-weight: 800; border: 1px solid transparent; }
    .cjr-badge--pending { background: #fffbeb; color: #92400e; border-color: #fde68a; }
    .cjr-badge--approved { background: #ecfdf5; color: #15803d; border-color: #bbf7d0; }
    .cjr-badge--rejected { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
    .cjr-actions { display: flex; gap: 6px; }
    .cjr-btn { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 8px; font-size: .78rem; font-weight: 700; border: 1px solid transparent; cursor: pointer; text-decoration: none; transition: all .18s; }
    .cjr-btn--approve { background: #ecfdf5; color: #15803d; border-color: #bbf7d0; }
    .cjr-btn--approve:hover { background: #d1fae5; }
    .cjr-btn--reject { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
    .cjr-btn--reject:hover { background: #fee2e2; }
    .cjr-btn--review { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
    .cjr-btn--review:hover { background: #dbeafe; }
    .cjr-filter { display: flex; gap: 10px; align-items: center; margin-bottom: 16px; flex-wrap: wrap; }
    .cjr-filter select, .cjr-filter input { padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: .85rem; }
    .cjr-empty { text-align: center; padding: 40px 20px; color: #64748b; }
    .cjr-empty i { font-size: 3rem; color: #cbd5e1; margin-bottom: 16px; display: block; }
    .status-color-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-inline-end: 6px; }
    .dot-pending { background: #d97706; }
    .dot-approved { background: #16a34a; }
    .dot-rejected { background: #dc2626; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <header class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1"><i class="fas fa-user-plus me-2" aria-hidden="true"></i>طلبات الانضمام</h1>
            <p class="text-muted mb-0">إدارة طلبات انضمام العملاء الجدد وموافقة المشرف عليها.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('root')); ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>العودة للوحة التحكم
            </a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$approvalEnabled || !$approvalRequired): ?>
                <a href="<?php echo e(route('integrations.index')); ?>" class="btn btn-warning btn-sm text-dark">
                    <i class="fas fa-cog me-1"></i>تكوين الإعدادات
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </header>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show"><?php echo e(session('success')); ?> <button type="button" class="btn-close" data-dismiss="alert"></button></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show"><?php echo e(session('error')); ?> <button type="button" class="btn-close" data-dismiss="alert"></button></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="cjr-stats">
        <div class="cjr-stat total">
            <div class="cjr-label">إجمالي الطلبات</div>
            <div class="value"><?php echo e($stats['total']); ?></div>
        </div>
        <div class="cjr-stat pending">
            <div class="cjr-label">بانتظار الموافقة</div>
            <div class="value"><?php echo e($stats['pending']); ?></div>
        </div>
        <div class="cjr-stat approved">
            <div class="cjr-label">مقبولة</div>
            <div class="value"><?php echo e($stats['approved']); ?></div>
        </div>
        <div class="cjr-stat rejected">
            <div class="cjr-label">مرفوضة</div>
            <div class="value"><?php echo e($stats['rejected']); ?></div>
        </div>
    </div>

    
    <div class="cjr-filter">
        <form method="GET" action="<?php echo e(route('customer-join-requests.index')); ?>" class="d-flex gap-2 flex-wrap">
            <select name="status" class="form-select" style="width:auto;" onchange="this.form.submit()">
                <option value="" <?php echo e($statusFilter === null ? 'selected' : ''); ?>>جميع الحالات</option>
                <option value="pending" <?php echo e($statusFilter === 'pending' ? 'selected' : ''); ?>>بانتظار الموافقة</option>
                <option value="approved" <?php echo e($statusFilter === 'approved' ? 'selected' : ''); ?>>مقبولة</option>
                <option value="rejected" <?php echo e($statusFilter === 'rejected' ? 'selected' : ''); ?>>مرفوضة</option>
            </select>
            <input type="text" name="search" class="form-control" placeholder="البحث بالاسم أو الهاتف أو البريد..." value="<?php echo e($search ?? ''); ?>" style="width:280px;">
            <button type="submit" class="btn btn-primary btn-sm">بحث</button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($search ?? ''): ?>
                <a href="<?php echo e(route('customer-join-requests.index')); ?>" class="btn btn-outline-secondary btn-sm">إعادة تعيين</a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </form>
    </div>

    
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">قائمة طلبات الانضمام</h5>
        </div>
        <div class="table-responsive">
            <table class="cjr-table mb-0">
                <thead>
                    <tr>
                        <th>العميل</th>
                        <th>الهاتف / البريد</th>
                        <th>تاريخ الطلب</th>
                        <th>الحالة</th>
                        <th>الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div>
                                    <strong><?php echo e($req->user->first_name); ?> <?php echo e($req->user->last_name); ?></strong>
                                    <?php $role = $req->user->roles->first()?->name ?? '—'; ?>
                                    <div class="text-muted small"><?php echo e($role); ?></div>
                                </div>
                            </td>
                            <td>
                                <?php echo e($req->user->mobile); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($req->user->email): ?><br><span class="text-muted"><?php echo e($req->user->email); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="text-muted"><?php echo e($req->created_at?->diffForHumans()); ?></td>
                            <td>
                                <span class="cjr-badge cjr-badge--<?php echo e($req->status); ?>">
                                    <span class="status-color-dot dot-<?php echo e($req->status); ?>"></span>
                                    <?php echo e($req->statusLabel()); ?>

                                </span>
                            </td>
                            <td>
                                <div class="cjr-actions">
                                    <a href="<?php echo e(route('customer-join-requests.show', $req->id)); ?>" class="cjr-btn cjr-btn--review">
                                        <i class="fas fa-eye"></i>عرض
                                    </a>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($req->isPending()): ?>
                                        <button type="button" class="cjr-btn cjr-btn--approve" onclick="confirmAction(<?php echo e($req->id); ?>, 'approve')">
                                            <i class="fas fa-check"></i>موافقة
                                        </button>
                                        <button type="button" class="cjr-btn cjr-btn--reject" onclick="confirmAction(<?php echo e($req->id); ?>, 'reject')">
                                            <i class="fas fa-times"></i>رفض
                                        </button>
                                    <?php else: ?>
                                        <span class="cjr-btn" style="background:#e2e8f0;color:#64748b;cursor:default;">تم المعالجة</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-muted text-center py-4">
                                <i class="fas fa-inbox" style="font-size:3rem;color:#cbd5e1;display:block;margin-bottom:16px;"></i>
                                لا توجد طلبات انضمام مطابقة.
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php echo e($requests->links()); ?>

    </div>
</div>

<script>
    function confirmAction(id, action) {
        if (action === 'reject') {
            const reason = prompt('اكتب سبب رفض الطلب:');
            if (!reason || reason.trim() === '') {
                return;
            }
        }
        if (!confirm(action === 'approve' || reason ? 'هل أنت متأكد من إجراء هذا الإجراء؟' : false)) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo e(route('customer-join-requests.review', 0)); ?>'.replace('0', id);
        form.innerHTML = '<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">' +
            '<input type="hidden" name="_method" value="POST">' +
            '<input type="hidden" name="action" value="' + action + '">' +
            (action === 'reject' ? '<input type="hidden" name="rejection_reason" value="' + reason.trim() + '">' : '');
        document.body.appendChild(form);
        form.submit();
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\root\customer-join-requests\index.blade.php ENDPATH**/ ?>
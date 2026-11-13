<?php $__env->startSection('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('web/css/admin-identity.css?v=21')); ?>" type="text/css">
<style>
    .cjr-detail-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 16px; box-shadow: 0 1px 2px rgba(23,43,77,.05); }
    .cjr-detail-card h2 { font-size: 1rem; font-weight: 800; color: #172b4d; margin-bottom: 14px; display: flex; align-items: center; gap: 10px; }
    .cjr-detail-card h2 i { color: #0f9d98; }
    .cjr-detail-row { display: flex; gap: 24px; margin-bottom: 12px; flex-wrap: wrap; }
    .cjr-detail-col { flex: 1 1 200px; min-width: 200px; }
    .cjr-detail-label { font-size: .78rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 4px; }
    .cjr-detail-value { font-size: .92rem; color: #172b4d; font-weight: 600; }
    .cjr-detail-value.muted { color: #64748b; font-weight: 500; }
    .cjr-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 999px; font-size: .75rem; font-weight: 800; border: 1px solid transparent; }
    .cjr-badge--pending { background: #fffbeb; color: #92400e; border-color: #fde68a; }
    .cjr-badge--approved { background: #ecfdf5; color: #15803d; border-color: #bbf7d0; }
    .cjr-badge--rejected { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
    .cjr-actions-bar { display: flex; gap: 10px; margin-top: 16px; flex-wrap: wrap; }
    .cjr-btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 8px; font-size: .9rem; font-weight: 700; border: 1px solid transparent; cursor: pointer; text-decoration: none; transition: all .18s; }
    .cjr-btn--approve { background: #16a34a; color: #fff; }
    .cjr-btn--approve:hover { background: #15803d; }
    .cjr-btn--reject { background: #dc2626; color: #fff; }
    .cjr-btn--reject:hover { background: #b91c1c; }
    .cjr-btn--outline { background: #fff; color: #172b4d; border-color: #cbd5e1; }
    .cjr-btn--outline:hover { background: #f8fafc; }
    .cjr-btn--disabled { background: #e2e8f0; color: #64748b; border-color: #cbd5e1; cursor: not-allowed; }
    .cjr-reason-box { background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px 16px; margin-top: 10px; }
    .cjr-reason-title { font-size: .8rem; font-weight: 800; color: #b91c1c; }
    .cjr-review-note { display: block; width: 100%; max-width: 600px; min-height: 90px; padding: 10px; margin: 8px 0 12px; border: 1px solid #94a3b8; border-radius: 8px; background: #fff; color: #172b4d; }
    .cjr-reason-box .value { font-size: .9rem; color: #172b4d; margin-top: 6px; line-height: 1.6; }
    .cjr-meta { font-size: .8rem; color: #64748b; font-style: italic; }
    .cjr-empty { text-align: center; padding: 40px 20px; color: #64748b; }
    .cjr-empty i { font-size: 3rem; color: #cbd5e1; margin-bottom: 16px; display: block; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <header class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <a href="<?php echo e(route('customer-join-requests.index')); ?>" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="fas fa-arrow-left me-1"></i>العودة للقائمة
            </a>
            <h1 class="mb-1"><i class="fas fa-user-circle me-2" aria-hidden="true"></i>تفاصيل طلب الانضمام</h1>
            <p class="text-muted mb-0">طلب رقم <strong><?php echo e($joinRequest->id); ?></strong> — حالة: <span class="cjr-badge cjr-badge--<?php echo e($joinRequest->status); ?>"><?php echo e($joinRequest->statusLabel()); ?></span></p>
        </div>
    </header>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show"><?php echo e(session('success')); ?> <button type="button" class="btn-close" data-dismiss="alert"></button></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show"><?php echo e(session('error')); ?> <button type="button" class="btn-close" data-dismiss="alert"></button></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="cjr-detail-card">
        <h2><i class="fas fa-user" aria-hidden="true"></i>معلومات العميل</h2>
        <div class="cjr-detail-row">
            <div class="cjr-detail-col">
                <div class="cjr-detail-label">الاسم الكامل</div>
                <div class="cjr-detail-value"><?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?></div>
            </div>
            <div class="cjr-detail-col">
                <div class="cjr-detail-label">رقم الهاتف</div>
                <div class="cjr-detail-value"><?php echo e($user->mobile); ?></div>
            </div>
            <div class="cjr-detail-col">
                <div class="cjr-detail-label">البريد الإلكتروني</div>
                <div class="cjr-detail-value"><?php echo e($user->email ?? 'غير محدد'); ?></div>
            </div>
            <div class="cjr-detail-col">
                <div class="cjr-detail-label">نوع الحساب والدور</div>
                <div class="cjr-detail-value">
                    <?php $role = $user->roles->first()?->name ?? '—'; ?>
                    <?php echo e(ucfirst($role)); ?>

                </div>
            </div>
            <div class="cjr-detail-col">
                <div class="cjr-detail-label">تاريخ الإنشاء</div>
                <div class="cjr-detail-value"><?php echo e($user->created_at?->format('d M, Y H:i')); ?></div>
            </div>
            <div class="cjr-detail-col">
                <div class="cjr-detail-label">تاريخ التحقق من الهاتف</div>
                <div class="cjr-detail-value"><?php echo e($user->mobile_verified_at?->format('d M, Y H:i') ?? 'غير مverify'); ?></div>
            </div>
            <div class="cjr-detail-col">
                <div class="cjr-detail-label">الحساب نشط</div>
                <div class="cjr-detail-value"><?php echo e($user->is_active ? 'نعم' : 'لا'); ?></div>
            </div>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->alternative_phone): ?>
            <div class="cjr-detail-row">
                <div class="cjr-detail-col">
                    <div class="cjr-detail-label">الهاتف البديل</div>
                    <div class="cjr-detail-value"><?php echo e($user->alternative_phone); ?></div>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->gender): ?>
            <div class="cjr-detail-row">
                <div class="cjr-detail-col">
                    <div class="cjr-detail-label">النوع</div>
                    <div class="cjr-detail-value"><?php echo e($user->gender); ?></div>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->date_of_birth): ?>
            <div class="cjr-detail-row">
                <div class="cjr-detail-col">
                    <div class="cjr-detail-label">تاريخ الميلاد</div>
                    <div class="cjr-detail-value"><?php echo e(\Carbon\Carbon::parse($user->date_of_birth)->format('d M, Y')); ?></div>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div class="cjr-detail-card">
        <h2><i class="fas fa-file-alt" aria-hidden="true"></i>معلومات الطلب</h2>
        <div class="cjr-detail-row">
            <div class="cjr-detail-col">
                <div class="cjr-detail-label">حالة الطلب</div>
                <div class="cjr-detail-value">
                    <span class="cjr-badge cjr-badge--<?php echo e($joinRequest->status); ?>">
                        <?php echo e($joinRequest->statusLabel()); ?>

                    </span>
                </div>
            </div>
            <div class="cjr-detail-col">
                <div class="cjr-detail-label">تاريخ الطلب</div>
                <div class="cjr-detail-value"><?php echo e($joinRequest->created_at?->format('d M, Y H:i')); ?></div>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($joinRequest->isApproved()): ?>
                <div class="cjr-detail-col">
                    <div class="cjr-detail-label">تاريخ الموافقة</div>
                    <div class="cjr-detail-value"><?php echo e($joinRequest->approved_at?->format('d M, Y H:i')); ?></div>
                </div>
                <div class="cjr-detail-col">
                    <div class="cjr-detail-label">الموظف الموافق</div>
                    <div class="cjr-detail-value"><?php echo e($joinRequest->approver?->name ?? '—'); ?></div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($joinRequest->isRejected()): ?>
                <div class="cjr-detail-col">
                    <div class="cjr-detail-label">تاريخ الرفض</div>
                    <div class="cjr-detail-value"><?php echo e($joinRequest->rejected_at?->format('d M, Y H:i')); ?></div>
                </div>
                <div class="cjr-detail-col">
                    <div class="cjr-detail-label">الموظف الرافض</div>
                    <div class="cjr-detail-value"><?php echo e($joinRequest->rejecter?->name ?? '—'); ?></div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($joinRequest->rejection_reason): ?>
                <div class="cjr-detail-col" style="flex-basis:100%;">
                    <div class="cjr-reason-box">
                        <div class="cjr-reason-title">سبب الرفض</div>
                        <div class="value"><?php echo e($joinRequest->rejection_reason); ?></div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$joinRequest->isTrackingTokenExpired() && !$joinRequest->isTrackingTokenRevoked()): ?>
        <div class="cjr-actions-bar">
            <form method="POST" action="<?php echo e(route('customer-join-requests.revoke-tracking-token', $joinRequest->id)); ?>" style="display:inline;">
                <?php echo csrf_field(); ?>
                <button type="submit" class="cjr-btn cjr-btn--reject" onclick="return confirm('هل أنت متأكد من إبطال رمز المتابعة؟ لن يتمكن العميل من استعلام حالة طلبه بهذا الرمز.')">
                    <i class="fas fa-ban"></i>إبطال رمز المتابعة
                </button>
            </form>
        </div>
    <?php elseif($joinRequest->isTrackingTokenExpired()): ?>
        <div class="cjr-meta mt-3"><i class="fas fa-hourglass-half me-1"></i>رمز المتابعة منتهي الصلاحية.</div>
    <?php elseif($joinRequest->isTrackingTokenRevoked()): ?>
        <div class="cjr-meta mt-3"><i class="fas fa-ban me-1"></i>رمز المتابعة مُبطل.</div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($joinRequest->isRejected()): ?>
        <div class="cjr-actions-bar">
            <form method="POST" action="<?php echo e(route('customer-join-requests.review-again', $joinRequest->id)); ?>" style="display:inline;">
                <?php echo csrf_field(); ?>
                <label for="review-note" class="cjr-detail-label">سبب إعادة النظر (مطلوب)</label>
                <textarea id="review-note" name="note" class="cjr-review-note" required maxlength="1000" aria-describedby="review-note-help@error('note') review-note-error@enderror"><?php echo e(old('note')); ?></textarea>
                <p id="review-note-help" class="cjr-meta">سيُحفظ السبب في سجل التدقيق، وسيعود الطلب إلى انتظار الموافقة دون تفعيل الحساب.</p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['note'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p id="review-note-error" class="text-danger" role="alert"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <button type="submit" class="cjr-btn cjr-btn--outline" onclick="return confirm('هل أنت متأكد من إعادة النظر في هذا الطلب؟ ستتم إعادته إلى انتظار الموافقة.')">
                    <i class="fas fa-undo"></i>إعادة النظر في الطلب
                </button>
            </form>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($joinRequest->isPending()): ?>
        <div class="cjr-actions-bar">
            <form method="POST" action="<?php echo e(route('customer-join-requests.approve', $joinRequest->id)); ?>" style="display:inline;">
                <?php echo csrf_field(); ?>
                <button type="submit" class="cjr-btn cjr-btn--approve" onclick="return confirm('هل أنت متأكد من الموافقة على هذا الطلب وتفعيل الحساب؟')">
                    <i class="fas fa-check"></i>قبول الطلب
                </button>
            </form>
            <form method="POST" action="<?php echo e(route('customer-join-requests.reject', $joinRequest->id)); ?>" style="display:inline;">
                <?php echo csrf_field(); ?>
                <button type="submit" class="cjr-btn cjr-btn--reject" onclick="return confirmReject()">
                    <i class="fas fa-times"></i>رفض الطلب
                </button>
            </form>
        </div>
        <div class="cjr-meta mt-3">سيتم إرسال إشعار للعميل فور اتخاذ القرار.</div>
    <?php else: ?>
        <div class="cjr-actions-bar">
            <button type="button" class="cjr-btn cjr-btn--disabled" disabled>
                <i class="fas fa-lock"></i>تمت المعالجة بالفعل
            </button>
        </div>
        <div class="cjr-meta mt-3">لا يمكن تعديل طلب تم معالجته سابقًا.</div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<script>
    function confirmReject() {
        if (!confirm('هل أنت متأكد من رفض هذا الطلب؟')) return false;
        const reason = prompt('الرجاء كتابة سبب الرفض:');
        if (!reason || reason.trim() === '') {
            alert('يجب كتابة سبب الرفض.');
            return false;
        }
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'rejection_reason';
        input.value = reason.trim();
        event.currentTarget.closest('form').appendChild(input);
        return true;
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\root\customer-join-requests\show.blade.php ENDPATH**/ ?>
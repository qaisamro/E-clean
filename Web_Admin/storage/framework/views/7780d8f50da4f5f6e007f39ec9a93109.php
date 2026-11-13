<!doctype html>
<html lang="<?php echo e(app()->getLocale()); ?>" <?php if(app()->getLocale() === 'ar'): ?> dir="rtl" <?php endif; ?>>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />
    <?php
        $websetting = App\Models\WebSetting::first();
    ?>
    <link rel="icon" type="image/png" href="<?php echo e($websetting?->websiteFaviconPath ?? asset('web/favIcon.png?v=2')); ?>">
    <title><?php echo e($websetting->title ?? config('app.name')); ?></title>
    <!-- Fonts -->

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo e(asset('web/css/all.min.css')); ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo e(asset('web/css/bootstrap.css')); ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo e(asset('web/css/select2.min.css')); ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo e(asset('web/css/style.css?v=3')); ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo e(asset('web/css/custom.css')); ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo e(asset('web/css/datatables.min.css')); ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo e(asset('web/css/toastr.min.css')); ?>" type="text/css">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(app()->getLocale() === 'ar'): ?>
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap"
            rel="stylesheet">
        <link rel="stylesheet" href="<?php echo e(asset('web/css/rtl.css?v=9')); ?>" type="text/css">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    
    <link rel="stylesheet" href="<?php echo e(asset('web/css/admin-identity.css?v=21')); ?>" type="text/css">
    <?php echo $__env->yieldContent('styles'); ?>

</head>

<style>
    .dropdown-toggle {
        white-space: nowrap;
    }

    .notification-empty {
        min-width: 310px;
        padding: 18px;
        text-align: center;
        color: #6c757d;
    }

    .notification-sound-toggle {
        border: 0;
        background: transparent;
        color: #6c757d;
        padding: 8px;
        cursor: pointer;
    }

    #notification .dropdown-item {
        min-width: 330px;
        max-width: 420px;
        white-space: normal;
    }

    #notification .notification-title {
        font-weight: 700;
        margin-bottom: 3px;
    }
</style>

<body>

    

    <?php echo $__env->make('layouts.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-content">

        <div class="main-header shadow-sm">
            <div class="btn-group dropdown">
                <button type="button" id="notificationSoundToggle" class="notification-sound-toggle"
                    title="تشغيل أو كتم صوت الإشعارات" aria-label="تشغيل أو كتم صوت الإشعارات">
                    <i class="fas fa-volume-up"></i>
                </button>
                <button type="button" class="notificationBell dropdown-toggle" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-bell Icon"></i>
                    <div id="total">0</div>
                </button>
                <div class="dropdown-menu dropdown-menu-right" id="notification">
                    <div class="notification-empty">جاري تحميل الإشعارات...</div>
                </div>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
            <div class="container-fluid mt-3">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="fa fa-exclamation-triangle"></i> <?php echo e(__('تحقق من البيانات المدخلة')); ?>:</strong>
                    <ul class="mb-0 mt-1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>

    </div>

    
    <button type="button" class="ec-mobile-menu-btn" id="mobileSidebarToggle"
        aria-label="القائمة" aria-expanded="false" aria-controls="sidenav-main">
        <i class="fas fa-bars"></i>
    </button>
    <div class="ec-sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>

    <script src="<?php echo e(asset('web/js/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('web/js/popper.js')); ?>"></script>
    <script src="<?php echo e(asset('web/js/sweet-alert.js')); ?>"></script>
    <script src="<?php echo e(asset('web/js/bootstrap.min.js')); ?>"></script>
    <script src="<?php echo e(asset('web/js/select2.min.js')); ?>"></script>

    <script src="<?php echo e(asset('web/js/argon.js')); ?>"></script>
    <script src="<?php echo e(asset('web/js/main.js')); ?>"></script>
    <script src="<?php echo e(asset('web/js/datatables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('web/js/toastr.min.js')); ?>"></script>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
    <script>
        (function() {
            const feedUrl = <?php echo json_encode(route('web-notifications.feed'), 15, 512) ?>;
            const storagePrefix = 'eclean-web-notifications-<?php echo e(auth()->id()); ?>';
            const soundKey = storagePrefix + '-sound';
            const knownKeys = new Set();
            let initialized = false;
            let timer = null;
            let audioContext = null;
            let audioUnlocked = false;
            let soundEnabled = localStorage.getItem(soundKey) !== 'muted';

            function updateSoundIcon() {
                const icon = document.querySelector('#notificationSoundToggle i');
                if (!icon) return;
                icon.className = soundEnabled ? 'fas fa-volume-up' : 'fas fa-volume-mute';
            }

            async function unlockAudio() {
                if (!soundEnabled) return false;
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return false;
                audioContext = audioContext || new AudioContext();
                if (audioContext.state === 'suspended') {
                    try {
                        await audioContext.resume();
                    } catch (error) {
                        return false;
                    }
                }
                audioUnlocked = audioContext.state === 'running';
                return audioUnlocked;
            }

            function playNotificationSound() {
                if (!soundEnabled || !audioUnlocked || !audioContext || audioContext.state !== 'running') return;

                const startedAt = audioContext.currentTime;
                const notes = [784, 988, 1175, 988, 1175, 1319];
                const masterGain = audioContext.createGain();
                masterGain.gain.setValueAtTime(0.62, startedAt);
                masterGain.gain.setValueAtTime(0.62, startedAt + 2.85);
                masterGain.gain.exponentialRampToValueAtTime(0.0001, startedAt + 3);
                masterGain.connect(audioContext.destination);

                notes.forEach(function(frequency, index) {
                    const noteStart = startedAt + (index * 0.48);
                    const oscillator = audioContext.createOscillator();
                    const noteGain = audioContext.createGain();
                    oscillator.type = 'sine';
                    oscillator.frequency.setValueAtTime(frequency, noteStart);
                    noteGain.gain.setValueAtTime(0.0001, noteStart);
                    noteGain.gain.exponentialRampToValueAtTime(0.72, noteStart + 0.025);
                    noteGain.gain.setValueAtTime(0.72, noteStart + 0.34);
                    noteGain.gain.exponentialRampToValueAtTime(0.0001, noteStart + 0.44);
                    oscillator.connect(noteGain);
                    noteGain.connect(masterGain);
                    oscillator.start(noteStart);
                    oscillator.stop(noteStart + 0.45);
                });
            }

            function cleanMessage(item) {
                let msg = (item && typeof item.message === 'string') ? item.message.trim() : '';
                if (msg.startsWith('{') || msg.startsWith('[')) {
                    try {
                        const parsed = JSON.parse(msg);
                        if (parsed && typeof parsed === 'object') {
                            const inner = parsed.message ?? parsed.title ?? parsed.body ?? parsed.notification ?? '';
                            msg = typeof inner === 'string' ? inner : String(inner ?? '');
                            msg = msg.trim();
                        }
                    } catch (e) {
                        // keep original text if it does not parse
                    }
                }
                return msg;
            }

            function render(items) {
                const container = document.getElementById('notification');
                const total = document.getElementById('total');
                if (!container || !total) return;

                total.textContent = String(items.length);
                container.replaceChildren();

                if (!items.length) {
                    const empty = document.createElement('div');
                    empty.className = 'notification-empty';
                    empty.textContent = 'لا توجد إشعارات جديدة';
                    container.appendChild(empty);
                    return;
                }

                items.forEach(function(item) {
                    const link = document.createElement('a');
                    link.className = 'dropdown-item';
                    link.href = item.url || '#';

                    const cleanMsg = cleanMessage(item);

                    const title = document.createElement('div');
                    title.className = 'notification-title';
                    title.textContent = item.title || 'إشعار جديد';

                    const message = document.createElement('div');
                    message.className = 'message';
                    message.textContent = cleanMsg || (item.title || 'إشعار جديد');

                    const time = document.createElement('div');
                    time.className = 'time';
                    time.textContent = item.time || '';

                    link.append(title, message, time);
                    container.appendChild(link);
                });
            }

            function schedule(delay) {
                window.clearTimeout(timer);
                timer = window.setTimeout(poll, delay);
            }

            function poll() {
                if (document.hidden) {
                    schedule(15000);
                    return;
                }

                $.ajax({
                    type: 'GET',
                    url: feedUrl,
                    dataType: 'json',
                    cache: false,
                    success: function(response) {
                        const items = Array.isArray(response?.data?.items) ? response.data.items : [];
                        const newItems = initialized
                            ? items.filter(item => !knownKeys.has(item.key))
                            : [];

                        items.forEach(item => knownKeys.add(item.key));
                        render(items);

                        if (newItems.length) {
                            playNotificationSound();
                            const first = newItems[0];
                            const firstMsg = cleanMessage(first) || (first.title || 'إشعار جديد');
                            const suffix = newItems.length > 1 ? ` (+${newItems.length - 1})` : '';
                            toastr.success(firstMsg + suffix, first.title || 'إشعار جديد');
                        }

                        initialized = true;
                        schedule(8000);
                    },
                    error: function(xhr) {
                        if (xhr.status === 401 || xhr.status === 403) {
                            window.clearTimeout(timer);
                            return;
                        }
                        schedule(20000);
                    }
                });
            }

            document.addEventListener('pointerdown', unlockAudio, { once: true });
            document.addEventListener('keydown', unlockAudio, { once: true });
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) poll();
            });
            document.getElementById('notificationSoundToggle')?.addEventListener('click', function(event) {
                event.stopPropagation();
                soundEnabled = !soundEnabled;
                localStorage.setItem(soundKey, soundEnabled ? 'enabled' : 'muted');
                updateSoundIcon();
                if (soundEnabled) {
                    unlockAudio().then(function(unlocked) {
                        if (unlocked) playNotificationSound();
                    });
                }
            });

            updateSoundIcon();
            poll();
        })();

        $('.visitorMessage').click(function(e) {
            e.preventDefault()
            Swal.fire(
                'صلاحية مرفوضة!',
                "ليس لديك صلاحية للإنشاء أو التعديل أو الحذف لأنك مستخدم زائر.",
                'warning'
            )
        })
    </script>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('visitor')): ?>
        <script>
            Swal.fire(
                'أنت مستخدم زائر.',
                'عذراً، لا يمكنك إنشاء أو تعديل أو حذف أي شيء.',
                'question'
            )
        </script>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })

            Toast.fire({
                icon: 'success',
                title: '<?php echo e(session('success')); ?>'
            })
        </script>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
        <script>
            Swal.fire({
                position: 'top-center',
                icon: 'error',
                title: '<?php echo e(session('error')); ?>',
                showConfirmButton: false,
                timer: 2000
            })
        </script>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>

    <script>
        $('#myTable').DataTable({
            order: [],
            language: {
                'paginate': {
                    'previous': '<i class="fas fa-angle-double-left"></i>',
                    'next': '<i class="fas fa-angle-double-right"></i>'
                },
                "lengthMenu": "يعرض _MENU_ إدخالات",
                "zeroRecords": "لم يتم العثور على سجلات مطابقة",
                "info": "إظهار _START_ إلى _END_ من أصل _TOTAL_ إدخالات",
                "infoEmpty": "لا توجد بيانات متوفرة في الجدول",
                "infoFiltered": "(تمت تصفيته من إجمالي _MAX_ إدخالات)",
                "search": "يبحث:",
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select One",
            });
        });

        //delete confirm sweet alert
        $(document).on('click', '.delete-confirm', function(e) {
            e.preventDefault();
            const $el = $(this);
            const href = $el.attr('href');
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
                if (!result.isConfirmed) return;
                if (href) {
                    $('<form>', { method: 'POST', action: href })
                        .append($('<input>', { type: 'hidden', name: '_method', value: 'DELETE' }))
                        .append($('<input>', { type: 'hidden', name: '_token', value: $('meta[name="csrf-token"]').attr('content') }))
                        .appendTo('body')[0].submit();
                } else {
                    $el.closest('form')[0].submit();
                }
            })
        });
    </script>

    
    <script>
        (function () {
            var btn = document.getElementById('mobileSidebarToggle');
            var backdrop = document.getElementById('sidebarBackdrop');
            var sidebar = document.getElementById('sidenav-main');
            if (!btn || !sidebar) return;
            var open = function () {
                document.body.classList.add('ec-sidebar-open');
                btn.setAttribute('aria-expanded', 'true');
            };
            var close = function () {
                document.body.classList.remove('ec-sidebar-open');
                btn.setAttribute('aria-expanded', 'false');
            };
            btn.addEventListener('click', function () {
                document.body.classList.contains('ec-sidebar-open') ? close() : open();
            });
            if (backdrop) backdrop.addEventListener('click', close);
            sidebar.querySelectorAll('a[href]').forEach(function (a) {
                a.addEventListener('click', function () {
                    if (window.innerWidth < 992) close();
                });
            });
        })();
    </script>
    <!-- Print handler: opens the invoice (HTML POS invoice or PDF) in a new tab.
         The previous hidden 0x0 iframe approach failed silently, because browsers
         do not load PDFs inside zero-sized frames, so the print page never opened.
         Opening a new tab works everywhere; the POS invoice view auto-triggers window.print(). -->
    <script>
        document.addEventListener('click', function(e){
            var link = e.target.closest('a.print-same-page');
            if(link && link.href){
                e.preventDefault();
                window.open(link.href, '_blank');
            }
        });
    </script>
</body>

</html>
<?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\layouts\app.blade.php ENDPATH**/ ?>
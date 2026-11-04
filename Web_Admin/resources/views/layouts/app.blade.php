<!doctype html>
<html lang="{{ app()->getLocale() }}" @if (app()->getLocale() === 'ar') dir="rtl" @endif>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    @php
        $websetting = App\Models\WebSetting::first();
    @endphp
    <link rel="icon" type="image/png" href="{{ $websetting?->websiteFaviconPath ?? asset('web/favIcon.png?v=2') }}">
    <title>{{ $websetting->title ?? config('app.name') }}</title>
    <!-- Fonts -->

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('web/css/all.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('web/css/bootstrap.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('web/css/select2.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('web/css/style.css?v=2') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('web/css/custom.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('web/css/datatables.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('web/css/toastr.min.css') }}" type="text/css">
    @if (app()->getLocale() === 'ar')
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap"
            rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('web/css/rtl.css?v=8') }}" type="text/css">
    @endif
    {{-- Identity overlay last so it wins ties --}}
    <link rel="stylesheet" href="{{ asset('web/css/admin-identity.css?v=8') }}" type="text/css">

</head>

<style>
    .nav-profile-box {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
    }

    .language-image {
        background: #29aae130;
        padding: 8px 8px 4px 8px;
        border-radius: 4px;
    }

    .language-image div {
        margin-top: 2px;
        font-weight: 400;
    }

    .language-image a img {
        width: 28px;
    }

    .nav-profile-box.dropdown-toggle::after {
        display: none;
    }

    .badgeButtonBox .dropdown-toggle::after {
        display: none;
    }

    .dropdown-toggle {
        white-space: nowrap;
    }
</style>

<body>

    {{-- <div class="preload">
        <div class="flexbox">
            <div>
                <img src="{{ asset('images/loader/GoldStar-Loader.gif') }}" alt="">
            </div>
        </div>
    </div> --}}

    @include('layouts.partials.sidebar')



    <div class="main-content">

        @php
            use App\Models\Language;
            $languages = Language::All();
            $language = Language::where('name', app()->getLocale())->first();
        @endphp

        {{-- Mobile language toggle --}}
        <div class="mobile-lang-toggle" id="mobileLangToggle" style="display:none;">
            <span class="lang-icon"><i class="fa fa-language"></i></span>
            <img src="{{ $language?->file }}" alt="{{ $language?->title }}">
            <span>{{ $language?->title }}</span>
        </div>
        <div class="dropdown-menu profile-item" id="mobileLangDropdown" style="position:fixed;top:52px;right:12px;z-index:1001;min-width:140px;display:none;">
            @foreach ($languages as $lang)
                <a href="{{ route('change.local', 'ln=' . $lang->name) }}" class="dropdown-item">
                    <img src="{{ $lang->file }}" alt="{{ $lang->title }}" width="20" class="me-2">
                    {{ $lang->title }}
                </a>
            @endforeach
        </div>

        <div class="main-header shadow-sm">

            <div class="user-profile-box dropdown mx-3" style="display:none;">
                <div class="nav-profile-box dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <div class="profile-image language-image d-flex gap-2">
                        <a href="#">
                            <img class="" src="{{ $language?->file }}" alt="{{ $language?->title }}">
                        </a>
                        <div>{{ $language?->title }}</div>
                    </div>
                </div>
                <div class="dropdown-menu profile-item">
                    @foreach ($languages as $lang)
                        <a href="{{ route('change.local', 'ln=' . $lang->name) }}" class="dropdown-item">
                            <img src="{{ $lang->file }}" alt="{{ $lang->title }}" width="20" class="me-2">
                            {{ $lang->title }}
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="btn-group dropdown">
                <button type="button" class="notificationBell dropdown-toggle" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-bell Icon"></i>
                    <div id="total">0</div>
                </button>
                <div class="dropdown-menu dropdown-menu-right" id="notification">
                    <a class="dropdown-item" href="#">
                        <div class="message"></div>
                        <div class="time"></div>
                    </a>
                </div>
            </div>
            @auth
                <div class="dropdown mx-3 ">
                    <div class="nav-profile-box dropdown-toggle" data-toggle="dropdown">
                        <div class="profile-image language-image d-flex gap-2">
                            <img src="{{ auth()->user()->image ?? asset('images/dummy/dummy-user.png?v=2') }}" width="28">
                        </div>

                    </div>

                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="{{ route('profile.index') }}" class="dropdown-item">
                            <i class="fas fa-user me-2"></i> {{ __('Profile') }}
                        </a>

                        @if (!auth()->user()?->hasRole('root'))
                            <a href="{{ auth()->user()?->hasRole('vendor_admin') ? route('vendor.settings') : route('webSetting.index') }}" class="dropdown-item">
                                <i class="fas fa-cog me-2"></i> {{ __('Settings') }}
                            </a>
                        @endif

                        <div class="dropdown-divider"></div>

                        <a href="{{ route('logout') }}" class="dropdown-item"
                            onclick="event.preventDefault(); if(confirm('{{ __('Are you sure you want to logout?') }}')){ document.getElementById('logout-form').submit(); }">
                            <i class="fas fa-sign-out-alt me-2"></i> {{ __('Logout') }}
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                            @csrf
                        </form>
                    </div>
                </div>
            @endauth
        </div>

        @if ($errors->any())
            <div class="container-fluid mt-3">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="fa fa-exclamation-triangle"></i> {{ __('تحقق من البيانات المدخلة') }}:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            </div>
        @endif

        @yield('content')

    </div>

    <script src="{{ asset('web/js/jquery.min.js') }}"></script>
    <script src="{{ asset('web/js/popper.js') }}"></script>
    <script src="{{ asset('web/js/sweet-alert.js') }}"></script>
    <script src="{{ asset('web/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('web/js/select2.min.js') }}"></script>

    <script src="{{ asset('web/js/argon.js') }}"></script>
    <script src="{{ asset('web/js/main.js') }}"></script>
    <script src="{{ asset('web/js/datatables.min.js') }}"></script>
    <script src="{{ asset('web/js/toastr.min.js') }}"></script>

    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script>
        var pusher = new Pusher("{{ config('app.pusher_key') }}", {
            cluster: "{{ config('app.pusher_cluster') }}"
        });

        var channel = pusher.subscribe('popup-channel');
        channel.bind('order-notification', function(data) {
            toastr.success(JSON.stringify(data.message))
            showNotifications()
        });
    </script>

    <script>
        const showNotifications = function() {
            var totalNumber = $('#total');

            $.ajax({
                type: 'GET',
                url: "{{ route('new.orders') }}",
                dataType: 'json',
                success: function(response) {
                    $('#total').text(response.data.orders.length)
                    $('#notification').empty()
                    $.each(response.data.orders, function(key, value) {
                        $('#notification').append(
                            "<a class='dropdown-item' href='/orders/" + value.id +
                            "'><div class='message'>طلب جديد من <strong>" + value.customer
                            .user.name + "</strong> رقم الطلب: " + value.order_code +
                            "</div> <div class='time'>" + value.ordered_at + "</div></a>"
                        );
                    })
                },
                error: function(e) {
                    $('#notification').empty()
                    $("#notification").html(e.responseText);
                }
            });
        }
        showNotifications()

        $('.visitorMessage').click(function(e) {
            e.preventDefault()
            Swal.fire(
                'صلاحية مرفوضة!',
                "ليس لديك صلاحية للإنشاء أو التعديل أو الحذف لأنك مستخدم زائر.",
                'warning'
            )
        })
    </script>

    @if (session('visitor'))
        <script>
            Swal.fire(
                'أنت مستخدم زائر.',
                'عذراً، لا يمكنك إنشاء أو تعديل أو حذف أي شيء.',
                'question'
            )
        </script>
    @endif

    @if (session('success'))
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
                title: '{{ session('success') }}'
            })
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                position: 'top-center',
                icon: 'error',
                title: '{{ session('error') }}',
                showConfirmButton: false,
                timer: 2000
            })
        </script>
    @endif

    @stack('scripts')

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

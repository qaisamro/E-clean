@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h3 class="card-title m-0">
                        {{ __('Order_Details') . ' ' . __('of') }} {{ $order->customer->user->name }}
                    </h3>

                    <div class="">
                        <a class="btn btn-light" href="{{ auth()->user()->hasRole('driver') ? route('driver.account') : route('order.index') }}"> {{ __('Back') }} </a>
                        @can('order.edit')
                            <a class="btn btn-warning" href="{{ route('order.edit', $order->id) }}">
                                <i class="fas fa-edit"></i> {{ __('Edit') }}
                            </a>
                        @endcan
                        {{-- زر «الفاتورة السابقة» أُزيل (لا يُعرض لأي مستخدم). --}}

                        @if (auth()->user()->hasAnyRole(['root', 'admin', 'vendor_admin']) || (auth()->user()->hasRole('vendor_sub') && auth()->user()->can('order.pos-invoice')))
                            <button class="btn btn-primary" type="button" data-bt-invoice="normal" data-copy="normal">
                                <i class="fas fa-bluetooth-b" aria-hidden="true"></i> طباعة
                            </button>
                            <button class="btn btn-outline-primary" type="button" data-bt-invoice="customer" data-copy="customer">
                                <i class="fas fa-bluetooth-b" aria-hidden="true"></i> نسخة الزبون
                            </button>
                            <button class="btn btn-outline-primary" type="button" data-bt-invoice="store" data-copy="store">
                                <i class="fas fa-bluetooth-b" aria-hidden="true"></i> نسخة المحل
                            </button>
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('order.pos-invoice', ['order' => $order->id]) }}" target="_blank" rel="noopener" title="طباعة ورقية / حفظ PDF عبر المتصفح">
                                <i class="fas fa-file-pdf" aria-hidden="true"></i> PDF
                            </a>
                        @endif

                        @can('order.status.change')
                            <div class="dropdown d-inline-block">
                                <button class="btn btn-primary dropdown-toggle shadow-sm" type="button" id="statusDropdown"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                    style="min-width:200px; border-radius:8px;">
                                    <i class="fas fa-sync-alt mr-1"></i>
                                    <span>{{ __($order->order_status) }}</span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right shadow-lg border-0" aria-labelledby="statusDropdown"
                                    style="min-width:220px; max-height:360px; overflow-y:auto; border-radius:10px; padding:6px;">
                                    @foreach (config('enums.order_status') as $key => $order_status)
                                        <a class="dropdown-item status-change d-flex align-items-center justify-content-between {{ $key === $order->order_status ? 'active font-weight-bold' : '' }}"
                                            href="#" data-status="{{ $key }}"
                                            style="border-radius:6px; margin:2px 0;">
                                            <span>{{ __($order_status) }}</span>
                                            @if ($key === $order->order_status)
                                                <i class="fas fa-check text-success"></i>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endcan

                        @unless(auth()->user()->hasRole('driver'))
@if ($order->drivers->isEmpty() && $order->order_status != 'Delivered' && count($order->driverHistories) < 2)
                            <button class="btn btn-primary" data-toggle="modal"
                                data-target="#assinDriver">{{ __('Assign_driver') }}</button>
                        @endif
@endunless

                    </div>
                </div>
                @if (auth()->user()->hasAnyRole(['root', 'admin', 'vendor_admin']) || (auth()->user()->hasRole('vendor_sub') && auth()->user()->can('order.pos-invoice')))
                    <div id="bt-invoice-mount" class="no-print mt-2"></div>
                @endif
                @if (auth()->user()->hasAnyRole(['root', 'admin', 'vendor_admin']) || (auth()->user()->hasRole('vendor_sub') && auth()->user()->can('order.print.labels')))
                    <div id="bt-stickers-mount" class="no-print mt-2"></div>
                @endif
            </div>
            <div class="col-md-6 mt-2">
                <div class="card d-flex flex-column h-100">
                    <div class="card-header py-2">
                        <h2 class="m-0">{{ __('Order Details') }}</h2>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive-md">
                            <table class="table table-bordered table-striped {{ session()->get('local') }}">
                                <tr>
                                    <th class="py-2">{{ __('Order') . ' ' . __('Id') }}</th>
                                    <td class="py-2">{{ $order->order_code }}</td>
                                </tr>
                                <tr>
                                    <th class="py-2">{{ __('Order') . ' ' . __('Status') }}</th>
                                    <td class="py-2">{{ __($order->order_status) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Assign_driver') }}</th>
                                    <td>
                                        @if ($order->drivers->isNotEmpty())
                                            <li>
                                                <strong> {{ $order->drivers[0]->user->name }} </strong>
                                            </li>
                                        @endif
                                        @foreach ($order->driverHistories as $orderHistory)
                                            <li>
                                                <strong> {{ $orderHistory->driver->user->name }}</strong>
                                                <span
                                                    class="badge badge-pill badge-primary text-capitalize">{{ $orderHistory->status }}</span>
                                            </li>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <th class="py-2">{{ __('Payment') . ' ' . __('Status') }}</th>
                                    <td class="py-2">
                                        @if ($order->payment_status != 'Paid')
                                            <div class="d-flex justify-content-between align-items-center">
                                                {{ __($order->payment_status) }}
                                                @unless(auth()->user()->hasRole('driver'))
                                                    <a href="#" data-url="{{ route('orderIncomplete.paid', $order->id) }}"
                                                        class="btn btn-primary py-2 px-4 paid-confirm">{{ __('Paid') }}</a>
                                                @endunless
                                            </div>
                                        @else
                                            {{ __($order->payment_status) }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="py-2">{{ __('Total') . ' ' . __('Amount') }}</th>
                                    <td class="py-2">{{ currencyPosition($order->total_amount) }}</td>
                                </tr>
                                <tr>
                                    <th class="py-2">{{ __('Payment') . ' ' . __('Method') }}</th>
                                    <td class="py-2">{{ $order->payment_method_label }}</td>
                                </tr>
                                <tr>
                                    <th class="py-2">{{ __('Discount') }}</th>
                                    <td class="py-2">{{ currencyPosition($order->discount) }}</td>
                                </tr>
                                <tr>
                                    <th class="py-2">{{ __('Delivery_charge') }}</th>
                                    <td class="py-2">{{ currencyPosition($order->delivery_charge) }}</td>
                                </tr>
                                <tr>
                                    <th class="py-2">{{ __('Total') . ' ' . __('Quantity') }}</th>
                                    <td class="py-2">{{ $quantity . ' ' . __('Pieces') }}</td>
                                </tr>
                                <tr>
                                    <th class="py-2">{{ __('Items') }}</th>
                                    <td class="py-2">{{ $order->products->count() }}</td>
                                </tr>
                                <tr>
                                    <th class="py-2">{{ __('Picked') . ' ' . __('Date') }}</th>
                                    <td class="py-2">
                                        {{ Carbon\Carbon::parse($order->pick_date)->format('F d, Y') }}
                                        <span class="badge badge-pill bg-light text-dark" style="font-size: 14px">
                                            {{ $order->getTime(substr($order->pick_hour, 0, 2)) ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="py-2">{{ __('Delivery') . ' ' . __('Date') }}</th>
                                    <td class="py-2">
                                        {{ Carbon\Carbon::parse($order->delivery_date)->format('F d, Y') }}
                                        <span class="badge badge-pill bg-light text-dark" style="font-size: 14px">
                                            {{ $order->getTime(substr($order->delivery_hour, 0, 2)) ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>

                            <!-- ===== Address ===== -->
                            <div class="mt-3 pt-3" style="border-top:1px dashed #E2E8F0;">
                                <h3 class="m-0 mb-2 d-flex align-items-center gap-2" style="font-size:13.5px; font-weight:800; color:#0B1E2E;">
                                    <i class="fas fa-map-marker-alt" style="color:#028090;"></i>
                                    {{ __('Address') }}
                                </h3>
                                @if ($order->address)
                                    @php
                                        $mapUrl = \App\Support\MapLink::addressUrl($order->address);
                                    @endphp
                                    <div style="display:flex; gap:11px; align-items:center; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:14px; padding:10px 12px; flex-wrap:wrap;">
                                        <div style="flex:0 0 auto; width:46px; height:46px; border-radius:12px; background:#E6F6F7; display:flex; align-items:center; justify-content:center;">
                                            <i class="fas fa-map-marker-alt" style="font-size:20px; color:#028090;"></i>
                                        </div>
                                        <div style="flex:1 1 180px; min-width:0;">
                                            <div style="font-weight:800; font-size:13px; color:#0B1E2E; line-height:1.4;">
                                                {{ $order->address->address_name ?: '—' }}
                                            </div>
                                            <div style="margin-top:3px; font-size:11.5px; color:#475569; display:flex; align-items:center; gap:7px; flex-wrap:wrap;">
                                                <span>
                                                    <i class="fas fa-city" style="color:#028090; margin-inline-end:4px;"></i>{{ $order->address->area ?: '—' }}
                                                </span>
                                                @if ($order->address->address_line)
                                                    <span style="color:#CBD5E1;">|</span>
                                                    <span>
                                                        <i class="fas fa-signs-post" style="color:#028090; margin-inline-end:4px;"></i>{{ $order->address->address_line }}
                                                    </span>
                                                @endif
                                            </div>
                                            @if ($order->address->delivery_note)
                                                <div style="margin-top:6px; display:inline-flex; align-items:center; gap:6px; font-size:11px; color:#92400E; background:#FFFBEB; border:1px solid #FDE68A; border-radius:10px; padding:4px 9px;">
                                                    <i class="fas fa-sticky-note"></i>{{ $order->address->delivery_note }}
                                                </div>
                                            @endif
                                        </div>
                                        @if ($mapUrl)
                                            <a href="{{ $mapUrl }}" target="_blank" rel="noopener"
                                               style="flex:0 0 auto; display:inline-flex; align-items:center; gap:6px; background:#ffffff; border:1.6px solid #028090; color:#028090 !important; font-weight:800; font-size:11.5px; padding:8px 12px; border-radius:22px; text-decoration:none; box-shadow:0 3px 10px rgba(2,128,144,.14); align-self:center;">
                                                <i class="fas fa-map-marked-alt" style="font-size:13px; color:#028090;"></i>
                                                <span>الموقع على الخريطة</span>
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <div style="text-align:center; padding:16px; border:1px dashed #CBD5E1; border-radius:14px; color:#94A3B8; background:#F8FAFC;">
                                        <i class="fas fa-map-marker-alt" style="font-size:18px; display:block; margin-bottom:4px;"></i>
                                        لا يوجد عنوان لهذا الطلب
                                    </div>
                                @endif
                            </div>

                            <!-- ===== Rating ===== -->
                            <div class="mt-2 pt-2 d-flex align-items-center justify-content-between flex-wrap gap-2" style="border-top:1px dashed #E2E8F0;">
                                <h3 class="m-0 d-flex align-items-center gap-2" style="font-size:13.5px; font-weight:800; color:#0B1E2E;">
                                    <i class="fas fa-star" style="color:#F59E0B;"></i>
                                    {{ __('Rating') }}
                                </h3>
                                @php
                                    $rate = $order->rating ? (float) $order->rating->rating : 0;
                                @endphp
                                @if ($order->rating)
                                    <div class="d-flex align-items-center gap-2">
                                        <span style="font-size:16px; letter-spacing:2px;">
                                            <i class="fas fa-star {{ $rate >= 1 ? 'rate' : 'unrate' }}"></i>
                                            <i class="fas fa-star {{ $rate >= 2 ? 'rate' : 'unrate' }}"></i>
                                            <i class="fas fa-star {{ $rate >= 3 ? 'rate' : 'unrate' }}"></i>
                                            <i class="fas fa-star {{ $rate >= 4 ? 'rate' : 'unrate' }}"></i>
                                            <i class="fas fa-star {{ $rate == 5 ? 'rate' : 'unrate' }}"></i>
                                        </span>
                                        @if ($order->rating->content)
                                            <span style="font-size:12px; color:#475569; max-width:220px;">{{ $order->rating->content }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span style="font-size:12px; color:#94A3B8;">لا يوجد تقييم بعد</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            <div class="col-md-6 mt-2">
                <div class="card d-flex flex-column h-100">
                    <div class="card-header py-2">
                        <h2 class="m-0">{{ __('Customer Details') }}</h2>
                    </div>
                    <div class="card-body pt-2">
                        <div class="table-responsive-md">
                            <table class="table table-bordered table-striped {{ session()->get('local') }}">
                                <tr>
                                    <th class="py-2">{{ __('Name') }}</th>
                                    <td class="py-2" class="py-2">{{ $order->customer->user->name }}</td>
                                </tr>
                                <tr>
                                    <th class="py-2">{{ __('First_Name') }}</th>
                                    <td class="py-2">{{ $order->customer->user->first_name }}</td>
                                </tr>
                                <tr>
                                    <th class="py-2">{{ __('Last_Name') }}</th>
                                    <td class="py-2">{{ $order->customer->user->last_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="py-2">{{ __('Gender') }}</th>
                                    <td class="py-2">{{ $order->customer->user->gender ? __('' . $order->customer->user->gender) : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="py-2">{{ __('Joined') }}</th>
                                    <td class="py-2">{{ $order->customer->user->created_at?->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <th class="py-2">{{ __('Customer') . ' ' . __('Photo') }}</th>
                                    <td class="py-2">
                                        <img style="max-width: 80px" src="{{ $order->customer->profilePhotoPath }}"
                                            alt="">
                                    </td>
                                </tr>
                                <tr>
                                    <th class="py-2">{{ __('Email') }}</th>
                                    <td class="py-2">{{ $order->customer->user->email }}
                                        @if ($order->customer->user->email_verified_at)
                                            <span class="badge bg-success text-white">{{ __('verified') }}</span>
                                        @else
                                            <span class="badge bg-danger text-white">{{ __('Unverified') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="py-2">{{ __('Phone_number') }}</th>
                                    <td class="py-2">
                                        @if ($order->customer->user->mobile)
                                            {{ $order->customer->user->mobile }}
                                            @if ($order->customer->user->mobile_verified_at)
                                                <span class="badge bg-success text-white">{{ __('verified') }}</span>
                                            @else
                                                <span class="badge bg-danger text-white">{{ __('Unverified') }}</span>
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

@unless(auth()->user()->hasRole('driver'))
        <div class="modal fade" id="assinDriver">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Select Driver') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered {{ session()->get('local') }}">
                                <tr>
                                    <td>{{ __('Name') }}</td>
                                    <td>{{ __('Email') }}</td>
                                    <td>{{ __('Contact') }}</td>
                                    <td>{{ __('Action') }}</td>
                                </tr>
                                @foreach ($drivers as $driver)
                                    @php
                                        $pickup = 0;
                                        $delivery = 0;
                                        foreach ($driver->orders as $driverOrder) {
                                            if (
                                                $driverOrder->pick_date == $order->pick_date &&
                                                $driverOrder->getTime($driverOrder->pick_hour) ==
                                                    $order->getTime($order->pick_hour)
                                            ) {
                                                $pickup += 1;
                                            }
                                            if (
                                                $driverOrder->delivery_date == $order->delivery_date &&
                                                $driverOrder->getTime($driverOrder->delivery_hour) ==
                                                    $order->getTime($order->delivery_hour)
                                            ) {
                                                $delivery += 1;
                                            }
                                        }
                                    @endphp
                                    @if ($pickup < 4 || $delivery < 4)
                                        <tr>
                                            <td class="py-2">{{ $driver->user->name }}</td>
                                            <td class="py-2">{{ $driver->user->email }}</td>
                                            <td class="py-2">{{ $driver->user->mobile }}</td>
                                            <td class="py-2">
                                                <a href="#" data-url="{{ route('driver.assign', [$order->id, $driver->id]) }}"
                                                    class="btn btn-primary assign-driver">{{ __('Assign') }}</a>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach

                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
@endunless
        <style>
            .rate {
                color: rgb(255, 166, 0)
            }

            .unrate {
                color: rgb(136, 136, 136)
            }
        </style>
    </div>
@endsection

@push('scripts')
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });

        function postJson(url, data, onSuccess) {
            return $.ajax({
                url: url,
                method: 'POST',
                data: $.extend({}, data, { _token: "{{ csrf_token() }}" }),
                success: onSuccess,
                error: function(error) {
                    Toast.fire({
                        icon: 'error',
                        title: error.responseJSON?.message || 'فشل الطلب',
                    });
                },
            });
        }

        $('.paid-confirm').on('click', function(e) {
            e.preventDefault();
            const url = $(this).attr('data-url');
            const $btn = $(this);

            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لن تتمكن من التراجع عن هذا الإجراء!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#00B894',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، تم الدفع!',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    postJson(url, {}, function(response) {
                        Toast.fire({
                            icon: 'success',
                            title: response.message,
                        });
                        setTimeout(() => location.reload(), 800);
                    });
                }
            })
        });

        $(document).on('click', '.status-change', function(e) {
            e.preventDefault();
            const status = $(this).attr('data-status');
            const url = "{{ route('order.status.change', $order->id) }}";

            postJson(url, { status: status }, function(response) {
                Toast.fire({
                    icon: 'success',
                    title: response.message,
                });
                setTimeout(() => location.reload(), 800);
            });
        });

        $(document).on('click', '.assign-driver', function(e) {
            e.preventDefault();
            const url = $(this).attr('data-url');
            const $btn = $(this);

            $btn.prop('disabled', true).text('...');

            postJson(url, {}, function(response) {
                Toast.fire({
                    icon: 'success',
                    title: response.message || 'تم تعيين السائق بنجاح',
                });
                setTimeout(() => location.reload(), 800);
            }).always(function() {
                $btn.prop('disabled', false);
            });
        });
    </script>

    {{-- ملصقات القطع (بلوتوث + PDF) --}}
    @php
        $btStickers = $order->products->flatMap(function ($p) use ($order) {
            $qty = max(1, (int) $p->pivot->quantity);
            $title = $p->pivot->title ?: $p->name;
            $item = trim($title . ($p->service?->name ? ' — ' . $p->service->name : ''));
            return collect(range(1, $qty))->map(fn () => [
                'customer' => optional(optional($order->customer)->user)->name ?? '',
                'order' => (string) $order->order_code,
                'item' => $item,
            ]);
        })->values();
    @endphp
    @if (auth()->user()->hasAnyRole(['root', 'admin', 'vendor_admin']) || (auth()->user()->hasRole('vendor_sub') && auth()->user()->can('order.print.labels')))
    <div class="card border-0 shadow-sm mt-4 no-print">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 font-weight-bold"><i class="fas fa-tags text-primary mr-2" aria-hidden="true"></i> ملصقات القطع ({{ $quantity }})</h5>
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-primary btn-sm" type="button" id="printStickersBT">
                    <i class="fas fa-bluetooth-b" aria-hidden="true"></i> طباعة الكل (بلوتوث)
                </button>
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('order.print.labels', ['order' => $order->id, 'quantity' => $quantity]) }}" target="_blank" rel="noopener">
                    <i class="fas fa-file-pdf" aria-hidden="true"></i> ملصقات PDF
                </a>
            </div>
        </div>
        <div class="card-body">
            <div id="bt-stickers-grid" class="d-flex flex-wrap gap-2"></div>
            <script id="bt-stickers-data" type="application/json">@json($btStickers)</script>
        </div>
    </div>
    @endif
@endpush

@push('scripts')
    @php
        $btPayload = \App\Support\PosInvoiceData::make($order->vendor, $order);
        $btPayload['copy'] = 'normal';
    @endphp
    <script id="bt-pos-payload" type="application/json">@json($btPayload)</script>
    <script src="{{ asset('web/js/eclean-bt-core.js') }}?v=bt3"></script>
    <script src="{{ asset('web/js/eclean-bt-invoice.js') }}?v=bt3"></script>
    <script src="{{ asset('web/js/eclean-bt-stickers.js') }}?v=bt3"></script>
    <script src="{{ asset('web/js/eclean-bt-ui.js') }}?v=bt3"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            var invoicePayload = JSON.parse(document.getElementById('bt-pos-payload').textContent || '{}');
            var stickerPayloadEl = document.getElementById('bt-stickers-data');
            var stickerPayload = stickerPayloadEl ? JSON.parse(stickerPayloadEl.textContent || '[]') : [];

            var connInvoice = EcleanBT.connection.create();
            var connLabel = EcleanBT.connection.create();
            var invoicePref = function () { return (connInvoice.loadPref('invoice') || {}).profile || 'rpp320-tspl'; };
            var labelPref = function () { return (connLabel.loadPref('label') || {}).profile || 'generic-escpos'; };
            var printing = false;

            var invoiceMountEl = document.getElementById('bt-invoice-mount');
            if (invoiceMountEl) EcleanBTUI.mount(invoiceMountEl, {
                role: 'invoice',
                connection: connInvoice,
                onTestPrint: async function (c) {
                    if (!invoicePayload.order) return;
                    await EcleanBTInvoice.printInvoice(invoicePayload, {
                        conn: c,
                        profile: invoicePref(),
                        copy: 'normal',
                    });
                },
            });

            var stickersMountEl = document.getElementById('bt-stickers-mount');
            if (stickersMountEl) EcleanBTUI.mount(stickersMountEl, {
                role: 'label',
                connection: connLabel,
                onTestPrint: async function (c) {
                    if (!stickerPayload.length) return;
                    await EcleanBTStickers.printStickers([stickerPayload[0]], {
                        conn: c,
                        profile: labelPref(),
                        widthPx: 384,
                        heightPx: 220,
                    });
                },
            });

            async function printInvoiceWithCopy(copy) {
                if (printing) return;
                printing = true;
                var btn = document.querySelector('[data-bt-invoice="' + copy + '"]');
                var previous = btn ? btn.innerHTML : null;
                if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin" aria-hidden="true"></i> جارٍ الطباعة'; }
                try {
                    await EcleanBT.connection.ensureInteractive(connInvoice, 'invoice');
                    await EcleanBTInvoice.printInvoice(invoicePayload, {
                        conn: connInvoice,
                        profile: invoicePref(),
                        copy: copy,
                    });
                } catch (err) {
                    console.error('[BT] invoice print failed', err);
                } finally {
                    printing = false;
                    if (btn) { btn.disabled = false; btn.innerHTML = previous; }
                }
            }

            document.querySelectorAll('[data-bt-invoice]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    printInvoiceWithCopy(btn.dataset.copy || 'normal');
                });
            });

            async function printStickerList(subset) {
                if (printing) return;
                printing = true;
                try {
                    await EcleanBT.connection.ensureInteractive(connLabel, 'label');
                    await EcleanBTStickers.printStickers(subset, {
                        conn: connLabel,
                        profile: labelPref(),
                        widthPx: 384,
                        heightPx: 220,
                    });
                } catch (err) {
                    console.error('[BT] stickers print failed', err);
                } finally {
                    printing = false;
                }
            }

            var printStickersBtn = document.getElementById('printStickersBT');
            if (printStickersBtn) {
                printStickersBtn.addEventListener('click', function () {
                    printStickerList(stickerPayload);
                });
            }

            /* render sticker thumbnails */
            var grid = document.getElementById('bt-stickers-grid');
            if (grid) {
                stickerPayload.forEach(function (item, idx) {
                    var cell = document.createElement('div');
                    cell.className = 'border rounded p-2 text-center';
                    cell.style.width = '150px';
                    cell.innerHTML = '<div style="font-size:11px;color:#666">قطعة ' + (idx + 1) + '</div>'
                        + '<div style="font-weight:700;font-size:13px">' + (item.customer || '') + '</div>'
                        + '<div style="font-size:11px;color:#333"># ' + item.order + '</div>'
                        + '<div style="font-size:10px;color:#555;margin-top:4px">' + (item.item || '') + '</div>';
                    grid.appendChild(cell);
                });
            }
        });
    </script>
@endpush

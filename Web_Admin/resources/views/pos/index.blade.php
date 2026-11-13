@extends('layouts.app')

@section('content')
@php
    // Buttons below the permission gates: hidden entirely for ungranted
    // child (sub) accounts; owners always see everything.
    $ecIsSub = auth()->user()?->hasRole('vendor_sub');
    $ecCanForSub = fn ($permission) => !$ecIsSub || (bool) auth()->user()?->can($permission);
@endphp
    <style>
        .client_payment_box {
            cursor: pointer;
            transition: all 0.3s;
            height: 50px;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            background: #fff;
        }
        .client_payment_box.selected {
            border: 1px solid #487be9;
            border-radius: 5px;
            background: #f0f4ff;
        }
        .client_payment_box .visa-label {
            font-weight: 800;
            font-size: 20px;
            letter-spacing: 1px;
            color: #1a1f71;
            line-height: 1;
        }
        .client_payment_box.selected .visa-label {
            color: #0a0e5c;
        }
        /* Basket quantity (+/-): explicit contrast — never rely on the
           theme's button text color */
        .ec-qty-btn {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px !important;
            background: linear-gradient(135deg, #028090, #00A896) !important;
            border: none !important;
            color: #fff !important;
            font-size: 15px !important;
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(2, 128, 144, .35);
        }
        .ec-qty-btn i,
        .ec-qty-btn span {
            color: #fff !important;
        }
        .ec-qty-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(2, 128, 144, .45);
        }
        .ec-qty-btn:active {
            transform: translateY(0);
        }
    </style>

    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3">
            <h3 class="mb-0">
                {{ __('Point_Of_Sale') }}
            </h3>
        </div>

        <form id="buscatForm" method="POST">
            @csrf
            <div class="row mb-3">
                <div class="col-xl-8">



                    {{-- services --}}
                    <div class="card border-0 overflow-hidden">
                        <div class="card-header py-3">
                            <h3 class="m-0">{{ __('Select') . ' ' . __('Service') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-flex gap-3 flex-wrap">
                                @forelse ($services as $service)
                                    <div class="service-card" onclick="selectService({{ $service->id }})"
                                        id="service-{{ $service->id }}">
                                        <img src="{{ asset($service->thumbnailPath) }}" alt="" width="100%"
                                            class="rounded">
                                        <h3 class="text-center mb-0">{{ $service->name_bn ?? $service->name }}</h3>
                                    </div>
                                @empty
                                    <h3 class="text-center">
                                        {{ __('No') . ' ' . __('Service') }}
                                    </h3>
                                @endforelse

                            </div>
                        </div>
                    </div>

                    {{-- products --}}



                    <div class="card mt-3 border-0 overflow-hidden" style="border-radius: 12px;">
                        <div class="card-header py-3">
                            <h3 class="m-0">{{ __('Select') . ' ' . __('Products') }}</h3>
                        </div>
                        <div class="card-body">
                            {{-- product list --}}
                            <div class="d-flex gap-3 flex-wrap" id="products">

                            </div>

                        </div>
                    </div>

                </div>
                <div class="col-xl-4 mt-2 mt-xl-0">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="m-0">{{ __('Order') . ' ' . __('Basket') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-3 border-bottom border-light pb-3 mb-3"
                                style="border-top-right-radius: 0 !important;">
                                <div class="flex-grow-1">
                                    <div class="input-group d-flex">
                                        <span class="input-group-text border-start border bg-white ps-2 pe-1 ">
                                            <i class="fas fa-user-circle text-muted"></i>
                                        </span>
                                        <div class="flex-grow-1 customerSelect">
                                            <select name="customer_id" class="form-control select2" style="width: 100%;"
                                                data-placeholder="{{ __('Enter customer name or phone number') }}"
                                                id="customerId">
                                                <option selected value="">
                                                    {{ __('Select Customer') }}
                                                </option>
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}">
                                                        {{ $customer->user?->name . '-(' . $customer->user?->mobile . ')' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>
                                </div>
                                @if ($ecCanForSub('pos.customerStore'))
                                    <div class="flex-grow-1 flex-lg-grow-0">
                                        <button type="button" class="btn btn-outline-primary w-100 py-2" data-toggle="modal"
                                            data-target="#customerModal">
                                            <i class="bi bi-plus-circle-fill me-2"></i>
                                            {{ __('Add Customer') }}
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex flex-wrap gap-3 border-bottom border-light pb-3 mb-3"
                                style="border-top-right-radius: 0 !important;">
                                <div class="flex-grow-1">
                                    <div class="input-group d-flex align-items-center">

                                        <div class="flex-grow-1 addressSelect">
                                            <select name="address_id" class="form-control select2" style="width: 100%;"
                                                data-placeholder="{{ __('Enter customer address') }}" id="addressId">
                                                <option selected value="">{{ __('بدون عنوان') }} — حقل اختياري</option>

                                            </select>
                                            <input type="hidden" name="address_id" id="addressInfo">
                                        </div>

                                    </div>
                                </div>
                                @if ($ecCanForSub('pos.addressStore'))
                                    <div class="flex-grow-1 flex-lg-grow-0">
                                        <button type="button"
                                            class="btn btn-outline-primary w-100 py-2 d-none"id="addAddressButton"
                                            data-toggle="modal" data-target="#addressModal">
                                            <i class="bi bi-plus-circle-fill me-2"></i>
                                            {{ __('Add Address') }}
                                        </button>
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex flex-column gap-3" id="basketProducts">

                            </div>

                            <div class="border-top mt-3 d-flex flex-column">

                                <p
                                    class="mt-1 mb-0 border-bottom border-light py-2 d-flex justify-content-between font-weight-500">
                                    {{ __('Total_Amount:') }} <span id="totalAmount"></span>
                                </p>
                                <input type="hidden" name="total_amount" id="totalAmountInput">

                                <p
                                    class="mb-0 border-bottom border-light py-2 d-flex justify-content-between font-weight-500">
                                    {{ __('Discount:') }} <input type="text" name="discount" id="discountInput"
                                        value="" placeholder="{{ currencyPosition(0) }}" class="form-control w-25" onkeydown="return event.key !== 'Enter';">
                                </p>


                                <p
                                    class="mb-0 border-bottom border-light py-2 d-flex justify-content-between font-weight-500">
                                    {{ __('Delivery_Charge:') }} <input type="text" name="delivery_charge"
                                        id="deliveryChargeInput" value="" placeholder="{{ currencyPosition(0) }}"
                                        class="form-control w-25" onkeydown="return event.key !== 'Enter';">
                                </p>

                                <h3 class="mb-0 border-bottom border-light py-2 d-flex justify-content-between">
                                    {{ __('Grand_Total:') }} <span id="grandTotal"></span>

                                </h3>
                                <input type="hidden" name="grand_total" id="grandTotalInput">

                                <div class="mt-3">
                                    <label class="form-label mb-1">{{ __('Delivery_Date') }}</label>
                                    <input type="date" name="delivery_date" id="deliveryDateInput" class="form-control"
                                        min="{{ now()->format('Y-m-d') }}" value="{{ now()->format('Y-m-d') }}">
                                </div>

                                <div class="mt-2">
                                    <label class="form-label mb-1">ساعة التسليم</label>
                                    <input type="time" name="delivery_hour" id="deliveryHourInput" class="form-control"
                                        value="{{ now()->format('H:i') }}">
                                </div>

                                <div class="mt-3">
                                    <label class="form-label mb-1">{{ __('Notes') }} ({{ __('optional') }})</label>
                                    <textarea name="instruction" id="instructionInput" class="form-control" rows="2"
                                        placeholder="{{ __('Enter order notes') }}"></textarea>
                                </div>

                                <span class="detail mt-3 d-block">{{ __('Payment_method') }}</span>
                                <div class="credit rounded flex row mt-3 row mx-1">
                                    <div
                                        class="col-6 client_payment_box d-flex justify-content-center align-items-center selected"
                                        data-payment-method="cash">
                                        <input type="radio" name="payment_method" value="cash" id="paymentMethodCash"
                                            class="d-none" checked>
                                        <label for="paymentMethodCash" class="mb-0">
                                            <img src="{{ asset('images/cash2.png') }}" class="rounded" width="100"
                                                alt="">
                                        </label>
                                    </div>
                                    <div class="col-6 client_payment_box d-flex justify-content-center align-items-center"
                                        data-payment-method="visa">
                                        <input type="radio" name="payment_method" value="visa" id="paymentMethodVisa"
                                            class="d-none">
                                        <label for="paymentMethodVisa" class="mb-0 d-flex align-items-center gap-2">
                                            <i class="bi bi-credit-card-2-front fs-4 text-primary"></i>
                                            <span class="visa-label">VISA</span>
                                        </label>
                                    </div>
                                </div>
                                <a href="#" class="btn btn-primary btn-sm mb-1 py-2 mt-3 w-100"
                                    onclick="submitform()">
                                    {{ __('Confirm_Order') }}</a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <iframe id="webView"></iframe>
    </div>
    <!-- order success modal - redesigned -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
                <div class="text-center pt-4 pb-3" style="background: linear-gradient(135deg, #00b894 0%, #019875 100%); color: white;">
                    <div class="mb-2">
                        <i class="bi bi-check-circle-fill" style="font-size: 52px; filter: drop-shadow(0 2px 6px rgba(0,0,0,0.15));"></i>
                    </div>
                    <h4 class="mb-1 font-weight-bold" style="font-family: 'Tajawal', sans-serif;">{{ __('Order_Successful') }}</h4>
                    <p class="mb-0" style="font-size: 13px; opacity: 0.9;">تم حفظ الطلب وجاهز للطباعة</p>
                </div>
                <div class="modal-body p-4" style="background: #fff;">
                    <div class="rounded p-3 mb-3" style="background: #f8f9ff; border: 1px solid #e9ecff;">
                        <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid #e9ecff;">
                            <span class="text-muted" style="font-size: 13px;"><i class="bi bi-hash me-1"></i>{{ __('Order_Number') }}</span>
                            <strong id="successOrderCode" class="text-primary" style="font-size: 15px; letter-spacing: 0.3px;"></strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid #e9ecff;">
                            <span class="text-muted" style="font-size: 13px;"><i class="bi bi-currency-exchange me-1"></i>{{ __('Grand_Total') }}</span>
                            <strong id="successTotalAmount" style="font-size: 18px; color: #1a1f71;"></strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid #e9ecff;">
                            <span class="text-muted" style="font-size: 13px;"><i class="bi bi-wallet2 me-1"></i>{{ __('Payment_method') }}</span>
                            <span id="successPaymentMethod" class="badge badge-primary px-3 py-2" style="font-size: 12px; border-radius: 8px;"></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2">
                            <span class="text-muted" style="font-size: 13px;"><i class="bi bi-receipt me-1"></i>حالة الدفع</span>
                            <span id="successPaymentStatusBadge" class="badge badge-warning px-3 py-2" style="font-size: 12px; border-radius: 8px;">
                                <i class="bi bi-clock me-1"></i>غير مدفوع
                            </span>
                        </div>
                    </div>

                    <div class="mb-3" id="markPaidWrapper">
                        <button type="button" id="successMarkPaid" class="btn btn-success w-100 py-2 d-flex align-items-center justify-content-center gap-2" style="border-radius: 12px; font-weight: 700; background: #00b894; border: none; font-size: 14px;">
                            <i class="bi bi-check-circle" style="font-size: 18px;"></i>
                            {{ __('Mark_As_Paid') }} — تأكيد الدفع
                        </button>
                        <small class="text-muted d-block text-center mt-2" style="font-size: 11px;">اضغط لتغيير الحالة إلى مدفوع قبل الطباعة</small>
                    </div>

                    <a id="printOrderInvoice" href="#" class="btn btn-primary w-100 py-3 mb-2 d-flex align-items-center justify-content-center gap-2 print-same-page" style="border-radius: 12px; font-weight: 800; font-size: 15px; background: #487be9; border: none; box-shadow: 0 4px 12px rgba(72,123,233,0.25);">
                        <i class="bi bi-printer" style="font-size: 20px;"></i>
                        {{ __('Print_Invoice') }}
                    </a>

                    <button type="button" class="btn btn-light w-100 py-2" data-dismiss="modal" onclick="location.reload()" style="border-radius: 10px; border: 1px solid #e9ecef; font-weight: 600; color: #495057;">
                        {{ __('Done') }} — متابعة
                    </button>
                    <p class="text-center text-muted mt-3 mb-0" style="font-size: 11px;">يمكنك الطباعة الآن أو بعد تأكيد الدفع — الفاتورة ستعكس حالة الدفع الحالية</p>
                </div>
            </div>
        </div>
    </div>
    @if ($ecCanForSub('pos.addressStore'))
    <!-- address create modal -->
    <form action="#" id="addressForm">
        <div class="modal fade" id="addressModal">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content p-4">
                    <div class="modal-header border-0">
                        <h1 class="modal-title fs-4" id="productModalLabel">
                            {{ __('Add_New_Address') }}
                        </h1>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body py-0">
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mt-3">
                                            <label class="form-label">{{ __('Address_name') }}</label>
                                            <x-input label="First Name" name="address_name" type="text"
                                                placeholder="{{ __('Address_name') }}" class="form-control"
                                                required="true" />
                                        </div>
                                        <input name="customer_id" type="hidden" class="form-control" required="true"
                                            id="hiddenCustomerId" />
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mt-3">
                                            <label class="form-label">{{ __('Road_no') }}</label>
                                            <x-input label="Last Name" name="road_no" type="text"
                                                placeholder="{{ __('Road_no') }}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mt-3">
                                            <label class="form-label">{{ __('House_number') }}</label>
                                            <x-input label="First Name" name="house_no" type="text"
                                                placeholder="{{ __('House_number') }}" class="form-control" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mt-3">
                                            <label class="form-label">{{ __('Houser_name') }}</label>
                                            <x-input label="First Name" name="house_name" type="text"
                                                placeholder="{{ __('House_name') }}" class="form-control" required />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mt-3">
                                            <label class="form-label">{{ __('Area') }}</label>
                                            <x-input label="First Name" name="area" type="text"
                                                placeholder="{{ __('Area') }}" class="form-control" required />
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="mt-3 p-3 rounded" style="background:#f8fafc; border:1px dashed #cbd5e1;">
                                            <div class="d-flex flex-wrap align-items-center gap-2">
                                                <button type="button" class="btn btn-outline-primary" id="posUseMyLocation">
                                                    <i class="fas fa-map-marker-alt me-2"></i>تحديد موقعي الحالي (موقع العميل)
                                                </button>
                                                <a href="#" id="posViewOnMap" class="btn btn-outline-info d-none" target="_blank" rel="noopener">
                                                    <i class="fas fa-map-marked-alt me-2"></i>رؤية الموقع على الخريطة
                                                </a>
                                                <small id="posLocationStatus" class="text-muted"></small>
                                            </div>
                                            <input type="hidden" name="latitude" id="posLatitude">
                                            <input type="hidden" name="longitude" id="posLongitude">
                                            <div class="mt-3">
                                                <label class="form-label mb-1" for="posLocationText">الموقع <span class="text-muted">(اختياري — اكتبه يدويًا)</span></label>
                                                <input type="text" name="address_line" id="posLocationText" class="form-control"
                                                    placeholder="اكتبه يدويًا (مثال: الشارع الرئيسي بجانب الفرن)" />
                                            </div>
                                            <div class="mt-3">
                                                <label class="form-label mb-1" for="posLocationNote">وصف الموقع <span class="text-muted">(اختياري)</span></label>
                                                <input type="text" name="delivery_note" id="posLocationNote" class="form-control"
                                                    placeholder="مثال: مبنى أبيض — الطابق الثاني، اطرق على الجرس" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    <div
                        class="modal-footer border-0 mt-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <button type="button" class="btn btn-danger py-3 flex-grow-1" data-dismiss="modal">
                            {{ __('Close') }}
                        </button>

                        <button type="submit" class="btn btn-primary py-3 flex-grow-1">
                            {{ __('Submit') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @endif
    <!-- customer create modal -->
    @if ($ecCanForSub('pos.customerStore'))
    <form action="#" id="customerForm">
        <div class="modal fade" id="customerModal">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content p-4">
                    <div class="modal-header border-0">
                        <h1 class="modal-title fs-4" id="productModalLabel">
                            {{ __('Add_New_Customer') }}
                        </h1>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body py-0">
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mt-3">
                                            <label class="form-label">{{ __('First_Name') }}</label>
                                            <x-input label="First Name" name="first_name" type="text"
                                                placeholder="{{ __('First_Name') }}" class="form-control"
                                                required="true" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mt-3">
                                            <label class="form-label">{{ __('Last_Name') }}</label>
                                            <x-input label="Last Name" name="last_name" type="text"
                                                placeholder="{{ __('Enter_Last_Name') }}" />
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="form-label">{{ __('Phone_Number') }}</label>
                                    <x-input label="Phone Number" name="phone" type="number"
                                        placeholder="{{ __('Enter_Phone_Number') }}" required="true" />
                                </div>

                                <div class="mt-3">
                                    <label class="form-label">{{ __('Gender') }}</label>
                                    <x-select label="Gender" name="gender">
                                        <option value="male" selected>{{ __('Male') }}</option>
                                        <option value="female">{{ __('Female') }}</option>
                                    </x-select>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label">{{ __('Email') }}</label>
                                    <x-input type="email" name="email" label="Email"
                                        placeholder="{{ __('Enter_Email_Address') }}" />
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mt-3">
                                            <label class="form-label">{{ __('Password') }}</label>
                                            <x-input label="Password" name="password" type="password"
                                                placeholder="{{ __('Password') }}" required="true" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mt-3">
                                            <label class="form-label">{{ __('Confirm_Password') }}</label>
                                            <x-input label="Confirm Password" name="password_confirmation"
                                                type="password" placeholder="{{ __('Confirm_Password') }}"
                                                required="true" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="is_active" value="1">
                    </div>
                    <div
                        class="modal-footer border-0 mt-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <button type="button" class="btn btn-danger py-3 flex-grow-1" data-dismiss="modal">
                            {{ __('Close') }}
                        </button>

                        <button type="submit" class="btn btn-primary py-3 flex-grow-1">
                            {{ __('Submit') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @endif
@endsection
@push('scripts')
    <script>
        // Safe Ajax error message — never crash on HTML 403/419/500 bodies.
        window.ecErrorMessage = function (error, fallback) {
            if (error && error.responseJSON) {
                if (error.responseJSON.message) {
                    return error.responseJSON.message;
                }
                if (error.responseJSON.errors) {
                    var first = Object.values(error.responseJSON.errors)[0];
                    return Array.isArray(first) ? first[0] : first;
                }
            }
            if (error && error.status === 403) {
                return 'غير مصرح — يحتاج الحساب صلاحية من مالك المتجر';
            }
            if (error && error.status === 419) {
                return 'انتهت الجلسة — حدّث الصفحة وحاول مجددًا';
            }
            return fallback || 'حدث خطأ — حاول مرة أخرى';
        };
        $(document).ready(function() {
            $('#customerId').on('change', function() {
                let customerId = $(this).val();
                let addressSelect = $('#addressId');

                $('#hiddenCustomerId').val(customerId);

                let addAddressButton = $('#addAddressButton');

                if (customerId) {

                    addAddressButton.removeClass('d-none');
                } else {

                    addAddressButton.addClass('d-none');
                }
                if (customerId) {
                    addressSelect.html('<option value="">{{ __('Loading...') }}</option>');

                    $.ajax({
                        url: '/customers/' +
                            customerId + '/addresses',
                        type: 'GET',
                        success: function(response) {
                            var html = '<option value="">{{ __('بدون عنوان') }} — حقل اختياري</option>';
                            response.forEach(function(address) {
                                var name = address.address_name || address.address_line || ('عنوان ' + address.id);
                                var detail = [address.area, address.road_no, address.house_no]
                                    .filter(function (v) { return v && String(v).trim() !== ''; })
                                    .join('، ');
                                html += '<option value="' + address.id + '">'
                                    + name
                                    + (detail ? ' — ' + detail : '')
                                    + '</option>';
                            });
                            addressSelect.html(html);
                            // select2 caches its own copy of <option> — rebuild it so new rows appear.
                            addressSelect.select2('destroy').select2({ placeholder: '{{ __('Select Address') }}' });
                        },
                        error: function() {
                            addressSelect.html('<option value="">{{ __('Address field is hidden — no permission') }}</option>');
                            addressSelect.select2('destroy').select2({ placeholder: '{{ __('Select Address') }}' });
                        },
                    });
                } else {
                    addressSelect.html('<option value="">{{ __('بدون عنوان') }} — حقل اختياري</option>');
                    addressSelect.select2('destroy').select2({ placeholder: '{{ __('Select Address') }}' });
                }
            });
        });



        $(document).on('click', '.client_payment_box', function() {
            $(this).addClass('selected').siblings().removeClass('selected');
            $(this).find('input[type="radio"]').prop('checked', true);
        });

        function submitform() {

            let stripe, elements, cardElement;
            const form = document.getElementById('buscatForm');
            const formData = new FormData(form);

            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });


            data['products'] = basket.map(product => ({
                id: product.id,
                quantity: product.quantity,
                price: product.current_price
            }));

            if (!data['customer_id']) {
                alert('customer must be selected');
                return;
            }
            if (!data['products'] || data['products'].length == 0) {
                alert('product must be selected');
                return;
            }
            var payment_gateway = data['payment_gateway'] ?? 'cash'
            var payment_method = $('input[name="payment_method"]:checked').val() || 'cash';

            let addressId = $('select[name="address_id"]').val();


            $.ajax({
                url: "{{ route('pos.store') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    customer_id: data['customer_id'],
                    delivery_charge: data['delivery_charge'],
                    delivery_date: data['delivery_date'],
                    delivery_hour: data['delivery_hour'],
                    payment_id: payment_gateway,
                    payment_method: payment_method,
                    payment_status: 'pending',
                    instruction: data['instruction'],
                    discount: data['discount'],
                    products: data['products'],
                    address_id: $('#addressId').val() || null,
                },
                success: (response) => {

                    if (response.data['payment_type'] != 'cash') {

                        const iframe = document.getElementById('webView');
                        let url = response.data['payment_url'];

                        const popupWidth = 600;
                        const popupHeight = 600;
                        const screenWidth = window.screen.width;
                        const screenHeight = window.screen.height;
                        const left = (screenWidth - popupWidth) / 2;
                        const top = (screenHeight - popupHeight) / 2;

                        const popup = window.open(
                            url,
                            "WebViewPopup",
                            `width=${popupWidth},height=${popupHeight},top=${top},left=${left},resizable=yes,scrollbars=yes`
                        );
                        if (popup) {

                            const checkPopupLoaded = setInterval(() => {

                                if (popup.document.readyState === 'complete') {
                                    popup.postMessage({
                                        order: response.data.orders
                                    }, '*');
                                    clearInterval(
                                        checkPopupLoaded);
                                }
                            }, 100);
                            setTimeout(() => {
                                location.reload();
                            }, 2000);


                        } else {
                            console.error('Popup could not be opened.');
                        }


                    } else if (response.data['payment_type'] === 'cash') {

                        var order = response.data.orders;
                        window.lastOrderId = order.id;

                        $('#successOrderCode').text(order.order_code);
                        $('#successTotalAmount').text(currencyPosition(order.total_amount));
                        var pmText = order.payment_method === 'visa' ? 'VISA' : 'كاش';
                        var pmIcon = order.payment_method === 'visa' ? '<i class="bi bi-credit-card me-1"></i>' : '<i class="bi bi-cash me-1"></i>';
                        $('#successPaymentMethod').html(pmIcon + pmText);
                        // reset payment status to unpaid — identity badge (white text on gold)
                        $('#successPaymentStatusBadge').removeClass('badge-success').addClass('badge-warning').html('<i class="bi bi-clock me-1"></i>غير مدفوع');
                        $('#successMarkPaid').show().prop('disabled', false).html('<i class="bi bi-check-circle" style="font-size:18px;"></i> {{ __('Mark_As_Paid') }} — تأكيد الدفع');
                        $('#markPaidWrapper').show();
                        $('#printOrderInvoice').attr('href', "{{ route(auth()->user()->hasAnyRole(['root', 'admin']) || (auth()->user()->hasRole('vendor_admin')) || (auth()->user()->hasRole('vendor_sub') && auth()->user()->can('order.pos-invoice')) ? 'order.pos-invoice' : 'order.print.invioce', ':orderId') }}".replace(':orderId', order.id));
                        $('#successModal').modal('show');

                    }


                },
                error: (error) => {
                    var msg = window.ecErrorMessage(error, 'تعذر تنفيذ العملية');
                    Toast.fire({
                        icon: 'error',
                        title: msg,
                    });
                    console.log(msg);
                },


            });


        }
    </script>
    <script>
        var currentServiceId = null;

        function selectService(serviceId) {
            $('.service-card').removeClass('active');
            $('#service-' + serviceId).addClass('active');
            currentServiceId = serviceId;
            fetchProducts();
        }

        function fetchProducts() {
            $.ajax({
                url: "{{ route('pos.fetch.products') }}",
                type: 'GET',
                data: {
                    service_id: currentServiceId
                },
                success: function(response) {
                    // Clear existing products
                    $('#products').empty();

                    // Append new products
                    response.data.products.forEach(function(product) {
                        var productCard = $('<div>')
                            .addClass('service-card')
                            .attr('onclick', 'addProductToBasket(' + JSON.stringify(product) + ')');

                        var productImage = $('<img>')
                            .attr('src', product.image_path)
                            .attr('alt', '')
                            .css('width', '100%')
                            .addClass('rounded');

                        var productTitle = $('<h3>')
                            .addClass('text-center mb-0')
                            .text(product.name);

                        productCard.append(productImage).append(productTitle);
                        $('#products').append(productCard);
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching products:', error);
                }
            });
        }

        let basket = [];

        function addProductToBasket(product) {
            // Check if product is already in basket
            let existingProduct = basket.find(p => p.id === product.id);

            if (existingProduct) {
                existingProduct.quantity += 1;
            } else {
                product.quantity = 1;
                basket.push(product);
            }

            renderBasket();
            updateTotalAmount();
        }


        function renderBasket() {
            $('#basketProducts').empty();

            basket.forEach(product => {
                let productDiv = $(`
                <input type="hidden" name="products[${product.id}][id]" value="${product.id}">
                <input type="hidden" name="products[${product.id}][quantity]" value="${product.quantity}">
                <div class="d-flex gap-3 align-items-center pt-2" style="border-top: 1px dashed #eee" data-product-id="${product.id}">
                    <div class="border rounded">
                        <img src="${product.image_path}" alt="" width="100" height="90" class="rounded object-fit-cover">
                    </div>
                    <div class="flex-grow-1">
                        <h3 class="m-0 d-flex justify-content-between flex-wrap gap-3">
                            ${product.name}
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeProductFromBasket(${product.id})">
                                <i class="fa fa-trash"></i>
                            </button>
                        </h3>
                        <p class="m-0 font-weight-500">
                            ${currencyPosition(product.current_price)}
                        </p>
                        <input type="hidden" name="products[${product.id}][price]" value="${product.current_price}">
                        <div class="d-flex align-items-center gap-3">
                            <button type="button" class="btn btn-sm m-0 ec-qty-btn" onclick="changeQuantity(${product.id}, -1)" aria-label="إنقاص الكمية">
                                <i class="fa fa-minus"></i>
                            </button>
                            <input type="text" class="text-center border rounded" value="${product.quantity}" readonly style="width: 46px">
                            <button type="button" class="btn btn-sm m-0 ec-qty-btn" onclick="changeQuantity(${product.id}, 1)" aria-label="زيادة الكمية">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `);
                $('#basketProducts').append(productDiv);
            });

            updateTotalAmount();
        }

        function changeQuantity(productId, change) {
            let product = basket.find(p => p.id === productId);

            if (product) {
                product.quantity += change;
                if (product.quantity <= 0) {
                    removeProductFromBasket(productId);
                } else {
                    renderBasket();
                }
            }
        }

        function removeProductFromBasket(productId) {
            basket = basket.filter(p => p.id !== productId);
            renderBasket();
        }

        $('#discountInput').on('input', function() {
            var input = document.getElementById('discountInput');
            input.value = input.value.replace(/[^0-9.]/g, '');
            updateTotalAmount();
        });

        $('#deliveryChargeInput').on('input', function() {
            var input = document.getElementById('deliveryChargeInput');
            input.value = input.value.replace(/[^0-9.]/g, '');
            updateTotalAmount();
        });

        function updateTotalAmount() {
            let totalAmount = basket.reduce((total, product) => {
                let price = product.current_price;
                return total + (price * product.quantity);
            }, 0);

            $('#totalAmount').text(currencyPosition(totalAmount.toFixed(2)));
            $('#totalAmountInput').val(totalAmount.toFixed(2));

            let discount = parseFloat($('#discountInput').val()) || 0;
            let deliveryCharge = parseFloat($('#deliveryChargeInput').val()) || 0;
            let grandTotal = totalAmount - discount + deliveryCharge;

            if (grandTotal < 0) {
                grandTotal = 0;
            }

            $('#grandTotal').text(currencyPosition(grandTotal.toFixed(2)));
            $('#grandTotalInput').val(grandTotal.toFixed(2));
        }

        function currencyPosition(amount) {
            @php $curr = \App\Models\WebSetting::first()?->currency ?? '₪'; $pos = config('app.currency_position','Prefix'); @endphp
            var curr = @json($curr);
            var pos = @json($pos);
            if (pos === 'Suffix') return amount + ' ' + curr;
            return curr + ' ' + amount;
        }

        function submitOrder() {
            console.log('Order submitted:', basket);
        }

        $('#successMarkPaid').on('click', function() {
            var $btn = $(this);
            var paymentStatusUrl = "{{ route('order.payment-status', ':orderId') }}".replace(':orderId', window.lastOrderId);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> جارٍ التحديث...');
            $.ajax({
                url: paymentStatusUrl,
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    payment_status: 'paid',
                },
                success: function(response) {
                    $('#successPaymentStatusBadge').removeClass('badge-warning').addClass('badge-success').html('<i class="bi bi-check-circle-fill me-1"></i>مدفوع');
                    $btn.html('<i class="bi bi-check-circle-fill me-1"></i> تم التأكيد — مدفوع').removeClass('btn-success').addClass('btn-outline-success').prop('disabled', true);
                    setTimeout(function(){ $('#markPaidWrapper').slideUp(300); }, 1200);
                    Toast.fire({
                        icon: 'success',
                        title: response.data.message || 'تم تحديث حالة الدفع إلى مدفوع',
                    });
                },
                error: function(error) {
                    $btn.prop('disabled', false).html('<i class="bi bi-check-circle" style="font-size:18px;"></i> {{ __('Mark_As_Paid') }} — تأكيد الدفع');
                    var msg = error.responseJSON?.message;
                    if (!msg && error.status === 403) {
                        msg = 'غير مصرح بتأكيد الدفع — يحتاج الحساب صلاحية «تأكيد الدفع» من صاحب المتجر';
                    }
                    Toast.fire({
                        icon: 'error',
                        title: msg || 'فشل التحديث',
                    });
                },
            });
        });

        $('#customerForm').submit(function(e) {
            e.preventDefault();

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                },
            });

            $.ajax({
                url: "{{ route('pos.customerStore') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    first_name: $('input[name="first_name"]').val(),
                    last_name: $('input[name="last_name"]').val(),
                    mobile: $('input[name="phone"]').val(),
                    gender: $('select[name="gender"]').val(),
                    email: $('input[name="email"]').val(),
                    password: $('input[name="password"]').val(),
                    password_confirmation: $('input[name="password_confirmation"]').val(),
                },
                success: (response) => {
                    $('#customerModal').modal('hide');
                    Toast.fire({
                        icon: 'success',
                        title: response.message,
                    });

                    var customer = $('select[name="customer_id"]');
                    var user = response.data.user;

                    customer.append(
                        `<option value="${user.id}" selected>${user.name}-(${user.mobile})</option>`
                    );
                    customer.val(user.id);
                    // Fire the same change pipeline as a manual selection so
                    // addresses reload and hiddenCustomerId is populated.
                    customer.trigger('change');

                    $('input[name="first_name"]').val('');
                    $('input[name="last_name"]').val('');
                    $('input[name="phone"]').val('');
                    $('select[name="gender"]').val('');
                    $('input[name="email"]').val('');
                    $('input[name="password"]').val('');
                    $('input[name="password_confirmation"]').val('');
                },
                error: (error) => {
                    var msg = window.ecErrorMessage(error, 'تعذر تنفيذ العملية');
                    Toast.fire({
                        icon: 'error',
                        title: msg,
                    });
                    console.log(msg);
                },
            });
        });

        // ===== POS "Add New Address": locate the customer on the map =====
        $('#posUseMyLocation').on('click', function() {
            const btn = $(this);
            const status = $('#posLocationStatus');
            const mapLink = $('#posViewOnMap');

            if (!navigator.geolocation) {
                status.text('المتصفح لا يدعم تحديد الموقع');
                return;
            }

            btn.prop('disabled', true);
            status.text('جاري تحديد الموقع…');

            navigator.geolocation.getCurrentPosition(function(pos) {
                const lat = pos.coords.latitude.toFixed(6);
                const lng = pos.coords.longitude.toFixed(6);

                $('#posLatitude').val(lat);
                $('#posLongitude').val(lng);

                mapLink.attr('href', 'https://www.google.com/maps?q=' + lat + ',' + lng)
                    .removeClass('d-none');

                status.text('تم تحديد الموقع (' + lat + ', ' + lng + ')');
                btn.prop('disabled', false);
                // «الموقع» (address_line) is deliberately NOT auto-filled —
                // the vendor types it manually when needed.
            }, function(err) {
                status.text(err && err.code === 1 ? 'تم رفض إذن الموقع من المتصفح' : 'تعذر تحديد الموقع');
                btn.prop('disabled', false);
            }, {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            });
        });

        $('#addressForm').submit(function(e) {
            e.preventDefault();

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                },
            });

            $.ajax({
                url: "{{ route('pos.addressStore') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    address_name: $('input[name="address_name"]').val(),
                    road_no: $('input[name="road_no"]').val(),
                    house_no: $('input[name="house_no"]').val(),
                    house_name: $('input[name="house_name"]').val(),
                    area: $('input[name="area"]').val(),
                    latitude: $('#posLatitude').val(),
                    longitude: $('#posLongitude').val(),
                    address_line: $('#posLocationText').val(),
                    delivery_note: $('#posLocationNote').val(),
                    customer_id: $('input[name="customer_id"]').val(),
                },
                success: (response) => {
                    $('#addressModal').modal('hide');
                    Toast.fire({
                        icon: 'success',
                        title: response.message,
                    });

                    var addressinfo = $('select[name="address_id"]');
                    var address = response.data.address;
                    console.log(response.data,address);


                    addressinfo.append(
                        `<option value="${address.id}"> ${address.address_name} (${address.road_no})</option>`
                    );
                    addressinfo.val(address.id);

                    $('input[name="address_name"]').val('');
                    $('input[name="road_no"]').val('');
                    $('input[name="house_no"]').val('');
                    $('input[name="house_name"]').val('');
                    $('input[name="area"]').val('');
                    $('#posLatitude').val('');
                    $('#posLongitude').val('');
                    $('#posLocationText').val('');
                    $('#posLocationNote').val('');
                    $('#posLocationStatus').text('');
                    $('#posViewOnMap').attr('href', '#').addClass('d-none');
                },
                error: (error) => {
                    var msg = window.ecErrorMessage(error, 'تعذر تنفيذ العملية');
                    Toast.fire({
                        icon: 'error',
                        title: msg,
                    });
                    console.log(msg);
                },
            });
        });
    </script>
@endpush

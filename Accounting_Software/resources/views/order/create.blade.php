<div>
    <!-- Search Input -->
    <!-- Search Input will be teleported to navbar inside dive #searchInputPlace -->
    @teleport('#searchInputPlace')
    <div class="nav-item d-flex align-items-center">
        <i class="bx bx-search fs-4 lh-0"></i>
        <input type="text" class="form-control border-0 shadow-none ps-1 ps-sm-2" placeholder="بحث عن العميل..."
               aria-label="بحث عن العميل..." wire:model.live.debounce.500ms="customerForm.search"/>
    </div>
    @endteleport
    <!-- / Search Input -->

    <!-- Notification -->
    @session('success-msg')
    <div
        x-data
        x-init="setTimeout(()=> $el.remove(),3000)"
        class="mb-3">
        <div class="alert alert-info alert-dismissible fade show text-dark" role="alert">
            <strong>{{ $value }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    @endsession
    <!-- / Notification -->

    <div>
        <!-- Search Results -->
        @if($customerForm->search)
            <div class="row gy-3 mb-4">
                <div class="col-md">
                    <div class="card">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-header pb-2">
                                نتائج البحث
                                <i class='ms-2 bx bx-loader-circle bx-spin' wire:loading
                                   wire:target="customerForm.search"></i>
                            </h5>
                            <span class="me-3 fs-5">
                        ({{ count($customerForm->customers) }})
                    </span>
                        </div>
                        <div class="card-body pt-2" style="max-height: 450px; min-height: 93px; overflow-y: scroll">
                            <div class="d-flex flex-column gap-1">

                                @foreach($customerForm->customers as $customer)
                                    <div wire:key="{{ 'customers-' . $customer->id . $customer->phone }}"
                                         class="col-md d-flex gap-2 align-items-center">
                                        <div
                                            @class([
                                                        'form-check custom-option custom-option-basic flex-grow-1',
                                                        'checked' => $customerForm->id === $customer->id
                                                ])
                                        >
                                            <label
                                                class="form-check-label custom-option-content py-1 d-flex align-items-center gap-2"
                                                for="{{ $customer->id . $customer->phone }}">
                                                <input name="customer_id" class="form-check-input" type="radio"
                                                       wire:model.live="customerForm.id" value="{{ $customer->id }}"
                                                       id="{{ $customer->id . $customer->phone }}">
                                                <div>
                                            <span class="custom-option-header">
                                                <span class="h6 mb-0">{{ $customer->name }}</span>
                                            </span>
                                                    <span class="custom-option-body">
                                                <small>{{ $customer->phone }}</small>
                                            </span>
                                                </div>
                                            </label>
                                        </div>

                                        <a href="{{ route('customers.show', $customer) }}" target="_blank"
                                           title="سجل العميل"
                                           class="btn btn-linkedin">
                                            <i class='bx bx-link-external'></i>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                            <x-input-error key="customerForm.id"/>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <!-- / Search Results -->

        <!-- Order Details -->
        <div class="row gy-3 mb-3">

            <!-- Services -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header pt-2 pb-3">
                        <span class="fs-5 text-black">الخدمات</span>
                    </div>
                    <div class="card-body" style=" max-height: 275px; overflow-y: scroll">
                        <div class="d-flex flex-column gap-2">

                            @foreach($this->orderForm->services() as $service)
                                <div wire:key="{{ 'service-' . $service->id }}"
                                     class="col-md d-flex gap-2 align-items-center">
                                    <div
                                        @class([
                                                    'form-check custom-option custom-option-basic flex-grow-1',
                                                    'checked' => $orderForm->service_id == $service->id
                                            ])
                                    >
                                        <label
                                            class="form-check-label custom-option-content py-3 d-flex align-items-center gap-2"
                                            for="{{ '$service-' . $service->id }}">
                                            <input name="service__id" class="form-check-input" type="radio"
                                                   wire:model.live="orderForm.service_id" value="{{ $service->id }}"
                                                   id="{{ '$service-' . $service->id }}">
                                            <div>
                                            <span class="custom-option-header">
                                                <span class="h6 mb-0">{{ $service->name }}</span>
                                            </span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <x-input-error key="orderForm.service_id"/>
                    </div>
                </div>
            </div>
            <!-- / Services -->

            <!-- Items -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header pt-2 pb-3">
                        <span class="fs-5 text-black">الاصناف</span>
                        <i class='ms-2 bx bx-loader-circle bx-spin' wire:loading
                           wire:target="orderForm.service_id"></i>
                    </div>
                    <div class="card-body" style=" max-height: 275px; overflow-y: scroll">
                        <div class="row gy-3" wire:key="{{ rand() }}">
                            @foreach($orderForm->items as $item)
                                <div wire:key="{{ 'item-' . $item->id }}" class="col-md-3">
                                    <div
                                        @class([
                                                   'form-check custom-option custom-option-basic',
//                                                   'checked' => $orderForm->item_id == $item->id
                                               ])
                                    >
                                        <label class="form-check-label custom-option-content "
                                               for="{{ '$item-' . $item->id }}">
                                            <input name="customRadioTemp" class="form-check-input" type="radio"
                                                   value="{{ $item->id }}" wire:model.live="orderForm.item_id"
                                                   id="{{ '$item-' . $item->id }}">
                                            <span class="custom-option-header">
                                              <span class="h6 mb-0">{{ $item->name }}</span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <x-input-error key="orderForm.service_id"/>
                    </div>
                </div>
            </div>
            <!-- / Items -->

            <!-- Customer Info & Orders -->

            <!-- Customer Info -->
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header pt-3 pb-1 d-flex justify-content-between align-items-center">
                        <h5>بيانات العميل</h5>
                        @if($customerForm->id)
                            <div>
                                <label for="customer_number" class="form-label">
                                    رقم العميل :
                                </label>
                                <span id="customer_number">{{ $customerForm->id }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">

                        <div class="row">
                            <div class="mb-1 col-md-6">
                                <label for="customer_name" class="form-label">
                                    اسم العميل
                                </label>
                                <input class="form-control" type="text" id="customer_name"
                                       wire:model="customerForm.name">
                                <x-input-error key="customerForm.name"/>
                            </div>
                            <div class="mb-1 col-md-6">
                                <label for="customer_phone" class="form-label">
                                    رقم التليفون
                                </label>
                                <input class="form-control text-start" type="tel" id="customer_phone"
                                       autocomplete="tel"
                                       wire:model="customerForm.phone">
                                <x-input-error key="customerForm.phone"/>
                            </div>
                            <div>
                                <label for="customer_address" class="form-label">
                                    العنوان
                                </label>
                                <input class="form-control" type="text" id="customer_address"
                                       wire:model="customerForm.address">
                                <x-input-error key="customerForm.address"/>
                            </div>
                            @if($customerForm->id)
                                <div class="mt-3 d-flex flex-column flex-md-row gap-3"
                                     wire:key="{{ 'delete-customer' . $customerForm->id }}">
                                    <button wire:click="cancelCustomer" class="btn btn-danger"
                                            wire:key="{{ 'delete-' . $customerForm->id }}" type="button">
                                        <i class="bx bx-trash me-1"></i>
                                        حذف العميل المحدد
                                    </button>

                                    <a href="{{route('customers.show', $customerForm->id)}}" class="btn btn-linkedin"
                                       target="_blank"
                                       type="button">
                                        طلبات العميل السابقة
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- / Customer Info -->

            <!-- Orders Table -->
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header pt-3 pb-1">
                        <h5>الاوردر</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered">
                                <thead>
                                <tr class="text-nowrap">
                                    <th class="text-center px-2">#</th>
                                    <th class="text-center px-2">الصنف</th>
                                    <th class="text-center px-2">الخدمه</th>
                                    <th class="text-center px-2">العدد</th>
                                    <th class="text-center px-2">السعر</th>
                                    <th class="text-center px-2">الاجمالي</th>
                                </tr>
                                </thead>
                                <tbody wire:key="{{ rand() }}">

                                @foreach($orderForm->orders as $index =>  $order)
                                    <tr
                                        wire:key="{{ 'orderDetail-' . $index }}"
                                        wire:click="selectOrder('{{ $index }}')"
                                        @class([
                                                'text-center cursor-pointer',
                                                'bg-secondary text-white' => $index == $selectedOrder
                                                ])
                                    >
                                        <td>
                                            <strong>{{ $loop->iteration }}</strong>
                                        </td>
                                        <td>
                                            {{ $order['item']['name'] }}
                                        </td>
                                        <td>
                                            {{ $order['service']['name'] }}
                                        </td>
                                        <td>
                                            {{ $order['quantity'] }}
                                        </td>
                                        <td>
                                            {{ $order['price'] }}
                                        </td>
                                        <td>
                                            {{ $order['total_price'] }}
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>

                        <x-input-error key="error_msg" />
                        @if($selectedOrder !== '')
                            <div class="mt-2">
                                <div
                                    class="d-flex align-items-center g-1 justify-content-between align-items-center  row">
                                    <button class="btn btn-primary col-md-4" wire:click="deleteOrder">حذف
                                        الصنف
                                    </button>
                                    <label class="col-3" wire:key="{{ rand() }}">
                                        <span>العدد </span>
                                        <input type="number" class="form-control" min="1"
                                               wire:model="orderForm.orders.{{ $selectedOrder }}.quantity"
                                               wire:input.debounce="applyChanges">
                                    </label>

                                    <label class="col-3" wire:key="{{ rand() }}">
                                        السعر
                                        @if(isset($orderForm->orders[$selectedOrder]['note']))
                                            <span>( {{ $orderForm->orders[$selectedOrder]['note'] }} )</span>
                                        @endif

                                        <input type="number" class="form-control" min="1"
                                               wire:model="orderForm.orders.{{ $selectedOrder }}.price"
                                               wire:input.debounce="applyChanges">
                                    </label>
                                </div>
                                <div wire:key="{{ rand() }}">
                                    <label for="order_detail_description" class="form-label">
                                        تفاصيل إضافيه
                                    </label>
                                    <textarea id="order_detail_description" class="form-control" style="resize: none"
                                              cols="30" rows="3"
                                              wire:model="orderForm.orders.{{ $selectedOrder }}.description"
                                    ></textarea>
                                    <x-input-error key="orderForm.orders.{{ $selectedOrder }}.description"/>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <!-- / Orders Table -->
            <!-- / Customer Info & Orders -->
        </div>
        <!-- / Order Deta        <!-- Payment & Submit -->
        <div class="row gy-3">
            <!-- Payment Details & Notes -->
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header pt-3 pb-1">
                        <h5>تفاصيل الطلب والخصم</h5>
                    </div>
                    <div class="card-body">
                        <div class="row gy-2">
                            <div class="col-md-6">
                                <label for="discount" class="form-label">الخصم (₪)</label>
                                <input class="form-control" type="number" id="discount" min="0"
                                       wire:model.live.debounce.500ms="discount">
                                <x-input-error key="discount"/>
                            </div>
                            <div class="col-md-6">
                                <label for="del-time" class="form-label">وقت التسليم المتوقع</label>
                                <input type="time" id="del-time" class="form-control" wire:model.live="deliver_time">
                                <x-input-error key="deliver_time"/>
                            </div>
                            <div class="col-12 mt-2">
                                <label for="notes" class="form-label">ملاحظات الطلب</label>
                                <textarea id="notes" class="form-control" style="resize: none" rows="2"
                                          wire:model="notes" placeholder="اكتب أي ملاحظات للطلب هنا..."></textarea>
                                <x-input-error key="notes"/>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body text-center">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <label for="del-date" class="form-label fw-bold">تاريخ التسليم</label>
                                <input type="date" id="del-date" class="form-control" wire:model.live="deliver_date">
                                <x-input-error key="deliver_date"/>
                            </div>
                        </div>
                        <hr>
                        <x-input-error key="error_msg" />
                        <div wire:key="{{ rand() }}" class="row justify-content-center" x-data="{isLoading:false}">
                            <button wire:click="saveOrder" @click="isLoading = true" :disabled="isLoading"
                                    class="btn btn-primary btn-lg w-100">
                                <i class="bx bx-check-circle me-1"></i>
                                حفظ وإنشاء الطلب
                                <i class='ms-2 bx bx-loader-circle bx-spin'
                                   x-show="isLoading" x-cloak
                                ></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Type -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header text-center pb-2 pt-3">
                        <h5>طريقة الدفع</h5>
                        <div class="d-flex flex-wrap justify-content-center gap-3 mt-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="payment_type" id="cash" value="1"
                                       wire:model.live="payment_type">
                                <label class="form-check-label h6" for="cash">نقدي (كاش)</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="payment_type" id="visa" value="3"
                                       wire:model.live="payment_type">
                                <label class="form-check-label h6" for="visa">فيزا</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="payment_type" id="deferred" value="2"
                                       wire:model.live="payment_type">
                                <label class="form-check-label h6" for="deferred">آجل / على الحساب</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="payment_type" id="partial" value="4"
                                       wire:model.live="payment_type">
                                <label class="form-check-label h6" for="partial">دفع جزئي</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body text-center">
                        <div class="row justify-content-center gap-3 align-items-center">
                            <div class="col-md-3 d-flex justify-content-center flex-column border rounded p-2">
                                <h6>الإجمالي المطلوب</h6>
                                <span class="fs-4 fw-bold text-primary">{{ $total_price }} ₪</span>
                            </div>
                            
                            @if($payment_type == 4)
                                <div class="col-md-4 border rounded p-2">
                                    <label for="paid_money" class="form-label fw-bold">المبلغ المدفوع</label>
                                    <input class="form-control text-center fs-5" type="number" id="paid_money" min="0"
                                           wire:model.live.debounce="paid_money">
                                    <x-input-error key="paid_money"/>
                                </div>
                            @endif

                            <div class="col-md-3 d-flex justify-content-center flex-column border rounded p-2">
                                <h6>المتبقي (الدين)</h6>
                                <span class="fs-4 fw-bold text-danger">{{ $remaining_money }} ₪</span>
                            </div>
                        </div>

                        <hr>
                        <div class="text-start mt-2">
                            <small class="text-muted">
                                * الدفع النقدي والفيزا يسجلان المقبوضات كاملة وتصبح حالة الدفع: مدفوع بالكامل.
                                <br>
                                * الدفع الآجل لا يسجل مقبوضات حالية وتصبح الحالة: غير مدفوع.
                                <br>
                                * الدفع الجزئي يسجل الجزء المدفوع وتصبح الحالة: مدفوع جزئياً.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <!-- / Payment Type -->
        </div>
        <!-- / Payment & Submit -->
    </div>
</div>

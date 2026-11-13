<div>
    <div class="ec-page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="ec-eyebrow"><i class="bx bx-group"></i> العملاء</div>
                <h1 class="ec-section-title">قائمة العملاء</h1>
            </div>
            @if(auth()->user()->hasPermission('customers.manage'))
                <a href="{{ route('customers.create') }}" class="btn btn-primary">
                    <i class="bx bx-user-plus me-1"></i> إضافة عميل جديد
                </a>
            @endif
        </div>
    </div>

    <div class="card">
        {{-- title table --}}
        <h5 class="card-header">العملاء</h5>

        <div class="card-body">
            <div class="col-md-5 mb-3">
                <label for="search" class="form-label">
                    بحث
                    <i wire:loading wire:target="search" class='ms-2 bx bx-loader-circle bx-spin'></i>
                </label>
                <input wire:model.live.debounce.500ms="search" id="search" class="form-control mt-1"
                    placeholder="أدخل كود العميل او رقم هاتف العميل او اسم العميل">
            </div>

            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الأسم</th>
                        <th>العنوان</th>
                        <th>الهاتف</th>
                        <th>تاريخ التسجيل</th>
                        <th>التحكم</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($customers as $customer)
                        <tr wire:key="{{ $customer->id }}">
                            <td>{{ $customer->id }}</td>
                            <td>
                                <strong>
                                    <a href="{{ route('customers.show', $customer) }}">{{ $customer->name }}</a>
                                </strong>
                            </td>
                            <td>{{ $customer->address }}</td>
                            <td>{{ $customer->phone }}</td>
                            <td>{{ $customer->created_at->diffForHumans() }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('customers.statement', $customer) }}" title="كشف حساب مفصل">
                                        <i class="bx bx-file"></i> كشف الحساب
                                    </a>
                                    @if(auth()->user()->hasPermission('customers.manage'))
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('customers.edit', $customer) }}" title="تعديل العميل">
                                            <i class="bx bx-edit-alt"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="5"><strong>لا يوجد بيانات</strong></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <!-- Pagination links -->
            <div class="mt-3 px-3 d-flex justify-content-end">
                {{ $customers->links(data: ['scrollTo' => false]) }}
            </div>
        </div>
    </div>
</div>

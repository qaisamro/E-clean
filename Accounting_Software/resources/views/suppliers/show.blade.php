<div>
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">الموردون /</span> كشف حساب: {{ $supplier->name }}</h4>

    @session('success-msg')
    <div x-data x-init="setTimeout(() => $el.remove(), 3000)" class="mb-3">
        <div class="alert alert-info alert-dismissible fade show text-dark" role="alert"><strong>{{ $value }}</strong></div>
    </div>
    @endsession
    @session('error-msg')
    <div x-data x-init="setTimeout(() => $el.remove(), 5000)" class="mb-3">
        <div class="alert alert-danger alert-dismissible fade show text-dark" role="alert"><strong>{{ $value }}</strong></div>
    </div>
    @endsession

    {{-- Supplier header card --}}
    <div class="card mb-4">
        <div class="card-body row gy-3 align-items-center">
            <div class="col-md-5">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded bg-label-primary p-3 fw-bold fs-4" style="width: 64px; height: 64px; display:flex; align-items:center; justify-content:center;">
                        {{ mb_substr($supplier->name, 0, 1) }}
                    </div>
                    <div>
                        <h5 class="mb-1">{{ $supplier->name }}</h5>
                        <div class="text-muted">
                            @if($supplier->phone)<div><i class="bx bx-phone"></i> {{ $supplier->phone }}</div>@endif
                            @if($supplier->address)<div><i class="bx bx-map"></i> {{ $supplier->address }}</div>@endif
                        </div>
                    </div>
                </div>
                @if($supplier->notes)
                <div class="small text-muted mt-2"><strong>ملاحظات:</strong> {{ $supplier->notes }}</div>
                @endif
                <span @class(['badge mt-2', 'bg-label-success' => $supplier->is_active, 'bg-label-secondary' => ! $supplier->is_active])>
                    {{ $supplier->is_active ? 'فعال' : 'معطل' }}
                </span>
            </div>
            <div class="col-md-7">
                <div class="row gy-2 text-center">
                    <div class="col-md-3 col-6">
                        <div class="border rounded p-2">
                            <div class="small text-muted">الرصيد الافتتاحي</div>
                            <div class="fw-bold">{{ number_format($statement['opening'], 2) }} ₪</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="border rounded p-2">
                            <div class="small text-muted">إجمالي الفواتير ({{ $statement['bills_count'] }})</div>
                            <div class="fw-bold">{{ number_format($statement['bills_total'], 2) }} ₪</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="border rounded p-2">
                            <div class="small text-muted">المدفوع ({{ $statement['payments_count'] }})</div>
                            <div class="fw-bold">{{ number_format($statement['payments_total'], 2) }} ₪</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div @class(['border rounded p-2', 'bg-label-danger' => $statement['balance'] > 0.0001, 'bg-label-success' => $statement['balance'] <= 0.0001])>
                            <div class="small text-muted">المتبقي عليه</div>
                            <div class="fw-bold fs-5">{{ number_format(max(0, $statement['balance']), 2) }} ₪</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex flex-wrap gap-2">
            <a href="{{ route('supplier-bills.create', ['supplier' => $supplier->id]) }}" class="btn btn-primary"><i class="bx bx-plus-circle"></i> فاتورة شراء جديدة</a>
            <a href="{{ route('supplier-payments.create', ['supplier' => $supplier->id]) }}" class="btn btn-outline-primary"><i class="bx bx-money-withdraw"></i> تسجيل دفعة</a>
            <a href="{{ route('suppliers.statement.print', $supplier) }}" target="_blank" class="btn btn-outline-secondary"><i class="bx bx-printer"></i> طباعة / حفظ PDF</a>
            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-outline-secondary"><i class="bx bx-edit-alt"></i> تعديل بيانات المورد</a>
        </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link @if($tab === 'statement') active @endif" href="{{ route('suppliers.show', ['supplier' => $supplier->id, 'tab' => 'statement']) }}">كشف الحساب التفصيلي</a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if($tab === 'bills') active @endif" href="{{ route('suppliers.show', ['supplier' => $supplier->id, 'tab' => 'bills']) }}">الفواتير</a>
        </li>
        <li class="nav-item">
            <a class="nav-link @if($tab === 'payments') active @endif" href="{{ route('suppliers.show', ['supplier' => $supplier->id, 'tab' => 'payments']) }}">الدفعات</a>
        </li>
    </ul>

    @if($tab === 'statement')
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>البيان</th>
                        <th class="text-end">عليه (فواتير)</th>
                        <th class="text-end">له (دفعات)</th>
                        <th class="text-end">الرصيد</th>
                        <th>ملاحظات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($statement['rows'] as $row)
                        <tr>
                            <td>{{ $row['date'] ? \Carbon\Carbon::parse($row['date'])->format('Y-m-d') : '—' }}</td>
                            <td>{{ $row['title'] }}</td>
                            <td class="text-end">{{ $row['debit'] > 0 ? number_format($row['debit'], 2) . ' ₪' : '—' }}</td>
                            <td class="text-end">{{ $row['credit'] > 0 ? number_format($row['credit'], 2) . ' ₪' : '—' }}</td>
                            <td class="text-end fw-bold">{{ number_format($row['balance_after'], 2) }} ₪</td>
                            <td class="text-muted small">{{ $row['notes'] ?? '' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">لا توجد حركات بعد لهذا المورد — أضف فاتورة شراء أو دفعة</td>
                        </tr>
                    @endforelse
                    </tbody>
                    @if(count($statement['rows']))
                    <tfoot>
                        <tr class="fw-bold">
                            <td colspan="2">الإجمالي / الرصيد النهائي</td>
                            <td class="text-end">{{ number_format($statement['bills_total'], 2) }} ₪</td>
                            <td class="text-end">{{ number_format($statement['payments_total'], 2) }} ₪</td>
                            <td class="text-end">{{ number_format($statement['balance'], 2) }} ₪</td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    @elseif($tab === 'bills')
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                    <tr>
                        <th>الفاتورة</th>
                        <th>التاريخ</th>
                        <th>رقم المورد</th>
                        <th class="text-end">الإجمالي</th>
                        <th class="text-end">المدفوع</th>
                        <th class="text-end">المتبقي</th>
                        <th>الحالة</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($bills as $bill)
                        <tr>
                            <td><strong>#{{ $bill->id }}</strong> @if($bill->lines_count)<span class="text-muted small">({{ $bill->lines_count }} سطر)</span>@endif</td>
                            <td>{{ $bill->bill_date->format('Y-m-d') }}</td>
                            <td>{{ $bill->bill_number ?? '—' }}</td>
                            <td class="text-end">{{ number_format((float) $bill->total, 2) }} ₪</td>
                            <td class="text-end">{{ number_format((float) $bill->paid, 2) }} ₪</td>
                            <td class="text-end fw-bold">{{ number_format((float) $bill->remaining, 2) }} ₪</td>
                            <td>
                                <span @class(['badge', 'bg-label-danger' => $bill->status === 'unpaid', 'bg-label-warning' => $bill->status === 'partial', 'bg-label-success' => $bill->status === 'paid'])>
                                    {{ $bill->status === 'paid' ? 'مسددة' : ($bill->status === 'partial' ? 'جزئية' : 'غير مسددة') }}
                                </span>
                            </td>
                            <td><a href="{{ route('supplier-bills.show', $bill) }}" class="btn btn-sm btn-outline-primary">عرض</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">لا توجد فواتير لهذا المورد بعد</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 px-2">{{ $bills->links() }}</div>
        </div>
    @elseif($tab === 'payments')
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                    <tr>
                        <th>الدفعة</th>
                        <th>التاريخ</th>
                        <th class="text-end">المبلغ</th>
                        <th>الطريقة</th>
                        <th>لفاتورة</th>
                        <th>ملاحظات</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td><strong>#{{ $payment->id }}</strong></td>
                            <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                            <td class="text-end fw-bold">{{ number_format((float) $payment->amount, 2) }} ₪</td>
                            <td>{{ $payment->methodLabel() }}</td>
                            <td>@if($payment->bill_id)<a href="{{ route('supplier-bills.show', $payment->bill_id) }}">#{{ $payment->bill_id }}</a>@else — (على الحساب)@endif</td>
                            <td class="text-muted small">{{ $payment->notes }}</td>
                            <td></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">لا توجد دفعات لهذا المورد بعد</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 px-2">{{ $payments->links() }}</div>
        </div>
    @endif
</div>

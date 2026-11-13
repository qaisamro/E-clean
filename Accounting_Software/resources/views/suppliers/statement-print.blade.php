<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>كشف حساب المورد — {{ $supplier->name }}</title>
    <style>
        @page { size: A4 portrait; margin: 12mm; }
        * { box-sizing: border-box; }
        body {
            font-family: 'Cairo', 'Tajawal', Tahoma, Arial, sans-serif;
            color: #1c1c1c;
            margin: 0;
            padding: 20px;
        }
        .toolbar {
            display: flex; gap: 8px; justify-content: space-between; align-items: center;
            border-bottom: 2px solid #1c1c1c; padding-bottom: 10px; margin-bottom: 16px;
        }
        .toolbar h1 { font-size: 20px; margin: 0; }
        .toolbar button {
            background: #1c1c1c; color: #fff; border: none; border-radius: 6px;
            padding: 8px 18px; cursor: pointer; font-size: 14px;
        }
        .meta { display: flex; gap: 24px; flex-wrap: wrap; margin-bottom: 14px; }
        .meta div { font-size: 13px; }
        .meta strong { display: block; color: #555; font-size: 11px; }
        .summary {
            display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 14px;
        }
        .summary .card {
            border: 1px solid #d9d9d9; border-radius: 8px; padding: 8px 14px; min-width: 150px;
        }
        .summary .card div { font-size: 11px; color: #555; }
        .summary .card strong { font-size: 16px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #d9d9d9; padding: 6px 8px; }
        th { background: #f5f5f5; text-align: right; }
        td.num { text-align: left; font-variant-numeric: tabular-nums; }
        tr.opening td { background: #fffbe8; }
        tr.total td { font-weight: 700; background: #f5f5f5; }
        .foot { margin-top: 24px; display: flex; justify-content: space-between; font-size: 12px; color: #555; }
        @media print {
            body { padding: 0; }
            .toolbar { display: none; }
            .summary .card, tr.opening td { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <h1>كشف حساب المورد</h1>
        <button onclick="window.print()">طباعة / حفظ PDF</button>
    </div>

    <div class="meta">
        <div><strong>اسم المورد</strong>{{ $supplier->name }}</div>
        @if($supplier->phone)<div><strong>الهاتف</strong>{{ $supplier->phone }}</div>@endif
        @if($supplier->address)<div><strong>العنوان</strong>{{ $supplier->address }}</div>@endif
        <div><strong>تاريخ الطباعة</strong>{{ now()->format('Y-m-d H:i') }}</div>
        <div><strong>الرصيد الافتتاحي</strong>{{ number_format($statement['opening'], 2) }} ₪</div>
    </div>

    <div class="summary">
        <div class="card"><div>إجمالي الفواتير ({{ $statement['bills_count'] }})</div><strong>{{ number_format($statement['bills_total'], 2) }} ₪</strong></div>
        <div class="card"><div>المدفوع ({{ $statement['payments_count'] }})</div><strong>{{ number_format($statement['payments_total'], 2) }} ₪</strong></div>
        <div class="card"><div>المتبقي عليه</div><strong>{{ number_format(max(0, $statement['balance']), 2) }} ₪</strong></div>
    </div>

    <table>
        <thead>
        <tr>
            <th>التاريخ</th>
            <th>البيان</th>
            <th>عليه (فواتير)</th>
            <th>له (دفعات)</th>
            <th>الرصيد</th>
            <th>ملاحظات</th>
        </tr>
        </thead>
        <tbody>
        @forelse($statement['rows'] as $row)
            <tr class="{{ $row['type'] === 'opening' ? 'opening' : '' }}">
                <td>{{ $row['date'] ? \Carbon\Carbon::parse($row['date'])->format('Y-m-d') : '—' }}</td>
                <td>{{ $row['title'] }}</td>
                <td class="num">{{ $row['debit'] > 0 ? number_format($row['debit'], 2) . ' ₪' : '—' }}</td>
                <td class="num">{{ $row['credit'] > 0 ? number_format($row['credit'], 2) . ' ₪' : '—' }}</td>
                <td class="num">{{ number_format($row['balance_after'], 2) }} ₪</td>
                <td>{{ $row['notes'] ?? '' }}</td>
            </tr>
        @empty
            <tr><td colspan="6" style="text-align:center; color:#777;">لا توجد حركات بعد</td></tr>
        @endforelse
        <tr class="total">
            <td colspan="2">الإجمالي / الرصيد النهائي</td>
            <td class="num">{{ number_format($statement['bills_total'], 2) }} ₪</td>
            <td class="num">{{ number_format($statement['payments_total'], 2) }} ₪</td>
            <td class="num">{{ number_format($statement['balance'], 2) }} ₪</td>
            <td></td>
        </tr>
        </tbody>
    </table>

    <div class="foot">
        <span>{{ config('app.name') }} — كشف حساب المورد</span>
        <span>{{ $supplier->name }}</span>
    </div>
</body>
</html>

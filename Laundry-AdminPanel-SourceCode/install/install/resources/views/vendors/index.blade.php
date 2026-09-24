@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="card-title mb-1">متاجر التطبيق</h2>
                        <p class="text-muted mb-0">يتحكم السوبر أدمن بالمتاجر الظاهرة للعملاء.</p>
                    </div>
                    <span class="badge badge-primary">{{ $vendors->count() }} متجر</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>المتجر</th>
                                    <th>المالك</th>
                                    <th>العنوان</th>
                                    <th>الحالة</th>
                                    <th>التحكم</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($vendors as $vendor)
                                    <tr>
                                        <td>
                                            <strong>{{ $vendor->name }}</strong>
                                            @if ($vendor->phone)
                                                <div class="text-muted small">{{ $vendor->phone }}</div>
                                            @endif
                                        </td>
                                        <td>{{ $vendor->owner?->name ?? 'غير محدد' }}</td>
                                        <td>{{ $vendor->address ?? '—' }}</td>
                                        <td>
                                            <span class="badge {{ $vendor->is_active ? 'badge-success' : 'badge-secondary' }}">
                                                {{ $vendor->is_active ? 'ظاهر في التطبيق' : 'مخفي' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('vendor.status.toggle', $vendor) }}" class="btn btn-sm {{ $vendor->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                                {{ $vendor->is_active ? 'إخفاء' : 'إظهار' }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">لا توجد متاجر مسجلة بعد.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
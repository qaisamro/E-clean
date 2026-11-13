@extends('layouts.app')

@section('content')
<style>
    /* Badges with solid identity colors — independent of theme's pale badge colors */
    .ec-badge { display:inline-block; padding: .32em .75em; border-radius: 9px; font-weight: 800; font-size: .76rem; line-height: 1; }
    .ec-badge-yes { background: #16A34A; color: #fff !important; }
    .ec-badge-no { background: #E2E8F0; border: 1px solid #CBD5E1; color: #0B1E2E !important; }
    .ec-badge-complete { background: #16A34A; color: #fff !important; }
    .ec-badge-partial { background: #F59E0B; color: #fff !important; }
    .ec-badge-none { background: #E2E8F0; border: 1px solid #CBD5E1; color: #0B1E2E !important; }
</style>
<div class="container-fluid mt-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-4" style="gap:12px;">
        <div>
            <h2 class="mb-1">صلاحيات «{{ $sub->user?->name }}»</h2>
            <p class="text-muted mb-0">متجر «{{ $sub->vendor?->name }}» — المستخدم يرى <strong>هذا المتجر فقط</strong>. ما تُحدده هنا يُطبَّق فوراً عند كل قسم.</p>
        </div>
        <div class="d-flex" style="gap:8px;">
            <a href="{{ route('vendor.subs.edit', $sub->id) }}" class="btn btn-light"><i class="fas fa-pen"></i> تعديل البيانات</a>
            <a href="{{ route('vendor.subs.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-right"></i> عودة للقائمة</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Baseline: always-on permissions (informational, no toggle) --}}
    <div class="card shadow-sm mb-4" style="border-inline-start:4px solid #028090;">
        <div class="card-body d-flex align-items-center flex-wrap" style="gap:14px;">
            <span style="flex:0 0 auto; width:46px; height:46px; border-radius:12px; background:linear-gradient(135deg,#028090,#00A896); color:#fff; display:inline-flex; align-items:center; justify-content:center; font-size:19px;">
                <i class="fas fa-shield-alt"></i>
            </span>
            <div>
                <h6 class="mb-1 font-weight-bold">صلاحيات أساسية تلقائية (لا تحتاج إعداد)</h6>
                <small class="text-muted">لوحة تحكم المتجر، الملف الشخصي وتغيير كلمة المرور، إشعارات الطلبات الجديدة، فتح الموقع على الخريطة.</small>
            </div>
        </div>
    </div>

    <div class="row px-1 pb-4">
        @foreach ($groups as $group)
            @php
                $grantedInGroup = collect($group['items'])->filter(fn ($item) => in_array($item['permission'], $permissions, true))->count();
                $totalInGroup = count($group['items']);
            @endphp
            <div class="col-12 col-sm-6 col-lg-6 col-xl-4 mb-4">
                <div class="card p-3 h-100 d-flex flex-column" style="min-height: 15em;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h5 class="m-0 font-weight-bold">
                            <i class="fas {{ $group['icon'] }}" style="color:{{ $group['color'] }};"></i>
                            {{ $group['title'] }}
                        </h5>
                        <span class="ec-badge {{ $grantedInGroup === $totalInGroup ? 'ec-badge-complete' : ($grantedInGroup === 0 ? 'ec-badge-none' : 'ec-badge-partial') }}">
                            {{ $grantedInGroup }} / {{ $totalInGroup }}
                        </span>
                    </div>
                    <div class="row mb-3" style="flex:1 1 auto;">
                        @foreach ($group['items'] as $item)
                            <div class="col-6 mb-2">
                                <small class="text-muted d-block" style="line-height:1.35; min-height:2.1em;">{{ $item['title'] }}</small>
                                @if (in_array($item['permission'], $permissions, true))
                                    <span class="ec-badge ec-badge-yes">Yes</span>
                                @else
                                    <span class="ec-badge ec-badge-no">No</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <button class="btn btn-primary btn-block" data-toggle="modal" data-target="#permModal-{{ $group['key'] }}">
                        <i class="fas fa-key"></i> تحديد الصلاحيات
                    </button>
                </div>
            </div>

            <!-- Modal: {{ $group['title'] }} -->
            <div class="modal fade" id="permModal-{{ $group['key'] }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background:{{ $group['color'] }}; color:#fff;">
                            <h5 class="modal-title font-weight-bold">
                                <i class="fas {{ $group['icon'] }}"></i> صلاحيات — {{ $group['title'] }}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#fff; opacity:.9;">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('vendor.subs.set-permission', $sub->id) }}" method="POST">
                                @csrf
                                @foreach ($group['items'] as $item)
                                    <x-permission-input :title="$item['title']" :user="$sub->user"
                                        :name="str_replace('.', '_', $item['permission'])"
                                        :permission="$item['permission']" />
                                @endforeach
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <button type="button" class="btn btn-sm btn-link" data-select-all="#permModal-{{ $group['key'] }}">تحديد الكل</button>
                                    <div>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> حفظ الصلاحيات</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    // «تحديد الكل»: checks every "Yes" radio inside this modal.
    document.querySelectorAll('[data-select-all]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var modal = document.querySelector(btn.getAttribute('data-select-all'));
            if (!modal) return;
            modal.querySelectorAll('input[type="radio"][value]:not([value=""])').forEach(function (radio) {
                radio.checked = true;
            });
        });
    });
</script>
@endsection

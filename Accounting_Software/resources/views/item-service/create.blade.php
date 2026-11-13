<x-app-layout :title="config('app.name') . ' | ' . 'إضافة تسعيرة'">

    <div class="ec-page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="ec-eyebrow"><i class="bx bx-dollar-circle"></i> أسعار الخدمات</div>
                <h1 class="ec-section-title">إضافة تسعيرة جديدة</h1>
            </div>
            <a href="{{ route('itemServices.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> قائمة الأسعار
            </a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12">
            <div class="card ec-card">
                <div class="card-header bg-transparent border-0 pb-1 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bx bx-receipt text-teal me-2"></i>بيانات التسعيرة</h5>
                    <span class="ec-chip ec-chip-teal">مطلوب: الصنف · الخدمة · السعر</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('itemServices.store') }}" method="post">
                        @csrf
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="item_id" class="form-label fw-semibold">الأصناف</label>
                                <select id="item_id" class="form-select select2 @error('item_id') is-invalid @enderror" name="item_id" required>
                                    <option selected disabled value>أختر صنف</option>
                                    @foreach($items as $item)
                                        <option @selected(old('item_id') == $item->id) value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error key="item_id"/>
                            </div>
                            <div class="col-md-4">
                                <label for="service_id" class="form-label fw-semibold">الخدمات</label>
                                <select id="service_id" class="form-select select2 @error('service_id') is-invalid @enderror" name="service_id" required>
                                    <option selected disabled value>أختر خدمة</option>
                                    @foreach($services as $service)
                                        <option @selected(old('service_id') == $service->id) value="{{ $service->id }}">{{ $service->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error key="service_id"/>
                            </div>
                            <div class="col-md-4">
                                <label for="price" class="form-label fw-semibold">السعر</label>
                                <div class="input-group">
                                    <span class="input-group-text">₪</span>
                                    <input type="number" step="0.01" min="0" id="price" class="form-control @error('price') is-invalid @enderror" name="price" required value="{{ old('price') }}" placeholder="0.00">
                                </div>
                                <x-input-error key="price"/>
                            </div>
                            <div class="col-md-8">
                                <label for="note" class="form-label fw-semibold">ملحوظه لحساب السعر</label>
                                <input type="text" id="note" class="form-control" name="note" placeholder="ملحوظه تظهر في حاله عدم وجود سعر للخدمه" value="{{ old('note') }}">
                                <x-input-error key="note"/>
                            </div>
                        </div>

                        <div class="d-flex gap-2 align-items-center border-top pt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-plus me-1"></i>إضافة تسعيرة
                            </button>
                            <span class="text-muted small">ستجري إضافته مباشرة إلى <strong>قائمة الأسعار</strong></span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>

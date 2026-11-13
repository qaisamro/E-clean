<x-app-layout :title="config('app.name') . ' | ' . 'إضافة مرتب'">

    <div class="ec-page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="ec-eyebrow"><i class="bx bxs-id-card"></i> المرتبات</div>
                <h1 class="ec-section-title">إضافة مرتب جديد</h1>
            </div>
            <a href="{{ route('salaries.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> قائمة المرتبات
            </a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12">
            <div class="card ec-card">
                <div class="card-header bg-transparent border-0 pb-1 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bx bx-receipt text-teal me-2"></i>بيانات المرتب</h5>
                    <span class="ec-chip ec-chip-teal">مطلوب: الموظف · القيمة</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('salaries.store') }}" method="post">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="user_id" class="form-label fw-semibold">الموظف *</label>
                                <select id="user_id" class="form-select select2" name="user_id" required>
                                    <option selected disabled value>أختر موظف</option>
                                    @foreach ($users as $user)
                                        <option @selected(old('user_id') == $user->id) value="{{ $user->id }}">
                                            {{ $user->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error key="user_id" />
                            </div>
                            <div class="col-md-6">
                                <label for="value" class="form-label fw-semibold">المرتب *</label>
                                <div class="input-group">
                                    <span class="input-group-text">₪</span>
                                    <input type="number" step="0.01" min="0.01" id="value" name="value" required value="{{ old('value') }}" placeholder="0.00"/>
                                </div>
                                <x-input-error key="value" />
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">الوصف</label>
                                <textarea name="description" class="form-control" id="description" rows="4" style="resize: none">{{ old('description') }}</textarea>
                                <x-input-error key="description" />
                            </div>
                        </div>
                        <div class="d-flex gap-2 align-items-center border-top pt-3 mt-3 flex-wrap">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>حفظ المرتب
                            </button>
                            <a href="{{ route('salaries.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>

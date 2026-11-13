<x-app-layout :title="config('app.name') . ' | ' . 'إضافة مصروف'">

    <div class="ec-page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="ec-eyebrow"><i class="bx bxs-wallet"></i> المصروفات</div>
                <h1 class="ec-section-title">إضافة مصروف جديد</h1>
            </div>
            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> قائمة المصروفات
            </a>
        </div>
    </div>

    <div x-data="{ type : '{{ old('type') }}' }" class="row g-3">
        <div class="col-12">
            <div class="card ec-card">
                <div class="card-header bg-transparent border-0 pb-1 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bx bx-receipt text-teal me-2"></i>بيانات المصروف</h5>
                    <span class="ec-chip ec-chip-teal">مطلوب: الاسم · النوع · القيمة</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('expenses.store') }}" method="post">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">اسم المصروف</label>
                                <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name" required value="{{ old('name') }}"/>
                                <x-input-error key="name"/>
                            </div>
                            <div class="col-md-6">
                                <label for="type" class="form-label fw-semibold">نوع المصروف</label>
                                <select id="type" class="form-select @error('type') is-invalid @enderror" name="type" x-model="type" required>
                                    <option selected disabled value>اختر نوع</option>
                                    @foreach(\App\Enums\ExpensesType::cases() as $expensesType)
                                        <option value="{{ $expensesType->value }}">{{ $expensesType->label() }}</option>
                                    @endforeach
                                </select>
                                <x-input-error key="type"/>
                            </div>
                            <div x-show="type === '{{ \App\Enums\ExpensesType::SALARY }}'" x-transition x-cloak class="col-md-6">
                                <label for="user_id" class="form-label fw-semibold">الموظف</label>
                                <select id="user_id" class="form-select select2 @error('user_id') is-invalid @enderror" x-ignore name="user_id">
                                    <option selected disabled value>اختر موظف</option>
                                    @foreach($users as $user)
                                        <option @selected(old('user_id') == $user->id) value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error key="user_id"/>
                            </div>
                            <div class="col-md-6">
                                <label for="value" class="form-label fw-semibold">القيمة</label>
                                <div class="input-group @error('value') is-invalid @enderror">
                                    <span class="input-group-text">₪</span>
                                    <input class="form-control" type="number" step="0.01" min="0.01" id="value" name="value" required value="{{ old('value') }}" placeholder="0.00"/>
                                </div>
                                <x-input-error key="value"/>
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">الوصف</label>
                                <textarea name="description" class="form-control" id="description" rows="4" style="resize: none">{{ old('description') }}</textarea>
                                <x-input-error key="description"/>
                            </div>
                        </div>
                        <div class="d-flex gap-2 align-items-center border-top pt-3 mt-3 flex-wrap">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>حفظ المصروف
                            </button>
                            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('css')
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @endpush

    @push('js')
    @vite(['resources/js/app.js'])
    @endpush
</x-app-layout>

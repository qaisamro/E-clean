<x-app-layout :title="config('app.name') . ' | ' . 'إضافة مستخدم'">

    <div class="ec-page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="ec-eyebrow"><i class="bx bxs-user-plus"></i> المستخدمون</div>
                <h1 class="ec-section-title">إضافة مستخدم جديد</h1>
            </div>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> قائمة المستخدمين
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- Profile Details -->
            <div class="card ec-card mb-4">
                <h5 class="card-header">بيانات المستخدم</h5>
                <div class="card-body">
                    <form action="{{ route('users.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">الاسم *</label>
                                <input class="form-control" type="text" id="name" name="name"
                                       required
                                       value="{{ old('name') }}"/>
                                <x-input-error key="name"/>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-start" for="phone">الهاتف *</label>
                                <input type="tel" id="phone" name="phone" required pattern="01[0125][0-9]{8}" value="{{ old('phone') }}"
                                    class="form-control text-start" />
                                <x-input-error key="phone" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">كلمة المرور</label>
                                <input class="form-control" type="password" id="password" name="password" autocomplete="password"/>
                                <div class="form-text">اتركه فارغًا لاستخدام كلمة المرور الافتراضية للموظف.</div>
                                <x-input-error key="password" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                                <input class="form-control" type="password" id="password_confirmation"
                                       autocomplete="password"
                                    name="password_confirmation" />
                                <x-input-error key="password_confirmation" />
                            </div>

                            <div class="col-md-6 mb-3 ecommerce-select2-dropdown">
                                <label for="role" class="form-label">الدور *</label>
                                <div class="select2-dark">
                                    <select id="role" class="select2 form-select" name="role" required>
                                        <option @selected( old('role') == \App\Enums\Roles::ADMIN->value) value="{{ \App\Enums\Roles::ADMIN->value }}">أدمن</option>
                                    </select>
                                </div>
                                <x-input-error key="role" />
                            </div>
                        </div>

                        {{-- Permission matrix --}}
                        <div class="card ec-card border mt-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">الصلاحيات بالتفصيل</h5>
                                <button type="button" id="selectAllPermissions" class="btn btn-outline-secondary btn-sm">تحديد / إلغاء الكل</button>
                            </div>
                            <div class="card-body">
                                @foreach($groups as $group => $members)
                                    <fieldset class="mb-3 border rounded p-3">
                                        <legend class="float-none w-auto px-2 fs-6 fw-bold">{{ $group }}</legend>
                                        <div class="row gy-2">
                                            @foreach($members as $member)
                                                <div class="col-md-6">
                                                    <label class="form-check">
                                                        <input type="checkbox" class="form-check-input permission-checkbox"
                                                               name="permissions[]"
                                                               value="{{ $member['key'] }}"
                                                               @checked(in_array($member['key'], old('permissions', [])))>
                                                        <span class="form-check-label">{{ $member['label'] }}</span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </fieldset>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">إضافة مستخدم
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /Profile Details -->
        </div>
    </div>

    <script>
        document.getElementById('selectAllPermissions')?.addEventListener('click', function () {
            const boxes = document.querySelectorAll('.permission-checkbox');
            const anyUnchecked = Array.from(boxes).some(b => !b.checked);
            boxes.forEach(b => b.checked = anyUnchecked);
        });
    </script>
</x-app-layout>

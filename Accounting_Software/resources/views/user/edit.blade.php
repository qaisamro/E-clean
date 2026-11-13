<x-app-layout :title="config('app.name') . ' | ' . 'تعديل ' . $user->name">

    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"><a href="{{ route('users.index') }}">المستخدمون</a>
            /</span>
        تعديل: {{ $user->name }}</h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <h5 class="card-header">بيانات المستخدم</h5>
                <div class="card-body">
                    <form action="{{ route('users.update', $user) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">الاسم *</label>
                                <input class="form-control" type="text" id="name" name="name"
                                       value="{{ old('name', $user->name) }}" required />
                                <x-input-error key="name" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-start" for="phone">الهاتف *</label>
                                <input type="tel" id="phone" name="phone" required pattern="01[0125][0-9]{8}"
                                       value="{{ old('phone', $user->phone) }}" class="form-control text-start" />
                                <x-input-error key="phone" />
                            </div>

                            <div class="col-md-6 mb-3 ecommerce-select2-dropdown">
                                <label for="role" class="form-label">الدور *</label>
                                <div class="select2-dark">
                                    <select id="role" class="select2 form-select" name="role" required>
                                        <option @selected(old('role', $user->role->value) == \App\Enums\Roles::ADMIN->value)
                                            value="{{ \App\Enums\Roles::ADMIN->value }}">أدمن</option>
                                    </select>
                                </div>
                                <x-input-error key="role" />
                            </div>
                        </div>

                        {{-- Permission matrix --}}
                        <div class="card border mt-3">
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
                                                               @checked(in_array($member['key'], old('permissions', $user->permissions ?? [])))>
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
                            <button type="submit" class="btn btn-primary me-2">حفظ التعديلات</button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
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

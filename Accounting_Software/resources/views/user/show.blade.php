<x-app-layout :title="config('app.name') . ' | ' . 'بيانات ' . $user->name">

    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"><a href="{{ route('users.index') }}">المستخدمون</a> /</span>
        بيانات {{ $user->name }}</h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h5 class="card-header">بيانات: {{ $user->name }}</h5>

                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary me-3"><i class="bx bx-edit-alt"></i> تعديل</a>
                </div>

                <hr>
                <div class="card-body">
                    <div class="row fs-5">
                        <div class="mb-3 col-md-6">
                            الاسم: {{ $user->name }}
                        </div>
                        <div class="mb-3 col-md-6">
                            الهاتف: {{ $user->phone }}
                        </div>
                        <div class="col-md-6">
                            الدور: {{ $user->role->label() }}
                        </div>
                        <div class="col-md-6">
                            تاريخ التعيين: {{ $user->created_at->format('Y-m-d') }}
                        </div>
                        <div class="col-md-6">
                            حالة الحساب:
                            <span @class([
                                'badge me-1',
                                'bg-label-primary' => !$user->isSuspended(),
                                'bg-label-danger' => $user->isSuspended(),
                            ])>{{ $user->readable_status }}</span>
                        </div>
                        <div class="col-md-6">
                            عدد الصلاحيات:
                            <span class="badge bg-label-primary">{{ count($user->permissions ?? []) }} من {{ count(\App\Support\PermissionRegistry::keys()) }}</span>
                        </div>
                    </div>
                </div>

                <div class="border-top pt-3">
                    <h6 class="fw-bold mb-3">الصلاحيات الممنوحة</h6>
                    <div class="row gy-3">
                        @foreach($groups as $group => $members)
                            <div class="col-md-6">
                                <fieldset class="border rounded p-2">
                                    <legend class="float-none w-auto px-2 fs-6 fw-bold">{{ $group }}</legend>
                                    <ul class="list-unstyled mb-0">
                                        @foreach($members as $member)
                                            @php $granted = in_array($member['key'], $user->permissions ?? [], true); @endphp
                                            <li class="d-flex align-items-center gap-2 py-1">
                                                <i class="bx bx-{{ $granted ? 'check' : 'minus' }} {{ $granted ? 'text-success' : 'text-muted' }}"></i>
                                                <span @class(['text-muted' => ! $granted])>{{ $member['label'] }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </fieldset>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

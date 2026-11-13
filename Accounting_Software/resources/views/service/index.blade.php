<x-app-layout :title="config('app.name') . ' | ' . 'الخدمات'">

    <div class="ec-page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="ec-eyebrow"><i class="bx bx-briefcase-alt-2"></i> الخدمات</div>
                <h1 class="ec-section-title">قائمة الخدمات</h1>
            </div>
            <a href="{{ route('services.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> إضافة خدمه
            </a>
        </div>
    </div>

    <!-- Basic Bootstrap Table -->
    <div class="card">
        <div class="d-flex justify-content-between align-items-center mb-3">

            {{-- title table --}}
            <h5 class="card-header">الخدمات</h5>

            {{-- add new item --}}
            <a href="{{ route('services.create') }}" class="btn btn-primary me-3">إضافة خدمه</a>
        </div>
        <!-- A div for making the table horizontally scrollable -->
        <div class="table-responsive text-nowrap">

            <!-- The table element with Bootstrap classes for styling -->
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>الأسم</th>
                        <th>التحكم</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($services as $service)
                        <tr>
                            <td>
                                <a href="{{ route('services.edit', $service) }}">
                                <strong>
                                    {{ $service->name }}
                                </strong>
                                </a>
                            </td>
                            <td>
                                <!-- Dropdown toggle button -->
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('services.edit', $service) }}">
                                            <i class="bx bx-edit-alt me-1"></i>تعديل
                                        </a>
                                        <form method="POST" action="{{ route('services.destroy', $service) }}">
                                            @csrf
                                            @method('DELETE')
                                            <a class="dropdown-item" href="{{ route('services.destroy', $service) }}"
                                                style="text-align:start"
                                                onclick="event.preventDefault(); if (confirm('هل تريد حذف الصنف من النظام ؟')) { this.closest('form').submit(); }">
                                                <i class="bx bx-trash me-1"></i>حذف
                                            </a>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="6"><strong>لا يوجد بيانات</strong></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <!-- Pagination links -->
            <div class="mt-4 px-4">
                {{ $services->links() }}
            </div>
        </div>

    </div>

</x-app-layout>

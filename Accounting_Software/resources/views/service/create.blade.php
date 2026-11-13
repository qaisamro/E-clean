<x-app-layout :title="config('app.name') . ' | ' . 'إضافة خدمه'">

    <div class="ec-page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="ec-eyebrow"><i class="bx bx-briefcase-alt-2"></i> الخدمات</div>
                <h1 class="ec-section-title">إضافة خدمه</h1>
            </div>
            <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> قائمة الخدمات
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">

            <!-- Profile Details -->
            <div class="card mb-4">
                <h5 class="card-header">بيانات الخدمه</h5>
                <div class="card-body">
                    <form action="{{ route('services.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                {{-- service name --}}
                                <label for="name" class="form-label">اسم الخدمه</label>
                                <input class="form-control" type="text" id="name" name="name" required
                                    value="{{ old('name') }}" />
                                <x-input-error key="name" />
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">إضافة خدمه </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

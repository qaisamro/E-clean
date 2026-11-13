<x-app-layout :title=" config('app.name') . ' | ' . 'تعديل حسابي' ">
    <div class="ec-page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="ec-eyebrow"><i class="bx bx-user"></i> حسابي</div>
                <h1 class="ec-section-title">بيانات حسابي</h1>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">

            <!-- Profile Details -->
            <div class="card mb-4">
                @include('profile.partials.update-profile-information-form')
            </div>
            <!-- /Profile Details -->

            <!-- Update Password -->
            <div class="card mb-4">
                @include('profile.partials.update-password-form')
            </div>
            <!-- /Update Password -->
        </div>
    </div>
</x-app-layout>


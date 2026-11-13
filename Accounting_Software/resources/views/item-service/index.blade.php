<x-app-layout :title="config('app.name') . ' | ' . 'أسعار الخدمات'">
    <div class="ec-page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="ec-eyebrow"><i class="bx bx-dollar-circle"></i> الأسعار</div>
                <h1 class="ec-section-title">قائمة الأسعار</h1>
            </div>
            <a href="{{ route('itemServices.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i> إضافة تسعيرة
            </a>
        </div>
    </div>

    <!-- Basic Bootstrap Table -->
    <div class="card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-header">الأسعار</h5>

            <a href="{{ route('itemServices.create') }}" class="btn btn-primary me-3">إضافة تسعيرة</a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-striped">
                <thead>
                <tr>
                    <th class="ec-price-head"><strong>الصنف</strong></th>
                    @foreach($services as $service)

                        <th class="ec-price-head">
                            <strong>{{ $service->name }}</strong>
                        </th>
                    @endforeach
                </tr>
                </thead>
                <tbody class="table-border-bottom-0">

                @forelse($itemPrices as $itemPrice)
                    <tr>
                        @foreach($itemPrice as $price)

                            <td>
                                @if(is_array( $price ))
                                    @foreach($price as $id => $name)
                                        <a href="{{ route('itemPrices.show', $id) }}">{{ $name }}</a>
                                    @endforeach
                                @else
                                    {{ $price }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td class="text-center" colspan="4"><strong>لا يوجد بيانات</strong></td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div class="mt-4 px-4">
                {{ $itemPrices->links() }}
            </div>
        </div>
    </div>
    <!--/ Basic Bootstrap Table -->
</x-app-layout>

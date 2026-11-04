@extends('website.layouts.app')
@section('content')
<section style="background-color:#FAF7F2;" class="max-w-2lg mx-auto px-4 py-8">
    <div class="flex items-center gap-4 mb-6">
        <img src="{{ $vendor->logoPath }}" alt="{{ $vendor->name }}" class="w-16 h-16 rounded-[14px] object-cover border">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900">{{ $vendor->name }}</h1>
            <p class="text-sm text-gray-500 flex items-center gap-1"><i class="fa fa-map-marker-alt"></i> {{ $vendor->address }} • {{ $vendor->phone }}</p>
        </div>
    </div>

    @if($services->isEmpty())
        <div class="text-center py-16">
            <p class="text-gray-500">لا توجد خدمات لهذا المحل حالياً</p>
            <a href="{{ route('web.home') }}" class="btn_solid mt-4 inline-block">العودة للرئيسية</a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($services as $service)
                <div class="flex flex-col p-4 rounded-[20px] bg-white shadow-[0_6px_14px_rgba(11,30,46,0.06)] border hover:shadow-lg transition">
                    <img src="{{ $service->thumbnailPath }}" alt="{{ $service->name }}" class="w-full h-32 object-cover rounded-[14px] mb-3">
                    <h3 class="font-semibold text-gray-900">{{ $service->name }}</h3>
                    <p class="text-sm text-gray-500 mb-3">{{ Str::limit($service->description, 80) }}</p>
                    <div class="mt-auto flex justify-between items-center border-t pt-3">
                        <span class="text-[#028090] font-bold">
                            @if($service->lowestProduct) {{ currencyPosition($service->lowestProduct->final_price) }} @else {{ __('Price on request') }} @endif
                        </span>
                        <a href="{{ route('web.cart', ['service_id' => $service->id]) }}" class="px-4 py-2 bg-[linear-gradient(135deg,#028090,#00A896)] text-white rounded-[12px] text-sm">احجز</a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>
@endsection

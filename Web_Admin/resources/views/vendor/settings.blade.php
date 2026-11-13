@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3>إعدادات المتجر</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('vendor.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @php
                            $vendor = \App\Models\Vendor::where('user_id', auth()->id())->first();
                        @endphp
                        <div class="mb-3">
                            <label>{{ __('Store Name') }}</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $vendor?->name) }}">
                        </div>
                        <div class="mb-3">
                            <label>الشعار (لوجو المتجر)</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                @if ($vendor?->logo)
                                    <img src="{{ $vendor->logoPath }}" alt="الشعار الحالي" style="width:72px;height:72px;object-fit:contain;border:1px solid #eee;border-radius:12px;background:#fff;padding:4px">
                                @else
                                    <div style="width:72px;height:72px;border:1px dashed #cbd5e1;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#94A3B8;font-size:11px;text-align:center">لا يوجد<br>شعار</div>
                                @endif
                                <input type="file" name="logo" accept="image/png,image/jpeg,image/webp" class="form-control" style="max-width:320px">
                                @if ($vendor?->logo)
                                    <label class="form-check-inline mb-0" style="font-size:12px;color:#5F6368;white-space:nowrap"><input type="checkbox" name="remove_logo" value="1" style="margin-left:4px">حذف الشعار</label>
                                @endif
                            </div>
                            <small class="text-muted">JPEG/PNG/WebP · حتى 2MB. سيظهر في المتجر داخل التطبيق.</small>
                        </div>
                        <div class="mb-3">
                            <label>صورة الخلفية (رأس متجر التطبيق)</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                @if ($vendor?->background)
                                    <img src="{{ $vendor->backgroundPath }}" alt="الخلفية الحالية" style="width:160px;height:72px;object-fit:cover;border:1px solid #eee;border-radius:12px">
                                @else
                                    <div style="width:160px;height:72px;border:1px dashed #cbd5e1;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#94A3B8;font-size:11px;text-align:center">لا توجد<br>خلفية</div>
                                @endif
                                <input type="file" name="background" accept="image/png,image/jpeg,image/webp" class="form-control" style="max-width:320px">
                                @if ($vendor?->background)
                                    <label class="form-check-inline mb-0" style="font-size:12px;color:#5F6368;white-space:nowrap"><input type="checkbox" name="remove_background" value="1" style="margin-left:4px">حذف الخلفية</label>
                                @endif
                            </div>
                            <small class="text-muted">JPEG/PNG/WebP · حتى 4MB · المقاس المقترح ≈ 1200×600. تظهر أعلى صفحة المتجر في التطبيق.</small>
                        </div>
                        <div class="mb-3">
                            <label>العنوان (اختياري)</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address', $vendor?->address) }}" placeholder="{{ __('Detailed address') }}">
                        </div>
                        <div class="mb-3">
                            <label>{{ __('Phone') }}</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $vendor?->phone) }}">
                        </div>

                        <div class="mb-3">
                            <label>الموقع الحالي (الخريطة)</label>
                            <div id="vendorMap" style="height: 320px; border: 1px solid #ddd; border-radius: 8px;"></div>
                            <div class="mt-2 d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="useMyLocation">استخدم موقعي الحالي</button>
                                <small class="text-muted align-self-center">انقر على الخريطة لتعيين موقع المتجر</small>
                            </div>
                            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $vendor?->latitude) }}">
                            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $vendor?->longitude) }}">
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    var latInput = document.getElementById('latitude');
    var lngInput = document.getElementById('longitude');
    var lat = parseFloat(latInput.value) || 31.9;
    var lng = parseFloat(lngInput.value) || 35.2;

    var map = L.map('vendorMap').setView([lat, lng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var marker = null;
    if (latInput.value && lngInput.value) {
        marker = L.marker([lat, lng]).addTo(map);
    }

    function setMarker(lat, lng) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng]).addTo(map);
        }
        latInput.value = lat.toFixed(7);
        lngInput.value = lng.toFixed(7);
    }

    map.on('click', function (e) {
        setMarker(e.latlng.lat, e.latlng.lng);
    });

    document.getElementById('useMyLocation').addEventListener('click', function () {
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by this browser.');
            return;
        }
        navigator.geolocation.getCurrentPosition(function (pos) {
            var la = pos.coords.latitude;
            var lo = pos.coords.longitude;
            map.setView([la, lo], 16);
            setMarker(la, lo);
        }, function () {
            alert('Unable to retrieve your location.');
        });
    });
})();
</script>
@endsection
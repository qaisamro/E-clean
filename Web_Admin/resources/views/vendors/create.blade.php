@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="card col-lg-8 mx-auto">
        <div class="card-header"><h3>إضافة متجر جديد</h3></div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('vendors.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3"><label>اسم المحل *</label><input type="text" name="name" class="form-control @error('name') is-invalid @enderror" required value="{{ old('name') }}">@error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6 mb-3"><label>رقم الجوال (للدخول) *</label><input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" required value="{{ old('phone') }}" placeholder="05xxxxxxxx">@error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6 mb-3"><label>الشعار</label><input type="file" name="logo" class="form-control"></div>
                    <div class="col-12 mb-3"><label>العنوان التفصيلي *</label><textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address') }}</textarea>@error('address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6 mb-3">
                        <label>المدينة</label>
                        <select name="city" id="citySelect" class="form-control @error('city') is-invalid @enderror">
                            <option value="">-- اختر المدينة --</option>
                            @foreach (config('westbank_cities') as $district => $cities)
                                <optgroup label="{{ $district }}">
                                    @foreach (array_unique($cities) as $c)
                                        <option value="{{ $c }}" {{ old('city') == $c ? 'selected' : '' }}>{{ $c }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                            <option value="other" {{ old('city') == 'other' ? 'selected' : '' }}>أخرى (أدخل يدويًا)</option>
                        </select>
                        @error('city')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3" id="cityOtherWrap" style="display: {{ old('city') == 'other' ? 'block' : 'none' }};">
                        <label>اسم المدينة (أخرى)</label>
                        <input type="text" name="city_other" class="form-control @error('city_other') is-invalid @enderror" value="{{ old('city_other') }}" placeholder="أدخل اسم المدينة">
                        @error('city_other')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3"><label>كلمة المرور *</label><input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>@error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
                    <div class="col-md-6 mb-3"><label>تأكيد كلمة المرور *</label><input type="password" name="password_confirmation" class="form-control" required></div>
                </div>
                <div class="col-12 mb-3">
                    <label>الموقع على الخريطة (اختياري)</label>
                    <div id="vendorMap" style="height: 280px; border: 1px solid #ddd; border-radius: 8px;"></div>
                    <div class="mt-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="useMyLocation">استخدم موقعي الحالي</button>
                        <small class="text-muted">انقر على الخريطة لتعيين الموقع وتعبئة العنوان</small>
                    </div>
                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                </div>
                <button class="btn btn-primary w-100">إنشاء المتجر</button>
            </form>
            <script>
                (function () {
                    var sel = document.getElementById('citySelect');
                    var wrap = document.getElementById('cityOtherWrap');
                    if (!sel || !wrap) return;
                    function toggle() {
                        wrap.style.display = (sel.value === 'other') ? 'block' : 'none';
                    }
                    sel.addEventListener('change', toggle);
                    toggle();
                })();
            </script>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    var addressInput = document.querySelector('textarea[name="address"]');
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

    function reverseGeocode(la, lo) {
        fetch('{{ route('maps.reverse-geocode') }}?lat=' + encodeURIComponent(la) + '&lng=' + encodeURIComponent(lo), {
            headers: { 'Accept': 'application/json' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data && data.address && addressInput) {
                addressInput.value = data.address;
            }
        }).catch(function () {});
    }

    function setMarker(la, lo, doReverse) {
        if (marker) {
            marker.setLatLng([la, lo]);
        } else {
            marker = L.marker([la, lo]).addTo(map);
        }
        latInput.value = la.toFixed(7);
        lngInput.value = lo.toFixed(7);
        if (doReverse) reverseGeocode(la, lo);
    }

    map.on('click', function (e) {
        setMarker(e.latlng.lat, e.latlng.lng, true);
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
            setMarker(la, lo, true);
        }, function () {
            alert('Unable to retrieve your location.');
        });
    });
})();
</script>
@endsection

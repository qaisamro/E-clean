<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4">
    <div class="card col-lg-8 mx-auto">
        <div class="card-header d-flex justify-content-between"><h3>تعديل متجر: <?php echo e($vendor->name); ?></h3><a href="<?php echo e(route('vendors.index')); ?>" class="btn btn-light">رجوع</a></div>
        <div class="card-body">
            <form action="<?php echo e(route('vendors.update', $vendor->id)); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="row">
                    <div class="col-md-6 mb-3"><label>اسم المحل *</label><input type="text" name="name" class="form-control" required value="<?php echo e(old('name', $vendor->name)); ?>"></div>
                    <div class="col-md-6 mb-3"><label>رقم الجوال (للدخول) *</label><input type="text" name="phone" class="form-control" required value="<?php echo e(old('phone', $vendor->phone)); ?>"></div>
                    <div class="col-12 mb-3"><label>العنوان *</label><textarea name="address" class="form-control" rows="2"><?php echo e(old('address', $vendor->address)); ?></textarea></div>
                    <?php
                        $cityList = collect(config('westbank_cities'))->flatten()->unique()->values();
                        $currentCity = old('city', $vendor->city);
                        $isOther = $currentCity && !$cityList->contains($currentCity);
                        $citySelectValue = $isOther ? 'other' : $currentCity;
                        $cityOtherValue = $isOther ? $currentCity : old('city_other');
                    ?>
                    <div class="col-md-6 mb-3">
                        <label>المدينة</label>
                        <select name="city" id="citySelect" class="form-control">
                            <option value="">-- اختر المدينة --</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = config('westbank_cities'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district => $cities): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <optgroup label="<?php echo e($district); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = array_unique($cities); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($c); ?>" <?php echo e($citySelectValue == $c ? 'selected' : ''); ?>><?php echo e($c); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </optgroup>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <option value="other" <?php echo e($citySelectValue == 'other' ? 'selected' : ''); ?>>أخرى (أدخل يدويًا)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3" id="cityOtherWrap" style="display: <?php echo e($citySelectValue == 'other' ? 'block' : 'none'); ?>;">
                        <label>اسم المدينة (أخرى)</label>
                        <input type="text" name="city_other" class="form-control" value="<?php echo e($cityOtherValue); ?>" placeholder="أدخل اسم المدينة">
                    </div>
                    <div class="col-md-6 mb-3"><label>الشعار الحالي</label><br><img src="<?php echo e($vendor->logoPath); ?>" width="80" class="rounded"></div>
                    <div class="col-md-6 mb-3"><label>تغيير الشعار</label><input type="file" name="logo" class="form-control"></div>
                    <div class="col-md-6 mb-3"><label>كلمة مرور جديدة (اتركه فارغاً للاحتفاظ)</label><input type="password" name="password" class="form-control"><input type="password" name="password_confirmation" class="form-control mt-1" placeholder="تأكيد"></div>
                </div>
                <div class="col-12 mb-3">
                    <label>الموقع على الخريطة (اختياري)</label>
                    <div id="vendorMap" style="height: 280px; border: 1px solid #ddd; border-radius: 8px;"></div>
                    <div class="mt-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="useMyLocation">استخدم موقعي الحالي</button>
                        <small class="text-muted">انقر على الخريطة لتعيين الموقع وتعبئة العنوان</small>
                    </div>
                    <input type="hidden" name="latitude" id="latitude" value="<?php echo e(old('latitude', $vendor->latitude)); ?>">
                    <input type="hidden" name="longitude" id="longitude" value="<?php echo e(old('longitude', $vendor->longitude)); ?>">
                </div>
                <button class="btn btn-primary w-100">تحديث</button>
            </form>
            <script>
                (function () {
                    var sel = document.getElementById('citySelect');
                    var wrap = document.getElementById('cityOtherWrap');
                    if (!sel || !wrap) return;
                    function toggle() { wrap.style.display = (sel.value === 'other') ? 'block' : 'none'; }
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
        fetch('<?php echo e(route('maps.reverse-geocode')); ?>?lat=' + encodeURIComponent(la) + '&lng=' + encodeURIComponent(lo), {
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\vendors\edit.blade.php ENDPATH**/ ?>
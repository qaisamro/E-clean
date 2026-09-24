<?php

return [
    'roles' => [
        'root',
        'admin',
        'vendor',
        'store',
        'customer',
        'visitor',
        'driver'
    ],
    'permissions' => [
        'root' => ['root', 'admin', 'visitor'],

        'service.index' => ['root', 'visitor', 'vendor', 'store'],
        'service.create' => ['root', 'visitor', 'vendor', 'store'],
        'service.store' => ['root', 'visitor', 'vendor', 'store'],
        'service.edit' => ['root', 'visitor', 'vendor', 'store'],
        'service.update' => ['root', 'visitor', 'vendor', 'store'],
        'service.status.toggle' => ['root', 'visitor', 'vendor', 'store'],

        'additional.index' => ['root', 'visitor'],
        'additional.create' => ['root', 'visitor'],
        'additional.store' => ['root', 'visitor'],
        'additional.edit' => ['root', 'visitor'],
        'additional.update' => ['root', 'visitor'],
        'additional.status.toggle' => ['root'],

        'variant.index' => ['root', 'visitor'],
        'variant.create' => ['root', 'visitor'],
        'variant.edit' => ['root', 'visitor'],
        'variant.update' => ['root', 'visitor'],
        'variant.store' => ['root', 'visitor'],
        'variant.products' => ['root', 'visitor'],

        'notification.index' => ['root', 'visitor'],
        'notification.send' => ['root', 'visitor'],

        'customer.index' => ['root', 'visitor'],
        'customer.show' => ['root', 'visitor'],
        'customer.create' => ['root', 'visitor'],
        'customer.store' => ['root', 'visitor'],
        'customer.edit' => ['root', 'visitor'],
        'customer.update' => ['root', 'visitor'],

        'product.index' => ['root', 'visitor'],
        'product.create' => ['root', 'visitor'],
        'product.store' => ['root', 'visitor'],
        'product.show' => ['root', 'visitor'],
        'product.edit' => ['root', 'visitor'],
        'product.update' => ['root', 'visitor'],
        'product.status.toggle' => ['root'],

        'banner.index' => ['root', 'visitor'],
        'banner.promotional' => ['root', 'visitor', 'vendor', 'store'],
        'banner.store' => ['root', 'visitor', 'vendor', 'store'],
        'banner.edit' => ['root', 'visitor', 'vendor', 'store'],
        'banner.update' => ['root', 'visitor', 'vendor', 'store'],
        'banner.destroy' => ['root', 'visitor', 'vendor', 'store'],
        'banner.status.toggle' => ['root', 'visitor', 'vendor', 'store'],

        'order.index' => ['root', 'visitor'],
        'order.show' => ['root', 'visitor'],
        'order.status.change' => ['root', 'visitor'],
        'order.print.labels' => ['root', 'visitor'],
        'order.print.invioce' => ['root', 'visitor'],
        'orderIncomplete.index' => ['root', 'visitor'],
        'orderIncomplete.paid' => ['root'],

        'revenue.index' => ['root', 'visitor'],
        'revenue.generate.pdf' => ['root', 'visitor'],
        'report.generate.pdf' => ['root', 'visitor'],

        'coupon.index' => ['root', 'visitor'],
        'coupon.create' => ['root', 'visitor'],
        'coupon.store' => ['root', 'visitor'],
        'coupon.edit' => ['root', 'visitor'],
        'coupon.update' => ['root', 'visitor'],

        'contact' => ['root', 'visitor'],

        'driver.index' => ['root', 'visitor'],
        'driver.create' => ['root', 'visitor'],
        'driver.store' => ['root', 'visitor'],
        'driverAssign' => ['root', 'visitor'],
        'driver.details' => ['root', 'visitor'],

        'profile.index' => ['root', 'visitor'],
        'profile.update' => ['root', 'visitor'],
        'profile.edit' => ['root', 'visitor'],
        'profile.change-password' => ['root', 'visitor'],

        'schedule.index' => ['root', 'visitor'],
        'toggole.status.update' => ['root'],
        'schedule.update' => ['root', 'visitor'],

        'dashboard.calculation' => ['root', 'visitor'],
        'dashboard.revenue' => ['root', 'visitor'],
        'dashboard.overview' => ['root', 'visitor'],

        'setting.show' => ['root', 'visitor'],
        'setting.edit' => ['root', 'visitor'],
        'setting.update' => ['root', 'visitor'],

        'sms-gateway.index' => ['root', 'visitor'],
        'sms-gateway.update' => ['root', 'visitor'],

        'admin.index' => ['root', 'visitor'],
        'admin.status-update' => ['root'],
        'admin.create' => ['root', 'visitor'],
        'admin.store' => ['root'],
        'admin.edit' => ['root', 'visitor'],
        'admin.update' => ['root'],
        'admin.show' => ['root', 'visitor'],
        'admin.set-permission' => ['root'],
    ],
];

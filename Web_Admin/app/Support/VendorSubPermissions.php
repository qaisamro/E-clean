<?php

namespace App\Support;

/**
 * The exact catalog the store owner uses to grant capabilities to a child
 * account. Every permission name is literally the route name the middleware
 * checks (`can($routeName)`), so what the owner toggles is exactly what the
 * child may reach — nothing more.
 *
 * Services, additional services, banners, settings, root dashboards and
 * sub-account management are deliberately NOT offered: they belong to the
 * Super Admin / the owner only.
 */
class VendorSubPermissions
{
    /** Always granted implicitly to every sub account (no toggle shown). */
    public const BASELINE = [
        'profile.index',
        'profile.edit',
        'profile.update',
        'profile.change-password',
        'profile.change-password.form',
        'web-notifications.feed',
        'maps.reverse-geocode',
        'root',
    ];

    /** @return array<int, array{key:string,title:string,icon:string,color:string,items:array<int,array{permission:string,title:string}>}> */
    public static function groups(): array
    {
        return [
            [
                'key' => 'dashboard',
                'title' => 'لوحة التحكم والبطاقات',
                'icon' => 'fa-gauge-high',
                'color' => '#028090',
                'items' => [
                    ['permission' => 'vendor.dashboard', 'title' => 'الوصول للوحة التحكم (الرئيسية)'],
                    ['permission' => 'vendor.dashboard.revenue', 'title' => 'بطاقة الإيرادات المحصلة'],
                    ['permission' => 'vendor.dashboard.total_orders', 'title' => 'بطاقة إجمالي الطلبات'],
                    ['permission' => 'vendor.dashboard.pending', 'title' => 'بطاقة «بانتظار التأكيد»'],
                    ['permission' => 'vendor.dashboard.delivered', 'title' => 'بطاقة «تم التوصيل»'],
                    ['permission' => 'vendor.dashboard.in_progress', 'title' => 'بطاقة «قيد التنفيذ»'],
                    ['permission' => 'vendor.dashboard.services', 'title' => 'بطاقة «الخدمات»'],
                    ['permission' => 'vendor.dashboard.products', 'title' => 'بطاقة «المنتجات»'],
                    ['permission' => 'vendor.dashboard.offers', 'title' => 'بطاقة «العروض»'],
                    ['permission' => 'vendor.dashboard.recent_orders', 'title' => 'جدول «أحدث الطلبات»'],
                ],
            ],
            [
                'key' => 'pos',
                'title' => 'نقطة البيع (POS)',
                'icon' => 'fa-cash-register',
                'color' => '#028090',
                'items' => [
                    ['permission' => 'pos.index', 'title' => 'فتح شاشة نقطة البيع'],
                    ['permission' => 'pos.sales', 'title' => 'سجل المبيعات (POS)'],
                    ['permission' => 'pos.store', 'title' => 'تنفيذ عملية بيع'],
                    ['permission' => 'pos.payment', 'title' => 'شاشة إتمام الدفع'],
                    ['permission' => 'pos.fetch.products', 'title' => 'البحث عن المنتجات في POS'],
                    ['permission' => 'pos.customerStore', 'title' => 'إضافة زبون من POS'],
                    ['permission' => 'pos.addressStore', 'title' => 'إضافة عنوان من POS'],
                    ['permission' => 'order.payment-status', 'title' => 'تأكيد الدفع / وضع كمدفوع من شاشة POS'],
                ],
            ],
            [
                'key' => 'orders',
                'title' => 'الطلبات',
                'icon' => 'fa-shopping-cart',
                'color' => '#00A896',
                'items' => [
                    ['permission' => 'order.index', 'title' => 'قائمة الطلبات'],
                    ['permission' => 'order.show', 'title' => 'تفاصيل الطلب'],
                    ['permission' => 'order.edit', 'title' => 'فتح تعديل الفاتورة'],
                    ['permission' => 'order.update', 'title' => 'حفظ تعديل الفاتورة'],
                    ['permission' => 'order.status.change', 'title' => 'تغيير حالة الطلب'],
                    ['permission' => 'orderIncomplete.paid', 'title' => 'تأكيد الدفع (زر Paid)'],
                    ['permission' => 'order.print.invioce', 'title' => 'طباعة الفاتورة'],
                    ['permission' => 'order.pos-invoice', 'title' => 'طباعة فاتورة POS'],
                    ['permission' => 'order.print.labels', 'title' => 'طباعة ملصقات الطلب'],
                    ['permission' => 'driver.assign', 'title' => 'إسناد سائق للطلب'],
                ],
            ],
            [
                'key' => 'products',
                'title' => 'المنتجات',
                'icon' => 'fa-tshirt',
                'color' => '#156172',
                'items' => [
                    ['permission' => 'product.index', 'title' => 'قائمة المنتجات'],
                    ['permission' => 'product.create', 'title' => 'فتح إنشاء منتج'],
                    ['permission' => 'product.store', 'title' => 'حفظ منتج جديد'],
                    ['permission' => 'product.edit', 'title' => 'فتح تعديل منتج'],
                    ['permission' => 'product.update', 'title' => 'حفظ تعديل منتج'],
                    ['permission' => 'product.status.toggle', 'title' => 'تفعيل / تعطيل منتج'],
                ],
            ],
            [
                'key' => 'offers',
                'title' => 'العروض',
                'icon' => 'fa-tags',
                'color' => '#DC2626',
                'items' => [
                    ['permission' => 'offer.index', 'title' => 'قائمة العروض'],
                    ['permission' => 'offer.create', 'title' => 'فتح إنشاء عرض'],
                    ['permission' => 'offer.store', 'title' => 'حفظ عرض جديد'],
                    ['permission' => 'offer.edit', 'title' => 'فتح تعديل عرض'],
                    ['permission' => 'offer.update', 'title' => 'حفظ تعديل عرض'],
                    ['permission' => 'offer.destroy', 'title' => 'حذف عرض'],
                    ['permission' => 'offer.status.toggle', 'title' => 'تفعيل / تعطيل عرض'],
                ],
            ],
            [
                'key' => 'coupons',
                'title' => 'الكوبونات',
                'icon' => 'fa-ticket-alt',
                'color' => '#F59E0B',
                'items' => [
                    ['permission' => 'coupon.index', 'title' => 'قائمة الكوبونات'],
                    ['permission' => 'coupon.create', 'title' => 'فتح إنشاء كوبون'],
                    ['permission' => 'coupon.store', 'title' => 'حفظ كوبون جديد'],
                    ['permission' => 'coupon.edit', 'title' => 'فتح تعديل كوبون'],
                    ['permission' => 'coupon.update', 'title' => 'حفظ تعديل كوبون'],
                    ['permission' => 'coupon.destroy', 'title' => 'حذف كوبون'],
                ],
            ],
            [
                'key' => 'drivers',
                'title' => 'السائقون',
                'icon' => 'fa-motorcycle',
                'color' => '#0B1E2E',
                'items' => [
                    ['permission' => 'driver.index', 'title' => 'قائمة السائقين'],
                    ['permission' => 'driver.create', 'title' => 'فتح إنشاء سائق'],
                    ['permission' => 'driver.store', 'title' => 'حفظ سائق جديد'],
                    ['permission' => 'driver.edit', 'title' => 'فتح تعديل سائق'],
                    ['permission' => 'driver.update', 'title' => 'حفظ تعديل سائق'],
                    ['permission' => 'driver.destroy', 'title' => 'حذف سائق'],
                    ['permission' => 'driver.details', 'title' => 'تفاصيل السائق'],
                    ['permission' => 'driver.status.toggle', 'title' => 'تفعيل / تعطيل سائق'],
                ],
            ],
            [
                'key' => 'customers_notifications',
                'title' => 'العملاء والإشعارات والمواعيد',
                'icon' => 'fa-users',
                'color' => '#16A34A',
                'items' => [
                    ['permission' => 'vendor.customers.index', 'title' => 'صفحة زبائن المتجر'],
                    ['permission' => 'vendor.customers.edit', 'title' => 'فتح تعديل زبون'],
                    ['permission' => 'vendor.customers.update', 'title' => 'حفظ تعديل زبون'],
                    ['permission' => 'vendor.notifications.index', 'title' => 'صفحة إرسال إشعار'],
                    ['permission' => 'vendor.notifications.send', 'title' => 'تنفيذ إرسال الإشعار'],
                    ['permission' => 'vendor.appointments.edit', 'title' => 'صفحة مواعيد الاستلام والتوصيل'],
                    ['permission' => 'vendor.appointments.update', 'title' => 'حفظ تعديل المواعيد'],
                    ['permission' => 'vendor.appointments.reschedule', 'title' => 'إعادة جدولة موعد'],
                ],
            ],
            [
                'key' => 'invoices_settings',
                'title' => 'قوالب الفاتورة والإعدادات',
                'icon' => 'fa-file-invoice',
                'color' => '#64748B',
                'items' => [
                    ['permission' => 'vendor.invoice-template.edit', 'title' => 'صفحة قالب الفاتورة'],
                    ['permission' => 'vendor.invoice-template.update', 'title' => 'حفظ قالب الفاتورة'],
                    ['permission' => 'vendor.invoice-template.reset', 'title' => 'استرجاع القالب الافتراضي'],
                    ['permission' => 'vendor.invoice-template.preview', 'title' => 'معاينة قالب الفاتورة'],
                    ['permission' => 'vendor.settings', 'title' => 'صفحة إعدادات المتجر'],
                ],
            ],
        ];
    }

    /** Flat list of every permission a sub account may be granted. */
    public static function all(): array
    {
        $names = [];
        foreach (static::groups() as $group) {
            foreach ($group['items'] as $item) {
                $names[] = $item['permission'];
            }
        }
        return $names;
    }
}

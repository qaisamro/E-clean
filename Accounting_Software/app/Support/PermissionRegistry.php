<?php

namespace App\Support;

/**
 * Single source of truth for every permission an admin/employee can hold.
 * Grouped by module for the checkbox matrix in the user form.
 * Super admin holds none of these literally — Super admins bypass checks.
 */
class PermissionRegistry
{
    /** @return array<string, array<int, array{key:string,label:string}>> */
    public static function groups(): array
    {
        return [
            'الطلبات' => [
                ['key' => 'orders.view',   'label' => 'عرض الطلبات وقائمة الطلبات'],
                ['key' => 'orders.create','label' => 'إضافة طلب جديد'],
                ['key' => 'orders.edit',  'label' => 'تعديل أي طلب'],
                ['key' => 'orders.delete','label' => 'حذف طلب'],
            ],
            'العملاء' => [
                ['key' => 'customers.view',  'label' => 'عرض العملاء والمديونية'],
                ['key' => 'customers.manage','label' => 'إدارة العملاء (تعديل/حذف/إعادة تعيين كلمة المرور)'],
            ],
            'المخزن' => [
                ['key' => 'inventory.view',  'label' => 'عرض المنتجات وحركات المخزن والنواقص والتقارير'],
                ['key' => 'inventory.manage','label' => 'إدارة المخزن (إضافة/تعديل/حذف منتج، حركة، فئة)'],
            ],
            'الأسعار' => [
                ['key' => 'item_services.view',  'label' => 'عرض أسعار الخدمات'],
                ['key' => 'item_services.manage','label' => 'إدارة أسعار الخدمات (إضافة/تعديل/حذف)'],
                ['key' => 'services.view',       'label' => 'عرض الخدمات'],
                ['key' => 'services.manage',     'label' => 'إدارة الخدمات (إضافة/تعديل/حذف)'],
            ],
            'المصروفات والرواتب' => [
                ['key' => 'expenses.view',  'label' => 'عرض المصروفات'],
                ['key' => 'expenses.manage','label' => 'إدارة المصروفات (إضافة/تعديل/حذف)'],
                ['key' => 'salaries.view',  'label' => 'عرض الرواتب'],
                ['key' => 'salaries.manage','label' => 'إدارة الرواتب (إضافة/تعديل/حذف)'],
            ],
            'الموردون' => [
                ['key' => 'suppliers.view',  'label' => 'عرض الموردين والفواتير وكشف الحساب'],
                ['key' => 'suppliers.manage','label' => 'إدارة الموردين (إضافة/تعديل/حذف) والفواتير والدفعات'],
            ],
            'التقارير المالية' => [
                ['key' => 'reports.view', 'label' => 'عرض التقارير المالية'],
            ],
        ];
    }

    /** @return array<int, string> */
    public static function keys(): array
    {
        $keys = [];
        foreach (self::groups() as $members) {
            foreach ($members as $member) {
                $keys[] = $member['key'];
            }
        }
        return $keys;
    }

    /** @return array<string, string> */
    public static function labels(): array
    {
        $labels = [];
        foreach (self::groups() as $members) {
            foreach ($members as $member) {
                $labels[$member['key']] = $member['label'];
            }
        }
        return $labels;
    }

    public static function isValid(string $key): bool
    {
        return in_array($key, self::keys(), true);
    }
}

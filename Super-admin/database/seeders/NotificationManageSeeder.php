<?php

namespace Database\Seeders;

use App\Models\NotificationManage;
use Illuminate\Database\Seeder;

class NotificationManageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'title' => 'طلب جديد',
                'name' => 'new_order',
                'message' => 'تم تقديم طلب جديد',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'إشعار كوبون',
                'name' => 'coupon_notify',
                'message' => 'احصل على كوبون خصم على منتجاتنا',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'تم تأكيد الطلب',
                'name' => 'order_confirmed',
                'message' => 'تم تأكيد طلبك بنجاح',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'تم استلام الطلب',
                'name' => 'order_picked',
                'message' => 'قام سائق التوصيل باستلام طلبك',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'طلب قيد التجهيز',
                'name' => 'order_processing',
                'message' => 'جارٍ تجهيز طلبك، سنخبرك عند الانتهاء',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'تم إلغاء الطلب',
                'name' => 'order_cancelled',
                'message' => 'تم إلغاء طلبك',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'تم تسليم الطلب',
                'name' => 'order_delivered',
                'message' => 'تم تسليم طلبك بنجاح',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'تم تعيين سائق',
                'name' => 'driver_assigned',
                'message' => 'تم تعيين سائق توصيل لطلبك',
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        NotificationManage::truncate();

        NotificationManage::insert($data);
    }
}

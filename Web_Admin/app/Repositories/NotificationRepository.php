<?php


namespace App\Repositories;

use App\Models\Notification;

class NotificationRepository extends Repository
{
    public function model()
    {
        return Notification::class;
    }

    public function getAll()
    {
        return $this->model()::all();
    }

    public function notificationListByStatus($read = null)
    {
        $customer = auth()->user()->customer;
        $notifications = $this->model()::where('customer_id', $customer->id);

        if ($read) {
            $notifications = $notifications->where('isRead', $read);
        }

        return $notifications->latest()->get();
    }

    public function storeByRequest($customerId,$message,$title): Notification
    {
        $notification = $this->model()::create([
            'customer_id' => $customerId,
            'title' => $title,
            'message' => $message,
            'isRead' => (int)0
        ]);
        return $notification;
    }

    /**
     * Create the per-customer inbox rows for a store notification campaign.
     *
     * @param  array<int, int>  $customerIds
     */
    public function storeManyForVendor(
        array $customerIds,
        string $message,
        ?string $title,
        int $vendorId,
        int $vendorNotificationLogId
    ): int {
        $customerIds = array_values(array_unique(array_map('intval', $customerIds)));
        if ($customerIds === []) {
            return 0;
        }

        $now = now();
        $rows = [];
        foreach ($customerIds as $customerId) {
            $rows[] = [
                'customer_id' => $customerId,
                'vendor_id' => $vendorId,
                'vendor_notification_log_id' => $vendorNotificationLogId,
                'title' => $title,
                'message' => $message,
                'isRead' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            $this->model()::insert($chunk);
        }

        return count($rows);
    }

    public function readUpdateByRequest(Notification $notification): Notification
    {
        $notification->update([
            'isRead' => 1
        ]);
        return $notification;
    }

    public function deleteByRequest(Notification $notification): Notification
    {
        $notification->delete();
        return $notification;
    }


}

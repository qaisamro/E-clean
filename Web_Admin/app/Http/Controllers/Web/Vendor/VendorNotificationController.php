<?php

namespace App\Http\Controllers\Web\Vendor;

use App\Http\Controllers\Controller;
use App\Models\DeviceKey;
use App\Models\VendorNotificationLog;
use App\Repositories\NotificationRepository;
use App\Services\FirebaseMessagingService;
use App\Services\VendorStoreCustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class VendorNotificationController extends Controller
{
    public function __construct(
        private VendorStoreCustomerService $storeCustomers,
        private NotificationRepository $notifications,
        private FirebaseMessagingService $firebase
    ) {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        abort_unless($user && \App\Support\VendorAccount::isStoreStaff($user), 403);

        $vendor = $this->storeCustomers->vendorForUser($user);
        $customers = $this->storeCustomers->allCustomersForSelection($vendor);

        $requestedCustomerId = (int) $request->query('customer_id', 0);
        $selectedCustomerId = $customers
            ->contains(fn ($customer) => (int) $customer->id === $requestedCustomerId)
            ? $requestedCustomerId
            : null;

        $logs = VendorNotificationLog::query()
            ->where('vendor_id', $vendor->id)
            ->with(['customer.user'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'customers' => $this->storeCustomers->eligibleCustomerCount($vendor),
            'disabled_customers' => $this->storeCustomers->notificationDisabledCount($vendor),
            'campaigns' => VendorNotificationLog::where('vendor_id', $vendor->id)->count(),
            'last_sent_at' => VendorNotificationLog::where('vendor_id', $vendor->id)
                ->where('status', '!=', 'skipped')
                ->latest()
                ->value('created_at'),
        ];

        return view('vendor.notifications.index', [
            'vendor' => $vendor,
            'customers' => $customers,
            'logs' => $logs,
            'summary' => $summary,
            'selectedCustomerId' => $selectedCustomerId,
            'requestToken' => (string) Str::uuid(),
        ]);
    }

    public function send(Request $request)
    {
        $user = $request->user();
        abort_unless($user && \App\Support\VendorAccount::isStoreStaff($user), 403);

        $vendor = $this->storeCustomers->vendorForUser($user);

        $validated = $request->validate([
            'audience' => ['required', Rule::in(['all', 'single'])],
            'customer_id' => ['nullable', 'integer', Rule::exists('customers', 'id')],
            'message' => ['required', 'string', 'min:2', 'max:255'],
            'request_token' => ['required', 'string', 'max:80'],
        ], [
            'audience.required' => 'اختر نوع المستلمين.',
            'audience.in' => 'نوع المستلمين غير صالح.',
            'customer_id.required' => 'اختر عميلًا من قائمة عملاء متجرك.',
            'customer_id.exists' => 'العميل المحدد غير موجود.',
            'message.required' => 'اكتب نص الإشعار.',
            'message.min' => 'نص الإشعار قصير جدًا.',
            'message.max' => 'نص الإشعار يجب ألا يتجاوز 255 حرفًا.',
            'request_token.required' => 'انتهت صلاحية النموذج، أعد تحميل الصفحة.',
        ]);

        $audience = $validated['audience'];
        $customerId = $audience === 'single' ? (int) ($validated['customer_id'] ?? 0) : null;

        if ($audience === 'single') {
            if ($customerId <= 0) {
                return back()->withInput()->withErrors([
                    'customer_id' => 'اختر عميلًا من قائمة عملاء متجرك.',
                ]);
            }

            if (!$this->storeCustomers->isCustomerOfVendor($vendor, $customerId)) {
                return back()->withInput()->withErrors([
                    'customer_id' => 'العميل المحدد ليس من عملاء متجرك.',
                ]);
            }
        }

        if (VendorNotificationLog::where('request_token', $validated['request_token'])->exists()) {
            return back()->withInput()->with('error', 'تم إرسال هذا النموذج مسبقًا. أعد تحميل الصفحة قبل الإرسال مرة أخرى.');
        }

        $message = trim($validated['message']);
        $fingerprint = hash('sha256', implode('|', [
            (int) $vendor->id,
            $audience,
            $customerId ?? 0,
            $message,
        ]));

        $duplicate = VendorNotificationLog::query()
            ->where('vendor_id', $vendor->id)
            ->where('fingerprint', $fingerprint)
            ->where('created_at', '>=', now()->subMinutes(VendorStoreCustomerService::DUPLICATE_WINDOW_MINUTES))
            ->exists();

        if ($duplicate) {
            return back()->withInput()->with('error', 'نفس الإشعار أُرسل حديثًا. انتظر قليلًا قبل إرساله مرة أخرى.');
        }

        $latestCampaign = VendorNotificationLog::query()
            ->where('vendor_id', $vendor->id)
            ->latest('id')
            ->first();

        if ($latestCampaign
            && $latestCampaign->created_at
            && $latestCampaign->created_at->gt(now()->subSeconds(VendorStoreCustomerService::COOLDOWN_SECONDS))) {
            return back()->withInput()->with('error', 'تم الإرسال قبل لحظات. انتظر ' . VendorStoreCustomerService::COOLDOWN_SECONDS . ' ثانية بين كل إشعار وآخر.');
        }

        $hourlyCampaigns = VendorNotificationLog::query()
            ->where('vendor_id', $vendor->id)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($hourlyCampaigns >= VendorStoreCustomerService::HOURLY_LIMIT) {
            return back()->withInput()->with('error', 'تم بلوغ الحد الأقصى للإشعارات خلال الساعة. حاول لاحقًا.');
        }

        $recipients = $this->storeCustomers->recipients($vendor, $customerId);
        $recipientIds = $recipients->pluck('id')->map(fn ($id) => (int) $id)->all();
        $skippedCount = $audience === 'single'
            ? max(0, 1 - count($recipientIds))
            : $this->storeCustomers->notificationDisabledCount($vendor);

        if ($recipientIds === []) {
            VendorNotificationLog::create([
                'vendor_id' => $vendor->id,
                'user_id' => $user->id,
                'audience' => $audience,
                'customer_id' => $customerId,
                'title' => $vendor->name,
                'message' => $message,
                'recipients_count' => 0,
                'skipped_count' => $skippedCount,
                'push_success_count' => 0,
                'push_failed_count' => 0,
                'push_status' => 'skipped',
                'status' => 'skipped',
                'fingerprint' => $fingerprint,
                'request_token' => $validated['request_token'],
                'details' => ['reason' => 'no_eligible_recipients'],
            ]);

            return back()->with('error', 'لم يتم إرسال أي إشعار: لا يوجد عملاء مؤهلون (قد يكون العميل أوقف إشعارات المتاجر).');
        }

        $title = $vendor->name;

        try {
            $log = DB::transaction(function () use (
                $vendor,
                $user,
                $audience,
                $customerId,
                $title,
                $message,
                $recipientIds,
                $skippedCount,
                $fingerprint,
                $validated
            ) {
                $log = VendorNotificationLog::create([
                    'vendor_id' => $vendor->id,
                    'user_id' => $user->id,
                    'audience' => $audience,
                    'customer_id' => $customerId,
                    'title' => $title,
                    'message' => $message,
                    'recipients_count' => 0,
                    'skipped_count' => $skippedCount,
                    'push_success_count' => 0,
                    'push_failed_count' => 0,
                    'push_status' => null,
                    'status' => 'sending',
                    'fingerprint' => $fingerprint,
                    'request_token' => $validated['request_token'],
                    'details' => ['audience' => $audience],
                ]);

                $created = $this->notifications->storeManyForVendor(
                    $recipientIds,
                    $message,
                    $title,
                    (int) $vendor->id,
                    (int) $log->id
                );

                if ($created !== count($recipientIds)) {
                    throw new \RuntimeException('Not all in-app notification rows were created.');
                }

                $log->update(['recipients_count' => $created]);

                return $log;
            });
        } catch (Throwable $exception) {
            report($exception);

            return back()->withInput()->with('error', 'تعذر حفظ الإشعار. حاول مرة أخرى.');
        }

        $deviceTokens = DeviceKey::query()
            ->whereIn('customer_id', $recipientIds)
            ->pluck('key')
            ->filter()
            ->unique()
            ->values()
            ->all();

        try {
            $push = $this->firebase->sendMany(
                $deviceTokens,
                $title,
                $message,
                [
                    'type' => 'vendor_notification',
                    'vendor_id' => (string) $vendor->id,
                    'vendor_notification_log_id' => (string) $log->id,
                ]
            );
        } catch (Throwable $exception) {
            report($exception);
            $push = [
                'status' => 'failed',
                'success' => 0,
                'failed' => count($deviceTokens),
            ];
        }

        $pushStatus = (string) ($push['status'] ?? 'skipped');
        $pushSuccess = max(0, (int) ($push['success'] ?? 0));
        $pushFailed = max(0, (int) ($push['failed'] ?? 0));
        $campaignStatus = in_array($pushStatus, ['partial', 'failed', 'error'], true)
            ? 'partial'
            : 'sent';

        $log->update([
            'push_status' => $pushStatus,
            'push_success_count' => $pushSuccess,
            'push_failed_count' => $pushFailed,
            'status' => $campaignStatus,
        ]);

        $summary = 'تم حفظ الإشعار داخل تطبيق ' . count($recipientIds) . ' عميل.';
        if ($deviceTokens !== []) {
            $summary .= ' نجح Push لـ ' . $pushSuccess . ' جهاز';
            if ($pushFailed > 0) {
                $summary .= ' وفشل ' . $pushFailed;
            }
            $summary .= '.';
        } else {
            $summary .= ' لا توجد أجهزة مفعّلة لاستلام Push.';
        }

        return redirect()
            ->route('vendor.notifications.index')
            ->with('success', $summary);
    }
}

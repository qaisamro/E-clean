<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Vendor;
use App\Models\VendorInvoiceTemplate;
use App\Support\InvoiceTemplate;
use App\Support\OrderVendorOwnership;
use App\Support\PosInvoiceData;
use App\Support\StoreOrderAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class VendorInvoiceTemplateController extends Controller
{
    private function vendor(): Vendor
    {
        abort_unless(\App\Support\VendorAccount::isStoreStaff(auth()->user()), 403);
        $vendor = \App\Support\VendorAccount::vendorFor(auth()->user());
        abort_unless($vendor, 403);
        return $vendor;
    }

    private function logo(?VendorInvoiceTemplate $template): ?string
    {
        if (!$template?->logo_path || !Storage::disk('local')->exists($template->logo_path)) {
            return null;
        }
        $disk = Storage::disk('local');
        return 'data:'.$disk->mimeType($template->logo_path).';base64,'.base64_encode($disk->get($template->logo_path));
    }

    public function edit()
    {
        $vendor = $this->vendor();
        $template = VendorInvoiceTemplate::where('vendor_id', $vendor->id)->first();
        $settings = InvoiceTemplate::settings($template);
        $orders = OrderVendorOwnership::filterConsistentOrders(
            Order::where('vendor_id', $vendor->id)->with('products.service')->latest()->limit(30)->get()
        );
        return response()->view('vendor.invoice-template.edit', [
            'vendor' => $vendor, 'settings' => $settings, 'defaults' => InvoiceTemplate::defaults(),
            'fields' => InvoiceTemplate::fields(), 'texts' => InvoiceTemplate::texts(),
            'orders' => $orders, 'logo' => $this->logo($template),
        ])->header('Cache-Control', 'private, no-store');
    }

    public function update(Request $request)
    {
        $vendor = $this->vendor();
        if (is_string($request->input('settings'))) {
            try {
                $request->merge(['settings' => json_decode($request->input('settings'), true, 32, JSON_THROW_ON_ERROR)]);
            } catch (\JsonException) {
                throw ValidationException::withMessages(['settings' => 'إعدادات القالب غير صالحة.']);
            }
        }
        $validated = $request->validate(InvoiceTemplate::rules() + [
            'logo' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024', 'dimensions:max_width=2400,max_height=2400'],
            'remove_logo' => ['sometimes', 'boolean'],
        ]);
        $newLogo = null;
        $oldLogo = null;
        try {
            DB::transaction(function () use ($vendor, $request, $validated, &$newLogo, &$oldLogo) {
                Vendor::whereKey($vendor->id)->lockForUpdate()->firstOrFail();
                $template = VendorInvoiceTemplate::firstOrNew(['vendor_id' => $vendor->id]);
                $oldLogo = $template->logo_path;
                if ($request->hasFile('logo')) {
                    $newLogo = $request->file('logo')->store('invoice-logos/'.$vendor->id, 'local');
                    if (!$newLogo) {
                        throw new \RuntimeException('Unable to store invoice logo.');
                    }
                    $template->logo_path = $newLogo;
                } elseif ($request->boolean('remove_logo')) {
                    $template->logo_path = null;
                }
                $settings = array_replace_recursive(InvoiceTemplate::defaults(), $validated['settings']);
                foreach (InvoiceTemplate::texts() as $key => $label) {
                    $settings['text'][$key] = (string) ($settings['text'][$key] ?? '');
                }
                $template->settings = $settings;
                $template->save();
            });
        } catch (\Throwable $e) {
            if ($newLogo) {
                Storage::disk('local')->delete($newLogo);
            }
            throw $e;
        }
        if ($oldLogo && ($newLogo || $request->boolean('remove_logo'))) {
            Storage::disk('local')->delete($oldLogo);
        }
        return response()->json(['message' => 'تم حفظ قالب الفاتورة.']);
    }

    public function reset()
    {
        $vendor = $this->vendor();
        $oldLogo = DB::transaction(function () use ($vendor) {
            Vendor::whereKey($vendor->id)->lockForUpdate()->firstOrFail();
            $template = VendorInvoiceTemplate::where('vendor_id', $vendor->id)->first();
            $logo = $template?->logo_path;
            $template?->delete();
            return $logo;
        });
        if ($oldLogo) {
            Storage::disk('local')->delete($oldLogo);
        }
        return response()->json(['message' => 'تمت استعادة القالب الافتراضي.', 'settings' => InvoiceTemplate::defaults()]);
    }

    public function preview(Request $request)
    {
        $vendor = $this->vendor();
        $request->validate(['order' => ['nullable', 'integer']]);
        $order = $request->filled('order') ? Order::findOrFail($request->integer('order')) : null;
        if ($order) {
            StoreOrderAccess::assertCanView($order);
            abort_unless((int) $order->vendor_id === (int) $vendor->id, 404);
        }
        return $this->document($vendor, $order, 'normal', true);
    }

    public function print(Request $request, Order $order)
    {
        abort_unless(auth()->user()?->hasAnyRole(['vendor_admin', 'root', 'admin']), 403);
        StoreOrderAccess::assertCanView($order);
        $request->validate(['copy' => ['nullable', 'in:normal,customer,store']]);
        // Do not brand a mixed or unresolved legacy order as belonging to one store.
        abort_unless($order->vendor && OrderVendorOwnership::productsMatchOrder($order), 409,
            'تعذر تحديد متجر واحد موثوق لهذا الطلب. يمكن للمسؤول مراجعة الفاتورة الأصلية.');
        return $this->document($order->vendor, $order, $request->input('copy') ?: 'normal', false);
    }

    private function document(Vendor $vendor, ?Order $order, string $copy, bool $preview)
    {
        $template = VendorInvoiceTemplate::where('vendor_id', $vendor->id)->first();
        return response()->view('vendor.invoice-template.document', [
            'payload' => ['settings' => InvoiceTemplate::settings($template), 'data' => PosInvoiceData::make($vendor, $order),
                'logo' => $this->logo($template), 'copy' => $copy, 'preview' => $preview],
        ])->header('Cache-Control', 'private, no-store')->header('X-Frame-Options', 'SAMEORIGIN');
    }
}

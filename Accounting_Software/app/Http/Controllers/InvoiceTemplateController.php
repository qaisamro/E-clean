<?php

namespace App\Http\Controllers;

use App\Models\InvoiceTemplate;
use App\Models\Order;
use App\Support\InvoiceTemplate as InvoiceTemplateSchema;
use App\Support\PosInvoiceData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class InvoiceTemplateController extends Controller
{
    private function logo(?InvoiceTemplate $template): ?string
    {
        if (! $template?->logo_path || ! Storage::disk('local')->exists($template->logo_path)) {
            return null;
        }
        $disk = Storage::disk('local');
        return 'data:'.$disk->mimeType($template->logo_path).';base64,'.base64_encode($disk->get($template->logo_path));
    }

    public function edit()
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

        $template = InvoiceTemplate::first();
        $settings = InvoiceTemplateSchema::settings($template);
        $orders = Order::latest()->limit(30)->get();

        return response()->view('invoice-template.edit', [
            'settings' => $settings,
            'defaults' => InvoiceTemplateSchema::defaults(),
            'fields' => InvoiceTemplateSchema::fields(),
            'texts' => InvoiceTemplateSchema::texts(),
            'orders' => $orders,
            'logo' => $this->logo($template),
        ])->header('Cache-Control', 'private, no-store');
    }

    public function update(Request $request)
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

        if (is_string($request->input('settings'))) {
            try {
                $request->merge(['settings' => json_decode($request->input('settings'), true, 32, JSON_THROW_ON_ERROR)]);
            } catch (\JsonException) {
                throw ValidationException::withMessages(['settings' => 'إعدادات القالب غير صالحة.']);
            }
        }

        $validated = $request->validate(InvoiceTemplateSchema::rules() + [
            'logo' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024', 'dimensions:max_width=2400,max_height=2400'],
            'remove_logo' => ['sometimes', 'boolean'],
        ]);

        $newLogo = null;
        $oldLogo = null;
        try {
            DB::transaction(function () use ($request, $validated, &$newLogo, &$oldLogo) {
                $template = InvoiceTemplate::firstOrNew([]);
                $oldLogo = $template->logo_path;
                if ($request->hasFile('logo')) {
                    $newLogo = $request->file('logo')->store('invoice-logos', 'local');
                    if (! $newLogo) {
                        throw new \RuntimeException('Unable to store invoice logo.');
                    }
                    $template->logo_path = $newLogo;
                } elseif ($request->boolean('remove_logo')) {
                    $template->logo_path = null;
                }
                $settings = array_replace_recursive(InvoiceTemplateSchema::defaults(), $validated['settings']);
                foreach (InvoiceTemplateSchema::texts() as $key => $label) {
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
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

        $oldLogo = DB::transaction(function () {
            $template = InvoiceTemplate::first();
            $logo = $template?->logo_path;
            $template?->delete();
            return $logo;
        });

        if ($oldLogo) {
            Storage::disk('local')->delete($oldLogo);
        }

        return response()->json(['message' => 'تمت استعادة القالب الافتراضي.', 'settings' => InvoiceTemplateSchema::defaults()]);
    }

    public function preview(Request $request)
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

        $request->validate(['order' => ['nullable', 'integer', 'exists:orders,id']]);
        $order = $request->filled('order') ? Order::findOrFail($request->integer('order')) : null;
        return $this->document($order, 'normal', true);
    }

    public function show(Request $request, Order $order)
    {
        $request->validate(['copy' => ['nullable', 'in:normal,customer,store']]);
        return $this->document($order, $request->input('copy') ?: 'normal', $request->boolean('preview'));
    }

    protected function document(?Order $order, string $copy, bool $preview)
    {
        $template = InvoiceTemplate::first();

        return response()->view('invoice-template.document', [
            'payload' => [
                'settings' => InvoiceTemplateSchema::settings($template),
                'data' => PosInvoiceData::make($order),
                'logo' => $this->logo($template),
                'copy' => $copy,
                'preview' => $preview,
            ],
        ])->header('Cache-Control', 'private, no-store')->header('X-Frame-Options', 'SAMEORIGIN');
    }
}

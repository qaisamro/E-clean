<?php

namespace App\Support;

use App\Models\InvoiceTemplate as InvoiceTemplateModel;
use Illuminate\Validation\Rule;

/** The schema is shared by validation, the editor and the print renderer. */
class InvoiceTemplate
{
    public const SECTIONS = ['brand', 'header', 'details', 'items', 'totals', 'footer'];

    public static function fields(): array
    {
        return [
            'logo' => 'شعار المحل', 'store_name' => 'اسم الشركة / المحل', 'address' => 'عنوان المحل',
            'phone' => 'هاتف المحل', 'email' => 'البريد الإلكتروني', 'tax_number' => 'الرقم الضريبي',
            'contact' => 'معلومات التواصل الإضافية', 'invoice_number' => 'رقم الفاتورة (رقم الطلب الحالي)',
            'order_number' => 'رقم الطلب', 'date' => 'التاريخ', 'time' => 'الوقت',
            'customer_name' => 'اسم العميل', 'customer_phone' => 'هاتف العميل', 'cashier' => 'الاسم (منشئ الطلب)',
            'item_name' => 'اسم المنتج / الخدمة', 'quantity' => 'الكمية', 'unit_price' => 'سعر الوحدة',
            'item_discount' => 'خصم الصنف إن توفر', 'line_total' => 'إجمالي الصنف',
            'subtotal' => 'المجموع الفرعي', 'discount' => 'خصم الطلب', 'tax' => 'الضريبة المحفوظة إن توفرت',
            'delivery' => 'رسوم التوصيل', 'total' => 'الإجمالي النهائي', 'payment_method' => 'طريقة الدفع',
            'payment_status' => 'حالة الدفع', 'order_notes' => 'ملاحظات الطلب', 'barcode' => 'باركود رقم الطلب',
        ];
    }

    public static function texts(): array
    {
        return ['store_name' => 'اسم المحل المعروض (فارغ لاستخدام اسم المتجر)',
            'address' => 'العنوان المعروض', 'phone' => 'الهاتف المعروض', 'email' => 'البريد المعروض',
            'tax_number' => 'الرقم الضريبي', 'contact' => 'معلومات تواصل إضافية',
            'title' => 'عنوان الفاتورة', 'header' => 'نص أعلى الفاتورة',
            'footer' => 'نص أسفل الفاتورة', 'thanks' => 'رسالة الشكر', 'notes' => 'ملاحظات ثابتة'];
    }

    public static function defaults(): array
    {
        return [
            'paper' => '80mm', 'language' => 'ar', 'direction' => 'rtl', 'font' => 'system',
            'font_size' => 12, 'heading_size' => 20, 'spacing' => 10, 'padding' => 5,
            'border_width' => 1, 'align' => 'start', 'logo_align' => 'center', 'logo_size' => 70,
            'accent' => '#0f766e', 'text_color' => '#172b4d', 'background' => '#ffffff',
            'border_color' => '#cbd5e1', 'sections' => self::SECTIONS,
            'visible' => array_fill_keys(array_keys(self::fields()), true),
            'text' => array_replace(array_fill_keys(array_keys(self::texts()), ''), [
                'store_name' => 'شركة النخبة للتنظيف',
                'title' => 'فاتورة مبيعات', 'thanks' => 'شكرًا لزيارتكم',
            ]),
        ];
    }

    public static function settings(?InvoiceTemplateModel $template): array
    {
        return array_replace_recursive(self::defaults(), $template?->settings ?? []);
    }

    public static function rules(): array
    {
        $defaults = self::defaults();
        $rules = ['settings' => ['required', 'array:'.implode(',', array_keys($defaults))]];
        foreach (['paper' => ['A4', '80mm', '58mm'], 'language' => ['ar', 'en'], 'direction' => ['rtl', 'ltr'],
            'font' => ['system', 'serif', 'mono'], 'align' => ['start', 'center', 'end'],
            'logo_align' => ['start', 'center', 'end']] as $field => $values) {
            $rules['settings.'.$field] = ['required', Rule::in($values)];
        }
        foreach (['font_size' => [9, 24], 'heading_size' => [12, 36], 'spacing' => [0, 24],
            'padding' => [2, 15], 'border_width' => [0, 3], 'logo_size' => [24, 160]] as $key => [$min, $max]) {
            $rules['settings.'.$key] = ['required', 'integer', "between:$min,$max"];
        }
        foreach (['accent', 'text_color', 'background', 'border_color'] as $key) {
            $rules['settings.'.$key] = ['required', 'regex:/^#[0-9a-fA-F]{6}$/'];
        }
        $rules['settings.sections'] = ['required', 'array', 'size:6'];
        $rules['settings.sections.*'] = ['required', 'distinct', Rule::in(self::SECTIONS)];
        $rules['settings.visible'] = ['required', 'array:'.implode(',', array_keys(self::fields()))];
        foreach (self::fields() as $key => $label) {
            $rules['settings.visible.'.$key] = ['required', 'boolean'];
        }
        $rules['settings.text'] = ['required', 'array:'.implode(',', array_keys(self::texts()))];
        foreach (self::texts() as $key => $label) {
            // Plain text only; it is also escaped by the DOM renderer and Blade JSON encoder.
            $rules['settings.text.'.$key] = ['nullable', 'string', 'max:1000', 'not_regex:/[<>]/u'];
        }
        return $rules;
    }
}

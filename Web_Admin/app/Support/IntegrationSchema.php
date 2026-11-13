<?php

namespace App\Support;

class IntegrationSchema
{
    /**
     * Central definition for every setting exposed by the integration center.
     * Keeping this in one place prevents arbitrary keys from being written and
     * lets the UI render consistent labels, types and secret handling.
     */
    public static function groups(): array
    {
        return [
            'sms' => [
                'label' => 'SMS',
                'icon' => 'fa-sms',
                'description' => 'مزود الرسائل النصية وقوالب OTP.',
                'setup_notes' => [
                    'أدخل بيانات المزود كما هي في لوحة المزود (API URL واسم المستخدم وكلمة المرور وSender ID).',
                    'بعض المزودين يطلبون تسجيل عنوان السيرفر أو نطاق الموقع في قائمة IP المسموحة.',
                    'تجربة SMS ترسل رسالة فعلية إلى الرقم الذي تدخله. استخدم رقم اختبار مملوك لك.',
                ],
                'fields' => [
                    'enabled' => self::bool('تفعيل SMS', '1'),
                    'provider' => self::select('المزود', ['generic_http' => 'Generic HTTP GET', 'log' => 'Log only (اختبار)'], 'generic_http'),
                    'api_url' => self::text('API URL', '', ['url']),
                    'user_name' => self::text('اسم المستخدم'),
                    'password' => self::secret('كلمة المرور'),
                    'sender_id' => self::text('Sender ID / Originator'),
                    'route' => self::text('Route'),
                    'country_code' => self::text('Country Code', '44'),
                    'timeout' => self::number('مهلة الاتصال (ثانية)', '15'),
                ],
            ],
            'otp' => [
                'label' => 'OTP & Authentication',
                'icon' => 'fa-key',
                'description' => 'طول الرمز، الصلاحية، المحاولات، القنوات والحدود.',
                'setup_notes' => [
                    'يمكن تفعيل أو تعطيل OTP لكل عملية على حدة: التسجيل، الدخول، استعادة كلمة المرور، وتغيير الهاتف.',
                    'قنوات OTP تعتمد على الخدمة نفسها: Email يحتاج SMTP مهيأ، SMS يحتاج مزود SMS، وWhatsApp يحتاج WhatsApp Cloud API.',
                    'إظهار OTP داخل التطبيق يعمل فقط في local/staging/testing ولحسابات الاختبار المذكورة صراحةً، ولا يعمل في الإنتاج.',
                    'تغيير طول الرمز أو صلاحيته يؤثر على الأكواد الجديدة فقط، وليس على الأكواد الصادرة سابقًا.',
                ],
                'fields' => [
                    'enabled' => self::bool('تفعيل OTP', '1'),
                    'enabled_registration' => self::bool('تفعيل OTP للتسجيل', '1'),
                    'enabled_login' => self::bool('تفعيل OTP لتسجيل الدخول', '1'),
                    'enabled_password_reset' => self::bool('تفعيل OTP لاستعادة كلمة المرور', '1'),
                    'enabled_phone_change' => self::bool('تفعيل OTP لتغيير الهاتف', '1'),
                    'length' => self::select('طول الرمز', ['4' => '4 أرقام', '6' => '6 أرقام', '8' => '8 أرقام'], '6'),
                    'ttl' => self::number('مدة الصلاحية (ثانية)', '300', ['min:30', 'max:3600']),
                    'max_attempts' => self::number('أقصى محاولات تحقق', '5', ['min:1', 'max:20']),
                    'resend_after' => self::number('انتظار إعادة الإرسال (ثانية)', '60', ['min:0', 'max:3600']),
                    'rate_limit_window' => self::number('نافذة الحدود (ثانية)', '3600', ['min:60']),
                    'send_limit' => self::number('حد الإرسال لكل جهة اتصال', '5', ['min:1']),
                    'resend_limit' => self::number('حد إعادة الإرسال', '3', ['min:1']),
                    'verify_limit' => self::number('حد محاولات التحقق لكل نافذة', '10', ['min:1']),
                    'ip_limit' => self::number('حد الطلبات لكل IP', '20', ['min:1']),
                    'device_limit' => self::number('حد الطلبات لكل جهاز', '10', ['min:1']),
                    'lockout_seconds' => self::number('مدة الحظر المؤقت (ثانية)', '900', ['min:0']),
                    'show_in_app' => self::bool('إظهار OTP داخل التطبيق (بيئة الاختبار فقط)', '0'),
                    'test_contacts' => self::text('حسابات الاختبار (بريد/هاتف مفصولة بفاصلة)', ''),
                    'channels_registration' => self::multiselect('قنوات التسجيل', self::channelOptions(), 'email'),
                    'channels_login' => self::multiselect('قنوات الدخول', self::channelOptions(), 'email'),
                    'channels_password_reset' => self::multiselect('قنوات استعادة كلمة المرور', self::channelOptions(), 'email'),
                    'channels_phone_change' => self::multiselect('قنوات تغيير الهاتف', self::channelOptions(), 'sms'),
                ],
            ],
            'firebase' => [
                'label' => 'Firebase / FCM',
                'icon' => 'fa-bell',
                'description' => 'إشعارات Push عبر Firebase Cloud Messaging.',
                'setup_notes' => [
                    'أنشئ Service Account من Firebase Console ← Project Settings ← Service Accounts ثم الصق JSON كاملًا هنا.',
                    'تطبيق الهاتف يجب أن يُبنى بنفس Firebase Project (google-services.json لأندرويد وGoogleService-Info.plist لـiOS). هذه خطوة بناء خارجية وليست إعدادًا من اللوحة.',
                    'اختبار الاتصال الافتراضي يستخدم OAuth وFCM validate_only ولا يرسل إشعارًا. إرسال إشعار فعلي يتطلب Device Token وتحديد خيار الإرسال.',
                    'إذا كان ملف الاعتماد موجودًا داخل public/firebase_credentials.json فانقله إلى storage/app/private/firebase/credentials.json وادوّر المفتاح لأنه يُعتبر مكشوفًا.',
                ],
                'fields' => [
                    'enabled' => self::bool('تفعيل Push', '1'),
                    'project_id' => self::text('Project ID'),
                    'credentials_json' => self::textarea('Service Account JSON (سري)', '', true),
                    'legacy_server_key' => self::secret('Legacy Server Key (انتقالي)'),
                    'default_channel_id' => self::text('Android Channel ID', 'high_importance_channel'),
                    'default_sound' => self::text('الصوت', 'default'),
                ],
            ],
            'google_maps' => [
                'label' => 'Google Maps',
                'icon' => 'fa-map-marked-alt',
                'description' => 'مفاتيح الخرائط والخدمات المستخدمة.',
                'setup_notes' => [
                    'فعّل Geocoding API وPlaces API وDirections API من Google Cloud Console، وتأكد من ربط الفوترة بالمشروع.',
                    'Server API Key يُستخدم في الخادم ويجب تقييده بعنوان IP السيرفر. لا تستخدم مفتاح الخادم في تطبيق الهاتف.',
                    'Web/Android/iOS API Keys تُضمَّن عند بناء التطبيق أو الموقع. أي تغيير لها يحتاج إعادة بناء/نشر خارج لوحة الإدارة.',
                    'إذا فشل Google يمكن تفعيل الرجوع إلى OpenStreetMap، لكن ذلك لا يختبر صحة مفتاح Google.',
                ],
                'fields' => [
                    'enabled' => self::bool('تفعيل Google Maps', '0'),
                    'server_api_key' => self::secret('Server API Key (Geocoding)'),
                    'web_api_key' => self::secret('Web API Key'),
                    'android_api_key' => self::secret('Android API Key'),
                    'ios_api_key' => self::secret('iOS API Key'),
                    'reverse_geocoding_enabled' => self::bool('تفعيل Reverse Geocoding', '1'),
                    'places_enabled' => self::bool('تفعيل Places', '1'),
                    'directions_enabled' => self::bool('تفعيل Directions', '1'),
                    'fallback_osm' => self::bool('الرجوع إلى OpenStreetMap عند فشل Google', '1'),
                ],
            ],
            'smtp' => [
                'label' => 'SMTP / Email',
                'icon' => 'fa-envelope',
                'description' => 'بريد Laravel وقوالب البريد.',
                'setup_notes' => [
                    'أدخل بيانات SMTP من مزود البريد (Host وPort وUsername وPassword وEncryption).',
                    'اختبار الاتصال الافتراضي يتحقق من الاتصال وTLS والمصادقة دون إرسال بريد. أدخل بريدًا فقط إذا أردت اختبار إرسال فعلي.',
                    'يجب إعداد SPF وDKIM وDMARC في DNS لدى مزود النطاق لتحسين التسليم. هذه خطوة خارجية.',
                    'From Address يجب أن يكون عنوانًا مسموحًا به من مزود البريد، وإلا سيرفض المزود الإرسال.',
                ],
                'fields' => [
                    'enabled' => self::bool('تفعيل SMTP من المركز', '0'),
                    'host' => self::text('SMTP Host'),
                    'port' => self::number('Port', '587', ['min:1', 'max:65535']),
                    'username' => self::text('Username'),
                    'password' => self::secret('Password'),
                    'encryption' => self::select('Encryption', ['tls' => 'TLS', 'ssl' => 'SSL', 'none' => 'بدون'], 'tls'),
                    'from_address' => self::text('From Address', '', ['email']),
                    'from_name' => self::text('From Name', config('app.name')),
                    'timeout' => self::number('Timeout (ثانية)', '15', ['min:1']),
                ],
            ],
            'whatsapp' => [
                'label' => 'WhatsApp Business',
                'icon' => 'fa-comment-dots',
                'description' => 'WhatsApp Cloud API الرسمي، القوالب وWebhooks.',
                'setup_notes' => [
                    'أنشئ تطبيق WhatsApp Cloud API في Meta for Developers واحصل على Phone Number ID وAccess Token دائم.',
                    'أضف Webhook URL في لوحة Meta: /api/webhooks/whatsapp مع Verify Token نفسه المحفوظ هنا، واشترك في حدث messages.',
                    'اختبار الاتصال الافتراضي يقرأ بيانات رقم الأعمال عبر GET ولا يرسل رسالة. إدخال رقم مستلم يفعّل إرسال رسالة اختبار فعلية.',
                    'قوالب WhatsApp تحتاج اعتمادًا مسبقًا من Meta قبل استخدامها في الإنتاج. إنشاء القالب واعتماده يتم خارج لوحة الإدارة.',
                ],
                'fields' => [
                    'enabled' => self::bool('تفعيل WhatsApp', '0'),
                    'api_version' => self::text('Graph API Version', 'v20.0'),
                    'access_token' => self::secret('Access Token'),
                    'phone_number_id' => self::text('Phone Number ID'),
                    'business_account_id' => self::text('Business Account ID'),
                    'app_secret' => self::secret('App Secret (للتحقق من Webhook)'),
                    'webhook_verify_token' => self::secret('Webhook Verify Token'),
                    'webhook_enabled' => self::bool('تفعيل استقبال Webhooks', '1'),
                ],
            ],
            'api' => [
                'label' => 'API Keys',
                'icon' => 'fa-key',
                'description' => 'مفاتيح التكامل، الصلاحيات والحدود.',
                'setup_notes' => [
                    'أنشئ المفاتيح من صفحة API Keys داخل المركز. يُعرض المفتاح مرة واحدة فقط عند الإنشاء.',
                    'المفتاح يُخزَّن كـHash، ولا يمكن استرجاعه لاحقًا. إذا فُقد، أبطله وأنشئ مفتاحًا جديدًا.',
                    'طبّق حدود الطلبات وIdempotency على العميل الذي يستخدم المفتاح.',
                ],
                'fields' => [
                    'enabled' => self::bool('تفعيل API Keys', '1'),
                    'rate_limit_per_minute' => self::number('حد الطلبات لكل مفتاح (دقيقة)', '60', ['min:1']),
                    'idempotency_ttl' => self::number('مدة Idempotency (ثانية)', '86400', ['min:60']),
                ],
            ],
            'webhooks' => [
                'label' => 'Webhooks',
                'icon' => 'fa-project-diagram',
                'description' => 'توقيع الطلبات، إعادة المحاولة وIdempotency.',
                'setup_notes' => [
                    'أنشئ Endpoint من صفحة Webhooks داخل المركز، وحدد الأحداث المطلوبة.',
                    'يجب أن يكون عنوان Endpoint متاحًا عبر HTTPS ويستقبل POST. التوقيع يُرسل في Header ويجب التحقق منه في الطرف المستلم.',
                    'إذا كان Endpoint خلف جدار حماية، اسمح بعناوين السيرفر أو استخدم عنوانًا عامًا.',
                ],
                'fields' => [
                    'enabled' => self::bool('تفعيل Webhooks', '1'),
                    'max_attempts' => self::number('أقصى محاولات', '5', ['min:1', 'max:10']),
                    'timeout' => self::number('Timeout (ثانية)', '10', ['min:1', 'max:60']),
                    'signature_ttl' => self::number('صلاحية التوقيع (ثانية)', '300', ['min:30', 'max:900']),
                ],
            ],
            'security' => [
                'label' => 'Security & Audit',
                'icon' => 'fa-lock',
                'description' => 'إعادة المصادقة، سجل التدقيق وحماية الأسرار.',
                'setup_notes' => [
                    'إعادة المصادقة تطلب كلمة مرور المدير الحالي عند إدخال أو تغيير أي سر.',
                    'سجل التدقيق لا يحتوي قيم الأسرار، ويُستخدم لتتبع من غيّر الإعداد ومتى.',
                    'لا تغيّر APP_KEY بعد حفظ أسرار مشفرة، لأن ذلك يجعلها غير قابلة للفك. احتفظ بنسخة آمنة من APP_KEY.',
                ],
                'fields' => [
                    'reauth_required' => self::bool('طلب كلمة مرور عند تغيير الأسرار', '1'),
                    'audit_enabled' => self::bool('تفعيل سجل التدقيق', '1'),
                    'audit_retention_days' => self::number('الاحتفاظ بسجل التدقيق (يوم)', '365', ['min:30']),
                    'mask_secrets' => self::bool('إخفاء الأسرار في الواجهات', '1'),
                ],
            ],
            'customer_registration' => [
                'label' => 'طلبات انضمام العملاء',
                'icon' => 'fa-user-plus',
                'description' => 'إدارة طلبات انضمام العملاء الجدد وموافقة المشرف.',
                'setup_notes' => [
                    'عند التفعيل، يتم إنشاء حساب العميل الجديد بحالة "بانتظار الموافقة" ولا يمكنه استخدام التطبيق حتى يوافق المشرف.',
                    'عند التعطيل، يعود التسجيل إلى السلوك الطبيعي (الحساب مفعل فورًا).',
                    'لا تتأثر الحسابات الحالية المسجلة بالفعل.',
                    'رسائل الحالة يمكن تخصيصها لكل حالة: pending و approved و rejected.',
                ],
                'fields' => [
                    'enabled' => self::bool('تفعيل نظام طلبات الانضمام', '0'),
                    'approval_required' => self::bool('اشتراط موافقة الإدارة على الحسابات الجديدة', '0'),
                    'pending_message' => self::text('رسالة الطلب المعلق', 'تم إنشاء حسابك بنجاح، وطلبك قيد المراجعة. يرجى انتظار موافقة الإدارة لتفعيل حسابك.'),
                    'approved_message' => self::text('رسالة الموافقة', 'تم قبول طلبك، يمكنك الآن استخدام تطبيق Elite Clean.'),
                    'rejected_message' => self::text('رسالة الرفض', 'للأسف، تم رفض طلب انضمامك. يمكنك التواصل مع الدعم للمزيد من التفاصيل.'),
                ],
            ],
        ];
    }

    public static function group(string $group): ?array
    {
        return self::groups()[$group] ?? null;
    }

    public static function fieldKeys(string $group): array
    {
        return array_keys(self::groups()[$group]['fields'] ?? []);
    }

    public static function defaults(string $group): array
    {
        $defaults = [];
        foreach (self::groups()[$group]['fields'] ?? [] as $key => $field) {
            $defaults[$key] = $field['default'] ?? null;
        }
        return $defaults;
    }

    public static function notes(string $group): array
    {
        return self::groups()[$group]['setup_notes'] ?? [];
    }

    public static function isSecretField(string $group, string $field): bool
    {
        return (bool) (self::groups()[$group]['fields'][$field]['secret'] ?? false);
    }

    public static function channelOptions(): array
    {
        return [
            'email' => 'Email',
            'sms' => 'SMS',
            'whatsapp' => 'WhatsApp',
        ];
    }

    private static function text(string $label, string $default = '', array $rules = []): array
    {
        return ['label' => $label, 'type' => 'text', 'default' => $default, 'rules' => $rules];
    }

    private static function textarea(string $label, string $default = '', bool $secret = false): array
    {
        return ['label' => $label, 'type' => 'textarea', 'default' => $default, 'secret' => $secret];
    }

    private static function number(string $label, string $default = '', array $rules = []): array
    {
        return ['label' => $label, 'type' => 'number', 'default' => $default, 'rules' => array_merge(['numeric'], $rules)];
    }

    private static function bool(string $label, string $default = '0'): array
    {
        return ['label' => $label, 'type' => 'bool', 'default' => $default];
    }

    private static function select(string $label, array $options, string $default = ''): array
    {
        return ['label' => $label, 'type' => 'select', 'options' => $options, 'default' => $default];
    }

    private static function multiselect(string $label, array $options, string $default = ''): array
    {
        return ['label' => $label, 'type' => 'multiselect', 'options' => $options, 'default' => $default];
    }

    private static function secret(string $label): array
    {
        return ['label' => $label, 'type' => 'password', 'default' => '', 'secret' => true];
    }
}

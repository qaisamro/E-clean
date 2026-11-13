<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $rows = [];

        $smsBodies = [
            'otp_registration' => 'Elite Clean: رمز التحقق {{code}}. صالح لمدة {{minutes}} دقائق. لا تشاركه مع أحد.',
            'otp_login' => 'Elite Clean: رمز الدخول {{code}}. صالح لمدة {{minutes}} دقائق.',
            'otp_password_reset' => 'Elite Clean: رمز استعادة كلمة المرور {{code}}. صالح لمدة {{minutes}} دقائق.',
            'otp_phone_change' => 'Elite Clean: رمز تأكيد رقم الهاتف {{code}}. صالح لمدة {{minutes}} دقائق.',
            'test' => 'Elite Clean: رسالة اختبار في {{time}}.',
        ];
        foreach ($smsBodies as $key => $body) {
            $rows[] = $this->row('sms', $key, $this->name($key), null, $body, $now);
        }

        $emailBodies = [
            'otp_registration' => 'مرحبًا {{name}}،<br><br>رمز التحقق الخاص بك هو: <strong>{{code}}</strong><br>صالح لمدة {{minutes}} دقائق.<br><br>إذا لم تطلب هذا الرمز فتجاهل الرسالة.',
            'otp_login' => 'مرحبًا {{name}}،<br><br>رمز الدخول الخاص بك هو: <strong>{{code}}</strong><br>صالح لمدة {{minutes}} دقائق.',
            'otp_password_reset' => 'مرحبًا {{name}}،<br><br>رمز استعادة كلمة المرور هو: <strong>{{code}}</strong><br>صالح لمدة {{minutes}} دقائق.',
            'otp_phone_change' => 'مرحبًا {{name}}،<br><br>رمز تأكيد رقم الهاتف الجديد هو: <strong>{{code}}</strong><br>صالح لمدة {{minutes}} دقائق.',
            'test' => 'هذه رسالة اختبار من Elite Clean في {{time}}.',
        ];
        foreach ($emailBodies as $key => $body) {
            $rows[] = $this->row('email', $key, $this->name($key), 'Elite Clean — رمز التحقق', $body, $now);
        }

        $whatsappBodies = $smsBodies;
        foreach ($whatsappBodies as $key => $body) {
            $rows[] = $this->row('whatsapp', $key, $this->name($key), null, $body, $now);
        }

        $rows[] = $this->row('push', 'test', 'Push test', 'Elite Clean', 'إشعار اختبار من Elite Clean', $now);

        DB::table('notification_templates')->insertOrIgnore($rows);
    }

    public function down(): void
    {
        DB::table('notification_templates')->whereIn('key', [
            'otp_registration', 'otp_login', 'otp_password_reset', 'otp_phone_change', 'test',
        ])->delete();
    }

    private function row(string $channel, string $key, string $name, ?string $subject, string $body, $now): array
    {
        return [
            'channel' => $channel,
            'key' => $key,
            'name' => $name,
            'subject' => $subject,
            'body' => $body,
            'provider_template_name' => null,
            'language' => 'ar',
            'variables' => json_encode(['code', 'name', 'minutes', 'app', 'time'], JSON_UNESCAPED_UNICODE),
            'active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    private function name(string $key): string
    {
        return match ($key) {
            'otp_registration' => 'OTP — التسجيل',
            'otp_login' => 'OTP — الدخول',
            'otp_password_reset' => 'OTP — استعادة كلمة المرور',
            'otp_phone_change' => 'OTP — تغيير الهاتف',
            'test' => 'رسالة اختبار',
            default => $key,
        };
    }
};

# نشر Backend مواعيد الاستلام والتوصيل

التاريخ: 2026-11-09، الإنتاج: `elitecleanps.com`.

## النتيجة

تم نشر Laravel Backend وتشغيل Migration المواعيد المحددة وحدها بنجاح. لم يُنشر Flutter، ولم تُفعّل جداول المواعيد لأي متجر. لذلك يستمر التطبيق المنشور باستخدام الجدول العام الحالي حتى نشر Flutter ثم تفعيل المتاجر تدريجيًا.

- بيئة الإنتاج: Laravel 13.40.0، PHP CLI 8.4.27، OPcache فعال، Debug معطل.
- Migration: `2026_11_09_000001_create_vendor_appointment_tables`، Batch 16، الحالة `Ran`.
- الجداول الجديدة: `vendor_appointment_settings` و`order_appointments`.
- بعد النشر: صفر إعدادات متاجر وصفر حجوزات في الجدولين الجديدين.
- لم تُشغّل Migrations جماعية.
- لم يتغير وضع الصيانة؛ بقي الموقع متاحًا.

## المقارنة والملفات المنشورة

جُلبت الملفات الـ9 المشتركة من الإنتاج قبل النشر وقورنت بالنسخة المحلية. كانت كل الفروق محصورة في ميزة المواعيد، ولم تظهر تعديلات إنتاج أحدث تحتاج دمجًا منفصلًا.

الملفات الجديدة:

```text
app/Services/VendorAppointments.php
app/Models/VendorAppointmentSetting.php
app/Models/OrderAppointment.php
app/Http/Controllers/API/VendorAppointmentController.php
app/Http/Controllers/Web/VendorAppointmentController.php
resources/views/vendor/appointments/edit.blade.php
public/web/js/vendor-appointments.js
database/migrations/2026_11_09_000001_create_vendor_appointment_tables.php
```

الملفات المشتركة المحدثة:

```text
app/Models/Order.php
app/Http/Resources/OrderResource.php
app/Repositories/OrderRepository.php
app/Repositories/CheckoutRepository.php
app/Http/Controllers/API/Order/OrderController.php
app/Http/Middleware/CheckPermission.php
routes/api.php
routes/web.php
resources/views/layouts/partials/sidebar.blade.php
```

المجموع: 17 ملفًا. لم تُرفع اختبارات أو Fixtures أو ملفات Flutter أو أدوات النشر إلى تطبيق الإنتاج.

## النسخة الاحتياطية

المسار الخاص خارج مجلد الويب:

```text
/home/u205641829/backups/appointments-backend-20261109T025242Z
```

تحتوي النسخة على الملفات الأصلية، نسخة قاعدة بيانات كاملة، manifest، بصمات الملفات والجداول، حالة cache، وبيانات التحقق قبل وبعد النشر.

- حجم SQL: `270851` بايت.
- SHA-256: `862fd2ceda6878b82f51f4d521af4074358aa63ce50b92b72b60891d3ea8264d`.
- جداول الإنتاج الأساسية: 74.
- أوامر `CREATE TABLE` في النسخة: 74.
- علامة إكمال mysqldump موجودة.
- البصمة متطابقة.

## التحقق بعد النشر

- تم مسح Laravel config/routes/views cache بشكل محدد، دون مسح sessions أو OTP أو rate-limit cache.
- تم إبطال OPcache لـ16 ملف PHP؛ لا توجد ملفات فشل.
- `GET /api/vendors` أعاد نفس status ونفس JSON قبل وبعد النشر.
- `GET /api/pick-schedules/2026-11-11` أعاد نفس status ونفس JSON قبل وبعد النشر.
- `GET /api/delivery-schedules/2026-11-11` أعاد نفس status ونفس JSON قبل وبعد النشر.
- `GET /api/vendors/6/appointments` أعاد HTTP 200، `enabled=false` و`timezone=Asia/Hebron`.
- بصمات 13 مجموعة بيانات حساسة متطابقة: vendors، products، addresses، delivery_costs، web_settings، users، roles، permissions، role/model permissions، oauth_clients، customer_join_requests.
- لم تتغير الملفات المحمية خارج manifest، بما فيها `.env` وComposer وبقية كود التطبيق.
- لم تُسجل أخطاء Laravel جديدة خلال النشر والتحقق.
- حُذفت ملفات `/tmp` الخاصة بالنشر، وبقيت النسخة الاحتياطية.

لا ينشئ فحص الإنتاج طلبات تجريبية، لذلك لم تُجر تجربة `POST /api/orders` على بيانات العملاء. سلامة الحجز والتزامن مغطاة محليًا باختبارات Laravel وباختبار MariaDB متزامن موثق في التقرير التطويري.

## التراجع

أداة التراجع المحفوظة داخل النسخة الاحتياطية تعيد الملفات الأصلية. إذا كان الجدولان الجديدان ما زالا فارغين، يمكنها كذلك تشغيل rollback لهذه Migration وحذف الجدولين. إذا أضيفت إعدادات أو حجوزات لاحقًا، تترك الجدولين وبياناتهما وتحذف كود الميزة فقط، بدل إسقاط بيانات حقيقية.

لا تُستورد نسخة SQL كاملة للتراجع الروتيني؛ ذلك قد يحذف طلبات أو تحديثات أُنشئت بعد وقت النسخة. يُستخدم SQL فقط لاستعادة كارثية مدروسة.

## الخطوة التالية

Backend جاهز لاستقبال Flutter الجديد، لكن كل المتاجر ما زالت غير مفعلة. التسلسل الآمن المتبقي:

1. اختبار APK الجديد على هاتف مع API الإنتاج دون إنشاء طلب غير مقصود.
2. نشر Flutter.
3. تفعيل جدول متجر تجريبي من لوحة صاحب المتجر واختبار حجز حقيقي.
4. تفعيل بقية المتاجر تدريجيًا.


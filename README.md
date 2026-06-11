# School App

نظام مدرسي متكامل لإدارة تجربة الطالب والعمليات الإدارية للمدرسة. يتكون المشروع من واجهة Flutter للطلاب والإدارة، وواجهة خلفية Laravel تقدم REST API مع صلاحيات متعددة للأدمن والموجهين والطلاب.

## Project Structure

```text
School-app/
├── back-end/      # Laravel API backend
├── froont-end/    # Flutter mobile/web frontend
├── TESTING.md     # Testing guide
└── README.md
```

## Key Features

- تسجيل دخول الطلاب، الأدمن، والموجهين باستخدام token authentication.
- لوحة طالب لعرض:
  - العلامات
  - الدفعات والملف المالي
  - الإعلانات
  - الجداول الامتحانية
  - الجدول الأسبوعي
  - النقل والاشتراك بالخدمة
  - الإشعارات
- لوحة إدارة كاملة للأدمن تشمل:
  - إدارة الطلاب
  - إدارة العلامات
  - إدارة الدفعات
  - إدارة خطوط واشتراكات النقل
  - إدارة الجداول الامتحانية
  - إدارة الجدول الأسبوعي
  - إدارة الإعلانات
- صلاحيات الموجهين حسب المرحلة والجنس:
  - ابتدائي ذكور
  - ابتدائي إناث
  - إعدادي ذكور
  - إعدادي إناث
  - ثانوي ذكور
  - ثانوي إناث
- كل موجه يرى ويدير فقط الطلاب ضمن نطاقه.
- إدارة الإعلانات محصورة بالأدمن فقط.
- دعم العربية والإنكليزية مع مراعاة RTL/LTR.
- اختبارات Backend شاملة لسيناريوهات الطالب، الصلاحيات، الإدارة، والأداء.
- اختبارات Flutter للموديلات والواجهة الأساسية.

## Tech Stack

### Back-end

- PHP
- Laravel
- Laravel Sanctum
- Spatie Laravel Permission
- MySQL
- PHPUnit
- Vite / Tailwind tooling

### Froont-end

- Flutter
- Dart
- Dio
- HTTP
- Firebase Core
- Firebase Messaging
- Flutter Local Notifications
- Easy Localization
- Secure Storage
- Shared Preferences

## Testing

### Backend

```powershell
cd back-end
php artisan test
```

آخر نتيجة تحقق:

```text
25 passed
119 assertions
```

### Froont-end

```powershell
cd froont-end
flutter analyze
flutter test
```

## Roles and Permissions

| Role | Scope | Permissions |
|---|---|---|
| `admin` | كل المدرسة | صلاحيات إدارة كاملة |
| `primary_male_supervisor` | ابتدائي ذكور | إدارة بيانات طلاب النطاق فقط |
| `primary_female_supervisor` | ابتدائي إناث | إدارة بيانات طالبات النطاق فقط |
| `secondary_male_supervisor` | إعدادي ذكور | إدارة بيانات طلاب النطاق فقط |
| `secondary_female_supervisor` | إعدادي إناث | إدارة بيانات طالبات النطاق فقط |
| `thirdy_male_supervisor` | ثانوي ذكور | إدارة بيانات طلاب النطاق فقط |
| `thirdy_female_supervisor` | ثانوي إناث | إدارة بيانات طالبات النطاق فقط |
| `student` | حساب الطالب | عرض بياناته وخدماته فقط |

## Backend Setup

```powershell
cd back-end
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Froont-end Setup

```powershell
cd froont-end
flutter pub get
flutter run
```

## Environment Files

لا يتم رفع ملفات البيئة أو المفاتيح الخاصة إلى GitHub. استخدم ملفات `.env.example` أو أنشئ ملفات البيئة محليا حسب الحاجة.

## Security Notes

- ملفات `.env`، مجلدات dependencies، ملفات build، وملفات Firebase الحساسة مستثناة من Git.
- صلاحيات الإعلانات محصورة بالأدمن فقط.
- صلاحيات الموجهين محددة حسب المرحلة والجنس، ولا تعطيهم وصولا خارج نطاق الطلاب المسؤولين عنهم.

## Repository

GitHub: [randaboshamleh/School-app](https://github.com/randaboshamleh/School-app)

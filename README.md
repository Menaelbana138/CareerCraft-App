<div align="center">

# 🚀 CareerCraft — Backend API

**AI-powered career path & skill gap analysis platform**

![Laravel](https://img.shields.io/badge/Laravel-%5E9.19-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

</div>

---

## 📌 عن المشروع

CareerCraft هو باك إند REST API مبني بـ **Laravel ^9.19 + Sanctum** لتطبيق موبايل Flutter.
يوفر المنصة تحليل مسارات مهنية، كشف فجوات المهارات، ومطابقة الوظائف بالذكاء الاصطناعي.

---

## ⚙️ المتطلبات

| الأداة | الإصدار |
|--------|---------|
| PHP | 8.0+ |
| Composer | Latest |
| MySQL | 5.7+ |
| Redis *(اختياري)* | للـ Queue في Production |

---

## 🛠️ التثبيت والتشغيل

```bash
# 1. تثبيت الباكدجات
composer install

# 2. إنشاء ملف البيئة
cp .env.example .env

# 3. توليد مفتاح التطبيق
php artisan key:generate

# 4. إعداد قاعدة البيانات في .env ثم:
php artisan migrate

# 5. ربط Storage
php artisan storage:link

# 6. تشغيل السيرفر
php artisan serve
```

---

## 🚀 Production URLs

- **Backend API:** https://careercraft-app-production.up.railway.app
- **AI Service:** https://careercraft-ai-service-production.up.railway.app

## 🗄️ Database

- **Provider:** Aiven MySQL
- **Host:** mysql-12189bfc-mena94502-75c9.h.aivencloud.com
- **Port:** 23321
- **Database:** defaultdb

> **ملاحظة:** لا تُرفع اسم المستخدم أو كلمة المرور في الـ README؛ استخدم متغيرات البيئة أو Railway Secrets فقط.

---

## 🚀 Production Checklist

| المهمة | الأمر |
|--------|-------|
| تشغيل Migrations | `php artisan migrate --force` |
| ربط Storage | `php artisan storage:link` |
| Cache الإعدادات | `php artisan config:cache` |
| Cache الـ Routes | `php artisan route:cache` |
| تشغيل Queue Worker | `php artisan queue:work --tries=3` |
| إضافة Scheduler | `* * * * * cd /path && php artisan schedule:run` |

---

## 📡 API Endpoints

### 🔓 Auth (Public)

| Method | Endpoint | الوصف |
|--------|----------|-------|
| POST | `/api/auth/register` | تسجيل مستخدم أو شركة |
| POST | `/api/auth/login` | تسجيل الدخول |
| POST | `/api/auth/login/google` | دخول بـ Google OAuth |
| POST | `/api/auth/forgot-password` | إرسال إيميل إعادة تعيين كلمة السر |
| POST | `/api/auth/reset-password` | إعادة تعيين كلمة السر |
| GET | `/api/auth/verify-email/{id}/{hash}` | تأكيد الإيميل |

### 🔐 Auth (Protected)

| Method | Endpoint | الوصف |
|--------|----------|-------|
| POST | `/api/auth/logout` | تسجيل الخروج |
| POST | `/api/auth/resend-verification` | إعادة إرسال إيميل التأكيد |

### 👤 User

| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET/PUT | `/api/user/profile` | الملف الشخصي (يدعم رفع صورة) |
| GET/POST | `/api/user/skills` | المهارات |
| GET | `/api/user/roadmap` | مسارات مهنية + فجوات مهارات + كورسات |
| GET | `/api/user/dashboard` | الداشبورد |
| GET/POST | `/api/user/assessments` | التقييمات (مرة يومياً) |
| GET/POST | `/api/user/messages` | الرسائل |
| GET | `/api/user/notifications` | الإشعارات |
| GET | `/api/user/report` | تقرير مهني |

### 💼 Jobs

| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/api/jobs?search=&location=&skills[]=` | قائمة الوظائف مع فلترة |
| GET | `/api/jobs/{id}` | تفاصيل وظيفة |

### 🏢 Company

| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET/PUT | `/api/company/profile` | ملف الشركة + رفع صورة |
| POST | `/api/jobs` | إضافة وظيفة |
| GET | `/api/jobs/{id}/applications` | عرض الطلبات |
| PUT | `/api/jobs/{id}/applications/{appId}` | تحديث حالة الطلب |

### 🤖 AI Endpoints

| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/api/ai/status` | حالة الـ AI |
| GET | `/api/ai/career-recommendations` | اقتراحات مسارات مهنية |
| GET | `/api/ai/job-match/{jobId}` | نسبة التطابق مع وظيفة |
| POST | `/api/ai/advice` | نصيحة مهنية |
| GET | `/api/ai/suggested-skills?career_path=` | اقتراح مهارات |
| POST | `/api/ai/resume-review` | مراجعة السيرة الذاتية |
| POST | `/api/ai/interview-simulate` | محاكاة مقابلة عمل |

### 🛡️ Admin

| Method | Endpoint | الوصف |
|--------|----------|-------|
| GET | `/api/admin/users` | قائمة المستخدمين |
| PUT | `/api/admin/users/{id}/suspend` | تعليق مستخدم |
| CRUD | `/api/admin/skills` | إدارة المهارات |
| CRUD | `/api/admin/courses` | إدارة الكورسات |
| CRUD | `/api/admin/career-paths` | إدارة المسارات المهنية |

---

## 🔑 Authentication

النظام يستخدم **Laravel Sanctum** للـ API tokens.

```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

---

## 🤖 إعداد الـ AI

| المتغير | الوصف |
|---------|-------|
| `AI_SERVICE_URL` | عنوان الـ Internal AI (مثلاً `http://localhost:8001`) |
| `OPENAI_API_KEY` | لو بتستخدم OpenAI كـ Fallback |

---

## 📁 رفع الصور

أرسل الصورة كـ `multipart/form-data` باسم الحقل `profile_picture` (صورة، أقصى حجم 2MB).

---

## ⏰ Scheduler

تشغيل الإشعارات اليومية الساعة 9 صباحاً. تشغيل يدوي:

```bash
php artisan notifications:send-reminders
```

---

## 👥 الفريق

مشروع تخرج — فريق CareerCraft 🎓

---

<div align="center">
Made with ❤️ using Laravel
</div>

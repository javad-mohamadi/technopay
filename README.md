# سیستم پرداخت امن کیف پول

## ویژگی‌های اصلی

- **احراز هویت امن کاربران:** با استفاده از Laravel Passport برای احراز هویت API (OAuth 2.0).

- **مدیریت کیف پول:** کاربران دارای کیف پول با موجودی مشخص هستند.

- هندل کردن Global Daily Limit Amount با Redis hybrid

- **فرآیند کامل پرداخت فاکتور:** یک فرآیند پرداخت دو مرحله‌ای شامل:
    1.  **درخواست پرداخت:** پیش‌نیازها را اعتبارسنجی کرده و یک رمز یکبار مصرف (OTP) زمان‌دار ارسال می‌کند.
    2.  **تایید پرداخت:** رمز یکبار مصرف را تایید کرده و تراکنش را به صورت اتمیک (Atomic) پردازش می‌کند.
- 
- **اعتبارسنجی قوی:** پیاده‌سازی الگوی Pipeline به همراه Specification Pattern برای اعتبارسنجی قوانین کسب‌وکار به شکلی تمیز و قابل توسعه.
- 
- **کنترل همزمانی (Concurrency):** استفاده از قفل بدبینانه (Pessimistic Locking) با `SELECT ... FOR UPDATE` برای جلوگیری از وقوع Race Condition در حین پردازش پرداخت.
- 
- **محدودسازی درخواست‌ها (Throttling):** جلوگیری از اسپم درخواست OTP با استفاده از Rate Limiter لاراول.
- 
- **محدودیت خرج روزانه:** اعمال یک محدودیت خرج روزانه در سطح کل سیستم.
- 
- **سیستم بازگشت وجه (Refund)
- 
- **اطلاع‌رسانی رویداد-محور:** استفاده از سیستم Event و Listener لاراول برای جداسازی منطق اطلاع‌رسانی از منطق اصلی برنامه.
- 
- **تست‌نویسی جامع:** شامل مجموعه‌ای از تست‌های Feature برای اطمینان از پایداری و صحت عملکرد فرآیند پرداخت.

## پشته فناوری و الگوهای طراحی

- **فریمورک:** Laravel 12
- 
- **احراز هویت:** Laravel Passport (OAuth 2.0)
- 
- **دیتابیس:** MySQL (و SQLite برای محیط تست)
- 
- **کشینگ:** Redis (برای قفل‌های اتمیک و Rate Limiting)
- 
- **الگوهای طراحی کلیدی:**
    - Repository Pattern
    - Service Layer
    - Pipeline Pattern (برای اعتبارسنجی)
    - Specification Pattern (برای قوانین کسب‌وکار)
    - Observer Pattern (Events & Listeners)
    - Data Transfer Objects (DTOs)

---

## 🚀 راه‌اندازی پروژه

برای راه‌اندازی پروژه روی سیستم локал خود جهت توسعه و تست، مراحل زیر را دنبال کنید.

### پیش‌نیازها

- Docker
- Docker Compose

### ۱. کلون کردن پروژه

```
git clone [YOUR_PROJECT_GIT_URL]
cd [PROJECT_DIRECTORY]
```

### ۲. تنظیمات محیط

ابتدا فایل نمونه‌ی محیط را کپی کنید.

```
cp .env.example .env
```

فایل `.env` برای کار با Docker از پیش تنظیم شده است و نیازی به تغییر اطلاعات دیتابیس ندارید.

### ۳. ساخت و اجرای کانتینرهای Docker

می‌توانید مستقیماً از Docker Compose یا از دستور میانبر موجود در Makefile استفاده کنید.

```
# با استفاده از Docker Compose
docker-compose up -d --build

# یا با استفاده از Makefile
make app_build
```

این دستور ایمیج‌های مورد نیاز را ساخته و سرویس‌های `app` (اپلیکیشن)، `db` (MySQL) و `redis` را اجرا می‌کند.

### ۴. نصب وابستگی‌ها

وارد شل کانتینر اپلیکیشن شوید.

```
docker exec -it technopay-app sh
```

پس از ورود به کانتینر، دستورات زیر را اجرا کنید:


### ۵. تنظیمات دیتابیس و Passport

این دستور کلیدهای رمزنگاری و یک "Password Grant Client" ایجاد می‌کند. Client ID و Secret در کنسول نمایش داده می‌شوند.
```
php artisan passport:client --password
```
**مهم:** فایل `.env` خود را باز کرده و متغیرهای زیر را با اطلاعاتی که دریافت کردید، به‌روزرسانی کنید:

```
CLIENT_ID=...
CLIENT_SECRET=...
```


## ✅ اجرای تست‌ها

برای اجرای تمام تست‌های Feature و Unit، دستور زیر را در داخل کانتینر اپلیکیشن اجرا کنید:

```
php artisan test
```

## 🎯 لیست API Endpoints

### احراز هویت
- `POST /api/v1/auth/register`
- `POST /api/v1/auth/login`
- `GET /api/v1/auth/user` (نیازمند Bearer Token)
- `GET /api/v1/auth/logout` (نیازمند Bearer Token)

### فرآیند پرداخت
- `POST /api/v1/invoice/{invoice}/request-payment` (نیازمند Bearer Token)
- `POST /api/v1/invoice/pay` (نیازمند Bearer Token)

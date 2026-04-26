# 🏦 LoanUnlock – Full-Stack Laravel Loan Application Platform

A complete fintech loan application platform built with Laravel 10, inspired by InstaMoney's mobile-first UI.

---

## 🚀 Quick Setup (5 Minutes)

### Prerequisites
- PHP 8.1+
- Composer
- MySQL 5.7+ / MariaDB
- Node.js (optional, for asset compilation)

### Step 1 — Install Dependencies
```bash
composer install
```

### Step 2 — Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### Step 3 — Configure Database
Edit `.env`:
```env
DB_DATABASE=loanunlock
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Step 4 — Create Database & Run Migrations
```bash
mysql -u root -p -e "CREATE DATABASE loanunlock CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate
php artisan db:seed
```

### Step 5 — Storage Link
```bash
php artisan storage:link
```

### Step 6 — Serve
```bash
php artisan serve
```

Visit: **http://localhost:8000**

---

## 🔑 Default Credentials

### Admin Panel — http://localhost:8000/admin/login
| Role        | Email                       | Password    |
|-------------|-----------------------------|-------------|
| Super Admin | admin@loanunlock.com        | Admin@123   |
| Reviewer    | reviewer@loanunlock.com     | Review@123  |

### User (OTP Login) — http://localhost:8000/verify
- Enter any 10-digit mobile number
- OTP is always **123456** in non-production mode

---

## 📱 User Flow

```
Splash → Mobile Verification → OTP Entry
  → Personal Details (Name, PAN, DOB, State)
  → Permissions Screen
  → Loan Details (Employment, Income, Amount, Tenure)
  → Eligibility Check (auto-calculated)
  → ₹299 Payment Unlock (UPI / Card / Net Banking)
  → Application Under Review
  → Status Tracking Dashboard
```

## 🛡️ Admin Panel Flow

```
Admin Login → Dashboard (Stats + Recent Apps)
  → Loan Applications (Filter by status, search)
    → Review Application
      → Approve (set amount, tenure, rate) OR
      → Reject (with reason) OR
      → Disburse (mark as paid out)
      → Add Notes
  → User Management (Block/Unblock)
  → Reports (CSV Export)
  → Settings
```

---

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/OtpController.php         # OTP send/verify
│   │   ├── User/
│   │   │   ├── ApplicationController.php  # All application steps
│   │   │   ├── PaymentController.php      # ₹299 payment
│   │   │   └── DashboardController.php    # User dashboard
│   │   └── Admin/
│   │       ├── AdminAuthController.php
│   │       ├── AdminDashboardController.php
│   │       ├── AdminLoanController.php    # Approve/Reject/Disburse
│   │       └── AdminUserController.php
│   ├── Middleware/
│   │   ├── OtpAuth.php                    # Protect user routes
│   │   └── AdminAuth.php                  # Protect admin routes
│   └── Kernel.php
├── Models/
│   ├── User.php
│   ├── Admin.php
│   ├── LoanApplication.php
│   ├── Otp.php
│   ├── Payment.php
│   ├── Document.php
│   └── LoanNote.php
database/
├── migrations/                            # All 7 table migrations
└── seeders/DatabaseSeeder.php             # Admin accounts
resources/views/
├── layouts/
│   ├── app.blade.php                      # Mobile-first user layout
│   └── admin.blade.php                    # Admin panel layout
├── auth/
│   ├── mobile.blade.php                   # Phone verification
│   └── otp.blade.php                      # OTP entry
├── user/
│   ├── splash.blade.php
│   ├── personal.blade.php
│   ├── permissions.blade.php
│   ├── loan_details.blade.php
│   ├── eligibility.blade.php
│   ├── unlock.blade.php                   # ₹299 paywall
│   ├── payment_success.blade.php
│   ├── status.blade.php
│   └── dashboard.blade.php
└── admin/
    ├── login.blade.php
    ├── dashboard.blade.php
    ├── reports.blade.php
    ├── settings.blade.php
    ├── loans/
    │   ├── index.blade.php
    │   ├── show.blade.php
    │   └── documents.blade.php
    └── users/
        ├── index.blade.php
        └── show.blade.php
routes/web.php                             # All routes
```

---

## 🔧 Key Features

### User Side
- ✅ Mobile OTP authentication (no password)
- ✅ Multi-step loan application with progress bar
- ✅ Live EMI calculator
- ✅ Eligibility engine (income-based)
- ✅ ₹299 payment gateway (Razorpay-ready)
- ✅ Application status tracking with timeline
- ✅ Document upload (PAN, Aadhar, Salary Slip, etc.)
- ✅ User dashboard with all applications

### Admin Side
- ✅ Secure admin login (separate from users)
- ✅ Dashboard with stats and charts
- ✅ Approve loans (set custom amount, rate, tenure)
- ✅ Reject with mandatory reason
- ✅ Mark loans as disbursed
- ✅ Add notes/activity log per application
- ✅ Block/unblock users
- ✅ CSV export of all applications
- ✅ Reports and payment analytics

---

## 💳 Razorpay Integration (Production)

In `PaymentController.php`, replace the demo simulation with:

```php
// 1. Add to composer: "razorpay/razorpay": "2.*"
// 2. In .env: RAZORPAY_KEY=rzp_live_xxx, RAZORPAY_SECRET=xxx

use Razorpay\Api\Api;

$api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
$order = $api->order->create([
    'amount'   => 29900, // in paise
    'currency' => 'INR',
    'receipt'  => $payment->transaction_id,
]);
```

## 📱 SMS OTP Integration (Production)

In `OtpController.php`:
```php
// MSG91 example
$response = Http::post('https://api.msg91.com/api/v5/otp', [
    'authkey'  => env('MSG91_AUTH_KEY'),
    'mobile'   => '91' . $mobile,
    'otp'      => $otpCode,
    'sender'   => env('MSG91_SENDER_ID'),
]);
```

---

## 🎨 Design System

| Color        | Hex       | Usage                      |
|--------------|-----------|----------------------------|
| Primary Blue | `#3B5BDB` | Buttons, links, progress   |
| Accent Orange| `#F09210` | Paywall, warnings          |
| Success Green| `#12B76A` | Approved, verified         |
| Error Red    | `#F04438` | Rejected, errors           |

Font: **Plus Jakarta Sans** (Google Fonts)

---

## 📞 Support

- Email: support@loanunlock.com
- Helpline: 1800-986-3452

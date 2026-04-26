<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#3B5BDB">
    <title>@yield('title', 'First Smart Loan') – Life me Load nahi, Loan le</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="h÷ttps://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --blue: #3B5BDB;
            --blue-dark: #2f4ac7;
            --blue-light: #EEF2FF;
            --orange: #F09210;
            --green: #12B76A;
            --red: #F04438;
            --gray-50: #F9FAFB;
            --gray-100: #F2F4F7;
            --gray-200: #E4E7EC;
            --gray-400: #98A2B3;
            --gray-600: #475467;
            --gray-900: #101828;
            --radius: 14px;
            --shadow: 0 4px 24px rgba(59,91,219,0.10);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--gray-50);
            color: var(--gray-900);
            font-size: 15px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }
        /* Mobile Container */
        .app-shell {
            max-width: 430px;
            min-height: 100vh;
            margin: 0 auto;
            background: #fff;
            position: relative;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        /* Progress Bar */
        .progress-bar-top {
            display: flex;
            gap: 6px;
            padding: 16px 20px 0;
        }
        .progress-segment {
            flex: 1;
            height: 4px;
            border-radius: 99px;
            background: var(--gray-200);
        }
        .progress-segment.active { background: var(--blue); }
        .progress-segment.done { background: var(--blue); }

        /* Header */
        .app-header {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            gap: 12px;
        }
        .btn-back {
            width: 36px; height: 36px;
            border-radius: 50%;
            border: 1.5px solid var(--gray-200);
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: var(--gray-900); font-size: 18px;
            text-decoration: none;
            transition: background .15s;
        }
        .btn-back:hover { background: var(--gray-100); color: var(--gray-900); }

        /* Content */
        .app-content {
            flex: 1;
            padding: 0 20px 20px;
            overflow-y: auto;
        }
        .screen-title {
            font-size: 26px;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 6px;
            line-height: 1.2;
        }
        .screen-subtitle {
            font-size: 14px;
            color: var(--gray-600);
            margin-bottom: 28px;
        }

        /* Form Controls */
        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-600);
            margin-bottom: 7px;
        }
        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 1.5px solid var(--gray-200);
            border-radius: var(--radius);
            font-family: inherit;
            font-size: 15px;
            color: var(--gray-900);
            background: #fff;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
            appearance: none;
        }
        .form-control:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(59,91,219,0.12);
        }
        .form-control.is-invalid { border-color: var(--red); }
        .invalid-feedback { color: var(--red); font-size: 12px; margin-top: 5px; }

        /* Phone input */
        .phone-input-wrap {
            display: flex;
            border: 1.5px solid var(--gray-200);
            border-radius: var(--radius);
            overflow: hidden;
            transition: border-color .2s, box-shadow .2s;
        }
        .phone-input-wrap:focus-within {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(59,91,219,0.12);
        }
        .phone-code {
            padding: 14px 14px;
            font-weight: 700;
            color: var(--gray-900);
            background: var(--gray-50);
            border-right: 1.5px solid var(--gray-200);
            white-space: nowrap;
            font-size: 15px;
        }
        .phone-input {
            flex: 1;
            border: none;
            outline: none;
            padding: 14px 16px;
            font-family: inherit;
            font-size: 15px;
            color: var(--gray-900);
            background: #fff;
        }

        /* Radio Cards */
        .radio-card-group { display: flex; gap: 12px; }
        .radio-card {
            flex: 1;
            border: 1.5px solid var(--gray-200);
            border-radius: var(--radius);
            padding: 16px;
            cursor: pointer;
            transition: all .2s;
            position: relative;
        }
        .radio-card input { position: absolute; opacity: 0; }
        .radio-card:has(input:checked) {
            border-color: var(--blue);
            background: var(--blue-light);
        }
        .radio-card-icon { font-size: 24px; margin-bottom: 8px; }
        .radio-card-label { font-weight: 600; font-size: 14px; }

        /* Consent */
        .consent-wrap {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            padding: 16px;
            background: var(--gray-50);
            border-radius: var(--radius);
            margin-bottom: 20px;
        }
        .consent-wrap input[type="checkbox"] {
            width: 20px; height: 20px;
            accent-color: var(--blue);
            flex-shrink: 0;
            margin-top: 1px;
            cursor: pointer;
        }
        .consent-text { font-size: 12.5px; color: var(--gray-600); line-height: 1.6; }
        .consent-text a { color: var(--blue); font-weight: 600; text-decoration: none; }

        /* Buttons */
        .btn-primary {
            display: block;
            width: 100%;
            padding: 16px;
            background: var(--blue);
            color: #fff;
            border: none;
            border-radius: var(--radius);
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            text-align: center;
            cursor: pointer;
            transition: background .2s, transform .1s;
            text-decoration: none;
            letter-spacing: .3px;
        }
        .btn-primary:hover { background: var(--blue-dark); color: #fff; }
        .btn-primary:active { transform: scale(.98); }
        .btn-primary:disabled { opacity: .5; cursor: not-allowed; }
        .btn-outline {
            display: block;
            width: 100%;
            padding: 14px;
            background: #fff;
            color: var(--blue);
            border: 1.5px solid var(--blue);
            border-radius: var(--radius);
            font-family: inherit;
            font-size: 15px;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            text-decoration: none;
        }

        /* Sticky Bottom */
        .sticky-bottom {
            position: sticky;
            bottom: 0;
            padding: 16px 20px 24px;
            background: linear-gradient(to top, #fff 80%, transparent);
        }

        /* Alerts */
        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13.5px;
            margin-bottom: 16px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        .alert-success { background: #ECFDF3; color: #027A48; border: 1px solid #A9EFC5; }
        .alert-error   { background: #FEF3F2; color: #B42318; border: 1px solid #FECDCA; }
        .alert-info    { background: #EFF8FF; color: #175CD3; border: 1px solid #B2DDFF; }
        .alert-warning { background: #FFFAEB; color: #B54708; border: 1px solid #FEC84B; }

        /* Trust Row */
        .trust-row {
            display: flex;
            justify-content: space-around;
            padding: 16px 0;
            gap: 8px;
        }
        .trust-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--gray-600);
            text-align: center;
        }
        .trust-item .icon-wrap {
            width: 38px; height: 38px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
        }
        .trust-item.blue .icon-wrap   { background: var(--blue-light); color: var(--blue); }
        .trust-item.green .icon-wrap  { background: #ECFDF3; color: var(--green); }
        .trust-item.orange .icon-wrap { background: #FFF7E6; color: var(--orange); }

        /* Secure Badge */
        .secure-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12.5px;
            color: var(--green);
            font-weight: 600;
            padding: 10px 14px;
            background: #ECFDF3;
            border-radius: 10px;
            margin-top: 16px;
        }

        /* OTP Inputs */
        .otp-inputs {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 28px 0;
        }
        .otp-input {
            width: 52px; height: 60px;
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            color: var(--gray-900);
            background: var(--gray-50);
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .otp-input:focus {
            border-color: var(--blue);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(59,91,219,0.12);
        }

        /* Permission Items */
        .permission-list { display: flex; flex-direction: column; gap: 0; }
        .permission-item {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 18px 0;
            border-bottom: 1px solid var(--gray-100);
        }
        .permission-item:last-child { border-bottom: none; }
        .perm-icon {
            width: 48px; height: 48px; border-radius: 14px;
            background: var(--blue-light);
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; color: var(--blue); flex-shrink: 0;
        }
        .perm-title { font-weight: 700; font-size: 15px; }
        .perm-desc  { font-size: 13px; color: var(--gray-600); margin-top: 2px; }

        /* Eligibility Card */
        .eligibility-card {
            background: linear-gradient(135deg, var(--blue) 0%, #5B7FE8 100%);
            color: #fff;
            border-radius: 20px;
            padding: 28px 24px;
            text-align: center;
            margin-bottom: 24px;
        }
        .eligibility-card .amount {
            font-size: 40px;
            font-weight: 800;
            letter-spacing: -1px;
        }
        .loan-detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 24px;
        }
        .loan-detail-card {
            background: var(--gray-50);
            border-radius: 12px;
            padding: 16px;
        }
        .loan-detail-label { font-size: 12px; color: var(--gray-600); font-weight: 500; }
        .loan-detail-value { font-size: 20px; font-weight: 700; color: var(--gray-900); margin-top: 4px; }

        /* Payment Options */
        .payment-option {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px;
            border: 1.5px solid var(--gray-200);
            border-radius: var(--radius);
            cursor: pointer;
            transition: all .2s;
            margin-bottom: 10px;
        }
        .payment-option:has(input:checked) {
            border-color: var(--blue);
            background: var(--blue-light);
        }
        .payment-option input { accent-color: var(--blue); }
        .payment-option-icon { font-size: 24px; width: 40px; text-align: center; }
        .payment-option-name { font-weight: 600; font-size: 15px; }
        .payment-option-desc { font-size: 12px; color: var(--gray-600); }

        /* Success Animation */
        @keyframes scaleIn {
            0% { transform: scale(0); opacity: 0; }
            60% { transform: scale(1.15); }
            100% { transform: scale(1); opacity: 1; }
        }
        .success-icon {
            width: 80px; height: 80px;
            background: var(--green);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 40px; color: #fff;
            margin: 0 auto 20px;
            animation: scaleIn .5s cubic-bezier(.36, .07, .19, .97);
        }

        /* Status Timeline */
        .status-timeline { padding: 0; list-style: none; }
        .timeline-item {
            display: flex;
            gap: 16px;
            padding-bottom: 24px;
            position: relative;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 17px;
            top: 36px;
            width: 2px;
            height: calc(100% - 24px);
            background: var(--gray-200);
        }
        .timeline-item:last-child::before { display: none; }
        .timeline-dot {
            width: 36px; height: 36px;
            border-radius: 50%;
            flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px;
        }
        .timeline-dot.done    { background: #ECFDF3; color: var(--green); }
        .timeline-dot.active  { background: var(--blue-light); color: var(--blue); }
        .timeline-dot.pending { background: var(--gray-100); color: var(--gray-400); }
        .timeline-dot.failed  { background: #FEF3F2; color: var(--red); }

        /* Shimmer loader */
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 8px;
        }

        select.form-control { cursor: pointer; }
        @media (min-width: 430px) {
            .app-shell { border-left: 1px solid var(--gray-100); border-right: 1px solid var(--gray-100); box-shadow: var(--shadow); }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="app-shell">
    @yield('content')
</div>
@stack('scripts')
</body>
</html>

<x-guest-layout>
    <style>
        /* =========================================================
           RESET & BASE
        ========================================================= */
        .nto-login-page,
        .nto-login-page * {
            box-sizing: border-box;
        }

        .nto-login-page {
            --nto-green: #024a35;
            --nto-green-dark: #013727;
            --nto-green-light: #059669;
            --nto-bg: #dfc88b;

            width: 100%;
            height: 100vh;
            height: 100dvh;
            display: grid;
            grid-template-columns: 42% 58%;
            overflow: hidden;
            position: relative;
            background: var(--nto-bg);
            font-family: inherit;
        }

        /* =========================================================
           LEFT PANEL
        ========================================================= */
        .nto-left {
            position: relative;
            min-width: 0;
            height: 100vh;
            overflow: hidden;
            background: linear-gradient(135deg, #024a35 0%, #013b2a 50%, #002b1f 100%);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 22px 36px 18px;
            z-index: 2;
        }

        /* Background Decorations */
        .nto-left-bg {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
            z-index: 0;
        }

        .nto-circle {
            position: absolute;
            border-radius: 50%;
            border: 1px solid rgba(16, 185, 129, .15);
        }

        .nto-circle-1 {
            width: 600px;
            height: 600px;
            left: -280px;
            top: -150px;
        }

        .nto-circle-2 {
            width: 480px;
            height: 480px;
            left: -220px;
            top: -100px;
        }

        .nto-circle-3 {
            width: 380px;
            height: 380px;
            left: -170px;
            top: -50px;
        }

        .nto-glow {
            position: absolute;
            width: 300px;
            height: 300px;
            right: -80px;
            top: -80px;
            border-radius: 50%;
            background: rgba(16, 185, 129, .15);
            filter: blur(80px);
        }

        /* Dot Grid */
        .nto-dots {
            position: absolute;
            top: 25px;
            right: 30px;
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 10px;
            opacity: .18;
        }

        .nto-dots span {
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: white;
        }

        /* Top Badge */
        .nto-top-badge {
            position: relative;
            z-index: 10;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            align-self: flex-start;
            padding: 6px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .1);
            border: 1px solid rgba(255, 255, 255, .18);
            backdrop-filter: blur(12px);
        }

        .nto-top-badge-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #34d399;
            box-shadow: 0 0 8px #34d399;
        }

        .nto-top-badge-text {
            color: rgba(255, 255, 255, .95);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        /* Hero Left Content */
        .nto-left-content {
            position: relative;
            z-index: 10;
            margin: auto 0;
            padding: 10px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        /* Logo Card */
        .nto-logo-card {
            position: relative;
            width: 230px;
            padding: 18px 18px 16px;
            border-radius: 22px;
            background: rgba(255, 255, 255, .97);
            border: 1px solid rgba(255, 255, 255, .4);
            box-shadow: 0 20px 45px -10px rgba(0, 0, 0, .35), 0 0 0 1px rgba(255, 255, 255, .2);
            backdrop-filter: blur(16px);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: transform .3s ease;
        }

        .nto-logo-card:hover {
            transform: translateY(-3px);
        }

        .nto-logo-image-wrapper {
            width: 100%;
            height: 135px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .nto-logo-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 8px 12px rgba(0, 0, 0, .12));
        }

        .nto-school-name {
            margin-top: 10px;
            color: #0f172a;
            font-size: 14px;
            font-weight: 900;
            letter-spacing: .04em;
            line-height: 1.2;
        }

        .nto-school-level {
            color: #059669;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .16em;
            margin-top: 2px;
        }

        .nto-logo-line {
            width: 36px;
            height: 3px;
            margin: 8px auto;
            border-radius: 999px;
            background: #10b981;
        }

        .nto-school-motto {
            color: #64748b;
            font-size: 9px;
            line-height: 1.4;
            font-style: italic;
            font-weight: 600;
        }

        /* Portal Title */
        .nto-portal-title {
            margin-top: 16px;
            color: white;
            font-size: 26px;
            line-height: 1.15;
            font-weight: 900;
            letter-spacing: -.03em;
        }

        .nto-title-line {
            width: 40px;
            height: 3px;
            margin: 10px auto;
            border-radius: 999px;
            background: #34d399;
        }

        .nto-description {
            max-width: 340px;
            color: rgba(236, 253, 245, .92);
            font-size: 12px;
            line-height: 1.5;
            font-weight: 500;
        }

        /* Location */
        .nto-location-wrapper {
            position: relative;
            z-index: 10;
            padding-left: 0;
            display: flex;
            justify-content: center;
        }

        .nto-location {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 15px;
            border: 1px solid rgba(167, 243, 208, .3);
            border-radius: 999px;
            background: rgba(0, 50, 40, .45);
            backdrop-filter: blur(10px);
        }

        .nto-location svg {
            width: 14px;
            height: 14px;
            flex-shrink: 0;
            color: #34d399;
        }

        .nto-location span {
            color: #ecfdf5;
            font-size: 10px;
            font-weight: 600;
        }

        /* School Silhouette */
        .nto-school-silhouette {
            position: absolute;
            left: -20px;
            bottom: 0;
            width: 440px;
            height: 230px;
            opacity: .18;
            z-index: 1;
            pointer-events: none;
        }

        /* Curved Divider */
        .nto-divider {
            position: absolute;
            top: 0;
            right: -1px;
            width: 130px;
            height: 100%;
            z-index: 3;
            pointer-events: none;
        }

        .nto-divider svg {
            width: 100%;
            height: 100%;
        }

        .nto-divider-path {
            fill: #dfc88b;
        }



        /* =========================================================
           RIGHT PANEL
        ========================================================= */
        .nto-right {
            position: relative;
            min-width: 0;
            height: 100vh;
            overflow: hidden;
            background: #dfc88b;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            padding: 24px 44px 16px;
        }

        .nto-right-decoration {
            position: absolute;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .nto-right-circle-1 {
            position: absolute;
            width: 600px;
            height: 600px;
            top: -320px;
            right: -280px;
            border-radius: 50%;
            border: 50px solid rgba(226, 232, 240, .55);
        }

        .nto-right-circle-2 {
            position: absolute;
            width: 680px;
            height: 680px;
            bottom: -400px;
            right: -320px;
            border-radius: 50%;
            border: 35px solid rgba(226, 232, 240, .4);
        }

        .nto-right-arc {
            position: absolute;
            right: 0;
            top: 0;
            width: 260px;
            height: 100%;
        }

        .nto-right-arc svg {
            width: 100%;
            height: 100%;
        }

        /* Login Container */
        .nto-login-area {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 460px;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 0;
        }

        /* Login Card */
        .nto-login-card {
            width: 100%;
            background: white;
            border-radius: 24px;
            border: 1px solid #e7edf1;
            box-shadow: 0 20px 60px rgba(15, 23, 42, .12);
            overflow: hidden;
        }

        .nto-login-accent {
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #10b981, #08a77c, #024a35);
        }

        .nto-login-inner {
            padding: 28px 36px 24px;
        }

        .nto-user-icon-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 14px;
        }

        .nto-user-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(16, 185, 129, .14);
        }

        .nto-user-icon svg {
            width: 26px;
            height: 26px;
            color: #059669;
        }

        .nto-login-header {
            text-align: center;
            margin-bottom: 22px;
        }

        .nto-login-header h2 {
            margin: 0;
            color: #0f172a;
            font-size: 24px;
            font-weight: 900;
            letter-spacing: -.03em;
        }

        .nto-login-header p {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 500;
        }

        .nto-login-header-line {
            width: 38px;
            height: 3px;
            margin: 12px auto 0;
            border-radius: 999px;
            background: #10b981;
        }

        /* Form */
        .nto-form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .nto-field {
            display: flex;
            flex-direction: column;
        }

        .nto-field label {
            margin-bottom: 6px;
            color: #1e293b;
            font-size: 12px;
            font-weight: 800;
        }

        .nto-password-label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .nto-password-label label {
            margin-bottom: 0;
        }

        .nto-forgot {
            color: #059669;
            font-size: 11px;
            font-weight: 700;
            text-decoration: none;
            transition: color .2s ease;
        }

        .nto-forgot:hover {
            color: #047857;
            text-decoration: underline;
        }

        .nto-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .nto-input-icon {
            position: absolute;
            left: 14px;
            width: 18px;
            height: 18px;
            color: #94a3b8;
            pointer-events: none;
            transition: color .2s ease;
        }

        .nto-input-wrapper:focus-within .nto-input-icon {
            color: #059669;
        }

        .nto-input {
            width: 100%;
            height: 46px;
            padding: 0 14px 0 44px;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            background: #f8fafc;
            color: #0f172a;
            font-size: 13px;
            font-weight: 600;
            outline: none;
            transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
        }

        .nto-input.password {
            padding-right: 44px;
        }

        .nto-input::placeholder {
            color: #94a3b8;
        }

        .nto-input:focus {
            border-color: #10b981;
            background: white;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, .12);
        }

        .nto-eye-button {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 28px;
            height: 28px;
            padding: 0;
            border: 0;
            background: transparent;
            color: #94a3b8;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: color .2s ease, background-color .2s ease;
        }

        .nto-eye-button:hover {
            color: #059669;
            background: rgba(16, 185, 129, .08);
        }

        .nto-eye-button svg {
            width: 18px;
            height: 18px;
        }

        /* Remember */
        .nto-remember {
            display: flex;
            align-items: center;
            cursor: pointer;
            user-select: none;
        }

        .nto-remember input {
            width: 16px;
            height: 16px;
            accent-color: #059669;
            cursor: pointer;
            border-radius: 4px;
        }

        .nto-remember span {
            margin-left: 8px;
            color: #475569;
            font-size: 12px;
            font-weight: 600;
        }

        /* Login Button */
        .nto-login-button {
            width: 100%;
            height: 48px;
            border: 0;
            border-radius: 14px;
            background: linear-gradient(90deg, #10b981, #059669);
            color: white;
            font-size: 14px;
            font-weight: 900;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 10px 22px rgba(5, 150, 105, .22);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .nto-login-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 26px rgba(5, 150, 105, .28);
        }

        .nto-login-button:active {
            transform: scale(.99);
        }

        .nto-login-button svg {
            width: 18px;
            height: 18px;
            transition: transform .2s ease;
        }

        .nto-login-button:hover svg {
            transform: translateX(4px);
        }

        /* Security */
        .nto-security {
            margin-top: 16px;
            padding-top: 14px;
            border-top: 1px solid #eef2f5;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .nto-security svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            color: #059669;
        }

        .nto-security span {
            color: #94a3b8;
            font-size: 11px;
            font-weight: 600;
        }

        /* Footer */
        .nto-footer {
            position: relative;
            z-index: 10;
            width: 100%;
            text-align: center;
            padding-bottom: 0;
        }

        .nto-footer p {
            margin: 0;
            color: #64748b;
            font-size: 11px;
            font-weight: 500;
        }

        .nto-footer strong {
            color: #024a35;
            font-weight: 800;
        }

        /* Responsive */
        @media (max-width: 1023px) {
            .nto-login-page {
                display: block;
                height: auto;
                min-height: 100vh;
                overflow: auto;
            }

            .nto-left {
                height: auto;
                min-height: auto;
                padding: 25px 22px 45px;
            }

            .nto-divider {
                display: none;
            }

            .nto-right {
                height: auto;
                min-height: auto;
                padding: 35px 22px 25px;
            }

            .nto-login-area {
                padding: 10px 0 25px;
            }
        }

        @media (max-width: 480px) {
            .nto-left {
                padding-left: 18px;
                padding-right: 18px;
            }

            .nto-top-badge-text {
                font-size: 10px;
            }

            .nto-logo-card {
                width: 210px;
                padding: 16px;
            }

            .nto-logo-image-wrapper {
                height: 120px;
            }

            .nto-logo-image {
                width: 125px;
                height: 125px;
            }

            .nto-portal-title {
                font-size: 24px;
            }

            .nto-description {
                font-size: 12px;
            }

            .nto-location span {
                font-size: 10px;
            }

            .nto-right {
                padding-left: 15px;
                padding-right: 15px;
            }

            .nto-login-card {
                border-radius: 20px;
            }

            .nto-login-inner {
                padding: 24px 18px 20px;
            }

            .nto-login-header h2 {
                font-size: 22px;
            }
        }
    </style>

    <div class="nto-login-page">
        {{-- =====================================================
           LEFT PANEL
        ====================================================== --}}
        <section class="nto-left">
            {{-- Background --}}
            <div class="nto-left-bg">
                <div class="nto-circle nto-circle-1"></div>
                <div class="nto-circle nto-circle-2"></div>
                <div class="nto-circle nto-circle-3"></div>
                <div class="nto-glow"></div>
                <div class="nto-dots">
                    @for ($i = 0; $i < 24; $i++)
                        <span></span>
                    @endfor
                </div>
            </div>

            {{-- Top Badge --}}
            <div class="nto-top-badge">
                <span class="nto-top-badge-dot"></span>
                <span class="nto-top-badge-text">
                    NTO National Plus Primary School
                </span>
            </div>

            {{-- Hero --}}
            <div class="nto-left-content">
                {{-- Logo Card --}}
                <div class="nto-logo-card">
                    <div class="nto-logo-image-wrapper">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo NTO National Plus Primary School"
                            class="nto-logo-image" onerror="this.onerror=null;this.src='{{ asset('logo.png') }}';">
                    </div>
                    <div class="nto-school-name">
                        NTO NATIONAL PLUS
                    </div>
                    <div class="nto-school-level">
                        PRIMARY SCHOOL
                    </div>
                    <div class="nto-logo-line"></div>
                    <div class="nto-school-motto">
                        "Nurtured in God • Observed in Humanity • Taught in Knowledge"
                    </div>
                </div>

                {{-- Portal Title --}}
                <h1 class="nto-portal-title">
                    Sistem Presensi &amp;<br>
                    Management Sekolah
                </h1>
                <div class="nto-title-line"></div>
                <p class="nto-description">
                    Platform digital terintegrasi untuk pengelolaan absensi, data siswa, serta rekapitulasi sekolah secara real-time.
                </p>
            </div>

            {{-- Location --}}
            <div class="nto-location-wrapper">
                <div class="nto-location">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Kupang, Nusa Tenggara Timur</span>
                </div>
            </div>

            {{-- School Building Silhouette --}}
            <svg class="nto-school-silhouette" viewBox="0 0 500 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Ground Line -->
                <rect x="0" y="280" width="500" height="20" fill="white" />
                <!-- Main Building Central Block -->
                <rect x="180" y="100" width="140" height="180" fill="white" />
                <!-- Central Clock Tower/Gable -->
                <polygon points="180,100 250,50 320,100" fill="white" />
                <!-- Clock Circle -->
                <circle cx="250" cy="90" r="14" fill="#024a35" />
                <!-- Left Wing -->
                <rect x="60" y="140" width="120" height="140" fill="white" />
                <polygon points="60,140 120,110 180,140" fill="white" opacity="0.9" />
                <!-- Right Wing -->
                <rect x="320" y="140" width="120" height="140" fill="white" />
                <polygon points="320,140 380,110 440,140" fill="white" opacity="0.9" />
                <!-- Entrance Pillars -->
                <rect x="220" y="210" width="15" height="70" fill="#024a35" />
                <rect x="265" y="210" width="15" height="70" fill="#024a35" />
                <rect x="210" y="200" width="80" height="10" fill="white" />
                <!-- Windows grid -->
                <rect x="85" y="160" width="20" height="30" rx="3" fill="#024a35" />
                <rect x="130" y="160" width="20" height="30" rx="3" fill="#024a35" />
                <rect x="85" y="210" width="20" height="30" rx="3" fill="#024a35" />
                <rect x="130" y="210" width="20" height="30" rx="3" fill="#024a35" />
                <rect x="350" y="160" width="20" height="30" rx="3" fill="#024a35" />
                <rect x="395" y="160" width="20" height="30" rx="3" fill="#024a35" />
                <rect x="350" y="210" width="20" height="30" rx="3" fill="#024a35" />
                <rect x="395" y="210" width="20" height="30" rx="3" fill="#024a35" />
                <!-- Flagpole -->
                <line x1="250" y1="50" x2="250" y2="15" stroke="white" stroke-width="3" />
                <polygon points="250,15 275,23 250,31" fill="#ecfdf5" />
            </svg>

            {{-- Curved Divider --}}
            <div class="nto-divider">
                <svg viewBox="0 0 130 800" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path class="nto-divider-path" d="M130 0 C35 250 35 550 130 800 L130 800 L130 0 Z" />
                </svg>
            </div>
        </section>



        {{-- =====================================================
           RIGHT PANEL
        ====================================================== --}}
        <section class="nto-right">
            {{-- Background Decoration --}}
            <div class="nto-right-decoration">
                <div class="nto-right-circle-1"></div>
                <div class="nto-right-circle-2"></div>
                <div class="nto-right-arc">
                    <svg viewBox="0 0 300 800" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M170 -30 C310 170 310 630 170 830" stroke="#e2e8f0" stroke-width="45" />
                        <path d="M235 -30 C350 180 350 620 235 830" stroke="#e2e8f0" stroke-width="18" />
                    </svg>
                </div>
            </div>

            {{-- Login Area --}}
            <div class="nto-login-area">
                <div class="nto-login-card">
                    <div class="nto-login-accent"></div>

                    <div class="nto-login-inner">
                        {{-- User Icon --}}
                        <div class="nto-user-icon-wrapper">
                            <div class="nto-user-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0z M12 14a7 7 0 00-7 7h14 a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        </div>

                        {{-- Header --}}
                        <div class="nto-login-header">
                            <h2>Selamat Datang Kembali!</h2>
                            <p>Silakan masuk untuk melanjutkan ke portal presensi.</p>
                            <div class="nto-login-header-line"></div>
                        </div>

                        {{-- Session Status --}}
                        <x-auth-session-status class="mb-4 text-xs" :status="session('status')" />

                        {{-- Login Form --}}
                        <form method="POST" action="{{ route('login') }}" class="nto-form">
                            @csrf

                            {{-- EMAIL --}}
                            <div class="nto-field">
                                <label for="email">Alamat Email</label>
                                <div class="nto-input-wrapper">
                                    <svg class="nto-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                            d="M3 8l7.89 5.26 a2 2 0 002.22 0L21 8 M5 19h14a2 2 0 002-2V7 a2 2 0 00-2-2H5 a2 2 0 00-2 2v10 a2 2 0 002 2z" />
                                    </svg>
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                        autofocus autocomplete="username" placeholder="admin@nto-kupang.sch.id"
                                        class="nto-input">
                                </div>
                                <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-600 font-semibold" />
                            </div>

                            {{-- PASSWORD --}}
                            <div class="nto-field" x-data="{ showPassword: false }">
                                <div class="nto-password-label">
                                    <label for="password">Kata Sandi</label>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="nto-forgot">
                                            Lupa kata sandi?
                                        </a>
                                    @endif
                                </div>
                                <div class="nto-input-wrapper">
                                    <svg class="nto-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                            d="M12 15v2m-6 4h12 a2 2 0 002-2v-6 a2 2 0 00-2-2H6 a2 2 0 00-2 2v6 a2 2 0 002 2zm10-10V7 a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    <input id="password" x-bind:type="showPassword ? 'text' : 'password'"
                                        type="password" name="password" required autocomplete="current-password"
                                        placeholder="••••••••" class="nto-input password">
                                    <button type="button" id="togglePasswordBtn" class="nto-eye-button"
                                        title="Tampilkan / Sembunyikan Kata Sandi"
                                        @click="showPassword = !showPassword">
                                        {{-- Eye --}}
                                        <svg x-show="!showPassword" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5 c4.478 0 8.268 2.943 9.542 7 -1.274 4.057 -5.064 7 -9.542 7 -4.477 0 -8.268-2.943 -9.542-7z" />
                                        </svg>
                                        {{-- Eye Off --}}
                                        <svg x-show="showPassword" x-cloak fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                d="M13.875 18.825 A10.05 10.05 0 0112 19 c-4.478 0-8.268-2.943 -9.543-7 a9.97 9.97 0 011.563-3.029 m5.858-5.908 a10.02 10.02 0 013.122-.563 c4.478 0 8.268 2.943 9.542 7 a9.97 9.97 0 01-2.43 3.978 m-3.83-3.83 a3 3 0 00-4.243-4.243 M9.878 9.878l4.242 4.242 M9.88 9.88l-3.29-3.29 m7.532 7.532l3.29 3.29 M3 3l18 18" />
                                        </svg>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-600 font-semibold" />
                            </div>

                            {{-- REMEMBER --}}
                            <div>
                                <label for="remember_me" class="nto-remember">
                                    <input id="remember_me" type="checkbox" name="remember">
                                    <span>Ingat saya di perangkat ini</span>
                                </label>
                            </div>

                            {{-- LOGIN BUTTON --}}
                            <div>
                                <button type="submit" class="nto-login-button">
                                    <span>Masuk Sekarang</span>
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </button>
                            </div>
                        </form>

                        {{-- Security --}}
                        <div class="nto-security">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M9 12l2 2 4-4 m5.618-4.016 A11.955 11.955 0 0112 2.944 a11.955 11.955 0 01-8.618 3.04 A12.02 12.02 0 003 9 c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Keamanan data kami terjamin dan terenkripsi.</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <footer class="nto-footer">
                <p>
                    &copy; {{ date('Y') }} <strong>NTO National Plus Primary School</strong>. Hak Cipta Dilindungi.
                </p>
            </footer>
        </section>
    </div>

    {{-- =========================================================
       PASSWORD TOGGLE FALLBACK (VANILLA JS)
    ========================================================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('togglePasswordBtn');
            const passInput = document.getElementById('password');

            if (toggleBtn && passInput) {
                toggleBtn.addEventListener('click', function () {
                    if (!window.Alpine) {
                        if (passInput.type === 'password') {
                            passInput.type = 'text';
                        } else {
                            passInput.type = 'password';
                        }
                    }
                });
            }
        });
    </script>
</x-guest-layout>
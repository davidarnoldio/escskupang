<x-guest-layout>

    <style>
        /* =========================================================
           GLOBAL RESET
        ========================================================= */

        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: hidden !important;
            background: #ffffff;
        }

        .nto-login-page,
        .nto-login-page * {
            box-sizing: border-box;
        }

        .nto-login-page {
            width: 100%;
            max-width: 100%;
            min-height: 100vh;

            display: grid;
            grid-template-columns: minmax(0, 55fr) minmax(0, 45fr);

            overflow: hidden;
            position: relative;

            background: #ffffff;
            font-family: inherit;
        }

        @media (min-width: 1024px) {
            .nto-login-page {
                height: 100vh;
                height: 100dvh;
            }
        }

        @media (min-width: 1400px) {
            .nto-login-page {
                grid-template-columns: minmax(0, 57fr) minmax(0, 43fr);
            }
        }


        /* =========================================================
           LEFT PANEL
           SCHOOL PHOTO + BRANDING + VISI MISI
        ========================================================= */

        .nto-left {
            position: relative;
            min-width: 0;

            background: #450a0a;
            color: white;

            display: flex;
            flex-direction: column;
            justify-content: space-between;

            padding: 18px 46px 14px 28px;

            z-index: 2;

            overflow-y: auto;
            overflow-x: hidden;

            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.15) transparent;
        }

        @media (min-width: 1280px) {
            .nto-left {
                padding: 22px 54px 18px 36px;
            }
        }


        /* =========================================================
           BACKGROUND LAYERS
        ========================================================= */

        .nto-left-bg {
            position: absolute;
            inset: 0;

            pointer-events: none;
            overflow: hidden;

            z-index: 0;
        }


        /* ---------------------------------------------------------
           SCHOOL PHOTO
        --------------------------------------------------------- */

        .nto-school-photo-bg {
            position: absolute;
            inset: 0;

            background-image: url('{{ asset('images/nto_school.jpg') }}?v={{ time() }}');

            background-size: cover;

            /*
             * Ubah posisi ini jika objek utama foto
             * terlalu ke atas / bawah.
             */
            background-position: center center;

            background-repeat: no-repeat;

            /*
             * Foto dibuat cukup terlihat.
             */
            opacity: 0.88;

            /*
             * Sedikit desaturasi agar menyatu
             * dengan tema merah.
             */
            filter:
                contrast(1.04) saturate(1.02);

            pointer-events: none;

            z-index: 1;
        }


        /* ---------------------------------------------------------
           RED OVERLAY
        --------------------------------------------------------- */

        .nto-red-gradient-overlay {
            position: absolute;
            inset: 0;

            background:
                linear-gradient(105deg,
                    rgba(45, 6, 6, 0.95) 0%,
                    rgba(65, 8, 8, 0.88) 35%,
                    rgba(127, 29, 29, 0.48) 68%,
                    rgba(65, 8, 8, 0.65) 100%);

            pointer-events: none;

            z-index: 2;
        }


        /* ---------------------------------------------------------
           DECORATIVE CIRCLES
        --------------------------------------------------------- */

        .nto-circle-mesh {
            position: absolute;

            border-radius: 50%;

            border: 1px solid rgba(255, 255, 255, 0.07);

            pointer-events: none;

            z-index: 3;
        }

        .nto-circle-mesh-1 {
            width: 600px;
            height: 600px;

            left: -180px;
            top: -160px;
        }

        .nto-circle-mesh-2 {
            width: 440px;
            height: 440px;

            right: 20px;
            bottom: -100px;
        }

        .nto-glow-red {
            position: absolute;

            width: 380px;
            height: 380px;

            left: 5%;
            top: 20%;

            border-radius: 50%;

            background: rgba(220, 38, 38, 0.12);

            filter: blur(90px);

            pointer-events: none;

            z-index: 3;
        }


        /* =========================================================
           TOP BADGE
        ========================================================= */

        .nto-top-badge {
            position: relative;
            z-index: 10;

            display: inline-flex;
            align-items: center;

            gap: 7px;

            align-self: flex-start;

            padding: 4px 12px;

            border-radius: 999px;

            background: rgba(255, 255, 255, 0.14);

            border: 1px solid rgba(255, 255, 255, 0.22);

            backdrop-filter: blur(10px);
        }

        .nto-top-badge-dot {
            width: 6.5px;
            height: 6.5px;

            border-radius: 50%;

            background: #f87171;

            box-shadow: 0 0 8px #f87171;
        }

        .nto-top-badge-text {
            color: rgba(255, 255, 255, 0.95);

            font-size: 9.5px;

            font-weight: 800;

            letter-spacing: .08em;

            text-transform: uppercase;
        }


        /* =========================================================
           HERO ROW
           LOGO + TITLE
        ========================================================= */

        .nto-hero-row {
            position: relative;
            z-index: 10;

            display: grid;

            grid-template-columns: 210px minmax(0, 1fr);

            gap: 20px;

            align-items: center;

            margin-top: 8px;
            margin-bottom: 12px;

            min-width: 0;
        }


        /* =========================================================
           LOGO CARD
        ========================================================= */

        .nto-logo-card {
            width: 210px;

            padding: 16px 14px 13px;

            border-radius: 22px;

            background: rgba(255, 255, 255, 0.98);

            border: 1px solid rgba(255, 255, 255, 0.7);

            box-shadow:
                0 16px 36px -8px rgba(0, 0, 0, 0.35);

            display: flex;
            flex-direction: column;

            align-items: center;

            text-align: center;

            flex-shrink: 0;

            transition: transform 0.2s ease;
        }

        .nto-logo-card:hover {
            transform: translateY(-2px);
        }

        .nto-logo-image-wrapper {
            width: 100%;
            height: 98px;

            display: flex;

            align-items: center;
            justify-content: center;
        }

        .nto-logo-image {
            width: 95px;
            height: 95px;

            object-fit: contain;

            filter:
                drop-shadow(0 3px 6px rgba(0, 0, 0, 0.1));
        }

        .nto-school-name {
            color: #0f172a;

            font-size: 12px;

            font-weight: 900;

            letter-spacing: .05em;

            margin-top: 6px;

            line-height: 1.2;
        }

        .nto-school-level {
            color: #dc2626;

            font-size: 10px;

            font-weight: 800;

            letter-spacing: .12em;

            margin-top: 1px;
        }

        .nto-school-motto {
            color: #64748b;

            font-size: 8.5px;

            line-height: 1.38;

            font-style: italic;

            font-weight: 600;

            margin-top: 6px;

            padding-top: 5px;

            border-top: 1px solid #f1f5f9;
        }


        /* =========================================================
           HERO INFO
        ========================================================= */

        .nto-hero-info {
            display: flex;

            flex-direction: column;

            justify-content: center;

            gap: 6px;

            min-width: 0;
        }

        .nto-portal-title {
            color: #ffffff;

            font-size: 24px;

            line-height: 1.2;

            font-weight: 900;

            letter-spacing: -0.02em;

            margin: 0;

            word-break: break-word;
        }

        @media (min-width: 1280px) {
            .nto-portal-title {
                font-size: 28px;
            }
        }

        .nto-portal-desc {
            color: rgba(254, 242, 242, 0.92);

            font-size: 12px;

            line-height: 1.5;

            font-weight: 500;

            margin: 0;

            word-break: break-word;
        }

        .nto-portal-loc-badge {
            display: inline-flex;

            align-items: center;

            gap: 6px;

            align-self: flex-start;

            padding: 4px 12px;

            border-radius: 999px;

            background: rgba(0, 0, 0, 0.32);

            border: 1px solid rgba(255, 255, 255, 0.16);

            color: #fef2f2;

            font-size: 10.5px;

            font-weight: 600;

            backdrop-filter: blur(8px);

            margin-top: 3px;
        }

        .nto-portal-loc-badge svg {
            width: 13px;
            height: 13px;

            color: #f87171;

            flex-shrink: 0;
        }


        .nto-vision-mission-grid {
            position: relative;
            z-index: 10;

            display: grid;

            grid-template-columns:
                minmax(0, 1fr) minmax(0, 1.25fr);

            gap: 26px;

            width: 100%;
            max-width: 860px;

            margin-top: 8px;
            margin-bottom: 10px;

            align-items: stretch;

            min-width: 0;
        }

        .nto-card-vm {
            position: relative;

            background:
                rgba(36, 4, 4, 0.58);

            border:
                1px solid rgba(255, 255, 255, 0.18);

            border-radius: 18px;

            padding: 15px 18px;

            backdrop-filter: blur(14px);

            display: flex;

            flex-direction: column;

            overflow: hidden;

            box-shadow:
                0 10px 24px rgba(0, 0, 0, 0.22);

            min-width: 0;
        }

        .nto-card-vm-header {
            display: flex;

            align-items: center;

            gap: 8px;

            margin-bottom: 8px;

            flex-shrink: 0;
        }

        .nto-card-vm-icon {
            width: 22px;
            height: 22px;

            border-radius: 50%;

            background: #ef4444;

            display: flex;

            align-items: center;
            justify-content: center;

            color: white;

            flex-shrink: 0;

            box-shadow:
                0 2px 6px rgba(239, 68, 68, 0.35);
        }

        .nto-card-vm-icon svg {
            width: 12px;
            height: 12px;
        }

        .nto-card-vm-title {
            color: #ffffff;

            font-size: 14px;

            font-weight: 800;

            letter-spacing: -0.01em;

            margin: 0;
        }

        .nto-card-quote-watermark {
            position: absolute;

            right: 10px;
            bottom: 2px;

            font-size: 38px;

            line-height: 1;

            font-family: Georgia, serif;

            color: rgba(255, 255, 255, 0.08);

            pointer-events: none;

            user-select: none;
        }


        /* =========================================================
           VISION
        ========================================================= */

        .nto-vision-content {
            font-size: 11.5px;

            line-height: 1.6;

            color: rgba(254, 242, 242, 0.95);

            font-weight: 500;

            position: relative;

            z-index: 2;

            word-break: break-word;

            padding-top: 2px;
        }


        /* =========================================================
           MISSION
        ========================================================= */

        .nto-mission-list {
            position: relative;

            z-index: 2;

            list-style: none;

            margin: 0;
            padding: 0;

            display: flex;

            flex-direction: column;

            gap: 5.5px;

            flex: 1;

            min-width: 0;
        }

        .nto-mission-list::-webkit-scrollbar {
            width: 3px;
        }

        .nto-mission-list::-webkit-scrollbar-thumb {
            background:
                rgba(255, 255, 255, 0.22);

            border-radius: 999px;
        }

        .nto-mission-item {
            display: flex;

            align-items: flex-start;

            gap: 8px;

            font-size: 10.2px;

            line-height: 1.4;

            color:
                rgba(254, 242, 242, 0.95);

            font-weight: 500;

            min-width: 0;
        }

        .nto-mission-badge {
            width: 16.5px;
            height: 16.5px;

            border-radius: 50%;

            background: #ef4444;

            color: #ffffff;

            font-size: 8px;

            font-weight: 800;

            display: flex;

            align-items: center;
            justify-content: center;

            flex-shrink: 0;

            margin-top: 1px;

            box-shadow:
                0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .nto-mission-text {
            flex: 1;

            min-width: 0;

            word-break: break-word;
        }


        /* =========================================================
           LEFT FOOTER
        ========================================================= */

        .nto-left-footer-row {
            position: relative;

            z-index: 10;

            display: flex;

            align-items: center;

            justify-content: flex-start;

            gap: 24px;

            padding-top: 4px;

            min-width: 0;
        }

        .nto-dots-grid {
            display: grid;

            grid-template-columns:
                repeat(6, 1fr);

            gap: 6px;

            opacity: 0.16;
        }

        .nto-dots-grid span {
            width: 3px;
            height: 3px;

            border-radius: 50%;

            background: white;
        }

        .nto-bottom-location {
            display: inline-flex;

            align-items: center;

            gap: 5px;

            padding: 3px 11px;

            border-radius: 999px;

            background: rgba(0, 0, 0, 0.32);

            border:
                1px solid rgba(255, 255, 255, 0.14);

            color: #fef2f2;

            font-size: 9px;

            font-weight: 600;

            backdrop-filter: blur(8px);
        }

        .nto-bottom-location svg {
            width: 11px;
            height: 11px;

            color: #f87171;
        }


        /* =========================================================
           CURVED DIVIDER
        ========================================================= */

        .nto-divider {
            position: absolute;

            top: 0;
            right: 0;
            bottom: 0;

            width: 58px;
            height: 100%;

            z-index: 4;

            pointer-events: none;

            overflow: hidden;
        }

        .nto-divider svg {
            width: 100%;
            height: 100%;

            display: block;
        }

        .nto-divider-path {
            fill: #ffffff;
        }


        /* =========================================================
           RIGHT PANEL
        ========================================================= */

        .nto-right {
            position: relative;

            min-width: 0;

            background: #ffffff;

            display: flex;

            flex-direction: column;

            align-items: center;

            justify-content: space-between;

            padding: 18px 24px 12px;

            overflow-y: auto;

            overflow-x: hidden;

            z-index: 3;
        }

        @media (min-width: 1280px) {
            .nto-right {
                padding: 24px 34px 16px;
            }
        }


        /* =========================================================
           RIGHT DECORATION
        ========================================================= */

        .nto-right-decoration {
            position: absolute;

            inset: 0;

            pointer-events: none;

            overflow: hidden;
        }

        .nto-right-circle-1 {
            position: absolute;

            width: 440px;
            height: 440px;

            top: -190px;
            right: -170px;

            border-radius: 50%;

            border:
                32px solid rgba(254, 202, 202, 0.45);
        }

        .nto-right-circle-2 {
            position: absolute;

            width: 520px;
            height: 520px;

            bottom: -270px;
            right: -210px;

            border-radius: 50%;

            border:
                26px solid rgba(254, 226, 226, 0.35);
        }

        .nto-right-arc {
            position: absolute;

            right: 0;
            top: 0;

            width: 180px;
            height: 100%;
        }

        .nto-right-arc svg {
            width: 100%;
            height: 100%;
        }


        /* =========================================================
           LOGIN AREA
        ========================================================= */

        .nto-login-area {
            position: relative;

            z-index: 10;

            width: 100%;

            max-width: 360px;

            margin: auto 0;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 6px 0;

            min-width: 0;
        }


        /* =========================================================
           LOGIN CARD
        ========================================================= */

        .nto-login-card {
            width: 100%;

            background: white;

            border-radius: 22px;

            border:
                1px solid #edf2f7;

            box-shadow:
                0 16px 45px rgba(15, 23, 42, 0.08);

            overflow: hidden;
        }

        .nto-login-accent {
            width: 100%;

            height: 4.5px;

            background:
                linear-gradient(90deg,
                    #ef4444,
                    #b91c1c,
                    #7f1d1d);
        }

        .nto-login-inner {
            padding: 20px 24px 16px;
        }


        /* =========================================================
           USER ICON
        ========================================================= */

        .nto-user-icon-wrapper {
            display: flex;

            justify-content: center;

            margin-bottom: 8px;
        }

        .nto-user-icon {
            width: 44px;
            height: 44px;

            border-radius: 50%;

            background: #fef2f2;

            border:
                1px solid #fee2e2;

            display: flex;

            align-items: center;
            justify-content: center;

            box-shadow:
                0 4px 12px rgba(239, 68, 68, 0.12);
        }

        .nto-user-icon svg {
            width: 20px;
            height: 20px;

            color: #dc2626;
        }


        /* =========================================================
           LOGIN HEADER
        ========================================================= */

        .nto-login-header {
            text-align: center;

            margin-bottom: 14px;
        }

        .nto-login-header h2 {
            margin: 0;

            color: #0f172a;

            font-size: 21px;

            font-weight: 800;

            letter-spacing: -0.02em;
        }

        .nto-login-header p {
            margin: 4px 0 0;

            color: #64748b;

            font-size: 12px;

            font-weight: 500;
        }

        .nto-login-header-line {
            width: 32px;
            height: 3px;

            margin: 8px auto 0;

            border-radius: 999px;

            background: #dc2626;
        }


        /* =========================================================
           FORM
        ========================================================= */

        .nto-form {
            display: flex;

            flex-direction: column;

            gap: 11px;
        }

        .nto-field {
            display: flex;

            flex-direction: column;
        }

        .nto-field label {
            margin-bottom: 3.5px;

            color: #1e293b;

            font-size: 10.5px;

            font-weight: 800;
        }

        .nto-password-label {
            display: flex;

            align-items: center;

            justify-content: space-between;

            margin-bottom: 3.5px;
        }

        .nto-password-label label {
            margin-bottom: 0;
        }

        .nto-forgot {
            color: #dc2626;

            font-size: 10px;

            font-weight: 700;

            text-decoration: none;

            transition: color 0.2s ease;
        }

        .nto-forgot:hover {
            color: #b91c1c;

            text-decoration: underline;
        }


        /* =========================================================
           INPUT
        ========================================================= */

        .nto-input-wrapper {
            position: relative;

            display: flex;

            align-items: center;
        }

        .nto-input-icon {
            position: absolute;

            left: 12px;

            width: 15px;
            height: 15px;

            color: #94a3b8;

            pointer-events: none;

            transition: color 0.2s ease;
        }

        .nto-input-wrapper:focus-within .nto-input-icon {
            color: #dc2626;
        }

        .nto-input {
            width: 100%;

            height: 40px;

            padding:
                0 12px 0 36px;

            border-radius: 10px;

            border:
                1.5px solid #e2e8f0;

            background: #f8fafc;

            color: #0f172a;

            font-size: 12px;

            font-weight: 600;

            outline: none;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease,
                background-color 0.2s ease;
        }

        .nto-input.password {
            padding-right: 36px;
        }

        .nto-input::placeholder {
            color: #94a3b8;

            font-size: 11.5px;
        }

        .nto-input:focus {
            border-color: #ef4444;

            background: white;

            box-shadow:
                0 0 0 3px rgba(239, 68, 68, 0.12);
        }


        /* =========================================================
           PASSWORD EYE
        ========================================================= */

        .nto-eye-button {
            position: absolute;

            right: 8px;

            top: 50%;

            transform: translateY(-50%);

            width: 25px;
            height: 25px;

            padding: 0;

            border: 0;

            background: transparent;

            color: #94a3b8;

            cursor: pointer;

            display: flex;

            align-items: center;
            justify-content: center;

            border-radius: 6px;

            transition:
                color 0.2s ease,
                background-color 0.2s ease;
        }

        .nto-eye-button:hover {
            color: #dc2626;

            background:
                rgba(239, 68, 68, 0.08);
        }

        .nto-eye-button svg {
            width: 15px;
            height: 15px;
        }


        /* =========================================================
           REMEMBER ME
        ========================================================= */

        .nto-remember {
            display: flex;

            align-items: center;

            cursor: pointer;

            user-select: none;
        }

        .nto-remember input {
            width: 14px;
            height: 14px;

            accent-color: #dc2626;

            cursor: pointer;

            border-radius: 4px;
        }

        .nto-remember span {
            margin-left: 6px;

            color: #475569;

            font-size: 11px;

            font-weight: 600;
        }


        /* =========================================================
           LOGIN BUTTON
        ========================================================= */

        .nto-login-button {
            width: 100%;

            height: 42px;

            border: 0;

            border-radius: 10px;

            background:
                linear-gradient(90deg,
                    #dc2626,
                    #b91c1c);

            color: white;

            font-size: 12.5px;

            font-weight: 800;

            cursor: pointer;

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 6px;

            box-shadow:
                0 6px 16px rgba(220, 38, 38, 0.28);

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .nto-login-button:hover {
            transform: translateY(-1px);

            box-shadow:
                0 9px 20px rgba(220, 38, 38, 0.35);
        }

        .nto-login-button:active {
            transform: scale(0.99);
        }

        .nto-login-button svg {
            width: 14px;
            height: 14px;

            transition:
                transform 0.2s ease;
        }

        .nto-login-button:hover svg {
            transform: translateX(4px);
        }


        /* =========================================================
           SECURITY MESSAGE
        ========================================================= */

        .nto-security {
            margin-top: 10px;

            padding-top: 8px;

            border-top:
                1px solid #f1f5f9;

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 5px;
        }

        .nto-security svg {
            width: 13px;
            height: 13px;

            flex-shrink: 0;

            color: #dc2626;
        }

        .nto-security span {
            color: #94a3b8;

            font-size: 10px;

            font-weight: 600;
        }


        /* =========================================================
           FOOTER
        ========================================================= */

        .nto-footer {
            position: relative;

            z-index: 10;

            width: 100%;

            text-align: center;

            padding-top: 4px;
        }

        .nto-footer p {
            margin: 0;

            color: #64748b;

            font-size: 10px;

            font-weight: 500;
        }

        .nto-footer strong {
            color: #7f1d1d;

            font-weight: 800;
        }


        /* =========================================================
           RESPONSIVE
        ========================================================= */

        @media (max-width: 1023px) {

            .nto-login-page {
                display: block;

                height: auto;

                min-height: 100vh;
            }

            .nto-left {
                height: auto;

                padding:
                    24px 20px 30px;
            }

            .nto-divider {
                display: none;
            }

            .nto-hero-row {
                grid-template-columns: 1fr;

                text-align: center;

                justify-items: center;
            }

            .nto-hero-info {
                align-items: center;

                text-align: center;
            }

            .nto-portal-loc-badge {
                align-self: center;
            }

            .nto-vision-mission-grid {
                grid-template-columns: 1fr;
            }

            .nto-right {
                height: auto;

                padding:
                    32px 20px 24px;
            }

            .nto-login-area {
                padding:
                    10px 0 20px;
            }
        }


        /* =========================================================
           SMALL MOBILE
        ========================================================= */

        @media (max-width: 600px) {

            .nto-left {
                padding:
                    20px 16px 25px;
            }

            .nto-top-badge-text {
                font-size: 8px;
            }

            .nto-logo-card {
                width: 145px;
            }

            .nto-portal-title {
                font-size: 21px;
            }

            .nto-portal-desc {
                font-size: 10px;
            }

            .nto-card-vm {
                min-height: 200px;

                padding: 14px;
            }

            .nto-mission-item {
                font-size: 9.5px;
            }

            .nto-right {
                padding:
                    25px 15px 20px;
            }

            .nto-login-card {
                border-radius: 18px;
            }

            .nto-login-inner {
                padding:
                    20px 20px 16px;
            }
        }
    </style>


    {{-- =====================================================
    MAIN LOGIN PAGE
    ====================================================== --}}

    <div class="nto-login-page">


        {{-- =====================================================
        LEFT PANEL
        BRANDING + SCHOOL PHOTO + VISI MISI
        ====================================================== --}}

        <section class="nto-left">


            {{-- Background Photo & Decorative Layers --}}

            <div class="nto-left-bg">

                {{-- FOTO SEKOLAH --}}
                <div class="nto-school-photo-bg"></div>

                {{-- RED OVERLAY --}}
                <div class="nto-red-gradient-overlay"></div>

                {{-- DECORATIVE CIRCLES --}}
                <div class="nto-circle-mesh nto-circle-mesh-1"></div>

                <div class="nto-circle-mesh nto-circle-mesh-2"></div>

                <div class="nto-glow-red"></div>

            </div>


            {{-- =================================================
            TOP BADGE
            ================================================== --}}

            <div class="nto-top-badge">

                <span class="nto-top-badge-dot"></span>

                <span class="nto-top-badge-text">
                    NTO National Plus Primary School
                </span>

            </div>


            {{-- =================================================
            HERO ROW
            ================================================== --}}

            <div class="nto-hero-row">


                {{-- LOGO CARD --}}

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


                    <div class="nto-school-motto">
                        "Nurtured in God • Observed in Humanity • Taught in Knowledge"
                    </div>

                </div>


                {{-- PORTAL INFORMATION --}}

                <div class="nto-hero-info">

                    <h1 class="nto-portal-title">
                        Sistem Presensi &amp;<br>
                        Management Sekolah
                    </h1>


                    <p class="nto-portal-desc">
                        Platform digital terintegrasi untuk pengelolaan absensi,
                        data siswa, serta rekapitulasi sekolah secara real-time.
                    </p>


                    <div class="nto-portal-loc-badge">

                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                clip-rule="evenodd" />
                        </svg>

                        <span>
                            Kupang, Nusa Tenggara Timur
                        </span>

                    </div>

                </div>

            </div>


            {{-- =================================================
            VISI & MISI
            ================================================== --}}

            <div class="nto-vision-mission-grid">


                {{-- =================================================
                VISI
                ================================================== --}}

                <div class="nto-card-vm">

                    <div class="nto-card-vm-header">

                        <div class="nto-card-vm-icon">

                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />

                            </svg>

                        </div>

                        <h2 class="nto-card-vm-title">
                            Visi
                        </h2>

                    </div>


                    <div class="nto-vision-content">
                        "Menjadi lembaga pendidikan dasar yang unggul dalam membentuk generasi bertakwa (Nurture in
                        God), berwawasan luas (Teach with Knowledge), dan berkarakter nasionalis dengan kesiapan global
                        serta kepedulian sesama (Observe in Humanity)."
                    </div>

                    <span class="nto-card-quote-watermark">
                        ”
                    </span>

                </div>


                {{-- =================================================
                MISI
                ================================================== --}}

                <div class="nto-card-vm">

                    <div class="nto-card-vm-header">

                        <div class="nto-card-vm-icon">

                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                <circle cx="12" cy="12" r="9" stroke-width="2" />

                                <circle cx="12" cy="12" r="5" stroke-width="2" />

                                <circle cx="12" cy="12" r="1" fill="currentColor" />

                            </svg>

                        </div>

                        <h2 class="nto-card-vm-title">
                            Misi
                        </h2>

                    </div>


                    <ul class="nto-mission-list">

                        <li class="nto-mission-item">

                            <span class="nto-mission-badge">
                                1
                            </span>

                            <span class="nto-mission-text">
                                Menumbuhkembangkan iman, ketakwaan, akhlak mulia, dan karakter peserta didik melalui
                                pendidikan yang penuh kasih, pembiasaan nilai-nilai spiritualitas, serta keteladanan
                                dalam lingkungan sekolah yang aman dan inklusif.
                            </span>

                        </li>


                        <li class="nto-mission-item">

                            <span class="nto-mission-badge">
                                2
                            </span>

                            <span class="nto-mission-text">
                                Menyelenggarakan pembelajaran berkualitas tinggi yang berpusat pada murid melalui
                                penguatan literasi, numerasi, dan dasar-dasar ilmu pengetahuan untuk mengembangkan
                                kemampuan berpikir kritis, kreatif, inovatif, kolaboratif, dan adaptif.
                            </span>

                        </li>


                        <li class="nto-mission-item">

                            <span class="nto-mission-badge">
                                3
                            </span>

                            <span class="nto-mission-text">
                                Menumbuhkan kepekaan sosial, kepedulian terhadap lingkungan, toleransi, empati, dan rasa
                                hormat terhadap keberagaman melalui pengalaman belajar yang kolaboratif dan kontekstual.
                            </span>

                        </li>


                        <li class="nto-mission-item">

                            <span class="nto-mission-badge">
                                4
                            </span>

                            <span class="nto-mission-text">
                                Menanamkan patriotisme, cinta tanah air, kebanggaan terhadap budaya bangsa, serta
                                penghayatan dan pengamalan nilai-nilai Pancasila dalam kehidupan sehari-hari.
                            </span>

                        </li>


                        <li class="nto-mission-item">

                            <span class="nto-mission-badge">
                                5
                            </span>

                            <span class="nto-mission-text">
                                Memberdayakan peserta didik dengan keterampilan abad ke-21, literasi digital, kecakapan
                                komunikasi, kemampuan berbahasa asing secara kontekstual, serta wawasan global agar
                                mampu beradaptasi dan berkontribusi secara positif dalam masyarakat global.
                            </span>

                        </li>

                    </ul>

                    <span class="nto-card-quote-watermark">
                        ”
                    </span>

                </div>

            </div>


            {{-- =================================================
            LEFT FOOTER
            ================================================== --}}

            <div class="nto-left-footer-row">


                {{-- DOTS --}}

                <div class="nto-dots-grid">

                    @for ($i = 0; $i < 18; $i++)

                        <span></span>

                    @endfor

                </div>


                {{-- LOCATION --}}

                <div class="nto-bottom-location">

                    <svg fill="currentColor" viewBox="0 0 20 20">

                        <path fill-rule="evenodd"
                            d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                            clip-rule="evenodd" />

                    </svg>


                    </span>

                </div>

            </div>


            {{-- =================================================
            ORGANIC CURVED DIVIDER
            ================================================== --}}

            <div class="nto-divider">

                <svg viewBox="0 0 58 800" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">

                    <path class="nto-divider-path" d="M58 0 C12 250 12 550 58 800 L58 0 Z" />

                </svg>

            </div>

        </section>



        {{-- =====================================================
        RIGHT PANEL
        LOGIN FORM
        ====================================================== --}}

        <section class="nto-right">


            {{-- =================================================
            RIGHT BACKGROUND DECORATION
            ================================================== --}}

            <div class="nto-right-decoration">

                <div class="nto-right-circle-1"></div>

                <div class="nto-right-circle-2"></div>

                <div class="nto-right-arc">

                    <svg viewBox="0 0 300 800" fill="none" xmlns="http://www.w3.org/2000/svg">

                        <path d="M170 -30 C310 170 310 630 170 830" stroke="rgba(254, 202, 202, 0.45)"
                            stroke-width="45" />

                        <path d="M235 -30 C350 180 350 620 235 830" stroke="rgba(254, 226, 226, 0.35)"
                            stroke-width="18" />

                    </svg>

                </div>

            </div>


            {{-- =================================================
            LOGIN CARD
            ================================================== --}}

            <div class="nto-login-area">

                <div class="nto-login-card">


                    {{-- RED ACCENT --}}

                    <div class="nto-login-accent"></div>


                    <div class="nto-login-inner">


                        {{-- =================================================
                        USER ICON
                        ================================================== --}}

                        <div class="nto-user-icon-wrapper">

                            <div class="nto-user-icon">

                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0z M12 14a7 7 0 00-7 7h14 a7 7 0 00-7-7z" />

                                </svg>

                            </div>

                        </div>


                        {{-- =================================================
                        LOGIN HEADER
                        ================================================== --}}

                        <div class="nto-login-header">

                            <h2>
                                Selamat Datang Kembali!
                            </h2>

                            <p>
                                Silakan masuk untuk melanjutkan ke portal presensi.
                            </p>

                            <div class="nto-login-header-line"></div>

                        </div>


                        {{-- =================================================
                        SESSION STATUS
                        ================================================== --}}

                        <x-auth-session-status class="mb-3 text-xs" :status="session('status')" />


                        {{-- =================================================
                        LOGIN FORM
                        ================================================== --}}

                        <form method="POST" action="{{ route('login') }}" class="nto-form">

                            @csrf


                            {{-- =================================================
                            EMAIL
                            ================================================== --}}

                            <div class="nto-field">

                                <label for="email">
                                    Alamat Email
                                </label>

                                <div class="nto-input-wrapper">

                                    <svg class="nto-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                            d="M3 8l7.89 5.26 a2 2 0 002.22 0L21 8 M5 19h14a2 2 0 002-2V7 a2 2 0 00-2-2H5 a2 2 0 00-2 2v10 a2 2 0 002 2z" />

                                    </svg>


                                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                        autofocus autocomplete="username" placeholder="Masukkan alamat email"
                                        class="nto-input">

                                </div>


                                <x-input-error :messages="$errors->get('email')"
                                    class="mt-1 text-xs text-rose-600 font-semibold" />

                            </div>


                            {{-- =================================================
                            PASSWORD
                            ================================================== --}}

                            <div class="nto-field" x-data="{ showPassword: false }">

                                <div class="nto-password-label">

                                    <label for="password">
                                        Kata Sandi
                                    </label>


                                    @if (Route::has('password.request'))

                                        <a href="{{ route('password.request') }}" class="nto-forgot">
                                            Lupa kata sandi?
                                        </a>

                                    @endif

                                </div>


                                <div class="nto-input-wrapper">


                                    {{-- PASSWORD ICON --}}

                                    <svg class="nto-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                            d="M12 15v2m-6 4h12 a2 2 0 002-2v-6 a2 2 0 00-2-2H6 a2 2 0 00-2 2v6 a2 2 0 002 2zm10-10V7 a4 4 0 00-8 0v4h8z" />

                                    </svg>


                                    {{-- PASSWORD INPUT --}}

                                    <input id="password" x-bind:type="showPassword ? 'text' : 'password'"
                                        type="password" name="password" required autocomplete="current-password"
                                        placeholder="••••••••" class="nto-input password">


                                    {{-- PASSWORD TOGGLE --}}

                                    <button type="button" id="togglePasswordBtn" class="nto-eye-button"
                                        title="Tampilkan / Sembunyikan Kata Sandi"
                                        @click="showPassword = !showPassword">


                                        {{-- EYE --}}

                                        <svg x-show="!showPassword" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">

                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5 c4.478 0 8.268 2.943 9.542 7 -1.274 4.057 -5.064 7 -9.542 7 -4.477 0 -8.268-2.943 -9.542-7z" />

                                        </svg>


                                        {{-- EYE OFF --}}

                                        <svg x-show="showPassword" x-cloak fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">

                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                d="M13.875 18.825 A10.05 10.05 0 0112 19 c-4.478 0-8.268-2.943 -9.543-7 a9.97 9.97 0 011.563-3.029 m5.858-5.908 a10.02 10.02 0 013.122-.563 c4.478 0 8.268 2.943 9.542 7 a9.97 9.97 0 01-2.43 3.978 m-3.83-3.83 a3 3 0 00-4.243-4.243 M9.878 9.878l4.242 4.242 M9.88 9.88l-3.29-3.29 m7.532 7.532l3.29 3.29 M3 3l18 18" />

                                        </svg>

                                    </button>

                                </div>


                                <x-input-error :messages="$errors->get('password')"
                                    class="mt-1 text-xs text-rose-600 font-semibold" />

                            </div>


                            {{-- =================================================
                            REMEMBER ME
                            ================================================== --}}

                            <div>

                                <label for="remember_me" class="nto-remember">

                                    <input id="remember_me" type="checkbox" name="remember">

                                    <span>
                                        Ingat saya di perangkat ini
                                    </span>

                                </label>

                            </div>


                            {{-- =================================================
                            LOGIN BUTTON
                            ================================================== --}}

                            <div>

                                <button type="submit" class="nto-login-button">

                                    <span>
                                        Masuk Sekarang
                                    </span>


                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3" />

                                    </svg>

                                </button>

                            </div>

                        </form>


                        {{-- =================================================
                        SECURITY NOTICE
                        ================================================== --}}

                        <div class="nto-security">

                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M9 12l2 2 4-4 m5.618-4.016 A11.955 11.955 0 0112 2.944 a11.955 11.955 0 01-8.618 3.04 A12.02 12.02 0 003 9 c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />

                            </svg>


                            <span>
                                Keamanan data kami terjamin dan terenkripsi.
                            </span>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =================================================
            FOOTER
            ================================================== --}}

            <footer class="nto-footer">

                <p>
                    &copy; {{ date('Y') }}
                    <strong>
                        NTO National Plus Primary School
                    </strong>.
                    Hak Cipta Dilindungi.
                </p>

            </footer>

        </section>

    </div>


    {{-- =====================================================
    FALLBACK PASSWORD TOGGLE
    ====================================================== --}}

    <script>

        document.addEventListener('DOMContentLoaded', function () {

            const toggleBtn =
                document.getElementById('togglePasswordBtn');

            const passInput =
                document.getElementById('password');


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
<!DOCTYPE html>

<html lang="th">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>เข้าสู่ระบบ | KYRIX</title>

    <!-- นำเข้าฟอนต์ Prompt จาก Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Prompt', sans-serif;
            background: #faf7f4;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #302a27;
            padding: 20px;
        }

        /* บังคับให้ input และ button ใช้ฟอนต์ Prompt ด้วย (เพื่อความเต็มระบบ) */
        input, button, a {
            font-family: 'Prompt', sans-serif;
        }

        .box {
            width: 420px;
            max-width: 100%;
            background: #fff;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, .08);
        }

        .logo {
            text-align: center;
            font-size: 32px;
            font-weight: 800;
            color: #7a1f2b;
            margin-bottom: 8px;
        }

        .subtitle {
            text-align: center;
            color: #777;
            margin-bottom: 30px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 15px;
        }

        input:focus {
            outline: none;
            border-color: #7a1f2b;
        }

        .btn {
            width: 100%;
            padding: 14px;
            border: 0;
            border-radius: 10px;
            background: #7a1f2b;
            color: #fff;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: .2s;
        }

        .btn:hover {
            background: #601722;
        }

        .link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            line-height: 1.8;
        }

        .link a {
            color: #7a1f2b;
            text-decoration: none;
            font-weight: 600;
        }

        .link a:hover {
            text-decoration: underline;
        }

        .error {
            background: #fdf0f1;
            color: #a32738;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 14px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            cursor: pointer;
        }

        .remember input {
            width: auto;
            margin: 0;
            cursor: pointer;
        }

        .social-login {
            margin-top: 24px;
        }

        .social-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #999;
            font-size: 13px;
            margin: 22px 0;
        }

        .social-divider::before,
        .social-divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #e5e5e5;
        }

        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background: #fff;
            color: #302a27;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 12px;
            transition: .2s;
        }

        .social-btn:hover {
            background: #fafafa;
            border-color: #bbb;
        }

        .social-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            font-size: 18px;
            font-weight: 700;
        }

        .google-icon {
            color: #4285F4;
        }

        @media (max-width: 480px) {
            .box {
                padding: 28px 22px;
                border-radius: 18px;
            }
        }
    </style>

</head>

<body>

    <div class="box">

        <div class="logo">KYRIX</div>

        <div class="subtitle">
            เข้าสู่ระบบร้านเช่าชุด
        </div>

        @if ($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Login ด้วยอีเมลและรหัสผ่าน --}}

        <form method="POST" action="{{ route('login.submit') }}">

            @csrf

            <label for="email">อีเมล</label>

            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="กรอกอีเมล"
                required autocomplete="email">

            <label for="password">รหัสผ่าน</label>

            <input type="password" id="password" name="password" placeholder="กรอกรหัสผ่าน" required
                autocomplete="current-password">

            <div
                style="
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 18px;
                font-size: 14px;
                gap: 10px;
            ">

                <label class="remember">

                    <input type="checkbox" name="remember" value="1">

                    <span>จดจำการเข้าสู่ระบบ</span>

                </label>

                <a href="{{ route('password.request') }}"
                    style="
                        color: #7a1f2b;
                        text-decoration: none;
                        font-weight: 600;
                        white-space: nowrap;
                    ">
                    ลืมรหัสผ่าน?
                </a>

            </div>

            <button type="submit" class="btn">
                เข้าสู่ระบบ
            </button>

        </form>

        {{-- Social Login --}}

        <div class="social-login">

            <div class="social-divider">
                หรือเข้าสู่ระบบด้วย
            </div>

            {{-- Google --}}

            <a href="{{ route('social.redirect', 'google') }}" class="social-btn">
                <span class="social-icon google-icon">G</span>
                <span>เข้าสู่ระบบด้วย Google</span>
            </a>

        </div>

        {{-- Links --}}

        <div class="link">

            ยังไม่มีบัญชี?

            <a href="{{ route('register') }}">สมัครสมาชิก</a>

            |

            <a href="{{ route('home') }}">กลับหน้าหลัก</a>

        </div>

    </div>

</body>

</html>
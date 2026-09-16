<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>สมัครสมาชิก | KYRIX</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        body {
            font-family: Arial, "Tahoma", sans-serif;
            background: #faf7f4;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px
        }

        .box {
            width: 480px;
            background: #fff;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, .08)
        }

        .logo {
            text-align: center;
            font-size: 32px;
            font-weight: 800;
            color: #7a1f2b;
            margin-bottom: 8px
        }

        .subtitle {
            text-align: center;
            color: #777;
            margin-bottom: 28px
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 7px
        }

        input,
        textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin-bottom: 16px;
            font-size: 15px;
            font-family: inherit
        }

        textarea {
            height: 90px;
            resize: none
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: #7a1f2b
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
            cursor: pointer
        }

        .link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px
        }

        .link a {
            color: #7a1f2b;
            text-decoration: none;
            font-weight: 600
        }

        .error {
            background: #fdf0f1;
            color: #a32738;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 14px
        }

        .social-login {
            margin-top: 24px;
        }

        .social-divider {
            display: flex;
            align-items: center;
            text-align: center;
            color: #888;
            font-size: 13px;
            margin: 20px 0;
        }

        .social-divider::before,
        .social-divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #e5e5e5;
        }

        .social-divider span {
            padding: 0 10px;
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
    </style>
</head>

<body>
    <div class="box">
        <div class="logo">KYRIX</div>
        <div class="subtitle">สมัครสมาชิกเพื่อเช่าชุด</div>
        @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif
        <form method="POST" action="{{ route('register.submit') }}">
            @csrf
            <label>ชื่อ - นามสกุล</label>
            <input type="text" name="name" value="{{ old('name') }}" placeholder="กรอกชื่อ - นามสกุล" required>
            <label>อีเมล</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="example@email.com" required>
            <label>เบอร์โทรศัพท์</label>
            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="กรอกเบอร์โทรศัพท์">
            <label>ที่อยู่</label>
            <textarea name="address" placeholder="กรอกที่อยู่">{{ old('address') }}</textarea>
            <label>รหัสผ่าน</label>
            <input type="password" name="password" placeholder="อย่างน้อย 6 ตัวอักษร" required>
            <label>ยืนยันรหัสผ่าน</label>
            <input type="password" name="password_confirmation" placeholder="กรอกรหัสผ่านอีกครั้ง" required>
            <button type="submit" class="btn">สมัครสมาชิก</button>
        </form>

        {{-- Social Register --}}
        <div class="social-login">
            <div class="social-divider">
                <span>หรือสมัครสมาชิกด้วย</span>
            </div>

            <a
                href="{{ route('social.redirect', ['provider' => 'google']) }}"
                class="social-btn"
            >
                <span class="social-icon google-icon">G</span>
                <span>สมัครสมาชิกด้วย Google</span>
            </a>
        </div>

        <div class="link">มีบัญชีแล้ว? <a href="{{ route('login') }}">เข้าสู่ระบบ</a></div>
    </div>
</body>

</html>

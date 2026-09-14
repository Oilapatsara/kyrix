<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>ลืมรหัสผ่าน / ตั้งรหัสผ่านใหม่ | KYRIX</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Prompt:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Prompt', 'Plus Jakarta Sans', sans-serif;
            background: #faf7f4;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #302a27;
            padding: 20px;
        }
        .box {
            width: 440px;
            background: #fff;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, .08);
            border: 1px solid #ede8e3;
        }
        .logo {
            text-align: center;
            font-size: 32px;
            font-weight: 800;
            color: #7a1f2b;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }
        .subtitle {
            text-align: center;
            color: #777;
            margin-bottom: 26px;
            font-size: 14px;
        }
        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 6px;
        }
        input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin-bottom: 16px;
            font-size: 14px;
            font-family: inherit;
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
            transition: all 0.2s;
        }
        .btn:hover {
            background: #58141d;
        }
        .link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
        .link a {
            color: #7a1f2b;
            text-decoration: none;
            font-weight: 600;
        }
        .error {
            background: #fdf0f1;
            color: #a32738;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="box">
        <div class="logo">KYRIX</div>
        <div class="subtitle">ตั้งค่ารหัสผ่านใหม่ (Reset Password)</div>

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.reset.submit') }}">
            @csrf
            <label>อีเมลบัญชีของคุณ</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="กรอกอีเมลที่ใช้สมัคร" required>

            <label>รหัสผ่านใหม่</label>
            <input type="password" name="new_password" placeholder="รหัสผ่านใหม่ (อย่างน้อย 6 ตัวอักษร)" required>

            <label>ยืนยันรหัสผ่านใหม่อีกครั้ง</label>
            <input type="password" name="new_password_confirmation" placeholder="กรอกรหัสผ่านใหม่อีกครั้ง" required>

            <button type="submit" class="btn">ตั้งรหัสผ่านใหม่</button>
        </form>

        <div class="link">
            จำรหัสผ่านได้แล้ว? <a href="{{ route('login') }}">เข้าสู่ระบบ</a>
        </div>
    </div>
</body>
</html>

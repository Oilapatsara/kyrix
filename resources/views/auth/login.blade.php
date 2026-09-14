<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>เข้าสู่ระบบ | KYRIX</title>
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
            color: #302a27
        }

        .box {
            width: 420px;
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
            margin-bottom: 30px
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px
        }

        input {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 15px
        }

        input:focus {
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

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 18px;
            font-size: 14px
        }

        .remember input {
            width: auto;
            margin: 0
        }
    </style>
</head>

<body>
    <div class="box">
        <div class="logo">KYRIX</div>
        <div class="subtitle">เข้าสู่ระบบร้านเช่าชุด</div>
        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            <label>อีเมล</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="กรอกอีเมล" required>
            <label>รหัสผ่าน</label>
            <input type="password" name="password" placeholder="กรอกรหัสผ่าน" required>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; font-size: 14px;">
                <label class="remember" style="margin-bottom: 0;">
                    <input type="checkbox" name="remember" value="1">
                    จดจำการเข้าสู่ระบบ
                </label>
                <a href="{{ route('password.request') }}" style="color: #7a1f2b; text-decoration: none; font-weight: 600;">ลืมรหัสผ่าน?</a>
            </div>
            <button type="submit" class="btn">เข้าสู่ระบบ</button>
        </form>
        <div class="link">ยังไม่มีบัญชี? <a href="{{ route('register') }}">สมัครสมาชิก</a> | <a href="{{ route('home') }}">กลับหน้าหลัก</a></div>
    </div>
</body>

</html>

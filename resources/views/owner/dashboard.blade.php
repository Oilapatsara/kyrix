<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Dashboard เจ้าของร้าน | KYRIX</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, "Tahoma", sans-serif;
            background: #faf7f4;
            color: #302a27
        }

        .header {
            background: #7a1f2b;
            color: #fff;
            padding: 18px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center
        }

        .logo {
            font-size: 26px;
            font-weight: 800
        }

        .logout {
            background: #fff;
            color: #7a1f2b;
            border: 0;
            padding: 10px 18px;
            border-radius: 9px;
            cursor: pointer
        }

        .container {
            max-width: 1150px;
            margin: 40px auto;
            padding: 0 25px
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px
        }

        .card {
            background: #fff;
            border-radius: 18px;
            padding: 25px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, .05)
        }

        .card h3 {
            margin-top: 0
        }

        .number {
            font-size: 30px;
            font-weight: 800;
            color: #7a1f2b
        }

        @media(max-width:900px) {
            .grid {
                grid-template-columns: repeat(2, 1fr)
            }
        }

        @media(max-width:600px) {
            .grid {
                grid-template-columns: 1fr
            }
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="logo">KYRIX OWNER</div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="logout">ออกจากระบบ</button>
        </form>
    </header>
    <div class="container">
        <h1>Dashboard เจ้าของร้าน</h1>
        <p>ยินดีต้อนรับ {{ auth()->user()->name }}</p>
        <div class="grid">
            <div class="card">
                <h3>ชุดทั้งหมด</h3>
                <div class="number">0</div>
            </div>
            <div class="card">
                <h3>รายการเช่า</h3>
                <div class="number">0</div>
            </div>
            <div class="card">
                <h3>รอตรวจสอบ</h3>
                <div class="number">0</div>
            </div>
            <div class="card">
                <h3>ลูกค้า</h3>
                <div class="number">0</div>
            </div>
        </div>
    </div>
</body>

</html>

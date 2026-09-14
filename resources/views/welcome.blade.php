<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KYRIX | ร้านเช่าชุด</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box
        }

        body {
            font-family: Arial, "Tahoma", sans-serif;
            background: #faf8f5;
            color: #302a27
        }

        .header {
            height: 70px;
            background: #fff;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 60px
        }

        .logo {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 1px;
            color: #7a1f2b
        }

        .nav {
            display: flex;
            gap: 28px
        }

        .nav a {
            text-decoration: none;
            color: #444;
            font-size: 15px
        }

        .nav a:hover {
            color: #7a1f2b
        }

        .hero {
            min-height: calc(100vh - 70px);
            display: flex;
            align-items: center;
            padding: 70px 10%;
            background: linear-gradient(135deg, #f8efe9, #fff)
        }

        .hero-content {
            max-width: 650px
        }

        .tag {
            display: inline-block;
            padding: 8px 16px;
            background: #7a1f2b;
            color: #fff;
            border-radius: 30px;
            font-size: 13px;
            margin-bottom: 22px
        }

        h1 {
            font-size: 58px;
            line-height: 1.1;
            margin-bottom: 20px
        }

        h1 span {
            color: #7a1f2b
        }

        .hero p {
            font-size: 18px;
            line-height: 1.8;
            color: #6b625d;
            margin-bottom: 30px
        }

        .buttons {
            display: flex;
            gap: 14px
        }

        .btn {
            display: inline-block;
            padding: 14px 28px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600
        }

        .btn-primary {
            background: #7a1f2b;
            color: #fff
        }

        .btn-secondary {
            border: 1px solid #7a1f2b;
            color: #7a1f2b;
            background: #fff
        }

        .hero-card {
            margin-left: auto;
            width: 380px;
            height: 450px;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, .08);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #aaa;
            font-size: 18px
        }

        @media(max-width:900px) {
            .header {
                padding: 0 20px
            }

            .nav {
                display: none
            }

            .hero {
                padding: 50px 25px
            }

            h1 {
                font-size: 42px
            }

            .hero-card {
                display: none
            }
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="logo">KYRIX</div>
        <nav class="nav">
            <a href="/">หน้าแรก</a>
            <a href="#">ชุดทั้งหมด</a>
            <a href="#">ประเภทชุด</a>
            <a href="#">วิธีการเช่า</a>
            <a href="#">ติดต่อเรา</a>
        </nav>
        <div>
            <a href="#" class="btn btn-secondary">เข้าสู่ระบบ</a>
        </div>
    </header>
    <section class="hero">
        <div class="hero-content">
            <div class="tag">KYRIX DRESS RENTAL</div>
            <h1>เช่าชุดสวย<br><span>สำหรับวันพิเศษ</span></h1>
            <p>เลือกชุดที่คุณชอบ จองง่าย ตรวจสอบสถานะการเช่า และจัดการทุกขั้นตอนได้ในระบบเดียว</p>
            <div class="buttons">
                <a href="#" class="btn btn-primary">เลือกชุดของเรา</a>
                <a href="#" class="btn btn-secondary">ดูวิธีการเช่า</a>
            </div>
        </div>
        <div class="hero-card">พื้นที่สำหรับรูปชุด</div>
    </section>
</body>

</html>

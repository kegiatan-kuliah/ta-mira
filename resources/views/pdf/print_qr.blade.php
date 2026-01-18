<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Absensi</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, sans-serif;
            background: #f5f5f5;
        }

        .page {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            width: 360px;
            height: 540px;
            background: white;
            border-radius: 18px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,.15);
            text-align: center;
        }

        /* ===== ORNAMEN ===== */
        .shape-top-left {
            position: absolute;
            width: 140px;
            height: 140px;
            background: #ff7aa2;
            border-bottom-right-radius: 100%;
            top: -40px;
            left: -40px;
        }

        .shape-top-right {
            position: absolute;
            width: 140px;
            height: 140px;
            background: #4fd1c5;
            border-bottom-left-radius: 100%;
            top: -40px;
            right: -40px;
        }

        .shape-bottom-left {
            position: absolute;
            width: 160px;
            height: 160px;
            background: #4fd1c5;
            border-top-right-radius: 100%;
            bottom: -50px;
            left: -50px;
        }

        .shape-bottom-right {
            position: absolute;
            width: 160px;
            height: 160px;
            background: #ff7aa2;
            border-top-left-radius: 100%;
            bottom: -50px;
            right: -50px;
        }

        /* ===== CONTENT ===== */
        .content {
            position: relative;
            z-index: 2;
            padding: 40px 24px;
        }

        .school {
            font-weight: bold;
            font-size: 16px;
            letter-spacing: .5px;
        }

        .title {
            margin-top: 30px;
            font-size: 26px;
            font-weight: bold;
        }

        .name {
            margin-top: 6px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: .6px;
        }

        .qr-box {
            margin: 40px auto 20px;
            width: 220px;
            height: 220px;
            background: #e6f7f5;
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .footer {
            position: absolute;
            bottom: 16px;
            width: 100%;
            font-size: 10px;
            color: #777;
        }
    </style>
</head>
<body>

<div class="page">
    <div class="card">

        <!-- ORNAMEN -->
        <div class="shape-top-left"></div>
        <div class="shape-top-right"></div>
        <div class="shape-bottom-left"></div>
        <div class="shape-bottom-right"></div>

        <!-- CONTENT -->
        <div class="content">
            <div class="school">
                SMK CITRA UTAMA<br>
                PADANG
            </div>

            <div class="title">KARTU ABSENSI</div>

            <div class="name">
                {{ $siswa->nama ?? 'NAMA SISWA' }}
            </div>

            <div class="qr-box">
                <img src="{{ $siswa->qr_code }}" width="220" height="220">
            </div>
        </div>

        <div class="footer">
            Scan QR untuk melakukan absensi
        </div>

    </div>
</div>

</body>
</html>

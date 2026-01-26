<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAPORAN ABSEN SISWA</title>
    <style>
        body {
            font-family: sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th {
            background-color: #e0e0e0;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .signature {
            float: right;
            margin-top: 40px;
        }
        
    </style>
    @php
        use Carbon\Carbon;

        Carbon::setLocale('id');
    @endphp
</head>

<body>
    <div>
        <img src="{{ public_path('logo-smk-citra.png') }}" alt="" width="70px" height="70px" style="position: absolute; top: 5px;">
        <h3 style="margin-bottom: 0px; text-align: center;">SMK CITRA UTAMA PADANG</h3>
        {{-- <h1 style="margin-top: 0px; margin-bottom: 0px; text-align: center;">SMK CITRA UTAMA PADANG</h1> --}}
        
        <p style="margin-bottom: 0px; margin-top: 0px; text-align: center;">Jl. Seberang Padang Utara II No.27, Seberang Padang</p>
        <p style="margin-bottom: 0px; margin-top: 0px; text-align: center;">Kec. Padang Selatan, Kota Padang, Sumatera Barat 25214, Indonesia</p>
        <hr>
    </div>
    <h2 style="text-align: center;">LAPORAN ABSEN SISWA</h2>
    <p style="margin-bottom: 0px; margin-top: 0px; text-align: center;">Periode: {{ $from }} s/d {{ $until }}</p>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>NIS</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Mapel</th>
                <th>Jam</th>
                <th>Jam Absen</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    <td>{{ $row->tanggal }}</td>
                    <td>{{ $row->nis }}</td>
                    <td>{{ $row->nama }}</td>
                    <td>{{ $row->kelas }}</td>
                    <td>{{ $row->mata_pelajaran }}</td>
                    <td>{{ substr($row->jam_mulai,0,5) }} - {{ substr($row->jam_selesai,0,5) }}</td>
                    <td>{{ $row->absen_jam ? substr($row->absen_jam,0,5) : '-' }}</td>
                    <td>{{ $row->status_absen }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature">
        {{ Carbon::now()->translatedFormat('d F Y')}}<br>
        <strong>Kepala Sekolah</strong></p>
        <br>
        <br>
        <br>
        <p><strong>Deswi Yeviatni, SE</strong><br>
        NUKS: 18023L0010861142039102</p>
    </div>
</body>

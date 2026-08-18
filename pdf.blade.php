<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Serah Terima</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 16px;
        }
        .container {
            width: 210mm;
            height: 297mm;
            padding: 20mm;
            margin: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background: white;
            position: relative;
        }
        .header {
            position: relative;
            padding-bottom: 10px;
            border-bottom: 1px solid black; /* Border line below header */
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border: none !important;
            margin-bottom: 0;
        }

        .header-table td {
            border: none !important;
            vertical-align: middle;
            padding: 0;
        }

        .logo-td {
            width: 80px;
            text-align: left;
        }

        .logo-img {
            width: 80px;
            height: auto;
        }

        .text-td {
            text-align: center;
        }

        .spacer-td {
            width: 80px;
        }

        .header-text h2 {
            margin: 2px 0;
            font-size: 18px;
            line-height: 1.2;
        }
        .content {
            margin-top: 20px; /* Add some space above content */
        }
        .content p {
            margin: 10px 0;
        }
        .data-section {
            margin-top: 20px;
        }
        .data-section h4 {
            margin: 0 0 10px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .data-table td {
            padding: 10px 0; /* Increased padding */
            vertical-align: top;
            font-size: 16px; /* Increased font size for table */
        }
        .data-table .label {
            width: 150px;
            font-weight: bold;
        }
        .data-table .separator {
            width: 10px;
            text-align: center;
        }
        .data-table tr {
            border: none;
        }
        .data-table td, .data-table th {
            border: none;
        }
        .data-table {
            border: none;
        }
        .signature {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature div {
            width: 45%;
            text-align: center;
        }
        .signature div p {
            font-weight: bold; /* Bold for names */
            font-size: 18px; /* Increased font size for names */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        .footer {
            display: flex;
            justify-content: left; /* Align logo to the left */
            margin-top: 40px; /* Optional spacing above footer */
            border-top: 1px solid black; /* Border line at the top of footer */
            padding-top: 10px; /* Padding for spacing */
        }
        .footer img {
            width: 450px; /* Adjust size as needed */
            height: auto; /* Maintain aspect ratio */
        }

        .qrcode-section {
            margin-top: 30px;
            display: table;
            width: auto;
            border: 1px solid #ccc;
            padding: 10px;
            background-color: #f9f9f9;
        }

        .qrcode-section-table {
            border: none !important;
        }

        .qrcode-section-table td {
            border: none !important;
            vertical-align: middle;
            padding: 5px 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="logo-td">
                        <img class="logo-img" src="{{ asset('assets/default/web/default.png') }}" alt="Default">
                    </td>
                    <td class="text-td">
                        <div class="header-text">
                            <h2>BERITA ACARA SERAH TERIMA</h2>
                            <h2>PTPN IV REGIONAL III</h2>
                        </div>
                    </td>
                    <td class="spacer-td"></td>
                </tr>
            </table>
        </div>

<div class="content">
            <p>Pada hari ini {{ $hari }}, tanggal {{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}, kami yang bertandatangan di bawah ini:</p>

            <div class="data-section">
                <h4>I. Data Pihak Pertama</h4>
                <table class="data-table">
                    <tr>
                        <td class="label">Nama</td>
                        <td class="separator">:</td>
                        <td>{{ $namaPihakPertama }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jabatan</td>
                        <td class="separator">:</td>
                        <td>{{ $jabatanPihakPertama }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nomor HP</td>
                        <td class="separator">:</td>
                        <td>{{ $teleponPihakPertama }}</td>
                    </tr>
                    <tr>
                        <td class="label">Bagian
                        </td>
                        <td class="separator">:</td>
                        <td>{{ $unitPihakPertama }}</td>
                    </tr>
                </table>
            </div>

            <p>Dalam hal ini disebut sebagai PIHAK PERTAMA (yang menyerahkan) </p>

            <div class="data-section">
                <h4>II. Data Pihak Kedua</h4>
                <table class="data-table">
                    <tr>
                        <td class="label">Nama</td>
                        <td class="separator">:</td>
                        <td>{{ $namaPihakKedua }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jabatan</td>
                        <td class="separator">:</td>
                        <td>{{ $jabatanPihakKedua }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nomor HP</td>
                        <td class="separator">:</td>
                        <td>{{ $teleponPihakKedua }}</td>
                    </tr>
                    <tr>
                        <td class="label">Bagian</td>
                        <td class="separator">:</td>
                        <td>{{ $unitPihakKedua }}</td>
                    </tr>
                </table>
            </div>

            <p>Dalam hal ini disebut sebagai PIHAK KEDUA (yang menerima).</p>

            <p>Dengan ini menyatakan bahwa PIHAK PERTAMA telah menyerahkan kepada PIHAK KEDUA dan PIHAK KEDUA telah menerima perangkat sebagai berikut:</p>

            <table>
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th>Type</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($barang as $item)
                        <tr>
                            <td>{{ $item['namaBarang'] }}</td>
                            <td>{{ $item['typeBarang'] }}</td>
                            <td>{{ $item['jumlahBarang'] }}</td>
                            <td>{{ $item['keteranganBarang'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <p>Sejak penandatanganan berita acara ini, maka barang tersebut menjadi tanggung jawab PIHAK KEDUA untuk dipergunakan sebagai alat kerja.</p>

            <div class="signature">
                <div>
                    <p>PIHAK PERTAMA</p>
                    <br><br><br>
                    <p>{{ $namaPihakPertama }}</p>
                </div>
                <div>
                    <p>PIHAK KEDUA</p>
                    <br><br><br>
                    <p>{{ $namaPihakKedua }}</p>
                </div>
            </div>
        </div>

<script>
            window.onload = function() {
                window.print();
            };
        </script>

        @if (isset($foto_bukti_url) && $foto_bukti_url)
            <div class="qrcode-section">
                <table class="qrcode-section-table" style="border: none;">
                    <tr style="border: none;">
                        <td style="padding-right: 15px; border: none;">
                            <img src="https://chart.googleapis.com/chart?chs=100x100&cht=qr&chl={{ urlencode($foto_bukti_url) }}&choe=UTF-8"
                                alt="QR Code Bukti Foto" style="width: 100px; height: 100px;">
                        </td>
                        <td style="border: none;">
                            <p style="font-weight: bold; font-size: 14px; margin-bottom: 5px; margin-top: 0;">QR Code Bukti
                                Foto</p>
                            <p style="font-size: 11px; color: #555; line-height: 1.4; margin: 0;">Scan QR Code ini untuk
                                melihat bukti<br>foto fisik serah terima barang secara online.</p>
                        </td>
                    </tr>
                </table>
            </div>
        @endif

        <div class="footer">
            <img src="{{ (isset($is_pdf) && $is_pdf) ? public_path('assets/default/web/akhlak.png') : asset('assets/default/web/akhlak.png') }}"
                alt="akhlak">
        </div>

    </div>
</body>
</html>


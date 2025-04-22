<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Jalan Manual Barang Keluar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header img {
            max-width: 150px;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        .signature-section {
            margin-top: 50px;
        }
        .signature-box {
            margin-top: 60px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/unsada.jpg') }}" alt="Logo">
        <h1>SURAT JALAN BARANG KELUAR</h1>
    </div>

    <div class="info-section">
        <p>No. Surat : ___________________________</p>
        <p>Tanggal : ___________________________</p>
        <p>Nama Barang : ___________________________</p>
        <p>Deskripsi : ___________________________</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @for($i = 1; $i <= 5; $i++)
            <tr>
                <td style="height: 25px">{{ $i }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endfor
        </tbody>
    </table>

    <div class="signature-section">
        <table style="border: none; width: 100%">
            <tr style="border: none;">
                <td style="border: none; width: 33%; text-align: center">
                    Dibuat Oleh,
                    <div class="signature-box">
                        ________________<br>
                        Admin
                    </div>
                </td>
                <td style="border: none; width: 33%; text-align: center">
                    Diperiksa Oleh,
                    <div class="signature-box">
                        ________________<br>
                        Supervisor
                    </div>
                </td>
                <td style="border: none; width: 33%; text-align: center">
                    Diterima Oleh,
                    <div class="signature-box">
                        ________________<br>
                        Customer
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html> 
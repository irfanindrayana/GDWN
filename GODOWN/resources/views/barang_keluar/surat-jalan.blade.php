<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Barang Keluar</title>
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
        <!-- <img src="{{ Storage::url('gambar/logo.png') }}" alt="Logo Godown"> -->
        <img src="img/logo.png" alt="Logo Godown">
        <h1>SURAT JALAN BARANG KELUAR</h1>
    </div>

    <div class="info-section">
        <p>No. Surat : {{ $nomorSurat }}</p>
        <p>Tanggal : {{ $tanggal }}</p>
        <p>Nama Barang : {{ $barangKeluar->nama_barang }}</p>
        <p>Deskripsi : {{ $barangKeluar->deskripsi }}</p>
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
            <tr>
                <td>1</td>
                <td>{{ $barangKeluar->nama_barang }}</td>
                <td>{{ $barangKeluar->jumlah }}</td>
                <td>Pcs</td>
                <td>{{ $barangKeluar->deskripsi }}</td>
            </tr>
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
                        {{ $barangKeluar->customer->nama ?? 'Customer' }}
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html> 
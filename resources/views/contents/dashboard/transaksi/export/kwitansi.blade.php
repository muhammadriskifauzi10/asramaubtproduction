<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kwitansi {{ $transaksi->no_transaksi }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            margin: 0;
            padding: 25px;
            color: #000;
        }

        .kwitansi-box {
            border: 2px solid #000;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header img {
            height: 60px;
            display: block;
            margin: 0 auto 5px auto;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }

        .header h2 {
            margin: 3px 0 0;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .dashed {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .info-table td:first-child {
            width: 30%;
        }

        .rincian-title {
            font-weight: bold;
            margin: 10px 0 5px 0;
        }

        .rincian-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        .rincian-table th {
            border: 1px solid #000;
            padding: 4px 8px;
            text-align: left;
            background: #f0f0f0;
        }

        .rincian-table td {
            border: 1px solid #000;
            padding: 4px 8px;
        }

        .rincian-table td:last-child {
            text-align: right;
        }

        .amount-box {
            border: 1px solid #000;
            padding: 8px;
            font-weight: bold;
            margin-top: 10px;
        }

        .terbilang-box {
            border: 1px dashed #000;
            padding: 8px;
            margin-top: 8px;
            font-style: italic;
        }

        .total {
            font-size: 16px;
            font-weight: bold;
            text-align: right;
            margin-top: 8px;
        }

        .footer-left {
            font-size: 10px;
        }

        .footer-right {
            text-align: center;
            width: 200px;
        }

        .ttd-line {
            border-top: 1px solid #000;
            margin-top: 40px;
        }
    </style>
</head>

<body>

    <div class="kwitansi-box">

        {{-- HEADER --}}
        <div class="header">
            <img src="{{ public_path('images/ubt-logo.webp') }}" alt="Logo UBT">
            <h1>UNIVERSITAS BUNDA THAMRIN</h1>
            <h2>KWITANSI</h2>
        </div>

        <div class="dashed"></div>

        {{-- INFORMASI --}}
        <table class="info-table">
            <tr>
                <td>No. Kwitansi</td>
                <td>: {{ $transaksi->no_transaksi }}</td>
            </tr>
            <tr>
                <td>Nama Mahasiswa</td>
                <td>: {{ $tagihan->penyewa->namalengkap }}</td>
            </tr>
            <tr>
                <td>NIM</td>
                <td>: {{ $tagihan->penyewa->nim }}</td>
            </tr>
            <tr>
                <td>Tahun Akademik</td>
                <td>: {{ $tagihan->penyewa->angkatan }}</td>
            </tr>
            <tr>
                <td>Periode</td>
                <td>: {{ \Carbon\Carbon::parse($tagihan->tanggal_masuk)->format('d-m-Y') }} -
                    {{ \Carbon\Carbon::parse($tagihan->keluar)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td>Tanggal Tagihan</td>
                <td>: {{ \Carbon\Carbon::parse($tagihan->created_at)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td>: Pembayaran No. Tagihan {{ $transaksi->no_invoice }}</td>
            </tr>
        </table>

        {{-- JUMLAH --}}
        <div class="amount-box">
            Total Bayar
            <div class="total">
                Rp {{ number_format($transaksi->jumlah_uang, 0, ',', '.') }}
            </div>
        </div>

        {{-- TERBILANG --}}
        <div class="terbilang-box">
            Terbilang: {{ $terbilang }} Rupiah
        </div>

        <div class="dashed"></div>

        {{-- FOOTER --}}
        <div style="margin-top:40px; display:flex; justify-content:space-between; align-items:flex-start;">

            {{-- Kiri: Tanggal Cetak --}}
            <div class="footer-left">
                Dicetak pada: {{ now()->format('d-m-Y H:i') }}
            </div>

            {{-- Kanan: Operator / TTD --}}
            <div class="footer-right" style="text-align:center; width:200px;">
                Dibuat oleh,<br><br><br><br><br><br><br>

                {{-- Garis tanda tangan --}}
                <div class="ttd-line" style="border-top:1px solid #000; margin:5px 0;"></div>

                {{-- Nama & NIP --}}
                {{ $transaksi->user->name ?? '-' }}
            </div>
        </div>

    </div>

</body>

</html>

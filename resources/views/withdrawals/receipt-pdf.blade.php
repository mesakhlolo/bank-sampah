<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Bukti Penarikan - {{ $withdrawal->code }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }

        .header h1 {
            font-size: 24px;
            margin: 0 0 10px 0;
            font-weight: bold;
        }

        .header p {
            font-size: 14px;
            margin: 0;
            color: #666;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            width: 30%;
            padding: 5px 0;
            font-weight: bold;
        }

        .info-value {
            display: table-cell;
            padding: 5px 0;
        }

        .section-title {
            font-weight: bold;
            font-size: 13px;
            margin: 20px 0 10px 0;
            padding: 5px;
            background-color: #f5f5f5;
            border-left: 4px solid #dc2626;
        }

        .customer-box {
            background-color: #f9f9f9;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }

        .amount-box {
            background-color: #fee2e2;
            border: 2px solid #dc2626;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }

        .amount-box .label {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .amount-box .value {
            font-size: 28px;
            font-weight: bold;
            color: #dc2626;
        }

        .note-box {
            background-color: #fef3c7;
            border: 1px solid #fbbf24;
            padding: 10px;
            margin: 20px 0;
        }

        .signatures {
            margin-top: 50px;
            display: table;
            width: 100%;
        }

        .signature {
            display: table-cell;
            width: 50%;
            text-align: center;
        }

        .signature-line {
            margin-top: 70px;
            padding-top: 2px;
            border-top: 1px solid #333;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h1>BUKTI PENARIKAN</h1>
        <p>Bank Sampah</p>
    </div>

    <!-- Transaction Info -->
    <div class="info-grid">
        <div class="info-row">
            <div class="info-label">Kode Penarikan</div>
            <div class="info-value">: {{ $withdrawal->code }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal Transaksi</div>
            <div class="info-value">: {{ $withdrawal->transaction_date->format('d F Y') }}</div>
        </div>
    </div>

    <!-- Customer Info -->
    <div class="section-title">INFORMASI NASABAH</div>
    <div class="customer-box">
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Kode Nasabah</div>
                <div class="info-value">: {{ $withdrawal->customer->code }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Nama</div>
                <div class="info-value">: {{ $withdrawal->customer->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">No. Telepon</div>
                <div class="info-value">: {{ $withdrawal->customer->phone }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Alamat</div>
                <div class="info-value">: {{ $withdrawal->customer->address }}</div>
            </div>
        </div>
    </div>

    <!-- Amount -->
    <div class="amount-box">
        <div class="label">NOMINAL PENARIKAN</div>
        <div class="value">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</div>
    </div>

    <!-- Note -->
    @if ($withdrawal->note)
        <div class="note-box">
            <strong>Catatan:</strong><br>
            {{ $withdrawal->note }}
        </div>
    @endif

    <!-- Creator Info -->
    <div class="info-grid" style="margin-top: 20px;">
        <div class="info-row">
            <div class="info-label">Dibuat Oleh</div>
            <div class="info-value">: {{ $withdrawal->creator->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Waktu Dibuat</div>
            <div class="info-value">: {{ $withdrawal->created_at->format('d F Y H:i') }}</div>
        </div>
    </div>

    <!-- Signatures -->
    <div class="signatures">
        <div class="signature">
            <div>Nasabah</div>
            <div class="signature-line">{{ $withdrawal->customer->name }}</div>
        </div>
        <div class="signature">
            <div>Petugas</div>
            <div class="signature-line">{{ $withdrawal->creator->name }}</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Dicetak pada {{ now()->format('d F Y H:i') }}</p>
        <p>Dokumen ini sah dan diproses oleh sistem Bank Sampah</p>
    </div>
</body>

</html>

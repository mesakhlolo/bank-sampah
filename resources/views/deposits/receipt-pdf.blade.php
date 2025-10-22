<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Struk Setoran - {{ $deposit->code }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0;
            color: #666;
        }

        .code {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
            margin-top: 10px;
        }

        .info-section {
            margin-bottom: 20px;
        }

        .info-section h3 {
            font-size: 11px;
            font-weight: bold;
            color: #666;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .info-row {
            margin-bottom: 5px;
        }

        .info-label {
            display: inline-block;
            width: 120px;
            color: #666;
        }

        .info-value {
            font-weight: 500;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        thead {
            background-color: #f3f4f6;
        }

        th {
            padding: 10px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            color: #666;
            border-bottom: 2px solid #ddd;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .text-right {
            text-align: right;
        }

        .font-medium {
            font-weight: 500;
        }

        tfoot {
            background-color: #f3f4f6;
            font-weight: bold;
        }

        tfoot td {
            padding: 15px 10px;
            border-top: 2px solid #333;
        }

        .total-amount {
            font-size: 16px;
            color: #2563eb;
        }

        .notes {
            background-color: #f9fafb;
            padding: 15px;
            margin: 20px 0;
            border-left: 3px solid #2563eb;
        }

        .notes h3 {
            font-size: 11px;
            font-weight: bold;
            color: #666;
            margin: 0 0 8px 0;
            text-transform: uppercase;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            background-color: #d1fae5;
            color: #065f46;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .grid-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 15px;
        }

        hr {
            border: none;
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h1>BANK SAMPAH</h1>
        <p>Struk Transaksi Setoran</p>
        <div class="code">{{ $deposit->code }}</div>
    </div>

    <!-- Transaction Info -->
    <div class="grid">
        <div class="grid-col">
            <div class="info-section">
                <h3>Informasi Nasabah</h3>
                <div class="info-row">
                    <span class="info-label">Kode Nasabah:</span>
                    <span class="info-value">{{ $deposit->customer->member_code }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nama:</span>
                    <span class="info-value">{{ $deposit->customer->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Telepon:</span>
                    <span class="info-value">{{ $deposit->customer->phone ?? '-' }}</span>
                </div>
            </div>
        </div>
        <div class="grid-col">
            <div class="info-section">
                <h3>Informasi Transaksi</h3>
                <div class="info-row">
                    <span class="info-label">Tanggal:</span>
                    <span class="info-value">{{ $deposit->transaction_date->format('d F Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">creator:</span>
                    <span class="info-value">{{ $deposit->creator->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="status-badge">{{ strtoupper($deposit->status) }}</span>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <!-- Deposit Items -->
    <h3 style="font-size: 11px; font-weight: bold; color: #666; margin-bottom: 10px; text-transform: uppercase;">Rincian
        Setoran</h3>
    <table>
        <thead>
            <tr>
                <th>Kategori Sampah</th>
                <th class="text-right">Berat (kg)</th>
                <th class="text-right">Harga/kg</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($deposit->items as $item)
                <tr>
                    <td>{{ $item->wasteCategory->name }}</td>
                    <td class="text-right">{{ number_format($item->weight_kg, 2, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->price_per_kg, 0, ',', '.') }}</td>
                    <td class="text-right font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right">TOTAL SETORAN:</td>
                <td class="text-right total-amount">Rp {{ number_format($deposit->total_amount, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    @if ($deposit->notes)
        <div class="notes">
            <h3>Catatan</h3>
            <p>{{ $deposit->notes }}</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Terima kasih atas kontribusi Anda untuk lingkungan yang lebih bersih!</p>
        <p style="margin-top: 10px;">Dicetak pada: {{ now()->format('d F Y, H:i') }}</p>
    </div>
</body>

</html>

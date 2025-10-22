<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Ringkasan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
        }

        .header p {
            margin: 5px 0;
            font-size: 12px;
        }

        .summary-boxes {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .summary-box {
            display: table-cell;
            width: 33.33%;
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        .summary-box .value {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .summary-box .label {
            font-size: 10px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tfoot {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin: 20px 0 10px 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>LAPORAN RINGKASAN BANK SAMPAH</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($start)->format('d/m/Y') }} -
            {{ \Carbon\Carbon::parse($end)->format('d/m/Y') }}</p>
        <p>Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="summary-boxes">
        <div class="summary-box">
            <div class="value">Rp {{ number_format($totalDeposits->total_amount ?? 0, 0, ',', '.') }}</div>
            <div class="label">Total Setoran</div>
            <div class="label">{{ number_format($totalDeposits->total_weight ?? 0, 2, ',', '.') }} kg |
                {{ $totalDeposits->count ?? 0 }} transaksi</div>
        </div>
        <div class="summary-box">
            <div class="value">Rp {{ number_format($totalWithdrawals->total_amount ?? 0, 0, ',', '.') }}</div>
            <div class="label">Total Penarikan</div>
            <div class="label">{{ $totalWithdrawals->count ?? 0 }} transaksi</div>
        </div>
        <div class="summary-box">
            <div class="value">Rp {{ number_format($netBalanceChange, 0, ',', '.') }}</div>
            <div class="label">Perubahan Saldo Bersih</div>
        </div>
    </div>

    <div class="section-title">Breakdown per Kategori Sampah</div>
    @if ($categorySummary->isNotEmpty())
        <table>
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th style="text-align: right;">Total Berat (kg)</th>
                    <th style="text-align: right;">Total Nilai (Rp)</th>
                    <th style="text-align: center;">Jml Transaksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categorySummary as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td style="text-align: right;">{{ number_format($category->total_weight, 2, ',', '.') }}</td>
                        <td style="text-align: right;">{{ number_format($category->total_amount, 0, ',', '.') }}</td>
                        <td style="text-align: center;">{{ $category->transaction_count }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>TOTAL</td>
                    <td style="text-align: right;">
                        {{ number_format($categorySummary->sum('total_weight'), 2, ',', '.') }}
                    </td>
                    <td style="text-align: right;">
                        {{ number_format($categorySummary->sum('total_amount'), 0, ',', '.') }}
                    </td>
                    <td style="text-align: center;">{{ $categorySummary->sum('transaction_count') }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    <div class="section-title">Top 10 Nasabah (berdasarkan Total Setoran)</div>
    @if ($topCustomers->isNotEmpty())
        <table>
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">#</th>
                    <th>Nama Nasabah</th>
                    <th>Telepon</th>
                    <th style="text-align: right;">Total Nilai (Rp)</th>
                    <th style="text-align: right;">Total Berat (kg)</th>
                    <th style="text-align: center;">Jml Transaksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($topCustomers as $index => $customer)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td style="text-align: right;">{{ number_format($customer->total_amount, 0, ',', '.') }}</td>
                        <td style="text-align: right;">{{ number_format($customer->total_weight, 2, ',', '.') }}</td>
                        <td style="text-align: center;">{{ $customer->transaction_count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>

</html>

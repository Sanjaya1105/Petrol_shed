<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Company bills — {{ $companyName }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
        h1 { font-size: 16px; margin: 0 0 6px 0; }
        .meta { font-size: 9px; color: #444; margin-bottom: 14px; line-height: 1.4; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 5px 6px; text-align: left; vertical-align: top; }
        th { background: #eee; font-weight: bold; }
        .num { text-align: right; }
        .empty { padding: 12px; text-align: center; color: #666; }
        tfoot td { font-weight: bold; background: #f5f5f5; }
    </style>
</head>
<body>
    <h1>Company bill report</h1>
    <div class="meta">
        <div><strong>Company:</strong> {{ $companyName }}</div>
        <div><strong>Generated:</strong> {{ $generatedAt }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Invoice #</th>
                <th>Staff</th>
                <th>Category</th>
                <th class="num">Price</th>
                <th class="num">Liters</th>
                <th class="num">Bill value</th>
            </tr>
        </thead>
        <tbody>
            @if (! ($showRows ?? false))
                <tr>
                    <td colspan="7" class="empty">No bill records for this company.</td>
                </tr>
            @else
                @foreach ($bills as $bill)
                    <tr>
                        <td>{{ $bill->date?->format('Y-m-d') ?? '—' }}</td>
                        <td>{{ $bill->invoice_number }}</td>
                        <td>{{ $bill->staff?->name ?? '—' }}</td>
                        <td>{{ $bill->category?->category ?? '—' }}</td>
                        <td class="num">{{ number_format((float) $bill->price, 2) }}</td>
                        <td class="num">{{ number_format((float) $bill->liters, 2) }}</td>
                        <td class="num">{{ number_format((float) $bill->bill_value, 2) }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" style="text-align: right;">Total bill value</td>
                <td class="num">{{ $totalFormatted }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>

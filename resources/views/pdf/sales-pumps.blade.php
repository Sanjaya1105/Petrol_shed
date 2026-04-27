<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Pumps sale — {{ $reportDate }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
        h1 { font-size: 16px; margin: 0 0 6px 0; }
        .header-wrap { margin-bottom: 14px; }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { border: 0; padding: 0; vertical-align: top; }
        .logo-cell { text-align: left; width: 28%; }
        .center-cell { text-align: center; width: 34%; vertical-align: middle; }
        .details-cell { text-align: right; width: 48%; }
        .enterprise {
            font-family: "Times New Roman", Georgia, serif;
            font-size: 34px;
            line-height: 1;
            font-weight: 700;
            font-style: italic;
            letter-spacing: 1px;
            color: #0d2f5f;
            text-transform: uppercase;
        }
        .enterprise-address {
            margin-top: 4px;
            font-size: 9px;
            color: #27374d;
            font-weight: 600;
            line-height: 1.25;
        }
        .logo { height: 110px; width: auto; }
        .meta { font-size: 9px; color: #444; line-height: 1.4; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 5px 6px; text-align: left; vertical-align: top; }
        th { background: #eee; font-weight: bold; }
        .num { text-align: right; }
        .empty { padding: 12px; text-align: center; color: #666; }
        tfoot td { font-weight: bold; background: #f5f5f5; }
    </style>
</head>
<body>
    <div class="header-wrap">
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    @if (!empty($logoDataUri))
                        <img src="{{ $logoDataUri }}" alt="Logo" class="logo">
                    @endif
                </td>
                <td class="center-cell">
                    <div class="enterprise">Wijesinghe Enterprises</div>
                    <div class="enterprise-address">No.70 / 1A, CANAL ROAD, HENDALA, WATTALA. Contact: 077 344 76 79</div>
                </td>
                <td class="details-cell">
                    <h1>Pumps sale report</h1>
                    <div class="meta">
                        <div><strong>Report date:</strong> {{ $reportDateFormatted }} ({{ $reportDate }})</div>
                        <div><strong>Starting meter (L)</strong> uses the prior calendar day: {{ $priorDateLabel }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    @if (! ($showReportTable ?? false))
        <p class="empty">No pumps configured.</p>
    @elseif (count($rows) === 0)
        <p class="empty">No pump rows.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Pump</th>
                    <th>Staff</th>
                    <th class="num">Starting meter (L)</th>
                    <th class="num">Ending meter (L)</th>
                    <th class="num">Difference (L)</th>
                    <th class="num">Per liter price</th>
                    <th class="num">Line total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        <td>{{ $row['pump_name'] }}</td>
                        <td>{{ $row['staff_name'] }}</td>
                        <td class="num">{{ $row['starting'] }}</td>
                        <td class="num">{{ $row['ending'] }}</td>
                        <td class="num">{{ $row['difference'] }}</td>
                        <td class="num">{{ $row['price'] }}</td>
                        <td class="num">{{ $row['line_total'] }}</td>
                    </tr>
                @endforeach
            </tbody>
            @if ($showGrandTotal && $grandTotalFormatted)
                <tfoot>
                    <tr>
                        <td colspan="6" style="text-align: right;">Grand total</td>
                        <td class="num">{{ $grandTotalFormatted }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    @endif
</body>
</html>

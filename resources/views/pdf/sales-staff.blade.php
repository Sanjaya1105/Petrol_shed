<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Staff sale — {{ $reportDate }}</title>
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
    <h1>Staff sale report</h1>
    <div class="meta">
        <div><strong>Report date:</strong> {{ $reportDateFormatted }} ({{ $reportDate }})</div>
        <div><strong>Starting meter (L)</strong> uses the prior calendar day: {{ $priorDateLabel }}</div>
    </div>

    @if (! ($showReportTable ?? false))
        <p class="empty">No pumps configured.</p>
    @elseif (count($tableGroups ?? []) === 0)
        <p class="empty">No pump rows.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Staff</th>
                    <th>Pump</th>
                    <th class="num">Starting meter (L)</th>
                    <th class="num">Ending meter (L)</th>
                    <th class="num">Difference (L)</th>
                    <th class="num">Per liter price</th>
                    <th class="num">Line total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tableGroups as $group)
                    @foreach ($group['lines'] as $idx => $line)
                        <tr>
                            @if ($idx === 0)
                                <td rowspan="{{ count($group['lines']) }}">{{ $group['staff'] }}</td>
                            @endif
                            <td>{{ $line['pump_name'] }}</td>
                            <td class="num">{{ $line['starting'] }}</td>
                            <td class="num">{{ $line['ending'] }}</td>
                            <td class="num">{{ $line['difference'] }}</td>
                            <td class="num">{{ $line['price'] }}</td>
                            <td class="num">{{ $line['line_total'] }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
            @if (($showGrandTotal ?? false) && ($grandTotalFormatted ?? null))
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

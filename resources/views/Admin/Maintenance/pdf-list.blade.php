<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: {{ !empty($logoDataUri) ? '108px' : '78px' }} 40px 52px 40px;
        }
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9.5pt;
            color: #111827;
            margin: 0;
            padding: 0;
        }
        /* Repeats on every page (Dompdf fixed positioning) */
        .pdf-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            padding: 8px 40px 10px 40px;
            text-align: center;
            background: #ffffff;
        }
        .pdf-header .header-logo-wrap { margin-bottom: 6px; }
        .pdf-header .header-logo {
            width: 112px;
            height: auto;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .society-name {
            font-size: 16pt;
            font-weight: bold;
            color: #0f172a;
            line-height: 1.2;
            margin: 0;
            text-align: center;
        }
        .letterhead-sub {
            font-size: 8.5pt;
            color: #64748b;
            margin: 5px 0 0 0;
            text-align: center;
        }
        .header-rule {
            border: 0;
            border-top: 2px solid #0f6bff;
            margin: 10px 0 0 0;
            max-width: 100%;
        }
        .pdf-body {
            margin: 0;
            padding: 0;
        }
        h1.doc-title { font-size: 12pt; margin: 0 0 4px 0; font-weight: bold; color: #0f172a; }
        .meta { font-size: 8.5pt; color: #4b5563; margin-bottom: 10px; line-height: 1.35; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.data thead { display: table-header-group; }
        table.data th, table.data td { border: 1px solid #d1d5db; padding: 4px 5px; text-align: left; vertical-align: top; }
        table.data th { background: #f3f4f6; font-weight: bold; font-size: 8.5pt; }
        .num { text-align: right; width: 2rem; }
        .muted { color: #6b7280; font-size: 9pt; }
        .wrap { word-wrap: break-word; max-width: 8rem; }
        .pdf-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 6px 40px 8px 40px;
            border-top: 1px solid #cbd5e1;
            font-size: 7.5pt;
            color: #64748b;
            text-align: center;
            line-height: 1.45;
            background: #ffffff;
        }
        .pdf-footer strong { color: #334155; font-size: 8pt; }
        .pdf-footer .disclaimer { margin-top: 2px; font-size: 7pt; color: #94a3b8; font-style: italic; }
    </style>
</head>
<body>
    <div class="pdf-header">
        @if(!empty($logoDataUri))
            <div class="header-logo-wrap">
                <img class="header-logo" src="{{ $logoDataUri }}" alt="" width="112">
            </div>
        @endif
        <p class="society-name">{{ $societyName !== '' ? $societyName : 'Society' }}</p>
        <p class="letterhead-sub">Maintenance collection report</p>
        <hr class="header-rule">
    </div>

    <div class="pdf-body">
        <h1 class="doc-title">Maintenance — {{ $maintenance->label }} ({{ $listLabel }} list)</h1>
        <div class="meta">
            @if($maintenance->amount !== null)
                Amount per flat: Rs. {{ number_format((float) $maintenance->amount, 2) }}.
            @endif
            Generated {{ now()->format('d M Y, h:i A') }} · {{ $payments->count() }} flat(s)
        </div>

        @if($payments->isEmpty())
            <p class="muted">No {{ strtolower($listLabel) }} flats for this cycle.</p>
        @else
            <table class="data">
                <thead>
                    <tr>
                        <th class="num">#</th>
                        <th>Wing</th>
                        <th>Floor</th>
                        <th>Unit</th>
                        <th>Owner</th>
                        <th>Mobile</th>
                        @if($listLabel === 'Paid')
                            <th>Paid on</th>
                            <th>Txn ID</th>
                            <th class="wrap">Note</th>
                        @else
                            <th>Status</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $i => $payment)
                        @php $flat = $payment->flat; @endphp
                        <tr>
                            <td class="num">{{ $i + 1 }}</td>
                            <td>{{ $flat?->wing?->code ?? '—' }}</td>
                            <td>{{ (int) ($flat?->floor_number) === 0 ? 'Ground' : $flat?->floor_number }}</td>
                            <td>{{ $flat?->unit_code ?? '—' }}</td>
                            <td>{{ trim((string) $flat?->owner_name) !== '' ? $flat->owner_name : 'Vacant' }}</td>
                            <td>{{ $flat?->owner_mobile ?: '—' }}</td>
                            @if($listLabel === 'Paid')
                                <td>{{ $payment->paid_at ? $payment->paid_at->format('d M Y') : '—' }}</td>
                                <td>{{ $payment->transaction_id ?: '—' }}</td>
                                <td class="wrap">{{ $payment->payment_note ? \Illuminate\Support\Str::limit($payment->payment_note, 200) : '—' }}</td>
                            @else
                                <td>Unpaid</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="pdf-footer">
        <strong>{{ $brandName }}</strong>
        <div class="disclaimer">This document is system-generated for administrative reference only. It does not require a physical signature.</div>
    </div>
</body>
</html>

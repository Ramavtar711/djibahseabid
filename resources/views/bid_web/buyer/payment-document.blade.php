<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ ucfirst($documentType) }} #{{ $settlement->id }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 32px;
            color: #0f172a;
            background: #f8fafc;
        }
        .document-wrap {
            max-width: 920px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #dbe4ee;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        }
        .document-head {
            background: #0f4c81;
            color: #fff;
            padding: 28px 32px;
            display: flex;
            justify-content: space-between;
            gap: 24px;
        }
        .document-body {
            padding: 32px;
        }
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 28px;
        }
        .panel {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px;
        }
        .panel h3 {
            margin: 0 0 14px;
            font-size: 16px;
        }
        .meta, .totals {
            width: 100%;
            border-collapse: collapse;
        }
        .meta td, .totals td, .items th, .items td {
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .meta td:last-child, .totals td:last-child {
            text-align: right;
            font-weight: 700;
        }
        .items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
        }
        .items th {
            text-align: left;
            color: #475569;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .items td:last-child, .items th:last-child {
            text-align: right;
        }
        .actions {
            max-width: 920px;
            margin: 0 auto 18px;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }
        .actions a, .actions button {
            border: 0;
            background: #0f4c81;
            color: #fff;
            padding: 10px 16px;
            border-radius: 10px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }
        .actions a.secondary {
            background: #334155;
        }
        .muted {
            color: #64748b;
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .actions {
                display: none;
            }
            .document-wrap {
                box-shadow: none;
                border: 0;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>
    @if(! $isDownload)
        <div class="actions">
            <a href="{{ request()->fullUrlWithQuery(['download' => 1]) }}">Download</a>
            <button type="button" onclick="window.print()">Print</button>
            <a href="{{ route('buyer.payments.show', $settlement->id) }}" class="secondary">Back</a>
        </div>
    @endif

    <div class="document-wrap">
        <div class="document-head">
            <div>
                <h1 style="margin:0 0 8px;">Djibah SeaBid {{ strtoupper($documentType) }}</h1>
                <div>Settlement #{{ $settlement->id }}</div>
                <div>Lot #{{ str_pad((string) $settlement->lot_id, 4, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div style="text-align:right;">
                <div><strong>Status:</strong> {{ ucfirst($settlement->status ?? 'pending') }}</div>
                <div><strong>Issued:</strong> {{ optional($settlement->created_at)->format('M d, Y H:i') }}</div>
                @if($documentType === 'receipt')
                    <div><strong>Paid At:</strong> {{ optional($settlement->paid_at)->format('M d, Y H:i') ?: 'Paid' }}</div>
                @endif
            </div>
        </div>

        <div class="document-body">
            <div class="grid">
                <div class="panel">
                    <h3>Buyer Details</h3>
                    <table class="meta">
                        <tr><td>Name</td><td>{{ $settlement->buyer->name ?? 'Buyer' }}</td></tr>
                        <tr><td>Account Type</td><td>Buyer</td></tr>
                        <tr><td>Payment Provider</td><td>{{ ucfirst(str_replace('_', ' ', $settlement->payment_provider ?? 'pending')) }}</td></tr>
                    </table>
                </div>
                <div class="panel">
                    <h3>Seller Details</h3>
                    <table class="meta">
                        <tr><td>Name</td><td>{{ $settlement->seller->name ?? 'Seller' }}</td></tr>
                        <tr><td>Settlement Route</td><td>Platform Managed</td></tr>
                        <tr><td>Reference</td><td>{{ $settlement->payment_reference ?: 'Pending' }}</td></tr>
                    </table>
                </div>
            </div>

            <table class="items">
                <thead>
                    <tr>
                        <th>Lot</th>
                        <th>Species</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $lot->title ?? ('Lot #' . $settlement->lot_id) }}</td>
                        <td>{{ $lot->species ?? 'Seafood' }}</td>
                        <td>{{ number_format((float) ($lot->quantity ?? 0), 2) }} KG</td>
                        <td>${{ number_format((float) (($lot->final_price ?? $lot->starting_price ?? 0)), 2) }}</td>
                        <td>${{ number_format((float) ($settlement->amount ?? 0), 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="grid">
                <div class="panel">
                    <h3>{{ ucfirst($documentType) }} Notes</h3>
                    <p class="muted" style="margin:0; line-height:1.6;">
                        @if($documentType === 'receipt')
                            This receipt confirms that the settlement amount has been recorded against the buyer account for the won auction lot.
                        @else
                            This invoice shows the amount currently due for the won auction lot. Use the settlement reference for manual verification if paying outside Stripe or wallet.
                        @endif
                    </p>
                </div>
                <div class="panel">
                    <h3>Totals</h3>
                    <table class="totals">
                        <tr><td>Lot Amount</td><td>${{ number_format((float) ($settlement->amount ?? 0), 2) }}</td></tr>
                        <tr><td>Commission</td><td>${{ number_format((float) ($settlement->commission_amount ?? 0), 2) }}</td></tr>
                        <tr><td>Net Amount</td><td>${{ number_format((float) ($settlement->net_amount ?? $settlement->amount ?? 0), 2) }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

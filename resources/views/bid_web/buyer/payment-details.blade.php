@include('bid_web.buyer.include.header')
@include('bid_web.buyer.include.side_menu')

@php
    $lot = $settlement->lot;
    $status = $settlement->status ?? 'pending';
    $statusText = $status === 'paid' ? 'Paid' : ($status === 'processing' ? 'Processing' : ($status === 'failed' ? 'Failed' : 'Pending'));
    $statusClass = $status === 'paid' ? 'bg-success' : ($status === 'processing' ? 'bg-warning text-dark' : ($status === 'failed' ? 'bg-danger' : 'bg-primary'));
    $deadline = $deadlineAt ?? null;
@endphp

<div class="page-wrapper">
    <div class="content container-fluid">
        <style>
            .payment-card {
                background: rgba(255, 255, 255, 0.08);
                border: 1px solid rgba(255, 255, 255, 0.12);
                border-radius: 18px;
                padding: 22px;
                color: #fff;
                height: 100%;
            }
            .payment-card h5, .payment-card h6, .payment-card label, .payment-card p, .payment-card small, .payment-card strong {
                color: #fff;
            }
            .payment-input, .payment-input.form-control, .payment-input.form-select, .payment-input textarea {
                background: rgba(255, 255, 255, 0.12);
                border: 1px solid rgba(255, 255, 255, 0.16);
                color: #fff;
            }
            .payment-input::placeholder {
                color: rgba(255, 255, 255, 0.7);
            }
            .payment-table {
                width: 100%;
                color: #fff;
            }
            .payment-table td {
                padding: 10px 0;
                border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            }
            .payment-table td:last-child {
                text-align: right;
                font-weight: 700;
            }
            .history-table th, .history-table td {
                color: #fff;
                border-color: rgba(255, 255, 255, 0.1);
                vertical-align: middle;
            }
        </style>

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="page-title text-white">Payment Details</h1>
                    <p class="text-white">Settlement and payment management for your won lot.</p>
                </div>
                <div class="col-auto d-flex flex-wrap gap-2 justify-content-end">
                    <a href="{{ route('buyer.won-auction') }}" class="btn btn-outline-light">Back</a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="payment-card">
                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
                        <div>
                            <div class="small text-white-50">Settlement #{{ $settlement->id }}</div>
                            <h4 class="mb-1">{{ $lot->title ?? ($lot->species ?? 'Won Lot') }}</h4>
                            <div class="text-white-50">Lot #{{ str_pad((string) $settlement->lot_id, 4, '0', STR_PAD_LEFT) }}</div>
                        </div>
                        <span class="badge {{ $statusClass }} px-3 py-2">{{ $statusText }}</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <table class="payment-table">
                                <tr><td>Buyer Name</td><td>{{ $settlement->buyer->name ?? 'Buyer' }}</td></tr>
                                <tr><td>Seller Name</td><td>{{ $settlement->seller->name ?? 'Seller' }}</td></tr>
                                <tr><td>Species</td><td>{{ $lot->species ?? 'Seafood' }}</td></tr>
                                <tr><td>Quantity</td><td>{{ number_format((float) ($lot->quantity ?? 0), 2) }} KG</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="payment-table">
                                <tr><td>Total Amount</td><td>${{ number_format((float) ($settlement->amount ?? 0), 2) }}</td></tr>
                                <tr><td>Provider</td><td>{{ ucfirst(str_replace('_', ' ', $settlement->payment_provider ?? 'pending')) }}</td></tr>
                                <tr><td>Reference</td><td>{{ $settlement->payment_reference ?: 'Not generated yet' }}</td></tr>
                                <tr><td>Deadline</td><td>{{ $status === 'paid' ? 'Settled' : ($deadline ? $deadline->format('M d, Y H:i') : '-') }}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="payment-card">
                    <h5 class="mb-3">Wallet Summary</h5>
                    <table class="payment-table">
                        <tr><td>Available Balance</td><td>${{ number_format((float) ($wallet->available_balance ?? 0), 2) }}</td></tr>
                        <tr><td>Blocked Balance</td><td>${{ number_format((float) ($wallet->blocked_balance ?? 0), 2) }}</td></tr>
                        <tr><td>Currency</td><td>{{ $wallet->currency ?? 'USD' }}</td></tr>
                    </table>

                    <hr class="border-light border-opacity-25">

                    <h6 class="mb-2">Add Wallet Amount</h6>
                    <form method="POST" action="{{ route('buyer.payments.topup') }}">
                        @csrf
                        <div class="input-group">
                            <input type="number" min="1" step="0.01" name="amount" class="form-control payment-input" placeholder="Amount (USD)" required>
                            <button type="submit" class="btn btn-primary">Stripe Top-up</button>
                        </div>
                    </form>
                    <small class="d-block mt-2 text-white-50">Wallet top-up uses Stripe checkout and credits your buyer wallet after success.</small>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-3">
                <div class="payment-card">
                    <h5 class="mb-3">Pay by Wallet</h5>
                    <p class="text-white-50">Use current wallet balance and settle this lot immediately.</p>
                    <form method="POST" action="{{ route('buyer.payments.wallet', $settlement->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" {{ $status === 'paid' ? 'disabled' : '' }}>Pay ${{ number_format((float) ($settlement->amount ?? 0), 2) }}</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="payment-card">
                    <h5 class="mb-3">Pay by Stripe</h5>
                    <p class="text-white-50">Use card checkout for direct settlement payment.</p>
                    <form method="GET" action="{{ route('buyer.payments.checkout', $settlement->id) }}">
                        <button type="submit" class="btn btn-primary w-100" {{ $status === 'paid' ? 'disabled' : '' }}>Open Stripe</button>
                    </form>
                </div>
            </div>

            {{-- Bank Transfer and WaafiPay sections hidden for now. --}}
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="payment-card">
                    <h5 class="mb-3">Seller Settlement Info</h5>
                    <table class="payment-table">
                        <tr><td>Seller</td><td>{{ $settlement->seller->name ?? 'Seller' }}</td></tr>
                        <tr><td>Settlement Route</td><td>Platform managed</td></tr>
                        <tr><td>Required Reference</td><td>SETTLEMENT-{{ $settlement->id }}</td></tr>
                        <tr><td>Instruction</td><td>After manual payment, submit reference here so admin can verify and release payout.</td></tr>
                    </table>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="payment-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Settlement History</h5>
                        <a href="{{ route('buyer.transactions') }}" class="btn btn-sm btn-outline-light">Full History</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table history-table mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($settlementTransactions as $tx)
                                    <tr>
                                        <td>{{ optional($tx->created_at)->format('M d, Y H:i') }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $tx->payment_provider ?? 'manual')) }}</td>
                                        <td>{{ ucfirst($tx->status ?? 'pending') }}</td>
                                        <td class="text-end">${{ number_format((float) ($tx->amount ?? 0), 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-white-50">No settlement payment history yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <hr class="border-light border-opacity-25">

                    <h6 class="mb-3">Recent Wallet Activity</h6>
                    <div class="table-responsive">
                        <table class="table history-table mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($walletTransactions as $tx)
                                    <tr>
                                        <td>{{ optional($tx->created_at)->format('M d, Y H:i') }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $tx->type ?? 'transaction')) }}</td>
                                        <td class="text-end">${{ number_format((float) ($tx->amount ?? 0), 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-white-50">No wallet activity yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('bid_web.buyer.include.footer')


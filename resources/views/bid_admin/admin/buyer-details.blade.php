@include('bid_admin.admin.include.header')

@include('bid_admin.admin.include.side_menu')

<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="premium-card buyer-header mb-4">
            <div class="row align-items-center">
                <div class="col-lg-2 text-center">
                    <img class="buyer-avatar" src="{{ $profileImageUrl }}" alt="{{ $buyer->company_legal_name ?: $buyer->name }}">
                </div>
                <div class="col-lg-6">
                    <h4 class="fw-bold mb-1">{{ $buyer->company_legal_name ?: $buyer->name }}</h4>
                    <p class="text-muted mb-2">Buyer ID: {{ $buyerCode }} | {{ $buyer->city ?: ($buyer->country ?: 'No location added') }}</p>

                    @if($buyer->company_registration_file)
                        <span class="badge bg-success">Verified License</span>
                    @endif

                    @if($buyer->bank_transfer_validated)
                        <span class="badge bg-primary">Premium Buyer</span>
                    @endif

                    <span class="badge {{ strtolower((string) ($buyer->status ?: 'pending')) === 'active' ? 'bg-success' : 'bg-warning text-dark' }}">
                        {{ \Illuminate\Support\Str::title((string) ($buyer->status ?: 'pending')) }}
                    </span>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                    <h3 class="text-primary fw-bold">${{ number_format((float) $totalAuctionPurchase, 2) }}</h3>
                    <p class="mb-1">Total Auction Purchase</p>
                    <span class="badge-soft badge">Wallet Balance: ${{ number_format((float) ($wallet?->available_balance ?? 0), 2) }}</span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="premium-card p-3 mb-4">
                    <h5 class="section-title">Financial Overview</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-label">Total Paid Amount</div>
                            <div class="info-value text-success">${{ number_format((float) $totalPaidAmount, 2) }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Pending Settlement</div>
                            <div class="info-value text-danger">${{ number_format((float) $pendingSettlement, 2) }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Average Bid Value</div>
                            <div class="info-value">${{ number_format((float) $averageBidValue, 2) }}</div>
                        </div>
                    </div>
                    <div class="soft-line"></div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-label">Most Purchased Fish</div>
                            <div class="info-value text-primary">
                                {{ $mostPurchasedFish?->species ?: 'N/A' }}
                                @if($mostPurchasedFish && $auctionsParticipated > 0)
                                    ({{ round(($mostPurchasedFish->total_bids / max($auctionsParticipated, 1)) * 100) }}%)
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Preferred Auction Time</div>
                            <div class="info-value">{{ $preferredAuctionTime }}</div>
                        </div>
                    </div>
                </div>

                <div class="premium-card p-3">
                    <h5 class="section-title">Business &amp; Activity Insights</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="stat-box">
                                <h6>{{ $activeBidsThisMonth }}</h6>
                                <p class="mb-0">Active Bids This Month</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-box">
                                <h6>{{ $buyerRankLabel }}</h6>
                                <p class="mb-0">Buyer Ranking</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-box">
                                <h6>On-Time {{ $paymentReliability }}%</h6>
                                <p class="mb-0">Payment Reliability</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="premium-card p-3">
                    <h5 class="section-title">Recent Transactions</h5>
                    <ul class="list-group list-group-flush">
                        @forelse($recentTransactions as $transaction)
                            <li class="list-group-item transaction-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ $transaction->lot?->title ?: ($transaction->lot?->species ?: 'Settlement') }}</strong><br>
                                        <small>{{ $transaction->provider_label }} • {{ optional($transaction->paid_at ?: $transaction->created_at)->format('d M Y') }}</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-semibold">${{ number_format((float) $transaction->amount, 2) }}</div>
                                        <span class="badge {{ $transaction->status_badge_class }}">{{ \Illuminate\Support\Str::title((string) $transaction->status) }}</span>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item transaction-item text-muted">No recent transactions found.</li>
                        @endforelse
                    </ul>
                    <hr>
                    <div class="text-center">
                        <a class="btn btn-primary" href="{{ route('admin.transactions') }}">View All</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="premium-card p-3">
                    <h5 class="section-title">Auction History</h5>
                    <div class="auction-slider">
                        @forelse($auctionHistory as $history)
                            <div class="auction-card">
                                <img src="{{ $history['lot']->image_url }}" alt="{{ $history['lot']->title ?: 'Lot' }}"/>
                                <h6>{{ $history['lot']->title ?: ($history['lot']->species ?: 'Lot') }}</h6>
                                <small>AUC{{ str_pad((string) $history['lot']->id, 4, '0', STR_PAD_LEFT) }}</small>
                                <p>Qty: {{ number_format((float) $history['lot']->quantity, 2) }} Kg</p>
                                <p>{{ $history['won'] ? 'Won' : 'Bid' }}: ${{ number_format((float) ($history['won'] ? $history['won_amount'] : $history['bid_amount']), 2) }}</p>
                                <span class="badge {{ $history['won'] ? 'bg-success' : 'bg-danger' }}">{{ $history['won'] ? 'Won' : 'Lost' }}</span>
                            </div>
                        @empty
                            <div class="text-muted">No auction history found for this buyer.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="premium-card p-3">
                    <h5 class="section-title">Auction Stats</h5>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="stat-box">
                                <h5>{{ $auctionsParticipated }}</h5>
                                <small class="mb-0 small">Auctions Participated</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box">
                                <h5>{{ $auctionsWon }}</h5>
                                <small class="mb-0 small">Auctions Won</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box">
                                <h5>{{ $winRate }}%</h5>
                                <small class="mb-0 small">Win Rate</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box">
                                <h5>{{ number_format((float) $totalFishPurchased, 2) }} Kg</h5>
                                <small class="mb-0 small">Total Fish Purchased</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('bid_admin.admin.include.footer')

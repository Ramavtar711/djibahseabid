@include('bid_admin.admin.include.header')

@include('bid_admin.admin.include.side_menu')

<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="premium-card mb-4 buyer-header">
            <div class="row align-items-center">
                <div class="col-md-8 d-flex gap-3 align-items-center">
                    <img src="{{ $profileImageUrl }}" class="buyer-avatar" alt="{{ $seller->company_name ?: $seller->name }}">

                    <div>
                        <h5 class="mb-1 text-dark">{{ $seller->company_name ?: $seller->name }}</h5>
                        <small class="text-dark">Seller ID: {{ $sellerCode }} | {{ $seller->landing_site_port ?: 'No port added' }}</small><br>

                        @if($seller->trade_license_file)
                            <span class="badge bg-success badge-sm mt-2">Verified License</span>
                        @endif

                        @foreach($supplyTypeBadges as $supplyType)
                            <span class="badge bg-primary badge-sm mt-2">{{ $supplyType }}</span>
                        @endforeach

                        <span class="badge {{ strtolower((string) $seller->status) === 'active' ? 'bg-success' : 'bg-warning text-dark' }} badge-sm mt-2">
                            {{ \Illuminate\Support\Str::title((string) ($seller->status ?: 'pending')) }}
                        </span>
                    </div>
                </div>

                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <h4 class="fw-bold text-primary">${{ number_format((float) $totalRevenue, 2) }}</h4>
                    <small class="text-dark">Total Revenue Generated</small><br>
                    <small class="text-muted">Commission Paid: ${{ number_format((float) $commissionPaid, 2) }}</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card card-soft p-4 mb-4">
                    <h6 class="mb-3">Financial Overview</h6>
                    <div class="row text-center">
                        <div class="col-md-4">
                            <small>Total Lots Listed</small>
                            <h6 class="fw-bold">{{ $totalLotsListed }} Lots</h6>
                        </div>
                        <div class="col-md-4">
                            <small>Pending Settlement</small>
                            <h6 class="text-danger">${{ number_format((float) $pendingSettlement, 2) }}</h6>
                        </div>
                        <div class="col-md-4">
                            <small>Average Lot Value</small>
                            <h6>${{ number_format((float) $averageLotValue, 2) }}</h6>
                        </div>
                    </div>
                </div>

                <div class="card card-soft p-4 mb-4">
                    <h6 class="mb-3">Business &amp; Activity Insights</h6>
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="stat-box">
                                <h5>{{ $activeAuctionsCount }}</h5>
                                <small>Active Auctions</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-box">
                                <h5>{{ $sellerRankLabel }}</h5>
                                <small>Seller Ranking</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-box">
                                <h5>{{ $sellThroughRate }}%</h5>
                                <small>Sell-Through Rate</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-soft p-4">
                    <h6 class="mb-3">Recent Lots Sold</h6>

                    <div class="row">
                        @forelse($recentSoldLots as $lot)
                            <div class="col-md-4 mb-3">
                                <img src="{{ $lot->image_url }}" class="lot-img w-100" alt="{{ $lot->title ?: $lot->species ?: 'Lot' }}">
                                <div class="mt-2">
                                    <strong>{{ $lot->title ?: $lot->species ?: 'Untitled Lot' }}</strong><br>
                                    <small>Qty: {{ number_format((float) $lot->quantity, 2) }} Kg</small><br>
                                    <span class="badge bg-success mt-1">Sold - ${{ number_format((float) ($lot->final_price ?? 0), 2) }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-muted">No sold lots found for this seller.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-soft p-4 mb-4">
                    <h6 class="mb-3">Seller Information</h6>

                    <p class="mb-1"><strong>Contact Name:</strong> {{ $seller->name ?: '-' }}</p>
                    <p class="mb-1"><strong>Company Name:</strong> {{ $seller->company_name ?: '-' }}</p>
                    <p class="mb-1"><strong>Country:</strong> {{ $seller->country ?: '-' }}</p>
                    <p class="mb-1"><strong>Contact:</strong> {{ $seller->phone ?: '-' }}</p>
                    <p class="mb-1"><strong>Address:</strong> {{ $seller->address ?: '-' }}</p>
                    <p class="mb-0"><strong>Email:</strong> {{ $seller->email ?: '-' }}</p>
                </div>

                <div class="card card-soft p-4">
                    <h6 class="mb-3">Recent Payouts</h6>

                    @forelse($recentPayouts as $payout)
                        <div class="mb-3">
                            <strong>${{ number_format((float) $payout->net_amount, 2) }}</strong><br>
                            <small>{{ $payout->provider_label }} - {{ optional($payout->paid_at ?: $payout->created_at)->format('d M Y') }}</small><br>
                            <span class="badge {{ $payout->status_badge_class }}">{{ $payout->status_label }}</span>
                        </div>
                    @empty
                        <div class="text-muted">No payouts found for this seller.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@include('bid_admin.admin.include.footer')

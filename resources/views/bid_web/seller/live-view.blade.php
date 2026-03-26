@include('bid_web.seller.include.header')

@include('bid_web.seller.include.side_menu')

<div class="page-wrapper" style="min-height: 225px;">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="page-title text-white">Live View</h1>
                    <p class="text-white">Auction Management &amp; Perform Optimization</p>
                </div>
            </div>
        </div>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="glass mb-4 price-focus-card">
                    <div class="mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="live-tag">{{ $lot->auction_end_at && $lot->auction_end_at->isFuture() ? 'LIVE' : 'CLOSED' }}</span>
                        </div>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-3 text-white text-uppercase">
                            {{ $lot->title ?: ($lot->species ?: 'Lot') }} • {{ $lot->species ?: 'Seafood' }} • {{ number_format((float) $lot->quantity, 0) }}kg
                        </h5>
                        <img src="{{ $lot->image_url }}" class="img-fluid rounded-4 mb-3 shadow-lg" alt="{{ $lot->title }}" style="width:100%; max-height:320px; object-fit:cover;">
                    </div>
                    <div class="p-4 text-center">
                        <p class="small opacity-75 mb-1 text-uppercase letter-spacing">Current Highest Bid</p>
                        <h1 class="display-4 fw-bold text-info mb-0">${{ number_format((float) $currentPrice, 2) }}</h1>
                        <p class="text-success small fw-bold mt-2">
                            <i class="bi bi-graph-up-arrow"></i> +${{ number_format((float) $priceDelta, 2) }} from Start
                        </p>
                    </div>
                    <div class="bidder-info p-3 border-top border-white-10">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="small">Highest Bidder</span>
                            <span class="fw-bold"><i class="bi bi-person-fill text-info"></i> {{ $highestBidder }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2"><span>Harvest Date:</span><span class="fw-bold">{{ $lot->harvest_date?->format('d F Y') ?? 'N/A' }}</span></div>
                        <div class="d-flex justify-content-between mb-2"><span>Temperature:</span><span class="fw-bold">{{ $lot->storage_temperature ?: 'N/A' }}</span></div>
                        <div class="d-flex justify-content-between"><span>Seller:</span><span class="fw-bold">{{ $lot->seller?->name ?: 'Seller' }}</span></div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="glass p-3 text-center border-warning-glow">
                            <small class="d-block mb-1">TIME REMAINING</small>
                            <h4 class="fw-bold text-warning mb-0">{{ $countdownLabel }}</h4>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="glass p-3 text-center">
                            <small class="d-block mb-1">TOTAL BIDS</small>
                            <h4 class="fw-bold mb-0 text-white">{{ $totalBids }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="glass h-100">
                    <div class="row g-0 h-100">
                        <div>
                            <h6 class="fw-bold mb-4 text-white text-uppercase small opacity-75">Price Evolution (USD/kg)</h6>
                            <div style="height: 300px; margin-top:30px">
                                <canvas id="livePriceChart"></canvas>
                            </div>
                            <div class="mt-4 p-3 glass-panel-dark rounded-3">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <small>Opening</small>
                                        <div class="fw-bold">${{ number_format((float) $openingPrice, 2) }}</div>
                                    </div>
                                    <div class="col-4 border-start border-white-10">
                                        <small>Current</small>
                                        <div class="fw-bold">${{ number_format((float) $currentPrice, 2) }}</div>
                                    </div>
                                    <div class="col-4 border-start border-white-10">
                                        <small>Volume</small>
                                        <div class="fw-bold">{{ number_format((float) $lot->quantity, 0) }}kg</div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-3 border-bottom border-white-10 d-flex justify-content-between">
                                <h6 class="fw-bold mb-0">Live Bid Feed</h6>
                                <span class="small text-info">Recent</span>
                            </div>
                            <div class="bid-history-list" style="max-height: 250px; overflow-y: auto;">
                                @forelse ($bids as $bid)
                                    <div class="bid-item {{ $loop->first ? 'active-bid' : '' }}">
                                        <div class="d-flex justify-content-between">
                                            <span class="me-2">{{ $bid->buyer?->name ?? 'Buyer' }}</span>
                                            <span class="{{ $loop->first ? 'text-info' : '' }} fw-bold">${{ number_format((float) $bid->amount, 2) }}</span>
                                        </div>
                                        <small>{{ optional($bid->created_at)->diffForHumans() }}</small>
                                    </div>
                                @empty
                                    <div class="bid-item">
                                        <div class="d-flex justify-content-between">
                                            <span class="me-2">No bids yet</span>
                                            <span class="fw-bold">${{ number_format((float) $openingPrice, 2) }}</span>
                                        </div>
                                        <small>Waiting for the first bidder</small>
                                    </div>
                                @endforelse
                            </div>

                            @if ($recentMessages->isNotEmpty())
                                <div class="p-3 border-bottom border-white-10 d-flex justify-content-between mt-4">
                                    <h6 class="fw-bold mb-0">Auction Chat</h6>
                                    <span class="small text-info">Recent</span>
                                </div>
                                <div class="bid-history-list" style="max-height: 220px; overflow-y: auto;">
                                    @foreach ($recentMessages as $message)
                                        <div class="bid-item">
                                            <div class="d-flex justify-content-between">
                                                <span class="me-2">{{ $message->buyer?->name ?? 'Buyer' }}</span>
                                                <small>{{ optional($message->created_at)->diffForHumans() }}</small>
                                            </div>
                                            <small class="d-block mt-1">{{ $message->message }}</small>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" data-cfasync="false"></script>

<script>
    const chartCanvas = document.getElementById('livePriceChart');
    if (chartCanvas) {
        const ctx = chartCanvas.getContext('2d');
        const chartGradient = ctx.createLinearGradient(0, 0, 0, 300);
        chartGradient.addColorStop(0, 'rgba(0, 208, 132, 0.3)');
        chartGradient.addColorStop(1, 'rgba(0, 208, 132, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    data: @json($chartValues),
                    borderColor: '#00ff88',
                    borderWidth: 3,
                    tension: 0.35,
                    fill: true,
                    backgroundColor: chartGradient,
                    pointRadius: 3,
                    pointBackgroundColor: '#00ff88'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        ticks: { color: 'rgba(255,255,255,0.5)' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: 'rgba(255,255,255,0.5)' }
                    }
                }
            }
        });
    }
</script>

@include('bid_web.seller.include.footer')

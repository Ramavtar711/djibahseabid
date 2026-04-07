@include('bid_admin.admin.include.header')
@include('bid_admin.admin.include.side_menu')

<div class="page-wrapper dashboard-page">
    <div class="content container-fluid">
        <style>
            .dashboard-page .content.container-fluid { padding-top: 10px !important; }
            .dashboard-page { background: transparent; }
            .dashboard-page .content.container-fluid {
                background:
                    linear-gradient(135deg, rgba(5, 145, 215, 0.12), rgba(23, 120, 191, 0.08)),
                    radial-gradient(circle at top left, rgba(255,255,255,0.15), transparent 30%);
                border-radius: 24px;
            }
            .status-strip {
                background: linear-gradient(135deg, rgba(13, 95, 149, 0.72), rgba(24, 126, 188, 0.58));
                border: 1px solid rgba(255,255,255,.12);
                border-radius: 18px;
                padding: 14px 22px;
                box-shadow: 0 18px 40px rgba(8, 57, 94, 0.16);
                backdrop-filter: blur(8px);
            }
            .status-dot {
                width: 12px;
                height: 12px;
                border-radius: 999px;
                display: inline-block;
                margin-right: 8px;
                box-shadow: 0 0 0 3px rgba(255,255,255,.08);
            }
            .status-pill {
                color: #f8fbff;
                font-size: 15px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                white-space: nowrap;
            }
            .status-pill strong,
            .buyers-online-pill strong {
                color: #ffffff;
                font-weight: 800;
            }
            .buyers-online-pill {
                color: #ffffff;
                font-size: 17px;
                font-weight: 700;
                white-space: nowrap;
            }
            .auction-slider { display:flex; gap:16px; overflow-x:auto; padding-bottom:10px; scroll-behavior:smooth; }
            .auction-item { min-width:260px; max-width:260px; flex:0 0 auto; }
            .metric-card, .dashboard-card, .small-card, .revenue-card {
                background: rgba(248, 251, 255, 0.88);
                border-radius: 22px;
                box-shadow: 0 16px 35px rgba(15,23,42,.08);
                border: 1px solid rgba(255,255,255,.45);
                backdrop-filter: blur(10px);
            }
            .dashboard-card,
            .revenue-card {
                padding: 22px;
            }
            .metric-card {
                padding: 16px 18px;
                min-height: 140px;
            }
            .metric-card.danger-outline {
                border: 2px solid #ff4b5c;
            }
            .metric-card .icon-box {
                width: 58px;
                height: 58px;
                border-radius: 18px;
                display:inline-flex;
                align-items:center;
                justify-content:center;
                font-size: 24px;
            }
            .metric-label {
                color: #0f172a;
                font-size: 14px;
                font-weight: 700;
                margin-bottom: 8px;
            }
            .metric-value {
                font-size: 20px;
                line-height: 1.1;
                font-weight: 800;
                color: #25124d;
                margin-bottom: 4px;
            }
            .metric-change {
                font-size: 15px;
                font-weight: 700;
            }
            .metric-note {
                font-size: 15px;
                color: #0f172a;
            }
            .small-card { padding:18px; height:100%; }
            .rank-item { display:flex; justify-content:space-between; gap:16px; padding:10px 0; border-bottom:1px solid rgba(148,163,184,.18); }
            .rank-item:last-child { border-bottom:0; padding-bottom:0; }
            .rank-number { display:inline-flex; width:28px; height:28px; align-items:center; justify-content:center; border-radius:999px; background:#dbeafe; color:#1d4ed8; font-weight:700; margin-right:8px; }
            .section-title {
                font-size: 17px;
                font-weight: 800;
                color: #1d0f49;
                margin-bottom: 0;
            }
            .section-title span {
                color: #111827;
                font-weight: 500;
            }
            .header-icon-btn {
                width: 40px;
                height: 40px;
                border-radius: 999px;
                border: 1px solid rgba(15,23,42,.08);
                background: rgba(255,255,255,.72);
                color: #9ca3af;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
            .header-dots-btn {
                border: 0;
                background: transparent;
                color: #4b5563;
                font-size: 22px;
                line-height: 1;
                padding: 0 4px;
            }
            .auction-stage-card {
                background: rgba(255,255,255,.92);
                border: 1px solid rgba(12, 74, 110, .08);
                border-radius: 18px;
                padding: 8px;
                box-shadow: 0 10px 24px rgba(15,23,42,.08);
            }
            .auction-stage-card .card-body {
                padding: 10px 6px 6px;
            }
            .live-badge {
                border-radius: 999px;
                background: #ff5a67;
                color: #fff;
                font-size: 11px;
                font-weight: 800;
                padding: 6px 12px;
                box-shadow: 0 8px 16px rgba(255, 90, 103, 0.32);
            }
            .alert-item {
                display: flex;
                gap: 14px;
                align-items: flex-start;
                padding: 14px 0;
                border-bottom: 1px solid rgba(148,163,184,.18);
            }
            .alert-item:last-child {
                border-bottom: 0;
                padding-bottom: 0;
            }
            .alert-icon-wrap {
                width: 34px;
                height: 34px;
                border-radius: 999px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }
            .alert-title {
                font-size: 14px;
                font-weight: 800;
                margin-bottom: 2px;
            }
            .alert-message {
                font-size: 13px;
                color: #111827;
                margin-bottom: 0;
            }
            .alert-time {
                color: #111827;
                font-size: 14px;
                font-weight: 500;
                white-space: nowrap;
            }
        </style>

        <div class="status-strip d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div class="d-flex flex-wrap align-items-center gap-4">
                <div class="status-pill"><span id="systemStatusDot" class="status-dot {{ $systemStatus === 'LIVE' ? 'bg-success' : 'bg-secondary' }}"></span>SYSTEM STATUS: <strong id="systemStatusText" class="ms-1">{{ $systemStatus }}</strong></div>
                <div class="status-pill"><span class="status-dot bg-danger"></span><strong id="liveAuctionsTop">{{ $liveAuctionsCount }}</strong><span class="ms-1">Live Auctions</span></div>
                <div class="status-pill"><span class="status-dot bg-warning"></span><strong id="upcomingAuctionsTop">{{ $upcomingAuctionsCount }}</strong><span class="ms-1">Upcoming</span></div>
                <div class="status-pill"><span class="status-dot bg-success"></span><strong id="revenueTodayTop">${{ number_format($revenueToday, 2) }}</strong><span class="ms-1">Revenue Today</span></div>
            </div>
            <div class="buyers-online-pill"><strong id="registeredBuyersCount">{{ number_format($registeredBuyersCount) }}</strong> Buyers Online</div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="metric-card">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <div class="metric-label">Active Auctions</div>
                            <div id="metricLiveAuctions" class="metric-value">{{ $liveAuctionsCount }}</div>
                            <div class="metric-change text-success">+12%</div>
                        </div>
                        <div class="icon-box bg-primary text-white"><i class="bi bi-hourglass-split"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="metric-card">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <div class="metric-label">Gross Revenue</div>
                            <div id="metricGrossRevenue" class="metric-value">${{ number_format($grossRevenue, 2) }}</div>
                            <div class="metric-change text-success">+8.4%</div>
                        </div>
                        <div class="icon-box bg-success text-white"><i class="bi bi-currency-dollar"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="metric-card">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <div class="metric-label">Volume Sold</div>
                            <div class="metric-value"><span id="metricVolumeSold">{{ number_format($volumeSoldKg, 2) }}</span> <span class="fs-6 fw-normal">kg</span></div>
                            <div class="metric-change text-success">+5.2%</div>
                        </div>
                        <div class="icon-box bg-warning text-white"><i class="bi bi-box-seam"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="metric-card danger-outline">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <div class="metric-label">Ending Soon (24h)</div>
                            <div id="metricEndingSoon" class="metric-value text-danger">{{ $endingSoonCount }}</div>
                            <div class="metric-note">High priority</div>
                        </div>
                        <div class="icon-box bg-danger-subtle text-danger"><i class="bi bi-clock-history"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="dashboard-card mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="section-title">Live Auctions <span>Control Center</span></h6>
                        <div class="d-flex align-items-center gap-2">
                            <small id="dashboardRefreshState" class="text-muted">Auto refresh on</small>
                            <button class="header-icon-btn" onclick="scrollSlider(-300)"><i class="bi bi-chevron-left"></i></button>
                            <button class="header-icon-btn" onclick="scrollSlider(300)"><i class="bi bi-chevron-right"></i></button>
                            <button class="header-dots-btn" type="button">...</button>
                        </div>
                    </div>
                    <div class="auction-slider" id="auctionSlider">
                        @forelse($activeLots as $lot)
                            <div class="auction-item">
                                <div class="auction-stage-card position-relative mb-0">
                                    <span class="live-badge position-absolute m-2 top-0 start-0">LIVE</span>
                                    <img src="{{ $lot['image_url'] }}" class="card-img-top rounded" alt="{{ $lot['title'] ?? 'Lot image' }}" style="height:170px;object-fit:cover;">
                                    <div class="card-body px-1 py-2">
                                        <p class="mb-0 fw-bold">Lot #{{ $lot['id'] }} {{ $lot['species'] ?: ($lot['title'] ?? 'Auction Lot') }}</p>
                                        <small class="text-muted">{{ $lot['seller_name'] }}</small>
                                        <div class="d-flex justify-content-between mt-2 small"><span class="text-muted">Current Bid</span><span class="fw-bold">${{ number_format($lot['current_bid'], 2) }}/kg</span></div>
                                        <div class="d-flex justify-content-between small"><span class="text-muted">Bids</span><span class="fw-bold">{{ $lot['bids_count'] }}</span></div>
                                        <div class="d-flex justify-content-between mb-3 small"><span class="text-muted">Quantity</span><span class="badge bg-light text-dark border">{{ number_format($lot['quantity'], 2) }} kg <span class="js-auction-timer {{ $lot['time_left_class'] }} ms-1" data-end-at="{{ $lot['auction_end_at'] ?? '' }}">{{ $lot['time_left_label'] }}</span></span></div>
                                        <a class="btn btn-primary btn-sm w-100" href="{{ route('admin.live-auction') }}">Open Live Auction</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted py-4">No live auctions available right now.</div>
                        @endforelse
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6"><div class="dashboard-card p-3"><div class="fw-bold mb-3">Fish Volume <span class="text-muted fw-normal small">Last 7 days sold quantity</span></div><canvas id="volumeChart" height="110"></canvas></div></div>
                    <div class="col-md-6"><div class="dashboard-card p-3"><div class="fw-bold mb-3">Transaction Overview <span class="text-muted fw-normal small">Settlements vs bids</span></div><canvas id="transactionChart" height="110"></canvas></div></div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="small-card">
                            <div class="fw-bold mb-2">Top Species by Volume</div>
                            <div id="topSpeciesByVolumeList">
                                @forelse($topSpeciesByVolume as $index => $item)
                                    <div class="rank-item"><div><span class="rank-number">#{{ $index + 1 }}</span>{{ $item['species'] ?: 'Unknown Species' }}</div><div>{{ number_format($item['total_quantity'], 2) }} kg</div></div>
                                @empty
                                    <div class="text-muted">No sold lots yet.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="small-card">
                            <div class="fw-bold mb-2">Top Species by Value</div>
                            <div id="topSpeciesByValueList">
                                @forelse($topSpeciesByValue as $index => $item)
                                    @php
                                        $maxValue = max((float) (collect($topSpeciesByValue)->max('total_amount') ?: 1), 1);
                                        $width = round(($item['total_amount'] / $maxValue) * 100);
                                    @endphp
                                    <div class="rank-item">
                                        <div><span class="rank-number">#{{ $index + 1 }}</span>{{ $item['species'] ?: 'Unknown Species' }}</div>
                                        <div class="d-flex align-items-center" style="width:42%;"><div class="progress w-100 me-2"><div class="progress-bar bg-primary" style="width: {{ $width }}%"></div></div><span class="small">${{ number_format($item['total_amount'], 0) }}</span></div>
                                    </div>
                                @empty
                                    <div class="text-muted">No settlement data yet.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="revenue-card p-3"><div class="d-flex justify-content-between align-items-center mb-3"><div class="fw-bold">Revenue Trend</div><div class="small text-muted">Last 7 days</div></div><canvas id="revenueChart" height="110"></canvas></div>
            </div>

            <div class="col-lg-4">
                <div class="dashboard-card mb-4">
                    <div class="d-flex justify-content-between mb-3 align-items-center"><h6 class="section-title">Alert Center</h6><button class="header-dots-btn" type="button">...</button></div>
                    <div id="alertsList">
                        @foreach($alerts as $alert)
                            <div class="alert-item">
                                <div class="alert-icon-wrap bg-{{ $alert['tone'] }}-subtle"><i class="bi {{ $alert['icon'] }} text-{{ $alert['tone'] }}"></i></div>
                                <div class="flex-grow-1">
                                    <p class="alert-title text-{{ $alert['tone'] }}">{{ strtoupper($alert['title']) }}</p>
                                    <p class="alert-message">{{ $alert['message'] }}</p>
                                </div>
                                <span class="alert-time">{{ $alert['time'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="dashboard-card p-3 mb-4">
                    <h6 class="fw-bold mb-3">Upcoming Auctions</h6>
                    <div id="upcomingLotsList">
                        @forelse($upcomingLots as $lot)
                            <div class="border rounded p-3 mb-3 bg-light bg-opacity-25">
                                <div class="d-flex gap-3 align-items-center mb-3">
                                    <img src="{{ $lot['image_url'] }}" class="rounded" alt="{{ $lot['title'] ?? 'Fish' }}" style="width:60px;height:60px;object-fit:cover;">
                                    <div><small class="text-muted d-block">Lot #{{ $lot['id'] }} {{ $lot['species'] ?: ($lot['title'] ?? 'Auction Lot') }}</small><h4 class="fw-bold mb-0">{{ $lot['starts_in_label'] }}</h4></div>
                                </div>
                                <div class="small mb-2 text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i> {{ strtoupper($lot['status'] ?? 'scheduled auction') }}</div>
                                <a href="{{ route('admin.upcoming-auction') }}" class="btn btn-primary w-100 py-2 fw-bold">View Schedule</a>
                            </div>
                        @empty
                            <div class="text-muted">No upcoming auctions scheduled.</div>
                        @endforelse
                    </div>
                </div>

                <div class="dashboard-card p-3 mb-4">
                    <div class="d-flex justify-content-between mb-3"><h6 class="fw-bold">Top Buyers</h6><i class="bi bi-people"></i></div>
                    <div id="topBuyersList">
                        @forelse($topBuyers as $buyer)
                            <div class="border-bottom py-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div><h6 class="mb-0 fw-bold">{{ $buyer['name'] }}</h6><small class="text-muted">{{ $buyer['country'] ?: 'Country not set' }}</small></div>
                                    <div class="text-end"><div class="fw-bold text-success">${{ number_format($buyer['total_amount'], 2) }}</div><small class="text-muted">{{ number_format($buyer['total_quantity'], 2) }} kg · {{ $buyer['wins_count'] }} Wins</small></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted">No buyer performance data yet.</div>
                        @endforelse
                    </div>
                </div>

                <div class="dashboard-card p-3">
                    <div class="d-flex justify-content-between mb-3"><h6 class="fw-bold">Top Sellers</h6><i class="bi bi-award"></i></div>
                    <div id="topSellersList">
                        @forelse($topSellers as $seller)
                            <div class="border-bottom py-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div><h6 class="mb-0 fw-bold">{{ $seller['name'] }}</h6><small class="text-muted">Value: <span class="text-dark fw-bold">${{ number_format($seller['total_amount'], 2) }}</span></small></div>
                                    <span class="badge {{ $seller['success_rate'] >= 85 ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}">{{ $seller['success_rate'] }}% Success</span>
                                </div>
                                <div class="progress mb-1" style="height:4px;"><div class="progress-bar {{ $seller['success_rate'] >= 85 ? 'bg-success' : 'bg-warning' }}" style="width: {{ $seller['success_rate'] }}%"></div></div>
                                <div class="d-flex justify-content-between small text-muted"><span>Listed: {{ number_format($seller['listed_quantity'], 2) }} kg</span><span>Sold: {{ number_format($seller['sold_quantity'], 2) }} kg</span></div>
                            </div>
                        @empty
                            <div class="text-muted">No seller performance data yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" data-cfasync="false"></script>
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1/dist/echo.iife.js"></script>
<script>
function scrollSlider(amount) {
    const slider = document.getElementById('auctionSlider');
    if (slider) slider.scrollBy({ left: amount, behavior: 'smooth' });
}

function formatMoney(value) {
    return '$' + Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatNumber(value, decimals = 0) {
    return Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

const liveAuctionUrl = @json(route('admin.live-auction'));
const upcomingAuctionUrl = @json(route('admin.upcoming-auction'));
const dashboardDataUrl = @json(route('admin.dashboard.data'));
const pusherKey = @json(config('broadcasting.connections.pusher.key'));
const pusherHost = @json(config('broadcasting.connections.pusher.options.host'));
const pusherPort = @json(config('broadcasting.connections.pusher.options.port'));
const pusherScheme = @json(config('broadcasting.connections.pusher.options.scheme'));

let volumeChart;
let transactionChart;
let revenueChart;
let refreshInFlight = false;
let refreshTimer;
let websocketConnected = false;

const initialDashboardData = {
    chartLabels: @json($chartLabels),
    volumeChartData: @json($volumeChartData),
    transactionBarData: @json($transactionBarData),
    transactionLineData: @json($transactionLineData),
    revenueChartData: @json($revenueChartData)
};

function renderActiveLots(activeLots) {
    const slider = document.getElementById('auctionSlider');
    if (!slider) return;

    if (!activeLots.length) {
        slider.innerHTML = '<div class="text-muted py-4">No live auctions available right now.</div>';
        return;
    }

    slider.innerHTML = activeLots.map((lot) => `
        <div class="auction-item">
            <div class="auction-stage-card position-relative mb-0">
                <span class="live-badge position-absolute m-2 top-0 start-0">LIVE</span>
                <img src="${escapeHtml(lot.image_url || '')}" class="card-img-top rounded" alt="${escapeHtml(lot.title || 'Lot image')}" style="height:170px;object-fit:cover;">
                <div class="card-body px-1 py-2">
                    <p class="mb-0 fw-bold">Lot #${escapeHtml(lot.id)} ${escapeHtml(lot.species || lot.title || 'Auction Lot')}</p>
                    <small class="text-muted">${escapeHtml(lot.seller_name || 'Seller')}</small>
                    <div class="d-flex justify-content-between mt-2 small"><span class="text-muted">Current Bid</span><span class="fw-bold">${formatMoney(lot.current_bid)}/kg</span></div>
                    <div class="d-flex justify-content-between small"><span class="text-muted">Bids</span><span class="fw-bold">${formatNumber(lot.bids_count)}</span></div>
                    <div class="d-flex justify-content-between mb-3 small"><span class="text-muted">Quantity</span><span class="badge bg-light text-dark border">${formatNumber(lot.quantity, 2)} kg <span class="js-auction-timer ${escapeHtml(lot.time_left_class || 'text-success')} ms-1" data-end-at="${escapeHtml(lot.auction_end_at || '')}">${escapeHtml(lot.time_left_label || 'Live')}</span></span></div>
                    <a class="btn btn-primary btn-sm w-100" href="${liveAuctionUrl}">Open Live Auction</a>
                </div>
            </div>
        </div>
    `).join('');

    updateLiveCountdowns();
}

function getCountdownClass(remainingMs) {
    if (remainingMs <= 5 * 60 * 1000) return 'text-danger';
    if (remainingMs <= 30 * 60 * 1000) return 'text-warning';
    return 'text-success';
}

function formatCountdownLabel(endAt) {
    if (!endAt) return 'Live';

    const endTime = new Date(endAt).getTime();
    if (Number.isNaN(endTime)) return 'Live';

    const remainingMs = Math.max(0, endTime - Date.now());
    const totalSeconds = Math.floor(remainingMs / 1000);
    const days = Math.floor(totalSeconds / 86400);
    const hours = Math.floor((totalSeconds % 86400) / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = totalSeconds % 60;

    const hh = String(hours).padStart(2, '0');
    const mm = String(minutes).padStart(2, '0');
    const ss = String(seconds).padStart(2, '0');

    if (days > 0) {
        return `${days}d ${hh}:${mm}:${ss}`;
    }

    return `${hh}:${mm}:${ss}`;
}

function updateLiveCountdowns() {
    document.querySelectorAll('.js-auction-timer').forEach((timerEl) => {
        const endAt = timerEl.getAttribute('data-end-at');
        const endTime = endAt ? new Date(endAt).getTime() : NaN;

        timerEl.textContent = formatCountdownLabel(endAt);
        timerEl.classList.remove('text-success', 'text-warning', 'text-danger');

        if (!Number.isNaN(endTime)) {
            const remainingMs = Math.max(0, endTime - Date.now());
            timerEl.classList.add(getCountdownClass(remainingMs));
        } else {
            timerEl.classList.add('text-success');
        }
    });
}

function renderAlerts(alerts) {
    document.getElementById('alertsList').innerHTML = alerts.map((alert) => `
        <div class="alert-item">
            <div class="alert-icon-wrap bg-${escapeHtml(alert.tone || 'info')}-subtle"><i class="bi ${escapeHtml(alert.icon || 'bi-info-circle-fill')} text-${escapeHtml(alert.tone || 'info')}"></i></div>
            <div class="flex-grow-1">
                <p class="alert-title text-${escapeHtml(alert.tone || 'info')}">${escapeHtml((alert.title || '').toUpperCase())}</p>
                <p class="alert-message">${escapeHtml(alert.message || '')}</p>
            </div>
            <span class="alert-time">${escapeHtml(alert.time || 'Live')}</span>
        </div>
    `).join('');
}

function renderUpcomingLots(upcomingLots) {
    const container = document.getElementById('upcomingLotsList');
    if (!upcomingLots.length) {
        container.innerHTML = '<div class="text-muted">No upcoming auctions scheduled.</div>';
        return;
    }

    container.innerHTML = upcomingLots.map((lot) => `
        <div class="border rounded p-3 mb-3 bg-light bg-opacity-25">
            <div class="d-flex gap-3 align-items-center mb-3">
                <img src="${escapeHtml(lot.image_url || '')}" class="rounded" alt="${escapeHtml(lot.title || 'Fish')}" style="width:60px;height:60px;object-fit:cover;">
                <div><small class="text-muted d-block">Lot #${escapeHtml(lot.id)} ${escapeHtml(lot.species || lot.title || 'Auction Lot')}</small><h4 class="fw-bold mb-0">${escapeHtml(lot.starts_in_label || 'Scheduled')}</h4></div>
            </div>
            <div class="small mb-2 text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i> ${escapeHtml((lot.status || 'scheduled auction').toUpperCase())}</div>
            <a href="${upcomingAuctionUrl}" class="btn btn-primary w-100 py-2 fw-bold">View Schedule</a>
        </div>
    `).join('');
}

function renderSpeciesVolume(items) {
    const container = document.getElementById('topSpeciesByVolumeList');
    if (!items.length) {
        container.innerHTML = '<div class="text-muted">No sold lots yet.</div>';
        return;
    }

    container.innerHTML = items.map((item, index) => `
        <div class="rank-item"><div><span class="rank-number">#${index + 1}</span>${escapeHtml(item.species || 'Unknown Species')}</div><div>${formatNumber(item.total_quantity, 2)} kg</div></div>
    `).join('');
}

function renderSpeciesValue(items) {
    const container = document.getElementById('topSpeciesByValueList');
    if (!items.length) {
        container.innerHTML = '<div class="text-muted">No settlement data yet.</div>';
        return;
    }

    const maxValue = Math.max(...items.map((item) => Number(item.total_amount || 0)), 1);

    container.innerHTML = items.map((item, index) => `
        <div class="rank-item">
            <div><span class="rank-number">#${index + 1}</span>${escapeHtml(item.species || 'Unknown Species')}</div>
            <div class="d-flex align-items-center" style="width:42%;"><div class="progress w-100 me-2"><div class="progress-bar bg-primary" style="width: ${Math.round((Number(item.total_amount || 0) / maxValue) * 100)}%"></div></div><span class="small">${formatMoney(item.total_amount).replace('.00', '')}</span></div>
        </div>
    `).join('');
}

function renderTopBuyers(items) {
    const container = document.getElementById('topBuyersList');
    if (!items.length) {
        container.innerHTML = '<div class="text-muted">No buyer performance data yet.</div>';
        return;
    }

    container.innerHTML = items.map((buyer) => `
        <div class="border-bottom py-3">
            <div class="d-flex align-items-center justify-content-between">
                <div><h6 class="mb-0 fw-bold">${escapeHtml(buyer.name || 'Buyer')}</h6><small class="text-muted">${escapeHtml(buyer.country || 'Country not set')}</small></div>
                <div class="text-end"><div class="fw-bold text-success">${formatMoney(buyer.total_amount)}</div><small class="text-muted">${formatNumber(buyer.total_quantity, 2)} kg · ${formatNumber(buyer.wins_count)} Wins</small></div>
            </div>
        </div>
    `).join('');
}

function renderTopSellers(items) {
    const container = document.getElementById('topSellersList');
    if (!items.length) {
        container.innerHTML = '<div class="text-muted">No seller performance data yet.</div>';
        return;
    }

    container.innerHTML = items.map((seller) => `
        <div class="border-bottom py-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div><h6 class="mb-0 fw-bold">${escapeHtml(seller.name || 'Seller')}</h6><small class="text-muted">Value: <span class="text-dark fw-bold">${formatMoney(seller.total_amount)}</span></small></div>
                <span class="badge ${Number(seller.success_rate || 0) >= 85 ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning'}">${formatNumber(seller.success_rate)}% Success</span>
            </div>
            <div class="progress mb-1" style="height:4px;"><div class="progress-bar ${Number(seller.success_rate || 0) >= 85 ? 'bg-success' : 'bg-warning'}" style="width: ${formatNumber(seller.success_rate)}%"></div></div>
            <div class="d-flex justify-content-between small text-muted"><span>Listed: ${formatNumber(seller.listed_quantity, 2)} kg</span><span>Sold: ${formatNumber(seller.sold_quantity, 2)} kg</span></div>
        </div>
    `).join('');
}

function applyDashboardData(data) {
    document.getElementById('systemStatusText').textContent = data.systemStatus || 'STANDBY';
    document.getElementById('systemStatusDot').className = `status-dot ${(data.systemStatus === 'LIVE') ? 'bg-success' : 'bg-secondary'}`;
    document.getElementById('liveAuctionsTop').textContent = formatNumber(data.liveAuctionsCount);
    document.getElementById('upcomingAuctionsTop').textContent = formatNumber(data.upcomingAuctionsCount);
    document.getElementById('revenueTodayTop').textContent = formatMoney(data.revenueToday);
    document.getElementById('registeredBuyersCount').textContent = formatNumber(data.registeredBuyersCount);
    document.getElementById('metricLiveAuctions').textContent = formatNumber(data.liveAuctionsCount);
    document.getElementById('metricGrossRevenue').textContent = formatMoney(data.grossRevenue);
    document.getElementById('metricVolumeSold').textContent = formatNumber(data.volumeSoldKg, 2);
    document.getElementById('metricEndingSoon').textContent = formatNumber(data.endingSoonCount);

    renderActiveLots(data.activeLots || []);
    renderAlerts(data.alerts || []);
    renderUpcomingLots(data.upcomingLots || []);
    renderSpeciesVolume(data.topSpeciesByVolume || []);
    renderSpeciesValue(data.topSpeciesByValue || []);
    renderTopBuyers(data.topBuyers || []);
    renderTopSellers(data.topSellers || []);

    if (volumeChart) {
        volumeChart.data.labels = data.chartLabels || [];
        volumeChart.data.datasets[0].data = data.volumeChartData || [];
        volumeChart.update();
    }

    if (transactionChart) {
        transactionChart.data.labels = data.chartLabels || [];
        transactionChart.data.datasets[0].data = data.transactionBarData || [];
        transactionChart.data.datasets[1].data = data.transactionLineData || [];
        transactionChart.update();
    }

    if (revenueChart) {
        revenueChart.data.labels = data.chartLabels || [];
        revenueChart.data.datasets[0].data = data.revenueChartData || [];
        revenueChart.update();
    }
}

async function refreshDashboardData() {
    if (refreshInFlight) return;
    refreshInFlight = true;
    document.getElementById('dashboardRefreshState').textContent = 'Syncing...';

    try {
        const requestUrl = new URL(dashboardDataUrl, window.location.origin);
        requestUrl.searchParams.set('_', Date.now());
        const response = await fetch(requestUrl.toString(), {
            cache: 'no-store',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Cache-Control': 'no-cache'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to load dashboard updates.');
        }

        const data = await response.json();
        applyDashboardData(data);
        document.getElementById('dashboardRefreshState').textContent = 'Live updates on';
    } catch (error) {
        document.getElementById('dashboardRefreshState').textContent = 'Auto refresh paused';
        console.error(error);
    } finally {
        refreshInFlight = false;
    }
}

function initializeCharts() {
    if (typeof Chart === 'undefined') {
        console.warn('Chart.js failed to load. Live dashboard refresh will continue without charts.');
        return;
    }

    volumeChart = new Chart(document.getElementById('volumeChart'), {
        type: 'bar',
        data: {
            labels: initialDashboardData.chartLabels,
            datasets: [{ data: initialDashboardData.volumeChartData, backgroundColor: '#4e73df', borderRadius: 6 }]
        },
        options: { plugins: { legend: { display: false } } }
    });

    transactionChart = new Chart(document.getElementById('transactionChart'), {
        data: {
            labels: initialDashboardData.chartLabels,
            datasets: [
                { type: 'bar', data: initialDashboardData.transactionBarData, backgroundColor: '#a0c4ff', borderRadius: 6 },
                { type: 'line', data: initialDashboardData.transactionLineData, borderColor: '#4e73df', tension: 0.4, fill: false }
            ]
        },
        options: { plugins: { legend: { display: false } } }
    });

    revenueChart = new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: initialDashboardData.chartLabels,
            datasets: [{ data: initialDashboardData.revenueChartData, borderColor: '#4338ca', backgroundColor: 'rgba(67, 56, 202, 0.1)', fill: true, tension: 0.4 }]
        },
        options: { plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true } } }
    });
}

function startDashboardRefresh() {
    refreshDashboardData();
    refreshTimer = window.setInterval(refreshDashboardData, 15000);
    window.setInterval(updateLiveCountdowns, 1000);
}

function initializeDashboardRealtime() {
    if (window.Echo === undefined && window.Pusher && typeof Echo !== 'undefined' && pusherKey) {
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: pusherKey,
            wsHost: pusherHost || window.location.hostname,
            wsPort: pusherPort || 6001,
            wssPort: pusherPort || 6001,
            forceTLS: pusherScheme === 'https',
            disableStats: true,
            enabledTransports: ['ws', 'wss'],
        });
    }

    if (!window.Echo) {
        return;
    }

    window.Echo.channel('dashboard.admin')
        .listen('.dashboard.updated', function () {
            websocketConnected = true;
            document.getElementById('dashboardRefreshState').textContent = 'Live websocket sync';
            refreshDashboardData();
        });
}

initializeCharts();
startDashboardRefresh();
initializeDashboardRealtime();
updateLiveCountdowns();

document.addEventListener('visibilitychange', () => {
    if (!document.hidden) {
        refreshDashboardData();
    }
});
</script>
@include('bid_admin.admin.include.footer')

@include('bid_admin.admin.include.header')
@include('bid_admin.admin.include.side_menu')

<div class="page-wrapper admin-live-page">
    <div class="content container-fluid">
        <style>
            .admin-live-page .content.container-fluid { padding-top: 10px !important; }
            .live-page-shell {
                background: linear-gradient(180deg, rgba(5, 145, 215, 0.12), rgba(23, 120, 191, 0.08));
                border-radius: 24px;
                padding: 18px;
            }
            .status-strip {
                background: linear-gradient(135deg, rgba(13, 95, 149, 0.72), rgba(24, 126, 188, 0.58));
                border: 1px solid rgba(255,255,255,.12);
                border-radius: 18px;
                padding: 14px 22px;
                box-shadow: 0 18px 40px rgba(8, 57, 94, 0.16);
                backdrop-filter: blur(8px);
            }
            .status-pill, .buyers-online-pill {
                color: #f8fbff;
                font-size: 15px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                white-space: nowrap;
            }
            .status-pill strong, .buyers-online-pill strong { color: #fff; font-weight: 800; }
            .buyers-online-pill { font-size: 17px; font-weight: 700; }
            .status-dot {
                width: 12px;
                height: 12px;
                border-radius: 999px;
                display: inline-block;
                margin-right: 8px;
                box-shadow: 0 0 0 3px rgba(255,255,255,.08);
            }
            .live-page-card {
                background: rgba(248, 251, 255, 0.90);
                border-radius: 22px;
                box-shadow: 0 16px 35px rgba(15,23,42,.08);
                border: 1px solid rgba(255,255,255,.45);
                backdrop-filter: blur(10px);
                padding: 22px;
            }
            .live-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 16px;
                margin-bottom: 18px;
            }
            .live-header h4 {
                margin: 0;
                font-size: 26px;
                font-weight: 800;
                color: #180f49;
            }
            .live-header p {
                margin: 4px 0 0;
                color: #4b5563;
            }
            .live-sync {
                color: #4b5563;
                font-size: 14px;
                font-weight: 600;
            }
            .auction-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(255px, 1fr));
                gap: 20px;
            }
            .auction-card {
                background: rgba(255,255,255,.95);
                border-radius: 18px;
                padding: 8px;
                border: 1px solid rgba(12, 74, 110, .08);
                box-shadow: 0 10px 24px rgba(15,23,42,.08);
            }
            .img-container {
                position: relative;
                overflow: hidden;
                border-radius: 16px;
            }
            .img-container img {
                width: 100%;
                height: 180px;
                object-fit: cover;
                display: block;
            }
            .live-label {
                position: absolute;
                top: 12px;
                left: 12px;
                background: #ff5a67;
                color: #fff;
                font-size: 11px;
                font-weight: 800;
                padding: 6px 12px;
                border-radius: 999px;
                box-shadow: 0 8px 16px rgba(255, 90, 103, 0.32);
                z-index: 1;
            }
            .card-info {
                padding: 14px 8px 10px;
            }
            .lot-name {
                color: #1f1149;
                font-size: 16px;
                font-weight: 800;
                margin-bottom: 4px;
            }
            .market-label {
                color: #6b7280;
                font-size: 13px;
                margin-bottom: 12px;
            }
            .data-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 12px;
                margin-bottom: 8px;
                font-size: 14px;
            }
            .data-label { color: #6b7280; }
            .data-value { color: #111827; font-weight: 700; text-align: right; }
            .timer { margin-left: 6px; font-weight: 800; }
            .action-group {
                display: flex;
                gap: 10px;
                padding: 0 8px 8px;
            }
            .btn-pause, .btn-stop {
                width: 44px;
                height: 40px;
                border-radius: 12px;
                border: 0;
                color: #fff;
            }
            .btn-pause { background: #0ea5e9; }
            .btn-stop { background: #ef4444; }
            .btn-extend {
                flex: 1;
                border: 0;
                border-radius: 12px;
                background: #f59e0b;
                color: #fff;
                font-weight: 700;
                padding: 0 12px;
            }
            .empty-state {
                text-align: center;
                color: #4b5563;
                padding: 40px 20px;
                background: rgba(255,255,255,.72);
                border-radius: 18px;
            }
        </style>

        <div class="live-page-shell">
            <div class="status-strip d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div class="d-flex flex-wrap align-items-center gap-4">
                    <div class="status-pill"><span id="systemStatusDot" class="status-dot {{ $systemStatus === 'LIVE' ? 'bg-success' : 'bg-secondary' }}"></span>SYSTEM STATUS: <strong id="systemStatusText" class="ms-1">{{ $systemStatus }}</strong></div>
                    <div class="status-pill"><span class="status-dot bg-danger"></span><strong id="liveAuctionsTop">{{ $liveAuctionsCount }}</strong><span class="ms-1">Live Auctions</span></div>
                    <div class="status-pill"><span class="status-dot bg-warning"></span><strong id="upcomingAuctionsTop">{{ $upcomingAuctionsCount }}</strong><span class="ms-1">Upcoming</span></div>
                    <div class="status-pill"><span class="status-dot bg-success"></span><strong id="revenueTodayTop">${{ number_format($revenueToday, 2) }}</strong><span class="ms-1">Revenue Today</span></div>
                </div>
                <div class="buyers-online-pill"><strong id="registeredBuyersCount">{{ number_format($registeredBuyersCount) }}</strong> Buyers Online</div>
            </div>

            <div class="live-page-card">
                <div class="live-header">
                    <div>
                        <h4>Live Auctions</h4>
                        <p>All currently active lots with latest bid activity and auction timer.</p>
                    </div>
                    <div id="liveAuctionSyncState" class="live-sync">Live updates on</div>
                </div>

                <div id="liveAuctionGrid" class="auction-grid">
                    @forelse ($liveAuctionItems as $item)
                        <div class="auction-card">
                            <div class="img-container">
                                <span class="live-label">LIVE</span>
                                <a href="{{ $item['detail_url'] }}" class="d-block">
                                    <img src="{{ $item['image_url'] }}" alt="{{ $item['title'] ?: 'Lot image' }}">
                                </a>
                            </div>
                            <div class="card-info">
                                <a href="{{ $item['detail_url'] }}" class="lot-name d-block text-decoration-none">Lot #{{ $item['id'] }} {{ $item['species'] ?: ($item['title'] ?: 'Auction Lot') }}</a>
                                <div class="market-label">{{ $item['seller_name'] }}</div>
                                <div class="data-row">
                                    <span class="data-label">Current Bid</span>
                                    <span class="data-value">${{ number_format($item['current_bid'], 2) }}/kg</span>
                                </div>
                                <div class="data-row">
                                    <span class="data-label">Quantity</span>
                                    <span class="data-value">{{ number_format($item['quantity'], 2) }} kg <span class="timer {{ $item['time_left_class'] }}">{{ $item['time_left_label'] }}</span></span>
                                </div>
                                <div class="data-row">
                                    <span class="data-label">Bids</span>
                                    <span class="data-value">{{ $item['bid_count'] }}</span>
                                </div>
                            </div>
                            <div class="action-group">
                                <button class="btn-pause" type="button" title="Pause" data-pause-url="{{ $item['pause_url'] }}"><i class="fas fa-pause"></i></button>
                                <button class="btn-extend d-inline-flex align-items-center justify-content-center text-decoration-none" type="button" data-extend-url="{{ $item['extend_url'] }}">Extend 5m</button>
                                <button class="btn-stop" type="button" title="Stop" data-stop-url="{{ $item['stop_url'] }}"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">No live auctions are running right now.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const liveAuctionDataUrl = @json(route('admin.live-auction.data'));
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

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

function renderLiveAuctionItems(items) {
    const grid = document.getElementById('liveAuctionGrid');
    if (!grid) return;

    if (!items.length) {
        grid.innerHTML = '<div class="empty-state">No live auctions are running right now.</div>';
        return;
    }

    grid.innerHTML = items.map((item) => `
        <div class="auction-card">
            <div class="img-container">
                <span class="live-label">LIVE</span>
                <a href="${escapeHtml(item.detail_url || '#')}" class="d-block">
                    <img src="${escapeHtml(item.image_url || '')}" alt="${escapeHtml(item.title || 'Lot image')}">
                </a>
            </div>
            <div class="card-info">
                <a href="${escapeHtml(item.detail_url || '#')}" class="lot-name d-block text-decoration-none">Lot #${escapeHtml(item.id)} ${escapeHtml(item.species || item.title || 'Auction Lot')}</a>
                <div class="market-label">${escapeHtml(item.seller_name || 'Seller')}</div>
                <div class="data-row">
                    <span class="data-label">Current Bid</span>
                    <span class="data-value">${formatMoney(item.current_bid)}/kg</span>
                </div>
                <div class="data-row">
                    <span class="data-label">Quantity</span>
                    <span class="data-value">${formatNumber(item.quantity, 2)} kg <span class="timer ${escapeHtml(item.time_left_class || '')}">${escapeHtml(item.time_left_label || 'Live')}</span></span>
                </div>
                <div class="data-row">
                    <span class="data-label">Bids</span>
                    <span class="data-value">${formatNumber(item.bid_count)}</span>
                </div>
            </div>
            <div class="action-group">
                <button class="btn-pause" type="button" title="Pause" data-pause-url="${escapeHtml(item.pause_url || '#')}"><i class="fas fa-pause"></i></button>
                <button class="btn-extend d-inline-flex align-items-center justify-content-center text-decoration-none" type="button" data-extend-url="${escapeHtml(item.extend_url || '#')}">Extend 5m</button>
                <button class="btn-stop" type="button" title="Stop" data-stop-url="${escapeHtml(item.stop_url || '#')}"><i class="fas fa-times"></i></button>
            </div>
        </div>
    `).join('');
}

function applyLiveAuctionData(data) {
    document.getElementById('systemStatusText').textContent = data.systemStatus || 'STANDBY';
    document.getElementById('systemStatusDot').className = `status-dot ${(data.systemStatus === 'LIVE') ? 'bg-success' : 'bg-secondary'}`;
    document.getElementById('liveAuctionsTop').textContent = formatNumber(data.liveAuctionsCount);
    document.getElementById('upcomingAuctionsTop').textContent = formatNumber(data.upcomingAuctionsCount);
    document.getElementById('revenueTodayTop').textContent = formatMoney(data.revenueToday);
    document.getElementById('registeredBuyersCount').textContent = formatNumber(data.registeredBuyersCount);
    renderLiveAuctionItems(data.liveAuctionItems || []);
}

let liveAuctionRefreshInFlight = false;

async function refreshLiveAuctionData() {
    if (liveAuctionRefreshInFlight) return;
    liveAuctionRefreshInFlight = true;
    document.getElementById('liveAuctionSyncState').textContent = 'Syncing...';

    try {
        const response = await fetch(liveAuctionDataUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to load live auctions.');
        }

        const data = await response.json();
        applyLiveAuctionData(data);
        document.getElementById('liveAuctionSyncState').textContent = 'Live updates on';
    } catch (error) {
        document.getElementById('liveAuctionSyncState').textContent = 'Auto refresh paused';
        console.error(error);
    } finally {
        liveAuctionRefreshInFlight = false;
    }
}

async function extendLiveAuction(url, button) {
    if (!url || !button) return;

    const originalText = button.textContent;
    button.disabled = true;
    button.textContent = 'Extending...';
    document.getElementById('liveAuctionSyncState').textContent = 'Updating auction time...';

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Unable to extend auction.');
        }

        document.getElementById('liveAuctionSyncState').textContent = data.message || 'Auction extended.';
        await refreshLiveAuctionData();
    } catch (error) {
        document.getElementById('liveAuctionSyncState').textContent = error.message || 'Extend failed';
        console.error(error);
    } finally {
        button.disabled = false;
        button.textContent = originalText;
    }
}

async function postAuctionAction(url, button, inProgressText, successFallback, confirmText = null) {
    if (!url || !button) return;

    if (confirmText && !window.confirm(confirmText)) {
        return;
    }

    const originalHtml = button.innerHTML;
    button.disabled = true;
    button.innerHTML = inProgressText;
    document.getElementById('liveAuctionSyncState').textContent = inProgressText;

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Action failed.');
        }

        document.getElementById('liveAuctionSyncState').textContent = data.message || successFallback;
        await refreshLiveAuctionData();
    } catch (error) {
        document.getElementById('liveAuctionSyncState').textContent = error.message || 'Action failed';
        console.error(error);
    } finally {
        button.disabled = false;
        button.innerHTML = originalHtml;
    }
}

document.getElementById('liveAuctionGrid')?.addEventListener('click', function (event) {
    const extendButton = event.target.closest('.btn-extend[data-extend-url]');
    if (extendButton) {
        event.preventDefault();
        extendLiveAuction(extendButton.getAttribute('data-extend-url'), extendButton);
        return;
    }

    const pauseButton = event.target.closest('.btn-pause[data-pause-url]');
    if (pauseButton) {
        event.preventDefault();
        postAuctionAction(
            pauseButton.getAttribute('data-pause-url'),
            pauseButton,
            'Pausing...',
            'Auction paused for 5 minutes.'
        );
        return;
    }

    const stopButton = event.target.closest('.btn-stop[data-stop-url]');
    if (stopButton) {
        event.preventDefault();
        postAuctionAction(
            stopButton.getAttribute('data-stop-url'),
            stopButton,
            'Stopping...',
            'Auction stopped.',
            'Stop this auction now? This will immediately close the auction.'
        );
    }
});

setInterval(refreshLiveAuctionData, 15000);
</script>

@include('bid_admin.admin.include.footer')

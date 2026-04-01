@include('bid_admin.admin.include.header')
@include('bid_admin.admin.include.side_menu')

<div class="page-wrapper admin-upcoming-page">
    <div class="content container-fluid">
        <style>
            .admin-upcoming-page .content.container-fluid { padding-top: 10px !important; }
            .admin-upcoming-shell {
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
            .upcoming-page-card {
                background: rgba(248, 251, 255, 0.90);
                border-radius: 22px;
                box-shadow: 0 16px 35px rgba(15,23,42,.08);
                border: 1px solid rgba(255,255,255,.45);
                backdrop-filter: blur(10px);
                padding: 22px;
            }
            .upcoming-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 16px;
                margin-bottom: 18px;
            }
            .upcoming-header h4 {
                margin: 0;
                font-size: 26px;
                font-weight: 800;
                color: #180f49;
            }
            .upcoming-header p {
                margin: 4px 0 0;
                color: #4b5563;
            }
            .upcoming-grid {
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
            .upcoming-label {
                position: absolute;
                top: 12px;
                left: 12px;
                background: #f59e0b;
                color: #111827;
                font-size: 11px;
                font-weight: 800;
                padding: 6px 12px;
                border-radius: 999px;
                box-shadow: 0 8px 16px rgba(245, 158, 11, 0.28);
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
            .action-group {
                display: flex;
                gap: 10px;
                padding: 0 8px 8px;
            }
            .btn-extend {
                width: 100%;
                border: 0;
                border-radius: 12px;
                background: #3b82f6;
                color: #fff;
                font-weight: 700;
                padding: 12px;
                text-align: center;
                text-decoration: none;
            }
            .empty-state {
                text-align: center;
                color: #4b5563;
                padding: 40px 20px;
                background: rgba(255,255,255,.72);
                border-radius: 18px;
            }
        </style>

        <div class="admin-upcoming-shell">
            <div class="status-strip d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div class="d-flex flex-wrap align-items-center gap-4">
                    <div class="status-pill"><span class="status-dot {{ $systemStatus === 'LIVE' ? 'bg-success' : 'bg-secondary' }}"></span>SYSTEM STATUS: <strong class="ms-1">{{ $systemStatus }}</strong></div>
                    <div class="status-pill"><span class="status-dot bg-danger"></span><strong>{{ $liveAuctionsCount }}</strong><span class="ms-1">Live Auctions</span></div>
                    <div class="status-pill"><span class="status-dot bg-warning"></span><strong>{{ $upcomingAuctionsCount }}</strong><span class="ms-1">Upcoming</span></div>
                    <div class="status-pill"><span class="status-dot bg-success"></span><strong>${{ number_format($revenueToday, 2) }}</strong><span class="ms-1">Revenue Today</span></div>
                </div>
                <div class="buyers-online-pill"><strong>{{ number_format($registeredBuyersCount) }}</strong> Buyers Online</div>
            </div>

            <div class="upcoming-page-card">
                <div class="upcoming-header">
                    <div>
                        <h4>Upcoming Auctions</h4>
                        <p>Scheduled lots that will move into live bidding automatically.</p>
                    </div>
                </div>

                <div class="upcoming-grid">
                    @forelse($upcomingLots as $lot)
                        @php
                            $startAt = optional($lot->auction_start_at)->toIso8601String();
                        @endphp
                        <div class="auction-card">
                            <div class="img-container">
                                <span class="upcoming-label">SCHEDULED</span>
                                <img src="{{ $lot->image_url }}" alt="{{ $lot->title ?? ($lot->species ?? 'Lot image') }}">
                            </div>
                            <div class="card-info">
                                <div class="lot-name">Lot #{{ $lot->id }} {{ $lot->title ?? ($lot->species ?? 'Auction Lot') }}</div>
                                <div class="market-label">{{ $lot->seller?->name ?? 'Seller' }}</div>
                                <div class="data-row">
                                    <span class="data-label">Starting Price</span>
                                    <span class="data-value">${{ number_format((float) ($lot->starting_price ?? 0), 2) }}/kg</span>
                                </div>
                                <div class="data-row">
                                    <span class="data-label">Quantity</span>
                                    <span class="data-value">{{ number_format((float) ($lot->quantity ?? 0), 2) }} kg</span>
                                </div>
                                <div class="data-row">
                                    <span class="data-label">Starts In</span>
                                    <span class="data-value countdown" data-start="{{ $startAt }}">{{ $lot->starts_in_label ?? '--:--:--' }}</span>
                                </div>
                            </div>
                            <div class="action-group">
                                <a class="btn-extend" href="{{ route('admin.lot-details', ['lot' => $lot->id]) }}">View Details</a>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">No upcoming auctions are scheduled right now.</div>
                    @endforelse
                </div>

                @if(method_exists($upcomingLots, 'links'))
                    <div class="d-flex justify-content-center mt-4">
                        {{ $upcomingLots->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" data-cfasync="false"></script>

<script>
  (function () {
    var hasReloaded = false;

    function formatCountdown(ms) {
      if (ms <= 0) return '00:00:00';

      var totalSeconds = Math.floor(ms / 1000);
      var hours = Math.floor(totalSeconds / 3600);
      var minutes = Math.floor((totalSeconds % 3600) / 60);
      var seconds = totalSeconds % 60;

      return String(hours).padStart(2, '0') + ':' +
        String(minutes).padStart(2, '0') + ':' +
        String(seconds).padStart(2, '0');
    }

    function updateCountdowns() {
      document.querySelectorAll('.countdown[data-start]').forEach(function (el) {
        var start = el.getAttribute('data-start');
        if (!start) return;

        var remaining = new Date(start).getTime() - Date.now();
        el.textContent = formatCountdown(remaining);

        if (remaining <= 0 && !hasReloaded) {
          hasReloaded = true;
          window.location.reload();
        }
      });
    }

    updateCountdowns();
    setInterval(updateCountdowns, 1000);
  })();
</script>

@include('bid_admin.admin.include.footer')

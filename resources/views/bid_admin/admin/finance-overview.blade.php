@include('bid_admin.admin.include.header')

@include('bid_admin.admin.include.side_menu')

@php
    $kpiMeta = [
        'Total Revenue' => ['icon' => 'bi bi-cash-stack', 'box' => 'icon-revenue', 'type' => 'currency'],
        'Total Commission' => ['icon' => 'bi bi-percent', 'box' => 'icon-commission', 'type' => 'currency'],
        'Platform Balance' => ['icon' => 'bi bi-wallet2', 'box' => 'icon-balance', 'type' => 'currency'],
        'Escrow Holding' => ['icon' => 'bi bi-safe2', 'box' => 'icon-escrow', 'type' => 'currency'],
        'Pending Payments' => ['icon' => 'bi bi-hourglass-split', 'box' => 'icon-pending', 'type' => 'currency'],
        'Failed Transactions' => ['icon' => 'bi bi-x-circle', 'box' => 'icon-failed', 'type' => 'count'],
        'Generated Invoices' => ['icon' => 'bi bi-receipt', 'box' => 'icon-invoice', 'type' => 'count'],
        'Avg Validation Time' => ['icon' => 'bi bi-clock-history', 'box' => 'icon-validation', 'type' => 'hours'],
    ];
@endphp

<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="status-header d-flex flex-wrap justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-4">
                <div class="small"><i class="bi bi-circle-fill text-success me-1"></i> SYSTEM STATUS: <strong id="systemStatusValue">{{ $systemStatus }}</strong></div>
                <div class="small"><i class="bi bi-circle-fill text-danger me-1"></i> <strong id="liveAuctionsValue">{{ number_format($liveAuctionsCount) }}</strong> Live Auctions</div>
                <div class="small"><i class="bi bi-circle-fill text-warning me-1"></i> <strong id="upcomingAuctionsValue">{{ number_format($upcomingAuctionsCount) }}</strong> Upcoming</div>
                <div class="small"><i class="bi bi-circle-fill text-success me-1"></i> <strong id="revenueTodayValue">${{ number_format((float) $revenueToday, 2) }}</strong> Revenue Today</div>
            </div>
            <div class="fw-bold"><span id="buyersOnlineValue">{{ number_format($buyersOnlineCount) }}</span> <span class="text-muted fw-normal">Buyers Online</span></div>
        </div>

        <style>
            .page-wrapper{
                background:
                radial-gradient(1200px 400px at 20% -10%, rgba(255,255,255,0.35), transparent 65%),
                radial-gradient(1000px 500px at 90% 0%, rgba(255,255,255,0.25), transparent 70%),
                linear-gradient(180deg, #11a8e8 0%, #0e9dde 32%, #0a91d6 100%);
                min-height:calc(100vh - 64px);
            }

            .status-header{
                background:rgba(14,64,115,0.45);
                color:#fff;
                border-radius:14px;
                padding:14px 18px;
                margin-bottom:22px;
                backdrop-filter:blur(3px);
            }

            .status-header .small,
            .status-header .text-muted{
                color:#fff !important;
            }

            .kpi-card{
                background:rgba(255,255,255,0.82);
                border:1px solid rgba(255,255,255,0.45);
                border-radius:18px;
                padding:20px;
                display:flex;
                align-items:center;
                gap:15px;
                box-shadow:0 10px 20px rgba(0,0,0,0.08);
                transition:0.25s ease;
                backdrop-filter:blur(2px);
                height:100%;
            }

            .kpi-card:hover{
                transform:translateY(-3px);
                box-shadow:0 14px 26px rgba(0,0,0,0.13);
            }

            .icon-box{
                width:50px;
                height:50px;
                border-radius:14px;
                display:flex;
                align-items:center;
                justify-content:center;
                font-size:22px;
                color:#fff;
                flex-shrink:0;
            }

            .icon-revenue{background:#1e6fe7;}
            .icon-commission{background:#1f8d53;}
            .icon-balance{background:#6b49c8;}
            .icon-escrow{background:#f2bf08;color:#000;}
            .icon-pending{background:#e53950;}
            .icon-failed{background:#ff8b11;}
            .icon-invoice{background:#1ecb9a;}
            .icon-validation{background:#14c5eb;color:#000;}

            .kpi-title{
                font-size:13px;
                color:#0c1729;
                font-weight:700;
                margin-bottom:4px;
            }

            .kpi-value{
                font-size:34px;
                line-height:1.1;
                font-weight:700;
                color:#071427;
            }

            .card-soft{
                background:rgba(236,247,255,0.72);
                border:1px solid rgba(255,255,255,0.48);
                border-radius:18px;
                box-shadow:0 10px 22px rgba(0,0,0,0.1);
                backdrop-filter:blur(2px);
            }

            .page-title{
                font-size:42px;
                line-height:1.1;
                font-weight:700;
                color:#071427;
                margin-bottom:16px;
            }

            .section-title{
                font-size:18px;
                font-weight:700;
                color:#071427;
                margin-bottom:16px;
            }

            .table{
                --bs-table-bg:transparent;
            }
        </style>

        <h4 class="page-title">Finance Overview</h4>

        <div class="row g-3 mb-5" id="financeKpiGrid">
            @foreach ($financeKpis as $kpi)
                @php
                    $meta = $kpiMeta[$kpi['title']] ?? ['icon' => 'bi bi-graph-up', 'box' => 'icon-revenue', 'type' => 'count'];
                    $displayValue = match ($meta['type']) {
                        'currency' => '$' . number_format((float) $kpi['value'], 2),
                        'hours' => number_format(((float) $kpi['value']) / 60, 1) . ' Hours',
                        default => number_format((float) $kpi['value'], 0),
                    };
                @endphp
                <div class="col-md-3">
                    <div class="kpi-card">
                        <div class="icon-box {{ $meta['box'] }}">
                            <i class="{{ $meta['icon'] }}"></i>
                        </div>
                        <div>
                            <div class="kpi-title">{{ $kpi['title'] }}</div>
                            <div class="kpi-value">{{ $displayValue }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card card-soft p-4">
                    <h6 class="section-title">Revenue Trend (Monthly)</h6>
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card card-soft p-4">
                    <h6 class="section-title">Payment Method Distribution</h6>
                    <canvas id="paymentChart"></canvas>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card card-soft p-4">
                    <h6 class="section-title">Commission vs Payout</h6>
                    <canvas id="commissionChart"></canvas>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card card-soft p-4">
                    <h6 class="section-title">Bank Transfer Status</h6>
                    <canvas id="transferChart"></canvas>
                </div>
            </div>
        </div>

        <div class="card card-soft p-4 mb-4">
            <h6 class="section-title">Financial Risk Flags</h6>
            <div class="row text-center" id="riskFlagsGrid">
                @foreach ($riskFlags as $flag)
                    <div class="col-md-3">
                        <h6 class="{{ $flag['class'] }} mb-2">{{ $flag['label'] }}</h6>
                        <h4>{{ number_format((float) $flag['value'], 0) }}</h4>
                        <div class="small text-muted">{{ $flag['helper'] ?? '' }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card card-soft p-4">
            <h6 class="section-title">Recent Large Transactions</h6>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Auction ID</th>
                        <th>Buyer</th>
                        <th>Seller</th>
                        <th>Amount</th>
                        <th>Payment Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="recentTransactionsBody">
                    @forelse ($recentTransactions as $transaction)
                        <tr>
                            <td>{{ $transaction['auction_code'] }}</td>
                            <td>{{ $transaction['buyer_name'] }}</td>
                            <td>{{ $transaction['seller_name'] }}</td>
                            <td>${{ number_format((float) $transaction['amount'], 2) }}</td>
                            <td>{{ $transaction['payment_type'] }}</td>
                            <td><span class="badge {{ $transaction['status_badge_class'] }}">{{ $transaction['status_label'] }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" data-cfasync="false"></script>

<script>
    const financeOverviewDataUrl = @json(route('admin.finance-overview.data'));
    const financeKpiMeta = @json($kpiMeta);

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

    function formatKpiValue(title, value) {
        const meta = financeKpiMeta[title] || { type: 'count' };
        if (meta.type === 'currency') {
            return formatMoney(value);
        }
        if (meta.type === 'hours') {
            return `${formatNumber((Number(value || 0) / 60), 1)} Hours`;
        }

        return formatNumber(value);
    }

    function renderFinanceKpis(financeKpis) {
        const grid = document.getElementById('financeKpiGrid');
        if (!grid) return;

        grid.innerHTML = (financeKpis || []).map((kpi) => {
            const meta = financeKpiMeta[kpi.title] || { icon: 'bi bi-graph-up', box: 'icon-revenue', type: 'count' };

            return `
                <div class="col-md-3">
                    <div class="kpi-card">
                        <div class="icon-box ${escapeHtml(meta.box)}">
                            <i class="${escapeHtml(meta.icon)}"></i>
                        </div>
                        <div>
                            <div class="kpi-title">${escapeHtml(kpi.title)}</div>
                            <div class="kpi-value">${escapeHtml(formatKpiValue(kpi.title, kpi.value))}</div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    function renderRiskFlags(riskFlags) {
        const grid = document.getElementById('riskFlagsGrid');
        if (!grid) return;

        grid.innerHTML = (riskFlags || []).map((flag) => `
            <div class="col-md-3">
                <h6 class="${escapeHtml(flag.class || 'text-muted')} mb-2">${escapeHtml(flag.label || '')}</h6>
                <h4>${escapeHtml(formatNumber(flag.value || 0))}</h4>
                <div class="small text-muted">${escapeHtml(flag.helper || '')}</div>
            </div>
        `).join('');
    }

    function renderRecentTransactions(transactions) {
        const body = document.getElementById('recentTransactionsBody');
        if (!body) return;

        if (!transactions || !transactions.length) {
            body.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">No transactions found.</td></tr>';
            return;
        }

        body.innerHTML = transactions.map((transaction) => `
            <tr>
                <td>${escapeHtml(transaction.auction_code || '')}</td>
                <td>${escapeHtml(transaction.buyer_name || '')}</td>
                <td>${escapeHtml(transaction.seller_name || '')}</td>
                <td>${escapeHtml(formatMoney(transaction.amount || 0))}</td>
                <td>${escapeHtml(transaction.payment_type || '')}</td>
                <td><span class="badge ${escapeHtml(transaction.status_badge_class || 'bg-secondary')}">${escapeHtml(transaction.status_label || '')}</span></td>
            </tr>
        `).join('');
    }

    const revenueChart = new Chart(document.getElementById("revenueChart"), {
        type: "line",
        data: {
            labels: @json($monthlyRevenueLabels),
            datasets: [{
                label: "Revenue",
                data: @json($monthlyRevenueData),
                borderColor: "#0d6efd",
                backgroundColor: "rgba(13,110,253,0.12)",
                tension: 0.35,
                fill: true
            }]
        }
    });

    const commissionChart = new Chart(document.getElementById("commissionChart"), {
        type: "bar",
        data: {
            labels: @json($monthlyRevenueLabels),
            datasets: [
                {
                    label: "Commission",
                    data: @json($monthlyCommissionData),
                    backgroundColor: "#198754"
                },
                {
                    label: "Payout",
                    data: @json($monthlyPayoutData),
                    backgroundColor: "#ffc107"
                }
            ]
        }
    });

    const paymentChart = new Chart(document.getElementById("paymentChart"), {
        type: "doughnut",
        data: {
            labels: @json($paymentMethodLabels),
            datasets: [{
                data: @json($paymentMethodData),
                backgroundColor: ["#0d6efd", "#198754", "#ffc107", "#dc3545", "#6f42c1", "#20c997"]
            }]
        }
    });

    const transferChart = new Chart(document.getElementById("transferChart"), {
        type: "doughnut",
        data: {
            labels: @json($bankTransferLabels),
            datasets: [{
                data: @json($bankTransferData),
                backgroundColor: ["#198754", "#17a2b8", "#ffc107", "#dc3545", "#6c757d"]
            }]
        }
    });

    function applyFinanceOverviewData(data) {
        document.getElementById('systemStatusValue').textContent = data.systemStatus || 'STANDBY';
        document.getElementById('liveAuctionsValue').textContent = formatNumber(data.liveAuctionsCount || 0);
        document.getElementById('upcomingAuctionsValue').textContent = formatNumber(data.upcomingAuctionsCount || 0);
        document.getElementById('revenueTodayValue').textContent = formatMoney(data.revenueToday || 0);
        document.getElementById('buyersOnlineValue').textContent = formatNumber(data.buyersOnlineCount || 0);

        renderFinanceKpis(data.financeKpis || []);
        renderRiskFlags(data.riskFlags || []);
        renderRecentTransactions(data.recentTransactions || []);

        revenueChart.data.labels = data.monthlyRevenueLabels || [];
        revenueChart.data.datasets[0].data = data.monthlyRevenueData || [];
        revenueChart.update();

        commissionChart.data.labels = data.monthlyRevenueLabels || [];
        commissionChart.data.datasets[0].data = data.monthlyCommissionData || [];
        commissionChart.data.datasets[1].data = data.monthlyPayoutData || [];
        commissionChart.update();

        paymentChart.data.labels = data.paymentMethodLabels || [];
        paymentChart.data.datasets[0].data = data.paymentMethodData || [];
        paymentChart.update();

        transferChart.data.labels = data.bankTransferLabels || [];
        transferChart.data.datasets[0].data = data.bankTransferData || [];
        transferChart.update();
    }

    let financeRefreshInFlight = false;

    async function refreshFinanceOverview() {
        if (financeRefreshInFlight) return;
        financeRefreshInFlight = true;

        try {
            const response = await fetch(financeOverviewDataUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();
            applyFinanceOverviewData(data);
        } catch (error) {
            console.error('Finance overview refresh failed:', error);
        } finally {
            financeRefreshInFlight = false;
        }
    }

    setInterval(refreshFinanceOverview, 10000);
</script>

@include('bid_admin.admin.include.footer')

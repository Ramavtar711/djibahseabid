@include('bid_admin.admin.include.header')
@include('bid_admin.admin.include.side_menu')

<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="status-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-4 flex-wrap">
                <div class="small">
                    <i class="bi bi-circle-fill {{ $systemStatus === 'LIVE' ? 'text-success' : 'text-secondary' }} me-1"></i>
                    SYSTEM STATUS: <strong>{{ $systemStatus }}</strong>
                </div>
                <div class="small"><i class="bi bi-circle-fill text-danger me-1"></i> <strong>{{ $liveAuctionsCount }}</strong> Live Auctions</div>
                <div class="small"><i class="bi bi-circle-fill text-warning me-1"></i> <strong>{{ $upcomingAuctionsCount }}</strong> Upcoming</div>
                <div class="small"><i class="bi bi-circle-fill text-success me-1"></i> <strong>${{ number_format((float) $revenueToday, 2) }}</strong> Revenue Today</div>
            </div>
            <div class="fw-bold">{{ $buyersOnlineCount }} <span class="text-muted fw-normal">Buyers Active (24h)</span></div>
        </div>

        <style>
            .kpi-box {
                background: var(--surface-white);
                padding: 18px;
                border-radius: 14px;
                display: flex;
                align-items: center;
                gap: 15px;
                box-shadow: 0 5px 20px rgba(0,0,0,0.05);
                height: 100%;
            }
            .icon-box {
                width: 45px;
                height: 45px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                color: #fff;
            }
            .icon-alert { background: #dc3545; }
            .icon-risk { background: #fd7e14; }
            .icon-kyc { background: #ffc107; color: #000; }
            .icon-dispute { background: #0d6efd; }
            .kpi-info h6 {
                margin: 0;
                font-size: 13px;
                color: #6c757d;
            }
            .kpi-info h4 {
                margin: 0;
                font-weight: 600;
            }
            .card-soft {
                border: 0;
                border-radius: 16px;
                box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            }
            .section-title {
                font-weight: 700;
                margin-bottom: 1rem;
            }
            .risk-empty {
                padding: 32px 18px;
                text-align: center;
                color: #6c757d;
            }
        </style>

        <h4 class="fw-bold mb-4">Risk Monitoring Overview</h4>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="kpi-box">
                    <div class="icon-box icon-alert">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div class="kpi-info">
                        <h6>Total Risk Alerts</h6>
                        <h4 class="risk-high">{{ $riskSummary['total_alerts'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-box">
                    <div class="icon-box icon-risk">
                        <i class="bi bi-shield-exclamation"></i>
                    </div>
                    <div class="kpi-info">
                        <h6>High Risk Accounts</h6>
                        <h4 class="risk-high">{{ $riskSummary['high_risk_accounts'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-box">
                    <div class="icon-box icon-kyc">
                        <i class="bi bi-person-vcard"></i>
                    </div>
                    <div class="kpi-info">
                        <h6>Pending Buyer KYC</h6>
                        <h4 class="risk-medium">{{ $riskSummary['pending_kyc'] }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-box">
                    <div class="icon-box icon-dispute">
                        <i class="bi bi-chat-left-text"></i>
                    </div>
                    <div class="kpi-info">
                        <h6>Open Payment Reviews</h6>
                        <h4 class="risk-medium">{{ $riskSummary['open_reviews'] }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <div class="card card-soft p-4">
                    <h6>Risk Trend</h6>
                    <canvas id="fraudTrend"></canvas>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-soft p-4">
                    <h6>Risk Distribution</h6>
                    <canvas id="riskChart"></canvas>
                </div>
            </div>
        </div>

        <div class="card card-soft p-4">
            <h6 class="section-title">Detected Risk Cases</h6>

            @if (count($riskCases) > 0)
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Account</th>
                                <th>Issue Type</th>
                                <th>Auction ID</th>
                                <th>Country</th>
                                <th>Last Activity</th>
                                <th>Risk Score</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($riskCases as $case)
                                <tr>
                                    <td>{{ $case['account_name'] }}</td>
                                    <td>{{ $case['issue_type'] }}</td>
                                    <td>{{ $case['auction_code'] }}</td>
                                    <td>{{ $case['country'] }}</td>
                                    <td>{{ $case['last_activity_label'] }}</td>
                                    <td class="{{ $case['risk_score'] >= 80 ? 'risk-high' : 'risk-medium' }}">{{ $case['risk_score'] }}%</td>
                                    <td><span class="badge {{ $case['status_badge_class'] }}">{{ $case['status_label'] }}</span></td>
                                    <td>
                                        <a href="{{ $case['action_url'] }}" class="btn btn-sm {{ $case['action_class'] }}">
                                            {{ $case['action_label'] }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="risk-empty">
                    No risk signals found yet. As soon as payment, KYC, or bidding anomalies appear, they will show up here.
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" data-cfasync="false"></script>
<script>
    new Chart(document.getElementById('riskChart'), {
        type: 'doughnut',
        data: {
            labels: @json($riskChartLabels),
            datasets: [{
                data: @json($riskChartData),
                backgroundColor: ['#198754', '#ffc107', '#dc3545']
            }]
        }
    });

    new Chart(document.getElementById('fraudTrend'), {
        type: 'line',
        data: {
            labels: @json($fraudTrendLabels),
            datasets: [{
                label: 'Risk Cases',
                data: @json($fraudTrendData),
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.08)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
</script>

@include('bid_admin.admin.include.footer')

@include('bid_web.seller.include.header')

@include('bid_web.seller.include.side_menu')

<style>
/* Financial Cards */
.glass-card {
    background: var(--glass);
    backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
}

.main-balance-card {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(0, 255, 255, 0.05));
    border-color: rgba(59, 130, 246, 0.4);
}
.progress{
    width:100%;
}
/* Settlement List */
.settlement-item {
    background: rgba(255, 255, 255, 0.03);100%
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.settlement-item.pending {
    border-left: 3px solid #ffc107;
}

.status-dot-success { height: 8px; width: 8px; background: #198754; border-radius: 50%; display: inline-block; }
.status-dot-warning { height: 8px; width: 8px; background: #ffc107; border-radius: 50%; display: inline-block; }

/* Form Elements */
.glass-input-sm {
    background: rgba(255,255,255,0.05);
    border: 1px solid var(--glass-border);
    color: white;
    border-radius: 50px;
    font-size: 0.8rem;
    outline: none;
}

.btn-glass-outline {
    background: rgba(255,255,255,0.05);
    border: 1px solid var(--glass-border);
    color: white;
    font-size: 0.8rem;
    padding: 10px;
    border-radius: 10px;
}

.border-white-10 { border-color: rgba(255, 255, 255, 0.1) !important; }
    </style>


<div class="page-wrapper">
            <div class="content container-fluid">
                   <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <h1 class="page-title text-white">Financial Summary (Revenue)</h1>
        <p class="text-white">Detailed revenue breakdown and payment tracking</p>
      </div>
      <div class="col-auto">
       <div class="d-flex gap-3">
            <select class="glass-input-sm py-2 px-3">
                <option>Last 6 Months</option>
                <option>Last Year</option>
                <option>All Time</option>
            </select>
            <button class="btn btn-outline-light btn-sm rounded-pill px-3"><i class="bi bi-download"></i> Export Report</button>
        </div>
       
      </div>
    </div>
  </div>
    
    
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="glass main-balance-card p-4">
                <div class="d-flex justify-content-between mb-3">
                    <span class="small">Net Payable (Balance)</span>
                    <i class="bi bi-wallet2 text-info fs-4"></i>
                </div>
                <h2 class="display-6 fw-bold mb-1">${{ number_format($pendingTotal ?? 0, 2) }}</h2>
                <span class="badge bg-success-subtle text-success small">Ready for Withdrawal</span>
            </div>
        </div>
        <div class="col-md-8">
            <div class="glass p-4">
                <div class="row g-4 text-center">
                    <div class="col-sm-3 border-end border-white-10">
                        <small class=" d-block">Total Gross</small>
                        <h4 class="fw-bold mb-0 mt-2 text-white">${{ number_format($grossTotal ?? 0, 2) }}</h4>
                    </div>
                    <div class="col-sm-3 border-end border-white-10">
                        <small class=" d-block">Commission (5%)</small>
                        <h4 class="fw-bold mb-0 mt-2 text-danger">-${{ number_format($commissionTotal ?? 0, 2) }}</h4>
                    </div>
                    <div class="col-sm-3 border-end border-white-10">
                        <small class=" d-block">Payments Received</small>
                        <h4 class="fw-bold mb-0 mt-2 text-success">${{ number_format($paidTotal ?? 0, 2) }}</h4>
                    </div>
                    <div class="col-sm-3">
                        <small class=" d-block">Payments Pending</small>
                        <h4 class="fw-bold mb-0 mt-2 text-warning">${{ number_format($pendingTotal ?? 0, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="glass p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold m-0">Monthly Sales vs Volume</h6>
                    <div class="d-flex gap-3 small">
                        <span><i class="bi bi-circle-fill text-info small"></i> Revenue ($)</span>
                        <span><i class="bi bi-circle-fill text-warning small"></i> Volume (kg)</span>
                    </div>
                </div>
                <div style="height: 350px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="glass p-4 h-100">
                <h6 class="fw-bold mb-4">Recent Settlements</h6>
                <div class="settlement-list">
                    @forelse(($recentSettlements ?? collect()) as $settlement)
                        @php
                            $isPaid = $settlement->status === 'paid';
                            $label = $isPaid ? 'Paid' : 'Pending';
                        @endphp
                        <div class="settlement-item d-flex justify-content-between align-items-center p-3 mb-3 {{ $isPaid ? '' : 'pending' }}">
                            <div>
                                <h6 class="mb-0 small fw-bold">LOT #{{ str_pad((string) $settlement->lot_id, 4, '0', STR_PAD_LEFT) }} Settlement</h6>
                                <small class="">{{ optional($settlement->created_at)->format('M d, Y') }}</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-success">+${{ number_format($settlement->net_amount ?? 0, 2) }}</div>
                                <span class="{{ $isPaid ? 'status-dot-success' : 'status-dot-warning' }}"></span> {{ $label }}
                            </div>
                        </div>
                    @empty
                        <div class="text-white-50">No settlements yet.</div>
                    @endforelse
                </div>
                <button class="btn btn-glass-outline w-100 mt-2">View All Transactions</button>
            </div>
        </div>
    </div>
    <div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="glass p-4 text-center">
            <div class="circular-progress-ui mb-3">
                <span class="h4 fw-bold text-info">92%</span>
            </div>
            <h6 class="small  text-uppercase mb-3">Auction Success Rate</h6>
            <p class="small mb-0">Lots Sold vs. Listed</p>
        </div>
    </div>

    <div class="col-md-3">
        <div class="glass p-4 text-center">
            <div class="mb-3">
                <i class="bi bi-hammer text-warning fs-1"></i>
            </div>
            <h6 class="small  text-uppercase">Avg. Bids Per Lot</h6>
            <h4 class="fw-bold mb-0">14.5</h4>
            <p class="text-success small">+12% vs last month</p>
        </div>
    </div>

    <div class="col-md-6">
        <div class="glass p-4">
            <h6 class="small  text-uppercase text-white mb-3">Top Revenue Contributors</h6>
            <div class="species-bar mb-2">
                <div class="d-flex justify-content-between small mb-1">
                    <span>Yellowfin Tuna</span>
                    <span>$420k (45%)</span>
                </div>
                <div class="progress bg-dark" style="height: 6px;">
                    <div class="progress-bar bg-info" style="width: 45%"></div>
                </div>
            </div>
            <div class="species-bar mb-2">
                <div class="d-flex justify-content-between small mb-1">
                    <span>King Lobster</span>
                    <span>$280k (30%)</span>
                </div>
                <div class="progress bg-dark" style="height: 6px;">
                    <div class="progress-bar bg-primary" style="width: 30%"></div>
                </div>
            </div>
            <div class="species-bar">
                <div class="d-flex justify-content-between small mb-1">
                    <span>Others</span>
                    <span>$230k (25%)</span>
                </div>
                <div class="progress bg-dark" style="height: 6px;">
                    <div class="progress-bar bg-secondary" style="width: 25%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="glass p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold m-0">Detailed Auction Performance Log</h6>
                <div class="search-box-glass">
                  
                    <input type="text" placeholder="Search Lot ID..." class="glass-input">
                </div>
            </div>
            <div class="table-responsive">
                <table class="submitted-table">
                    <thead>
                        <tr class=" small">
                            <th>LOT ID</th>
                            <th>SPECIES</th>
                            <th>QTY</th>
                            <th>START PRICE</th>
                            <th>FINAL PRICE</th>
                            <th>BIDS</th>
                            <th>PLATFORM FEE</th>
                            <th>NET EARNED</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($lots ?? collect()) as $lot)
                            @php
                                $finalPrice = (float) ($lot->final_price ?? 0);
                                $gross = (float) ($lot->gross_amount ?? 0);
                                $commission = (float) ($lot->commission_amount ?? 0);
                                $net = (float) ($lot->net_amount ?? 0);
                                $status = $lot->status === 'unsold' ? 'unsold' : ($lot->settlement_status_label ?? 'pending');
                                $statusClass = $status === 'paid' ? 'success' : ($status === 'unsold' ? 'warning' : 'warning');
                                $statusText = $status === 'paid' ? 'Settled' : ($status === 'unsold' ? 'Unsold' : 'Pending');
                            @endphp
                            <tr class="table-row-glass">
                                <td>#LOT-{{ str_pad((string) $lot->id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $lot->species ?? '-' }}</td>
                                <td>{{ number_format((float) ($lot->quantity ?? 0), 2) }}kg</td>
                                <td>${{ number_format((float) ($lot->starting_price ?? 0), 2) }}</td>
                                <td class="text-info fw-bold">${{ number_format($finalPrice, 2) }}</td>
                                <td>{{ $lot->bids_count ?? 0 }}</td>
                                <td class="text-danger">-${{ number_format($commission, 2) }}</td>
                                <td class="text-success fw-bold">${{ number_format($net, 2) }}</td>
                                <td><span class="status-pill {{ $statusClass }}">{{ $statusText }}</span></td>
                            </tr>
                        @empty
                            <tr class="table-row-glass">
                                <td colspan="9" class="text-white-50">No auction records yet.</td>
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
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" data-cfasync="false"></script>

<script>
    // Analytics Chart
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'],
            datasets: [
                {
                    label: 'Revenue ($)',
                    data: [85000, 120000, 150000, 110000, 190000, 210000],
                    backgroundColor: 'rgba(0, 208, 132, 0.6)',
                    borderColor: '#00ff88',
                    borderWidth: 1,
                    borderRadius: 5,
                    yAxisID: 'y'
                },
                {
                    label: 'Volume (kg)',
                    data: [1200, 1800, 2200, 1600, 2500, 2800],
                    type: 'line',
                    borderColor: '#ffc107',
                    borderWidth: 3,
                    tension: 0.4,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear', position: 'left',
                    grid: { color: 'rgba(255,255,255,0.05)' },
                    ticks: { color: 'rgba(255,255,255,0.5)' }
                },
                y1: {
                    type: 'linear', position: 'right',
                    grid: { display: false },
                    ticks: { color: 'rgba(255,193,7,0.5)' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: 'rgba(255,255,255,0.5)' }
                }
            },
            plugins: { legend: { display: false } }
        }
    });
</script>

@include('bid_web.seller.include.footer')

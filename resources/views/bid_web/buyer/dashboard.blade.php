@include('bid_web.buyer.include.header')

@include('bid_web.buyer.include.side_menu')


<style>
    @media screen and (max-width: 767px) {
  .desktop-view {
    display:none !important;
  }
  .mobile-view {
    display:flex !important;
  }

  .kpi-item {
     margin-bottom: 0px !important;
   }
   .page-wrapper {
        
        padding-top: 20px;
    }
}

</style>

<!-- Page Wrapper -->
         <div class="page-wrapper">
            <div class="content container-fluid">
  <div class="page-header">
    <div class="row align-items-center gap-3">
      <div class="col">
        <h1 class="page-title text-white">Buyer Dashboard</h1>
        <p class="text-white">Auction Management &amp; Perform Optimization</p>
      </div>
      <div class="col-auto">
         <button class="btn btn-primary btn-toggle me-2"><i class="bi bi-box"></i> {{ $stats['pending_lots'] ?? 0 }} lot pending</button> 
       
      </div>
    </div>
  </div>
  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
  @endif
          
   <div class="row g-3 desktop-view">
       
 <div class="col-lg-2 col-6">  
<div class="kpi-item">
    <div class="kpi-icon icon-orange">
        <i class="bi bi-box"></i>
    </div>
    <div class="kpi-content">
        <h3>{{ $stats['active_auctions'] ?? 0 }}</h3>
        <p>Active Auctions</p>
        <small>{{ $trends['active_auctions'] ?? 'Live auctions available now' }}</small>
    </div>
</div>
</div>
<div class="col-lg-2 col-6"> 
<div class="kpi-item">
    <div class="kpi-icon icon-blue">
        <i class="bi bi-check-lg"></i>
    </div>
    <div class="kpi-content">
        <h3>{{ $stats['auctions_won'] ?? 0 }}</h3>
        <p>Auctions Won</p>
        <small>{{ $trends['auctions_won'] ?? 'Settlements created for won lots' }}</small>
    </div>
</div>
</div>
<div class="col-lg-2 col-6"> 
<div class="kpi-item">
    <div class="kpi-icon icon-green">
        <i class="bi bi-currency-dollar"></i>
    </div>
    <div class="kpi-content">
        <h3>${{ number_format((float) ($stats['total_purchased'] ?? 0), 2) }}</h3>
        <p>Total Purchased</p>
        <small>{{ $trends['total_purchased'] ?? 'Total paid settlements' }}</small>
    </div>
</div>
</div>
<div class="col-lg-2 col-6"> 
<div class="kpi-item">
    <div class="kpi-icon icon-blue">
        <i class="bi bi-credit-card"></i>
    </div>
    <div class="kpi-content">
        <h3>${{ number_format((float) ($stats['credit_available'] ?? 0), 2) }}</h3>
        <p>Credit Available</p>
        <small>{{ $trends['credit_available'] ?? 'Wallet available balance' }}</small>
    </div>
</div>
</div>
<div class="col-lg-2 col-6"> 
<div class="kpi-item">
    <div class="kpi-icon icon-yellow">
        <i class="bi bi-exclamation-triangle"></i>
    </div>
    <div class="kpi-content">
        <h3>${{ number_format((float) ($stats['pending_payments'] ?? 0), 2) }}</h3>
        <p>Pending Payments</p>
        <small>{{ $trends['pending_payments'] ?? 'Pending + processing settlements' }}</small>
    </div>
</div>
</div>
<div class="col-lg-2 col-6"> 
<div class="kpi-item">
    <div class="kpi-icon icon-trophy">
        <i class="bi bi-trophy"></i>
    </div>
    <div class="kpi-content">
        <h3>{{ number_format((float) ($stats['win_rate'] ?? 0), 1) }}%</h3>
        <p>Win Rate</p>
        <small>{{ $trends['win_rate'] ?? 'Won lots vs lots bid on' }}</small>
    </div>
</div>
 </div>
 </div>
    <div class="row">
        <div class="col-lg-8">
            <div class="section-glass mb-3">
            <ul class="nav nav-tabs" id="myTab">
      <li class="nav-item"><button class="nav-link active" data-bs-target="#live" data-bs-toggle="tab">Live Auction</button></li>
      <li class="nav-item"><button class="nav-link" data-bs-target="#upcoming" data-bs-toggle="tab">Upcoming Auction</button></li>
      
   </ul>  

<div class="tab-content">
  <div class="tab-pane fade show active" id="live">
<div class="row g-4">

@forelse ($liveLots as $lot)
@php
    $highest = $highestBids[$lot->id] ?? null;
    $currentPrice = $highest ?? $lot->starting_price;
    $minBid = max((float) $lot->starting_price, (float) ($highest ?? 0) + 0.01);
    $timeLeft = $lot->auction_end_at ? $lot->auction_end_at->diffForHumans() : 'Live';
@endphp
<div class="col-md-4">
<div class="auction-card">
    <img src="{{ $lot->image_url }}" class="auction-img">
    <h6 class="mb-3 mt-1 fw-bold text-white">{{ $lot->title }}</h6>
    <span class="badge-soft badge-green">Live</span>
    <div class="mt-2 price">${{ number_format((float) $currentPrice, 2) }} <small>/kg</small></div>
    <div class="small-meta mt-1">
        <i class="bi bi-clock"></i> {{ $timeLeft }} &nbsp;
        <i class="bi bi-box"></i> {{ number_format((float) $lot->quantity, 0) }}
    </div>
    <div class="d-flex gap-2 mt-3">
        <button
            class="btn btn-success w-100 bid-btn"
            data-bs-toggle="modal"
            data-bs-target="#bidModal"
            data-lot-id="{{ $lot->id }}"
            data-lot-title="{{ $lot->title }}"
            data-min-bid="{{ number_format((float) $minBid, 2, '.', '') }}"
            data-current-bid="{{ number_format((float) $currentPrice, 2, '.', '') }}"
        >Bid Now</button>
        <a
            href="{{ route('buyer.live-auction', ['lot' => $lot->id]) }}"
            class="btn btn-primary w-100"
        >View</a>
    </div>
    <div class="mt-2 small-meta">Quantity: {{ number_format((float) $lot->quantity, 0) }}</div>
</div>
</div>
@empty
<div class="col-12">
    <div class="text-white-50">No live auctions right now.</div>
</div>
@endforelse

</div>
</div>
<div class="tab-pane fade" id="upcoming">
    <div class="row g-4">

@forelse ($upcomingLots as $lot)
@php
    $startLabel = $lot->auction_start_at ? $lot->auction_start_at->diffForHumans() : 'Upcoming';
@endphp
<div class="col-md-4">
<div class="auction-card">
    <img src="{{ $lot->image_url }}" class="auction-img">
    <h6 class="mb-3 mt-1 fw-bold text-white">{{ $lot->title }}</h6>
    <span class="badge-soft badge-blue">Upcoming</span>
    <div class="mt-2 price">${{ number_format((float) $lot->starting_price, 2) }} <small>/kg</small></div>
    <div class="small-meta mt-1">
        <i class="bi bi-clock"></i> {{ $startLabel }} &nbsp;
        <i class="bi bi-box"></i> {{ number_format((float) $lot->quantity, 0) }}
    </div>
    <div class="mt-3">
        <form method="POST" action="{{ route('buyer.upcoming-auction.remind', $lot->id) }}">
            @csrf
            <button class="btn btn-primary w-100" type="submit">Remind</button>
        </form>
    </div>
</div>
</div>
@empty
<div class="col-12">
    <div class="text-white-50">No upcoming auctions yet.</div>
</div>
@endforelse

</div>        
</div>

</div>
</div>
<div class="row g-3 mobile-view mb-3" style="display:none;">
       
 <div class="col-lg-2">  
<div class="kpi-item">
    <div class="kpi-icon icon-orange">
        <i class="bi bi-box"></i>
    </div>
    <div class="kpi-content">
        <h3>{{ $stats['active_auctions'] ?? 0 }}</h3>
        <p>Active Auctions</p>
        <small>{{ $trends['active_auctions'] ?? 'Live auctions available now' }}</small>
    </div>
</div>
</div>
<div class="col-lg-2"> 
<div class="kpi-item">
    <div class="kpi-icon icon-blue">
        <i class="bi bi-check-lg"></i>
    </div>
    <div class="kpi-content">
        <h3>{{ $stats['auctions_won'] ?? 0 }}</h3>
        <p>Auctions Won</p>
        <small>{{ $trends['auctions_won'] ?? 'Settlements created for won lots' }}</small>
    </div>
</div>
</div>
<div class="col-lg-2"> 
<div class="kpi-item">
    <div class="kpi-icon icon-green">
        <i class="bi bi-currency-dollar"></i>
    </div>
    <div class="kpi-content">
        <h3>${{ number_format((float) ($stats['total_purchased'] ?? 0), 2) }}</h3>
        <p>Total Purchased</p>
        <small>{{ $trends['total_purchased'] ?? 'Total paid settlements' }}</small>
    </div>
</div>
</div>
<div class="col-lg-2"> 
<div class="kpi-item">
    <div class="kpi-icon icon-blue">
        <i class="bi bi-credit-card"></i>
    </div>
    <div class="kpi-content">
        <h3>${{ number_format((float) ($stats['credit_available'] ?? 0), 2) }}</h3>
        <p>Credit Available</p>
        <small>{{ $trends['credit_available'] ?? 'Wallet available balance' }}</small>
    </div>
</div>
</div>
<div class="col-lg-2"> 
<div class="kpi-item">
    <div class="kpi-icon icon-yellow">
        <i class="bi bi-exclamation-triangle"></i>
    </div>
    <div class="kpi-content">
        <h3>${{ number_format((float) ($stats['pending_payments'] ?? 0), 2) }}</h3>
        <p>Pending Payments</p>
        <small>{{ $trends['pending_payments'] ?? 'Pending + processing settlements' }}</small>
    </div>
</div>
</div>
<div class="col-lg-2"> 
<div class="kpi-item">
    <div class="kpi-icon icon-trophy">
        <i class="bi bi-trophy"></i>
    </div>
    <div class="kpi-content">
        <h3>{{ number_format((float) ($stats['win_rate'] ?? 0), 1) }}%</h3>
        <p>Win Rate</p>
        <small>{{ $trends['win_rate'] ?? 'Won lots vs lots bid on' }}</small>
    </div>
</div>
 </div>
 </div>         

<div class="section-glass mb-3">
   <div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold text-white">My Active Bids</h5>
    <a href="{{ route('buyer.active-auction') }}" class="btn btn-info"> See All</a>
    
   </div>

  <div class="table-container table-responsive">
        <table class="submitted-table">
            <thead>
                <tr>
                    <th>Lot</th>
                    <th>My Bid</th>
                    <th>Highest Bid</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($activeBids ?? collect()) as $bidRow)
                <tr>
                    <td>
                        <div class="d-flex align-items-center flex-wrap gap-3">
                            <div>{{ $bidRow['lot_id'] }}</div>
                            <img class="lot-img" src="{{ $bidRow['lot']->image_url }}" style="width:50px; height:35px; margin-left:0px">
                        </div>
                    </td>
                    <td>${{ number_format((float) $bidRow['my_bid'], 2) }}/kg</td>
                    <td>${{ number_format((float) $bidRow['highest_bid'], 2) }}/kg</td>
                    <td>
                        @if($bidRow['is_highest'])
                            <span class="badge badge-success"><i class="bi bi-check-circle"></i> Highest Bidder</span>
                        @else
                            <span class="badge badge-primary"><i class="bi bi-lightning"></i> Outbid</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-white-50">No active bids yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
     </div>
  </div>
          
        </div>

        
   

     <!-- RIGHT SIDE -->
<div class="col-lg-4">
 <!-- My Active Bids -->
<div class="right-card mb-4">
<h6 class="fw-bold mb-3 text-white">My Active Bids</h6>
<div class="d-flex justify-content-between mt-3">
<div style="height:130px">
<canvas id="bidChart"></canvas>
</div>

<div class="mt-3">
    <div class="mb-2">
        <strong class="me-2 text-warning">${{ number_format((float) ($stats['credit_used'] ?? 0), 2) }}</strong>
        <small>(Credit Used)</small>
    </div>
    <div>
        <strong class="me-2 text-danger">${{ number_format((float) ($stats['pending_payments'] ?? 0), 2) }}</strong>
        <small>(Pending Payments)</small>
    </div>
</div>
</div>
<hr>

<h6 class="fw-bold mb-3 text-white">Pending Settlement</h6>
<div class="card p-3">
<canvas id="barChart" height="120"></canvas>
</div>
</div>

<!-- Analytics -->
<div class="right-card">
<h6 class="fw-bold mb-3 text-white">Analytics</h6>
<ul class="list-unstyled analytics-list">

<li>
    <i class="bi bi-water me-2 text-white"></i>
    Preferred Species 
    <span class="float-end fw-semibold">{{ $analytics['preferred_species'] ?? 'N/A' }}</span>
</li>

<li>
    <i class="bi bi-currency-dollar me-2 text-white"></i>
    Avg Bid Value 
    <span class="float-end fw-semibold">${{ number_format((float) ($analytics['avg_bid_value'] ?? 0), 2) }}/kg</span>
</li>

<li>
    <i class="bi bi-clock-history me-2 text-white"></i>
    Most Active Time Slot 
    <span class="float-end fw-semibold">{{ $analytics['active_time_slot'] ?? 'N/A' }}</span>
</li>

@forelse(($analytics['top_species'] ?? collect()) as $speciesRow)
<li>
    <i class="bi bi-graph-up-arrow me-2 text-white"></i>
    {{ $speciesRow->species }}
    <span class="float-end fw-semibold">${{ number_format((float) $speciesRow->avg_amount, 2) }}/kg</span>
</li>
@empty
<li>
    <i class="bi bi-bar-chart-line me-2 text-white"></i>
    No analytics yet
    <span class="float-end fw-semibold">-</span>
</li>
@endforelse

</ul>
</div>


 

 

</div>
</div>
</div>

              
              
            </div>
         </div>
         <!-- /Page Wrapper -->
      </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" data-cfasync="false"></script>

<!-- Bid Modal -->
<div class="modal fade" id="bidModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('buyer.place-bid') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Place Bid</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="lot_id" id="bidLotId">
                    <div class="mb-2">
                        <div class="fw-semibold" id="bidLotTitle">Lot</div>
                        <div class="text-muted small">Current: <span id="bidCurrentAmount">$0.00</span></div>
                    </div>
                    <label class="form-label" for="bidAmount">Your Bid ($/kg)</label>
                    <input type="number" step="0.01" min="0.01" class="form-control" id="bidAmount" name="amount" required>
                    <div class="form-text">Minimum bid: <span id="bidMinAmount">$0.00</span></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Bid</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const creditUsed = {!! json_encode((float) ($chartData['credit_used'] ?? 0)) !!};
const creditRemaining = {!! json_encode((float) ($chartData['credit_remaining'] ?? 0)) !!};
const settlementLabels = {!! json_encode($chartData['settlement_labels'] ?? []) !!};
const settlementValues = {!! json_encode($chartData['settlement_values'] ?? []) !!};

const bidChartLabelPlugin = {
    id: 'bidChartLabelPlugin',
    afterDraw(chart) {
        const total = (chart.data.datasets[0]?.data || []).reduce((sum, value) => sum + Number(value || 0), 0);
        if (total !== 0) return;

        const meta = chart.getDatasetMeta(0);
        if (!meta?.data?.length) return;

        const x = meta.data[0].x;
        const y = meta.data[0].y;
        const { ctx } = chart;
        ctx.save();
        ctx.fillStyle = '#9fbad7';
        ctx.font = '600 13px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('No wallet usage', x, y);
        ctx.restore();
    }
};

const barChartLabelPlugin = {
    id: 'barChartLabelPlugin',
    afterDraw(chart) {
        if ((chart.data.labels || []).length > 0) return;

        const { ctx, chartArea } = chart;
        if (!chartArea) return;

        ctx.save();
        ctx.fillStyle = '#9fbad7';
        ctx.font = '600 13px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('No pending settlements', (chartArea.left + chartArea.right) / 2, (chartArea.top + chartArea.bottom) / 2);
        ctx.restore();
    }
};

// Donut Chart
new Chart(document.getElementById('bidChart'),{
    type:'doughnut',
    data:{
        labels:['Used','Remaining'],
        datasets:[{
            data:[creditUsed, creditRemaining],
            backgroundColor:['#3d7eff','#cce0ff']
        }]
    },
    options:{ plugins:{ legend:{ display:false } } },
    plugins:[bidChartLabelPlugin]
});

// Bar Chart
new Chart(document.getElementById('barChart'),{
    type:'bar',
    data:{
        labels:settlementLabels,
        datasets:[{
            data:settlementValues,
            backgroundColor:'#3d7eff'
        }]
    },
    options:{
        plugins:{ legend:{ display:false } },
        scales:{
            y:{
                beginAtZero:true
            }
        }
    },
    plugins:[barChartLabelPlugin]
});

</script>

<script>
    document.querySelectorAll('.bid-btn[data-bs-target="#bidModal"]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var lotId = btn.getAttribute('data-lot-id');
            var lotTitle = btn.getAttribute('data-lot-title');
            var minBid = btn.getAttribute('data-min-bid');
            var currentBid = btn.getAttribute('data-current-bid');

            document.getElementById('bidLotId').value = lotId || '';
            document.getElementById('bidLotTitle').textContent = lotTitle || 'Lot';
            document.getElementById('bidMinAmount').textContent = '$' + (minBid || '0.00');
            document.getElementById('bidCurrentAmount').textContent = '$' + (currentBid || '0.00');

            var amountInput = document.getElementById('bidAmount');
            amountInput.min = minBid || '0.01';
            amountInput.value = minBid || '';
        });
    });
</script>

<script>
    (function () {
        var refreshDelay = 30000;

        function canReload() {
            var bidModal = document.getElementById('bidModal');
            return !bidModal || !bidModal.classList.contains('show');
        }

        setInterval(function () {
            if (document.hidden || !canReload()) {
                return;
            }

            window.location.reload();
        }, refreshDelay);
    })();
</script>

@include('bid_web.buyer.include.footer')

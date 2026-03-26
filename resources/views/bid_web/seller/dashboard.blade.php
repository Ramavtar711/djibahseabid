@include('bid_web.seller.include.header')

@include('bid_web.seller.include.side_menu')

@php
  $activeAuctionsCount = $activeAuctionsCount ?? 0;
  $totalLotsCount = $totalLotsCount ?? 0;
  $totalRevenue = $totalRevenue ?? 0;
  $averagePricePerKg = $averagePricePerKg ?? 0;
  $conversionRate = $conversionRate ?? 0;
  $unsoldRate = $unsoldRate ?? 0;
  $pendingPayoutTotal = $pendingPayoutTotal ?? 0;
  $paidAmountTotal = $paidAmountTotal ?? 0;
  $isLive = $activeAuctionsCount > 0;
@endphp

<!-- Page Wrapper -->
         <div class="page-wrapper">
            <div class="content container-fluid">
              <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <h1 class="page-title text-white">Seller Dashboard</h1>
        <p class="text-white">Auction Management & Performance Optimization</p>
      </div>
      <div class="col-auto">
        <span id="liveStatusBadge" class="badge {{ $isLive ? 'bg-success' : 'bg-secondary' }} px-3 py-2">
          <i class="bi bi-broadcast me-2"></i><span id="liveStatusText">{{ $isLive ? 'LIVE NOW' : 'OFFLINE' }}</span>
        </span>
      </div>
    </div>
  </div>
          
    <div class="row g-3 mb-4">
         <div class="col-lg-2 col-6">
            <div class="glass kpi">
                <div class="bg-warning p-1 px-2 rounded-1"> <i class="bi bi-house text-white"></i></div>
               <div>
                  <h4 id="activeAuctionCount">{{ $activeAuctionsCount }}</h4><small>Active Auctions</small>
               </div>
            </div>
         </div>
         <div class="col-lg-2 col-6">
            <div class="glass kpi">
               <div class="bg-success p-1 px-2 rounded-1"><i class="bi bi-box-seam text-white"></i></div>
               <div>
                  <h4>{{ $totalLotsCount }}</h4><small>Total Lots Created</small>
               </div>
            </div>
         </div>
         <div class="col-lg-2 col-6">
            <div class="glass kpi">
               <div class="bg-info p-1 px-2 rounded-1"><i class="bi bi-currency-dollar text-white"></i></div>
               <div>
                  <h4>${{ number_format($totalRevenue, 2) }}</h4><small>Total Revenue</small>
               </div>
            </div>
         </div>
         <div class="col-lg-2 col-6">
            <div class="glass kpi">
               <div class="bg-success p-1 px-2 rounded-1"><i class="bi bi-graph-up-arrow text-white"></i></div>
               <div>
                  <h4>${{ number_format($averagePricePerKg, 2) }}</h4><small>Avg Price /kg</small>
               </div>
            </div>
         </div>
         <div class="col-lg-2 col-6">
            <div class="glass kpi">
               <div class="bg-warning p-1 px-2 rounded-1"><i class="bi bi-bar-chart text-white"></i></div>
               <div>
                  <h4>{{ $conversionRate }}%</h4><small>Conversion Rate</small>
               </div>
            </div>
         </div>
         <div class="col-lg-2 col-6">
            <div class="glass kpi">
               <div class="bg-danger p-1 px-2 rounded-1"><i class="bi bi-arrow-counterclockwise text-white"></i></div>
               <div>
                  <h4>{{ $unsoldRate }}%</h4><small>Unsold Lots</small>
               </div>
            </div>
         </div>
      </div>
    <div class="row">
        <div class="col-lg-8">
            <div class="section-glass mb-3">
         
              
               <h5 class="mb-3 text-white">My Active Auctions</h5>
               <div id="activeAuctionsGrid" class="row g-3">
                  @forelse(($activeAuctions ?? collect()) as $auction)
                     <div class="col-md-4">
                        <div class="auction-card text-white">
                           <div class="auction-title mb-2">
                              {{ $auction->title ?? 'Lot' }}
                           </div>
                           <img src="{{ $auction->image_url }}">
                           <span class="badge bg-success mb-2">Live</span>
                           <div>
                              ${{ number_format($auction->current_price ?? 0, 2) }} /kg
                           </div>
                           <small>{{ $auction->bids_count ?? 0 }} Bids</small>
                           <div class="d-flex justify-content-between gap-2 mt-2">
                              <button class="btn btn-sm btn-success w-100 bid-btn px-1 py-2" type="button">Extend +5m</button>
                              <a class="btn btn-sm btn-primary w-100 bid-btn px-1 py-2" href="{{ route('seller.active-auction', ['lot' => $auction->id]) }}">Live View</a>
                           </div>
                        </div>
                     </div>
                  @empty
                     <div class="col-12">
                        <div class="text-white-50">No active auctions right now.</div>
                     </div>
                  @endforelse
               </div>
           

           </div>
         

<div class="section-glass">
   <div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold text-white">Payout & Finance</h5>
   
    
  </div>
   <div class="row g-3 mb-4">
         <div class="col-lg-6 col-6">
            <div class="glass kpi">
                <div class="bg-warning p-1 px-2 rounded-1"> <i class="bi bi-currency-dollar text-white"></i></div>
               <div  class="d-flex justify-content-between align-items-center gap-2">
                  <h4>${{ number_format($pendingPayoutTotal, 2) }}</h4><small>Pending Payment</small>
               </div>
            </div>
         </div>
         <div class="col-lg-6 col-6">
            <div class="glass kpi">
                <div class="bg-success p-1 px-2 rounded-1"> <i class="bi bi-currency-dollar text-white"></i></div>
               <div class="d-flex justify-content-between align-items-center gap-2">
                  <h4>${{ number_format($paidAmountTotal, 2) }}</h4><small>Paid Amount</small>
               </div>
            </div>
         </div>
    </div>
  <div class="table-containertable-responsive">
        <table class="submitted-table">
            <thead>
                <tr>
                  <th>Date</th>
                    <th>Lot</th>
                    <th>Species</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($soldLots ?? collect()) as $lot)
                    @php
                        $statusKey = $lot->status === 'unsold'
                            ? 'unsold'
                            : strtolower((string) ($lot->settlement_status_label ?? 'pending'));
                        $statusLabel = match ($statusKey) {
                            'paid' => 'Paid',
                            'processing' => 'Processing',
                            'shipped' => 'Shipped',
                            'unsold' => 'Unsold',
                            default => 'Pending Payment',
                        };
                        $statusClass = match ($statusKey) {
                            'paid' => 'bg-success',
                            'processing' => 'bg-primary',
                            'shipped' => 'bg-info text-dark',
                            'unsold' => 'bg-secondary',
                            default => 'bg-warning text-dark',
                        };
                    @endphp
                    <tr>
                      <td>{{ optional($lot->auction_end_at ?? $lot->created_at)->format('d M') }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                            <img class="lot-img" src="{{ $lot->image_url }}">
                            <div>{{ $lot->id }}</div></div>
                        </td>
                        <td>{{ $lot->species ?? '-' }}</td>
                        <td>${{ number_format((float) ($lot->final_price ?? 0), 2) }}/kg</td>
                        <td><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-white-50">No payout records yet.</td>
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
<h5 class="fw-bold mb-3 text-white">Performance Analytics</h5>
<div class="mt-3">
<div class="text-white text-center">

           <center>    <canvas id="speciesChart"></canvas></center>
</div>


</div>
<hr>


<div class="glass p-3">
<div class="d-flex justify-content-between mb-2">
<h5 class="mb-0 text-white">Avg Time to Sell</h5>
<strong>4m 23s</strong>
</div>

<canvas id="sellChart" height="150"></canvas>
</div>
</div>

<!-- Analytics -->
<div class="right-card">
<h5 class="fw-bold mb-3 text-white">Inventory Management</h5>
<ul class="list-unstyled analytics-list">

<li>
    <i class="bi bi-droplet-fill me-2 text-white"></i>
    Fresh Stock
    <span class="float-end fw-semibold">5400kg</span>
</li>

<li>
    <i class="bi bi-snow me-2 text-white"></i>
    Frozen Stock

    <span class="float-end fw-semibold">5200kg</span>
</li>
<li>
    <i class="bi bi-box-seam me-2 text-white"></i>
    Upcoming lots

    <span class="float-end fw-semibold">4</span>
</li>
<li>
    <i class="bi bi-exclamation-circle me-2 text-white"></i>
    Qc Pending

    <span class="float-end fw-semibold">2</span>
</li>


</ul>
</div>


 

 

</div>
</div>
</div>

              
              
            </div>
         </div>
         <!-- /Page Wrapper -->

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" data-cfasync="false"></script>

<script>
new Chart(document.getElementById("speciesChart"),{

type:"doughnut",

data:{
labels:["Swordfish","Lobster","Tuna","Snapper"],
datasets:[{
data:[112,69,59,68],
backgroundColor:[
"#4dabf7",
"#69db7c",
"#ffd43b",
"#ff8787"
]
}]
},

options:{
plugins:{
legend:{
labels:{
color:"#ffffff"   // legend text color
}
},
tooltip:{
titleColor:"#ffffff",
bodyColor:"#ffffff"
}
}
}

});
</script>

<script>

const ctx = document.getElementById("sellChart");

new Chart(ctx, {

type: "line",

data: {
labels: [
"Late 30 Days",
"",
"",
"",
"",
"Last 30 Days",
"",
"",
"",
"",
"Latest 30 Days"
],

datasets: [

{
label:"Auction Trend",
data:[3200,4200,5500,6200,7100,9000,6500,7200,6800,7900,9500],
borderColor:"#4a7cff",
backgroundColor:"rgba(74,124,255,0.20)",
fill:true,
tension:0.4,
pointRadius:0,
borderWidth:3
},

{
label:"Average",
data:[2800,3100,3400,3600,4200,4800,5000,5200,5400,5600,5800],
borderColor:"#32d296",
backgroundColor:"rgba(50,210,150,0.15)",
fill:true,
tension:0.4,
pointRadius:0,
borderWidth:3
}

]
},

options: {

plugins:{
legend:{display:false}
},

scales: {

x:{
ticks:{
color:"#ffffff",
font:{size:11}
},
grid:{
display:false
}
},

y:{
ticks:{
color:"#ffffff"
},
grid:{
color:"rgba(255,255,255,0.2)",
borderDash:[4,4]
}
}

}

}

});

</script>

<script>
  const statusUrl = "{{ route('seller.auction-status') }}";
  const activeAuctionsUrl = "{{ route('seller.active-auctions.data') }}";
  const statusBadge = document.getElementById('liveStatusBadge');
  const statusText = document.getElementById('liveStatusText');
  const activeCountEl = document.getElementById('activeAuctionCount');
  const activeGrid = document.getElementById('activeAuctionsGrid');

  function applyLiveStatus(data) {
    if (!statusBadge || !statusText || !activeCountEl) return;

    const isLive = !!data.is_live;
    statusText.textContent = isLive ? 'LIVE NOW' : 'OFFLINE';
    statusBadge.classList.remove('bg-success', 'bg-secondary');
    statusBadge.classList.add(isLive ? 'bg-success' : 'bg-secondary');
    if (typeof data.active_count === 'number') {
      activeCountEl.textContent = data.active_count;
    }
  }

  function refreshLiveStatus() {
    fetch(statusUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (res) { return res.json(); })
      .then(applyLiveStatus)
      .catch(function () {});
  }

  function escapeHtml(value) {
    var div = document.createElement('div');
    div.textContent = value ?? '';
    return div.innerHTML;
  }

  function renderActiveAuctions(items) {
    if (!activeGrid) return;
    if (!items || !items.length) {
      activeGrid.innerHTML = '<div class="col-12"><div class="text-white-50">No active auctions right now.</div></div>';
      return;
    }

    var html = items.map(function (item) {
      var title = escapeHtml(item.title || 'Lot');
      var imageUrl = escapeHtml(item.image_url || '');
      var price = Number(item.current_price || 0).toFixed(2);
      var bids = Number(item.bids_count || 0);
      var liveUrl = "{{ route('seller.active-auction') }}" + '?lot=' + encodeURIComponent(item.id);

      return '' +
        '<div class="col-md-4">' +
          '<div class="auction-card text-white">' +
            '<div class="auction-title mb-2">' + title + '</div>' +
            '<img src="' + imageUrl + '">' +
            '<span class="badge bg-success mb-2">Live</span>' +
            '<div>$' + price + ' /kg</div>' +
            '<small>' + bids + ' Bids</small>' +
            '<div class="d-flex justify-content-between gap-2 mt-2">' +
              '<button class="btn btn-sm btn-success w-100 bid-btn px-1 py-2" type="button">Extend +5m</button>' +
              '<a class="btn btn-sm btn-primary w-100 bid-btn px-1 py-2" href="' + liveUrl + '">Live View</a>' +
            '</div>' +
          '</div>' +
        '</div>';
    }).join('');

    activeGrid.innerHTML = html;
  }

  function refreshActiveAuctions() {
    fetch(activeAuctionsUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (res) { return res.json(); })
      .then(function (data) { renderActiveAuctions(data.items || []); })
      .catch(function () {});
  }

  refreshLiveStatus();
  refreshActiveAuctions();
  setInterval(function () {
    refreshLiveStatus();
    refreshActiveAuctions();
  }, 20000);
</script>

@include('bid_web.seller.include.footer')

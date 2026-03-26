@include('bid_web.buyer.include.header')

@include('bid_web.buyer.include.side_menu')

<!-- Page Wrapper -->
         <div class="page-wrapper">
            <div class="content container-fluid">
              <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <h1 class="page-title text-white">Won Auction</h1>
        <p class="text-white">Won Auction Management &amp; Perform Optimization</p>
      </div>
      <div class="col-auto">
         <span class="btn btn-primary btn-toggle me-2 text-white"><i class="bi bi-box"></i> LIVE: {{ $liveCount ?? 0 }} LOTS</span> 
       
      </div>
    </div>
  </div>
          
    <div class="col-lg-12">
      <nav class="filter-nav">
            <form method="GET" class="d-flex gap-3 align-items-center">
                <input type="text" name="search" class="form-control" placeholder="Search Lot ID or Species..." value="{{ $search ?? '' }}">
                <select name="status" class="form-select" style="width: 150px;">
                    <option value="all" {{ ($statusFilter ?? 'all') === 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="pending" {{ ($statusFilter ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ ($statusFilter ?? '') === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="failed" {{ ($statusFilter ?? '') === 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="paid" {{ ($statusFilter ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="expired" {{ ($statusFilter ?? '') === 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
                <button class="btn btn-primary" type="submit">Filter</button>
            </form>
            <div class="text-end">
                <span class="small text-white me-2">Total Due:</span>
                <span class="fw-bold text-success fs-5">${{ number_format($totalDue ?? 0, 2) }}</span>
            </div>
        </nav>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="mb-3">


<div class="ledger-container">
            <table class="table-ledger">
                <thead>
                    <tr>
                        <th>Lot ID</th>
                        <th>Product Details</th>
                        <th>Qty / Weight</th>
                        <th>Win Price</th>
                        <th>Total Amount</th>
                        <th>Payment Status</th>
                        <th>Deadline</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(($settlements ?? collect()) as $settlement)
                        @php
                            $lot = $settlement->lot;
                            $status = $settlement->status ?? 'pending';
                            $statusClass = $status === 'paid' ? 'status-paid' : ($status === 'processing' ? 'status-processing' : 'status-pending');
                            $statusLabel = $status === 'paid' ? 'Paid' : ($status === 'processing' ? 'Processing' : ($status === 'failed' ? 'Failed' : ($status === 'expired' ? 'Expired' : 'Pending')));
                            $deadline = $settlement->deadline_at ? $settlement->deadline_at->format('M d • H:i') : '—';
                        @endphp
                        <tr>
                            <td class="lot-id">#{{ str_pad((string) $settlement->lot_id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td><span class="fw-bold d-block">{{ $lot->title ?? ($lot->species ?? 'Lot') }}</span><small class="opacity-50 text-uppercase">{{ $lot->species ?? 'Seafood' }}</small></td>
                            <td>{{ $lot->quantity ?? 0 }} KG</td>
                            <td class="price-tag">${{ number_format((float) ($lot->final_price ?? $settlement->amount ?? 0), 2) }}/kg</td>
                            <td class="total-val">${{ number_format((float) ($settlement->amount ?? 0), 2) }}</td>
                            <td><span class="status-pill {{ $statusClass }}">{{ $statusLabel }}</span></td>
                            <td><span class="deadline-text">{{ $status === 'paid' ? 'Settled' : $deadline }}</span></td>
                            <td class="text-end">
                                @if ($status === 'paid')
                                    <span class="btn btn-success fw-bold">Receipt Sent <i class="bi bi-check-lg"></i></span>
                                @elseif ($status === 'processing')
                                    <button class="btn-primary disabled opacity-50" style="background:#9E9E9E">Verifying</button>
                                @elseif ($status === 'expired')
                                    <span class="btn btn-secondary disabled opacity-75">Expired</span>
                                @else
                                    <a class="btn btn-primary pay-now-btn" href="{{ route('buyer.payments.show', $settlement->id) }}">Pay Now</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-white-50">No won auctions yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
     </div>
  </div>
</div>
         



  
          
        </div>
        @if(($settlements ?? null) && method_exists($settlements, 'links'))
            <div class="d-flex justify-content-center mt-4">
                {{ $settlements->links() }}
            </div>
        @endif

</div>
</div>

              
              
            </div>
         </div>
         <!-- /Page Wrapper -->
      </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" data-cfasync="false"></script>


<script>

// Donut Chart
new Chart(document.getElementById('bidChart'),{
    type:'doughnut',
    data:{
        labels:['Used','Remaining'],
        datasets:[{
            data:[12500, 37500],
            backgroundColor:['#3d7eff','#cce0ff']
        }]
    },
    options:{ plugins:{ legend:{ display:false } } }
});

// Bar Chart
new Chart(document.getElementById('barChart'),{
    type:'bar',
    data:{
        labels:['Mon','Tue','Wed','Thu','Fri'],
        datasets:[{
            data:[500,900,700,1000,1200],
            backgroundColor:'#3d7eff'
        }]
    },
    options:{ plugins:{ legend:{ display:false } } }
});

</script>

@include('bid_web.buyer.include.footer')





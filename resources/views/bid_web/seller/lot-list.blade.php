@include('bid_web.seller.include.header')

@include('bid_web.seller.include.side_menu')

<!-- Page Wrapper -->
         <div class="page-wrapper">
            <div class="content container-fluid">
              <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <h1 class="page-title text-white">All Lot List</h1>
        <p class="text-white">Seller Auction Management &amp; Perform Optimization</p>
      </div>
      <div class="col-auto">
         <a class="btn btn-primary" href="{{ route('seller.create-lot') }}"><i class="bi bi-plus-lg"></i> Create New Lot</a>
       
      </div>
    </div>
  </div>
          
   
    <div class="row g-4 mb-5">
      <div class="col-lg-3">
         <div class="glass p-3 d-flex justify-content-between align-items-center">
            <div>
               <h6 class="text-white">Total Lots</h6>
               <h3 class="text-white">{{ $totalLots }}</h3>
            </div>
            <div class="icon-box bg-primary">
               <i class="bi bi-box-seam"></i>
            </div>
         </div>
      </div>
      <div class="col-lg-3">
         <div class="glass p-3 d-flex justify-content-between align-items-center">
            <div>
               <h6 class="text-white">Pending Validation</h6>
               <h3 class="text-white">{{ $pendingValidationCount }}</h3>
            </div>
            <div class="icon-box bg-warning">
               <i class="bi bi-hourglass-split"></i>
            </div>
         </div>
      </div>
      <div class="col-lg-3">
         <div class="glass p-3 d-flex justify-content-between align-items-center">
            <div>
               <h6 class="text-white">Active Auctions</h6>
               <h3 class="text-white">{{ $activeAuctionsCount }}</h3>
            </div>
            <div class="icon-box bg-info">
               <i class="bi bi-broadcast"></i>
            </div>
         </div>
      </div>
      <div class="col-lg-3">
         <div class="glass p-3 d-flex justify-content-between align-items-center">
            <div>
               <h6 class="text-white">Sold Lots</h6>
               <h3 class="text-white">{{ $soldLotsCount }}</h3>
            </div>
            <div class="icon-box bg-success">
               <i class="bi bi-cart-check"></i>
            </div>
         </div>
      </div>
   </div>

   @php
      $searchValue = request('search');
      $selectedStatus = request('status');
      $selectedSpecies = request('species');
      $selectedDate = request('date');
   @endphp
   <div>
      <div>
         @if(session('success'))
            <div class="alert alert-success text-white" id="lot-success" role="alert">
               {{ session('success') }}
            </div>
         @endif
         <div class="glass p-3 mb-4">
            <form class="row g-3 align-items-end" method="GET" action="{{ route('seller.lot-list') }}">
               <div class="col-lg-4">
                  <label class="form-label text-white">Search Lot</label>
                  <input class="form-control glass-input" placeholder="Lot ID or species" type="text" name="search" value="{{ $searchValue }}">
               </div>
               <div class="col-lg-2">
                  <label class="form-label text-white">Status</label>
                  <select class="form-select glass-input" name="status">
                     <option value="">All Status</option>
                     @foreach($statusOptions as $statusOption)
                        <option value="{{ $statusOption }}" {{ $selectedStatus === $statusOption ? 'selected' : '' }}>
                           {{ ucwords($statusOption) }}
                        </option>
                     @endforeach
                  </select>
               </div>
               <div class="col-lg-2">
                  <label class="form-label text-white">Species</label>
                  <select class="form-select glass-input" name="species">
                     <option value="">All Species</option>
                     @foreach($speciesOptions as $speciesOption)
                        <option value="{{ $speciesOption }}" {{ $selectedSpecies === $speciesOption ? 'selected' : '' }}>
                           {{ $speciesOption }}
                        </option>
                     @endforeach
                  </select>
               </div>
               <div class="col-lg-2">
                  <label class="form-label text-white">Date</label>
                  <input class="form-control glass-input" type="date" name="date" value="{{ $selectedDate }}">
               </div>
               <div class="col-lg-1 d-grid">
                  <button class="btn btn-primary" type="submit">Filter</button>
               </div>
            </form>
         </div>
      </div>
   </div>
    <div class="mb-2 row">
      <div class="col-lg-12">
      <div class="glass p-2">
         @php
            $statusClasses = [
                'draft' => 'bg-secondary',
                'pending qc' => 'bg-warning text-dark',
                'pending payment' => 'bg-warning text-dark',
                'approved' => 'bg-success',
                'scheduled' => 'bg-primary',
                'active auction' => 'bg-info text-dark',
                'active' => 'bg-info text-dark',
                'processing' => 'bg-info text-dark',
                'shipped' => 'bg-info text-dark',
                'completed' => 'bg-success',
                'sold' => 'bg-dark',
            ];
         @endphp
         <table class="submitted-table">
           <thead>
<tr>
<th>Lot ID</th>
<th>Image</th>
<th>Species</th>
<th>Quantity</th>
<th>Starting Price</th>
<th>Status</th>
<th>Date</th>
<th>Action</th>
</tr>
</thead>
            <tbody>
                @forelse($lots as $lot)
                    @php
                        $statusKey = strtolower(trim($lot->status ?? 'draft'));
                        $badgeClass = $statusClasses[$statusKey] ?? 'bg-secondary';
                    @endphp
                    <tr>
                        <td>#LOT{{ $lot->id }}</td>
                        <td><img src="{{ $lot->image_url }}" class="prod-img" alt="{{ $lot->title ?? 'Lot image' }}"></td>
                        <td>{{ $lot->species }}</td>
                        <td>{{ number_format($lot->quantity, 2) }}kg</td>
                        <td>${{ number_format($lot->starting_price, 2) }}/kg</td>
                        <td><span class="badge {{ $badgeClass }}">{{ ucfirst($lot->status ?? 'Draft') }}</span></td>
                        <td>{{ optional($lot->created_at)->format('d M') }}</td>
                        <td><a class="btn btn-sm btn-primary" href="{{ route('seller.lot-details', $lot->id) }}">View</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-white">No lots found yet.</td>
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
         </div>
         <!-- /Page Wrapper -->
@if(session('success'))
<script>
    setTimeout(() => {
        const alertEl = document.getElementById('lot-success');
        if (alertEl) {
            alertEl.style.transition = 'opacity 0.5s ease';
            alertEl.style.opacity = '0';
            setTimeout(() => {
                alertEl.remove();
            }, 500);
        }
    }, 5000);
</script>
@endif

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

@include('bid_web.seller.include.footer')

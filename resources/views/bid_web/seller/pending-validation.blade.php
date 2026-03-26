@include('bid_web.seller.include.header')

@include('bid_web.seller.include.side_menu')

@php
    $searchValue = request('search');
    $selectedStatus = request('status');
    $selectedSpecies = request('species');
    $selectedDate = request('date');

    $statusClasses = [
        'draft' => 'bg-secondary',
        'pending qc' => 'bg-warning text-dark',
    ];
@endphp

<!-- Page Wrapper -->
         <div class="page-wrapper">
            <div class="content container-fluid">
              <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <h1 class="page-title text-white">Pending Lot Validation </h1>
        <p class="text-white">Seller Auction Management &amp; Perform Optimization</p>
      </div>
      <div class="col-auto">
       
       
      </div>
    </div>
  </div>
          
   
    <div class="row g-4 mb-4">
      <div class="col-lg-3">
         <div class="glass p-3 d-flex justify-content-between align-items-center">
            <div>
               <p class="text-white mb-1">Pending Lots</p>
               <h4 class="text-white">{{ $pendingLotsCount }}</h4>
            </div>
            <div class="icon-box bg-warning">
               <i class="bi bi-hourglass-split"></i>
            </div>
         </div>
      </div>
      <div class="col-lg-3">
        <div class="glass p-3 d-flex justify-content-between align-items-center">
            <div>
               <p class="text-white mb-1">Submitted Today</p>
               <h4 class="text-white">{{ $submittedTodayCount }}</h4>
            </div>
            <div class="icon-box bg-info">
               <i class="bi bi-upload"></i>
            </div>
         </div>
      </div>
      <div class="col-lg-3">
         <div class="glass p-3 d-flex justify-content-between align-items-center">
            <div>
               <p class="text-white mb-1">Under QC</p>
               <h4 class="text-white">{{ $underQcCount }}</h4>
            </div>
            <div class="icon-box bg-primary">
               <i class="bi bi-search"></i>
            </div>
         </div>
      </div>
      <div class="col-lg-3">
        <div class="glass p-3 d-flex justify-content-between align-items-center">
            <div>
               <p class="text-white mb-1">Draft</p>
               <h4 class="text-white">{{ $rejectedCount }}</h4>
            </div>
            <div class="icon-box bg-secondary">
               <i class="bi bi-file-earmark"></i>
            </div>
         </div>
      </div>
   </div>

   <div>
      <div>
         <div class="glass p-3 mb-4">
            <form class="row g-3 align-items-end" method="GET" action="{{ route('seller.pending-validation') }}">
               <div class="col-lg-3">
                  <label class="form-label text-white">Search Lot</label>
                  <input class="form-control glass-input" name="search" placeholder="Lot ID or species" value="{{ $searchValue }}">
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
                  <label class="form-label text-white">Submission Date</label>
                  <input class="form-control glass-input" type="date" name="date" value="{{ $selectedDate }}">
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
               <div class="col-lg-3">
                  <button class="btn btn-primary" type="submit">Apply Filters</button>
                  <a class="btn btn-outline-light" href="{{ route('seller.pending-validation') }}">Reset</a>
               </div>
            </form>
   </div>
      </div>
   </div>
    <div class="mb-2 row">
      <div class="col-lg-12">
      <div class="glass p-3">

<h5 class="mb-3">Lots Pending Validation</h5>

<table class="submitted-table">

<thead>
<tr>
<th>Lot ID</th>
<th>Image</th>
<th>Species</th>
<th>Quantity</th>
<th>Starting Price</th>
<th>Submission Date</th>
<th>QC Status</th>
<th>Action</th>
</tr>
</thead>

<tbody>
@forelse($pendingLots as $lot)
@php
   $statusKey = strtolower(trim($lot->status ?? 'pending qc'));
   $badgeClass = $statusClasses[$statusKey] ?? 'bg-secondary';
@endphp
<tr>
<td>#LOT{{ $lot->id }}</td>
<td><img src="{{ $lot->image_url }}" class="prod-img" alt="{{ $lot->title ?? 'Lot image' }}"></td>
<td>{{ $lot->species ?: 'N/A' }}</td>
<td>{{ number_format((float) $lot->quantity, 2) }}kg</td>
<td>${{ number_format((float) $lot->starting_price, 2) }}/kg</td>
<td>{{ optional($lot->created_at)->format('d M') }}</td>
<td><span class="badge {{ $badgeClass }}">{{ ucwords($lot->status ?? 'Pending QC') }}</span></td>
<td><a class="btn btn-sm btn-primary" href="{{ route('seller.lot-details', $lot->id) }}">View</a></td>
</tr>
@empty
<tr>
<td colspan="8" class="text-center text-white">No pending validation lots found.</td>
</tr>
@endforelse

</tbody>

</table>

@if(method_exists($pendingLots, 'links'))
<div class="mt-4">
   {{ $pendingLots->links() }}
</div>
@endif

</div>
      </div>
    </div>
         



  
          
        </div>

        
   

</div>
</div>

              
              
            </div>
         </div>
         <!-- /Page Wrapper -->

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

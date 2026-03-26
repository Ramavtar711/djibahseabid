@include('bid_web.seller.include.header')

@include('bid_web.seller.include.side_menu')

@php
    $statusClasses = [
        'approved' => 'bg-success',
        'scheduled' => 'bg-info text-dark',
        'scheduled auction' => 'bg-primary',
    ];
@endphp

<!-- Page Wrapper -->
         <div class="page-wrapper">
            <div class="content container-fluid">
              <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <h1 class="page-title text-white">Approved Lots </h1>
        <p class="text-white">Seller Auction Management &amp; Perform Optimization</p>
      </div>
      <div class="col-auto">
       
       
      </div>
    </div>
  </div>
          
   
    <div class="mb-2 row">
      <div class="col-lg-12">
      <div class="glass p-3">
      <h5 class="mb-3">Approved Lots</h5>
      <table class="submitted-table">
         <thead>
            <tr>
               <th>Lot ID</th>
               <th>Images</th>
               <th>Species</th>
               <th>Validated Volume</th>
               <th>Starting Price</th>
               <th>Auction Date</th>
               <th>Auction Time</th>
               <th>Status</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
            @forelse($lots as $lot)
               @php
                  $statusKey = strtolower(trim($lot->status ?? 'approved'));
                  $badgeClass = $statusClasses[$statusKey] ?? 'bg-secondary';
                  $shareUrl = route('seller.lot-details', $lot->id);
               @endphp
               <tr>
                  <td>#LOT{{ $lot->id }}</td>
                  <td><img src="{{ $lot->image_url }}" class="prod-img" alt="{{ $lot->title ?? 'Lot image' }}"></td>
                  <td>{{ $lot->species ?: 'N/A' }}</td>
                  <td>{{ number_format((float) $lot->quantity, 2) }}kg</td>
                  <td>${{ number_format((float) $lot->starting_price, 2) }}/kg</td>
                  <td>{{ $lot->auction_start_at?->format('d M') ?? 'Not set' }}</td>
                  <td>{{ $lot->auction_start_at?->format('H:i') ?? 'Not set' }}</td>
                  <td><span class="badge {{ $badgeClass }}">{{ ucwords($lot->status ?? 'Approved') }}</span></td>
                  <td>
                     <a class="btn btn-sm btn-primary" href="{{ route('seller.lot-details', $lot->id) }}">View</a>
                     <button class="btn btn-sm btn-success js-share-lot" type="button" data-share-url="{{ $shareUrl }}">Share</button>
                  </td>
               </tr>
            @empty
               <tr>
                  <td colspan="9" class="text-center text-white">No approved lots found.</td>
               </tr>
            @endforelse
         </tbody>
      </table>

      @if(method_exists($lots, 'links'))
         <div class="mt-4">
            {{ $lots->links() }}
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

<script>
document.querySelectorAll('.js-share-lot').forEach((button) => {
    button.addEventListener('click', async () => {
        const shareUrl = button.dataset.shareUrl;

        try {
            if (navigator.clipboard && shareUrl) {
                await navigator.clipboard.writeText(shareUrl);
                button.textContent = 'Copied';
                setTimeout(() => {
                    button.textContent = 'Share';
                }, 1500);
            }
        } catch (error) {
            console.error('Unable to copy lot link.', error);
        }
    });
});
</script>

@include('bid_web.seller.include.footer')

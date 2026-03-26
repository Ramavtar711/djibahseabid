@include('bid_web.seller.include.header')

@include('bid_web.seller.include.side_menu')

@php
    $statusMap = [
        'pending' => ['label' => 'Pending Payment', 'class' => 'bg-warning text-dark'],
        'paid' => ['label' => 'Paid', 'class' => 'bg-success'],
        'processing' => ['label' => 'Processing', 'class' => 'bg-primary'],
        'shipped' => ['label' => 'Shipped', 'class' => 'bg-info text-dark'],
        'expired' => ['label' => 'Expired', 'class' => 'bg-dark'],
        'unsold' => ['label' => 'Unsold', 'class' => 'bg-secondary'],
    ];
@endphp

<!-- Page Wrapper -->
         <div class="page-wrapper">
            <div class="content container-fluid">
              <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <h1 class="page-title text-white">Sold Lots </h1>
        <p class="text-white">Seller Auction Management &amp; Perform Optimization</p>
      </div>
      <div class="col-auto">
       
       
      </div>
    </div>
  </div>
          
   
    <div class="mb-2 row">
      <div class="col-lg-12">
      <div class="glass p-3">
      <h5 class="mb-3">Sold Lots</h5>
      <table class="submitted-table">
         <thead>
            <tr>
               <th>Lot ID</th>
               <th>image</th>
               <th>Species</th>
               <th>Quantity</th>
               <th>Winning Price/kg</th>
               <th>Gross</th>
               <th>Commission</th>
               <th>Net Revenue</th>
               <th>Status</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
            @forelse(($lots ?? collect()) as $lot)
               @php
                  $winningPrice = (float) ($lot->final_price ?? 0);
                  $gross = (float) ($lot->gross_amount ?? 0);
                  $commission = (float) ($lot->commission_amount ?? 0);
                  $net = (float) ($lot->net_amount ?? 0);
                  $statusKey = $lot->status === 'unsold'
                     ? 'unsold'
                     : strtolower((string) ($lot->settlement_status_label ?? 'pending'));
                  $statusMeta = $statusMap[$statusKey] ?? ['label' => ucwords($statusKey), 'class' => 'bg-secondary'];
               @endphp
               <tr>
                  <td>#LOT-{{ str_pad((string) $lot->id, 4, '0', STR_PAD_LEFT) }}</td>
                  <td><img src="{{ $lot->image_url }}" class="prod-img"></td>
                  <td>{{ $lot->species ?? '-' }}</td>
                  <td>{{ number_format((float) ($lot->quantity ?? 0), 2) }}kg</td>
                  <td>${{ number_format($winningPrice, 2) }}</td>
                  <td>${{ number_format($gross, 2) }}</td>
                  <td>${{ number_format($commission, 2) }}</td>
                  <td class="text-success">${{ number_format($net, 2) }}</td>
                  <td><span class="badge {{ $statusMeta['class'] }}">{{ $statusMeta['label'] }}</span></td>
                  <td>
                     @if ($lot->status === 'unsold')
                        <form method="POST" action="{{ route('seller.relist-lot') }}">
                           @csrf
                           <input type="hidden" name="lot_id" value="{{ $lot->id }}">
                           <button class="btn btn-sm btn-outline-info" type="submit">Relist</button>
                        </form>
                     @else
                        <a class="btn btn-sm btn-primary" href="{{ route('seller.lot-details', $lot->id) }}">View</a>
                     @endif
                  </td>
               </tr>
            @empty
               <tr>
                  <td colspan="10" class="text-white-50">No sold/unsold lots yet.</td>
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

@include('bid_web.seller.include.footer')

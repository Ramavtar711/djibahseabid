@include('bid_admin.admin.include.header')
@include('bid_admin.admin.include.side_menu')

<div class="page-wrapper">
   <div class="content container-fluid">
      @if (! $lot)
         <div class="card border-0 shadow-sm p-4">
            <h3 class="fw-bold mb-2">Lot not found</h3>
            <p class="text-muted mb-3">Requested lot details are not available.</p>
            <a href="{{ route('admin.lot-management') }}" class="btn btn-primary">Back to Lot Management</a>
         </div>
      @else
         @php
            $lotLabel = '#LOT-' . str_pad((string) $lot->id, 4, '0', STR_PAD_LEFT);
            $statusLabel = strtoupper($lot->status ?? 'draft');
            $currentBid = $highestBidAmount ?: (float) ($lot->starting_price ?? 0);
            $quantity = (float) ($lot->quantity ?? 0);
            $totalValue = $currentBid * $quantity;
            $bidLeader = optional($bidHistory->first()?->buyer)->name ?? optional($lot->winner)->name ?? 'No bids yet';
            $countdown = $lot->auction_end_at && $lot->auction_end_at->isFuture()
               ? now()->diff($lot->auction_end_at)->format('%H:%I:%S')
               : 'Not live';
         @endphp

         <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
               <h2 class="fw-bold mb-1">{{ $lotLabel }} {{ $lot->title ?: ($lot->species ?: 'Lot Details') }}</h2>
               <p class="text-muted mb-0">Admin review page for submitted lot details and bidding activity.</p>
            </div>
            <a href="{{ route('admin.lot-management') }}" class="btn btn-primary">Back to Lots</a>
         </div>

         <div class="row g-4">
            <div class="col-lg-7">
               <div class="card border-0 shadow-sm p-3 h-100">
                  <div class="position-relative">
                     <img src="{{ $lot->image_url }}" alt="{{ $lot->title ?: 'Lot image' }}" class="w-100 rounded" style="height: 360px; object-fit: cover;">
                     <span class="badge {{ in_array(strtolower((string) $lot->status), ['active auction', 'active']) ? 'bg-danger' : 'bg-primary' }} position-absolute top-0 start-0 m-3 px-3 py-2">
                        {{ $statusLabel }}
                     </span>
                  </div>

                  <div class="row g-3 mt-1">
                     <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                           <p class="text-muted small fw-bold mb-2">Lot Overview</p>
                           <div class="d-flex justify-content-between mb-2"><span class="text-muted">Species</span><strong>{{ $lot->species ?: 'N/A' }}</strong></div>
                           <div class="d-flex justify-content-between mb-2"><span class="text-muted">Quantity</span><strong>{{ number_format($quantity, 2) }} kg</strong></div>
                           <div class="d-flex justify-content-between mb-2"><span class="text-muted">Harvest Date</span><strong>{{ optional($lot->harvest_date)->format('d M Y') ?? $lot->harvest_date ?? 'N/A' }}</strong></div>
                           <div class="d-flex justify-content-between"><span class="text-muted">Storage</span><strong>{{ $lot->storage_temperature ?: 'N/A' }}</strong></div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                           <p class="text-muted small fw-bold mb-2">Seller & Auction</p>
                           <div class="d-flex justify-content-between mb-2"><span class="text-muted">Seller</span><strong>{{ $lot->seller?->name ?: 'Unknown Seller' }}</strong></div>
                           <div class="d-flex justify-content-between mb-2"><span class="text-muted">Current Leader</span><strong>{{ $bidLeader }}</strong></div>
                           <div class="d-flex justify-content-between mb-2"><span class="text-muted">Starts At</span><strong>{{ optional($lot->auction_start_at)->format('d M Y h:i A') ?: 'Not scheduled' }}</strong></div>
                           <div class="d-flex justify-content-between"><span class="text-muted">Ends In</span><strong>{{ $countdown }}</strong></div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>

            <div class="col-lg-5">
               <div class="card border-0 shadow-sm p-4 mb-4">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                     <span class="text-muted small fw-bold">{{ $lotLabel }}</span>
                     <span class="badge bg-light text-dark">{{ $statusLabel }}</span>
                  </div>
                  <h4 class="fw-bold mb-1">{{ $lot->title ?: ($lot->species ?: 'Auction Lot') }}</h4>
                  <p class="text-muted small">Current pricing and admin-side lot summary.</p>

                  <div class="bg-light rounded p-3 text-center mb-4">
                     <div class="text-muted small">Current Highest Bid</div>
                     <div class="display-6 fw-bold text-dark">{{ '$' . number_format($currentBid, 2) }} <span class="fs-6">/kg</span></div>
                     <div class="text-success small fw-bold mt-1">Estimated Total: {{ '$' . number_format($totalValue, 2) }}</div>
                  </div>

                  <div class="border rounded p-3">
                     <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-shield-check text-primary me-2"></i>
                        <span class="fw-bold small">ADMIN SUMMARY</span>
                     </div>
                     <div class="d-flex justify-content-between mb-2"><span class="text-muted">Starting Price</span><strong>{{ '$' . number_format((float) ($lot->starting_price ?? 0), 2) }}/kg</strong></div>
                     <div class="d-flex justify-content-between mb-2"><span class="text-muted">Bid Count</span><strong>{{ $bidHistory->count() }}</strong></div>
                     <div class="d-flex justify-content-between mb-2"><span class="text-muted">Winner</span><strong>{{ $lot->winner?->name ?: 'Pending' }}</strong></div>
                     <div class="d-flex justify-content-between"><span class="text-muted">Submitted</span><strong>{{ optional($lot->created_at)->diffForHumans() ?: 'N/A' }}</strong></div>
                  </div>
               </div>

               <div class="card border-0 shadow-sm p-4">
                  <h6 class="fw-bold mb-3">Notes</h6>
                  <p class="text-muted mb-0">{{ $lot->notes ?: 'No notes available for this lot.' }}</p>
               </div>
            </div>
         </div>

         <div class="card border-0 shadow-sm p-4 mt-4">
            <h5 class="fw-bold mb-3">Recent Bidding History</h5>
            <div class="table-responsive">
               <table class="table table-striped align-middle mb-0">
                  <thead>
                     <tr>
                        <th>Bidder</th>
                        <th>Bid Time</th>
                        <th>Amount</th>
                        <th>Status</th>
                     </tr>
                  </thead>
                  <tbody>
                     @forelse ($bidHistory as $index => $bid)
                        <tr>
                           <td class="fw-bold">{{ $bid->buyer?->name ?: ('Buyer #' . ($bid->buyer_id ?: 'N/A')) }}</td>
                           <td>{{ optional($bid->created_at)->format('d M Y h:i A') ?: 'N/A' }}</td>
                           <td class="fw-bold">{{ '$' . number_format((float) $bid->amount, 2) }}/kg</td>
                           <td>
                              @if ($index === 0)
                                 <span class="badge bg-success">Leading</span>
                              @else
                                 <span class="text-muted">Outbid</span>
                              @endif
                           </td>
                        </tr>
                     @empty
                        <tr>
                           <td colspan="4" class="text-center text-muted py-4">No bidding history available for this lot yet.</td>
                        </tr>
                     @endforelse
                  </tbody>
               </table>
            </div>
         </div>
      @endif
   </div>
</div>

@include('bid_admin.admin.include.footer')

@include('bid_web.buyer.include.header')

@include('bid_web.buyer.include.side_menu')

<!-- Page Wrapper -->
         <div class="page-wrapper">
            <div class="content container-fluid">
  <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <h1 class="page-title text-white">Active Auction</h1>
        <p class="text-white">Auction Management &amp; Perform Optimization</p>
      </div>
      <div class="col-auto">
         <span class="btn btn-primary btn-toggle me-2 text-white"><i class="bi bi-box"></i> LIVE: {{ $liveLots->count() }} LOTS</span> 
       
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
          

    <div class="row">
        <div class="col-lg-12">
            <div class="section-glass mb-3">


<div class="table-responsive">
        <table class="submitted-table">
            
                <thead>
                    <tr>
                        <th>Lot ID</th>
                        <th>Media</th>
                        <th>Species / Grade</th>
                        <th>Available Volume</th>
                        <th>Current Price</th>
                        <th>Time Remaining</th>
                        <th class="text-end">Command</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($liveLots as $lot)
                        @php
                            $highest = $highestBids[$lot->id] ?? null;
                            $currentPrice = $highest ?? $lot->starting_price;
                            $minBid = max((float) $lot->starting_price, (float) ($highest ?? 0) + 0.01);
                            $requiredWalletBalance = $minBid * max(1, (float) ($lot->quantity ?? 1));
                            $timeLeft = $lot->auction_end_at ? $lot->auction_end_at->diffForHumans() : 'Live';
                        @endphp
                        <tr>
                            <td class="lot-id">#{{ $lot->id }}</td>
                            <td><img src="{{ $lot->image_url }}" class="prod-img"></td>
                            <td><span class="species">{{ $lot->species }}</span><span class="grade">{{ $lot->title }}</span></td>
                            <td><span class="fw-bold">{{ number_format((float) $lot->quantity, 0) }}</span> <small class="opacity-50">KG</small></td>
                            <td><span class="price">${{ number_format((float) $currentPrice, 2) }}<small>/kg</small></span></td>
                            <td><span class="timer">{{ $timeLeft }}</span></td>
                            <td class="text-end">
                                <button
                                    class="btn-live me-2 bid-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#bidModal"
                                    data-lot-id="{{ $lot->id }}"
                                    data-lot-title="{{ $lot->title }}"
                                    data-min-bid="{{ number_format((float) $minBid, 2, '.', '') }}"
                                    data-current-bid="{{ number_format((float) $currentPrice, 2, '.', '') }}"
                                    data-required-balance="{{ number_format((float) $requiredWalletBalance, 2, '.', '') }}"
                                >Bid</button>
                                <a href="{{ route('buyer.live-auction', ['lot' => $lot->id]) }}" class="btn-live">Join Live</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-white-50">No active auctions right now.</td>
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
      </div>

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
                    <div class="form-text">Wallet available: ${{ number_format((float) ($wallet->available_balance ?? 0), 2) }}</div>
                    <div class="form-text">Estimated wallet hold if balance is available: <span id="bidRequiredBalance">$0.00</span></div>
                    <div class="form-text text-warning">If wallet balance is low, you can still bid and pay later.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Bid</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('bid_web.buyer.include.footer')


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
            document.getElementById('bidRequiredBalance').textContent = '$' + (btn.getAttribute('data-required-balance') || '0.00');

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

@include('bid_web.buyer.include.header')

@include('bid_web.buyer.include.side_menu')

<!-- Page Wrapper -->
         <div class="page-wrapper">

            <style>
                /* 4-Column Grid */
        .auction-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }

        .upcoming-card {
            background: var(--glass);
            
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            overflow: hidden;
            transition: 0.3s ease;
        }

        .upcoming-card:hover {
            transform: translateY(-5px);
            border-color: var(--upcoming-purple);
            background: rgba(255, 255, 255, 0.08);
        }

        .card-visual {
            height: 130px;
            background-size: cover;
            background-position: center;
            position: relative;
            background-color: #16222a;
        }

        /* Static Timer Styling */
        .timer-tag {
            position: absolute; bottom: -18px; left: 50%;
            transform: translateX(-50%);
            background: #000; border: 1px solid var(--glass-border);
            border-radius: 8px; padding: 6px 15px;
            color: var(--gold); font-family: 'JetBrains Mono', monospace;
            font-size: 1rem; font-weight: 700; white-space: nowrap;
            box-shadow: 0 8px 16px rgba(0,0,0,0.4);
        }

        .card-content { padding: 30px 18px 18px 18px; }

        .label { font-size: 0.65rem; color: #fff; text-transform: uppercase; letter-spacing: 1px; display: block; }
        .species-name { font-size: 1.05rem; font-weight: 800; margin-bottom: 12px; height: 2.4em; overflow: hidden;     color: #fff; }

        .data-row {
            display: flex; justify-content: space-between;
            background: rgba(0,0,0,0.2);
            padding: 10px; border-radius: 10px; margin-bottom: 15px;
        }

        .price-text { color: var(--accent-cyan); font-weight: 700; font-family: 'JetBrains Mono', monospace; }

        /* Action Buttons */
        .btn-stack { display: grid;  gap: 8px; }
        .btn-sub {
            background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border);
            color: #fff; font-size: 0.7rem; padding: 10px 5px; border-radius: 8px;
            font-weight: 700; text-transform: uppercase; transition: 0.2s;
        }
        .btn-sub:hover { background: var(--upcoming-purple); border-color: var(--upcoming-purple); }
        .btn-cal:hover { background: var(--accent-cyan); border-color: var(--accent-cyan); color: #000; }

        @media (max-width: 1200px) { .auction-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 900px) { .auction-grid { grid-template-columns: repeat(2, 1fr); } }
            </style>
            <div class="content container-fluid">
  <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <h1 class="page-title text-white">Upcoming Auction</h1>
        <p class="text-white">Auction Management &amp; Perform Optimization</p>
      </div>
      <div class="col-auto">
         <span class="btn btn-primary btn-toggle me-2 text-white"><i class="bi bi-box"></i> LIVE: {{ $liveCount ?? 0 }} LOTS</span> 
       
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
          
    <div class="col-lg-12">
      <div class="auction-grid">
        @forelse(($upcomingLots ?? collect()) as $lot)
            @php
                $startAt = optional($lot->auction_start_at)->toIso8601String();
            @endphp
            <div class="upcoming-card">
                <div class="card-visual" style="background-image: url('{{ $lot->image_url }}')">
                    <div class="timer-tag" data-start="{{ $startAt }}">--:--:--</div>
                </div>
                <div class="card-content">
                    <span class="label">Lot #{{ str_pad((string) $lot->id, 4, '0', STR_PAD_LEFT) }}</span>
                    <div class="species-name">{{ $lot->title ?? ($lot->species ?? 'Lot') }}</div>
                    <div class="data-row">
                        <div><span class="label">Start Price</span><span class="price-text">${{ number_format((float) ($lot->starting_price ?? 0), 2) }}/kg</span></div>
                        <div class="text-end"><span class="label">Volume</span><span class="fw-bold">{{ $lot->quantity ?? 0 }} KG</span></div>
                    </div>
                    <div class="btn-stack">
                        <form method="POST" action="{{ route('buyer.upcoming-auction.remind', $lot->id) }}">
                            @csrf
                            <button type="submit" class="btn-sub w-100"><i class="bi bi-bell-fill me-1"></i> Remind</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-white-50">No upcoming auctions right now.</div>
        @endforelse
    </div>
    @if(($upcomingLots ?? null) && method_exists($upcomingLots, 'links'))
      <div class="d-flex justify-content-center mt-4">
        {{ $upcomingLots->links() }}
      </div>
    @endif
    </div>
        

        
   

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

<script>
  (function () {
    var refreshDelay = 30000;
    var hasTriggeredImmediateReload = false;

  function formatCountdown(ms) {
    if (ms <= 0) return '00:00:00';
    var totalSeconds = Math.floor(ms / 1000);
    var hours = Math.floor(totalSeconds / 3600);
    var minutes = Math.floor((totalSeconds % 3600) / 60);
    var seconds = totalSeconds % 60;
    return String(hours).padStart(2, '0') + ':' +
      String(minutes).padStart(2, '0') + ':' +
      String(seconds).padStart(2, '0');
  }

  function updateCountdowns() {
    document.querySelectorAll('.timer-tag[data-start]').forEach(function (el) {
      var start = el.getAttribute('data-start');
      if (!start) return;
      var target = new Date(start).getTime();
      var now = Date.now();
      var remaining = target - now;

      el.textContent = formatCountdown(remaining);

      if (remaining <= 0 && !hasTriggeredImmediateReload) {
        hasTriggeredImmediateReload = true;
        window.location.reload();
      }
    });
  }

  updateCountdowns();
  setInterval(updateCountdowns, 1000);

    setInterval(function () {
      if (document.hidden) {
        return;
      }

      window.location.reload();
    }, refreshDelay);
  })();
</script>

@include('bid_web.buyer.include.footer')




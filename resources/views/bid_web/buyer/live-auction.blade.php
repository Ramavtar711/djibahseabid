@include('bid_web.buyer.include.header')

@include('bid_web.buyer.include.side_menu')

@php
    $minimumBid = max((float) ($lot?->starting_price ?? 0), (float) ($currentPrice ?? 0) + 0.01);
    $requiredWalletBalance = $minimumBid * max(1, (float) ($lot?->quantity ?? 1));
    $availableWalletBalance = (float) ($wallet->available_balance ?? 0);
@endphp

<style>
    #bidModal .modal-content {
        background: #f4f7fb;
        color: #0f172a;
        border: 1px solid #d7e3f4;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.35);
    }

    #bidModal .modal-header,
    #bidModal .modal-footer {
        border-color: #d7e3f4;
        background: #f4f7fb;
    }

    #bidModal .modal-title,
    #bidModal .form-label,
    #bidModal #bidLotTitle {
        color: #0f172a;
    }

    #bidModal .text-muted,
    #bidModal .form-text {
        color: #475569 !important;
    }

    #bidModal .form-control {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #0f172a;
    }

    #bidModal .form-control:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 0.2rem rgba(56, 189, 248, 0.18);
        color: #0f172a;
    }

    #bidModal .btn-close {
        filter: none;
    }
</style>

<!-- Page Wrapper -->
         <div class="page-wrapper">
            <div class="content container-fluid">
              
          

 <!-- TOPBAR -->
        <div class="topbar">
            <h4 class="text-white">Live <strong>Action</strong></h4>
            <div class="d-flex align-items-center gap-3">
            <div class="badge-soft">
                <i class="bi bi-people"></i> {{ $activeBidders ?: 0 }} watching
            </div>
            <div class="badge-soft">
                <i class="bi bi-bell"></i> {{ $messages->count() }} alerts
            </div>
            </div>
        </div>
        <div class="row g-4">
            <!-- LEFT SIDE -->
            <div class="col-lg-8">
                <div class="hero">
                    @php
                        $videoUrl = $lot?->media_video_url;
                        $liveUrl = $lot?->media_live_source;
                        $mediaMode = $lot?->media_mode ?? 'fixed';
                        $mediaImages = is_array($lot?->media_images) ? $lot->media_images : [];
                        $mediaImage = count($mediaImages)
                            ? asset('storage/' . ltrim($mediaImages[0], '/'))
                            : ($lot?->image_url ?? 'https://via.placeholder.com/900x400?text=Lot');
                        $embedUrl = $videoUrl;
                        if ($videoUrl && str_contains($videoUrl, 'watch?v=')) {
                            $embedUrl = str_replace('watch?v=', 'embed/', $videoUrl);
                        }
                        $liveEmbed = $liveUrl;
                        if ($liveUrl && str_contains($liveUrl, 'watch?v=')) {
                            $liveEmbed = str_replace('watch?v=', 'embed/', $liveUrl);
                        }
                        if ($liveUrl && str_contains($liveUrl, 'youtu.be/')) {
                            $liveEmbed = str_replace('youtu.be/', 'www.youtube.com/embed/', $liveUrl);
                        }
                        if ($liveUrl && str_contains($liveUrl, 'youtube.com/live/')) {
                            $liveEmbed = str_replace('youtube.com/live/', 'youtube.com/embed/', $liveUrl);
                        }
                        if ($videoUrl && str_contains($videoUrl, 'youtu.be/')) {
                            $embedUrl = str_replace('youtu.be/', 'www.youtube.com/embed/', $videoUrl);
                        }
                        if ($videoUrl && str_contains($videoUrl, 'youtube.com/live/')) {
                            $embedUrl = str_replace('youtube.com/live/', 'youtube.com/embed/', $videoUrl);
                        }
                        $isLiveFile = $liveUrl && preg_match('/\\.(mp4|webm|ogg|m3u8)(\\?.*)?$/i', $liveUrl);
                        $isVideoFile = $videoUrl && preg_match('/\\.(mp4|webm|ogg|m3u8)(\\?.*)?$/i', $videoUrl);
                        $safeLiveUrl = $liveUrl && !str_starts_with($liveUrl, 'http') ? 'https://' . $liveUrl : $liveUrl;
                        $safeVideoUrl = $videoUrl && !str_starts_with($videoUrl, 'http') ? 'https://' . $videoUrl : $videoUrl;
                    @endphp

                    @if ($mediaMode === 'video' && $videoUrl)
                        @if ($isVideoFile)
                            <video class="w-100" controls autoplay muted playsinline>
                                <source src="{{ $safeVideoUrl }}" type="video/mp4">
                            </video>
                        @else
                            <div class="ratio ratio-16x9">
                                <iframe
                                    src="{{ $embedUrl }}"
                                    title="Live Video"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                ></iframe>
                            </div>
                        @endif
                    @elseif ($liveUrl)
                        @if ($isLiveFile)
                            <video class="w-100" controls autoplay muted playsinline>
                                <source src="{{ $safeLiveUrl }}" type="video/mp4">
                            </video>
                        @else
                            <div class="ratio ratio-16x9">
                                <iframe src="{{ $liveEmbed }}" title="Live Stream" allowfullscreen></iframe>
                            </div>
                        @endif
                    @else
                        <img src="{{ $mediaImage }}">
                    @endif
                    <div class="hero-overlay">
                        <div>
                            @if ($lot)
                                Lot #{{ $lot->id }} - <span class="live">LIVE</span>
                            @else
                                No active lot
                            @endif
                        </div>
                        <h2 class="mt-2 text-white">{{ $lot?->title ?? 'No active auction' }}</h2>
                        <div class="mt-2">
                            @if ($lot)
                                <span class="badge bg-success">{{ $lot->species }}</span>
                                <span class="badge bg-dark border border-light">QC Verified</span>
                            @endif
                        </div>


                        <div class="price-box">
                        <div class="price-live">
                            $<span id="currentPrice">{{ number_format((float) ($currentPrice ?? 0), 2) }}</span> <small>/kg</small>
                        </div>
                        <div class="mt-2">
                            @if ($lot)
                                $<span id="totalPrice">{{ number_format((float) $currentPrice * (float) $lot->quantity, 2) }}</span> total
                            @endif
                        </div><input class="form-range mt-3" type="range"> <small>Starting â€¢ Current â€¢ Reserve â€¢ Target</small>
                    </div>
                    </div>
                    
                </div><!-- LIVE BIDS -->
                <div class="glass mt-4">
                    <h5>LIVE BIDS</h5>
                    <div id="bidList" class="coment p-2" style="height:230px; overflow-y:auto">
                    @forelse ($bids as $bid)
                        <div class="bid-item">
                            <div class="d-flex align-items-center gap-3">
                                <img src="https://randomuser.me/api/portraits/men/11.jpg">
                                <div>
                                    <div>
                                        {{ $bid->buyer->name ?? 'Buyer' }}
                                    </div><small>{{ $bid->created_at?->diffForHumans() }}</small>
                                </div>
                            </div>
                            <div class="text-success fw-bold">
                                +${{ number_format((float) $bid->amount, 2) }}/kg
                            </div>
                        </div>
                    @empty
                        <div class="text-white-50">No bids yet.</div>
                    @endforelse
                 </div>

                    <h5 class="mt-4">LIVE CHAT</h5>
                    <div id="chatList" class="coment p-2" style="height:180px; overflow-y:auto">
                        @forelse ($messages as $message)
                            <div class="bid-item">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="https://randomuser.me/api/portraits/men/11.jpg">
                                    <div>
                                        <div>{{ $message->buyer->name ?? 'Buyer' }}</div>
                                        <small>{{ $message->created_at?->diffForHumans() }}</small>
                                    </div>
                                </div>
                                <div class="chat-text text-white-50">
                                    {{ $message->message }}
                                </div>
                            </div>
                        @empty
                            <div class="text-white-50">No chat yet.</div>
                        @endforelse
                    </div>

                    <form id="chatForm" method="POST" action="{{ route('buyer.live-auction.chat') }}" class="mt-3" autocomplete="off">
                        @csrf
                        <input type="hidden" name="lot_id" value="{{ $lot?->id }}">
                        <input id="chatInput" class="form-control border-0 text-white" name="message" placeholder="Type your comment..." required style="color:#fff;background:rgba(255,255,255,0.12);caret-color:#fff;">
                    </form>
                </div>
            </div><!-- RIGHT SIDE -->
            <div class="col-lg-4">
                
                <div class="side-panel">
            <div class="text-center mb-4">
                <span class="summary-label">Closing In</span>
                <span class="timer-big" id="countdownTimer" data-ends-at="{{ $lot?->auction_end_at?->toIso8601String() }}">â° {{ $lot?->auction_end_at ? $lot->auction_end_at->diffForHumans() : 'N/A' }}</span>
            </div>

            <h6 class="fw-bold mb-3 text-white"><i class="bi bi-shield-check me-2"></i>Quick Summary</h6>
            
            <div class="summary-item">
                <span class="summary-label">Market Avg Today:</span>
                <span class="summary-value text-warning fw-bold">
                    ${{ number_format((float) ($lot?->starting_price ?? 0), 2) }}<small>/kg</small>
                </span>
            </div>
            
            <div class="summary-item">
                <span class="summary-label">Volume Remaining:</span>
                <span class="summary-value text-warning fw-bold" id="lotQuantity">{{ $lot ? number_format((float) $lot->quantity, 0) : 0 }} KG</span>
            </div>

            <div class="summary-item">
                <span class="summary-label">Bidders Active:</span>
                <span class="summary-value text-warning fw-bold"><i class="bi bi-person-fill"></i> {{ $activeBidders ?: 0 }}</span>
            </div>

            <div class="mt-4">
                <div class="trust-badge"><i class="bi bi-lock-fill"></i> Secured Transaction</div>
                <div class="trust-badge"><i class="bi bi-patch-check-fill"></i> QC Certified</div>
                <div class="trust-badge"><i class="bi bi-snow"></i> Cold Chain Verified</div>
            </div>

            <button type="button" class="btn btn-primary w-100 mt-3 mb-2" id="quickBidButton">QUICK BID: +$0.00</button>
            

            <div class="increment-row">
                <button type="button" class="btn-inc" data-inc="0.10">+ $0.10</button>
                <button type="button" class="btn-inc" data-inc="0.25">+ $0.25</button>
                <button type="button" class="btn-inc" data-inc="0.50">+ $0.50</button>
            </div>

            <button type="button" class="btn-smart" id="smartIncrement">Smart Increment</button>
            <button class="btn btn-success w-100 mt-3" data-bs-toggle="modal" data-bs-target="#bidModal">Place Bid</button>
            <div class="small text-warning mt-3">Wallet is optional. If balance is available, the hold will be created; otherwise, payment can be made later.</div>
        </div>
            </div>
        </div>
        
   

</div>
</div>

              
              
            </div>
         </div>
         <!-- /Page Wrapper -->
      </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" data-cfasync="false"></script>
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1/dist/echo.iife.js"></script>


<script>

var bidChartEl = document.getElementById('bidChart');
if (bidChartEl) {
    new Chart(bidChartEl,{
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
}

var barChartEl = document.getElementById('barChart');
if (barChartEl) {
    new Chart(barChartEl,{
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
}

</script>

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
                    <input type="hidden" name="lot_id" id="bidLotId" value="{{ $lot?->id }}">
                    <div class="mb-2">
                        <div class="fw-semibold" id="bidLotTitle">{{ $lot?->title ?? 'Lot' }}</div>
                        <div class="text-muted small">Current: <span id="bidCurrentAmount">${{ number_format((float) ($currentPrice ?? 0), 2) }}</span></div>
                    </div>
                    <label class="form-label" for="bidAmount">Your Bid ($/kg)</label>
                    <input type="number" step="0.01" min="0.01" class="form-control" id="bidAmount" name="amount" required>
                    <div class="form-text">Minimum bid: <span id="bidMinAmount">$0.00</span></div>
                    <div class="form-text">Wallet available: ${{ number_format((float) $availableWalletBalance, 2) }}</div>
                    <div class="form-text">Estimated wallet hold if balance is available: <span id="bidRequiredBalance">${{ number_format((float) $requiredWalletBalance, 2) }}</span></div>
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
document.addEventListener('DOMContentLoaded', function () {
    var lotId = @json($lot?->id);
    var quantity = @json($lot ? (float) $lot->quantity : 0);
    var dataUrl = @json(route('buyer.live-auction.data'));
    var pusherKey = @json(config('broadcasting.connections.pusher.key'));
    var pusherHost = @json(config('broadcasting.connections.pusher.options.host'));
    var pusherPort = @json(config('broadcasting.connections.pusher.options.port'));
    var pusherScheme = @json(config('broadcasting.connections.pusher.options.scheme'));
    var currentPriceEl = document.getElementById('currentPrice');
    var totalPriceEl = document.getElementById('totalPrice');
    var bidListEl = document.getElementById('bidList');
    var chatListEl = document.getElementById('chatList');
    var timerEl = document.getElementById('countdownTimer');
    var quickBidBtn = document.getElementById('quickBidButton');
    var smartBtn = document.getElementById('smartIncrement');
    var bidAmountInput = document.getElementById('bidAmount');
    var bidMinAmountEl = document.getElementById('bidMinAmount');
    var bidRequiredBalanceEl = document.getElementById('bidRequiredBalance');
    var bidCurrentAmountEl = document.getElementById('bidCurrentAmount');
    var bidModalEl = document.getElementById('bidModal');
    var chatForm = document.getElementById('chatForm');
    var chatInput = document.getElementById('chatInput');
    var csrfToken = document.querySelector('meta[name="csrf-token"]');
    var tokenValue = csrfToken ? csrfToken.getAttribute('content') : '';
    var currentPrice = parseFloat((currentPriceEl && currentPriceEl.textContent) || '0') || 0;
    var pendingBidAmount = null;
    var availableWallet = @json((float) ($wallet->available_balance ?? 0));

    function formatMoney(value) {
        return (parseFloat(value || 0) || 0).toFixed(2);
    }

    function calcIncrement(total) {
        if (total < 100000) return 0.03;
        if (total <= 250000) return 0.05;
        return 0.08;
    }

    function calcSmartIncrement(total) {
        if (total > 250000) {
            return Math.random() * (0.10 - 0.08) + 0.08;
        }

        return calcIncrement(total);
    }

    function refreshBidUi(nextMin, replaceValue) {
        var minValue = parseFloat(nextMin || 0) || 0;
        var requiredWallet = minValue * Math.max(quantity || 1, 1);

        if (bidAmountInput) {
            bidAmountInput.min = formatMoney(minValue);

            var existingValue = parseFloat(bidAmountInput.value || '0') || 0;
            if (replaceValue || existingValue < minValue) {
                bidAmountInput.value = formatMoney(minValue);
            }
        }

        if (bidMinAmountEl) {
            bidMinAmountEl.textContent = '$' + formatMoney(minValue);
        }

        if (bidRequiredBalanceEl) {
            bidRequiredBalanceEl.textContent = '$' + formatMoney(requiredWallet);
        }

        if (bidCurrentAmountEl) {
            bidCurrentAmountEl.textContent = '$' + formatMoney(currentPrice);
        }
    }

    function updateIncrement() {
        var total = currentPrice * quantity;
        var inc = calcIncrement(total);

        if (quickBidBtn) {
            quickBidBtn.textContent = 'QUICK BID: +$' + formatMoney(inc);
        }

        return inc;
    }

    function syncCurrentPrice(price, replaceValue) {
        currentPrice = parseFloat(price || 0) || 0;

        if (currentPriceEl) {
            currentPriceEl.textContent = formatMoney(currentPrice);
        }

        if (totalPriceEl) {
            totalPriceEl.textContent = formatMoney(currentPrice * quantity);
        }

        updateIncrement();
        refreshBidUi(currentPrice + 0.01, replaceValue);
    }

    function renderBids(bids) {
        if (!bidListEl) return;

        if (!bids.length) {
            bidListEl.innerHTML = '<div class="text-white-50">No bids yet.</div>';
            return;
        }

        bidListEl.innerHTML = bids.map(function (bid) {
            return (
                '<div class="bid-item">' +
                    '<div class="d-flex align-items-center gap-3">' +
                        '<img src="https://randomuser.me/api/portraits/men/11.jpg">' +
                        '<div><div>' + bid.buyer + '</div><small>' + bid.time + '</small></div>' +
                    '</div>' +
                    '<div class="text-success fw-bold">+$' + formatMoney(bid.amount) + '/kg</div>' +
                '</div>'
            );
        }).join('');
    }

    function prependBid(bid) {
        if (!bidListEl) return;

        if (bidListEl.textContent.indexOf('No bids yet.') !== -1) {
            bidListEl.innerHTML = '';
        }

        bidListEl.insertAdjacentHTML('afterbegin', (
            '<div class="bid-item">' +
                '<div class="d-flex align-items-center gap-3">' +
                    '<img src="https://randomuser.me/api/portraits/men/11.jpg">' +
                    '<div><div>' + bid.buyer + '</div><small>just now</small></div>' +
                '</div>' +
                '<div class="text-success fw-bold">+$' + formatMoney(bid.amount) + '/kg</div>' +
            '</div>'
        ));
    }

    function renderChats(messages) {
        if (!chatListEl) return;

        if (!messages.length) {
            chatListEl.innerHTML = '<div class="text-white-50">No chat yet.</div>';
            return;
        }

        chatListEl.innerHTML = messages.map(function (msg) {
            return (
                '<div class="bid-item">' +
                    '<div class="d-flex align-items-center gap-3">' +
                        '<img src="https://randomuser.me/api/portraits/men/11.jpg">' +
                        '<div><div>' + msg.buyer + '</div><small>' + msg.time + '</small></div>' +
                    '</div>' +
                    '<div class="chat-text text-white-50">' + msg.message + '</div>' +
                '</div>'
            );
        }).join('');
    }

    function prependChat(msg) {
        if (!chatListEl) return;

        if (chatListEl.textContent.indexOf('No chat yet.') !== -1) {
            chatListEl.innerHTML = '';
        }

        chatListEl.insertAdjacentHTML('afterbegin', (
            '<div class="bid-item">' +
                '<div class="d-flex align-items-center gap-3">' +
                    '<img src="https://randomuser.me/api/portraits/men/11.jpg">' +
                    '<div><div>' + msg.buyer + '</div><small>just now</small></div>' +
                '</div>' +
                '<div class="chat-text text-white-50">' + msg.message + '</div>' +
            '</div>'
        ));
    }

    function updateTimer(endsAtIso) {
        if (!timerEl || !endsAtIso) return;

        var end = new Date(endsAtIso).getTime();
        var now = Date.now();
        var diff = Math.max(0, end - now);
        var mins = Math.floor(diff / 60000);
        var secs = Math.floor((diff % 60000) / 1000);

        timerEl.textContent = '⏰ ' + String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
    }

    function fetchLiveData() {
        if (!lotId) return;

        fetch(dataUrl + '?lot_id=' + lotId)
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (!data) return;

                syncCurrentPrice(data.current_price || 0, false);
                renderBids(data.bids || []);
                renderChats(data.messages || []);

                if (data.ends_at) {
                    updateTimer(data.ends_at);
                }
            })
            .catch(function () {});
    }

    syncCurrentPrice(currentPrice, true);
    fetchLiveData();
    setInterval(fetchLiveData, 10000);

    if (window.Echo === undefined && window.Pusher) {
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: pusherKey,
            wsHost: pusherHost || window.location.hostname,
            wsPort: pusherPort || 6001,
            wssPort: pusherPort || 6001,
            forceTLS: pusherScheme === 'https',
            disableStats: true,
            enabledTransports: ['ws', 'wss'],
        });
    }

    if (window.Echo && lotId) {
        window.Echo.channel('lot.' + lotId)
            .listen('.bid.placed', function (e) {
                syncCurrentPrice(e.amount || currentPrice, true);
                prependBid(e);
            })
            .listen('.chat.message', function (e) {
                prependChat(e);
            });
    }

    if (bidModalEl) {
        bidModalEl.addEventListener('show.bs.modal', function () {
            refreshBidUi(pendingBidAmount ?? (currentPrice + 0.01), true);
        });

        bidModalEl.addEventListener('hidden.bs.modal', function () {
            pendingBidAmount = null;
        });
    }

    if (chatForm && chatInput) {
        chatForm.addEventListener('submit', function (event) {
            event.preventDefault();

            var message = chatInput.value.trim();
            if (!message || !lotId) return;

            fetch(chatForm.getAttribute('action'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': tokenValue,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    lot_id: lotId,
                    message: message,
                }),
            })
            .then(function (res) { return res.json(); })
            .then(function () {
                chatInput.value = '';
            })
            .catch(function () {});
        });

        chatInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                chatForm.requestSubmit();
            }
        });
    }

    if (quickBidBtn) {
        quickBidBtn.addEventListener('click', function (event) {
            event.preventDefault();
            pendingBidAmount = currentPrice + updateIncrement();
            refreshBidUi(pendingBidAmount, true);

            if (bidAmountInput) {
                bidAmountInput.focus();
            }

            if (bidModalEl) {
                bootstrap.Modal.getOrCreateInstance(bidModalEl).show();
            }
        });
    }

    if (smartBtn) {
        smartBtn.addEventListener('click', function (event) {
            event.preventDefault();
            pendingBidAmount = currentPrice + calcSmartIncrement(currentPrice * quantity);
            refreshBidUi(pendingBidAmount, true);

            if (bidAmountInput) {
                bidAmountInput.focus();
            }

            if (bidModalEl) {
                bootstrap.Modal.getOrCreateInstance(bidModalEl).show();
            }
        });
    }

    document.querySelectorAll('.btn-inc[data-inc]').forEach(function (btn) {
        btn.addEventListener('click', function (event) {
            event.preventDefault();
            var inc = parseFloat(btn.getAttribute('data-inc') || '0') || 0;
            pendingBidAmount = currentPrice + inc;
            refreshBidUi(pendingBidAmount, true);

            if (bidModalEl) {
                bootstrap.Modal.getOrCreateInstance(bidModalEl).show();
            }
        });
    });
});
</script>

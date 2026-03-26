@include('bid_web.seller.include.header')

@include('bid_web.seller.include.side_menu')

<style>
    .auction-pagination .pagination {
        margin-bottom: 0;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .auction-pagination .page-item .page-link {
        min-width: 42px;
        height: 42px;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.14);
        background: rgba(255, 255, 255, 0.06);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        box-shadow: none;
    }

    .auction-pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #3d7eff, #28c1ff);
        border-color: transparent;
        color: #fff;
    }

    .auction-pagination .page-item.disabled .page-link {
        background: rgba(255, 255, 255, 0.03);
        border-color: rgba(255, 255, 255, 0.08);
        color: rgba(255, 255, 255, 0.45);
    }

    .auction-pagination .page-item:not(.active):not(.disabled) .page-link:hover {
        background: rgba(61, 126, 255, 0.18);
        border-color: rgba(61, 126, 255, 0.35);
        color: #fff;
    }

</style>

<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="page-title text-white">Active Bid</h1>
                    <p class="text-white">Auction Management &amp; Perform Optimization</p>
                </div>
                <div class="col-auto">
                    <div class="d-flex gap-2 flex-wrap">
                        <a class="glass-pill {{ $activeFilter === 'all' ? 'active' : '' }}" href="{{ route('seller.active-auction', ['filter' => 'all']) }}">All Species</a>
                        <a class="glass-pill {{ $activeFilter === 'fresh' ? 'active' : '' }}" href="{{ route('seller.active-auction', ['filter' => 'fresh']) }}">Fresh</a>
                        <a class="glass-pill {{ $activeFilter === 'priority' ? 'active' : '' }}" href="{{ route('seller.active-auction', ['filter' => 'priority']) }}">High Priority</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 activebidpage">
            @forelse ($lots as $lot)
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="glass {{ $lot->priority_class === 'text-danger' ? 'border-danger' : ($lot->priority_class === 'text-warning' ? 'border-warning' : '') }}">
                        <div class="card-header-info">
                            <span>#LOT-{{ str_pad((string) $lot->id, 3, '0', STR_PAD_LEFT) }}</span>
                            <span class="{{ $lot->priority_class }} fw-bold">{{ $lot->remaining_label }}</span>
                        </div>
                        <div class="img-frame">
                            <img src="{{ $lot->image_url }}" alt="{{ $lot->title ?? $lot->species }}">
                            <div class="bid-count">{{ $lot->bids_count }} Bids</div>
                        </div>
                        <div class="card-details">
                            <div class="d-flex justify-content-between mb-2 gap-2">
                                <h6 class="fw-bold m-0 text-white">{{ $lot->title ?: ($lot->species ?: 'Lot') }}</h6>
                                <span class="qty">{{ number_format((float) $lot->quantity, 0) }}kg</span>
                            </div>
                            <div class="price-strip">
                                <small>CURRENT BID</small>
                                <div class="val">${{ number_format((float) $lot->current_price, 2) }}/kg</div>
                            </div>
                            <div class="mt-3">
                                <a class="btn-bid w-100" href="{{ route('seller.live-view', ['lot' => $lot->id]) }}">
                                    <i class="bi bi-eye"></i> Live View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="glass p-4 text-center text-white-50">
                        No active auctions found for this filter.
                    </div>
                </div>
            @endforelse
        </div>

        @if ($lots->hasPages())
            <div class="d-flex justify-content-center mt-4">
                <div class="glass p-3 auction-pagination">
                    {{ $lots->onEachSide(1)->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    setInterval(function () {
        if (!document.hidden) {
            window.location.reload();
        }
    }, 30000);
</script>

@include('bid_web.seller.include.footer')

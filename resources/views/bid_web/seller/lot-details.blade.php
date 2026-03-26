@include('bid_web.seller.include.header')

@include('bid_web.seller.include.side_menu')

@php
    $statusKey = strtolower(trim($lot->status ?? 'draft'));
    $badgeClass = match ($statusKey) {
        'pending qc', 'pending payment' => 'bg-warning text-dark',
        'approved', 'completed', 'sold' => 'bg-success',
        'scheduled', 'scheduled auction' => 'bg-primary',
        'active auction', 'active', 'processing', 'shipped' => 'bg-info text-dark',
        default => 'bg-secondary',
    };
    $auctionDate = $lot->auction_start_at?->format('d M Y') ?? 'Not scheduled';
    $auctionTime = $lot->auction_start_at?->format('h:i A') ?? 'Not scheduled';
    $timeRemaining = $lot->auction_end_at?->isFuture() ? $lot->auction_end_at->diffForHumans() : 'Auction closed';
@endphp

<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="page-title text-white">Lot details</h1>
                    <p class="text-white">Seller Auction Management &amp; Perform Optimization</p>
                </div>
                <div class="col-auto">
                    <a class="btn btn-primary" href="{{ route('seller.lot-list') }}"><i class="bi bi-eye"></i> View all</a>
                </div>
            </div>
        </div>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row g-4 detailspage">
            <div class="col-lg-4">
                <div class="glass p-3">
                    <img src="{{ $lot->image_url }}" style="height:275px; width:100%; object-fit:cover;" class="lot-img" alt="{{ $lot->title }}">
                    <div class="mt-3 d-flex justify-content-between align-items-center gap-3">
                        <div>
                            <h5>{{ $lot->title ?: ('Lot #' . $lot->id) }}</h5>
                            <span class="badge {{ $badgeClass }}">{{ ucwords($lot->status ?? 'Draft') }}</span>
                        </div>
                        <div class="icon-box bg-blue">
                            <i class="bi bi-box-seam"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="glass p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-purple me-3">
                            <i class="bi bi-info-circle"></i>
                        </div>
                        <h5 class="mb-0">Lot Information</h5>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="info"><div class="label">Lot ID</div><div>#LOT{{ $lot->id }}</div></div>
                            <div class="info"><div class="label">Species</div><div>{{ $lot->species ?: 'N/A' }}</div></div>
                            <div class="info"><div class="label">Quantity</div><div>{{ number_format((float) $lot->quantity, 2) }} kg</div></div>
                            <div class="info"><div class="label">Starting Price</div><div>${{ number_format((float) $lot->starting_price, 2) }} / kg</div></div>
                        </div>
                        <div class="col-lg-6">
                            <div class="info"><div class="label">Harvest Date</div><div>{{ $lot->harvest_date?->format('d F Y') ?? 'N/A' }}</div></div>
                            <div class="info"><div class="label">Storage Temperature</div><div>{{ $lot->storage_temperature ?: 'N/A' }}</div></div>
                            <div class="info"><div class="label">Created Date</div><div>{{ $lot->created_at?->format('d F Y') ?? 'N/A' }}</div></div>
                            <div class="info"><div class="label">Seller</div><div>{{ $lot->seller?->name ?: 'N/A' }}</div></div>
                        </div>
                    </div>
                    @if ($lot->notes)
                        <div class="info mt-3">
                            <div class="label">Notes</div>
                            <div>{{ $lot->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-8">
                <div class="glass p-4 mb-3">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-orange me-3">
                            <i class="bi bi-hammer"></i>
                        </div>
                        <h5 class="mb-0">Auction Details</h5>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3"><div class="label">Auction Date</div><div>{{ $auctionDate }}</div></div>
                        <div class="col-6 mb-3"><div class="label">Auction Time</div><div>{{ $auctionTime }}</div></div>
                        <div class="col-6 mb-3"><div class="label">Current Price</div><div>${{ number_format((float) $highestBidAmount, 2) }} / kg</div></div>
                        <div class="col-6 mb-3"><div class="label">Highest Bidder</div><div>{{ $highestBidder }}</div></div>
                        <div class="col-6 mb-3"><div class="label">Total Bids</div><div>{{ $totalBids }}</div></div>
                        <div class="col-6 mb-3"><div class="label">Time Remaining</div><div class="text-warning">{{ $timeRemaining }}</div></div>
                    </div>
                </div>

                <div class="glass p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-green me-3">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h5 class="mb-0">Quality Control</h5>
                    </div>
                    <div class="info">
                        <div class="label">QC Status</div>
                        <span class="badge {{ $lot->qc_decision === 'approved' ? 'bg-success' : ($lot->qc_decision === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                            {{ $lot->qc_decision ? ucwords($lot->qc_decision) : 'Pending Review' }}
                        </span>
                    </div>
                    <div class="info"><div class="label">Verified Boxes</div><div>{{ $lot->qc_verified_boxes ?: 'N/A' }}</div></div>
                    <div class="info"><div class="label">Actual Weight</div><div>{{ $lot->qc_actual_weight ?: 'N/A' }}</div></div>
                    <div class="info"><div class="label">QC Temperature</div><div>{{ $lot->qc_temperature ?: 'N/A' }}</div></div>
                    <div class="info"><div class="label">QC Comment</div><div>{{ $lot->qc_notes ?: 'No QC notes yet.' }}</div></div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="glass p-4 mb-3">
                    <h5 class="mb-3">Documents</h5>
                    @forelse ($lot->document_list as $document)
                        <div class="d-flex justify-content-between align-items-center mb-2 gap-3">
                            <div>
                                <div>{{ $document['type'] }}</div>
                            </div>
                            @if ($document['url'])
                                <a class="btn btn-sm btn-primary" href="{{ $document['url'] }}" target="_blank">View</a>
                            @else
                                <button class="btn btn-sm btn-secondary" disabled>Missing</button>
                            @endif
                        </div>
                    @empty
                        <div class="text-white-50">No documents uploaded.</div>
                    @endforelse
                </div>

                <div class="glass p-4">
                    <h5 class="mb-3">Price Summary</h5>
                    <div class="d-flex justify-content-between mb-2"><span>Winning Price / kg</span> <b>${{ number_format((float) $priceSummary['winning_price'], 2) }}</b></div>
                    <div class="d-flex justify-content-between mb-2"><span>Quantity</span> <b>{{ number_format((float) $priceSummary['quantity'], 2) }} kg</b></div>
                    <div class="d-flex justify-content-between mb-2"><span>Gross Total</span> <b>${{ number_format((float) $priceSummary['gross_total'], 2) }}</b></div>
                    <div class="d-flex justify-content-between mb-2"><span>Commission</span> <b>${{ number_format((float) $priceSummary['commission'], 2) }}</b></div>
                    <hr>
                    <div class="d-flex justify-content-between"><span>Net Revenue</span> <b class="text-success">${{ number_format((float) $priceSummary['net_revenue'], 2) }}</b></div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('bid_web.seller.include.footer')

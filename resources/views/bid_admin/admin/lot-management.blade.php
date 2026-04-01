@include('bid_admin.admin.include.header')
@include('bid_admin.admin.include.side_menu')

<div class="page-wrapper admin-lot-management-page">
    <div class="content container-fluid">
        <style>
            .admin-lot-management-page .content.container-fluid { padding-top: 10px !important; }
            .management-shell {
                background: linear-gradient(180deg, rgba(5, 145, 215, 0.12), rgba(23, 120, 191, 0.08));
                border-radius: 24px;
                padding: 18px;
            }
            .status-strip {
                background: linear-gradient(135deg, rgba(13, 95, 149, 0.72), rgba(24, 126, 188, 0.58));
                border: 1px solid rgba(255,255,255,.12);
                border-radius: 18px;
                padding: 14px 22px;
                box-shadow: 0 18px 40px rgba(8, 57, 94, 0.16);
                backdrop-filter: blur(8px);
            }
            .status-pill, .buyers-online-pill {
                color: #f8fbff;
                font-size: 15px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                white-space: nowrap;
            }
            .status-pill strong, .buyers-online-pill strong { color: #fff; font-weight: 800; }
            .buyers-online-pill { font-size: 17px; font-weight: 700; }
            .status-dot {
                width: 12px;
                height: 12px;
                border-radius: 999px;
                display: inline-block;
                margin-right: 8px;
                box-shadow: 0 0 0 3px rgba(255,255,255,.08);
            }
            .page-head-card, .lot-filter-card, .table-glass {
                background: rgba(248, 251, 255, 0.90);
                border-radius: 22px;
                box-shadow: 0 16px 35px rgba(15,23,42,.08);
                border: 1px solid rgba(255,255,255,.45);
                backdrop-filter: blur(10px);
            }
            .page-head-card, .lot-filter-card {
                padding: 18px;
                margin-bottom: 18px;
            }
            .table-glass {
                padding: 16px;
            }
            .lotfish {
                width: 54px;
                height: 54px;
                object-fit: cover;
                border-radius: 12px;
                border: 1px solid rgba(15,23,42,.08);
            }
            .action-buttons {
                display: flex;
                gap: 8px;
                align-items: center;
            }
            .status-badge {
                display: inline-flex;
                align-items: center;
                border-radius: 999px;
                padding: 6px 12px;
                font-size: 12px;
                font-weight: 700;
                color: #fff;
            }
            .status-active { background: #16a34a; }
            .status-ended, .status-sold, .status-unsold { background: #6b7280; }
            .status-draft, .status-needs-modification { background: #f59e0b; color: #111827; }
            .status-pending-qc, .status-pending-payment { background: #eab308; color: #111827; }
            .status-approved, .status-scheduled-auction, .status-scheduled { background: #3b82f6; }
            .status-rejected { background: #ef4444; }
            .empty-state {
                text-align: center;
                color: #4b5563;
                padding: 40px 20px;
            }
            .filter-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-bottom: 14px;
            }
            .filter-chip {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 7px 12px;
                border-radius: 999px;
                background: rgba(59, 130, 246, 0.10);
                color: #1e3a8a;
                font-size: 12px;
                font-weight: 700;
            }
            .filter-help {
                color: #475569;
                font-size: 13px;
                margin-bottom: 0;
            }
        </style>

        <div class="management-shell">
            <div class="status-strip d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div class="d-flex flex-wrap align-items-center gap-4">
                    <div class="status-pill"><span class="status-dot {{ $systemStatus === 'LIVE' ? 'bg-success' : 'bg-secondary' }}"></span>SYSTEM STATUS: <strong class="ms-1">{{ $systemStatus }}</strong></div>
                    <div class="status-pill"><span class="status-dot bg-danger"></span><strong>{{ $liveAuctionsCount }}</strong><span class="ms-1">Live Auctions</span></div>
                    <div class="status-pill"><span class="status-dot bg-warning"></span><strong>{{ $upcomingAuctionsCount }}</strong><span class="ms-1">Upcoming</span></div>
                    <div class="status-pill"><span class="status-dot bg-success"></span><strong>${{ number_format($revenueToday, 2) }}</strong><span class="ms-1">Revenue Today</span></div>
                </div>
                <div class="buyers-online-pill"><strong>{{ number_format($registeredBuyersCount) }}</strong> Buyers Online</div>
            </div>

            <div class="page-head-card">
                <div class="row align-items-center">
                    <div class="col">
                        <h1 class="page-title mb-1">Lot Management</h1>
                        <p class="text-muted mb-0">Manage and analyze auction lots</p>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.create-lot') }}" class="btn btn-primary">
                            <i class="fe fe-plus me-2"></i>Create New Lot
                        </a>
                    </div>
                </div>
            </div>

            <div class="lot-filter-card">
                @php
                    $selectedSellerName = collect($sellerOptions)->firstWhere('id', (int) request('seller'))?->name;
                @endphp
                <div class="filter-meta">
                    <span class="filter-chip">Species: {{ $speciesOptions->count() }}</span>
                    <span class="filter-chip">Statuses: {{ $statusOptions->count() }}</span>
                    <span class="filter-chip">Sellers: {{ $sellerOptions->count() }}</span>
                    @if(request('species'))
                        <span class="filter-chip">Selected Species: {{ request('species') }}</span>
                    @endif
                    @if(request('status'))
                        <span class="filter-chip">Selected Status: {{ ucwords(request('status')) }}</span>
                    @endif
                    @if($selectedSellerName)
                        <span class="filter-chip">Selected Seller: {{ $selectedSellerName }}</span>
                    @endif
                    @if(request('date'))
                        <span class="filter-chip">Selected Date: {{ request('date') }}</span>
                    @endif
                </div>
                <p class="filter-help">Filters are loaded dynamically from current lots and seller records.</p>
                <form method="GET" action="{{ route('admin.lot-management') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-3">
                            <label class="form-label">Species</label>
                            <select class="form-select" name="species">
                                <option value="">All Species ({{ $speciesOptions->count() }})</option>
                                @foreach($speciesOptions as $speciesOption)
                                    <option value="{{ $speciesOption }}" {{ request('species') === $speciesOption ? 'selected' : '' }}>{{ $speciesOption }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="">All Status ({{ $statusOptions->count() }})</option>
                                @foreach($statusOptions as $statusOption)
                                    <option value="{{ $statusOption }}" {{ request('status') === $statusOption ? 'selected' : '' }}>{{ ucwords($statusOption) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <label class="form-label">Seller</label>
                            <select class="form-select" name="seller">
                                <option value="">All Sellers ({{ $sellerOptions->count() }})</option>
                                @foreach($sellerOptions as $sellerOption)
                                    <option value="{{ $sellerOption->id }}" {{ (string) request('seller') === (string) $sellerOption->id ? 'selected' : '' }}>{{ $sellerOption->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2">
                            <label class="form-label">End Date</label>
                            <input class="form-control" type="date" name="date" value="{{ request('date') }}">
                        </div>
                        <div class="col-lg-2 d-flex gap-2">
                            <button class="btn btn-primary" type="submit">Filter</button>
                            <a class="btn btn-outline-secondary" href="{{ route('admin.lot-management') }}">Reset</a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-glass">
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Lot</th>
                                <th>Seller</th>
                                <th>Starting Price</th>
                                <th>Increment</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lots as $lot)
                                @php
                                    $statusKey = str_replace(' ', '-', strtolower(trim($lot->status ?? 'draft')));
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ $lot->image_url }}" class="lotfish" alt="{{ $lot->title ?? ($lot->species ?? 'Lot image') }}">
                                            <div>
                                                <div class="fw-semibold">{{ $lot->title ?? ($lot->species ?? 'Auction Lot') }}</div>
                                                <small class="text-muted">#LOT-{{ str_pad((string) $lot->id, 4, '0', STR_PAD_LEFT) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $lot->seller?->name ?? 'Seller' }}</td>
                                    <td>${{ number_format((float) ($lot->starting_price ?? 0), 2) }}</td>
                                    <td>${{ number_format((float) ($lot->increment_amount ?? 0), 2) }}</td>
                                    <td>{{ optional($lot->auction_end_at)->format('Y-m-d H:i') ?? 'Not scheduled' }}</td>
                                    <td><span class="status-badge status-{{ $statusKey }}">{{ ucwords($lot->status ?? 'Draft') }}</span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.lot-details', ['lot' => $lot->id]) }}" class="btn btn-sm btn-primary" title="View"><i class="bi bi-eye"></i></a>
                                            <a href="{{ route('admin.edit-lot', ['lot' => $lot->id]) }}" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                            @if(($lot->bids_count ?? 0) > 0)
                                                <button class="btn btn-sm btn-secondary" type="button" title="Cannot delete because bids already exist" disabled><i class="bi bi-trash3"></i></button>
                                            @else
                                                <form method="POST" action="{{ route('admin.delete-lot', ['lot' => $lot->id]) }}" onsubmit="return confirm('Are you sure you want to delete this lot?')" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger" type="submit" title="Delete"><i class="bi bi-trash3"></i></button>
                                                </form>
                                            @endif
                                        </div>
                                        @if(($lot->bids_count ?? 0) > 0)
                                            <small class="d-block text-muted mt-1">Delete locked: {{ $lot->bids_count }} bid(s)</small>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="empty-state">No lots found for the selected filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($lots, 'links'))
                    <div class="d-flex justify-content-center mt-4">
                        {{ $lots->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@include('bid_admin.admin.include.footer')

@include('bid_admin.admin.include.header')

@include('bid_admin.admin.include.side_menu')

<div class="page-wrapper dashboard-page">
    <div class="content container-fluid">
        <div class="status-header d-flex flex-wrap justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-4">
                <div class="small"><i class="bi bi-circle-fill text-success me-1"></i> SYSTEM STATUS: <strong id="systemStatusValue">{{ $systemStatus }}</strong></div>
                <div class="small"><i class="bi bi-circle-fill text-danger me-1"></i> <strong id="liveAuctionsValue">{{ number_format($liveAuctionsCount) }}</strong> Live Auctions</div>
                <div class="small"><i class="bi bi-circle-fill text-warning me-1"></i> <strong id="upcomingAuctionsValue">{{ number_format($upcomingAuctionsCount) }}</strong> Upcoming</div>
                <div class="small"><i class="bi bi-circle-fill text-success me-1"></i> <strong id="revenueTodayValue">${{ number_format((float) $revenueToday, 2) }}</strong> Revenue Today</div>
            </div>
            <div class="fw-bold"><span id="buyersOnlineValue">{{ number_format($registeredBuyersCount) }}</span> <span class="text-muted fw-normal">Buyers Online</span></div>
        </div>

        <style>
            .dashboard-page .content.container-fluid { padding-top: 10px !important; }
            .dashboard-page { background: transparent; }
            .dashboard-page .content.container-fluid {
                background:
                    linear-gradient(135deg, rgba(5, 145, 215, 0.12), rgba(23, 120, 191, 0.08)),
                    radial-gradient(circle at top left, rgba(255,255,255,0.15), transparent 30%);
                border-radius: 24px;
                min-height: calc(100vh - 64px);
            }

            .status-header{
                background: linear-gradient(135deg, rgba(13, 95, 149, 0.72), rgba(24, 126, 188, 0.58));
                color:#fff;
                border:1px solid rgba(255,255,255,.12);
                border-radius:18px;
                padding:14px 22px;
                margin-bottom:24px;
                box-shadow: 0 18px 40px rgba(8, 57, 94, 0.16);
                backdrop-filter:blur(8px);
            }

            .status-header .small,
            .status-header .text-muted{
                color:#fff !important;
            }

            .kpi-card,
            .filter-box,
            .card-soft{
                background:rgba(236, 246, 252, 0.88);
                border:1px solid rgba(255,255,255,0.42);
                border-radius:20px;
                box-shadow:none;
                backdrop-filter:blur(3px);
            }

            .kpi-card{
                padding:16px 18px;
                display:flex;
                align-items:center;
                gap:14px;
                height:100%;
                min-height:84px;
            }

            .icon-box{
                width:56px;
                height:56px;
                border-radius:16px;
                display:flex;
                align-items:center;
                justify-content:center;
                font-size:22px;
                color:#fff;
                flex-shrink:0;
            }

            .bg1{background:#0d6efd;}
            .bg2{background:#198754;}
            .bg3{background:#ffc107;color:#000;}
            .bg4{background:#dc3545;}

            .page-title{
                font-size:24px;
                line-height:1.1;
                font-weight:700;
                color:#171a8d;
                margin-bottom:28px;
            }

            .section-title{
                font-size:17px;
                font-weight:700;
                color:#071427;
                margin-bottom:0;
            }

            .table{
                --bs-table-bg:transparent;
                margin-bottom:0;
            }

            .summary-label{
                font-size:13px;
                line-height:1.2;
                color:#0f172a;
                font-weight:500;
                margin-bottom:4px;
            }

            .summary-value{
                font-size:18px;
                line-height:1;
                font-weight:700;
                color:#000;
            }

            .filter-box{
                padding:16px;
                background:rgba(201, 231, 247, 0.9);
            }

            .filter-box .form-control,
            .filter-box .form-select{
                height:52px;
                border-radius:12px;
                border:1px solid #d6dbe5;
                background:#fff;
                color:#233044;
                font-size:14px;
                box-shadow:none;
            }

            .filter-box .btn{
                height:52px;
                border-radius:12px;
                font-weight:700;
                font-size:14px;
            }

            .filter-box .btn-primary{
                background:#1fa2ea;
                border-color:#1fa2ea;
            }

            .filter-box .btn-outline-secondary{
                background:rgba(255,255,255,0.65);
                border-color:#8f9aac;
                color:#6a7383;
            }

            .card-soft{
                overflow:hidden;
                padding:0 !important;
                background:rgba(196, 225, 242, 0.92);
            }

            .transactions-card-header{
                padding:18px 24px 16px;
                background:rgba(194, 225, 245, 0.98);
            }

            .transactions-card-body{
                padding:0 24px 24px;
            }

            .table thead th{
                background:linear-gradient(180deg, rgba(244,247,252,0.98) 0%, rgba(224,230,239,0.96) 100%);
                color:#0f2642;
                font-size:11px;
                font-weight:700;
                text-transform:uppercase;
                letter-spacing:0.08em;
                padding:18px 16px;
                border-bottom:0;
                white-space:nowrap;
            }

            .table tbody td{
                background:rgba(234, 242, 231, 0.96);
                color:#12263f;
                font-size:14px;
                padding:22px 16px;
                border-top:1px solid rgba(213, 225, 219, 0.9);
                vertical-align:middle;
                white-space:nowrap;
            }

            .table tbody td:nth-child(5){
                white-space:normal;
                min-width:150px;
            }

            .table tbody tr:first-child td{
                border-top:0;
            }

            .status-pill{
                display:inline-flex;
                align-items:center;
                justify-content:center;
                min-width:76px;
                padding:6px 14px;
                border-radius:999px;
                font-size:13px;
                font-weight:700;
            }

            .status-pill.bg-warning{
                background:#ffc21a !important;
                color:#1b1b1b !important;
            }

            .status-pill.bg-success{
                background:#1f9d57 !important;
                color:#fff !important;
            }

            .status-pill.bg-info{
                background:#31a8f0 !important;
                color:#fff !important;
            }

            .status-pill.bg-danger{
                background:#ef476f !important;
                color:#fff !important;
            }

            .table-responsive{
                border-radius:16px;
                overflow:auto;
            }

            .pagination-wrap .btn{
                min-width:42px;
            }

            .pagination-wrap{
                padding:16px 24px 22px;
            }

            @media (max-width: 991.98px){
                .page-title{
                    font-size:22px;
                }

                .transactions-card-body{
                    padding:0 14px 16px;
                }

                .transactions-card-header,
                .pagination-wrap{
                    padding-left:14px;
                    padding-right:14px;
                }
            }
        </style>

        <h4 class="page-title">Bank Transfer</h4>

        <div class="row g-3 mb-4" id="bankTransferSummaryGrid">
            <div class="col-md-3">
                <div class="kpi-card">
                    <div class="icon-box bg1"><i class="bi bi-bank"></i></div>
                    <div>
                        <div class="summary-label">Total Transfers</div>
                        <div class="summary-value">{{ number_format($bankTransferSummary['total']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card">
                    <div class="icon-box bg2"><i class="bi bi-check-circle"></i></div>
                    <div>
                        <div class="summary-label">Validated</div>
                        <div class="summary-value">{{ number_format($bankTransferSummary['validated']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card">
                    <div class="icon-box bg3"><i class="bi bi-clock-history"></i></div>
                    <div>
                        <div class="summary-label">Pending</div>
                        <div class="summary-value">{{ number_format($bankTransferSummary['pending']) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="kpi-card">
                    <div class="icon-box bg4"><i class="bi bi-x-circle"></i></div>
                    <div>
                        <div class="summary-label">Rejected</div>
                        <div class="summary-value">{{ number_format($bankTransferSummary['rejected']) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <form id="bankTransferFilterForm" class="filter-box p-3 mb-4" method="get" action="{{ route('admin.bank-transfer') }}">
            <div class="row g-3">
                <input type="hidden" name="page" value="{{ $bankTransferPagination['current_page'] }}">
                <div class="col-md-3">
                    <input class="form-control" name="search" value="{{ $bankTransferFilters['search'] }}" placeholder="Search Buyer / Auction" type="text">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="status">
                        <option value="">Status</option>
                        <option value="pending" @selected($bankTransferFilters['status'] === 'pending')>Pending</option>
                        <option value="under_review" @selected($bankTransferFilters['status'] === 'under_review')>Under Review</option>
                        <option value="approved" @selected($bankTransferFilters['status'] === 'approved')>Approved</option>
                        <option value="rejected" @selected($bankTransferFilters['status'] === 'rejected')>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="country">
                        <option value="">Country</option>
                        @foreach ($bankTransferCountryOptions as $option)
                            <option value="{{ $option['value'] }}" @selected($bankTransferFilters['country'] === $option['value'])>{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input class="form-control" name="date" value="{{ $bankTransferFilters['date'] }}" type="date">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" type="submit"><i class="bi bi-funnel me-2"></i>Apply</button>
                </div>
                <div class="col-md-2">
                    <a class="btn btn-outline-secondary w-100" href="{{ route('admin.bank-transfer') }}">Reset</a>
                </div>
            </div>
        </form>

        <div class="card card-soft">
            <div class="transactions-card-header d-flex justify-content-between align-items-center">
                <h6 class="section-title">All Transactions</h6>
                <div class="small text-muted">Auto-refresh every 10 seconds</div>
            </div>
            <div class="transactions-card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Buyer</th>
                            <th>Company</th>
                            <th>Country</th>
                            <th>Auction ID</th>
                            <th>Lot Description</th>
                            <th>Amount</th>
                            <th>Auction Close</th>
                            <th>Payment Deadline</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="bankTransferTableBody">
                        @forelse ($bankTransferRows as $row)
                            <tr>
                                <td>{{ $row['buyer_name'] }}</td>
                                <td>{{ $row['company_name'] }}</td>
                                <td>{{ $row['country'] }}</td>
                                <td>{{ $row['auction_code'] }}</td>
                                <td>{{ $row['lot_description'] }}</td>
                                <td>${{ number_format((float) $row['amount'], 2) }}</td>
                                <td>{{ $row['auction_close_label'] }}</td>
                                <td>{{ $row['payment_deadline_label'] }}</td>
                                <td><span class="badge status-pill {{ $row['status_badge_class'] }}">{{ $row['status_label'] }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">No bank transfer records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            </div>
            <div class="pagination-wrap d-flex flex-wrap justify-content-between align-items-center gap-3 mt-3">
                <div class="small text-muted" id="bankTransferPaginationInfo">
                    @if (($bankTransferPagination['total'] ?? 0) > 0)
                        Showing {{ $bankTransferPagination['from'] }} to {{ $bankTransferPagination['to'] }} of {{ $bankTransferPagination['total'] }} results
                    @else
                        No results found
                    @endif
                </div>
                <div class="d-flex align-items-center gap-2" id="bankTransferPaginationControls">
                    @php
                        $currentPage = $bankTransferPagination['current_page'];
                        $lastPage = $bankTransferPagination['last_page'];
                        $pagesToShow = collect([1, $currentPage - 1, $currentPage, $currentPage + 1, $lastPage])
                            ->filter(fn ($page) => $page >= 1 && $page <= $lastPage)
                            ->unique()
                            ->sort()
                            ->values();
                        $previousRenderedPage = null;
                    @endphp
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-page="{{ max(1, $currentPage - 1) }}" @disabled($currentPage <= 1)>Prev</button>
                    @foreach ($pagesToShow as $page)
                        @if ($previousRenderedPage !== null && $page - $previousRenderedPage > 1)
                            <span class="px-1 text-muted">...</span>
                        @endif
                        <button class="btn btn-sm {{ $page === $currentPage ? 'btn-primary' : 'btn-outline-secondary' }}" type="button" data-page="{{ $page }}">{{ $page }}</button>
                        @php $previousRenderedPage = $page; @endphp
                    @endforeach
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-page="{{ min($lastPage, $currentPage + 1) }}" @disabled($currentPage >= $lastPage)>Next</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const bankTransferDataUrl = @json(route('admin.bank-transfer.data'));

    function formatMoney(value) {
        return '$' + Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function formatNumber(value) {
        return Number(value || 0).toLocaleString();
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function renderBankTransferSummary(summary) {
        const grid = document.getElementById('bankTransferSummaryGrid');
        if (!grid) return;

        grid.innerHTML = `
            <div class="col-md-3"><div class="kpi-card"><div class="icon-box bg1"><i class="bi bi-bank"></i></div><div><div class="summary-label">Total Transfers</div><div class="summary-value">${escapeHtml(formatNumber(summary.total || 0))}</div></div></div></div>
            <div class="col-md-3"><div class="kpi-card"><div class="icon-box bg2"><i class="bi bi-check-circle"></i></div><div><div class="summary-label">Validated</div><div class="summary-value">${escapeHtml(formatNumber(summary.validated || 0))}</div></div></div></div>
            <div class="col-md-3"><div class="kpi-card"><div class="icon-box bg3"><i class="bi bi-clock-history"></i></div><div><div class="summary-label">Pending</div><div class="summary-value">${escapeHtml(formatNumber(summary.pending || 0))}</div></div></div></div>
            <div class="col-md-3"><div class="kpi-card"><div class="icon-box bg4"><i class="bi bi-x-circle"></i></div><div><div class="summary-label">Rejected</div><div class="summary-value">${escapeHtml(formatNumber(summary.rejected || 0))}</div></div></div></div>
        `;
    }

    function renderBankTransferRows(rows) {
        const body = document.getElementById('bankTransferTableBody');
        if (!body) return;

        if (!rows || !rows.length) {
            body.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">No bank transfer records found.</td></tr>';
            return;
        }

        body.innerHTML = rows.map((row) => `
            <tr>
                <td>${escapeHtml(row.buyer_name || '')}</td>
                <td>${escapeHtml(row.company_name || '')}</td>
                <td>${escapeHtml(row.country || '')}</td>
                <td>${escapeHtml(row.auction_code || '')}</td>
                <td>${escapeHtml(row.lot_description || '')}</td>
                <td>${escapeHtml(formatMoney(row.amount || 0))}</td>
                <td>${escapeHtml(row.auction_close_label || '')}</td>
                <td>${escapeHtml(row.payment_deadline_label || '')}</td>
                <td><span class="badge status-pill ${escapeHtml(row.status_badge_class || 'bg-secondary')}">${escapeHtml(row.status_label || '')}</span></td>
            </tr>
        `).join('');
    }

    function renderBankTransferPagination(pagination) {
        const info = document.getElementById('bankTransferPaginationInfo');
        const controls = document.getElementById('bankTransferPaginationControls');
        if (!info || !controls) return;

        if (!pagination || !pagination.total) {
            info.textContent = 'No results found';
            controls.innerHTML = '';
            return;
        }

        info.textContent = `Showing ${pagination.from} to ${pagination.to} of ${pagination.total} results`;

        const currentPage = Number(pagination.current_page || 1);
        const lastPage = Number(pagination.last_page || 1);
        const pagesToShow = Array.from(new Set([1, currentPage - 1, currentPage, currentPage + 1, lastPage]))
            .filter((page) => page >= 1 && page <= lastPage)
            .sort((a, b) => a - b);
        const pages = [];
        let previousRenderedPage = null;

        for (const page of pagesToShow) {
            if (previousRenderedPage !== null && page - previousRenderedPage > 1) {
                pages.push('<span class="px-1 text-muted">...</span>');
            }

            pages.push(`<button class="btn btn-sm ${page === currentPage ? 'btn-primary' : 'btn-outline-secondary'}" type="button" data-page="${page}">${page}</button>`);
            previousRenderedPage = page;
        }

        controls.innerHTML = `
            <button class="btn btn-sm btn-outline-secondary" type="button" data-page="${Math.max(1, currentPage - 1)}" ${currentPage <= 1 ? 'disabled' : ''}>Prev</button>
            ${pages.join('')}
            <button class="btn btn-sm btn-outline-secondary" type="button" data-page="${Math.min(lastPage, currentPage + 1)}" ${currentPage >= lastPage ? 'disabled' : ''}>Next</button>
        `;
    }

    function updateBrowserUrl() {
        if (!filterForm) return;
        const params = new URLSearchParams(new FormData(filterForm));
        const nextUrl = `${filterForm.action}${params.toString() ? `?${params.toString()}` : ''}`;
        window.history.replaceState({}, '', nextUrl);
    }

    function applyBankTransferData(data) {
        document.getElementById('systemStatusValue').textContent = data.systemStatus || 'STANDBY';
        document.getElementById('liveAuctionsValue').textContent = formatNumber(data.liveAuctionsCount || 0);
        document.getElementById('upcomingAuctionsValue').textContent = formatNumber(data.upcomingAuctionsCount || 0);
        document.getElementById('revenueTodayValue').textContent = formatMoney(data.revenueToday || 0);
        document.getElementById('buyersOnlineValue').textContent = formatNumber(data.registeredBuyersCount || 0);

        renderBankTransferSummary(data.bankTransferSummary || {});
        renderBankTransferRows(data.bankTransferRows || []);
        renderBankTransferPagination(data.bankTransferPagination || {});
    }

    const filterForm = document.getElementById('bankTransferFilterForm');
    let bankTransferRefreshInFlight = false;

    async function refreshBankTransfers() {
        if (bankTransferRefreshInFlight || !filterForm) return;
        bankTransferRefreshInFlight = true;

        try {
            const params = new URLSearchParams(new FormData(filterForm));
            const response = await fetch(`${bankTransferDataUrl}?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();
            applyBankTransferData(data);
        } catch (error) {
            console.error('Bank transfer refresh failed:', error);
        } finally {
            bankTransferRefreshInFlight = false;
        }
    }

    filterForm?.addEventListener('submit', function (event) {
        event.preventDefault();

        const pageInput = filterForm.querySelector('input[name="page"]');
        if (pageInput) {
            pageInput.value = '1';
        }

        updateBrowserUrl();
        refreshBankTransfers();
    });

    document.getElementById('bankTransferPaginationControls')?.addEventListener('click', function (event) {
        const button = event.target.closest('[data-page]');
        if (!button || button.disabled || !filterForm) return;

        const pageInput = filterForm.querySelector('input[name="page"]');
        if (!pageInput) return;

        pageInput.value = button.getAttribute('data-page') || '1';
        updateBrowserUrl();
        refreshBankTransfers();
    });

    setInterval(refreshBankTransfers, 10000);
</script>

@include('bid_admin.admin.include.footer')

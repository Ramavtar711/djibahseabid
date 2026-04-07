@include('bid_admin.admin.include.header')

@include('bid_admin.admin.include.side_menu')

<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="status-header d-flex flex-wrap justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-4">
                <div class="small"><i class="bi bi-circle-fill text-success me-1"></i> SYSTEM STATUS: <strong id="systemStatusValue">{{ $systemStatus }}</strong></div>
                <div class="small"><i class="bi bi-circle-fill text-danger me-1"></i> <strong id="liveAuctionsValue">{{ number_format($liveAuctionsCount) }}</strong> Live Auctions</div>
                <div class="small"><i class="bi bi-circle-fill text-warning me-1"></i> <strong id="upcomingAuctionsValue">{{ number_format($upcomingAuctionsCount) }}</strong> Upcoming</div>
                <div class="small"><i class="bi bi-circle-fill text-success me-1"></i> <strong id="revenueTodayValue">${{ number_format((float) $revenueToday, 2) }}</strong> Revenue Today</div>
            </div>
            <div class="fw-bold"><span id="buyersOnlineValue">{{ number_format($registeredBuyersCount) }}</span> <span class="text-muted fw-normal">Registered Buyers</span></div>
        </div>

        <style>
            .page-wrapper{
                background:
                radial-gradient(1200px 400px at 20% -10%, rgba(255,255,255,0.35), transparent 65%),
                radial-gradient(1000px 500px at 90% 0%, rgba(255,255,255,0.25), transparent 70%),
                linear-gradient(180deg, #11a8e8 0%, #0e9dde 32%, #0a91d6 100%);
                min-height:calc(100vh - 64px);
            }

            .status-header{
                background:rgba(14,64,115,0.45);
                color:#fff;
                border-radius:14px;
                padding:14px 18px;
                margin-bottom:22px;
                backdrop-filter:blur(3px);
            }

            .status-header .small,
            .status-header .text-muted{
                color:#fff !important;
            }

            .summary-card,
            .filter-box,
            .card-soft{
                background:rgba(255,255,255,0.84);
                border:1px solid rgba(255,255,255,0.45);
                border-radius:16px;
                box-shadow:0 10px 22px rgba(0,0,0,0.08);
                backdrop-filter:blur(2px);
            }

            .summary-card{
                padding:18px;
            }

            .summary-label{
                font-size:13px;
                color:#41536a;
                font-weight:700;
                margin-bottom:6px;
            }

            .summary-value{
                font-size:32px;
                line-height:1;
                font-weight:700;
                color:#071427;
            }

            .page-title{
                font-size:40px;
                line-height:1.1;
                font-weight:700;
                color:#071427;
                margin-bottom:18px;
            }

            .section-title{
                font-size:18px;
                font-weight:700;
                color:#071427;
                margin-bottom:16px;
            }

            .table{
                --bs-table-bg:transparent;
                margin-bottom:0;
            }

            .pagination-wrap .btn{
                min-width:42px;
            }
        </style>

        <h4 class="page-title">All Transactions</h4>

        <div class="row g-3 mb-4" id="transactionsSummaryGrid">
            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-label">Total Results</div>
                    <div class="summary-value">{{ number_format($transactionsSummary['total']) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-label">Successful</div>
                    <div class="summary-value">{{ number_format($transactionsSummary['successful']) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-label">Pending</div>
                    <div class="summary-value">{{ number_format($transactionsSummary['pending']) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-label">Failed</div>
                    <div class="summary-value">{{ number_format($transactionsSummary['failed']) }}</div>
                </div>
            </div>
        </div>

        <form id="transactionsFilterForm" class="filter-box p-3 mb-4" method="get" action="{{ route('admin.transactions') }}">
            <div class="row g-3">
                <input type="hidden" name="page" value="{{ $transactionsPagination['current_page'] }}">
                <div class="col-md-3">
                    <input class="form-control" name="search" value="{{ $transactionFilters['search'] }}" placeholder="Search Buyer / Auction ID" type="text">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="status">
                        <option value="">Status</option>
                        <option value="successful" @selected($transactionFilters['status'] === 'successful')>Successful</option>
                        <option value="pending" @selected($transactionFilters['status'] === 'pending')>Pending</option>
                        <option value="failed" @selected($transactionFilters['status'] === 'failed')>Failed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="payment_type">
                        <option value="">Payment Type</option>
                        @foreach ($paymentTypeOptions as $option)
                            <option value="{{ $option['value'] }}" @selected($transactionFilters['payment_type'] === $option['value'])>{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input class="form-control" name="date" value="{{ $transactionFilters['date'] }}" type="date">
                </div>
                <div class="col-md-1">
                    <select class="form-select" name="per_page">
                        @foreach ($transactionsPerPageOptions as $option)
                            <option value="{{ $option }}" @selected((int) $transactionFilters['per_page'] === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" type="submit">Apply Filter</button>
                </div>
                <div class="col-md-2">
                    <a class="btn btn-outline-secondary w-100" href="{{ route('admin.transactions') }}">Reset</a>
                </div>
            </div>
        </form>

        <div class="card card-soft p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="section-title mb-0">All Transactions</h6>
                <div class="small text-muted">Auto-refresh every 10 seconds</div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Auction ID</th>
                            <th>Buyer</th>
                            <th>Company</th>
                            <th>Amount</th>
                            <th>Payment Type</th>
                            <th>Status</th>
                            <th>Risk</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="transactionsTableBody">
                        @forelse ($transactionsRows as $row)
                            <tr>
                                <td>{{ $row['auction_code'] }}</td>
                                <td>{{ $row['buyer_name'] }}</td>
                                <td>{{ $row['company_name'] }}</td>
                                <td>${{ number_format((float) $row['amount'], 2) }}</td>
                                <td>{{ $row['payment_type'] }}</td>
                                <td><span class="badge {{ $row['status_badge_class'] }}">{{ $row['status_label'] }}</span></td>
                                <td><span class="badge {{ $row['risk_badge_class'] }}">{{ $row['risk_label'] }}</span></td>
                                <td>{{ $row['date_label'] }}</td>
                                <td>
                                    @if ($row['action_url'])
                                        <a href="{{ $row['action_url'] }}" class="btn btn-sm {{ $row['action_class'] }}">{{ $row['action_label'] }}</a>
                                    @else
                                        <span class="btn btn-sm {{ $row['action_class'] }} disabled">{{ $row['action_label'] }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">No transactions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pagination-wrap d-flex flex-wrap justify-content-between align-items-center gap-3 mt-3">
                <div class="small text-muted" id="transactionsPaginationInfo">
                    @if (($transactionsPagination['total'] ?? 0) > 0)
                        Showing {{ $transactionsPagination['from'] }} to {{ $transactionsPagination['to'] }} of {{ $transactionsPagination['total'] }} results
                    @else
                        No results found
                    @endif
                </div>
                <div class="d-flex align-items-center gap-2" id="transactionsPaginationControls">
                    @php
                        $currentPage = $transactionsPagination['current_page'];
                        $lastPage = $transactionsPagination['last_page'];
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
    const transactionsDataUrl = @json(route('admin.transactions.data'));

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

    function renderTransactionsSummary(summary) {
        const grid = document.getElementById('transactionsSummaryGrid');
        if (!grid) return;

        grid.innerHTML = `
            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-label">Total Results</div>
                    <div class="summary-value">${escapeHtml(formatNumber(summary.total || 0))}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-label">Successful</div>
                    <div class="summary-value">${escapeHtml(formatNumber(summary.successful || 0))}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-label">Pending</div>
                    <div class="summary-value">${escapeHtml(formatNumber(summary.pending || 0))}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-label">Failed</div>
                    <div class="summary-value">${escapeHtml(formatNumber(summary.failed || 0))}</div>
                </div>
            </div>
        `;
    }

    function renderTransactionsRows(rows) {
        const body = document.getElementById('transactionsTableBody');
        if (!body) return;

        if (!rows || !rows.length) {
            body.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">No transactions found.</td></tr>';
            return;
        }

        body.innerHTML = rows.map((row) => `
            <tr>
                <td>${escapeHtml(row.auction_code || '')}</td>
                <td>${escapeHtml(row.buyer_name || '')}</td>
                <td>${escapeHtml(row.company_name || '')}</td>
                <td>${escapeHtml(formatMoney(row.amount || 0))}</td>
                <td>${escapeHtml(row.payment_type || '')}</td>
                <td><span class="badge ${escapeHtml(row.status_badge_class || 'bg-secondary')}">${escapeHtml(row.status_label || '')}</span></td>
                <td><span class="badge ${escapeHtml(row.risk_badge_class || 'bg-light text-dark border')}">${escapeHtml(row.risk_label || '')}</span></td>
                <td>${escapeHtml(row.date_label || '')}</td>
                <td>${row.action_url ? `<a href="${escapeHtml(row.action_url)}" class="btn btn-sm ${escapeHtml(row.action_class || 'btn-outline-primary')}">${escapeHtml(row.action_label || 'View')}</a>` : `<span class="btn btn-sm ${escapeHtml(row.action_class || 'btn-outline-secondary')} disabled">${escapeHtml(row.action_label || 'View')}</span>`}</td>
            </tr>
        `).join('');
    }

    function renderTransactionsPagination(pagination) {
        const info = document.getElementById('transactionsPaginationInfo');
        const controls = document.getElementById('transactionsPaginationControls');
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

            pages.push(`
                <button class="btn btn-sm ${page === currentPage ? 'btn-primary' : 'btn-outline-secondary'}" type="button" data-page="${page}">${page}</button>
            `);

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

    function applyTransactionsData(data) {
        document.getElementById('systemStatusValue').textContent = data.systemStatus || 'STANDBY';
        document.getElementById('liveAuctionsValue').textContent = formatNumber(data.liveAuctionsCount || 0);
        document.getElementById('upcomingAuctionsValue').textContent = formatNumber(data.upcomingAuctionsCount || 0);
        document.getElementById('revenueTodayValue').textContent = formatMoney(data.revenueToday || 0);
        document.getElementById('buyersOnlineValue').textContent = formatNumber(data.registeredBuyersCount || 0);

        renderTransactionsSummary(data.transactionsSummary || {});
        renderTransactionsRows(data.transactionsRows || []);
        renderTransactionsPagination(data.transactionsPagination || {});
    }

    const filterForm = document.getElementById('transactionsFilterForm');
    let transactionsRefreshInFlight = false;

    async function refreshTransactions() {
        if (transactionsRefreshInFlight || !filterForm) return;
        transactionsRefreshInFlight = true;

        try {
            const params = new URLSearchParams(new FormData(filterForm));
            const response = await fetch(`${transactionsDataUrl}?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();
            applyTransactionsData(data);
        } catch (error) {
            console.error('Transactions refresh failed:', error);
        } finally {
            transactionsRefreshInFlight = false;
        }
    }

    filterForm?.addEventListener('submit', function (event) {
        event.preventDefault();

        const pageInput = filterForm.querySelector('input[name="page"]');
        if (pageInput) {
            pageInput.value = '1';
        }

        updateBrowserUrl();
        refreshTransactions();
    });

    document.getElementById('transactionsPaginationControls')?.addEventListener('click', function (event) {
        const button = event.target.closest('[data-page]');
        if (!button || button.disabled || !filterForm) return;

        const pageInput = filterForm.querySelector('input[name="page"]');
        if (!pageInput) return;

        pageInput.value = button.getAttribute('data-page') || '1';
        updateBrowserUrl();
        refreshTransactions();
    });

    setInterval(refreshTransactions, 10000);
</script>

@include('bid_admin.admin.include.footer')

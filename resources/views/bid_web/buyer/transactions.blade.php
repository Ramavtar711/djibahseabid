@include('bid_web.buyer.include.header')

@include('bid_web.buyer.include.side_menu')

<!-- Page Wrapper -->
         <div class="page-wrapper">

         	 <style>
        

       

        /* Summary Bar */
        .stat-card {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-radius: 16px; padding: 20px 20px;
            color: #fff;
           
        }
        .stat-label { font-size: 14px; text-transform: uppercase; color: #fff; letter-spacing: 1px; font-weight: bold;}
        .stat-val {  font-size: 1.4rem; font-weight: 700; }

        /* Navigation Tabs */
        .nav-pills-custom {
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-radius: 14px; padding: 6px;
            margin-bottom: 30px;
        }
        .nav-pills-custom .nav-link {
            color:#fff;
            border-radius: 10px; padding: 12px 25px;
            font-weight: 700; font-size: 0.85rem; transition: 0.3s;
        }
        .nav-pills-custom .nav-link.active {
            background: var(--accent-cyan); color: #000;
        }
        .nav-pills-custom .nav-link:hover:not(.active) {
            background: rgba(255,255,255,0.05);
        }

        /* Filter Section */
        .filter-row {
            display: grid; grid-template-columns: 2fr 1fr 1fr 60px; gap: 15px;
            margin-bottom: 20px;
        }
        .f-input {
            border: 1px solid var(--glass-border);
            border-radius: 10px;  padding: 10px 15px; font-size: 0.85rem;
        }

        /* Table Aesthetics */
        .transaction-table { width: 100%; border-collapse: separate; border-spacing: 0 10px; color: #fff;
}
        .transaction-table tr { background: rgba(255,255,255,0.02); transition: 0.2s; }
        .transaction-table td { 
            padding: 16px; vertical-align: middle; 
            border-top: 1px solid var(--glass-border); border-bottom: 1px solid var(--glass-border);
        }
        .transaction-table td:first-child { border-left: 1px solid var(--glass-border); border-radius: 12px 0 0 12px; }
        .transaction-table td:last-child { border-right: 1px solid var(--glass-border); border-radius: 0 12px 12px 0; }

        /* Card Design (for Card Tab) */
        .credit-card-ui {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border-radius: 16px; padding: 25px; width: 100%;
            position: relative; overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        }
        .card-chip { width: 45px; height: 35px; background: #f1c40f; border-radius: 6px; margin-bottom: 20px; }
        .card-num { font-size: 1.2rem; letter-spacing: 3px; color: #fff; }

        /* Status Badges */
        .status-pill {
            font-size: 0.65rem; font-weight: 800; text-transform: uppercase;
            padding: 5px 12px; border-radius: 6px; border: 1px solid transparent;
        }
        .st-done { background: rgba(0, 255, 136, 0.1); color: var(--success); border-color: var(--success); }
        .st-pending { background: rgba(255, 193, 7, 0.1); color: var(--warning); border-color: var(--warning); }
        .st-refund { background: rgba(168, 85, 247, 0.1); color: var(--purple); border-color: var(--purple); }

    </style>
            <div class="content container-fluid">
              <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <h1 class="page-title text-white">Payments</h1>
        <p class="text-white">Manage all type of payment and transactions</p>
      </div>
      <div class="col-auto">
        
       
      </div>
    </div>
  </div>
          
   
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Available Balance</div>
                <div class="stat-val text-success">${{ number_format($availableBalance ?? 0, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Blocked Balance</div>
                <div class="stat-val text-warning">${{ number_format($blockedBalance ?? 0, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Refunds Issued</div>
                <div class="stat-val text-purple" style="color:var(--purple)">${{ number_format((float) ($refundsIssued ?? 0), 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Active Methods</div>
                <div class="stat-val text-info">{{ (int) ($activeMethods ?? 0) }} Sources</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="stat-card">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <div class="stat-label">Top-up Options</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-pills nav-pills-custom" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pills-all-tab" data-bs-toggle="pill" data-bs-target="#pills-all" type="button"><i class="bi bi-grid-fill me-2"></i>All Transactions</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-card-tab" data-bs-toggle="pill" data-bs-target="#pills-card" type="button"><i class="bi bi-credit-card-fill me-2"></i>Card Payments</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-bank-tab" data-bs-toggle="pill" data-bs-target="#pills-bank" type="button"><i class="bi bi-bank2 me-2"></i>Bank Transfers</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-refund-tab" data-bs-toggle="pill" data-bs-target="#pills-refund" type="button"><i class="bi bi-arrow-counterclockwise me-2"></i>Refunds</button>
        </li>
    </ul>

    <div class="tab-content" id="pills-tabContent">
        
        <div class="tab-pane fade show active" id="pills-all" role="tabpanel">
    
    <form class="filter-row" method="GET" action="{{ route('buyer.transactions') }}">
        <input type="text" name="search" class="f-input" placeholder="Search Auction ID or Txn Hash..." value="{{ $search ?? '' }}">
        <input type="date" name="date" class="f-input" value="{{ $dateFilter ?? '' }}">
        <select name="status" class="f-input">
            <option value="all" {{ ($statusFilter ?? 'all') === 'all' ? 'selected' : '' }}>All Status</option>
            <option value="paid" {{ ($statusFilter ?? '') === 'paid' ? 'selected' : '' }}>Success</option>
            <option value="pending" {{ ($statusFilter ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="processing" {{ ($statusFilter ?? '') === 'processing' ? 'selected' : '' }}>Processing</option>
            <option value="failed" {{ ($statusFilter ?? '') === 'failed' ? 'selected' : '' }}>Failed</option>
            <option value="expired" {{ ($statusFilter ?? '') === 'expired' ? 'selected' : '' }}>Expired</option>
        </select>
        <button class="btn btn-primary rounded-3" type="submit"><i class="bi bi-search"></i></button>
    </form>

    <table class="submitted-table">
        <thead>
            <tr>
                <th class="ps-3 pb-2">Date/Time</th>
                <th class="pb-2">Auction Ref</th>
                <th class="pb-2">Payment Method</th>
                <th class="pb-2">Status</th>
                <th class="text-end pb-2">Amount</th>
                <th class="text-end pe-3 pb-2">Doc</th>
            </tr>
        </thead>
        <tbody>
                @forelse(($settlements ?? collect()) as $settlement)
                @php
                    $isPaid = $settlement->status === 'paid';
                    $isProcessing = $settlement->status === 'processing';
                    $statusClass = $isPaid ? 'st-done' : 'st-pending';
                    $statusText = $isPaid ? 'Success' : ($isProcessing ? 'Processing' : ucfirst($settlement->status ?? 'Pending'));
                    $method = $settlement->payment_provider ? ucfirst(str_replace('_', ' ', $settlement->payment_provider)) : 'Platform Settlement';
                @endphp
                <tr>
                    <td class="ps-3">
                        <div class="small">{{ optional($settlement->created_at)->format('M d, Y') }}</div>
                        <div class="opacity-50" style="font-size: 0.7rem;">{{ optional($settlement->created_at)->format('H:i A') }}</div>
                    </td>
                    <td><span class="fw-bold text-info">#LOT-{{ str_pad((string) $settlement->lot_id, 4, '0', STR_PAD_LEFT) }}</span></td>
                    <td><i class="bi bi-cash-stack me-2 opacity-50"></i>{{ $method }}</td>
                    <td><span class="status-pill {{ $statusClass }}">{{ $statusText }}</span></td>
                    <td class="text-end fw-bold text-white">-${{ number_format($settlement->amount ?? 0, 2) }}</td>
                    <td class="text-end text-white-50 pe-3">
                        <a href="{{ route('buyer.payments.invoice', ['settlement' => $settlement->id, 'download' => 1]) }}" class="text-info me-2" title="Download Invoice PDF">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </a>
                        @if($isPaid)
                            <a href="{{ route('buyer.payments.receipt', ['settlement' => $settlement->id, 'download' => 1]) }}" class="text-success" title="Download Receipt PDF">
                                <i class="bi bi-receipt"></i>
                            </a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-white-50 ps-3">No transactions yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    <h6 class="fw-bold text-white mb-3">Transaction History</h6>
    <div class="table-responsive">
        <table class="submitted-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th class="text-end">Amount</th>
                    <th>Reference</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($walletTransactions ?? collect()) as $tx)
                    <tr>
                        <td>{{ optional($tx->created_at)->format('M d, Y H:i') }}</td>
                        <td class="text-capitalize">{{ str_replace('_', ' ', $tx->type) }}</td>
                        <td>{{ ucfirst($tx->status) }}</td>
                        <td class="text-end">${{ number_format((float) ($tx->amount ?? 0), 2) }}</td>
                        <td>{{ $tx->payment_reference ?? ($tx->reference_type ? strtoupper($tx->reference_type) . '-' . $tx->reference_id : '-') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-white-50">No transactions yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

        <div class="tab-pane fade" id="pills-card" role="tabpanel">
            <div class="row">
                <div class="col-md-5">
                    <div class="credit-card-ui mb-4">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="card-chip"></div>
                            <i class="bi bi-stripe fs-2 text-white-50"></i>
                        </div>
                        <div class="card-num mb-3">4412 â€¢â€¢â€¢â€¢ â€¢â€¢â€¢â€¢ 8821</div>
                        <div class="d-flex justify-content-between small text-white">
                            <span>AHMED AL-MAJED</span>
                            <span>12/28</span>
                        </div>
                    </div>
                    <button class="btn btn-outline-info w-100 py-3 rounded-4"><i class="bi bi-plus-lg me-2"></i>Add New Card</button>
                </div>
                <div class="col-md-7 ps-md-5">
                    <h6 class="fw-bold  text-white mb-3 text-uppercase small">Card History</h6>
                    <div class="transaction-table">
                        @forelse(($cardTransactions ?? collect()) as $settlement)
                            <div class="p-3 border-bottom border-white border-opacity-10 d-flex justify-content-between">
                                <span>Stripe Payment - Lot #{{ str_pad((string) $settlement->lot_id, 4, '0', STR_PAD_LEFT) }}</span>
                                <span class="fw-bold">-${{ number_format((float) ($settlement->amount ?? 0), 2) }}</span>
                            </div>
                        @empty
                            <div class="p-3 text-white-50">No card payments yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="pills-bank" role="tabpanel">
            <div class="stat-card mb-4" style="border-style: dashed;">
                <h6 class="fw-bold text-info"><i class="bi bi-info-circle me-2"></i>Official Wire Instructions</h6>
                @forelse(($bankTransactions ?? collect()) as $settlement)
                    <div class="row mt-3 g-4">
                        <div class="col-md-4"><small class="label opacity-50 d-block">Method</small><strong>{{ ucfirst(str_replace('_', ' ', $settlement->payment_provider ?? 'bank_transfer')) }}</strong></div>
                        <div class="col-md-4"><small class="label opacity-50 d-block">Reference</small><strong>{{ $settlement->payment_reference ?? 'Pending' }}</strong></div>
                        <div class="col-md-4"><small class="label opacity-50 d-block">Amount</small><strong>${{ number_format((float) ($settlement->amount ?? 0), 2) }}</strong></div>
                    </div>
                @empty
                    <div class="row mt-3 g-4">
                        <div class="col-12 text-white-50">No bank transfer or WaafiPay records yet.</div>
                    </div>
                @endforelse
            </div>
            <div class="alert alert-info bg-dark border-info text-info small">
                <i class="bi bi-lightbulb me-2"></i> Include your Auction ID in the payment reference for instant verification.
            </div>
        </div>

        <div class="tab-pane fade" id="pills-refund" role="tabpanel">
            @if(($refundTransactions ?? collect())->isNotEmpty())
                <div class="table-responsive">
                    <table class="submitted-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th class="text-end">Amount</th>
                                <th>Reference</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($refundTransactions as $tx)
                                <tr>
                                    <td>{{ optional($tx->created_at)->format('M d, Y H:i') }}</td>
                                    <td class="text-capitalize">{{ str_replace('_', ' ', $tx->type) }}</td>
                                    <td><span class="status-pill st-refund">{{ ucfirst($tx->status ?? 'completed') }}</span></td>
                                    <td class="text-end">${{ number_format((float) ($tx->amount ?? 0), 2) }}</td>
                                    <td>{{ $tx->payment_reference ?? ($tx->reference_type ? strtoupper($tx->reference_type) . '-' . $tx->reference_id : '-') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="display-6 opacity-25 mb-3"><i class="bi bi-arrow-counterclockwise"></i></div>
                    <h5 class="fw-bold">No active refunds in progress</h5>
                    <p class="text-white-50 small">All previous refunds have been successfully settled to your original payment source.</p>
                </div>
            @endif
        </div>

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

@include('bid_web.buyer.include.footer')




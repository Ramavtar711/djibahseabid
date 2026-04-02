@include('bid_admin.admin.include.header')

@include('bid_admin.admin.include.side_menu')

<!-- Page Wrapper -->
      <div class="page-wrapper">
         <div class="content container-fluid">
            <div class="status-header d-flex flex-wrap justify-content-between align-items-center">
               <div class="d-flex align-items-center gap-4">
                  <div class="small">
                     <i class="bi bi-circle-fill {{ $systemStatus === 'LIVE' ? 'text-success' : 'text-secondary' }} me-1"></i> SYSTEM STATUS: <strong>{{ $systemStatus }}</strong>
                  </div>
                  <div class="small">
                     <i class="bi bi-circle-fill text-danger me-1"></i> <strong>{{ $liveAuctionsCount }}</strong> Live Auctions
                  </div>
                  <div class="small">
                     <i class="bi bi-circle-fill text-warning me-1"></i> <strong>{{ $upcomingAuctionsCount }}</strong> Upcoming
                  </div>
                  <div class="small">
                     <i class="bi bi-circle-fill text-success me-1"></i> <strong>${{ number_format((float) $revenueToday, 2) }}</strong> Revenue Today
                  </div>
               </div>
               <div class="fw-bold">
                  {{ $registeredBuyersCount }} <span class="text-muted fw-normal">Buyers Registered</span>
               </div>
            </div>
            

            <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <h1 class="page-title">Sellers</h1>
        <p class="text-muted">Manage and analyze Sellers</p>
      </div>
      <div class="col-auto">
         
        <a href="{{ route('admin.add-seller') }}" class="btn btn-primary">
          <i class="fe fe-plus me-2"></i>Add New
        </a>
      </div>
    </div>
  </div>

  @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if (session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
  @endif
  
    <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Total Sellers</h6>
                        <h4>{{ $sellerStats['totalSellers'] }}</h4>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Active Sellers</h6>
                        <h4>{{ $sellerStats['activeSellers'] }}</h4>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Under Review</h6>
                        <h4>{{ $sellerStats['underReviewSellers'] }}</h4>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card d-flex justify-content-between align-items-center">
                    <div>
                        <h6>Total Sales</h6>
                        <h4>${{ number_format((float) $sellerStats['totalSales'], 2) }}</h4>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
   <!-- ================= TABLE VIEW ================= -->
        <div class="table-glass" id="tableView">
             <div class="d-flex justify-content-between mb-3">
               <select class="form-select w-25" onchange="window.location=this.value">
                    <option value="{{ route('admin.sellers') }}" {{ $selectedSellerStatus === '' ? 'selected' : '' }}>
                        Filter by Status
                    </option>
                    @foreach($sellerStatusOptions as $statusOption)
                        <option value="{{ route('admin.sellers', ['status' => $statusOption]) }}" {{ $selectedSellerStatus === $statusOption ? 'selected' : '' }}>
                            {{ \Illuminate\Support\Str::title($statusOption) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="table-responsive">
                <table class="table table-striped" id="userTable">
                    <thead>
                        <tr>
                            <th>Seller</th>
                            <th>Email</th>
                            <th>Port</th>
                            <th>Auctions</th>
                            <th>Total Sales</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sellers as $seller)
                            @php
                                $statusClass = match($seller->normalized_status) {
                                    'active' => 'status-active',
                                    'under review', 'pending', 'pending review', 'review' => 'status-review',
                                    'suspended', 'inactive', 'blocked' => 'bg-danger',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img class="seller-avatar" src="{{ $seller->profile_image_url }}" alt="{{ $seller->display_name }}">
                                        <div>
                                            <strong>{{ $seller->display_name }}</strong><br>
                                            <small class="text-muted">{{ $seller->seller_code }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $seller->email ?: '-' }}</td>
                                <td>{{ $seller->landing_site_port ?: '-' }}</td>
                                <td>{{ (int) $seller->auctions_count }}</td>
                                <td class="fw-semibold text-primary">${{ number_format((float) $seller->total_sales, 2) }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.sellers.status', $seller->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                            @foreach(['pending', 'active', 'under review', 'inactive', 'suspended', 'blocked'] as $statusOption)
                                                <option value="{{ $statusOption }}" @selected($seller->normalized_status === $statusOption)>
                                                    {{ \Illuminate\Support\Str::title($statusOption) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <div class="action-buttons d-flex gap-2">
                                        <a href="{{ route('admin.seller-details', ['seller' => $seller->id]) }}" class="btn btn-sm btn-primary" title="View"><i class="bi bi-eye"></i></a>
                                        <a href="{{ route('admin.edit-seller', $seller->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                        <form method="POST" action="{{ route('admin.delete-seller', $seller->id) }}" onsubmit="return confirm('Are you sure you want to delete this seller?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" type="submit" title="Delete"><i class="bi bi-trash3"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No sellers found for the selected filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        </div><!-- ================= KANBAN VIEW ================= -->
        
   

         </div>
      </div>
   </div><!-- /Page Wrapper -->

<!-- jQuery -->
      <!-- Bootstrap Core JS -->
      <!-- Feather Icon JS -->
      <!-- Slimscroll JS -->
      <!-- Theme Settings JS -->
      <!-- Custom JS -->
      <!-- Datatable JS -->
      <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"  type="text/javascript"></script>
      <script src="https://cdn.datatables.net/2.2.1/js/dataTables.bootstrap5.js"  type="text/javascript"></script>
      <script src="https://cdn.datatables.net/buttons/3.2.0/js/dataTables.buttons.js"  type="text/javascript"></script>
      <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.dataTables.js"  type="text/javascript"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"  type="text/javascript"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"  type="text/javascript"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"  type="text/javascript"></script>
      <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.html5.min.js"  type="text/javascript"></script>
      <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.print.min.js"  type="text/javascript"></script>
      <!-- Feather Icon JS -->
      <script>
         // Initialize DataTable with enhanced features
         $(document).ready(function() {
            $('#userTable').DataTable({
               pageLength: 10,
               order: [[0, 'asc']],
               responsive: true
               
              
            });
         });
      </script>

@include('bid_admin.admin.include.footer')


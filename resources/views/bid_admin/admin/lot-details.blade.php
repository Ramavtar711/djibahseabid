@include('bid_admin.admin.include.header')

@include('bid_admin.admin.include.side_menu')

<!-- Page Wrapper -->
      <div class="page-wrapper">
         <div class="content container-fluid">
            <div class="status-header d-flex flex-wrap justify-content-between align-items-center">
               <div class="d-flex align-items-center gap-4">
                  <div class="small">
                     <i class="bi bi-circle-fill text-success me-1"></i> SYSTEM STATUS: <strong>LIVE</strong>
                  </div>
                  <div class="small">
                     <i class="bi bi-circle-fill text-danger me-1"></i> <strong>6</strong> Live Auctions
                  </div>
                  <div class="small">
                     <i class="bi bi-circle-fill text-warning me-1"></i> <strong>3</strong> Upcoming
                  </div>
                  <div class="small">
                     <i class="bi bi-circle-fill text-success me-1"></i> <strong>$12,450</strong> Revenue Today
                  </div>
               </div>
               <div class="fw-bold">
                  48 <span class="text-muted fw-normal">Buyers Online</span>
               </div>
            </div>
            

            <div class="page-header">
    <div class="row align-items-center">
      <div class="col">
        <h1 class="page-title">Details: Lot #12 Yellowfin Tuna</h1>
       
      </div>
      <div class="col-auto">
         
        <a href="{{ route('admin.lot-management') }}" class="btn btn-primary">
          <i class="fe fe-eye me-2"></i>View All
        </a>
      </div>
    </div>
  </div>
  
 
    
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm p-3 mb-4">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1615141982883-c7ad0e69fd62?auto=format&fit=crop&w=400&q=80" class="main-img mb-3" id="mainImage">
                    <span class="badge bg-danger position-absolute top-0 start-0 m-3 px-3 py-2">LIVE NOW</span>
                </div>
                
            </div>

           
        </div>

        <div class="col-lg-5">
            <div class="card p-4 shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small fw-bold">LOT #12</span>
                    <span class="text-danger fw-bold small d-flex align-items-center"><i data-lucide="clock" class="me-1" style="width:14px"></i> 22:31 REMAINING</span>
                </div>
                <h4 class="fw-bold mb-1">Yellowfin Tuna</h4>
                <p class="text-muted small">Current leading bid for 120 kg</p>

               <div class="current-bid-box mb-4 text-center mt-3 bg-light rounded-1 p-3">
                    <div class="text-muted small">Current Highest Bid</div>
                    <div class="display-6 fw-bold text-dark">$210.00 <span class="fs-6">/kg</span></div>
                    <div class="text-success small fw-bold mt-1">Total: $25,200.00</div>
                </div>

                <div class="admin-panel p-1 mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="text-danger me-2 bi bi-shield-exclamation" style="width:20px"></i>
                        <span class="fw-bold text-danger small">ADMINISTRATOR CONTROLS</span>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <button class="btn btn-white border w-100 btn-sm fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#editModal">Edit Price</button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-white border w-100 btn-sm fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#editModal">Extend Time</button>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-warning w-100 btn-sm fw-bold mt-1">Suspend Auction</button>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-danger w-100 btn-sm fw-bold mt-1">Force Close & Award</button>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-2">
                    
                    <small class="text-muted text-center mt-2">Minimum increment: <span class="fw-bold">$0.50</span></small>
                </div>
                </div>

                
            </div>
        </div>
      </div>
         <div class="row">
                <div class="col-md-6">
                    <div class="card p-3">
                        <h6 class="fw-bold mb-3 d-flex justify-content-between align-items-center">
                            Product Specs <span class="qc-verified"><i data-lucide="check-circle" class="d-inline" style="width:14px"></i> QC VERIFIED</span>
                        </h6>
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex justify-content-between mb-2"><span class="text-muted">Species:</span> <strong>Yellowfin Tuna (Thunnus albacares)</strong></li>
                            <li class="d-flex justify-content-between mb-2"><span class="text-muted">Catch Method:</span> <strong>Longline</strong></li>
                            <li class="d-flex justify-content-between mb-2"><span class="text-muted">Quality Grade:</span> <strong>Grade A+ (Sashimi)</strong></li>
                            <li class="d-flex justify-content-between"><span class="text-muted">Storage:</span> <strong>Fresh / On Ice</strong></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card p-3">
                        <h6 class="fw-bold mb-3">Origin & Seller</h6>
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex justify-content-between mb-2"><span class="text-muted">Vessel:</span> <strong>MV DeepOcean 7</strong></li>
                            <li class="d-flex justify-content-between mb-2"><span class="text-muted">Port:</span> <strong>Djibah Main Port</strong></li>
                            <li class="d-flex justify-content-between mb-2"><span class="text-muted">Seller:</span> <strong class="text-primary">Tokyo Marine Co.</strong></li>
                            <li class="d-flex justify-content-between"><span class="text-muted">Harvest Date:</span> <strong>Feb 26, 2026</strong></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm p-3">
                <h6 class="fw-bold mb-3">Recent Bidding History</h6>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Bidder ID</th>
                                <th>Bid Time</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold">Buyer #492</td>
                                <td>14:22:10</td>
                                <td class="fw-bold">$210.00/kg</td>
                                <td><span class="badge bg-success">Leading</span></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Buyer #115</td>
                                <td class="text-muted">14:21:45</td>
                                <td class="text-muted">$205.00/kg</td>
                                <td><span class="text-muted">Outbid</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
    </div>
        
   

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


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
        <h1 class="page-title">Add Buyers</h1>
        <p class="text-muted">Manage and analyze buyers</p>
      </div>
      <div class="col-auto">
        
        <a href="{{ route('admin.buyers') }}" class="btn btn-primary">
          <i class="fe fe-eye me-2"></i>View All
        </a>
      </div>
    </div>
  </div>

  <div class="glass-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold">Add New Buyer</h4><span class="badge-soft badge">Bulk Auction Buyer Registration</span>
            </div>
            <form>
                <div class="row">
                    <!-- Basic Info -->
                    <div class="col-lg-6 mb-3">
                        <label class="form-label">Buyer Company Name</label> <input class="form-control" placeholder="Enter company name" type="text">
                    </div>
                    <div class="col-lg-6 mb-3">
                        <label class="form-label">Contact Person</label> <input class="form-control" placeholder="Enter contact person name" type="text">
                    </div>
                    <div class="col-lg-6 mb-3">
                        <label class="form-label">Email Address</label> <input class="form-control" placeholder="Enter email" type="email">
                    </div>
                    <div class="col-lg-6 mb-3">
                        <label class="form-label">Mobile Number</label> <input class="form-control" placeholder="Enter phone number" type="text">
                    </div>
                    <div class="col-lg-6 mb-3">
                        <label class="form-label">Location / Port</label> <select class="form-select">
                            <option>
                                Select Port
                            </option>
                            <option>
                                Mumbai Dockyard
                            </option>
                            <option>
                                Kochi Harbor
                            </option>
                            <option>
                                Chennai Port
                            </option>
                            <option>
                                Visakhapatnam
                            </option>
                        </select>
                    </div>
                    <div class="col-lg-6 mb-3">
                        <label class="form-label">Buyer Type</label> <select class="form-select">
                            <option>
                                Select Type
                            </option>
                            <option>
                                Wholesaler
                            </option>
                            <option>
                                Exporter
                            </option>
                            <option>
                                Retail Distributor
                            </option>
                        </select>
                    </div>
                    <div class="col-lg-6 mb-3">
                        <label class="form-label">License Number</label> <input class="form-control" placeholder="Enter trade license" type="text">
                    </div>
                    <div class="col-lg-6 mb-3">
                        <label class="form-label">GST Number</label> <input class="form-control" placeholder="Enter GST number" type="text">
                    </div>
                    <div class="col-lg-6 mb-3">
                        <label class="form-label">Credit Limit (â‚¹)</label> <input class="form-control" placeholder="Enter credit limit" type="number">
                    </div>
                    <div class="col-lg-6 mb-3">
                        <label class="form-label">Preferred Payment Method</label> <select class="form-select">
                            <option>
                                Select Method
                            </option>
                            <option>
                                UPI
                            </option>
                            <option>
                                Bank Transfer
                            </option>
                            <option>
                                Wallet
                            </option>
                            <option>
                                Credit Line
                            </option>
                        </select>
                    </div>
                    <div class="col-lg-6 mb-3">
                        <label class="form-label">Risk Profile</label> <select class="form-select">
                            <option>
                                Select Risk Level
                            </option>
                            <option>
                                Low Risk
                            </option>
                            <option>
                                Medium Risk
                            </option>
                            <option>
                                High Risk
                            </option>
                        </select>
                    </div>
                    <div class="col-lg-6 mb-3">
                        <label class="form-label">Status</label> <select class="form-select">
                            <option>
                                Active
                            </option>
                            <option>
                                Under Review
                            </option>
                            <option>
                                Suspended
                            </option>
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Address</label> 
                        <textarea class="form-control" placeholder="Enter full address" rows="3"></textarea>
                    </div>
                </div>
                <hr class="my-4">
                <div class="d-flex justify-content-end gap-2">
                    <button class="btn btn-outline-secondary px-4" type="reset">Cancel</button> <button class="btn btn-primary px-4" type="submit">Save Buyer</button>
                </div>
            </form>
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


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
        <h1 class="page-title">Create Auction Lot</h1>
        <p class="text-muted">Create new auction lot for display</p>
      </div>
      <div class="col-auto">
         
        <a href="{{ route('admin.lot-management') }}" class="btn btn-primary">
          <i class="fe fe-eye me-2"></i> View All
        </a>
      </div>
    </div>
  </div>
  
  <div class="card mb-4">
   <div class="card-body">
      <form>
      <div class="row">
         <div class="col-md-6 mb-3">
            <label class="form-label">Lot Title</label> <input class="form-control" placeholder="Enter lot name" type="text">
         </div>
         <div class="col-md-6 mb-3">
            <label class="form-label">Species</label> <select class="form-select">
               <option>
                  Select Species
               </option>
               <option>
                  Tiger Shrimps
               </option>
               <option>
                  Dry Fish
               </option>
               <option>
                  Lobster
               </option>
               <option>
                  Mixed Fish
               </option>
            </select>
         </div>
         <div class="col-md-6 mb-3">
            <label class="form-label">Seller</label> <select class="form-select">
               <option>
                  Select Seller
               </option>
               <option>
                  SAGAL CLEM
               </option>
               <option>
                  Harnai Market
               </option>
               <option>
                  Ocean Traders
               </option>
            </select>
         </div>
         <div class="col-md-6 mb-3">
            <label class="form-label">Quantity (KG)</label> <input class="form-control" placeholder="Enter quantity" type="number">
         </div>
         <div class="col-md-4 mb-3">
            <label class="form-label">Starting Price ($)</label> <input class="form-control" type="number">
         </div>
         <div class="col-md-4 mb-3">
            <label class="form-label">Increment ($)</label> <input class="form-control" type="number">
         </div>
         <div class="col-md-4 mb-3">
            <label class="form-label">Reserve Price ($)</label> <input class="form-control" type="number">
         </div>
         <div class="col-md-6 mb-3">
            <label class="form-label">Start Date & Time</label> <input class="form-control" type="datetime-local">
         </div>
         <div class="col-md-6 mb-3">
            <label class="form-label">End Date & Time</label> <input class="form-control" type="datetime-local">
         </div>
         <div class="col-md-6 mb-3">
            <label class="form-label">Status</label> <select class="form-select">
               <option>
                  Draft
               </option>
               <option>
                  Active
               </option>
            </select>
         </div>
         
         <div class="col-md-6 mb-3">
            <label class="form-label">Upload Lot Image</label> <input class="form-control" id="imageUpload" type="file">
         </div>

         <div class="col-md-6 mb-3 d-flex align-items-center">
            <div class="form-check mt-4">
               <input class="form-check-input" id="qc" type="checkbox"> <label class="form-check-label" for="qc">QC Validation Required</label>
            </div>
         </div>
         
      </div>
      <hr>
      <div class="text-end">
         <button class="btn btn-secondary" type="reset">Cancel</button> <button class="btn btn-primary" type="submit">Create Lot</button>
      </div>
   </form>
   </div>
            
        </div><!-- Auction Table -->
    
  
        
   

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


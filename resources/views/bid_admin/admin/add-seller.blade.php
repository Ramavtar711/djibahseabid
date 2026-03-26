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
        <h1 class="page-title">Add Seller</h1>
        <p class="text-muted">Manage and analyze sellers </p>
      </div>
      <div class="col-auto">
         
        <a href="{{ route('admin.sellers') }}" class="btn btn-primary">
          <i class="fe fe-eye me-2"></i> View All
        </a>
      </div>
    </div>
  </div>
  
  <div class="card mb-4">
   <div class="card-body">
      <form>

<!-- 1ï¸âƒ£ BASIC ACCOUNT -->
<h5 class="section-title">Basic Account Information</h5>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label>Email</label>
        <input type="email" class="form-control">
    </div>
    <div class="col-md-4">
        <label>Password</label>
        <input type="password" class="form-control">
    </div>
    <div class="col-md-4">
        <label>Status</label>
        <select class="form-select">
            <option>Active</option>
            <option>Suspended</option>
            <option>Blocked</option>
            <option>Under Review</option>
        </select>
    </div>
</div>

<!-- 2ï¸âƒ£ COMPANY LEGAL -->
<h5 class="section-title">Company Legal Information</h5>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label>Company Name</label>
        <input type="text" class="form-control">
    </div>
    <div class="col-md-4">
        <label>Registration Number</label>
        <input type="text" class="form-control">
    </div>
    <div class="col-md-4">
        <label>Tax ID / VAT</label>
        <input type="text" class="form-control">
    </div>
    <div class="col-md-6">
        <label>Country</label>
        <input type="text" class="form-control">
    </div>
    <div class="col-md-6">
        <label>Full Business Address</label>
        <input type="text" class="form-control">
    </div>
</div>

<!-- 3ï¸âƒ£ AUTHORIZED PERSON -->
<h5 class="section-title">Authorized Representative</h5>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label>Full Name</label>
        <input type="text" class="form-control">
    </div>
    <div class="col-md-3">
        <label>Designation</label>
        <input type="text" class="form-control">
    </div>
    <div class="col-md-3">
        <label>Phone</label>
        <input type="text" class="form-control">
    </div>
    <div class="col-md-3">
        <label>WhatsApp</label>
        <input type="text" class="form-control">
    </div>
</div>

<!-- 4ï¸âƒ£ EXPORT DETAILS -->
<h5 class="section-title">Operational & Export Details</h5>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label>Primary Product Category</label>
        <input type="text" class="form-control">
    </div>
    <div class="col-md-4">
        <label>Annual Export Volume (Tons)</label>
        <input type="number" class="form-control">
    </div>
    <div class="col-md-4">
        <label>Cold Storage Capacity (Tons)</label>
        <input type="number" class="form-control">
    </div>
</div>

<!-- 5ï¸âƒ£ FINANCIAL SETTINGS -->
<h5 class="section-title">Financial & Commission Settings</h5>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label>Commission %</label>
        <input type="number" class="form-control">
    </div>
    <div class="col-md-3">
        <label>Max Auction Value ($)</label>
        <input type="number" class="form-control">
    </div>
    <div class="col-md-3">
        <label>Minimum Reserve Price</label>
        <input type="number" class="form-control">
    </div>
    <div class="col-md-3">
        <label>Payment Terms</label>
        <select class="form-select">
            <option>Advance Only</option>
            <option>Partial Allowed</option>
            <option>Escrow Required</option>
        </select>
    </div>
</div>

<!-- 6ï¸âƒ£ BANK DETAILS -->
<h5 class="section-title">Bank & Settlement Details</h5>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label>Bank Name</label>
        <input type="text" class="form-control">
    </div>
    <div class="col-md-3">
        <label>Account Holder</label>
        <input type="text" class="form-control">
    </div>
    <div class="col-md-3">
        <label>IBAN / Account No</label>
        <input type="text" class="form-control">
    </div>
    <div class="col-md-3">
        <label>SWIFT Code</label>
        <input type="text" class="form-control">
    </div>
</div>

<!-- 7ï¸âƒ£ RISK SETTINGS -->
<h5 class="section-title">Risk & Security Controls</h5>
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <label>Risk Score (0-100)</label>
        <input type="number" class="form-control">
    </div>
    <div class="col-md-3">
        <label>Allow Large Auctions?</label>
        <select class="form-select">
            <option>Yes</option>
            <option>No</option>
        </select>
    </div>
    <div class="col-md-3">
        <label>KYC Status</label>
        <select class="form-select">
            <option>Pending</option>
            <option>Verified</option>
            <option>Rejected</option>
        </select>
    </div>
    <div class="col-md-3">
        <label>Multi-Account Flag</label>
        <select class="form-select">
            <option>No</option>
            <option>Yes</option>
        </select>
    </div>
</div>

<!-- 8ï¸âƒ£ DOCUMENT UPLOAD -->
<h5 class="section-title">Compliance Documents</h5>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label>Trade License</label>
        <input type="file" class="form-control">
    </div>
    <div class="col-md-4">
        <label>Export Certificate</label>
        <input type="file" class="form-control">
    </div>
    <div class="col-md-4">
        <label>Bank Statement</label>
        <input type="file" class="form-control">
    </div>
</div>

<!-- 9ï¸âƒ£ PERFORMANCE LIMITS -->
<h5 class="section-title">Performance & Limits</h5>
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label>Max Active Lots</label>
        <input type="number" class="form-control">
    </div>
    <div class="col-md-4">
        <label>Average Rating</label>
        <input type="text" class="form-control">
    </div>
    <div class="col-md-4">
        <label>Account Creation Date</label>
        <input type="datetime-local" class="form-control">
    </div>
</div>

<!-- 10ï¸âƒ£ ADMIN NOTES -->
<h5 class="section-title">Internal Admin Notes</h5>
<textarea class="form-control mb-4" rows="4"></textarea>

<div class="text-end">
<button class="btn btn-primary btn-lg">Save Seller</button>
<button class="btn btn-outline-secondary btn-lg">Cancel</button>
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


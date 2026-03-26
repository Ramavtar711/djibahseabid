@include('bid_admin.admin.include.header')

@include('bid_admin.admin.include.side_menu')

<!-- Page Wrapper -->
      <div class="page-wrapper">
         <div class="content container-fluid">
           
         <!-- HEADER -->
<div class="premium-card mb-4 buyer-header">
    <div class="row align-items-center">
        <div class="col-md-8 d-flex gap-3 align-items-center">
            <img src="https://randomuser.me/api/portraits/men/32.jpg" class="buyer-avatar">

            <div>
                <h5 class="mb-1 text-dark">Fishries Seller</h5>
                <small class="text-dark">Seller ID: SEL0045 | Fish Dockyard</small><br>
                <span class="badge bg-success badge-sm mt-2">Verified License</span>
                <span class="badge bg-primary badge-sm mt-2">Wholesaler</span>
                <span class="badge bg-warning text-dark badge-sm mt-2">Premium Seller</span>
            </div>
        </div>

        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <h4 class="fw-bold text-primary">$ 1,85,750</h4>
            <small class="text-dark">Total Revenue Generated</small><br>
            <small class="text-muted">Commission Paid: $12,800</small>
        </div>
    </div>
</div>


<div class="row">

<!-- LEFT SIDE -->
<div class="col-lg-8">

    <!-- Financial Overview -->
    <div class="card card-soft p-4 mb-4">
        <h6 class="mb-3">Financial Overview</h6>
        <div class="row text-center">
            <div class="col-md-4">
                <small>Total Lots Listed</small>
                <h6 class="fw-bold">58 Lots</h6>
            </div>
            <div class="col-md-4">
                <small>Pending Settlement</small>
                <h6 class="text-danger">$ 18,200</h6>
            </div>
            <div class="col-md-4">
                <small>Average Lot Value</small>
                <h6>$ 3,750</h6>
            </div>
        </div>
    </div>


    <!-- Business & Activity -->
    <div class="card card-soft p-4 mb-4">
        <h6 class="mb-3">Business & Activity Insights</h6>
        <div class="row text-center">
            <div class="col-md-4">
                <div class="stat-box">
                    <h5>8</h5>
                    <small>Active Auctions</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box">
                    <h5>Top 3</h5>
                    <small>Seller Ranking</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-box">
                    <h5>94%</h5>
                    <small>On-Time Delivery</small>
                </div>
            </div>
        </div>
    </div>


    <!-- Auction History -->
    <div class="card card-soft p-4">
        <h6 class="mb-3">Recent Lots Sold</h6>

        <div class="row">

            <div class="col-md-4 mb-3">
                <img src="https://images.unsplash.com/photo-1599488615731-7e5c2823ff28?auto=format&fit=crop&w=400&h=250" class="lot-img w-100">
                <div class="mt-2">
                    <strong>Yellowfin Tuna</strong><br>
                    <small>Qty: 1200 Kg</small><br>
                    <span class="badge bg-success mt-1">Sold - $4,800</span>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <img src="https://images.unsplash.com/photo-1615141982883-c7ad0e69fd62?auto=format&fit=crop&w=400&h=250" class="lot-img w-100">
                <div class="mt-2">
                    <strong>King Salmon</strong><br>
                    <small>Qty: 850 Kg</small><br>
                    <span class="badge bg-success mt-1">Sold - $3,200</span>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <img src="https://loremflickr.com/400/300/fish,sea?lock=5" class="lot-img w-100">
                <div class="mt-2">
                    <strong>Black Tiger Prawns</strong><br>
                    <small>Qty: 600 Kg</small><br>
                    <span class="badge bg-success mt-1">Sold - $2,100</span>
                </div>
            </div>

        </div>
    </div>

</div>


<!-- RIGHT SIDE -->
<div class="col-lg-4">

    <!-- Seller Information -->
    <div class="card card-soft p-4 mb-4">
        <h6 class="mb-3">Seller Information</h6>

        <p class="mb-1"><strong>Company Type:</strong> Private Limited</p>
        <p class="mb-1"><strong>GST No:</strong> 27AABCR1234F1Z5</p>
        <p class="mb-1"><strong>License No:</strong> FSSAI-77889944</p>
        <p class="mb-1"><strong>Contact:</strong> +91 9876543210</p>
        <p class="mb-0"><strong>Email:</strong> rahulseafood@mail.com</p>
    </div>

    <!-- Recent Payouts -->
    <div class="card card-soft p-4">
        <h6 class="mb-3">Recent Payouts</h6>

        <div class="mb-3">
            <strong>$4,800</strong><br>
            <small>Bank Transfer - 15 Feb 2026</small><br>
            <span class="badge bg-success">Completed</span>
        </div>

        <div>
            <strong>$3,200</strong><br>
            <small>UPI - 12 Feb 2026</small><br>
            <span class="badge bg-warning text-dark">Processing</span>
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


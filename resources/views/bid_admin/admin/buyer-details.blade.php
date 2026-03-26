@include('bid_admin.admin.include.header')

@include('bid_admin.admin.include.side_menu')

<!-- Page Wrapper -->
      <div class="page-wrapper">
         <div class="content container-fluid">
            <div class="premium-card buyer-header mb-4">
            <div class="row align-items-center">
                <div class="col-lg-2 text-center"><img class="buyer-avatar" src="https://i.pravatar.cc/150?img=1"></div>
                <div class="col-lg-6">
                    <h4 class="fw-bold mb-1">Rahul Seafood Traders</h4>
                    <p class="text-muted mb-2">Buyer ID: BAU1024 | Mumbai Wholesale Dockyard</p><span class="badge bg-success">Verified License</span> <span class="badge bg-primary">Premium Buyer</span> <span class="badge bg-warning text-dark">Low Risk</span>
                   
                    
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                    <h3 class="text-primary fw-bold">$ 28,75,000</h3>
                    <p class="mb-1">Total Auction Purchase</p><span class="badge-soft badge">Credit Limit: $ 5,00,000</span>
                </div>
            </div>
        </div><!-- BUYER FINANCIAL SUMMARY -->
        <div class="row">
            <!-- LEFT SIDE - AUCTION SLIDER -->
            <div class="col-lg-8">
              <div class="premium-card p-3 mb-4">
            <h5 class="section-title">Financial Overview</h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="info-label">
                        Total Paid Amount
                    </div>
                    <div class="info-value text-success">
                        $ 25,55,000
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-label">
                        Pending Settlement
                    </div>
                    <div class="info-value text-danger">
                        $ 3,20,000
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-label">
                        Average Bid Value
                    </div>
                    <div class="info-value">
                        $ 3,45,000
                    </div>
                </div>
            </div>
            <div class="soft-line"></div>
            <div class="row">
                <div class="col-md-6">
                    <div class="info-label">
                        Most Purchased Fish
                    </div>
                    <div class="info-value text-primary">
                        Yellowfin Tuna (35%)
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-label">
                        Preferred Auction Time
                    </div>
                    <div class="info-value">
                        Morning Batch (6AM - 10AM)
                    </div>
                </div>
            </div>
        </div><!-- BUSINESS SUMMARY -->
        <div class="premium-card p-3">
            <h5 class="section-title">Business & Activity Insights</h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="stat-box">
                        <h6>8</h6>
                        <p class="mb-0">Active Bids This Month</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box">
                        <h6>Top 5</h6>
                        <p class="mb-0">Buyer Ranking</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box">
                        <h6>On-Time 92%</h6>
                        <p class="mb-0">Payment Reliability</p>
                    </div>
                </div>
            </div>
        </div> 
            </div><!-- RIGHT SIDE - RECENT TRANSACTIONS -->
            <div class="col-lg-4">
                <div class="premium-card p-3">
                    <h5 class="section-title">Recent Transactions</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item transaction-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>Yellowfin Tuna</strong><br>
                                    <small>UPI Payment â€¢ 15 Feb 2026</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold">
                                        $ 4,80,000
                                    </div><span class="badge bg-success">Paid</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item transaction-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>King Salmon</strong><br>
                                    <small>Bank Transfer â€¢ 12 Feb 2026</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold">
                                        $ 3,20,000
                                    </div><span class="badge bg-warning text-dark">Pending</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item transaction-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>Black Tiger Prawns</strong><br>
                                    <small>Wallet â€¢ 08 Feb 2026</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold">
                                        $ 2,10,000
                                    </div><span class="badge bg-success">Paid</span>
                                </div>
                            </div>
                        </li>
                        
                    </ul>
                    <hr>
                    <div class="text-center">
                        <a class="btn btn-primary">View All</a>
                    </div>
                </div>
            </div>
        </div>
         <div class="row">
            <!-- LEFT SIDE - AUCTION SLIDER -->
            <div class="col-lg-8">
        <div class="premium-card p-3">
                    <h5 class="section-title">Auction History</h5>
                    <div class="auction-slider">
                        <div class="auction-card">
                           <img src="assets/img/f1.jpg"/>
                            <h6>Yellowfin Tuna</h6><small>AUC101</small>
                            
                            <p>Qty: 1200 Kg</p>
                            <p>Won: $4,80,000</p><span class="badge bg-success">Won</span>
                        </div>
                        <div class="auction-card">
                           <img src="assets/img/f2.jpg"/>
                            <h6>King Salmon</h6><small>AUC102</small>
                            
                            <p>Qty: 850 Kg</p>
                            <p>Bid: $3,20,000</p><span class="badge bg-danger">Lost</span>
                        </div>
                        <div class="auction-card">
                           <img src="assets/img/f3.jpg"/>
                            <h6>Black Tiger Prawns</h6><small>AUC103</small>
                            
                            <p>Qty: 600 Kg</p>
                            <p>Won: $2,10,000</p><span class="badge bg-success">Won</span>
                        </div>
                        <div class="auction-card">
                           <img src="assets/img/f4.jpg"/>
                            <h6>Blue Crab</h6><small>AUC104</small>
                            
                            <p>Qty: 400 Kg</p>
                            <p>Won: $1,40,000</p><span class="badge bg-success">Won</span>
                        </div>
                        <div class="auction-card">
                           <img src="assets/img/f5.jpg"/>
                            <h6>Pomfret</h6><small>AUC105</small>
                           
                            <p>Qty: 700 Kg</p>
                            <p>Won: $2,60,000</p><span class="badge bg-success">Won</span>
                        </div>
                        <div class="auction-card">
                           <img src="assets/img/f1.jpg"/>
                            <h6>Hilsa</h6><small>AUC106</small>
                           
                            <p>Qty: 500 Kg</p>
                            <p>Bid: $1,90,000</p><span class="badge bg-danger">Lost</span>
                        </div>
                    </div>
                </div>
        </div>

            <div class="col-lg-4">
               <!-- PERFORMANCE STATS -->
        
          <div class="premium-card p-3">
             <h5 class="section-title">Auction Stats</h5>
            <div class="row g-3">
            <div class="col-6">
                <div class="stat-box">
                    <h5>42</h5>
                    <small class="mb-0 small">Auctions Participated</small>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-box">
                    <h5>27</h5>
                    <small class="mb-0 small">Auctions Won</small>
                </div>
            </div>
           <div class="col-6">
                <div class="stat-box">
                    <h5>64%</h5>
                    <small class="mb-0 small">Win Rate</small>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-box">
                    <h5>18,500 Kg</h5>
                    <small class="mb-0 small">Total Fish Purchased</small>
                </div>
            </div>
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


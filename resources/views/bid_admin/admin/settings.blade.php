@include('bid_admin.admin.include.header')

@include('bid_admin.admin.include.side_menu')

<!-- Page Wrapper -->
         <div class="page-wrapper">
            <div class="content container-fluid">
              
              <div class="status-header d-flex flex-wrap justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-4">
            <div class="small"><i class="bi bi-circle-fill text-success me-1"></i> SYSTEM STATUS: <strong>LIVE</strong></div>
            <div class="small"><i class="bi bi-circle-fill text-danger me-1"></i> <strong>6</strong> Live Auctions</div>
            <div class="small"><i class="bi bi-circle-fill text-warning me-1"></i> <strong>3</strong> Upcoming</div>
            <div class="small"><i class="bi bi-circle-fill text-success me-1"></i> <strong>$12,450</strong> Revenue Today</div>
        </div>
        <div class="fw-bold">48 <span class="text-muted fw-normal">Buyers Online</span></div>
    </div>
    
     <style>
   .settings-container{
   background:white;
   border-radius:12px;
   box-shadow:0 5px 20px rgba(0,0,0,0.05);
   }

   .settings-menu{
  
   min-height:500px;
   }

   .settings-menu .nav-link{
   color:#fff;
   font-weight:700;
   padding:12px 15px;
   border-radius:8px;
   text-align: left;
   }

   .settings-menu .nav-link.active{
   background:#0b3c5d59 !important;
   color:white;
   }

   

   .section-title{
   font-weight:600;
   margin-bottom:20px;
   }


     </style>
     <div class="d-flex justify-content-between align-items-center mb-4">
<h4 class="fw-bold">Settings</h4>

</div>
    
  
      <div class="row g-0">
            <!-- LEFT MENU -->
            <div class="col-md-3 settings-menu">
               <div class="nav flex-column nav-pills p-3 text-white" id="v-pills-tab" role="tablist">
                  <button class="nav-link active" type="button" data-bs-target="#platform" data-bs-toggle="pill"><i class="bi bi-gear me-2"></i> Platform</button> <button class="nav-link" type="button" data-bs-target="#payments" data-bs-toggle="pill"><i class="bi bi-credit-card me-2"></i> Payments</button> <button class="nav-link" type="button" data-bs-target="#auction" data-bs-toggle="pill"><i class="bi bi-hammer me-2"></i> Auction</button> <button class="nav-link" type="button" data-bs-target="#notifications" data-bs-toggle="pill"><i class="bi bi-bell me-2"></i> Notifications</button> <button class="nav-link" type="button" data-bs-target="#security" data-bs-toggle="pill"><i class="bi bi-shield-lock me-2"></i> Security</button>
               </div>
            </div><!-- RIGHT CONTENT -->
            <div class="col-md-9">
                <div class="card card-soft p-4">
               <div class="tab-content p-3" id="settings-tab-content">
                  <!-- PLATFORM SETTINGS -->
                  <div class="tab-pane fade show active" id="platform">
                     <h5 class="section-title">Platform Settings</h5>
                     <div class="row g-3">
                        <div class="col-md-6">
                           <label>Platform Name</label> <input class="form-control" type="text" value="Global Fish Auction">
                        </div>
                        <div class="col-md-6">
                           <label>Support Email</label> <input class="form-control" type="email" value="support@auction.com">
                        </div>
                        <div class="col-md-6">
                           <label>Default Currency</label> <select class="form-select">
                              <option>
                                 USD
                              </option>
                              <option>
                                 EUR
                              </option>
                              <option>
                                 INR
                              </option>
                           </select>
                        </div>
                        <div class="col-md-6">
                           <label>Platform Commission (%)</label> <input class="form-control" type="number" value="5">
                        </div>

                        <div class="col-md-12">
                           <button class="btn btn-primary">Save </button>
                        </div>
                     </div>
                  </div><!-- PAYMENT SETTINGS -->
                  <div class="tab-pane fade" id="payments">
                     <h5 class="section-title">Payment Settings</h5>
                     <div class="form-check form-switch mb-3">
                        <input checked class="form-check-input" type="checkbox"> <label>Enable Bank Transfer</label>
                     </div>
                     <div class="form-check form-switch mb-3">
                        <input checked class="form-check-input" type="checkbox"> <label>Enable Credit / Debit Card</label>
                     </div>
                     <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox"> <label>Enable Wallet Payment</label>
                     </div>
                     <div class="form-group mt-3">
                        <label>Payment Deadline (Hours)</label> <input class="form-control" type="number" value="48">
                     </div>
                     <div class="col-md-12">
                           <button class="btn btn-primary">Save </button>
                        </div>
                  </div><!-- AUCTION SETTINGS -->
                  <div class="tab-pane fade" id="auction">
                     <h5 class="section-title">Auction Settings</h5>
                     <div class="row g-3">
                        <div class="col-md-6">
                           <label>Minimum Bid Increment ($)</label> <input class="form-control" type="number" value="50">
                        </div>
                        <div class="col-md-6">
                           <label>Auction Duration (Minutes)</label> <input class="form-control" type="number" value="30">
                        </div>
                        <div class="col-md-6">
                           <label>Auto Extend Auction</label> <select class="form-select">
                              <option>
                                 Enabled
                              </option>
                              <option>
                                 Disabled
                              </option>
                           </select>
                        </div>
                        <div class="col-md-12">
                           <button class="btn btn-primary">Save </button>
                        </div>
                     </div>
                  </div><!-- NOTIFICATION SETTINGS -->
                  <div class="tab-pane fade" id="notifications">
                     <h5 class="section-title">Notification Settings</h5>
                     <div class="form-check form-switch mb-3">
                        <input checked class="form-check-input" type="checkbox"> <label>Email Notifications</label>
                     </div>
                     <div class="form-check form-switch mb-3">
                        <input checked class="form-check-input" type="checkbox"> <label>SMS Notifications</label>
                     </div>
                     <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox"> <label>Push Notifications</label>
                     </div>
                     <div class="col-md-12">
                           <button class="btn btn-primary">Save </button>
                        </div>
                  </div><!-- SECURITY SETTINGS -->
                  <div class="tab-pane fade" id="security">
                     <h5 class="section-title">Security Settings</h5>
                     <div class="form-check form-switch mb-3">
                        <input checked class="form-check-input" type="checkbox"> <label>Enable Two-Factor Authentication</label>
                     </div>
                     <div class="form-check form-switch mb-3">
                        <input checked class="form-check-input" type="checkbox"> <label>Fraud Detection System</label>
                     </div>
                     <div class="form-group mt-3 mb-2">
                        <label>Password Minimum Length</label> <input class="form-control" type="number" value="8">
                     </div>
                     <div class="col-md-12">
                           <button class="btn btn-primary">Save </button>
                        </div>
                  </div>
               </div>
            </div>
            </div>
         </div>
   
      </div>


              
              
            </div>
         </div>
         <!-- /Page Wrapper -->

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" data-cfasync="false"></script>

<script>
         (function () {
            var tabButtons = document.querySelectorAll('#v-pills-tab [data-bs-target]');
            var tabPanes = document.querySelectorAll('#settings-tab-content .tab-pane');
            if (!tabButtons.length || !tabPanes.length) return;

            function activate(targetSelector) {
               tabButtons.forEach(function (btn) {
                  btn.classList.toggle('active', btn.getAttribute('data-bs-target') === targetSelector);
               });

               tabPanes.forEach(function (pane) {
                  var shouldShow = ('#' + pane.id) === targetSelector;
                  pane.classList.toggle('show', shouldShow);
                  pane.classList.toggle('active', shouldShow);
               });
            }

            tabButtons.forEach(function (btn) {
               btn.addEventListener('click', function (e) {
                  e.preventDefault();
                  var target = btn.getAttribute('data-bs-target');
                  if (!target) return;

                  if (window.bootstrap && window.bootstrap.Tab) {
                     window.bootstrap.Tab.getOrCreateInstance(btn).show();
                  } else {
                     activate(target);
                  }
               });
            });
         })();
      </script>

@include('bid_admin.admin.include.footer')

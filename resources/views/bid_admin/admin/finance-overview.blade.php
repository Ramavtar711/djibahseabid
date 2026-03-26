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

   .kpi-card{
   background:rgba(255,255,255,0.82);
   border:1px solid rgba(255,255,255,0.45);
   border-radius:18px;
   padding:20px;
   display:flex;
   align-items:center;
   gap:15px;
   box-shadow:0 10px 20px rgba(0,0,0,0.08);
   transition:0.25s ease;
   backdrop-filter:blur(2px);
   }

   .kpi-card:hover{
   transform:translateY(-3px);
   box-shadow:0 14px 26px rgba(0,0,0,0.13);
   }

   .icon-box{
   width:50px;
   height:50px;
   border-radius:14px;
   display:flex;
   align-items:center;
   justify-content:center;
   font-size:22px;
   color:#fff;
   }

   .icon-revenue{background:#1e6fe7;}
   .icon-commission{background:#1f8d53;}
   .icon-balance{background:#6b49c8;}
   .icon-escrow{background:#f2bf08;color:#000;}
   .icon-pending{background:#e53950;}
   .icon-failed{background:#ff8b11;}
   .icon-invoice{background:#1ecb9a;}
   .icon-validation{background:#14c5eb;color:#000;}

   .kpi-title{
   font-size:13px;
   color:#0c1729;
   font-weight:700;
   margin-bottom:4px;
   }

   .kpi-value{
   font-size:34px;
   line-height:1.1;
   font-weight:700;
   color:#071427;
   }

   .card-soft{
   background:rgba(236,247,255,0.72);
   border:1px solid rgba(255,255,255,0.48);
   border-radius:18px;
   box-shadow:0 10px 22px rgba(0,0,0,0.1);
   backdrop-filter:blur(2px);
   }

   .page-title{
   font-size:42px;
   line-height:1.1;
   font-weight:700;
   color:#071427;
   margin-bottom:16px;
   }

   .section-title{
   font-size:18px;
   font-weight:700;
   color:#071427;
   margin-bottom:16px;
   }

   .table{
   --bs-table-bg:transparent;
   }
     </style>
     <h4 class="page-title">Finance Overview </h4>
     <div class="row g-3 mb-5">
      <div class="col-md-3">
         <div class="kpi-card">
            <div class="icon-box icon-revenue">
               <i class="bi bi-cash-stack"></i>
            </div>
            <div>
               <div class="kpi-title">
                  Total Revenue
               </div>
               <div class="kpi-value">
                  $5,280,000
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="kpi-card">
            <div class="icon-box icon-commission">
               <i class="bi bi-percent"></i>
            </div>
            <div>
               <div class="kpi-title">
                  Total Commission
               </div>
               <div class="kpi-value">
                  $420,800
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="kpi-card">
            <div class="icon-box icon-balance">
               <i class="bi bi-wallet2"></i>
            </div>
            <div>
               <div class="kpi-title">
                  Platform Balance
               </div>
               <div class="kpi-value">
                  $1,240,000
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="kpi-card">
            <div class="icon-box icon-escrow">
               <i class="bi bi-safe2"></i>
            </div>
            <div>
               <div class="kpi-title">
                  Escrow Holding
               </div>
               <div class="kpi-value">
                  $680,000
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="kpi-card">
            <div class="icon-box icon-pending">
               <i class="bi bi-hourglass-split"></i>
            </div>
            <div>
               <div class="kpi-title">
                  Pending Payments
               </div>
               <div class="kpi-value">
                  $145,000
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="kpi-card">
            <div class="icon-box icon-failed">
               <i class="bi bi-x-circle"></i>
            </div>
            <div>
               <div class="kpi-title">
                  Failed Transactions
               </div>
               <div class="kpi-value">
                  56
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="kpi-card">
            <div class="icon-box icon-invoice">
               <i class="bi bi-receipt"></i>
            </div>
            <div>
               <div class="kpi-title">
                  Generated Invoices
               </div>
               <div class="kpi-value">
                  3,420
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="kpi-card">
            <div class="icon-box icon-validation">
               <i class="bi bi-clock-history"></i>
            </div>
            <div>
               <div class="kpi-title">
                  Avg Validation Time
               </div>
               <div class="kpi-value">
                  4.8 Hours
               </div>
            </div>
         </div>
      </div>
   </div>
      <div class="row g-4 mb-4">
         <div class="col-lg-8">
            <div class="card card-soft p-4">
               <h6 class="section-title">Revenue Trend (Monthly)</h6>
               <canvas id="revenueChart"></canvas>
            </div>
         </div>
         <div class="col-lg-4">
            <div class="card card-soft p-4">
               <h6 class="section-title">Payment Method Distribution</h6>
               <canvas id="paymentChart"></canvas>
            </div>
         </div>
         
      </div>
      <div class="row g-4 mb-4">
         <div class="col-lg-8">
            <div class="card card-soft p-4">
               <h6 class="section-title">Commission vs Payout</h6>
               <canvas id="commissionChart"></canvas>
            </div>
         </div>
         
         <div class="col-lg-4">
            <div class="card card-soft p-4">
               <h6 class="section-title">Bank Transfer Status</h6>
               <canvas id="transferChart"></canvas>
            </div>
         </div>
      </div>

      <!-- ================= RISK FLAGS ================= -->
      <div class="card card-soft p-4 mb-4">
         <h6 class="section-title">Financial Risk Flags</h6>
         <div class="row text-center">
            <div class="col-md-3">
               <h6 class="text-warning mb-2">Partial Payments</h6>
               <h4>12</h4>
            </div>
            <div class="col-md-3">
               <h6 class="text-danger mb-2">Underpayments</h6>
               <h4>8</h4>
            </div>
            <div class="col-md-3">
               <h6 class="text-danger mb-2">Duplicate Proof</h6>
               <h4>5</h4>
            </div>
            <div class="col-md-3">
               <h6 class="text-danger mb-2">Fraud Attempts</h6>
               <h4>2 Critical</h4>
            </div>
         </div>
      </div><!-- ================= RECENT LARGE TRANSACTIONS ================= -->
      <div class="card card-soft p-4">
         <h6 class="section-title">Recent Large Transactions</h6>
         <table class="table table-striped">
            <thead>
               <tr>
                  <th>Auction ID</th>
                  <th>Buyer</th>
                  <th>Seller</th>
                  <th>Amount</th>
                  <th>Payment Type</th>
                  <th>Status</th>
               </tr>
            </thead>
            <tbody>
               <tr>
                  <td>AUC2045</td>
                  <td>Oceanic Traders</td>
                  <td>Rahul Seafood</td>
                  <td>$ 48,000</td>
                  <td>Bank Transfer</td>
                  <td><span class="badge bg-success">Validated</span></td>
               </tr>
               <tr>
                  <td>AUC2048</td>
                  <td>BlueWave Ltd</td>
                  <td>Sea Export Co</td>
                  <td>$ 36,500</td>
                  <td>Card</td>
                  <td><span class="badge bg-warning text-dark">Pending</span></td>
               </tr>
               <tr>
                  <td>AUC2050</td>
                  <td>Global Fish Inc</td>
                  <td>FreshCatch Ltd</td>
                  <td>$ 52,200</td>
                  <td>Bank Transfer</td>
                  <td><span class="badge bg-danger">Under Review</span></td>
               </tr>
            </tbody>
         </table>
      </div>


      </div>

              
              
            </div>
         </div>
         <!-- /Page Wrapper -->

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" data-cfasync="false"></script>

<script>
   new Chart(document.getElementById("revenueChart"),{
   type:"line",
   data:{
   labels:["Jan","Feb","Mar","Apr","May","Jun"],
   datasets:[{
   label:"Revenue",
   data:[650000,720000,800000,760000,890000,950000],
   borderColor:"#0d6efd",
   fill:false
   }]
   }
   });

   new Chart(document.getElementById("commissionChart"),{
   type:"bar",
   data:{
   labels:["Jan","Feb","Mar","Apr","May","Jun"],
   datasets:[
   {label:"Commission",data:[52000,58000,64000,60000,72000,80000],backgroundColor:"#198754"},
   {label:"Payout",data:[45000,50000,55000,52000,60000,68000],backgroundColor:"#ffc107"}
   ]
   }
   });

   new Chart(document.getElementById("paymentChart"),{
   type:"doughnut",
   data:{
   labels:["Bank Transfer","Card","UPI"],
   datasets:[{
   data:[65,25,10],
   backgroundColor:["#0d6efd","#198754","#ffc107"]
   }]
   }
   });

   new Chart(document.getElementById("transferChart"),{
   type:"doughnut",
   data:{
   labels:["Validated","Pending","Rejected"],
   datasets:[{
   data:[780000,145000,60000],
   backgroundColor:["#198754","#ffc107","#dc3545"]
   }]
   }
   });
   </script>

@include('bid_admin.admin.include.footer')

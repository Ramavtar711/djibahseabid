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
      .kpi-box{
background: var(--surface-white);
padding:18px;
border-radius:14px;
display:flex;
align-items:center;
gap:15px;
box-shadow:0 5px 20px rgba(0,0,0,0.05);
}

.icon-box{
width:45px;
height:45px;
border-radius:12px;
display:flex;
align-items:center;
justify-content:center;
font-size:20px;
color:#fff;
}

.icon-alert{background:#dc3545;}
.icon-risk{background:#fd7e14;}
.icon-kyc{background:#ffc107;color:#000;}
.icon-dispute{background:#0d6efd;}

.kpi-info h6{
margin:0;
font-size:13px;
color:#6c757d;
}

.kpi-info h4{
margin:0;
font-weight:600;
}
     </style>

    <h4 class="fw-bold mb-4">Risk Monitoring Overview </h4>

    <div class="row g-3 mb-4">
      <div class="col-md-3">
         <div class="kpi-box">
            <div class="icon-box icon-alert">
               <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="kpi-info">
               <h6>Total Risk Alerts</h6>
               <h4 class="risk-high">28</h4>
            </div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="kpi-box">
            <div class="icon-box icon-risk">
               <i class="bi bi-shield-exclamation"></i>
            </div>
            <div class="kpi-info">
               <h6>High Risk Accounts</h6>
               <h4 class="risk-high">6</h4>
            </div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="kpi-box">
            <div class="icon-box icon-kyc">
               <i class="bi bi-person-vcard"></i>
            </div>
            <div class="kpi-info">
               <h6>Pending KYC</h6>
               <h4 class="risk-medium">12</h4>
            </div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="kpi-box">
            <div class="icon-box icon-dispute">
               <i class="bi bi-chat-left-text"></i>
            </div>
            <div class="kpi-info">
               <h6>Active Disputes</h6>
               <h4 class="risk-medium">4</h4>
            </div>
         </div>
      </div>
   </div>

      <div class="row g-4">
         
         <div class="col-md-8">
            <div class="card card-soft p-4">
               <h6>Fraud Activity Trend</h6>
               <canvas id="fraudTrend"></canvas>
            </div>
         </div>
         <div class="col-md-4">
            <div class="card card-soft p-4">
               <h6>Risk Distribution</h6>
               <canvas id="riskChart"></canvas>
            </div>
         </div>
      </div>
    

     
      <div class="card card-soft p-4">
         <h6 class="section-title">Detected Risk Cases</h6>
         <table class="table table-striped">
           <thead>
               <tr>
                  <th>Account</th>
                  <th>Issue Type</th>
                  <th>Auction ID</th>
                  <th>Country</th>
                  <th>Last Login</th>
                  <th>Risk Score</th>
                  <th>Status</th>
                  <th>Action</th>
               </tr>
            </thead>
            <tbody>
               <tr>
                  <td>BlueWave Ltd</td>
                  <td>Multi-Account Detection</td>
                  <td>AUC1024</td>
                  <td>UK</td>
                  <td>2 Hours Ago</td>
                  <td class="risk-high">92%</td>
                  <td><span class="badge bg-danger">High Risk</span></td>
                  <td><button class="btn btn-sm btn-outline-danger">Freeze</button> <button class="btn btn-sm btn-outline-primary">Review</button></td>
               </tr>
               <tr>
                  <td>Oceanic Traders</td>
                  <td>Partial Payment</td>
                  <td>AUC1026</td>
                  <td>USA</td>
                  <td>5 Hours Ago</td>
                  <td class="risk-medium">64%</td>
                  <td><span class="badge bg-warning text-dark">Medium</span></td>
                  <td><button class="btn btn-sm btn-outline-primary">Investigate</button></td>
               </tr>
               <tr>
                  <td>Global Fish Co</td>
                  <td>Duplicate Proof Uploaded</td>
                  <td>AUC1028</td>
                  <td>Canada</td>
                  <td>Yesterday</td>
                  <td class="risk-medium">58%</td>
                  <td><span class="badge bg-warning text-dark">Medium</span></td>
                  <td><button class="btn btn-sm btn-outline-primary">Check Proof</button></td>
               </tr>
               <tr>
                  <td>Sea Export Ltd</td>
                  <td>Fraud Attempt</td>
                  <td>AUC1030</td>
                  <td>India</td>
                  <td>1 Day Ago</td>
                  <td class="risk-high">97%</td>
                  <td><span class="badge bg-danger">Critical</span></td>
                  <td><button class="btn btn-sm btn-danger">Immediate Block</button></td>
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
   new Chart(document.getElementById("riskChart"),{
   type:"doughnut",
   data:{
   labels:["Low Risk","Medium Risk","High Risk"],
   datasets:[{
   data:[45,28,12],
   backgroundColor:["#198754","#ffc107","#dc3545"]
   }]
   }
   });

   new Chart(document.getElementById("fraudTrend"),{
   type:"line",
   data:{
   labels:["Jan","Feb","Mar","Apr","May","Jun"],
   datasets:[{
   label:"Fraud Cases",
   data:[3,6,4,9,7,11],
   borderColor:"#dc3545",
   fill:false
   }]
   }
   });
   </script>

@include('bid_admin.admin.include.footer')

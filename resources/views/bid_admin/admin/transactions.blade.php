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
      .kpi-card{
   background: var(--surface-white);
   padding:18px;
   border-radius:14px;
   display:flex;
   align-items:center;
   gap:15px;
   color:#000;
   }

   .icon-box{
   width:48px;
   height:48px;
   border-radius:12px;
   display:flex;
   align-items:center;
   justify-content:center;
   font-size:20px;
   color:white;
   }

   .bg1{background:#0d6efd;}
   .bg2{background:#198754;}
   .bg3{background:#ffc107;color:#000;}
   .bg4{background:#dc3545;}

   
   .filter-box{
  background: var(--surface-white);
   padding:15px;
   border-radius:12px;
   }

     </style>
   
      <h4 class="fw-bold mb-4">All Transactions </h4>
      <div class="filter-box mb-4">
         <div class="row g-3">
            <div class="col-md-3">
               <input class="form-control" placeholder="Search Buyer / Auction ID" type="text">
            </div>
            <div class="col-md-2">
               <select class="form-select">
                  <option>
                     Status
                  </option>
                  <option>
                     Successful
                  </option>
                  <option>
                     Pending
                  </option>
                  <option>
                     Failed
                  </option>
               </select>
            </div>
            <div class="col-md-2">
               <select class="form-select">
                  <option>
                     Payment Type
                  </option>
                  <option>
                     Bank Transfer
                  </option>
                  <option>
                     UPI
                  </option>
                  <option>
                     Card
                  </option>
                  <option>
                     Wallet
                  </option>
               </select>
            </div>
            <div class="col-md-2">
               <input class="form-control" type="date">
            </div>
            <div class="col-md-2">
               <button class="btn btn-primary w-100">Apply Filter</button>
            </div>
            <div class="col-md-1">
               <button class="btn btn-outline-secondary w-100">Reset</button>
            </div>
         </div>
      </div>
     

      
      <div class="card card-soft p-4">
         <h6 class="section-title">All Transactions</h6>
         <table class="table table-striped">
            <thead>
               <tr>
                  <th>Auction ID</th>
                  <th>Buyer</th>
                  <th>Company</th>
                  <th>Amount</th>
                  <th>Payment Type</th>
                  <th>Status</th>
                  <th>Risk</th>
                  <th>Date</th>
                  <th>Action</th>
               </tr>
            </thead>
            <tbody>
               <tr>
                  <td>AUC1024</td>
                  <td>Michael Lee</td>
                  <td>Oceanic Traders</td>
                  <td>$4,800</td>
                  <td>Bank Transfer</td>
                  <td><span class="badge bg-success">Successful</span></td>
                  <td>—</td>
                  <td>05 Mar 2026</td>
                  <td><button class="btn btn-sm btn-outline-primary">View</button></td>
               </tr>
               <tr>
                  <td>AUC1025</td>
                  <td>Daniel Kim</td>
                  <td>BlueWave Exports</td>
                  <td>$3,200</td>
                  <td>Bank Transfer</td>
                  <td><span class="badge bg-warning text-dark">Pending</span></td>
                  <td><span class="badge bg-danger">Underpayment</span></td>
                  <td>05 Mar 2026</td>
                  <td><button class="btn btn-sm btn-outline-primary">Review</button></td>
               </tr>
               <tr>
                  <td>AUC1026</td>
                  <td>John Smith</td>
                  <td>Global Fish Ltd</td>
                  <td>$6,100</td>
                  <td>Card</td>
                  <td><span class="badge bg-danger">Failed</span></td>
                  <td>Duplicate Proof</td>
                  <td>04 Mar 2026</td>
                  <td><button class="btn btn-sm btn-outline-secondary">Details</button></td>
               </tr>
               <tr>
                  <td>AUC1027</td>
                  <td>Raj Patel</td>
                  <td>India Marine</td>
                  <td>$2,900</td>
                  <td>UPI</td>
                  <td><span class="badge bg-success">Successful</span></td>
                  <td>—</td>
                  <td>04 Mar 2026</td>
                  <td><button class="btn btn-sm btn-outline-primary">View</button></td>
               </tr>
               <tr>
                  <td>AUC1028</td>
                  <td>Ahmed Ali</td>
                  <td>Sea Export Ltd</td>
                  <td>$5,500</td>
                  <td>Bank Transfer</td>
                  <td><span class="badge bg-warning text-dark">Pending</span></td>
                  <td><span class="badge bg-warning text-dark">Partial Payment</span></td>
                  <td>03 Mar 2026</td>
                  <td><button class="btn btn-sm btn-outline-primary">Investigate</button></td>
               </tr>
               <tr>
                  <td>AUC1029</td>
                  <td>Lucas Martin</td>
                  <td>Atlantic Foods</td>
                  <td>$7,200</td>
                  <td>Card</td>
                  <td><span class="badge bg-success">Successful</span></td>
                  <td>—</td>
                  <td>03 Mar 2026</td>
                  <td><button class="btn btn-sm btn-outline-primary">View</button></td>
               </tr>
               <tr>
                  <td>AUC1030</td>
                  <td>Chris Walker</td>
                  <td>SeaTrade Ltd</td>
                  <td>$8,900</td>
                  <td>Bank Transfer</td>
                  <td><span class="badge bg-danger">Failed</span></td>
                  <td><span class="badge bg-danger">Fraud Attempt</span></td>
                  <td>02 Mar 2026</td>
                  <td><button class="btn btn-sm btn-danger">Block</button></td>
               </tr>
               <tr>
                  <td>AUC1031</td>
                  <td>Maria Lopez</td>
                  <td>Pacific Export</td>
                  <td>$4,400</td>
                  <td>Wallet</td>
                  <td><span class="badge bg-success">Successful</span></td>
                  <td>—</td>
                  <td>02 Mar 2026</td>
                  <td><button class="btn btn-sm btn-outline-primary">View</button></td>
               </tr>
            </tbody>
         </table>
      </div>


      </div>

              
              
            </div>
         </div>
         <!-- /Page Wrapper -->

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" data-cfasync="false"></script>

@include('bid_admin.admin.include.footer')

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
     <h4 class="fw-bold mb-4">Bank Transfer </h4>
     <div class="row g-3 mb-4">
         <div class="col-md-3">
            <div class="kpi-card">
               <div class="icon-box bg1">
                  <i class="bi bi-bank"></i>
               </div>
               <div>
                  <small>Total Transfers</small>
                  <h5>124</h5>
               </div>
            </div>
         </div>
         <div class="col-md-3">
            <div class="kpi-card">
               <div class="icon-box bg2">
                  <i class="bi bi-check-circle"></i>
               </div>
               <div>
                  <small>Validated</small>
                  <h5>82</h5>
               </div>
            </div>
         </div>
         <div class="col-md-3">
            <div class="kpi-card">
               <div class="icon-box bg3">
                  <i class="bi bi-clock-history"></i>
               </div>
               <div>
                  <small>Pending</small>
                  <h5>29</h5>
               </div>
            </div>
         </div>
         <div class="col-md-3">
            <div class="kpi-card">
               <div class="icon-box bg4">
                  <i class="bi bi-x-circle"></i>
               </div>
               <div>
                  <small>Rejected</small>
                  <h5>13</h5>
               </div>
            </div>
         </div>
      </div>

      <div class="filter-box mb-4">
         <div class="row g-3">
            <div class="col-md-3">
               <input class="form-control" placeholder="Search Buyer / Auction" type="text">
            </div>
            <div class="col-md-2">
               <select class="form-select">
                  <option>
                     Status
                  </option>
                  <option>
                     Pending
                  </option>
                  <option>
                     Under Review
                  </option>
                  <option>
                     Approved
                  </option>
                  <option>
                     Rejected
                  </option>
               </select>
            </div>
            <div class="col-md-2">
               <select class="form-select">
                  <option>
                     Country
                  </option>
                  <option>
                     USA
                  </option>
                  <option>
                     UK
                  </option>
                  <option>
                     France
                  </option>
                  <option>
                     India
                  </option>
                  <option>
                     Canada
                  </option>
               </select>
            </div>
            <div class="col-md-2">
               <input class="form-control" type="date">
            </div>
            <div class="col-md-2">
               <button class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Apply</button>
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
                  <th>Buyer</th>
                  <th>Company</th>
                  <th>Country</th>
                  <th>Auction ID</th>
                  <th>Lot Description</th>
                  <th>Amount</th>
                  <th>Auction Close</th>
                  <th>Payment Deadline</th>
                  <th>Status</th>
                  <th>Action</th>
               </tr>
            </thead>
            <tbody>
               <tr>
                  <td>Michael Lee</td>
                  <td>BlueWave Ltd</td>
                  <td>UK</td>
                  <td>AUC1041</td>
                  <td>Tuna 500kg</td>
                  <td>$12,500</td>
                  <td>04 Mar</td>
                  <td>08 Mar</td>
                  <td><span class="badge bg-warning text-dark">Pending</span></td>
                  <td><button class="btn btn-sm btn-success">Approve</button> <button class="btn btn-sm btn-danger">Reject</button></td>
               </tr>
               <tr>
                  <td>David Chen</td>
                  <td>Oceanic Traders</td>
                  <td>USA</td>
                  <td>AUC1042</td>
                  <td>Salmon Frozen</td>
                  <td>$9,200</td>
                  <td>04 Mar</td>
                  <td>08 Mar</td>
                  <td><span class="badge bg-info">Under Review</span></td>
                  <td><button class="btn btn-sm btn-primary">Review</button></td>
               </tr>
               <tr>
                  <td>Ahmed Ali</td>
                  <td>Sea Export Ltd</td>
                  <td>UAE</td>
                  <td>AUC1043</td>
                  <td>Shrimp IQF</td>
                  <td>$18,900</td>
                  <td>03 Mar</td>
                  <td>07 Mar</td>
                  <td><span class="badge bg-success">Approved</span></td>
                  <td><button class="btn btn-sm btn-outline-secondary">View</button></td>
               </tr>
               <tr>
                  <td>Lucas Martin</td>
                  <td>Global Fish Co</td>
                  <td>France</td>
                  <td>AUC1044</td>
                  <td>Cod Fillet</td>
                  <td>$7,400</td>
                  <td>03 Mar</td>
                  <td>07 Mar</td>
                  <td><span class="badge bg-danger">Rejected</span></td>
                  <td><button class="btn btn-sm btn-outline-secondary">Details</button></td>
               </tr>
               <tr>
                  <td>John Smith</td>
                  <td>Atlantic Foods</td>
                  <td>USA</td>
                  <td>AUC1045</td>
                  <td>Tuna Steak</td>
                  <td>$13,500</td>
                  <td>02 Mar</td>
                  <td>06 Mar</td>
                  <td><span class="badge bg-warning text-dark">Pending</span></td>
                  <td><button class="btn btn-sm btn-success">Approve</button> <button class="btn btn-sm btn-danger">Reject</button></td>
               </tr>
               <tr>
                  <td>Raj Patel</td>
                  <td>India Marine</td>
                  <td>India</td>
                  <td>AUC1046</td>
                  <td>Fresh Prawns</td>
                  <td>$5,900</td>
                  <td>02 Mar</td>
                  <td>06 Mar</td>
                  <td><span class="badge bg-info">Under Review</span></td>
                  <td><button class="btn btn-sm btn-primary">Review</button></td>
               </tr>
               <tr>
                  <td>Maria Lopez</td>
                  <td>Pacific Export</td>
                  <td>Spain</td>
                  <td>AUC1047</td>
                  <td>Frozen Sardine</td>
                  <td>$4,200</td>
                  <td>01 Mar</td>
                  <td>05 Mar</td>
                  <td><span class="badge bg-warning text-dark">Pending</span></td>
                  <td><button class="btn btn-sm btn-success">Approve</button> <button class="btn btn-sm btn-danger">Reject</button></td>
               </tr>
               <tr>
                  <td>Chris Walker</td>
                  <td>SeaTrade Ltd</td>
                  <td>UK</td>
                  <td>AUC1048</td>
                  <td>Crab Meat</td>
                  <td>$6,300</td>
                  <td>01 Mar</td>
                  <td>05 Mar</td>
                  <td><span class="badge bg-info">Under Review</span></td>
                  <td><button class="btn btn-sm btn-primary">Review</button></td>
               </tr>
               <tr>
                  <td>Daniel Kim</td>
                  <td>Korea Seafood</td>
                  <td>Korea</td>
                  <td>AUC1049</td>
                  <td>Octopus Frozen</td>
                  <td>$11,800</td>
                  <td>28 Feb</td>
                  <td>04 Mar</td>
                  <td><span class="badge bg-success">Approved</span></td>
                  <td><button class="btn btn-sm btn-outline-secondary">View</button></td>
               </tr>
               <tr>
                  <td>Peter Brown</td>
                  <td>Nordic Fish</td>
                  <td>Norway</td>
                  <td>AUC1050</td>
                  <td>Salmon Premium</td>
                  <td>$21,000</td>
                  <td>28 Feb</td>
                  <td>04 Mar</td>
                  <td><span class="badge bg-warning text-dark">Pending</span></td>
                  <td><button class="btn btn-sm btn-success">Approve</button> <button class="btn btn-sm btn-danger">Reject</button></td>
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

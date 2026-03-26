
   
     @include('bid_admin.admin.include.header')
    
    @include('bid_admin.admin.include.side_menu')
    
    <!-- Page Wrapper -->
         <div class="page-wrapper dashboard-page">
            <div class="content container-fluid">
              <style>
                 .dashboard-page .content.container-fluid{
                    padding-top: 10px !important;
                 }

                 .dashboard-page .status-header{
                    margin-top: 0 !important;
                 }
              </style>
              
              <div class="status-header d-flex flex-wrap justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-4">
            <div class="small"><i class="bi bi-circle-fill text-success me-1"></i> SYSTEM STATUS: <strong>LIVE</strong></div>
            <div class="small"><i class="bi bi-circle-fill text-danger me-1"></i> <strong>6</strong> Live Auctions</div>
            <div class="small"><i class="bi bi-circle-fill text-warning me-1"></i> <strong>3</strong> Upcoming</div>
            <div class="small"><i class="bi bi-circle-fill text-success me-1"></i> <strong>$12,450</strong> Revenue Today</div>
        </div>
        <div class="fw-bold">48 <span class="text-muted fw-normal">Buyers Online</span></div>
    </div>
    <div class="row g-3">
        <div class="col-md-3">
            <div class="card p-3 kpi-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small uppercase fw-bold">Active Auctions</p>
                        <h3 class="fw-bold mb-0">142</h3>
                        <span class="trend-up"><i data-lucide="trending-up" class="d-inline" style="width:14px"></i> +12%</span>
                    </div>
                    <div class="icon-box bg-primary-soft"><i class="bi bi-hourglass-bottom" style="font-size:20px"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 kpi-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small fw-bold">Gross Revenue</p>
                        <h3 class="fw-bold mb-0">$1.2M</h3>
                        <span class="trend-up">+8.4%</span>
                    </div>
                    <div class="icon-box bg-success text-white"><i class="bi bi-currency-dollar" style="font-size:20px"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 kpi-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small fw-bold">Volume Sold</p>
                        <h3 class="fw-bold mb-0">42.5 <span class="fs-6 fw-normal">tons</span></h3>
                        <span class="trend-up">+5.2%</span>
                    </div>
                    <div class="icon-box bg-warning text-white"><i class="bi bi-box" style="font-size:20px"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 kpi-card border-start border-danger border-4">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small fw-bold">Ending Soon (24h)</p>
                        <h3 class="fw-bold mb-0 text-danger">18</h3>
                        <span class="small text-muted">High priority</span>
                    </div>
                    <div class="icon-box bg-danger-subtle text-danger"><i class="bi bi-clock" style="font-size:20px"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8">
            
            <div class="dashboard-card p-3 pb-0">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">Live Auctions <span class="text-muted fw-normal">Control Center</span></h6>
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-sm btn-light border" onclick="scrollSlider(-300)"><i class="bi bi-chevron-left"></i></button>
                        <button class="btn btn-sm btn-light border" onclick="scrollSlider(300)"><i class="bi bi-chevron-right"></i></button>
                        <i class="bi bi-three-dots text-muted ms-2"></i>
                    </div>
                </div>

                <div class="auction-slider" id="auctionSlider">
                    <div class="auction-item">
                        <div class="card p-2 position-relative pb-0 mb-0">
                            <span class="badge badge-live position-absolute m-2 top-0 start-0">â— LIVE</span>
                            <img src="https://images.unsplash.com/photo-1599488615731-7e5c2823ff28?auto=format&fit=crop&w=400&h=250" class="card-img-top rounded" alt="Tuna">
                            <div class="card-body px-1 py-2">
                                <p class="mb-0 fw-bold">Lot #12 Yellowfin Tuna</p>
                                <small class="text-muted">Hanani Market</small>
                                <div class="d-flex justify-content-between mt-2 small">
                                    <span class="text-muted">Current Bid</span>
                                    <span class="fw-bold">$210/kg</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 small">
                                    <span class="text-muted">Quantity</span>
                                    <span class="badge bg-light text-dark border">120 kg <span class="text-warning ms-1">22:31</span></span>
                                </div>
                                <div class="btn-group w-100 btn-action-group">
                                    <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-pause-fill"></i></button>
                                    <button class="btn btn-primary btn-sm w-100">Extend 5m</button>
                                    <button class="btn btn-danger btn-sm"><i class="bi bi-x-lg"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="auction-item">
                        <div class="card p-2 position-relative pb-0 mb-0">
                            <span class="badge badge-live position-absolute m-2 top-0 start-0">â— LIVE</span>
                            <img src="https://images.unsplash.com/photo-1615141982883-c7ad0e69fd62?auto=format&fit=crop&w=400&h=250" class="card-img-top rounded" alt="Sardines">
                            <div class="card-body px-1 py-2">
                                <p class="mb-0 fw-bold">Lot #8 Sardines</p>
                                <small class="text-muted">Creata Ltd</small>
                                <div class="d-flex justify-content-between mt-2 small">
                                    <span class="text-muted">Current Bid</span>
                                    <span class="fw-bold">$55/kg</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 small">
                                    <span class="text-muted">Quantity</span>
                                    <span class="badge bg-light text-dark border">11 kg <span class="text-danger ms-1">1:15</span></span>
                                </div>
                                <div class="btn-group w-100 btn-action-group">
                                    <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-pause-fill"></i></button>
                                    <button class="btn btn-primary btn-sm w-100">Extend 5m</button>
                                    <button class="btn btn-danger btn-sm"><i class="bi bi-x-lg"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="auction-item">
                        <div class="card p-2 position-relative pb-0 mb-0">
                            <span class="badge badge-live position-absolute m-2 top-0 start-0">â— LIVE</span>
                            <img src="https://images.unsplash.com/photo-1599488615731-7e5c2823ff28?auto=format&fit=crop&w=400&h=250" class="card-img-top rounded" alt="Squid">
                            <div class="card-body px-1 py-2">
                                <p class="mb-0 fw-bold">Lot #15 Squid</p>
                                <small class="text-muted">SIDAL CLEM</small>
                                <div class="d-flex justify-content-between mt-2 small">
                                    <span class="text-muted">Current Bid</span>
                                    <span class="fw-bold">$182/kg</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 small">
                                    <span class="text-muted">Quantity</span>
                                    <span class="badge bg-light text-dark border">90 kg <span class="text-warning ms-1">3:42</span></span>
                                </div>
                                <div class="btn-group w-100 btn-action-group">
                                    <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-pause-fill"></i></button>
                                    <button class="btn btn-primary btn-sm w-100">Extend 5m</button>
                                    <button class="btn btn-danger btn-sm"><i class="bi bi-x-lg"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="auction-item">
                        <div class="card p-2 position-relative pb-0 mb-0">
                            <span class="badge badge-live position-absolute m-2 top-0 start-0">â— LIVE</span>
                            <img src="https://images.unsplash.com/photo-1599488615731-7e5c2823ff28?auto=format&fit=crop&w=400&h=250" class="card-img-top rounded" alt="Tuna">
                            <div class="card-body px-1 py-2">
                                <p class="mb-0 fw-bold">Lot #12 Yellowfin Tuna</p>
                                <small class="text-muted">Hanani Market</small>
                                <div class="d-flex justify-content-between mt-2 small">
                                    <span class="text-muted">Current Bid</span>
                                    <span class="fw-bold">$210/kg</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 small">
                                    <span class="text-muted">Quantity</span>
                                    <span class="badge bg-light text-dark border">120 kg <span class="text-warning ms-1">22:31</span></span>
                                </div>
                                <div class="btn-group w-100 btn-action-group">
                                    <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-pause-fill"></i></button>
                                    <button class="btn btn-primary btn-sm w-100">Extend 5m</button>
                                    <button class="btn btn-danger btn-sm"><i class="bi bi-x-lg"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="auction-item">
                        <div class="card p-2 position-relative pb-0 mb-0">
                            <span class="badge badge-live position-absolute m-2 top-0 start-0">â— LIVE</span>
                            <img src="https://images.unsplash.com/photo-1599488615731-7e5c2823ff28?auto=format&fit=crop&w=400&h=250" class="card-img-top rounded" alt="Squid">
                            <div class="card-body px-1 py-2">
                                <p class="mb-0 fw-bold">Lot #15 Squid</p>
                                <small class="text-muted">SIDAL CLEM</small>
                                <div class="d-flex justify-content-between mt-2 small">
                                    <span class="text-muted">Current Bid</span>
                                    <span class="fw-bold">$182/kg</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 small">
                                    <span class="text-muted">Quantity</span>
                                    <span class="badge bg-light text-dark border">90 kg <span class="text-warning ms-1">3:42</span></span>
                                </div>
                                <div class="btn-group w-100 btn-action-group">
                                    <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-pause-fill"></i></button>
                                    <button class="btn btn-primary btn-sm w-100">Extend 5m</button>
                                    <button class="btn btn-danger btn-sm"><i class="bi bi-x-lg"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

         
<div class="card-custom mb-4">



<div class="row">
<div class="col-md-6">
<div>
<div class="title">Fish Volume <span class="sub">6.90 â€¢ 44 Granting</span></div>
</div>
<div class="card shadow-sm p-2">
<canvas id="volumeChart" height="60" width="100%"></canvas>
</div>
</div>

<div class="col-md-6">
   <div>
<div class="title">Transaction Overview <span class="sub">6.90 â€¢ 44 Granting</span></div>
</div>
   <div class="card shadow-sm p-2">
<canvas id="transactionChart" height="60" width="100%"></canvas>
</div>
</div>
</div>

</div>

<!-- Analytics -->
<div class="card-custom">


<div class="row g-4">
         <!-- LEFT SIDE -->
         <div class="col-lg-12">
            <div class="analytics-wrapper">
               <div class="d-flex justify-content-between align-items-center mb-3">
                  <div class="section-title">
                     Analytics & Reports
                  </div>
                  <div>
                     <button class="filter-btn">Today</button> <button class="filter-btn">This Week</button> <button class="filter-btn">This Month</button> <button class="filter-btn">Custom</button>
                  </div>
               </div>
               <div class="row g-3">
                  <!-- Fish Volume Card -->
                  <div class="col-md-6">
                     <div class="small-card">
                        <div class="card-title">
                           Fish Volume
                        </div>
                        <div class="rank-item">
                           <div>
                              <span class="rank-number">#1</span> SIDAL CLEM
                           </div>
                           <div>
                              45
                           </div>
                        </div>
                        <div class="rank-item">
                           <div>
                              <span class="rank-number">#2</span> Hanani Market
                           </div>
                           <div>
                              40
                           </div>
                        </div>
                        <div class="rank-item mb-0">
                           <div>
                              <span class="rank-number">#3</span> Everfish Co.
                           </div>
                           <div>
                              35
                           </div>
                        </div>
                     </div>
                  </div><!-- Transaction Value Card -->
                  <div class="col-md-6">
                     <div class="small-card">
                        <div class="card-title">
                           Transaction Value ($)
                        </div>
                        <div class="rank-item">
                           <div>
                              <span class="rank-number">#1</span> Yellowfin Tuna
                           </div>
                           <div class="d-flex align-items-center" style="width:40%;">
                              <div class="progress w-100">
                                 <div class="progress-bar blue" style="width:70%"></div>
                              </div><span class="percent">0.29%</span>
                           </div>
                        </div>
                        <div class="rank-item">
                           <div>
                              <span class="rank-number">#2</span> Sardines
                           </div>
                           <div class="d-flex align-items-center" style="width:40%;">
                              <div class="progress w-100">
                                 <div class="progress-bar orange" style="width:40%"></div>
                              </div><span class="percent">2.23%</span>
                           </div>
                        </div>
                        <div class="rank-item mb-0">
                           <div>
                              <span class="rank-number">#3</span> Grouper
                           </div>
                           <div class="d-flex align-items-center" style="width:40%;">
                              <div class="progress w-100">
                                 <div class="progress-bar blue" style="width:55%"></div>
                              </div><span class="percent">1.10%</span>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div><!-- RIGHT SIDE Revenue -->
          <div class="col-lg-12">
         <div class="revenue-card">
               <div class="d-flex justify-content-between align-items-center mb-3">
                  <div class="section-title">
                     Revenue Trend
                  </div>
                  <div style="font-size:20px;">
                     â‹¯
                  </div>
               </div>
               <canvas height="110" id="revenueChart"></canvas>
            </div>
         </div>
      </div>



</div>   
        </div>

        
   

               


   



<!-- RIGHT SIDE -->
<div class="col-lg-4">
 <div class="dashboard-card card p-3">
                <div class="d-flex justify-content-between mb-3">
                    <h6 class="fw-bold">Alert Center</h6>
                    <i class="bi bi-three-dots"></i>
                </div>
                <div class="d-flex gap-2 mb-3 align-items-start">
                    <div class="rounded-circle bg-danger-subtle p-1 px-2"><i class="bi bi-lightning-fill text-danger"></i></div>
                    <div class="flex-grow-1">
                        <p class="mb-0 small fw-bold text-danger">SERVER LAG</p>
                        <small class="text-muted">High latency detected!</small>
                    </div>
                    <small class="text-muted">2m</small>
                </div>
                <div class="d-flex gap-2 mb-3 align-items-start">
                    <div class="rounded-circle bg-danger-subtle p-1 px-2"><i class="bi bi-exclamation-triangle-fill text-warning"></i></div>
                    <div class="flex-grow-1">
                        <p class="mb-0 small fw-bold">NO BIDS</p>
                        <small class="text-muted">Lot #8 has no bids for 5 min</small>
                    </div>
                    <small class="text-muted">4m</small>
                </div>
                <div class="d-flex gap-2 mb-3 align-items-start">
                    <div class="rounded-circle bg-danger-subtle p-1 px-2"><i class="bi bi-exclamation-triangle-fill text-danger"></i></div>
                    <div class="flex-grow-1">
                        <p class="mb-0 small fw-bold">PAYMENT FAILED</p>
                        <small class="text-muted">Lot #8 has no bids for 5 min</small>
                    </div>
                    <small class="text-muted">4m</small>
                </div>

                <div class="d-flex gap-2 mb-3 align-items-start">
                    <div class="rounded-circle bg-danger-subtle p-1 px-2"><i class="bi bi-exclamation-triangle-fill text-warning"></i></div>
                    <div class="flex-grow-1">
                        <p class="mb-0 small fw-bold">QC PENDING</p>
                        <small class="text-muted">Lot #8 has no bids for 5 min</small>
                    </div>
                    <small class="text-muted">4m</small>
                </div>
                <hr>
                <div class="text-center"><a href="#" class="text-decoration-none small">View All <i class="bi bi-chevron-right"></i></a></div>
            </div>  

<div class="dashboard-card card p-3">
                <h6 class="fw-bold mb-3">Upcoming Auctions</h6>
                <div class="border rounded p-3 mb-3 bg-light bg-opacity-25">
                    <div class="d-flex gap-3 align-items-center mb-3">
                        <img src="https://images.unsplash.com/photo-1599488615731-7e5c2823ff28?auto=format&fit=crop&w=400&h=250" class="rounded" alt="Fish" style="width: 60px; height: 60px;">
                        <div>
                            <small class="text-muted d-block">Lot #2 King Fish</small>
                            <h4 class="fw-bold mb-0">01:20:00</h4>
                        </div>
                    </div>
                    <div class="small mb-2 text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i> GG: Approved</div>
                    <button class="btn btn-primary w-100 py-2 fw-bold">Launch Now</button>
                </div>
                
                
            </div>
 <div class="dashboard-card card p-3">
   <div class="d-flex justify-content-between mb-3">
                    <h6 class="fw-bold">ðŸ† Top Buyers</h6>
                    <i class="bi bi-three-dots"></i>
                </div>
            
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <div class="list-group-item p-3 border-0 border-bottom">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary-soft rounded-circle p-3 me-3">
                                    <i class="bi bi-building" style="width: 20px;"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Oceanic Foods Ltd</h6>
                                    <small class="text-muted"><i data-lucide="map-pin" class="d-inline" style="width:12px"></i> Japan</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-success">$240,500</div>
                                <small class="text-muted">5.2 tons Â· 42 Wins</small>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item p-3 border-0 border-bottom">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary-soft rounded-circle p-3 me-3">
                                    <i class="bi bi-building" style="width: 20px;"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Global Catch Co.</h6>
                                    <small class="text-muted"><i data-lucide="map-pin" class="d-inline" style="width:12px"></i> USA</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-success">$185,200</div>
                                <small class="text-muted">3.8 tons Â· 31 Wins</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

  <div class="dashboard-card card p-3">
   <div class="d-flex justify-content-between mb-3">
                    <h6 class="fw-bold">âš“ Top Sellers</h6>
                    <i class="bi bi-three-dots"></i>
                </div>
           
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <div class="list-group-item p-3 border-0 border-bottom">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-0 fw-bold">Atlantic Blue Fin</h6>
                                <small class="text-muted">Value: <span class="text-dark fw-bold">$380,000</span></small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-success-subtle text-success">93% Success</span>
                            </div>
                        </div>
                        <div class="row g-0 align-items-center">
                            <div class="col-12">
                                <div class="progress mb-1" style="height: 4px;">
                                    <div class="progress-bar bg-success" style="width: 93%"></div>
                                </div>
                                <div class="d-flex justify-content-between small text-muted">
                                    <span>Listed: 8.5t</span>
                                    <span>Sold: 7.9t</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item p-3 border-0 border-bottom">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-0 fw-bold">DeepSea Trawlers</h6>
                                <small class="text-muted">Value: <span class="text-dark fw-bold">$210,400</span></small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-warning-subtle text-warning">82% Success</span>
                            </div>
                        </div>
                        <div class="row g-0 align-items-center">
                            <div class="col-12">
                                <div class="progress mb-1" style="height: 4px;">
                                    <div class="progress-bar bg-warning" style="width: 82%"></div>
                                </div>
                                <div class="d-flex justify-content-between small text-muted">
                                    <span>Listed: 6.2t</span>
                                    <span>Sold: 5.1t</span>
                                </div>
                            </div>
                        </div>
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
      </div>
      <!-- /Main Wrapper -->
 

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" data-cfasync="false"></script>

    
      <!-- jQuery -->
      <!-- Bootstrap Core JS -->
      <!-- Feather Icon JS -->
      <!-- Slimscroll JS -->
      <!-- Theme Settings JS -->
      <!-- Custom JS -->
      <script>

// Volume Bar
new Chart(document.getElementById("volumeChart"),{
type:"bar",
data:{
labels:["Mon","Tue","Wed","Thu","Fri","Sat","Sun"],
datasets:[{
data:[1500,1400,1300,1600,1700,2000,2200],
backgroundColor:"#4e73df",
borderRadius:6
}]
},
options:{plugins:{legend:{display:false}}}
});

// Transaction Mixed Chart
new Chart(document.getElementById("transactionChart"),{
data:{
labels:["Mon","Tue","Wed","Thu","Fri","Sat","Sun"],
datasets:[
{
type:"bar",
data:[800,1000,1500,1200,2000,2500,3000],
backgroundColor:"#a0c4ff",
borderRadius:6
},
{
type:"line",
data:[600,900,1300,1500,2200,2700,3200],
borderColor:"#4e73df",
tension:0.4,
fill:false
}
]
},
options:{plugins:{legend:{display:false}}}
});

// Revenue Line
new Chart(document.getElementById("revenueChart"),{
type:"line",
data:{
labels:["Mon","Tue","Wed","Thu","Fri","Sat","Sun"],
datasets:[{
data:[2000,2300,2500,2800,3000,3400,3700],
borderColor:"#4e73df",
backgroundColor:"rgba(78,115,223,0.1)",
fill:true,
tension:0.4
}]
},
options:{
plugins:{legend:{display:false}},
scales:{x:{display:false},y:{display:false}}
}
});

</script>
<script>
    function scrollSlider(amount) {
        const slider = document.getElementById('auctionSlider');
        slider.scrollBy({
            left: amount,
            behavior: 'smooth'
        });
    }
</script>
<script>
    // Initialize Lucide Icons
    lucide.createIcons();

    // 1. Revenue Chart (Line)
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Revenue (USD)',
                data: [400000, 550000, 480000, 700000, 850000, 1200000],
                borderColor: '#4338ca',
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(67, 56, 202, 0.1)'
            }]
        },
        options: { maintainAspectRatio: false, plugins: { legend: { display: false } } }
    });

    // 2. Species Chart (Doughnut)
    new Chart(document.getElementById('speciesChart'), {
        type: 'doughnut',
        data: {
            labels: ['Tuna', 'Salmon', 'Cod', 'Shrimp'],
            datasets: [{
                data: [45, 25, 20, 10],
                backgroundColor: ['#4338ca', '#10b981', '#f59e0b', '#64748b']
            }]
        },
        options: { maintainAspectRatio: false }
    });

    // 3. Fresh vs Frozen Chart (Bar)
    new Chart(document.getElementById('priceEvolutionChart'), {
        type: 'bar',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [
                { label: 'Fresh', data: [12, 15, 14, 18], backgroundColor: '#10b981' },
                { label: 'Frozen', data: [8, 9, 7, 10], backgroundColor: '#64748b' }
            ]
        },
        options: { maintainAspectRatio: false }
    });
</script>
@include('bid_admin.admin.include.footer')







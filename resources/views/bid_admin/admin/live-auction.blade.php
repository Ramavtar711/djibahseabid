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

    <div class="row">
        <div class="col-lg-12">
         <h4 class="fw-bold mb-4">Live Auctions </h4>
    
    <div class="auction-grid">
        
        <div class="auction-card">
            <div class="img-container">
                <span class="live-label">â— LIVE</span>
                <img src="https://loremflickr.com/400/300/fish,tuna?lock=1" alt="Fish">
            </div>
            <div class="card-info">
                <div class="lot-name">Lot #12 Yellowfin Tuna</div>
                <div class="market-label">Hanani Market</div>
                <div class="data-row">
                    <span class="data-label">Current Bid</span>
                    <span class="data-value">$210/kg</span>
                </div>
                <div class="data-row">
                    <span class="data-label">Quantity</span>
                    <span class="data-value">120 kg <span class="timer">22:31</span></span>
                </div>
            </div>
            <div class="action-group">
                <button class="btn-pause"><i class="fas fa-pause"></i></button>
                <button class="btn-extend">Extend 5m</button>
                <button class="btn-stop"><i class="fas fa-times"></i></button>
            </div>
        </div>

        <div class="auction-card">
            <div class="img-container">
                <span class="live-label">â— LIVE</span>
                <img src="https://loremflickr.com/400/300/fish,sardine?lock=2" alt="Fish">
            </div>
            <div class="card-info">
                <div class="lot-name">Lot #8 Sardines</div>
                <div class="market-label">Creata Ltd</div>
                <div class="data-row">
                    <span class="data-label">Current Bid</span>
                    <span class="data-value">$55/kg</span>
                </div>
                <div class="data-row">
                    <span class="data-label">Quantity</span>
                    <span class="data-value">11 kg <span class="timer text-danger">1:15</span></span>
                </div>
            </div>
            <div class="action-group">
                <button class="btn-pause"><i class="fas fa-pause"></i></button>
                <button class="btn-extend">Extend 5m</button>
                <button class="btn-stop"><i class="fas fa-times"></i></button>
            </div>
        </div>

        <div class="auction-card">
            <div class="img-container">
                <span class="live-label">â— LIVE</span>
                <img src="https://loremflickr.com/400/300/fish,squid?lock=3" alt="Fish">
            </div>
            <div class="card-info">
                <div class="lot-name">Lot #15 Squid</div>
                <div class="market-label">SIDAL CLEM</div>
                <div class="data-row">
                    <span class="data-label">Current Bid</span>
                    <span class="data-value">$182/kg</span>
                </div>
                <div class="data-row">
                    <span class="data-label">Quantity</span>
                    <span class="data-value">90 kg <span class="timer">3:42</span></span>
                </div>
            </div>
            <div class="action-group">
                <button class="btn-pause"><i class="fas fa-pause"></i></button>
                <button class="btn-extend">Extend 5m</button>
                <button class="btn-stop"><i class="fas fa-times"></i></button>
            </div>
        </div>

        <div class="auction-card">
            <div class="img-container">
                <span class="live-label">â— LIVE</span>
                <img src="https://loremflickr.com/400/300/fish,sea?lock=4" alt="Fish">
            </div>
            <div class="card-info">
                <div class="lot-name">Lot #12 Yellowfin Tuna</div>
                <div class="market-label">Hanani Market</div>
                <div class="data-row">
                    <span class="data-label">Current Bid</span>
                    <span class="data-value">$210/kg</span>
                </div>
                <div class="data-row">
                    <span class="data-label">Quantity</span>
                    <span class="data-value">120 kg <span class="timer">22:31</span></span>
                </div>
            </div>
            <div class="action-group">
                <button class="btn-pause"><i class="fas fa-pause"></i></button>
                <button class="btn-extend">Extend 5m</button>
                <button class="btn-stop"><i class="fas fa-times"></i></button>
            </div>
        </div>

        <div class="auction-card">
            <div class="img-container">
                <span class="live-label">â— LIVE</span>
                <img src="https://loremflickr.com/400/300/fish,sea?lock=5" alt="Fish">
            </div>
            <div class="card-info"><div class="lot-name">Lot #22 Blue Marlin</div><div class="market-label">Oceanic Trade</div><div class="data-row"><span class="data-label">Current Bid</span><span class="data-value">$340/kg</span></div><div class="data-row"><span class="data-label">Quantity</span><span class="data-value">200 kg <span class="timer">15:40</span></span></div></div>
            <div class="action-group"><button class="btn-pause"><i class="fas fa-pause"></i></button><button class="btn-extend">Extend 5m</button><button class="btn-stop"><i class="fas fa-times"></i></button></div>
        </div>

        <div class="auction-card">
            <div class="img-container">
                <span class="live-label">â— LIVE</span>
                <img src="https://loremflickr.com/400/300/fish,sea?lock=6" alt="Fish">
            </div>
            <div class="card-info"><div class="lot-name">Lot #9 Red Snapper</div><div class="market-label">Global Catch</div><div class="data-row"><span class="data-label">Current Bid</span><span class="data-value">$120/kg</span></div><div class="data-row"><span class="data-label">Quantity</span><span class="data-value">45 kg <span class="timer">5:20</span></span></div></div>
            <div class="action-group"><button class="btn-pause"><i class="fas fa-pause"></i></button><button class="btn-extend">Extend 5m</button><button class="btn-stop"><i class="fas fa-times"></i></button></div>
        </div>

        <div class="auction-card">
            <div class="img-container">
                <span class="live-label">â— LIVE</span>
                <img src="https://loremflickr.com/400/300/fish,sea?lock=7" alt="Fish">
            </div>
            <div class="card-info"><div class="lot-name">Lot #30 Lobster</div><div class="market-label">Aqua Gold</div><div class="data-row"><span class="data-label">Current Bid</span><span class="data-value">$85/kg</span></div><div class="data-row"><span class="data-label">Quantity</span><span class="data-value">30 kg <span class="timer">10:05</span></span></div></div>
            <div class="action-group"><button class="btn-pause"><i class="fas fa-pause"></i></button><button class="btn-extend">Extend 5m</button><button class="btn-stop"><i class="fas fa-times"></i></button></div>
        </div>

        <div class="auction-card">
            <div class="img-container">
                <span class="live-label">â— LIVE</span>
                <img src="https://loremflickr.com/400/300/fish,sea?lock=8" alt="Fish">
            </div>
            <div class="card-info"><div class="lot-name">Lot #14 King Prawns</div><div class="market-label">Seafood Exp</div><div class="data-row"><span class="data-label">Current Bid</span><span class="data-value">$42/kg</span></div><div class="data-row"><span class="data-label">Quantity</span><span class="data-value">150 kg <span class="timer">18:12</span></span></div></div>
            <div class="action-group"><button class="btn-pause"><i class="fas fa-pause"></i></button><button class="btn-extend">Extend 5m</button><button class="btn-stop"><i class="fas fa-times"></i></button></div>
        </div>

        <div class="auction-card">
            <div class="img-container">
                <span class="live-label">â— LIVE</span>
                <img src="https://loremflickr.com/400/300/fish,sea?lock=9" alt="Fish">
            </div>
            <div class="card-info"><div class="lot-name">Lot #5 Atlantic Cod</div><div class="market-label">Cold Waters</div><div class="data-row"><span class="data-label">Current Bid</span><span class="data-value">$190/kg</span></div><div class="data-row"><span class="data-label">Quantity</span><span class="data-value">80 kg <span class="timer">2:50</span></span></div></div>
            <div class="action-group"><button class="btn-pause"><i class="fas fa-pause"></i></button><button class="btn-extend">Extend 5m</button><button class="btn-stop"><i class="fas fa-times"></i></button></div>
        </div>

        <div class="auction-card">
            <div class="img-container">
                <span class="live-label">â— LIVE</span>
                <img src="https://loremflickr.com/400/300/fish,sea?lock=10" alt="Fish">
            </div>
            <div class="card-info"><div class="lot-name">Lot #41 Sea Bass</div><div class="market-label">Premium Catch</div><div class="data-row"><span class="data-label">Current Bid</span><span class="data-value">$260/kg</span></div><div class="data-row"><span class="data-label">Quantity</span><span class="data-value">55 kg <span class="timer">12:15</span></span></div></div>
            <div class="action-group"><button class="btn-pause"><i class="fas fa-pause"></i></button><button class="btn-extend">Extend 5m</button><button class="btn-stop"><i class="fas fa-times"></i></button></div>
        </div>

        <div class="auction-card">
            <div class="img-container">
                <span class="live-label">â— LIVE</span>
                <img src="https://loremflickr.com/400/300/fish,sea?lock=11" alt="Fish">
            </div>
            <div class="card-info"><div class="lot-name">Lot #7 Mackerel</div><div class="market-label">Quick Trade</div><div class="data-row"><span class="data-label">Current Bid</span><span class="data-value">$38/kg</span></div><div class="data-row"><span class="data-label">Quantity</span><span class="data-value">300 kg <span class="timer">25:00</span></span></div></div>
            <div class="action-group"><button class="btn-pause"><i class="fas fa-pause"></i></button><button class="btn-extend">Extend 5m</button><button class="btn-stop"><i class="fas fa-times"></i></button></div>
        </div>

        <div class="auction-card">
            <div class="img-container">
                <span class="live-label">â— LIVE</span>
                <img src="https://loremflickr.com/400/300/fish,sea?lock=12" alt="Fish">
            </div>
            <div class="card-info"><div class="lot-name">Lot #19 Octopus</div><div class="market-label">Deep Sea Ltd</div><div class="data-row"><span class="data-label">Current Bid</span><span class="data-value">$410/kg</span></div><div class="data-row"><span class="data-label">Quantity</span><span class="data-value">12 kg <span class="timer text-danger">0:45</span></span></div></div>
            <div class="action-group"><button class="btn-pause"><i class="fas fa-pause"></i></button><button class="btn-extend">Extend 5m</button><button class="btn-stop"><i class="fas fa-times"></i></button></div>
        </div>

    </div>

        </div>
   

               


   



</div>

              
              
            </div>
         </div>
         <!-- /Page Wrapper -->

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" data-cfasync="false"></script>

    
      <!-- jQuery -->
      <!-- Bootstrap Core JS -->
      <!-- Feather Icon JS -->
      <!-- Slimscroll JS -->
      <!-- Theme Settings JS -->
      <!-- Custom JS -->

@include('bid_admin.admin.include.footer')


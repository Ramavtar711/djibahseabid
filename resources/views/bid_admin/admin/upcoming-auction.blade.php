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
         <h4 class="fw-bold mb-4">Upcoming Auctions </h4>
    
    <div class="auction-grid">
        
        <div class="auction-card">
            <div class="img-container">
                <span class="upcoming-label">â— SCHEDULED</span>
                <img src="https://loremflickr.com/400/300/fish,sea?lock=7" alt="Fish">
            </div>
            <div class="card-info">
                <div class="lot-name">Lot #30 Lobster</div>
                <div class="market-label">Hanani Market</div>
                <div class="data-row">
                    <span class="data-label">Starting Price</span>
                    <span class="data-value">$60/kg</span>
                </div>
                <div class="data-row">
                    <span class="data-label">Quantity</span>
                    <span class="data-value">250 kg </span>
                   
                </div>
                <div class="data-row">
                    <span class="data-label">Starts In</span>
                    <span class="data-value">01:45:12 </span>
                   
                </div>
            </div>
            <div class="action-group">
                <button class="btn-extend">Notify</button>
            </div>
        </div>
   
        <div class="auction-card">
            <div class="img-container">
                <span class="upcoming-label">â— SCHEDULED</span>
                <img src="https://images.unsplash.com/photo-1615141982883-c7ad0e69fd62?auto=format&fit=crop&w=400&q=80" alt="Fish">
            </div>
            <div class="card-info">
                <div class="lot-name">Lot #22 Red Sea Bream</div>
                <div class="market-label">Red Sea Port</div>
                <div class="data-row">
                   <span class="data-label">Starting Price</span>
                    <span class="data-value">$100/kg</span>
                </div>
                <div class="data-row">
                    <span class="data-label">Quantity</span>
                    <span class="data-value">250 kg </span>
                   
                </div>
                <div class="data-row">
                    <span class="data-label">Starts In</span>
                    <span class="data-value">01:45:12 </span>
                   
                </div>
            </div>
            <div class="action-group">
                
                <button class="btn-extend">Notify</button>
              
            </div>
        </div>

        <div class="auction-card">
            <div class="img-container">
                <span class="upcoming-label">â— SCHEDULED</span>
                <img src="https://loremflickr.com/400/300/fish,sea?lock=9" alt="Fish">
            </div>
            <div class="card-info">
                <div class="lot-name">Lot #12 Yellowfin Tuna</div>
                <div class="market-label">Hanani Market</div>
                <div class="data-row">
                   <span class="data-label">Starting Price</span>
                    <span class="data-value">$75/kg</span>
                </div>
                <div class="data-row">
                    <span class="data-label">Quantity</span>
                    <span class="data-value">250 kg </span>
                   
                </div>
                <div class="data-row">
                    <span class="data-label">Starts In</span>
                    <span class="data-value">01:45:12 </span>
                   
                </div>
            </div>
            <div class="action-group">
                <button class="btn-extend">Notify</button>
            </div>
        </div>

        <div class="auction-card">
            <div class="img-container">
                <span class="upcoming-label">â— SCHEDULED</span>
                <img src="https://loremflickr.com/400/300/fish,sea?lock=11" alt="Fish">
            </div>
            <div class="card-info">
                <div class="lot-name">Lot #12 Yellowfin Tuna</div>
                <div class="market-label">Hanani Market</div>
                <div class="data-row">
                    <span class="data-label">Starting Price</span>
                    <span class="data-value">$210/kg</span>
                </div>
                <div class="data-row">
                    <span class="data-label">Quantity</span>
                    <span class="data-value">250 kg </span>
                   
                </div>
                <div class="data-row">
                    <span class="data-label">Starts In</span>
                    <span class="data-value">01:45:12 </span>
                   
                </div>
            </div>
            <div class="action-group">
                <button class="btn-extend">Notify</button>
            </div>
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


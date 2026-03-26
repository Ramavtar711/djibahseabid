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
     .notification-card{
background: var(--surface-white);
padding:15px;
border-radius:12px;
margin-bottom:12px;
display:flex;
align-items:center;
gap:15px;
box-shadow:0 3px 12px rgba(0,0,0,0.05);
}

.notification-avatar{
width:45px;
height:45px;
border-radius:50%;
object-fit:cover;
}

.notification-content{
flex:1;
}

.notification-time{
font-size:12px;
color:gray;
}

.unread{
border-left:4px solid #0d6efd;
background: var(--surface-white);
}

   }

     </style>
     <div class="d-flex justify-content-between align-items-center mb-4">
<h4 class="fw-bold">Notifications</h4>
<button class="btn btn-sm btn-outline-primary">
<i class="bi bi-check2-all"></i> Mark All as Read
</button>
</div>
    

      
     <div class="notification-card unread">
         <img class="notification-avatar" src="https://i.pravatar.cc/100?img=5">
         <div class="notification-content">
            <div class="d-flex justify-content-between">
               <strong>New Auction Payment Received</strong> <span class="notification-time">2 min ago</span>
            </div>
            <p class="mb-1 text-muted">Oceanic Traders completed payment for Auction AUC1024.</p><span class="badge bg-success">Payment</span>
         </div>
      </div>
      <div class="notification-card unread">
         <img class="notification-avatar" src="https://i.pravatar.cc/100?img=8">
         <div class="notification-content">
            <div class="d-flex justify-content-between">
               <strong>Pending KYC Verification</strong> <span class="notification-time">15 min ago</span>
            </div>
            <p class="mb-1 text-muted">New seller "BlueWave Exports" requires KYC validation.</p><span class="badge bg-warning text-dark">KYC</span>
         </div>
      </div>
      <div class="notification-card">
         <img class="notification-avatar" src="https://i.pravatar.cc/100?img=12">
         <div class="notification-content">
            <div class="d-flex justify-content-between">
               <strong>Suspicious Bidding Activity</strong> <span class="notification-time">1 hour ago</span>
            </div>
            <p class="mb-1 text-muted">Multiple bids detected from the same IP for Auction AUC1030.</p><span class="badge bg-danger">Risk Alert</span>
         </div>
      </div>
      <div class="notification-card">
         <img class="notification-avatar" src="https://i.pravatar.cc/100?img=9">
         <div class="notification-content">
            <div class="d-flex justify-content-between">
               <strong>New Seller Registration</strong> <span class="notification-time">3 hours ago</span>
            </div>
            <p class="mb-1 text-muted">Pacific Export Ltd created a new seller account.</p><span class="badge bg-primary">Account</span>
         </div>
      </div>
      <div class="notification-card">
         <img class="notification-avatar" src="https://i.pravatar.cc/100?img=4">
         <div class="notification-content">
            <div class="d-flex justify-content-between">
               <strong>Bank Transfer Pending Validation</strong> <span class="notification-time">Yesterday</span>
            </div>
            <p class="mb-1 text-muted">Payment proof uploaded for Auction AUC1028.</p><span class="badge bg-info">Finance</span>
         </div>
      </div>


      </div>

              
              
            </div>
         </div>
         <!-- /Page Wrapper -->

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" data-cfasync="false"></script>

@include('bid_admin.admin.include.footer')

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
   <div class="sidebar-inner">
      <div id="sidebar-menu" class="sidebar-menu">
         <ul class="sidebar-vertical">
            <li>
               <a href="{{ route('qc.dashboard') }}"><i class="fe fe-home"></i> <span>Dashboard</span></a>
            </li>
 <li>
               <a href="{{ route('qc.lot-submitted') }}"><i class="bi bi-clipboard-check"></i> <span>Lot Submitted</span></a>
            </li>
              <li>
               <a href="{{ route('qc.notifications') }}">
                  <i class="bi bi-bell"></i>
                  <span>Notifications</span>
                  <span id="qcNotificationMenuBadge" class="badge bg-success ms-auto" style="display:none;"></span>
               </a>
            </li>

          <!--  <li>
               <a href="{{ route('qc.lot-submitted') }}"><i class="bi bi-clipboard-check"></i> <span>Lot Submitted</span></a>
            </li>
            <li>
               <a href="{{ route('qc.auction-setup') }}"><i class="bi bi-box"></i> <span>Auction Setup</span></a>
            </li>
            <li>
               <a href="{{ route('qc.auction-scheduled') }}"><i class="bi bi-calendar-event"></i> <span>Auction Scheduled</span></a>
            </li>
            <li>
               <a href="{{ route('qc.media-control') }}"><i class="bi bi-camera-video"></i> <span>Media Control</span></a>
            </li>
            <li>
               <a href="{{ route('qc.notifications') }}">
                  <i class="bi bi-bell"></i>
                  <span>Notifications</span>
                  <span id="qcNotificationMenuBadge" class="badge bg-success ms-auto" style="display:none;"></span>
               </a>
            </li>
            <li>
               <a href="{{ route('qc.permissions') }}"><i class="bi bi-shield-lock"></i> <span>Permissions</span></a>
            </li> -->
         </ul>
      </div>
   </div>
</div>
<!-- /Sidebar -->

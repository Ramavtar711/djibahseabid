<!-- Sidebar -->
         <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
               <div id="sidebar-menu" class="sidebar-menu">

                  <ul class="sidebar-vertical">
                     <li>
                        <a href="{{ route('seller.dashboard') }}" class="{{ request()->routeIs('seller.dashboard') ? 'active' : '' }}" ><i class="fe fe-home"></i> <span> Dashboard</span> </a>

                     </li>
                     <li>
                        <a href="{{ route('seller.create-lot') }}" class="{{ request()->routeIs('seller.create-lot') ? 'active' : '' }}"><i class="bi bi-plus"></i> <span> Crete New Lot</span> </a>

                     </li>
                     <li>
                        <a href="{{ route('seller.lot-list') }}" class="{{ request()->routeIs('seller.lot-list','seller.lot-details','seller.live-view') ? 'active' : '' }}"><i class="bi bi-box"></i> <span> My Lot list</span> </a>

                     </li>

                     <li>
                        <a href="{{ route('seller.active-auction') }}" class="{{ request()->routeIs('seller.active-auction') ? 'active' : '' }}"><i class="bi bi-hammer"></i> <span> Active Auction</span> </a>

                     </li>

                     <li>
                        <a href="{{ route('seller.pending-validation') }}" class="{{ request()->routeIs('seller.pending-validation') ? 'active' : '' }}"><i class="bi bi-hourglass-split"></i> <span> Lot Pending Validation</span> </a>

                     </li>

                     <li>
                        <a href="{{ route('seller.approve-lots') }}" class="{{ request()->routeIs('seller.approve-lots') ? 'active' : '' }}"><i class="bi bi-patch-check"></i> <span> Approved Lots</span> </a>

                     </li>
                     <li>
                        <a href="{{ route('seller.sold-lots') }}" class="{{ request()->routeIs('seller.sold-lots') ? 'active' : '' }}"><i class="bi bi-check-circle"></i> <span> Sold Lots</span> </a>

                     </li>
                    
 
                     <li>
                        <a href="{{ route('seller.revenue') }}" class="{{ request()->routeIs('seller.revenue') ? 'active' : '' }}"><i class="bi bi-cash-coin"></i> <span> Revenue </span> </a>

                     </li>
                     <li>
                        <a href="{{ route('seller.notifications') }}" class="{{ request()->routeIs('seller.notifications') ? 'active' : '' }}">
                           <i class="bi bi-bell"></i>
                           <span> Notifications</span>
                           <span id="sellerNotificationMenuBadge" class="badge bg-success ms-auto" style="display:none;"></span>
                        </a>
                     </li>
                     <li>
                        <a href="#"><i class="bi bi-gear"></i> <span> Setting</span> </a>

                     </li>

                     
                     </ul>

                     
               </div>
            </div>
         </div>
         <!-- /Sidebar -->


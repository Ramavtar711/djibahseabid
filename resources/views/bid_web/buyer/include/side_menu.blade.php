<!-- Sidebar -->
         <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
               <div id="sidebar-menu" class="sidebar-menu">

                  <ul class="sidebar-vertical">
                     <li>
                        <a href="{{ route('buyer.dashboard') }}" class="{{ request()->routeIs('buyer.dashboard') ? 'active' : '' }}"><i class="fe fe-home"></i> <span> Dashboard</span> </a>
                     </li>
                     <li>
                        <a href="{{ route('buyer.active-auction') }}" class="{{ request()->routeIs('buyer.active-auction') ? 'active' : '' }}"><i class="bi bi-broadcast"></i> <span> Active Auction</span> </a>
                     </li>
                     <li>
                        <a href="{{ route('buyer.upcoming-auction') }}" class="{{ request()->routeIs('buyer.upcoming-auction') ? 'active' : '' }}"><i class="bi bi-hourglass-bottom"></i> <span> Upcoming Auction</span> </a>
                     </li>
                  <!--   <li>
                        <a href="{{ route('buyer.live-auction') }}" class="{{ request()->routeIs('buyer.live-auction') ? 'active' : '' }}"><i class="bi bi-play-circle"></i> <span> Live Auction</span> </a>
                     </li>
                     -->
                       <li>
                        <a href="{{ route('buyer.won-auction') }}" class="{{ request()->routeIs('buyer.won-auction') ? 'active' : '' }}"><i class="bi bi-check-lg"></i> <span> Won Auction</span> </a>
                     </li>
                     <li>
                        <a href="{{ route('buyer.transactions') }}" class="{{ request()->routeIs('buyer.transactions') ? 'active' : '' }}"><i class="bi bi-cash-coin"></i> <span> My Payments</span> </a>
                     </li>
                     <li>
                        <a href="{{ route('buyer.profile-settings') }}" class="{{ request()->routeIs('buyer.profile-settings*') ? 'active' : '' }}"><i class="bi bi-person-gear"></i> <span> Profile Settings</span> </a>
                     </li>
                     
                     
                  
                  </ul>

               </div>
            </div>
         </div>
         <!-- /Sidebar -->

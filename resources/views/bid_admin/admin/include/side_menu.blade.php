
         <!-- Sidebar -->
         <div class="sidebar" id="sidebar">
            <div class="sidebar-inner">
               <div id="sidebar-menu" class="sidebar-menu">

                  <ul class="sidebar-vertical">
                     <li>
                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fe fe-home"></i> <span> Dashboard</span> </a>

                     </li>
                     <!-- Main -->
                     <li class="menu-title"><span>AUCTIONS</span></li>
                     
                     <li>
                        <a href="{{ route('admin.live-auction') }}" class="{{ request()->routeIs('admin.live-auction') ? 'active' : '' }}"><i class="bi bi-broadcast"></i> <span> Live</span> </a>

                     </li>

                     <li>
                        <a href="{{ route('admin.upcoming-auction') }}" class="{{ request()->routeIs('admin.upcoming-auction') ? 'active' : '' }}"><i class="bi bi-hourglass-bottom"></i> <span> Upcoming</span> </a>

                     </li>
                     <li>
                        <a href="{{ route('admin.lot-management') }}" class="{{ request()->routeIs('admin.lot-management', 'admin.create-lot', 'admin.lot-details') ? 'active' : '' }}"><i class="bi bi-box"></i> <span> Lots Managements</span> </a>

                     </li>
                     <li class="menu-title"><span>USERS</span></li>

                     <li>
                        <a href="{{ route('admin.buyers') }}" class="{{ request()->routeIs('admin.buyers', 'admin.buyer-details', 'admin.add-buyer') ? 'active' : '' }}"><i class="bi bi-person"></i> <span> Buyers</span> </a>

                     </li>
                     <li>
                        <a href="{{ route('admin.sellers') }}" class="{{ request()->routeIs('admin.sellers', 'admin.seller-details', 'admin.add-seller') ? 'active' : '' }}"><i class="bi bi-person"></i> <span> Sellers</span> </a>

                     </li>

                     <li class="menu-title"><span>FINANCIAL CONTROL</span></li>
                     <li>
                        <a href="{{ route('admin.finance-overview') }}" class="{{ request()->routeIs('admin.finance-overview') ? 'active' : '' }}"><i class="bi bi-bar-chart"></i> <span> Finance Overview</span> </a>

                     </li>
                     <li>
                        <a href="{{ route('admin.transactions') }}" class="{{ request()->routeIs('admin.transactions') ? 'active' : '' }}"><i class="bi bi-cash-coin"></i> <span> Transactions</span> </a>

                     </li>
                     <li>
                        <a href="{{ route('admin.bank-transfer') }}" class="{{ request()->routeIs('admin.bank-transfer') ? 'active' : '' }}"><i class="bi bi-cash"></i> <span> Bank Transfer</span> </a>

                     </li>
                     <li class="menu-title"><span>DISPUTE</span></li>
                     <li>
                        <a href="{{ route('admin.risk-monitoring') }}" class="{{ request()->routeIs('admin.risk-monitoring') ? 'active' : '' }}"><i class="bi bi-exclamation-triangle"></i> <span> Risk Monitoring </span> </a>

                     </li>

                     <li class="menu-title"><span>OTHERS</span></li>
                     <li>
                        <a href="{{ route('admin.alets') }}" class="{{ request()->routeIs('admin.alets') ? 'active' : '' }}"><i class="bi bi-bell"></i> <span> Alerts</span> </a>

                     </li>
                     
                     <li>
                        <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings', 'admin.account-settings') ? 'active' : '' }}"><i class="fe fe-settings"></i> <span> Settings</span> </a>

                     </li>
                  </ul>
               </div>
            </div>
         </div>
         <!-- /Sidebar -->

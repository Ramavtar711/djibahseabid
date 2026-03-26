<!DOCTYPE html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="light"  data-sidebar-size="sm" data-sidebar-image="none">
   <head>
            <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Auction</title>
      <!-- Favicon -->
      <link rel="shortcut icon" href="{{ asset('buyer/assets/img/favicon.png') }}">
      <!-- Bootstrap CSS -->
      <link rel="stylesheet" href="{{ asset('buyer/assets/css/bootstrap.min.css') }}">
      <!-- Fontawesome CSS -->
      <link rel="stylesheet" href="{{ asset('buyer/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
      <link rel="stylesheet" href="{{ asset('buyer/assets/plugins/fontawesome/css/all.min.css') }}">
      <!-- Feather CSS -->
      <link rel="stylesheet" href="{{ asset('buyer/assets/plugins/feather/feather.css') }}">
      <!-- Datepicker CSS -->
      <link rel="stylesheet" href="{{ asset('buyer/assets/css/bootstrap-datetimepicker.min.css') }}">
      <!-- Datatables CSS -->
      <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
      <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.dataTables.css">

      <!-- Main CSS -->
      <link rel="stylesheet" href="{{ asset('buyer/assets/css/style.css') }}">
      <!-- Layout JS -->
      <script src="{{ asset('buyer/assets/js/layout.js') }}" type="text/javascript"></script>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
   </head>
   <body class="mini-sidebar">
      <!-- Main Wrapper -->
      <div class="main-wrapper">
         <!-- Header -->
         <div class="header header-one">
            <a href="{{ route('buyer.dashboard') }}"  class="d-inline-flex d-sm-inline-flex align-items-center d-md-inline-flex d-lg-none align-items-center device-logo">
            <img src="{{ asset('buyer/assets/img/logo-small.png') }}" class="img-fluid logo2" alt="Logo" style="width:50px">
            </a>
            <div class="main-logo d-inline float-start d-lg-flex align-items-center d-none d-sm-none d-md-none">
               <div class="logo-color">
                  <a href="{{ route('buyer.dashboard') }}">
                  <div class="d-flex gap-2 align-items-center">
                     <img src="{{ asset('buyer/assets/img/logo-small.png') }}" class="img-fluid logo-blue" alt="Logo" style="width:50px !important"> 
                     
                  </div>
                  </a>
                  <a href="{{ route('buyer.dashboard') }}">
                  <img src="{{ asset('buyer/assets/img/logo-small.png') }}" class="img-fluid logo-small" alt="Logo">
                  </a>
               </div>
            </div>
            <!-- Sidebar Toggle -->
            <a href="javascript:void(0);" id="toggle_btn">
            <span class="toggle-bars">
            <span class="bar-icons"></span>
            <span class="bar-icons"></span>
            <span class="bar-icons"></span>
            <span class="bar-icons"></span>
            </span>
            </a>
            <!-- /Sidebar Toggle -->
            <!-- Search -->
            <div class="top-nav-search">
               <div class="d-flex align-items-center"> 
                  <img src="{{ asset('buyer/assets/img/logo-small.png') }}" class="img-fluid" alt="Ã‰choTerra Logo" style="max-width: 50px;">
                            <h4 class="logo-text">Djibah SeaBid</h4>
                           </div>
            </div>
            <!-- /Search -->
            <!-- Mobile Menu Toggle -->
            <a class="mobile_btn" id="mobile_btn">
            <i class="fas fa-bars"></i>
            </a>
            <!-- /Mobile Menu Toggle -->
            <!-- Header Menu -->
            <ul class="nav nav-tabs user-menu">
              
              
               <div class="d-flex align-items-center">   

                    

                     <!-- Language Dropdown -->
                     <div class="nav-item dropdown has-arrow flag-nav me-2">
                        <a class="btn btn-menubar" data-bs-toggle="dropdown" href="javascript:void(0);" role="button" aria-expanded="false">
                           <img src="{{ asset('buyer/assets/img/flags/us.svg') }}" alt="Language" class="img-fluid">
                        </a>
                        <ul class="dropdown-menu p-2" style="">

                           <!-- item-->
                           <li>
                              <a href="javascript:void(0);" class="dropdown-item">
                                 <img src="{{ asset('buyer/assets/img/flags/us.svg') }}" alt="flag" class="me-2">English
                              </a>
                           </li>

                           

                           <!-- item-->
                           <li>
                              <a href="javascript:void(0);" class="dropdown-item">
                                 <img src="{{ asset('buyer/assets/img/flags/fr.svg') }}" alt="flag" class="me-2">French
                              </a>
                           </li>

                           <!-- item-->
                           <li>
                              <a href="javascript:void(0);" class="dropdown-item">
                                 <img src="{{ asset('buyer/assets/img/flags/ae.svg') }}" alt="flag" class="me-2">Arabic
                              </a>
                           </li>

                        </ul>
                     </div>

                     <!-- Notification -->
                     <div class="notification_item me-3">
                        <a href="#" class="btn btn-menubar position-relative" id="notification_popup" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                           <i class="bi bi-bell"></i>
                           <span id="buyerUnreadBadge" class="position-absolute badge bg-success border border-white" style="display:none;"></span>
                        </a>
                        <div class="dropdown-menu p-0 dropdown-menu-end dropdown-menu-lg" style="min-height: 300px;">
                     
                           <div class="p-2 border-bottom">
                              <div class="row align-items-center">
                                 <div class="col">
                                    <h6 class="m-0 fs-16 fw-semibold"> Notifications</h6>
                                 </div>
                                 <div class="col-auto">
                                    <div class="dropdown">
                                       <a href="#" class="dropdown-toggle drop-arrow-none link-dark" data-bs-toggle="dropdown" data-bs-offset="0,15" aria-expanded="false">
                                          <i class="isax isax-setting-2 fs-16 text-body align-middle"></i>
                                       </a>
                                       <div class="dropdown-menu dropdown-menu-end">
                                          <!-- item-->
                                          <a href="javascript:void(0);" id="buyerMarkAllRead" class="dropdown-item"><i class="ti ti-bell-check me-1"></i>Mark as Read</a>
                                          <!-- item-->
                                          <a href="javascript:void(0);" class="dropdown-item"><i class="ti ti-trash me-1"></i>Delete All</a>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           
                           <!-- Notification Dropdown -->
                           <div class="notification-body position-relative z-2 rounded-0 simplebar-scrollable-y" data-simplebar="init">
                              <div class="simplebar-wrapper" style="margin: 0px;">
                                 <div class="simplebar-height-auto-observer-wrapper"><div class="simplebar-height-auto-observer"></div></div>
                                 <div class="simplebar-mask">
                                    <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                       <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content" style="height: auto; overflow: hidden scroll;">
                                          <div class="simplebar-content" style="padding: 0px;">
                                             <div id="buyerNotificationList" class="buyer-notify-list"></div>
                                             <div id="buyerNotificationEmpty" class="p-3 text-center text-white-50">No notifications yet.</div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="simplebar-placeholder" style="width: 320px; height: 412px;"></div>
                              </div>
                              <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;"><div class="simplebar-scrollbar" style="width: 0px; display: none;"></div></div>
                              <div class="simplebar-track simplebar-vertical" style="visibility: visible;"><div class="simplebar-scrollbar" style="height: 190px; transform: translate3d(0px, 0px, 0px); display: block;"></div></div>
                           </div>
                           
                           <!-- View All-->
                           <div class="p-2 rounded-bottom border-top text-center">
                              <a href="javascript:void(0);" class="text-center fw-medium fs-14 mb-0">
                                 View All
                              </a>
                           </div>
                           
                        </div>
                     </div>

                    

                     <!-- User Dropdown -->
                     @php
                        $loggedBuyer = session('logged_user', []);
                        $buyerName = $loggedBuyer['name'] ?? 'Buyer';
                        $buyerEmail = $loggedBuyer['email'] ?? '';
                        $buyerProfileImage = ! empty($loggedBuyer['profile_image']) ? asset('storage/' . $loggedBuyer['profile_image']) : null;
                        $buyerInitials = collect(explode(' ', trim($buyerName)))
                           ->filter()
                           ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                           ->take(2)
                           ->implode('');
                     @endphp
                     <div class="dropdown profile-dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                           <span class="avatar online">
                              @if ($buyerProfileImage)
                                 <img src="{{ $buyerProfileImage }}" alt="Profile Image" class="img-fluid rounded-circle" style="width:40px;height:40px;object-fit:cover;">
                              @else
                                 <span class="img-fluid rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold" style="width:40px;height:40px;background:linear-gradient(135deg,#22c1c3,#2563eb);">
                                    {{ $buyerInitials ?: 'BY' }}
                                 </span>
                              @endif
                           </span>
                        </a>
                        <div class="dropdown-menu p-2">
                           <div class="d-flex align-items-center bg-light rounded-1 p-2 mb-2">
                              <span class="avatar avatar-lg me-2">
                                 @if ($buyerProfileImage)
                                    <img src="{{ $buyerProfileImage }}" alt="Profile Image" class="rounded-circle" style="width:48px;height:48px;object-fit:cover;">
                                 @else
                                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold" style="width:48px;height:48px;background:linear-gradient(135deg,#22c1c3,#2563eb);">
                                       {{ $buyerInitials ?: 'BY' }}
                                    </span>
                                 @endif
                              </span>
                              <div>
                                 <h6 class="fs-12 fw-medium mb-1">{{ $buyerName }}</h6>
                                 <p class="fs-10 mb-0">Buyer</p>
                                 @if ($buyerEmail)
                                    <p class="fs-10 text-muted mb-0">{{ $buyerEmail }}</p>
                                 @endif
                              </div>
                           </div>

                           <!-- Item-->
                           <a class="dropdown-item d-flex align-items-center" href="{{ route('buyer.profile-settings') }}">
                              <i class="isax isax-profile-circle me-2"></i>Profile Settings
                           </a>



                           <hr class="dropdown-divider my-2">

                           <!-- Item-->
                           <a class="dropdown-item logout d-flex align-items-center" href="{{ route('home.login') }}">
                              <i class="isax isax-logout me-2"></i>Sign Out
                           </a>
                        </div>
                     </div>

                  </div>
            </ul>
            <!-- /Header Menu -->
         </div>
         <!-- /Header -->
         
<style>
   .buyer-notify-list .notification-item {
      padding: 12px 14px;
   }
   .buyer-notify-list .notification-item:hover {
      background: rgba(13, 110, 253, 0.06);
   }
   .buyer-notify-icon {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
      color: #fff;
      flex-shrink: 0;
   }
   .buyer-notify-icon.info { background: #0dcaf0; }
   .buyer-notify-icon.success { background: #22c55e; }
   .buyer-notify-icon.warning { background: #f59e0b; }
   .buyer-notify-icon.danger { background: #ef4444; }
   .buyer-notify-title { font-weight: 700; font-size: 0.95rem; color: #0f172a; }
   .buyer-notify-msg { font-size: 0.85rem; color: #334155; }
   .buyer-notify-time { font-size: 0.75rem; color: #64748b; }
   .buyer-notify-read {
      width: 8px; height: 8px; border-radius: 50%; background: #0dcaf0; display: inline-block;
   }
</style>

<script>
   (function () {
      var popup = document.getElementById('notification_popup');
      var list = document.getElementById('buyerNotificationList');
      var empty = document.getElementById('buyerNotificationEmpty');
      var badge = document.getElementById('buyerUnreadBadge');
      var markAll = document.getElementById('buyerMarkAllRead');

      if (!popup || !list || !badge || !empty) return;

      var dataUrl = "{{ route('buyer.notifications.data') }}";
      var markReadUrl = "{{ route('buyer.notifications.mark-read') }}";
      var markAllUrl = "{{ route('buyer.notifications.mark-all-read') }}";

      function setBadge(count) {
         if (!badge) return;
         if (count > 0) {
            badge.style.display = 'inline-block';
            badge.textContent = count > 99 ? '99+' : count;
         } else {
            badge.style.display = 'none';
            badge.textContent = '';
         }
      }

      function escapeHtml(value) {
         var div = document.createElement('div');
         div.textContent = value ?? '';
         return div.innerHTML;
      }

      function renderItems(items) {
         list.innerHTML = '';
         if (!items || !items.length) {
            empty.style.display = 'block';
            return;
         }
         empty.style.display = 'none';

         items.forEach(function (item) {
            var title = escapeHtml(item.title || 'Notification');
            var message = escapeHtml(item.message || '');
            var time = escapeHtml(item.time || '');
            var id = item.id;
            var url = item.url ? escapeHtml(item.url) : '';
            var type = (item.type || 'info').toLowerCase();
            var iconMap = {
               success: 'bi-check-circle',
               warning: 'bi-exclamation-triangle',
               danger: 'bi-x-circle',
               info: 'bi-info-circle'
            };
            var icon = iconMap[type] || 'bi-info-circle';

            var row = document.createElement('div');
            row.className = 'dropdown-item notification-item py-2 text-wrap border-bottom';
            row.dataset.id = id;

            row.innerHTML =
               '<div class="d-flex gap-2">' +
                  '<div class="buyer-notify-icon ' + type + '"><i class="bi ' + icon + '"></i></div>' +
                  '<div class="flex-grow-1">' +
                     '<div class="buyer-notify-title">' + title + '</div>' +
                     '<div class="buyer-notify-msg">' + message + '</div>' +
                     '<div class="d-flex justify-content-between align-items-center mt-1">' +
                        '<span class="buyer-notify-time"><i class="isax isax-clock me-1"></i>' + time + '</span>' +
                        '<div class="notification-action d-flex align-items-center float-end gap-2">' +
                           (item.is_read ? '' : '<span class="buyer-notify-read"></span>') +
                           '<a href="javascript:void(0);" class="notification-read rounded-circle bg-info" data-id="' + id + '" title="Mark as read"></a>' +
                        '</div>' +
                     '</div>' +
                  '</div>' +
               '</div>';

            if (url) {
               row.style.cursor = 'pointer';
               row.addEventListener('click', function (e) {
                  if (e.target && e.target.classList.contains('notification-read')) return;
                  window.location.href = url;
               });
            }

            list.appendChild(row);
         });
      }

      function fetchNotifications() {
         fetch(dataUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (res) { return res.json(); })
            .then(function (data) {
               setBadge(data.unread_count || 0);
               renderItems(data.items || []);
            })
            .catch(function () {});
      }

      list.addEventListener('click', function (e) {
         var target = e.target;
         if (!target || !target.classList.contains('notification-read')) return;
         var id = target.getAttribute('data-id');
         if (!id) return;
         fetch(markReadUrl, {
            method: 'POST',
            headers: {
               'Content-Type': 'application/json',
               'X-Requested-With': 'XMLHttpRequest',
               'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ id: id })
         }).then(function () {
            fetchNotifications();
         });
      });

      if (markAll) {
         markAll.addEventListener('click', function () {
            fetch(markAllUrl, {
               method: 'POST',
               headers: {
                  'X-Requested-With': 'XMLHttpRequest',
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
               }
            }).then(function () {
               fetchNotifications();
            });
         });
      }

      popup.addEventListener('click', function () {
         fetchNotifications();
      });

      fetchNotifications();
   })();
</script>

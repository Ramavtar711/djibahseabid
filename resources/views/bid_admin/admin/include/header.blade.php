<!DOCTYPE html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="light" data-sidebar-size="sm" data-sidebar-image="none">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <title>Auction</title>
      <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
      <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
      <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
      <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
      <link rel="stylesheet" href="{{ asset('assets/plugins/feather/feather.css') }}">
      <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}">
      <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
      <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.dataTables.css">
@php
   $adminSession = session('admin_user', []);
   $adminName = $adminSession['name'] ?? 'Administrator';
   $adminEmail = $adminSession['email'] ?? '';
   $adminRole = ucfirst($adminSession['role'] ?? 'admin');
   $adminInitials = collect(explode(' ', trim($adminName)))
      ->filter()
      ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
      ->take(2)
      ->implode('');
@endphp
      <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
      <script src="{{ asset('assets/js/layout.js') }}" type="text/javascript"></script>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
      <style>
         .admin-responsive-table {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
         }

         .admin-responsive-table > table {
            min-width: 720px;
         }

         @media (max-width: 1199.98px) {
            .top-nav-search .logo-text {
               font-size: 28px;
            }

            .header .user-menu {
               margin-left: auto;
            }
         }

         @media (max-width: 991.98px) {
            .top-nav-search {
               display: none;
            }

            .page-wrapper {
               margin-left: 0 !important;
               padding-top: 70px;
            }

            .page-wrapper .content {
               padding: 18px 14px 22px;
            }

            .header .user-menu {
               padding-right: 6px;
            }

            .header .user-menu .btn-menubar {
               min-width: 38px;
               min-height: 38px;
               padding: 8px;
            }

            .dropdown-menu-lg {
               width: min(360px, calc(100vw - 24px));
            }

            .status-header {
               gap: 12px !important;
               padding: 14px 16px !important;
               border-radius: 14px !important;
            }

            .status-header .d-flex.gap-4,
            .status-header .d-flex.gap-3 {
               gap: 12px !important;
            }

            .status-header .fw-bold {
               width: 100%;
               text-align: left;
            }

            .alerts-shell,
            .settings-card,
            .card-soft,
            .glass-card,
            .page-card {
               border-radius: 18px !important;
            }
         }

         @media (max-width: 767.98px) {
            body {
               font-size: 14px;
            }

            .page-wrapper .content {
               padding: 14px 10px 18px;
            }

            .header {
               padding: 0 10px;
            }

            .header .flag-nav,
            .header .notification_item {
               margin-right: 8px !important;
            }

            .header .profile-dropdown .dropdown-menu,
            .header .notification_item .dropdown-menu {
               position: fixed !important;
               top: 64px !important;
               left: 12px !important;
               right: 12px !important;
               width: auto !important;
               transform: none !important;
            }

            .content.container-fluid > .row,
            .content .row {
               --bs-gutter-x: 14px;
            }

            .status-pill {
               font-size: 12px !important;
            }

            .page-heading,
            .alerts-title,
            .page-title {
               font-size: 24px !important;
               line-height: 1.2;
            }

            .btn,
            .form-control,
            .form-select {
               min-height: 44px;
            }

            .settings-nav {
               gap: 8px !important;
            }

            .settings-tab-btn {
               padding: 11px 14px !important;
               font-size: 14px;
            }

            .settings-card .card-body,
            .card-soft,
            .alerts-shell {
               padding: 16px !important;
            }
         }
      </style>
   </head>
   <body class="mini-sidebar">
      <div class="main-wrapper">
         <div class="header header-one">
            <a href="{{ route('admin.dashboard') }}" class="d-inline-flex d-sm-inline-flex align-items-center d-md-inline-flex d-lg-none align-items-center device-logo">
               <img src="{{ asset('assets/img/logo-small.png') }}" class="img-fluid logo2" alt="Logo" style="width:50px">
            </a>
            <div class="main-logo d-inline float-start d-lg-flex align-items-center d-none d-sm-none d-md-none">
               <div class="logo-color">
                  <a href="{{ route('admin.dashboard') }}">
                     <div class="d-flex gap-2 align-items-center">
                        <img src="{{ asset('assets/img/logo-small.png') }}" class="img-fluid logo-blue" alt="Logo" style="width:50px !important">
                     </div>
                  </a>
                  <a href="{{ route('admin.dashboard') }}">
                     <img src="{{ asset('assets/img/logo-small.png') }}" class="img-fluid logo-small" alt="Logo">
                  </a>
               </div>
            </div>
            <a href="javascript:void(0);" id="toggle_btn">
               <span class="toggle-bars">
                  <span class="bar-icons"></span>
                  <span class="bar-icons"></span>
                  <span class="bar-icons"></span>
                  <span class="bar-icons"></span>
               </span>
            </a>
            <div class="top-nav-search">
               <div class="d-flex align-items-center">
                  <img src="{{ asset('assets/img/logo-small.png') }}" class="img-fluid" alt="Djibah SeaBid Logo" style="max-width: 50px;">
                  <h4 class="logo-text">Djibah SeaBid</h4>
               </div>
            </div>
            <a class="mobile_btn" id="mobile_btn">
               <i class="fas fa-bars"></i>
            </a>
            <ul class="nav nav-tabs user-menu">
               <div class="d-flex align-items-center">
                  <div class="nav-item dropdown has-arrow flag-nav me-2">
                     <a class="btn btn-menubar" data-bs-toggle="dropdown" href="javascript:void(0);" role="button" aria-expanded="false">
                        <img src="{{ asset('assets/img/flags/us.svg') }}" alt="Language" class="img-fluid">
                     </a>
                     <ul class="dropdown-menu p-2">
                        <li><a href="javascript:void(0);" class="dropdown-item"><img src="{{ asset('assets/img/flags/us.svg') }}" alt="flag" class="me-2">English</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item"><img src="{{ asset('assets/img/flags/fr.svg') }}" alt="flag" class="me-2">French</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item"><img src="{{ asset('assets/img/flags/ae.svg') }}" alt="flag" class="me-2">Arabic</a></li>
                     </ul>
                  </div>

                  <div class="notification_item me-3">
                     <a href="#"
                        class="btn btn-menubar position-relative"
                        id="notification_popup"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="outside"
                        data-notifications-url="{{ route('admin.notifications.data') }}"
                        data-notifications-mark-read="{{ route('admin.notifications.mark-read') }}"
                        data-notifications-mark-all="{{ route('admin.notifications.mark-all-read') }}"
                        aria-expanded="false">
                        <i class="bi bi-bell"></i>
                        <span id="adminNotificationBadge" class="position-absolute badge bg-success border border-white" style="display:none;"></span>
                     </a>
                     <div class="dropdown-menu p-0 dropdown-menu-end dropdown-menu-lg" style="min-height: 300px;">
                        <div class="p-2 border-bottom">
                           <div class="row align-items-center">
                              <div class="col">
                                 <h6 class="m-0 fs-16 fw-semibold">Notifications</h6>
                              </div>
                              <div class="col-auto">
                                 <div class="dropdown">
                                    <a href="#" class="dropdown-toggle drop-arrow-none link-dark" data-bs-toggle="dropdown" data-bs-offset="0,15" aria-expanded="false">
                                       <i class="isax isax-setting-2 fs-16 text-body align-middle"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                       <a href="javascript:void(0);" class="dropdown-item" id="adminNotificationMarkAll"><i class="ti ti-bell-check me-1"></i>Mark as Read</a>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>

                        <div class="notification-body position-relative z-2 rounded-0 simplebar-scrollable-y" data-simplebar="init">
                           <div class="simplebar-wrapper" style="margin: 0px;">
                              <div class="simplebar-height-auto-observer-wrapper"><div class="simplebar-height-auto-observer"></div></div>
                              <div class="simplebar-mask">
                                 <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                    <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content" style="height: auto; overflow: hidden scroll;">
                                       <div class="simplebar-content" style="padding: 0px;">
                                          <div id="adminNotificationList">
                                             <div class="text-center text-muted py-4">No notifications yet.</div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="simplebar-placeholder" style="width: 320px; height: 412px;"></div>
                           </div>
                           <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;"><div class="simplebar-scrollbar" style="width: 0px; display: none;"></div></div>
                           <div class="simplebar-track simplebar-vertical" style="visibility: visible;"><div class="simplebar-scrollbar" style="height: 190px; transform: translate3d(0px, 0px, 0px); display: block;"></div></div>
                        </div>

                        <div class="p-2 rounded-bottom border-top text-center">
                           <a href="{{ route('admin.notifications') }}" class="text-center fw-medium fs-14 mb-0">
                              View All
                           </a>
                        </div>
                     </div>
                  </div>

                  <div class="dropdown profile-dropdown">
                     <a href="javascript:void(0);" class="dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        <span class="avatar online">
                           <span class="img-fluid rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold" style="width:40px;height:40px;background:linear-gradient(135deg,#0f766e,#2563eb);">
                              {{ $adminInitials ?: 'AD' }}
                           </span>
                        </span>
                     </a>
                     <div class="dropdown-menu p-2">
                        <div class="d-flex align-items-center bg-light rounded-1 p-2 mb-2">
                           <span class="avatar avatar-lg me-2">
                              <span class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold" style="width:48px;height:48px;background:linear-gradient(135deg,#0f766e,#2563eb);">
                                 {{ $adminInitials ?: 'AD' }}
                              </span>
                           </span>
                           <div>
                              <h6 class="fs-12 fw-medium mb-1">{{ $adminName }}</h6>
                              <p class="fs-10 mb-0">{{ $adminRole }}</p>
                              @if ($adminEmail)
                                 <p class="fs-10 text-muted mb-0">{{ $adminEmail }}</p>
                              @endif
                           </div>
                        </div>

                        <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.account-settings') }}">
                           <i class="isax isax-profile-circle me-2"></i>Profile Settings
                        </a>

                        <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.change-password') }}">
                           <i class="isax isax-lock me-2"></i>Update Password
                        </a>

                        <hr class="dropdown-divider my-2">

                        <a class="dropdown-item logout d-flex align-items-center" href="{{ route('admin.login') }}">
                           <i class="isax isax-logout me-2"></i>Sign Out
                        </a>
                     </div>
                  </div>
               </div>
            </ul>
         </div>

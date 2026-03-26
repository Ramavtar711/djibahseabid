<!DOCTYPE html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="light"  data-sidebar-size="sm" data-sidebar-image="none">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <title>Auction</title>
      <!-- Favicon -->
      <link rel="shortcut icon" href="{{ url('public/qc/assets/img/favicon.png') }}">
      <!-- Bootstrap CSS -->
      <link rel="stylesheet" href="{{ url('public/qc/assets/css/bootstrap.min.css') }}">
      <!-- Fontawesome CSS -->
      <link rel="stylesheet" href="{{ url('public/qc/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
      <link rel="stylesheet" href="{{ url('public/qc/assets/plugins/fontawesome/css/all.min.css') }}">
      <!-- Feather CSS -->
      <link rel="stylesheet" href="{{ url('public/qc/assets/plugins/feather/feather.css') }}">
      <!-- Datepicker CSS -->
      <link rel="stylesheet" href="{{ url('public/qc/assets/css/bootstrap-datetimepicker.min.css') }}">
      <!-- Datatables CSS -->
      <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css">
      <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.dataTables.css">

      <!-- Main CSS -->
      <link rel="stylesheet" href="{{ url('public/qc/assets/css/style.css') }}">
      <!-- Layout JS -->
      <script src="{{ url('public/qc/assets/js/layout.js') }}" type="text/javascript"></script>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
   </head>
   <body class="mini-sidebar">
      <!-- Main Wrapper -->
      <div class="main-wrapper">
         <!-- Header -->
         <div class="header header-one">
            <a href="{{ route('qc.dashboard') }}"  class="d-inline-flex d-sm-inline-flex align-items-center d-md-inline-flex d-lg-none align-items-center device-logo">
            <img src="{{ url('public/qc/assets/img/logo-small.png') }}" class="img-fluid logo2" alt="Logo" style="width:50px">
            </a>
            <div class="main-logo d-inline float-start d-lg-flex align-items-center d-none d-sm-none d-md-none">
               <div class="logo-color">
                  <a href="{{ route('qc.dashboard') }}">
                  <div class="d-flex gap-2 align-items-center">
                     <img src="{{ url('public/qc/assets/img/logo-small.png') }}" class="img-fluid logo-blue" alt="Logo" style="width:50px !important"> 
                     
                  </div>
                  </a>
                  <a href="{{ route('qc.dashboard') }}">
                  <img src="{{ url('public/qc/assets/img/logo-small.png') }}" class="img-fluid logo-small" alt="Logo">
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
                  <img src="{{ url('public/qc/assets/img/logo-small.png') }}" class="img-fluid" alt="ÉchoTerra Logo" style="max-width: 50px;">
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
                           <img src="{{ url('public/qc/assets/img/flags/us.svg') }}" alt="Language" class="img-fluid">
                        </a>
                        <ul class="dropdown-menu p-2" style="">

                           <!-- item-->
                           <li>
                              <a href="javascript:void(0);" class="dropdown-item">
                                 <img src="{{ url('public/qc/assets/img/flags/us.svg') }}" alt="flag" class="me-2">English
                              </a>
                           </li>

                           

                           <!-- item-->
                           <li>
                              <a href="javascript:void(0);" class="dropdown-item">
                                 <img src="{{ url('public/qc/assets/img/flags/fr.svg') }}" alt="flag" class="me-2">French
                              </a>
                           </li>

                           <!-- item-->
                           <li>
                              <a href="javascript:void(0);" class="dropdown-item">
                                 <img src="{{ url('public/qc/assets/img/flags/ae.svg') }}" alt="flag" class="me-2">Arabic
                              </a>
                           </li>

                        </ul>
                     </div>

                     <!-- Notification -->
                     <div class="notification_item me-3">
                        <a href="#"
                           class="btn btn-menubar position-relative"
                           id="notification_popup"
                           data-bs-toggle="dropdown"
                           data-bs-auto-close="outside"
                           data-notifications-url="{{ route('qc.notifications.data') }}"
                           data-notifications-mark-read="{{ route('qc.notifications.mark-read') }}"
                           data-notifications-mark-all="{{ route('qc.notifications.mark-all-read') }}"
                           aria-expanded="false">
                           <i class="bi bi-bell"></i>
                           <span id="qcNotificationBadge" class="position-absolute badge bg-success border border-white" style="display:none;"></span>
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
                                          <a href="javascript:void(0);" class="dropdown-item" id="qcNotificationMarkAll"><i class="ti ti-bell-check me-1"></i>Mark as Read</a>
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
                                             <div id="qcNotificationList">
                                                <div class="text-center text-white-50 py-4">No notifications yet.</div>
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
                           
                           <!-- View All-->
                           <div class="p-2 rounded-bottom border-top text-center">
                              <a href="{{ route('qc.notifications') }}" class="text-center fw-medium fs-14 mb-0">
                                 View All
                              </a>
                           </div>
                           
                        </div>
                     </div>

                    

                     <!-- User Dropdown -->
                     <div class="dropdown profile-dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                           <span class="avatar online">
                              <img src="{{ url('public/qc/assets/img/profiles/avatar-01.jpg') }}" alt="Img" class="img-fluid rounded-circle">
                           </span>
                        </a>
                        <div class="dropdown-menu p-2">
                           <div class="d-flex align-items-center bg-light rounded-1 p-2 mb-2">
                              <span class="avatar avatar-lg me-2">
                                 <img src="{{ url('public/qc/assets/img/profiles/avatar-01.jpg') }}" alt="img" class="rounded-circle">
                              </span>
                              <div>
                                 <h6 class="fs-12 fw-medium mb-1">Jafna Cremson</h6>
                                 <p class="fs-10">Administrator</p>
                              </div>
                           </div>

                           <!-- Item-->
                           <a class="dropdown-item d-flex align-items-center" href="{{ route('qc.account-settings') }}">
                              <i class="isax isax-profile-circle me-2"></i>Profile Settings
                           </a>



                           <hr class="dropdown-divider my-2">

                           <!-- Item-->
                           <a class="dropdown-item logout d-flex align-items-center" href="{{ route('qc.login') }}">
                              <i class="isax isax-logout me-2"></i>Sign Out
                           </a>
                        </div>
                     </div>

                  </div>
            </ul>
            <!-- /Header Menu -->
         </div>
         <!-- /Header -->




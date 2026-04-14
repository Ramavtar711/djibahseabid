@include('bid_admin.admin.include.header')

@include('bid_admin.admin.include.side_menu')

@php
   $settings = $settingsData ?? [];
   $platform = $settings['platform'] ?? [];
   $payments = $settings['payments'] ?? [];
   $auction = $settings['auction'] ?? [];
   $notifications = $settings['notifications'] ?? [];
   $security = $settings['security'] ?? [];
   $activeTab = in_array(($activeSettingsTab ?? 'platform'), ['platform', 'payments', 'auction', 'notifications', 'security'], true)
      ? ($activeSettingsTab ?? 'platform')
      : 'platform';
@endphp

<style>
   .status-header {
      background: linear-gradient(135deg, rgba(8, 47, 73, 0.92), rgba(14, 116, 144, 0.82));
      border-radius: 18px;
      padding: 18px 24px;
      margin-bottom: 28px;
      color: #fff;
      box-shadow: 0 18px 40px rgba(8, 47, 73, 0.18);
   }

   .status-pill {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 13px;
      font-weight: 600;
      color: rgba(255, 255, 255, 0.92);
   }

   .settings-nav {
      display: flex;
      flex-direction: column;
      gap: 10px;
      padding-top: 6px;
   }

   .settings-tab-btn {
      display: flex;
      align-items: center;
      gap: 10px;
      width: 100%;
      border: 0;
      background: transparent;
      color: #fff;
      padding: 12px 18px;
      border-radius: 12px;
      font-weight: 700;
      transition: all 0.18s ease;
      text-align: left;
   }

   .settings-tab-btn.active {
      background: rgba(11, 60, 93, 0.35);
      box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.06);
   }

   .settings-card {
      border: 0;
      border-radius: 24px;
      background: rgba(255, 255, 255, 0.72);
      box-shadow: 0 18px 42px rgba(15, 23, 42, 0.08);
      overflow: hidden;
   }

   .settings-pane {
      display: none;
   }

   .settings-pane.active {
      display: block;
   }

   .section-title {
      color: #0f172a;
      font-weight: 800;
      margin-bottom: 8px;
   }

   .section-subtitle {
      color: #64748b;
      margin-bottom: 22px;
   }

   .form-label {
      font-weight: 700;
      color: #334155;
   }

   .form-control,
   .form-select {
      min-height: 50px;
      border-radius: 14px;
      border-color: rgba(148, 163, 184, 0.35);
      box-shadow: none;
   }

   .form-control:focus,
   .form-select:focus {
      border-color: #38bdf8;
      box-shadow: 0 0 0 0.2rem rgba(56, 189, 248, 0.15);
   }

   .switch-card {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 14px;
      padding: 0;
      border: 0;
      background: transparent;
      margin-bottom: 14px;
   }

   .switch-card-title {
      font-weight: 700;
      color: #0f172a;
      margin-bottom: 4px;
   }

   .switch-card-copy {
      color: #64748b;
      font-size: 14px;
      margin-bottom: 0;
   }

   .form-check-input {
      width: 30px;
      height: 16px;
      cursor: pointer;
      background-color: #6d28d9;
      border-color: #6d28d9;
    }

   .save-row {
      display: flex;
      gap: 12px;
      align-items: center;
      margin-top: 24px;
   }

   .page-heading {
      font-size: 22px;
      font-weight: 800;
      color: #21004d;
      margin-bottom: 22px;
   }

   .settings-alert {
      max-width: 920px;
   }

   .settings-inner {
      max-width: 920px;
   }

   @media (max-width: 767.98px) {
      .settings-nav {
         margin-bottom: 18px;
      }
   }
</style>

<div class="page-wrapper">
   <div class="content container-fluid">
      <div class="status-header d-flex flex-wrap justify-content-between align-items-center gap-3">
         <div class="d-flex flex-wrap align-items-center gap-4">
            <div class="status-pill">
               <i class="bi bi-circle-fill text-success"></i>
               <span>SYSTEM STATUS: <strong>{{ $systemStatus ?? 'STANDBY' }}</strong></span>
            </div>
            <div class="status-pill">
               <i class="bi bi-circle-fill text-danger"></i>
               <span><strong>{{ $liveAuctionsCount ?? 0 }}</strong> Live Auctions</span>
            </div>
            <div class="status-pill">
               <i class="bi bi-circle-fill text-warning"></i>
               <span><strong>{{ $upcomingAuctionsCount ?? 0 }}</strong> Upcoming</span>
            </div>
            <div class="status-pill">
               <i class="bi bi-circle-fill text-success"></i>
               <span><strong>${{ number_format((float) ($revenueToday ?? 0), 2) }}</strong> Revenue Today</span>
            </div>
         </div>
         <div class="fw-bold">
            {{ $registeredBuyersCount ?? 0 }}
            <span class="text-white-50 fw-normal">Registered Buyers</span>
         </div>
      </div>

      <h1 class="page-heading">Settings</h1>

         @if (session('success'))
            <div class="alert alert-success settings-alert">{{ session('success') }}</div>
         @endif

         @if ($errors->any())
            <div class="alert alert-danger settings-alert">
               {{ $errors->first() }}
            </div>
         @endif

         <div class="row g-0 settings-inner">
            <div class="col-md-3">
               <div class="settings-nav" id="settingsTabNav">
                  <button class="settings-tab-btn {{ $activeTab === 'platform' ? 'active' : '' }}" type="button" data-target="platform">
                     <i class="bi bi-gear"></i> Platform
                  </button>
                  <button class="settings-tab-btn {{ $activeTab === 'payments' ? 'active' : '' }}" type="button" data-target="payments">
                     <i class="bi bi-credit-card"></i> Payments
                  </button>
                  <button class="settings-tab-btn {{ $activeTab === 'auction' ? 'active' : '' }}" type="button" data-target="auction">
                     <i class="bi bi-hammer"></i> Auction
                  </button>
                  <button class="settings-tab-btn {{ $activeTab === 'notifications' ? 'active' : '' }}" type="button" data-target="notifications">
                     <i class="bi bi-bell"></i> Notifications
                  </button>
                  <button class="settings-tab-btn {{ $activeTab === 'security' ? 'active' : '' }}" type="button" data-target="security">
                     <i class="bi bi-shield-lock"></i> Security
                  </button>
               </div>
            </div>

            <div class="col-md-9">
               <div class="settings-card">
                  <div class="card-body p-4">
                     <div class="settings-pane {{ $activeTab === 'platform' ? 'active' : '' }}" data-pane="platform">
                        <h4 class="section-title">Platform Settings</h4>
                        <p class="section-subtitle d-none">Core marketplace identity aur billing defaults manage karein.</p>

                        <form method="POST" action="{{ route('admin.settings.update') }}">
                           @csrf
                           <input type="hidden" name="settings_tab" value="platform">

                           <div class="row g-3">
                              <div class="col-md-6">
                                 <label class="form-label">Platform Name</label>
                                 <input class="form-control" type="text" name="platform_name" value="{{ old('platform_name', $platform['platform_name'] ?? '') }}">
                              </div>
                              <div class="col-md-6">
                                 <label class="form-label">Support Email</label>
                                 <input class="form-control" type="email" name="support_email" value="{{ old('support_email', $platform['support_email'] ?? '') }}">
                              </div>
                              <div class="col-md-6">
                                 <label class="form-label">Default Currency</label>
                                 <select class="form-select" name="default_currency">
                                    @foreach (['USD', 'EUR', 'INR', 'AED'] as $currency)
                                       <option value="{{ $currency }}" @selected(old('default_currency', $platform['default_currency'] ?? 'USD') === $currency)>{{ $currency }}</option>
                                    @endforeach
                                 </select>
                              </div>
                              <div class="col-md-6">
                                 <label class="form-label">Platform Commission (%)</label>
                                 <input class="form-control" type="number" step="0.01" min="0" max="100" name="platform_commission" value="{{ old('platform_commission', $platform['platform_commission'] ?? 0) }}">
                              </div>
                           </div>

                           <div class="save-row">
                              <button class="btn btn-primary px-4" type="submit">Save</button>
                           </div>
                        </form>
                     </div>

                     <div class="settings-pane {{ $activeTab === 'payments' ? 'active' : '' }}" data-pane="payments">
                        <h4 class="section-title">Payment Settings</h4>
                        <p class="section-subtitle d-none">Payment methods aur settlement deadline yahin se control honge.</p>

                        <form method="POST" action="{{ route('admin.settings.update') }}">
                           @csrf
                           <input type="hidden" name="settings_tab" value="payments">

                           <div class="switch-card">
                              <div>
                                 <div class="switch-card-title">Enable Bank Transfer</div>
                                 <p class="switch-card-copy">Manual transfer payments allow karein.</p>
                              </div>
                              <div class="form-check form-switch m-0">
                                 <input class="form-check-input" type="checkbox" name="enable_bank_transfer" value="1" @checked(old('enable_bank_transfer', $payments['enable_bank_transfer'] ?? false))>
                              </div>
                           </div>

                           <div class="switch-card">
                              <div>
                                 <div class="switch-card-title">Enable Credit / Debit Card</div>
                                 <p class="switch-card-copy">Card-based checkout option active rakhein.</p>
                              </div>
                              <div class="form-check form-switch m-0">
                                 <input class="form-check-input" type="checkbox" name="enable_card_payment" value="1" @checked(old('enable_card_payment', $payments['enable_card_payment'] ?? false))>
                              </div>
                           </div>

                           <div class="switch-card">
                              <div>
                                 <div class="switch-card-title">Enable Wallet Payment</div>
                                 <p class="switch-card-copy">Wallet balance se pay karne ka option dikhayein.</p>
                              </div>
                              <div class="form-check form-switch m-0">
                                 <input class="form-check-input" type="checkbox" name="enable_wallet_payment" value="1" @checked(old('enable_wallet_payment', $payments['enable_wallet_payment'] ?? false))>
                              </div>
                           </div>

                           <div class="row g-3 mt-1">
                              <div class="col-md-6">
                                 <label class="form-label">Payment Deadline (Hours)</label>
                                 <input class="form-control" type="number" name="payment_deadline_hours" min="1" max="720" value="{{ old('payment_deadline_hours', $payments['payment_deadline_hours'] ?? 48) }}">
                              </div>
                           </div>

                           <div class="save-row">
                              <button class="btn btn-primary px-4" type="submit">Save</button>
                           </div>
                        </form>
                     </div>

                     <div class="settings-pane {{ $activeTab === 'auction' ? 'active' : '' }}" data-pane="auction">
                        <h4 class="section-title">Auction Settings</h4>
                        <p class="section-subtitle d-none">Bid rules aur standard auction timing ko configure karein.</p>

                        <form method="POST" action="{{ route('admin.settings.update') }}">
                           @csrf
                           <input type="hidden" name="settings_tab" value="auction">

                           <div class="row g-3">
                              <div class="col-md-6">
                                 <label class="form-label">Minimum Bid Increment ($)</label>
                                 <input class="form-control" type="number" step="0.01" min="0" name="minimum_bid_increment" value="{{ old('minimum_bid_increment', $auction['minimum_bid_increment'] ?? 0) }}">
                              </div>
                              <div class="col-md-6">
                                 <label class="form-label">Auction Duration (Minutes)</label>
                                 <input class="form-control" type="number" min="1" max="1440" name="auction_duration_minutes" value="{{ old('auction_duration_minutes', $auction['auction_duration_minutes'] ?? 30) }}">
                              </div>
                              <div class="col-md-6">
                                 <label class="form-label">Auto Extend Auction</label>
                                 <select class="form-select" name="auto_extend_auction">
                                    <option value="enabled" @selected(old('auto_extend_auction', $auction['auto_extend_auction'] ?? 'enabled') === 'enabled')>Enabled</option>
                                    <option value="disabled" @selected(old('auto_extend_auction', $auction['auto_extend_auction'] ?? 'enabled') === 'disabled')>Disabled</option>
                                 </select>
                              </div>
                           </div>

                           <div class="save-row">
                              <button class="btn btn-primary px-4" type="submit">Save</button>
                           </div>
                        </form>
                     </div>

                     <div class="settings-pane {{ $activeTab === 'notifications' ? 'active' : '' }}" data-pane="notifications">
                        <h4 class="section-title">Notification Settings</h4>
                        <p class="section-subtitle d-none">Admin communication channels on/off yahin se manage honge.</p>

                        <form method="POST" action="{{ route('admin.settings.update') }}">
                           @csrf
                           <input type="hidden" name="settings_tab" value="notifications">

                           <div class="switch-card">
                              <div>
                                 <div class="switch-card-title">Email Notifications</div>
                                 <p class="switch-card-copy">Operational updates email par bheje jayen.</p>
                              </div>
                              <div class="form-check form-switch m-0">
                                 <input class="form-check-input" type="checkbox" name="email_notifications" value="1" @checked(old('email_notifications', $notifications['email_notifications'] ?? false))>
                              </div>
                           </div>

                           <div class="switch-card">
                              <div>
                                 <div class="switch-card-title">SMS Notifications</div>
                                 <p class="switch-card-copy">Critical notifications SMS channel par enable karein.</p>
                              </div>
                              <div class="form-check form-switch m-0">
                                 <input class="form-check-input" type="checkbox" name="sms_notifications" value="1" @checked(old('sms_notifications', $notifications['sms_notifications'] ?? false))>
                              </div>
                           </div>

                           <div class="switch-card">
                              <div>
                                 <div class="switch-card-title">Push Notifications</div>
                                 <p class="switch-card-copy">Browser/app push alerts dikhane ke liye use hota hai.</p>
                              </div>
                              <div class="form-check form-switch m-0">
                                 <input class="form-check-input" type="checkbox" name="push_notifications" value="1" @checked(old('push_notifications', $notifications['push_notifications'] ?? false))>
                              </div>
                           </div>

                           <div class="save-row">
                              <button class="btn btn-primary px-4" type="submit">Save</button>
                           </div>
                        </form>
                     </div>

                     <div class="settings-pane {{ $activeTab === 'security' ? 'active' : '' }}" data-pane="security">
                        <h4 class="section-title">Security Settings</h4>
                        <p class="section-subtitle d-none">Login hardening aur fraud protection defaults set karein.</p>

                        <form method="POST" action="{{ route('admin.settings.update') }}">
                           @csrf
                           <input type="hidden" name="settings_tab" value="security">

                           <div class="switch-card">
                              <div>
                                 <div class="switch-card-title">Enable Two-Factor Authentication</div>
                                 <p class="switch-card-copy">Admin accounts ke liye extra login verification.</p>
                              </div>
                              <div class="form-check form-switch m-0">
                                 <input class="form-check-input" type="checkbox" name="enable_two_factor_authentication" value="1" @checked(old('enable_two_factor_authentication', $security['enable_two_factor_authentication'] ?? false))>
                              </div>
                           </div>

                           <div class="switch-card">
                              <div>
                                 <div class="switch-card-title">Fraud Detection System</div>
                                 <p class="switch-card-copy">Suspicious activity flagging system ko active rakhein.</p>
                              </div>
                              <div class="form-check form-switch m-0">
                                 <input class="form-check-input" type="checkbox" name="fraud_detection_system" value="1" @checked(old('fraud_detection_system', $security['fraud_detection_system'] ?? false))>
                              </div>
                           </div>

                           <div class="row g-3 mt-1">
                              <div class="col-md-6">
                                 <label class="form-label">Password Minimum Length</label>
                                 <input class="form-control" type="number" min="6" max="64" name="password_minimum_length" value="{{ old('password_minimum_length', $security['password_minimum_length'] ?? 8) }}">
                              </div>
                           </div>

                           <div class="save-row">
                              <button class="btn btn-primary px-4" type="submit">Save</button>
                           </div>
                        </form>
                     </div>
                  </div>
               </div>
            </div>
         </div>
   </div>
</div>

@push('scripts')
<script type="text/javascript">
   (function () {
      var buttons = document.querySelectorAll('.settings-tab-btn');
      var panes = document.querySelectorAll('.settings-pane');

      if (!buttons.length || !panes.length) {
         return;
      }

      var activate = function (target) {
         buttons.forEach(function (button) {
            button.classList.toggle('active', button.getAttribute('data-target') === target);
         });

         panes.forEach(function (pane) {
            pane.classList.toggle('active', pane.getAttribute('data-pane') === target);
         });
      };

      buttons.forEach(function (button) {
         button.addEventListener('click', function () {
            activate(button.getAttribute('data-target'));
         });
      });
   })();
</script>
@endpush

@include('bid_admin.admin.include.footer')

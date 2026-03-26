@include('bid_admin.qc.include.header')

@include('bid_admin.qc.include.side_menu')

@php
   $lotLabel = $lot ? '#LOT-' . str_pad($lot->id, 4, '0', STR_PAD_LEFT) : 'Select a lot';
   $defaultStartValue = ($defaultStart ?? now()->addHours(2)->startOfHour());
   $defaultDate = $defaultStartValue->format('Y-m-d');
   $defaultTime = $defaultStartValue->format('H:i');
   $defaultDuration = $defaultDuration ?? 30;
@endphp

<style>
   .sniping-switch-wrap {
      display: flex;
      align-items: center;
      gap: 12px;
   }

   .sniping-switch-wrap .form-check-input {
      cursor: pointer;
   }

   .sniping-state {
      min-width: 42px;
      font-size: 0.8rem;
      font-weight: 700;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      color: #38bdf8;
   }

   .sniping-panel.is-enabled {
      box-shadow: 0 0 0 1px rgba(56, 189, 248, 0.35) inset;
   }
</style>

<!-- Page Wrapper -->
<div class="page-wrapper">
   <div class="content container-fluid" style="padding-bottom:120px">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

      <div class="d-flex justify-content-between align-items-center mb-4">
         <div>
            <h2 class="page-title text-white mb-1">Auction Scheduled</h2>
            <p class="text-white">Configuring Auction Timeline</p>
         </div>
      </div>

      @if(session('success'))
         <div class="alert alert-success px-4 py-3 rounded-3">
            {{ session('success') }}
         </div>
      @endif

      @if(! $lot)
         <div class="alert alert-warning px-4 py-3 rounded-3">
            Select a lot from the submitted queue or use the lot query parameter (e.g. <strong>?lot=13</strong>).
         </div>
      @endif

      <form method="POST" action="{{ route('qc.auction-scheduled.update') }}">
         @csrf
         @if ($lot)
            <input type="hidden" name="lot_id" value="{{ $lot->id }}">
         @endif
         <input type="hidden" name="duration_minutes" id="durationInput" value="{{ $defaultDuration }}">
         <input type="hidden" name="start_date" id="startDateInput" value="{{ $defaultDate }}">
         <input type="hidden" name="start_time" id="startTimeInput" value="{{ $defaultTime }}">

      <div class="row g-4">
         <div class="col-lg-4">
            <div class="glass-card-setup">
               <span class="section-label"><i class="bi bi-calendar-check me-2"></i> Select Date</span>
               <input type="text" id="auctionCalendar" class="inline-cal">
               <div id="calendar-container"></div>
               <div class="mt-3 text-white-50 small">
                  Lot: <strong class="text-white">{{ $lotLabel }}</strong>
               </div>
            </div>
         </div>

         <div class="col-lg-8">
            <div class="glass-card-setup mb-4">
               <span class="section-label"><i class="bi bi-clock me-2"></i> Auction Start Time</span>
               <div class="row align-items-center">
                  <div class="col-md-6">
                     <div class="time-display">
                        <span class="time-val" id="displayTime">18:30</span>
                        <span class="d-block text-white-50 small mt-1">UTC TIME</span>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <label class="small text-white-50 mb-2">Adjust Time</label>
                     <input type="time" class="form-control bg-dark text-white border-secondary mb-3" id="timeInput" value="{{ $defaultTime }}">
                     <p class="small text-warning"><i class="bi bi-info-circle me-1"></i> Auction must start at least 2 hours from now.</p>
                  </div>
               </div>
            </div>

            <div class="glass-card-setup">
               <span class="section-label"><i class="bi bi-hourglass-split me-2"></i> Duration</span>
               <div class="row g-2">
                  <div class="col-4"><button class="duration-btn {{ $defaultDuration === 15 ? 'active' : '' }}" type="button">15 Min</button></div>
                  <div class="col-4"><button class="duration-btn {{ $defaultDuration === 30 ? 'active' : '' }}" type="button">30 Min</button></div>
                  <div class="col-4"><button class="duration-btn {{ $defaultDuration === 60 ? 'active' : '' }}" type="button">60 Min</button></div>
               </div>
            </div>
         </div>

         <div class="col-lg-12">
            <div class="glass-card-setup sniping-panel {{ ($antiSnipingEnabled ?? true) ? 'is-enabled' : '' }}" id="snipingPanel">
               <div class="d-flex justify-content-between align-items-center">
                  <div class="d-flex align-items-center gap-3">
                     <div class="p-3 rounded-circle bg-purple bg-opacity-10">
                        <i class="bi bi-shield-lock-fill fs-3" style="color: white"></i>
                     </div>
                     <div>
                        <h4 class="fw-bold mb-2 text-white">Anti-Sniping Protocol</h4>
                        <p class="small text-white mb-0">If a bid occurs in the last 60 seconds, the clock adds +2 minutes to prevent unfair sniping.</p>
                     </div>
                  </div>
                  <div class="sniping-switch-wrap">
                     <span class="sniping-state" id="snipingState">{{ ($antiSnipingEnabled ?? true) ? 'On' : 'Off' }}</span>
                     <div class="form-check form-switch m-0">
                        <input class="form-check-input fs-3" id="antiSnipingToggle" type="checkbox" name="anti_sniping" value="1" {{ ($antiSnipingEnabled ?? true) ? 'checked' : '' }}>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <div class="action-bar">
      <div>
         <small class="text-white-50 d-block">Scheduled For</small>
         <strong class="text-info me-4" id="selectedDateLabel">-</strong>
         <strong class="text-white" id="selectedTimeRange">-</strong>
      </div>
      <button class="btn-finalize" type="submit" {{ $lot ? '' : 'disabled' }}>CONFIRM & FINALIZE SCHEDULE</button>
   </div>
   </form>
</div>
<!-- /Page Wrapper -->

@include('bid_admin.qc.include.footer')

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
   (function () {
      const dateLabel = document.getElementById('selectedDateLabel');
      const timeRange = document.getElementById('selectedTimeRange');
      const timeInput = document.getElementById('timeInput');
      const displayTime = document.getElementById('displayTime');
      const durationButtons = document.querySelectorAll('.duration-btn');
      const antiSnipingToggle = document.getElementById('antiSnipingToggle');
      const snipingState = document.getElementById('snipingState');
      const snipingPanel = document.getElementById('snipingPanel');
      const durationValues = { '15 Min': 15, '30 Min': 30, '60 Min': 60 };

      let selectedDate = new Date("{{ $defaultDate }}T00:00:00");
      let selectedDuration = {{ (int) $defaultDuration }};

      function toTwo(n) {
         return String(n).padStart(2, '0');
      }

      function formatDate(date) {
         return `${date.getFullYear()}-${toTwo(date.getMonth() + 1)}-${toTwo(date.getDate())}`;
      }

      function addMinutes(hhmm, minutes) {
         const parts = hhmm.split(':');
         const date = new Date(2000, 0, 1, parseInt(parts[0], 10), parseInt(parts[1], 10), 0);
         date.setMinutes(date.getMinutes() + minutes);
         return `${toTwo(date.getHours())}:${toTwo(date.getMinutes())}`;
      }

      function refreshSummary() {
         const from24 = timeInput.value || '18:30';
         const to24 = addMinutes(from24, selectedDuration);
         dateLabel.textContent = formatDate(selectedDate);
         timeRange.textContent = `${from24} - ${to24}`;
         displayTime.textContent = from24;
         document.getElementById('durationInput').value = selectedDuration;
         document.getElementById('startDateInput').value = formatDate(selectedDate);
         document.getElementById('startTimeInput').value = from24;
      }

      if (typeof flatpickr !== 'undefined') {
         flatpickr('#auctionCalendar', {
            inline: true,
            appendTo: document.getElementById('calendar-container'),
            defaultDate: selectedDate,
            dateFormat: 'Y-m-d',
            onChange: function (selectedDates) {
               if (selectedDates && selectedDates.length) {
                  selectedDate = selectedDates[0];
                  refreshSummary();
               }
            }
         });
      }

      timeInput.addEventListener('input', function () {
         displayTime.textContent = timeInput.value || '18:30';
         refreshSummary();
      });

      durationButtons.forEach(function (btn) {
         btn.addEventListener('click', function () {
            durationButtons.forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');
            selectedDuration = durationValues[btn.textContent.trim()] || 30;
            refreshSummary();
         });
      });

      function refreshSnipingState() {
         if (!antiSnipingToggle || !snipingState || !snipingPanel) {
            return;
         }

         snipingState.textContent = antiSnipingToggle.checked ? 'On' : 'Off';
         snipingPanel.classList.toggle('is-enabled', antiSnipingToggle.checked);
      }

      if (antiSnipingToggle) {
         antiSnipingToggle.addEventListener('change', refreshSnipingState);
      }

      if (snipingPanel && antiSnipingToggle) {
         snipingPanel.addEventListener('click', function (event) {
            if (event.target.closest('input, label, button, a')) {
               return;
            }

            antiSnipingToggle.checked = !antiSnipingToggle.checked;
            antiSnipingToggle.dispatchEvent(new Event('change'));
         });
      }

      refreshSummary();
      refreshSnipingState();
   })();
</script>

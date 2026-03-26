@include('bid_admin.qc.include.header')

@include('bid_admin.qc.include.side_menu')

@php
   $lotLabel = $lot ? '#LOT-' . str_pad($lot->id, 4, '0', STR_PAD_LEFT) : 'Select a lot';
   $statusText = $lot?->status ? strtoupper($lot->status) : 'PENDING';
   $statusApproved = $lot && stripos((string) $lot->status, 'approved') !== false;
   $statusScheduled = $lot && stripos((string) $lot->status, 'scheduled') !== false;
   $liveAllowed = $statusApproved || $statusScheduled;
   $badgeClass = $statusApproved
      ? 'bg-success-subtle text-success border border-success'
      : 'bg-warning-subtle text-warning border border-warning';
   $mediaImages = is_array($lot?->media_images) ? $lot->media_images : [];
   $liveSource = $lot?->media_live_source ?? 'in_app';
@endphp

<!-- Page Wrapper -->
<div class="page-wrapper">
   <div class="content container-fluid" style="padding-bottom:120px">
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

      <form method="POST" action="{{ route('qc.media-control.update') }}" enctype="multipart/form-data">
         @csrf
         @if ($lot)
            <input type="hidden" name="lot_id" value="{{ $lot->id }}">
         @endif
         <input type="hidden" id="mediaModeInput" name="media_mode" value="{{ $activeMode ?? 'video' }}">
         <input type="hidden" id="mediaLiveInput" name="media_live_source" value="{{ $liveSource }}">

      <header class="mb-5 d-flex justify-content-between align-items-center">
         <div>
            <h2 class="page-title fw-bold mb-1 text-white">Media Control Center</h2>
            <p class="text-white">Select broadcast mode for <span class="text-white">{{ $lotLabel }}</span></p>
         </div>
         <div id="status-badge" class="badge {{ $badgeClass }} px-3 py-2">
            <i class="bi bi-check-circle-fill me-2"></i>STATUS: <span id="status-text">{{ $statusText }}</span>
         </div>
      </header>

      <div class="row g-4 mb-5">
         <div class="col-md-4">
            <div id="card-fixed" class="mode-card {{ ($activeMode ?? 'video') === 'fixed' ? 'active' : '' }}" onclick="switchMode('fixed', this)">
               <i class="bi bi-images fs-1 mb-2 d-block"></i>
               <h6 class="fw-bold">Fixed Images</h6>
            </div>
         </div>
         <div class="col-md-4">
            <div id="card-video" class="mode-card {{ ($activeMode ?? 'video') === 'video' ? 'active' : '' }}" onclick="switchMode('video', this)">
               <i class="bi bi-youtube fs-1 mb-2 d-block"></i>
               <h6 class="fw-bold">Shared Video</h6>
            </div>
         </div>
         <div class="col-md-4">
            <div id="live-card" class="mode-card {{ ($activeMode ?? 'video') === 'live' ? 'active' : '' }} {{ $liveAllowed ? '' : 'locked' }}" onclick="switchMode('live', this)">
               <i class="bi bi-lock-fill lock-icon"></i>
               <i class="bi bi-record-circle fs-1 mb-2 d-block text-danger"></i>
               <h6 class="fw-bold">Live Stream</h6>
            </div>
         </div>
      </div>

      <div id="section-fixed" class="media-section">
         <div class="glass-card-setup text-center">
            <h6 class="text-white text-uppercase small fw-bold mb-4">Fixed Image Gallery</h6>
            <div class="p-5 border-2 border-dashed rounded-4 border-secondary mb-4 bg-info bg-opacity-25" onclick="document.getElementById('fileInput').click()">
               <i class="bi bi-cloud-arrow-up fs-1 text-white"></i>
               <h5 class="text-white">Click to upload product photos</h5>
               <input type="file" id="fileInput" name="media_images[]" hidden multiple accept="image/*" onchange="handleFiles(this.files)">
            </div>
            <div class="img-preview-container" id="preview-grid">
               @php $previewCount = 0; @endphp
               @foreach($mediaImages as $image)
                  @php $previewCount++; @endphp
                  <div class="img-slot">
                     <img src="{{ asset('storage/' . ltrim($image, '/')) }}" alt="preview">
                  </div>
               @endforeach
               @for($i = $previewCount; $i < 3; $i++)
                  <div class="img-slot"><i class="bi bi-plus-lg"></i></div>
               @endfor
            </div>
         </div>
      </div>

      <div id="section-video" class="media-section active-section">
         <div class="glass-card-setup">
            <h6 class="text-white text-uppercase small fw-bold mb-4">Video Source Control</h6>
            <div class="input-group mb-3">
               <span class="input-group-text bg-dark border-secondary text-white-50">URL</span>
               <input type="text" id="ytUrl" name="media_video_url" class="form-control bg-dark border-secondary text-white" value="{{ $lot->media_video_url ?? '' }}" placeholder="https://www.youtube.com/watch?v=...">
               <button class="btn btn-primary px-4" type="button" onclick="testVideo()">TEST PLAY</button>
            </div>
            <div id="video-feedback" class="small text-white-50"></div>
         </div>
      </div>

      <div id="section-live" class="media-section">
         <div class="glass-card-setup">
            <h6 class="text-white text-uppercase small fw-bold mb-4">Live Broadcast Source</h6>
            <div class="list-group">
               <button class="btn btn-outline-primary text-start p-3 mb-2 rounded-3 {{ $liveSource === 'in_app' ? 'active' : '' }}" type="button" data-source="in_app" onclick="setLiveSource('in_app', this)" {{ $liveAllowed ? '' : 'disabled' }}><i class="bi bi-phone me-3"></i>In-App Live Stream</button>
               <button class="btn btn-outline-primary text-start p-3 mb-2 rounded-3 {{ $liveSource === 'youtube' ? 'active' : '' }}" type="button" data-source="youtube" onclick="setLiveSource('youtube', this)" {{ $liveAllowed ? '' : 'disabled' }}><i class="bi bi-youtube me-3"></i>YouTube Live Feed</button>
               <button id="rtmpBtn" class="btn btn-outline-secondary text-start p-3 mb-2 rounded-3 {{ $liveSource === 'rtmp' ? 'active' : '' }}" type="button" data-source="rtmp" onclick="setLiveSource('rtmp', this)" {{ $liveAllowed ? '' : 'disabled' }}><i class="bi bi-hdd-network me-3"></i>RTMP Endpoint</button>
            </div>
            @if (! $liveAllowed)
               <small class="text-warning d-block mt-3">Live stream unlocks after approval or auction scheduling.</small>
            @endif
         </div>
      </div>
   </div>

   <div class="action-bar">
      <div>
         <small class="text-white-50 d-block">Active Mode:</small>
         <strong class="text-info" id="activeModeLabel">VIDEO</strong>
      </div>
      <button class="btn-finalize" type="submit" {{ $lot ? '' : 'disabled' }}>AUTHORIZE & BROADCAST</button>
   </div>
   </form>
</div>
<!-- /Page Wrapper -->

@include('bid_admin.qc.include.footer')

<script>
   function modeLabel(mode) {
      if (mode === 'fixed') return 'FIXED IMAGES';
      if (mode === 'live') return 'LIVE STREAM';
      return 'VIDEO';
   }

   function switchMode(mode, el) {
      var liveCard = document.getElementById('live-card');
      if (mode === 'live' && liveCard.classList.contains('locked')) {
         return;
      }

      document.querySelectorAll('.mode-card').forEach(function (card) {
         card.classList.remove('active');
      });
      if (el) {
         el.classList.add('active');
      }

      document.querySelectorAll('.media-section').forEach(function (section) {
         section.classList.remove('active-section');
      });

      var activeSection = document.getElementById('section-' + mode);
      if (activeSection) {
         activeSection.classList.add('active-section');
      }

      var modeText = document.getElementById('activeModeLabel');
      if (modeText) {
         modeText.textContent = modeLabel(mode);
      }

      var modeInput = document.getElementById('mediaModeInput');
      if (modeInput) {
         modeInput.value = mode;
      }
   }

   function setLiveSource(source, el, suppressModeSwitch) {
      var input = document.getElementById('mediaLiveInput');
      if (input) {
         input.value = source;
      }

      document.querySelectorAll('#section-live .list-group .btn').forEach(function (btn) {
         btn.classList.remove('active');
      });
      if (el) {
         el.classList.add('active');
      }

      if (!suppressModeSwitch) {
         var liveCard = document.getElementById('live-card');
         if (liveCard && !liveCard.classList.contains('locked')) {
            switchMode('live', liveCard);
         }
      }
   }

   function handleFiles(files) {
      var grid = document.getElementById('preview-grid');
      if (!grid) return;

      grid.innerHTML = '';
      var maxFiles = Math.min(files.length, 3);

      for (var i = 0; i < maxFiles; i++) {
         var file = files[i];
         var slot = document.createElement('div');
         slot.className = 'img-slot';

         var img = document.createElement('img');
         img.alt = 'preview';
         img.src = URL.createObjectURL(file);
         slot.appendChild(img);
         grid.appendChild(slot);
      }

      for (var j = maxFiles; j < 3; j++) {
         var empty = document.createElement('div');
         empty.className = 'img-slot';
         empty.innerHTML = '<i class="bi bi-plus-lg"></i>';
         grid.appendChild(empty);
      }

      var modeInput = document.getElementById('mediaModeInput');
      if (modeInput) {
         modeInput.value = 'fixed';
      }

      var form = document.querySelector('form');
      var lotIdInput = document.querySelector('input[name=\"lot_id\"]');
      if (form && files.length && lotIdInput) {
         setTimeout(function () {
            form.submit();
         }, 200);
      }
   }

   function testVideo() {
      var input = document.getElementById('ytUrl');
      var feedback = document.getElementById('video-feedback');
      var value = (input.value || '').trim();

      if (!value) {
         feedback.textContent = 'Please paste a YouTube URL first.';
         feedback.className = 'small text-warning';
         return;
      }

      var isValid = value.indexOf('youtube.com') !== -1 || value.indexOf('youtu.be') !== -1;
      if (isValid) {
         feedback.textContent = 'Video URL looks valid. Ready to broadcast.';
         feedback.className = 'small text-success';
      } else {
         feedback.textContent = 'Invalid URL. Use a YouTube link.';
         feedback.className = 'small text-danger';
      }
   }

   document.addEventListener('DOMContentLoaded', function () {
      var initialMode = "{{ $activeMode ?? 'video' }}";
      var initialCard = document.getElementById('card-' + initialMode);
      switchMode(initialMode, initialCard);

      var liveInput = document.getElementById('mediaLiveInput');
      if (liveInput && liveInput.value) {
         var btn = document.querySelector('#section-live .list-group .btn[data-source=\"' + liveInput.value + '\"]');
         if (btn) {
            setLiveSource(liveInput.value, btn, true);
         }
      }
   });
</script>

@include('bid_admin.qc.include.header')

@include('bid_admin.qc.include.side_menu')

@php use Illuminate\Support\Str; @endphp

<!-- Page Wrapper -->
         <div class="page-wrapper">
            <div class="content container-fluid">

              <div class="d-flex justify-content-between align-items-center mb-4">
            <div>

                <h2 class="page-title  mb-1">Final Auction Configuration</h2>
                <p class="text-muted">
                    Setting market parameters for
                    <span class="text-muted">
                        {{ $lot
                            ? '#LOT-' . str_pad($lot->id, 4, '0', STR_PAD_LEFT) . ' - ' . ($lot->title ?? $lot->species ?? 'Unnamed Lot')
                            : 'Select a lot from the submitted queue'
                        }}
                    </span>
                </p>
                @if ($lot)
                    <p class="text-muted small mb-0">
                        Current status:
                        <strong>{{ Str::title($lot->qc_decision ?? $lot->status ?? 'pending review') }}</strong>
                        · Updated {{ optional($lot->updated_at)->format('M d, H:i') }}
                    </p>
                @endif
                @if (! $lot)
                    <p class="text-muted small mb-1 mt-3">Verify, validate or modify key QC inputs before deciding.</p>
                    <p class="text-muted small mb-0">Decide: <strong>Approve</strong> · <strong>Reject</strong> · <strong>Modify</strong></p>
                @endif
            </div>
            <a href="{{ route('qc.lot-submitted') }}" class="text-white text-decoration-none small"><i class="bi bi-arrow-left me-2"></i>Back to Queue</a>
        </div>

        @php
            $lotQuery = $lot ? ['lot' => $lot->id] : [];
        @endphp

        @if(session('success'))
            <div class="alert alert-success px-4 py-3 rounded-3">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger px-4 py-3 rounded-3">
                {{ session('error') }}
            </div>
        @endif

        @if(! $lot)
            <div class="alert alert-warning px-4 py-3 rounded-3">
                Select a submitted lot to enable QC controls and decision buttons.
            </div>
        @endif

        @php
            $startingPrice = $lot->starting_price ?? 0;
            $lotQuantity = $lot->quantity ?? 0;
            $auctionValue = $startingPrice * $lotQuantity;
        @endphp

        @if ($lot)
            <form method="POST" action="{{ route('qc.auction-setup.update') }}">
                @csrf
                <input type="hidden" name="lot_id" value="{{ $lot->id }}">

        <div class="row g-3">
            <div class="col-lg-8">
                @if ($lot)
                    <div class="glass-card-setup">
                        <div class="section-title"><i class="bi bi-currency-dollar me-2"></i>Starting Price Configuration</div>
                        
                        <div class="row align-items-center g-4">
                            <div class="col-md-7">
                                <div class="price-input-group">
                                    <span class="sub-label">Starting Price (USD / KG)</span>
                                    <div class="d-flex align-items-center">
                                        <span class="currency-label me-2">$</span>
                                        <input type="number" class="big-input" value="{{ number_format($startingPrice, 2) }}" step="0.10" readonly>
                                    </div>
                                    <div class="mt-2 d-flex align-items-center">
                                        <i class="bi bi-lightbulb text-warning me-2"></i>
                                        <span class="small text-white">
                                            Suggested: <span class="text-white fw-bold">${{ number_format(max($startingPrice - 0.30, 0), 2) }} - ${{ number_format($startingPrice + 0.15, 2) }}</span>
                                            (Based on seller benchmarks)
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="text-center p-3">
                                    <span class="sub-label d-block mb-2">Estimated Total Value</span>
                                    <h1 class="fw-800 text-success mb-0">${{ number_format($auctionValue, 2) }}</h1>
                                    <span class="small text-white">{{ number_format($startingPrice, 2) }} USD × {{ number_format($lotQuantity, 2) }} KG</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3 mt-4">
                            <div class="section-title mb-0"><i class="bi bi-geo-fill me-2"></i>Target Audience Selection</div>
                        </div>

                        @php
                            $selectedAudienceIds = $lot ? $lot->audienceSegments->pluck('id')->toArray() : [];
                            $selectedAudienceCount = count($selectedAudienceIds);
                        @endphp

                        <div class="target-grid mb-4">
                            @foreach($audienceSegments as $segment)
                                @php $isChecked = in_array($segment->id, $selectedAudienceIds, true); @endphp
                                <div class="target-item {{ $isChecked ? 'selected' : '' }}">
                                    <label class="d-flex align-items-center gap-3 w-100 mb-0">
                                        <input type="checkbox"
                                            name="audience_segments[]"
                                            value="{{ $segment->id }}"
                                            class="form-check-input mt-0"
                                            {{ $isChecked ? 'checked' : '' }}>
                                        <span class="fw-bold">{{ $segment->name }}</span>
                                    </label>
                                    @if ($isChecked)
                                        <span class="primary-tag">PRIMARY</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 p-3 bg-dark bg-opacity-25 rounded text-white-50">
                            <div class="d-flex justify-content-between align-items-center mb-2 small">
                                <span id="audience-selected-count" class="fw-bold text-white">{{ $selectedAudienceCount }} Selected</span>
                                <span class="text-warning"><i class="bi bi-exclamation-circle me-1"></i> 1 Primary Target Required</span>
                            </div>
                            <div class="d-flex align-items-start gap-2 small text-white-50">
                                <i class="bi bi-info-circle mt-1"></i>
                                <span>Click and hold any selected segment to set it as the <strong>Primary Target</strong> for notification priority.</span>
                            </div>
                        </div>

                        <hr class="border-white-10 my-5">

                        <div class="section-title"><i class="bi bi-clipboard-check me-2"></i>Verify & Validate</div>
                        <p class="text-white-50 small mb-3">Confirm each parameter before locking in the auction.</p>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label text-white-50 small">Number of Boxes</label>
                                <input type="number" name="qc_verified_boxes" class="form-control form-control-lg bg-dark text-white border-white-10" value="{{ old('qc_verified_boxes', $lot->qc_verified_boxes ?? $lot->quantity ?? '') }}" min="0" step="1">
                                <small class="text-white-50 d-block mt-1">Confirm packing slips and dock manifest.</small>
                                @error('qc_verified_boxes')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white-50 small">Actual Weight (KG)</label>
                                <input type="number" name="qc_actual_weight" class="form-control form-control-lg bg-dark text-white border-white-10" value="{{ old('qc_actual_weight', $lot->qc_actual_weight ?? $lot->quantity ?? '') }}" step="0.01" min="0">
                                <small class="text-white-50 d-block mt-1">Match with weighment ticket.</small>
                                @error('qc_actual_weight')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white-50 small">Temperature (°C)</label>
                                <input type="number" name="qc_temperature" class="form-control form-control-lg bg-dark text-white border-white-10" value="{{ old('qc_temperature', $lot->qc_temperature ?? $lot->storage_temperature ?? '') }}" step="0.1">
                                <small class="text-white-50 d-block mt-1">Ensure storage stays within tolerance.</small>
                                @error('qc_temperature')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-white-50 small">Documents</label>
                                <div class="doc-checklist p-3 bg-white-10 rounded">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <p class="mb-0 text-white fw-bold">Submitted</p>
                                        <small class="text-white-50">Opens in new tab</small>
                                    </div>
                                    @forelse($lot->document_list as $document)
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <div class="text-white fw-semibold">{{ $document['type'] }}</div>
                                                <div class="small text-white-50">{{ $document['name'] }}</div>
                                            </div>
                                            @if ($document['url'])
                                                <a
                                                    href="{{ $document['url'] }}"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="text-info small text-decoration-none"
                                                >View</a>
                                            @else
                                                <span class="text-white-50 small">Unavailable</span>
                                            @endif
                                        </div>
                                    @empty
                                        <p class="text-white-50 small mb-0">No documents uploaded yet.</p>
                                    @endforelse
                                    <small class="text-white-50 d-block mt-2">Flag missing docs before publishing.</small>
                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" value="1" id="documentsVerified" name="qc_documents_verified" {{ old('qc_documents_verified', $lot->qc_documents_verified) ? 'checked' : '' }}>
                                        <label class="form-check-label text-white-75" for="documentsVerified">Documentation verified</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="glass-card-setup border border-warning">
                        <p class="text-white-50 mb-0">Pick a lot from the submitted queue to activate the QC checklist.</p>
                    </div>
                @endif
            </div>

            <div class="col-lg-4 d-flex flex-column gap-3">
                <div class="glass-card-setup flex-fill">
                    <div class="section-title"><i class="bi bi-flag-fill me-2"></i>Decision</div>
                    <p class="text-white-50 small mb-3">Choose one action after verifying every check.</p>
                    @error('qc_decision')
                        <p class="text-danger small mb-2">{{ $message }}</p>
                    @enderror
                    <div class="d-grid gap-2 mb-3">
                        <button type="submit" name="qc_decision" value="approve" class="btn btn-success btn-lg">
                            Approve & Publish
                        </button>
                        <button type="submit" name="qc_decision" value="reject" class="btn btn-danger btn-lg">
                            Reject Lot
                        </button>
                        <button type="submit" name="qc_decision" value="modify" class="btn btn-warning text-dark btn-lg">
                            Request Modification
                        </button>
                    </div>
                    <label class="form-label text-white-50 small">QC Notes (optional)</label>
                    <textarea name="qc_notes" class="form-control bg-dark text-white border-white-10" rows="4" placeholder="Log deviations, decisions, or follow-up requirements.">{{ old('qc_notes', $lot->qc_notes) }}</textarea>
                    @error('qc_notes')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
                <div class="glass-card-setup flex-fill">
                    <div class="section-title">Auction Summary</div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-white">Species:</span>
                        <span class="fw-bold">{{ $lot->species ?? 'Species TBD' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-white">Total Weight:</span>
                        <span class="fw-bold">{{ number_format($lotQuantity, 2) }} KG</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-white">Segments:</span>
                        <span id="segments-count-label" class="fw-bold">{{ $selectedAudienceCount }} Selected</span>
                    </div>
                    <hr class="border-white-10">
                    <div class="summary-box text-center">
                        <span class="sub-label">Total Auction Value</span>
                        <h2 class="text-success fw-bold mb-0">${{ number_format($auctionValue, 2) }}</h2>
                    </div>
                </div>

            </div>
        </div>



            <script>
                window.addEventListener('DOMContentLoaded', function () {
                    const segmentsLabel = document.getElementById('segments-count-label');
                    const audienceSummaryLabel = document.getElementById('audience-selected-count');
                    const quote = String.fromCharCode(34);
                    const selector = 'input[name=' + quote + 'audience_segments[]' + quote + ']';
                    const segmentCheckboxes = document.querySelectorAll(selector);

                    if (!segmentCheckboxes.length) {
                        return;
                    }

                    const updateListener = function () {
                        const selectedCount = Array.from(segmentCheckboxes).filter(function (input) {
                            return input.checked;
                        }).length;

                        if (segmentsLabel) {
                            segmentsLabel.textContent = selectedCount + ' Selected';
                        }

                        if (audienceSummaryLabel) {
                            audienceSummaryLabel.textContent = selectedCount + ' Selected';
                        }
                    };

                    segmentCheckboxes.forEach(function (checkbox) {
                        checkbox.addEventListener('change', updateListener);
                    });

                    updateListener();
                });
            </script>
        </form>
        @endif


        
          
         </div>
         <!-- /Page Wrapper -->

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" data-cfasync="false"></script>

    
     <!-- jQuery -->
      <!-- Bootstrap Core JS -->
      <!-- Feather Icon JS -->
      <!-- Slimscroll JS -->
      <!-- Theme Settings JS -->
      <!-- Custom JS -->
      <!-- Datatable JS -->
      <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"  type="text/javascript"></script>
      <script src="https://cdn.datatables.net/2.2.1/js/dataTables.bootstrap5.js"  type="text/javascript"></script>
      <script src="https://cdn.datatables.net/buttons/3.2.0/js/dataTables.buttons.js"  type="text/javascript"></script>
      <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.dataTables.js"  type="text/javascript"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"  type="text/javascript"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"  type="text/javascript"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"  type="text/javascript"></script>
      <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.html5.min.js"  type="text/javascript"></script>
      <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.print.min.js"  type="text/javascript"></script>
      <!-- Feather Icon JS -->
      <script>
         // Initialize DataTable with enhanced features
         $(document).ready(function() {
            $('#userTable').DataTable({
               pageLength: 10,
               order: [[0, 'asc']],
               responsive: true
               
              
            });
         });
      </script>

@include('bid_admin.qc.include.footer')


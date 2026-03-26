@include('bid_admin.qc.include.header')

@include('bid_admin.qc.include.side_menu')

@php use Illuminate\Support\Str; @endphp

<!-- Page Wrapper -->
         <div class="page-wrapper">
            <div class="content container-fluid">

                <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="page-title mb-1">Submitted Lots details</h2>
                <p class="text-white mb-0">Reviewing and validating incoming auction drafts</p>
                <p class="text-white-50 small mb-0">
                    {{ $lot ? '#LOT-' . str_pad($lot->id, 4, '0', STR_PAD_LEFT) : 'Lot not selected' }}
                    · Submitted {{ optional($lot->created_at)->format('M d, H:i') }}
                </p>
            </div>
            <div class="d-flex gap-2">
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-lg-5">
                <div class="glass-card-setup">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-images fs-5 text-white"></i>
                        <h6 class="fw-bold mb-0 text-white">Product Visuals</h6>
                    </div>
                    <div class="ratio ratio-16x9 rounded overflow-hidden mb-3">
                        <img src="{{ $lot->image_url }}" alt="Lot image" class="object-fit-cover w-100 h-100">
                    </div>
                    <div class="d-flex justify-content-between align-items-center text-white small mb-1">
                        <span>#LOT-{{ str_pad($lot->id ?? 0, 4, '0', STR_PAD_LEFT) }}</span>
                        <span>{{ $lot->title ?? ($lot->species ?? 'Unnamed Lot') }}</span>
                    </div>
                    <div class="row g-2 text-white small">
                        <div class="col-6">
                            <div class="fw-semibold">Seller</div>
                            <div>{{ optional($lot->seller)->name ?? 'Unknown Seller' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="fw-semibold">Storage Temp</div>
                            <div>{{ $lot->storage_temperature ?? 'N/A' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="fw-semibold">Declared Weight</div>
                            <div>{{ number_format($lot->quantity ?? 0, 2) }} KG</div>
                        </div>
                        <div class="col-6">
                            <div class="fw-semibold">Starting Price</div>
                            <div>${{ number_format($lot->starting_price ?? 0, 2) }} / KG</div>
                        </div>
                        <div class="col-6">
                            <div class="fw-semibold">Catch Method</div>
                            <div>{{ $lot->notes ? Str::limit($lot->notes, 28) : 'Long-line / Wild' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="fw-semibold">QC Status</div>
                            <div>{{ Str::title($lot->qc_decision ?? $lot->status ?? 'pending') }}</div>
                        </div>
                    </div>
                </div>

                <div class="glass-card-setup">
                    <h6 class="fw-bold mb-3 text-white"><i class="bi bi-file-earmark-pdf me-2"></i>Required Documents</h6>
                    @forelse ($lot->document_list as $document)
                        @php
                            $docTitle = pathinfo($document['name'], PATHINFO_FILENAME);
                            $docLabel = Str::title(str_replace(['-', '_'], ' ', $docTitle));
                        @endphp
                        <div class="doc-item d-flex justify-content-between align-items-center">
                            <span class="small text-white-75">
                                <i class="bi bi-file-earmark-check text-success me-2"></i>
                                {{ $docLabel }}
                            </span>
                            <a
                                href="{{ $document['url'] ?? '#' }}"
                                target="{{ $document['url'] ? '_blank' : '_self' }}"
                                rel="noopener noreferrer"
                                class="text-info small text-decoration-none {{ $document['url'] ? '' : 'disabled' }}"
                            >VIEW</a>
                        </div>
                    @empty
                        <p class="small text-white-50 mb-0">No documents submitted yet.</p>
                    @endforelse
                    <div class="doc-item mt-2">
                        <span class="small text-white-50">
                            {{ $lot->qc_notes ? 'QC Notes logged' : 'QC notes pending' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="glass-card-setup">
                    <h6 class="fw-bold mb-4 text-white"><i class="bi bi-info-circle me-2"></i>Lot Specifications</h6>
                <div class="row">
                    <div class="col-md-4 spec-item">
                        <span class="spec-label">Seller</span>
                        <span class="spec-value">{{ optional($lot->seller)->name ?? 'Unknown Seller' }}</span>
                    </div>
                    <div class="col-md-4 spec-item">
                        <span class="spec-label">Species</span>
                        <span class="spec-value">{{ $lot->species ?? 'Unknown Species' }}</span>
                    </div>
                    <div class="col-md-4 spec-item">
                        <span class="spec-label">Declared Weight</span>
                        <span class="spec-value">{{ number_format($lot->quantity ?? 0, 2) }} KG</span>
                    </div>
                    <div class="col-md-4 spec-item">
                        <span class="spec-label">Storage Temp</span>
                        <span class="spec-value text-success">
                            {{ $lot->storage_temperature ?? '—' }}
                            <i class="bi bi-thermometer-snow"></i>
                        </span>
                    </div>
                    <div class="col-md-4 spec-item">
                        <span class="spec-label">Catch Method</span>
                        <span class="spec-value">{{ $lot->notes ? Str::limit($lot->notes, 32) : 'Long-line / Wild' }}</span>
                    </div>
                    <div class="col-md-4 spec-item">
                        <span class="spec-label">QC Status</span>
                        <span class="spec-value">{{ Str::title($lot->qc_decision ?? $lot->status ?? 'pending') }}</span>
                    </div>
                </div>
                </div>

                <div class="glass-card-setup">
                    <div class="section-title mb-3"><i class="bi bi-geo-alt me-2"></i>Source Tracking</div>
                    <div class="list-group list-group-flush text-white small">
                        <div class="list-group-item bg-transparent border-0 px-0 py-1 d-flex justify-content-between">
                            <span>GPS Coordinates</span>
                            <span class="fw-semibold">{{ $lot->gps_coordinates ?? '25.2048° N, 55.2708° E' }}</span>
                        </div>
                        <div class="list-group-item bg-transparent border-0 px-0 py-1 d-flex justify-content-between">
                            <span>Origin</span>
                            <span class="fw-semibold">{{ $lot->origin ?? 'Indian Ocean · FAO Area 51' }}</span>
                        </div>
                        <div class="list-group-item bg-transparent border-0 px-0 py-1 d-flex justify-content-between">
                            <span>Storage Temp</span>
                            <span class="fw-semibold">{{ $lot->storage_temperature ?? 'TBD' }}</span>
                        </div>
                        <div class="list-group-item bg-transparent border-0 px-0 py-1 d-flex justify-content-between">
                            <span>Harvest Date</span>
                            <span class="fw-semibold">{{ optional($lot->harvest_date)->format('M d, Y') ?? 'Unknown' }}</span>
                        </div>
                        <div class="list-group-item bg-transparent border-0 px-0 py-1 d-flex justify-content-between">
                            <span>Seller</span>
                            <span class="fw-semibold">{{ optional($lot->seller)->name ?? 'Unknown Seller' }}</span>
                        </div>
                        <div class="list-group-item bg-transparent border-0 px-0 py-1 d-flex justify-content-between">
                            <span>Catch Method</span>
                            <span class="fw-semibold">{{ $lot->notes ? Str::limit($lot->notes, 28) : 'Long-line / Wild' }}</span>
                        </div>
                        <div class="list-group-item bg-transparent border-0 px-0 py-1 d-flex justify-content-between align-items-center">
                            <span>QC Status</span>
                            <span class="badge bg-success text-uppercase small">{{ Str::title($lot->qc_decision ?? $lot->status ?? 'pending') }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="action-bar">
            <div class="d-flex align-items-center gap-4">
                <div>
                    <span class="text-white-50 small d-block">Suggested Starting Bid</span>
                    <h5 class="fw-bold mb-0 text-info">$7.40 / KG</h5>
                </div>
                <div class="border-start border-white-10 ps-4">
                    <span class="text-white-50 small d-block">Auction Date</span>
                    <h6 class="mb-0">March 05, 2026 - 18:00</h6>
                </div>
            </div>
            @php
                $lotQuery = request()->filled('lot') ? ['lot' => request()->query('lot')] : [];
            @endphp
            <div class="d-flex gap-3">
                <a href="{{ route('qc.auction-setup', $lotQuery) }}" class="btn btn-outline-light">
                    <i class="bi bi-gear me-2"></i>Auction Setup
                </a>
                <a href="{{ route('qc.auction-scheduled', $lotQuery) }}" class="btn btn-hold">
                    <i class="bi bi-clock-history me-2"></i>Scheduled
                </a>
                <a href="{{ route('qc.media-control', $lotQuery) }}" class="btn btn-reject">
                    <i class="bi bi-x-circle me-2"></i>Media Control
                </a>
            </div>
        </div>

        
          
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


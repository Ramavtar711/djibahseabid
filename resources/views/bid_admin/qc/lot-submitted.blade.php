@include('bid_admin.qc.include.header')

@include('bid_admin.qc.include.side_menu')

@php use Illuminate\Support\Str; @endphp

<!-- Page Wrapper -->
         <div class="page-wrapper">
            <div class="content container-fluid">

               <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="page-title mb-1">Submitted Lots Queue</h2>
                <p class="text-muted mb-0">Reviewing and validating incoming auction drafts</p>
            </div>
            <div class="d-flex gap-2">
                <button id="exportCsvBtn" type="button" class="btn btn-outline-light btn-sm"><i class="bi bi-cloud-arrow-down me-2"></i>Export CSV</button>
               
            </div>
        </div>

        <div class="glass-panel">
            <form id="submittedLotsFilters" action="{{ route('qc.lot-submitted') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-2">
                        <span class="filter-label">Status</span>
                        <select name="status" id="filterStatus" class="form-select filter-select">
                            <option value="all" {{ $filters['status'] === 'all' ? 'selected' : '' }}>All Statuses</option>
                            @foreach($statusOptions as $status)
                                <option value="{{ $status }}" {{ strcasecmp($filters['status'], $status) === 0 ? 'selected' : '' }}>
                                    {{ Str::title($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <span class="filter-label">Species</span>
                        <select name="species" id="filterSpecies" class="form-select filter-select">
                            <option value="all" {{ $filters['species'] === 'all' ? 'selected' : '' }}>All Species</option>
                            @foreach($speciesOptions as $species)
                                <option value="{{ $species }}" {{ strcasecmp($filters['species'], $species) === 0 ? 'selected' : '' }}>
                                    {{ Str::title($species) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <span class="filter-label">Temperature Alert</span>
                        <select name="temperature_alert" id="filterTemperature" class="form-select filter-select">
                            @foreach($temperatureFilters as $key => $label)
                                <option value="{{ $key }}" {{ $filters['temperature_alert'] === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <span class="filter-label">Weight Variance</span>
                        <select name="weight_variance" id="filterVariance" class="form-select filter-select">
                            @foreach($weightVarianceFilters as $key => $label)
                                <option value="{{ $key }}" {{ $filters['weight_variance'] === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <span class="filter-label">Submission Date</span>
                        <input type="date" name="submission_date" class="form-control filter-input" value="{{ $filters['submission_date'] ?? '' }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-light w-100 btn-sm py-2 fw-bold">
                            <i class="bi bi-funnel me-2"></i>Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="glass-panel glass-card  p-0 overflow-hidden">
            <div class="table-container p-3">
                <table id="submittedLotsTable" class="submitted-table">
                    <thead>
                        <tr>
                            <th>Lot / Draft ID</th>
                            <th>Seller / Landing Site</th>
                            <th>Species & Grade</th>
                            <th>Boxes</th>
                            <th>Weight (KG)</th>
                            <th>Temp (°C)</th>
                            <th>Docs</th>
                            <th>Status</th>
                            <th>Submitted At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lots as $lot)
                            @php
                                $tempValue = $lot->temperature_value;
                                $tempClass = ($tempValue !== null && $tempValue > 4) ? 'text-danger' : 'text-success';
                                $tempIcon = ($tempValue !== null && $tempValue > 4) ? 'bi-thermometer-high' : 'bi-thermometer-snow';
                                $statusKey = Str::lower((string) $lot->status);
                                $statusClass = 'sp-pending';

                                if (Str::contains($statusKey, 'approved')) {
                                    $statusClass = 'sp-approved';
                                } elseif (Str::contains($statusKey, 'review')) {
                                    $statusClass = 'sp-review';
                                } elseif (Str::contains($statusKey, 'needs')) {
                                    $statusClass = 'sp-needsfix';
                                } elseif (Str::contains($statusKey, 'reject')) {
                                    $statusClass = 'sp-rejected';
                                }
                            @endphp
                            <tr>
                                <td class="fw-bold text-info">#LOT-{{ str_pad($lot->id ?? 0, 4, '0', STR_PAD_LEFT) }}</td>
                                <td>
                                    <span class="d-block">{{ $lot->seller_name }}</span>
                                    <small class="text-white-50">{{ $lot->landing_site }}</small>
                                </td>
                                <td>
                                    <span class="d-block fw-bold">{{ $lot->species ?? 'Unknown Species' }}</span>
                                    <small class="badge bg-secondary" style="font-size: 0.6rem;">{{ $lot->grade_label }}</small>
                                </td>
                                <td>{{ number_format($lot->quantity ?? 0, 0) }} Boxes</td>
                                <td>{{ number_format($lot->quantity ?? 0, 2) }}</td>
                                <td>
                                    <span class="fw-bold {{ $tempClass }}">
                                        <i class="bi {{ $tempIcon }} temp-alert"></i>
                                        {{ $lot->storage_temperature ?? '—' }}
                                    </span>
                                </td>
                                <td>
                                    @if ($lot->documents_path)
                                        <i class="bi bi-file-earmark-check text-success fs-5"></i>
                                    @else
                                        <i class="bi bi-file-earmark-x text-danger fs-5"></i>
                                    @endif
                                </td>
                                <td><span class="status-pill {{ $statusClass }}">{{ Str::title($lot->status ?? 'Pending') }}</span></td>
                                <td>{{ optional($lot->created_at)->format('M d, H:i') ?? '—' }}</td>
                                <td><a href="{{ route('qc.lot-subimitted-details', ['lot' => $lot->id]) }}" class="btn-open">OPEN</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-white-50">No submissions match the selected filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <span class="text-white-50 small">Showing {{ $lots->count() }} of {{ $totalLots }} Submissions</span>
        </div>
          
         </div>
         <!-- /Page Wrapper -->

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" data-cfasync="false"></script>

@push('scripts')
<script>
   $(function () {
      const table = $('#submittedLotsTable').DataTable({
         pageLength: 10,
         order: [[8, 'desc']],
         responsive: true,
         info: false,
         dom: 'frtip',
         buttons: [
            {
               extend: 'csvHtml5',
               title: 'submitted-lots-queue',
               filename: 'submitted-lots-queue',
               className: 'd-none',
               exportOptions: {
                  columns: ':visible',
               },
            },
         ],
      });

      $('#exportCsvBtn').on('click', function () {
         table.button(0).trigger();
      });
   });
</script>
@endpush

@include('bid_admin.qc.include.footer')


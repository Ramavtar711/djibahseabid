@include('bid_admin.admin.include.header')

@include('bid_admin.admin.include.side_menu')

<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="status-header d-flex flex-wrap justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-4">
                <div class="small">
                    <i class="bi bi-circle-fill {{ $systemStatus === 'LIVE' ? 'text-success' : 'text-secondary' }} me-1"></i> SYSTEM STATUS: <strong>{{ $systemStatus }}</strong>
                </div>
                <div class="small">
                    <i class="bi bi-circle-fill text-danger me-1"></i> <strong>{{ $liveAuctionsCount }}</strong> Live Auctions
                </div>
                <div class="small">
                    <i class="bi bi-circle-fill text-warning me-1"></i> <strong>{{ $upcomingAuctionsCount }}</strong> Upcoming
                </div>
                <div class="small">
                    <i class="bi bi-circle-fill text-success me-1"></i> <strong>${{ number_format((float) $revenueToday, 2) }}</strong> Revenue Today
                </div>
            </div>
            <div class="fw-bold">
                {{ $registeredBuyersCount }} <span class="text-muted fw-normal">Buyers Registered</span>
            </div>
        </div>

        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="page-title">Buyers</h1>
                    <p class="text-muted">Manage and analyze buyers</p>
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary btn-toggle me-2" onclick="showTable()"><i class="bi bi-table"></i> Table</button>
                    <button class="btn btn-outline-primary btn-toggle" onclick="showKanban()"><i class="bi bi-grid-3x3-gap"></i> Kanban</button>
                    <a href="{{ route('admin.add-buyer') }}" class="btn btn-primary">
                        <i class="fe fe-plus me-2"></i>Add New
                    </a>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="table-glass" id="tableView">
            <div class="table-responsive">
                <table class="table table-striped" id="userTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($buyers as $index => $buyer)
                            @php
                                $statusClass = match($buyer->normalized_status) {
                                    'active' => 'status-active',
                                    'inactive', 'blocked', 'suspended' => 'status-inactive',
                                    'pending', 'under review', 'review' => 'status-review',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $buyer->name ?: '-' }}</td>
                                <td>{{ $buyer->email ?: '-' }}</td>
                                <td>{{ $buyer->phone ?: '-' }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.buyers.status', $buyer->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                            @foreach(['pending', 'active', 'under review', 'inactive', 'suspended', 'blocked'] as $statusOption)
                                                <option value="{{ $statusOption }}" @selected($buyer->normalized_status === $statusOption)>
                                                    {{ \Illuminate\Support\Str::title($statusOption) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td>{{ optional($buyer->created_at)->format('d M Y') ?: '-' }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.buyer-details', ['buyer' => $buyer->id]) }}" class="btn btn-sm btn-primary" title="View"><i class="bi bi-eye"></i></a>
                                        <a href="{{ route('admin.edit-buyer', $buyer->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                        <form method="POST" action="{{ route('admin.delete-buyer', $buyer->id) }}" onsubmit="return confirm('Are you sure you want to delete this buyer?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" type="submit" title="Delete"><i class="bi bi-trash3"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No buyers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div id="kanbanView" style="display: none;">
            <div class="row g-4">
                @forelse($buyers as $buyer)
                    @php
                        $statusClass = match($buyer->normalized_status) {
                            'active' => 'status-active',
                            'inactive', 'blocked', 'suspended' => 'status-inactive',
                            'pending', 'under review', 'review' => 'status-review',
                            default => 'badge bg-secondary',
                        };
                    @endphp
                    <div class="col-lg-3 col-md-6">
                        <div class="glass-card-new">
                            <center><img class="avatar mb-3" src="{{ $buyer->profile_image_url }}" alt="{{ $buyer->name }}"></center>
                            <h6>{{ $buyer->name ?: 'Buyer' }}</h6>
                            <p>{{ $buyer->email ?: '-' }}</p>
                            <span class="{{ str_contains($statusClass, 'badge') ? $statusClass : $statusClass }}">{{ \Illuminate\Support\Str::title($buyer->normalized_status) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center text-muted">No buyers found.</div>
                    </div>
                @endforelse
            </div>
        </div>

        <script>
            function showTable() {
                document.getElementById("tableView").style.display = "block";
                document.getElementById("kanbanView").style.display = "none";
            }

            function showKanban() {
                document.getElementById("tableView").style.display = "none";
                document.getElementById("kanbanView").style.display = "block";
            }
        </script>
    </div>
</div>

<script src="https://cdn.datatables.net/2.2.1/js/dataTables.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/2.2.1/js/dataTables.bootstrap5.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/buttons/3.2.0/js/dataTables.buttons.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.dataTables.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.html5.min.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.print.min.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        $('#userTable').DataTable({
            pageLength: 10,
            order: [[0, 'asc']],
            responsive: true
        });
    });
</script>

@include('bid_admin.admin.include.footer')

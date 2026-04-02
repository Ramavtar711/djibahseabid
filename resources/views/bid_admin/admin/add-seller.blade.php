@include('bid_admin.admin.include.header')

@include('bid_admin.admin.include.side_menu')

@php
    $selectedSupplyTypes = old('supply_type', $seller?->supply_type ?? []);
    if (! is_array($selectedSupplyTypes)) {
        $decodedSupplyTypes = json_decode((string) $selectedSupplyTypes, true);
        $selectedSupplyTypes = is_array($decodedSupplyTypes) ? $decodedSupplyTypes : [];
    }

    $selectedProcessingStatuses = old('processing_status', $seller?->processing_status ?? []);
    if (! is_array($selectedProcessingStatuses)) {
        $decodedProcessingStatuses = json_decode((string) $selectedProcessingStatuses, true);
        $selectedProcessingStatuses = is_array($decodedProcessingStatuses) ? $decodedProcessingStatuses : [];
    }
@endphp

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
                    <h1 class="page-title">Add Seller</h1>
                    <p class="text-muted">Use the same fields as seller registration to create a seller account.</p>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.sellers') }}" class="btn btn-primary">
                        <i class="fe fe-eye me-2"></i>View All
                    </a>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <form method="POST" action="{{ $isEditMode ? route('admin.update-seller', $seller) : route('admin.add-seller.store') }}" enctype="multipart/form-data">
                    @csrf
                    @if($isEditMode)
                        @method('PUT')
                    @endif

                    <h5 class="section-title mb-3">Contact Information</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $seller?->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone *</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $seller?->phone) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $seller?->email) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password {{ $isEditMode ? '' : '*' }}</label>
                            <input type="password" name="password" class="form-control" {{ $isEditMode ? '' : 'required' }}>
                            @if($isEditMode)
                                <small class="text-muted">Leave blank to keep the current password.</small>
                            @endif
                        </div>
                    </div>

                    <h5 class="section-title mb-3">Company &amp; Location</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Company Name *</label>
                            <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $seller?->company_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Landing Site / Port *</label>
                            <input type="text" name="landing_site_port" class="form-control" value="{{ old('landing_site_port', $seller?->landing_site_port) }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Address *</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address', $seller?->address) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Country *</label>
                            <select name="country" class="form-select" required>
                                <option value="">Select Country</option>
                                @foreach($sellerCountries as $country)
                                    <option value="{{ $country }}" @selected(old('country', $seller?->country) === $country)>{{ $country }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <h5 class="section-title mb-3">Capabilities</h5>
                    <div class="mb-3">
                        <label class="form-label d-block">Supply Type</label>
                        <div class="row g-2">
                            @foreach($sellerSupplyTypes as $supplyType)
                                <div class="col-md-4">
                                    <div class="form-check border rounded p-3 h-100">
                                        <input class="form-check-input" type="checkbox" name="supply_type[]" value="{{ $supplyType }}" id="supply_{{ \Illuminate\Support\Str::slug($supplyType, '_') }}" @checked(in_array($supplyType, $selectedSupplyTypes, true))>
                                        <label class="form-check-label ms-1" for="supply_{{ \Illuminate\Support\Str::slug($supplyType, '_') }}">{{ $supplyType }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block">Processing Status</label>
                        <div class="row g-2">
                            @foreach($sellerProcessingStatuses as $processingStatus)
                                <div class="col-md-4 col-lg-3">
                                    <div class="form-check border rounded p-3 h-100">
                                        <input class="form-check-input" type="checkbox" name="processing_status[]" value="{{ $processingStatus }}" id="processing_{{ \Illuminate\Support\Str::slug($processingStatus, '_') }}" @checked(in_array($processingStatus, $selectedProcessingStatuses, true))>
                                        <label class="form-check-label ms-1" for="processing_{{ \Illuminate\Support\Str::slug($processingStatus, '_') }}">{{ $processingStatus }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Estimated Weekly Volume (kg)</label>
                            <input type="text" name="estimated_weekly_volume" class="form-control" value="{{ old('estimated_weekly_volume', $seller?->estimated_weekly_volume) }}">
                        </div>
                    </div>

                    <h5 class="section-title mb-3">Compliance</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Trade License {{ $isEditMode ? '' : '*' }}</label>
                            <input type="file" name="trade_license_file" class="form-control" {{ $isEditMode ? '' : 'required' }}>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Facility Photos</label>
                            <input type="file" name="facility_photos_file" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Certificates (HACCP / Health)</label>
                            <input type="file" name="certificates_file" class="form-control">
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('admin.sellers') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">{{ $isEditMode ? 'Update Seller' : 'Save Seller' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('bid_admin.admin.include.footer')

@include('bid_admin.admin.include.header')

@include('bid_admin.admin.include.side_menu')

@php
    $selectedBusinessTypes = old('business_type', $buyer?->business_type ?? []);
    if (! is_array($selectedBusinessTypes)) {
        $decoded = json_decode((string) $selectedBusinessTypes, true);
        $selectedBusinessTypes = is_array($decoded) ? $decoded : [];
    }

    $selectedInterestedIn = old('interested_in', $buyer?->interested_in ?? []);
    if (! is_array($selectedInterestedIn)) {
        $decoded = json_decode((string) $selectedInterestedIn, true);
        $selectedInterestedIn = is_array($decoded) ? $decoded : [];
    }

    $selectedPreferredDelivery = old('preferred_delivery', $buyer?->preferred_delivery ?? []);
    if (! is_array($selectedPreferredDelivery)) {
        $decoded = json_decode((string) $selectedPreferredDelivery, true);
        $selectedPreferredDelivery = is_array($decoded) ? $decoded : [];
    }

    $selectedPreferredPayment = old('preferred_payment', $buyer?->preferred_payment ?? []);
    if (! is_array($selectedPreferredPayment)) {
        $decoded = json_decode((string) $selectedPreferredPayment, true);
        $selectedPreferredPayment = is_array($decoded) ? $decoded : [];
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
                    <h1 class="page-title">Add Buyer</h1>
                    <p class="text-muted">Use the same fields as buyer registration to create a buyer account.</p>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.buyers') }}" class="btn btn-primary">
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
                <form method="POST" action="{{ $isEditMode ? route('admin.update-buyer', $buyer) : route('admin.add-buyer.store') }}" enctype="multipart/form-data">
                    @csrf
                    @if($isEditMode)
                        @method('PUT')
                    @endif

                    <h5 class="section-title mb-3">Personal Info</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Full Name *</label>
                            <input class="form-control" name="name" value="{{ old('name', $buyer?->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Job Title</label>
                            <input class="form-control" name="job_title" value="{{ old('job_title', $buyer?->job_title) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email *</label>
                            <input class="form-control" name="email" type="email" value="{{ old('email', $buyer?->email) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone (WhatsApp) *</label>
                            <input class="form-control" name="phone" value="{{ old('phone', $buyer?->phone) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password {{ $isEditMode ? '' : '*' }}</label>
                            <input class="form-control" name="password" type="password" {{ $isEditMode ? '' : 'required' }}>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password {{ $isEditMode ? '' : '*' }}</label>
                            <input class="form-control" name="password_confirmation" type="password" {{ $isEditMode ? '' : 'required' }}>
                        </div>
                    </div>

                    <h5 class="section-title mb-3">Company Info</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Company Legal Name *</label>
                            <input class="form-control" name="company_legal_name" value="{{ old('company_legal_name', $buyer?->company_legal_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">City</label>
                            <input class="form-control" name="city" value="{{ old('city', $buyer?->city) }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Business Address</label>
                            <input class="form-control" name="business_address" value="{{ old('business_address', $buyer?->business_address) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Country</label>
                            <select class="form-select" name="country">
                                <option value="">Select Country</option>
                                @foreach($buyerCountries as $country)
                                    <option value="{{ $country }}" @selected(old('country', $buyer?->country) === $country)>{{ $country }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Website</label>
                            <input class="form-control" name="website" value="{{ old('website', $buyer?->website) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Company Registration Number</label>
                            <input class="form-control" name="company_registration_number" value="{{ old('company_registration_number', $buyer?->company_registration_number) }}">
                        </div>
                    </div>

                    <h5 class="section-title mb-3">Business Type</h5>
                    <div class="row g-3 mb-4">
                        @foreach($buyerBusinessTypes as $businessType)
                            <div class="col-md-3">
                                <div class="form-check border rounded p-3 h-100">
                                    <input class="form-check-input" type="checkbox" name="business_type[]" value="{{ $businessType }}" id="business_{{ \Illuminate\Support\Str::slug($businessType, '_') }}" @checked(in_array($businessType, $selectedBusinessTypes, true))>
                                    <label class="form-check-label ms-1" for="business_{{ \Illuminate\Support\Str::slug($businessType, '_') }}">{{ $businessType }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <h5 class="section-title mb-3">Preferences</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label d-block">Interested In</label>
                            @foreach($buyerInterestedInOptions as $interestedIn)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="interested_in[]" value="{{ $interestedIn }}" id="interest_{{ \Illuminate\Support\Str::slug($interestedIn, '_') }}" @checked(in_array($interestedIn, $selectedInterestedIn, true))>
                                    <label class="form-check-label" for="interest_{{ \Illuminate\Support\Str::slug($interestedIn, '_') }}">{{ $interestedIn }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Monthly Volume</label>
                            <select class="form-select" name="monthly_volume">
                                <option value="">Select Monthly Volume</option>
                                @foreach($buyerMonthlyVolumeOptions as $volumeOption)
                                    <option value="{{ $volumeOption }}" @selected(old('monthly_volume', $buyer?->monthly_volume) === $volumeOption)>{{ $volumeOption }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label d-block">Preferred Delivery</label>
                            @foreach($buyerPreferredDeliveryOptions as $deliveryOption)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="preferred_delivery[]" value="{{ $deliveryOption }}" id="delivery_{{ \Illuminate\Support\Str::slug($deliveryOption, '_') }}" @checked(in_array($deliveryOption, $selectedPreferredDelivery, true))>
                                    <label class="form-check-label" for="delivery_{{ \Illuminate\Support\Str::slug($deliveryOption, '_') }}">{{ $deliveryOption }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <h5 class="section-title mb-3">KYC &amp; Payment</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label d-block">Preferred Payment</label>
                            @foreach($buyerPreferredPaymentOptions as $paymentOption)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="preferred_payment[]" value="{{ $paymentOption }}" id="payment_{{ \Illuminate\Support\Str::slug($paymentOption, '_') }}" @checked(in_array($paymentOption, $selectedPreferredPayment, true))>
                                    <label class="form-check-label" for="payment_{{ \Illuminate\Support\Str::slug($paymentOption, '_') }}">{{ $paymentOption }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bank Country</label>
                            <input class="form-control" name="bank_country" value="{{ old('bank_country', $buyer?->bank_country) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Upload Company Registration</label>
                            <input class="form-control" type="file" name="company_registration_file">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Upload ID</label>
                            <input class="form-control" type="file" name="id_file">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Import License</label>
                            <input class="form-control" type="file" name="import_license_file">
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_registered_business" value="1" id="registered_business" @checked(old('is_registered_business', $buyer?->is_registered_business))>
                            <label class="form-check-label" for="registered_business">I confirm this is a registered business</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="accepted_terms" value="1" id="accepted_terms" @checked(old('accepted_terms', $buyer?->accepted_terms))>
                            <label class="form-check-label" for="accepted_terms">I accept Terms &amp; Auction Rules</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="bank_transfer_validated" value="1" id="bank_transfer_validated" @checked(old('bank_transfer_validated', $buyer?->bank_transfer_validated))>
                            <label class="form-check-label" for="bank_transfer_validated">Bank transfers validated by Admin</label>
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('admin.buyers') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">{{ $isEditMode ? 'Update Buyer' : 'Save Buyer' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('bid_admin.admin.include.footer')

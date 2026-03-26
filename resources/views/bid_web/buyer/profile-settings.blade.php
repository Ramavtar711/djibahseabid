@include('bid_web.buyer.include.header')

@include('bid_web.buyer.include.side_menu')

@php
    $initials = collect(explode(' ', trim($buyer->name ?? 'Buyer')))
        ->filter()
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->take(2)
        ->implode('');
    $profileImageUrl = $buyer->profile_image ? asset('storage/' . $buyer->profile_image) : null;
@endphp

<style>
    .profile-hero-card,
    .profile-form-card {
        background: rgba(12, 74, 110, 0.82);
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(10px);
        box-shadow: 0 18px 45px rgba(2, 8, 23, 0.18);
    }

    .profile-avatar-lg {
        width: 96px;
        height: 96px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #22c1c3, #2563eb);
        color: #fff;
        font-size: 2rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        overflow: hidden;
    }

    .profile-avatar-lg img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .profile-stat {
        min-height: 100%;
        padding: 18px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.08);
    }

    .profile-label {
        display: block;
        margin-bottom: 8px;
        color: #dbeafe;
        font-weight: 600;
    }

    .profile-form-card .form-control,
    .profile-form-card .form-select,
    .profile-form-card textarea {
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 14px;
        min-height: 48px;
    }

    .profile-choice {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        min-height: 52px;
        padding: 12px 14px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
        border: 1px solid transparent;
    }

    .profile-choice input {
        accent-color: #22c55e;
    }

    .profile-choice:hover {
        border-color: rgba(255, 255, 255, 0.25);
    }

    .document-preview {
        margin-top: 12px;
        padding: 12px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.08);
    }

    .document-preview img {
        width: 100%;
        max-height: 180px;
        object-fit: cover;
        border-radius: 12px;
        display: block;
    }

    .document-preview a {
        color: #7dd3fc;
        font-weight: 600;
        text-decoration: none;
    }

    .document-preview a:hover {
        text-decoration: underline;
    }

    @media (max-width: 767px) {
        .page-wrapper {
            padding-top: 18px;
        }
    }
</style>

<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center g-3">
                <div class="col">
                    <h1 class="page-title text-white">Profile Settings</h1>
                    <p class="text-white mb-0">Buyer account ko dynamic aur editable bana diya gaya hai.</p>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="profile-hero-card p-4 p-lg-5 mb-4">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <div class="d-flex align-items-center gap-3">
                        <div class="profile-avatar-lg">
                            @if ($profileImageUrl)
                                <img src="{{ $profileImageUrl }}" alt="Profile Image">
                            @else
                                {{ $initials ?: 'BY' }}
                            @endif
                        </div>
                        <div>
                            <span class="badge bg-info-subtle text-info mb-2">Buyer Account</span>
                            <h3 class="text-white mb-1">{{ $buyer->name }}</h3>
                            <p class="text-white-50 mb-1">{{ $buyer->email }}</p>
                            <p class="text-white-50 mb-0">{{ $buyer->company_legal_name ?: 'Company not added yet' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="profile-stat">
                                <small class="text-white-50 d-block mb-1">Phone</small>
                                <strong class="text-white">{{ $buyer->phone ?: 'Not added' }}</strong>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="profile-stat">
                                <small class="text-white-50 d-block mb-1">Country</small>
                                <strong class="text-white">{{ $buyer->country ?: 'Not added' }}</strong>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="profile-stat">
                                <small class="text-white-50 d-block mb-1">Bank Country</small>
                                <strong class="text-white">{{ $buyer->bank_country ?: 'Not added' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('buyer.profile-settings.update') }}" enctype="multipart/form-data">
            @csrf
            <div class="profile-form-card p-4 p-lg-5">
                <div class="row g-4">
                    <div class="col-12">
                        <h5 class="text-white mb-1">Personal Info</h5>
                        <p class="text-white-50 mb-0">Buyer register form ke same personal fields.</p>
                    </div>
                    <div class="col-lg-6">
                        <label class="profile-label" for="name">Full Name</label>
                        <input id="name" type="text" name="name" class="form-control" value="{{ old('name', $buyer->name) }}" required>
                    </div>
                    <div class="col-lg-6">
                        <label class="profile-label" for="email">Email</label>
                        <input id="email" type="email" name="email" class="form-control" value="{{ old('email', $buyer->email) }}" required>
                    </div>
                    <div class="col-lg-6">
                        <label class="profile-label" for="phone">Phone</label>
                        <input id="phone" type="text" name="phone" class="form-control" value="{{ old('phone', $buyer->phone) }}">
                    </div>
                    <div class="col-lg-6">
                        <label class="profile-label" for="profile_image">Profile Image</label>
                        <input id="profile_image" type="file" name="profile_image" class="form-control">
                        @if ($buyer->profile_image)
                            <div class="document-preview">
                                <small class="text-white-50 d-block mb-2">Current profile image</small>
                                <img src="{{ $profileImageUrl }}" alt="Profile Preview">
                                <div class="mt-2">
                                    <a href="{{ $profileImageUrl }}" target="_blank">View current image</a>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-lg-6">
                        <label class="profile-label" for="job_title">Job Title</label>
                        <input id="job_title" type="text" name="job_title" class="form-control" value="{{ old('job_title', $buyer->job_title) }}">
                    </div>

                    <div class="col-12 pt-3">
                        <h5 class="text-white mb-1">Company Info</h5>
                        <p class="text-white-50 mb-0">Same company fields as buyer registration.</p>
                    </div>
                    <div class="col-lg-6">
                        <label class="profile-label" for="company_legal_name">Company Legal Name</label>
                        <input id="company_legal_name" type="text" name="company_legal_name" class="form-control" value="{{ old('company_legal_name', $buyer->company_legal_name) }}">
                    </div>
                    <div class="col-lg-6">
                        <label class="profile-label" for="city">City</label>
                        <input id="city" type="text" name="city" class="form-control" value="{{ old('city', $buyer->city) }}">
                    </div>
                    <div class="col-12">
                        <label class="profile-label" for="business_address">Business Address</label>
                        <input id="business_address" type="text" name="business_address" class="form-control" value="{{ old('business_address', $buyer->business_address) }}">
                    </div>
                    <div class="col-lg-6">
                        <label class="profile-label" for="country">Country</label>
                        <select id="country" name="country" class="form-select">
                            <option value="">Select Country</option>
                            @foreach ($profileOptions['country'] as $option)
                                <option value="{{ $option }}" @selected(old('country', $buyer->country) === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label class="profile-label" for="website">Website</label>
                        <input id="website" type="text" name="website" class="form-control" value="{{ old('website', $buyer->website) }}">
                    </div>
                    <div class="col-lg-6">
                        <label class="profile-label" for="company_registration_number">Company Registration Number</label>
                        <input id="company_registration_number" type="text" name="company_registration_number" class="form-control" value="{{ old('company_registration_number', $buyer->company_registration_number) }}">
                    </div>
                    <div class="col-12 pt-3">
                        <h5 class="text-white mb-1">Business Type & Preferences</h5>
                        <p class="text-white-50 mb-0">Same options as buyer registration form.</p>
                    </div>

                    @foreach ([
                        'business_type' => 'Business Type',
                        'interested_in' => 'Interested In',
                        'preferred_delivery' => 'Preferred Delivery'
                    ] as $field => $label)
                        <div class="col-lg-6">
                            <label class="profile-label">{{ $label }}</label>
                            <div class="row g-3">
                                @foreach ($profileOptions[$field] as $option)
                                    @php
                                        $selectedValues = old($field, $buyer->{$field} ?? []);
@endphp

@php
    $documentFields = [
        'company_registration_file' => 'Company Registration',
        'id_file' => 'ID Document',
        'import_license_file' => 'Import License',
    ];
@endphp
                                    <div class="col-sm-6">
                                        <label class="profile-choice">
                                            <input
                                                type="checkbox"
                                                name="{{ $field }}[]"
                                                value="{{ $option }}"
                                                {{ in_array($option, $selectedValues, true) ? 'checked' : '' }}
                                            >
                                            <span>{{ $option }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <div class="col-lg-6">
                        <label class="profile-label" for="monthly_volume">Monthly Volume</label>
                        <select id="monthly_volume" name="monthly_volume" class="form-select">
                            <option value="">Select Monthly Volume</option>
                            @foreach ($profileOptions['monthly_volume'] as $option)
                                <option value="{{ $option }}" @selected(old('monthly_volume', $buyer->monthly_volume) === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 pt-3">
                        <h5 class="text-white mb-1">KYC & Payment</h5>
                        <p class="text-white-50 mb-0">Registration wale payment aur document fields bhi include kiye gaye hain.</p>
                    </div>

                    <div class="col-lg-6">
                        <label class="profile-label">Preferred Payment</label>
                        <div class="row g-3">
                            @foreach ($profileOptions['preferred_payment'] as $option)
                                @php
                                    $selectedValues = old('preferred_payment', $buyer->preferred_payment ?? []);
                                @endphp
                                <div class="col-sm-6">
                                    <label class="profile-choice">
                                        <input
                                            type="checkbox"
                                            name="preferred_payment[]"
                                            value="{{ $option }}"
                                            {{ in_array($option, $selectedValues, true) ? 'checked' : '' }}
                                        >
                                        <span>{{ $option }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <label class="profile-label" for="bank_country">Bank Country</label>
                        <input id="bank_country" type="text" name="bank_country" class="form-control" value="{{ old('bank_country', $buyer->bank_country) }}">
                    </div>

                    <div class="col-lg-4">
                        <label class="profile-label" for="company_registration_file">Upload Company Registration</label>
                        <input id="company_registration_file" type="file" name="company_registration_file" class="form-control">
                        @if ($buyer->company_registration_file)
                            @php
                                $filePath = $buyer->company_registration_file;
                                $fileUrl = asset('storage/' . $filePath);
                                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                            @endphp
                            <div class="document-preview">
                                <small class="text-white-50 d-block mb-2">Current document</small>
                                @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true))
                                    <img src="{{ $fileUrl }}" alt="Company Registration Preview">
                                @endif
                                <div class="{{ in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true) ? 'mt-2' : '' }}">
                                    <a href="{{ $fileUrl }}" target="_blank">View {{ basename($filePath) }}</a>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-lg-4">
                        <label class="profile-label" for="id_file">Upload ID</label>
                        <input id="id_file" type="file" name="id_file" class="form-control">
                        @if ($buyer->id_file)
                            @php
                                $filePath = $buyer->id_file;
                                $fileUrl = asset('storage/' . $filePath);
                                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                            @endphp
                            <div class="document-preview">
                                <small class="text-white-50 d-block mb-2">Current document</small>
                                @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true))
                                    <img src="{{ $fileUrl }}" alt="ID Preview">
                                @endif
                                <div class="{{ in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true) ? 'mt-2' : '' }}">
                                    <a href="{{ $fileUrl }}" target="_blank">View {{ basename($filePath) }}</a>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-lg-4">
                        <label class="profile-label" for="import_license_file">Import License</label>
                        <input id="import_license_file" type="file" name="import_license_file" class="form-control">
                        @if ($buyer->import_license_file)
                            @php
                                $filePath = $buyer->import_license_file;
                                $fileUrl = asset('storage/' . $filePath);
                                $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                            @endphp
                            <div class="document-preview">
                                <small class="text-white-50 d-block mb-2">Current document</small>
                                @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true))
                                    <img src="{{ $fileUrl }}" alt="Import License Preview">
                                @endif
                                <div class="{{ in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true) ? 'mt-2' : '' }}">
                                    <a href="{{ $fileUrl }}" target="_blank">View {{ basename($filePath) }}</a>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-12">
                        <div class="row g-3">
                            <div class="col-lg-4">
                                <label class="profile-choice">
                                    <input type="checkbox" name="is_registered_business" value="1" @checked(old('is_registered_business', $buyer->is_registered_business))>
                                    <span>I confirm this is a registered business</span>
                                </label>
                            </div>
                            <div class="col-lg-4">
                                <label class="profile-choice">
                                    <input type="checkbox" name="accepted_terms" value="1" @checked(old('accepted_terms', $buyer->accepted_terms))>
                                    <span>I accept Terms & Auction Rules</span>
                                </label>
                            </div>
                            <div class="col-lg-4">
                                <label class="profile-choice">
                                    <input type="checkbox" name="bank_transfer_validated" value="1" @checked(old('bank_transfer_validated', $buyer->bank_transfer_validated))>
                                    <span>Bank transfers validated by Admin</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2 pt-2">
                        <a href="{{ route('buyer.dashboard') }}" class="btn btn-outline-light">Back</a>
                        <button type="submit" class="btn btn-success px-4">Save Changes</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@include('bid_web.buyer.include.footer')

@include('bid_web.seller.include.header')

@include('bid_web.seller.include.side_menu')

@php
    $initials = collect(explode(' ', trim($seller->name ?? 'Seller')))
        ->filter()
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->take(2)
        ->implode('');
    $profileImageUrl = $seller->profile_image ? asset('storage/' . $seller->profile_image) : null;
    $documentFields = [
        'trade_license_file' => 'Trade License',
        'facility_photos_file' => 'Facility Photos',
        'certificates_file' => 'Certificates',
    ];
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
</style>

<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center g-3">
                <div class="col">
                    <h1 class="page-title text-white">Profile Settings</h1>
                   
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
                                {{ $initials ?: 'SL' }}
                            @endif
                        </div>
                        <div>
                            <span class="badge bg-info-subtle text-info mb-2">Seller Account</span>
                            <h3 class="text-white mb-1">{{ $seller->name }}</h3>
                            <p class="text-white-50 mb-1">{{ $seller->email }}</p>
                            <p class="text-white-50 mb-0">{{ $seller->company_name ?: 'Company not added yet' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="profile-stat">
                                <small class="text-white-50 d-block mb-1">Phone</small>
                                <strong class="text-white">{{ $seller->phone ?: 'Not added' }}</strong>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="profile-stat">
                                <small class="text-white-50 d-block mb-1">Country</small>
                                <strong class="text-white">{{ $seller->country ?: 'Not added' }}</strong>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="profile-stat">
                                <small class="text-white-50 d-block mb-1">Port</small>
                                <strong class="text-white">{{ $seller->landing_site_port ?: 'Not added' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('seller.profile-settings.update') }}" enctype="multipart/form-data">
            @csrf
            <div class="profile-form-card p-4 p-lg-5">
                <div class="row g-4">
                    <div class="col-12">
                        <h5 class="text-white mb-1">Contact Info</h5>
                        <p class="text-white-50 mb-0">Seller register form ke same contact fields.</p>
                    </div>
                    <div class="col-lg-6">
                        <label class="profile-label" for="name">Full Name</label>
                        <input id="name" type="text" name="name" class="form-control" value="{{ old('name', $seller->name) }}" required>
                    </div>
                    <div class="col-lg-6">
                        <label class="profile-label" for="email">Email</label>
                        <input id="email" type="email" name="email" class="form-control" value="{{ old('email', $seller->email) }}" required>
                    </div>
                    <div class="col-lg-6">
                        <label class="profile-label" for="phone">Phone</label>
                        <input id="phone" type="text" name="phone" class="form-control" value="{{ old('phone', $seller->phone) }}" required>
                    </div>
                    <div class="col-lg-6">
                        <label class="profile-label" for="profile_image">Profile Image</label>
                        <input id="profile_image" type="file" name="profile_image" class="form-control">
                        @if ($seller->profile_image)
                            <div class="document-preview">
                                <small class="text-white-50 d-block mb-2">Current profile image</small>
                                <img src="{{ $profileImageUrl }}" alt="Profile Preview">
                            </div>
                        @endif
                    </div>

                    <div class="col-12 pt-3">
                        <h5 class="text-white mb-1">Company Info</h5>
                        <p class="text-white-50 mb-0">Seller registration ke company aur location fields editable hain.</p>
                    </div>
                    <div class="col-lg-6">
                        <label class="profile-label" for="company_name">Company Name</label>
                        <input id="company_name" type="text" name="company_name" class="form-control" value="{{ old('company_name', $seller->company_name) }}" required>
                    </div>
                    <div class="col-lg-6">
                        <label class="profile-label" for="landing_site_port">Landing Site / Port</label>
                        <input id="landing_site_port" type="text" name="landing_site_port" class="form-control" value="{{ old('landing_site_port', $seller->landing_site_port) }}" required>
                    </div>
                    <div class="col-12">
                        <label class="profile-label" for="address">Address</label>
                        <input id="address" type="text" name="address" class="form-control" value="{{ old('address', $seller->address) }}" required>
                    </div>
                    <div class="col-lg-6">
                        <label class="profile-label" for="country">Country</label>
                        <select id="country" name="country" class="form-select" required>
                            <option value="">Select Country</option>
                            @foreach ($profileOptions['country'] as $option)
                                <option value="{{ $option }}" @selected(old('country', $seller->country) === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <label class="profile-label" for="estimated_weekly_volume">Estimated Weekly Volume</label>
                        <select id="estimated_weekly_volume" name="estimated_weekly_volume" class="form-select">
                            <option value="">Select Weekly Volume</option>
                            @foreach ($profileOptions['estimated_weekly_volume'] as $option)
                                <option value="{{ $option }}" @selected(old('estimated_weekly_volume', $seller->estimated_weekly_volume) === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 pt-3">
                        <h5 class="text-white mb-1">Capabilities</h5>
                        <p class="text-white-50 mb-0">Supply type aur processing status bhi dynamic ho gaye hain.</p>
                    </div>

                    <div class="col-lg-6">
                        <label class="profile-label">Supply Type</label>
                        <div class="row g-3">
                            @foreach ($profileOptions['supply_type'] as $option)
                                @php $selectedValues = old('supply_type', $seller->supply_type ?? []); @endphp
                                <div class="col-sm-6">
                                    <label class="profile-choice">
                                        <input type="checkbox" name="supply_type[]" value="{{ $option }}" {{ in_array($option, $selectedValues, true) ? 'checked' : '' }}>
                                        <span>{{ $option }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <label class="profile-label">Processing Status</label>
                        <div class="row g-3">
                            @foreach ($profileOptions['processing_status'] as $option)
                                @php $selectedValues = old('processing_status', $seller->processing_status ?? []); @endphp
                                <div class="col-sm-6">
                                    <label class="profile-choice">
                                        <input type="checkbox" name="processing_status[]" value="{{ $option }}" {{ in_array($option, $selectedValues, true) ? 'checked' : '' }}>
                                        <span>{{ $option }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-12 pt-3">
                        <h5 class="text-white mb-1">Compliance Documents</h5>
                        <p class="text-white-50 mb-0">Current uploaded seller documents view aur replace dono kar sakte ho.</p>
                    </div>

                    @foreach ($documentFields as $field => $label)
                        <div class="col-lg-4">
                            <label class="profile-label" for="{{ $field }}">{{ $label }}</label>
                            <input id="{{ $field }}" type="file" name="{{ $field }}" class="form-control">
                            @if ($seller->{$field})
                                @php
                                    $filePath = $seller->{$field};
                                    $fileUrl = asset('storage/' . $filePath);
                                    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                @endphp
                                <div class="document-preview">
                                    <small class="text-white-50 d-block mb-2">Current document</small>
                                    @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true))
                                        <img src="{{ $fileUrl }}" alt="{{ $label }} Preview">
                                    @endif
                                    <div class="{{ in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true) ? 'mt-2' : '' }}">
                                        <a href="{{ $fileUrl }}" target="_blank">View {{ basename($filePath) }}</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <div class="col-12 d-flex justify-content-end gap-2 pt-2">
                        <a href="{{ route('seller.dashboard') }}" class="btn btn-outline-light">Back</a>
                        <button type="submit" class="btn btn-success px-4">Save Changes</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@include('bid_web.seller.include.footer')

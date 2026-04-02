@include('bid_admin.admin.include.header')

@include('bid_admin.admin.include.side_menu')

@php
    $initials = collect(explode(' ', trim($admin->name ?? 'Administrator')))
        ->filter()
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->take(2)
        ->implode('');
@endphp

<style>
    .profile-card {
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
        background: linear-gradient(135deg, #0f766e, #2563eb);
        color: #fff;
        font-size: 2rem;
        font-weight: 700;
        letter-spacing: 0.08em;
    }

    .profile-label {
        display: block;
        margin-bottom: 8px;
        color: #dbeafe;
        font-weight: 600;
    }

    .profile-card .form-control {
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 14px;
        min-height: 48px;
    }
    .profile-stat {
        min-height: 100%;
        padding: 18px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.08);
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

        <div class="profile-card p-4 p-lg-5 mb-4">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <div class="d-flex align-items-center gap-3">
                        <div class="profile-avatar-lg">{{ $initials ?: 'AD' }}</div>
                        <div>
                            <span class="badge bg-info-subtle text-info mb-2">Admin Account</span>
                            <h3 class="text-white mb-1">{{ $admin->name }}</h3>
                            <p class="text-white-50 mb-1">{{ $admin->email }}</p>
                            <p class="text-white-50 mb-0">{{ ucfirst($admin->role ?? 'admin') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="profile-stat">
                                <small class="text-white-50 d-block mb-1">Role</small>
                                <strong class="text-white">{{ ucfirst($admin->role ?? 'admin') }}</strong>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="profile-stat">
                                <small class="text-white-50 d-block mb-1">Account ID</small>
                                <strong class="text-white">#{{ $admin->id }}</strong>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="profile-stat">
                                <small class="text-white-50 d-block mb-1">Status</small>
                                <strong class="text-white">Active</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.account-settings.update') }}">
            @csrf
            <div class="profile-card p-4 p-lg-5">
                <div class="row g-4">
                    <div class="col-12">
                        <h5 class="text-white mb-1">Account Info</h5>
                        <p class="text-white-50 mb-0">Admin profile details yahan se update kar sakte hain.</p>
                    </div>
                    <div class="col-lg-6">
                        <label class="profile-label" for="name">Full Name</label>
                        <input id="name" type="text" name="name" class="form-control" value="{{ old('name', $admin->name) }}" required>
                    </div>
                    <div class="col-lg-6">
                        <label class="profile-label" for="email">Email</label>
                        <input id="email" type="email" name="email" class="form-control" value="{{ old('email', $admin->email) }}" required>
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2 pt-2">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light">Back</a>
                        <button type="submit" class="btn btn-success px-4">Save Changes</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@include('bid_admin.admin.include.footer')

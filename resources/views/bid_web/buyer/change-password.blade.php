@include('bid_web.buyer.include.header')

@include('bid_web.buyer.include.side_menu')

<style>
    .password-card {
        background: rgba(12, 74, 110, 0.82);
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(10px);
        box-shadow: 0 18px 45px rgba(2, 8, 23, 0.18);
    }

    .password-card .form-control {
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 14px;
        min-height: 48px;
    }

    .password-label {
        display: block;
        margin-bottom: 8px;
        color: #dbeafe;
        font-weight: 600;
    }
</style>

<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center g-3">
                <div class="col">
                    <h1 class="page-title text-white">Change Password</h1>
                   
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="password-card p-4 p-lg-5">
            <form method="POST" action="{{ route('buyer.change-password.update') }}">
                @csrf
                <div class="row g-4">
                    <div class="col-lg-4">
                        <label class="password-label" for="current_password">Current Password</label>
                        <input id="current_password" type="password" name="current_password" class="form-control" autocomplete="current-password" required>
                    </div>
                    <div class="col-lg-4">
                        <label class="password-label" for="new_password">New Password</label>
                        <input id="new_password" type="password" name="new_password" class="form-control" autocomplete="new-password" required>
                    </div>
                    <div class="col-lg-4">
                        <label class="password-label" for="new_password_confirmation">Confirm New Password</label>
                        <input id="new_password_confirmation" type="password" name="new_password_confirmation" class="form-control" autocomplete="new-password" required>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2 pt-2">
                        <a href="{{ route('buyer.profile-settings') }}" class="btn btn-outline-light">Back to Profile</a>
                        <button type="submit" class="btn btn-success px-4">Update Password</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@include('bid_web.buyer.include.footer')

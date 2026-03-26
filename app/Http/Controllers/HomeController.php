<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('bid_web.home.index');
    }

    public function login(): View
    {
        return view('bid_web.home.login');
    }

    public function loginStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'login_as' => ['required', 'in:buyer,seller'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $user = DB::table('users')
            ->where('email', $validated['email'])
            ->first();

        if (! $user) {
            return back()
                ->withErrors(['email' => 'Invalid email or password.'])
                ->withInput($request->except('password'));
        }

        $passwordMatches = Hash::check($validated['password'], (string) $user->password)
            || hash_equals((string) $user->password, $validated['password']);

        if (! $passwordMatches) {
            return back()
                ->withErrors(['email' => 'Invalid email or password.'])
                ->withInput($request->except('password'));
        }

        if (($user->type ?? null) !== $validated['login_as']) {
            return back()
                ->withErrors(['login_as' => 'Please choose the correct login type for this account.'])
                ->withInput($request->except('password'));
        }

        $request->session()->regenerate();
        $request->session()->put('logged_user', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'type' => $user->type,
            'profile_image' => $user->profile_image ?? null,
        ]);

        if ($user->type === 'seller') {
            return redirect()->route('seller.dashboard');
        }

        return redirect()->route('buyer.dashboard');
    }

    public function buyerRegister(): View
    {
        return view('bid_web.home.buyer-register');
    }

    public function buyerRegisterStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'company_legal_name' => ['required', 'string', 'max:255'],
            'business_type' => ['required', 'array', 'min:1'],
            'business_type.*' => ['string', 'max:100'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'business_address' => ['nullable', 'string', 'max:500'],
            'country' => ['nullable', 'string', 'max:100'],
            'website' => ['nullable', 'string', 'max:255'],
            'company_registration_number' => ['nullable', 'string', 'max:255'],
            'interested_in' => ['nullable', 'array'],
            'interested_in.*' => ['string', 'max:30'],
            'monthly_volume' => ['nullable', 'string', 'max:50'],
            'preferred_delivery' => ['nullable', 'array'],
            'preferred_delivery.*' => ['string', 'max:30'],
            'preferred_payment' => ['nullable', 'array'],
            'preferred_payment.*' => ['string', 'max:50'],
            'bank_country' => ['nullable', 'string', 'max:100'],
            'company_registration_file' => ['nullable', 'file', 'max:5120'],
            'id_file' => ['nullable', 'file', 'max:5120'],
            'import_license_file' => ['nullable', 'file', 'max:5120'],
        ]);

        $companyRegistrationFile = $request->file('company_registration_file');
        $idFile = $request->file('id_file');
        $importLicenseFile = $request->file('import_license_file');

        $insertData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'job_title' => $validated['job_title'] ?? null,
            'company_legal_name' => $validated['company_legal_name'],
            'city' => $validated['city'] ?? null,
            'business_address' => $validated['business_address'] ?? null,
            'country' => $validated['country'] ?? null,
            'website' => $validated['website'] ?? null,
            'company_registration_number' => $validated['company_registration_number'] ?? null,
            'business_type' => json_encode($validated['business_type']),
            'interested_in' => isset($validated['interested_in']) ? json_encode($validated['interested_in']) : null,
            'monthly_volume' => $validated['monthly_volume'] ?? null,
            'preferred_delivery' => isset($validated['preferred_delivery']) ? json_encode($validated['preferred_delivery']) : null,
            'preferred_payment' => isset($validated['preferred_payment']) ? json_encode($validated['preferred_payment']) : null,
            'bank_country' => $validated['bank_country'] ?? null,
            'company_registration_file' => $companyRegistrationFile ? $companyRegistrationFile->store('buyer-kyc', 'public') : null,
            'id_file' => $idFile ? $idFile->store('buyer-kyc', 'public') : null,
            'import_license_file' => $importLicenseFile ? $importLicenseFile->store('buyer-kyc', 'public') : null,
            'is_registered_business' => $request->boolean('is_registered_business'),
            'accepted_terms' => $request->boolean('accepted_terms'),
            'bank_transfer_validated' => $request->boolean('bank_transfer_validated'),
            'type' => 'buyer',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('users', 'status')) {
            $insertData['status'] = 'pending';
        }

        DB::table('users')->insert($insertData);

        return redirect()
            ->route('home.buyer-register')
            ->with('success', 'Buyer registration submitted successfully.');
    }

    public function sellerRegister(): View
    {
        return view('bid_web.home.seller-register');
    }

    public function sellerRegisterStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'company_name' => ['required', 'string', 'max:255'],
            'landing_site_port' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'country' => ['required', 'string', 'max:100'],
            'supply_type' => ['nullable', 'array'],
            'supply_type.*' => ['string', 'max:50'],
            'processing_status' => ['nullable', 'array'],
            'processing_status.*' => ['string', 'max:50'],
            'estimated_weekly_volume' => ['nullable', 'string', 'max:100'],
            'trade_license_file' => ['required', 'file', 'max:5120'],
            'facility_photos_file' => ['nullable', 'file', 'max:5120'],
            'certificates_file' => ['nullable', 'file', 'max:5120'],
        ]);

        $tradeLicenseFile = $request->file('trade_license_file');
        $facilityPhotosFile = $request->file('facility_photos_file');
        $certificatesFile = $request->file('certificates_file');

        $insertData = [
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'company_name' => $validated['company_name'],
            'landing_site_port' => $validated['landing_site_port'],
            'address' => $validated['address'],
            'country' => $validated['country'],
            'supply_type' => isset($validated['supply_type']) ? json_encode($validated['supply_type']) : null,
            'processing_status' => isset($validated['processing_status']) ? json_encode($validated['processing_status']) : null,
            'estimated_weekly_volume' => $validated['estimated_weekly_volume'] ?? null,
            'trade_license_file' => $tradeLicenseFile ? $tradeLicenseFile->store('seller-kyc', 'public') : null,
            'facility_photos_file' => $facilityPhotosFile ? $facilityPhotosFile->store('seller-kyc', 'public') : null,
            'certificates_file' => $certificatesFile ? $certificatesFile->store('seller-kyc', 'public') : null,
            'type' => 'seller',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('users', 'status')) {
            $insertData['status'] = 'pending';
        }

        DB::table('users')->insert($insertData);

        return redirect()
            ->route('home.seller-register')
            ->with('success', 'Seller registration submitted successfully.');
    }
}

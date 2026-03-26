<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index(): View
    {
        return view('bid_admin.admin.index');
    }

    public function dashboard(): View
    {
        return view('bid_admin.admin.dashboard');
    }

    public function liveAuction(): View
    {
        return view('bid_admin.admin.live-auction');
    }

    public function upcomingAuction(): View
    {
        return view('bid_admin.admin.upcoming-auction');
    }

    public function lotManagement(): View
    {
        return view('bid_admin.admin.lot-management');
    }

    public function createLot(): View
    {
        return view('bid_admin.admin.create-lot');
    }

    public function lotDetails(): View
    {
        return view('bid_admin.admin.lot-details');
    }

    public function buyers(): View
    {
        return view('bid_admin.admin.buyers');
    }

    public function buyerDetails(): View
    {
        return view('bid_admin.admin.buyer-details');
    }

    public function addBuyer(): View
    {
        return view('bid_admin.admin.add-buyer');
    }

    public function sellers(): View
    {
        return view('bid_admin.admin.sellers');
    }

    public function sellerDetails(): View
    {
        return view('bid_admin.admin.seller-details');
    }

    public function addSeller(): View
    {
        return view('bid_admin.admin.add-seller');
    }

    public function financeOverview(): View
    {
        return view('bid_admin.admin.finance-overview');
    }

    public function notifications(): View
    {
        return $this->renderOrDashboard('bid_admin.admin.notifications');
    }

    public function accountSettings(): View
    {
        return $this->renderOrDashboard('bid_admin.admin.account-settings');
    }

    public function login(): View
    {
        return view('bid_admin.admin.index');
    }

    public function loginStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'login_type' => ['required', 'in:admin,qc'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $admin = Admin::where('email', $validated['email'])->first();

        if (! $admin || ! Hash::check($validated['password'], $admin->password)) {
            return back()
                ->withErrors(['email' => 'Invalid credentials for the provided email.'])
                ->withInput($request->except('password'));
        }

        if ($admin->role !== $validated['login_type']) {
            return back()
                ->withErrors(['login_type' => 'Please select the role assigned to this account.'])
                ->withInput($request->except('password'));
        }

        $request->session()->regenerate();
        $request->session()->put('admin_user', [
            'id' => $admin->id,
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => $admin->role,
        ]);

        $route = $admin->role === 'qc' ? 'qc.dashboard' : 'admin.dashboard';

        return redirect()->route($route);
    }

    public function transactions(): View
    {
        return view('bid_admin.admin.transactions');
    }

    public function bankTransfer(): View
    {
        return view('bid_admin.admin.bank-transfer');
    }

    public function riskMonitoring(): View
    {
        return view('bid_admin.admin.risk-monitoring');
    }

    public function alets(): View
    {
        return view('bid_admin.admin.alets');
    }

    public function settings(): View
    {
        return view('bid_admin.admin.settings');
    }

    private function renderOrDashboard(string $view): View
    {
        if (view()->exists($view)) {
            return view($view);
        }

        return view('bid_admin.admin.dashboard');
    }

}

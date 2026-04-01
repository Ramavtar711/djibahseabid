<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\AppNotification;
use App\Models\Bid;
use App\Models\Lot;
use App\Models\Settlement;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function index(): View
    {
        return view('bid_admin.admin.index');
    }

    public function dashboard(): View
    {
        return view('bid_admin.admin.dashboard', $this->buildDashboardData());
    }

    public function dashboardData(): JsonResponse
    {
        return response()->json($this->buildDashboardData());
    }

    public function liveAuction(): View
    {
        return view('bid_admin.admin.live-auction', $this->buildLiveAuctionData());
    }

    public function liveAuctionData(): JsonResponse
    {
        return response()->json($this->buildLiveAuctionData());
    }

    public function extendLiveAuction(Lot $lot): JsonResponse
    {
        if (! $lot->isCurrentlyActive()) {
            return response()->json([
                'message' => 'Only active auctions can be extended.',
            ], 422);
        }

        $baseEndAt = $lot->auction_end_at && $lot->auction_end_at->isFuture()
            ? $lot->auction_end_at->copy()
            : now();

        $lot->update([
            'auction_end_at' => $baseEndAt->addMinutes(5),
        ]);

        return response()->json([
            'message' => 'Auction extended by 5 minutes.',
            'auction_end_at' => optional($lot->fresh()->auction_end_at)->toIso8601String(),
        ]);
    }

    public function pauseLiveAuction(Lot $lot): JsonResponse
    {
        if (! $lot->isCurrentlyActive()) {
            return response()->json([
                'message' => 'Only active auctions can be paused.',
            ], 422);
        }

        $now = now();
        $currentEndAt = $lot->auction_end_at && $lot->auction_end_at->isFuture()
            ? $lot->auction_end_at->copy()
            : $now->copy()->addMinutes((int) ($lot->auction_duration_minutes ?? 30));

        $remainingSeconds = max(60, $now->diffInSeconds($currentEndAt, false));
        $resumeAt = $now->copy()->addMinutes(5);

        $lot->update([
            'status' => 'scheduled auction',
            'auction_start_at' => $resumeAt,
            'auction_end_at' => $resumeAt->copy()->addSeconds($remainingSeconds),
        ]);

        return response()->json([
            'message' => 'Auction paused for 5 minutes.',
        ]);
    }

    public function stopLiveAuction(Lot $lot): JsonResponse
    {
        if (! $lot->isCurrentlyActive()) {
            return response()->json([
                'message' => 'Only active auctions can be stopped.',
            ], 422);
        }

        $winnerBid = Bid::query()
            ->where('lot_id', $lot->id)
            ->orderByDesc('amount')
            ->orderBy('created_at')
            ->with('buyer')
            ->first();

        if ($winnerBid) {
            $finalPrice = (float) $winnerBid->amount;

            $lot->update([
                'status' => 'sold',
                'winner_id' => $winnerBid->buyer_id,
                'final_price' => $finalPrice,
                'settlement_status' => 'pending',
                'auction_end_at' => now(),
            ]);

            Bid::query()->where('lot_id', $lot->id)->update(['status' => 'lost']);
            $winnerBid->update(['status' => 'won']);

            $this->createAuctionSettlement($lot, $winnerBid, $finalPrice);
            $this->notifyAuctionStoppedSold($lot, $winnerBid);

            return response()->json([
                'message' => 'Auction stopped and awarded to the highest bidder.',
            ]);
        }

        $lot->update([
            'status' => 'unsold',
            'winner_id' => null,
            'final_price' => null,
            'settlement_status' => null,
            'auction_end_at' => now(),
        ]);

        $this->notifyAuctionStoppedUnsold($lot);

        return response()->json([
            'message' => 'Auction stopped with no bids. Lot marked as unsold.',
        ]);
    }
    public function upcomingAuction(): View
    {
        $this->syncScheduledAuctionsToActive();

        $now = now();
        $todayStart = $now->copy()->startOfDay();
        $tomorrowStart = $todayStart->copy()->addDay();

        $upcomingLots = Lot::query()
            ->currentlyUpcoming()
            ->with('seller')
            ->orderBy('auction_start_at')
            ->paginate(12)
            ->withQueryString();

        $upcomingLots->getCollection()->transform(function (Lot $lot) use ($now) {
            $lot->starts_in_label = $this->formatCountdown($lot->auction_start_at, $now, false);

            return $lot;
        });

        return view('bid_admin.admin.upcoming-auction', [
            'systemStatus' => Lot::query()->currentlyActive()->count() > 0 ? 'LIVE' : 'STANDBY',
            'liveAuctionsCount' => Lot::query()->currentlyActive()->count(),
            'upcomingAuctionsCount' => Lot::query()->currentlyUpcoming()->count(),
            'revenueToday' => (float) Settlement::query()->whereBetween('created_at', [$todayStart, $tomorrowStart])->sum('amount'),
            'registeredBuyersCount' => User::query()->where('type', 'buyer')->count(),
            'upcomingLots' => $upcomingLots,
        ]);
    }
    public function lotManagement(Request $request): View
    {
        $this->syncScheduledAuctionsToActive();

        $now = now();
        $todayStart = $now->copy()->startOfDay();
        $tomorrowStart = $todayStart->copy()->addDay();

        $speciesFilter = trim((string) $request->query('species', ''));
        $statusFilter = trim((string) $request->query('status', ''));
        $sellerFilter = trim((string) $request->query('seller', ''));
        $dateFilter = trim((string) $request->query('date', ''));

        $baseQuery = Lot::query()->with('seller')->withCount('bids');

        $lots = (clone $baseQuery)
            ->when($speciesFilter !== '', fn ($query) => $query->where('species', $speciesFilter))
            ->when($statusFilter !== '', fn ($query) => $query->whereRaw('LOWER(status) = ?', [Str::lower($statusFilter)]))
            ->when($sellerFilter !== '', fn ($query) => $query->where('seller_id', $sellerFilter))
            ->when($dateFilter !== '', fn ($query) => $query->whereDate('auction_end_at', $dateFilter))
            ->orderByRaw('CASE WHEN auction_end_at IS NULL THEN 1 ELSE 0 END')
            ->orderByDesc('auction_end_at')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $lots->getCollection()->transform(function (Lot $lot) {
            $lot->setAttribute('increment_amount', max(50, round(((float) ($lot->starting_price ?? 0)) * 0.05, 2)));

            return $lot;
        });

        $speciesOptions = Lot::query()
            ->whereNotNull('species')
            ->distinct()
            ->orderBy('species')
            ->pluck('species');

        $statusOptions = Lot::query()
            ->select('status')
            ->distinct()
            ->pluck('status')
            ->filter()
            ->map(fn ($status) => Str::lower(trim((string) $status)))
            ->unique()
            ->values();

        $sellerOptions = User::query()
            ->where('type', 'seller')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('bid_admin.admin.lot-management', [
            'systemStatus' => Lot::query()->currentlyActive()->count() > 0 ? 'LIVE' : 'STANDBY',
            'liveAuctionsCount' => Lot::query()->currentlyActive()->count(),
            'upcomingAuctionsCount' => Lot::query()->currentlyUpcoming()->count(),
            'revenueToday' => (float) Settlement::query()->whereBetween('created_at', [$todayStart, $tomorrowStart])->sum('amount'),
            'registeredBuyersCount' => User::query()->where('type', 'buyer')->count(),
            'lots' => $lots,
            'speciesOptions' => $speciesOptions,
            'statusOptions' => $statusOptions,
            'sellerOptions' => $sellerOptions,
        ]);
    }
    public function createLot(): View
    {
        $this->syncScheduledAuctionsToActive();

        return view('bid_admin.admin.create-lot', $this->buildAdminLotFormData(new Lot()));
    }

    public function editLot(Lot $lot): View
    {
        $this->syncScheduledAuctionsToActive();

        return view('bid_admin.admin.create-lot', $this->buildAdminLotFormData($lot, true));
    }

    public function storeLot(Request $request): RedirectResponse
    {
        $validated = $this->validateAdminLotRequest($request);

        $seller = User::query()
            ->where('id', $validated['seller_id'])
            ->where('type', 'seller')
            ->first();

        if (! $seller) {
            return back()
                ->withInput()
                ->withErrors([
                    'seller_id' => 'Selected seller is invalid.',
                ]);
        }

        $lotData = [
            'seller_id' => $seller->id,
            'title' => $validated['title'],
            'species' => $validated['species'],
            'quantity' => $validated['quantity'],
            'starting_price' => $validated['starting_price'],
            'harvest_date' => $validated['harvest_date'],
            'storage_temperature' => $validated['storage_temperature'],
            'notes' => $validated['notes'],
            'status' => 'draft',
        ];

        if ($request->hasFile('product_image')) {
            $lotData['image_path'] = $request->file('product_image')->store('lot-images', 'public');
        }

        if ($request->hasFile('health_certificate')) {
            $lotData['health_certificate_path'] = $request->file('health_certificate')->store('lot-documents', 'public');
        }

        if ($request->hasFile('additional_documents')) {
            $lotData['documents_path'] = $request->file('additional_documents')->store('lot-documents', 'public');
        }

        $lot = Lot::create($lotData);

        $this->notifyQcAndAdminsNewLot($lot);
        $this->notifySellerLotCreatedByAdmin($lot);
        $this->notifyBuyersNewLot($lot);

        return redirect()
            ->route('admin.lot-management')
            ->with('success', 'Lot created successfully for the selected seller.');
    }

    public function updateLot(Request $request, Lot $lot): RedirectResponse
    {
        $validated = $this->validateAdminLotRequest($request, false);

        $seller = User::query()
            ->where('id', $validated['seller_id'])
            ->where('type', 'seller')
            ->first();

        if (! $seller) {
            return back()
                ->withInput()
                ->withErrors([
                    'seller_id' => 'Selected seller is invalid.',
                ]);
        }

        $lotData = [
            'seller_id' => $seller->id,
            'title' => $validated['title'],
            'species' => $validated['species'],
            'quantity' => $validated['quantity'],
            'starting_price' => $validated['starting_price'],
            'harvest_date' => $validated['harvest_date'],
            'storage_temperature' => $validated['storage_temperature'],
            'notes' => $validated['notes'],
        ];

        if ($request->hasFile('product_image')) {
            $this->deleteStoredFiles([$lot->image_path]);
            $lotData['image_path'] = $request->file('product_image')->store('lot-images', 'public');
        }

        if ($request->hasFile('health_certificate')) {
            $this->deleteStoredFiles([$lot->health_certificate_path]);
            $lotData['health_certificate_path'] = $request->file('health_certificate')->store('lot-documents', 'public');
        }

        if ($request->hasFile('additional_documents')) {
            $this->deleteStoredFiles($this->extractStoredPaths($lot->documents_path));
            $lotData['documents_path'] = $request->file('additional_documents')->store('lot-documents', 'public');
        }

        $lot->update($lotData);

        return redirect()
            ->route('admin.lot-management')
            ->with('success', 'Lot updated successfully.');
    }

    public function destroyLot(Lot $lot): RedirectResponse
    {
        if ($lot->bids()->exists()) {
            return redirect()
                ->route('admin.lot-management')
                ->with('error', 'This lot cannot be deleted because bids have already been placed on it.');
        }

        $this->deleteStoredFiles([
            $lot->image_path,
            $lot->health_certificate_path,
            ...$this->extractStoredPaths($lot->documents_path),
        ]);

        $lot->delete();

        return redirect()
            ->route('admin.lot-management')
            ->with('success', 'Lot deleted successfully.');
    }
    public function lotDetails(Request $request): View
    {
        $lotId = $request->query('lot');

        $lotQuery = Lot::query()->with(['seller', 'winner']);
        $lot = $lotId
            ? $lotQuery->find($lotId)
            : $lotQuery->latest()->first();

        $highestBidAmount = $lot
            ? (float) (Bid::query()->where('lot_id', $lot->id)->max('amount') ?? $lot->starting_price ?? 0)
            : 0.0;

        $bidHistory = $lot
            ? Bid::query()
                ->with('buyer')
                ->where('lot_id', $lot->id)
                ->latest()
                ->take(10)
                ->get()
            : collect();

        return view('bid_admin.admin.lot-details', [
            'lot' => $lot,
            'highestBidAmount' => $highestBidAmount,
            'bidHistory' => $bidHistory,
        ]);
    }
    public function buyers(): View { return view('bid_admin.admin.buyers'); }
    public function buyerDetails(): View { return view('bid_admin.admin.buyer-details'); }
    public function addBuyer(): View { return view('bid_admin.admin.add-buyer'); }
    public function sellers(): View { return view('bid_admin.admin.sellers'); }
    public function sellerDetails(): View { return view('bid_admin.admin.seller-details'); }
    public function addSeller(): View { return view('bid_admin.admin.add-seller'); }
    public function financeOverview(): View { return view('bid_admin.admin.finance-overview'); }
    public function notifications(): View { return $this->renderOrDashboard('bid_admin.admin.notifications'); }
    public function accountSettings(): View { return $this->renderOrDashboard('bid_admin.admin.account-settings'); }
    public function login(): View { return view('bid_admin.admin.index'); }

    public function notificationData(Request $request): JsonResponse
    {
        if (! Schema::hasTable('app_notifications') || ! Schema::hasColumn('app_notifications', 'admin_id')) {
            return response()->json([
                'unread_count' => 0,
                'items' => [],
            ]);
        }

        $adminId = $this->resolveAdminId();

        if (! $adminId) {
            return response()->json([
                'unread_count' => 0,
                'items' => [],
            ]);
        }

        $limit = (int) $request->query('limit', 5);
        $limit = max(1, min($limit, 50));

        $notifications = AppNotification::query()
            ->where('admin_id', $adminId)
            ->latest()
            ->take($limit)
            ->get();

        $items = $notifications->map(function (AppNotification $notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'type' => $notification->type,
                'is_read' => $notification->is_read,
                'time' => optional($notification->created_at)->diffForHumans() ?? '',
                'created_at' => optional($notification->created_at)->toIso8601String(),
                'url' => data_get($notification->data, 'url'),
                'lot_id' => data_get($notification->data, 'lot_id'),
            ];
        })->values();

        $unreadCount = AppNotification::query()
            ->where('admin_id', $adminId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'unread_count' => $unreadCount,
            'items' => $items,
        ]);
    }

    public function markNotificationRead(Request $request): JsonResponse
    {
        if (! Schema::hasTable('app_notifications') || ! Schema::hasColumn('app_notifications', 'admin_id')) {
            return response()->json([
                'unread_count' => 0,
            ]);
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $adminId = $this->resolveAdminId();

        if ($adminId) {
            AppNotification::query()
                ->where('id', $validated['id'])
                ->where('admin_id', $adminId)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
        }

        $unreadCount = $adminId
            ? AppNotification::query()->where('admin_id', $adminId)->where('is_read', false)->count()
            : 0;

        return response()->json([
            'unread_count' => $unreadCount,
        ]);
    }

    public function markAllNotificationsRead(): JsonResponse
    {
        if (! Schema::hasTable('app_notifications') || ! Schema::hasColumn('app_notifications', 'admin_id')) {
            return response()->json([
                'unread_count' => 0,
            ]);
        }

        $adminId = $this->resolveAdminId();

        if ($adminId) {
            AppNotification::query()
                ->where('admin_id', $adminId)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
        }

        return response()->json([
            'unread_count' => 0,
        ]);
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
            return back()->withErrors(['email' => 'Invalid credentials for the provided email.'])->withInput($request->except('password'));
        }

        if ($admin->role !== $validated['login_type']) {
            return back()->withErrors(['login_type' => 'Please select the role assigned to this account.'])->withInput($request->except('password'));
        }

        $request->session()->regenerate();
        $request->session()->put('admin_user', ['id' => $admin->id, 'name' => $admin->name, 'email' => $admin->email, 'role' => $admin->role]);

        return redirect()->route($admin->role === 'qc' ? 'qc.dashboard' : 'admin.dashboard');
    }

    public function transactions(): View { return view('bid_admin.admin.transactions'); }
    public function bankTransfer(): View { return view('bid_admin.admin.bank-transfer'); }
    public function riskMonitoring(): View { return view('bid_admin.admin.risk-monitoring'); }
    public function alets(): View { return view('bid_admin.admin.alets'); }
    public function settings(): View { return view('bid_admin.admin.settings'); }

    private function buildDashboardData(): array
    {
        $this->syncScheduledAuctionsToActive();

        $now = now();
        $todayStart = $now->copy()->startOfDay();
        $tomorrowStart = $todayStart->copy()->addDay();
        $sevenDaysAgo = $now->copy()->subDays(6)->startOfDay();

        $liveAuctionsCount = Lot::query()->currentlyActive()->count();
        $upcomingAuctionsCount = Lot::query()->currentlyUpcoming()->count();
        $revenueToday = (float) Settlement::query()->whereBetween('created_at', [$todayStart, $tomorrowStart])->sum('amount');
        $registeredBuyersCount = User::query()->where('type', 'buyer')->count();
        $grossRevenue = (float) Settlement::query()->sum('amount');
        $volumeSoldKg = (float) Lot::query()->where('status', 'sold')->sum('quantity');
        $endingSoonCount = Lot::query()->currentlyActive()->whereNotNull('auction_end_at')->whereBetween('auction_end_at', [$now, $now->copy()->addDay()])->count();
        $pendingQcCount = Lot::query()->where('status', 'pending qc')->count();
        $failedPaymentsCount = Settlement::query()->where('status', 'failed')->count();

        $activeLots = Lot::query()->currentlyActive()->with('seller')->orderBy('auction_end_at')->take(6)->get();
        $activeLotIds = $activeLots->pluck('id')->values();

        $highestBids = $activeLotIds->isEmpty() ? collect() : Bid::query()
            ->selectRaw('lot_id, MAX(amount) as max_amount')
            ->whereIn('lot_id', $activeLotIds)
            ->groupBy('lot_id')
            ->pluck('max_amount', 'lot_id');

        $bidCounts = $activeLotIds->isEmpty() ? collect() : Bid::query()
            ->selectRaw('lot_id, COUNT(*) as total')
            ->whereIn('lot_id', $activeLotIds)
            ->groupBy('lot_id')
            ->pluck('total', 'lot_id');

        $activeLots = $activeLots->map(function (Lot $lot) use ($highestBids, $bidCounts, $now) {
            $lot->current_bid = (float) ($highestBids[$lot->id] ?? $lot->starting_price ?? 0);
            $lot->bids_count = (int) ($bidCounts[$lot->id] ?? 0);
            $lot->time_left_label = $this->formatCountdown($lot->auction_end_at, $now);
            $lot->time_left_class = $this->auctionUrgencyClass($lot->auction_end_at, $now);
            return $lot;
        });

        $upcomingLots = Lot::query()->currentlyUpcoming()->with('seller')->orderBy('auction_start_at')->take(3)->get()
            ->map(function (Lot $lot) use ($now) {
                $lot->starts_in_label = $this->formatCountdown($lot->auction_start_at, $now, false);
                return $lot;
            });

        $topSpeciesByVolume = Lot::query()->where('status', 'sold')->whereNotNull('species')
            ->selectRaw('species, SUM(quantity) as total_quantity')
            ->groupBy('species')->orderByDesc('total_quantity')->take(3)->get();

        $topSpeciesByValue = Settlement::query()->join('lots', 'settlements.lot_id', '=', 'lots.id')
            ->whereNotNull('lots.species')
            ->selectRaw('lots.species as species, SUM(settlements.amount) as total_amount')
            ->groupBy('lots.species')->orderByDesc('total_amount')->take(3)->get();

        $topBuyers = Settlement::query()->join('users', 'settlements.buyer_id', '=', 'users.id')
            ->leftJoin('lots', 'settlements.lot_id', '=', 'lots.id')
            ->whereNotNull('settlements.buyer_id')
            ->selectRaw('users.id, users.name, users.country, SUM(settlements.amount) as total_amount, COUNT(settlements.id) as wins_count, COALESCE(SUM(lots.quantity), 0) as total_quantity')
            ->groupBy('users.id', 'users.name', 'users.country')
            ->orderByDesc('total_amount')->take(5)->get();

        $topSellers = DB::table('users')->leftJoin('lots', 'users.id', '=', 'lots.seller_id')
            ->leftJoin('settlements', 'lots.id', '=', 'settlements.lot_id')
            ->where('users.type', 'seller')
            ->selectRaw('users.id, users.name, COUNT(DISTINCT lots.id) as listed_count, COUNT(DISTINCT CASE WHEN lots.status = "sold" THEN lots.id END) as sold_count, COALESCE(SUM(lots.quantity), 0) as listed_quantity, COALESCE(SUM(CASE WHEN lots.status = "sold" THEN lots.quantity ELSE 0 END), 0) as sold_quantity, COALESCE(SUM(settlements.amount), 0) as total_amount')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_amount')->take(5)->get()
            ->map(function ($seller) {
                $seller->success_rate = (int) ($seller->listed_count ?? 0) > 0 ? round(($seller->sold_count / $seller->listed_count) * 100) : 0;
                return $seller;
            });

        $alerts = collect();
        if ($pendingQcCount > 0) {
            $alerts->push(['title' => 'QC Pending', 'message' => "{$pendingQcCount} lots are waiting for QC validation.", 'time' => 'Now', 'icon' => 'bi-exclamation-triangle-fill', 'tone' => 'warning']);
        }
        if ($lotWithoutBids = $activeLots->firstWhere('bids_count', 0)) {
            $alerts->push(['title' => 'No Bids', 'message' => "Lot #{$lotWithoutBids->id} has not received any bids yet.", 'time' => 'Live', 'icon' => 'bi-lightning-fill', 'tone' => 'danger']);
        }
        if ($failedPaymentsCount > 0) {
            $alerts->push(['title' => 'Payment Failed', 'message' => "{$failedPaymentsCount} settlements are marked as failed.", 'time' => 'Today', 'icon' => 'bi-x-octagon-fill', 'tone' => 'danger']);
        }
        if ($upcomingAuctionsCount > 0) {
            $alerts->push(['title' => 'Upcoming Auctions', 'message' => "{$upcomingAuctionsCount} auctions are scheduled to start soon.", 'time' => 'Scheduled', 'icon' => 'bi-clock-fill', 'tone' => 'info']);
        }
        if ($alerts->isEmpty()) {
            $alerts->push(['title' => 'Operations Normal', 'message' => 'No priority alerts at the moment.', 'time' => 'Live', 'icon' => 'bi-check-circle-fill', 'tone' => 'success']);
        }

        $dateLabels = collect(range(0, 6))->map(fn ($offset) => $sevenDaysAgo->copy()->addDays($offset));
        $volumeByDay = Lot::query()->where('status', 'sold')->where('created_at', '>=', $sevenDaysAgo)->selectRaw('DATE(created_at) as day, COALESCE(SUM(quantity), 0) as total')->groupBy('day')->pluck('total', 'day');
        $transactionsByDay = Settlement::query()->where('created_at', '>=', $sevenDaysAgo)->selectRaw('DATE(created_at) as day, COALESCE(SUM(amount), 0) as total')->groupBy('day')->pluck('total', 'day');
        $bidsByDay = Bid::query()->where('created_at', '>=', $sevenDaysAgo)->selectRaw('DATE(created_at) as day, COALESCE(SUM(amount), 0) as total')->groupBy('day')->pluck('total', 'day');

        return [
            'systemStatus' => $liveAuctionsCount > 0 ? 'LIVE' : 'STANDBY',
            'liveAuctionsCount' => $liveAuctionsCount,
            'upcomingAuctionsCount' => $upcomingAuctionsCount,
            'revenueToday' => $revenueToday,
            'registeredBuyersCount' => $registeredBuyersCount,
            'grossRevenue' => $grossRevenue,
            'volumeSoldKg' => $volumeSoldKg,
            'endingSoonCount' => $endingSoonCount,
            'activeLots' => $activeLots->map(function (Lot $lot) {
                return [
                    'id' => $lot->id,
                    'title' => $lot->title,
                    'species' => $lot->species,
                    'image_url' => $lot->image_url,
                    'seller_name' => $lot->seller?->name ?: 'Seller',
                    'current_bid' => round((float) $lot->current_bid, 2),
                    'bids_count' => (int) $lot->bids_count,
                    'quantity' => round((float) $lot->quantity, 2),
                    'time_left_label' => $lot->time_left_label,
                    'time_left_class' => $lot->time_left_class,
                ];
            })->values()->all(),
            'upcomingLots' => $upcomingLots->map(function (Lot $lot) {
                return [
                    'id' => $lot->id,
                    'title' => $lot->title,
                    'species' => $lot->species,
                    'image_url' => $lot->image_url,
                    'status' => $lot->status,
                    'starts_in_label' => $lot->starts_in_label,
                ];
            })->values()->all(),
            'alerts' => $alerts->values()->all(),
            'topSpeciesByVolume' => $topSpeciesByVolume->map(fn ($item) => [
                'species' => $item->species,
                'total_quantity' => round((float) $item->total_quantity, 2),
            ])->values()->all(),
            'topSpeciesByValue' => $topSpeciesByValue->map(fn ($item) => [
                'species' => $item->species,
                'total_amount' => round((float) $item->total_amount, 2),
            ])->values()->all(),
            'topBuyers' => $topBuyers->map(fn ($buyer) => [
                'name' => $buyer->name,
                'country' => $buyer->country,
                'total_amount' => round((float) $buyer->total_amount, 2),
                'wins_count' => (int) $buyer->wins_count,
                'total_quantity' => round((float) $buyer->total_quantity, 2),
            ])->values()->all(),
            'topSellers' => $topSellers->map(fn ($seller) => [
                'name' => $seller->name,
                'total_amount' => round((float) $seller->total_amount, 2),
                'success_rate' => (int) $seller->success_rate,
                'listed_quantity' => round((float) $seller->listed_quantity, 2),
                'sold_quantity' => round((float) $seller->sold_quantity, 2),
            ])->values()->all(),
            'chartLabels' => $dateLabels->map(fn ($date) => $date->format('D'))->all(),
            'volumeChartData' => $dateLabels->map(fn ($date) => round((float) ($volumeByDay[$date->toDateString()] ?? 0), 2))->all(),
            'transactionBarData' => $dateLabels->map(fn ($date) => round((float) ($transactionsByDay[$date->toDateString()] ?? 0), 2))->all(),
            'transactionLineData' => $dateLabels->map(fn ($date) => round((float) ($bidsByDay[$date->toDateString()] ?? 0), 2))->all(),
            'revenueChartData' => $dateLabels->map(fn ($date) => round((float) ($transactionsByDay[$date->toDateString()] ?? 0), 2))->all(),
        ];
    }

    private function buildLiveAuctionData(): array
    {
        $this->syncScheduledAuctionsToActive();

        $dashboardData = $this->buildDashboardData();
        $now = now();

        $activeLots = Lot::query()
            ->currentlyActive()
            ->with(['seller', 'winner'])
            ->orderBy('auction_end_at')
            ->take(24)
            ->get();

        $lotIds = $activeLots->pluck('id')->values();

        $highestBids = $lotIds->isEmpty()
            ? collect()
            : Bid::query()
                ->selectRaw('lot_id, MAX(amount) as max_amount')
                ->whereIn('lot_id', $lotIds)
                ->groupBy('lot_id')
                ->pluck('max_amount', 'lot_id');

        $bidCounts = $lotIds->isEmpty()
            ? collect()
            : Bid::query()
                ->selectRaw('lot_id, COUNT(*) as total')
                ->whereIn('lot_id', $lotIds)
                ->groupBy('lot_id')
                ->pluck('total', 'lot_id');

        $items = $activeLots->map(function (Lot $lot) use ($highestBids, $bidCounts, $now) {
            $currentBid = (float) ($highestBids[$lot->id] ?? $lot->starting_price ?? 0);

            return [
                'id' => $lot->id,
                'title' => $lot->title,
                'species' => $lot->species,
                'seller_name' => $lot->seller?->name ?: 'Seller',
                'image_url' => $lot->image_url,
                'current_bid' => round($currentBid, 2),
                'quantity' => round((float) ($lot->quantity ?? 0), 2),
                'bid_count' => (int) ($bidCounts[$lot->id] ?? 0),
                'time_left_label' => $this->formatCountdown($lot->auction_end_at, $now),
                'time_left_class' => $this->auctionUrgencyClass($lot->auction_end_at, $now),
                'status' => $lot->status,
                'detail_url' => route('admin.lot-details', ['lot' => $lot->id]),
                'extend_url' => route('admin.live-auction.extend', ['lot' => $lot->id]),
                'pause_url' => route('admin.live-auction.pause', ['lot' => $lot->id]),
                'stop_url' => route('admin.live-auction.stop', ['lot' => $lot->id]),
            ];
        })->values()->all();

        return array_merge($dashboardData, [
            'liveAuctionItems' => $items,
        ]);
    }

    private function renderOrDashboard(string $view): View
    {
        return view()->exists($view) ? view($view) : $this->dashboard();
    }

    private function resolveAdminId(): ?int
    {
        $adminId = session('admin_user.id');
        $adminRole = session('admin_user.role');

        if ($adminId && $adminRole === 'admin') {
            return (int) $adminId;
        }

        $fallback = DB::table('admins')
            ->where('role', 'admin')
            ->value('id');

        return $fallback ? (int) $fallback : null;
    }

    private function createAuctionSettlement(Lot $lot, Bid $winnerBid, float $finalPrice): void
    {
        if (! Schema::hasTable('settlements')) {
            return;
        }

        $commissionRate = 5.00;
        $quantity = (float) ($lot->quantity ?? 0);
        $grossAmount = round($finalPrice * $quantity, 2);
        $commissionAmount = round($grossAmount * ($commissionRate / 100), 2);
        $netAmount = round($grossAmount - $commissionAmount, 2);

        $existing = Settlement::query()->where('lot_id', $lot->id)->first();

        if ($existing) {
            $existing->update([
                'seller_id' => $lot->seller_id,
                'buyer_id' => $winnerBid->buyer_id,
                'amount' => $grossAmount,
                'commission_amount' => $commissionAmount,
                'net_amount' => $netAmount,
                'commission_rate' => $commissionRate,
                'status' => $existing->status ?: 'pending',
            ]);
            return;
        }

        Settlement::create([
            'lot_id' => $lot->id,
            'seller_id' => $lot->seller_id,
            'buyer_id' => $winnerBid->buyer_id,
            'amount' => $grossAmount,
            'commission_amount' => $commissionAmount,
            'net_amount' => $netAmount,
            'commission_rate' => $commissionRate,
            'status' => 'pending',
        ]);
    }

    private function notifyAuctionStoppedSold(Lot $lot, Bid $winnerBid): void
    {
        if (! Schema::hasTable('app_notifications') || ! Schema::hasColumn('app_notifications', 'user_id')) {
            return;
        }

        $lotLabel = '#LOT-' . str_pad((string) $lot->id, 4, '0', STR_PAD_LEFT);
        $amountLabel = '$' . number_format((float) $winnerBid->amount, 2) . '/kg';
        $buyerName = $winnerBid->buyer?->name ?? 'Buyer';

        if ($lot->seller_id) {
            AppNotification::create([
                'user_id' => $lot->seller_id,
                'title' => 'Auction Stopped And Sold',
                'message' => "{$lotLabel} was closed by admin and sold to {$buyerName} at {$amountLabel}.",
                'type' => 'success',
                'data' => [
                    'lot_id' => $lot->id,
                    'event' => 'admin_stop_sold',
                    'url' => route('seller.sold-lots'),
                ],
            ]);
        }

        if ($winnerBid->buyer_id) {
            AppNotification::create([
                'user_id' => $winnerBid->buyer_id,
                'title' => 'Auction Awarded',
                'message' => "Admin closed {$lotLabel} and awarded it to you at {$amountLabel}.",
                'type' => 'success',
                'data' => [
                    'lot_id' => $lot->id,
                    'event' => 'admin_stop_awarded',
                    'url' => route('buyer.won-auction'),
                ],
            ]);
        }
    }

    private function notifyAuctionStoppedUnsold(Lot $lot): void
    {
        if (! Schema::hasTable('app_notifications') || ! Schema::hasColumn('app_notifications', 'user_id')) {
            return;
        }

        if (! $lot->seller_id) {
            return;
        }

        $lotLabel = '#LOT-' . str_pad((string) $lot->id, 4, '0', STR_PAD_LEFT);

        AppNotification::create([
            'user_id' => $lot->seller_id,
            'title' => 'Auction Stopped',
            'message' => "{$lotLabel} was stopped by admin and marked unsold.",
            'type' => 'warning',
            'data' => [
                'lot_id' => $lot->id,
                'event' => 'admin_stop_unsold',
                'url' => route('seller.sold-lots'),
            ],
        ]);
    }

    private function notifyQcAndAdminsNewLot(Lot $lot): void
    {
        if (! Schema::hasTable('app_notifications') || ! Schema::hasColumn('app_notifications', 'admin_id')) {
            return;
        }

        $admins = Admin::query()
            ->whereIn('role', ['qc', 'admin'])
            ->get(['id', 'role']);

        if ($admins->isEmpty()) {
            return;
        }

        $lotLabel = '#LOT-' . str_pad((string) $lot->id, 4, '0', STR_PAD_LEFT);
        $sellerName = $lot->seller?->name
            ?? DB::table('users')->where('id', $lot->seller_id)->value('name')
            ?? 'Seller';

        foreach ($admins as $admin) {
            $url = $admin->role === 'admin'
                ? route('admin.lot-details', ['lot' => $lot->id])
                : route('qc.lot-subimitted-details', ['lot' => $lot->id]);

            AppNotification::create([
                'admin_id' => $admin->id,
                'title' => 'New Lot Added By Admin',
                'message' => "Admin created {$lotLabel} for {$sellerName}.",
                'type' => 'info',
                'data' => [
                    'lot_id' => $lot->id,
                    'event' => 'admin_created_lot',
                    'url' => $url,
                ],
            ]);
        }
    }

    private function notifySellerLotCreatedByAdmin(Lot $lot): void
    {
        if (! Schema::hasTable('app_notifications') || ! Schema::hasColumn('app_notifications', 'user_id')) {
            return;
        }

        if (! $lot->seller_id) {
            return;
        }

        $lotLabel = '#LOT-' . str_pad((string) $lot->id, 4, '0', STR_PAD_LEFT);
        $url = route('seller.lot-list') . '?search=' . urlencode((string) $lot->id);

        AppNotification::create([
            'user_id' => $lot->seller_id,
            'title' => 'Lot Added By Admin',
            'message' => "Admin created {$lotLabel} under your seller account.",
            'type' => 'info',
            'data' => [
                'lot_id' => $lot->id,
                'event' => 'admin_created_lot_seller',
                'url' => $url,
            ],
        ]);
    }

    private function notifyBuyersNewLot(Lot $lot): void
    {
        if (! Schema::hasTable('app_notifications') || ! Schema::hasColumn('app_notifications', 'user_id')) {
            return;
        }

        $buyerIds = $this->resolveTargetBuyerIdsForLot($lot);

        if (! $buyerIds) {
            return;
        }

        $lotLabel = '#LOT-' . str_pad((string) $lot->id, 4, '0', STR_PAD_LEFT);
        $lotName = $lot->species ?: ($lot->title ?: 'new seafood lot');
        $sellerName = $lot->seller?->name
            ?? DB::table('users')->where('id', $lot->seller_id)->value('name')
            ?? 'Seller';
        $url = route('buyer.upcoming-auction');

        foreach ($buyerIds as $buyerId) {
            AppNotification::create([
                'user_id' => $buyerId,
                'title' => 'New Lot Available',
                'message' => "{$sellerName} has a new lot {$lotLabel} ({$lotName}). Check upcoming auctions.",
                'type' => 'info',
                'data' => [
                    'lot_id' => $lot->id,
                    'event' => 'admin_created_lot_buyer',
                    'url' => $url,
                ],
            ]);
        }
    }

    private function resolveTargetBuyerIdsForLot(Lot $lot): array
    {
        $buyers = User::query()
            ->where('type', 'buyer')
            ->get(['id', 'interested_in']);

        if ($buyers->isEmpty()) {
            return [];
        }

        $allBuyerIds = $buyers->pluck('id')->map(fn ($id) => (int) $id)->all();
        $interestHint = $this->inferBuyerInterestHint($lot);

        $preferenceMatchedIds = $buyers
            ->filter(function (User $buyer) use ($interestHint) {
                $interests = collect($this->decodeJsonArray($buyer->interested_in ?? null))
                    ->map(fn ($value) => Str::lower(trim((string) $value)))
                    ->filter()
                    ->values();

                if ($interests->isEmpty()) {
                    return false;
                }

                if ($interests->contains('both')) {
                    return true;
                }

                return $interestHint ? $interests->contains($interestHint) : false;
            })
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $speciesMatchedIds = [];

        if (filled($lot->species)) {
            $speciesMatchedIds = DB::table('bids')
                ->join('lots', 'lots.id', '=', 'bids.lot_id')
                ->whereIn('bids.buyer_id', $allBuyerIds)
                ->whereRaw('LOWER(lots.species) = ?', [Str::lower(trim((string) $lot->species))])
                ->distinct()
                ->pluck('bids.buyer_id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        $targetedBuyerIds = array_values(array_unique(array_merge($preferenceMatchedIds, $speciesMatchedIds)));

        return $targetedBuyerIds ?: $allBuyerIds;
    }

    private function inferBuyerInterestHint(Lot $lot): ?string
    {
        $haystack = Str::lower(trim(implode(' ', array_filter([
            (string) ($lot->title ?? ''),
            (string) ($lot->species ?? ''),
            (string) ($lot->notes ?? ''),
            (string) ($lot->storage_temperature ?? ''),
        ]))));

        if ($haystack === '') {
            return null;
        }

        if (Str::contains($haystack, ['frozen', 'iqf'])) {
            return 'frozen';
        }

        if (Str::contains($haystack, ['fresh', 'ice', 'chilled'])) {
            return 'fresh';
        }

        return null;
    }

    private function decodeJsonArray(mixed $value): array
    {
        if (is_array($value)) {
            return array_values(array_filter($value, fn ($item) => filled($item)));
        }

        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded)
            ? array_values(array_filter($decoded, fn ($item) => filled($item)))
            : [];
    }

    private function syncScheduledAuctionsToActive(): void
    {
        $now = now();

        Lot::query()
            ->where('status', 'scheduled auction')
            ->whereNotNull('auction_start_at')
            ->where('auction_start_at', '<=', $now)
            ->where(function ($query) use ($now) {
                $query->whereNull('auction_end_at')
                    ->orWhere('auction_end_at', '>', $now);
            })
            ->update([
                'status' => 'active',
                'updated_at' => $now,
            ]);
    }

    private function formatCountdown($target, $now, bool $futureOnly = true): string
    {
        if (! $target) {
            return $futureOnly ? 'Live' : 'Not scheduled';
        }
        if ($futureOnly && ! $target->isFuture()) {
            return 'Closing';
        }

        $seconds = abs($now->diffInSeconds($target, false));
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $remainingSeconds = $seconds % 60;

        return $hours > 0
            ? sprintf('%02d:%02d:%02d', $hours, $minutes, $remainingSeconds)
            : sprintf('%02d:%02d', $minutes, $remainingSeconds);
    }

    private function auctionUrgencyClass($auctionEndAt, $now): string
    {
        if (! $auctionEndAt) {
            return 'text-success';
        }
        $seconds = $now->diffInSeconds($auctionEndAt, false);
        if ($seconds <= 300) {
            return 'text-danger';
        }
        if ($seconds <= 1800) {
            return 'text-warning';
        }
        return 'text-success';
    }

    private function validateAdminLotRequest(Request $request, bool $isCreate = true): array
    {
        return $request->validate([
            'seller_id' => ['required', 'integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'species' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'starting_price' => ['required', 'numeric', 'min:0'],
            'harvest_date' => ['required', 'date'],
            'storage_temperature' => ['required', 'string', 'max:50'],
            'notes' => ['required', 'string', 'max:1000'],
            'product_image' => [$isCreate ? 'required' : 'nullable', 'image', 'max:5120'],
            'health_certificate' => [$isCreate ? 'required' : 'nullable', 'file', 'max:5120'],
            'additional_documents' => [$isCreate ? 'required' : 'nullable', 'file', 'max:5120'],
        ], [
            'seller_id.required' => 'Seller is required.',
            'title.required' => 'Lot title is required.',
            'species.required' => 'Species is required.',
            'quantity.required' => 'Quantity is required.',
            'starting_price.required' => 'Starting price is required.',
            'harvest_date.required' => 'Harvest date is required.',
            'storage_temperature.required' => 'Storage temperature is required.',
            'notes.required' => 'Lot notes are required.',
            'product_image.required' => 'Product image is required.',
            'health_certificate.required' => 'Health certificate is required.',
            'additional_documents.required' => 'Additional documents are required.',
        ]);
    }

    private function buildAdminLotFormData(Lot $lot, bool $isEditMode = false): array
    {
        $now = now();
        $todayStart = $now->copy()->startOfDay();
        $tomorrowStart = $todayStart->copy()->addDay();

        return [
            'systemStatus' => Lot::query()->currentlyActive()->count() > 0 ? 'LIVE' : 'STANDBY',
            'liveAuctionsCount' => Lot::query()->currentlyActive()->count(),
            'upcomingAuctionsCount' => Lot::query()->currentlyUpcoming()->count(),
            'revenueToday' => (float) Settlement::query()->whereBetween('created_at', [$todayStart, $tomorrowStart])->sum('amount'),
            'registeredBuyersCount' => User::query()->where('type', 'buyer')->count(),
            'sellerOptions' => User::query()->where('type', 'seller')->orderBy('name')->get(['id', 'name']),
            'lot' => $lot,
            'isEditMode' => $isEditMode,
        ];
    }

    private function extractStoredPaths(?string $value): array
    {
        return collect(explode(',', (string) $value))
            ->map(fn ($path) => trim($path))
            ->filter()
            ->values()
            ->all();
    }

    private function deleteStoredFiles(array $paths): void
    {
        foreach ($paths as $path) {
            $normalizedPath = trim((string) $path);

            if ($normalizedPath === '') {
                continue;
            }

            Storage::disk('public')->delete($normalizedPath);
        }
    }
}

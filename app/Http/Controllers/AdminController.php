<?php

namespace App\Http\Controllers;

use App\Events\AdminDashboardUpdated;
use App\Models\Admin;
use App\Models\AppNotification;
use App\Models\Bid;
use App\Models\Lot;
use App\Models\Settlement;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\SettlementLifecycleService;

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
        return response()
            ->json($this->buildDashboardData())
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
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
        event(new AdminDashboardUpdated('auction-extended', $lot->id));

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
        event(new AdminDashboardUpdated('auction-paused', $lot->id));

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
            event(new AdminDashboardUpdated('auction-stopped-sold', $lot->id));

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
        event(new AdminDashboardUpdated('auction-stopped-unsold', $lot->id));

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
        event(new AdminDashboardUpdated('lot-created', $lot->id));

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
        event(new AdminDashboardUpdated('lot-updated', $lot->id));

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
        event(new AdminDashboardUpdated('lot-deleted'));

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
    public function buyers(): View
    {
        $dashboardData = $this->buildDashboardData();

        $buyers = User::query()
            ->where('type', 'buyer')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (User $buyer) {
                $buyer->normalized_status = Str::lower(trim((string) ($buyer->status ?: 'pending')));
                $buyer->profile_image_url = $buyer->profile_image
                    ? asset('storage/' . ltrim((string) $buyer->profile_image, '/'))
                    : 'https://via.placeholder.com/120x120?text=Buyer';

                return $buyer;
            });

        return view('bid_admin.admin.buyers', array_merge($dashboardData, [
            'buyers' => $buyers,
        ]));
    }
    public function buyerDetails(Request $request): View
    {
        $buyerId = (int) $request->query('buyer', 0);

        $buyer = User::query()
            ->where('type', 'buyer')
            ->findOrFail($buyerId);

        $wallet = $buyer->wallet;
        $settlements = Settlement::query()
            ->where('buyer_id', $buyer->id)
            ->with('lot')
            ->latest('id')
            ->get();

        $bids = Bid::query()
            ->where('buyer_id', $buyer->id)
            ->with('lot')
            ->latest('id')
            ->get();

        $walletTransactions = $wallet
            ? $wallet->transactions()->latest('id')->take(5)->get()
            : collect();

        $totalAuctionPurchase = (float) $settlements->sum('amount');
        $totalPaidAmount = (float) $settlements->where('status', 'paid')->sum('amount');
        $pendingSettlement = (float) $settlements->whereIn('status', ['pending', 'processing'])->sum('amount');
        $averageBidValue = (float) ($bids->count() > 0 ? ($bids->avg('amount') ?? 0) : 0);

        $speciesStats = DB::table('bids')
            ->join('lots', 'lots.id', '=', 'bids.lot_id')
            ->where('bids.buyer_id', $buyer->id)
            ->selectRaw('COALESCE(lots.species, "Unknown") as species, COUNT(*) as total_bids')
            ->groupBy('lots.species')
            ->orderByDesc('total_bids')
            ->get();

        $mostPurchasedFish = $speciesStats->first();

        $hourCounts = $bids->groupBy(function (Bid $bid) {
            return (int) optional($bid->created_at)->format('G');
        })->map->count();

        $mostActiveHour = $hourCounts->sortDesc()->keys()->first();
        $preferredAuctionTime = $mostActiveHour === null
            ? 'N/A'
            : sprintf('%02d:00-%02d:59', $mostActiveHour, $mostActiveHour);

        $activeBidsThisMonth = $bids->filter(function (Bid $bid) {
            return optional($bid->created_at)?->isCurrentMonth();
        })->count();

        $buyerRanking = Settlement::query()
            ->selectRaw('buyer_id, COALESCE(SUM(amount), 0) as total_amount')
            ->whereNotNull('buyer_id')
            ->groupBy('buyer_id')
            ->orderByDesc('total_amount')
            ->pluck('buyer_id')
            ->values();

        $rankingPosition = $buyerRanking->search($buyer->id);
        $buyerRankLabel = $rankingPosition === false ? '-' : 'Top ' . ((int) $rankingPosition + 1);

        $paymentReliability = $settlements->count() > 0
            ? round(($settlements->where('status', 'paid')->count() / $settlements->count()) * 100)
            : 0;

        $recentTransactions = $settlements->take(5)->map(function (Settlement $settlement) {
            $provider = Str::of((string) ($settlement->payment_provider ?: 'manual'))
                ->replace('_', ' ')
                ->title();

            $settlement->provider_label = (string) $provider;
            $settlement->status_badge_class = match (Str::lower((string) $settlement->status)) {
                'paid' => 'bg-success',
                'processing' => 'bg-warning text-dark',
                'failed' => 'bg-danger',
                default => 'bg-secondary',
            };

            return $settlement;
        });

        $auctionHistory = $bids
            ->unique('lot_id')
            ->take(6)
            ->map(function (Bid $bid) use ($buyer, $settlements) {
                $lot = $bid->lot;
                $wonSettlement = $settlements->firstWhere('lot_id', $bid->lot_id);

                return [
                    'lot' => $lot,
                    'bid_amount' => (float) $bid->amount,
                    'won' => (bool) $wonSettlement,
                    'won_amount' => (float) ($wonSettlement?->amount ?? 0),
                ];
            })
            ->filter(fn ($item) => $item['lot'])
            ->values();

        $auctionsParticipated = $bids->pluck('lot_id')->unique()->count();
        $auctionsWon = $settlements->pluck('lot_id')->unique()->count();
        $winRate = $auctionsParticipated > 0 ? round(($auctionsWon / $auctionsParticipated) * 100) : 0;
        $totalFishPurchased = (float) $settlements->filter(fn (Settlement $settlement) => $settlement->lot)->sum(fn (Settlement $settlement) => (float) ($settlement->lot->quantity ?? 0));

        $profileImageUrl = $buyer->profile_image
            ? asset('storage/' . ltrim((string) $buyer->profile_image, '/'))
            : 'https://via.placeholder.com/150x150?text=Buyer';

        return view('bid_admin.admin.buyer-details', [
            'buyer' => $buyer,
            'wallet' => $wallet,
            'walletTransactions' => $walletTransactions,
            'profileImageUrl' => $profileImageUrl,
            'buyerCode' => 'BAU' . str_pad((string) $buyer->id, 4, '0', STR_PAD_LEFT),
            'totalAuctionPurchase' => $totalAuctionPurchase,
            'totalPaidAmount' => $totalPaidAmount,
            'pendingSettlement' => $pendingSettlement,
            'averageBidValue' => $averageBidValue,
            'mostPurchasedFish' => $mostPurchasedFish,
            'preferredAuctionTime' => $preferredAuctionTime,
            'activeBidsThisMonth' => $activeBidsThisMonth,
            'buyerRankLabel' => $buyerRankLabel,
            'paymentReliability' => $paymentReliability,
            'recentTransactions' => $recentTransactions,
            'auctionHistory' => $auctionHistory,
            'auctionsParticipated' => $auctionsParticipated,
            'auctionsWon' => $auctionsWon,
            'winRate' => $winRate,
            'totalFishPurchased' => $totalFishPurchased,
        ]);
    }
    public function addBuyer(): View
    {
        return view('bid_admin.admin.add-buyer', $this->buildAdminBuyerFormData());
    }

    public function storeBuyer(Request $request): RedirectResponse
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
            'company_registration_file' => $request->hasFile('company_registration_file')
                ? $request->file('company_registration_file')->store('buyer-kyc', 'public')
                : null,
            'id_file' => $request->hasFile('id_file')
                ? $request->file('id_file')->store('buyer-kyc', 'public')
                : null,
            'import_license_file' => $request->hasFile('import_license_file')
                ? $request->file('import_license_file')->store('buyer-kyc', 'public')
                : null,
            'is_registered_business' => $request->boolean('is_registered_business'),
            'accepted_terms' => $request->boolean('accepted_terms'),
            'bank_transfer_validated' => $request->boolean('bank_transfer_validated'),
            'type' => 'buyer',
        ];

        if (Schema::hasColumn('users', 'status')) {
            $insertData['status'] = 'pending';
        }

        User::query()->create($insertData);

        return redirect()
            ->route('admin.buyers')
            ->with('success', 'Buyer created successfully and marked as pending review.');
    }
    
    public function editBuyer(User $buyer): View
    {
        abort_unless($buyer->type === 'buyer', 404);

        return view('bid_admin.admin.add-buyer', $this->buildAdminBuyerFormData($buyer, true));
    }

    public function updateBuyer(Request $request, User $buyer): RedirectResponse
    {
        abort_unless($buyer->type === 'buyer', 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $buyer->id],
            'phone' => ['required', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
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

        $buyer->name = $validated['name'];
        $buyer->email = $validated['email'];
        $buyer->phone = $validated['phone'];
        $buyer->job_title = $validated['job_title'] ?? null;
        $buyer->company_legal_name = $validated['company_legal_name'];
        $buyer->city = $validated['city'] ?? null;
        $buyer->business_address = $validated['business_address'] ?? null;
        $buyer->country = $validated['country'] ?? null;
        $buyer->website = $validated['website'] ?? null;
        $buyer->company_registration_number = $validated['company_registration_number'] ?? null;
        $buyer->business_type = $validated['business_type'];
        $buyer->interested_in = $validated['interested_in'] ?? null;
        $buyer->monthly_volume = $validated['monthly_volume'] ?? null;
        $buyer->preferred_delivery = $validated['preferred_delivery'] ?? null;
        $buyer->preferred_payment = $validated['preferred_payment'] ?? null;
        $buyer->bank_country = $validated['bank_country'] ?? null;
        $buyer->is_registered_business = $request->boolean('is_registered_business');
        $buyer->accepted_terms = $request->boolean('accepted_terms');
        $buyer->bank_transfer_validated = $request->boolean('bank_transfer_validated');

        if (! empty($validated['password'])) {
            $buyer->password = Hash::make($validated['password']);
        }

        if ($request->hasFile('company_registration_file')) {
            $this->deleteStoredFiles([$buyer->company_registration_file]);
            $buyer->company_registration_file = $request->file('company_registration_file')->store('buyer-kyc', 'public');
        }

        if ($request->hasFile('id_file')) {
            $this->deleteStoredFiles([$buyer->id_file]);
            $buyer->id_file = $request->file('id_file')->store('buyer-kyc', 'public');
        }

        if ($request->hasFile('import_license_file')) {
            $this->deleteStoredFiles([$buyer->import_license_file]);
            $buyer->import_license_file = $request->file('import_license_file')->store('buyer-kyc', 'public');
        }

        $buyer->save();

        return redirect()
            ->route('admin.buyers')
            ->with('success', 'Buyer updated successfully.');
    }

    public function destroyBuyer(User $buyer): RedirectResponse
    {
        abort_unless($buyer->type === 'buyer', 404);

        $hasBids = $buyer->bids()->exists();
        $hasSettlements = Settlement::query()->where('buyer_id', $buyer->id)->exists();

        if ($hasBids || $hasSettlements) {
            return redirect()
                ->route('admin.buyers')
                ->with('error', 'This buyer cannot be deleted because related bids or settlements already exist.');
        }

        $this->deleteStoredFiles([
            $buyer->company_registration_file,
            $buyer->id_file,
            $buyer->import_license_file,
            $buyer->profile_image,
        ]);

        $buyer->delete();

        return redirect()
            ->route('admin.buyers')
            ->with('success', 'Buyer deleted successfully.');
    }

    public function updateBuyerStatus(Request $request, User $buyer): RedirectResponse
    {
        abort_unless($buyer->type === 'buyer', 404);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:active,pending,under review,suspended,blocked,inactive'],
        ]);

        $buyer->status = $validated['status'];
        $buyer->save();

        return redirect()
            ->route('admin.buyers')
            ->with('success', 'Buyer status updated successfully.');
    }
    public function sellers(Request $request): View
    {
        $this->syncScheduledAuctionsToActive();

        $dashboardData = $this->buildDashboardData();
        $statusFilter = Str::lower(trim((string) $request->query('status', '')));

        $sellerBaseQuery = User::query()
            ->where('type', 'seller');

        if ($statusFilter !== '') {
            $sellerBaseQuery->whereRaw('LOWER(COALESCE(status, ?)) = ?', ['active', $statusFilter]);
        }

        $sellers = $sellerBaseQuery
            ->select('users.*')
            ->selectSub(
                Lot::query()
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('lots.seller_id', 'users.id'),
                'auctions_count'
            )
            ->selectSub(
                Settlement::query()
                    ->selectRaw('COALESCE(SUM(amount), 0)')
                    ->whereColumn('settlements.seller_id', 'users.id'),
                'total_sales'
            )
            ->orderByDesc('users.created_at')
            ->get()
            ->map(function ($seller) {
                $profileImage = $seller->profile_image
                    ? asset('storage/' . ltrim((string) $seller->profile_image, '/'))
                    : 'https://via.placeholder.com/80x80?text=Seller';

                $normalizedStatus = Str::lower(trim((string) ($seller->status ?: 'active')));

                $seller->display_name = $seller->company_name ?: $seller->name ?: 'Seller';
                $seller->seller_code = 'SEL' . str_pad((string) $seller->id, 4, '0', STR_PAD_LEFT);
                $seller->profile_image_url = $profileImage;
                $seller->normalized_status = $normalizedStatus;

                return $seller;
            });

        $allSellerStatuses = User::query()
            ->where('type', 'seller')
            ->select('status')
            ->distinct()
            ->pluck('status')
            ->filter()
            ->map(fn ($status) => Str::lower(trim((string) $status)))
            ->unique()
            ->sort()
            ->values();

        $underReviewStatuses = ['under review', 'pending', 'pending review', 'review'];

        $stats = [
            'totalSellers' => User::query()->where('type', 'seller')->count(),
            'activeSellers' => User::query()->where('type', 'seller')->whereRaw('LOWER(COALESCE(status, ?)) = ?', ['active', 'active'])->count(),
            'underReviewSellers' => User::query()
                ->where('type', 'seller')
                ->where(function ($query) use ($underReviewStatuses) {
                    foreach ($underReviewStatuses as $index => $status) {
                        $method = $index === 0 ? 'whereRaw' : 'orWhereRaw';
                        $query->{$method}('LOWER(COALESCE(status, ?)) = ?', ['active', $status]);
                    }
                })
                ->count(),
            'totalSales' => (float) Settlement::query()->sum('amount'),
        ];

        return view('bid_admin.admin.sellers', array_merge($dashboardData, [
            'sellers' => $sellers,
            'sellerStatusOptions' => $allSellerStatuses,
            'selectedSellerStatus' => $statusFilter,
            'sellerStats' => $stats,
        ]));
    }
    public function sellerDetails(Request $request): View
    {
        $sellerId = (int) $request->query('seller', 0);

        $seller = User::query()
            ->where('type', 'seller')
            ->findOrFail($sellerId);

        $lots = Lot::query()
            ->where('seller_id', $seller->id)
            ->latest('id')
            ->get();

        $settlements = Settlement::query()
            ->where('seller_id', $seller->id)
            ->with('lot')
            ->latest('id')
            ->get();

        $totalRevenue = (float) $settlements->sum('amount');
        $commissionPaid = (float) $settlements->sum('commission_amount');
        $pendingSettlement = (float) $settlements
            ->filter(fn (Settlement $settlement) => in_array(Str::lower((string) $settlement->status), ['pending', 'processing'], true))
            ->sum('net_amount');
        $averageLotValue = (float) ($lots->count() > 0 ? ($totalRevenue / max($lots->count(), 1)) : 0);
        $activeAuctionsCount = $lots->filter(fn (Lot $lot) => $lot->isCurrentlyActive())->count();
        $soldLotsCount = $lots->where('status', 'sold')->count();
        $sellThroughRate = $lots->count() > 0 ? round(($soldLotsCount / $lots->count()) * 100) : 0;

        $sellerRevenueRanking = Settlement::query()
            ->selectRaw('seller_id, COALESCE(SUM(amount), 0) as total_amount')
            ->groupBy('seller_id')
            ->orderByDesc('total_amount')
            ->pluck('seller_id')
            ->values();

        $rankingPosition = $sellerRevenueRanking->search($seller->id);
        $sellerRankLabel = $rankingPosition === false ? '-' : 'Top ' . ((int) $rankingPosition + 1);

        $recentSoldLots = $lots
            ->where('status', 'sold')
            ->sortByDesc('id')
            ->take(3)
            ->values();

        $recentPayouts = $settlements
            ->take(5)
            ->map(function (Settlement $settlement) {
                $provider = Str::of((string) ($settlement->payment_provider ?: 'manual'))
                    ->replace('_', ' ')
                    ->title();

                $settlement->provider_label = (string) $provider;
                $settlement->status_label = Str::title((string) $settlement->status);
                $settlement->status_badge_class = match (Str::lower((string) $settlement->status)) {
                    'paid' => 'bg-success',
                    'processing' => 'bg-warning text-dark',
                    'failed' => 'bg-danger',
                    default => 'bg-secondary',
                };

                return $settlement;
            });

        $profileImageUrl = $seller->profile_image
            ? asset('storage/' . ltrim((string) $seller->profile_image, '/'))
            : 'https://via.placeholder.com/120x120?text=Seller';

        $supplyTypeBadges = is_array($seller->supply_type)
            ? $seller->supply_type
            : (json_decode((string) $seller->supply_type, true) ?: []);

        return view('bid_admin.admin.seller-details', [
            'seller' => $seller,
            'profileImageUrl' => $profileImageUrl,
            'sellerCode' => 'SEL' . str_pad((string) $seller->id, 4, '0', STR_PAD_LEFT),
            'totalRevenue' => $totalRevenue,
            'commissionPaid' => $commissionPaid,
            'totalLotsListed' => $lots->count(),
            'pendingSettlement' => $pendingSettlement,
            'averageLotValue' => $averageLotValue,
            'activeAuctionsCount' => $activeAuctionsCount,
            'sellerRankLabel' => $sellerRankLabel,
            'sellThroughRate' => $sellThroughRate,
            'recentSoldLots' => $recentSoldLots,
            'recentPayouts' => $recentPayouts,
            'supplyTypeBadges' => $supplyTypeBadges,
        ]);
    }
    public function addSeller(): View
    {
        return view('bid_admin.admin.add-seller', $this->buildAdminSellerFormData());
    }

    public function storeSeller(Request $request): RedirectResponse
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

        $seller = new User();
        $seller->name = $validated['name'];
        $seller->phone = $validated['phone'];
        $seller->email = $validated['email'];
        $seller->password = Hash::make($validated['password']);
        $seller->company_name = $validated['company_name'];
        $seller->landing_site_port = $validated['landing_site_port'];
        $seller->address = $validated['address'];
        $seller->country = $validated['country'];
        $seller->supply_type = $validated['supply_type'] ?? null;
        $seller->processing_status = $validated['processing_status'] ?? null;
        $seller->estimated_weekly_volume = $validated['estimated_weekly_volume'] ?? null;
        $seller->type = 'seller';

        if (Schema::hasColumn('users', 'status')) {
            $seller->status = 'pending';
        }

        if ($request->hasFile('trade_license_file')) {
            $seller->trade_license_file = $request->file('trade_license_file')->store('seller-kyc', 'public');
        }

        if ($request->hasFile('facility_photos_file')) {
            $seller->facility_photos_file = $request->file('facility_photos_file')->store('seller-kyc', 'public');
        }

        if ($request->hasFile('certificates_file')) {
            $seller->certificates_file = $request->file('certificates_file')->store('seller-kyc', 'public');
        }

        $seller->save();

        return redirect()
            ->route('admin.sellers')
            ->with('success', 'Seller created successfully and marked as pending review.');
    }

    public function editSeller(User $seller): View
    {
        abort_unless($seller->type === 'seller', 404);

        return view('bid_admin.admin.add-seller', $this->buildAdminSellerFormData($seller, true));
    }

    public function updateSeller(Request $request, User $seller): RedirectResponse
    {
        abort_unless($seller->type === 'seller', 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $seller->id],
            'password' => ['nullable', 'string', 'min:8'],
            'company_name' => ['required', 'string', 'max:255'],
            'landing_site_port' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'country' => ['required', 'string', 'max:100'],
            'supply_type' => ['nullable', 'array'],
            'supply_type.*' => ['string', 'max:50'],
            'processing_status' => ['nullable', 'array'],
            'processing_status.*' => ['string', 'max:50'],
            'estimated_weekly_volume' => ['nullable', 'string', 'max:100'],
            'trade_license_file' => ['nullable', 'file', 'max:5120'],
            'facility_photos_file' => ['nullable', 'file', 'max:5120'],
            'certificates_file' => ['nullable', 'file', 'max:5120'],
        ]);

        $seller->name = $validated['name'];
        $seller->phone = $validated['phone'];
        $seller->email = $validated['email'];
        $seller->company_name = $validated['company_name'];
        $seller->landing_site_port = $validated['landing_site_port'];
        $seller->address = $validated['address'];
        $seller->country = $validated['country'];
        $seller->supply_type = $validated['supply_type'] ?? null;
        $seller->processing_status = $validated['processing_status'] ?? null;
        $seller->estimated_weekly_volume = $validated['estimated_weekly_volume'] ?? null;

        if (! empty($validated['password'])) {
            $seller->password = Hash::make($validated['password']);
        }

        if ($request->hasFile('trade_license_file')) {
            $this->deleteStoredFiles([$seller->trade_license_file]);
            $seller->trade_license_file = $request->file('trade_license_file')->store('seller-kyc', 'public');
        }

        if ($request->hasFile('facility_photos_file')) {
            $this->deleteStoredFiles([$seller->facility_photos_file]);
            $seller->facility_photos_file = $request->file('facility_photos_file')->store('seller-kyc', 'public');
        }

        if ($request->hasFile('certificates_file')) {
            $this->deleteStoredFiles([$seller->certificates_file]);
            $seller->certificates_file = $request->file('certificates_file')->store('seller-kyc', 'public');
        }

        $seller->save();

        return redirect()
            ->route('admin.sellers')
            ->with('success', 'Seller updated successfully.');
    }

    public function destroySeller(User $seller): RedirectResponse
    {
        abort_unless($seller->type === 'seller', 404);

        $hasLots = Lot::query()->where('seller_id', $seller->id)->exists();
        $hasSettlements = Settlement::query()->where('seller_id', $seller->id)->exists();

        if ($hasLots || $hasSettlements) {
            return redirect()
                ->route('admin.sellers')
                ->with('error', 'This seller cannot be deleted because related lots or settlements already exist.');
        }

        $this->deleteStoredFiles([
            $seller->trade_license_file,
            $seller->facility_photos_file,
            $seller->certificates_file,
            $seller->profile_image,
        ]);

        $seller->delete();

        return redirect()
            ->route('admin.sellers')
            ->with('success', 'Seller deleted successfully.');
    }

    public function updateSellerStatus(Request $request, User $seller): RedirectResponse
    {
        abort_unless($seller->type === 'seller', 404);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:active,pending,under review,suspended,blocked,inactive'],
        ]);

        $seller->status = $validated['status'];
        $seller->save();

        return redirect()
            ->route('admin.sellers')
            ->with('success', 'Seller status updated successfully.');
    }
    public function financeOverview(): View
    {
        return view('bid_admin.admin.finance-overview', $this->buildFinanceOverviewData());
    }
    public function financeOverviewData(): JsonResponse
    {
        return response()->json($this->buildFinanceOverviewData());
    }
    public function notifications(): View { return $this->renderOrDashboard('bid_admin.admin.notifications'); }
    public function accountSettings(): View|RedirectResponse
    {
        $admin = $this->getAuthenticatedAdmin();

        if (! $admin) {
            return redirect()->route('admin.login')->with('error', 'Please log in as admin to access your profile.');
        }

        return view('bid_admin.admin.account-settings', [
            'admin' => $admin,
        ]);
    }

    public function updateAccountSettings(Request $request): RedirectResponse
    {
        $admin = $this->getAuthenticatedAdmin();

        if (! $admin) {
            return redirect()->route('admin.login')->with('error', 'Please log in as admin to update your profile.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins,email,' . $admin->id],
        ]);

        $admin->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        $admin->save();

        $request->session()->put('admin_user', [
            'id' => $admin->id,
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => $admin->role,
        ]);

        return redirect()
            ->route('admin.account-settings')
            ->with('success', 'Profile updated successfully.');
    }

    public function changePassword(): View|RedirectResponse
    {
        $admin = $this->getAuthenticatedAdmin();

        if (! $admin) {
            return redirect()->route('admin.login')->with('error', 'Please log in as admin to access password settings.');
        }

        return view('bid_admin.admin.change-password', [
            'admin' => $admin,
        ]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $admin = $this->getAuthenticatedAdmin();

        if (! $admin) {
            return redirect()->route('admin.login')->with('error', 'Please log in as admin to update your password.');
        }

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], (string) $admin->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect.',
            ]);
        }

        $admin->password = Hash::make($validated['new_password']);
        $admin->save();

        return redirect()
            ->route('admin.change-password')
            ->with('success', 'Password updated successfully.');
    }
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

    public function transactions(Request $request): View
    {
        return view('bid_admin.admin.transactions', $this->buildTransactionsData($request));
    }
    public function transactionsData(Request $request): JsonResponse
    {
        return response()->json($this->buildTransactionsData($request));
    }
    public function bankTransfer(Request $request): View
    {
        return view('bid_admin.admin.bank-transfer', $this->buildBankTransferData($request));
    }
    public function bankTransferData(Request $request): JsonResponse
    {
        return response()->json($this->buildBankTransferData($request));
    }
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
                    'auction_end_at' => optional($lot->auction_end_at)->toIso8601String(),
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
                'auction_end_at' => optional($lot->auction_end_at)->toIso8601String(),
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

    private function buildAdminSellerFormData(?User $seller = null, bool $isEditMode = false): array
    {
        $dashboardData = $this->buildDashboardData();

        return array_merge($dashboardData, [
            'sellerCountries' => ['France', 'Spain', 'India', 'Morocco'],
            'sellerSupplyTypes' => ['Fresh', 'Frozen', 'Both'],
            'sellerProcessingStatuses' => ['Whole', 'Fillet', 'Packed', 'IQF', 'Other'],
            'seller' => $seller,
            'isEditMode' => $isEditMode,
        ]);
    }

    private function buildAdminBuyerFormData(?User $buyer = null, bool $isEditMode = false): array
    {
        return array_merge($this->buildDashboardData(), [
            'buyerCountries' => ['France', 'Spain', 'Italy', 'India'],
            'buyerBusinessTypes' => ['Hotels', 'Restaurants', 'Supermarkets', 'Catering', 'Bulk Importer', 'Distributor', 'Reseller', 'Processing Company'],
            'buyerInterestedInOptions' => ['Fresh', 'Frozen', 'Both'],
            'buyerMonthlyVolumeOptions' => ['100 - 500 kg', '500 - 1000 kg', '1000 - 5000 kg', '5000+ kg'],
            'buyerPreferredDeliveryOptions' => ['Air', 'Sea', 'Local Pickup'],
            'buyerPreferredPaymentOptions' => ['Bank Transfer', 'Online Payment', 'LC'],
            'buyer' => $buyer,
            'isEditMode' => $isEditMode,
        ]);
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

    private function getAuthenticatedAdmin(): ?Admin
    {
        $adminId = session('admin_user.id');
        $adminRole = session('admin_user.role');

        if (! $adminId || $adminRole !== 'admin') {
            return null;
        }

        return Admin::query()
            ->where('id', $adminId)
            ->where('role', 'admin')
            ->first();
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

    private function buildFinanceOverviewData(): array
    {
        $dashboardData = $this->buildDashboardData();
        $now = now();
        $todayStart = $now->copy()->startOfDay();
        $tomorrowStart = $todayStart->copy()->addDay();
        $months = collect(range(5, 1))
            ->map(fn (int $offset) => $now->copy()->subMonths($offset)->startOfMonth())
            ->push($now->copy()->startOfMonth())
            ->values();

        $totalRevenue = (float) Settlement::query()->sum('amount');
        $totalCommission = (float) Settlement::query()->sum('commission_amount');
        $platformBalance = (float) Wallet::query()->sum('available_balance');
        $escrowHolding = (float) Wallet::query()->sum('blocked_balance');
        $pendingPayments = (float) Settlement::query()
            ->whereIn('status', ['pending', 'processing'])
            ->sum('amount');
        $failedSettlementCount = (int) Settlement::query()->where('status', 'failed')->count();
        $failedWalletTransactionCount = (int) WalletTransaction::query()->where('status', 'failed')->count();
        $failedTransactions = $failedSettlementCount + $failedWalletTransactionCount;
        $generatedInvoices = (int) Settlement::query()->count();
        $pendingSettlementCount = (int) Settlement::query()->where('status', 'pending')->count();
        $processingSettlementCount = (int) Settlement::query()->where('status', 'processing')->count();
        $expiredSettlementCount = (int) Settlement::query()->where('status', 'expired')->count();

        $avgValidationMinutes = (float) Settlement::query()
            ->where('status', 'paid')
            ->whereNotNull('paid_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, paid_at)) as avg_minutes')
            ->value('avg_minutes');

        $monthlyRevenue = [];
        $monthlyCommission = [];
        $monthlyPayout = [];
        $monthlyLabels = [];

        foreach ($months as $monthStart) {
            $monthEnd = $monthStart->copy()->endOfMonth();
            $monthlyLabels[] = $monthStart->format('M');
            $monthlyRevenue[] = round((float) Settlement::query()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount'), 2);
            $monthlyCommission[] = round((float) Settlement::query()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('commission_amount'), 2);
            $monthlyPayout[] = round((float) Settlement::query()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('net_amount'), 2);
        }

        $paymentMethodRows = Settlement::query()
            ->selectRaw('COALESCE(NULLIF(payment_provider, ""), "manual") as provider, COUNT(*) as total')
            ->groupBy('provider')
            ->orderByDesc('total')
            ->get();

        $paymentMethodLabels = $paymentMethodRows
            ->map(fn ($row) => Str::of((string) $row->provider)->replace('_', ' ')->title()->toString())
            ->values()
            ->all();
        $paymentMethodData = $paymentMethodRows
            ->map(fn ($row) => (int) $row->total)
            ->values()
            ->all();

        $bankTransferStatuses = ['paid', 'processing', 'pending', 'failed', 'expired'];
        $bankTransferRows = Settlement::query()
            ->where('payment_provider', 'bank_transfer')
            ->selectRaw('status, COALESCE(SUM(amount), 0) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $bankTransferLabels = collect($bankTransferStatuses)
            ->filter(fn (string $status) => isset($bankTransferRows[$status]) || in_array($status, ['paid', 'pending', 'failed'], true))
            ->map(fn (string $status) => Str::of($status)->replace('_', ' ')->title()->toString())
            ->values()
            ->all();
        $bankTransferData = collect($bankTransferStatuses)
            ->filter(fn (string $status) => isset($bankTransferRows[$status]) || in_array($status, ['paid', 'pending', 'failed'], true))
            ->map(fn (string $status) => round((float) ($bankTransferRows[$status] ?? 0), 2))
            ->values()
            ->all();

        $duplicateProofCount = (int) Settlement::query()
            ->whereNotNull('payment_reference')
            ->where('payment_reference', '!=', '')
            ->selectRaw('payment_reference')
            ->groupBy('payment_reference')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();

        $riskFlags = [
            [
                'label' => 'Pending Settlements',
                'value' => $pendingSettlementCount,
                'helper' => 'Awaiting buyer payment',
                'class' => 'text-warning',
            ],
            [
                'label' => 'Payment Verification',
                'value' => $processingSettlementCount,
                'helper' => 'Submitted and under review',
                'class' => 'text-info',
            ],
            [
                'label' => 'Duplicate References',
                'value' => $duplicateProofCount,
                'helper' => 'Repeated payment proofs',
                'class' => 'text-danger',
            ],
            [
                'label' => 'Expired Windows',
                'value' => $expiredSettlementCount,
                'helper' => 'Payment window missed',
                'class' => 'text-danger',
            ],
        ];

        $recentTransactions = Settlement::query()
            ->with(['buyer:id,name', 'seller:id,name'])
            ->orderByDesc('amount')
            ->orderByDesc('created_at')
            ->take(5)
            ->get()
            ->map(function (Settlement $settlement) {
                $providerLabel = Str::of((string) ($settlement->payment_provider ?: 'manual'))
                    ->replace('_', ' ')
                    ->title()
                    ->toString();

                $status = Str::lower((string) $settlement->status);
                $statusLabel = match ($status) {
                    'paid' => 'Validated',
                    'processing' => 'Processing',
                    'pending' => 'Pending',
                    'failed' => 'Failed',
                    'expired' => 'Expired',
                    default => Str::of($status)->replace('_', ' ')->title()->toString(),
                };

                $statusBadgeClass = match ($status) {
                    'paid' => 'bg-success',
                    'processing', 'pending' => 'bg-warning text-dark',
                    'failed', 'expired' => 'bg-danger',
                    default => 'bg-secondary',
                };

                return [
                    'auction_code' => 'AUC' . str_pad((string) $settlement->lot_id, 4, '0', STR_PAD_LEFT),
                    'buyer_name' => $settlement->buyer?->name ?: 'N/A',
                    'seller_name' => $settlement->seller?->name ?: 'N/A',
                    'amount' => (float) $settlement->amount,
                    'payment_type' => $providerLabel,
                    'status_label' => $statusLabel,
                    'status_badge_class' => $statusBadgeClass,
                ];
            })
            ->values()
            ->all();

        $buyersOnlineCount = (int) User::query()
            ->where('type', 'buyer')
            ->whereRaw('LOWER(COALESCE(status, "")) = ?', ['active'])
            ->count();

        $kpis = [
            ['title' => 'Total Revenue', 'value' => $totalRevenue, 'suffix' => null],
            ['title' => 'Total Commission', 'value' => $totalCommission, 'suffix' => null],
            ['title' => 'Platform Balance', 'value' => $platformBalance, 'suffix' => null],
            ['title' => 'Escrow Holding', 'value' => $escrowHolding, 'suffix' => null],
            ['title' => 'Pending Payments', 'value' => $pendingPayments, 'suffix' => null],
            ['title' => 'Failed Transactions', 'value' => $failedTransactions, 'suffix' => 'count'],
            ['title' => 'Generated Invoices', 'value' => $generatedInvoices, 'suffix' => 'count'],
            ['title' => 'Avg Validation Time', 'value' => $avgValidationMinutes, 'suffix' => 'hours'],
        ];

        return array_merge($dashboardData, [
            'buyersOnlineCount' => $buyersOnlineCount,
            'financeKpis' => $kpis,
            'riskFlags' => $riskFlags,
            'recentTransactions' => $recentTransactions,
            'monthlyRevenueLabels' => $monthlyLabels,
            'monthlyRevenueData' => $monthlyRevenue,
            'monthlyCommissionData' => $monthlyCommission,
            'monthlyPayoutData' => $monthlyPayout,
            'paymentMethodLabels' => $paymentMethodLabels,
            'paymentMethodData' => $paymentMethodData,
            'bankTransferLabels' => $bankTransferLabels,
            'bankTransferData' => $bankTransferData,
        ]);
    }

    private function buildTransactionsData(Request $request): array
    {
        $dashboardData = $this->buildDashboardData();
        $allowedPerPage = [15, 25, 50];
        $perPage = (int) $request->query('per_page', 15);
        if (! in_array($perPage, $allowedPerPage, true)) {
            $perPage = 15;
        }

        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'status' => trim((string) $request->query('status', '')),
            'payment_type' => trim((string) $request->query('payment_type', '')),
            'date' => trim((string) $request->query('date', '')),
            'per_page' => (string) $perPage,
        ];

        $statusAliases = [
            'successful' => ['paid'],
            'pending' => ['pending', 'processing'],
            'failed' => ['failed', 'expired'],
        ];

        $duplicateReferences = Settlement::query()
            ->whereNotNull('payment_reference')
            ->where('payment_reference', '!=', '')
            ->selectRaw('payment_reference, COUNT(*) as total')
            ->groupBy('payment_reference')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('total', 'payment_reference');

        $query = Settlement::query()
            ->with([
                'buyer:id,name,company_legal_name,company_name',
                'seller:id,name,company_name',
            ])
            ->latest('created_at');

        if ($filters['search'] !== '') {
            $search = $filters['search'];
            $query->where(function ($builder) use ($search) {
                $numericSearch = preg_replace('/\D+/', '', $search);

                if ($numericSearch !== '') {
                    $builder->orWhere('lot_id', 'like', '%' . $numericSearch . '%');
                }

                $builder->orWhere('payment_reference', 'like', '%' . $search . '%')
                    ->orWhereHas('buyer', function ($buyerQuery) use ($search) {
                        $buyerQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('company_legal_name', 'like', '%' . $search . '%')
                            ->orWhere('company_name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($filters['status'] !== '' && isset($statusAliases[$filters['status']])) {
            $query->whereIn('status', $statusAliases[$filters['status']]);
        }

        if ($filters['payment_type'] !== '') {
            $query->whereRaw('LOWER(COALESCE(payment_provider, "")) = ?', [Str::lower($filters['payment_type'])]);
        }

        if ($filters['date'] !== '') {
            $query->whereDate('created_at', $filters['date']);
        }

        $summaryQuery = clone $query;
        $paidCount = (clone $summaryQuery)->where('status', 'paid')->count();
        $pendingCount = (clone $summaryQuery)->whereIn('status', ['pending', 'processing'])->count();
        $failedCount = (clone $summaryQuery)->whereIn('status', ['failed', 'expired'])->count();

        $paginatedSettlements = $query
            ->paginate($perPage)
            ->appends(array_filter($filters, fn ($value) => $value !== ''));

        $settlements = $paginatedSettlements->getCollection();

        $transactions = $settlements->map(function (Settlement $settlement) use ($duplicateReferences) {
            $buyer = $settlement->buyer;
            $provider = Str::lower((string) ($settlement->payment_provider ?: 'manual'));
            $providerLabel = Str::of($provider)->replace('_', ' ')->title()->toString();

            $status = Str::lower((string) $settlement->status);
            $statusLabel = match ($status) {
                'paid' => 'Successful',
                'processing' => 'Pending Verification',
                'pending' => 'Pending',
                'failed' => 'Failed',
                'expired' => 'Expired',
                default => Str::of($status)->replace('_', ' ')->title()->toString(),
            };

            $statusBadgeClass = match ($status) {
                'paid' => 'bg-success',
                'processing', 'pending' => 'bg-warning text-dark',
                'failed', 'expired' => 'bg-danger',
                default => 'bg-secondary',
            };

            $risk = $this->buildTransactionRiskLabel($settlement, $duplicateReferences);

            return [
                'auction_code' => 'AUC' . str_pad((string) $settlement->lot_id, 4, '0', STR_PAD_LEFT),
                'buyer_name' => $buyer?->name ?: 'N/A',
                'company_name' => $buyer?->company_legal_name ?: ($buyer?->company_name ?: 'N/A'),
                'amount' => (float) $settlement->amount,
                'payment_type' => $providerLabel,
                'status_label' => $statusLabel,
                'status_badge_class' => $statusBadgeClass,
                'risk_label' => $risk['label'],
                'risk_badge_class' => $risk['badge_class'],
                'date_label' => optional($settlement->created_at)->format('d M Y') ?: '-',
                'action_label' => $risk['action_label'],
                'action_url' => $buyer ? route('admin.buyer-details', ['buyer' => $buyer->id]) : null,
                'action_class' => $risk['action_class'],
            ];
        })->values()->all();

        $paymentTypeOptions = Settlement::query()
            ->selectRaw('COALESCE(NULLIF(payment_provider, ""), "manual") as provider')
            ->distinct()
            ->orderBy('provider')
            ->pluck('provider')
            ->map(fn ($provider) => [
                'value' => (string) $provider,
                'label' => Str::of((string) $provider)->replace('_', ' ')->title()->toString(),
            ])
            ->values()
            ->all();

        return array_merge($dashboardData, [
            'transactionFilters' => $filters,
            'transactionsPerPageOptions' => $allowedPerPage,
            'paymentTypeOptions' => $paymentTypeOptions,
            'transactionsRows' => $transactions,
            'transactionsSummary' => [
                'total' => $paginatedSettlements->total(),
                'successful' => $paidCount,
                'pending' => $pendingCount,
                'failed' => $failedCount,
            ],
            'transactionsPagination' => [
                'current_page' => $paginatedSettlements->currentPage(),
                'last_page' => $paginatedSettlements->lastPage(),
                'per_page' => $paginatedSettlements->perPage(),
                'total' => $paginatedSettlements->total(),
                'from' => $paginatedSettlements->firstItem(),
                'to' => $paginatedSettlements->lastItem(),
                'has_more_pages' => $paginatedSettlements->hasMorePages(),
            ],
        ]);
    }

    private function buildBankTransferData(Request $request): array
    {
        $dashboardData = $this->buildDashboardData();
        $allowedPerPage = [10, 15, 25, 50];
        $perPage = (int) $request->query('per_page', 10);
        if (! in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'status' => trim((string) $request->query('status', '')),
            'country' => trim((string) $request->query('country', '')),
            'date' => trim((string) $request->query('date', '')),
            'per_page' => (string) $perPage,
        ];

        $statusAliases = [
            'pending' => ['pending'],
            'under_review' => ['processing'],
            'approved' => ['paid'],
            'rejected' => ['failed', 'expired'],
        ];

        $query = Settlement::query()
            ->with([
                'buyer:id,name,company_legal_name,company_name,country',
                'lot:id,title,species,quantity,auction_end_at',
            ])
            ->where('payment_provider', 'bank_transfer')
            ->latest('created_at');

        if ($filters['search'] !== '') {
            $search = $filters['search'];
            $query->where(function ($builder) use ($search) {
                $numericSearch = preg_replace('/\D+/', '', $search);

                if ($numericSearch !== '') {
                    $builder->orWhere('lot_id', 'like', '%' . $numericSearch . '%');
                }

                $builder->orWhere('payment_reference', 'like', '%' . $search . '%')
                    ->orWhereHas('buyer', function ($buyerQuery) use ($search) {
                        $buyerQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('company_legal_name', 'like', '%' . $search . '%')
                            ->orWhere('company_name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('lot', function ($lotQuery) use ($search) {
                        $lotQuery->where('title', 'like', '%' . $search . '%')
                            ->orWhere('species', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($filters['status'] !== '' && isset($statusAliases[$filters['status']])) {
            $query->whereIn('status', $statusAliases[$filters['status']]);
        }

        if ($filters['country'] !== '') {
            $country = Str::lower($filters['country']);
            $query->whereHas('buyer', function ($buyerQuery) use ($country) {
                $buyerQuery->whereRaw('LOWER(COALESCE(country, "")) = ?', [$country]);
            });
        }

        if ($filters['date'] !== '') {
            $query->whereDate('created_at', $filters['date']);
        }

        $summaryQuery = clone $query;
        $totalTransfers = (clone $summaryQuery)->count();
        $validatedCount = (clone $summaryQuery)->where('status', 'paid')->count();
        $pendingCount = (clone $summaryQuery)->whereIn('status', ['pending', 'processing'])->count();
        $rejectedCount = (clone $summaryQuery)->whereIn('status', ['failed', 'expired'])->count();

        $paginatedSettlements = $query
            ->paginate($perPage)
            ->appends(array_filter($filters, fn ($value) => $value !== ''));

        $deadlineService = app(SettlementLifecycleService::class);

        $rows = $paginatedSettlements->getCollection()
            ->map(function (Settlement $settlement) use ($deadlineService) {
                $buyer = $settlement->buyer;
                $lot = $settlement->lot;
                $status = Str::lower((string) $settlement->status);

                $statusLabel = match ($status) {
                    'paid' => 'Approved',
                    'processing' => 'Under Review',
                    'pending' => 'Pending',
                    'failed', 'expired' => 'Rejected',
                    default => Str::of($status)->replace('_', ' ')->title()->toString(),
                };

                $statusBadgeClass = match ($status) {
                    'paid' => 'bg-success',
                    'processing' => 'bg-info',
                    'pending' => 'bg-warning text-dark',
                    'failed', 'expired' => 'bg-danger',
                    default => 'bg-secondary',
                };

                $action = match ($status) {
                    'pending' => ['label' => 'Follow Up', 'class' => 'btn-success'],
                    'processing' => ['label' => 'Review', 'class' => 'btn-primary'],
                    'paid' => ['label' => 'View', 'class' => 'btn-outline-secondary'],
                    default => ['label' => 'Details', 'class' => 'btn-outline-secondary'],
                };

                $lotDescriptionParts = array_filter([
                    $lot?->title,
                    $lot?->species,
                    $lot && $lot->quantity !== null ? number_format((float) $lot->quantity, 0) . 'kg' : null,
                ]);

                $deadlineAt = $deadlineService->paymentDeadlineFor($settlement);

                return [
                    'buyer_name' => $buyer?->name ?: 'N/A',
                    'company_name' => $buyer?->company_legal_name ?: ($buyer?->company_name ?: 'N/A'),
                    'country' => $buyer?->country ?: 'N/A',
                    'auction_code' => 'AUC' . str_pad((string) $settlement->lot_id, 4, '0', STR_PAD_LEFT),
                    'lot_description' => $lotDescriptionParts ? implode(' - ', $lotDescriptionParts) : 'N/A',
                    'amount' => (float) $settlement->amount,
                    'auction_close_label' => optional($lot?->auction_end_at)->format('d M Y') ?: '-',
                    'payment_deadline_label' => $deadlineAt?->format('d M Y') ?: '-',
                    'status_label' => $statusLabel,
                    'status_badge_class' => $statusBadgeClass,
                    'action_label' => $action['label'],
                    'action_class' => $action['class'],
                    'action_url' => $buyer ? route('admin.buyer-details', ['buyer' => $buyer->id]) : null,
                ];
            })
            ->values()
            ->all();

        $countryOptions = Settlement::query()
            ->join('users', 'users.id', '=', 'settlements.buyer_id')
            ->where('settlements.payment_provider', 'bank_transfer')
            ->whereNotNull('users.country')
            ->where('users.country', '!=', '')
            ->selectRaw('users.country as country')
            ->distinct()
            ->orderBy('users.country')
            ->pluck('country')
            ->map(fn ($country) => [
                'value' => (string) $country,
                'label' => (string) $country,
            ])
            ->values()
            ->all();

        return array_merge($dashboardData, [
            'bankTransferFilters' => $filters,
            'bankTransferPerPageOptions' => $allowedPerPage,
            'bankTransferCountryOptions' => $countryOptions,
            'bankTransferSummary' => [
                'total' => $totalTransfers,
                'validated' => $validatedCount,
                'pending' => $pendingCount,
                'rejected' => $rejectedCount,
            ],
            'bankTransferRows' => $rows,
            'bankTransferPagination' => [
                'current_page' => $paginatedSettlements->currentPage(),
                'last_page' => $paginatedSettlements->lastPage(),
                'per_page' => $paginatedSettlements->perPage(),
                'total' => $paginatedSettlements->total(),
                'from' => $paginatedSettlements->firstItem(),
                'to' => $paginatedSettlements->lastItem(),
                'has_more_pages' => $paginatedSettlements->hasMorePages(),
            ],
        ]);
    }

    private function buildTransactionRiskLabel(Settlement $settlement, \Illuminate\Support\Collection $duplicateReferences): array
    {
        $reference = (string) ($settlement->payment_reference ?? '');
        $status = Str::lower((string) $settlement->status);
        $provider = Str::lower((string) ($settlement->payment_provider ?? ''));

        if ($reference !== '' && isset($duplicateReferences[$reference])) {
            return [
                'label' => 'Duplicate Reference',
                'badge_class' => 'bg-danger',
                'action_label' => 'Review',
                'action_class' => 'btn-outline-danger',
            ];
        }

        if ($status === 'expired') {
            return [
                'label' => 'Expired Window',
                'badge_class' => 'bg-danger',
                'action_label' => 'Investigate',
                'action_class' => 'btn-danger',
            ];
        }

        if ($status === 'failed') {
            return [
                'label' => 'Failed Payment',
                'badge_class' => 'bg-danger',
                'action_label' => 'Review',
                'action_class' => 'btn-outline-danger',
            ];
        }

        if ($status === 'processing') {
            return [
                'label' => 'Needs Verification',
                'badge_class' => 'bg-warning text-dark',
                'action_label' => 'Review',
                'action_class' => 'btn-outline-primary',
            ];
        }

        if ($status === 'pending' && in_array($provider, ['bank_transfer', 'waafipay'], true)) {
            return [
                'label' => 'Awaiting Payment Proof',
                'badge_class' => 'bg-warning text-dark',
                'action_label' => 'Follow Up',
                'action_class' => 'btn-outline-primary',
            ];
        }

        return [
            'label' => 'No Risk',
            'badge_class' => 'bg-light text-dark border',
            'action_label' => 'View',
            'action_class' => 'btn-outline-primary',
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

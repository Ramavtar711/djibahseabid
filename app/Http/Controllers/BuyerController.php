<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Lot;
use App\Models\AppNotification;
use App\Models\AuctionMessage;
use App\Models\Settlement;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Carbon;
use App\Events\BidPlaced;
use App\Events\AuctionMessageSent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Services\SettlementLifecycleService;

class BuyerController extends Controller
{
    public function dashboard(): View
    {
        $buyerId = $this->resolveBuyerId();
        $now = now();
        $currentWeekStart = $now->copy()->startOfWeek();
        $previousWeekStart = $currentWeekStart->copy()->subWeek();
        $currentMonthStart = $now->copy()->startOfMonth();
        $previousMonthStart = $currentMonthStart->copy()->subMonth();

        $liveLots = Lot::query()
            ->currentlyActive()
            ->orderByDesc('auction_start_at')
            ->take(6)
            ->get();

        $upcomingLots = Lot::query()
            ->currentlyUpcoming()
            ->orderBy('auction_start_at')
            ->take(6)
            ->get();

        $lotIds = $liveLots->pluck('id')->merge($upcomingLots->pluck('id'))->unique()->values();
        $highestBids = $lotIds->isEmpty()
            ? collect()
            : Bid::query()
                ->selectRaw('lot_id, MAX(amount) as max_amount')
                ->whereIn('lot_id', $lotIds)
                ->groupBy('lot_id')
                ->pluck('max_amount', 'lot_id');

        $wallet = null;
        $settlements = collect();
        $activeBids = collect();
        $stats = [
            'active_auctions' => $liveLots->count(),
            'auctions_won' => 0,
            'total_purchased' => 0,
            'credit_available' => 0,
            'pending_payments' => 0,
            'win_rate' => 0,
            'pending_lots' => $upcomingLots->count(),
            'credit_used' => 0,
        ];
        $analytics = [
            'preferred_species' => 'N/A',
            'avg_bid_value' => 0,
            'active_time_slot' => 'N/A',
            'top_species' => collect(),
        ];
        $chartData = [
            'credit_used' => 0,
            'credit_remaining' => 0,
            'settlement_labels' => [],
            'settlement_values' => [],
        ];
        $trends = [
            'active_auctions' => 'Live auctions available now',
            'auctions_won' => 'No wins recorded yet',
            'total_purchased' => 'No monthly purchases yet',
            'credit_available' => 'Wallet available balance',
            'pending_payments' => 'No pending settlements',
            'win_rate' => 'No bids placed yet',
        ];

        if ($buyerId) {
            $wallet = Schema::hasTable('wallets')
                ? $this->getOrCreateWallet($buyerId)
                : null;

            $settlements = Settlement::query()
                ->where('buyer_id', $buyerId)
                ->with('lot')
                ->latest()
                ->get();

            $buyerBidRows = Bid::query()
                ->where('buyer_id', $buyerId)
                ->select('id', 'lot_id', 'amount', 'created_at')
                ->orderByDesc('created_at')
                ->get();

            $buyerLotIds = $buyerBidRows->pluck('lot_id')->unique()->values();
            $allHighestBids = $buyerLotIds->isEmpty()
                ? collect()
                : Bid::query()
                    ->selectRaw('lot_id, MAX(amount) as max_amount')
                    ->whereIn('lot_id', $buyerLotIds)
                    ->groupBy('lot_id')
                    ->pluck('max_amount', 'lot_id');

            $latestBidPerLot = $buyerBidRows
                ->unique('lot_id')
                ->values();

            $activeBidLotIds = Lot::query()
                ->whereIn('id', $latestBidPerLot->pluck('lot_id'))
                ->currentlyActive()
                ->pluck('id');

            $activeBids = $latestBidPerLot
                ->whereIn('lot_id', $activeBidLotIds)
                ->take(6)
                ->map(function (Bid $bid) use ($allHighestBids) {
                    $lot = Lot::find($bid->lot_id);
                    $highest = (float) ($allHighestBids[$bid->lot_id] ?? $bid->amount);

                    return [
                        'lot_id' => $bid->lot_id,
                        'lot' => $lot,
                        'my_bid' => (float) $bid->amount,
                        'highest_bid' => $highest,
                        'is_highest' => (float) $bid->amount >= $highest,
                    ];
                })
                ->filter(fn ($row) => $row['lot'])
                ->values();

            $paidSettlements = $settlements->where('status', 'paid');
            $pendingSettlements = $settlements->whereIn('status', ['pending', 'processing']);
            $distinctLotsBidOn = $buyerBidRows->pluck('lot_id')->unique()->count();
            $wonLots = $settlements->pluck('lot_id')->unique()->count();

            $speciesCounts = $buyerBidRows->isEmpty()
                ? collect()
                : DB::table('bids')
                    ->join('lots', 'lots.id', '=', 'bids.lot_id')
                    ->where('bids.buyer_id', $buyerId)
                    ->selectRaw('COALESCE(lots.species, "Unknown") as species, COUNT(*) as total_bids')
                    ->groupBy('lots.species')
                    ->orderByDesc('total_bids')
                    ->get();

            $topSpecies = $buyerBidRows->isEmpty()
                ? collect()
                : DB::table('bids')
                    ->join('lots', 'lots.id', '=', 'bids.lot_id')
                    ->where('bids.buyer_id', $buyerId)
                    ->selectRaw('COALESCE(lots.species, "Unknown") as species, AVG(bids.amount) as avg_amount')
                    ->groupBy('lots.species')
                    ->orderByDesc('avg_amount')
                    ->limit(3)
                    ->get();

            $hourCounts = $buyerBidRows->groupBy(function (Bid $bid) {
                return (int) optional($bid->created_at)->format('G');
            })->map->count();

            $mostActiveHour = $hourCounts->sortDesc()->keys()->first();
            $activeTimeSlot = $mostActiveHour === null
                ? 'N/A'
                : sprintf('%02d:00-%02d:59', $mostActiveHour, $mostActiveHour);

            $stats = [
                'active_auctions' => $liveLots->count(),
                'auctions_won' => $wonLots,
                'total_purchased' => (float) $paidSettlements->sum('amount'),
                'credit_available' => (float) ($wallet?->available_balance ?? 0),
                'pending_payments' => (float) $pendingSettlements->sum('amount'),
                'win_rate' => $distinctLotsBidOn > 0 ? round(($wonLots / $distinctLotsBidOn) * 100, 1) : 0,
                'pending_lots' => $upcomingLots->count(),
                'credit_used' => (float) ($wallet?->blocked_balance ?? 0),
            ];

            $analytics = [
                'preferred_species' => $speciesCounts->first()->species ?? 'N/A',
                'avg_bid_value' => round((float) $buyerBidRows->avg('amount'), 2),
                'active_time_slot' => $activeTimeSlot,
                'top_species' => $topSpecies,
            ];

            $recentPendingSettlements = $pendingSettlements
                ->take(5)
                ->values();

            $chartData = [
                'credit_used' => (float) ($wallet?->blocked_balance ?? 0),
                'credit_remaining' => (float) ($wallet?->available_balance ?? 0),
                'settlement_labels' => $recentPendingSettlements->map(function (Settlement $settlement) {
                    return '#LOT-' . str_pad((string) $settlement->lot_id, 3, '0', STR_PAD_LEFT);
                })->all(),
                'settlement_values' => $recentPendingSettlements->map(function (Settlement $settlement) {
                    return round((float) $settlement->amount, 2);
                })->all(),
            ];

            $currentWeekWins = $settlements->filter(function (Settlement $settlement) use ($currentWeekStart) {
                return $settlement->created_at && $settlement->created_at->gte($currentWeekStart);
            })->count();
            $previousWeekWins = $settlements->filter(function (Settlement $settlement) use ($previousWeekStart, $currentWeekStart) {
                return $settlement->created_at
                    && $settlement->created_at->gte($previousWeekStart)
                    && $settlement->created_at->lt($currentWeekStart);
            })->count();
            $currentMonthPurchased = (float) $paidSettlements->filter(function (Settlement $settlement) use ($currentMonthStart) {
                return $settlement->created_at && $settlement->created_at->gte($currentMonthStart);
            })->sum('amount');
            $previousMonthPurchased = (float) $paidSettlements->filter(function (Settlement $settlement) use ($previousMonthStart, $currentMonthStart) {
                return $settlement->created_at
                    && $settlement->created_at->gte($previousMonthStart)
                    && $settlement->created_at->lt($currentMonthStart);
            })->sum('amount');
            $pendingSettlementCount = $pendingSettlements->count();

            $trends = [
                'active_auctions' => $liveLots->count() . ' live, ' . $upcomingLots->count() . ' upcoming',
                'auctions_won' => $this->formatCountDelta($currentWeekWins, $previousWeekWins, 'vs last week'),
                'total_purchased' => $this->formatAmountDelta($currentMonthPurchased, $previousMonthPurchased, 'vs last month'),
                'credit_available' => 'Blocked $' . number_format((float) ($wallet?->blocked_balance ?? 0), 2),
                'pending_payments' => $pendingSettlementCount . ' settlement(s) awaiting payment',
                'win_rate' => $wonLots . ' won from ' . $distinctLotsBidOn . ' lot(s) bid',
            ];
        }

        return view('bid_web.buyer.dashboard', [
            'liveLots' => $liveLots,
            'upcomingLots' => $upcomingLots,
            'highestBids' => $highestBids,
            'stats' => $stats,
            'activeBids' => $activeBids,
            'analytics' => $analytics,
            'chartData' => $chartData,
            'trends' => $trends,
        ]);
    }

    public function remindUpcomingAuction(Request $request, int $lot): RedirectResponse
    {
        $buyerId = $this->resolveBuyerId();
        if (! $buyerId) {
            return back()->with('error', 'Buyer account not found.');
        }

        if (! Schema::hasTable('app_notifications') || ! Schema::hasColumn('app_notifications', 'user_id')) {
            return back()->with('error', 'Notifications are not available.');
        }

        $record = Lot::query()
            ->where('id', $lot)
            ->currentlyUpcoming()
            ->firstOrFail();

        $existing = AppNotification::query()
            ->where('user_id', $buyerId)
            ->where('title', 'Auction Reminder Set')
            ->where('message', 'like', '%' . $record->id . '%')
            ->latest()
            ->first();

        if ($existing) {
            return back()->with('success', 'Reminder already saved for this auction.');
        }

        $lotLabel = '#LOT-' . str_pad((string) $record->id, 4, '0', STR_PAD_LEFT);
        $startLabel = $record->auction_start_at
            ? Carbon::parse($record->auction_start_at)->format('M d, Y h:i A')
            : 'scheduled time';

        AppNotification::create([
            'user_id' => $buyerId,
            'title' => 'Auction Reminder Set',
            'message' => "Reminder saved for {$lotLabel}. Auction starts on {$startLabel}.",
            'type' => 'info',
            'data' => [
                'lot_id' => $record->id,
                'url' => route('buyer.upcoming-auction'),
            ],
        ]);

        return back()->with('success', 'Reminder saved successfully.');
    }

    public function placeBid(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lot_id' => ['required', 'integer', 'exists:lots,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $buyerId = $this->resolveAuthenticatedBuyerId();

        if (! $buyerId) {
            return redirect()->route('home.login')->with('error', 'Please log in as a buyer to place a bid.');
        }

        $lot = Lot::findOrFail($validated['lot_id']);
        if (! $lot->isCurrentlyActive()) {
            return back()->with('error', 'This auction is not active.');
        }

        $buyerName = DB::table('users')->where('id', $buyerId)->value('name') ?? 'Buyer';
        $highest = Bid::where('lot_id', $lot->id)->max('amount');
        $minimum = max((float) $lot->starting_price, (float) ($highest ?? 0) + 0.01);

        if ((float) $validated['amount'] < $minimum) {
            return back()->with('error', 'Your bid must be at least $' . number_format($minimum, 2) . '.');
        }

        $bid = DB::transaction(function () use ($buyerId, $lot, $validated) {
            if (Schema::hasTable('wallets') && Schema::hasTable('wallet_transactions')) {
                $wallet = Wallet::query()
                    ->where('user_id', $buyerId)
                    ->lockForUpdate()
                    ->first();

                if (! $wallet) {
                    $wallet = Wallet::create([
                        'user_id' => $buyerId,
                        'available_balance' => 0,
                        'blocked_balance' => 0,
                        'currency' => 'USD',
                    ]);
                }

                $quantity = max(1, (float) ($lot->quantity ?? 1));
                $requiredTotal = (float) $validated['amount'] * $quantity;

                $currentHold = WalletTransaction::query()
                    ->where('wallet_id', $wallet->id)
                    ->where('type', 'hold')
                    ->where('status', 'completed')
                    ->where('reference_type', 'lot')
                    ->where('reference_id', $lot->id)
                    ->lockForUpdate()
                    ->sum('amount');

                $additionalHold = max(0, $requiredTotal - (float) $currentHold);
                $canCreateHold = $additionalHold > 0 && (float) $wallet->available_balance >= $additionalHold;

                if ($canCreateHold) {
                    $wallet->update([
                        'available_balance' => (float) $wallet->available_balance - $additionalHold,
                        'blocked_balance' => (float) $wallet->blocked_balance + $additionalHold,
                    ]);

                    WalletTransaction::create([
                        'wallet_id' => $wallet->id,
                        'user_id' => $buyerId,
                        'type' => 'hold',
                        'amount' => $additionalHold,
                        'status' => 'completed',
                        'reference_type' => 'lot',
                        'reference_id' => $lot->id,
                        'description' => 'Bid hold for Lot #' . $lot->id,
                    ]);
                }
            }

            return Bid::create([
                'lot_id' => $lot->id,
                'buyer_id' => $buyerId,
                'amount' => $validated['amount'],
                'status' => 'active',
            ]);
        });

        if ($lot->anti_sniping_enabled && $lot->auction_end_at) {
            $secondsRemaining = now()->diffInSeconds($lot->auction_end_at, false);

            if ($secondsRemaining <= 60) {
                $lot->forceFill([
                    'auction_end_at' => $lot->auction_end_at->copy()->addMinutes(2),
                ])->save();
            }
        }

        $this->notifySellerBidPlaced($lot, $buyerId, (float) $validated['amount']);
        event(new BidPlaced($bid, $buyerName ?? 'Buyer'));

        return back()->with('success', 'Your bid has been placed.');
    }

    private function notifySellerBidPlaced(Lot $lot, int $buyerId, float $amount): void
    {
        if (! Schema::hasTable('app_notifications') || ! Schema::hasColumn('app_notifications', 'user_id')) {
            return;
        }

        if (! $lot->seller_id) {
            return;
        }

        $lotLabel = '#LOT-' . str_pad((string) $lot->id, 4, '0', STR_PAD_LEFT);
        $buyerName = DB::table('users')->where('id', $buyerId)->value('name') ?? 'Buyer';
        $amountLabel = '$' . number_format($amount, 2) . '/kg';
        $url = route('seller.lot-list') . '?search=' . urlencode((string) $lot->id);

        AppNotification::create([
            'user_id' => $lot->seller_id,
            'title' => 'New Bid Received',
            'message' => "{$buyerName} placed a bid of {$amountLabel} on {$lotLabel}.",
            'type' => 'info',
            'data' => [
                'lot_id' => $lot->id,
                'buyer_id' => $buyerId,
                'amount' => $amount,
                'url' => $url,
            ],
        ]);
    }

    public function activeAuction(): View
    {
        $buyerId = $this->resolveAuthenticatedBuyerId() ?? $this->resolveBuyerId();
        $wallet = $buyerId && Schema::hasTable('wallets')
            ? $this->getOrCreateWallet($buyerId)
            : null;

        $liveLots = Lot::query()
            ->currentlyActive()
            ->orderByDesc('auction_start_at')
            ->get();

          

        $lotIds = $liveLots->pluck('id')->unique()->values();
        $highestBids = $lotIds->isEmpty()
            ? collect()
            : Bid::query()
                ->selectRaw('lot_id, MAX(amount) as max_amount')
                ->whereIn('lot_id', $lotIds)
                ->groupBy('lot_id')
                ->pluck('max_amount', 'lot_id');

        return view('bid_web.buyer.active-auction', [
            'liveLots' => $liveLots,
            'highestBids' => $highestBids,
            'wallet' => $wallet,
        ]);
    }

    public function profileSettings(): View|RedirectResponse
    {
        $buyer = $this->getAuthenticatedBuyer();

        if (! $buyer) {
            return redirect()->route('home.login')->with('error', 'Please log in as a buyer to access your profile.');
        }

        return view('bid_web.buyer.profile-settings', [
            'buyer' => $buyer,
            'profileOptions' => $this->profileOptions(),
        ]);
    }

    public function updateProfileSettings(Request $request): RedirectResponse
    {
        $buyer = $this->getAuthenticatedBuyer();

        if (! $buyer) {
            return redirect()->route('home.login')->with('error', 'Please log in as a buyer to update your profile.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $buyer->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'profile_image' => ['nullable', 'image', 'max:5120'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'company_legal_name' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'business_address' => ['nullable', 'string', 'max:500'],
            'country' => ['nullable', 'string', 'max:100'],
            'website' => ['nullable', 'string', 'max:255'],
            'company_registration_number' => ['nullable', 'string', 'max:255'],
            'monthly_volume' => ['nullable', 'string', 'max:50'],
            'bank_country' => ['nullable', 'string', 'max:100'],
            'business_type' => ['nullable', 'array'],
            'business_type.*' => ['string', 'max:100'],
            'interested_in' => ['nullable', 'array'],
            'interested_in.*' => ['string', 'max:50'],
            'preferred_delivery' => ['nullable', 'array'],
            'preferred_delivery.*' => ['string', 'max:50'],
            'preferred_payment' => ['nullable', 'array'],
            'preferred_payment.*' => ['string', 'max:50'],
            'company_registration_file' => ['nullable', 'file', 'max:5120'],
            'id_file' => ['nullable', 'file', 'max:5120'],
            'import_license_file' => ['nullable', 'file', 'max:5120'],
        ]);

        $companyRegistrationFile = $request->file('company_registration_file');
        $idFile = $request->file('id_file');
        $importLicenseFile = $request->file('import_license_file');
        $profileImage = $request->file('profile_image');

        $buyer->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'job_title' => $validated['job_title'] ?? null,
            'company_legal_name' => $validated['company_legal_name'] ?? null,
            'city' => $validated['city'] ?? null,
            'business_address' => $validated['business_address'] ?? null,
            'country' => $validated['country'] ?? null,
            'website' => $validated['website'] ?? null,
            'company_registration_number' => $validated['company_registration_number'] ?? null,
            'monthly_volume' => $validated['monthly_volume'] ?? null,
            'bank_country' => $validated['bank_country'] ?? null,
            'business_type' => $this->normalizeJsonArrayInput($validated['business_type'] ?? []),
            'interested_in' => $this->normalizeJsonArrayInput($validated['interested_in'] ?? []),
            'preferred_delivery' => $this->normalizeJsonArrayInput($validated['preferred_delivery'] ?? []),
            'preferred_payment' => $this->normalizeJsonArrayInput($validated['preferred_payment'] ?? []),
            'is_registered_business' => $request->boolean('is_registered_business'),
            'accepted_terms' => $request->boolean('accepted_terms'),
            'bank_transfer_validated' => $request->boolean('bank_transfer_validated'),
        ]);

        if ($companyRegistrationFile) {
            $buyer->company_registration_file = $companyRegistrationFile->store('buyer-kyc', 'public');
        }

        if ($idFile) {
            $buyer->id_file = $idFile->store('buyer-kyc', 'public');
        }

        if ($importLicenseFile) {
            $buyer->import_license_file = $importLicenseFile->store('buyer-kyc', 'public');
        }

        if ($profileImage) {
            $buyer->profile_image = $profileImage->store('buyer-profile', 'public');
        }

        $buyer->save();

        $request->session()->put('logged_user', [
            'id' => $buyer->id,
            'name' => $buyer->name,
            'email' => $buyer->email,
            'type' => $buyer->type,
            'profile_image' => $buyer->profile_image,
        ]);

        return redirect()
            ->route('buyer.profile-settings')
            ->with('success', 'Profile updated successfully.');
    }

    public function upcomingAuction(): View
    {
        $upcomingLots = Lot::query()
            ->currentlyUpcoming()
            ->orderBy('auction_start_at')
            ->paginate(12)
            ->withQueryString();

        $liveCount = Lot::query()
            ->currentlyActive()
            ->count();

        return view('bid_web.buyer.upcoming-auction', [
            'upcomingLots' => $upcomingLots,
            'liveCount' => $liveCount,
        ]);
    }

    public function liveAuction(Request $request): View
    {
        $buyerId = $this->resolveAuthenticatedBuyerId() ?? $this->resolveBuyerId();
        $wallet = $buyerId && Schema::hasTable('wallets')
            ? $this->getOrCreateWallet($buyerId)
            : null;

        $lotId = $request->query('lot');

        $lot = $lotId
            ? Lot::query()->currentlyActive()->find($lotId)
            : Lot::query()->currentlyActive()->orderByDesc('auction_start_at')->first();

        $bids = collect();
        $currentPrice = 0;
        $messages = collect();
        $activeBidders = 0;

        if ($lot) {
            $bids = Bid::with('buyer')
                ->where('lot_id', $lot->id)
                ->latest()
                ->take(20)
                ->get();

            $highest = Bid::where('lot_id', $lot->id)->max('amount');
            $currentPrice = $highest ?? $lot->starting_price;

            $messages = AuctionMessage::with('buyer')
                ->where('lot_id', $lot->id)
                ->latest()
                ->take(30)
                ->get();

            $activeBidders = Bid::where('lot_id', $lot->id)
                ->distinct('buyer_id')
                ->count('buyer_id');
        }

        return view('bid_web.buyer.live-auction', [
            'lot' => $lot,
            'bids' => $bids,
            'messages' => $messages,
            'currentPrice' => $currentPrice,
            'activeBidders' => $activeBidders,
            'wallet' => $wallet,
        ]);
    }

    public function liveAuctionData(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lot_id' => ['required', 'integer', 'exists:lots,id'],
        ]);

        $lot = Lot::find($validated['lot_id']);

        if (! $lot || ! $lot->isCurrentlyActive()) {
            return response()->json([
                'current_price' => 0,
                'ends_at' => null,
                'bids' => [],
                'messages' => [],
            ]);
        }

        $highest = Bid::where('lot_id', $lot->id)->max('amount');
        $currentPrice = $highest ?? $lot->starting_price;

        $bids = Bid::with('buyer')
            ->where('lot_id', $lot->id)
            ->latest()
            ->take(20)
            ->get()
            ->map(function (Bid $bid) {
                return [
                    'id' => $bid->id,
                    'buyer' => $bid->buyer->name ?? 'Buyer',
                    'amount' => (float) $bid->amount,
                    'time' => optional($bid->created_at)->diffForHumans(),
                ];
            })
            ->values();

        $messages = AuctionMessage::with('buyer')
            ->where('lot_id', $lot->id)
            ->latest()
            ->take(30)
            ->get()
            ->map(function (AuctionMessage $message) {
                return [
                    'id' => $message->id,
                    'buyer' => $message->buyer->name ?? 'Buyer',
                    'message' => $message->message,
                    'time' => optional($message->created_at)->diffForHumans(),
                ];
            })
            ->values();

        return response()->json([
            'current_price' => (float) $currentPrice,
            'ends_at' => optional($lot->auction_end_at)->toIso8601String(),
            'bids' => $bids,
            'messages' => $messages,
        ]);
    }

    public function postAuctionMessage(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'lot_id' => ['required', 'integer', 'exists:lots,id'],
            'message' => ['required', 'string', 'max:500'],
        ]);

        $buyerId = $this->resolveAuthenticatedBuyerId();

        if (! $buyerId) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Please log in as a buyer to continue.'], 401);
            }
            return redirect()->route('home.login')->with('error', 'Please log in as a buyer to continue.');
        }

        $lot = Lot::find($validated['lot_id']);
        if (! $lot || ! $lot->isCurrentlyActive()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'This auction is no longer active.'], 422);
            }

            return back()->with('error', 'This auction is no longer active.');
        }

        $message = AuctionMessage::create([
            'lot_id' => $validated['lot_id'],
            'buyer_id' => $buyerId,
            'message' => $validated['message'],
        ]);

        $buyerName = DB::table('users')->where('id', $buyerId)->value('name') ?? 'Buyer';
        event(new AuctionMessageSent($message, $buyerName));

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $message->id,
                'buyer' => $buyerName,
                'message' => $message->message,
                'created_at' => optional($message->created_at)->toIso8601String(),
            ]);
        }

        return back();
    }

    public function wonAuction(): View
    {
        $buyerId = session('logged_user.id');
        if (! $buyerId) {
            $buyerId = DB::table('users')->where('type', 'buyer')->value('id');
        }

        $search = trim((string) request()->query('search', ''));
        $statusFilter = strtolower((string) request()->query('status', 'all'));

        $liveCount = Lot::query()
            ->currentlyActive()
            ->count();

        $settlementsQuery = Settlement::query()
            ->where('buyer_id', $buyerId)
            ->with('lot')
            ->when($statusFilter !== 'all', function ($query) use ($statusFilter) {
                return $query->where('status', $statusFilter);
            })
            ->when($search !== '', function ($query) use ($search) {
                $trimmed = ltrim($search, '#');
                $query->where(function ($sub) use ($search, $trimmed) {
                    if (ctype_digit($trimmed)) {
                        $sub->orWhere('lot_id', $trimmed);
                    }
                    $sub->orWhereHas('lot', function ($lotQuery) use ($search) {
                        $lotQuery->where('title', 'like', '%' . $search . '%')
                            ->orWhere('species', 'like', '%' . $search . '%');
                    });
                });
            });

        $settlements = (clone $settlementsQuery)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalDue = Settlement::query()
            ->where('buyer_id', $buyerId)
            ->where('status', 'pending')
            ->sum('amount');

        $deadlineService = app(SettlementLifecycleService::class);

        $settlements->getCollection()->transform(function (Settlement $settlement) use ($deadlineService) {
            $settlement->setAttribute('deadline_at', $deadlineService->paymentDeadlineFor($settlement));
            return $settlement;
        });

        return view('bid_web.buyer.won-auction', [
            'settlements' => $settlements,
            'liveCount' => $liveCount,
            'totalDue' => $totalDue,
            'search' => $search,
            'statusFilter' => $statusFilter,
        ]);
    }

    public function transactions(Request $request): View
    {
        $buyerId = session('logged_user.id');
        if (! $buyerId) {
            $buyerId = DB::table('users')->where('type', 'buyer')->value('id');
        }

        $wallet = Schema::hasTable('wallets')
            ? $this->getOrCreateWallet($buyerId)
            : null;

        $settlements = Settlement::query()
            ->where('buyer_id', $buyerId)
            ->latest()
            ->get();

        $pendingTotal = $settlements->where('status', 'pending')->sum('amount');
        $paidTotal = $settlements->where('status', 'paid')->sum('amount');

        $walletTransactions = Schema::hasTable('wallet_transactions')
            ? WalletTransaction::query()
                ->where('user_id', $buyerId)
                ->latest()
                ->take(50)
                ->get()
            : collect();

        $search = trim((string) $request->query('search', ''));
        $dateFilter = $request->query('date');
        $statusFilter = strtolower((string) $request->query('status', 'all'));

        $refundTransactions = $walletTransactions->filter(function (WalletTransaction $tx) {
            $description = Str::lower((string) ($tx->description ?? ''));
            return in_array($tx->type, ['release', 'credit'], true)
                || str_contains($description, 'refund')
                || str_contains($description, 'release');
        })->values();

        $refundsIssued = $refundTransactions->sum('amount');

        $activeMethods = collect()
            ->merge($settlements->pluck('payment_provider'))
            ->merge($walletTransactions->pluck('payment_provider'))
            ->filter()
            ->map(fn ($provider) => Str::lower((string) $provider))
            ->unique()
            ->count();

        $filteredSettlements = $settlements
            ->when($search !== '', function ($collection) use ($search) {
                $term = Str::lower($search);
                $numericLot = preg_replace('/^lot[-#]*/i', '', ltrim($search, '#')) ?? $search;

                return $collection->filter(function (Settlement $settlement) use ($term, $numericLot) {
                    $reference = Str::lower((string) ($settlement->payment_reference ?? ''));
                    $provider = Str::lower((string) ($settlement->payment_provider ?? ''));
                    $lotLabel = (string) $settlement->lot_id;

                    return ($numericLot !== '' && ctype_digit($numericLot) && (string) $settlement->lot_id === $numericLot)
                        || str_contains($reference, $term)
                        || str_contains($provider, $term)
                        || str_contains($lotLabel, $term);
                });
            })
            ->when($dateFilter, function ($collection) use ($dateFilter) {
                return $collection->filter(fn (Settlement $settlement) => optional($settlement->created_at)->toDateString() === $dateFilter);
            })
            ->when($statusFilter !== 'all', function ($collection) use ($statusFilter) {
                return $collection->filter(fn (Settlement $settlement) => Str::lower((string) ($settlement->status ?? '')) === $statusFilter);
            })
            ->values();

        $cardTransactions = $settlements
            ->filter(fn (Settlement $settlement) => Str::lower((string) ($settlement->payment_provider ?? '')) === 'stripe')
            ->take(10)
            ->values();

        $bankTransactions = $settlements
            ->filter(function (Settlement $settlement) {
                return in_array(Str::lower((string) ($settlement->payment_provider ?? '')), ['bank_transfer', 'waafipay'], true);
            })
            ->take(10)
            ->values();

        return view('bid_web.buyer.transactions', [
            'settlements' => $filteredSettlements,
            'pendingTotal' => $pendingTotal,
            'paidTotal' => $paidTotal,
            'availableBalance' => $wallet?->available_balance ?? 0,
            'blockedBalance' => $wallet?->blocked_balance ?? 0,
            'walletTransactions' => $walletTransactions,
            'refundsIssued' => $refundsIssued,
            'activeMethods' => $activeMethods,
            'search' => $search,
            'dateFilter' => $dateFilter,
            'statusFilter' => $statusFilter,
            'cardTransactions' => $cardTransactions,
            'bankTransactions' => $bankTransactions,
            'refundTransactions' => $refundTransactions,
        ]);
    }

    private function getOrCreateWallet(int $userId): Wallet
    {
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $userId],
            ['available_balance' => 0, 'blocked_balance' => 0, 'currency' => 'USD']
        );

        return $wallet;
    }

    private function getAuthenticatedBuyer(): ?User
    {
        $buyerId = $this->resolveAuthenticatedBuyerId();

        if (! $buyerId) {
            return null;
        }

        $buyer = User::query()
            ->where('id', $buyerId)
            ->where('type', 'buyer')
            ->first();

        if (! $buyer) {
            return null;
        }

        foreach (['business_type', 'interested_in', 'preferred_delivery', 'preferred_payment'] as $field) {
            $buyer->{$field} = $this->decodeJsonArray($buyer->{$field} ?? null);
        }

        return $buyer;
    }

    private function resolveBuyerId(): ?int
    {
        $buyerId = session('logged_user.id');
        if ($buyerId) {
            return (int) $buyerId;
        }

        $fallbackId = DB::table('users')->where('type', 'buyer')->value('id');

        return $fallbackId ? (int) $fallbackId : null;
    }

    private function resolveAuthenticatedBuyerId(): ?int
    {
        $buyerId = session('logged_user.id');
        $userType = session('logged_user.type');

        if (! $buyerId || $userType !== 'buyer') {
            return null;
        }

        return (int) $buyerId;
    }

    private function formatCountDelta(int $current, int $previous, string $suffix): string
    {
        $delta = $current - $previous;
        $prefix = $delta > 0 ? '+' : '';

        return $prefix . $delta . ' ' . $suffix;
    }

    private function formatAmountDelta(float $current, float $previous, string $suffix): string
    {
        $delta = $current - $previous;
        $prefix = $delta > 0 ? '+' : '';

        return $prefix . '$' . number_format($delta, 2) . ' ' . $suffix;
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

    private function normalizeJsonArrayInput(array $value): string
    {
        $filtered = array_values(array_filter($value, fn ($item) => filled($item)));

        return json_encode($filtered);
    }

    private function profileOptions(): array
    {
        return [
            'business_type' => [
                'Hotels',
                'Restaurants',
                'Supermarkets',
                'Catering',
                'Bulk Importer',
                'Distributor',
                'Reseller',
                'Processing Company',
            ],
            'interested_in' => [
                'Fresh',
                'Frozen',
                'Both',
            ],
            'monthly_volume' => [
                '100 - 500 kg',
                '500 - 1000 kg',
                '1000 - 5000 kg',
                '5000+ kg',
            ],
            'preferred_delivery' => [
                'Air',
                'Sea',
                'Local Pickup',
            ],
            'preferred_payment' => [
                'Bank Transfer',
                'Online Payment',
                'LC',
            ],
            'country' => [
                'France',
                'Spain',
                'Italy',
                'India',
            ],
        ];
    }

    public function notificationData(Request $request): JsonResponse
    {
        $buyerId = $this->resolveAuthenticatedBuyerId();

        if (! $buyerId) {
            return response()->json([
                'unread_count' => 0,
                'items' => [],
            ]);
        }

        $limit = (int) $request->query('limit', 5);
        $limit = max(1, min($limit, 50));

        $notifications = AppNotification::where('user_id', $buyerId)
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
        });

        $unreadCount = AppNotification::where('user_id', $buyerId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'unread_count' => $unreadCount,
            'items' => $items,
        ]);
    }

    public function markNotificationRead(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $buyerId = $this->resolveAuthenticatedBuyerId();

        if ($buyerId) {
            AppNotification::where('id', $validated['id'])
                ->where('user_id', $buyerId)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
        }

        $unreadCount = $buyerId
            ? AppNotification::where('user_id', $buyerId)->where('is_read', false)->count()
            : 0;

        return response()->json([
            'unread_count' => $unreadCount,
        ]);
    }

    public function markAllNotificationsRead(): JsonResponse
    {
        $buyerId = $this->resolveAuthenticatedBuyerId();

        if ($buyerId) {
            AppNotification::where('user_id', $buyerId)
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
}


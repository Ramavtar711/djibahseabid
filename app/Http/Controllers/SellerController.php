<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\AppNotification;
use App\Models\AuctionMessage;
use App\Models\Bid;
use App\Models\Lot;
use App\Models\Settlement;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SellerController extends Controller
{
    public function dashboard(): View
    {
        $this->syncScheduledAuctionsToActive();

        $sellerId = session('logged_user.id');
        if (! $sellerId) {
            $sellerId = DB::table('users')->where('type', 'seller')->value('id');
        }

        $baseQuery = Lot::query()
            ->when($sellerId, fn ($query) => $query->where('seller_id', $sellerId));

        $activeAuctions = (clone $baseQuery)
            ->whereIn('status', ['active auction', 'active'])
            ->orderByDesc('auction_start_at')
            ->take(6)
            ->get();

        $activeLotIds = $activeAuctions->pluck('id')->values();
        $highestBids = $activeLotIds->isEmpty()
            ? collect()
            : Bid::query()
                ->selectRaw('lot_id, MAX(amount) as max_amount')
                ->whereIn('lot_id', $activeLotIds)
                ->groupBy('lot_id')
                ->pluck('max_amount', 'lot_id');

        $bidCounts = $activeLotIds->isEmpty()
            ? collect()
            : Bid::query()
                ->selectRaw('lot_id, COUNT(*) as total')
                ->whereIn('lot_id', $activeLotIds)
                ->groupBy('lot_id')
                ->pluck('total', 'lot_id');

        $activeAuctions = $activeAuctions->map(function (Lot $lot) use ($highestBids, $bidCounts) {
            $lot->setAttribute('current_price', (float) ($highestBids[$lot->id] ?? $lot->starting_price ?? 0));
            $lot->setAttribute('bids_count', (int) ($bidCounts[$lot->id] ?? 0));
            return $lot;
        });

        $soldLots = (clone $baseQuery)
            ->whereIn('status', ['sold', 'unsold'])
            ->withCount('bids')
            ->orderByDesc('auction_end_at')
            ->take(6)
            ->get();

        $settlements = Settlement::query()
            ->when($sellerId, fn ($query) => $query->where('seller_id', $sellerId))
            ->get()
            ->keyBy('lot_id');

        $soldLots = $soldLots->map(function (Lot $lot) use ($settlements) {
            $settlement = $settlements->get($lot->id);
            $winningPrice = (float) ($lot->final_price ?? 0);
            $quantity = (float) ($lot->quantity ?? 0);
            $grossAmount = round($winningPrice * $quantity, 2);
            $commissionAmount = (float) ($settlement?->commission_amount ?? round($grossAmount * 0.10, 2));
            $netAmount = (float) ($settlement?->net_amount ?? round($grossAmount - $commissionAmount, 2));

            $lot->setAttribute('settlement_status_label', $settlement?->status ?? ($lot->status === 'sold' ? 'pending' : null));
            $lot->setAttribute('gross_amount', $grossAmount);
            $lot->setAttribute('commission_amount', $commissionAmount);
            $lot->setAttribute('net_amount', $netAmount);

            return $lot;
        });

        $settledLots = $soldLots->where('status', 'sold');
        $activeAuctionsCount = (clone $baseQuery)->whereIn('status', ['active auction', 'active'])->count();
        $totalLotsCount = (clone $baseQuery)->count();
        $pendingPayoutTotal = $settledLots->where('settlement_status_label', 'pending')->sum('net_amount');
        $paidAmountTotal = $settledLots->where('settlement_status_label', 'paid')->sum('net_amount');
        $totalRevenue = $settledLots->sum('gross_amount');
        $averagePricePerKg = round($settledLots->avg('final_price') ?? 0, 2);
        $soldCount = (clone $baseQuery)->where('status', 'sold')->count();
        $unsoldCount = (clone $baseQuery)->where('status', 'unsold')->count();
        $completedCount = $soldCount + $unsoldCount;
        $conversionRate = $completedCount > 0 ? round(($soldCount / $completedCount) * 100) : 0;
        $unsoldRate = $completedCount > 0 ? round(($unsoldCount / $completedCount) * 100) : 0;

        return view('bid_web.seller.dashboard', [
            'activeAuctionsCount' => $activeAuctionsCount,
            'totalLotsCount' => $totalLotsCount,
            'activeAuctions' => $activeAuctions,
            'totalRevenue' => $totalRevenue,
            'averagePricePerKg' => $averagePricePerKg,
            'conversionRate' => $conversionRate,
            'unsoldRate' => $unsoldRate,
            'pendingPayoutTotal' => $pendingPayoutTotal,
            'paidAmountTotal' => $paidAmountTotal,
            'soldLots' => $soldLots,
        ]);
    }

    public function createLot(): View
    {
        return view('bid_web.seller.create-lot');
    }

    public function storeLot(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'species' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'starting_price' => ['required', 'numeric', 'min:0'],
            'harvest_date' => ['required', 'date'],
            'storage_temperature' => ['required', 'string', 'max:50'],
            'notes' => ['required', 'string', 'max:1000'],
            'product_image' => ['required', 'image', 'max:5120'],
            'health_certificate' => ['required', 'file', 'max:5120'],
            'additional_documents' => ['required', 'file', 'max:5120'],
        ], [
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

        $sellerId = session('logged_user.id');
        if (! $sellerId) {
            $sellerId = DB::table('users')->where('type', 'seller')->value('id');
        }

        $lotData = [
            'seller_id' => $sellerId,
            'title' => $validated['title'],
            'species' => $validated['species'],
            'quantity' => $validated['quantity'],
            'starting_price' => $validated['starting_price'],
            'harvest_date' => $validated['harvest_date'],
            'storage_temperature' => $validated['storage_temperature'] ?? null,
            'notes' => $validated['notes'] ?? null,
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

        $this->notifyQcNewLot($lot);
        $this->notifySellerLotSubmitted($lot);

        return redirect()
            ->route('seller.lot-list')
            ->with('success', 'Lot submitted for QC review.');
    }

    public function lotList(Request $request): View
    {
        $sellerId = session('logged_user.id');
        $baseQuery = Lot::query()
            ->when($sellerId, fn ($query) => $query->where('seller_id', $sellerId));

        $statusFilter = $request->query('status');
        $speciesFilter = $request->query('species');
        $search = trim((string) $request->query('search', ''));
        $dateFilter = $request->query('date');

        $filteredQuery = (clone $baseQuery)
            ->when($statusFilter, fn ($query) => $query->whereRaw('LOWER(status) = ?', [Str::lower($statusFilter)]))
            ->when($speciesFilter, fn ($query) => $query->where('species', $speciesFilter))
            ->when($search, function ($query) use ($search) {
                $trimmed = ltrim($search, '#');
                $query->where(function ($sub) use ($search, $trimmed) {
                    if (ctype_digit($trimmed)) {
                        $sub->orWhere('id', $trimmed);
                    }

                    $sub->orWhere('title', 'like', '%' . $search . '%')
                        ->orWhere('species', 'like', '%' . $search . '%');
                });
            })
            ->when($dateFilter, fn ($query) => $query->whereDate('harvest_date', $dateFilter));

        $lots = $filteredQuery->latest()->get();

        $totalLots = (clone $baseQuery)->count();
        $pendingValidationCount = (clone $baseQuery)->where('status', 'pending qc')->count();
        $activeAuctionsCount = (clone $baseQuery)->whereIn('status', ['active auction', 'active'])->count();
        $soldLotsCount = (clone $baseQuery)->where('status', 'sold')->count();

        $statusOptions = (clone $baseQuery)
            ->select('status')
            ->distinct()
            ->pluck('status')
            ->filter()
            ->map(fn ($status) => Str::lower((string) $status))
            ->unique()
            ->values();

        $speciesOptions = (clone $baseQuery)
            ->whereNotNull('species')
            ->distinct()
            ->orderBy('species')
            ->pluck('species');

        return view('bid_web.seller.lot-list', compact(
            'lots',
            'totalLots',
            'pendingValidationCount',
            'activeAuctionsCount',
            'soldLotsCount',
            'statusOptions',
            'speciesOptions'
        ));
    }

    public function lotDetails(?Lot $lot = null): View|RedirectResponse
    {
        $sellerId = session('logged_user.id');
        if (! $sellerId) {
            $sellerId = DB::table('users')->where('type', 'seller')->value('id');
        }

        $sellerLots = Lot::query()
            ->when($sellerId, fn ($query) => $query->where('seller_id', $sellerId));

        if (! $lot) {
            $lot = (clone $sellerLots)->latest()->first();
        }

        if (! $lot || ($sellerId && (int) $lot->seller_id !== (int) $sellerId)) {
            return redirect()->route('seller.lot-list')->with('error', 'Lot details not found.');
        }

        $lot->loadMissing(['seller', 'winner']);

        $highestBidAmount = (float) (Bid::query()
            ->where('lot_id', $lot->id)
            ->max('amount') ?? $lot->starting_price ?? 0);

        $totalBids = Bid::query()
            ->where('lot_id', $lot->id)
            ->count();

        $highestBidRow = Bid::query()
            ->with('buyer')
            ->where('lot_id', $lot->id)
            ->orderByDesc('amount')
            ->latest('id')
            ->first();

        $highestBidder = $highestBidRow?->buyer?->name
            ?? $lot->winner?->name
            ?? 'No bids yet';

        $priceSummary = [
            'winning_price' => (float) ($lot->final_price ?: $highestBidAmount),
            'quantity' => (float) ($lot->quantity ?? 0),
        ];
        $priceSummary['gross_total'] = $priceSummary['winning_price'] * $priceSummary['quantity'];
        $priceSummary['commission'] = round($priceSummary['gross_total'] * 0.10, 2);
        $priceSummary['net_revenue'] = round($priceSummary['gross_total'] - $priceSummary['commission'], 2);

        return view('bid_web.seller.lot-details', [
            'lot' => $lot,
            'highestBidAmount' => $highestBidAmount,
            'highestBidder' => $highestBidder,
            'totalBids' => $totalBids,
            'priceSummary' => $priceSummary,
        ]);
    }

    public function liveView(Request $request): View|RedirectResponse
    {
        $this->syncScheduledAuctionsToActive();

        $sellerId = session('logged_user.id');
        if (! $sellerId) {
            $sellerId = DB::table('users')->where('type', 'seller')->value('id');
        }

        $lotId = $request->query('lot');

        $sellerLots = Lot::query()
            ->when($sellerId, fn ($query) => $query->where('seller_id', $sellerId))
            ->whereIn('status', ['active auction', 'active']);

        $lot = $lotId
            ? (clone $sellerLots)->find($lotId)
            : (clone $sellerLots)->orderBy('auction_end_at')->first();

        if (! $lot) {
            return redirect()->route('seller.active-auction')->with('error', 'Active lot not found.');
        }

        $lot->loadMissing(['seller', 'winner']);

        $bids = Bid::query()
            ->with('buyer')
            ->where('lot_id', $lot->id)
            ->latest()
            ->take(20)
            ->get();

        $highestBid = $bids->sortByDesc('amount')->first();
        $currentPrice = (float) ($highestBid?->amount ?? $lot->starting_price ?? 0);
        $openingPrice = (float) ($lot->starting_price ?? 0);
        $delta = max(0, $currentPrice - $openingPrice);
        $totalBids = $bids->count();
        $highestBidder = $highestBid?->buyer?->name ?? $lot->winner?->name ?? 'No bids yet';

        $recentMessages = AuctionMessage::query()
            ->with('buyer')
            ->where('lot_id', $lot->id)
            ->latest()
            ->take(10)
            ->get();

        $chartPoints = Bid::query()
            ->where('lot_id', $lot->id)
            ->oldest()
            ->take(8)
            ->get(['amount', 'created_at']);

        if ($chartPoints->isEmpty()) {
            $chartLabels = ['Opening', 'Current'];
            $chartValues = [$openingPrice, $currentPrice];
        } else {
            $chartLabels = $chartPoints->map(function (Bid $bid) {
                return optional($bid->created_at)->format('H:i') ?? 'Bid';
            })->all();

            $chartValues = $chartPoints->map(function (Bid $bid) {
                return (float) $bid->amount;
            })->all();
        }

        return view('bid_web.seller.live-view', [
            'lot' => $lot,
            'bids' => $bids,
            'recentMessages' => $recentMessages,
            'currentPrice' => $currentPrice,
            'openingPrice' => $openingPrice,
            'priceDelta' => $delta,
            'totalBids' => $totalBids,
            'highestBidder' => $highestBidder,
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
            'countdownLabel' => $this->formatClockCountdown($lot->auction_end_at),
        ]);
    }

    public function activeAuction(): View
    {
        $this->syncScheduledAuctionsToActive();

        $sellerId = session('logged_user.id');
        if (! $sellerId) {
            $sellerId = DB::table('users')->where('type', 'seller')->value('id');
        }

        $filter = request('filter', 'all');
        $now = now();

        $query = Lot::query()
            ->when($sellerId, fn ($builder) => $builder->where('seller_id', $sellerId))
            ->whereIn('status', ['active auction', 'active']);

        if ($filter === 'fresh') {
            $query->whereDate('harvest_date', '>=', $now->copy()->subDays(7)->toDateString());
        }

        if ($filter === 'priority') {
            $query->whereNotNull('auction_end_at')
                ->whereBetween('auction_end_at', [$now, $now->copy()->addMinutes(15)]);
        }

        $lots = $query
            ->orderBy('auction_end_at')
            ->paginate(8)
            ->withQueryString();

        $lotIds = $lots->pluck('id')->values();

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

        $lots->getCollection()->transform(function (Lot $lot) use ($highestBids, $bidCounts, $now) {
            $remainingSeconds = $lot->auction_end_at?->isFuture()
                ? $now->diffInSeconds($lot->auction_end_at, false)
                : null;

            $lot->setAttribute('current_price', (float) ($highestBids[$lot->id] ?? $lot->starting_price ?? 0));
            $lot->setAttribute('bids_count', (int) ($bidCounts[$lot->id] ?? 0));
            $lot->setAttribute('remaining_label', $this->formatRemainingTime($remainingSeconds));
            $lot->setAttribute('priority_class', $this->auctionPriorityClass($remainingSeconds));

            return $lot;
        });

        return view('bid_web.seller.active-auction', [
            'lots' => $lots,
            'activeFilter' => $filter,
        ]);
    }

    public function pendingValidation(Request $request): View
    {
        $sellerId = session('logged_user.id');
        if (! $sellerId) {
            $sellerId = DB::table('users')->where('type', 'seller')->value('id');
        }

        $baseQuery = Lot::query()
            ->when($sellerId, fn ($query) => $query->where('seller_id', $sellerId))
            ->whereIn('status', ['draft', 'pending qc']);

        $statusFilter = $request->query('status');
        $speciesFilter = $request->query('species');
        $search = trim((string) $request->query('search', ''));
        $dateFilter = $request->query('date');

        $filteredQuery = (clone $baseQuery)
            ->when($statusFilter, fn ($query) => $query->whereRaw('LOWER(status) = ?', [Str::lower($statusFilter)]))
            ->when($speciesFilter, fn ($query) => $query->where('species', $speciesFilter))
            ->when($search, function ($query) use ($search) {
                $trimmed = ltrim($search, '#');
                $numericLotId = preg_replace('/^lot/i', '', $trimmed) ?? $trimmed;

                $query->where(function ($sub) use ($search, $trimmed, $numericLotId) {
                    if (ctype_digit($numericLotId)) {
                        $sub->orWhere('id', $numericLotId);
                    }

                    $sub->orWhere('title', 'like', '%' . $search . '%')
                        ->orWhere('species', 'like', '%' . $search . '%');
                });
            })
            ->when($dateFilter, fn ($query) => $query->whereDate('created_at', $dateFilter));

        $pendingLots = $filteredQuery
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $today = now()->toDateString();

        $pendingLotsCount = (clone $baseQuery)->count();
        $submittedTodayCount = (clone $baseQuery)->whereDate('created_at', $today)->count();
        $underQcCount = (clone $baseQuery)
            ->where('status', 'pending qc')
            ->where(function ($query) {
                $query->whereNotNull('qc_verified_boxes')
                    ->orWhereNotNull('qc_actual_weight')
                    ->orWhereNotNull('qc_temperature')
                    ->orWhereNotNull('qc_notes')
                    ->orWhere('qc_documents_verified', true);
            })
            ->count();
        $rejectedCount = (clone $baseQuery)->where('status', 'draft')->count();

        $statusOptions = (clone $baseQuery)
            ->select('status')
            ->distinct()
            ->pluck('status')
            ->filter()
            ->map(fn ($status) => Str::lower((string) $status))
            ->unique()
            ->values();

        $speciesOptions = (clone $baseQuery)
            ->whereNotNull('species')
            ->distinct()
            ->orderBy('species')
            ->pluck('species');

        return view('bid_web.seller.pending-validation', [
            'pendingLots' => $pendingLots,
            'pendingLotsCount' => $pendingLotsCount,
            'submittedTodayCount' => $submittedTodayCount,
            'underQcCount' => $underQcCount,
            'rejectedCount' => $rejectedCount,
            'statusOptions' => $statusOptions,
            'speciesOptions' => $speciesOptions,
        ]);
    }

    public function approveLots(): View
    {
        $sellerId = session('logged_user.id');
        if (! $sellerId) {
            $sellerId = DB::table('users')->where('type', 'seller')->value('id');
        }

        $lots = Lot::query()
            ->when($sellerId, fn ($query) => $query->where('seller_id', $sellerId))
            ->whereIn('status', ['approved', 'scheduled', 'scheduled auction'])
            ->orderByRaw('CASE WHEN auction_start_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('auction_start_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('bid_web.seller.approve-lots', [
            'lots' => $lots,
        ]);
    }

    public function soldLots(): View
    {
        $sellerId = session('logged_user.id');
        if (! $sellerId) {
            $sellerId = DB::table('users')->where('type', 'seller')->value('id');
        }

        $lots = Lot::query()
            ->when($sellerId, fn ($query) => $query->where('seller_id', $sellerId))
            ->whereIn('status', ['sold', 'unsold'])
            ->orderByDesc('auction_end_at')
            ->paginate(10)
            ->withQueryString();

        $settlements = Settlement::query()
            ->when($sellerId, fn ($query) => $query->where('seller_id', $sellerId))
            ->get()
            ->keyBy('lot_id');

        $lots->getCollection()->transform(function (Lot $lot) use ($settlements) {
            $settlement = $settlements->get($lot->id);
            $winningPrice = (float) ($lot->final_price ?? 0);
            $quantity = (float) ($lot->quantity ?? 0);
            $grossAmount = round($winningPrice * $quantity, 2);
            $commissionAmount = (float) ($settlement?->commission_amount ?? round($grossAmount * 0.10, 2));
            $netAmount = (float) ($settlement?->net_amount ?? round($grossAmount - $commissionAmount, 2));

            $lot->setAttribute('settlement_status_label', $settlement?->status ?? ($lot->status === 'sold' ? 'pending' : null));
            $lot->setAttribute('gross_amount', $grossAmount);
            $lot->setAttribute('commission_amount', $commissionAmount);
            $lot->setAttribute('net_amount', $netAmount);
            return $lot;
        });

        return view('bid_web.seller.sold-lots', [
            'lots' => $lots,
        ]);
    }

    public function revenue(): View
    {
        $sellerId = session('logged_user.id');
        if (! $sellerId) {
            $sellerId = DB::table('users')->where('type', 'seller')->value('id');
        }

        $settlements = Settlement::query()
            ->where('seller_id', $sellerId)
            ->latest()
            ->get();

        $recentSettlements = $settlements->take(5);

        $lots = Lot::query()
            ->where('seller_id', $sellerId)
            ->whereIn('status', ['sold', 'unsold'])
            ->orderByDesc('auction_end_at')
            ->take(20)
            ->withCount('bids')
            ->get()
            ->map(function (Lot $lot) use ($settlements) {
                $settlement = $settlements->firstWhere('lot_id', $lot->id);
                $winningPrice = (float) ($lot->final_price ?? 0);
                $quantity = (float) ($lot->quantity ?? 0);
                $grossAmount = round($winningPrice * $quantity, 2);
                $commissionAmount = (float) ($settlement?->commission_amount ?? round($grossAmount * 0.10, 2));
                $netAmount = (float) ($settlement?->net_amount ?? round($grossAmount - $commissionAmount, 2));

                $lot->setAttribute('settlement_status_label', $settlement?->status ?? ($lot->status === 'sold' ? 'pending' : null));
                $lot->setAttribute('gross_amount', $grossAmount);
                $lot->setAttribute('commission_amount', $commissionAmount);
                $lot->setAttribute('net_amount', $netAmount);
                return $lot;
            });

        $settledLots = $lots->where('status', 'sold');
        $pendingTotal = $settledLots->where('settlement_status_label', 'pending')->sum('net_amount');
        $paidTotal = $settledLots->where('settlement_status_label', 'paid')->sum('net_amount');
        $grossTotal = $settledLots->sum('gross_amount');
        $commissionTotal = $settledLots->sum('commission_amount');

        return view('bid_web.seller.revenue', [
            'settlements' => $settlements,
            'recentSettlements' => $recentSettlements,
            'pendingTotal' => $pendingTotal,
            'paidTotal' => $paidTotal,
            'grossTotal' => $grossTotal,
            'commissionTotal' => $commissionTotal,
            'lots' => $lots,
        ]);
    }

    public function relistLot(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lot_id' => ['required', 'integer', 'exists:lots,id'],
        ]);

        $lot = Lot::findOrFail($validated['lot_id']);

        if ($lot->status !== 'unsold') {
            return back()->with('error', 'Only unsold lots can be relisted.');
        }

        $startAt = now()->addHours(2)->startOfMinute();
        $duration = $lot->auction_duration_minutes ?? 30;
        $endAt = (clone $startAt)->addMinutes((int) $duration);

        $lot->update([
            'status' => 'scheduled auction',
            'auction_start_at' => $startAt,
            'auction_end_at' => $endAt,
            'settlement_status' => null,
            'winner_id' => null,
            'final_price' => null,
        ]);

        return back()->with('success', 'Lot relisted and scheduled automatically.');
    }

    public function notifications(): View
    {
        return view('bid_web.seller.notifications');
    }

    public function auctionStatus(Request $request): JsonResponse
    {
        $this->syncScheduledAuctionsToActive();

        $sellerId = session('logged_user.id');
        if (! $sellerId) {
            $sellerId = DB::table('users')->where('type', 'seller')->value('id');
        }

        if (! $sellerId) {
            return response()->json([
                'active_count' => 0,
                'is_live' => false,
            ]);
        }

        $activeCount = Lot::query()
            ->where('seller_id', $sellerId)
            ->whereIn('status', ['active auction', 'active'])
            ->count();

        return response()->json([
            'active_count' => $activeCount,
            'is_live' => $activeCount > 0,
        ]);
    }

    public function activeAuctionsData(Request $request): JsonResponse
    {
        $this->syncScheduledAuctionsToActive();

        $sellerId = session('logged_user.id');
        if (! $sellerId) {
            $sellerId = DB::table('users')->where('type', 'seller')->value('id');
        }

        if (! $sellerId) {
            return response()->json([
                'items' => [],
            ]);
        }

        $lots = Lot::query()
            ->where('seller_id', $sellerId)
            ->whereIn('status', ['active auction', 'active'])
            ->orderByDesc('auction_start_at')
            ->take(6)
            ->get();

        $lotIds = $lots->pluck('id')->values();
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

        $items = $lots->map(function (Lot $lot) use ($highestBids, $bidCounts) {
            return [
                'id' => $lot->id,
                'title' => $lot->title ?? 'Lot',
                'image_url' => $lot->image_url,
                'current_price' => (float) ($highestBids[$lot->id] ?? $lot->starting_price ?? 0),
                'bids_count' => (int) ($bidCounts[$lot->id] ?? 0),
            ];
        })->values();

        return response()->json([
            'items' => $items,
        ]);
    }

    private function notifyQcNewLot(Lot $lot): void
    {
        if (! Schema::hasTable('app_notifications') || ! Schema::hasColumn('app_notifications', 'admin_id')) {
            return;
        }

        $qcAdminIds = Admin::query()
            ->whereIn('role', ['qc', 'admin'])
            ->pluck('id')
            ->all();

        if (! $qcAdminIds) {
            return;
        }

        $lotLabel = '#LOT-' . str_pad((string) $lot->id, 4, '0', STR_PAD_LEFT);
        $sellerName = DB::table('users')->where('id', $lot->seller_id)->value('name') ?? 'Seller';
        $url = route('qc.lot-subimitted-details', ['lot' => $lot->id]);

        foreach ($qcAdminIds as $qcAdminId) {
            AppNotification::create([
                'admin_id' => $qcAdminId,
                'title' => 'New Lot Submitted',
                'message' => "{$sellerName} submitted {$lotLabel} for QC review.",
                'type' => 'info',
                'data' => [
                    'lot_id' => $lot->id,
                    'url' => $url,
                ],
            ]);
        }
    }

    private function formatRemainingTime(?int $seconds): string
    {
        if ($seconds === null) {
            return 'Live';
        }

        if ($seconds <= 0) {
            return 'Closed';
        }

        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $remainingSeconds = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02dh %02dm', $hours, $minutes);
        }

        return sprintf('%02dm %02ds', $minutes, $remainingSeconds);
    }

    private function auctionPriorityClass(?int $seconds): string
    {
        if ($seconds !== null && $seconds > 0 && $seconds <= 300) {
            return 'text-danger';
        }

        if ($seconds !== null && $seconds > 300 && $seconds <= 900) {
            return 'text-warning';
        }

        return 'text-success';
    }

    private function formatClockCountdown($auctionEndAt): string
    {
        if (! $auctionEndAt) {
            return 'Live';
        }

        if (! $auctionEndAt->isFuture()) {
            return 'Auction closed';
        }

        $seconds = now()->diffInSeconds($auctionEndAt);
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $remainingSeconds = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $remainingSeconds);
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
                'status' => 'active auction',
                'updated_at' => $now,
            ]);
    }

    private function notifySellerLotSubmitted(Lot $lot): void
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
            'title' => 'Lot Submitted',
            'message' => "Your lot {$lotLabel} has been submitted for QC review.",
            'type' => 'info',
            'data' => [
                'lot_id' => $lot->id,
                'url' => $url,
            ],
        ]);
    }

    public function notificationData(Request $request): JsonResponse
    {
        $userId = session('logged_user.id');
        if (! $userId) {
            $userId = DB::table('users')->where('type', 'seller')->value('id');
        }

        if (! $userId) {
            return response()->json([
                'unread_count' => 0,
                'items' => [],
            ]);
        }

        $limit = (int) $request->query('limit', 5);
        $limit = max(1, min($limit, 50));

        $notifications = AppNotification::where('user_id', $userId)
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

        $unreadCount = AppNotification::where('user_id', $userId)
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

        $userId = session('logged_user.id');
        if (! $userId) {
            $userId = DB::table('users')->where('type', 'seller')->value('id');
        }

        if ($userId) {
            AppNotification::where('id', $validated['id'])
                ->where('user_id', $userId)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
        }

        $unreadCount = $userId
            ? AppNotification::where('user_id', $userId)->where('is_read', false)->count()
            : 0;

        return response()->json([
            'unread_count' => $unreadCount,
        ]);
    }

    public function markAllNotificationsRead(): JsonResponse
    {
        $userId = session('logged_user.id');
        if (! $userId) {
            $userId = DB::table('users')->where('type', 'seller')->value('id');
        }

        if ($userId) {
            AppNotification::where('user_id', $userId)
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

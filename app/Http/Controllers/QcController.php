<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\AudienceSegment;
use App\Models\Lot;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class QcController extends Controller
{
    public function index(): View
    {
        return $this->dashboard();
    }

    public function dashboard(): View
    {
        $todayStart = now()->startOfDay();
        $tomorrowStart = now()->addDay()->startOfDay();
        $yesterdayStart = now()->subDay()->startOfDay();

        $pendingCount = Lot::where('status', 'pending qc')->count();
        $pendingToday = Lot::where('status', 'pending qc')
            ->whereBetween('created_at', [$todayStart, $tomorrowStart])
            ->count();
        $pendingYesterday = Lot::where('status', 'pending qc')
            ->whereBetween('created_at', [$yesterdayStart, $todayStart])
            ->count();

        $approvedToday = Lot::where('status', 'approved')
            ->whereBetween('created_at', [$todayStart, $tomorrowStart])
            ->count();
        $approvedYesterday = Lot::where('status', 'approved')
            ->whereBetween('created_at', [$yesterdayStart, $todayStart])
            ->count();

        $rejectedToday = Lot::where('status', 'rejected')
            ->whereBetween('created_at', [$todayStart, $tomorrowStart])
            ->count();
        $rejectedYesterday = Lot::where('status', 'rejected')
            ->whereBetween('created_at', [$yesterdayStart, $todayStart])
            ->count();

        $tempAlertsCount = Lot::where('qc_temperature', '>', 4)->count();
        $tempAlertsToday = Lot::where('qc_temperature', '>', 4)
            ->whereBetween('created_at', [$todayStart, $tomorrowStart])
            ->count();
        $tempAlertsYesterday = Lot::where('qc_temperature', '>', 4)
            ->whereBetween('created_at', [$yesterdayStart, $todayStart])
            ->count();

        $missingDocsCount = Lot::where('qc_documents_verified', false)
            ->whereNull('documents_path')
            ->whereNull('health_certificate_path')
            ->count();
        $missingDocsToday = Lot::where('qc_documents_verified', false)
            ->whereNull('documents_path')
            ->whereNull('health_certificate_path')
            ->whereBetween('created_at', [$todayStart, $tomorrowStart])
            ->count();
        $missingDocsYesterday = Lot::where('qc_documents_verified', false)
            ->whereNull('documents_path')
            ->whereNull('health_certificate_path')
            ->whereBetween('created_at', [$yesterdayStart, $todayStart])
            ->count();

        $auctionsSevenDays = Lot::where('status', 'approved')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->count();
        $auctionsToday = Lot::where('status', 'approved')
            ->whereBetween('created_at', [$todayStart, $tomorrowStart])
            ->count();
        $auctionsYesterday = Lot::where('status', 'approved')
            ->whereBetween('created_at', [$yesterdayStart, $todayStart])
            ->count();

        $kpis = [
            'pending' => [
                'count' => $pendingCount,
                'delta' => $pendingToday - $pendingYesterday,
            ],
            'approved' => [
                'count' => $approvedToday,
                'delta' => $approvedToday - $approvedYesterday,
            ],
            'rejected' => [
                'count' => $rejectedToday,
                'delta' => $rejectedToday - $rejectedYesterday,
            ],
            'temp_alerts' => [
                'count' => $tempAlertsCount,
                'delta' => $tempAlertsToday - $tempAlertsYesterday,
            ],
            'missing_docs' => [
                'count' => $missingDocsCount,
                'delta' => $missingDocsToday - $missingDocsYesterday,
            ],
            'auctions' => [
                'count' => $auctionsSevenDays,
                'delta' => $auctionsToday - $auctionsYesterday,
            ],
        ];

        $lots = Lot::with('seller')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString()
            ->through(function (Lot $lot) {
                $temperatureValue = $this->extractTemperatureValue($lot->storage_temperature);

                $lot->setAttribute('temperature_value', $temperatureValue);
                $lot->setAttribute('seller_name', $lot->seller->name ?? 'Unknown Seller');

                return $lot;
            });

        return view('bid_admin.qc.dashboard', [
            'lots' => $lots,
            'kpis' => $kpis,
        ]);
    }

    public function lotSubmitted(Request $request): View
    {
        $filters = [
            'status' => $request->query('status', 'all'),
            'species' => $request->query('species', 'all'),
            'temperature_alert' => $request->query('temperature_alert', 'all'),
            'weight_variance' => $request->query('weight_variance', 'all'),
            'submission_date' => $request->query('submission_date'),
        ];

        $lots = Lot::orderByDesc('created_at')->get();
        $sellerIds = $lots->pluck('seller_id')->filter();
        $sellers = DB::table('users')->whereIn('id', $sellerIds)->get()->keyBy('id');

        $lots = $lots->map(function (Lot $lot) use ($sellers) {
            $temperatureValue = $this->extractTemperatureValue($lot->storage_temperature);
            $variance = $this->calculateVariancePercent($lot->quantity);

            $lot->setAttribute('temperature_value', $temperatureValue);
            $lot->setAttribute('variance_percent', $variance);
            $lot->setAttribute('seller_name', $sellers->get($lot->seller_id)->name ?? 'Unknown Seller');
            $lot->setAttribute('landing_site', Str::limit($lot->notes ?? 'Landing site not provided', 40));
            $lot->setAttribute('grade_label', $this->gradeLabel((float) $lot->quantity));
            return $lot;
        });

        $totalLots = $lots->count();

        $statusOptions = $lots->pluck('status')
            ->filter()
            ->unique()
            ->sortBy(fn ($value) => mb_strtolower($value))
            ->values()
            ->all();

        $speciesOptions = $lots->pluck('species')
            ->filter()
            ->unique()
            ->sortBy(fn ($value) => mb_strtolower($value))
            ->values()
            ->all();

        $temperatureFilters = [
            'all' => 'All',
            'alert' => '> 4°C Alert',
            'safe' => 'Safe Zone (≤ 0°C)',
        ];

        $weightVarianceFilters = [
            'all' => 'All',
            'over_10' => '> 10% Variance',
            'over_20' => '> 20% Variance',
        ];

        $filteredLots = $lots->filter(function (Lot $lot) use ($filters) {
            if ($filters['status'] !== 'all' && strcasecmp($lot->status ?? '', $filters['status']) !== 0) {
                return false;
            }

            if ($filters['species'] !== 'all' && strcasecmp($lot->species ?? '', $filters['species']) !== 0) {
                return false;
            }

            if ($filters['temperature_alert'] !== 'all') {
                $temp = $lot->temperature_value;

                if ($filters['temperature_alert'] === 'alert' && ($temp === null || $temp <= 4)) {
                    return false;
                }

                if ($filters['temperature_alert'] === 'safe' && ($temp === null || $temp > 0)) {
                    return false;
                }
            }

            if ($filters['weight_variance'] === 'over_20' && ($lot->variance_percent ?? 0) <= 20) {
                return false;
            }

            if ($filters['weight_variance'] === 'over_10' && ($lot->variance_percent ?? 0) <= 10) {
                return false;
            }

            if ($filters['submission_date']) {
                $submissionDate = optional($lot->created_at)->toDateString();
                if ($submissionDate !== $filters['submission_date']) {
                    return false;
                }
            }

            return true;
        })->values();

        return view('bid_admin.qc.lot-submitted', [
            'lots' => $filteredLots,
            'statusOptions' => $statusOptions,
            'speciesOptions' => $speciesOptions,
            'temperatureFilters' => $temperatureFilters,
            'weightVarianceFilters' => $weightVarianceFilters,
            'filters' => $filters,
            'totalLots' => $totalLots,
        ]);
    }

    public function lotSubmittedDetails(Request $request): View
    {
        $lot = $this->resolveLotFromRequest($request);

        if (! $lot) {
            return redirect()->route('qc.lot-submitted')->with('error', 'The lot could not be found.');
        }

        return view('bid_admin.qc.lot-subimitted-details', [
            'lot' => $lot,
        ]);
    }

    public function auctionSetup(Request $request): View
    {
        $lot = $this->resolveLotFromRequest($request);
        $audienceSegments = AudienceSegment::orderBy('name')->get();

        return view('bid_admin.qc.auction-setup', [
            'lot' => $lot,
            'audienceSegments' => $audienceSegments,
        ]);
    }

    public function storeAuctionSetup(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lot_id' => 'required|integer|exists:lots,id',
            'qc_verified_boxes' => 'nullable|numeric|min:0',
            'qc_actual_weight' => 'nullable|numeric|min:0',
            'qc_temperature' => 'nullable|numeric',
            'qc_documents_verified' => 'sometimes|boolean',
            'qc_notes' => 'nullable|string|max:2000',
            'qc_decision' => 'required|in:approve,reject,modify',
            'audience_segments' => 'nullable|array',
            'audience_segments.*' => 'integer|exists:audience_segments,id',
        ]);

        $lot = Lot::findOrFail($validated['lot_id']);
        $previousDecision = $lot->qc_decision;

        $lot->update([
            'qc_verified_boxes' => $validated['qc_verified_boxes'],
            'qc_actual_weight' => $validated['qc_actual_weight'],
            'qc_temperature' => $validated['qc_temperature'],
            'qc_documents_verified' => $request->boolean('qc_documents_verified'),
            'qc_notes' => $validated['qc_notes'],
            'qc_decision' => $validated['qc_decision'],
            'status' => $this->mapQcDecisionToStatus($validated['qc_decision']),
        ]);

        $lot->audienceSegments()->sync($validated['audience_segments'] ?? []);

        if ($lot->seller_id) {
            $decision = $validated['qc_decision'];
            $alreadyNotified = AppNotification::where('user_id', $lot->seller_id)
                ->where('data->lot_id', $lot->id)
                ->where('data->decision', $decision)
                ->exists();

            if ($previousDecision !== $decision || ! $alreadyNotified) {
                $this->notifySellerDecision($lot, $decision);
            }
        }

        $message = 'QC review saved. Decision: ' . Str::title($validated['qc_decision']);

        return redirect()->route('qc.auction-setup', ['lot' => $lot->id])->with('success', $message);
    }

    public function auctionScheduled(): View
    {
        $lot = $this->resolveLotFromRequest(request());

        $defaultStart = $lot?->auction_start_at
            ? Carbon::parse($lot->auction_start_at)
            : now()->addHours(2)->startOfHour();

        return view('bid_admin.qc.auction-scheduled', [
            'lot' => $lot,
            'defaultStart' => $defaultStart,
            'defaultDuration' => $lot?->auction_duration_minutes ?? 30,
            'antiSnipingEnabled' => (bool) ($lot?->anti_sniping_enabled ?? true),
        ]);
    }

    public function storeAuctionScheduled(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lot_id' => ['required', 'integer', 'exists:lots,id'],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'start_time' => ['required', 'date_format:H:i'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:240'],
            'anti_sniping' => ['nullable', 'boolean'],
        ]);

        $startAt = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['start_date'] . ' ' . $validated['start_time'],
            config('app.timezone')
        );

        $duration = (int) $validated['duration_minutes'];
        $endAt = (clone $startAt)->addMinutes($duration);

        $lot = Lot::findOrFail($validated['lot_id']);
        $lot->update([
            'auction_start_at' => $startAt,
            'auction_end_at' => $endAt,
            'auction_duration_minutes' => $duration,
            'anti_sniping_enabled' => $request->boolean('anti_sniping'),
            'status' => 'scheduled auction',
        ]);

        return redirect()
            ->route('qc.auction-scheduled', ['lot' => $lot->id])
            ->with('success', 'Auction schedule saved for #' . str_pad((string) $lot->id, 4, '0', STR_PAD_LEFT));
    }

    public function mediaControl(): View
    {
        $lot = $this->resolveLotFromRequest(request());

        return view('bid_admin.qc.media-control', [
            'lot' => $lot,
            'activeMode' => $lot?->media_mode ?? 'video',
        ]);
    }

    public function storeMediaControl(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lot_id' => ['required', 'integer', 'exists:lots,id'],
            'media_mode' => ['required', 'in:fixed,video,live'],
            'media_video_url' => ['nullable', 'string', 'max:2000'],
            'media_live_source' => ['nullable', 'string', 'max:255'],
            'media_images' => ['nullable', 'array'],
            'media_images.*' => ['file', 'image', 'max:4096'],
        ]);

        $lot = Lot::findOrFail($validated['lot_id']);

        $updates = [
            'media_mode' => $validated['media_mode'],
            'media_video_url' => $validated['media_video_url'] ?: null,
            'media_live_source' => $validated['media_live_source'] ?: null,
        ];

        if ($validated['media_mode'] === 'fixed') {
            $updates['media_video_url'] = null;
            $updates['media_live_source'] = null;
        } elseif ($validated['media_mode'] === 'video') {
            $updates['media_live_source'] = null;
        } elseif ($validated['media_mode'] === 'live') {
            $updates['media_video_url'] = null;
        }

        if ($request->hasFile('media_images')) {
            $stored = [];
            foreach ($request->file('media_images') as $file) {
                if (! $file) {
                    continue;
                }
                $stored[] = $file->store('lot-media', 'public');
            }
            $existing = is_array($lot->media_images) ? $lot->media_images : [];
            $updates['media_images'] = array_values(array_filter(array_merge($existing, $stored)));
        }

        $lot->update($updates);

        return redirect()
            ->route('qc.media-control', ['lot' => $lot->id])
            ->with('success', 'Media control settings saved for #' . str_pad((string) $lot->id, 4, '0', STR_PAD_LEFT));
    }

    private function resolveLotFromRequest(Request $request): ?Lot
    {
        $lotId = $request->query('lot') ?? $request->input('lot_id') ?? $request->input('lot');

        if (! $lotId) {
            return null;
        }

        return Lot::with(['seller', 'audienceSegments'])->find($lotId);
    }

    private function mapQcDecisionToStatus(string $decision): string
    {
        return match ($decision) {
            'approve' => 'approved',
            'reject' => 'rejected',
            'modify' => 'needs modification',
            default => 'pending qc',
        };
    }

    private function extractTemperatureValue(?string $value): ?float
    {
        if (! $value) {
            return null;
        }

        $clean = str_replace('Â', '', $value);
        $numeric = preg_replace('/[^0-9\\.-]/', '', $clean);

        if ($numeric === null || $numeric === '') {
            return null;
        }

        return (float) $numeric;
    }

    private function calculateVariancePercent(?float $quantity): float
    {
        $baseline = 100;

        if ($quantity === null || $baseline === 0) {
            return 0;
        }

        return round(abs($quantity - $baseline) / $baseline * 100, 1);
    }

    private function gradeLabel(float $quantity): string
    {
        if ($quantity >= 140) {
            return 'Grade A / Large';
        }

        if ($quantity >= 90) {
            return 'Grade B / Med';
        }

        if ($quantity >= 60) {
            return 'Grade C / Small';
        }

        return 'Grade C';
    }

    private function notifySellerDecision(Lot $lot, string $decision): void
    {
        $lotLabel = '#LOT-' . str_pad((string) $lot->id, 4, '0', STR_PAD_LEFT);

        $title = match ($decision) {
            'approve' => 'Lot Approved & Published',
            'reject' => 'Lot Rejected',
            default => 'Modification Requested',
        };

        $message = match ($decision) {
            'approve' => "Your lot {$lotLabel} has been approved and published.",
            'reject' => "Your lot {$lotLabel} was rejected by QC. Please review the notes.",
            default => "QC requested changes for {$lotLabel}. Please update and resubmit.",
        };

        $type = match ($decision) {
            'approve' => 'success',
            'reject' => 'danger',
            default => 'warning',
        };

        $url = route('seller.lot-list') . '?search=' . urlencode((string) $lot->id);

        AppNotification::create([
            'user_id' => $lot->seller_id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'data' => [
                'lot_id' => $lot->id,
                'decision' => $decision,
                'url' => $url,
            ],
        ]);
    }

    public function notifications(): View
    {
        return $this->renderOrDashboard('bid_admin.qc.notifications');
    }

    public function notificationData(Request $request): JsonResponse
    {
        if (! Schema::hasTable('app_notifications') || ! Schema::hasColumn('app_notifications', 'admin_id')) {
            return response()->json([
                'unread_count' => 0,
                'items' => [],
            ]);
        }

        $adminId = $this->resolveQcAdminId();

        if (! $adminId) {
            return response()->json([
                'unread_count' => 0,
                'items' => [],
            ]);
        }

        $limit = (int) $request->query('limit', 5);
        $limit = max(1, min($limit, 50));

        $notifications = AppNotification::where('admin_id', $adminId)
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

        $unreadCount = AppNotification::where('admin_id', $adminId)
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

        $adminId = $this->resolveQcAdminId();

        if ($adminId) {
            AppNotification::where('id', $validated['id'])
                ->where('admin_id', $adminId)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
        }

        $unreadCount = $adminId
            ? AppNotification::where('admin_id', $adminId)->where('is_read', false)->count()
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

        $adminId = $this->resolveQcAdminId();

        if ($adminId) {
            AppNotification::where('admin_id', $adminId)
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

    public function permissions(): View
    {
        return $this->renderOrDashboard('bid_admin.qc.permissions');
    }

    public function accountSettings(): View
    {
        return $this->renderOrDashboard('bid_admin.qc.account-settings');
    }

    public function login(): View
    {
        return view('bid_admin.admin.index');
    }

    private function renderOrDashboard(string $view): View
    {
        if (view()->exists($view)) {
            return view($view);
        }

        return $this->dashboard();
    }

    private function resolveQcAdminId(): ?int
    {
        $adminId = session('admin_user.id');

        if ($adminId) {
            return (int) $adminId;
        }

        $fallback = DB::table('admins')
            ->whereIn('role', ['qc', 'admin'])
            ->value('id');

        return $fallback ? (int) $fallback : null;
    }
}

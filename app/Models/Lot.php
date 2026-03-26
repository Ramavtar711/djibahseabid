<?php

namespace App\Models;

use App\Models\AudienceSegment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Lot extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'seller_id',
        'winner_id',
        'title',
        'species',
        'quantity',
        'starting_price',
        'final_price',
        'harvest_date',
        'storage_temperature',
        'notes',
        'status',
        'settlement_status',
        'image_path',
        'health_certificate_path',
        'documents_path',
        'qc_verified_boxes',
        'qc_actual_weight',
        'qc_temperature',
        'qc_documents_verified',
        'qc_decision',
        'qc_notes',
        'auction_start_at',
        'auction_end_at',
        'auction_duration_minutes',
        'anti_sniping_enabled',
        'media_mode',
        'media_video_url',
        'media_live_source',
        'media_images',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'harvest_date' => 'date',
        'quantity' => 'decimal:2',
        'starting_price' => 'decimal:2',
        'final_price' => 'decimal:2',
        'qc_verified_boxes' => 'decimal:2',
        'qc_actual_weight' => 'decimal:2',
        'qc_documents_verified' => 'boolean',
        'auction_start_at' => 'datetime',
        'auction_end_at' => 'datetime',
        'auction_duration_minutes' => 'integer',
        'anti_sniping_enabled' => 'boolean',
        'media_images' => 'array',
    ];

    /**
     * Appended attributes.
     */
    protected $appends = [
        'image_url',
    ];

    /**
     * Return URL for the lot image or a placeholder.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image_path && Storage::disk('public')->exists($this->image_path)) {
            return asset('storage/' . ltrim($this->image_path, '/'));
        }

        return 'https://via.placeholder.com/80x80?text=Lot';
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function audienceSegments(): BelongsToMany
    {
        return $this->belongsToMany(AudienceSegment::class, 'audience_segment_lot');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    public function scopeCurrentlyActive(Builder $query): Builder
    {
        $now = now();

        return $query
            ->whereIn('status', ['active auction', 'active'])
            ->where(function (Builder $activeQuery) use ($now) {
                $activeQuery->whereNull('auction_start_at')
                    ->orWhere('auction_start_at', '<=', $now);
            })
            ->where(function (Builder $activeQuery) use ($now) {
                $activeQuery->whereNull('auction_end_at')
                    ->orWhere('auction_end_at', '>', $now);
            });
    }

    public function scopeCurrentlyUpcoming(Builder $query): Builder
    {
        $now = now();

        return $query
            ->where('status', 'scheduled auction')
            ->where(function (Builder $upcomingQuery) use ($now) {
                $upcomingQuery->whereNull('auction_start_at')
                    ->orWhere('auction_start_at', '>', $now);
            });
    }

    public function isCurrentlyActive(): bool
    {
        if (! in_array($this->status, ['active auction', 'active'], true)) {
            return false;
        }

        $now = now();

        if ($this->auction_start_at && $this->auction_start_at->gt($now)) {
            return false;
        }

        if ($this->auction_end_at && ! $this->auction_end_at->gt($now)) {
            return false;
        }

        return true;
    }

    public function getDocumentListAttribute(): array
    {
        $files = [];

        if ($this->health_certificate_path) {
            $files[] = [
                'path' => $this->health_certificate_path,
                'type' => 'Health Certificate',
            ];
        }

        if ($this->documents_path) {
            $extraPaths = array_filter(
                explode(',', $this->documents_path),
                fn ($path) => trim($path) !== ''
            );

            foreach ($extraPaths as $path) {
                $files[] = [
                    'path' => $path,
                    'type' => 'Supporting Document',
                ];
            }
        }

        return collect($files)
            ->map(function ($file) {
                $path = trim($file['path']);
                if ($path === '') {
                    return null;
                }

                $normalized = ltrim($path, '/');
                $url = Storage::disk('public')->exists($normalized)
                    ? asset('storage/' . $normalized)
                    : null;

                return [
                    'name' => basename($normalized) ?: 'Document',
                    'type' => $file['type'] ?? 'Document',
                    'url' => $url,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Settlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'lot_id',
        'seller_id',
        'buyer_id',
        'amount',
        'commission_amount',
        'net_amount',
        'commission_rate',
        'status',
        'payment_provider',
        'payment_reference',
        'manual_payment_sender',
        'manual_payment_account',
        'manual_payment_note',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}

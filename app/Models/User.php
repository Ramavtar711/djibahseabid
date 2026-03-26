<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'profile_image',
        'password',
        'job_title',
        'company_legal_name',
        'city',
        'business_address',
        'country',
        'website',
        'company_registration_number',
        'business_type',
        'interested_in',
        'monthly_volume',
        'preferred_delivery',
        'preferred_payment',
        'bank_country',
        'company_registration_file',
        'id_file',
        'import_license_file',
        'is_registered_business',
        'accepted_terms',
        'bank_transfer_validated',
        'type',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_registered_business' => 'boolean',
        'accepted_terms' => 'boolean',
        'bank_transfer_validated' => 'boolean',
    ];

    public function lots()
    {
        return $this->hasMany(Lot::class, 'seller_id');
    }

    public function appNotifications(): HasMany
    {
        return $this->hasMany(AppNotification::class, 'user_id');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class, 'buyer_id');
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }
}

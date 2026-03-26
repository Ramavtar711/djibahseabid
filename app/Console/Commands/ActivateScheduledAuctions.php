<?php

namespace App\Console\Commands;

use App\Models\AppNotification;
use App\Models\Lot;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ActivateScheduledAuctions extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'auctions:activate';

    /**
     * The console command description.
     */
    protected $description = 'Activate scheduled auctions that have reached their start time and notify sellers.';

    public function handle(): int
    {
        $now = now();

        $lots = Lot::query()
            ->where('status', 'scheduled auction')
            ->whereNotNull('auction_start_at')
            ->where('auction_start_at', '<=', $now)
            ->get();

        if ($lots->isEmpty()) {
            $this->info('No scheduled auctions to activate.');
            return self::SUCCESS;
        }

        foreach ($lots as $lot) {
            $lot->update([
                'status' => 'active',
            ]);

            $this->notifySellerAuctionLive($lot);
        }

        $this->info("Activated {$lots->count()} auction(s).");
        return self::SUCCESS;
    }

    private function notifySellerAuctionLive(Lot $lot): void
    {
        if (! Schema::hasTable('app_notifications') || ! Schema::hasColumn('app_notifications', 'user_id')) {
            return;
        }

        if (! $lot->seller_id) {
            return;
        }

        $lotLabel = '#LOT-' . str_pad((string) $lot->id, 4, '0', STR_PAD_LEFT);

        $alreadyNotified = AppNotification::where('user_id', $lot->seller_id)
            ->where('data->lot_id', $lot->id)
            ->where('data->event', 'auction_live')
            ->exists();

        if ($alreadyNotified) {
            return;
        }

        $url = route('seller.active-auction', ['lot' => $lot->id]);

        AppNotification::create([
            'user_id' => $lot->seller_id,
            'title' => 'Auction Live',
            'message' => "Your auction {$lotLabel} is now live.",
            'type' => 'success',
            'data' => [
                'lot_id' => $lot->id,
                'event' => 'auction_live',
                'url' => $url,
            ],
        ]);
    }
}


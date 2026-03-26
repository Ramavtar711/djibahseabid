<?php

namespace App\Console\Commands;

use App\Models\AppNotification;
use App\Models\Bid;
use App\Models\Lot;
use App\Models\Settlement;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class CloseExpiredAuctions extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'auctions:close';

    /**
     * The console command description.
     */
    protected $description = 'Close active auctions that have ended, mark sold/unsold, and notify parties.';

    public function handle(): int
    {
        $now = now();

        $lots = Lot::query()
            ->whereIn('status', ['active auction', 'active'])
            ->whereNotNull('auction_end_at')
            ->where('auction_end_at', '<=', $now)
            ->get();

        if ($lots->isEmpty()) {
            $this->info('No active auctions to close.');
            return self::SUCCESS;
        }

        foreach ($lots as $lot) {
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
                ]);

                Bid::where('lot_id', $lot->id)->update(['status' => 'lost']);
                $winnerBid->update(['status' => 'won']);

                $this->createSettlement($lot, $winnerBid, $finalPrice);
                $this->handleWalletHolds($lot, $winnerBid);
                $this->notifySellerAuctionSold($lot, $winnerBid);
                $this->notifyWinnerBuyer($lot, $winnerBid);
                $this->notifySettlementPending($lot, $winnerBid);
            } else {
                $lot->update([
                    'status' => 'unsold',
                    'winner_id' => null,
                    'final_price' => null,
                    'settlement_status' => null,
                ]);

                $this->notifySellerAuctionUnsold($lot);
            }
        }

        $this->info("Closed {$lots->count()} auction(s).");
        return self::SUCCESS;
    }

    private function notifySellerAuctionSold(Lot $lot, Bid $winnerBid): void
    {
        if (! $this->canNotifyUser($lot->seller_id)) {
            return;
        }

        $alreadyNotified = AppNotification::where('user_id', $lot->seller_id)
            ->where('data->lot_id', $lot->id)
            ->where('data->event', 'auction_sold')
            ->exists();

        if ($alreadyNotified) {
            return;
        }

        $amountLabel = '$' . number_format((float) $winnerBid->amount, 2) . '/kg';
        $lotLabel = '#LOT-' . str_pad((string) $lot->id, 4, '0', STR_PAD_LEFT);
        $buyerName = $winnerBid->buyer->name ?? 'Buyer';

        AppNotification::create([
            'user_id' => $lot->seller_id,
            'title' => 'Auction Sold',
            'message' => "{$lotLabel} sold to {$buyerName} at {$amountLabel}.",
            'type' => 'success',
            'data' => [
                'lot_id' => $lot->id,
                'event' => 'auction_sold',
                'url' => route('seller.sold-lots'),
            ],
        ]);
    }

    private function notifySellerAuctionUnsold(Lot $lot): void
    {
        if (! $this->canNotifyUser($lot->seller_id)) {
            return;
        }

        $alreadyNotified = AppNotification::where('user_id', $lot->seller_id)
            ->where('data->lot_id', $lot->id)
            ->where('data->event', 'auction_unsold')
            ->exists();

        if ($alreadyNotified) {
            return;
        }

        $lotLabel = '#LOT-' . str_pad((string) $lot->id, 4, '0', STR_PAD_LEFT);

        AppNotification::create([
            'user_id' => $lot->seller_id,
            'title' => 'Auction Unsold',
            'message' => "{$lotLabel} ended without bids.",
            'type' => 'warning',
            'data' => [
                'lot_id' => $lot->id,
                'event' => 'auction_unsold',
                'url' => route('seller.sold-lots'),
            ],
        ]);
    }

    private function notifyWinnerBuyer(Lot $lot, Bid $winnerBid): void
    {
        $buyerId = $winnerBid->buyer_id;
        if (! $this->canNotifyUser($buyerId)) {
            return;
        }

        $alreadyNotified = AppNotification::where('user_id', $buyerId)
            ->where('data->lot_id', $lot->id)
            ->where('data->event', 'auction_won')
            ->exists();

        if ($alreadyNotified) {
            return;
        }

        $amountLabel = '$' . number_format((float) $winnerBid->amount, 2) . '/kg';
        $lotLabel = '#LOT-' . str_pad((string) $lot->id, 4, '0', STR_PAD_LEFT);

        AppNotification::create([
            'user_id' => $buyerId,
            'title' => 'You Won the Auction',
            'message' => "You won {$lotLabel} at {$amountLabel}.",
            'type' => 'success',
            'data' => [
                'lot_id' => $lot->id,
                'event' => 'auction_won',
                'url' => route('buyer.won-auction'),
            ],
        ]);
    }

    private function notifySettlementPending(Lot $lot, Bid $winnerBid): void
    {
        if (! Schema::hasTable('app_notifications') || ! Schema::hasColumn('app_notifications', 'user_id')) {
            return;
        }

        $lotLabel = '#LOT-' . str_pad((string) $lot->id, 4, '0', STR_PAD_LEFT);
        $amountLabel = '$' . number_format((float) $winnerBid->amount, 2) . '/kg';

        if ($this->canNotifyUser($lot->seller_id)) {
            $sellerAlready = AppNotification::where('user_id', $lot->seller_id)
                ->where('data->lot_id', $lot->id)
                ->where('data->event', 'settlement_pending_seller')
                ->exists();

            if (! $sellerAlready) {
                AppNotification::create([
                    'user_id' => $lot->seller_id,
                    'title' => 'Settlement Pending',
                    'message' => "Settlement pending for {$lotLabel} at {$amountLabel}.",
                    'type' => 'info',
                    'data' => [
                        'lot_id' => $lot->id,
                        'event' => 'settlement_pending_seller',
                        'url' => route('seller.revenue'),
                    ],
                ]);
            }
        }

        $buyerId = $winnerBid->buyer_id;
        if ($this->canNotifyUser($buyerId)) {
            $buyerAlready = AppNotification::where('user_id', $buyerId)
                ->where('data->lot_id', $lot->id)
                ->where('data->event', 'settlement_pending_buyer')
                ->exists();

            if (! $buyerAlready) {
                AppNotification::create([
                    'user_id' => $buyerId,
                    'title' => 'Payment Required',
                    'message' => "Payment required for {$lotLabel} at {$amountLabel}.",
                    'type' => 'warning',
                    'data' => [
                        'lot_id' => $lot->id,
                        'event' => 'settlement_pending_buyer',
                        'url' => route('buyer.transactions'),
                    ],
                ]);
            }
        }
    }

    private function createSettlement(Lot $lot, Bid $winnerBid, float $finalPrice): void
    {
        if (! Schema::hasTable('settlements')) {
            return;
        }

        $commissionRate = 5.00;
        $quantity = (float) ($lot->quantity ?? 0);
        $grossAmount = round($finalPrice * $quantity, 2);
        $commissionAmount = round($grossAmount * ($commissionRate / 100), 2);
        $netAmount = round($grossAmount - $commissionAmount, 2);

        $existing = Settlement::where('lot_id', $lot->id)->first();
        if ($existing) {
            $existing->update([
                'seller_id' => $lot->seller_id,
                'buyer_id' => $winnerBid->buyer_id,
                'amount' => $grossAmount,
                'commission_amount' => $commissionAmount,
                'net_amount' => $netAmount,
                'commission_rate' => $commissionRate,
                'status' => $existing->status ?? 'pending',
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
    
    private function handleWalletHolds(Lot $lot, Bid $winnerBid): void
    {
        if (! Schema::hasTable('wallets') || ! Schema::hasTable('wallet_transactions')) {
            return;
        }

        $holds = WalletTransaction::query()
            ->where('type', 'hold')
            ->where('status', 'completed')
            ->where('reference_type', 'lot')
            ->where('reference_id', $lot->id)
            ->get()
            ->groupBy('user_id');

        if ($holds->isEmpty()) {
            return;
        }

        $quantity = (float) ($lot->quantity ?? 1);
        $winnerAmount = round(((float) $winnerBid->amount) * $quantity, 2);

        foreach ($holds as $userId => $rows) {
            $totalHold = (float) $rows->sum('amount');
            $wallet = Wallet::where('user_id', $userId)->first();
            if (! $wallet) {
                continue;
            }

            if ((int) $userId === (int) $winnerBid->buyer_id) {
                $debitAmount = min($winnerAmount, $totalHold);
                $wallet->update([
                    'blocked_balance' => max(0, (float) $wallet->blocked_balance - $debitAmount),
                ]);

                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'user_id' => $userId,
                    'type' => 'auction_payment',
                    'amount' => $debitAmount,
                    'status' => 'completed',
                    'reference_type' => 'lot',
                    'reference_id' => $lot->id,
                    'description' => 'Auction payment for Lot #' . $lot->id,
                ]);

                $extra = $totalHold - $debitAmount;
                if ($extra > 0) {
                    $wallet->update([
                        'blocked_balance' => max(0, (float) $wallet->blocked_balance - $extra),
                        'available_balance' => (float) $wallet->available_balance + $extra,
                    ]);

                    WalletTransaction::create([
                        'wallet_id' => $wallet->id,
                        'user_id' => $userId,
                        'type' => 'release',
                        'amount' => $extra,
                        'status' => 'completed',
                        'reference_type' => 'lot',
                        'reference_id' => $lot->id,
                        'description' => 'Release excess hold for Lot #' . $lot->id,
                    ]);
                }
            } else {
                $wallet->update([
                    'blocked_balance' => max(0, (float) $wallet->blocked_balance - $totalHold),
                    'available_balance' => (float) $wallet->available_balance + $totalHold,
                ]);

                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'user_id' => $userId,
                    'type' => 'release',
                    'amount' => $totalHold,
                    'status' => 'completed',
                    'reference_type' => 'lot',
                    'reference_id' => $lot->id,
                    'description' => 'Release bid hold for Lot #' . $lot->id,
                ]);
            }
        }
    }
    private function canNotifyUser(?int $userId): bool
    {
        if (! $userId) {
            return false;
        }

        return Schema::hasTable('app_notifications') && Schema::hasColumn('app_notifications', 'user_id');
    }
}








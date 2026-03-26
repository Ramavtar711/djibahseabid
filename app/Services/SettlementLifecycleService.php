<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\Bid;
use App\Models\Lot;
use App\Models\Settlement;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class SettlementLifecycleService
{
    public const PAYMENT_WINDOW_HOURS = 24;

    public function paymentDeadlineFor(Settlement $settlement): ?Carbon
    {
        return $settlement->created_at
            ? $settlement->created_at->copy()->addHours(self::PAYMENT_WINDOW_HOURS)
            : null;
    }

    public function processExpiredSettlements(): int
    {
        if (! Schema::hasTable('settlements')) {
            return 0;
        }

        $now = now();

        $expiredSettlements = Settlement::query()
            ->with('lot')
            ->whereIn('status', ['pending', 'processing', 'failed'])
            ->whereNull('paid_at')
            ->where('created_at', '<=', $now->copy()->subHours(self::PAYMENT_WINDOW_HOURS))
            ->get();

        $processed = 0;

        foreach ($expiredSettlements as $settlement) {
            $lot = $settlement->lot;
            if (! $lot) {
                continue;
            }

            if ($settlement->status === 'expired') {
                continue;
            }

            $this->expireSettlement($settlement);

            $nextBid = Bid::query()
                ->where('lot_id', $lot->id)
                ->where('buyer_id', '!=', $settlement->buyer_id)
                ->orderByDesc('amount')
                ->orderBy('created_at')
                ->with('buyer')
                ->first();

            if ($nextBid) {
                $this->offerLotToSecondBidder($lot, $nextBid);
            } else {
                $this->relistLot($lot);
            }

            $processed++;
        }

        return $processed;
    }

    private function expireSettlement(Settlement $settlement): void
    {
        $settlement->update([
            'status' => 'expired',
        ]);

        $lot = $settlement->lot;
        if (! $lot) {
            return;
        }

        $expiredBid = Bid::query()
            ->where('lot_id', $lot->id)
            ->where('buyer_id', $settlement->buyer_id)
            ->orderByDesc('amount')
            ->orderBy('created_at')
            ->first();

        if ($expiredBid) {
            $expiredBid->update(['status' => 'expired']);
        }

        $this->notifyBuyerPaymentExpired($settlement);
    }

    private function offerLotToSecondBidder(Lot $lot, Bid $nextBid): void
    {
        Bid::query()
            ->where('lot_id', $lot->id)
            ->update(['status' => 'lost']);

        $nextBid->update(['status' => 'won']);

        $lot->update([
            'status' => 'sold',
            'winner_id' => $nextBid->buyer_id,
            'final_price' => (float) $nextBid->amount,
            'settlement_status' => 'pending',
        ]);

        $this->createReplacementSettlement($lot, $nextBid);
        $this->notifySecondBidder($lot, $nextBid);
        $this->notifySellerReassigned($lot, $nextBid);
    }

    private function relistLot(Lot $lot): void
    {
        $startAt = now()->addHours(2)->startOfMinute();
        $duration = (int) ($lot->auction_duration_minutes ?? 30);
        $endAt = (clone $startAt)->addMinutes($duration);

        $lot->update([
            'status' => 'scheduled auction',
            'auction_start_at' => $startAt,
            'auction_end_at' => $endAt,
            'settlement_status' => null,
            'winner_id' => null,
            'final_price' => null,
        ]);

        Bid::query()
            ->where('lot_id', $lot->id)
            ->update(['status' => 'lost']);

        $this->notifySellerRelisted($lot);
    }

    private function createReplacementSettlement(Lot $lot, Bid $winnerBid): void
    {
        $commissionRate = 5.00;
        $quantity = (float) ($lot->quantity ?? 0);
        $grossAmount = round(((float) $winnerBid->amount) * $quantity, 2);
        $commissionAmount = round($grossAmount * ($commissionRate / 100), 2);
        $netAmount = round($grossAmount - $commissionAmount, 2);

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

    private function notifyBuyerPaymentExpired(Settlement $settlement): void
    {
        if (! $this->canNotifyUser($settlement->buyer_id)) {
            return;
        }

        AppNotification::create([
            'user_id' => $settlement->buyer_id,
            'title' => 'Payment Window Expired',
            'message' => 'Your payment window expired for #LOT-' . str_pad((string) $settlement->lot_id, 4, '0', STR_PAD_LEFT) . '.',
            'type' => 'danger',
            'data' => [
                'lot_id' => $settlement->lot_id,
                'settlement_id' => $settlement->id,
                'event' => 'payment_window_expired',
                'url' => route('buyer.won-auction'),
            ],
        ]);
    }

    private function notifySecondBidder(Lot $lot, Bid $bid): void
    {
        if (! $this->canNotifyUser($bid->buyer_id)) {
            return;
        }

        $lotLabel = '#LOT-' . str_pad((string) $lot->id, 4, '0', STR_PAD_LEFT);
        $amountLabel = '$' . number_format((float) $bid->amount, 2) . '/kg';

        AppNotification::create([
            'user_id' => $bid->buyer_id,
            'title' => 'Lot Offered To You',
            'message' => "{$lotLabel} is now offered to you at {$amountLabel}. Payment is required within " . self::PAYMENT_WINDOW_HOURS . ' hours.',
            'type' => 'warning',
            'data' => [
                'lot_id' => $lot->id,
                'event' => 'second_bidder_offer',
                'url' => route('buyer.won-auction'),
            ],
        ]);
    }

    private function notifySellerReassigned(Lot $lot, Bid $bid): void
    {
        if (! $this->canNotifyUser($lot->seller_id)) {
            return;
        }

        $buyerName = $bid->buyer->name ?? 'Buyer';
        $lotLabel = '#LOT-' . str_pad((string) $lot->id, 4, '0', STR_PAD_LEFT);

        AppNotification::create([
            'user_id' => $lot->seller_id,
            'title' => 'Winner Reassigned',
            'message' => "{$lotLabel} was reassigned to {$buyerName} after the original winner missed payment.",
            'type' => 'info',
            'data' => [
                'lot_id' => $lot->id,
                'event' => 'winner_reassigned',
                'url' => route('seller.sold-lots'),
            ],
        ]);
    }

    private function notifySellerRelisted(Lot $lot): void
    {
        if (! $this->canNotifyUser($lot->seller_id)) {
            return;
        }

        $lotLabel = '#LOT-' . str_pad((string) $lot->id, 4, '0', STR_PAD_LEFT);

        AppNotification::create([
            'user_id' => $lot->seller_id,
            'title' => 'Lot Relisted',
            'message' => "{$lotLabel} was relisted because the winner did not pay within " . self::PAYMENT_WINDOW_HOURS . ' hours.',
            'type' => 'warning',
            'data' => [
                'lot_id' => $lot->id,
                'event' => 'lot_relisted_unpaid',
                'url' => route('seller.sold-lots'),
            ],
        ]);
    }

    private function canNotifyUser(?int $userId): bool
    {
        if (! $userId) {
            return false;
        }

        return Schema::hasTable('app_notifications') && Schema::hasColumn('app_notifications', 'user_id');
    }
}

<?php

namespace App\Events;

use App\Models\Bid;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BidPlaced implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Bid $bid, public string $buyerName)
    {
    }

    public function broadcastOn(): Channel
    {
        return new Channel('lot.' . $this->bid->lot_id);
    }

    public function broadcastAs(): string
    {
        return 'bid.placed';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->bid->id,
            'lot_id' => $this->bid->lot_id,
            'buyer' => $this->buyerName,
            'amount' => (float) $this->bid->amount,
            'created_at' => optional($this->bid->created_at)->toIso8601String(),
        ];
    }
}

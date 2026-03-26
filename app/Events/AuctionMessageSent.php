<?php

namespace App\Events;

use App\Models\AuctionMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AuctionMessageSent implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public AuctionMessage $message, public string $buyerName)
    {
    }

    public function broadcastOn(): Channel
    {
        return new Channel('lot.' . $this->message->lot_id);
    }

    public function broadcastAs(): string
    {
        return 'chat.message';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'lot_id' => $this->message->lot_id,
            'buyer' => $this->buyerName,
            'message' => $this->message->message,
            'created_at' => optional($this->message->created_at)->toIso8601String(),
        ];
    }
}

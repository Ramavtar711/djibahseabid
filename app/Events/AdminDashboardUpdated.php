<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminDashboardUpdated implements ShouldBroadcastNow
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public string $reason = 'updated',
        public ?int $lotId = null,
    ) {
    }

    public function broadcastOn(): Channel
    {
        return new Channel('dashboard.admin');
    }

    public function broadcastAs(): string
    {
        return 'dashboard.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'reason' => $this->reason,
            'lot_id' => $this->lotId,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}

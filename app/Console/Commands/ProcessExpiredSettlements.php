<?php

namespace App\Console\Commands;

use App\Services\SettlementLifecycleService;
use Illuminate\Console\Command;

class ProcessExpiredSettlements extends Command
{
    protected $signature = 'settlements:process-expired';

    protected $description = 'Expire unpaid winning settlements and reassign or relist lots.';

    public function handle(SettlementLifecycleService $service): int
    {
        $count = $service->processExpiredSettlements();

        $this->info("Processed {$count} expired settlement(s).");

        return self::SUCCESS;
    }
}

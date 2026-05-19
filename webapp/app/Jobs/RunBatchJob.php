<?php

namespace App\Jobs;

use App\Services\BatchExecutionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RunBatchJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 600;
    public int $tries = 1;

    public function __construct(public readonly int $batchId) {}

    public function handle(BatchExecutionService $executor): void
    {
        $executor->execute($this->batchId);
    }
}

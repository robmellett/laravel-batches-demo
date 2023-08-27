<?php

namespace App\Console\Commands;

use App\Jobs\CreateCustomerUpdateBatchJob;
use Illuminate\Console\Command;

class CreateCustomerUpdateBatchCommand extends Command
{
    protected $signature = 'customer:create-update-batch';

    protected $description = 'Command description';

    public function handle(): void
    {
        CreateCustomerUpdateBatchJob::dispatch();

        $this->info('Job has been dispatched to the queue.');
    }
}

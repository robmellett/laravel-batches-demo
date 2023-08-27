<?php

namespace App\Jobs;


use App\Support\EloquentBatchPaginator;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class AppendCustomerUpdateChunk implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected EloquentBatchPaginator $paginator
    )
    {
    }

    public function handle()
    {
        if ($this->batch()->canceled()) {
            return;
        }

        $this->paginator->chunkById(function ($customers) {
            $customers->each(function ($customer) {

                ray($customer->id . ' : ' . $customer->name);

                // $this->batch()->add(
                //     new UpdateCustomerJob($customer)
                // );
            });
        });
    }
}

<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Support\EloquentBatchPaginator;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Throwable;

class CreateCustomerUpdateBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $batch = Bus::batch([])->then(function (Batch $batch) {
            // All jobs completed successfully...
        })->catch(function (Batch $batch, Throwable $e) {
            // First batch job failure detected...
        })->finally(function (Batch $batch) {
            // The batch has finished executing...
        })
            ->allowFailures()
            ->name('The Batch Name')
            ->dispatch();

        Customer::query()
            ->chunkById(100, function ($customers) use ($batch) {

                $paginator = EloquentBatchPaginator::make(
                    model: Customer::class,
                    startingFromId: $customers->first()->id,
                    chunkSize: 100
                );

                $batch->add(
                    new AppendCustomerUpdateChunk($paginator)
                );
            });
    }
}

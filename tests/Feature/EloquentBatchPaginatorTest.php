<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Support\EloquentBatchPaginator;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class EloquentBatchPaginatorTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_paginate_models()
    {
        $customers = Customer::factory()->count(500)->create();

        $paginator = EloquentBatchPaginator::make(
            model: Customer::class,
            startingFromId: $customers->first()->id,
            chunkSize: 100
        );

        $this->assertEquals(500, $paginator->count());
        $this->assertEquals(1, $paginator->fromChunkId());
        $this->assertEquals(101, $paginator->untilChunkId());
    }

    /** @test */
    public function second_iteration_attempt()
    {
        Customer::factory()->count(500)->create();

        $paginator = EloquentBatchPaginator::make(
            model: Customer::class,
            startingFromId: 101,
            chunkSize: 100
        );

        // Note:
        // We can't use limit() here. It will not work with chunkById() as its not respected by the query builder.
        // Customer::query()
        //     ->where('id', '>=', $paginator->fromChunkId())
        //     ->limit(100)
        //     ->chunkById(100, function ($customers) use ($paginator) {
        //         $customers->each(function (Customer $customer) use ($paginator) {
        //             ray($customer->id);
        //         });
        //     });


        // Feels like it's better to do this instead.
        Customer::query()
            ->where('id', '>=', $paginator->fromChunkId())
            ->where('id', '<', $paginator->untilChunkId())
            ->chunkById(100, function ($customers) use ($paginator) {
                $customers->each(function (Customer $customer) use ($paginator) {
                    ray($customer->id);
                });
            });
    }

    /**
     * @test
     * Note: Pretend you are on the second iteration.
     */
    public function can_create_fluent_chunk_by_id_queries()
    {
        Customer::factory()->count(500)->create();

        $paginator = EloquentBatchPaginator::make(
            model: Customer::class,
            startingFromId: 101,
            chunkSize: 100
        );

        $paginator
            ->chunkById(function ($customers) use ($paginator) {
                $customers->each(function (Customer $customer) use ($paginator) {
                    ray($customer->id);
                });
            });
    }
}

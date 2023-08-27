<?php

namespace App\Support;

use App\Models\Customer;

class EloquentBatchPaginator
{
    public function __construct(
        protected string $model,
        protected int $startingFromId = 1,
        protected int $chunkSize = 1000,
    ) {
    }

    public static function make(...$args)
    {
        return new static(...$args);
    }

    public function model()
    {
        return app($this->model);
    }

    public function chunkSize()
    {
        return $this->chunkSize;
    }

    public function fromChunkId()
    {
        return $this->startingFromId;
    }

    public function untilChunkId()
    {
        return $this->startingFromId + $this->chunkSize;
    }

    public function chunkById(callable $callback)
    {
        return $this->model()
            ->query()
            ->where('id', '>=', $this->fromChunkId())
            ->where('id', '<', $this->untilChunkId())
            ->chunkById($this->chunkSize(), $callback);
    }
}

<?php

namespace App\Support;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Laravel\SerializableClosure\SerializableClosure;

class EloquentBatchPaginator
{
    protected ?SerializableClosure $query;

    public function __construct(
        protected string $model,
        ?Closure $query = null,
        protected int $startingFromId = 1,
        protected int $chunkSize = 1000,
    ) {
        $this->query = $query
            ? new SerializableClosure($query)
            : null;
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

    public function query(): Builder
    {
        $query = $this->query instanceof SerializableClosure
            ? $this->query->getClosure()
            : $this->query;

        if (! $query) {
            return $this->model()
                ->where('id', '>=', $this->fromChunkId())
                ->where('id', '<', $this->untilChunkId());
        }

        return $this->model()
            ->setQuery(call_user_func($query)->getQuery())
            ->where('id', '>=', $this->fromChunkId())
            ->where('id', '<', $this->untilChunkId());
    }

    public function chunkById(callable $callback)
    {
        return $this->query()
            ->chunkById($this->chunkSize(), $callback);
    }
}

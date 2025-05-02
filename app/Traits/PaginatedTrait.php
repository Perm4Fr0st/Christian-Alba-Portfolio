<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

trait PaginatedTrait
{
    /**
     * Paginated Trait
     * @param Builder $builder
     * @param array $payload
     * @return LengthAwarePaginator
     */
    public function paginateBuilder(Builder $builder, array $payload, $defaultPerPage = 10): LengthAwarePaginator
    {

        if (isset($payload['data'])) {
            $builder->search($payload['data'], null, true, true);
        }

        if (isset($payload['order_by']) && isset($payload['sort_by'])) {
            $builder->orderBy($payload['order_by'], $payload['sort_by']);
        }

        if (isset($payload['per_page'])) {
            $defaultPerPage = $payload['per_page'];
        }

        $paginated = $builder->paginate($defaultPerPage)->setPath(
            env('APP_URL') . request()->getRequestUri()
        );

        return $paginated;
    }
}
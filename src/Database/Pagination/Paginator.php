<?php

namespace WebApp\Database\Pagination;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

final class Paginator
{
    /**
     * @return array{data: array, pagination: array}
     */
    public static function paginate(QueryBuilder|EloquentBuilder $query, int $page = 1, int $perPage = 20): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);

        $total = (int) $query->toBase()->cloneWithout(['orders', 'limit', 'offset'])->count();
        $data = $query->forPage($page, $perPage)->get()->toArray();

        return [
            'data' => $data,
            'pagination' => [
                'page' => $page,
                'perPage' => $perPage,
                'total' => $total,
                'totalPages' => (int) ceil($total / $perPage),
            ],
        ];
    }
}


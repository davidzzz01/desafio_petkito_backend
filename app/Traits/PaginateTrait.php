<?php

namespace App\Traits;

trait PaginateTrait
{
    public function paginate($total, $perPage, $page, $data, $options = [])
    {
        $lastPage = ceil($total / $perPage);

        $nextPage = $page == $lastPage ? null : $page + 1;

        $previousPage = $page > 1 ? $page - 1: null;

        return [
            'total' => $total,
            'per_page' => $perPage,
            'page' => $page,
            'next_page' => $nextPage,
            'last_page' => $lastPage,
            'previous_page' => $previousPage,
            'data' => $data,
            'options' => $options,
        ];
    }
} 
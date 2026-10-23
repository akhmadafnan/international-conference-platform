<?php

namespace App\Support\Academic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

final class AcademicPagination
{
    /** @template T of Model
     * @param  LengthAwarePaginator<int, T>  $rows
     * @return array<string, mixed>
     */
    public static function from(LengthAwarePaginator $rows): array
    {
        $numbers = array_values(array_unique([1, ...range(max(1, $rows->currentPage() - 2), min($rows->lastPage(), $rows->currentPage() + 2)), $rows->lastPage()]));
        sort($numbers);
        $pages = [];
        $previous = 0;
        foreach ($numbers as $number) {
            if ($number > $previous + 1) {
                $pages[] = ['page' => null, 'url' => null];
            }
            $pages[] = ['page' => $number, 'url' => $rows->url($number)];
            $previous = $number;
        }

        return ['total' => $rows->total(), 'from' => $rows->firstItem(), 'to' => $rows->lastItem(), 'perPage' => $rows->perPage(), 'currentPage' => $rows->currentPage(), 'lastPage' => $rows->lastPage(), 'firstUrl' => $rows->onFirstPage() ? null : $rows->url(1), 'lastUrl' => $rows->hasMorePages() ? $rows->url($rows->lastPage()) : null, 'previousUrl' => $rows->previousPageUrl(), 'nextUrl' => $rows->nextPageUrl(), 'pages' => $pages];
    }
}

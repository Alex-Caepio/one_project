<?php namespace App\Providers;

use Illuminate\Http\Response;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class ResponseServiceProvider
 *
 * @package App\Providers
 */
class ResponseServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Response::macro('withPaginationHeaders', function (LengthAwarePaginator $paginator) {
            return $this->withHeaders([
                'X-pagination-current_page'   => $paginator->currentPage(),
                'X-pagination-last_page'      => $paginator->lastPage(),
                'X-pagination-per_page'       => $paginator->perPage(),
                'X-pagination-from'           => $paginator->firstItem(),
                'X-pagination-to'             => $paginator->lastItem(),
                'X-pagination-total'          => $paginator->total(),
                'X-pagination-first_page_url' => $paginator->url(1),
                'X-pagination-last_page_url'  => $paginator->url($paginator->lastPage()),
                'X-pagination-next_page_url'  => $paginator->nextPageUrl(),
                'X-pagination-prev_page_url'  => $paginator->previousPageUrl(),
            ]);
        });
    }
}

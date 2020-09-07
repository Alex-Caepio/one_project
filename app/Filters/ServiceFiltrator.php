<?php


namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ServiceFiltrator
{
    public function apply($queryBuilder, Request $request)
    {
        if ($request->filled('type')) {
            $type = $request->get('type');
            $queryBuilder->where('type', 'like', "%$type%");
        }
        if ($request->filled('start_date')) {
            $queryBuilder->whereHas('schedules', function ($query) use ($request) {
                $query->where('start_date', '>=', $request->start_date);
            });
        }
        if ($request->filled('end_date')) {
            $queryBuilder->whereHas('schedules', function ($query) use ($request) {
                $query->where('end_date', '<=', $request->end_date);
            });
        }
        if ($request->filled('id')) {
            $queryBuilder->whereHas('schedules.locations', function ($query) use ($request) {
                $query->where('id', '=', $request->id);
            });
        }

        $searchString = $request->get('search');
        $sortBy = $request->get('sortby');
        $queryBuilder->when($request->filled('sortby'), static function (Builder $query) use ($sortBy, $searchString) {
            switch ($sortBy) {
                case 'priority':
                    $sortByField = '???';
                    break;
                case 'discipline':
                    $sortByField = 'discipline';
                    break;
                case 'schedule':
                    $query->selectRaw('*, DATEDIFF(start_date, now()) as dif')
                        ->join('schedules', 'services.id', '=', 'schedules.service_id')
                        ->orderByRaw('ABS(dif)');
                    break;
                case 'service-name':
                    $sortByField = 'service-name';
                    break;
                case 'keyword':
                    $sortByField = 'keyword';
                    break;
                case 'type':
                    $query->orderByRaw("FIELD({$sortBy}, '{$searchString}') DESC");
                    break;
                case 'service-introduction':
                    $query->selectRaw("MATCH (introduction)
                        AGAINST ('{$searchString}' IN BOOLEAN MODE) AS rel");
                    $query->orderBy('rel', 'desc');
                    break;
                case 'service-description':
                    $query->selectRaw("MATCH (description)
                        AGAINST ('{$searchString}' IN BOOLEAN MODE) AS rel");
                    $query->orderBy('rel', 'desc');
                    break;
                case 'similar-text':
                    $sortByField = '??????';
                    break;
                default:
                    $sortByField = '??????';
            }
        });
        return $queryBuilder;
    }

}

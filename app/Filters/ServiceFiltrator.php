<?php


namespace App\Filters;

use App\Http\Requests\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class ServiceFiltrator {

    public function apply(Builder $queryBuilder, Request $request) {
        if ($request->filled('start_date')) {
            $queryBuilder->whereHas('schedules', function($query) use ($request) {
                $query->where('start_date', '>=', $request->start_date);
            });
        }
        if ($request->filled('end_date')) {
            $queryBuilder->whereHas('schedules', function($query) use ($request) {
                $query->where('end_date', '<=', $request->end_date);
            });
        }
        if ($request->filled('id')) {
            $queryBuilder->whereHas('schedules.locations', function($query) use ($request) {
                $query->where('id', '=', $request->id);
            });
        }

        $serviceTypes = $request->getArrayFromRequest('service_type');
        if (!empty($serviceTypes)) {
            $queryBuilder->whereIn('service_type_id', array_values($serviceTypes));
        }

        $searchString = $request->get('search');
        $sortBy = $request->get('sortby');
        $queryBuilder->when($request->filled('sortby'), static function(Builder $query) use ($sortBy, $searchString) {
            switch ($sortBy) {
                case 'schedule':
                    $query->selectRaw('*, DATEDIFF(start_date, now()) as dif')->join('schedules', 'services.id', '=', 'schedules.service_id')
                          ->orderByRaw('ABS(dif)');
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
            }
        });
        return $queryBuilder;
    }

}

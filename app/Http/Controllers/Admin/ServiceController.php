<?php

namespace App\Http\Controllers\Admin;

use App\Filters\ServiceFiltrator;
use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Transformers\ServiceTransformer;
use App\Http\Requests\Request;

class ServiceController extends Controller
{

    public function index(Request $request)
    {
        $query = Service::query();
        $serviceFilter = new ServiceFiltrator();
        $serviceFilter->apply($query, $request);

        $includes = $request->getIncludes();

        $paginator = $query->with($includes)->paginate($request->getLimit());
        $services = $paginator->getCollection();

        $fractal = fractal($services, new ServiceTransformer())
            ->parseIncludes($includes)
            ->toArray();

        return response($fractal)
            ->withPaginationHeaders($paginator);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $service = Service::create($data);
        return fractal($service, new ServiceTransformer())->respond();
    }

    public function show(Service $service,Request $request)
    {
        return fractal($service, new ServiceTransformer())->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function update(Request $request, Service $service)
    {
        $service->update($request->all());
        return fractal($service, new ServiceTransformer())->respond();
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return response(null, 204);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Http\Requests\Request;
use App\Filters\ServiceFiltrator;
use App\Events\ServiceListingLive;
use App\Transformers\ServiceTransformer;
use App\Http\Requests\Services\StoreServiceRequest;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query         = Service::query();
        $serviceFilter = new ServiceFiltrator();
        $serviceFilter->apply($query, $request);

        $includes = $request->getIncludes();

        $paginator = $query->with($includes)->paginate($request->getLimit());
        $services  = $paginator->getCollection();

        $fractal = fractal($services, new ServiceTransformer())
            ->parseIncludes($includes)
            ->toArray();

        return response($fractal)
            ->withPaginationHeaders($paginator);

    }

    public function show(Request $request, Service $service)
    {
        return fractal($service, new ServiceTransformer())
            ->parseIncludes($request->getIncludes())
            ->respond();
    }

    public function store(StoreServiceRequest $request)
    {
        $user    = $request->user();
        $data    = $request->all();
        $service = $user->services()->create($data);

        if($request->filled('media_images')){
            $service->mediaImages()->createMany($request->get('media_images'));
        }

        return fractal($service, new ServiceTransformer())->respond();
    }

    public function update(Request $request, Service $service)
    {
        $service->update($request->all());
        return fractal($service, new ServiceTransformer())->respond();
    }

    public function publish(Request $request, Service $service)
    {
        $service->is_published = true;
        $service->save();
        $service->fresh();

        event(new ServiceListingLive($service, $request->user()));

        return fractal($service, new ServiceTransformer())->respond();
    }

    public function unpublish(Service $service)
    {
        $service->is_published = false;
        $service->save();
        $service->fresh();

        return fractal($service, new ServiceTransformer())->respond();
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return response(null, 204);
    }

    public function storeFavorite(Service $service)
    {
        if ($service->favorite()) {
            return response(null, 200);
        }

        Auth::user()->favourite_services()->attach($service->id);
        return response(null, 201);
    }

    public function deleteFavorite(Service $service)
    {
        Auth::user()->favourite_services()->detach($service->id);
        return response(null, 204);
    }

}

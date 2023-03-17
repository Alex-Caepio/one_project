<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Service\ServiceStore;
use App\Actions\Service\ServiceUpdate;
use App\Filters\ServiceFiltrator;
use App\Http\Controllers\Controller;
use App\Http\Requests\Services\ServiceOwnerRequest;
use App\Http\Requests\Services\ServicePublishRequest;
use App\Http\Requests\Services\StoreServiceRequest;
use App\Http\Requests\Services\UpdateServiceRequest;
use App\Models\Service;
use App\Transformers\ServiceTransformer;
use App\Http\Requests\Request;
use Stripe\StripeClient;

class ServiceController extends Controller {

    public function index(Request $request) {
        $query = Service::query();
        $serviceFilter = new ServiceFiltrator();
        $serviceFilter->apply($query, $request);

        $includes = $request->getIncludes();

        $paginator = $query->with($includes)->paginate($request->getLimit());
        $services = $paginator->getCollection();

        $fractal = fractal($services, new ServiceTransformer())->parseIncludes($includes)->toArray();

        return response($fractal)->withPaginationHeaders($paginator);
    }

    public function store(StoreServiceRequest $request) {
        $service = run_action(ServiceStore::class, $request, app(StripeClient::class));
        if ($service === null) {
            return abort(500, 'Service cannot be created');
        }
        return fractal($service, new ServiceTransformer())->respond();
    }

    public function show(Service $service, Request $request) {
        return fractal($service, new ServiceTransformer())->parseIncludes($request->getIncludes())->toArray();
    }

    public function update(Service $service, UpdateServiceRequest $request) {
        $service = run_action(ServiceUpdate::class, $request, $service);
        return fractal($service, new ServiceTransformer())->respond();
    }

    public function destroy(Service $service, ServiceOwnerRequest $request) {
        $service->delete();
        return response(null, 204);
    }

    public function unpublish(Service $service, ServiceOwnerRequest $request) {
        $service->is_published = false;
        $service->save();
        return response(null, 204);
    }

    public function publish(Service $service, ServicePublishRequest $request) {
        $service->is_published = true;
        $service->save();
        return response(null, 204);
    }
}

<?php

namespace App\Http\Controllers;

use App\Actions\Service\ServiceStore;
use App\Actions\Service\ServiceUpdate;
use App\Http\Requests\Services\ServiceOwnerRequest;
use App\Models\Service;
use App\Http\Requests\Request;
use App\Filters\ServiceFiltrator;
use App\Events\ServiceListingLive;
use App\Transformers\ServiceTransformer;
use App\Http\Requests\Services\StoreServiceRequest;
use App\Http\Requests\Services\ServicePublishRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class ServiceController extends Controller {

    public function index(Request $request) {
        $paginator = $this->getServiceList(Service::published(), $request);
        $services = $paginator->getCollection();
        $fractal = fractal($services, new ServiceTransformer())->parseIncludes($request->getIncludes())->toArray();
        return response($fractal)->withPaginationHeaders($paginator);
    }

    public function practitionerServiceList(Request $request) {
        $paginator = $this->getServiceList(Service::where('user_id', Auth::user()->id), $request);
        $services = $paginator->getCollection();
        $fractal = fractal($services, new ServiceTransformer())->parseIncludes($request->getIncludes())->toArray();
        return response($fractal)->withPaginationHeaders($paginator);
    }

    /**
     * @param Builder $queryBuilder
     * @param \App\Http\Requests\Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private function getServiceList(Builder $queryBuilder, Request $request): LengthAwarePaginator {
        $serviceFilter = new ServiceFiltrator();
        $serviceFilter->apply($queryBuilder, $request);
        return $queryBuilder->with($request->getIncludes())->paginate($request->getLimit());
    }

    public function show(Request $request, Service $publicService) {
        return fractal($publicService, new ServiceTransformer())->parseIncludes($request->getIncludes())->respond();
    }

    public function practitionerServiceShow(ServiceOwnerRequest $request, Service $service) {
        return fractal($service, new ServiceTransformer())->parseIncludes($request->getIncludes())->respond();
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


    public function store(StoreServiceRequest $request, StripeClient $stripe) {

        try {

            $stripeProduct = $stripe->products->create(['name' => $request->title,]);
            $service = run_action(ServiceStore::class, $request, $stripeProduct);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::channel('stripe_product_errors')->info("Client could not create product", [
                'user_id' => $request->user_id,
                'stripe_product'  => $stripeProduct->id,
                'name' => $request->title,
                'message' => $e->getMessage(),
            ]);

            return abort(500);
        }

        Log::channel('stripe_product_success')->info("Client created product", [
            'user_id' => $request->user_id,
            'stripe_product'  => $stripeProduct->id,
            'name' => $request->title,
        ]);

        return fractal($service, new ServiceTransformer())->respond();
    }

    public function update(StoreServiceRequest $request, Service $service) {
        $service = run_action(ServiceUpdate::class, $request, $service);
        return fractal($service, new ServiceTransformer())->respond();
    }

    public function publish(Service $service, ServicePublishRequest $request) {
        $service->is_published = true;
        $service->save();
        $service->fresh();
        event(new ServiceListingLive($service, $request->user()));
        return response(null, 204);
    }

    public function storeFavorite(Service $service) {
        if ($service->favorite()) {
            return response(null, 200);
        }

        Auth::user()->favourite_services()->attach($service->id);
        return response(null, 201);
    }

    public function deleteFavorite(Service $service) {
        Auth::user()->favourite_services()->detach($service->id);
        return response(null, 204);
    }

}

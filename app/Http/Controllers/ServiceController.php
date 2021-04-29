<?php

namespace App\Http\Controllers;

use App\Actions\Service\ServiceStore;
use App\Actions\Service\ServiceUpdate;
use App\Events\ServiceUnpublished;
use App\Http\Requests\Services\ServiceOwnerRequest;
use App\Http\Requests\Services\UpdateServiceRequest;
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
        if ($request->get('with')) {
            $service->load(array_filter($request->getArrayFromRequest('with'), static function($value) use($service) {
                $relationParts = explode('.', $value);
                if (method_exists($service, $relationParts[0])) {
                    return $value;
                }
            }));
        }
        return fractal($service, new ServiceTransformer())
            ->parseIncludes($request->getIncludes())->respond();
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
        $service = run_action(ServiceStore::class, $request, $stripe);
        if ($service === null)  {
            return abort(500, 'Service cannot be created');
        }
        return fractal($service, new ServiceTransformer())->respond();
    }

    public function update(UpdateServiceRequest $request, Service $service) {
        $service = run_action(ServiceUpdate::class, $request, $service);
        return fractal($service, new ServiceTransformer())->respond();
    }

    public function publish(Service $service, ServicePublishRequest $request) {
        $service->is_published = true;
        $service->save();
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

    public function copy(Service $service) {
        $serviceCopy = $service->replicate();
        $serviceCopy->title = "{$service->title} (copy)";
        $serviceCopy->is_published = false;
        $serviceCopy->published_at = null;
        $serviceCopy->save();

        foreach($service->schedules as $schedule) {
            $scheduleCopy = $schedule->replicate();
            $scheduleCopy->service_id = $serviceCopy->id;
            $scheduleCopy->is_published = false;
            $scheduleCopy->save();

            foreach ($schedule->prices as $price) {
                $priceCopy = $price->replicate();
                $priceCopy->schedule_id = $scheduleCopy->id;
                $priceCopy->save();
            }

            foreach($schedule->schedule_availabilities as $scheduleAvailabilitie) {
                $scheduleAvailabilitieCopy = $scheduleAvailabilitie->replicate();
                $scheduleAvailabilitieCopy->schedule_id = $scheduleCopy->id;
                $scheduleAvailabilitieCopy->save();
            }

            foreach($schedule->schedule_unavailabilities as $scheduleUnavailabilitie) {
                $scheduleUnavailabilitieCopy = $scheduleUnavailabilitie->replicate();
                $scheduleUnavailabilitieCopy->schedule_id = $scheduleCopy->id;
                $scheduleUnavailabilitieCopy->save();
            }
        }
        return fractal($serviceCopy, new ServiceTransformer())->respond();
    }

}

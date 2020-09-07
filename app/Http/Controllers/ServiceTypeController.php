<?php

namespace App\Http\Controllers;

use App\Actions\ServiceType\ServiceTypeStore;
use App\Models\ServiceType;
use App\Transformers\ServiceTypeTransformer;
use Illuminate\Http\Request;

class ServiceTypeController extends Controller
{
    public function index()
    {
        $serviceType = ServiceType::all();
        return fractal($serviceType, new ServiceTypeTransformer())->respond();
    }

    public function store(Request $request)
    {
        run_action(ServiceTypeStore::class, $request);
    }

    public function list()
    {
        $serviceTypeList = ServiceType::query();
        return $serviceTypeList->pluck('name', 'id');
    }
}

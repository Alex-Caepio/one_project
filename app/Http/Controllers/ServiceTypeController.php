<?php

namespace App\Http\Controllers;

use App\Actions\ServiceType\ServiceTypeStore;
use App\Models\ServiceType;
use App\Transformers\ServiceTypeTransformer;
use App\Http\Requests\Request;

class ServiceTypeController extends Controller
{
    public function index()
    {
        $serviceType = ServiceType::all();
        return fractal($serviceType, new ServiceTypeTransformer())->respond();
    }
}

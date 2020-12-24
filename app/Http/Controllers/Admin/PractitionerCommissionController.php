<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PractitionerCommissionRequest;
use App\Transformers\PractitionerCommissionTransformer;
use Illuminate\Http\Request;
use App\Models\PractitionerCommission;

class PractitionerCommissionController extends Controller
{
    public function index(Request $request)
    {
       $practitionerCommission = PractitionerCommission::all()->whereHas('user', 'account_type', 'practitioner')->get();

        return fractal($practitionerCommission, new PractitionerCommissionTransformer())
            ->parseIncludes($request->getIncludes());
    }

    public function show(Request $request, PractitionerCommission $practitionerCommission)
    {
        return fractal($practitionerCommission, new PractitionerCommissionTransformer())
            ->parseIncludes($request->getIncludes());
    }

    public function store(PractitionerCommissionRequest $request)
    {
        $practitionerCommission = new PractitionerCommission();

        $data = $request->all();

        $practitionerCommission->fill($data);
        $practitionerCommission->save();

        return fractal($practitionerCommission, new PractitionerCommissionTransformer())->respond();
    }

    public function update(Request $request,PractitionerCommission $practitionerCommission)
    {
        $data = $request->all();
        $practitionerCommission->update($data);

        return fractal($practitionerCommission, new PractitionerCommissionTransformer())->respond();
    }

    public function delete(PractitionerCommission $practitionerCommission)
    {
        $practitionerCommission->delete();

        return response(null, 204);
    }
}

<?php


namespace App\Actions\Country;


use App\Http\Requests\Request;
use App\Models\Country;

class CountryFilter
{
    public function execute(Request $request)
    {
        $county = Country::query();
        if ($request->filled('search')) {
            $search = $request->get('search');
            return $county->where('iso', 'like', "%$search%")
                ->orWhere('name', 'like', "%$search%")
                ->orWhere('nicename', 'like', "%$search%")
                ->orWhere('phonecode', 'like', "%$search%")->get();
        }
    }
}

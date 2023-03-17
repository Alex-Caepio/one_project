<?php


namespace App\Actions\Location;


use App\Http\Requests\Request;
use App\Models\Location;

class LocationList
{
    public function execute(Request $request)
    {
        $location = Location::query();
        if ($request->filled('title')) {
            $title = $request->get('title');
            $location->where('title', 'like', "%$title%");
        }
        return $location->pluck('title', 'id');
    }
}

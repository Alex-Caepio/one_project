<?php


namespace App\Actions\Discipline;


use App\Http\Requests\Request;
use App\Models\Discipline;

class DisciplineStore
{
    public function execute(Request $request)
    {
        $data = $request->all();
        $discipline = Discipline::create($data);
        if ($request->filled('users')) {
            $discipline->featured_practitioners()->attach($request->get('users'));
        }
        if ($request->filled('services')) {
            $discipline->featured_services()->attach($request->get('services'));
        }
        if ($request->filled('users')) {
            $discipline->practitioners()->attach($request->get('users'));
        }
        if ($request->filled('services')) {
            $discipline->services()->attach($request->get('services'));
        }
        if ($request->filled('articles')) {
            $discipline->articles()->attach($request->get('articles'));
        }
        if ($request->filled('focus_areas')) {
            $discipline->focus_areas()->attach($request->get('focus_areas'));
        }
    }
}

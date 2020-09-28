<?php


namespace App\Actions\Discipline;


use App\Http\Requests\Request;
use App\Models\Discipline;

class DisciplineUpdate
{
    public function execute(Request $request,Discipline $discipline)
    {
        $discipline->forceFill([
            'name' => $request->get('name'),
            'url' => $request->get('url'),
            'is_published' => $request->get('is_published'),

        ]);
        if ($request->filled('users')) {
          $discipline->featured_practitioners()->sync($request->get('users'));
        }
        if ($request->filled('services')) {
            $discipline->featured_services()->sync($request->get('services'));
        }
        if ($request->filled('users')) {
            $discipline->practitioners()->sync($request->get('users'));
        }
        if ($request->filled('services')) {
            $discipline->services()->sync($request->get('services'));
        }
        if ($request->filled('articles')) {
            $discipline->articles()->sync($request->get('articles'));
        }
        if ($request->filled('focus_areas')) {
            $discipline->focus_areas()->sync($request->get('focus_areas'));
        }
       return $discipline->update();
    }
}

<?php


namespace App\Actions\Discipline;


use App\Http\Requests\Request;
use App\Models\Discipline;

class DisciplineFilter
{
    public function execute(Request $request)
    {
        $discipline = Discipline::query()->where('is_published', true);
        if ($request->filled('name')) {
            $name = $request->get('name');
            $discipline->where('name', 'like', "%$name%");
        }
        return $discipline->get();
    }
}

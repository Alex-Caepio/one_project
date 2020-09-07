<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discipline;
use App\Models\DisciplineImage;
use App\Models\DisciplineVideo;
use App\Models\User;
use App\Transformers\DisciplineTransformer;
use App\Http\Requests\Request;

class DisciplineController extends Controller
{

    public function index(Request $request)
    {
        $discipline = Discipline::all();
        return fractal($discipline, new DisciplineTransformer())->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function store(Request $request)
    {
        //$user = User::where('account_type', 'practitioner')->get();
        $data = $request->all();
        $discipline = Discipline::create($data);
        $discipline->practitioners()->attach($request->get('users'));
        $discipline->services()->attach($request->get('services'));
        $discipline->articles()->attach($request->get('articles'));
        $discipline->focus_areas()->attach($request->get('focus_areas'));
        return fractal($discipline, new DisciplineTransformer())->respond();
    }

    public function storeImage(Request $request, Discipline $discipline)
    {
        $path = public_path('\img\discipline\\' . $discipline->id . '\\');
        $fileName = $request->file('image')->getClientOriginalName();
        $request->file('image')->move($path, $fileName);
        $imageDiscipline = new DisciplineImage();
        $imageDiscipline->forceFill([
            'discipline_id' => $discipline->id,
            'path' => $path . $fileName,
        ]);
        $imageDiscipline->save();
    }
    public function storeVideo(Request $request, Discipline $discipline)
    {
        $videoDiscipline = new DisciplineVideo();
        $videoDiscipline->forceFill([
            'discipline_id' => $discipline->id,
            'link' => $request->get('link'),
        ]);
        $videoDiscipline->save();
    }

    public function show(Discipline $discipline,Request $request)
    {
        return fractal($discipline, new DisciplineTransformer())->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function update(Request $request, Discipline $discipline)
    {
        $discipline->update($request->all());

        return fractal($discipline, new DisciplineTransformer())->respond();
    }

    public function destroy(Discipline $discipline)
    {
        $discipline->delete();

        return response(null, 204);
    }

}

<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Discipline\DisciplineStore;
use App\Actions\Discipline\DisciplineUpdate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DisciplinePublishRequest;
use App\Http\Requests\Request;
use App\Models\Discipline;
use App\Models\DisciplineImage;
use App\Models\DisciplineVideo;
use App\Transformers\DisciplineTransformer;

class
DisciplineController extends Controller
{

    public function index(Request $request)
    {
        $discipline = Discipline::all();
        return fractal($discipline, new DisciplineTransformer())->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function store(Request $request)
    {
        run_action(DisciplineStore::class, $request);
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

    public function show(Discipline $discipline, Request $request)
    {
        return fractal($discipline, new DisciplineTransformer())->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function update(Request $request, Discipline $discipline)
    {
        run_action(DisciplineUpdate::class, $request, $discipline);
    }

    public function destroy(Discipline $discipline)
    {
        $discipline->delete();

        return response(null, 204);
    }

    public function unpublish(Discipline $discipline)
    {
        $discipline->forceFill([
            'is_published' => false,
        ]);
        $discipline->update();
    }
    public function publish(Discipline $discipline,DisciplinePublishRequest $request)
    {
        $discipline->forceFill([
            'is_published' => true,
        ]);
        $discipline->update();
    }

}

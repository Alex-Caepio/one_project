<?php

namespace App\Http\Controllers;

use App\Actions\Discipline\DisciplineFilter;
use App\Http\Requests\Request;
use App\Models\Discipline;
use App\Transformers\DisciplineTransformer;
use App\Http\Requests\Disciplines\StoreDisciplineRequest;


class DisciplineController extends Controller
{
    public function index(Request $request)
    {
        $discipline = Discipline::where('is_published', true)->get();
        return fractal($discipline, new DisciplineTransformer())
            ->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function store(StoreDisciplineRequest $request)
    {
        $user    = $request->user();
        $data    = $request->all();
        $service = $user->services()->create($data);

        if($request->filled('media_images')){
            $service->mediaImages()->createMany($request->get('media_images'));
        }

        if($request->filled('media_videos')){
            $service->mediaVideos()->createMany($request->get('media_videos'));
        }

        if($request->filled('media_files')){
            $service->mediaFiles()->createMany($request->get('media_files'));
        }

        return fractal($service, new DisciplineTransformer())->respond();
    }
    public function show(Discipline $discipline,Request $request)
    {
        return fractal($discipline, new DisciplineTransformer())->parseIncludes($request->getIncludes())
            ->toArray();
    }
    public function list()
    {
        $discipline = Discipline::query()->where('is_published', true);
        return $discipline->pluck('name', 'id');
    }

    public function filter(Request $request)
    {
        $discipline = run_action(DisciplineFilter::class, $request);
        return fractal($discipline, new DisciplineTransformer())->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function indexImage(Discipline $discipline)
    {
        $allImage = $discipline->discipline_images;
        return response($allImage);
    }

    public function indexVideo(Discipline $discipline)
    {
        $allVideos = $discipline->discipline_videos;
        return response($allVideos);
    }
}

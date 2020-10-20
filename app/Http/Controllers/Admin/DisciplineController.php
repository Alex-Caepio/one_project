<?php

namespace App\Http\Controllers\Admin;

use App\Models\Discipline;
use App\Http\Requests\Request;
use App\Http\Controllers\Controller;
use App\Transformers\DisciplineTransformer;
use App\Actions\Discipline\DisciplineStore;
use App\Actions\Discipline\DisciplineUpdate;
use App\Http\Requests\Admin\DisciplineStoreRequest;
use App\Http\Requests\Admin\DisciplinePublishRequest;
use Illuminate\Support\Str;

class
DisciplineController extends Controller
{
    public function index(Request $request)
    {
        $query = Discipline::query();

        if ($request->hasSearch()) {
            $search = $request->search();

            $query->where(
                function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('introduction', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                }
            );
        }

        $includes = $request->getIncludes();
        $paginator = $query->with($includes)
            ->paginate($request->getLimit());

        $discipline = $paginator->getCollection();

        return response(fractal($discipline, new DisciplineTransformer())
            ->parseIncludes($includes)->toArray())
            ->withPaginationHeaders($paginator);
    }

    public function store(DisciplineStoreRequest $request)
    {
        $data        = $request->all();
        $url         = $data['url'] ?? to_url($data['name']);
        $data['url'] = $url;
        $discipline  = Discipline::create($data);

        if ($request->filled('featured_practitioners')) {
            $discipline->featured_practitioners()->sync($request->get('featured_practitioners'));
        }
        if ($request->filled('featured_services')) {
            $discipline->featured_services()->sync($request->get('featured_services'));
        }
        if ($request->filled('focus_areas')) {
            $discipline->focus_areas()->sync($request->get('focus_areas'));
        }
        if ($request->filled('related_disciplines')) {
            $discipline->related_disciplines()->sync($request->get('related_disciplines'));
        }
        if($request->filled('media_images')){
            $discipline->media_images()->createMany($request->get('media_images'));
        }
        if($request->filled('media_videos')){
            $discipline->media_videos()->createMany($request->get('media_videos'));
        }
        if($request->filled('media_files')){
            $discipline->media_files()->createMany($request->get('media_files'));
        }

        return fractal($discipline, new DisciplineTransformer())
            ->parseIncludes($request->getIncludes())
            ->respond();
    }

    public function show(Discipline $discipline, Request $request)
    {
        return fractal($discipline, new DisciplineTransformer())->parseIncludes($request->getIncludes())
            ->toArray();
    }

    // public function update(Request $request, Discipline $discipline)
    // {
    //     $data        = $request->all();
    //     $url         = $data['url'] ?? to_url($data['name']);
    //     $data['url'] = $url;
    //     $discipline->update($data);

    //     if($request->filled('media_images')){
    //         $discipline->mediaImages()->updateOrCreate($request->get('media_images'));
    //     }
    //     if($request->filled('media_videos')){
    //         $discipline->mediaVideos()->createMany($request->get('media_videos'));
    //     }
    //     if($request->filled('media_files')){
    //         $discipline->mediaFiles()->createMany($request->get('media_files'));
    //     }

    //     return fractal($discipline, new DisciplineTransformer())
    //         ->parseIncludes($request->getIncludes())
    //         ->respond();
    // }

    public function unpublish(Discipline $discipline)
    {
        $discipline->forceFill([
            'is_published' => false,
        ]);
        $discipline->update();
    }

    public function publish(Discipline $discipline, DisciplinePublishRequest $request)
    {
        $discipline->forceFill([
            'is_published' => true,
        ]);
        $discipline->update();
    }

    public function toUrl($string)
    {
        $kebab = Str::kebab($string);
        return preg_replace("/[^a-zA-Z0-9\-]+/", "", $kebab);
    }

}

<?php

namespace App\Http\Controllers\Admin;

use App\Models\Discipline;
use App\Http\Requests\Request;
use App\Http\Controllers\Controller;
use App\Transformers\DisciplineTransformer;
use App\Actions\Discipline\DisciplineStore;
use App\Http\Requests\Admin\DisciplineStoreRequest;
use App\Http\Requests\Admin\DisciplinePublishRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DisciplineController extends Controller
{
    public function index(Request $request)
    {
        $query = Discipline::query();

        if ($request->hasOrderBy()) {
            $order = $request->getOrderBy();
            $query->orderBy($order['column'], $order['direction']);
        }

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

        $isPublished = $request->getBoolFromRequest('is_published');
        if ($isPublished !== null) {
            $query->where('is_published', $isPublished);
        }

        $includes  = $request->getIncludes();
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
        if ($request->filled('featured_focus_areas')) {
            $discipline->featured_focus_areas()->sync($request->get('featured_focus_areas'));
        }
        if ($request->filled('featured_articles')) {
            $discipline->featured_articles()->sync($request->get('featured_articles'));
        }
        if ($request->filled('media_images')) {
            $discipline->media_images()->createMany($request->get('media_images'));
        }
        if ($request->filled('media_videos')) {
            $discipline->media_videos()->createMany($request->get('media_videos'));
        }
        if ($request->filled('media_files')) {
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

    public function update(Request $request, Discipline $discipline)
    {
        if ($request->filled('media_images') && !empty($request->media_images))
        {
            foreach ($request->media_images as $mediaImage)
            {
                if (Storage::disk(config('image.image_storage'))->missing(file_get_contents($mediaImage['url'])))
                {
                    $image = Storage::disk(config('image.image_storage'))
                        ->put("/images/disciplines/{$discipline->id}/media_images/", file_get_contents($mediaImage['url']));
                    $image_urls[]['url'] = Storage::url($image);
                }
            }
            $request->media_images = $image_urls;
        }
        $data        = $request->all();
        $url         = $data['url'] ?? to_url($data['name']);
        $data['url'] = $url;

        $discipline->update($data);

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
        if ($request->filled('featured_at_focus_area')) {
            $discipline->featured_focus_area()->sync($request->get('featured_focus_area'));
        }
        if ($request->filled('featured_articles')) {
            $discipline->featured_articles()->sync($request->get('featured_articles'));
        }
        if ($request->filled('featured_focus_areas')) {
            $discipline->featured_focus_areas()->sync($request->get('featured_focus_areas'));
        }
        if ($request->filled('media_images') && !empty($request->media_images)){
            $discipline->media_images()->whereNotIn('url', $request->media_images)->delete();
            $urls = collect($request->media_images)->pluck('url');
            $recurringURL = $discipline->media_images()->whereIn('url', $urls)->pluck('url')->toArray();
            $newImages = $urls->filter(function($value) use ($recurringURL) {
                return !in_array($value, $recurringURL);
            });

            foreach ($newImages as $url){
                $imageUrlToStore[]['url'] = $url;
            }

            $discipline->media_images()->createMany($imageUrlToStore);
        }
        if ($request->filled('media_videos') && !empty($request->media_videos)) {
            $discipline->media_videos()->whereNotIn('url', $request->media_videos)->delete();
            $urls = collect($request->media_videos)->pluck('url');
            $recurringURL = $discipline->media_videos()->whereIn('url', $urls)->pluck('url')->toArray();
            $newVideos = $urls->filter(function($value) use ($recurringURL) {
                return !in_array($value, $recurringURL);
            });

            foreach ($newVideos as $url){
                $videoUrlToStore[]['url'] = $url;
            }

            $discipline->media_videos()->createMany($videoUrlToStore);
        }
        if ($request->has('media_files')) {
            $discipline->media_files()->delete();
            $discipline->media_files()->createMany($request->get('media_files'));
        }

        return fractal($discipline, new DisciplineTransformer())
            ->parseIncludes($request->getIncludes())
            ->respond();
    }

    public function unpublish(Discipline $discipline)
    {
        $discipline->forceFill([
            'is_published' => false,
        ]);
        $discipline->update();

        return response(null, 204);
    }

    public function publish(Discipline $discipline, DisciplinePublishRequest $request)
    {
        $discipline->forceFill([
            'is_published' => true,
        ]);
        $discipline->update();

        return response(null, 204);
    }

    public function destroy(Discipline $discipline)
    {
        DB::beginTransaction();
        $discipline->services()->detach();
        $discipline->articles()->detach();
        $discipline->featured_services()->detach();
        $discipline->featured_practitioners()->detach();
        $discipline->practitioners()->detach();
        $discipline->focus_areas()->detach();
        $discipline->related_disciplines()->detach();

        $discipline->media_images()->delete();
        $discipline->media_videos()->delete();
        $discipline->media_files()->delete();
        $discipline->delete();
        DB::commit();

        return response(null, 204);
    }
}

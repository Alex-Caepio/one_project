<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FocusAreaPublishRequest;
use App\Http\Requests\Admin\FocusAreaStoreRequest;
use App\Http\Requests\Admin\FocusAreaUpdateRequest;
use App\Http\Requests\Image\IconRequests;
use App\Http\Requests\Image\ImageRequests;
use App\Http\Requests\Request;
use App\Models\FocusArea;
use App\Models\FocusAreaImage;
use App\Models\FocusAreaVideo;
use App\Transformers\FocusAreaTransformer;
use Illuminate\Support\Facades\DB;

class FocusAreaController extends Controller
{
    public function index(Request $request)
    {
        $query = FocusArea::query();

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

        $includes  = $request->getIncludes();
        $paginator = $query->with($includes)
            ->paginate($request->getLimit());

        $focus = $paginator->getCollection();

        return response(fractal($focus, new FocusAreaTransformer())
            ->parseIncludes($includes)->toArray())
            ->withPaginationHeaders($paginator);


    }

    public function store(FocusAreaStoreRequest $request)
    {
        $data        = $request->all();
        $url         = $data['url'] ?? to_url($data['name']);
        $data['url'] = $url;
        $focusArea   = FocusArea::create($data);

        if ($request->filled('featured_practitioners')) {
            $focusArea->featured_practitioners()->sync($request->get('featured_practitioners'));
        }
        if ($request->filled('featured_disciplines')) {
            $focusArea->featured_disciplines()->sync($request->get('featured_disciplines'));
        }
        if ($request->filled('featured_articles')) {
            $focusArea->featured_articles()->sync($request->get('featured_articles'));
        }
        if ($request->filled('featured_services')) {
            $focusArea->featured_services()->sync($request->get('featured_services'));
        }
        if ($request->filled('featured_focus_areas')) {
            $focusArea->featured_focus_areas()->sync($request->get('featured_focus_areas'));
        }

        if ($request->filled('media_images')) {
            $focusArea->media_images()->createMany($request->get('media_images'));
        }
        if ($request->filled('media_videos')) {
            $focusArea->media_videos()->createMany($request->get('media_videos'));
        }
        if ($request->filled('media_files')) {
            $focusArea->media_files()->createMany($request->get('media_files'));
        }

        $focusArea->practitioners()->attach($request->get('users'));
        $focusArea->services()->attach($request->get('services'));
        $focusArea->articles()->attach($request->get('articles'));
        $focusArea->disciplines()->attach($request->get('disciplines'));

        return fractal($focusArea, new FocusAreaTransformer())
            ->parseIncludes($request->getIncludes())
            ->respond();
    }

    public function show(FocusArea $focusArea, Request $request)
    {
        return fractal($focusArea, new FocusAreaTransformer())
            ->parseIncludes($request->getIncludes())
            ->respond();
    }

    public function update(FocusAreaUpdateRequest $request, FocusArea $focusArea)
    {
        $data        = $request->all();
//        $url         = $data['url'] ?? to_url($data['name']);
//        $data['url'] = $url;

        $focusArea->update($data);

        if ($request->filled('featured_practitioners')) {
            $focusArea->featured_practitioners()->sync($request->get('featured_practitioners'));
        }
        if ($request->filled('featured_disciplines')) {
            $focusArea->featured_disciplines()->sync($request->get('featured_disciplines'));
        }
        if ($request->filled('featured_articles')) {
            $focusArea->featured_articles()->sync($request->get('featured_articles'));
        }
        if ($request->filled('featured_services')) {
            $focusArea->featured_services()->sync($request->get('featured_services'));
        }
        if ($request->filled('featured_focus_areas')) {
            $focusArea->featured_focus_areas()->sync($request->get('featured_focus_areas'));
        }


        if ($request->filled('practitioners')) {
            $focusArea->practitioners()->sync($request->get('users'));
        }
        if ($request->filled('services')) {
            $focusArea->services()->sync($request->get('services'));
        }
        if ($request->filled('articles')) {
            $focusArea->articles()->sync($request->get('articles'));
        }
        if ($request->filled('disciplines')) {
            $focusArea->disciplines()->sync($request->get('disciplines'));
        }

        if ($request->has('media_images')) {
            $focusArea->media_images()->delete();
            $focusArea->media_images()->createMany($request->get('media_images'));
        }
        if ($request->has('media_videos')) {
            $focusArea->media_videos()->delete();
            $focusArea->media_videos()->createMany($request->get('media_videos'));
        }
        if ($request->has('media_files')) {
            $focusArea->media_files()->delete();
            $focusArea->media_files()->createMany($request->get('media_files'));
        }

        return fractal($focusArea, new FocusAreaTransformer())
            ->parseIncludes($request->getIncludes())
            ->respond();
    }

    public function destroy(FocusArea $focusArea)
    {
        DB::beginTransaction();
        $focusArea->practitioners()->detach();
        $focusArea->services()->detach();
        $focusArea->articles()->detach();
        $focusArea->delete();
        DB::commit();

        return response(null, 204);
    }

    public function storeImages(ImageRequests $request, FocusArea $focusArea)
    {
        $path = public_path('\img\focus-areas\images\\' . $focusArea->id . '\\');
        $fileName = $request->file('images')->getClientOriginalName();
        $request->file('images')->move($path, $fileName);
        $imageFocus = new FocusAreaImage();
        $imageFocus->forceFill([
            'focus_area_id' => $focusArea->id,
            'path' => $path . $fileName,
        ]);
        $imageFocus->save();
    }
   public function storeVideos(Request $request, FocusArea $focusArea)
   {
       $videoFocus = new FocusAreaVideo();
       $videoFocus->forceFill([
           'focus_area_id' => $focusArea->id,
           'link' => $request->get('link'),
       ]);
       $videoFocus->save();
   }
    public function image(ImageRequests $request, FocusArea $focusArea)
    {
        $path = public_path('\img\focus-areas\\' . $focusArea->id . '\\');
        $fileName = $request->file('image')->getClientOriginalName();
        $request->file('image')->move($path, $fileName);
    }

   public function icon(IconRequests $request, FocusArea $focusArea)
   {
       $path = public_path('\icon\focus-areas\\' . $focusArea->id . '\\');
       $fileName = $request->file('icon')->getClientOriginalName();
       $request->file('icon')->move($path, $fileName);
   }

    public function unpublish(FocusArea $focusArea)
    {
        $focusArea->forceFill([

            'is_published' => false,
        ]);
        $focusArea->update();

        return response(null, 204);
    }

    public function publish(FocusArea $focusArea, FocusAreaPublishRequest $request)
    {
        $focusArea->forceFill([
            'is_published' => true,
        ]);
        $focusArea->update();

        return response(null, 204);
    }
}

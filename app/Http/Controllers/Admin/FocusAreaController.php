<?php

namespace App\Http\Controllers\Admin;

use App\Actions\FocusArea\FocusAreaCleanupRequest;
use App\Actions\FocusArea\FocusAreaSaveRelationsRequest;
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
use App\Traits\HasMediaItems;
use App\Transformers\FocusAreaTransformer;
use Illuminate\Support\Facades\DB;

class FocusAreaController extends Controller
{
    use HasMediaItems;

    public function index(Request $request)
    {
        $query = FocusArea::query();

        if ($request->hasOrderBy()) {
            $order = $request->getOrderBy();
            $query->orderBy($order['column'], $order['direction']);
        }

        if ($request->hasSearch()) {
            $search = $request->search();

            $query->where(function ($query) use ($search) {
                $searchString = "%{$search}%";
                $query->where('name', 'like', $searchString)
                    ->orWhere('introduction', 'like', $searchString)
                    ->orWhere('description', 'like', $searchString)
                    ->orWhereHas('users', static function ($queryHas) use ($searchString) {
                        $queryHas->where('email', 'LIKE', $searchString);
                    });
            });
        }

        $includes = $request->getIncludes();
        $paginator = $query->with($includes)->paginate($request->getLimit());

        $focus = $paginator->getCollection();

        return response(fractal($focus, new FocusAreaTransformer())->parseIncludes($includes)
            ->toArray())->withPaginationHeaders($paginator);
    }

    public function store(FocusAreaStoreRequest $request)
    {
        $data = run_action(FocusAreaCleanupRequest::class, $request);
        $focusArea = FocusArea::create($data);

        run_action(FocusAreaSaveRelationsRequest::class, $focusArea, $request);
        return fractal($focusArea, new FocusAreaTransformer())->parseIncludes($request->getIncludes())->respond();
    }

    public function show(FocusArea $focusArea, Request $request)
    {
        return fractal($focusArea, new FocusAreaTransformer())->parseIncludes($request->getIncludes())->respond();
    }

    public function update(FocusAreaUpdateRequest $request, FocusArea $focusArea)
    {
        $focusArea->update(run_action(FocusAreaCleanupRequest::class, $request));

        run_action(FocusAreaSaveRelationsRequest::class, $focusArea, $request);

        return fractal($focusArea, new FocusAreaTransformer())->parseIncludes($request->getIncludes())->respond();
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
            'path'          => $path . $fileName,
        ]);
        $imageFocus->save();
    }

    public function storeVideos(Request $request, FocusArea $focusArea)
    {
        $videoFocus = new FocusAreaVideo();
        $videoFocus->forceFill([
            'focus_area_id' => $focusArea->id,
            'link'          => $request->get('link'),
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FocusArea\FocusAreaRequests;
use App\Http\Requests\Image\IconRequests;
use App\Http\Requests\Image\ImageRequests;
use App\Http\Requests\Request;
use App\Models\FocusArea;
use App\Models\FocusAreaImage;
use App\Models\FocusAreaVideo;
use App\Transformers\FocusAreaTransformer;

class FocusAreaController extends Controller
{
    public function index(Request $request)
    {
        $focus = FocusArea::all();
        return fractal($focus, new FocusAreaTransformer())
            ->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function store(FocusAreaRequests $request)
    {
        $data = $request->all();
        $focus = FocusArea::create($data);
        $focus->practitioners()->attach($request->get('users'));
        $focus->services()->attach($request->get('services'));
        $focus->articles()->attach($request->get('articles'));
        return fractal($focus, new FocusAreaTransformer())->respond();
    }

    public function destroy(FocusArea $focusArea)
    {
        $focusArea->delete();
        return response(null, 204);
    }

    public function update(FocusAreaRequests $request, FocusArea $focusArea)
    {
        $focusArea->update($request->all());
        return fractal($focusArea, new FocusAreaTransformer())->respond();
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

}

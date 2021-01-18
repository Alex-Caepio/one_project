<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Http\Requests\Image\ImageUploadRequest;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function upload(ImageUploadRequest $request)
    {
        Storage::disk(config('image.image_storage'))->put('tmp/', $request->file);

        config('image.image_storage') == 'local' ?
            $url = Storage::path($request->file) :
            $url = Storage::url($request->file);

        return response()->json(['url' => $url]);
    }
}

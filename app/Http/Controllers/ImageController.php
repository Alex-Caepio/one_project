<?php

namespace App\Http\Controllers;

use App\Http\Requests\Image\ImageUploadRequest;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function upload(ImageUploadRequest $request)
    {
        $image = Storage::disk(config('image.image_storage'))->put('tmp/', $request->file);

        $url = Storage::url($image);

        return response()->json(['url' => $url]);
    }
}

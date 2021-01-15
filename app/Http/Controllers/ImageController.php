<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Http\Requests\Image\ImageUploadRequest;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    private $image;
    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    public function upload(ImageUploadRequest $request)
    {
        $ImageStorage = config('image.image_storage');

        $path = Storage::disk($ImageStorage)->put('/tmp', $request->file);
        $request->merge([
            'size' => $request->file('file')->getSize(),
            'path' => $path
        ]);
        $image = Image::create($request->only('path', 'title', 'size'));

         return $image->url;
    }
}

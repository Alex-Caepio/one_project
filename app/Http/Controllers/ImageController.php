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

        $path = Storage::disk($ImageStorage)->put('images/originals', $request->file);
        $request->merge([
            'size' => $request->file->getClientSize(),
            'path' => $path
        ]);
        $this->image->create($request->only('path', 'title', 'size'));
        return back()->with('success', 'Image Successfully Saved');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\Image\ImageUploadRequest;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function upload(ImageUploadRequest $request)
    {
        $image = Storage::disk(config('image.image_storage'))->put('tmp', $request->file);
        $url = config('image.image_storage') != 'local'
            ? Storage::url($image)
            : env('APP_URL') . Storage::url($image);

        $message = $image == null
            ?'Image haven\'t been stored'
            :'Image stored successfully';

        $status = $image == null
            ? 'Error'
            : 'Success';

        $size = Storage::size($request->file);
        $name = $request->file->getClientOriginalName();
        $type = $request->file->extension();
        $mime = $request->file->getClientMimeType();
        $public_id = basename($url);
        $signature = hash_file('md5',$request->file);

        return response()->json([
            'url' => $url,
            'message?' => $message,
            'bytes' => $size,
            'mime' => $mime,
            'original_extension' => $type ,
            'original_filename' => $name ,
            'public_id' => $public_id ,
            'signature?' => $signature,
            'status' => $status
        ]);
    }
}

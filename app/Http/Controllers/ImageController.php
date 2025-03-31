<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageUploadRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function upload(ImageUploadRequest $request): JsonResponse
    {
        $path = $request->file('image')->store('public/images');
        $url = Storage::temporaryUrl($path, now()->addHours(3));

        return response()->json(['url' => $url]);
    }
}

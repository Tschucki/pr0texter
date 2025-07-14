<?php

namespace App\Http\Controllers;

use App\Http\Requests\PreviewRequest;
use App\Models\Template;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Spatie\Image\Image;

class PreviewController extends Controller
{
    public function __invoke(Template $template, PreviewRequest $request)
    {
        ignore_user_abort(false);

        $imagePath = Storage::disk('local')->path(Str::uuid()->toString() . '.png');

        if (connection_aborted()) {
            return response()->json(['message' => 'Request aborted'], 499);
        }

        try {
            Browsershot::html(view('templates/' . $template->view, [
                'content' => $request->tipTapContent,
            ])->render())
                ->deviceScaleFactor(3) // 3 is max
                ->fullPage()
                ->disableCaptureURLs()
                ->preventUnsuccessfulResponse()
                ->delay(500)
                ->hideBrowserHeaderAndFooter()
                ->setOption('args', ['--disable-web-security', '--waitForFonts'])
                ->waitUntilNetworkIdle()
                ->disableJavascript()
                ->windowSize(1052, 0)
                ->setNodeBinary(config('binaries.node'))
                ->setNpmBinary(config('binaries.npm'))
                ->save($imagePath);

            if (connection_aborted()) {
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                return response()->json(['message' => 'Request aborted'], 499);
            }

            if ($request->get('resize')) {
                $image = Image::load($imagePath);
                $image->width(1052)->save();
            }

            return response()
                ->download($imagePath,
                    headers: [
                        'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                        'Pragma' => 'no-cache',
                        'Expires' => 0,
                    ]
                )
                ->deleteFileAfterSend();
        } catch (\Exception $e) {
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

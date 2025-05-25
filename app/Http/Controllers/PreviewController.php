<?php

namespace App\Http\Controllers;

use App\Http\Requests\PreviewRequest;
use App\Models\Template;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;

class PreviewController extends Controller
{
    public function __invoke(Template $template, PreviewRequest $request)
    {
        $imagePath = Storage::disk('local')->path(Str::uuid()->toString() . '.png');

        Browsershot::html(view('templates/' . $template->view, [
            'content' => $request->tipTapContent,
        ])->render())
            ->noSandbox()
            ->deviceScaleFactor(3) // 3 is max
            ->fullPage()
            ->preventUnsuccessfulResponse()
            ->hideBrowserHeaderAndFooter()
            ->waitUntilNetworkIdle()
            ->windowSize(1043, 0)
            ->setNodeBinary(config('binaries.node'))
            ->setNpmBinary(config('binaries.npm'))
            ->save($imagePath);

        return response()
            ->download($imagePath,
                headers: [
                    'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                    'Pragma' => 'no-cache',
                    'Expires' => 0,
                ]
            )
            ->deleteFileAfterSend();
    }
}

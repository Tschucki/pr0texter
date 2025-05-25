<?php

use App\Http\Controllers\ImageController;
use App\Http\Controllers\LegalNoticeController;
use App\Http\Controllers\PreviewController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;

Route::get('/', TemplateController::class)->name('dashboard');
Route::get('post-erstellen/{template}', [TemplateController::class, 'create'])->name('post.create');
Route::post('preview/{template}', PreviewController::class)->name('preview');

Route::get('test', function () {
    $content = '<img src="https://pr0texter.pimmelmannjones.com/storage/public/images/rxZFGPqipgnTIG6yi3RXOONYuHEi1ajNbFPQWARS.png?expires=1748194404&amp;signature=c56f78297aad06ad07433ecef36700cb0ffceb67ef48a80a5a69b9ce03d4d8d9" alt="pr0texter.png" style="width: 194px; height: auto; cursor: pointer; margin: 0px auto 0px 0px;" draggable="true"><p><span style="font-family: Verdana, sans-serif; font-size: 5rem"><strong>Der neue </strong></span><span style="font-family: Verdana, sans-serif; font-size: 5rem; color: #ee4d2e"><strong>pr0texter</strong></span></p><p><span style="font-family: Inter, sans-serif; font-size: 1.8rem; color: #f2f5f4">Montserrat</span></p>';
    return view('templates.standard', compact('content'));
})->name('test');

Route::post('bild/hochladen', [ImageController::class, 'upload'])->name('image.upload');

Route::get('/impressum', LegalNoticeController::class)->name('legal-notice');
Route::get('/datenschutz', PrivacyPolicyController::class)->name('privacy-policy');

// require __DIR__.'/settings.php';
// require __DIR__.'/auth.php';

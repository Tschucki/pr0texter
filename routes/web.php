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
    $content = '<p>Das ist ein Test </p>';
    return view('templates.standard', compact('content'));
})->name('test');

Route::post('bild/hochladen', [ImageController::class, 'upload'])->name('image.upload');

Route::get('/impressum', LegalNoticeController::class)->name('legal-notice');
Route::get('/datenschutz', PrivacyPolicyController::class)->name('privacy-policy');

// require __DIR__.'/settings.php';
// require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\ImageController;
use App\Http\Controllers\PreviewController;
use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;

Route::get('/', TemplateController::class)->name('dashboard');
Route::get('post-erstellen/{template}', [TemplateController::class, 'create'])->name('post.create');
Route::post('preview/{template}', PreviewController::class)->name('preview');

Route::post('bild/hochladen', [ImageController::class, 'upload'])->name('image.upload');

// require __DIR__.'/settings.php';
// require __DIR__.'/auth.php';

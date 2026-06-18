<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CallController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ProjectRequestController;
use App\Http\Controllers\Api\ServiceController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->name('api.login');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('posts', [PostController::class, 'index'])->name('api.posts.index');
    Route::get('posts/{slug}', [PostController::class, 'show'])->name('api.posts.show');

    Route::get('services', [ServiceController::class, 'index'])->name('api.services.index');

    Route::get('projects', [ProjectController::class, 'index'])->name('api.projects.index');
});

Route::post('contact', [ContactController::class, 'store'])->name('api.contact.store');
Route::post('calls', [CallController::class, 'store'])->name('api.calls.store');
Route::post('project-request', [ProjectRequestController::class, 'store'])->name('api.project-request.store');
Route::post('newsletter', [NewsletterController::class, 'store'])->name('api.newsletter.store');

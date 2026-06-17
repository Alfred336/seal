<?php

use App\Livewire\Manage\CallRequests;
use App\Livewire\Manage\Categories;
use App\Livewire\Manage\ContactSubmissions;
use App\Livewire\Manage\PostForm;
use App\Livewire\Manage\Posts;
use App\Livewire\Manage\ProjectRequests;
use App\Livewire\Manage\Projects;
use App\Livewire\Manage\Services;
use App\Livewire\Manage\Subscriptions;
use App\Livewire\Manage\Tags;
use App\Livewire\Manage\Users;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('manage')
    ->name('manage.')
    ->group(function (): void {
        // Blog
        Route::get('posts', Posts::class)
            ->middleware('can:viewAny,App\Models\Post')
            ->name('posts.index');

        Route::get('posts/create', PostForm::class)
            ->middleware('can:create,App\Models\Post')
            ->name('posts.create');

        Route::get('posts/{post}/edit', PostForm::class)
            ->middleware('can:update,post')
            ->name('posts.edit');

        Route::get('categories', Categories::class)
            ->middleware('can:viewAny,App\Models\Category')
            ->name('categories.index');

        Route::get('tags', Tags::class)
            ->middleware('can:viewAny,App\Models\Tag')
            ->name('tags.index');

        // Content
        Route::get('services', Services::class)
            ->middleware('can:viewAny,App\Models\Service')
            ->name('services.index');

        Route::get('projects', Projects::class)
            ->middleware('can:viewAny,App\Models\Project')
            ->name('projects.index');

        // Inquiries
        Route::get('contact-submissions', ContactSubmissions::class)
            ->middleware('can:viewAny,App\Models\ContactSubmission')
            ->name('contact-submissions.index');

        Route::get('call-requests', CallRequests::class)
            ->middleware('can:viewAny,App\Models\CallRequest')
            ->name('call-requests.index');

        Route::get('project-requests', ProjectRequests::class)
            ->middleware('can:viewAny,App\Models\ProjectRequest')
            ->name('project-requests.index');

        // Newsletter
        Route::get('subscriptions', Subscriptions::class)
            ->middleware('can:viewAny,App\Models\Subscription')
            ->name('subscriptions.index');

        // Administration
        Route::get('users', Users::class)
            ->middleware('can:viewAny,App\Models\User')
            ->name('users.index');
    });

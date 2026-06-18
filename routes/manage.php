<?php

use App\Livewire\Manage\CallRequests;
use App\Livewire\Manage\Categories;
use App\Livewire\Manage\ContactSubmissions;
use App\Livewire\Manage\PostForm;
use App\Livewire\Manage\Posts;
use App\Livewire\Manage\ProjectRequests;
use App\Livewire\Manage\Projects;
use App\Livewire\Manage\Roles;
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
            ->middleware('permission:posts.view|posts.manage-all')
            ->name('posts.index');

        Route::get('posts/create', PostForm::class)
            ->middleware('permission:posts.create|posts.manage-all')
            ->name('posts.create');

        Route::get('posts/{post}/edit', PostForm::class)
            ->middleware('permission:posts.view|posts.manage-all')
            ->name('posts.edit');

        Route::get('categories', Categories::class)
            ->middleware('permission:categories.manage|posts.view')
            ->name('categories.index');

        Route::get('tags', Tags::class)
            ->middleware('permission:tags.manage|posts.view')
            ->name('tags.index');

        // Content
        Route::get('services', Services::class)
            ->middleware('permission:services.view|services.manage')
            ->name('services.index');

        Route::get('projects', Projects::class)
            ->middleware('permission:projects.view|projects.manage')
            ->name('projects.index');

        // Inquiries
        Route::get('contact-submissions', ContactSubmissions::class)
            ->middleware('permission:contact-submissions.view')
            ->name('contact-submissions.index');

        Route::get('call-requests', CallRequests::class)
            ->middleware('permission:call-requests.view')
            ->name('call-requests.index');

        Route::get('project-requests', ProjectRequests::class)
            ->middleware('permission:project-requests.view')
            ->name('project-requests.index');

        // Newsletter
        Route::get('subscriptions', Subscriptions::class)
            ->middleware('permission:subscriptions.view')
            ->name('subscriptions.index');

        // Administration
        Route::get('users', Users::class)
            ->middleware('permission:users.view')
            ->name('users.index');

        // Roles & Permissions — admin-only; requires "roles.manage" permission
        Route::get('roles', Roles::class)
            ->middleware('permission:roles.manage')
            ->name('roles.index');
    });

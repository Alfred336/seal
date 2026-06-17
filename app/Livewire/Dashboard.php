<?php

namespace App\Livewire;

use App\Enums\PostStatus;
use App\Models\CallRequest;
use App\Models\ContactSubmission;
use App\Models\Post;
use App\Models\Project;
use App\Models\ProjectRequest;
use App\Models\Service;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render(): View
    {
        return view('dashboard', [
            'stats'              => $this->stats(),
            'recentPosts'        => Post::query()->with('author')->latest()->limit(5)->get(),
            'recentSubmissions'  => ContactSubmission::query()->orderByDesc('submitted_at')->limit(5)->get(),
        ]);
    }

    /** @return array<string, int> */
    private function stats(): array
    {
        return [
            'users'               => User::count(),
            'posts_total'         => Post::count(),
            'posts_published'     => Post::where('status', PostStatus::Published)->count(),
            'posts_draft'         => Post::where('status', PostStatus::Draft)->count(),
            'services'            => Service::count(),
            'projects'            => Project::count(),
            'contact_submissions' => ContactSubmission::count(),
            'call_requests'       => CallRequest::count(),
            'project_requests'    => ProjectRequest::count(),
            'subscribers'         => Subscription::count(),
        ];
    }
}

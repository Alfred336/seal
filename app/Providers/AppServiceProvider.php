<?php

namespace App\Providers;

use App\Enums\Permission;
use App\Models\CallRequest;
use App\Models\Category;
use App\Models\ContactSubmission;
use App\Models\Post;
use App\Models\Project;
use App\Models\ProjectRequest;
use App\Models\Service;
use App\Models\Subscription;
use App\Models\Tag;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureAuthorization();
    }

    protected function configureAuthorization(): void
    {
        Gate::before(function ($user, string $ability): ?bool {
            if (! $user instanceof User) {
                return null;
            }

            return $user->hasRole('admin') ? true : null;
        });

        Gate::define('viewAny', function (User $user, string $model): bool {
            $permission = match ($model) {
                Post::class => Permission::PostsView->value,
                Service::class => Permission::ServicesView->value,
                Project::class => Permission::ProjectsView->value,
                User::class => Permission::UsersView->value,
                ContactSubmission::class => Permission::ContactSubmissionsView->value,
                CallRequest::class => Permission::CallRequestsView->value,
                ProjectRequest::class => Permission::ProjectRequestsView->value,
                Subscription::class => Permission::SubscriptionsView->value,
                default => null,
            };

            return $permission ? $user->can($permission) : false;
        });

        Gate::define('create', function (User $user, string $model): bool {
            $permission = match ($model) {
                Post::class => Permission::PostsCreate->value,
                Service::class => Permission::ServicesManage->value,
                Project::class => Permission::ProjectsManage->value,
                User::class => Permission::UsersManage->value,
                Category::class => Permission::CategoriesManage->value,
                Tag::class => Permission::TagsManage->value,
                default => null,
            };

            return $permission ? $user->can($permission) : false;
        });

        Gate::define('update', function (User $user, $target): bool {
            if ($target instanceof Post) {
                return $user->can(Permission::PostsManageAll->value)
                    || ($user->can(Permission::PostsUpdateOwn->value) && $target->isOwnedBy($user));
            }

            if ($target instanceof User) {
                return $user->can(Permission::UsersManage->value) && $user->id !== $target->id;
            }

            if ($target instanceof Service || $target instanceof Category || $target instanceof Tag) {
                $permission = match ($target::class) {
                    Service::class => Permission::ServicesManage->value,
                    Category::class => Permission::CategoriesManage->value,
                    Tag::class => Permission::TagsManage->value,
                    default => null,
                };

                return $permission ? $user->can($permission) : false;
            }

            if ($target instanceof Project) {
                return $user->can(Permission::ProjectsManage->value);
            }

            if ($target instanceof ContactSubmission || $target instanceof CallRequest || $target instanceof ProjectRequest) {
                $permission = match ($target::class) {
                    ContactSubmission::class => Permission::ContactSubmissionsUpdate->value,
                    CallRequest::class => Permission::CallRequestsUpdate->value,
                    ProjectRequest::class => Permission::ProjectRequestsUpdate->value,
                    default => null,
                };

                return $permission ? $user->can($permission) : false;
            }

            return false;
        });

        Gate::define('delete', function (User $user, $target): bool {
            if ($target instanceof Post) {
                return $user->can(Permission::PostsManageAll->value)
                    || ($user->can(Permission::PostsDeleteOwn->value) && $target->isOwnedBy($user));
            }

            if ($target instanceof Service || $target instanceof Category || $target instanceof Tag) {
                $permission = match ($target::class) {
                    Service::class => Permission::ServicesManage->value,
                    Category::class => Permission::CategoriesManage->value,
                    Tag::class => Permission::TagsManage->value,
                    default => null,
                };

                return $permission ? $user->can($permission) : false;
            }

            if ($target instanceof Project || $target instanceof Subscription) {
                return $user->can(Permission::ProjectsManage->value) || $user->can(Permission::SubscriptionsManage->value);
            }

            return false;
        });

        Gate::define('manage-inquiries', function (User $user): bool {
            return $user->can(Permission::ContactSubmissionsView->value)
                || $user->can(Permission::CallRequestsView->value)
                || $user->can(Permission::ProjectRequestsView->value);
        });

        Gate::define('manage', function (User $user): bool {
            return $user->can(Permission::PostsManageAll->value);
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );

        if (str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}

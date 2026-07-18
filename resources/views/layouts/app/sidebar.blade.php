<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>

                {{-- ─────────────────────────────────────────────────────────
                     Platform: always visible to every authenticated user.
                ───────────────────────────────────────────────────────────── --}}
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                {{-- ─────────────────────────────────────────────────────────
                     Blog section: shown to users with the "posts.view"
                     permission.  Sub-items for Categories and Tags are gated
                     by "categories.manage" / "tags.manage" respectively,
                     because there is no separate "view-only" permission for
                     those taxonomy resources.
                ───────────────────────────────────────────────────────────── --}}
                @can('posts.view')
                    <flux:sidebar.group :heading="__('Blog')" class="grid">

                        {{-- Posts index --}}
                        <flux:sidebar.item
                            icon="document-text"
                            :href="route('manage.posts.index')"
                            :current="request()->routeIs('manage.posts.*')"
                            wire:navigate
                        >
                            {{ __('Posts') }}
                        </flux:sidebar.item>

                        {{-- Categories: requires full taxonomy management permission --}}
                        @can('categories.manage')
                            <flux:sidebar.item
                                icon="rectangle-stack"
                                :href="route('manage.categories.index')"
                                :current="request()->routeIs('manage.categories.*')"
                                wire:navigate
                            >
                                {{ __('Categories') }}
                            </flux:sidebar.item>
                        @endcan

                        {{-- Tags: requires full taxonomy management permission --}}
                        @can('tags.manage')
                            <flux:sidebar.item
                                icon="tag"
                                :href="route('manage.tags.index')"
                                :current="request()->routeIs('manage.tags.*')"
                                wire:navigate
                            >
                                {{ __('Tags') }}
                            </flux:sidebar.item>
                        @endcan

                    </flux:sidebar.group>
                @endcan

                {{-- ─────────────────────────────────────────────────────────
                     Content section: shown to users with "services.view".
                     The Projects sub-item requires "projects.view".
                ───────────────────────────────────────────────────────────── --}}
                @canany(['services.view', 'careers.manage'])
                    <flux:sidebar.group :heading="__('Content')" class="grid">

                        {{-- Services index --}}
                        @can('services.view')
                            <flux:sidebar.item
                                icon="briefcase"
                                :href="route('manage.services.index')"
                                :current="request()->routeIs('manage.services.*')"
                                wire:navigate
                            >
                                {{ __('Services') }}
                            </flux:sidebar.item>
                        @endcan

                        {{-- Projects: separate view permission --}}
                        @can('projects.view')
                            <flux:sidebar.item
                                icon="photo"
                                :href="route('manage.projects.index')"
                                :current="request()->routeIs('manage.projects.*')"
                                wire:navigate
                            >
                                {{ __('Projects') }}
                            </flux:sidebar.item>
                        @endcan

                        {{-- Careers: open positions management --}}
                        @can('careers.manage')
                            <flux:sidebar.item
                                icon="identification"
                                :href="route('manage.open-positions.index')"
                                :current="request()->routeIs('manage.open-positions.*')"
                                wire:navigate
                            >
                                {{ __('Careers') }}
                            </flux:sidebar.item>
                        @endcan

                    </flux:sidebar.group>
                @endcanany

                {{-- ─────────────────────────────────────────────────────────
                     Inquiries section: shown when the user holds at least
                     one of the three inquiry-view permissions.  Each
                     sub-item is individually gated by its own permission.
                ───────────────────────────────────────────────────────────── --}}
                @canany(['contact-submissions.view', 'call-requests.view', 'project-requests.view'])
                    <flux:sidebar.group :heading="__('Inquiries')" class="grid">

                        {{-- Contact form submissions --}}
                        @can('contact-submissions.view')
                            <flux:sidebar.item
                                icon="envelope"
                                :href="route('manage.contact-submissions.index')"
                                :current="request()->routeIs('manage.contact-submissions.*')"
                                wire:navigate
                            >
                                {{ __('Contact') }}
                            </flux:sidebar.item>
                        @endcan

                        {{-- Scheduled call requests --}}
                        @can('call-requests.view')
                            <flux:sidebar.item
                                icon="phone"
                                :href="route('manage.call-requests.index')"
                                :current="request()->routeIs('manage.call-requests.*')"
                                wire:navigate
                            >
                                {{ __('Call Requests') }}
                            </flux:sidebar.item>
                        @endcan

                        {{-- Project brief requests --}}
                        @can('project-requests.view')
                            <flux:sidebar.item
                                icon="clipboard-document-list"
                                :href="route('manage.project-requests.index')"
                                :current="request()->routeIs('manage.project-requests.*')"
                                wire:navigate
                            >
                                {{ __('Project Requests') }}
                            </flux:sidebar.item>
                        @endcan

                    </flux:sidebar.group>
                @endcanany

                {{-- ─────────────────────────────────────────────────────────
                     Marketing section: shown to users with
                     "subscriptions.view" permission.
                ───────────────────────────────────────────────────────────── --}}
                @can('subscriptions.view')
                    <flux:sidebar.group :heading="__('Marketing')" class="grid">
                        <flux:sidebar.item
                            icon="newspaper"
                            :href="route('manage.subscriptions.index')"
                            :current="request()->routeIs('manage.subscriptions.*')"
                            wire:navigate
                        >
                            {{ __('Newsletter') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endcan

                {{-- ─────────────────────────────────────────────────────────
                     Administration section: shown to users with "users.view"
                     OR "roles.manage" so each permission can independently
                     reveal this group without requiring both.
                ───────────────────────────────────────────────────────────── --}}
                @canany(['users.view', 'roles.manage', 'backups.manage'])
                    <flux:sidebar.group :heading="__('Administration')" class="grid">

                        {{-- Users management: requires users.view --}}
                        @can('users.view')
                            <flux:sidebar.item
                                icon="users"
                                :href="route('manage.users.index')"
                                :current="request()->routeIs('manage.users.*')"
                                wire:navigate
                            >
                                {{ __('Users') }}
                            </flux:sidebar.item>
                        @endcan

                        {{-- Roles & Permissions management: requires roles.manage --}}
                        @can('roles.manage')
                            <flux:sidebar.item
                                icon="shield-check"
                                :href="route('manage.roles.index')"
                                :current="request()->routeIs('manage.roles.*')"
                                wire:navigate
                            >
                                {{ __('Roles & Permissions') }}
                            </flux:sidebar.item>
                        @endcan

                        {{-- Backups management: requires backups.manage --}}
                        @can('backups.manage')
                            <flux:sidebar.item
                                icon="circle-stack"
                                :href="route('manage.backups.index')"
                                :current="request()->routeIs('manage.backups.*')"
                                wire:navigate
                            >
                                {{ __('Backups') }}
                            </flux:sidebar.item>
                        @endcan

                    </flux:sidebar.group>
                @endcanany

            </flux:sidebar.nav>

            <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
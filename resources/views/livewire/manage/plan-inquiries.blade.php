<div class="flex flex-col gap-5">
    <div>
        <flux:heading size="lg">{{ __('Plan Inquiries') }}</flux:heading>
        <flux:text class="mt-1">{{ __('Client requests submitted from the pricing plans.') }}</flux:text>
    </div>

    <div class="flex flex-wrap gap-3">
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="Search client, business, email or phone…"
            icon="magnifying-glass"
            class="min-w-72 max-w-sm"
        />

        <flux:select wire:model.live="plan" class="w-48">
            <flux:select.option value="">All plans</flux:select.option>
            <flux:select.option value="starter">Business Starter</flux:select.option>
            <flux:select.option value="professional">Professional CMS</flux:select.option>
            <flux:select.option value="enterprise">Enterprise</flux:select.option>
        </flux:select>

        <flux:select wire:model.live="status" class="w-48">
            <flux:select.option value="">All statuses</flux:select.option>
            @foreach ($statuses as $s)
                <flux:select.option :value="$s->value">
                    {{ ucfirst(str_replace('_', ' ', $s->value)) }}
                </flux:select.option>
            @endforeach
        </flux:select>
    </div>

    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 text-left dark:bg-zinc-900">
                <tr>
                    <th class="px-4 py-3 font-medium text-zinc-500">Client</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">Plan</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">Contact</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">Submitted</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">Status</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 bg-white dark:divide-zinc-800 dark:bg-zinc-900">
                @forelse ($inquiries as $inquiry)
                    <tr x-data="{ open: false }" wire:key="plan-inquiry-{{ $inquiry->id }}">
                        <td class="px-4 py-4 align-top">
                            <button @click="open = !open" class="text-left">
                                <div class="font-semibold text-zinc-900 hover:underline dark:text-white">
                                    {{ $inquiry->full_name }}
                                </div>
                                <div class="mt-1 text-xs text-zinc-500">
                                    {{ $inquiry->business_name ?: '—' }}
                                </div>
                            </button>

                            <div x-show="open" x-collapse class="mt-4 max-w-xl rounded-lg bg-zinc-50 p-4 text-xs dark:bg-zinc-800">
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <div><strong>Phone:</strong> {{ $inquiry->phone }}</div>
                                    <div><strong>Email:</strong> {{ $inquiry->email }}</div>
                                    <div><strong>Business category:</strong> {{ $inquiry->business_category ?: '—' }}</div>
                                    <div><strong>Pages:</strong> {{ $inquiry->number_of_pages ?: '—' }}</div>
                                    <div><strong>Domain:</strong> {{ is_null($inquiry->has_domain) ? '—' : ($inquiry->has_domain ? 'Yes' : 'No') }}</div>
                                    <div><strong>Logo:</strong> {{ is_null($inquiry->has_logo) ? '—' : ($inquiry->has_logo ? 'Yes' : 'No') }}</div>
                                    <div><strong>CMS:</strong> {{ is_null($inquiry->needs_cms) ? '—' : ($inquiry->needs_cms ? 'Yes' : 'No') }}</div>
                                    <div><strong>Blog:</strong> {{ is_null($inquiry->needs_blog) ? '—' : ($inquiry->needs_blog ? 'Yes' : 'No') }}</div>
                                </div>

                                <div class="mt-4 space-y-2">
                                    <p><strong>Website purpose:</strong><br>{{ $inquiry->website_purpose ?: '—' }}</p>
                                    <p><strong>Project description:</strong><br>{{ $inquiry->project_description ?: '—' }}</p>
                                    <p><strong>Additional requirements:</strong><br>{{ $inquiry->additional_requirements ?: '—' }}</p>
                                    <p><strong>Existing system:</strong><br>{{ $inquiry->existing_system ?: '—' }}</p>
                                    <p><strong>Enterprise features:</strong><br>
                                        @php
                                            $features = collect([
                                                'E-commerce' => $inquiry->ecommerce,
                                                'Online Payments' => $inquiry->online_payments,
                                                'Booking System' => $inquiry->booking_system,
                                                'User Accounts' => $inquiry->user_accounts,
                                                'Admin Dashboard' => $inquiry->admin_dashboard,
                                                'API Integration' => $inquiry->api_integration,
                                                'SMS / Notifications' => $inquiry->sms_notifications,
                                            ])->filter(fn ($value) => $value === true)->keys();
                                        @endphp
                                        {{ $features->isNotEmpty() ? $features->join(', ') : '—' }}
                                    </p>
                                    <p><strong>Deadline:</strong> {{ $inquiry->project_deadline ?: '—' }} &nbsp; <strong>Budget:</strong> {{ $inquiry->budget_range ?: '—' }}</p>
                                </div>

                                @can('plan-inquiries.update')
                                    <div class="mt-4 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                                        @if ($editingId === $inquiry->id)
                                            <flux:textarea wire:model="notes" label="Admin notes" rows="3" />
                                            <div class="mt-2 flex gap-2">
                                                <flux:button wire:click="saveNotes" size="sm">Save notes</flux:button>
                                                <flux:button wire:click="cancelNotes" variant="ghost" size="sm">Cancel</flux:button>
                                            </div>
                                        @else
                                            <p><strong>Admin notes:</strong><br>{{ $inquiry->notes ?: 'No notes yet.' }}</p>
                                            <flux:button wire:click="editNotes({{ $inquiry->id }})" class="mt-2" variant="ghost" size="sm">
                                                Edit notes
                                            </flux:button>
                                        @endif
                                    </div>
                                @endcan
                            </div>
                        </td>

                        <td class="px-4 py-4 align-top">
                            <flux:badge size="sm">
                                {{ match($inquiry->plan) {
                                    'starter' => 'Business Starter',
                                    'professional' => 'Professional CMS',
                                    'enterprise' => 'Enterprise',
                                    default => ucfirst($inquiry->plan),
                                } }}
                            </flux:badge>
                        </td>

                        <td class="px-4 py-4 align-top text-zinc-500">
                            <div>{{ $inquiry->email }}</div>
                            <div class="mt-1">{{ $inquiry->phone }}</div>
                        </td>

                        <td class="px-4 py-4 align-top whitespace-nowrap text-zinc-500">
                            {{ $inquiry->created_at->format('M j, Y') }}
                        </td>

                        <td class="px-4 py-4 align-top">
                            @can('plan-inquiries.update')
                                <flux:select wire:change="updateStatus({{ $inquiry->id }}, $event.target.value)" class="text-xs py-1">
                                    @foreach ($statuses as $s)
                                        <flux:select.option :value="$s->value" :selected="$inquiry->status === $s">
                                            {{ ucfirst(str_replace('_', ' ', $s->value)) }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                            @else
                                <flux:badge size="sm">{{ str_replace('_', ' ', $inquiry->status->value) }}</flux:badge>
                            @endcan
                        </td>

                        <td class="px-4 py-4 align-top">
                            <a href="mailto:{{ $inquiry->email }}" class="text-sm font-medium text-cyan-600 hover:underline">
                                Contact
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-zinc-400">
                            No plan inquiries found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $inquiries->links() }}</div>
</div>

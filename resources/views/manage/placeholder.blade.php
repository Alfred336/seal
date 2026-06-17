<x-layouts::app :title="$title">
    <div class="flex flex-col gap-4">
        <div>
            <flux:heading size="lg">{{ $heading }}</flux:heading>
            <flux:text class="mt-1">{{ $description }}</flux:text>
        </div>

        <flux:callout variant="secondary" icon="information-circle">
            {{ __('This section is scaffolded in Stage 1. Functionality will be implemented in a later build stage.') }}
        </flux:callout>
    </div>
</x-layouts::app>

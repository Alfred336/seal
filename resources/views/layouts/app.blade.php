<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>

    {{-- Handle browser/Livewire 'notify' dispatches --}}
    <div x-data x-on:notify.window="window.dispatchEvent(new CustomEvent('toast-show', { detail: { duration: 4000, slots: { text: $event.detail.message }, dataset: { variant: 'success' } } }))"></div>

    {{-- Handle session flashed notifications --}}
    @if (session()->has('notify'))
        <div x-data x-init="window.dispatchEvent(new CustomEvent('toast-show', { detail: { duration: 4000, slots: { text: '{{ e(session('notify')) }}' }, dataset: { variant: 'success' } } }))"></div>
    @endif
</x-layouts::app.sidebar>

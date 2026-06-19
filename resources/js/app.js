import Quill from 'quill';

// ── Alpine.js directive: x-quill ─────────────────────────────────────────────
// Usage: <div x-quill="'content'"></div>
// expression = the Livewire property name as a quoted string, e.g. 'content'
document.addEventListener('alpine:init', () => {
    Alpine.directive('quill', (el, { expression }, { cleanup }) => {
        const wireProp = expression.replace(/['"]/g, '');

        // ── Reliable way to reach the Livewire component ──────────────────────
        // Alpine.$data() does NOT expose Livewire magic properties like $wire.
        // Use Livewire.find(id) instead — guaranteed to return the wire proxy.
        const getWireId = () => el.closest('[wire\\:id]')?.getAttribute('wire:id');
        const getWire  = () => {
            const id = getWireId();
            return id ? Livewire.find(id) : null;
        };

        // ── Mount Quill ───────────────────────────────────────────────────────
        el.innerHTML = '';
        const quill = new Quill(el, {
            theme: 'snow',
            placeholder: 'Write your post content here…',
            modules: {
                toolbar: [
                    [{ header: [1, 2, 3, 4, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ color: [] }, { background: [] }],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    [{ indent: '-1' }, { indent: '+1' }],
                    [{ align: [] }],
                    ['blockquote', 'code-block'],
                    ['link', 'image'],
                    ['clean'],
                ],
            },
        });

        // Expose the instance so window.quillSave() can reach it
        el._quill = quill;

        // ── Seed editor from Livewire on init ─────────────────────────────────
        const wire = getWire();
        if (wire) {
            const initial = wire.get(wireProp);
            if (initial) quill.root.innerHTML = initial;
        }

        // ── Push editor → Livewire on every keystroke ─────────────────────────
        // Direct property assignment updates local state WITHOUT a network
        // request. The next $wire.save() call will include the updated value.
        quill.on('text-change', () => {
            const w = getWire();
            if (!w) return;
            const isEmpty = quill.getText().trim() === '';
            w[wireProp] = isEmpty ? '' : quill.root.innerHTML;
        });

        // ── Livewire → editor after a server round-trip ───────────────────────
        let lastSynced = '';
        const observer = new MutationObserver(() => {
            const w = getWire();
            if (!w) return;
            const serverVal = w.get(wireProp) || '';
            if (serverVal !== lastSynced && serverVal !== quill.root.innerHTML) {
                lastSynced = serverVal;
                quill.root.innerHTML = serverVal;
            }
        });
        const wireRoot = el.closest('[wire\\:id]');
        if (wireRoot) {
            observer.observe(wireRoot, { childList: true, subtree: false });
        }

        // ── Cleanup on Livewire navigate ──────────────────────────────────────
        cleanup(() => {
            observer.disconnect();
            quill.off('text-change');
        });
    });
});

// ── Global helper called by the Save button ───────────────────────────────────
// Called as: window.quillSave($wire)
// 1. Reads the latest content from Quill
// 2. Writes it to the Livewire property via direct assignment (no network hit)
// 3. Then calls $wire.save() — ONE network request with the correct content
window.quillSave = function (wire) {
    const qEl = document.querySelector('[x-quill]');
    if (qEl && qEl._quill) {
        const isEmpty = qEl._quill.getText().trim() === '';
        // Direct assignment: updates local reactive state, no separate request
        wire.content = isEmpty ? '' : qEl._quill.root.innerHTML;
    }
    wire.save();
};

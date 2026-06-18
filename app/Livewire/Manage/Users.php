<?php

namespace App\Livewire\Manage;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Users')]
#[Layout('layouts.app')]
class Users extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $editingId = null;

    /** Controls flux:modal visibility via wire:model. */
    public bool $showModal = false;

    // form fields
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $selectedRole = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showModal = true;
    }

    public function create(): void
    {
        abort_unless(auth()->user()->can(Permission::UsersManage->value), 403);
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'selectedRole' => ['required', 'in:'.implode(',', array_column(Role::cases(), 'value'))],
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'email_verified_at' => now(),
        ]);
        $user->syncRoles([$this->selectedRole]);

        $this->resetForm();
        $this->showModal = false;
    }

    public function startEdit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editingId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->selectedRole = $user->roles->first()?->name ?? '';
        $this->showModal = true;
    }

    public function saveEdit(): void
    {
        $user = User::findOrFail($this->editingId);
        abort_unless(auth()->user()->can(Permission::UsersManage->value) && $user->id !== auth()->id(), 403);

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', "unique:users,email,{$this->editingId}"],
            'password' => ['nullable', 'string', 'min:8'],
            'selectedRole' => ['required', 'in:'.implode(',', array_column(Role::cases(), 'value'))],
        ]);

        $data = ['name' => $this->name, 'email' => $this->email];
        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        $user->update($data);
        $user->syncRoles([$this->selectedRole]);

        $this->resetForm();
        $this->showModal = false;
        $this->editingId = null;
    }

    public function toggleActive(int $id): void
    {
        $user = User::findOrFail($id);
        abort_unless(auth()->user()->can(Permission::UsersManage->value) && $user->id !== auth()->id(), 403);
        $user->update(['deactivated_at' => $user->isActive() ? now() : null]);
    }

    public function closeModal(): void
    {
        $this->resetForm();
        $this->showModal = false;
        $this->editingId = null;
    }

    public function render(): View
    {
        $users = User::query()
            ->with('roles')
            ->when($this->search, fn ($q) => $q
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(15);

        return view('livewire.manage.users', [
            'users' => $users,
            'roles' => Role::cases(),
        ]);
    }

    private function resetForm(): void
    {
        $this->reset('name', 'email', 'password', 'selectedRole');
    }
}

<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\User;
use App\Models\Group;

new 
#[Layout('components.layouts.app', ['title' => 'Mitarbeiter bearbeiten'])]
class extends Component {
    public User $user;
    public $role = '';
    public $group_id = '';
    public $status = '';

    public function mount(User $user)
    {
        $this->user = $user;
        $this->role = $user->role;
        $this->group_id = $user->group_id;
        $this->status = $user->status;
    }

    public function save()
    {
        $this->validate([
            'role' => 'required|in:admin,management,staff',
            'group_id' => 'nullable|exists:groups,id',
            'status' => 'required|in:active,sick,vacation',
        ]);

        $this->user->update([
            'role' => $this->role,
            'group_id' => $this->group_id ?: null,
            'status' => $this->status,
        ]);

        return redirect()->route('staff.index');
    }

    public function with()
    {
        return [
            'groups' => Group::all(),
        ];
    }
}; ?>

<div class="max-w-2xl mx-auto p-6">
    <div class="flex items-center gap-4 mb-8">
        <flux:button :href="route('staff.index')" icon="arrow-left" variant="ghost" />
        <flux:heading size="xl">Mitarbeiter bearbeiten</flux:heading>
    </div>
    
    <flux:subheading class="mb-6">Bearbeiten von {{ $user->name }}</flux:subheading>

    <form wire:submit="save" class="space-y-6">
        <flux:select wire:model="role" label="Rolle">
            <flux:select.option value="staff">Staff</flux:select.option>
            <flux:select.option value="management">Management</flux:select.option>
            <flux:select.option value="admin">Admin</flux:select.option>
        </flux:select>
        
        <flux:select wire:model="group_id" label="Gruppe" placeholder="Keine">
                <flux:select.option value="">Keine</flux:select.option>
            @foreach ($groups as $group)
                <flux:select.option value="{{ $group->id }}">{{ $group->name }}</flux:select.option>
            @endforeach
        </flux:select>
        
        <flux:select wire:model="status" label="Status">
            <flux:select.option value="active">Aktiv</flux:select.option>
            <flux:select.option value="sick">Krank</flux:select.option>
            <flux:select.option value="vacation">Urlaub</flux:select.option>
        </flux:select>

        <div class="flex justify-end gap-2">
            <flux:button :href="route('staff.index')" variant="ghost">Abbrechen</flux:button>
            <flux:button type="submit" variant="primary">Speichern</flux:button>
        </div>
    </form>
</div>

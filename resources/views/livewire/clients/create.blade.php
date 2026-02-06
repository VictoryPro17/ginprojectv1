<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Client;
use App\Models\Group;
use App\Models\User;
use Illuminate\Validation\Rule;

new 
#[Layout('components.layouts.app', ['title' => 'Neuer Klient'])]
class extends Component {
    public $name = '';
    public $dob = '';
    public $group_id = '';
    public $caretaker_id = '';
    public $description = '';
    public $notes = '';
    public $allergies = '';
    public $medications = '';

    public function mount()
    {
    }

    public function save()
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'dob' => ['required', 'date'],
            'group_id' => ['required', 'exists:groups,id'],
            'caretaker_id' => ['nullable', 'exists:users,id'],
            'description' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'allergies' => ['nullable', 'string'],
            'medications' => ['nullable', 'string'],
        ]);

        Client::create($validated);

        return redirect()->route('clients.index');
    }

    public function with()
    {
        return [
            'groups' => Group::all(),
            'users' => User::where('role', 'staff')->orWhere('role', 'admin')->get(),
        ];
    }
}; ?>

<div class="max-w-2xl mx-auto p-6">
    <div class="flex items-center gap-4 mb-8">
        <flux:button :href="route('clients.index')" icon="arrow-left" variant="ghost" />
        <flux:heading size="xl">Neuer Klient</flux:heading>
    </div>

    <form wire:submit="save" class="space-y-6">
        <flux:input wire:model="name" label="Name" placeholder="Max Mustermann" required />
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:input wire:model="dob" type="date" label="Geburtsdatum" required />
            
            <flux:select wire:model="group_id" label="Gruppe" placeholder="Wähle eine Gruppe">
                @foreach ($groups as $group)
                    <flux:select.option value="{{ $group->id }}">{{ $group->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        <flux:select wire:model="caretaker_id" label="Hauptbetreuer" placeholder="Optional">
            <flux:select.option value="">Kein Betreuer</flux:select.option>
            @foreach ($users as $user)
                <flux:select.option value="{{ $user->id }}">{{ $user->name }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:textarea wire:model="description" label="Beschreibung" placeholder="Allgemeine Infos..." />
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:textarea wire:model="allergies" label="Allergien" placeholder="Keine bekannt..." />
            <flux:textarea wire:model="medications" label="Medikamente" placeholder="Keine..." />
        </div>

            <flux:textarea wire:model="notes" text="Interne Notizen" placeholder="Nur für Mitarbeiter sichtbar..." />

        <div class="flex justify-end gap-2">
            <flux:button :href="route('clients.index')" variant="ghost">Abbrechen</flux:button>
            <flux:button type="submit" variant="primary">Speichern</flux:button>
        </div>
    </form>
</div>

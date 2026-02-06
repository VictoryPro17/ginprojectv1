<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Client;
use App\Models\Group;
use App\Models\User;
use Illuminate\Validation\Rule;

new 
#[Layout('components.layouts.app', ['title' => 'Klient bearbeiten'])]
class extends Component {
    public Client $client;

    public $name = '';
    public $dob = '';
    public $group_id = '';
    public $caretaker_id = '';
    public $description = '';
    public $notes = '';
    public $allergies = '';
    public $medications = '';

    public function mount(Client $client)
    {
        $this->client = $client;
        $this->name = $client->name;
        $this->dob = $client->dob ? $client->dob->format('Y-m-d') : '';
        $this->group_id = $client->group_id;
        $this->caretaker_id = $client->caretaker_id;
        $this->description = $client->description;
        $this->notes = $client->notes;
        $this->allergies = $client->allergies;
        $this->medications = $client->medications;
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

        $this->client->update($validated);

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
        <flux:heading size="xl">Klient bearbeiten</flux:heading>
    </div>

    <form wire:submit="save" class="space-y-6">
        <flux:input wire:model="name" label="Name" required />
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:input wire:model="dob" type="date" label="Geburtsdatum" required />
            
            <flux:select wire:model="group_id" label="Gruppe">
                @foreach ($groups as $group)
                    <flux:select.option value="{{ $group->id }}">{{ $group->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        <flux:select wire:model="caretaker_id" label="Hauptbetreuer">
            <flux:select.option value="">Kein Betreuer</flux:select.option>
            @foreach ($users as $user)
                <flux:select.option value="{{ $user->id }}">{{ $user->name }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:textarea wire:model="description" label="Beschreibung" />
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:textarea wire:model="allergies" label="Allergien" />
            <flux:textarea wire:model="medications" label="Medikamente" />
        </div>

            <flux:textarea wire:model="notes" label="Interne Notizen" />

        <div class="flex justify-end gap-2">
            <flux:button :href="route('clients.index')" variant="ghost">Abbrechen</flux:button>
            <flux:button type="submit" variant="primary">Speichern</flux:button>
        </div>
    </form>
</div>

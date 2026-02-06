<?php

use Livewire\Volt\Component;
use App\Models\Group;
use App\Models\User;

new class extends Component {
    public $name = '';
    public $team_lead_id = '';

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'team_lead_id' => 'nullable|exists:users,id',
        ]);

        Group::create([
            'name' => $this->name,
            'team_lead_id' => $this->team_lead_id ?: null,
        ]);

        return redirect()->route('groups.index');
    }

    public function with()
    {
        return [
            'users' => User::all(),
        ];
    }
}; ?>

<div class="space-y-6">
    <div>
        <flux:heading size="lg">Neue Gruppe erstellen</flux:heading>
        <flux:subheading>Fügen Sie eine neue Wohngruppe hinzu.</flux:subheading>
    </div>

    <form wire:submit="save" class="space-y-6">
        <flux:input wire:model="name" label="Gruppenname" placeholder="Gruppe A" required />
        
        <flux:select wire:model="team_lead_id" label="Gruppenleitung" placeholder="Keine">
             <flux:select.option value="">Keine</flux:select.option>
            @foreach ($users as $user)
                <flux:select.option value="{{ $user->id }}">{{ $user->name }}</flux:select.option>
            @endforeach
        </flux:select>

        <div class="flex justify-end gap-2">
            <flux:button type="submit" variant="primary">Erstellen</flux:button>
        </div>
    </form>
</div>

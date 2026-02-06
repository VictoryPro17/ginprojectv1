<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Shift;
use App\Models\User;

new 
#[Layout('components.layouts.app', ['title' => 'Neuer Dienst'])]
class extends Component {
    public $user_id = '';
    public $start_time = '';
    public $end_time = '';
    public $type = 'work';

    public function save()
    {
        $this->validate([
            'user_id' => 'required|exists:users,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'type' => 'required|string',
        ]);

        Shift::create([
            'user_id' => $this->user_id,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'type' => $this->type,
        ]);

        return redirect()->route('shifts.index');
    }

    public function with()
    {
        return [
            'users' => User::all(),
        ];
    }
}; ?>

<div class="max-w-2xl mx-auto p-6">
    <div class="flex items-center gap-4 mb-8">
        <flux:button :href="route('shifts.index')" icon="arrow-left" variant="ghost" />
        <flux:heading size="xl">Neuer Dienst</flux:heading>
    </div>

    <form wire:submit="save" class="space-y-6">
        <flux:select wire:model.live="user_id" label="Mitarbeiter" required>
            @foreach ($users as $user)
                <flux:select.option class="truncate" value="{{ $user->id }}">{{ $user->name }}</flux:select.option>
            @endforeach
        </flux:select>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:input wire:model="start_time" type="datetime-local" label="Startzeit" required />
            <flux:input wire:model="end_time" type="datetime-local" label="Endzeit" required />
        </div>

        <flux:select wire:model="type" label="Typ">
            <flux:select.option value="work">Dienst</flux:select.option>
            <flux:select.option value="vacation">Urlaub</flux:select.option>
            <flux:select.option value="sick">Krank</flux:select.option>
            <flux:select.option value="training">Fortbildung</flux:select.option>
        </flux:select>

        <div class="flex justify-end gap-2">
            <flux:button :href="route('shifts.index')" variant="ghost">Abbrechen</flux:button>
            <flux:button type="submit" variant="primary">Speichern</flux:button>
        </div>
    </form>
</div>

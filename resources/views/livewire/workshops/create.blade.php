<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Workshop;

new 
#[Layout('components.layouts.app', ['title' => 'Neuer Workshop'])]
class extends Component {
    public $title = '';
    public $description = '';
    public $date = '';
    public $location = '';

    public function save()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'location' => 'nullable|string',
        ]);

        Workshop::create([
            'title' => $this->title,
            'description' => $this->description,
            'date' => $this->date,
            'location' => $this->location,
        ]);

        return redirect()->route('workshops.index');
    }
}; ?>

<div class="max-w-2xl mx-auto p-6">
    <div class="flex items-center gap-4 mb-8">
        <flux:button :href="route('workshops.index')" icon="arrow-left" variant="ghost" />
        <flux:heading size="xl">Neuer Workshop</flux:heading>
    </div>

    <form wire:submit="save" class="space-y-6">
        <flux:input wire:model="title" label="Titel" placeholder="Erste-Hilfe Kurs" required />
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:input wire:model="date" type="datetime-local" label="Datum & Uhrzeit" required />
            <flux:input wire:model="location" label="Ort" placeholder="Besprechungsraum A" />
        </div>

        <flux:textarea wire:model="description" label="Beschreibung" placeholder="Inhalte des Workshops..." rows="4" />

        <div class="flex justify-end gap-2">
            <flux:button :href="route('workshops.index')" variant="ghost">Abbrechen</flux:button>
            <flux:button type="submit" variant="primary">Erstellen</flux:button>
        </div>
    </form>
</div>
